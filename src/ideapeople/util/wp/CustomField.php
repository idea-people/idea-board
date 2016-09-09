<?php
/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2015-02-14
 * Time: 오전 12:20
 */

namespace ideapeople\util\wp;

class CustomField {
	const KEY = 'CustomField';

	const FIELD_TYPE_TEXT = 'text';
	const FIELD_TYPE_NUMBER = 'number';
	const FIELD_TYPE_TEXT_AREA = 'textarea';
	const FIELD_TYPE_CHECK_BOX = 'checkbox';
	const FIELD_TYPE_PASSWORD = 'password';
	const FIELD_TYPE_SELECT = 'select';

	static $FIELD_TYPES = array(
		self::FIELD_TYPE_TEXT,
		self::FIELD_TYPE_NUMBER,
		self::FIELD_TYPE_TEXT_AREA,
		self::FIELD_TYPE_SELECT,
		self::FIELD_TYPE_CHECK_BOX,
		self::FIELD_TYPE_PASSWORD
	);

	public $label;
	public $name;
	public $field_type;
	public $visible;
	public $require;
	public $checked;
	public $order;
	public $render_option;
	public $placeHolder;
	public $cssClass;

	public $native;

	/**
	 * FIELD_TYPE_SELECT 라면 a=b,c=d 라고 입력해 준다. 결과는
	 * <select>
	 *   <option name="a">b</option>
	 *   <option name="c">d</option>
	 * </select>
	 * 라고 출력된다.
	 */
	public $value;
	public $default_value;

	private $args;

	function __construct( $args = array() ) {
		$defaults = array(
			'label'         => null,
			'name'          => null,
			'field_type'    => self::FIELD_TYPE_TEXT,
			'visible'       => true,
			'require'       => false,
			'checked'       => false,
			'native'        => false,
			'order'         => 9999,
			'value'         => '',
			'default_value' => '',
			'tag_end'       => '',
			'placeHolder'   => '',
			'cssClass'      => ''
		);

		$args = wp_parse_args( $args, $defaults );

		$this->args = $args;

		foreach ( $args as $key => $value ) {
			$this->$key = $value;
		}
	}

	public function getLabel() {
		return $this->label;
	}

	public function getName() {
		return $this->name;
	}

	public function getFieldType() {
		return $this->field_type;
	}

	public function getVisible() {
		return $this->visible;
	}

	public function getVisibleNm() {
		if ( $this->visible ) {
			return '표시';
		} else {
			return '표시안함';
		}
	}

	public function getRequire() {
		return $this->require;
	}

	public function getRequireNm() {
		if ( $this->require ) {
			return '필수';
		} else {
			return '필수아님';
		}
	}

	public function getChecked() {
		return $this->checked;
	}

	public function getOrder() {
		return $this->order;
	}

	public function getRenderOption() {
		return $this->render_option;
	}

	public function getValue() {
		return $this->value;
	}

	public function getDefaultValue() {
		return $this->default_value;
	}

	public function getNative() {
		return $this->native;
	}

	public function setNative( $native ) {
		$this->native = $native;
	}

	public function renderLabel( $args = array() ) {
		$defaults = array(
			'before_label' => '',
			'after_label'  => '',
			'label_style'  => '',
		);

		$args = wp_parse_args( $args, $this->args );
		$args = wp_parse_args( $args, $defaults );

		$output = '';
		$output .= "{$args['before_label']}<label for=\"{$this->getName()}\" style=\"{$args['label_style']}\">{$this->getLabel()}</label>{$args['after_label']}";

		return $output;
	}

	public function renderField( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'class'         => $this->getName(),
			'id'            => $this->getName(),
			'value'         => empty( $this->getValue() ) ? $this->default_value : $this->getValue(),
			'after_render'  => '',
			'style'         => '',
			'default_value' => $this->default_value,
			'cssClass'      => $this->cssClass
		) );

		$args = wp_parse_args( $args, $this->args );

		$output = '';

		switch ( $this->getFieldType() ) {
			case self::FIELD_TYPE_SELECT :
				$output .= "<select name=\"{$this->getName()}\" id=\"{$args['id']}\" class=\"{$args['cssClass']}\" style=\"{$args['style']}\" {$args['tag_end']}>";

				$options = $this->convertValueToOption();

				foreach ( $options as $option ) {
					$selected = '';

					if ( $option[ 'text' ] == $this->getValue() ) {
						$selected = 'selected="selected"';
					}

					$output .= "<option name=\"{$option['name']}\" {$selected}>{$option['text']}</option>";
				}

				$output .= "</select>";

				break;
			case self::FIELD_TYPE_TEXT_AREA :
				$output .= "<textarea name=\"{$this->getName()}\" id=\"{$args['id']}\" class=\"{$args['cssClass']}\" style=\"{$args['style']}\" ";
				$output .= $this->require ? 'required="required"' : '';
				$output .= "{$args['tag_end']}/>";

				$output .= $args[ 'value' ];
				$output .= "</textarea>";
				break;
			default:
				$output .= "<input type=\"{$this->getFieldType()}\" value=\"{$args['value']}\" name=\"{$this->getName()}\" id=\"{$args['name']}\" class=\"{$args['cssClass']}\" style=\"{$args['style']}\" placeholder='{$this->placeHolder}' ";
				$output .= $this->require ? 'required="required"' : '';
				$output .= "{$args['tag_end']}/>";
				break;
		}

		return $output;
	}

	public function render( $args = array() ) {
		$args = wp_parse_args( $args, $this->args );
		$args = wp_parse_args( $args, array(
			'class'         => $this->getName(),
			'id'            => $this->getName(),
			'value'         => empty( $this->getValue() ) ? $this->default_value : $this->getValue(),
			'before_label'  => '',
			'after_label'   => '',
			'after_render'  => '',
			'style'         => '',
			'label_style'   => '',
			'default_value' => $this->default_value
		) );

		if ( is_null( $args[ 'id' ] ) ) {
			return ' 존재하지않는 필드입니다.';
		}

		$output = '';
		$output .= $this->renderLabel( $args );
		$output .= $this->renderField( $args );

		return $output;
	}

	private function convertValueToOption() {
		$result = array();
		if ( is_array( $this->default_value ) ) {
			foreach ( $this->default_value as $name => $text ) {
				$result[] = array( 'name' => $name, 'text' => $text );
			}
		} else {
			$values = explode( ',', $this->default_value );

			foreach ( $values as $value ) {
				$v = explode( '=', $value );

				$result[] = array( 'name' => @$v[ 0 ], 'text' => @$v[ 1 ] );
			}
		}

		return $result;
	}
}