<?php
/**
 * Template Name: Login Page
 * Template Post Type: page
 *
 * Custom-styled login page for the Hard Stuff student portal.
 *
 * @package Myriam2026
 */

defined( 'ABSPATH' ) || exit;

if ( is_user_logged_in() ) {
	wp_safe_redirect( myriam2026_is_student() ? home_url( '/hard-stuff/' ) : admin_url() );
	exit;
}

$redirect_to = isset( $_GET['redirect_to'] )
	? wp_validate_redirect( wp_unslash( $_GET['redirect_to'] ), home_url( '/hard-stuff/' ) )
	: home_url( '/hard-stuff/' );

$has_error = ! empty( $_GET['login'] ) && 'failed' === $_GET['login'];

get_header();
?>

<div <?php generate_do_attr( 'content' ); ?>>
	<main <?php generate_do_attr( 'main' ); ?>>
		<?php do_action( 'generate_before_main_content' ); ?>

		<div class="login-page-wrap">
			<div class="login-card">

				<div class="login-card__header">
					<span class="login-card__class-name">Hard Stuff</span>
					<span class="login-card__site-name"><?php bloginfo( 'name' ); ?></span>
				</div>

				<?php if ( $has_error ) : ?>
				<div class="login-error" role="alert">
					<?php esc_html_e( 'Incorrect username or password. Please try again.', 'myriam2026' ); ?>
				</div>
				<?php endif; ?>

				<?php
				wp_login_form(
					array(
						'redirect'       => esc_url( $redirect_to ),
						'label_username' => __( 'Username or Email Address', 'myriam2026' ),
						'label_password' => __( 'Password', 'myriam2026' ),
						'label_remember' => __( 'Remember me', 'myriam2026' ),
						'label_log_in'   => __( 'Log In', 'myriam2026' ),
						'remember'       => true,
					)
				);
				?>

				<p class="login-forgot">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">
						<?php esc_html_e( 'Forgot your password?', 'myriam2026' ); ?>
					</a>
				</p>

			</div>
		</div>

		<?php do_action( 'generate_after_main_content' ); ?>
	</main>
</div>

<?php
do_action( 'generate_after_primary_content_area' );
generate_construct_sidebars();
get_footer();
