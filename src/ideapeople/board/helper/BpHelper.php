<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board\helper;

use ideapeople\board\helper\core\AbstractHelper;

class BpHelper extends AbstractHelper {
	public function run() {
		add_action( 'bp_register_activity_actions', array( $this, 'custom_plugin_register_activity_actions' ) );
		add_action( 'idea_board_action_post_edit_after', array( $this, 'edit_post' ), 10, 4 );
		add_action( 'idea_board_action_comment_edit_after', array( $this, 'edit_comment' ), 10, 4 );
	}

	function custom_plugin_register_activity_actions() {
		$component_id = 'idea-board';

		bp_activity_set_action( $component_id, 'idea_board_insert', esc_html__( 'idea_board_insert' ) );
		bp_activity_set_action( $component_id, 'idea_board_update', esc_html__( 'idea_board_update' ) );
		bp_activity_set_action( $component_id, 'idea_board_comment_insert', esc_html__( 'idea_board_comment_insert' ) );
		bp_activity_set_action( $component_id, 'idea_board_comment_update', esc_html__( 'idea_board_comment_update' ) );
	}

	public function edit_comment( $comment_data, $comment_id, $board, $mode ) {
		$comment = get_comment( $comment_id );
		$post    = get_post( $comment->comment_post_ID );

		if ( is_user_logged_in() ) {
			bp_activity_add( array(
				'component'    => 'idea-board',
				'item_id'      => $comment_id,
				'content'      => $comment->comment_content,
				'primary_link' => get_permalink( $post->ID ) . '#comment-' . $comment_id,
				'type'         => 'idea_board_comment_' . $mode,
				'action'       => sprintf( '<a href="%s"> %s이 IDEA-BOARD %s에서 게시글 %s에 댓글을 편집했습니다.</a>'
					, get_permalink( $post->ID ) . '#comment-' . $comment_id
					, _wp_get_current_user()->display_name
					, $board->name
					, $post->post_title )
			) );
		}
	}

	/**
	 * @param $post_data
	 * @param $post_id
	 * @param $board \WP_Term
	 * @param $mode
	 */
	public function edit_post( $post_data, $post_id, $board, $mode ) {
		$post = get_post( $post_id );

		if ( is_user_logged_in() ) {
			bp_activity_add( array(
				'component'    => 'idea-board',
				'item_id'      => $post_id,
				'content'      => $post->post_content,
				'primary_link' => get_permalink( $post->ID ),
				'type'         => 'idea_board_' . $mode,
				'action'       =>
					sprintf( '<a href="%s"> %s이 IDEA-BOARD %s에서 글을 %s 하였습니다</a>'
						, get_permalink( $post->ID )
						, _wp_get_current_user()->display_name
						, $board->name
						, $mode == 'update' ? '수정' : '생성' )
			) );
		}
	}

	public function get_name() {
		return 'buddypress/bp-loader.php';
	}
}