<?php
/**
 * Plugin Name: View Social Feeds
 * Description: Eye-catching Elementor Creative Products Grid, presenting a captivating product showcase with over 20 unique designs.
 * Plugin URI:  https://bestwpdeveloper.com/view-social-feeds
 * Version:     1.0
 * Author:      Best WP Developer
 * Author URI:  https://bestwpdeveloper.com/
 * Text Domain: view-social-feeds
 * Elementor tested up to: 3.19.0
 * Elementor Pro tested up to: 3.19.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once ( plugin_dir_path(__FILE__) ) . '/includes/requires-check.php';

final class VSFED_Products_Tiles{

	const VERSION = '1.0';

	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

	const MINIMUM_PHP_VERSION = '7.0';

	public function __construct() {
		// Load translation
		add_action( 'vsfed_init', array( $this, 'vsfed_loaded_textdomain' ) );
		// vsfed_init Plugin
		add_action( 'plugins_loaded', array( $this, 'vsfed_init' ) );
		// For woocommerce install check
		if ( ! did_action( 'woocommerce/loaded' ) ) {
			add_action( 'admin_notices', 'vsfed_WooCommerce_register_required_plugins' );
			return;
		}
	}

	public function vsfed_loaded_textdomain() {
		load_plugin_textdomain( 'view-social-feeds' );
	}

	public function vsfed_init() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', 'vsfed_products_register_required_plugins');
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'vsfed_admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'vsfed_admin_notice_minimum_php_version' ) );
			return;
		}

		// Once we get here, We have passed all validation checks so we can safely include our plugin
		require_once( 'vsfed_plugin_boots.php' );
	}

	public function vsfed_admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'view-social-feeds' ),
			'<strong>' . esc_html__( 'View Social Feeds', 'view-social-feeds' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'view-social-feeds' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('%1$s', 'view-social-feeds') . '</p></div>', $message );
	}

	public function vsfed_admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'view-social-feeds' ),
			'<strong>' . esc_html__( 'View Social Feeds', 'view-social-feeds' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'view-social-feeds' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('%1$s', 'view-social-feeds') . '</p></div>', $message );
	}
}

// Instantiate view-social-feeds.
new VSFED_Products_Tiles();
remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );