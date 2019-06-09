<?php
	get_header();
	?>
<div id="primary" class="content-area">
	<main id="main" class="site-main">
		<div class="row">
			<div class="col">
				<!-- Title & Description -->
				<header class="page-header">
					<div class="card mb-4 bg-light border-0">
						<div class="card-body">	
							<?php
								the_archive_title( '<h1 class="page-title mb-0">', '</h1>' );
								the_archive_description( '<div class="archive-description">', '</div>' );
								?>								
						</div>
					</div>
				</header>
				<!-- .page-header -->
				<!-- Grid Layout -->
				<div class="card-deck">
					<?php if (have_posts() ) : ?>
					<?php while (have_posts() ) : the_post(); ?>
					<div class="card">
						<!-- Featured Image-->
						<?php if (has_post_thumbnail() )
							echo '<div class="card-img-top">' . get_the_post_thumbnail(null, 'medium') . '</div>';
							?>  
						<div class="card-body d-flex flex-column">
							<div class="mb-2">
								<!-- Category Badge -->
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
							<h2 class="blog-post-title">
								<a href="<?php the_permalink(); ?>">
								<?php the_title(); ?>
								</a>
							</h2>
							<!-- Meta -->
							<?php if ( 'post' === get_post_type() ) : ?>
							<small class="text-secondary mb-2">
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
								<?php the_excerpt(); ?> <a class="read-more" href="<?php the_permalink(); ?>"><?php _e('Read more', 'bootscore'); ?></a>
							</div>
							<!-- Tags -->
							<?php bootscore_tags(); ?>
						</div>
					</div>
					<?php endwhile; ?>
					<?php endif; ?>
				</div>
				<!-- Pagination -->
				<div>
					<?php 
						if (function_exists("bootscore_pagination"))
						{
						  	bootscore_pagination();
						}
						?>
				</div>
			</div>
			<!-- col -->
		</div>
		<!-- row -->
	</main>
	<!-- #main -->
</div>
<!-- #primary -->
<?php
get_footer();