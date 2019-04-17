<?php
namespace VTS;

class VTSRead_Shortcodes {
	public function __construct() {
		add_shortcode('properties', array($this, 'list_properties'));
		add_shortcode('properties-check', array($this, 'properties_check'));
	}

	public function list_properties($atts) {
		require_once WP_PLUGIN_DIR . "/vtsread/inc/properties-list.inc";
	}

	public function properties_check($atts) {
	    $args = [
	        'post_type' => 'listing',
            'posts_per_page' => -1,
        ];
	    $q = new \WP_Query($args);
	    echo "<h2>Listings with no images:</h2>";

	    if ($q->have_posts()) :
            while ($q->have_posts()) :  $q->the_post();
                if (null == get_field('images')) :
                ?>
                    <a href="/wp-admin/post.php?post=<?php echo get_the_ID() ?>&action=edit"><?php echo the_title(); ?></a><br>
                <?php
                endif;
            endwhile;
        endif;

    }
}