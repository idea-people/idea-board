<?php
/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 4. 5.
 * Time: 오후 5:52
 */

namespace ideapeople\util\wp;

/**
 * todo 나중에 모두 분리할것
 */
class Option {
	public $option_name;

	private $options = array();

	public function __construct( $option_name ) {
		$this->option_name = '___' . $option_name;

		add_option( $this->option_name, array() );

		$this->options = get_option( $this->option_name );
	}

	public function get( $key, $default = false ) {
		$option = $this->get_options();

		if ( $this->has( $key ) ) {
			return $option[ $key ];
		}

		return $default;
	}

	public function put( $key, $value ) {
		if ( is_null( $key ) ) {
			return false;
		}

		$this->options[ $key ] = $value;

		return $this->commit();
	}

	public function remove( $key ) {
		if ( $this->has( $key ) ) {
			unset( $this->options[ $key ] );
		}

		return $this->commit();
	}

	public function has( $key ) {
		return array_key_exists( $key, $this->get_options() );
	}

	public function commit() {
		return update_option( $this->option_name, $this->get_options() );
	}

	public function destroy() {
		return delete_option( $this->option_name );
	}

	public function get_options() {
		return apply_filters( "get-{$this->option_name}", $this->options );
	}
}