<?php use VTS\VTS_Import;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

get_header();

?>
<div class="doc-portfolio-wrapper">

        <?php while (have_posts()) : the_post(); 

	$images = get_field('images');
	if ($images) {
		$img_url = $images[0]['url'];
	}
	else {
		$img_url = WP_PLUGIN_URL . "/vtsread/public/images/placeholder.jpg";
	}

	//Retrieve flyer
	$vts = new VTS_Import();
	$json_property = $vts->getById(get_the_ID());
	$flyer_url = $json_property['property']['custom_flyer_url'];
	?>
	<article <?php post_class(); ?>>
		<div class="portfolio">
			<div class="full">
				<div class="">
					<h1><?php the_title(); ?>
						<?php if(get_field('featured') == 1) : ?>
							<a href="#" class="featured">Featured <span class="star"></span></a>
						<?php endif; ?>
					</h1>
					<div class="building-details">
						<div class="building-details-image" style="background-image: url(<?= $img_url ?>;" >

							<a href="#" class="gallery-open"><img src="<?php echo WP_PLUGIN_URL . '/vtsread/public/images/gallery-icon.png';?>" alt=""></a>

						</div>
						<div class="building-details-info">
							<h4 class="address"><?php the_field('street_address');?> <br>
								<?php the_field('city'); ?>, <?php the_field('state'); ?> <?php the_field('zip'); ?></h4>
							<hr>
							<p><?php the_content(); ?></p>
							<br>
							<div class="building-specs">
								<ul class="left">
									<li> <strong>Space:</strong> <?= number_format(get_field('space_total')); ?> RSF</li>
									<li><strong>Space Available:</strong> <?= number_format(get_field('space_available')); ?> RSF</li>
									<?php if(get_field('number_of_floors')) : ?>
										<li><strong>Floors:</strong> <?php the_field('number_of_floors'); ?></li>
									<?php endif; ?>
									<?php if(get_field('floor_plan_pdf')) : ?>
										<li><strong>Floor Plan:</strong> <a href="<?= get_field('floor_plan_pdf') ?>" class="pdf">Floor Plan PDF <span></span></a></li>
									<?php endif; ?>
									<?php if(get_field('space_available') == 0) : ?>
										<p><em style="font-style: italic">This property is 100% leased. Kindly reach out to our leasing team so we may share other opportunities
												to fulfill your needs within our healthcare real estate portfolio.</em></p>
									<?php endif; ?>
								</ul>
							</div>
							<?php if($flyer_url) : ?>
								<a href="<?= $flyer_url ?>" class="brochure-button">Download Marketing Flyer</a>
							<?php endif; ?>
							<?php if(get_field('3d_tour_url')) : ?>
								<a href="<?php the_field('3d_tour_url') ?>" class="_360-button" target="_blank"><img src="<?php echo WP_PLUGIN_URL . 'vtsread/public/images/360-degrees.png';?>" alt=""></a>
							<?php endif; ?>

						</div>
					</div>
					<!--  Building Contact -->
					<div class="building-contact">
						<div class="building-contact-details">
							<h4>Contact Details</h4>
							<ul>
								<li> Amy Hall</li>
								<li> Vice President of Leasing</li>
								<li> Physicians Realty Trust</li>
								<li> T: 414-367-5531</li>
								<li> amh@docreit.com</li>
							</ul>

						</div>
						<div class="building-contact-map">
							<div class="marker" data-lat="<?php echo get_field('lat'); ?>" data-lng="<?php echo get_field('long'); ?>">
								<h4><?php the_title(); ?></h4>
								<p class="address">
									<?php the_field('street_address');?><br />
									<?php the_field('city'); ?>, <?php the_field('state'); ?> <?php the_field('zip'); ?>
								</p>
								<p><?php the_sub_field('description'); ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- end containing div -->

	</article>
	
</div>

	<script src="https://maps.googleapis.com/maps/api/js?key="></script>

	<script>
        jQuery(document).ready(function($){
            $('.gallery-open').click(function(e){
                e.preventDefault();
                $.fancybox.open([
					<?php
					foreach($images as $image) {
						echo "{\n";
						echo "src : '{$image['url']}',\n";
						echo "opts: {caption : '{$image['caption']}'}\n";
						echo "},\n";
					}
					?>
                ], {
                    loop : false
                });
            });
        });
	</script>
<?php endwhile; ?>


<?php get_footer(); ?>

