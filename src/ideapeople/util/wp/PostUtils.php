<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-25
 * Time: 오후 5:32
 */

namespace ideapeople\util\wp;


use PasswordHash;

class PostUtils {
	static function the_content( $more_link_text = null, $strip_teaser = false ) {
		$content = self::get_the_content( $more_link_text, $strip_teaser );
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );

		echo $content;
	}

	static function get_the_content( $more_link_text = null, $strip_teaser = false ) {
		global $page, $more, $preview, $pages, $multipage;

		$post = get_post();

		if ( null === $more_link_text ) {
			$more_link_text = sprintf(
				'<span aria-label="%1$s">%2$s</span>',
				sprintf(
				/* translators: %s: Name of current post */
					__( 'Continue reading %s' ),
					the_title_attribute( array( 'echo' => false ) )
				),
				__( '(more&hellip;)' )
			);
		}

		$output     = '';
		$has_teaser = false;

		if ( $page > count( $pages ) ) {
			$page = count( $pages );
		}

		$content = $pages[ $page - 1 ];
		if ( preg_match( '/<!--more(.*?)?-->/', $content, $matches ) ) {
			$content = explode( $matches[ 0 ], $content, 2 );
			if ( ! empty( $matches[ 1 ] ) && ! empty( $more_link_text ) ) {
				$more_link_text = strip_tags( wp_kses_no_null( trim( $matches[ 1 ] ) ) );
			}

			$has_teaser = true;
		} else {
			$content = array( $content );
		}

		if ( false !== strpos( $post->post_content, '<!--noteaser-->' ) && ( ! $multipage || $page == 1 ) ) {
			$strip_teaser = true;
		}

		$teaser = $content[ 0 ];

		if ( $more && $strip_teaser && $has_teaser ) {
			$teaser = '';
		}

		$output .= $teaser;

		if ( count( $content ) > 1 ) {
			if ( $more ) {
				$output .= '<span id="more-' . $post->ID . '"></span>' . $content[ 1 ];
			} else {
				if ( ! empty( $more_link_text ) ) /**
				 * Filters the Read More link text.
				 *
				 * @since 2.8.0
				 *
				 * @param string $more_link_element Read More link element.
				 * @param string $more_link_text Read More text.
				 */ {
					$output .= apply_filters( 'the_content_more_link', ' <a href="' . get_permalink() . "#more-{$post->ID}\" class=\"more-link\">$more_link_text</a>", $more_link_text );
				}
				$output = force_balance_tags( $output );
			}
		}

		if ( $preview ) {
			$output = preg_replace_callback( '/\%u([0-9A-F]{4})/', '_convert_urlencoded_to_entities', $output );
		}

		return $output;
	}

	public static function get_post_meta( $post = null, $name, $defaultValue = null ) {
		$post = get_post( $post );

		if ( ! $post || ! $post->ID ) {
			return null;
		}

		$value = get_post_meta( $post->ID, $name, true );

		if ( ! $value && ! is_null( $defaultValue ) ) {
			return $defaultValue;
		}

		return $value;
	}

	public static function insert_or_update_meta( $post_id, $meta_key, $meta_value, $unique = false ) {
		$has_meta = MetaUtils::has_meta( 'post', $meta_key, $post_id );

		if ( $has_meta ) {
			return update_post_meta( $post_id, $meta_key, $meta_value );
		} else {
			return add_post_meta( $post_id, $meta_key, $meta_value, $unique );
		}
	}

	public static function is_author( $post ) {
		$post = get_post( $post );

		if ( is_user_logged_in() ) {
			if ( $post->post_author == wp_get_current_user()->ID ) {
				return true;
			}
		}

		return false;
	}

	public static function get_shortcode_data( $name, $content ) {
		$result = array();

		$regex = get_shortcode_regex( array( $name ) );

		preg_match_all( '/' . $regex . '/', $content, $matches );

		if ( ! empty( $matches[ 2 ] ) ) {
			foreach ( $matches[ 2 ] as $i => $shortcode_name ) {
				$attrs = shortcode_parse_atts( $matches[ 3 ][ $i ] );
				$attrs = ( ! $attrs ) ? array() : $attrs;

				$result[ 'attributes' ] = $attrs;
				$result[ 'content' ]    = $matches[ 5 ][ $i ];
				$result[ 'type' ]       = $shortcode_name;
				$result[ 'original' ]   = $matches[ 0 ][ $i ];
			}
		}

		return $result;
	}
}