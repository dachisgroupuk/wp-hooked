<?php
/*
 * Uninstall plugin when deleted
 * @since 1.7
 */
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();
global $wpdb;

### Delete tables
$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."wpv_voting_meta");
$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."wpv_voting");

### Delete options
delete_option('wpv-voting-onoff');
delete_option('wpv-allow-author-vote');
delete_option('wpv-voted-custom-txt');
delete_option('wpv-vote-btn-custom-txt');
delete_option('wpv-custom-css');
delete_option('wpv-voting-alert-msg');
delete_option('wpv-allow-public-vote');
delete_option('wpv-voting-db-version');
?>