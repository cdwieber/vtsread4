<?php
/**
 * The template to be loaded when the [properties] shortcode is called.
 * This has a lot of ugly inline PHP, so as soon as I find a way to better
 * separate concerns I will.
 */
use VTS\VTSRead_Query;
global $wp;
//Get all the states we have entries for
$args = array(
	'post_type' => 'listing',
	'posts_per_page' => -1,
	'meta_key' => 'state',
	'meta_compare' => 'EXISTS'
);
$query = new WP_Query($args);

$states = array();
while ($query->have_posts()): $query->the_post();
	$states[] = get_field('state');
endwhile;

$states = array_unique($states);
sort($states);

wp_reset_query();

//Then get all the cities
$args = array(
	'post_type' => 'listing',
	'posts_per_page' => -1,
	'meta_key' => 'city',
	'meta_compare' => 'EXISTS'
);
$query = new WP_Query($args);

$cities = array();
//create an array to index the keys and keep unique
$index_array = array();

while ($query->have_posts()): $query->the_post();
	if(!in_array(get_field('city'), $index_array)) {
		$cities[] = [
			'city'  => get_field( 'city' ),
			'state' => get_field( 'state' )
		];
		$index_array[] = get_field('city');
	}
endwhile;

unset($index_array);

//User Defined sorting algorithm

function sort_cities($a, $b) {
	return strcasecmp($a['city'], $b['city']);
}

usort($cities, 'sort_cities');

wp_reset_query();

//We're left with two arrays of unique values.

?>
<div class="doc-portfolio-wrapper">
<div class="listing-map" style="height:400px !important; width: 100%; display: none;">
	<?php

	$custom_query = new VTSRead_Query();

	$args = $custom_query->build_custom_query($_GET);
    if ($_GET) {
        $the_query = new WP_Query( $args );
    } elseif (false === get_transient('portfolio_results')) {
        $the_query = new WP_Query( $args );
        set_transient('portfolio_results', $the_query, DAY_IN_SECONDS);
    } else {
        $the_query = get_transient('portfolio_results');
    }
	?>
	<?php if ( $the_query->have_posts() ) : ?>
		<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

			<?php
			if (get_field('lat') == 0 || get_field('long') == 0) {
				error_log('Geocode failed for ' . get_field('street_address'). " " . "" .get_field('city').  " " .get_field('state'));
				continue;
			}
			$images = get_field('images');
			if ($images) {
				$img_url = $images[0]['sizes']['thumbnail'];
			}
			else {
				$img_url = WP_PLUGIN_URL . '/vtsread/public/images/placeholder.jpg';
			}
			?>

			<div class="marker" data-lat="<?php echo get_field('lat'); ?>" data-lng="<?php echo get_field('long'); ?>">
				<a href="<?php the_permalink() ?>"><img src="<?=$img_url?>" align="left" style="max-width: 150px"></a>
				<a href="<?php the_permalink() ?>"><h4 style="color:black"><?php the_title(); ?></h4></a>
				<p class="address">
					<?php the_field('street_address');?><br />
					<?php the_field('city'); ?>, <?php the_field('state'); ?> <?php the_field('zip'); ?> <br />
				</p>
				<p><?php the_sub_field('description'); ?></p>
			</div>
		<?php endwhile; ?>
		<?php wp_reset_postdata();
		wp_reset_query();?>
	<?php endif; ?>

</div>



