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
<div id='<?php echo $contentCall; ?>' class='isopost-wrapper <?php echo $contentCall; ?>'>
	<!-- .isopost-wrapper starts -->
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
			<div class="card">
				<!-- Featured Image-->
				<?php the_post_thumbnail('medium', array('class' => 'card-img-top')); ?>
				<div class="card-body d-flex flex-column">
					<div class="mb-2">
						<!-- Category Badge -->
						<!-- Post Categories -->
						<?php
							$thelist = '';
							$i = 0;
							foreach( get_the_category() as $category ) {
							    if ( 0 < $i ) $thelist .= ' ';
							    $thelist .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="badge badge-secondary">' . $category->name.'</a>';
							    $i++;
							}
							echo $thelist;
							?>
						<!-- isopost Categories -->
						<?php
							$terms = get_the_terms( $post->ID, 'isopost_categories' );
								if ($terms && ! is_wp_error($terms)): ?>
						<?php foreach($terms as $term): ?>
						<a href="<?php echo get_term_link( $term->slug, 'isopost_categories'); ?>" rel="tag" class="badge badge-secondary"><?php echo $term->name; ?></a>
						<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<!-- Title -->
					<h3 class="blog-post-title">
						<a href="<?php the_permalink(); ?>">
						<?php the_title(); ?>
						</a>
					</h3>
					<!-- Meta -->
					<?php if ( 'post' === get_post_type() ) : ?>
					<small class="text-muted mb-2">
					<?php
						bootscore_date();
						bootscore_author();
						bootscore_comments();
						bootscore_edit();
						?>
					</small>
					<?php endif; ?>	
					<!-- Excerpt & Read more -->
					<div class="card-text mt-auto">
						<?php the_excerpt(); ?> <a class="read-more" href="<?php the_permalink(); ?>"><?php _e('Read more »', 'bootscore'); ?></a>
					</div>
					<!-- Tags -->
					<?php bootscore_tags(); ?>
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
	</script>
</div>
<!-- .isopost-wrapper ends -->
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
$rep = preg_replace("/(
<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
	// closing tag
	$rep = preg_replace("/(
<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);
	return $rep;
	}