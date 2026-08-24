<?php
/**
 * Student portal: roles, access control, nav, login, and week page meta.
 *
 * @package Myriam
 */

defined( 'ABSPATH' ) || exit;

// ── Helpers ──────────────────────────────────────────────────────────────────

/**
 * True if the current (or given) user can access the student portal.
 * Admins always pass; others need the 'student' role.
 */
function myriam_is_student( int $user_id = 0 ): bool {
	$user = $user_id ? get_user_by( 'id', $user_id ) : wp_get_current_user();
	if ( ! $user || ! $user->ID ) {
		return false;
	}
	return user_can( $user, 'manage_options' )
		|| in_array( 'student', (array) $user->roles, true );
}

/**
 * Converts any Vimeo URL to an embed URL.
 * Handles public (vimeo.com/ID) and private (vimeo.com/ID/HASH) formats.
 */
function myriam_vimeo_embed_url( string $url ): string {
	$url = trim( $url );
	if ( empty( $url ) ) {
		return '';
	}
	if ( strpos( $url, 'player.vimeo.com' ) !== false ) {
		return $url;
	}
	if ( preg_match( '#vimeo\.com/(\d+)(?:/([a-f0-9]+))?#i', $url, $m ) ) {
		$embed = 'https://player.vimeo.com/video/' . $m[1];
		if ( ! empty( $m[2] ) ) {
			$embed .= '?h=' . $m[2];
		}
		return $embed;
	}
	return '';
}

// ── Class Week custom post type ───────────────────────────────────────────────

add_action(
	'init',
	function (): void {
		register_post_type(
			'class_week',
			array(
				'labels'        => array(
					'name'          => __( 'Class Weeks', 'myriam' ),
					'singular_name' => __( 'Class Week', 'myriam' ),
					'add_new_item'  => __( 'Add New Week', 'myriam' ),
					'edit_item'     => __( 'Edit Week', 'myriam' ),
					'all_items'     => __( 'All Weeks', 'myriam' ),
				),
				'public'        => true,
				'show_in_rest'  => true,
				'supports'      => array( 'title', 'editor', 'page-attributes' ),
				'menu_icon'     => 'dashicons-video-alt3',
				'menu_position' => 20,
				'rewrite'       => array( 'slug' => 'hard-stuff' ),
				'has_archive'   => false,
			)
		);
	}
);

// Force single-class-week.php for all class_week posts, regardless of any
// page-template meta set via the block editor's Template dropdown.
add_filter(
	'template_include',
	function ( string $template ): string {
		if ( is_singular( 'class_week' ) ) {
			$custom = get_stylesheet_directory() . '/single-class-week.php';
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}
		return $template;
	},
	100
);

// Flush rewrite rules once after the CPT is first registered, and again any
// time the theme is reactivated.  Uses a versioned option so it only runs once
// per version bump.
add_action( 'after_switch_theme', 'myriam_schedule_rewrite_flush' );
function myriam_schedule_rewrite_flush(): void {
	set_transient( 'myriam_flush_rewrite', 1 );
}

add_action(
	'init',
	function (): void {
		$version = '1';
		if ( get_transient( 'myriam_flush_rewrite' ) || get_option( 'myriam_rewrite_version' ) !== $version ) {
			flush_rewrite_rules();
			update_option( 'myriam_rewrite_version', $version );
			delete_transient( 'myriam_flush_rewrite' );
		}
	},
	999
);

// ── Conditional nav: hide student-only items from non-students ───────────────

add_filter(
	'wp_nav_menu_objects',
	function ( array $items, $args ): array {
		if ( myriam_is_student() ) {
			return $items;
		}
		return array_values(
			array_filter(
				$items,
				fn( $item ) => ! in_array( 'student-only', (array) $item->classes, true )
			)
		);
	},
	10,
	2
);

// ── Footer menu: append dynamic Log In / Log Out link ────────────────────────

add_filter(
	'wp_nav_menu_items',
	function ( string $items, $args ): string {
		if ( ! isset( $args->theme_location ) || $args->theme_location !== 'footer' ) {
			return $items;
		}
		if ( is_user_logged_in() ) {
			$label = esc_html__( 'Log Out', 'myriam' );
			$url   = wp_logout_url( home_url( '/' ) );
		} else {
			$label = esc_html__( 'Log In', 'myriam' );
			$url   = wp_login_url( get_permalink() ?: home_url( '/' ) );
		}
		return $items . sprintf(
			'<li class="menu-item login-logout-link"><a href="%s">%s</a></li>',
			esc_url( $url ),
			$label
		);
	},
	10,
	2
);

// ── Custom login page ─────────────────────────────────────────────────────────

// Make wp_login_url() return the custom /login/ page when it exists.
add_filter(
	'login_url',
	function ( string $login_url, string $redirect ): string {
		$login_page = get_page_by_path( 'login' );
		if ( ! $login_page ) {
			return $login_url;
		}
		$url = get_permalink( $login_page->ID );
		if ( $redirect ) {
			$url = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $url );
		}
		return $url;
	},
	10,
	2
);

// Redirect wp-login.php GET requests to the custom page.
add_action(
	'login_init',
	function (): void {
		if ( strtoupper( $_SERVER['REQUEST_METHOD'] ) !== 'GET' ) {
			return;
		}
		$action = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : 'login';
		if ( $action !== 'login' ) {
			return;
		}
		$login_page = get_page_by_path( 'login' );
		if ( ! $login_page ) {
			return;
		}
		$redirect_to = isset( $_GET['redirect_to'] )
			? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) )
			: home_url( '/hard-stuff/' );
		wp_safe_redirect(
			add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), get_permalink( $login_page->ID ) )
		);
		exit;
	}
);

