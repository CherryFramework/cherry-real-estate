<?php
/**
 * Map view.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/widgets/search/map.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

$source_selector = 'tm-map-pins-' . uniqid();
$defaults        = apply_filters( 'cherry_re_search_map_pins_args', array(
	'css_id'          => $source_selector,
	'wrap_class'      => 'tm-map-pins__wrap tm-re-hidden',
	'item_class'      => 'tm-map-pins__item',
	'show_pagination' => false,
	'paged'           => true,
	'template'        => 'infowindow.tmpl',
	'echo'            => false,
) );
$args = wp_parse_args( $passed_vars, $defaults );

$data   = Cherry_RE_Property_Data::get_instance();
$params = $data->prepare_search_args();
$args   = wp_parse_args( $args, $params );
$pins   = $data->the_property( $args );

if ( empty( $pins ) ) {
	cherry_re_get_template( 'search/no-properties-found' );
	return;
}

$instance = 'tm-re-search-map-' . uniqid();

// Data attributes.
$defaults = Cherry_RE_Tools::get_google_map_defaults();
$atts     = array(
	'id'             => $instance,
	'sourceselector' => $source_selector,
	'infowindow'     => array(
		'content'  => esc_html__( 'loading...', 'cherry-real-estate' ),
		'maxWidth' => 200,
	),
);

$atts = wp_parse_args( $atts, $defaults );
$atts = apply_filters( 'cherry_re_search_map_data_atts', $atts ); ?>

<div id="<?php echo esc_attr( $instance ); ?>" class="widget-tm-re-search__map tm-re-map tm-re-map-loading" <?php cherry_re_print_data_atts( $atts, true ); ?>></div>

<?php echo $pins; ?>

<?php cherry_re_enqueue_script( array(
	Cherry_RE_Assets::get_googleapis_handle(),
	'cherry-re-locations',
	'cherry-re-script',
) ); // Enqueues a map scripts. ?>
