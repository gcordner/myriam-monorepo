<?php
/**
 * Single Book template (GeneratePress child theme)
 *
 * File: /wp-content/themes/your-gp-child/single-book.php
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	// ACF fields (defensive: use get_field(), escape, and only output if set).
	$featured_text   = get_field( 'featured_text' );
	$price           = get_field( 'price' );
	$publisher       = get_field( 'publisher' );
	$available_in    = get_field( 'available_in' );
	$isbn            = get_field( 'isbn' );
	$publication_y   = get_field( 'publication_year' );

	// Optional “Button 1” fields from your example.
	$btn1_text = get_field( 'button_1_button_1_text' );
	$btn1_url  = get_field( 'button_1_button_1_url' );

	// Thumbnail.
	$thumb = get_the_post_thumbnail( get_the_ID(), 'large', [
		'class' => 'book-cover',
		'loading' => 'lazy',
		'decoding' => 'async',
	] );
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'book-single' ); ?>>

		<header class="book-header">
			<h1 class="book-title"><?php the_title(); ?></h1>
			<?php if ( $featured_text ) : ?>
				<h2 class="book-subheading"><?php echo esc_html( $featured_text ); ?></h2>
			<?php endif; ?>
		</header>

		<div class="book-grid">
			<div class="book-main">
				<h2 class="screen-reader-text"><?php printf( esc_html__( 'About %s', 'your-textdomain' ), get_the_title() ); ?></h2>
				<?php
				the_content();

				// Paginated content support.
				wp_link_pages( [
					'before' => '<nav class="post-pages">' . esc_html__( 'Pages:', 'your-textdomain' ),
					'after'  => '</nav>',
				] );
				?>
			</div>

			<aside class="book-aside" aria-labelledby="book-meta-title">
				<?php if ( $thumb ) : ?>
					<div class="book-cover-wrap">
						<?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<div class="book-meta">
					<h2 id="book-meta-title" class="book-meta-heading"><?php esc_html_e( 'Book details', 'your-textdomain' ); ?></h2>

					<?php if ( $price ) : ?>
						<p class="book-price"><strong><?php esc_html_e( 'Price:', 'your-textdomain' ); ?></strong>
							<?php echo esc_html( $price ); ?></p>
					<?php endif; ?>

					<?php if ( $publisher ) : ?>
						<p><strong><?php esc_html_e( 'Publisher:', 'your-textdomain' ); ?></strong>
							<?php echo esc_html( $publisher ); ?></p>
					<?php endif; ?>

					<?php if ( $available_in ) : ?>
						<p><strong><?php esc_html_e( 'Available in:', 'your-textdomain' ); ?></strong>
							<?php echo wp_kses_post( $available_in ); ?></p>
					<?php endif; ?>

					<?php if ( $isbn ) : ?>
						<p><strong><?php esc_html_e( 'ISBN:', 'your-textdomain' ); ?></strong>
							<?php echo esc_html( $isbn ); ?></p>
					<?php endif; ?>

					<?php if ( $publication_y ) : ?>
						<p><strong><?php esc_html_e( 'Published:', 'your-textdomain' ); ?></strong>
							<?php echo esc_html( $publication_y ); ?></p>
					<?php endif; ?>

					<?php if ( $btn1_text && $btn1_url ) : ?>
						<p class="book-cta">
							<a class="button button-primary" href="<?php echo esc_url( $btn1_url ); ?>" target="_blank" rel="noopener">
								<?php echo esc_html( $btn1_text ); ?>
							</a>
						</p>
					<?php endif; ?>
				</div>
			</aside>
		</div>

		<footer class="book-footer">
			<?php
			// Categories/terms if you registered any taxonomies for "book"
			// echo get_the_term_list( get_the_ID(), 'book_category', '<div class="terms">', ', ', '</div>' );

			edit_post_link(
				esc_html__( 'Edit', 'your-textdomain' ),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer>
	</article>
<?php
endwhile;

get_footer();
