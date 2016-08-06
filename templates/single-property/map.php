<?php
/**
 * Single Property Map.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/single-property/map.php
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

if ( empty( $callbacks ) ) {
	return;
}

$location = $callbacks->get_property_location();

if ( empty( $location ) ) {
	return;
}

$heading   = esc_html( apply_filters( 'cherry_re_property_map_heading', esc_html__( 'Map', 'cherry-real-estate' ) ) );
$instance  = 'tm-re-map-' . uniqid();
$marker_id = Model_Settings::get_map_marker();
$marker    = wp_get_attachment_image_src( $marker_id );
$marker    = is_array( $marker ) ? esc_url( $marker[0] ) : '';

// Data attributes.
$atts = array(
	'id'                    => $instance,
	'address'               => array( $location ),
	'zoom'                  => 15,
	'scrollwheel'           => false,
	'draggable'             => wp_is_mobile() ? false : true,
	'icon'                  => $marker,
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

$atts = apply_filters( 'cherry_re_single_property_map_data_atts', $atts ); ?>

<div class="tm-property__location">

	<?php if ( $heading ) : ?>
		<h2 class="tm-property__subtitle"><?php echo $heading; ?></h2>
	<?php endif; ?>

	<div id="<?php echo esc_attr( $instance ); ?>" class="tm-property__map tm-re-map" <?php cherry_re_print_data_atts( $atts, true ); ?>></div>
</div>

<?php cherry_re_enqueue_script( array(
	Cherry_RE_Assets::get_googleapis_handle(),
	'cherry-re-locations',
	'cherry-re-script',
) ); // Enqueues a map scripts. ?>
