<?php
/**
 * Server-side render for myriam2026/upcoming-events.
 *
 * Layout is chosen purely by how many upcoming events the query returns
 * (see myriam2026_get_upcoming_events() in inc/events.php) — 0 renders
 * nothing, 1 is the single "flyer" layout below, 2 and 3 each have their
 * own tracked issue and aren't built yet (see the TODOs below).
 *
 * @package Myriam2026
 */

defined( 'ABSPATH' ) || exit;

$upcoming_events = myriam2026_get_upcoming_events();
$count            = count( $upcoming_events );

if ( 0 === $count ) {
    return;
}

if ( 2 === $count ) {
    // TODO: "Events homepage block — 2 events view" — stacked, mirrored
    // flyer layout (see notes/events.md). Not built yet.
    return;
}

if ( 3 === $count ) {
    // TODO: "Events homepage block — 3 events view" — 3-up card grid
    // (see notes/events.md). Not built yet.
    return;
}

$post    = $upcoming_events[0];
$post_id = $post->ID;
$event   = get_event_details( $post_id );
$url     = $event['url'] ?: get_permalink( $post_id );

$when_parts = array();
if ( $event['date'] ) {
    $when_parts[] = esc_html( date_i18n( 'D, M j', strtotime( $event['date'] ) ) );
}
if ( $event['time'] ) {
    $when_parts[] = esc_html( date_i18n( 'g:i A', strtotime( $event['date'] . ' ' . $event['time'] ) ) );
}
if ( $event['location'] ) {
    $when_parts[] = esc_html( $event['location'] );
}
$when_html = implode( '<span class="sep">&middot;</span>', $when_parts );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'upcoming-events' ) );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="upcoming-events-inner">
		<h2 class="section-label">Upcoming</h2>

		<div class="event-solo">
			<div class="pinned-cover">
				<div class="pin" aria-hidden="true"></div>
				<?php echo get_the_post_thumbnail( $post_id, 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="event-body">
				<?php if ( $when_html ) : ?>
					<span class="event-when"><?php echo $when_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
				<h3><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
				<?php if ( $event['subtitle'] ) : ?>
					<p class="event-subtitle"><?php echo esc_html( $event['subtitle'] ); ?></p>
				<?php endif; ?>
				<?php if ( $post->post_content ) : ?>
					<div class="event-desc"><?php echo apply_filters( 'the_content', $post->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
				<a class="stamp-btn on-teal-deep" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener">Event details</a>
			</div>
		</div>
	</div>
</section>
