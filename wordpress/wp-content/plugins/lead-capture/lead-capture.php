<?php
/**
 * Plugin Name: Lead Capture
 * Description: Stores lead-capture form submissions and exposes a REST API.
 * Version: 1.0.0
 * Author: Assessment
 * Text Domain: lead-capture
 */

defined( 'ABSPATH' ) || exit;

define( 'LEAD_CAPTURE_VERSION', '1.0.0' );
define( 'LEAD_CAPTURE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LEAD_CAPTURE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once LEAD_CAPTURE_PLUGIN_DIR . 'includes/class-database.php';
require_once LEAD_CAPTURE_PLUGIN_DIR . 'includes/class-rest-api.php';
require_once LEAD_CAPTURE_PLUGIN_DIR . 'includes/class-admin.php';

final class Lead_Capture_Plugin {

	public static function init(): void {
		register_activation_hook( __FILE__, array( 'Lead_Capture_Database', 'activate' ) );
		register_deactivation_hook( __FILE__, array( 'Lead_Capture_Database', 'deactivate' ) );

		add_action( 'plugins_loaded', array( self::class, 'boot' ) );
	}

	public static function boot(): void {
		Lead_Capture_Database::maybe_upgrade();
		Lead_Capture_REST_API::register();
		Lead_Capture_Admin::register();
	}
}

Lead_Capture_Plugin::init();
