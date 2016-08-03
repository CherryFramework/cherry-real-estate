<?php
/**
 * Model for submite form.
 *
 * @package    Cherry_Framework
 * @subpackage Model
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Model properties
 */
class Model_Submit_Form {

	public static $errors = array();

	/**
	 * Shortcode submit form
	 *
	 * @return html code.
	 */
	public static function shortcode_submit_form() {
		self::submit_form_assets();

		$terms = Model_Properties::get_types();

		return Cherry_Toolkit::render_view(
			TM_REAL_ESTATE_DIR . '/views/submit-form.php',
			array(
				'terms'              => $terms,
				'locations'          => self::get_locations(),
				'required_for_gests' => self::get_required_for_gests(),
			)
		);
	}

	/**
	 * Submit form assets
	 */
	public static function submit_form_assets() {
		wp_enqueue_script( 'submit-form', plugins_url( 'tm-real-estate' ) . '/assets/js/submit-form.js',array( 'jquery' ), '1.0.0', true );
	}

	/**
	 * Get locations
	 *
	 * @return [array] Locations.
	 */
	public static function get_locations() {
		$result    = array();
		$locations = get_terms(
			'location',
			array(
				'parent'     => 0,
				'hide_empty' => false,
			)
		);
		if ( ! is_wp_error( $locations ) ) {
			foreach ( $locations as $location ) {
				$result[ $location->term_id ] = $location->name;
			}
		}
		return $result;
	}

	/**
	 * Get required argument for not logged users
	 *
	 * @return [string] required argument.
	 */
	public static function get_required_for_gests() {
		return is_user_logged_in() ? '' : 'required="required"';
	}

	/**
	 * Callback of shortcode submit form
	 */
	public static function submit_form_callback() {
		$security = check_ajax_referer( '_tm-re-submit-form', 'nonce', false );

		if ( false === $security ) {
			wp_send_json_error( array( 'message' => self::get_errors( 'nonce' ) ) );
		}

		$post_type = cherry_real_estate()->get_post_type_name();
		$post_meta = cherry_real_estate()->get_meta_prefix();
		$data      = array();
		$fields    = array();
		$meta      = array();

		if ( ! empty( $_POST['property'] ) ) {
			$data = $_POST['property'];
		}

		foreach ( $data as $p ) {

			if ( ! array_key_exists( 'name', $p ) ) {
				continue;
			}

			if ( false !== strpos( $p['name'], $post_meta ) ) {
				// $key          = self::_prepare_key( $p['name'], $post_meta );
				$key          = '';
				$meta[ $key ] = $p['value'];

			} elseif ( false !== strpos( $p['name'], $post_type ) ) {
				$fields[] = $p['value'];
			} else {
				continue;
			}

			// $key = explode( $post_type, $p['name'] );

			// $data[] = $p['value'];
		}

		// var_dump( $property_data );

		// $headers = array( 'Content-type: text/html; charset=utf-8' );
		// $result  = wp_mail(
		// 	'cheh@templatemonster.me',
		// 	Model_Settings::get_confirn_subject(),
		// 	Model_Settings::get_confirn_message(),
		// 	$headers
		// );

		// if ( $result ) {
			wp_send_json_success( $data );
		// } else {
			// wp_send_json_error( array( 'message' => self::get_errors( 'internal' ) ) );
		// }

		// ****************************

		$messages = Model_Settings::get_submission_form_settings();
		$tm_json_request = array();
		if ( empty( $_POST ) ) {
			exit('post');
			wp_send_json_error( array( 'messages' => $messages ) );
		}
		if ( empty( $_POST['property']['title'] ) ) {
			exit('title');
			wp_send_json_error( array( 'messages' => $messages ) );
		}

		$property['title']       = $_POST['property']['title'];
		$property['description'] = ! empty( $_POST['property']['description'] ) ? $_POST['property']['description'] : '';

		$property_meta           = $_POST['property']['meta'];

		$post_id = Model_Properties::add_property( $property );

		if ( ! $post_id ) {
			wp_send_json_error( array( 'messages' => $messages ) );
		}

		if ( ! empty( $_POST['property']['location'] ) ) {
			$term_id = sanitize_key( $_POST['property']['location'] );
			$term = get_term( $term_id );
			if ( $term->parent ) {
				wp_set_post_terms( $post_id, array( $term->parent, $term_id ), 'location' );
			} else {
				wp_set_post_terms( $post_id, array( $term_id ), 'location' );
			}
		}

		if ( ! empty( $_POST['property']['type'] ) ) {
			$term_id = sanitize_key( $_POST['property']['type'] );
			$term = get_term( $term_id );
			if ( $term->parent ) {
				wp_set_post_terms( $post_id, array( $term->parent, $term_id ), 'property-type' );
			}
		}

		if ( ! empty( $_FILES['thumb'] ) ) {
			$attachment_id = Model_Submit_Form::insert_attacment( $_FILES['thumb'], $post_id );
			if ( $attachment_id ) {
				set_post_thumbnail( $post_id, $attachment_id );
			}
		}

		if ( ! empty( $_FILES['gallery'] ) ) {
			if ( is_array( $_FILES['gallery']['name'] ) ) {
				$files = Model_Submit_Form::re_array_files( $_FILES['gallery'] );
				foreach ( $files as $key => $file ) {
					if ( ! empty( $file ) ) {
						$image_id = Model_Submit_Form::insert_attacment( $file, $post_id );
						if ( ! empty( $image_id ) ) {
							$gallery[] = $image_id;
						}
					}
				}
			} else {
				$file = $_FILES['gallery'];
				$gallery[] = Model_Submit_Form::insert_attacment( $file, $post_id );
			}
		}

		$property_meta['gallery'] = implode( ',', $gallery );

		if ( current_user_can( 'administrator' ) || current_user_can( 're_agent' ) ) {
			$property_meta['agent'] = get_current_user_id();
		}

		$property_meta['state'] = 'active';
		$property_meta['activated_key'] = md5( time() . $post_id . rand( 1, 1000 ) );

		foreach ( $property_meta as $key => $value ) {
			update_post_meta( $post_id, sanitize_text_field( $key ), $value );
		}
		wp_send_json_success(
			array(
				'messages' => $messages,
				'send' => self::send_confirmation_email( $post_id, $property_meta['activated_key'] ),
			)
		);
	}

