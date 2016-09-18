<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board;

class Skins {
	public static function get_board_skins() {
		$skins[ 'board' ][] = array(
			'name' => 'basic',
			'path' => PluginConfig::$plugin_path . 'views/skin/board/basic',
			'url'  => PluginConfig::$plugin_url . 'views/skin/board/basic'
		);

		return apply_filters( 'idea_board_skins', $skins );
	}

	public static function get_board_skin_info( $type, $name ) {
		$skins = self::get_board_skins();

		foreach ( $skins[ $type ] as $skin ) {
			if ( $skin[ 'name' ] == $name ) {
				return $skin;
			}
		}

		return false;
	}
}