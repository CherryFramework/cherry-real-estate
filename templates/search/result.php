<?php
/**
 * The Template for displaying search from and map in result page.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/search-result.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */
?>
<div class="widget-tm-re-search widget-tm-re-search--result">
	<?php // Search form template. ?>
	<?php cherry_re_get_template( 'widgets/search/form', array(
			'values' => array_map( 'esc_attr', $_GET ),
		) ); ?>

	<?php // Map template. ?>
	<?php cherry_re_get_template( 'widgets/search/map' ); ?>
</div>
