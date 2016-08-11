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

	public static $errors = array();

	private function __construct() {
		add_action( 'wp_ajax_nopriv_login_form',    array( __CLASS__, 'login_callback' ) );
		add_action( 'wp_ajax_nopriv_register_form', array( __CLASS__, 'register_callback' ) );

		add_action( 'wp_ajax_submission_form', array( __CLASS__, 'submission_callback' ) );
		add_action( 'transition_post_status', array( $this, 'publish_property' ), 10, 3 );

		add_action( 'cherry_re_before_submission_form', array( $this, 'popup_link' ) );
		add_action( 'cherry_re_before_submission_form_btn', array( $this, 'popup_link' ) );
	}

	public function popup_link() {

		if ( is_user_logged_in() ) {
			return;
		}

		$args = array(
			'class' => 'tm-re-popup',
			'href'  => $this->get_popup_id(),
			'text'  => __( 'Please <a class="%1$s" href="#%2$s" data-tab="0">log in</a> or <a class="%1$s" href="#%2$s" data-tab="1">register</a> to create a new listing', 'cherry-real-estate' ),
		);

		$args = apply_filters( 'cherry_re_popup_link_args', $args );
		$text = sprintf(
			$args['text'],
			esc_attr( $args['class'] ),
			esc_attr( $args['href'] )
		);

		printf( '<div class="tm-re-auth-message">%s</div>', $text );
	}

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

		$need_confirm = current_user_can( 'manage_properties' ) ? false : true;
		$post_type    = cherry_real_estate()->get_post_type_name();
		$meta_prefix  = cherry_real_estate()->get_meta_prefix();
		$data         = wp_list_pluck( $_POST['property'], 'value', 'name' );

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
		), $_POST['property'] );

		$data = wp_parse_args( $data, $defaults );

		// Prepare data array for new property.
		$property_arr = array(
			'post_type'    => cherry_real_estate()->get_post_type_name(),
			'post_title'   => wp_strip_all_tags( $data['property_title'] ),
			'post_content' => $data['property_description'],
			'post_status'  => $need_confirm ? 'pending' : 'publish',
			'meta_input'   => array(
				$meta_prefix . 'price'          => $data['property_price'],
				$meta_prefix . 'status'         => $data['property_status'],
				$meta_prefix . 'bathrooms'      => $data['property_bathrooms'],
				$meta_prefix . 'bedrooms'       => $data['property_bedrooms'],
				$meta_prefix . 'area'           => $data['property_area'],
				$meta_prefix . 'parking_places' => $data['property_parking_places'],
				$meta_prefix . 'location'       => $data['property_address'],
			),
		);

		$property_arr = apply_filters( 'cherry_re_before_insert_post', $property_arr, $data );

		// Create new property.
		$property_ID = wp_insert_post( $property_arr, false );

		if ( 0 == $property_ID || is_wp_error( $property_ID ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Internal error. Please, try again later', 'cherry-real-estate' ),
			) );
		}

		// Set types for a property.
		$type_id = intval( $data['property_type'] );
		$term_taxonomy_id = wp_set_object_terms( $property_ID, $type_id, $post_type . '_type');

		// Retrieve the current user object (WP_User).
		$current_user = wp_get_current_user();
		$user_email   = false;

		if ( ! empty( $current_user->user_email ) ) {
			$user_email = sanitize_email( $current_user->user_email );
		}

		// Send notification e-mail.
		if ( $need_confirm && is_email( $user_email ) ) {

			$result = self::send_mail(
				$user_email,
				Model_Settings::get_notification_subject(),
				Model_Settings::get_notification_message()
			);

			if ( ! $result ) {
				wp_send_json_error( array(
					'message' => esc_html__( 'Internal error. Please, try again later', 'cherry-real-estate' ),
				) );
			}
		}

		if ( ! empty( $_POST['gallery'] ) ) {

			// These files need to be included as dependencies when on the front end.
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );

			// Let WordPress handle the upload.
			// Remember, 'my_image_upload' is the name of our file input in our form above.
			$attachment_id = media_handle_upload( 'property_gallery', $property_ID );

			if ( is_wp_error( $attachment_id ) ) {
				wp_send_json_error( array(
					'message' => $attachment_id->get_error_message(),
				) );
			}

		}

		wp_send_json_success( $property_ID );
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

		$property_id = $post->ID;
		$user_id     = $post->post_author;
		$user_data   = get_userdata( $user_id );
		$user_email  = false;

		if ( false !== $user_data ) {
			$user_email = isset( $user_data->user_email ) ? $user_data->user_email : false;
		}

		$message = Model_Settings::get_congratulate_message();
		$message .= sprintf( __( '<br>View: %s<br><br>', 'cherry-real-estate' ), get_permalink( $property_id ) );

		$meta_prefix    = cherry_real_estate()->get_meta_prefix();
		$agent_id       = get_post_meta( $property_id, $meta_prefix . 'agent', true );
		$agent_contacts = $this->_prepare_agent_contacts_to_mail( $agent_id );

		if ( ! empty( $agent_contacts ) ) {
			$message .= $agent_contacts;
		}

		return self::send_mail( $user_email, Model_Settings::get_congratulate_subject(), $message );
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

			$message = ! empty( $user->get_error_message() ) ? $user->get_error_message() : esc_html__( 'Authorization error. Please, check your personal data and try again.', 'cherry-real-estate' );

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

		// $need_keys = array( 'login', 'email', 'pass', 'cpass' );
		$need_keys = array( 'login', 'email' );
		$access    = wp_array_slice_assoc( $_POST['access'], $need_keys );

		if ( 2 != count( $access ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'All fields must not be empty.', 'cherry-real-estate' ),
			) );
		}

		$user_login = sanitize_text_field( $access['login'] );
		$user_email = sanitize_email( $access['email'] );
		$user       = register_new_user( $user_login, $user_email );

		if ( is_wp_error( $user ) ) {
			wp_send_json_error( array(
				'message' => $user->get_error_message(),
			) );
		}

		if ( 're_agent' !== get_option( 'default_role' ) ) {

			// Change user role.
			$u = new WP_User( $user );

			// Remove role
			$u->remove_role( get_option( 'default_role' ) );

			// Add role
			$u->add_role( 're_agent' );
		}

		wp_send_json_success( $user );
	}

	/**
	 * Collect an info about agent.
	 *
	 * @since  1.0.0
	 * @param  int $agent_id
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

			if ( ! empty( $info ) ) {
				$mail_message .= __( '<br>Your personal agent:<br><br>', 'cherry-real-estate' );
				$mail_message .= join( $info, ' ' );
			};
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
	 * @param  string|array $to          Array or comma-separated list of email addresses to send message.
	 * @param  string       $subject     Email subject
	 * @param  string       $message     Message contents
	 * @param  string|array $headers     Optional. Additional headers.
	 * @param  string|array $attachments Optional. Files to attach.
	 * @return bool Whether the email contents were sent successfully.
	 */
	public static function send_mail( $to, $subject, $message ) {
		$headers = apply_filters( 'cherry_re_mail_headers', array( 'Content-type: text/html; charset=utf-8' ) );

		return wp_mail( $to, $subject, $message, $headers );
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
