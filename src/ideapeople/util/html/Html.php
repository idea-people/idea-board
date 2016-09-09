<?php
namespace ideapeople\util\html;
class Html {
	public static function tag( $tagName, array $attributes = array(), $content = null, $escape_content = true ) {
		$result = '<' . $tagName . static::attributes( $attributes ) . '>';

		if ( $content !== null ) {
			$result .= ( $escape_content ? static::escape( $content ) : $content ) . '</' . $tagName . '>';
		}

		return $result;
	}

	public static function attributes( array $attributes ) {
		$result = '';

		foreach ( $attributes as $attribute => $value ) {
			if ( $value === false || $value === null ) {
				continue;
			}
			if ( $value === true ) {
				$result .= ' ' . $attribute;
			} else if ( is_numeric( $attribute ) ) {
				$result .= ' ' . $value;
			} else {
				if ( is_array( $value ) ) { // support cases like 'class' => array('one', 'two')
					$value = implode( ' ', $value );
				}
				$result .= ' ' . $attribute . '="' . static::escape( $value ) . '"';
			}
		}

		return $result;
	}

	public static function escape( $string = '' ) {
		return htmlspecialchars( $string, ENT_QUOTES, 'UTF-8' );
	}
}