<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-25
 * Time: 오후 8:28
 */

namespace ideapeople\board;

use ideapeople\board\action\FileAction;
use ideapeople\util\html\HtmlUtils;
use ideapeople\util\wp\PostUtils;
use ideapeople\util\wp\TermUtils;

class Post {
	public static function get_public_meta_keys( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'idea_board_is_secret',
			'idea_board_email',
			'idea_board_category',
			'idea_board_page_id',
			'idea_board_user_name',
			'idea_board_is_notice'
		) );

		return apply_filters( 'idea_board_public_meta_keys', $args );
	}

	public static function get_the_password_form( $post = 0 ) {
		return get_the_password_form( $post );
	}

	public static function update_read_cnt( $post = null ) {
		$post = self::get_post( $post );

		if ( ! $post ) {
			return false;
		}

		if ( $post->post_status == 'trash' ) {
			return false;
		}

		$session = \WP_Session::get_instance();

		$meta_key    = 'read_cnt';
		$session_key = 'read_cnt_' . $post->ID;

		$read_cnt = PostUtils::get_post_meta( $post->ID, $meta_key, 0 );

		if ( ! isset( $session[ 'idea_board_read_keys' ] ) ) {
			$session[ 'idea_board_read_keys' ] = array();
		}

		if ( ! @in_array( $session_key, $session[ 'idea_board_read_keys' ]->toArray() ) ) {
			$session[ 'idea_board_read_keys' ][] = $session_key;

			$read_cnt += 1;

			PostUtils::insert_or_update_meta( $post->ID, $meta_key, $read_cnt );
		}

		return $read_cnt;
	}

	public static function password_required( $post = null ) {
		if ( Capability::is_board_admin() ) {
			return false;
		}

		$post = self::get_post( $post );

		if ( ! $post ) {
			return false;
		}

		if ( is_user_logged_in() ) {
			if ( $post->post_author == wp_get_current_user()->ID ) {
				return false;
			}
		}

		if ( ! $post->post_password ) {
			return false;
		}

		$result = PostUtils::password_check( $post->post_password );

		if ( $result && $post->post_parent != 0 ) {
			$parent = get_post( $post->post_parent );

			return self::password_required( $parent );
		}

		return $result;
	}

	public static function get_post( $post = null ) {
		$post = get_post( $post );

		if ( ! $post ) {
			return false;
		}

		if ( $post->post_type == PluginConfig::$board_post_type ) {
			return $post;
		} else if ( $post->post_type == 'page' ) {
			return new \WP_Post( new \stdClass() );
		}

		return $post;
	}

	public static function get_the_date( $string, $post = null ) {
		$post = self::get_post( $post );

		return get_the_date( $string, $post );
	}

	public static function get_the_read_cnt( $post = null ) {
		$post = self::get_post( $post );

		return PostUtils::get_post_meta( $post->ID, 'read_cnt', 0 );
	}

	public static function the_content( $more_link_text = null, $strip_teaser = false, $echo = true ) {
		$content = self::get_the_content( $more_link_text, $strip_teaser );

//		if ( has_shortcode( $content, 'br' ) ) {
//			$data = PostUtils::get_shortcode_data( 'br', $content );
//			$content = preg_replace( '/\[idea_board.*\]/', '', $content );
//		}

		remove_filter( 'the_content', 'do_shortcode', 11 );
		$content = apply_filters( 'the_content', $content );
		add_filter( 'the_content', 'do_shortcode', 11 );

		$content = str_replace( ']]>', ']]&gt;', $content );

		if ( ! $echo ) {
			return $content;
		}

		echo $content;
	}

	public static function get_the_content( $more_link_text = null, $strip_teaser = false ) {
		return PostUtils::get_the_content( $more_link_text, $strip_teaser );
	}

	public static function get_the_author_nicename( $post = null ) {
		$post = self::get_post( $post );

		$author_id = $post->post_author;

		if ( $author_id ) {
			$name = get_the_author_meta( 'nicename', $author_id );
		} else {
			$name = PostUtils::get_post_meta( get_the_ID(), 'idea_board_user_name' );
		}

		$name = esc_html( $name );

		return $name;
	}

	public static function get_the_title( $post = null ) {
		$post = self::get_post( $post );

		$title     = get_the_title( $post );
		$is_secret = self::is_secret( $post );

		if ( $is_secret ) {
			$title = '<span class="idea-board-is-secret fa fa-lock"></span>' . $title;
		}

		$depth = self::get_depth( $post );

		if ( $depth > 0 ) {
			$title = '<span class="idea-board-depth-icon fa fa-commenting-o"></span>' . $title;

			for ( $i = 0; $i < $depth; $i ++ ) {
				$title = '<span class="idea-board-depth"></span>' . $title;
			}
		}

		$comment_count = self::get_comment_count( $post );

		if ( $comment_count > 0 ) {
			$title .= sprintf( '<span class="idea-board-comment-count">(%d)</span>', $comment_count );
		}

		$attachments = Post::get_attachments( $post );

		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $attachment ) {
				$title .= '<span class="idea-board-file fa ' . $attachment->fa . '"></span>';
			}
		}

		return apply_filters( 'idea_board_get_the_title', $title );
	}

	public static function get_comment_count( $post = null ) {
		$post = self::get_post( $post );

		$number = get_comments_number( $post->ID );

		return $number;
	}

	public static function get_attachments( $post = null ) {
		$post = get_post( $post );

		$result = array();

		$file_action    = new FileAction();
		$attachment_ids = get_post_meta( $post->ID, 'idea_upload_attach' );

		foreach ( $attachment_ids as $attachment_id ) {
			$attachment = get_post( $attachment_id );

			if ( $attachment ) {
				if ( Capability::current_user_can( 'file_down' ) ) {
					$attachment->guid = add_query_arg( array(
						'attach_id' => $attachment_id,
						'action'    => $file_action->board_file_uils->get_ajax_name()
					), admin_url( '/admin-ajax.php' ) );

					$attachment->delete_url = add_query_arg( array(
						'action'     => 'idea_board_delete_attach',
						'attach_id'  => $attachment_id,
						'return_url' => urlencode( add_query_arg( array() ) )
					), admin_url( '/admin-ajax.php' ) );
				} else {
					$attachment->guid = false;
				}

				$attachment->fa = HtmlUtils::font_awesome_file_icon_class( $attachment->post_mime_type );

				$result[] = $attachment;
			}
		}

		return $result;
	}

	public static function get_board_page_id( $post = null ) {
		return self::get_post_meta( $post, 'idea_board_page_id' );
	}

	public static function get_board_page( $post = null ) {
		$post = self::get_post( $post );

		return get_post( self::get_board_page_id( $post->ID ) );
	}

	public static function get_max_upload_cnt( $term_id, $post = null ) {
		$post = self::get_post( $post );

		$attah_files = self::get_attachments( $post );
		$input_count = TermUtils::get_term_meta( $term_id, 'max_upload_cnt' ) - count( $attah_files );

		return $input_count;
	}

	public static function is_secret( $post = null ) {
		return self::get_post_meta( $post, 'idea_board_is_secret', false );
	}

	public static function get_the_permalink( $post = null ) {
		$post = self::get_post( $post );

		return get_permalink( $post->ID );
	}

	public static function get_user_name( $post = null ) {
		return self::get_post_meta( $post, 'idea_board_user_name', false );
	}

	public static function get_user_email( $default = null, $post = null ) {
		return self::get_post_meta( $post, 'idea_board_email', false );
	}

	public static function get_category( $post = null ) {
		$post = self::get_post( $post );

		if ( ! $post->ID ) {
			$cate = get_query_var( 'idea_board_category' );
		} else {
			$cate = PostUtils::get_post_meta( $post->ID, 'idea_board_category', '' );
		}

		return $cate;
	}

	public static function get_depth( $post = null ) {
		return idea_board_plugin()->post_order_generator->get_depth( $post );
	}

	public static function get_board_term( $post = null ) {
		return self::get_post_meta( $post, 'idea_board_term', false );
	}

	public static function is_notice( $post = null ) {
		return self::get_post_meta( $post, 'idea_board_is_notice', false );
	}

	public static function get_post_meta( $post, $key, $defaultValue = null ) {
		$post = self::get_post( $post );

		if ( ! $post || ! $post->ID ) {
			return $defaultValue;
		}

		return PostUtils::get_post_meta( $post->ID, $key, $defaultValue );
	}

	public static function get_start_no( $post = null ) {
		global $wp_query;

		$wp_query->start_no --;

		$post = self::get_post( $post );

		if ( self::is_notice( $post ) ) {
			$text = __( "공지" );

			$result = "<i class='ib2_notice'>{$text}</i>";
		} else {
			$result = $wp_query->start_no + 1;
		}

		return $result;
	}

	public static function the_file_list( $delete = false, $board = null, $post = null ) {
		$attah_files = Post::get_attachments( $post );

		if ( empty( $attah_files ) ) {
			return null;
		}
		ob_start();
		?>
		<ul class="idea-board-file-list">
			<?php
			foreach ( $attah_files as &$attah_file ) {
				?>
				<li>
					<a href="<?php echo $attah_file->guid ? $attah_file->guid : '#'; ?>"
						<?php echo ! $attah_file->guid ? "onClick=\"alert('다운로드 권한이 없습니다.');\"" : ''; ?>>
						<span class="fa <?php echo $attah_file->fa ?>"></span>
						<?php echo $attah_file->post_title ?>
					</a>
					<?php if ( $delete ) { ?>
						<a href="<?php echo $attah_file->delete_url ?>">삭제</a>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>
		<?php
		$result = ob_get_contents();
		ob_end_clean();

		return apply_filters( 'idea_board_the_file_list', $result, $attah_files, $board, $post );
	}
}