<?php
/**
 * Single Property Gallery.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/single-property/gallery.php
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

$property_ID = get_the_ID();
$gallery_ids = $callbacks->post_gallery();

if ( ! $gallery_ids ) {
	return;
}

$instance           = 'tm-property-gallery-' . uniqid();
$gallery_ids_amount = count( $gallery_ids );
$gallery_js_class   = '';
$slide_atts         = array();

// Gallery or single image?
$is_gallery = false;

if ( $gallery_ids_amount > 1 ) {
	$is_gallery       = true;
	$gallery_js_class = 'tm-property-gallery-js';
	$slides_per_view  = $gallery_ids_amount < 6 ? count( $gallery_ids_amount ) : 6;

	// Slide data attributes.
	$slide_atts = apply_filters( 'cherry_re_single_property_gallery_data_atts', array(
		'nextButton'          => '.tm-property-gallery__button-next',
		'prevButton'          => '.tm-property-gallery__button-prev',
		'paginationClickable' => true,
		'autoHeight'          => false,
		'loop'                => true,
		'loopedSlides'        => $gallery_ids_amount + 1,
		'grabCursor'          => true,
		'speed'               => 600,
		'effect'              => 'slide',
		'group'               => array(
			'top'    => '.tm-property-gallery--top',
			'thumbs' => '.tm-property-gallery--thumbs',
		),
	) );
} ?>

<div id="<?php echo esc_attr( $instance ); ?>" class="tm-property-gallery swiper-container tm-property-gallery--top <?php echo $gallery_js_class; ?>" data-id="<?php echo esc_attr( $instance ); ?>" <?php cherry_re_print_data_atts( $slide_atts, true ); ?>>
	<div class="tm-property-gallery__wrapper swiper-wrapper">

		<?php foreach ( $gallery_ids as $key => $attachment_id ) {
			$image_title = get_the_title( $attachment_id );
			$image_src   = wp_get_attachment_image_src(
				$attachment_id,
				apply_filters( 'cherry_re_single_property_gallery_image_size', 'large' )
			);

			if ( ! $image_src ) {
				continue;
			}

			echo apply_filters( 'cherry_re_single_property_gallery_item_html',
				sprintf(
					'<div class="tm-property-gallery__item swiper-slide"><img src="%s" alt="%s"></div>',
					esc_url( $image_src[0] ), esc_attr( $image_title )
				),
				$attachment_id, $property_ID
			);
		} ?>

	</div><!-- .tm-property-gallery__wrapper -->

	<?php if ( $is_gallery ) :

		if ( ! empty( $slide_atts['nextButton'] ) ) { ?>
			<div class="tm-property-gallery__button-next swiper-button-next"></div>
		<?php } ?>

		<?php if ( ! empty( $slide_atts['prevButton'] ) ) { ?>
			<div class="tm-property-gallery__button-prev swiper-button-prev"></div>
		<?php }

	endif; ?>

</div><!-- .tm-property-gallery (slides) -->

<?php if ( $is_gallery ) :

	// Thumbnails data attributes.
	$thumb_atts = apply_filters( 'cherry_re_single_property_gallery_thumbnails_data_atts', array(
		'spaceBetween'        => 10,
		'slidesPerView'       => $slides_per_view,
		'slideToClickedSlide' => true,
		'loop'                => true,
		'loopedSlides'        => $gallery_ids_amount + 1,
		'centeredSlides'      => $gallery_ids_amount > 4 ? false : true,
		'group'               => array(
			'top'    => '.tm-property-gallery--top',
			'thumbs' => '.tm-property-gallery--thumbs',
		),
		'breakpoints' => array(
			'769' => array(
				'slidesPerView' => 3,
			),
		),
	) );
	$instance = 'tm-property-gallery-thumbs-' . uniqid(); ?>

	<div id="<?php echo esc_attr( $instance ); ?>" class="swiper-container tm-property-gallery--thumbs" data-id="<?php echo esc_attr( $instance ); ?>" <?php cherry_re_print_data_atts( $thumb_atts, true ); ?>>
		<div class="tm-property-gallery__thumbs-wrapper swiper-wrapper">

			<?php foreach ( $gallery_ids as $key => $attachment_id ) {
				$image_title   = get_the_title( $attachment_id );
				$thumbnail_src = wp_get_attachment_image_src(
					$attachment_id,
					apply_filters( 'cherry_re_single_property_gallery_thumbnail_size', 'thumbnail' )
				);

				if ( ! $thumbnail_src ) {
					continue;
				}

				echo apply_filters( 'cherry_re_single_property_gallery_thumbnail_item_html',
					sprintf(
						'<div class="tm-property-gallery__thumbs-item swiper-slide"><img src="%s" alt="%s"></div>',
						esc_url( $thumbnail_src[0] ), esc_attr( $image_title )
					),
					$attachment_id, $property_ID
				);
			} ?>

		</div>
	</div><!-- .tm-property-gallery (thumbnails) -->

<?php endif;

// Enqueues a gallery scripts.
cherry_re_enqueue_script( array( 'jquery-swiper', 'cherry-re-script' ) ); ?>
