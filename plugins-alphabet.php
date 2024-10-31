<?php
/**
 * Plugin name: Plugins Alphabet
 * Author: Julien Maury
 * Description: Adds an alphabet index on plugins page
 * Version: 0.75
 */
if ( ! defined( 'DB_USER' ) ) {
    die( '~tryin~' );
}

if ( ! is_admin() ) {
	return false;
}

define( 'SP_BYALPHA_DIR_PATH', plugin_dir_path( __FILE__ ) );

require_once( SP_BYALPHA_DIR_PATH . 'classes/admin/admin.php' );

add_action( 'admin_init', 'spby_alpha_load_plugin' );
/**
 * Load i18n files
 * @author Julien Maury
 */
function spby_alpha_load_plugin() {
	load_plugin_textdomain( 'spby-alpha', false, basename( dirname( __FILE__ ) ) . '/languages' );
	$main = new SP_ByAlpha\Admin\Admin();
	$main->hooks();
}

