<?php
/**
Plugin Name: Museum Mobile Interpretive App
Plugin URI: http://deeptiboddapati.com
Description: Adds the capability of turning your WordPress install into a mobile backend
Version: .1
Author: Deepti Boddapati
Author URI:  http://deeptiboddapati.com
Text Domain: db
*/


add_filter( 'wp_insert_post_data', 'db_remove_html', 100, 2 );
/*
Description: Removes html from a string and returns the modified string.

@param: $content - string

 */

function db_remove_html( $data , $postarr ) {
	$data['post_content'] = strip_tags( $data['post_content'] );
	return $data;
}


if ( ! function_exists( 'db_register_tours_taxonomy' ) ) {

// Register Custom Taxonomy for tours
function db_register_tours_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Tours', 'Taxonomy General Name', 'db' ),
		'singular_name'              => _x( 'Tour', 'Taxonomy Singular Name', 'db' ),
		'menu_name'                  => __( 'Tour', 'db' ),
		'all_items'                  => __( 'All Tours', 'db' ),
		'parent_item'                => __( 'Parent Tour', 'db' ),
		'parent_item_colon'          => __( 'Parent Tour:', 'db' ),
		'new_item_name'              => __( 'New Tour Name', 'db' ),
		'add_new_item'               => __( 'Add New Tour', 'db' ),
		'edit_item'                  => __( 'Edit Tour', 'db' ),
		'update_item'                => __( 'Update Tour', 'db' ),
		'view_item'                  => __( 'View Tour', 'db' ),
		'separate_items_with_commas' => __( 'Separate Tours with commas', 'db' ),
		'add_or_remove_items'        => __( 'Add or remove Tours', 'db' ),
		'choose_from_most_used'      => __( 'Choose from the most used Tours', 'db' ),
		'popular_items'              => __( 'Popular Tours', 'db' ),
		'search_items'               => __( 'Search Tours', 'db' ),
		'not_found'                  => __( 'Not Found', 'db' ),
		'no_terms'                   => __( 'No Tours', 'db' ),
		'items_list'                 => __( 'Tours list', 'db' ),
		'items_list_navigation'      => __( 'Tours list navigation', 'db' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'query_var'                  => 'tour',
		'show_in_rest'               => true,
	);
	register_taxonomy( 'tour', array( 'exhibit_item' ), $args );

}
add_action( 'init', 'db_register_tours_taxonomy', 0 );

}


if ( ! function_exists('db_add_exhibit_item_post_type') ) {

// Register Custom Post Type
function db_add_exhibit_item_post_type() {

	$labels = array(
		'name'                  => _x( 'Exhibit Items', 'Post Type General Name', 'db' ),
		'singular_name'         => _x( 'Exhibit Item', 'Post Type Singular Name', 'db' ),
		'menu_name'             => __( 'Exhibit Items', 'db' ),
		'name_admin_bar'        => __( 'Exhibit Item', 'db' ),
		'archives'              => __( 'Exhibit Item Archives', 'db' ),
		'attributes'            => __( 'Exhibit Item Attributes', 'db' ),
		'parent_item_colon'     => __( 'Parent Exhibit Item:', 'db' ),
		'all_items'             => __( 'All Exhibit Items', 'db' ),
		'add_new_item'          => __( 'Add New Exhibit Item', 'db' ),
		'add_new'               => __( 'Add New', 'db' ),
		'new_item'              => __( 'New Exhibit Item', 'db' ),
		'edit_item'             => __( 'Edit Exhibit Item', 'db' ),
		'update_item'           => __( 'Update Exhibit Item', 'db' ),
		'view_item'             => __( 'View Exhibit Item', 'db' ),
		'view_items'            => __( 'View Exhibit Item', 'db' ),
		'search_items'          => __( 'Search Exhibit Item', 'db' ),
		'not_found'             => __( 'Not found', 'db' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'db' ),
		'featured_image'        => __( 'Featured Image', 'db' ),
		'set_featured_image'    => __( 'Set featured image', 'db' ),
		'remove_featured_image' => __( 'Remove featured image', 'db' ),
		'use_featured_image'    => __( 'Use as featured image', 'db' ),
		'insert_into_item'      => __( 'Insert into Exhibit Item', 'db' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Exhibit Item', 'db' ),
		'items_list'            => __( 'Exhibit Items list', 'db' ),
		'items_list_navigation' => __( 'Exhibit Items list navigation', 'db' ),
		'filter_items_list'     => __( 'Filter Exhibit Items list', 'db' ),
	);
	$args = array(
		'label'                 => __( 'Exhibit Item', 'db' ),
		'description'           => __( 'Items shown in various exhibits in the museum', 'db' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', ),
		'taxonomies'            => array( 'tour' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-post-status',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'show_in_rest'          => true,
	);
	register_post_type( 'exhibit_item', $args );

}
add_action( 'init', 'db_add_exhibit_item_post_type', 0 );

}



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
	    error_log( print_r( $bridge, true));
	
		echo "<h3> Bridge ID - " . $bridge . "</h3>";
	}
}

add_action( 'edit_form_after_title', 'db_output_bridge_value', 10, 1 );

