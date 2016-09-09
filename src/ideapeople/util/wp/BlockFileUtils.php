<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\util\wp;


use ideapeople\util\http\Request;
use WP_Error;

class BlockFileUtils {
	public $name;

	public $upload_dir;

	public $upload_url;

	/**
	 * FileManager constructor.
	 *
	 * @param $name
	 */
	public function __construct( $name ) {
		$this->name = $name;

		$this->upload_dir = WP_CONTENT_DIR . '/uploads/' . $this->name;

		if ( ! is_dir( $this->upload_dir ) ) {
			mkdir( $this->upload_dir, 0755 );
		}
	}

	public function get_ajax_name() {
		return 'bfu_download_' . $this->name;
	}

	public function add_ajax_action() {
		$ajax_name = $this->get_ajax_name();

		add_action( 'wp_ajax_' . $ajax_name, array( $this, 'ajax_action' ) );
		add_action( 'wp_ajax_nopriv_' . $ajax_name, array( $this, 'ajax_action' ) );
	}

	public function ajax_action() {
		$attach_id = Request::getParameter( 'attach_id' );

		if ( ! $attach_id ) {
			wp_die();
		}

		$result = $this->download( $attach_id );

		wp_send_json( $result );
	}

	public function get_save_path_info( $key = 'path' ) {
		$upload_dir = wp_upload_dir();

		$subdir    = $upload_dir[ 'subdir' ];
		$save_path = $this->upload_dir . $subdir;
		$save_url  = $upload_dir[ 'baseurl' ] . '/' . $this->name . $subdir;

		if ( ! is_dir( $save_path ) ) {
			mkdir( $save_path, 0777, true );
		}

		if ( $key == 'path' ) {
			return $save_path;
		} else if ( $key == 'url' ) {
			return $save_url;
		} else if ( $key == 'subdir' ) {
		}

		return false;
	}

	public function create_block_http() {
		$cont = <<<NOTICE
RewriteEngine On
<Files *>
	Deny from all
</Files>
NOTICE;
		@file_put_contents( $this->upload_dir . '/.htaccess', $cont );

		$cont = <<<NOTICE
<configuration>
	<system.webServer>
		<security>
			<authorization>
				<remove users="*" roles="" verbs="" />
				<add accessType="Allow" roles="Administrators" />
			</authorization>
		</security>
	</system.webServer>
<configuration>
NOTICE;

		@file_put_contents( $this->upload_dir . '/web.config', $cont );
	}

	public function download( $attach_id ) {
		$error = new WP_Error();

		$post = get_post( $attach_id );

		$file     = get_post_meta( $attach_id, 'file_path', true );
		$filename = esc_html( $post->post_title );

		$is_pass_auth_check = apply_filters( 'bfu_auth_check_' . $this->name, true, $post, $file, $filename );

		if ( ! $is_pass_auth_check ) {
			$error->add( 'auth_check_fail', '권한 체크 실패' );

			return $error;
		}

		if ( is_file( $file ) ) {
			$file_size = filesize( $file );
		} else {
			$file_size = 0;
			$error->add( 'not_found_file', '파일을 찾을수 없습니다.' );
		}

		if ( $error->get_error_code() ) {
			return $error;
		}

		if ( function_exists( 'ini_set' ) ) {
			@ini_set( 'display_errors', 0 );
		}

		@session_write_close();

		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}

		if ( function_exists( 'ini_set' ) ) {
			@ini_set( 'zlib.output_compression', 'Off' );
		}

		@set_time_limit( 0 );
		@session_cache_limiter( 'none' );

		nocache_headers();
		header( "X-Robots-Tag: noindex, nofollow", true );
		header( "Robots: none" );
		header( "Content-Type: application/octet-stream" );
		header( "Content-disposition: attachment;filename=\"{$filename}\"" );
		header( "Content-Transfer-Encoding: binary" );
		header( "Content-Length: $file_size" );

		$fp = fopen( $file, "r" );

		if ( ! fpassthru( $fp ) ) {
			fclose( $fp );
		}

		die();
	}

	public function request_upload( $param_name, $convert_attached = false ) {
		$files = Request::getFiles( $param_name );

		$results = array();
		foreach ( $files as $file ) {
			if ( $convert_attached ) {
				$result_file = $this->upload_file( $file );;

				if ( ! $result_file[ 'result' ] ) {
					continue;
				}

				$results[] = $this->insert_attachment( $file[ 'name' ], $result_file[ 'url' ], $result_file[ 'type' ], $result_file[ 'file' ] );
			} else {
				$results[] = $this->upload_file( $file );
			}
		}

		return $results;
	}

	public function insert_attachment( $post_title, $post_url, $mime_type, $file_path ) {
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
		add_post_meta( $attach, 'attach_type', $this->name );

		return $attach;
	}

	public function upload_file( $files ) {
		$save_path = $this->get_save_path_info();

		return FileUtils::upload_file( $save_path, $this->get_save_path_info( 'url' ), $files );
	}
}