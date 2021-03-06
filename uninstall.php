<?php
/**
 * Uninstall Hotel custom post type data
 */

// Exit if uninstall not called from WordPress
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit;

// Delete Hotel URI meta key from all posts
delete_post_meta_by_key( '_tmpco_hotel_uri' );

// Delete Hotel custom post type posts
global $wpdb;
$wpdb->query( 'DELETE FROM ' . $wpdb->prefix.posts . ' WHERE post_type = "hotel";');

