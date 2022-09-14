<?php 
global $ns_db_version;

function setup_db_newsletter() {
	global $wpdb;
	global $ns_db_version;

	$table_name = $wpdb->prefix . 'newsletter';
	$charset_collate = $wpdb->get_charset_collate();	

	$sql = "CREATE TABLE $table_name (
				id_newsletter int(11) NOT NULL,
				subject varchar(255) DEFAULT '' NOT NULL,
				content text NOT NULL,
				type varchar(1) DEFAULT '' NOT NULL,
				created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				status char(12),
				menu_order int(11),
				author bigint(20),
				author_update bigint(20),
				PRIMARY KEY  (id_newsletter)
			) $charset_collate;";
			
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	add_option( 'ns_db_version', $ns_db_version );
}

function setup_db_member_newsletter() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'newsletter_member';
	$charset_collate = $wpdb->get_charset_collate();
	
	
	$sql = "CREATE TABLE $table_name (
				id_member int(11) NOT NULL,
				email varchar(255),
				first_name varchar(255),
				last_name varchar(255),
				sex char(1),
				status char(12),
				country varchar(255),
				type_member char(20),
				created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				menu_order int(11),
				author bigint(20),
				author_update bigint(20),
				PRIMARY KEY  (id_member)
			) $charset_collate;";		

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}



function setup_db_schedule_newsletter(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'newsletter_schedule';
	$charset_collate = $wpdb->get_charset_collate();


	$sql = "CREATE TABLE $table_name (
			id_schedule int(11) NOT NULL,
			id_newsletter int(11),
			start datetime  DEFAULT '0000-00-00 00:00:00' NOT NULL,
			type_member char(20),
			status char(12),
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			menu_order int(11),
			author bigint(20),
			author_update bigint(20),
			PRIMARY KEY (id_schedule)
			) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );		
}

function setup_db_record_newsletter(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'newsletter_record';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
			id_record int(11) NOT NULL,
			id_schedule int(11),
			id_member int(11),
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY (id_record)
			) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );		
}

?>