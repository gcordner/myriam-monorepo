<?php
/**
 * Featured Book block render template.
 *
 * @package Myriam2026
 */

defined( 'ABSPATH' ) || exit;

$book_id = get_field( 'book' );

if ( ! $book_id ) {
	if ( is_admin() ) {
		echo '<p>' . esc_html__( 'Featured Book: select a book to preview this block.', 'myriam2026' ) . '</p>';
	}
	return;
}

$title    = get_the_title( $book_id );
$subtitle = get_field( 'featured_text', $book_id );
$link     = get_permalink( $book_id );
$awards   = get_field( 'awards', $book_id );
$thumb    = get_the_post_thumbnail(
	$book_id,
	'large',
	array(
		'loading'  => 'lazy',
		'decoding' => 'async',
	)
);

$eyebrow_text = get_field( 'eyebrow_text' );
$eyebrow_text = $eyebrow_text ? $eyebrow_text : __( 'New book', 'myriam2026' );

$blurbs = get_field( 'blurbs' );

$button_text = get_field( 'button_text' );
$button_text = $button_text ? $button_text : __( 'Read more reviews', 'myriam2026' );
?>
<section class="featured-book paper-ground">
	<div class="featured-book-grid">
		<?php if ( $thumb ) : ?>
			<div class="featured-book-cover-wrap">
				<a href="<?php echo esc_url( $link ); ?>">
					<?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>
		<?php endif; ?>

		<div class="featured-book-content">
			<span class="eyebrow"><?php echo esc_html( $eyebrow_text ); ?></span>

			<?php if ( $awards ) : ?>
				<ul class="featured-book-awards">
					<?php foreach ( $awards as $award ) : ?>
						<li><?php echo esc_html( $award['award_text'] ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<h2><?php echo esc_html( $title ); ?></h2>

			<?php if ( $subtitle ) : ?>
				<p class="subtitle"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>

			<?php if ( $blurbs ) : ?>
				<ul class="blurbs">
					<?php
					foreach ( $blurbs as $blurb ) :
						if ( empty( $blurb['quote'] ) && empty( $blurb['citation'] ) ) {
							continue;
						}
						?>
						<li>
							<?php if ( ! empty( $blurb['quote'] ) ) : ?>
								<blockquote><?php echo esc_html( $blurb['quote'] ); ?></blockquote>
							<?php endif; ?>
							<?php if ( ! empty( $blurb['citation'] ) ) : ?>
								<cite><?php echo esc_html( $blurb['citation'] ); ?></cite>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<a class="stamp-btn" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $button_text ); ?></a>
		</div>
	</div>
</section>
