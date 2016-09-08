<?php
/**
 * Model for submission form.
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

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'wp_ajax_nopriv_login_form',    array( __CLASS__, 'login_callback' ) );
		add_action( 'wp_ajax_nopriv_register_form', array( __CLASS__, 'register_callback' ) );

		add_action( 'wp_ajax_submission_form', array( __CLASS__, 'submission_callback' ) );
		add_action( 'transition_post_status', array( $this, 'publish_property' ), 10, 3 );

		add_action( 'wp_ajax_upload_file', array( __CLASS__, 'upload_file_callback' ) );
	}

	/**
	 * Retrieve a popup id attribute (without `#`).
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_popup_id() {
		return apply_filters( 'cherry_re_get_popup_html_id', 'tm-re-auth-popup' );
	}

	/**
	 * Callback for submission form:
	 *
	 * - security check
	 * - create request (new property with `pending` status) or publish property
	 * - send notification e-mail*
	 *
	 * (*) - if it's a new or not approved agent
	 *
	 * @since 1.0.0
	 */
	public static function submission_callback() {

		// Check a nonce.
		$security = check_ajax_referer( '_tm-re-submission-form', 'nonce', false );

		if ( false === $security ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Security validation failed', 'cherry-real-estate' ),
			) );
		}

		if ( empty( $_POST['property'] ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Internal error. Please, try again later', 'cherry-real-estate' ),
			) );
		}

		$post_type   = cherry_real_estate()->get_post_type_name();
		$meta_prefix = cherry_real_estate()->get_meta_prefix();
		$_post       = wp_list_pluck( $_POST['property'], 'value', 'name' );
		$data        = array_map( 'wp_filter_kses', $_post );

		// Prepare defaults array for new property.
		$defaults = apply_filters( 'cherry_re_before_insert_post_defaults', array(
			'property_title'          => '',
			'property_description'    => '',
			'property_type'           => '',
			'property_price'          => '',
			'property_status'         => '',
			'property_bathrooms'      => '',
			'property_bedrooms'       => '',
			'property_area'           => '',
			'property_parking_places' => '',
			'property_address'        => '',
			'property_gallery'        => '',
		), $data );

		$data    = wp_parse_args( $data, $defaults );
		$gallery = '';

		if ( ! empty( $_POST['gallery'] ) ) {
			$gallery = array_map( 'intval', $_POST['gallery'] );
			$gallery = join( ',', $gallery );
		}

		$current_user    = wp_get_current_user();
		$is_admin        = current_user_can( 'manage_options' );
		$is_trusted_user = Model_Agents::get_agent_trust( $current_user->ID );
		$is_trusted_user = filter_var( $is_trusted_user, FILTER_VALIDATE_BOOLEAN );

		// Prepare data array for new property.
		$property_arr = array(
			'post_type'    => $post_type,
			'post_title'   => wp_strip_all_tags( $data['property_title'] ),
			'post_content' => $data['property_description'],
			'post_status'  => $is_trusted_user || $is_admin ? 'publish' : 'pending',
			'meta_input'   => array(
				$meta_prefix . 'price'          => $data['property_price'],
				$meta_prefix . 'status'         => $data['property_status'],
				$meta_prefix . 'bathrooms'      => $data['property_bathrooms'],
				$meta_prefix . 'bedrooms'       => $data['property_bedrooms'],
				$meta_prefix . 'area'           => $data['property_area'],
				$meta_prefix . 'parking_places' => $data['property_parking_places'],
				$meta_prefix . 'location'       => $data['property_address'],
				$meta_prefix . 'gallery'        => $gallery,
				$meta_prefix . 'author'         => isset( $current_user->ID ) ? $current_user->ID : '',
				$meta_prefix . 'state'          => $is_trusted_user || $is_admin ? 'active' : 'inactive',
			),
		);

		$property_arr = apply_filters( 'cherry_re_before_insert_post', $property_arr, $data );

		// Create new property.
		$property_id = wp_insert_post( $property_arr, false );

		if ( 0 == $property_id || is_wp_error( $property_id ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Request not created. Please, try again later', 'cherry-real-estate' ),
			) );
		}

		// Set types for a property.
		$type_id = intval( $data['property_type'] );
		$term_taxonomy_id = wp_set_object_terms( $property_id, $type_id, $post_type . '_type' );

		// Retrieve the current user e-mail.
		$user_email = false;

		if ( ! empty( $current_user->user_email ) ) {
			$user_email = sanitize_email( $current_user->user_email );
		}

		// Send notification e-mail.
		if ( ! ( $is_trusted_user || $is_admin ) && is_email( $user_email ) ) {

			$result = self::send_mail(
				$user_email,
				Model_Settings::get_notification_subject(),
				Model_Settings::get_notification_message()
			);

			if ( ! $result ) {
				wp_send_json_error( array(
					'message' => esc_html__( "Request are created. But we can't send your message.", 'cherry-real-estate' ),
				) );
			}
		}

		wp_send_json_success( $property_id );
	}

	/**
	 * Upload file via ajax
	 *
	 * No nonce field since the form may be statically cached.
	 */
	public static function upload_file_callback() {
		$data = array(
			'files'   => array(),
			'message' => '',
		);

		// Check a nonce.
		$security = check_ajax_referer( '_tm-re-submission-form', 'nonce', false );

		if ( false === $security ) {
			$data['message'] = esc_html__( 'Security validation failed', 'cherry-real-estate' );
			wp_send_json_error( $data );
		}

		if ( empty( $_POST['name'] ) ) {
			$data['message'] = esc_html__( 'Internal error. Please, try again later', 'cherry-real-estate' );
			wp_send_json_error( $data );
		}

		$name = sanitize_text_field( $_POST['name'] );
		$name = explode( '[]', $name, 2 );
		$name = $name[0];

		if ( empty( $_FILES[ $name ] ) ) {
			$data['message'] = esc_html__( 'Internal error. Please, try again later', 'cherry-real-estate' );
			wp_send_json_error( $data );
		}

		$files = $_FILES[ $name ];

		if ( ! empty( $files['name'] ) && is_array( $files['name'] ) ) {
			$_files = array();

			foreach ( $files as $key => $value ) {
				$_files[ $key ] = current( $value );
			}

			$_FILES[ $name ] = $_files;
		}

		$upload = self::media_handle_upload( $name );

		if ( is_wp_error( $upload ) ) {
			$data['message'] = $attachments_id->get_error_message();
			wp_send_json_error( $data );
		}

		$data['files'][] = $upload;
		wp_send_json_success( $data );
	}

	/**
	 * Wrapper-function for `media_handle_upload`.
	 *
	 * @since  1.0.0
	 * @param string $file_id   Index of the {@link $_FILES} array that the file was sent. Required.
	 * @param int    $post_id   The post ID of a post to attach the media item to. Required, but can
	 *                          be set to 0, creating a media item that has no relationship to a post.
	 * @return int|WP_Error ID of the attachment or a WP_Error object on failure.
	 */
	public static function media_handle_upload( $file_id, $post_id = 0 ) {

		// These files need to be included as dependencies when on the front end.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		// Let WordPress handle the upload.
		$upload = media_handle_upload( $file_id, $post_id );

		if ( is_wp_error( $upload ) ) {
			return $upload;

		} else {
			$attachemnt_atts = wp_get_attachment_image_src( $upload );
			$attachment_type = wp_check_filetype( basename( $_FILES[ $file_id ]['name'] ) );

			$uploaded_file = array();
			$uploaded_file['id']        = $upload;
			$uploaded_file['url']       = is_array( $attachemnt_atts ) ? esc_url( $attachemnt_atts[0] ) : false;
			$uploaded_file['name']      = basename( $_FILES[ $file_id ]['name'] );
			$uploaded_file['type']      = $_FILES[ $file_id ]['type'];
			$uploaded_file['size']      = $_FILES[ $file_id ]['size'];
			$uploaded_file['extension'] = is_array( $attachment_type ) ? $attachment_type['ext'] : false;

			return $uploaded_file;
		}
	}

	/**
	 * When property are published - send e-mail with congratulations.
	 *
	 * @since 1.0.0
	 * @param string  $new_status New post status.
	 * @param string  $old_status Old post status.
	 * @param WP_Post $post       Post object.
	 */
	public function publish_property( $new_status, $old_status, $post ) {

		if ( $new_status == $old_status ) {
			return;
		}

		if ( 'publish' !== $new_status ) {
			return;
		}

		$post_type = cherry_real_estate()->get_post_type_name();

		if ( $post_type !== $post->post_type ) {
			return;
		}

		$agent_id = $post->post_author;

		$property_id = $post->ID;
		$meta_prefix = cherry_real_estate()->get_meta_prefix();
		$author_id   = get_post_meta( $property_id, $meta_prefix . 'author', true );

		if ( empty( $author_id ) ) {
			return;
		}

		$author = get_user_by( 'ID', $author_id );

		if ( false === $author ) {
			return;
		}

		if ( user_can( $author, 'manage_options' ) ) {
			return;
		}

		$author_email = isset( $author->user_email ) ? $author->user_email : false;
		$author_email = sanitize_email( $author_email );

		if ( ! is_email( $author_email ) ) {
			return;
		}

		$message = Model_Settings::get_congratulate_message();
		$message .= sprintf( __( '<br>View: %s<br><br>', 'cherry-real-estate' ), get_permalink( $property_id ) );

		if ( $author_id != $agent_id ) {
			$agent_contacts = $this->_prepare_agent_contacts_to_mail( $agent_id );
			$message        .= ! empty( $agent_contacts ) ? $agent_contacts : '';
		}

		return self::send_mail( $author_email, Model_Settings::get_congratulate_subject(), $message );
	}

	/**
	 * Callback for login form.
	 *
	 * @since 1.0.0
	 */
	public static function login_callback() {

		// Check a nonce.
		$security = check_ajax_referer( '_tm-re-login-form', 'nonce', false );

		if ( false === $security ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Internal error. Please, try again later.', 'cherry-real-estate' ),
			) );
		}

		if ( empty( $_POST['access'] ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'The username and password fields must not be empty.', 'cherry-real-estate' ),
			) );
		}

		$need_keys = array( 'login', 'pass' );
		$access    = wp_array_slice_assoc( $_POST['access'], $need_keys );

		if ( 2 != count( $access ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'All fields must not be empty.', 'cherry-real-estate' ),
			) );
		}

		$creds = array(
			'user_login'    => $access['login'],
			'user_password' => $access['pass'],
			'remember'      => true,
		);

		$user = wp_signon( $creds, false );

		if ( is_wp_error( $user ) ) {

			$error   = $user->get_error_message();
			$message = ! empty( $error ) ? $error : esc_html__( 'Authorization error. Please, check your personal data and try again.', 'cherry-real-estate' );

			wp_send_json_error( array(
				'message' => $message,
			) );
		}

		wp_send_json_success( $user );
	}

	/**
	 * Callback for register form.
	 *
	 * @since 1.0.0
	 */
	public static function register_callback() {

		// Check a nonce.
		$security = check_ajax_referer( '_tm-re-register-form', 'nonce', false );

		if ( false === $security ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Internal error. Please, try again later.', 'cherry-real-estate' ),
			) );
		}

		if ( empty( $_POST['access'] ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'All fields must not be empty.', 'cherry-real-estate' ),
			) );
		}

		$need_keys = array( 'login', 'email' );
		$access    = wp_array_slice_assoc( $_POST['access'], $need_keys );

		if ( 2 != count( $access ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'All fields must not be empty.', 'cherry-real-estate' ),
			) );
		}

		$user_login = sanitize_user( $access['login'], true );
		$user_email = sanitize_email( $access['email'] );
		$user       = register_new_user( $user_login, $user_email );

		if ( is_wp_error( $user ) ) {
			wp_send_json_error( array(
				'message' => $user->get_error_message(),
			) );
		}

		if ( 're_contributor' !== get_option( 'default_role' ) ) {

			// Change user role.
			$u = new WP_User( $user );

			// Remove role
			$u->remove_role( get_option( 'default_role' ) );

			// Add role
			$u->add_role( 're_contributor' );
		}

		wp_send_json_success( $user );
	}

	/**
	 * Collect an info about agent.
	 *
	 * @since  1.0.0
	 * @param  int $agent_id Agent ID.
	 * @return string
	 */
	public function _prepare_agent_contacts_to_mail( $agent_id ) {
		$mail_message = '';
		$data         = get_userdata( $agent_id );
		$contacts     = Model_Agents::get_agent_contacts( $agent_id );
		$socials      = Model_Agents::get_agent_socials( $agent_id );

		if ( false !== $data ) {
			$info   = array();
			$info[] = ! empty( $data->first_name ) ? $data->first_name : '';
			$info[] = ! empty( $data->last_name ) ? $data->last_name : '';
			$email  = ! empty( $data->user_email ) ? $data->user_email : '';

			$mail_message .= __( '<br>Your personal agent:<br><br>', 'cherry-real-estate' );

			if ( ! empty( $info ) ) {
				$mail_message .= join( $info, ' ' );
			}

			$mail_message .= sprintf( __( '<br>Email: %s', 'cherry-real-estate' ), antispambot( $email ) );
		}

		if ( ! empty( $contacts ) ) {
			foreach ( $contacts as $data ) {
				$mail_message .= sprintf( '<br><strong>%s</strong>: %s', esc_attr( $data['label'] ), esc_attr( $data['value'] ) );
			}
		}

		if ( ! empty( $socials ) ) {
			foreach ( $socials as $data ) {
				$mail_message .= sprintf( '<br><strong>%s</strong>: %s', esc_attr( $data['label'] ), esc_attr( $data['value'] ) );
			}
		}

		return $mail_message;
	}

	/**
	 * Wrapper-function for `wp_mail`.
	 *
	 * @since  1.0.0
	 * @param  string|array $to      Array or comma-separated list of email addresses to send message.
	 * @param  string       $subject Email subject.
	 * @param  string       $message Message contents.
	 * @return bool Whether the email contents were sent successfully.
	 */
	public static function send_mail( $to, $subject, $message ) {
		$headers = apply_filters( 'cherry_re_mail_headers', array( 'Content-type: text/html; charset=utf-8' ) );

		return wp_mail( $to, $subject, $message, $headers );
	}

	/**
	 * Retrieve allowed image types.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function allowed_image_types() {
		return apply_filters( 'cherry_re_allowed_image_types', array(
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpe'  => 'image/jpeg',
			'gif'  => 'image/gif',
			'png'  => 'image/png',
		) );
	}

	/**
	 * Retrieve a options for Sort select.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_sort_options() {
		return apply_filters( 'cherry_re_get_sort_options', array(
			'asc_price'  => esc_html__( 'Price (Low to High)', 'cherry-real-estate' ),
			'desc_price' => esc_html__( 'Price (High to Low)', 'cherry-real-estate' ),
			'asc_date'   => esc_html__( 'Date Old to New', 'cherry-real-estate' ),
			'desc_date'  => esc_html__( 'Date New to Old', 'cherry-real-estate' ),
		) );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

Model_Submit_Form::get_instance();
