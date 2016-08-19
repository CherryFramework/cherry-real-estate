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

$layout = Model_Settings::get_search_layout(); ?>

<form method="post" id="tm-re-switch-layout" class="tm-re-switch-layout" action="#">

	<?php wp_nonce_field( '_tm-re-switch-layout', 'tm-re-switch-layout-nonce' ); ?>

	<button type="button" class="tm-re-switch-layout__btn tm-re-layout--grid<?php if ( 'grid' === $layout ) echo ' tm-re-switch-layout__btn--active'; ?>" value="grid"><?php esc_html_e( 'Grid', 'cherry-real-estate' ); ?></button>
	<button type="button" class="tm-re-switch-layout__btn tm-re-layout--list<?php if ( 'list' === $layout ) echo ' tm-re-switch-layout__btn--active'; ?>" value="list"><?php esc_html_e( 'List', 'cherry-real-estate' ); ?></button>
</form>

<?php cherry_re_enqueue_script( array( 'cherry-re-script') ); // Enqueue script. ?>
