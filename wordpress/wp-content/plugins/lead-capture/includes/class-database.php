<?php

defined( 'ABSPATH' ) || exit;

class Lead_Capture_Database {

	public const TABLE_SUFFIX = 'lead_submissions';

	public static function table_name(): string {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_SUFFIX;
	}

	public static function activate(): void {
		self::create_table();
	}

	public static function deactivate(): void {
		// Table retained on deactivation per typical WP practice.
	}

	public static function maybe_upgrade(): void {
		$installed = get_option( 'lead_capture_db_version', '' );
		if ( $installed !== LEAD_CAPTURE_VERSION ) {
			self::create_table();
			update_option( 'lead_capture_db_version', LEAD_CAPTURE_VERSION );
		}
	}

	public static function create_table(): void {
		global $wpdb;

		$table           = self::table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			first_name varchar(100) NOT NULL,
			last_name varchar(100) NOT NULL,
			email varchar(255) NOT NULL,
			phone varchar(50) DEFAULT NULL,
			country varchar(100) DEFAULT NULL,
			date_of_birth date DEFAULT NULL,
			consent tinyint(1) NOT NULL DEFAULT 0,
			source varchar(20) NOT NULL DEFAULT 'wordpress',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY email (email),
			KEY created_at (created_at)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * @param array<string, mixed> $data
	 * @return int|false Insert ID or false on failure.
	 */
	public static function insert( array $data ) {
		global $wpdb;

		$result = $wpdb->insert(
			self::table_name(),
			array(
				'first_name'    => $data['first_name'],
				'last_name'     => $data['last_name'],
				'email'         => $data['email'],
				'phone'         => $data['phone'] ?? null,
				'country'       => $data['country'] ?? null,
				'date_of_birth' => $data['date_of_birth'] ?? null,
				'consent'       => (int) ( $data['consent'] ?? 0 ),
				'source'        => $data['source'] ?? 'wordpress',
				'created_at'    => current_time( 'mysql', true ),
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
		);

		return false === $result ? false : (int) $wpdb->insert_id;
	}

	/**
	 * @return array<int, object>
	 */
	public static function get_all( int $limit = 100, int $offset = 0 ): array {
		global $wpdb;

		$table = self::table_name();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
				$limit,
				$offset
			)
		);

		return is_array( $rows ) ? $rows : array();
	}

	public static function count(): int {
		global $wpdb;
		$table = self::table_name();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
	}
}
