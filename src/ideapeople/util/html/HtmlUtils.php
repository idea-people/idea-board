<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-04
 * Time: 오후 2:39
 */

namespace ideapeople\util\html;


class HtmlUtils {
	static function font_awesome_file_icon_class( $mime_type ) {
		static $font_awesome_file_icon_classes = array(
			'image'                                                                   => 'fa-file-image-o',
			'audio'                                                                   => 'fa-file-audio-o',
			'video'                                                                   => 'fa-file-video-o',
			'application/pdf'                                                         => 'fa-file-pdf-o',
			'text/plain'                                                              => 'fa-file-text-o',
			'text/html'                                                               => 'fa-file-code-o',
			'application/json'                                                        => 'fa-file-code-o',
			'application/gzip'                                                        => 'fa-file-archive-o',
			'application/zip'                                                         => 'fa-file-archive-o',
			'application/octet-stream'                                                => 'fa-file-o',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'       => 'fa-file-excel-o',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word-o'
		);

		if ( isset( $font_awesome_file_icon_classes[ $mime_type ] ) ) {
			return $font_awesome_file_icon_classes[ $mime_type ];
		}

		$mime_parts = explode( '/', $mime_type, 2 );

		$mime_group = $mime_parts[ 0 ];

		if ( isset( $font_awesome_file_icon_classes[ $mime_group ] ) ) {
			return $font_awesome_file_icon_classes[ $mime_group ];
		}

		return "fa-file-o";
	}
}