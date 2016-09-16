<?php
/**
 * Properties.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Models
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Model properties.
 *
 * @since 1.0.0
 */
class Model_Properties {

	/**
	 * Get allowed property statuses.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public static function get_allowed_property_statuses() {
		return apply_filters( 'cherry_re_allowed_property_statuses', array(
			'rent' => esc_html__( 'Rent', 'cherry-real-estate' ),
			'sale' => esc_html__( 'Sale', 'cherry-real-estate' ),
		) );
	}

	/**
	 * Get allowed property states.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public static function get_allowed_property_states() {
		return apply_filters( 'cherry_re_allowed_property_states', array(
			'inactive' => esc_html__( 'Inactive', 'cherry-real-estate' ),
			'active'   => esc_html__( 'Active', 'cherry-real-estate' ),
			'sold'     => esc_html__( 'Sold', 'cherry-real-estate' ),
		) );
	}

	/**
	 * Get property types.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public static function get_property_types( $key = 'id' ) {
		$post_type = cherry_real_estate()->get_post_type_name();
		$tax       = $post_type . '_type';

		return self::get_terms( $tax, $key );
	}

	/**
	 * Get property tags.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public static function get_property_tags( $key = 'id' ) {
		$post_type = cherry_real_estate()->get_post_type_name();
		$tax       = $post_type . '_tag';

		return self::get_terms( $tax, $key );
	}

	/**
	 * Get property tags.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public static function get_property_features( $key = 'id' ) {
		$post_type = cherry_real_estate()->get_post_type_name();
		$tax       = $post_type . '_feature';

		return self::get_terms( $tax, $key );
	}

	/**
	 * Retrieve the terms in a given taxonomy or list of taxonomies.
	 *
	 * @since  1.0.0
	 * @param  string $tax Taxonomy name.
	 * @param  string $key Key - id or slug.
	 * @return array
	 */
	public static function get_terms( $tax, $key ) {
		$terms = array();

		if ( 'id' === $key ) {
			foreach ( (array) get_terms( $tax, array( 'hide_empty' => false ) ) as $term ) {
				$terms[ $term->term_id ] = $term->name;
			}
		} elseif ( 'slug' === $key ) {
			foreach ( (array) get_terms( $tax, array( 'hide_empty' => false ) ) as $term ) {
				$terms[ $term->slug ] = $term->name;
			}
		}

		return $terms;
	}

	/**
	 * Retrieve a post's terms as a list with specified format.
	 *
	 * @since  1.0.0
	 * @param  string $taxonomy Taxonomy name.
	 * @param  int    $post_id  Post ID.
	 * @param  array  $args     Arguments.
	 * @return string|false|WP_Error A list of terms on success, false if there are no terms, WP_Error on failure.
	 */
	public static function get_property_term_list( $taxonomy, $post_id = null, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'before' => '',
			'after'  => '',
			'sep'    => ', ',
		) );

		$post_type     = cherry_real_estate()->get_post_type_name();
		$taxonomy_name = $post_type . '_' . $taxonomy;

		$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
		$terms   = get_the_term_list(
			$post_id,
			$taxonomy_name,
			$args['before'],
			$args['sep'],
			$args['after']
		);

		if ( is_wp_error( $terms ) || ! $terms ) {
			return false;
		}

		return $terms;
	}
}
