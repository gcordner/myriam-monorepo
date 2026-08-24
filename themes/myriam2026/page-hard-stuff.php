<?php
/**
 * Portal index template — auto-selected for the page with slug "hard-stuff".
 *
 * @package Myriam2026
 */

defined( 'ABSPATH' ) || exit;

$week_pages = get_posts(
	array(
		'post_type'      => 'class_week',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'post_status'    => 'publish',
	)
);

get_header();
?>

<div <?php generate_do_attr( 'content' ); ?>>
	<main <?php generate_do_attr( 'main' ); ?>>
		<?php do_action( 'generate_before_main_content' ); ?>

		<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'portal-page' ); ?>>

			<header class="portal-header page-header">
				<h1 class="page-title"><?php the_title(); ?></h1>
			</header>

			<?php if ( get_the_content() ) : ?>
			<div class="portal-intro">
				<?php the_content(); ?>
			</div>
			<?php endif; ?>

			<?php if ( $week_pages ) : ?>
			<div class="week-grid">
				<?php foreach ( $week_pages as $week ) :
					$week_num  = get_post_meta( $week->ID, 'hs_week_number', true );
					$has_video = ! empty( get_post_meta( $week->ID, 'hs_vimeo_url', true ) );
				?>
				<a href="<?php echo esc_url( get_permalink( $week->ID ) ); ?>" class="week-card">
					<span class="week-card__number">
						<?php echo $week_num
							? esc_html( 'Week ' . $week_num )
							: esc_html__( 'Week', 'myriam2026' ); ?>
					</span>
					<span class="week-card__title"><?php echo esc_html( $week->post_title ); ?></span>
					<?php if ( $has_video ) : ?>
					<span class="week-card__badge"><?php esc_html_e( '&#9654; Recording available', 'myriam2026' ); ?></span>
					<?php endif; ?>
					<span class="week-card__arrow" aria-hidden="true">&#8594;</span>
				</a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

		</article>
		<?php endwhile; ?>

		<?php do_action( 'generate_after_main_content' ); ?>
	</main>
</div>

<?php
do_action( 'generate_after_primary_content_area' );
generate_construct_sidebars();
get_footer();
