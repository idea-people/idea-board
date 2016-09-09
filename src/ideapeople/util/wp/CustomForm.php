<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2015-02-14
 * Time: 오전 12:20
 */
namespace ideapeople\util\wp;

class CustomForm {
	private $option;

	private $key;

	public $row;

	public function __construct( $key ) {
		$this->key = $key;

		$this->option = new Option( $this->key );
	}

	/**
	 * @param $field CustomField|array
	 *
	 * @return bool
	 */
	public function update_field( $field ) {
		if ( is_array( $field ) ) {
			$field = new CustomField( $field );
		}

		do_action( 'pre-custom-form-field-update' );

		return $this->option->put( $field->getName(), $field );
	}

	/**
	 * @param $field_name
	 *
	 * @return bool
	 */
	public function has_field( $field_name ) {
		return $this->option->has( $field_name );
	}

	/**
	 * @param $field_name
	 *
	 * @return CustomField
	 */
	public function get_field( $field_name ) {
		$field = null;

		if ( $this->has_field( $field_name ) ) {
			$field = $this->option->get( $field_name, null );
		} else {
			$field = new CustomField();
		}

		return apply_filters( 'custom-form-get-field', $field );
	}

	/**
	 * @param $field_name
	 */
	public function remove_field( $field_name ) {
		do_action( 'pre-custom-form-field-remove' );

		$this->option->remove( $field_name );
	}

	public function get_field_count() {
		return count( $this->get_fields() );
	}

	/**
	 * @return CustomField[]
	 */
	public function get_fields() {
		$result = array();

		foreach ( $this->option->get_options() as $key => $value ) {
			$result[] = $value;
		}

		uasort( $result, function ( $a, $b ) {
			return $a->order - $b->order;
		} );

		$result = apply_filters( 'custom-form-get-fields', $result );

		$this->row = count( $result );

		return $result;
	}

	public function isEmpty() {
		return $this->get_field_count() == 0;
	}

	public function isRowOdd() {
		return $this->row % 2 === 1;
	}

	public function get_field_names() {
		$result = array();

		foreach ( $this->get_fields() as $field ) {
			$result[] = $field->getName();
		}

		return $result;
	}

	/**
	 * @return Option
	 */
	public function getOption() {
		return $this->option;
	}

	public function destroy() {
		$this->option->destroy();
	}
}