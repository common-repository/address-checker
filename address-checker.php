<?php
/**
 * The Address-Checker bootstrap file
 *
 *
 * Address-Checker validates the addressfields of Woocommerce
 *
 * @link              http://cherement.nl/demo
 * @since             1.0.0
 * @package           adress-checker
 *
 * @wordpress-plugin
 * Plugin Name:       Address Checker
 * Plugin URI:        http://cherement.nl/demo
 * Description:       This plugin validates the address fields on authenticity in Woocommerce on the checkout page. This requireds a Google API Key.
 * Version:           1.0.7
 * Author:            schaapkabap
 * Author URI:        http://schaapkabap.nl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       address-checker
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-address-checker-activator.php
 */
function activateAddressChecker(){
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-address-checker-activator.php';
    AddressCheckerActivator::activate();
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-address-checker-deactivator.php
 */
function deactivateAddressChecker() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-address-checker-deactivator.php';
    AddressCheckerDeactivator::deactivate();
}
register_activation_hook( __FILE__, 'activateAddressChecker' );
register_deactivation_hook( __FILE__, 'deactivateAddressChecker' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-address-checker.php';


/*
 * Defines te links on the plugin page
 */


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered with hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_AdressChecker() {
    $plugin = new AddressChecker();
    $plugin->run();

}

if (function_exists('run_AdressChecker')){

   run_AdressChecker();
}
add_filter( 'plugin_action_links', 'wac_add_links_menu', 10, 5 );
function wac_add_links_menu( $actions, $plugin_file )
{
    static $plugin;

    if (!isset($plugin))
        $plugin = plugin_basename(__FILE__);
    if ($plugin == $plugin_file) {

        $settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=shipping&section=address_checker">' . __('Settings', 'General') . '</a>');
        $actions = array_merge($settings, $actions);


    }

    return $actions;
}

?>
