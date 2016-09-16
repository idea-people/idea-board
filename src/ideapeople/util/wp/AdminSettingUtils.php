<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\util\wp;


class AdminSettingUtils {
	public $options = array();

	public $slug;

	public $option_name;

	public $option_group;

	/**
	 * AdminUtils constructor.
	 *
	 * @param $slug
	 * @param $option_name
	 * @param $option_group
	 */
	public function __construct( $slug, $option_name, $option_group ) {
		$this->slug         = $slug;
		$this->option_name  = $option_name;
		$this->option_group = $option_group;

		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	public function admin_init() {
		global $pagenow;

		if ( ! empty ( $pagenow ) && ( 'edit.php' === $pagenow || 'options.php' === $pagenow ) ) {
			$this->register_settings();
		}
	}

	public function add_submenu_page( $parent_slug, $page_title, $menu_title, $slug, $capability = 'manage_options' ) {
		add_submenu_page(
			$parent_slug,
			$page_title,
			$menu_title,
			$capability,
			$slug,
			array( $this, 'view' ) );
	}

	public function view() { ?>
		<div class="wrap">
			<h2><?php _e( '' ) ?></h2>
<<<<<<< HEAD

=======
>>>>>>> 7182463b56e448a16fdaf4ad5d4626e26d6f9dc8
			<form action="options.php" method="POST">
				<?php
				settings_fields( $this->option_group );
				do_settings_sections( $this->slug );
				?>
				<?php submit_button(); ?>
			</form>
		</div>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.jquery.min.js"></script>
		<script>
			(function ($) {
				$(document).ready(function () {
					$('.chosen-select').chosen({width: "80%"});
					$('.form-table .role_row').css('visibility', 'visible');
				});
			})(jQuery);
		</script>
		<?php
	}

	public function add_field( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'name'          => '',
			'id'            => '',
			'label'         => '',
			'section'       => '',
			'callback'      => '',
			'type'          => 'text',
<<<<<<< HEAD
			'value'         => $this->get_option( $args[ 'name' ] ),
			'default_value' => '',
			'required'      => false,
			'before'        => '',
			'after'         => ''
		) );

		if ( empty( $args[ 'callback' ] ) ) {
			$callback = array( $this, 'render_field' );
		} else {
			$callback = $args[ 'callback' ];
		}

		$this->options[ $args[ 'name' ] ] = '';

		add_settings_field( $args[ 'id' ]
			, $args[ 'label' ]
			, $callback
			, $this->slug
			, $args[ 'section' ]
=======
			'value'         => $this->get_option( $args['name'] ),
			'default_value' => '',
			'required'      => false
		) );

		if ( empty( $args['callback'] ) ) {
			$callback = array( $this, 'render_field' );
		} else {
			$callback = $args['callback'];
		}

		$this->options[ $args['name'] ] = '';

		add_settings_field( $args['id']
			, $args['label']
			, $callback
			, $this->slug
			, $args['section']
>>>>>>> 7182463b56e448a16fdaf4ad5d4626e26d6f9dc8
			, $args
		);
	}

	public function render_field( $args ) {
		$args = wp_parse_args( array(
<<<<<<< HEAD
			'name'       => sprintf( '%s[%s]', $this->option_name, $args[ 'name' ] ),
			'label'      => $args[ 'label' ],
			'field_type' => $args[ 'type' ],
			'require'    => $args[ 'required' ],
			'value'      => $args[ 'value' ]
=======
			'name'       => sprintf( '%s[%s]', $this->option_name, $args['name'] ),
			'label'      => $args['label'],
			'field_type' => $args['type'],
			'require'    => $args['required'],
			'value'      => $args['value']
>>>>>>> 7182463b56e448a16fdaf4ad5d4626e26d6f9dc8
		), $args );

		$f = new CustomField( $args );

<<<<<<< HEAD
		echo $args['before'];

		echo $f->renderField();

		echo $args['after'];
=======
		echo $f->renderField();
>>>>>>> 7182463b56e448a16fdaf4ad5d4626e26d6f9dc8
	}

	public function register_settings() {
		register_setting( $this->option_group, $this->option_name, array( $this, 'validate_option' ) );
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


	public function get_options() {
		return get_option( $this->option_name );
	}

	public function get_option( $key, $defaultValue = false ) {
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
		} else {
			return $defaultValue;
		}
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
}