<?php
/**
 * Agent trust view.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Views
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */
?>

<table class="form-table">
	<tr>
		<th>
			<label for="<?php echo esc_attr( $control_name ); ?>"><?php _e( 'Trusted User', 'cherry-real-estate' ); ?></label>
		</th>
		<td>
			<?php echo $control_html; ?>
			<p class="description"><?php esc_html_e( 'Automatically mark submitted properties as approved', 'cherry-real-estate' ); ?></p>
		</td>
	</tr>
</table>
