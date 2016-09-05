<?php
/**
 * The Template for displaying sort select.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/misc/switch-layout.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */
?>

<form id="tm-re-property-sort" class="tm-re-property-sort" action="#">

	<?php $select = Cherry_RE_Tools::select_form( $passed_vars['options'], array(
			'name'    => 'properties_sort',
			'default' => esc_html__( 'Default', 'cherry-real-estate' ),
			'value'   => $passed_vars['value'],
			'echo'    => false,
		) ); ?>

	<label>
		<span class="tm-re-property-sort__label"><?php esc_html_e( 'Sort by:', 'cherry-real-estate' ); ?></span>
		<?php echo $select; ?>
	</label>

</form>

<?php cherry_re_enqueue_script( array( 'cherry-re-script' ) ); // Enqueue script. ?>
