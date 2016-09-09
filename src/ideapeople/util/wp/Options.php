<?php

namespace ideapeople\util\wp;

use ideapeople\util\common\Utils;
use ideapeople\util\html\Form;

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 4. 5.
 * Time: 오후 5:52
 */
abstract class Options {
	public $setting_name;

	public function __construct( $setting_name = '' ) {
		$this->setting_name = $setting_name;

		add_action( 'admin_init', array( $this, 'register_setting' ) );
	}

	public function register_setting() {
		register_setting( $this->setting_name, $this->setting_name, array( $this, 'validate' ) );
	}

	public function un_register_setting() {
		unregister_setting( $this->setting_name, $this->setting_name );
	}

	public function destroy() {
		delete_option( $this->setting_name );
	}

	public function get_options() {
		return get_option( $this->setting_name, array() );
	}

	public function get_option( $key, $defaultValue = null ) {
		$options = $this->get_options();

		return Utils::getVar( $options, $key, $defaultValue );
	}

	public function update_option( $key, $value ) {
		$options         = $this->get_options();
		$options[ $key ] = $value;

		return update_option( $this->setting_name, $options );
	}

	public function add_option( $key, $value ) {
		return $this->update_option( $key, $value );
	}

	public function setting_fields() {
		settings_fields( $this->setting_name );
	}

	public function text( $key, $attr = array() ) {
		return Form::text( $this->setting_name . "[{$key}]", $this->get_option( $key ), $attr );
	}

	public function select( $key, $options = array(), $attr = array() ) {
		return Form::select( $this->setting_name . "[{$key}]", $options, $this->get_option( $key ), $attr );
	}

	public function wp_editor( $key, $settings = array() ) {
		$defaults = array(
			'media_buttons' => true,
			'textarea_name' => $this->setting_name . "[{$key}]"
		);

		$args = wp_parse_args( $settings, $defaults );

		wp_editor( $this->get_option( $key ), $key, $args );
	}

	public abstract function validate( $options );
}