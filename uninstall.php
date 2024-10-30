<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option('card-flip-image-slideshow');
 
// for site options in Multisite
delete_site_option('card-flip-image-slideshow');

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}cardflip_slideshow");