<?php
/*
Plugin Name: FGC Users Management
Description: Plugin created for BC interview
Version: 1.0
Author: Francisco Calderón
Author URI: http://fg.calderon.it
License: GPL2
*/


//If this file is called directly, abort.
if (! defined('WPINC')){
	die;
}


function fgc_settings_page() {
	add_menu_page(
		'FGC Users Management',
		'FGC Users Management',
		'manage_options',
		'fgc-users-management',
		'fgc_list_users',
		'dashicons-welcome-widgets-menus',
		100
	);


	add_submenu_page(
		'fgc-users-management',
		__('Edit User', 'fgc'),
		__('Edit', 'fgc'),
		'manage_options',
		'fgc-users-management-edit',
		'fgc_edit_user'
	);

	add_submenu_page(
		'fgc-users-management',
		__('Deactivate User', 'fgc'),
		__('Deactivate', 'fgc'),
		'manage_options',
		'fgc-users-management-deactivate',
		'fgc_deactivate_user'
	);

}

add_action('admin_menu', 'fgc_settings_page');


define('FGC_URL', plugin_dir_url(__FILE__) );
define('FGC_PATH', plugin_dir_path(__FILE__) );


include( FGC_PATH.'includes/fgc-functions.php');

?>