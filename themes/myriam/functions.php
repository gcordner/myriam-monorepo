<?php
/**
 * Myriam.
 *
 * @package Myriam
 */

/**
 * Enqueue parent theme styles
 *
 * @return void
 */
function generatepress_child_enqueue_styles() {
	wp_enqueue_style( 'generatepress-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'generatepress-child-style', get_stylesheet_directory_uri() . '/style.css', array( 'generatepress-style' ) );
}
add_action( 'wp_enqueue_scripts', 'generatepress_child_enqueue_styles' );