<div class="portfolio-listing" >
	<form action="<?= home_url( $wp->request  )?>" method="get">
		<div class="full">
			<div class="container">
				<div class="grid-sizer"></div>
				<div class="portfolio-listing-search">
					<label for="search-by-name" >Property Name or Address:</label><br>
					<input type="text"
                           id="search-by-name"
                           class="search-by-name"
                           name="name_address"
                           placeholder="Property Name or Address"
                           value="<?php echo $_GET['name_address'] ? urldecode($_GET['name_address']) : '' ?>"
                    > <br>

					<div class="custom-select search-quarter">
						<select name="state" id="state">
							<option value="">State</option>
							<?php
							foreach($states as $state) {
								 ?>
                                    <option value=<?php echo $state ?> class=<?php echo $state ?>
                                        <?php echo $_GET['state'] == $state ? 'selected' : '' ?>><?= $state ?></option>
                            <?php
							}
							?>

						</select>
						<span class="select-arrow"></span>
					</div>
					<div class="custom-select search-quarter">
						<select name="city" id="city">
							<option value="">City</option>
							<?php
							foreach($cities as $row) {
							    $selected =  $_GET['city'] == $row['city'] ? 'selected' : '';
								echo "<option value={$row['city']} class={$row['state']} {$selected} >{$row['city']}</option>";
							}
							?>
						</select>
						<span class="select-arrow"></span>
					</div>
					<div class="search-quarter no-margin-bottom">
						<a href="" class="bare-btn advanced-search-button">Advanced Search <span></span></a>
					</div>
					<div class="search-quarter no-margin-bottom">
						<a href="/portfolio"><button value="Reset" class="bare-btn" type="button">Clear Search</button></a>
					</div>
				</div>
			</div>
		</div>



		<div class="full more-options">
			<div class="container">
				<div class="portfolio-listing-search-more">

					<!-- <div class="search-quarter">
						<fieldset>
							<legend>Single Tenant or Multi-Tenant</legend>
							<div>
								<input type="checkbox" id="single-tenant" name="Single Tenant" value="single-tenant">
								<label for="single-tenant">Single Tenant</label>
							</div>
							<div>
								<input type="checkbox" id="multi-tenant" name="Multi-Tenant" value="multi-tenant">
								<label for="multi-tenant">Multi-Tenant</label>
							</div>
						</fieldset>

					</div> -->
					<div class="search-quarter">
						<fieldset>
							<legend>Total Building Square Footage</legend>
							<div>
								<label for="min-total-square-footage">Minimum</label>
								<input id="min-total-square-footage" type="number" min="0" name="min-space_total" value="<?php echo $_GET['min-space_total'] ? $_GET['min-space_total'] : 0 ?>">
							</div>
							<div>
								<label for="max-total-square-footage">Maximum</label>
								<input id="max-total-square-footage" type="number" min="0" name="max-space_total" value="<?php echo $_GET['max-space_total'] ? $_GET['max-space_total'] : 0 ?>">
							</div>
						</fieldset>
					</div>
					<div class="search-quarter">
						<fieldset>
							<legend>Available Square Footage</legend>
							<div>
								<label for="min-avail-square-footage">Minimum</label>
								<input id="min-avail-square-footage" type="number" min="0" name="min-space_available" value="<?php echo $_GET['min-space_available'] ? $_GET['min-space_available'] : 0 ?>">
							</div>
							<div>
								<label for="max-avail-square-footage">Maximum</label>
								<input id="max-avail-square-footage" type="number" min="0" name="max-space_available" value="<?php echo $_GET['max-space_available'] ? $_GET['max-space_available'] : 0 ?>">
							</div>
						</fieldset>
					</div>
					<div class="search-quarter">
						<div class="">

						</div>
					</div>
				</div>
			</div>

		</div>


		<div class="full building-listing-sort">
			<div class="container building-listing-sort-controls">
				<div class="grid-list">
					<a href="#" class="grid"><img src="<?php echo WP_PLUGIN_URL . '/vtsread/public/images/grid-btn.png'; ?>" alt=""></a>
					<a href="#" class="list"><img src="<?php echo WP_PLUGIN_URL . '/vtsread/public/images/list-btn.png'; ?>" alt=""></a>
				</div>

				<div class="sort-by">
					<a href="#" class="sort-by-btn reset-btn" data-sort-value="original-order">Reset</a>
					<a href="#" class="sort-by-btn ascending-btn" data-sort-value="asc">A-Z</a>
					<a href="#" class="sort-by-btn descending-btn" data-sort-value="desc">Z-A</a>
					<a href="#" class="sort-by-btn available-space-desc sf" data-sort-value="space-asc">Most SF Available</a>
					<a href="#" class="sort-by-btn available-space-asc sf" data-sort-value="space-desc">Least SF Available</a>
				</div>
			</div>
		</div>

		<div class="full">
			<div class="container">
				<div class="">
					<button type="submit" value="Submit" class="search-button">Search</button>

				</div>
			</div>
		</div>
		<div class="full">
			<div class="container">
				<div class="">
					<a href="#" id="email-results">Email These Results</a>
				</div>
			</div>
		</div>
	</form>




	<div class="container building-listing-sort-container grid-view" >
		<div class="grid-sizer"></div>
		<?php
		$custom_query = new VTSRead_Query();

		$args = $custom_query->build_custom_query($_GET);
        if ($_GET) {
            $the_query = new WP_Query( $args );
        } elseif (false === get_transient('portfolio_results')) {
            $the_query = new WP_Query( $args );
            set_transient('portfolio_results', $the_query, DAY_IN_SECONDS);
        } else {
            $the_query = get_transient('portfolio_results');
        }
		?>
		<?php if ( $the_query->have_posts() ) : ?>
			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
				<?php
				$images = get_field('images');
				if ($images) {
					$img_url = $images[0]['sizes']['large'];
				}
				else {
					$img_url = WP_PLUGIN_URL . '/vtsread/public/images/placeholder.jpg';
				}
				?>
				
					<div class="listing grid-item">
						<div class="listing-image">
							<a href="<?php the_permalink(); ?>">
								<img src="<?php echo WP_PLUGIN_URL . '/vtsread/public/images/Spinner-1.4s-200px.svg'; ?>" class="lazy load-spinner" data-original="<?= $img_url ?>" alt="" width="50" height="50">
							</a>
						</div>
						<div class="listing-details">
							<?php if(get_field('featured') == 1) : ?>
								<a href="<?php the_permalink(); ?>" class="featured">Featured <span class="star"></span></a>
							<?php endif; ?>
							<h3 class="name"><?php the_title(); ?></h3>
							<h4 class="address"><?php the_field('street_address');?> <br>
								<?php the_field('city'); ?> <?php the_field('state'); ?>, <?php the_field('zip'); ?></h4>
							<p><?php the_content(); ?> </p>
							<div class="measurables">
								<p>Space: <?= number_format(get_field('space_total')); ?> RSF    |  Space available: <span class="available-space"><?= number_format(get_field('space_available')); ?></span>
									RSF   |   Floors: <?php the_field('number_of_floors'); ?> <a href="<?php the_permalink(); ?>" class="more-info"><span class="accent">More</span>&nbsp;Info >></a></p>
							</div>
						</div>
					</div>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php else:  ?>
			<p><?php _e( 'Sorry, no listings matched your criteria.' ); ?></p>
		<?php endif; ?>



	</div>
