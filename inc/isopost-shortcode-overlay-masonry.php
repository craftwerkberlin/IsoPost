<?php
function isopost_shortcode_func( $atts ) {
	ob_start();
    $opts = shortcode_atts( array(
        'id' => '',
    ), $atts );
	
	$postID = $opts['id'];
	
	if(!empty($postID) && 'isopost-scode' == get_post_type($postID))
	{
		$isopost_data = get_post_meta($postID);
		
		// fetch isopost post meta data
		$_isopost_selpost  = !empty($isopost_data['_isopost_selpost'][0]) ? $isopost_data['_isopost_selpost'][0] : '';
		
		// if selected 'post type' exists
		if ( !empty($_isopost_selpost) && post_type_exists($_isopost_selpost) ) 
		{
			$_isopost_seltax   = !empty($isopost_data['_isopost_seltax'][0]) ? $isopost_data['_isopost_seltax'][0] : '';
			$_isopost_taxterms = !empty($isopost_data['_isopost_taxterms'][0]) ? unserialize($isopost_data['_isopost_taxterms'][0]) : array();
			$_isopost_st_dcol  = !empty($isopost_data['_isopost_st_dcol'][0]) ? $isopost_data['_isopost_st_dcol'][0] : '';
			$_isopost_st_tcol  = !empty($isopost_data['_isopost_st_tcol'][0]) ? $isopost_data['_isopost_st_tcol'][0] : '';
			$_isopost_st_pcol  = !empty($isopost_data['_isopost_st_pcol'][0]) ? $isopost_data['_isopost_st_pcol'][0] : '';
			$_isopost_st_size  = !empty($isopost_data['_isopost_st_size'][0]) ? $isopost_data['_isopost_st_size'][0] : '';
			
			wp_reset_query();
			
			// custom query with arguments to fetch project posts
			$isopost_posts_args = array(
				'post_type'			  => $_isopost_selpost,
				'posts_per_page'	  => '-1',
				'post_status'		  => 'publish',
				'ignore_sticky_posts' => true,
				'tax_query'  => array(
					array(
						'taxonomy' => $_isopost_seltax,
						'field'    => 'id',
						'terms'    => unserialize($_isopost_taxterms),
					),
				),
			);
			$filter_posts = new WP_Query( $isopost_posts_args );
			$contentCall  = 'isopost-shortcode-'.$postID;
			?>
			<div id='<?php echo $contentCall; ?>' class='isopost-wrapper <?php echo $contentCall; ?>'><!-- .isopost-wrapper starts -->
				
				

				
				
				<!-- .isopost-filter -->
				<div class="isopost-filter justify-content-center d-flex">
					<div class="filter isopostfilters">
						<a class="active btn btn-outline-primary" href="JavaScript:void(0);" data-filter="*"><?php _e('All','bootscore'); ?></a>
						<?php
						$filter_terms = unserialize($_isopost_taxterms);
						foreach( $filter_terms as $term_id ) 
						{
							$term      = get_term( $term_id, $_isopost_seltax );
							$term_slug = $term->slug;
							$term_name = $term->name; 
							?>
							<a class="btn btn-outline-primary" href="JavaScript:void(0);" data-filter="<?php echo $term_slug; ?>"><?php echo $term_name; ?></a>
							<?php 
						} 
						?>
					</div>
				</div>
				<div class="isopost-posts isopost-grids">
					<?php
					if ( $filter_posts->have_posts() ) :
					while ( $filter_posts->have_posts() ) : $filter_posts->the_post(); 
						$data_type  = '';
						$postID     = get_the_ID();
						$post_thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $postID ), $_isopost_st_size ); 	
						$terms      = get_the_terms( $postID, $_isopost_seltax ); 
						$cat_links  = array();
						foreach ( $terms as $term ) {
							$cat_links[] = $term->slug;
						}
						foreach($cat_links as $itm){
							$data_type .= $itm.' ';
						}
						?>

						<div class="isopost-item item <?php echo $data_type; $data_type = null; ?> isopost-grid <?php echo $_isopost_st_dcol.' '.$_isopost_st_tcol.' '.$_isopost_st_pcol; ?>">
							
							<div class="card equal-height bg-dark text-white border-0">
			
								<!-- Featured Image-->
								<?php if (has_post_thumbnail() )
									echo '<div class="card-img overlay">' . get_the_post_thumbnail(null, 'medium') . '</div>';
								?>  
			
								<div class="card-img-overlay d-flex align-items-center justify-content-center text-center">
									<div class="overlay-content">
										<!-- Title -->
										<h3 class="blog-post-title card-title">
											<!--<a href="<?php the_permalink(); ?>">-->
											<?php the_title(); ?>
											<!--</a>-->
										</h3>
				
										<!-- Read more -->
										<div class="readmore">
											<a class="btn btn-outline-light" href="<?php the_permalink(); ?>"><?php _e('Read more', 'isopost'); ?> »</a>
									</div>
									</div>
			
								</div>
		
							</div>						
		
						</div>
						
						<?php
					endwhile;
					wp_reset_postdata();
					else: 	
					_e( 'Sorry, no post items found. Kindly add.','isopost' );
					endif;
					?>
				</div>
				
				<script>
				
				jQuery(window).load( function() 
				{
					var $container = jQuery('#<?php echo $contentCall ?> .isopost-posts');
					// initialize isotope
					$container.isotope({
						filter: '*',
						animationOptions: {
							duration: 750,
							easing: 'linear',
							queue: false,
						}
					});
					
					// Fit Rows
					/*$container.isotope({
        				layoutMode: 'fitRows',
    				});*/
 

    				// Add Button Group to filters
    				jQuery(document).ready(function($) {					
						if (window.matchMedia("(min-width: 414px)").matches) {
							$('.isopostfilters').addClass('btn-group');
						}    				
    				});
    						

					// filter items when filter link is clicked
					jQuery('#<?php echo $contentCall ?> .isopostfilters a').click(function(){
						var selector = jQuery(this).attr('data-filter');
						if( selector !== '*' ) selector = selector.replace(selector, '.' + selector)
						$container.isotope({ 
							filter: selector ,
							animationOptions: {
								duration: 750,
								easing: 'linear',
								queue: false,	
							}
						});
						return false;
					});
					// set active filter items
					var $optionSets = jQuery('#<?php echo $contentCall ?> .filter'),
					$optionLinks    = $optionSets.find('a');
					$optionLinks.click(function(){
						var $this = jQuery(this);
						// don't proceed if already active
						if ( $this.hasClass('active') ) {
							return false;
						}
						var $optionSet = $this.parents('.filter');
						$optionSet.find('.active').removeClass('active');
						$this.addClass('active'); 
					});
				});
				
				
 					// Set each card to equal height
					/*jQuery(document).ready(function($) {   				
						if (window.matchMedia("(min-width: 768px)").matches) {
							$(document).ready(function() {
								//shared variable
								var max = 0,
									$els = $('.card.equal-height');
								$els.each(function() {
									max = Math.max($(this).height(), max); //use height method from jQuery
								});

								$els.height(max)
							});
						}

					});*/
				
				</script>
			</div><!-- .isopost-wrapper ends -->
			<?php
		}
		elseif(!empty($_isopost_selpost)){
			_e( 'Sorry, but '.ucwords($_isopost_selpost).' post type does not exists.','isopost' );
		}
	}
	else{
		_e( 'Sorry, isopost shortcode does not exists.','isopost' );
	}
	return ob_get_clean();
}
add_shortcode( 'isopost', 'isopost_shortcode_func' );

// Remove empty p tags for isopost shortcode
add_filter("the_content", "isopost_the_content_filter");

function isopost_the_content_filter($content) 
{
	// array of shortcode requiring the fix 
	$block = join("|",array("isopost"));
	
	// opening tag
	$rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
		
	// closing tag
	$rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);
	
	return $rep;
}


