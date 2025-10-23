<?php
/**
 * Myriam theme functions.
 *
 * @package Myriam
 */

// Enqueue frontend assets.
add_action(
	'wp_enqueue_scripts',
	function () {
		// Enqueue parent theme styles first.
		wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

		$theme_dir = get_stylesheet_directory();
		$theme_uri = get_stylesheet_directory_uri();
		$css_dir   = $theme_dir . '/css/build/';
		$js_dir    = $theme_dir . '/js/build/';

		// Enqueue CSS.
		foreach ( glob( $css_dir . 'theme.min.*.css' ) as $css_file ) {
			wp_enqueue_style(
				'myriam-theme-style',
				$theme_uri . '/css/build/' . basename( $css_file ),
				array( 'parent-style' ), // Load after parent theme.
				filemtime( $css_file )
			);
			break; // only the first match.
		}

		// Enqueue JS.
		foreach ( glob( $js_dir . 'main.min.*.js' ) as $js_file ) {
			wp_enqueue_script(
				'myriam-theme-script',
				$theme_uri . '/js/build/' . basename( $js_file ),
				array( 'wp-element', 'wp-hooks' ), // Add WordPress dependencies.
				filemtime( $js_file ),
				true
			);
			break;
		}
	},
	20
);

// Enqueue editor assets (makes Gutenberg match frontend).
add_action(
	'enqueue_block_editor_assets',
	function () {
		$theme_dir = get_stylesheet_directory();
		$theme_uri = get_stylesheet_directory_uri();

		// Enqueue editor CSS.
		$css_files = glob( $theme_dir . '/css/build/theme.min.*.css' );
		if ( ! empty( $css_files ) ) {
			wp_enqueue_style(
				'myriam-editor-styles',
				$theme_uri . '/css/build/' . basename( $css_files[0] ),
				array(),
				filemtime( $css_files[0] )
			);
		}

		// Enqueue editor JS.
		$js_files = glob( $theme_dir . '/js/build/main.min.*.js' );
		if ( ! empty( $js_files ) ) {
			wp_enqueue_script(
				'myriam-editor-scripts',
				$theme_uri . '/js/build/' . basename( $js_files[0] ),
				array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'wp-components', 'wp-element', 'wp-compose' ),
				filemtime( $js_files[0] ),
				true
			);
		}
	}
);

/**
 * Remove GeneratePress default fonts and use theme.json fonts
 *
 * @return void
 */
function remove_generatepress_default_fonts() {
	add_filter(
		'wp_theme_json_data_theme',
		function ( $theme_json ) {
			$data = $theme_json->get_data();

			// Read fonts from our theme.json file.
			$theme_json_path = get_stylesheet_directory() . '/theme.json';
			if ( file_exists( $theme_json_path ) ) {
				$our_theme_json = json_decode( file_get_contents( $theme_json_path ), true );
				$our_fonts      = $our_theme_json['settings']['typography']['fontFamilies'] ?? array();

				// Replace GeneratePress fonts with our theme.json fonts.
				if ( isset( $data['settings']['typography']['fontFamilies'] ) && ! empty( $our_fonts ) ) {
					$data['settings']['typography']['fontFamilies'] = $our_fonts;
				}
			}

			return new WP_Theme_JSON( $data );
		},
		10
	);
}
add_action( 'after_setup_theme', 'remove_generatepress_default_fonts', 10 );

/**
 * Remove page titles selectively for better design control.
 *
 * Removes automatic GeneratePress page titles on homepage and when
 * Post Title blocks are detected to prevent duplicate titles.
 *
 * @return void
 */
function customize_page_titles() {
	// Remove title on home page only.
	if ( is_front_page() ) {
		remove_action( 'generate_before_content', 'generate_page_header' );
		add_filter( 'generate_show_title', '__return_false' );
	}

	// Optional: Remove title on specific pages where you want to use the block instead.
	global $post;
	if ( is_page() && $post ) {
		// Check if page content contains the Page Title block.
		if ( has_block( 'core/post-title', $post->post_content ) ) {
			remove_action( 'generate_before_content', 'generate_page_header' );
			add_filter( 'generate_show_title', '__return_false' );
		}
	}
}
add_action( 'wp', 'customize_page_titles' );

