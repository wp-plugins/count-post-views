<?php
/*
Plugin Name: Count Post Views
Author: TuanVA
Author URI: http://tamhuyet.com
Plugin URI: http://tamhuyet.com/wpplugins/
Version: 1.0
Description: Count views if your Posts in wordpress. <br>Simple to emble to with simple code <strong>echo tamhuyet_count();</strong> or <strong>echo tamhuyet_count($post_id);</strong> with $post_id is id of post or page.
*/

global $wp_version;
$tb_name = "count_views_db";
$table_name = $wpdb->prefix .$tb_name;

if(version_compare($wp_version,'3.0.3','<')){
	exit("This plugin's support wp version 3.0.3 or higher. You need upgrade your wordpress version to use this plugin!");
}

function tamhuyet_create_db()
{
	global $wpdb;
	global $table_name;
    $sql = "CREATE TABLE $table_name (
      id int(11) NOT NULL AUTO_INCREMENT,
      item int(11) DEFAULT NULL,
      value int(11) NOT NULL DEFAULT 0,
      UNIQUE KEY id (id)
    );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

function tamhuyet_check_id($id)
{
	global $wpdb;
	global $table_name;
    $row = $wpdb->get_row($wpdb->prepare('SELECT id FROM '.$table_name.' WHERE item = %d',$id) );
    if($row->id != "")
    	return true;
    else return false;
}

function tamhuyet_insert_qb($id){
	global $wpdb;
	global $table_name;

	if(tamhuyet_check_id($id)){
		$views = $wpdb->get_row($wpdb->prepare('SELECT value FROM '.$table_name.' WHERE item = '.$id.''));
		$view = $views->value;
		$wpdb->update($table_name, array( 'value' => $view + 1), array('item' => $id ));
	} else{
		$wpdb->insert(
		        $table_name, 
		        array( 
		            'item' => $id, 
		            'value' => '1' 
		        ), 
		        array( 
		            '%d', 
		            '%d'
		        )
		    );
	}
}


function tamhuyet_count(){
	global $wpdb;
	global $table_name;
	global $wp_query; $postid = $wp_query->post->ID;
	tamhuyet_insert_qb($postid);

    $row = $wpdb->get_row($wpdb->prepare('SELECT value FROM '.$table_name.' WHERE item = '.$postid.''));
    return $row->value;
}

register_activation_hook( __FILE__,'tamhuyet_create_db');
?>