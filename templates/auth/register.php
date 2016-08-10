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
		<input type="text" id="tm-re-user-login" name="user_login" placeholder="<?php esc_html_e( 'Username', 'cherry-real-estate' ); ?>" required="required">
	</div>

	<div class="tm-re-register-form__group">
		<input type="email" id="tm-re-user-email" name="user_email" placeholder="<?php esc_html_e( 'Email', 'cherry-real-estate' ); ?>" required="required">
	</div>

	<div class="tm-re-register-form__group">
		<input type="password" id="user_pass" name="user_pass" placeholder="<?php esc_html_e( 'Password', 'cherry-real-estate' ); ?>" required="required">
	</div>

	<div class="tm-re-register-form__group">
		<input type="password" id="user_cpass" name="user_cpass" placeholder="<?php esc_html_e( 'Confirm Password', 'cherry-real-estate' ); ?>" required="required">
	</div>

	<div class="tm-re-register-form__group">
		<button type="submit" class="tm-re-register-form__btn"><?php esc_html_e( 'Register', 'cherry-real-estate' ); ?></button>
	</div>

	<div class="tm-re-register-form__messages">
		<div class="tm-re-register-form__success tm-re-hidden"><?php esc_html_e( 'Success', 'cherry-real-estate' ) ?></div>
		<div class="tm-re-register-form__error tm-re-hidden"></div>
	</div>
</form>
