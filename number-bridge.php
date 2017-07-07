<?php 

function db_register_custom_table_for_bridge() {
     global $wpdb;
     $table_name = $wpdb->prefix . 'db_bridge';
     $wpdb_collate = $wpdb->collate;
     $sql =
         "CREATE TABLE {$table_name} (
         bridge mediumint(8) unsigned NOT NULL auto_increment ,
         post_id mediumint(8) unsigned NOT NULL,
         PRIMARY KEY  (bridge),
         UNIQUE KEY post_id (post_id)
         )
         COLLATE {$wpdb_collate}";
 
     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
     dbDelta( $sql );
 }

register_activation_hook( __FILE__, 'db_register_custom_table_for_bridge' );

function db_set_bridge_value( $ID, $post ) {
	global $wpdb;
    $table_name = $wpdb->prefix . 'db_bridge';
	$wpdb->insert( 
	$table_name, 
		array( 		
			'post_id' => $ID, 
		) 
	);

}

add_action( 'auto-draft_exhibit_item', 'db_set_bridge_value', 10, 2 );

function db_output_bridge_value( $post){
	if( $post->post_type === 'exhibit_item' ){
		global $wpdb;
	    $table_name = $wpdb->prefix . 'db_bridge';
	    $bridge = $wpdb->get_results( "SELECT bridge FROM {$table_name} WHERE post_id = {$post->ID}" );
	    $bridge = $bridge[0]->bridge;	
		echo "<h3> Bridge ID - " . $bridge . "</h3>";
	}
}

add_action( 'edit_form_after_title', 'db_output_bridge_value', 10, 1 );

/*
You can even output the bridge IDs to a custom column on the all posts screen.
https://ryanbenhase.com/how-to-add-custom-columns-to-the-all-posts-screen-or-your-custom-post-type-in-wordpress/
 */

?>
