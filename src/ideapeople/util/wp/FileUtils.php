<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-05
 * Time: 오후 12:35
 */

namespace ideapeople\util\wp;


class FileUtils {
	public static function insert_attachment( $post_title, $post_url, $mime_type, $file_path, $attach_type ) {
		$object = array(
			'post_title'     => $post_title,
			'post_content'   => $post_url,
			'post_mime_type' => $mime_type,
			'guid'           => $post_url
		);

		$attach = wp_insert_attachment( $object );

		if ( file_is_valid_image( $file_path ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			wp_update_attachment_metadata( $attach, wp_generate_attachment_metadata( $attach, $file_path ) );
		}

		add_post_meta( $attach, 'file_path', str_replace( '\\', '/', $file_path ) );
		add_post_meta( $attach, 'attach_type', $attach_type );

		return $attach;
	}

	public static function upload_file_real( $save_path, $save_url, $files ) {
		if ( $files && is_array( $files ) && $files[ 'name' ] ) {
			$upload    = wp_handle_upload( $files, array( 'test_form' => false ) );
			$file_name = basename( $upload[ 'file' ] );

			$result = array(
				'file'   => $upload[ 'file' ],
				'url'    => $upload[ 'url' ],
				'name'   => $file_name,
				'result' => ! empty( $upload[ 'file' ] ),
				'type'   => $upload[ 'type' ]
			);

			return $result;
		}

		return false;
	}

	public static function upload_file( $save_path, $save_url, $files ) {
		if ( $files && is_array( $files ) && $files[ 'name' ] ) {
			$upload    = wp_handle_upload( $files, array( 'test_form' => false ) );
			$file_name = basename( $upload[ 'file' ] );
			$uname     = wp_unique_filename( $save_path, $file_name );
			$mv_upload = $save_path . '/' . $uname;

			$move = @rename( $upload[ 'file' ], $mv_upload );

			$result = array(
				'file'   => $mv_upload,
				'url'    => $save_url . '/' . $file_name,
				'name'   => $file_name,
				'result' => $move,
				'type'   => $upload[ 'type' ]
			);

			return $result;
		}

		return false;
	}
}