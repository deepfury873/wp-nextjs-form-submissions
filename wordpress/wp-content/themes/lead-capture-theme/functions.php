<?php

defined( 'ABSPATH' ) || exit;

function lead_capture_theme_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
}
add_action( 'after_setup_theme', 'lead_capture_theme_setup' );

function lead_capture_enqueue_assets(): void {
	if ( ! is_page_template( 'template-application.php' ) ) {
		return;
	}

	$theme_uri = get_template_directory_uri();
	$version   = '1.0.0';

	wp_enqueue_style(
		'lead-capture-form',
		$theme_uri . '/assets/css/form.css',
		array(),
		$version
	);

	wp_enqueue_script(
		'lead-capture-form',
		$theme_uri . '/assets/js/form.js',
		array(),
		$version,
		true
	);

	wp_localize_script(
		'lead-capture-form',
		'leadCaptureConfig',
		array(
			'restUrl' => esc_url_raw( rest_url( 'lead-capture/v1/submit' ) ),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
			'i18n'    => array(
				'success'       => __( 'Application submitted successfully.', 'lead-capture-theme' ),
				'genericError'  => __( 'Something went wrong. Please try again.', 'lead-capture-theme' ),
				'required'      => __( 'This field is required.', 'lead-capture-theme' ),
				'invalidEmail'  => __( 'Please enter a valid email address.', 'lead-capture-theme' ),
				'invalidPhone'  => __( 'Please enter a valid phone number.', 'lead-capture-theme' ),
				'invalidDate'   => __( 'Please enter a valid date.', 'lead-capture-theme' ),
				'consentRequired' => __( 'You must agree to the terms and privacy policy.', 'lead-capture-theme' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'lead_capture_enqueue_assets' );
