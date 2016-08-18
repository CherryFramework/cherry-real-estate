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
		<input type="text" id="tm-re-user-reg-login" name="user_login" placeholder="<?php esc_html_e( 'User Name', 'cherry-real-estate' ); ?>" required="required">
	</div>

	<div class="tm-re-register-form__group">
		<input type="email" id="tm-re-user-reg-email" name="user_email" placeholder="<?php esc_html_e( 'Email', 'cherry-real-estate' ); ?>" required="required">
	</div>

	<div class="tm-re-register-form__group">
		<button type="submit" class="tm-re-register-form__btn"><?php esc_html_e( 'Register', 'cherry-real-estate' ); ?></button>
	</div>

	<div class="tm-re-register-form__messages tm-re-messages">
		<span class="tm-re-register-form__success tm-re-hidden"><?php esc_html_e( 'Success', 'cherry-real-estate' ) ?></span>
		<span class="tm-re-register-form__error tm-re-hidden"></span>
	</div>
</form>
