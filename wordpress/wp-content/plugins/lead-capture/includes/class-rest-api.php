<?php

defined( 'ABSPATH' ) || exit;

class Lead_Capture_REST_API {

	public static function register(): void {
		add_action( 'rest_api_init', array( self::class, 'register_routes' ) );
		add_action( 'rest_api_init', array( self::class, 'register_cors' ), 15 );
	}

	public static function register_cors(): void {
		remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
		add_filter(
			'rest_pre_serve_request',
			static function ( $value ) {
				$origin = get_http_origin();
				$allowed = apply_filters(
					'lead_capture_allowed_origins',
					array(
						'http://localhost:3000',
						'http://127.0.0.1:3000',
					)
				);

				if ( $origin && in_array( $origin, $allowed, true ) ) {
					header( 'Access-Control-Allow-Origin: ' . esc_url_raw( $origin ) );
					header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS' );
					header( 'Access-Control-Allow-Credentials: true' );
					header( 'Access-Control-Allow-Headers: Content-Type, X-WP-Nonce' );
				}

				if ( 'OPTIONS' === $_SERVER['REQUEST_METHOD'] ) {
					status_header( 200 );
					exit;
				}

				return $value;
			}
		);
	}

	public static function register_routes(): void {
		register_rest_route(
			'lead-capture/v1',
			'/submit',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( self::class, 'handle_submit' ),
				'permission_callback' => '__return_true',
				'args'                => self::get_endpoint_args(),
			)
		);
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	private static function get_endpoint_args(): array {
		return array(
			'first_name'    => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'last_name'     => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'email'         => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_email',
				'validate_callback' => static function ( $value ) {
					return is_email( $value ) ? true : new WP_Error( 'invalid_email', __( 'Invalid email address.', 'lead-capture' ) );
				},
			),
			'phone'         => array(
				'required'          => false,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'country'       => array(
				'required'          => false,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'date_of_birth' => array(
				'required'          => false,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'consent'       => array(
				'required'          => true,
				'type'              => 'boolean',
			),
			'source'        => array(
				'required'          => false,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'api',
			),
		);
	}

	public static function handle_submit( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$errors = self::validate_payload( $request );
		if ( ! empty( $errors ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'errors'  => $errors,
				),
				400
			);
		}

		$dob = $request->get_param( 'date_of_birth' );
		if ( $dob && ! self::is_valid_date( $dob ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'errors'  => array( 'date_of_birth' => __( 'Invalid date format. Use YYYY-MM-DD.', 'lead-capture' ) ),
				),
				400
			);
		}

		$id = Lead_Capture_Database::insert(
			array(
				'first_name'    => $request->get_param( 'first_name' ),
				'last_name'     => $request->get_param( 'last_name' ),
				'email'         => $request->get_param( 'email' ),
				'phone'         => $request->get_param( 'phone' ) ?: null,
				'country'       => $request->get_param( 'country' ) ?: null,
				'date_of_birth' => $dob ?: null,
				'consent'       => rest_sanitize_boolean( $request->get_param( 'consent' ) ) ? 1 : 0,
				'source'        => $request->get_param( 'source' ) ?: 'api',
			)
		);

		if ( ! $id ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Could not save submission.', 'lead-capture' ),
				),
				500
			);
		}

		do_action(
			'lead_capture_submitted',
			$id,
			array(
				'first_name'    => $request->get_param( 'first_name' ),
				'last_name'     => $request->get_param( 'last_name' ),
				'email'         => $request->get_param( 'email' ),
				'phone'         => $request->get_param( 'phone' ),
				'country'       => $request->get_param( 'country' ),
				'date_of_birth' => $dob,
				'source'        => $request->get_param( 'source' ),
			)
		);

		return new WP_REST_Response(
			array(
				'success' => true,
				'id'      => $id,
				'message' => __( 'Application submitted successfully.', 'lead-capture' ),
			),
			201
		);
	}

	/**
	 * @return array<string, string>
	 */
	private static function validate_payload( WP_REST_Request $request ): array {
		$errors = array();

		$first = trim( (string) $request->get_param( 'first_name' ) );
		if ( '' === $first || strlen( $first ) > 100 ) {
			$errors['first_name'] = __( 'First name is required (max 100 characters).', 'lead-capture' );
		}

		$last = trim( (string) $request->get_param( 'last_name' ) );
		if ( '' === $last || strlen( $last ) > 100 ) {
			$errors['last_name'] = __( 'Last name is required (max 100 characters).', 'lead-capture' );
		}

		$email = trim( (string) $request->get_param( 'email' ) );
		if ( ! is_email( $email ) ) {
			$errors['email'] = __( 'A valid email address is required.', 'lead-capture' );
		}

		$phone = trim( (string) $request->get_param( 'phone' ) );
		if ( '' !== $phone && ! preg_match( '/^[\d\s\-\+\(\)\.]{6,50}$/', $phone ) ) {
			$errors['phone'] = __( 'Phone number format is invalid.', 'lead-capture' );
		}

		$consent = rest_sanitize_boolean( $request->get_param( 'consent' ) );
		if ( ! $consent ) {
			$errors['consent'] = __( 'You must agree to the terms and privacy policy.', 'lead-capture' );
		}

		return $errors;
	}

	private static function is_valid_date( string $date ): bool {
		$dt = DateTime::createFromFormat( 'Y-m-d', $date );
		return $dt && $dt->format( 'Y-m-d' ) === $date;
	}
}