/**
 * Remove featured images from pages.
 *
 * @return void
 */
function remove_featured_image_from_pages() {
    if (is_page()) {
        // Remove GeneratePress featured image action.
        remove_action('generate_before_content', 'generate_featured_page_header_inside_single', 10);
        remove_action('generate_after_header', 'generate_featured_page_header', 10);
        
        // Remove any other GeneratePress image hooks.
        add_filter('generate_show_featured_image', '__return_false');
    }
}
add_action('wp', 'remove_featured_image_from_pages');

/**
* Clean archive titles by removing WordPress default prefixes.
*
* Removes "Category:", "Tag:", "Author:", etc. prefixes from archive
* page titles and returns clean, plain text for theme styling.
*
* @param string $title The original archive title with prefix.
* @return string Clean title without prefix or HTML.
*/
function fm_clean_archive_title( $title ) {
   if ( is_category() || is_tag() || is_tax() ) {
       $title = single_term_title( '', false );
   } elseif ( is_post_type_archive() ) {
       $title = post_type_archive_title( '', false );
   } elseif ( is_author() ) {
       $author = get_queried_object();
       $title  = $author && isset( $author->display_name ) ? $author->display_name : '';
   } elseif ( is_year() ) {
       $title = get_the_date( _x( 'Y', 'yearly archives date format' ) );
   } elseif ( is_month() ) {
       $title = get_the_date( _x( 'F Y', 'monthly archives date format' ) );
   } elseif ( is_day() ) {
       $title = get_the_date( _x( 'F j, Y', 'daily archives date format' ) );
   }
   return $title; // return plain text; let templates handle markup/escaping
}
add_filter( 'get_the_archive_title', 'fm_clean_archive_title' );


/**
* Add custom CSS classes to body element on post type archive pages.
*
* @param array $classes Existing body classes.
* @return array Modified body classes array.
*/
function add_post_type_archive_body_classes($classes) {
   if (is_post_type_archive('writing')) {
       $classes[] = 'archive-writing';
       $classes[] = 'bg-brand-secondary'; // Semantic, won't break.
   }
   return $classes;
}
add_filter('body_class', 'add_post_type_archive_body_classes');

/**
 * Replace GeneratePress footer credits in place (no unhooking needed).
 */
add_filter( 'generate_copyright', function ( $original ) {
	$year = date_i18n( 'Y' );
	$site = get_bloginfo( 'name' );

	// Edit this to whatever you want:
	return sprintf(
		'<span class="copyright">&copy; %s %s</span> &bull; Designed and Built by <a href="https://former-model.com" target="_blank" rel="noopener">Former Model</a>.',
		esc_html( $year ),
		esc_html( $site )
	);
}, 10 );


/**
 * Include the `writing` CPT in tag archives.
 */
add_action( 'pre_get_posts', function ( WP_Query $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( $q->is_tag() ) {
		// Only writing posts with that tag:
		$q->set( 'post_type', [ 'writing' ] );

		// If you ALSO want regular blog posts, use:
		// $q->set( 'post_type', [ 'post', 'writing' ] );
	}
} );

/**
 * Use archive-writing.php to render tag archives.
 */
add_filter( 'template_include', function ( $template ) {
	if ( is_tag() ) {
		$alt = locate_template( 'archive-writing.php' );
		if ( $alt ) {
			return $alt;
		}
	}
	return $template;
} );

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
        'supports'            => array('title', 'thumbnail'), // Only title and featured image
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
    $date = get_post_meta($post->ID, '_event_date', true);
    $time = get_post_meta($post->ID, '_event_time', true);
    $location = get_post_meta($post->ID, '_event_location', true);
    $url = get_post_meta($post->ID, '_event_url', true);
    ?>
    <table class="form-table">
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
        'date'     => get_post_meta($post_id, '_event_date', true),
        'time'     => get_post_meta($post_id, '_event_time', true),
        'location' => get_post_meta($post_id, '_event_location', true),
        'url'      => get_post_meta($post_id, '_event_url', true),
    );
}