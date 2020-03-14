<?php
	/*
	 * Template Name: Sidebar left
	 * Template Post Type: post
	 */
	  
	 get_header();  ?>
<div id="primary" class="content-area">
	<main id="main" class="site-main">
		<div class="row">
			<!-- Sidebar -->
			<?php get_sidebar(); ?>
			<div class="col order-first order-md-last">
				<header class="entry-header">
					<?php the_post(); ?>
					<!-- Category Badge -->
					<div class="mb-2">
					<?php
						$thelist = '';
						$i = 0;
						foreach( get_the_category() as $category ) {
						    if ( 0 < $i ) $thelist .= ' ';
						    $thelist .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="badge badge-primary">' . $category->name.'</a>';
						    $i++;
						}
						echo $thelist;
						?>
						<!-- IsoPost Categories -->
						<?php
							$terms = get_the_terms( $post->ID, 'isopost_categories' );
								if ($terms && ! is_wp_error($terms)): ?>
						<?php foreach($terms as $term): ?>
						<a href="<?php echo get_term_link( $term->slug, 'isopost_categories'); ?>" rel="tag" class="badge badge-primary"><?php echo $term->name; ?></a>
						<?php endforeach; ?>
						<?php endif; ?>
						</div>
					<!-- Category link -->
					<!--<?php the_category(', ') ?><?php the_terms( $post->ID, 'isopost_categories', ' ', ' / ' ); ?>-->
					<!-- Title -->
					<?php the_title('<h1>', '</h1>'); ?>
					<!-- Meta -->
					<p class="entry-meta">
						<small class="text-muted">
						<?php
							bootscore_date();
							_e(' by ', 'bootscore'); the_author_posts_link();
							bootscore_comment_count();
							bootscore_edit();							
							 ?>
						</small>
						<!-- .entry-meta -->
					</p>
					<!-- Featured Image-->
					<?php bootscore_post_thumbnail(); ?>
					<!-- .entry-header -->
				</header>
				<div class="entry-content">
					<!-- Content -->
					<?php the_content(); ?>
					<!-- .entry-content -->
				</div>
				<footer class="entry-footer">
					<!-- Tags & Edit Link -->
					<p>
						<?php 
							bootscore_tags(); 
							
							?>
					</p>
					<!-- Pagination -->
					<nav aria-label="Page navigation example">
						<ul class="pagination justify-content-center">
							<li class="page-item">
								<?php previous_post_link('%link'); ?>
							</li>
							<li class="page-item">
								<?php next_post_link('%link'); ?>
							</li>
						</ul>
					</nav>
					<!-- .entry-footer -->
				</footer>
				<!-- Comments -->
				<?php comments_template(); ?>
				<!-- col -->
			</div>
			<!-- row -->
		</div>
		<!-- #main -->
	</main>
	<!-- #primary -->	
</div>
<?php get_footer(); ?>