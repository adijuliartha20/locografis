<?php 
/**
 * Plugin Name: Locografis Newsletter
 * Plugin URI: http://www.locografis.com/
 * Description: Plugins newsletter Locografis
 * Version: 1.0
 * Author URI: http://www.locografis.com/
 * Author : Adi Juliartha
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once('create_table.php');
require_once('newsletter.php');
require_once('newsletter_member.php');
require_once('newsletter_schedule.php');
//require_once('newsletter_cron.php');






//Hook table harus di class pertama yang dipanggil
register_activation_hook( __FILE__, 'setup_db_newsletter' );
register_activation_hook( __FILE__, 'setup_db_member_newsletter' );
register_activation_hook( __FILE__, 'setup_db_schedule_newsletter' );
register_activation_hook( __FILE__, 'setup_db_record_newsletter' );
?>