</div>
</div> <!-- End Portfolio Wrap -->
	<script>
        console.log("current_url" + document.location.href)
        //Populate Email Form
        jQuery(document).ready(function($) {

            var message = "Check out these search results from Docreit.com! " +
                "<p><a href=" + document.location.href + ">Go To Results</a></p>" +
                "<p>If the link above doesn't work, copy and paste the following in the browser:</p>" +
                "<p>" + document.location.href + "</p>";

            $("textarea").val(message);


            $('.listing-map').each(function(){
                map = new_map( $(this) );

                map.set('styles', [{
                    zoomControl: false,
                    zoom: 7
                }, {
                    "featureType": "all",
                    "stylers": [
                        {
                            "saturation": 0
                        },
                        {
                            "hue": "#e7ecf0"
                        }
                    ]
                },
                    {
                        "featureType": "road",
                        "stylers": [
                            {
                                "saturation": -70
                            }
                        ]
                    },
                    {
                        "featureType": "transit",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "water",
                        "stylers": [
                            {
                                "visibility": "simplified"
                            },
                            {
                                "saturation": -60
                            }
                        ]
                    }]);
            });

            //Show the map only after it's done rendering (otherwise a bunch of gunk pops up)
            $('.listing-map').show();

        });

	</script>

	<?php
	wp_enqueue_style( 'vts-ie' );
	wp_enqueue_style( 'vts-fancybox' );
	wp_enqueue_style( 'vts-screen' );
	wp_enqueue_style( 'vts-print' );
	wp_enqueue_style( 'vts-style' );

	wp_enqueue_script('fancybox');
	wp_enqueue_script('lazyload');
	wp_enqueue_script('isotope');
	wp_enqueue_script('images-loaded');
	wp_enqueue_script('front');
	?>
	<script src="https://maps.googleapis.com/maps/api/js?key="></script>
