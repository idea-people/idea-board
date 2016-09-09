<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-24
 * Time: 오후 11:43
 */

namespace ideapeople\board\action;


use ideapeople\board\setting\Setting;
use ideapeople\board\Capability;
use ideapeople\board\Post;
use ideapeople\board\Rewrite;
use ideapeople\board\PostView;
use ideapeople\board\PluginConfig;
use ideapeople\board\view\ErrorView;
use ideapeople\util\http\Request;
use ideapeople\util\view\View;
use ideapeople\util\wp\MetaUtils;
use ideapeople\util\wp\PostUtils;

/**
 * Class BoardPostAction
 * @package ideapeople\board\action
 */
class PostAction {
	public $post_data;

	public function __construct( $post_data = null ) {
		if ( empty( $post_data ) ) {
			$this->post_data = wp_parse_args( $post_data, $_REQUEST );
		}
	}

	public function idea_board_edit_post( $post_data = null ) {
		if ( empty( $post_data ) ) {
			$post_data = $this->post_data;
		}

		$post_data = wp_parse_args( $post_data, array(
			'post_type'    => PluginConfig::$board_post_type,
			'post_status'  => 'publish',
			'post_content' => Request::getParameter( 'idea_board_post_content' ),
			'post_parent'  => Request::getParameter( 'parent', 0 ),
			'post_author'  => ! is_user_logged_in() ? - 1 : null
		) );

		if ( ! Capability::is_board_admin() ) {
			$post_data[ 'post_content' ] = strip_shortcodes( $post_data[ 'post_content' ] );
		}

		$error      = new \WP_Error();
		$post_id    = ! empty( $post_data[ 'ID' ] ) ? $post_data[ 'ID' ] : $post_data[ 'pid' ];
		$return_url = Request::getParameter( 'return_url', null );
		$nonce      = $post_data[ PluginConfig::$idea_board_edit_nonce_name ];

		if ( ! $this->is_valid_nonce( $nonce ) ) {
			wp_die();
		}

		if ( isset( $post_data[ 'mode' ] ) ) {
			$mode = $post_data[ 'mode' ];
		} else {
			if ( empty( $post_id ) || $post_id == - 1 ) {
				$mode = 'insert';
			} else {
				$mode = 'update';
			}
		}

		$board_term = $this->get_board_term( $post_data );

		$board = Setting::get_board( $board_term->term_id );

		if ( ! $board ) {
			wp_die( 'BOARD REQUIRED' );
		}

		$view = apply_filters( 'pre_cap_check_edit_view', null, get_post( $post_id ) );

		PostView::handle_view( $view );

		switch ( $mode ) {
			case 'insert':
				unset( $post_data[ 'ID' ] );

				if ( empty( $post_data[ 'comment_status' ] ) ) {
					$post_data[ 'comment_status' ] = Setting::get_default_comment_status( $board->term_id );
				}

				$post_id = wp_insert_post( $post_data, $error );

				break;
			case 'update':
				if ( Capability::is_board_admin() ) {
					unset( $post_data[ 'post_author' ] );
				}

				$post_id = wp_update_post( $post_data, $error );

				break;
			case 'delete':
				$post_id = wp_trash_post( $post_id );
				break;
			default :
				break;
		}

		if ( $return_url ) {
			if ( $mode == 'delete' ) {
				$return_url = add_query_arg( array( 'pid' => false, 'page_mode' => 'list' ), $return_url );
			} else {
				$return_url = Rewrite::post_type_link( '', $post_id, $return_url );
			}
		}

		do_action( 'idea_board_edited_post', $post_id, $post_data, $return_url );

		if ( $return_url ) {
			wp_redirect( $return_url );
		}

		die;
	}

	public function post_update_private_meta( $board_term, $post_id ) {
		if ( $post_id ) {
			PostUtils::insert_or_update_meta( $post_id, 'idea_board_remote_ip', $_SERVER[ 'REMOTE_ADDR' ] );
			PostUtils::insert_or_update_meta( $post_id, 'idea_board_term', $board_term );
		}
	}

	public function post_update_tax( $post, $tax_input = array() ) {
		$post = get_post( $post );

		if ( ! $post ) {
			return false;
		}

		if ( ! is_array( $tax_input ) ) {
			return false;
		}

		foreach ( $tax_input as $taxonomy => $tags ) {
			$taxonomy_obj = get_taxonomy( $taxonomy );

			if ( ! $taxonomy_obj ) {
				continue;
			}

			if ( is_array( $tags ) ) {
				$tags = array_filter( $tags );
			}

			wp_set_post_terms( $post->ID, $tags, PluginConfig::$board_tax );
		}

		return true;
	}

	public function post_update_public_meta( $post, $meta_values = array() ) {
		$post = get_post( $post );

		if ( ! $post ) {
			return false;
		}

		$idea_board_public_meta_keys = Post::get_public_meta_keys();

		foreach ( $idea_board_public_meta_keys as $key ) {
			if ( is_array( $meta_values ) ) {
				$meta_keys = array_keys( $meta_values ? $meta_values : array() );

				if ( in_array( $key, $meta_keys ) ) {
					$new_meta_value = $meta_values[ $key ];

					PostUtils::insert_or_update_meta( $post->ID, $key, $new_meta_value );
				} else {
					if ( ! MetaUtils::has_meta( 'post', $key, $post->ID ) ) {
						add_post_meta( $post->ID, $key, false );
					} else {
						update_post_meta( $post->ID, $key, false );
					}
				}
			} else {
				if ( ! MetaUtils::has_meta( 'post', $key, $post->ID ) ) {
					add_post_meta( $post->ID, $key, false );
				}
			}
		}
		
		if ( Setting::is_only_secret() ) {
			PostUtils::insert_or_update_meta( $post->ID, 'idea_board_is_secret', true );
		}

		return true;
	}

	public function get_board_term( $post_data = array() ) {
		$board_term = false;

		if ( is_array( $post_data[ 'tax_input' ] ) && $post_data[ 'tax_input' ][ PluginConfig::$board_tax ][ 0 ] ) {
			$board_term = get_term_by( 'term_taxonomy_id', $post_data[ 'tax_input' ][ PluginConfig::$board_tax ][ 0 ], PluginConfig::$board_tax );
		} else {
			if ( isset( $post_data[ 'pid' ] ) ) {
				$board_term = Setting::get_board_from_post( $post_data[ 'pid' ] );
			} else if ( isset( $post_data[ 'ID' ] ) ) {
				$board_term = Setting::get_board_from_post( $post_data[ 'ID' ] );
			}
		}

		return $board_term;
	}

	public function update_idea_post( $post_id ) {
		$post = get_post( $post_id );

		if ( $post->post_type != PluginConfig::$board_post_type ) {
			return false;
		}

		$this->post_update_tax( $post_id, @$this->post_data[ 'tax_input' ] );

		$this->post_update_public_meta( $post_id, @$this->post_data[ 'meta_input' ] );

		if ( is_array( $this->post_data ) && isset( $this->post_data[ 'tax_input' ] ) ) {
			$tax = $this->post_data[ 'tax_input' ][ PluginConfig::$board_tax ];

			if ( count( $tax ) == 2 ) {
				$this->post_update_private_meta( $tax[ 1 ], $post_id );
			}
		}

		return $post_id;
	}

	public function is_valid_nonce( $nonce ) {
		$action = PluginConfig::$idea_board_edit_nonce_action;

		return $nonce && wp_verify_nonce( $nonce, $action );
	}
}