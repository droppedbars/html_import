<?php
require_once( dirname( __FILE__ ) . '/admin/includes/HtmlImportSettings.php' );

/**
 * Fired when the plugin is uninstalled.
 *
 * @package   HTMLImportPlugin
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

// If uninstall not called from WordPress, then exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = new \html_import\admin\HtmlImportSettings();
$settings->deleteFromDB();
