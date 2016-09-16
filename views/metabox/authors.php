<?php
/**
 * Authors metabox view.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Views
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

$key   = $passed_vars['key'];
$value = $passed_vars['value'];
$users = $passed_vars['users'];
$nonce = $passed_vars['nonce']; ?>

<div class="cherry-re-custom-authors">
	<input type="hidden" name="cherry_re_custom_authors_meta_nonce" value="<?php echo $nonce; ?>" />

	<select name="<?php echo $key; ?>">

		<?php foreach ( $users as $user ) : ?>

			<option value="<?php echo esc_attr( $user->ID ); ?>" <?php selected( $user->ID, $value ); ?>><?php echo esc_html( $user->display_name ); ?>&nbsp;(<?php echo esc_html( $user->user_login ); ?>)</option>

		<?php endforeach; ?>

	</select>
</div><!-- .cherry-re-custom-authors -->
