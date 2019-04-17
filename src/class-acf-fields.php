<?php
/**
 * Created by PhpStorm.
 * User: chriswieber
 * Date: 2/26/18
 * Time: 1:05 PM
 */

namespace VTS;


class ACF_Fields {
	/**
	 * ACF_Fields constructor.
	 * This is simply to separate out the creation of custom ACF fields
	 */
	public function __construct() {

		//add_action( 'pre_get_posts', array( $this, 'custom_columns_sortable_by_name'), 1 );
		add_action ( 'manage_listing_posts_custom_column', array($this, 'listing_custom_column'), 10, 2 );

		add_filter ( 'manage_listing_posts_columns', array($this, 'add_acf_columns') );
		add_filter('manage_edit-listing_sortable_columns', array($this, 'custom_columns_sortable') );

		$this->define_acf_fields();

	}

	/**
	 * Add additional ACF columns to the listings page
	 * @param $columns
	 *
	 * @return array
	 */
	public function add_acf_columns ($columns) {

		return array_merge ( $columns, array (
			'state' => __ ( 'State' ),
			'internal_code'   => __ ( 'Internal Code' )
		) );

	}

	/**
	 * Set up custom admin columns
	 * @param $column
	 * @param $post_id
	 */
	public function listing_custom_column ( $column, $post_id ) {

		switch ( $column ) {
			case 'state':
				echo get_post_meta ( $post_id, 'state', true );
				break;
			case 'internal_code':
				echo get_post_meta ( $post_id, 'internal_code', true );
				break;
		}

	}


	/**
	 * Register new columns as sortable
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function custom_columns_sortable($columns) {

		$columns['state'] = 'state';
		$columns['internal_code'] = 'internal_code';

		return $columns;

	}


	/**
	 * Makes the custom ACF columns sortable
	 * @param $query
	 */
	public function custom_columns_sortable_by_name( $query ) {

		if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {

			switch( $orderby ) {
				case 'internal_code':
					$query->set( 'meta_key', 'internal_code' );
					$query->set( 'orderby', 'meta_value' );
					break;
				case 'state':
					$query->set( 'meta_key', 'state' );
					$query->set( 'orderby', 'meta_value' );
			}
		}
	}

	/**
	 * Define Advanced Custom Fields
	 */
	public function define_acf_fields() {
		if( function_exists('acf_add_local_field_group') ):

			acf_add_local_field_group(array(
				'key' => 'group_5a4187742e665',
				'title' => 'VTS Fields',
				'fields' => array(
					array(
						'key' => 'field_5a739146c0c0d',
						'label' => 'Floor Plan PDF',
						'name' => 'floor_plan_pdf',
						'type' => 'file',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'return_format' => 'url',
						'library' => 'uploadedTo',
						'min_size' => '',
						'max_size' => '',
						'mime_types' => '',
					),
					array(
						'key' => 'field_5a7209a900412',
						'label' => 'Internal Code',
						'name' => 'internal_code',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5a41877c499fd',
						'label' => 'Images',
						'name' => 'images',
						'type' => 'gallery',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'min' => '',
						'max' => '',
						'insert' => 'append',
						'library' => 'uploadedTo',
						'min_width' => '',
						'min_height' => '',
						'min_size' => '',
						'max_width' => '',
						'max_height' => '',
						'max_size' => '',
						'mime_types' => '',
					),
					array(
						'key' => 'field_5a4187ab499fe',
						'label' => 'Space Available',
						'name' => 'space_available',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => 0,
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5a418861499ff',
						'label' => 'Total Space',
						'name' => 'space_total',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5a41889d49a00',
						'label' => 'Street Address',
						'name' => 'street_address',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5a4188c549a01',
						'label' => 'City',
						'name' => 'city',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5a4188cd49a02',
						'label' => 'State',
						'name' => 'state',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5a4188d149a03',
						'label' => 'Zip',
						'name' => 'zip',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5a4188dd49a04',
						'label' => 'Featured',
						'name' => 'featured',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 0,
						'ui' => 0,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
					array(
						'key' => 'field_5a4188f749a05',
						'label' => 'Year Built',
						'name' => 'year_built',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5a41891649a06',
						'label' => 'Number of Floors',
						'name' => 'number_of_floors',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5a41a94a0871a',
						'label' => 'Lat',
						'name' => 'lat',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5a41a95f0871b',
						'label' => 'long',
						'name' => 'long',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5a73c52dcabc4',
						'label' => 'name_address',
						'name' => 'name_address',
						'type' => 'text',
						'instructions' => 'The name and address meta fields for more accurate searching. No need to touch this.',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5a8cf54484a6e',
						'label' => '3D Tour URL',
						'name' => '3d_tour_url',
						'type' => 'url',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'listing',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'seamless',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => array(
					0 => 'excerpt',
					1 => 'discussion',
					2 => 'comments',
					3 => 'revisions',
					4 => 'slug',
					5 => 'author',
					6 => 'format',
					7 => 'page_attributes',
					8 => 'featured_image',
					9 => 'categories',
					10 => 'tags',
					11 => 'send-trackbacks',
				),
				'active' => 1,
				'description' => '',
			));

		endif;
	}

}