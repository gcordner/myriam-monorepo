<?php
/**
 * Myriam.
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

// Remove GeneratePress default fonts and use theme.json fonts.
function remove_generatepress_default_fonts() {
    add_filter('wp_theme_json_data_theme', function($theme_json) {
        $data = $theme_json->get_data();
        
        // Read fonts from our theme.json file
        $theme_json_path = get_stylesheet_directory() . '/theme.json';
        if (file_exists($theme_json_path)) {
            $our_theme_json = json_decode(file_get_contents($theme_json_path), true);
            $our_fonts = $our_theme_json['settings']['typography']['fontFamilies'] ?? [];
            
            // Replace GeneratePress fonts with our theme.json fonts
            if (isset($data['settings']['typography']['fontFamilies']) && !empty($our_fonts)) {
                $data['settings']['typography']['fontFamilies'] = $our_fonts;
            }
        }
        
        return new WP_Theme_JSON($data);
    }, 10);
}
add_action('after_setup_theme', 'remove_generatepress_default_fonts', 10);
