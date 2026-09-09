<?php
/**
 * "Backlist" block — a curated grid of up to 4 earlier books, each with a
 * one-line accolade (the `backlist_blurb` field on the `book` CPT). See
 * notes/backlist.md.
 *
 * @package Myriam2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the block.
 */
function myriam2026_register_backlist_block() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	acf_register_block_type(
		array(
			'name'            => 'backlist',
			'title'           => __( 'Backlist', 'myriam2026' ),
			'description'     => __( 'A curated grid of up to 4 earlier books, each with a one-line accolade.', 'myriam2026' ),
			'render_template' => get_stylesheet_directory() . '/blocks/backlist/render.php',
			'category'        => 'widgets',
			'icon'            => 'grid-view',
			'keywords'        => array( 'books', 'backlist' ),
			'supports'        => array(
				'align' => array( 'full' ),
				'mode'  => false,
			),
		)
	);
}
add_action( 'acf/init', 'myriam2026_register_backlist_block' );

/**
 * Register the block's own field group — just which books to feature.
 * Title/cover/accolade/link are all pulled from each selected book at
 * render time.
 */
function myriam2026_register_backlist_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_backlist',
			'title'    => 'Backlist Block',
			'fields'   => array(
				array(
					'key'           => 'field_backlist_books',
					'label'         => 'Books',
					'name'          => 'books',
					'type'          => 'relationship',
					'post_type'     => array( 'book' ),
					'filters'       => array( 'search' ),
					'max'           => 4,
					'return_format' => 'id',
					'instructions'  => 'Pick up to 4 books to feature, in order.',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/backlist',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'myriam2026_register_backlist_fields' );
