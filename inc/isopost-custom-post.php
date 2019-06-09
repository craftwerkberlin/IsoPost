<?php

 
// Custom Post types
function isopost_register() {
    $labels = array(
        'name' => _x('IsoPost', 'post type general name'),
        'singular_name' => _x('IsoPost Item', 'post type singular name'),
        'add_new' => _x('Add New', 'isopost item'),
        'add_new_item' => __('Add New IsoPost Item'),
        'edit_item' => __('Edit IsoPost Item'),
        'new_item' => __('New IsoPost Item'),
        'view_item' => __('View IsoPost Item'),
        'search_items' => __('Search IsoPost Items'),
        'not_found' =>  __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => 4,
        'supports' => array('title','editor','thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-screenoptions',
        //'rewrite' => array( 'slug' => 'my-slug' ), // Change your IsoPost slug here. Go to Backend - Settings - Permalinks and save
    ); 
    register_post_type( 'isopost' , $args );
}
add_action('init', 'isopost_register');


// Post Categories
function create_isopost_taxonomies() {
    $labels = array(
        'name'              => _x( 'IsoPost Categories', 'taxonomy general name' ),
        'singular_name'     => _x( 'IsoPost Category', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Categories' ),
        'all_items'         => __( 'All Categories' ),
        'parent_item'       => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category:' ),
        'edit_item'         => __( 'Edit Category' ),
        'update_item'       => __( 'Update Category' ),
        'add_new_item'      => __( 'Add New Category' ),
        'new_item_name'     => __( 'New Category Name' ),
        'menu_name'         => __( 'Categories' ),
    );

    $args = array(
        'hierarchical'      => true, // Set this to 'false' for non-hierarchical taxonomy (like tags)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'categories' ),
    );

    register_taxonomy( 'isopost_categories', array( 'isopost' ), $args );
}
add_action( 'init', 'create_isopost_taxonomies', 0 );





// Use the Plugin Single Template
add_filter( 'single_template', 'isopost_post_type_template' );
function isopost_post_type_template($single_template) {
     global $post;

     if ($post->post_type == 'isopost' ) {
          $single_template = dirname( __FILE__ ) . '/single-isopost.php';
     }
     return $single_template;
  
}



// Use the Plugin Archive Template
add_filter( 'archive_template', 'isopost_archive_type_template' );
function isopost_archive_type_template($archive_template) {
     global $post;

     if ($post->post_type == 'isopost' ) {
          $archive_template = dirname( __FILE__ ) . '/archive-isopost.php';
     }
     return $archive_template;
  
}



// Show Posts in Author Page
add_filter( 'pre_get_posts', 'isopost_author_custom_post_types' );
function isopost_author_custom_post_types( $query ) {
  if( is_author() && empty( $query->query_vars['suppress_filters'] ) ) {
    $query->set( 'post_type', array(
     'post', 'isopost'
		));
	  return $query;
	}
}


