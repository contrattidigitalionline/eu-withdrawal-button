<?php
/**
 * Plugin Name: EU Withdrawal Button
 * Plugin URI:  https://example.com/eu-withdrawal-button
 * Description: Implements the EU mandatory withdrawal button as required by Directive (EU) 2023/2673, effective 19 June 2026. Adds a compliant withdrawal function to WooCommerce order pages with email notifications and a full audit log.
 * Version:     1.0.0
 * Author:      Your Name
 * License:     GPL-2.0+
 * Text Domain: eu-withdrawal-button
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * WC requires at least: 7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'EUWB_VERSION', '1.0.0' );
define( 'EUWB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EUWB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EUWB_WITHDRAWAL_WINDOW_DAYS', 14 );

// Autoload includes
require_once EUWB_PLUGIN_DIR . 'includes/class-euwb-install.php';
require_once EUWB_PLUGIN_DIR . 'includes/class-euwb-withdrawal.php';
require_once EUWB_PLUGIN_DIR . 'includes/class-euwb-emails.php';
require_once EUWB_PLUGIN_DIR . 'includes/class-euwb-admin.php';
require_once EUWB_PLUGIN_DIR . 'includes/class-euwb-frontend.php';

register_activation_hook( __FILE__, array( 'EUWB_Install', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'EUWB_Install', 'deactivate' ) );

add_action( 'plugins_loaded', 'euwb_init' );

function euwb_init() {
    load_plugin_textdomain( 'eu-withdrawal-button', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'euwb_woocommerce_missing_notice' );
        return;
    }

    new EUWB_Frontend();
    new EUWB_Admin();
}

function euwb_woocommerce_missing_notice() {
    echo '<div class="notice notice-error"><p><strong>EU Withdrawal Button:</strong> WooCommerce deve essere attivo per usare questo plugin.</p></div>';
}
