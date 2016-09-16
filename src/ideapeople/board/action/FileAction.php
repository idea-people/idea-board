<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-27
 * Time: 오후 4:10
 */

namespace ideapeople\board\action;

use ideapeople\board\setting\GlobalSetting;
use ideapeople\board\setting\Setting;
use ideapeople\board\Capability;
use ideapeople\board\PluginConfig;
use ideapeople\util\common\Utils;
use ideapeople\util\http\Request;
use ideapeople\util\wp\BlockFileUtils;

class FileAction {
	/**
	 * @var BlockFileUtils
	 */
	public $board_file_uils;

	public function __construct() {
		add_filter( 'bfu_auth_check_idea_board_files', array( $this, 'bfu_auth_check_idea_board_files' ), 10, 2 );

		$this->board_file_uils = new BlockFileUtils( 'idea_board_files' );
	}

	public function add_ajax_action() {
		$this->board_file_uils->add_ajax_action();
	}

	public function bfu_auth_check_idea_board_files( $check, $post ) {
		$post_id    = get_post_meta( $post->ID, 'idea_upload_post', true );
		$board_term = Setting::get_board_from_post( $post_id );
		$c          = Capability::current_user_can( 'file_down', $board_term );

		return $c;
	}

	public function handle_upload( $post_data = null, $post_id = null, $board ) {
		$post = get_post( $post_id );

		$idea_upload_attach = get_post_meta( $post->ID, 'idea_upload_attach' );

		$max_upload_cnt = Setting::get_max_upload_cnt( $board->term_id );
		$count          = count( $idea_upload_attach ) + count( Request::getFiles( 'files', false, false ) );

		if ( $max_upload_cnt ) {
			if ( $count > $max_upload_cnt ) {
				wp_die( '업로드 한도초과' );
			}
		}

		$files = Request::getFiles( 'files' );

		foreach ( $files as $file ) {
			$max_byte = GlobalSetting::get_max_update_file_size_byte();

			if ( $file[ 'size' ] > $max_byte ) {
				wp_die( __idea_board( 'Exceeding file size limit' ) );
			}
		}

		$attach_ids = $this->board_file_uils->request_upload( 'files', true );

		foreach ( $attach_ids as $attach_id ) {
			add_post_meta( $post->ID, 'idea_upload_attach', $attach_id );
			add_post_meta( $attach_id, 'idea_upload_post', $post->ID );
		}
	}

	/**
	 * @param $attach_id
	 */
	public function delete_attach( $attach_id ) {
		$post = get_post( get_post_meta( $attach_id, 'idea_upload_post', true ) );
		$file = get_post_meta( $attach_id, 'file_path', true );

		delete_post_meta( $post->ID, 'idea_upload_attach', $attach_id );
		wp_delete_post( $attach_id, true );

		unlink( $file );
	}

	public function delete_attach_redirect( $attach_id = null ) {
		if ( empty( $attach_id ) ) {
			$attach_id = Request::getParameter( 'attach_id' );
		}

		$this->delete_attach( $attach_id );

		$return_url = Request::getParameter( 'return_url' );

		wp_redirect( $return_url );
		die;
	}
}