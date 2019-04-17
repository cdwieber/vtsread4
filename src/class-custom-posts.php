<?php
/**
 * Created by PhpStorm.
 * User: chriswieber
 * Date: 2/26/18
 * Time: 1:05 PM
 */

namespace VTS;
class Custom_Posts {

	/**
	 * Custom_Posts constructor.
	 */
	function __construct() {
		add_action( 'init', array($this, 'listing_post_type'), 0 );

		add_action('admin_menu', array($this,'remove_submenus'), 999);
		add_filter('single_template', array($this, 'listing_single_template'));
	}

	/**
	 * Removes the unneeded submenus from the 'listing' CPT
	 */
	public function remove_submenus() {
		remove_submenu_page('vts_refresh', 'post-new.php?post_type=listing');
		remove_submenu_page('vts_refresh', 'edit-tags.php?taxonomy=category&amp;post_type=listing');
		remove_submenu_page('vts_refresh', 'edit-tags.php?taxonomy=post_tag&amp;post_type=listing');
		remove_submenu_page('vts_refresh', 'reorder-listing');
	}

	/**
	 * Check for presence of the supplied custom template, else fall back to default theme single.
	 * @param $single
	 *
	 * @return string
	 */
	function listing_single_template($single) {

		global $wp_query, $post;
		error_log('ACHOO');
		/* Checks for single template by post type */
		if ( $post->post_type == 'listing' ) {
			if ( file_exists( WP_PLUGIN_DIR . '/vtsread/inc/listing-single.php' ) ) {
				return WP_PLUGIN_DIR . '/vtsread/inc/listing-single.php';
			}
		}

		return $single;

	}

	/**
	 * Create listing CPT.
	 */
	public function listing_post_type() {

		$labels = array(
			'name'                  => _x( 'Listings', 'Post Type General Name', 'text_domain' ),
			'singular_name'         => _x( 'Listing', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'             => __( 'Listings', 'text_domain' ),
			'name_admin_bar'        => __( 'Listings', 'text_domain' ),
			'add_new'               => __( 'Add New', 'text_domain' ),
			'new_item'              => __( 'New Listing', 'text_domain' ),
			'edit_item'             => __( 'Edit Listing', 'text_domain' ),
			'update_item'           => __( 'Update Listing', 'text_domain' ),
			'view_item'             => __( 'View Listings', 'text_domain' ),
			'view_items'            => __( 'View Listings', 'text_domain' ),
			'search_items'          => __( 'Search Listings', 'text_domain' ),
			'not_found'             => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
			'featured_image'        => __( 'Featured Image', 'text_domain' ),
			'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
			'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
			'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
			'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
			'items_list'            => __( 'Items list', 'text_domain' ),
			'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
			'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
		);
		$args = array(
			'label'                 => __( 'Listing', 'text_domain' ),
			'description'           => __( 'Property Listings fed from VTS', 'text_domain' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor' ),
			'taxonomies'            => array( 'category', 'post_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'show_in_rest'          => true,
			'menu_icon'   => 'dashicons-building',
		);
		register_post_type( 'listing', $args );

	}
}