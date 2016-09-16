<?php
/**
 * Single Agent Info.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/single-property/agent.php
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

$agent_id  = get_the_author_meta( 'ID' );
$agent     = get_userdata( $agent_id );
$callbacks = $passed_vars['callbacks'];
$callbacks->the_agent_meta( $agent );

$heading = esc_html( apply_filters(
	'cherry_re_agent_info_title',
	esc_html__( 'Contact Agent', 'cherry-real-estate' )
) );

$photo = $callbacks->get_agent_photo( apply_filters( 'cherry_re_single_property_agent_photo_args', array(
	'size'  => 'thumbnail',
	'class' => 'tm-agent-info__photo',
	'link'  => true,
) ) );

$name = $callbacks->get_agent_name( apply_filters( 'cherry_re_single_property_agent_name_args', array(
	'wrap'  => 'h1',
	'class' => 'tm-agent-info__title',
	'link'  => false,
) ) );

$desc = $callbacks->get_agent_description( apply_filters( 'cherry_re_single_property_agent_desc_args', array(
	'class'  => 'tm-agent-info__desc',
	'length' => 15,
) ) );

$contacts = $callbacks->get_agent_contacts( apply_filters( 'cherry_re_single_property_agent_contacts_args', array(
	'class' => 'tm-agent-info__contacts',
) ) );

$socials = $callbacks->get_agent_socials( apply_filters( 'cherry_re_single_property_agent_socials_args', array(
	'class' => 'tm-agent-info__socials',
) ) ); ?>

<div class="tm-agent-info">

	<?php if ( $heading ) : ?>
		<h2 class="tm-agent-info__subtitle"><?php echo $heading; ?></h2>
	<?php endif; ?>

	<?php if ( ! empty( $photo ) ) { ?>
		<?php echo $photo; ?>
	<?php } ?>

	<div class="tm-agent-info__details">

		<?php if ( ! empty( $name ) ) { ?>
			<?php echo $name; ?>
		<?php } ?>

		<?php if ( ! empty( $desc ) ) { ?>
			<?php echo $desc; ?>
		<?php } ?>

		<?php if ( ! empty( $contacts ) ) { ?>
			<?php echo $contacts; ?>
		<?php } ?>

		<?php if ( ! empty( $socials ) ) { ?>
			<?php echo $socials; ?>
		<?php } ?>

	</div>

</div><!-- .tm-agent-info -->
