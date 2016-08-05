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

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	public static $errors = array();

	private function __construct() {
		add_action( 'publish_tm-property', array( $this, 'property_published' ), 10, 2 );
		add_action( 'wp_footer', array( $this, 'popup' ), 99 );
	}

	/**
	 * Callback of shortcode submit form
	 */
	public static function submit_form_callback() {
		$security = check_ajax_referer( '_tm-re-submit-form', 'nonce', false );

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

		$data = wp_parse_args( $data, $defaults );


		// wp_send_json_success( $data['property_type'] );

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
				$meta_prefix . 'agent_email'    => sanitize_email( $data['agent_email'] ),
			),
		);

		$property_arr = apply_filters( 'cherry_re_before_insert_post', $property_arr, $data );
		$property_ID  = wp_insert_post( $property_arr, false );
		// wp_send_json_success( $property_ID );

		if ( 0 == $property_ID ) {
			wp_send_json_error( array( 'message' => self::get_errors( 'internal' ) ) );
		}

		if ( $need_confirm ) {

			$bare_url = add_query_arg( array(
				'property_id' => absint( $property_ID ),
				'from'        => sanitize_email( $data['agent_email'] ),
			), home_url() );

			$confirm_url = wp_nonce_url( $bare_url, 'confirm-property_' . $property_ID );
			$message     = sprintf( '%s <a href="%s" target="_blank">&#8690;</a>', Model_Settings::get_confirn_message(), $confirm_url );

			$result = self::send_mail(
				sanitize_email( $data['agent_email'] ),
				Model_Settings::get_confirn_subject(),
				$message
			);

			// wp_send_json_success( array(
			// 	sanitize_email( $data['agent_email'] ),
			// 	Model_Settings::get_confirn_subject(),
			// 	$message
			// ) );

			if ( ! $result ) {
				wp_send_json_error( array( 'message' => self::get_errors( 'internal' ) ) );
			}
		}

		wp_send_json_success( $property_ID );
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

	public function popup() {

		if ( empty( $_GET['property_id'] ) ) {
			return;
		}

		$property_ID = absint( $_GET['property_id'] );

		if ( empty( $_GET['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'confirm-property_' . $property_ID ) ) {
			return;
		}

		cherry_re_get_template( 'misc/confirm-popup' );

		if ( empty( $_GET['from'] ) ) {
			return;
		}

		if ( ! is_email( $_GET['from'] ) ) {
			return;
		}

		$property_status = get_post_status( $property_ID );

		if ( in_array( $property_status, array( 'pending', 'publish' ) ) ) {
			return;
		}

		$updated = wp_update_post( array(
			'ID'          => $property_ID,
			'post_status' => 'pending',
		), false );

		if ( ! $updated ) {
			return;
		}

		return self::send_mail(
			$_GET['from'],
			'Уведомление',
			'Ваша заявка успешно поставлена в очередь для модерации. Ожидайте письмо об активации заявки.'
		);
	}

	/**
	 * Callback when property is published.
	 *
	 * @since 1.0.0
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function property_published( $post_id, $post ) {
		$meta_prefix  = cherry_real_estate()->get_meta_prefix();
		$agent_email  = get_post_meta( $post_id, $meta_prefix . 'agent_email', true );
		$agent_access = self::create_agent( $agent_email, $post_id );

		if ( false === $agent_access ) {
			return;
		}

		$title     = $post->post_title;
		$permalink = get_permalink( $post_id );
		$subject   = sprintf( 'Published: %s', $title );

		$message = sprintf( 'Congratulations! Your property has been published.<br>' );
		$message .= sprintf( 'View: %s<br><br>', $permalink );
		$message .= sprintf( 'login: %s | password: %s', $agent_access['login'], $agent_access['password'] );

		self::send_mail( $agent_email, $subject, $message );
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

		if ( false !== username_exists( $email ) ) {
			return false;
		}

		// Generate the password and create the user.
		$password = wp_generate_password( 12, false );
		$agent_id = wp_insert_user( array(
			'user_login' => $email,
			'user_pass'  => $password,
			'user_email' => $email,
			'role'       => 're_agent',
		) );

		// // Set the nickname.
		// $agent_id = wp_update_user( array(
		// 	'ID'       => $agent_id,
		// 	'nickname' => $email,
		// ) );

		if ( is_wp_error( $user_id ) ) {
			return false;
		}

		// // Set the role.
		// $user = new WP_User( $agent_id );
		// $user->set_role( 're_agent' );

		$property_id = wp_update_post( array(
			'ID'          => $property_id,
			'post_author' => $agent_id,
		), false );

		if ( 0 == $property_id ) {
			return false;
		}

		return array(
			'login'    => $email,
			'password' => $password,
		);
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
