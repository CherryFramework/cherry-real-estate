<?php
/**
 * Authorization Popup.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/misc/auth-popup.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */
?>

<!-- <div id="tm-re-auth-popup" class="tm-re-auth-popup mfp-hide"> -->
<div id="tm-re-auth-popup" class="tm-re-auth-popup">
	<ul>
		<li><a href="#tabs-1">Log In</a></li>
		<li><a href="#tabs-2">Register</a></li>
	</ul>
	<div id="tabs-1">
		<h3>Login Form</h3>
		<?php echo $login_form; ?>
	</div>
	<div id="tabs-2">
		<?php echo $register_form; ?>
	</div>
</div>

<?php // Enqueue a script.
cherry_re_enqueue_script( array( 'jquery-magnific-popup', 'jquery-ui-tabs', 'cherry-re-script' ) ); ?>
