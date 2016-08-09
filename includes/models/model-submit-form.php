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
		add_action( 'wp_ajax_nopriv_submission_form', array( __CLASS__, 'step1' ) );
		add_action( 'wp_ajax_submission_form', array( __CLASS__, 'step1' ) );

		add_action( 'wp_ajax_nopriv_login_form', array( __CLASS__, 'login_callback' ) );
		add_action( 'wp_ajax_login_form', array( __CLASS__, 'login_callback' ) );

		add_action( 'wp_footer', array( $this, 'step2' ), 99 );

		add_action( 'publish_tm-property', array( $this, 'step3' ), 10, 2 );

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
			'text'  => __( 'Please log in or register to create a new listing', 'cherry-real-estate' ),
		);

		$link_format = apply_filters( 'cherry_re_popup_link_format', '<a class="%s" href="#%s">%s</a>', $args );

		printf(
			$link_format,
			esc_attr( $args['class'] ),
			esc_attr( $args['href'] ),
			esc_html( $args['text'] )
		);
	}

	public static function get_popup_id() {
		return apply_filters( 'cherry_re_get_popup_html_id', 'tm-re-loginform' );
	}

	/**
	 * Step 1:
	 *
	 * - security check
	 * - create request (new property with `draft` status)* or publish property
	 * - send confirm e-mail*
	 *
	 * (*) - if it's a new or not approved agent
	 *
	 * @since 1.0.0
	 */
	public static function step1() {

		// Check a nonce.
		$security = check_ajax_referer( '_tm-re-submission-form', 'nonce', false );

		if ( false === $security ) {
			wp_send_json_error( array( 'message' => self::get_errors( 'nonce' ) ) );
		}

		if ( empty( $_POST['property'] ) ) {
			wp_send_json_error( array( 'message' => self::get_errors( 'internal' ) ) );
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
			'agent_email'             => '',
		), $_POST['property'] );

		$data        = wp_parse_args( $data, $defaults );
		$agent_email = sanitize_email( $data['agent_email'] );

		// Prepare data array for new property.
		$property_arr = array(
			'post_type'    => cherry_real_estate()->get_post_type_name(),
			'post_title'   => wp_strip_all_tags( $data['property_title'] ),
			'post_content' => $data['property_description'],
			'post_status'  => $need_confirm ? 'draft' : 'publish',
			'tax_input'    => array(
				$post_type . '_type' => array( $data['property_type'] ),
			),
			'meta_input'   => array(
				$meta_prefix . 'price'          => $data['property_price'],
				$meta_prefix . 'status'         => $data['property_status'],
				$meta_prefix . 'bathrooms'      => $data['property_bathrooms'],
				$meta_prefix . 'bedrooms'       => $data['property_bedrooms'],
				$meta_prefix . 'area'           => $data['property_area'],
				$meta_prefix . 'parking_places' => $data['property_parking_places'],
				$meta_prefix . 'location'       => $data['property_address'],
				$meta_prefix . 'agent_email'    => $agent_email,
			),
		);

		$property_arr = apply_filters( 'cherry_re_before_insert_post', $property_arr, $data );

		// Create new property.
		$property_ID = wp_insert_post( $property_arr, false );

		if ( 0 == $property_ID || is_wp_error( $property_ID ) ) {
			wp_send_json_error( array( 'message' => self::get_errors( 'internal' ) ) );
		}

		// Send e-mail with confirm link.
		if ( $need_confirm ) {

			$bare_url = add_query_arg( array(
				'property_id' => $property_ID,
				'from'        => $agent_email,
			), home_url() );

			$confirm_url = wp_nonce_url( $bare_url, 'confirm-property_' . $property_ID );

			$message = sprintf(
				'%s <a href="%s" target="_blank">&#8690;</a>',
				Model_Settings::get_confirm_message(),
				$confirm_url
			);

			$result = self::send_mail( $agent_email, Model_Settings::get_confirm_subject(), $message );

			// if ( ! $result ) {
			// 	wp_send_json_error( array( 'message' => self::get_errors( 'internal' ) ) );
			// }
		}

		wp_send_json_success( $property_ID );
	}

	/**
	 * Step 2*:
	 *
	 * - security check
	 * - change property status to the `pending`
	 * - output popup
	 * - send e-mail with notification
	 *
	 * (*) - if it's a new or not approved agent
	 *
	 * @since 1.0.0
	 */
	public function step2() {

		// Check a property ID.
		if ( empty( $_GET['property_id'] ) ) {
			return;
		}

		$property_ID = absint( $_GET['property_id'] );

		// Check a nonce.
		if ( empty( $_GET['_wpnonce'] )
			|| ! wp_verify_nonce( $_GET['_wpnonce'], 'confirm-property_' . $property_ID )
		) {
			return;
		}

		$property_status = get_post_status( $property_ID );

		// Check a property status.
		if ( in_array( $property_status, array( 'pending', 'publish' ) ) ) {
			return;
		}

		// Update property status.
		$updated = wp_update_post( array(
			'ID'          => $property_ID,
			'post_status' => 'pending',
		), false );

		if ( 0 == $updated || is_wp_error( $updated ) ) {
			return;
		}

		// Include a popup template.
		cherry_re_get_template( 'misc/confirm-popup' );

		// Check e-mail.
		if ( empty( $_GET['from'] ) ) {
			return;
		}

		$email = sanitize_email( $_GET['from'] );

		if ( ! is_email( $email ) ) {
			return;
		}

		// Send e-mail.
		return self::send_mail(
			$email,
			Model_Settings::get_notification_subject(),
			Model_Settings::get_notification_message()
		);
	}

	/**
	 * Step 3*:
	 *
	 * - create new agent, if that not exists
	 * - update property author
	 * - send e-mail with congratulations
	 *
	 * (*) - if it's a new or not approved agent
	 *
	 * @since 1.0.0
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function step3( $property_id, $post ) {
		$meta_prefix  = cherry_real_estate()->get_meta_prefix();
		$agent_email  = get_post_meta( $property_id, $meta_prefix . 'agent_email', true );
		$agent_email  = sanitize_email( $agent_email );
		$agent_id     = email_exists( $agent_email );
		$agent_access = array();

		if ( false === $agent_id ) {
			$agent_access = self::create_agent( $agent_email, $property_id );
		}

		$agent_id = ! empty( $agent_access['id'] ) ? $agent_access['id'] : 0;

		if ( $agent_id != $post->post_author ) {

			// Unhook this function so it doesn't loop infinitely.
			remove_action( 'publish_tm-property', array( $this, 'step3' ) );

			$property_id = wp_update_post( array(
				'ID'          => $property_id,
				'post_author' => $agent_id,
			), false );

			// Re-hook this function.
			add_action( 'publish_tm-property', array( $this, 'step3' ) );

			if ( 0 == $property_id || is_wp_error( $property_id ) ) {
				return;
			}
		}

		$title     = $post->post_title;
		$permalink = get_permalink( $property_id );

		$message = Model_Settings::get_congratulate_message();
		$message .= sprintf( __( '<br>View: %s<br><br>', 'cherry-real-estate' ), $permalink );

		if ( ! empty( $agent_access['login'] ) && ! empty( $agent_access['pass'] ) ) {

			$access_message = sprintf(
				__( 'Dashboard: <a href="%1$s" target="_blank">%1$s</a><br><br>', 'cherry-real-estate' ),
				esc_url( get_edit_post_link( $property_id ) )
			);

			$access_message .= sprintf(
				__( 'Login: %s <br> Password: %s', 'cherry-real-estate' ),
				$agent_access['login'],
				$agent_access['pass']
			);

			$stop_access = apply_filters( 'cherry_re_stop_send_agent_access', false );

			if ( false === $stop_access ) {
				$message .= $access_message;
			}
		}

		return self::send_mail( $agent_email, Model_Settings::get_congratulate_subject(), $message );
	}

	/**
	 * Create new agent, if there not exists.
	 *
	 * @since  1.0.0
	 * @param  string     $email       Agent e-mail.
	 * @param  init       $property_id Property ID.
	 * @return bool|array Retrieve a new agent access data (login and password) or false - if agent are exists.
	 */
	public static function create_agent( $email, $property_id ) {

		// Generate the password and create the user.
		$password = wp_generate_password( 12, false );
		$agent_id = wp_insert_user( array(
			'user_login' => $email,
			'user_pass'  => $password,
			'user_email' => $email,
			'role'       => 're_agent',
		) );

		if ( is_wp_error( $agent_id ) ) {
			return false;
		}

		return array(
			'id'    => $agent_id,
			'login' => $email,
			'pass'  => $password,
		);
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

	/**
	 * Step 1:
	 *
	 * - security check
	 * - create request (new property with `draft` status)* or publish property
	 * - send confirm e-mail*
	 *
	 * (*) - if it's a new or not approved agent
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

		$access = $_POST['access'];

		if ( ! array_key_exists( 'login', $access ) || ! array_key_exists( 'pass', $access ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'The username and password fields must not be empty.', 'cherry-real-estate' ),
			) );
		}

		$creds = array(
			'user_login'    => $access['login'],
			'user_password' => $access['pass'],
			'remember'      => true,
		);

		$user = wp_signon( $creds, false );

		if ( is_wp_error( $user ) ) {
			wp_send_json_error( array(
				'message' => $user->get_error_message(),
			) );
		}

		wp_send_json_success( $user );
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
