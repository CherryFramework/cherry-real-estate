<?php
/**
 * Authorization Popup.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/auth/popup.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

do_action( 'cherry_re_before_auth_popup' ); ?>

<div id="<?php echo esc_attr( $popup_id ); ?>" class="tm-re-auth-popup mfp-with-anim mfp-hide" data-anim-effect="tm-re-mfp-move-from-top">
	<ul>
		<li><a href="#tm-re-login-form"><?php esc_html_e( 'Login', 'cherry-real-estate' ); ?></a></li>
		<li><a href="#tm-re-register-form"><?php esc_html_e( 'Register', 'cherry-real-estate' ); ?></a></li>
	</ul>
	<div id="tm-re-login-form">
		<?php echo $login_form; ?>
	</div>
	<div id="tm-re-register-form">
		<?php echo $register_form; ?>
	</div>
</div>

<?php do_action( 'cherry_re_after_auth_popup' ); ?>

<?php // Enqueue a popup assets.
wp_enqueue_style( 'jquery-magnific-popup' );
cherry_re_enqueue_script( array( 'jquery-magnific-popup', 'jquery-ui-tabs' ) ); ?>
