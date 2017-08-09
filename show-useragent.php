<?php
/*
Plugin Name: Show UserAgent
Plugin URI: https://zlz.im/show-useragent/
Description: Show visitors' IP to Country Flag, Web Browser, Operating System icons on WordPreess comment list.
Version: 1.0.9
Author: HzlzH
Author URI: https://zlz.im/
*/

if (!defined('WP_CONTENT_DIR')) {
	define( 'WP_CONTENT_DIR', ABSPATH.'wp-content');
}
if (!defined('WP_CONTENT_URL')) {
	define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
}
if (!defined('WP_PLUGIN_DIR')) {
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
}
if (!defined('WP_PLUGIN_URL')) {
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
}

function CID_init() {
	$CID_options = array();
	$CID_options['flag_icons_url'] = WP_PLUGIN_URL . "/show-useragent/flags";
	$CID_options['flag_template'] = '<span class="country-flag"><img src="%IMAGE_BASE%/%COUNTRY_CODE%.png" title="%COUNTRY_NAME%" alt="%COUNTRY_NAME%" /> %COUNTRY_NAME%</span> ';
	$CID_options['WB_OS_icons_url'] = WP_PLUGIN_URL . "/show-useragent/browsers";
	$CID_options['WB_OS_template'] = '<span class="WB-OS"><img src="%IMAGE_BASE%/%BROWSER_CODE%.png" title="%BROWSER_NAME%" alt="%BROWSER_NAME%" /> %BROWSER_NAME% %BROWSER_VERSION% <img src="%IMAGE_BASE%/%OS_CODE%.png" title="%OS_NAME%" alt="%OS_NAME%" /> %OS_NAME% %OS_VERSION%</span>';
	$CID_options['auto_display_flag'] = 0;
	$CID_options['auto_display_WB_OS'] = 0;	
	add_option('CID_options', $CID_options, 'Show-UserAgent Options');
}
add_action('activate_show-useragent/show-useragent.php', 'CID_init');

function CID_options_page() {
	add_options_page('Show-UserAgent Options', 'Show-UserAgent', 10, 'show-useragent/options.php');
}
add_action('admin_menu', 'CID_options_page');

function CID_css() {
	echo "\n".'<!-- Generated By Show-UserAgent 1.0.8 - http://zlz.im/show-useragent/ -->'."\n";
	if(@file_exists(TEMPLATEPATH.'/show-useragent.css')) {
		echo '<link rel="stylesheet" href="'.get_stylesheet_directory_uri().'/show-useragent.css" type="text/css" media="screen" />'."\n";	
	} else {
		echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/show-useragent/show-useragent.css" type="text/css" media="screen" />'."\n";
	}
}
add_action('wp_head', 'CID_css');

$CID_options = get_option('CID_options');

require(dirname(__FILE__).'/country.php');
require(dirname(__FILE__).'/browser.php');

if ($CID_options['auto_display_flag'] == 1 || ($CID_options['auto_display_flag'] == 2 && !is_admin()))
	add_filter('get_comment_author_link','CID_auto_display_flag'); 

if ($CID_options['auto_display_WB_OS'] == 1 || ($CID_options['auto_display_WB_OS'] == 2 && !is_admin()))
	add_filter('get_comment_author_link','CID_auto_display_WB_OS');
	
function CID_auto_display_flag($link) {
	global $comment;
	return $link . ' ' . CID_get_flag_without_template($comment->comment_author_IP,true,false,'','');
}

function CID_auto_display_WB_OS($link) {
	global $comment;
	return $link . ' ' . CID_browser_string_without_template($comment->comment_agent,true,false,'','');
}
?>