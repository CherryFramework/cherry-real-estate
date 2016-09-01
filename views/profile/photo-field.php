<?php
/**
 * Agent photo view.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Views
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */
?>

<h2><?php esc_html_e( 'Photo', 'cherry-real-estate' ); ?></h2>

<table class="form-table">
	<tr>
		<th>
			<label for="<?php echo esc_attr( $control_name ); ?>"><?php _e( 'Custom Photo', 'cherry-real-estate' ); ?></label>
		</th>
		<td>
			<?php echo $control_html; ?>
			<p class="description"><?php esc_html_e( 'Only for Real Estate roles and have a higher priority than `Profile Picture` option', 'cherry-real-estate' ); ?></p>
		</td>
	</tr>
</table>
