<?php

namespace VTS;

class VTSRead_Query {

	/**
	 * Parse through user-supplied options and return a WordPress-formatted
	 * meta-query argument, which can then be passed to a WP_Query object.
	 *
	 * This is specifically formatted for the kinds of data my plugin is likely to
	 * encounter in the wild, so not necessarily for general use.
	 * @param $criteria
	 *
	 * @return array
	 */
	public function build_custom_query($criteria) {
			//Instantiate the arguments array for WP_Query
			$args = array( 'post_type' => 'listing',
			               'posts_per_page' => -1,
			               'orderby' => 'meta_value',
			               'order' => 'DESC',
                            'no_found_rows' => true, // counts posts, remove if pagination required
                            'update_post_term_cache' => false, // grabs terms, remove if terms required (category, tag...)
                            'update_post_meta_cache' => false, // grabs post meta, remove if post meta required
			);


			//Loop through fields provided and add them to an array provided they are not null
			if ($criteria) {
				//Specify that we want ALL conditions to be true
				$args['meta_query'] = array('relation' => 'AND');
				foreach($criteria as $key => $value) {
					switch($value) {
						//Do nothing if the var is null
						case null || 0 :
							break;
						//is it a numeric value?
						case is_numeric($value) :
							//if so, append the proper array to the query
							$args['meta_query'][] = array(
								'compare' => preg_match('/\bmin\b/', $key) ? '>=' : '<=', //Is this key wanting a min or max value?
								'key' => str_replace(['min-', 'max-'], '', $key), //trim the key down to the database field name
								'value' => (int)$value,
								'type' => 'NUMERIC',
							);
							break;
						default :
							//if not, it's gotta be a string.
							$args['meta_query'][] = array(
								'key' => $key,
								'value' => $value,
								'compare' => 'LIKE'
							);
					}
				}
			}
//		//Nest an additional query that displays featured listings whether they are flagged as such or not.
//		//We have to "trick" WordPress in this fashion, since it thinks we ONLY want featured posts otherwise.
		$args['meta_query'][] = array(
			'relation' => 'OR',
			     array ('key' => 'featured', 'compare' => 'NOT EXISTS'),
				 array ('key' => 'featured', 'compare' => 'EXISTS'),
			);

		return $args;
	}

}