<?php
/**
 * The template used for displaying custom event archive content.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$event = get_event_details();
$event_url = $event['url'] ?: get_permalink();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'event-card' ); ?>>
	<a href="<?php echo esc_url( $event_url ); ?>" <?php echo $event['url'] ? 'target="_blank" rel="noopener"' : ''; ?> class="event-link">
		
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="event-image">
				<?php the_post_thumbnail( 'large' ); ?>
			</div>
		<?php endif; ?>

		<div class="event-info">
			<?php
			$date_parts = array();
			
			if ( $event['date'] ) {
				$date_parts[] = date_i18n( 'M j', strtotime( $event['date'] ) );
			}
			
			if ( $event['location'] ) {
				$date_parts[] = esc_html( $event['location'] );
			}
			
			if ( $event['time'] ) {
				$date_parts[] = date_i18n( 'g:i A', strtotime( $event['time'] ) );
			}
			
			if ( ! empty( $date_parts ) ) {
				echo '<div class="event-details">' . implode( ' &mdash; ', $date_parts ) . '</div>';
			}
			?>
		</div>

	</a>
</article>