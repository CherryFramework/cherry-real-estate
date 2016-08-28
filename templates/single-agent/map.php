<?php
/**
 * Single Agent Map.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/single-agent/map.php
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

// if ( empty( $callbacks ) ) {
// 	return;
// }

// $data = Cherry_RE_Property_Data::get_instance();

// Get saved object of WP_Query.
// $current_query = $data->get_wp_query();

// echo "<pre>";
// var_dump($current_query);
// echo "</pre>";

// $addresses = $data->get_property_data_from_query( $current_query, 'property_location' );

// echo "<pre>";
// var_dump($addresses);
// echo "</pre>";

// if ( empty( $addresses ) ) {
// 	return;
// }

$heading   = apply_filters( 'cherry_re_agent_map_heading', esc_html__( "This Agent's Active Listings:", 'cherry-real-estate' ) );
$instance  = 'tm-re-agent-map-' . uniqid();
$map_style = Model_Settings::get_map_style();
$marker_id = Model_Settings::get_map_marker();
$marker    = wp_get_attachment_image_src( $marker_id );
$marker    = is_array( $marker ) ? esc_url( $marker[0] ) : '';

// Data attributes.
$defaults = Cherry_RE_Tools::get_google_map_defaults();
$atts     = array(
	'id'          => $instance,
	'icon'        => $marker,
	'infowindow'  => array(
		'content'  => esc_html__( 'loading...', 'cherry-real-estate' ),
		'maxWidth' => 200,
	),
	'styles'         => $map_style,
	'sourceselector' => Model_Agents::get_property_wrap_id(),
);

$atts = wp_parse_args( $atts, $defaults );
$atts = apply_filters( 'cherry_re_agent_map_data_atts', $atts ); ?>

<div class="tm-agent-locations">

	<?php if ( $heading ) : ?>
		<h2 class="tm-agent-locations__subtitle"><?php echo $heading; ?></h2>
	<?php endif; ?>

	<div id="<?php echo esc_attr( $instance ); ?>" class="tm-agent-location__map tm-re-map tm-re-map-loading" <?php cherry_re_print_data_atts( $atts, true ); ?>></div>
</div><!-- .tm-agent-locations -->

<?php cherry_re_enqueue_script( array(
	Cherry_RE_Assets::get_googleapis_handle(),
	'cherry-re-locations',
	'cherry-re-script',
) ); // Enqueues a map scripts. ?>
