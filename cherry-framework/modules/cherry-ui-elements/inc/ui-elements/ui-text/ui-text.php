<?php
/**
 * Class for the building ui-text elements.
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.en.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'UI_Text' ) ) {

	/**
	 * Class for the building ui-text elements.
	 */
	class UI_Text extends UI_Element implements I_UI {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $defaults_settings = array(
			'type'        => 'text',
			'id'          => 'cherry-ui-input-id',
			'name'        => 'cherry-ui-input-name',
			'value'       => '',
			'placeholder' => '',
			'label'       => '',
			'class'       => '',
			'master'      => '',
			'required'    => false,
			'lock'        => false,
		);

		/**
		 * Instance of this Cherry5_Lock_Element class.
		 *
		 * @since 1.0.0
		 * @var object
		 * @access private
		 */
		private $lock_element = null;

		/**
		 * Constructor method for the UI_Text class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-input-text-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );
			$this->lock_element = new Cherry5_Lock_Element( $this->settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Get required attribute.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function get_required() {

			if ( $this->settings['required'] ) {
				return 'required="required"';
			}

			return '';
		}

		/**
		 * Render html UI_Text.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			$html = '';
			$class = implode( ' ',
				array(
					$this->settings['class'],
					$this->settings['master'],
					$this->lock_element->get_class(),
				)
			);

			$html .= '<div class="cherry-ui-container ' . esc_attr( $class ) . '">';
				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
				}
				$html .= '<input type="' . esc_attr( $this->settings['type'] ) . '" id="' . esc_attr( $this->settings['id'] ) . '" class="widefat cherry-ui-text" name="' . esc_attr( $this->settings['name'] ) . '" value="' . esc_html( $this->settings['value'] ) . '" placeholder="' . esc_attr( $this->settings['placeholder'] ) . '" ' . $this->get_required() . $this->lock_element->get_disabled_attr() . '>';
				$html .= $this->lock_element->get_html();
			$html .= '</div>';
			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Text.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_style(
				'ui-text',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-text/assets/min/ui-text.min.css', Cherry_UI_Elements::$module_path ) ),
				array(),
				Cherry_UI_Elements::$core_version,
				'all'
			);
		}
	}
}
