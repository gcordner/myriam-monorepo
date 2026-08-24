<?php
/**
 * Single template for the Class Week custom post type.
 *
 * @package Myriam2026
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div <?php generate_do_attr( 'content' ); ?>>
	<main <?php generate_do_attr( 'main' ); ?>>
		<?php do_action( 'generate_before_main_content' ); ?>

		<?php while ( have_posts() ) : the_post();
			$week_num     = get_post_meta( get_the_ID(), 'hs_week_number', true );
			$vimeo_url    = (string) get_post_meta( get_the_ID(), 'hs_vimeo_url', true );
			$slides_url   = (string) get_post_meta( get_the_ID(), 'hs_slides_url', true );
			$slides_label = get_post_meta( get_the_ID(), 'hs_slides_label', true ) ?: __( 'Download Slides', 'myriam2026' );
			$embed_url    = myriam2026_vimeo_embed_url( $vimeo_url );
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'week-page medium-width' ); ?>>

			<header class="week-header page-header">
				<?php if ( $week_num ) : ?>
				<p class="week-header__kicker"><?php echo esc_html( 'Week ' . $week_num ); ?></p>
				<?php endif; ?>
				<h1 class="week-header__title"><?php the_title(); ?></h1>
			</header>

			<?php if ( $embed_url || $slides_url ) : ?>
			<nav class="week-nav" aria-label="<?php esc_attr_e( 'Page sections', 'myriam2026' ); ?>">
				<?php if ( $embed_url ) : ?>
				<a href="#recording" class="week-nav__link"><?php esc_html_e( 'Recording', 'myriam2026' ); ?></a>
				<?php endif; ?>
				<?php if ( $slides_url ) : ?>
				<a href="#slides" class="week-nav__link"><?php esc_html_e( 'Slides', 'myriam2026' ); ?></a>
				<?php endif; ?>
			</nav>
			<?php endif; ?>

			<?php if ( $embed_url ) : ?>
			<section id="recording" class="week-section week-section--recording">
				<h2 class="week-section__title"><?php esc_html_e( 'Recording', 'myriam2026' ); ?></h2>
				<div class="vimeo-wrapper">
					<iframe
						src="<?php echo esc_url( $embed_url ); ?>"
						allow="autoplay; fullscreen; picture-in-picture"
						allowfullscreen
						loading="lazy"
						title="<?php echo esc_attr( get_the_title() . ' — recording' ); ?>"
					></iframe>
				</div>
			</section>
			<?php endif; ?>

			<?php if ( $slides_url ) : ?>
			<section id="slides" class="week-section week-section--slides">
				<h2 class="week-section__title"><?php esc_html_e( 'Slides', 'myriam2026' ); ?></h2>
				<a href="<?php echo esc_url( $slides_url ); ?>" class="slides-link" target="_blank" rel="noopener">
					<?php echo esc_html( $slides_label ); ?> <span aria-hidden="true">&#8595;</span>
				</a>
			</section>
			<?php endif; ?>

			<section id="materials" class="week-section week-section--materials">
				<div class="week-content">
					<?php the_content(); ?>
				</div>
			</section>

		</article>
		<?php endwhile; ?>

		<?php do_action( 'generate_after_main_content' ); ?>
	</main>
</div>

<?php
do_action( 'generate_after_primary_content_area' );
generate_construct_sidebars();
get_footer();
