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
		$qr_code_id = get_post_meta( $post->ID, '_QR_CODE', true );
		$qr_src = wp_get_attachment_image_src( $qr_code_id );
		error_log( print_r( $qr_src, true ));
		echo "<img src = '" . $qr_src[0] . "' >";
	}
}

add_action( 'submitpost_box', 'db_output_bridge_value', 10, 1 );

function db_create_qr_code( $ID, $post){
	if( $post->post_type === 'exhibit_item' && !get_post_meta( $post->ID, '_QR_CODE', true ) ){
		error_log( print_r( $post, true));
		global $wpdb;
	    $table_name = $wpdb->prefix . 'db_bridge';
	    $bridge = $wpdb->get_results( "SELECT bridge FROM {$table_name} WHERE post_id = {$post->ID}" );
	    $bridge = $bridge[0]->bridge;

		$parameters = array(
			'size' => '150x150',
			'data' => $bridge,
			);

		$url = "https://api.qrserver.com/v1/create-qr-code/?";
		$url = $url . http_build_query( $parameters);
		$response = wp_remote_get( $url );
		$img = wp_remote_retrieve_body($response);
		$temp_file_name =  $post->post_name . $post->ID .'.png';

		$handle = fopen( plugin_dir_path( __FILE__ ) . $temp_file_name, "w" );
		$success = fwrite( $handle, $img );
		fclose( $handle ); 
		$image_url = plugin_dir_url(  __FILE__ ) . $temp_file_name;
		$image = media_sideload_image( $image_url, $post->ID,'desc', 'src' );
		unlink( plugin_dir_path( __FILE__ ) . $temp_file_name );
		$media = get_attached_media( 'image' );
		$qr_attachment = array_pop( $media );
		add_post_meta( $post->ID, '_QR_CODE', $qr_attachment->ID, true );
		error_log( print_r( $media, true));
	}
}


add_action( 'publish_exhibit_item', 'db_create_qr_code', 10, 2 );






?>
