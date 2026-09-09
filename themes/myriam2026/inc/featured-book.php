<?php
/**
 * "Featured Book" block (see notes/events.md-style plan for the "New book"
 * spot on the homepage). Pulls title/subtitle/image/link/awards from the
 * selected `book` CPT post; the eyebrow label, blurbs, and button text are
 * curated per-placement, so they live on the block itself.
 *
 * @package Myriam2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the block.
 */
function myriam2026_register_featured_book_block() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	acf_register_block_type(
		array(
			'name'            => 'featured-book',
			'title'           => __( 'Featured Book', 'myriam2026' ),
			'description'     => __( 'The homepage "new book" spot — pulls title/subtitle/image/link/awards from a selected book, with curated blurbs.', 'myriam2026' ),
			'render_template' => get_stylesheet_directory() . '/blocks/featured-book/render.php',
			'category'        => 'widgets',
			'icon'            => 'book-alt',
			'keywords'        => array( 'book', 'featured' ),
			'supports'        => array(
				'align' => array( 'full' ),
				'mode'  => false,
			),
		)
	);
}
add_action( 'acf/init', 'myriam2026_register_featured_book_block' );

/**
 * Register the block's own field group (book, eyebrow text, blurbs, button
 * text). Awards live on the `book` CPT's own field group instead, since
 * they're a fact about the book, not a per-placement curatorial choice.
 */
function myriam2026_register_featured_book_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_featured_book',
			'title'    => 'Featured Book Block',
			'fields'   => array(
				array(
					'key'           => 'field_featured_book_book',
					'label'         => 'Book',
					'name'          => 'book',
					'type'          => 'post_object',
					'post_type'     => array( 'book' ),
					'return_format' => 'id',
					'required'      => 1,
					'instructions'  => 'Title, subtitle, cover image, link, and awards all come from this book.',
				),
				array(
					'key'           => 'field_featured_book_eyebrow',
					'label'         => 'Eyebrow text',
					'name'          => 'eyebrow_text',
					'type'          => 'text',
					'default_value' => 'New book',
				),
				array(
					'key'          => 'field_featured_book_blurbs',
					'label'        => 'Blurbs',
					'name'         => 'blurbs',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Add blurb',
					'sub_fields'   => array(
						array(
							'key'   => 'field_featured_book_blurb_quote',
							'label' => 'Quote',
							'name'  => 'quote',
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'   => 'field_featured_book_blurb_citation',
							'label' => 'Citation',
							'name'  => 'citation',
							'type'  => 'text',
						),
					),
				),
				array(
					'key'           => 'field_featured_book_button_text',
					'label'         => 'Button text',
					'name'          => 'button_text',
					'type'          => 'text',
					'default_value' => 'Read more reviews',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/featured-book',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'myriam2026_register_featured_book_fields' );
