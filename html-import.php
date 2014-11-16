<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   HTMLImportPlugin
 * @author    Patrick Mauro <patrick@mauro.ca>
 * @license   GPL-2.0+
 * @link      http://patrick.mauro.ca
 * @copyright 2014 Patrick Mauro
 *
 * @wordpress-plugin
 * Plugin Name:       Website Importer
 * Description:       Imports a full website provided an index file from either a Flare export, or a specialized XML index.
 * Version:           1.0.0
 * Author:            Patrick Mauro
 * Author URI:        http://patrick.mauro.ca
 * Text Domain:       plugin-name-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/droppedbars/html_import
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/HTMLImportPlugin.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'HTMLImportPlugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'HTMLImportPlugin', 'deactivate' ) );

/*
 */
add_action( 'plugins_loaded', array( 'HTMLImportPlugin', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/HTMLImportPluginAdmin.php' );
	add_action( 'plugins_loaded', array( 'HTMLImportPluginAdmin', 'get_instance' ) );

}
