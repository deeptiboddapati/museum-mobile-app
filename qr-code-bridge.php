<?php 
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
		echo "<img src = '" . $qr_src[0] . "' >";
	}
}

add_action( 'submitpost_box', 'db_output_bridge_value', 10, 1 );

function db_create_qr_code( $ID, $post){
	require_once(ABSPATH . 'wp-admin/includes/media.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	if( $post->post_type === 'exhibit_item' && !get_post_meta( $post->ID, '_QR_CODE', true ) ){
		global $wpdb;
	    $table_name = $wpdb->prefix . 'db_bridge';
	    $bridge = $wpdb->get_results( "SELECT bridge FROM {$table_name} WHERE post_id = {$post->ID}" );
	    $bridge = $bridge[0]->bridge;

		$parameters = array(
			'size' => '150x150',
			'data' => $bridge,
			);
		//save a temp version
		$url = "https://api.qrserver.com/v1/create-qr-code/?";
		$url = $url . http_build_query( $parameters);
		$response = wp_remote_get( $url );
		$img = wp_remote_retrieve_body($response);
		$temp_file_name =  $post->post_name . $post->ID .'.png';

		$handle = fopen( plugin_dir_path( __FILE__ ) . $temp_file_name, "w" );
		$success = fwrite( $handle, $img );
		fclose( $handle ); 
		$image_url = plugin_dir_url(  __FILE__ ) . $temp_file_name;

		//move the file to attachments
		$image_id = media_sideload_image( $image_url, $post->ID,'desc', 'src','id' );
		//delete the temp file
		unlink( plugin_dir_path( __FILE__ ) . $temp_file_name );
		//save image id to post meta.
		//and add an endpoint that outputs this!
		if( $image_id ){
			add_post_meta( $post->ID, '_QR_CODE', $image, true );
		}
	}
}


add_action( 'publish_exhibit_item', 'db_create_qr_code', 10, 2 );
?>
