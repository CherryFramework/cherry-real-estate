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
	'template'        => 'infowindow.tmpl',
	'echo'            => false,
) );
$args = wp_parse_args( $args, $defaults );

$data   = Cherry_RE_Property_Data::get_instance();
$params = $data->prepare_search_args();
$args   = wp_parse_args( $args, $params );
$pins   = $data->the_property( $args );

if ( empty( $pins ) ) {
	cherry_re_get_template( 'search/no-properties-found' );
	return;
}

$instance  = 'tm-re-search-map-' . uniqid();
$marker_id = Model_Settings::get_map_marker();
$marker    = wp_get_attachment_image_src( $marker_id );
$marker    = is_array( $marker ) ? esc_url( $marker[0] ) : '';

// Data attributes.
$atts = array(
	'id'          => $instance,
	'zoom'        => 15,
	'scrollwheel' => false,
	'draggable'   => wp_is_mobile() ? false : true,
	'icon'        => $marker,
	'animation'   => '', // BOUNCE, DROP
	'infowindow'  => array(
		'content'  => esc_html__( 'loading...', 'cherry-real-estate' ),
		'maxWidth' => 200,
	),
	'sourceselector'        => $source_selector,
	'mapTypeControl'        => true,
	'zoomControl'           => true,
	'streetViewControl'     => true,
	'mapTypeControlOptions' => array(
		'style'    => 'HORIZONTAL_BAR',
		'position' => 'TOP_CENTER',
	),
	'zoomControlOptions' => array(
		'position' => 'LEFT_CENTER',
	),
	'streetViewControlOptions' => array(
		'position' => 'LEFT_TOP',
	),
);

$map_style = Model_Settings::get_map_style();

if ( ! empty( $map_style ) ) {
	$atts = array_merge( $atts, array( 'styles' => $map_style ) );
}

$atts = apply_filters( 'cherry_re_search_map_data_atts', $atts ); ?>

<div id="<?php echo esc_attr( $instance ); ?>" class="widget-tm-re-search__map tm-re-map" <?php cherry_re_print_data_atts( $atts, true ); ?>></div>

<?php echo $pins; ?>

<?php cherry_re_enqueue_script( array(
	Cherry_RE_Assets::get_googleapis_handle(),
	'cherry-re-locations',
	'cherry-re-script',
) ); // Enqueues a map scripts. ?>
