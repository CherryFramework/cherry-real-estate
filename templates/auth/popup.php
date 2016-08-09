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
	<ul class="tabs">
		<li class="labels">
			<label for="tab1" id="label1">Log In</label>
			<label for="tab2" id="label2">Register</label>
		</li>
		<li>
			<input type="radio" checked name="tabs" id="tab1">
			<div id="tab-content1" class="tab-content">
				<h3>Login Form</h3>
				<?php echo $login_form; ?>
			</div>
		</li>
		<li>
			<input type="radio" name="tabs" id="tab2">
			<div id="tab-content2" class="tab-content">
				<h3>Register Form</h3>
				<?php echo $register_form; ?>
			</div>
		</li>
	</ul>
</div>

<?php // Enqueue a script.
cherry_re_enqueue_script( array( 'jquery-magnific-popup', 'cherry-re-script' ) ); ?>
