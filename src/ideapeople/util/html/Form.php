<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-24
 * Time: 오후 8:36
 */

namespace ideapeople\util\html;


class Form {
	public static function open( $action = '', array $attributes = array() ) {
		if ( isset( $attributes[ 'multipart' ] ) && $attributes[ 'multipart' ] ) {
			$attributes[ 'enctype' ] = 'multipart/form-data';
			unset( $attributes[ 'multipart' ] );
		}
		$attributes = array_merge( array( 'method' => 'post', 'accept-charset' => 'utf-8' ), $attributes );

		return "<form action=\"{$action}\"" . Html::attributes( $attributes ) . '>';
	}

	public static function close() {
		return '</form>';
	}

	public static function label( $text, $fieldName = null, array $attributes = array() ) {
		if ( ! isset( $attributes[ 'for' ] ) && $fieldName !== null ) {
			$attributes[ 'for' ] = static::autoId( $fieldName );
		}
		if ( ! isset( $attributes[ 'id' ] ) && isset( $attributes[ 'for' ] ) ) {
			$attributes[ 'id' ] = $attributes[ 'for' ] . '-label';
		}

		return Html::tag( 'label', $attributes, $text );
	}

	public static function text( $name, $value = null, array $attributes = array() ) {
		$attributes = array_merge( array(
			'id'    => static::autoId( $name ),
			'name'  => $name,
			'type'  => 'text',
			'value' => $value,
		), $attributes );

		return Html::tag( 'input', $attributes );
	}

	public static function password( $name, $value = null, array $attributes = array() ) {
		$attributes = array_merge( array(
			'id'    => static::autoId( $name ),
			'name'  => $name,
			'type'  => 'password',
			'value' => $value,
		), $attributes );

		return Html::tag( 'input', $attributes );
	}

	public static function hidden( $name, $value, array $attributes = array() ) {
		$attributes = array_merge( array(
			'id'    => static::autoId( $name ),
			'name'  => $name,
			'type'  => 'hidden',
			'value' => $value,
		), $attributes );

		return Html::tag( 'input', $attributes );
	}

	public static function textArea( $name, $text = null, array $attributes = array() ) {
		$attributes = array_merge( array(
			'id'   => static::autoId( $name ),
			'name' => $name,
		), $attributes );

		return Html::tag( 'textarea', $attributes, (string) $text );
	}

	public static function checkBox( $name, $checked = false, $value = 1, array $attributes = array(), $withHiddenField = true ) {
		$auto_id = static::autoId( $name );

		$checkboxAttributes = array_merge( array(
			'name'    => $name,
			'type'    => 'checkbox',
			'value'   => $value,
			'id'      => $auto_id,
			'checked' => (bool) $checked,
		), $attributes );
		$checkbox           = Html::tag( 'input', $checkboxAttributes );

		if ( $withHiddenField === false ) {
			return $checkbox;
		}

		$hiddenAttributes = array(
			'name'  => $name,
			'type'  => 'hidden',
			'value' => 0,
			'id'    => $auto_id . '-hidden',
		);
		$hidden           = Html::tag( 'input', $hiddenAttributes );

		return $withHiddenField === 'array'
			? array( $hidden, $checkbox )
			: $hidden . $checkbox;
	}

	public static function collectionCheckBoxes( $name, array $collection, $checked, array $labelAttributes = array(), $returnAsArray = false ) {
		if ( ! ( is_array( $checked ) || $checked instanceof \Traversable ) ) {
			throw new \InvalidArgumentException( "$name must be an array or Traversable!" );
		}

		$checkBoxes = array();
		foreach ( $collection as $value => $label ) {
			$checkBoxes[] = Html::tag(
				'label',
				$labelAttributes,
				self::checkBox( "{$name}[]", in_array( $value, $checked, true ), $value, array(), false ) . Html::escape( $label ),
				false
			);
		}

		return $returnAsArray ? $checkBoxes : implode( '', $checkBoxes );
	}

	public static function radio( $name, $value, $checked = false, array $attributes = array() ) {
		$attributes = array_merge( array(
			'type'    => 'radio',
			'name'    => $name,
			'value'   => $value,
			'checked' => (bool) $checked,
		), $attributes );

		return Html::tag( 'input', $attributes );
	}

	public static function collectionRadios( $name, array $collection, $checked, array $labelAttributes = array(), $returnAsArray = false ) {
		$radioButtons = array();
		foreach ( $collection as $value => $label ) {
			$radioButtons[] = Html::tag(
				'label',
				$labelAttributes,
				self::radio( $name, $value, $value === $checked ) . Html::escape( $label ),
				false
			);
		}

		return $returnAsArray ? $radioButtons : implode( '', $radioButtons );
	}

	public static function select( $name, array $collection, $selected = null, array $attributes = array() ) {
		$attributes = array_merge( array(
			'name'     => $name,
			'id'       => static::autoId( $name ),
			'multiple' => false,
		), $attributes );

		if ( is_string( $selected ) || is_numeric( $selected ) ) {
			$selected = array( $selected => 1 );
		} else if ( is_array( $selected ) ) {
			$selected = array_flip( $selected );
		} else {
			$selected = array();
		}

		$content = '';
		foreach ( $collection as $value => $element ) {
			if ( is_array( $element ) && $element ) {
				$groupHtml = '';
				foreach ( $element as $groupName => $groupElement ) {
					$groupHtml .= self::option( $groupName, $groupElement, $selected );
				}
				$content .= Html::tag( 'optgroup', array( 'label' => $value ), $groupHtml, false );
			} else {
				$content .= self::option( $value, $element, $selected );
			}
		}

		return Html::tag( 'select', $attributes, $content, false );
	}

	private static function option( $value, $label, $selected ) {
		$label = str_replace( '&amp;nbsp;', '&nbsp;', Html::escape( $label ) );

		return Html::tag(
			'option',
			array(
				'value'    => $value,
				'selected' => isset( $selected[ $value ] ),
			),
			$label,
			false
		);
	}

	public static function file( $name, array $attributes = array() ) {
		$attributes = array_merge( array(
			'type' => 'file',
			'name' => $name,
			'id'   => static::autoId( $name ),
		), $attributes );

		return Html::tag( 'input', $attributes );
	}

	public static function button( $name, $text, array $attributes = array() ) {
		$attributes = array_merge( array(
			'id'   => static::autoId( $name ),
			'name' => $name,
		), $attributes );

		return Html::tag( 'button', $attributes, $text );
	}

	public static function autoId( $name ) {
		if ( strpos( $name, '[]' ) !== false ) {
			return null;
		}

		$name = preg_replace( '/\[([^]]+)\]/u', '-\\1', $name );

		return $name;
	}
}