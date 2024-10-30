<?php
/*
Plugin Name: Card flip image slideshow
Plugin URI: http://www.gopiplus.com/work/2019/12/15/card-flip-image-slideshow-wordpress-plugin/
Description: Card flip image slideshow looks flipping card in the page. It uses hinge animation.
Author: Gopi Ramasamy
Version: 1.5
Author URI: http://www.gopiplus.com/work/about/
Donate link: http://www.gopiplus.com/work/2019/12/15/card-flip-image-slideshow-wordpress-plugin/
Tags: plugin, widget, image, slideshow, gallery
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: card-flip-image-slideshow
Domain Path: /languages
*/

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
	die('You are not allowed to call this page directly.');
}

if(!defined('CARDFLIP_DIR')) 
	define('CARDFLIP_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

if ( ! defined( 'CARDFLIP_ADMIN_URL' ) )
	define( 'CARDFLIP_ADMIN_URL', admin_url() . 'options-general.php?page=card-flip-image-slideshow' );

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'cardflip-register.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'cardflip-query.php');

function cardflip_textdomain() {
	  load_plugin_textdomain( 'card-flip-image-slideshow', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

//add_action("plugins_loaded", "cardflip_init");

add_shortcode( 'cardflip', array( 'cardflip_cls_shortcode', 'cardflip_shortcode' ) );

add_action('wp_enqueue_scripts', array('cardflip_cls_registerhook', 'cardflip_frontscripts'));
add_action('plugins_loaded', 'cardflip_textdomain');
add_action('widgets_init', array('cardflip_cls_registerhook', 'cardflip_widgetloading'));
add_action('admin_enqueue_scripts', array('cardflip_cls_registerhook', 'cardflip_adminscripts'));
add_action('admin_menu', array('cardflip_cls_registerhook', 'cardflip_addtomenu'));

register_activation_hook(CARDFLIP_DIR . 'card-flip-image-slideshow.php', array('cardflip_cls_registerhook', 'cardflip_activation'));
register_deactivation_hook(CARDFLIP_DIR . 'card-flip-image-slideshow.php', array('cardflip_cls_registerhook', 'cardflip_deactivation'));
?>