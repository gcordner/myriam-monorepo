<?php
/**
 * Backlist block render template.
 *
 * @package Myriam2026
 */

defined( 'ABSPATH' ) || exit;

$books = get_field( 'books' );

if ( ! $books ) {
	if ( is_admin() ) {
		echo '<p>' . esc_html__( 'Backlist: select up to 4 books to feature.', 'myriam2026' ) . '</p>';
	}
	return;
}
?>
<section class="backlist ink-ground">
	<div class="backlist-inner">
		<h2 class="section-label on-ink"><?php esc_html_e( 'Backlist', 'myriam2026' ); ?></h2>

		<div class="book-grid">
			<?php foreach ( $books as $book_id ) : ?>
				<?php
				$thumb = get_the_post_thumbnail(
					$book_id,
					'large',
					array(
						'loading'  => 'lazy',
						'decoding' => 'async',
					)
				);
				$blurb = get_field( 'backlist_blurb', $book_id );
				$link  = get_permalink( $book_id );
				?>
				<div class="book-card">
					<a href="<?php echo esc_url( $link ); ?>">
						<?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<h3><?php echo esc_html( get_the_title( $book_id ) ); ?></h3>
					</a>
					<?php if ( $blurb ) : ?>
						<p><?php echo esc_html( $blurb ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
