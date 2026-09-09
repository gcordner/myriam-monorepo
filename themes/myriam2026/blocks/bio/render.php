<?php
/**
 * Bio block render template.
 *
 * @package Myriam2026
 */

defined( 'ABSPATH' ) || exit;

$bio = get_field( 'bio_text', 'option' );

if ( ! $bio ) {
	if ( is_admin() ) {
		echo '<p>' . esc_html__( 'Bio: add the author bio under Site Content.', 'myriam2026' ) . '</p>';
	}
	return;
}
?>
<section class="bio paper-ground">
	<div class="bio-inner">
		<h2 class="section-label"><?php esc_html_e( 'Bio', 'myriam2026' ); ?></h2>
		<?php echo wp_kses_post( $bio ); ?>
	</div>
</section>
