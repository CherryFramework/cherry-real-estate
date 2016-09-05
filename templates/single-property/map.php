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

if ( empty( $passed_vars['callbacks'] ) ) {
	return;
}

$callbacks = $passed_vars['callbacks'];
$location  = $callbacks->get_property_location();

if ( empty( $location ) ) {
	return;
}

$heading  = esc_html( apply_filters( 'cherry_re_property_map_heading', esc_html__( 'Map', 'cherry-real-estate' ) ) );
$instance = 'tm-re-map-' . uniqid();

// Data attributes.
$defaults = Cherry_RE_Tools::get_google_map_defaults();
$atts     = array(
	'id'      => $instance,
	'address' => array( $location ),
);

$atts = wp_parse_args( $atts, $defaults );
$atts = apply_filters( 'cherry_re_single_property_map_data_atts', $atts ); ?>

<div class="tm-property__location">

	<?php if ( $heading ) : ?>
		<h2 class="tm-property__subtitle"><?php echo $heading; ?></h2>
	<?php endif; ?>

	<div id="<?php echo esc_attr( $instance ); ?>" class="tm-property__map tm-re-map tm-re-map-loading" <?php cherry_re_print_data_atts( $atts, true ); ?>></div>
</div>

<?php cherry_re_enqueue_script( array(
	Cherry_RE_Assets::get_googleapis_handle(),
	'cherry-re-locations',
	'cherry-re-script',
) ); // Enqueues a map scripts. ?>