// On failed login, redirect back to the custom page with an error flag.
add_action(
	'wp_login_failed',
	function (): void {
		$login_page = get_page_by_path( 'login' );
		if ( $login_page ) {
			wp_safe_redirect( add_query_arg( 'login', 'failed', get_permalink( $login_page->ID ) ) );
			exit;
		}
	}
);

// After a successful login, send students to the portal.
add_filter(
	'login_redirect',
	function ( string $redirect_to, string $requested_redirect_to, $user ): string {
		if ( $user && ! is_wp_error( $user ) && myriam_is_student( $user->ID ) ) {
			if ( $requested_redirect_to && strpos( $requested_redirect_to, home_url() ) === 0 ) {
				return $requested_redirect_to;
			}
			return home_url( '/hard-stuff/' );
		}
		return $redirect_to;
	},
	10,
	3
);

// WooCommerce has its own login redirect that overrides login_redirect.
add_filter(
	'woocommerce_login_redirect',
	function ( string $redirect, \WC_Customer $customer ): string {
		if ( myriam_is_student( $customer->get_id() ) ) {
			return home_url( '/hard-stuff/' );
		}
		return $redirect;
	},
	10,
	2
);

// ── Access control: gate the portal page and all class week posts ─────────────

add_action(
	'template_redirect',
	function (): void {
		if ( ! is_page( 'hard-stuff' ) && ! is_singular( 'class_week' ) ) {
			return;
		}
		if ( ! myriam_is_student() ) {
			wp_safe_redirect( wp_login_url( get_permalink() ) );
			exit;
		}
	}
);

// ── Meta box: class week fields ───────────────────────────────────────────────

add_action(
	'add_meta_boxes',
	function (): void {
		add_meta_box(
			'hs_week_details',
			__( 'Class Week Details', 'myriam' ),
			'myriam_render_week_metabox',
			'class_week',
			'normal',
			'high'
		);
	}
);

function myriam_render_week_metabox( WP_Post $post ): void {
	wp_nonce_field( 'hs_week_details_save', 'hs_week_nonce' );
	$week_num     = get_post_meta( $post->ID, 'hs_week_number', true );
	$vimeo_url    = get_post_meta( $post->ID, 'hs_vimeo_url', true );
	$slides_url   = get_post_meta( $post->ID, 'hs_slides_url', true );
	$slides_label = get_post_meta( $post->ID, 'hs_slides_label', true );
	?>
	<style>
		.hs-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: .5rem; }
		.hs-meta__field label { display: block; font-weight: 600; margin-bottom: 3px; }
		.hs-meta__field input { width: 100%; }
		.hs-meta__field--full { grid-column: 1 / -1; }
	</style>
	<div class="hs-meta">
		<div class="hs-meta__field">
			<label for="hs_week_number"><?php esc_html_e( 'Week Number', 'myriam' ); ?></label>
			<input type="number" id="hs_week_number" name="hs_week_number"
				value="<?php echo esc_attr( $week_num ); ?>" min="1" max="10" />
		</div>
		<div class="hs-meta__field">
			<label for="hs_slides_label"><?php esc_html_e( 'Slides Button Label', 'myriam' ); ?></label>
			<input type="text" id="hs_slides_label" name="hs_slides_label"
				value="<?php echo esc_attr( $slides_label ?: 'Download Slides' ); ?>" />
		</div>
		<div class="hs-meta__field hs-meta__field--full">
			<label for="hs_vimeo_url"><?php esc_html_e( 'Vimeo Recording URL (leave empty if not recorded)', 'myriam' ); ?></label>
			<input type="url" id="hs_vimeo_url" name="hs_vimeo_url"
				value="<?php echo esc_attr( $vimeo_url ); ?>"
				placeholder="https://vimeo.com/123456789" />
		</div>
		<div class="hs-meta__field hs-meta__field--full">
			<label for="hs_slides_url"><?php esc_html_e( 'Slides URL (leave empty if no slides)', 'myriam' ); ?></label>
			<input type="url" id="hs_slides_url" name="hs_slides_url"
				value="<?php echo esc_attr( $slides_url ); ?>"
				placeholder="https://drive.google.com/..." />
		</div>
	</div>
	<p class="description" style="margin-top: 8px;">
		<?php esc_html_e( 'Notes and suggested reading go in the block editor below. Empty URL fields hide that section from students.', 'myriam' ); ?>
	</p>
	<?php
}

add_action(
	'save_post_class_week',
	function ( int $post_id ): void {
		if (
			! isset( $_POST['hs_week_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['hs_week_nonce'] ), 'hs_week_details_save' )
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			|| ! current_user_can( 'edit_post', $post_id )
		) {
			return;
		}
		$fields = array(
			'hs_week_number'  => 'absint',
			'hs_vimeo_url'    => 'esc_url_raw',
			'hs_slides_url'   => 'esc_url_raw',
			'hs_slides_label' => 'sanitize_text_field',
		);
		foreach ( $fields as $key => $sanitizer ) {
			if ( array_key_exists( $key, $_POST ) ) {
				update_post_meta( $post_id, $key, $sanitizer( wp_unslash( $_POST[ $key ] ) ) );
			}
		}
	}
);
