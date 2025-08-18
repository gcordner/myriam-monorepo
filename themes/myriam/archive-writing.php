<?php
/**
 * Archive template for Writing post type
 *
 * @package Blocksy
 */

$prefix = blocksy_manager()->screen->get_prefix();

$container_class = 'ct-container';

/**
 * Note to code reviewers: This line doesn't need to be escaped.
 * Function blocksy_output_hero_section() used here escapes the value properly.
 */
echo blocksy_output_hero_section([
	'type' => 'type-2'
]);

$section_class = '';

if (! have_posts()) {
	$section_class = 'class="ct-no-results"';
}

?>

<div class="<?php echo $container_class ?>" <?php echo wp_kses_post(blocksy_sidebar_position_attr()); ?> <?php echo blocksy_get_v_spacing() ?>>
	<section <?php echo $section_class ?>>
		<?php
			/**
			 * Note to code reviewers: This line doesn't need to be escaped.
			 * Function blocksy_output_hero_section() used here
			 * escapes the value properly.
			 */
			echo blocksy_output_hero_section([
				'type' => 'type-1'
			]);

			if (have_posts()) : ?>
				<header class="page-header">
					<?php
					post_type_archive_title('<h1 class="page-title">', '</h1>');
					the_archive_description('<div class="taxonomy-description">', '</div>');
					?>
				</header>

				<div class="writing-archive-grid">
					<?php while (have_posts()) : the_post(); ?>
						
						<article class="writing-card entry-card">
							<?php if (has_post_thumbnail()) : ?>
								<div class="writing-featured-image">
									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail('medium_large'); ?>
									</a>
								</div>
							<?php endif; ?>

							<div class="writing-content">
								<h2 class="writing-title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h2>

								<div class="writing-meta">
									<?php 
									$writing_type = get_field('writing_type');
									$publication = get_field('publication');
									$publication_link = get_field('publication_link');
									$writing_date = get_field('writing_date');
									?>

									<?php if ($writing_date) : ?>
										<span class="writing-date"><?php echo esc_html($writing_date); ?></span>
									<?php endif; ?>

									<?php if ($writing_type) : ?>
										<span class="writing-type"><?php echo esc_html($writing_type); ?></span>
									<?php endif; ?>

									<?php if ($publication) : ?>
										<span class="publication">
											<?php if ($publication_link) : ?>
												<a href="<?php echo esc_url($publication_link); ?>" target="_blank">
													<?php echo esc_html($publication); ?>
												</a>
											<?php else : ?>
												<?php echo esc_html($publication); ?>
											<?php endif; ?>
										</span>
									<?php endif; ?>
								</div>

								<?php if (has_excerpt()) : ?>
									<div class="writing-excerpt">
										<?php the_excerpt(); ?>
									</div>
								<?php endif; ?>

								<a href="<?php the_permalink(); ?>" class="writing-read-more wp-element-button ct-button">
									Read More
								</a>
							</div>
						</article>

					<?php endwhile; ?>
				</div>

				<?php
				// Pagination
				the_posts_pagination();
				?>

			<?php else : ?>
				<p>No writing pieces found.</p>
			<?php endif; ?>
	</section>

	<?php get_sidebar(); ?>
</div>