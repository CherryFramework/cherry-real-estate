<?php
/**
 * Register Form.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/auth/register.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */
?>

<form method="post" id="tm-re-registerform" class="tm-re-register-form">
	<?php wp_nonce_field( '_tm-re-register-form', 'tm-re-registerform-nonce' ); ?>

	<div class="tm-re-register-form__group">
		<input type="text" id="tm-re-user-login" name="tm-re-user-login" placeholder="<?php esc_html_e( 'Username', 'cherry-real-estate' ); ?>">
	</div>

	<div class="tm-re-register-form__group">
		<input type="text" id="tm-re-user-email" name="tm-re-user-email" placeholder="<?php esc_html_e( 'Email', 'cherry-real-estate' ); ?>">
	</div>

	<div class="tm-re-register-form__group">
		<input type="password" id="tm-re-user-pass" name="tm-re-user-pass" placeholder="<?php esc_html_e( 'Password', 'cherry-real-estate' ); ?>">
	</div>

	<div class="tm-re-register-form__group">
		<input type="password" id="tm-re-user-confirm-pass" name="tm-re-user-confirm-pass" placeholder="<?php esc_html_e( 'Conform Password', 'cherry-real-estate' ); ?>">
	</div>

	<div class="tm-re-register-form__group">
		<button type="submit" class="tm-re-register-form__btn"><?php esc_html_e( 'Register', 'cherry-real-estate' ); ?></button>
	</div>

	<div class="tm-re-register-form__messages">
		<div class="tm-re-register-form__success tm-re-hidden"><?php esc_html_e( 'Success', 'cherry-real-estate' ) ?></div>
		<div class="tm-re-register-form__error tm-re-hidden"></div>
	</div>
</form>
