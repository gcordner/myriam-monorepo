<?php
/**
 * Single Book template (GeneratePress child theme)
 *
 * File: /wp-content/themes/myriam2026/single-book.php
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	// ACF fields (defensive: use get_field(), escape, and only output if set).
	$featured_text = get_field( 'featured_text' );
	$price         = get_field( 'price' );
	$publisher     = get_field( 'publisher' );
	$available_in  = get_field( 'available_in' );
	$isbn          = get_field( 'isbn' );
	$publication_y = get_field( 'publication_year' );

	// Purchase links.
	$btn1_text = get_field( 'button_1_button_1_text' );
	$btn1_url  = get_field( 'button_1_button_1_url' );
	$btn2_text = get_field( 'button_2_button_2_text' );
	$btn2_url  = get_field( 'button_2_button_2_url' );

	// Thumbnail.
	$thumb = get_the_post_thumbnail(
		get_the_ID(),
		'large',
		array(
			'class'    => 'book-cover',
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'book-single' ); ?>>

		<header <?php generate_do_attr( 'entry-header' ); ?>>
			<?php the_title( '<h1 class="entry-title book-title">', '</h1>' ); ?>
			<?php if ( $featured_text ) : ?>
				<p class="book-subheading"><?php echo esc_html( $featured_text ); ?></p>
			<?php endif; ?>
		</header>

		<div class="book-detail-grid">
			<div class="book-detail-main">
				<h2 class="screen-reader-text"><?php printf( esc_html__( 'About %s', 'myriam2026' ), get_the_title() ); ?></h2>
				<?php
				the_content();

				// Paginated content support.
				wp_link_pages(
					array(
						'before' => '<nav class="post-pages">' . esc_html__( 'Pages:', 'myriam2026' ),
						'after'  => '</nav>',
					)
				);
				?>
			</div>

			<aside class="book-detail-aside" aria-labelledby="book-meta-title">
				<?php if ( $thumb ) : ?>
					<div class="book-cover-wrap">
						<?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<div class="book-meta">
					<h2 id="book-meta-title" class="book-meta-heading"><?php esc_html_e( 'Book details', 'myriam2026' ); ?></h2>

					<?php if ( $price ) : ?>
						<p class="book-price"><strong><?php esc_html_e( 'Price:', 'myriam2026' ); ?></strong>
							<?php echo esc_html( $price ); ?></p>
					<?php endif; ?>

					<?php if ( $publisher ) : ?>
						<p><strong><?php esc_html_e( 'Publisher:', 'myriam2026' ); ?></strong>
							<?php echo esc_html( $publisher ); ?></p>
					<?php endif; ?>

					<?php if ( $available_in ) : ?>
						<p><strong><?php esc_html_e( 'Available in:', 'myriam2026' ); ?></strong>
							<?php echo wp_kses_post( $available_in ); ?></p>
					<?php endif; ?>

					<?php if ( $isbn ) : ?>
						<p><strong><?php esc_html_e( 'ISBN:', 'myriam2026' ); ?></strong>
							<?php echo esc_html( $isbn ); ?></p>
					<?php endif; ?>

					<?php if ( $publication_y ) : ?>
						<p><strong><?php esc_html_e( 'Published:', 'myriam2026' ); ?></strong>
							<?php echo esc_html( $publication_y ); ?></p>
					<?php endif; ?>

					<?php if ( $btn1_text && $btn1_url ) : ?>
						<p class="book-cta">
							<a class="button button-primary" href="<?php echo esc_url( $btn1_url ); ?>" target="_blank" rel="noopener">
								<?php echo esc_html( $btn1_text ); ?>
							</a>
						</p>
					<?php endif; ?>

					<?php if ( $btn2_text && $btn2_url ) : ?>
						<p class="book-cta">
							<a class="button button-secondary" href="<?php echo esc_url( $btn2_url ); ?>" target="_blank" rel="noopener">
								<?php echo esc_html( $btn2_text ); ?>
							</a>
						</p>
					<?php endif; ?>
				</div>
			</aside>
		</div>

		<footer class="book-footer">
			<?php
			edit_post_link(
				esc_html__( 'Edit', 'myriam2026' ),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer>
	</article>
	<?php
endwhile;

get_footer();
