<?php 
/*
 * Plugin Name:       Checkout Field Builder (Checkout Field Editor & Manager) for WooCommerce
 * Plugin URI:        https://devrypt.com/checkout-field-builder-checkout-manager-for-woocommerce/
 * Description:       Customize WooCommerce checkout fields(Add, Edit, Delete and re-arrange fields).
 * Version:           1.0.5
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Devrypt
 * Author URI:        https://devrypt.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       checkout-field-builder-checkout-manager-for-woocommerce
 * Domain Path:       /languages
 * Requires Plugins: woocommerce
 */

/**
 * @package checkout-field-builder-checkout-manager-for-woocommerce
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DCFEM_FILE', __FILE__ );
define( 'DCFEM_DIR', plugin_dir_path( DCFEM_FILE ) );
define( 'DCFEM_URL', plugins_url( '/', DCFEM_FILE ) );
define( 'DCFEM_ASSETS', plugins_url( '/assets', DCFEM_FILE ) );
define( 'DCFEM_VER', '1.0.4' );
define( 'DCFEM_ROOT', dirname( plugin_basename( __FILE__ ) ) );
define( 'DCFEM_ASSET_VER', get_option( '__dcfem_asset_version', DCFEM_VER ) );
define( 'DCFEM_ADMIN', DCFEM_DIR . 'admin/' );	
define( 'DCFEM_ADMIN_URL', plugins_url( '/', __FILE__ ) . 'admin/' );
define( 'DCFEM_PLUGIN_NAME', 'Checkout Field Builder' );



function dcfem_file_init() {
	
	if ( is_admin() ) {
		require_once DCFEM_ADMIN . 'class-dashboard-settings.php';
        require_once DCFEM_ADMIN . 'includes/class-settings.php';
	}
    require_once DCFEM_DIR . 'includes/class-dcfem.php';
}
add_action( 'plugins_loaded', 'dcfem_file_init' );

// plugin activation 
register_activation_hook(__FILE__, 'dcfem_plugin_activate');
add_action('admin_init', 'dcfem_plugin_redirect');

function dcfem_plugin_activate() {
    add_option('dcfem_do_activation_redirect', true);
}

// plugin redirect 
function dcfem_plugin_redirect() {
    if (get_option('dcfem_do_activation_redirect', false)) {
        delete_option('dcfem_do_activation_redirect');
        wp_redirect(admin_url('admin.php?page=dcfem-settings'));
    }
}

/**
 * Loads the translation files.
 *
 * @since  1.0.0
 */
add_action( 'init', 'dcfem_i18n' );
function dcfem_i18n() {
    load_plugin_textdomain( 'checkout-field-builder-checkout-manager-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}