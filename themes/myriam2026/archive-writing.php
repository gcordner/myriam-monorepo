<?php
/**
 * Archive template for Writing custom post type
 *
 * @package Myriam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>
<div <?php generate_do_attr( 'content' ); ?>>
	<main <?php generate_do_attr( 'main' ); ?>>
	<?php
	/**
	 * Generate_before_main_content hook.
	 *
	 * @since 0.1
	 */
	do_action( 'generate_before_main_content' );

	if ( have_posts() ) :
		?>

		<?php
		/**
		 * Generate_archive_title hook.
		 *
		 * @since 0.1
		 *
		 * @hooked generate_archive_title - 10
		 */
		do_action( 'generate_archive_title' );
		?>

			<div class="writing-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<?php
				// Get ACF fields.
				$article_url = get_field( 'url' );
				$magazine    = get_field( 'magazine_name' );
				$pub_date    = get_the_date();

				// Determine URL - external or internal.
				if ( $article_url ) {
					$url    = $article_url;
					$target = ' target="_blank"';
				} else {
					$url    = get_permalink();
					$target = '';
				}
				?>
				
					<div class="writing-item">
						<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
						
							<header class="entry-header">
								<!-- Magazine and Date -->
								<div class="magazine">
								<?php if ( $magazine ) : ?>
										<p>
											<a href="<?php echo esc_url( $article_url ); ?>" target="_blank">
											<?php echo esc_html( $magazine ); ?>
											</a>
											<span class="date"> | <?php echo esc_html( $pub_date ); ?></span>
										</p>
								<?php else : ?>
										<p><span class="date"><?php echo esc_html( $pub_date ); ?></span></p>
								<?php endif; ?>
								</div>
							
								<!-- Featured Image -->
							<?php if ( has_post_thumbnail() ) : ?>
									<div class="featured-image">
										<a href="<?php echo esc_url( $url ); ?>"<?php echo $target; ?> title="<?php echo esc_attr( get_the_title() ); ?>">
										<?php the_post_thumbnail( 'large' ); ?>
										</a>
									</div>
							<?php endif; ?>
							
								<!-- Title -->
								<h2 class="entry-title">
									<a href="<?php echo esc_url( $url ); ?>"<?php echo $target; ?> rel="bookmark">
									<?php the_title(); ?>
									</a>
								</h2>
							</header>
						
							<!-- Content -->
							<div class="entry-content">
							<?php the_excerpt(); ?>
							</div>
						
							<!-- Tags -->
							<footer class="entry-footer">
							<?php if ( has_tag() ) : ?>
									<div class="tags">
									<?php the_tags( '', ' ' ); ?>
									</div>
							<?php endif; ?>
							</footer>
						
						</article>
					</div>
				
			<?php endwhile; ?>
			</div>

		<?php
		// Pagination.
		the_posts_pagination();
		?>

	<?php else : ?>

			<p>No writing pieces found.</p>

	<?php endif; ?>

	<?php
	/**
	 * Generate_after_main_content hook.
	 *
	 * @since 0.1
	 */
	do_action( 'generate_after_main_content' );
	?>
	</main>
</div>

<?php
/**
 * Generate_after_primary_content_area hook.
 *
 * @since 2.0
 */
do_action( 'generate_after_primary_content_area' );

generate_construct_sidebars();

get_footer();
?>