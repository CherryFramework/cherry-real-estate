<?php
/**
 * Message with login/register links.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/auth/popup.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

if ( is_user_logged_in() ) {
	return;
} ?>

<div class="tm-re-auth-message">
	<?php printf(
		__( 'Please <a class="%1$s" href="#%2$s" data-tab="0">login</a> or <a class="%1$s" href="#%2$s" data-tab="1">register</a> to create a new listing', 'cherry-real-estate' ),
		esc_attr( $passed_vars['class'] ),
		esc_attr( $passed_vars['href'] )
	); ?>
</div>
