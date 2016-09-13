<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-08
 * Time: 오전 9:43
 */

namespace ideapeople\board\action;


use ideapeople\board\PluginConfig;
use ideapeople\util\html\Form;
use ideapeople\util\view\PathView;

class AdminGlobalAction2 {
	public $options = array();
	public $slug = 'idea_board_global';
	public $option_name = 'idea_board_global_option';
	public $option_group = 'idea_board_global_options';

	/**
	 * @return static
	 */
	public static function instance() {
		static $instance;

		if ( ! $instance ) {
			$instance = new static();
		}

		return $instance;
	}

	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ) );
	}

	public function admin_init() {
		global $pagenow;

		if ( ! empty ( $pagenow ) && ( 'edit.php' === $pagenow || 'options.php' === $pagenow ) ) {
			$this->register_settings();
		}
	}


	public function add_page() {
		add_submenu_page(
			'edit.php?post_type=' . PluginConfig::$board_post_type,
			'설정',
			'설정',
			'manage_options',
			'idea_board_global_settings',
			array( $this, 'view' ) );
	}

	public function register_settings() {
		register_setting( $this->option_group, $this->option_name, array( $this, 'validate_option' ) );

		add_settings_section(
			'section_1',
			'플러그인 환경설정',
			array( $this, 'view_section_1' ),
			$this->slug
		);

		$this->options['helpers'] = '';

		add_settings_field( 'helpers', 'helpers', array( $this, 'add_helper_field' ), $this->slug, 'section_1', array(
				'label_for'   => 'helpers',
				'name'        => 'helpers',
				'value'       => $this->get_option( 'helpers' ),
				'option_name' => $this->option_name
			)
		);

		$current_mime_types = wp_get_mime_types();

		$mime_types = array();

		foreach ( $current_mime_types as $key => $value ) {
			$mime_types[ $key ] = $key;
		}

		$args = array(
			'options'  => $mime_types,
			'selected' => $this->get_upload_file_types()
		);

		$this->create_field( 'idea_board_upload_file_types', '업로드 허용할 확장자', 'idea_board_upload_file_types', '_create_select_chosen', $args );

		$this->create_field( 'idea_board_upload_max_size',
			'업로드 가능한 용량',
			'idea_board_upload_max_size', '_create_text_field' );
	}

	public function get_upload_file_types() {
		$mime_types = wp_get_mime_types();

		$o = $this->get_option( 'idea_board_upload_file_types' );

		if ( ! $o ) {
			$o = array_keys( $mime_types );
		}

		return $o;
	}

	public function view_section_1() {
		echo '';
	}

	public function view() {
		$view = new PathView( PluginConfig::$plugin_path . 'views/admin/global_setting.php' );
		echo $view->render();
	}

	public function validate_option( $values ) {
		$out = array();

		foreach ( $this->options as $key => $value ) {
			if ( empty ( $values[ $key ] ) ) {
				$out[ $key ] = $value;
			} else {
				$out[ $key ] = $values[ $key ];
			}
		}

		return $out;
	}

	public function get_allowed_mime_types() {
		$current_mime_types = get_allowed_mime_types();
	}

	public function add_helper_field() {
		$helper_options = array();
		$helpers        = idea_board_plugin()->helper_loader->get_helpers();
		$helper_selects = $this->get_option( 'helpers' );

		foreach ( $helpers as $helper ) {
			$data = $helper->get_plugin_data();

			if ( $data['Name'] ) {
				$helper_options[ $helper->get_name() ] = $data['Name'];
			}
		}

		echo Form::select( $this->option_name . '[helpers][]', $helper_options, $helper_selects, array(
			'multiple' => true,
			'class'    => "chosen-select"
		) );
	}


	public function create_field( $id, $label, $option_name, $func, $args = array() ) {
		$this->options[ $option_name ] = '';

		add_settings_field( $id, $label, array( $this, $func ), $this->slug, 'section_1',
			array(
				'label_for'   => $option_name,
				'name'        => $option_name,
				'value'       => $this->get_option( $option_name ),
				'option_name' => $this->option_name,
				'args'        => $args
			)
		);
	}

	public function _create_checkbox_field( $args ) {
		printf(
			'<input type="checkbox" name="%1$s[%2$s]" id="%3$s" value="1" class="regular-checkbox" %5$s>',
			$args['option_name'],
			$args['name'],
			$args['label_for'],
			$args['value'],
			checked( 1, $this->get_option( $args['name'] ), false )
		);
	}

	public function _create_textarea_field( $args ) {
		printf(
			'<textarea type="text" name="%1$s[%2$s]" id="%3$s" class="regular-text">%4$s</textarea>',
			$args['option_name'],
			$args['name'],
			$args['label_for'],
			$args['value']
		);
	}

	public function _create_text_field( $args ) {
		printf(
			'<input type="text" name="%1$s[%2$s]" id="%3$s" value="%4$s" class="regular-text">',
			$args['option_name'],
			$args['name'],
			$args['label_for'],
			$args['value']
		);
	}

	public function _create_select_chosen( $args ) {
		echo Form::select( $this->option_name . '[' . $args['name'] . '][]', $args['args']['options'], $args['args']['selected'], array(
			'multiple' => true,
			'class'    => "chosen-select"
		) );
	}

	public function get_options() {
		return get_option( $this->option_name );
	}

	public function get_option( $key ) {
		$v = $this->get_options();

		if ( isset( $v[ $key ] ) ) {
			if ( is_array( $v[ $key ] ) ) {
				foreach ( $v[ $key ] as &$value ) {
					$value = esc_attr( $value );
				}

				return $v[ $key ];
			} else {
				return esc_attr( $v[ $key ] );
			}
		}

		return false;
	}
}