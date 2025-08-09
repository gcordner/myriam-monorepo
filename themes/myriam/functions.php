<?php
/**
 * Myriam Theme Functions
 *
 * @package Myriam
 */

if ( ! defined( 'WP_DEBUG' ) ) {
	die( 'Direct access forbidden.' );
}

// Enable block styles (required for theme.json support).
add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
	}
);

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

// Add this RIGHT AFTER the glob() calls to see what's happening.
// add_action('wp_head', function() {
//     $theme_dir = get_stylesheet_directory();
//     $css_dir   = $theme_dir . '/css/build/';
//     $css_files = glob($css_dir . 'theme.min.*.css');
    
    // echo "<!-- DEBUG: CSS dir: $css_dir -->\n";
    // echo "<!-- DEBUG: CSS files found: " . print_r($css_files, true) . " -->\n";
    // echo "<!-- DEBUG: Theme dir: $theme_dir -->\n";
    // echo "<!-- DEBUG: Theme URI: " . get_stylesheet_directory_uri() . " -->\n";
// });

add_filter(
	'blocksy:archive:render-card-layers',
	function ( $layers, $prefix, $featured_image_args ) {
		return array(
			// Only include the parts you want, in your preferred order.
			'featured_image' => $layers['featured_image'],
			'title'          => $layers['title'],
			'excerpt'        => $layers['excerpt'],
		);
	},
	10,
	3
);

/**
 * Print overrides.css inline at the very end of <head>, after Blocksy's inline CSS.
 */
// add_action('wp_head', function () {
// 	if ( is_admin() ) return; // front-end only

// 	$path = get_stylesheet_directory() . '/css/build/overrides.css';
// 	if ( ! file_exists($path) ) return;

// 	$css = trim(file_get_contents($path));
// 	if ( $css === '' ) return;

// 	// Force-win with !important if you want absolute precedence on vars/rules
// 	echo "\n<style id='myriam-overrides-inline'>\n{$css}\n</style>\n";
// }, 99999); // very late so it prints after ct-main-styles-inline-css


