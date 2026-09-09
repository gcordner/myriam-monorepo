<?php
/**
 * "Bio" block — displays the author bio set once on the Site Content
 * options page (see inc/site-content.php), so it can be placed on many
 * pages while staying a single source of truth. No per-instance
 * configuration, same "place it and it works" pattern as upcoming-events.
 *
 * @package Myriam2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the block.
 */
function myriam2026_register_bio_block() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	acf_register_block_type(
		array(
			'name'            => 'bio',
			'title'           => __( 'Bio', 'myriam2026' ),
			'description'     => __( 'Displays the author bio from Site Content. No per-instance configuration.', 'myriam2026' ),
			'render_template' => get_stylesheet_directory() . '/blocks/bio/render.php',
			'category'        => 'widgets',
			'icon'            => 'admin-users',
			'keywords'        => array( 'bio', 'about' ),
			'supports'        => array(
				'align' => array( 'full' ),
				'mode'  => false,
			),
		)
	);
}
add_action( 'acf/init', 'myriam2026_register_bio_block' );
