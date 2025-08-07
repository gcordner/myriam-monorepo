<?php

if (! defined('WP_DEBUG')) {
	die( 'Direct access forbidden.' );
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
});


add_filter('blocksy:archive:render-card-layers', function ($layers, $prefix, $featured_image_args) {
    return [
        // Only include the parts you want, in your preferred order:
        'featured_image' => $layers['featured_image'],
        'title' => $layers['title'],
        'excerpt' => $layers['excerpt'],
    ];
}, 10, 3);