	/**
	 * Send the confirmation email
	 *
	 * @return [object] current user.
	 */
	public static function send_confirmation_email( $post_id, $key = '' ) {
		if ( array_key_exists( 'property', $_POST ) ) {
			if ( array_key_exists( 'meta', $_POST['property'] ) ) {
				if ( array_key_exists( 'email', $_POST['property']['meta'] ) ) {
					$message = sprintf(
						'%s %s',
						self::get_mail_message(),
						Model_Settings::get_approved_page() . '?id=' . $post_id . '&key=' . $key
						/*add_query_arg(
							array(
								'id' => $post_id,
								'key' => $key
							),
							Model_Settings::get_approved_page()
						)
						get_permalink( $post_id ),
						add_query_arg(
							'publish_hidden',
							$post_id,
							get_bloginfo( 'url' )
						)*/
					);
					return wp_mail(
						$_POST['property']['meta']['email'],
						self::get_mail_subject(),
						$message
					);
				}
			}
		}
		return false;
	}

	/**
	 * Get mail subject
	 *
	 * @return [string] subject.
	 */
	public static function get_mail_subject() {
		return self::array_get(
			(array) get_option( 'tm-properties-submission-form' ),
			'confirmation-subject'
		);
	}

	/**
	 * Get mail message
	 *
	 * @return [string] msg.
	 */
	public static function get_mail_message() {
		return self::array_get(
			(array) get_option( 'tm-properties-submission-form' ),
			'confirmation-message'
		);
	}

	/**
	 * Try get value by key from array
	 *
	 * @param  array $array values list.
	 * @param  type  $key value key.
	 * @param  type  $default default value.
	 * @return mixed value by key
	 */
	public static function array_get( $array, $key, $default = '' ) {
		$array = (array) $array;
		if ( is_null( $key ) ) {
			return $array;
		}
		if ( array_key_exists( $key, $array ) ) {
			return $array[ $key ];
		}
		return $default;
	}

	/**
	 * Add new  attacment
	 *
	 * @param  [type] $file file of attachment.
	 * @param  [type] $post_id  ID of post.
	 *
	 * @return [type] code.
	 */
	public static function insert_attacment( $file, $post_id ) {

		require_once( ABSPATH . 'wp-admin/includes/admin.php' );
		$file_return = wp_handle_upload( $file, array( 'test_form' => false ) );
		if ( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
			return false;
		} else {
			$filename = $file_return['file'];
			$attachment = array(
				'post_mime_type' => $file_return['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
				'guid'           => $file_return['url'],
			);
			$attachment_id = wp_insert_attachment( $attachment, $file_return['url'], $post_id );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );
			if ( 0 < intval( $attachment_id ) ) {
				return $attachment_id;
			}
		}
		return false;
	}

	/**
	 * Reformate files array
	 *
	 * @param  [type] $file_post array of files.
	 *
	 * @return mixed file_array.
	 */
	public static function re_array_files( &$file_post ) {
		$file_array = array();
		$file_count = count( $file_post['name'] );
		$file_keys  = array_keys( $file_post );

		for ( $i = 0; $i < $file_count; $i ++ ) {
			foreach ( $file_keys as $key ) {
				$file_array[ $i ][ $key ] = $file_post[ $key ][ $i ];
			}
		}
		return $file_array;
	}

	/**
	 * Retrieve a error message by code.
	 *
	 * @since  1.0.0
	 * @param  string $code
	 * @return string
	 */
	public static function get_errors( $code ) {
		$errors = apply_filters( 'cherry_re_get_errors', array(
			'nonce'    => esc_html__( 'Security validation failed', 'cherry-real-estate' ),
			'internal' => esc_html__( 'Internal error. Please, try again later', 'cherry-real-estate' ),
		), $code );

		if ( ! array_key_exists( $code, $errors ) ) {
			reset( $errors );
			return current( $errors );
		}

		return $errors[ $code ];
	}

	// public static function _prepare_key( $subject, $search ) {
	// 	$result = str_replace( $search, '', $subject, 1 );

	// 	return $result;
	// }
}
