<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board\setting;


use ideapeople\board\PluginConfig;
use ideapeople\board\Post;
use ideapeople\board\Skins;
use ideapeople\util\wp\CustomField;
use ideapeople\util\wp\PostUtils;
use ideapeople\util\wp\TermUtils;
use stdClass;
use WP_Term;

class Setting {
	public static function get_meta_keys( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'board_title',
			'board_skin',
			'board_use_secret',
			'board_use_comment',
			'max_upload_cnt',
			'board_post_per_page',
			'board_comment_whitelist',
			'board_comment_moderation',
			'board_categories',
			'board_use_comment_skin',
			'board_editor',
			'board_basic_content',
			'board_only_secret',
			'board_use_page'
		) );

		$args = apply_filters( 'idea_board_setting_meta_keys', $args );

		return $args;
	}

	/**
	 * @param array $args
	 *
	 * @return WP_Term[]
	 */
	public static function get_boards( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'taxonomy'   => PluginConfig::$board_tax,
			'hide_empty' => false,
		) );

		return get_terms( $args );
	}

	public static function get_custom_fields( $board = null, $post = null ) {
		$meta_fields = apply_filters( 'idea_board_custom_fields', array(), $board, $post );

		return $meta_fields;
	}

	public static function get_board( $value = null ) {
		$board_term = false;

		if ( $value instanceof WP_Term ) {
			return $value;
		}

		$board = wp_cache_get( PluginConfig::$board_tax, PluginConfig::$plugin_name );

		if ( empty( $value ) && $board ) {
			return $board;
		} else {
			$post = Post::get_post();

			if ( $post ) {
				$board_term = Setting::get_board_from_post( $post );
			}

			if ( ! $board_term ) {
				$board_term = get_term_by( 'term_taxonomy_id', $value );

				if ( ! $board_term ) {
					$board_term = get_term_by( 'name', $value, PluginConfig::$board_tax );
				}
			}
		}

		if ( ! $board_term ) {
			$board_term = new WP_Term( new stdClass() );
		}

		wp_cache_add( PluginConfig::$board_tax, $board_term, PluginConfig::$plugin_name );

		return $board_term;
	}

	public static function get_board_id( $value = null ) {
		$board_term = self::get_board( $value );

		return $board_term->term_id;
	}

	public static function get_board_in_page( $value = null ) {
		$board_term = self::get_board( $value );

		$page_id = TermUtils::get_term_meta( $board_term->term_id, 'idea_board_page', false );

		return $page_id;
	}

	public static function get_board_from_page( $post = null ) {
		$post = get_post( $post );

		if ( $post ) {
			$term = PostUtils::get_post_meta( $post->ID, 'idea_board_page_term' );

			return get_term_by( 'term_taxonomy_id', $term );
		}

		return false;
	}

	public static function get_board_from_post( $post = null ) {
		$post = get_post( $post );

		if ( $post ) {
			return get_term_by( 'term_taxonomy_id', Post::get_board_term( $post ) );
		}

		return false;
	}

	public static function get_skin_info( $board_term = null, $defaultValue = '' ) {
		$skin_name = self::get_meta( 'board_skin', $board_term, $defaultValue );
		$result    = Skins::get_board_skin_info( 'board', $skin_name );

		return $result;
	}

	public static function is_only_secret( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'board_only_secret', $board_term, $defaultValue );
	}

	public static function get_editor( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'board_editor', $board_term, $defaultValue );
	}

	public static function get_use_pages( $board_term = null, $defaultValue = array() ) {
		return self::get_meta( 'board_use_page', $board_term, $defaultValue );
	}

	public static function get_basic_content( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'board_basic_content', $board_term, $defaultValue );
	}

	public static function get_use_comment_skin( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'board_use_comment_skin', $board_term, $defaultValue );
	}

	public static function get_categories( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'board_categories', $board_term, $defaultValue );
	}

	public static function get_categories_array( $board_term = null, $defaultValue = '' ) {
		$board_term = self::get_board( $board_term );

		$value = self::get_categories( $board_term, $defaultValue );

		if ( $value ) {
			$result = explode( ',', $value );
		} else {
			$result = false;
		}


		return $result;
	}

	public static function get_comment_moderation( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'board_comment_moderation', $board_term, $defaultValue );
	}

	public static function get_comment_whitelist( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'board_comment_whitelist', $board_term, $defaultValue );
	}

	public static function get_role( $board_term = null, $type, $defaultValue = '' ) {
		return self::get_meta( 'role_' . $type, $board_term, $defaultValue );
	}

	public static function get_post_per_page( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'board_post_per_page', $board_term, $defaultValue );
	}

	public static function get_title( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'board_title', $board_term, $defaultValue );
	}

	public static function get_use_secret( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'board_use_secret', $board_term, $defaultValue );
	}

	public static function get_skin( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'board_skin', $board_term, $defaultValue );
	}

	public static function get_board_skin_path( $board_term = null, $defaultValue = '' ) {
		$skin_info = self::get_skin_info( $board_term );

		if ( ! empty( $skin_info ) && isset( $skin_info[ 'path' ] ) ) {
			return $skin_info[ 'path' ];
		}

		return $defaultValue;
	}

	public static function get_use_comment( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'board_use_comment', $board_term, $defaultValue );
	}

	public static function get_max_upload_cnt( $board_term = null, $defaultValue = '' ) {
		return self::get_meta( 'max_upload_cnt', $board_term, $defaultValue );
	}

	public static function get_default_comment_status( $board_term = null ) {
		return self::get_use_comment( $board_term ) ? 'open' : 'closed';
	}

	public static function get_meta( $key, $board_term = null, $defaultValue = '' ) {
		$board_term = self::get_board( $board_term );

		return TermUtils::get_term_meta( $board_term->term_id, $key, $defaultValue );
	}

	/**
	 * @param string $type
	 * @param $custom_fields CustomField[]
	 * @param null $board
	 * @param null $post
	 *
	 * @return mixed|void
	 */
	public static function get_the_custom_field( $custom_fields, $type = 'single', $board = null, $post = null ) {
		ob_start();
		if ( $type == 'single' ) {
			?>
			<ul>
				<?php foreach ( $custom_fields as $custom_field ) { ?>
					<li>
						<span class="idea-custom-meta-title"><?php echo $custom_field->getLabel(); ?></span>
						<span class="idea-custom-meta-value"><?php echo $custom_field->getValue(); ?></span>
					</li>
				<?php } ?>
			</ul>
		<?php } else if ( $type == 'edit' ) { ?>
			<?php foreach ( $custom_fields as $custom_field ) { ?>
				<tr>
					<th><?php echo $custom_field->renderLabel(); ?></th>
					<td><?php echo $custom_field->renderField() ?></td>
				</tr>
			<?php }
		}
		$result = ob_get_contents();
		ob_end_clean();

		return apply_filters( "idea_board_get_the_{$type}_custom_field", $result, $custom_fields, $board, $post );
	}
}