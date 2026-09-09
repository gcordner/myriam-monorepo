<?php
/**
 * "Site Content" ACF Options Page — a single home for global content
 * fields that need to be edited once and reused in many places (starting
 * with the author bio; see notes/bio.md). Deliberately one general
 * options page rather than a new one per feature, to avoid settings
 * sprawl.
 *
 * @package Myriam2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the options page. The field group itself is added via
 * wp-admin (Custom Fields > Field Groups), same as the book CPT's fields
 * — not code-registered, so it's editable without a deploy.
 */
function myriam2026_register_site_content_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Site Content', 'myriam2026' ),
			'menu_title' => __( 'Site Content', 'myriam2026' ),
			'menu_slug'  => 'site-content',
			'capability' => 'edit_posts',
			'redirect'   => false,
		)
	);
}
add_action( 'acf/init', 'myriam2026_register_site_content_options_page' );
