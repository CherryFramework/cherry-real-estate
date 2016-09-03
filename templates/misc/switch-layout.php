<?php
/**
 * The Template for displaying switch layout form.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/misc/switch-layout.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

$layout = Model_Settings::get_listing_layout();
$is_grid_active = ( 'grid' === $layout ) ? ' tm-re-switch-layout__btn--active' : '';
$is_list_active = ( 'list' === $layout ) ? ' tm-re-switch-layout__btn--active' : ''; ?>

<form method="post" id="tm-re-switch-layout" class="tm-re-switch-layout" action="#">

	<?php wp_nonce_field( '_tm-re-switch-layout', 'tm-re-switch-layout-nonce' ); ?>

	<button type="button" class="tm-re-switch-layout__btn tm-re-layout--grid<?php echo $is_grid_active; ?>" value="grid"><?php esc_html_e( 'Grid', 'cherry-real-estate' ); ?></button>
	<button type="button" class="tm-re-switch-layout__btn tm-re-layout--list<?php echo $is_list_active; ?>" value="list"><?php esc_html_e( 'List', 'cherry-real-estate' ); ?></button>
</form>

<?php cherry_re_enqueue_script( array( 'cherry-re-script' ) ); // Enqueue script. ?>
