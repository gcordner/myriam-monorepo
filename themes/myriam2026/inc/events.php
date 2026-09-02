<?php
/**
 * Events: custom post type, meta box, and detail helper.
 *
 * @package Myriam2026
 */

defined( 'ABSPATH' ) || exit;

// Register Events Custom Post Type
function create_events_post_type() {
    $labels = array(
        'name'               => 'Events',
        'singular_name'      => 'Event',
        'menu_name'          => 'Events',
        'add_new'            => 'Add New Event',
        'add_new_item'       => 'Add New Event',
        'edit_item'          => 'Edit Event',
        'new_item'           => 'New Event',
        'view_item'          => 'View Event',
        'search_items'       => 'Search Events',
        'not_found'          => 'No events found',
        'not_found_in_trash' => 'No events found in trash',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true, // Enables Gutenberg
        'menu_icon'           => 'dashicons-calendar-alt',
        'supports'            => array('title', 'thumbnail', 'editor'), // title, featured image, and a WYSIWYG short description
        'rewrite'             => array('slug' => 'events'),
        'capability_type'     => 'post',
    );

    register_post_type('event', $args);
}
add_action('init', 'create_events_post_type');

// Add custom meta boxes for event details
function add_event_meta_boxes() {
    add_meta_box(
        'event_details',
        'Event Details',
        'render_event_meta_box',
        'event',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_event_meta_boxes');

// Render the meta box
function render_event_meta_box($post) {
    // Add nonce for security
    wp_nonce_field('save_event_details', 'event_details_nonce');

    // Get existing values
    $subtitle = get_post_meta($post->ID, '_event_subtitle', true);
    $date = get_post_meta($post->ID, '_event_date', true);
    $time = get_post_meta($post->ID, '_event_time', true);
    $location = get_post_meta($post->ID, '_event_location', true);
    $url = get_post_meta($post->ID, '_event_url', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="event_subtitle">Subtitle</label></th>
            <td><input type="text" id="event_subtitle" name="event_subtitle" value="<?php echo esc_attr($subtitle); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="event_date">Date</label></th>
            <td><input type="date" id="event_date" name="event_date" value="<?php echo esc_attr($date); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="event_time">Time</label></th>
            <td><input type="time" id="event_time" name="event_time" value="<?php echo esc_attr($time); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="event_location">Location</label></th>
            <td><input type="text" id="event_location" name="event_location" value="<?php echo esc_attr($location); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="event_url">Event URL</label></th>
            <td><input type="url" id="event_url" name="event_url" value="<?php echo esc_attr($url); ?>" class="regular-text"></td>
        </tr>
    </table>
    <?php
}

// Save the meta box data
function save_event_details($post_id) {
    // Verify nonce
    if (!isset($_POST['event_details_nonce']) || !wp_verify_nonce($_POST['event_details_nonce'], 'save_event_details')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save fields
    if (isset($_POST['event_subtitle'])) {
        update_post_meta($post_id, '_event_subtitle', sanitize_text_field($_POST['event_subtitle']));
    }
    if (isset($_POST['event_date'])) {
        update_post_meta($post_id, '_event_date', sanitize_text_field($_POST['event_date']));
    }
    if (isset($_POST['event_time'])) {
        update_post_meta($post_id, '_event_time', sanitize_text_field($_POST['event_time']));
    }
    if (isset($_POST['event_location'])) {
        update_post_meta($post_id, '_event_location', sanitize_text_field($_POST['event_location']));
    }
    if (isset($_POST['event_url'])) {
        update_post_meta($post_id, '_event_url', esc_url_raw($_POST['event_url']));
    }
}
add_action('save_post_event', 'save_event_details');

// Helper function to get event details
function get_event_details($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    return array(
        'subtitle' => get_post_meta($post_id, '_event_subtitle', true),
        'date'     => get_post_meta($post_id, '_event_date', true),
        'time'     => get_post_meta($post_id, '_event_time', true),
        'location' => get_post_meta($post_id, '_event_location', true),
        'url'      => get_post_meta($post_id, '_event_url', true),
    );
}
