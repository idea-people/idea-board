<?php
/*
Plugin Name: idea-board
Plugin URI: http://www.ideapeople.co.kr
Description: 아이디어 보드는 워드프레스 다양한 플러그인과 호환되는 한국형 게시판 플러그인 입니다.
Version: 1.0
Author: ideapeople
Author URI: http://www.ideapeople.co.kr
*/

use ideapeople\board\Plugin;
use ideapeople\board\PluginConfig;

require_once dirname( __FILE__ ) . '/vendor/wp-session-manager/wp-session-manager.php';

function run_idea_board() {
	$loader = require_once dirname( __FILE__ ) . '/vendor/autoload.php';
	$loader->add( 'ideapeople\\', dirname( __FILE__ ) . '/src/' );

	require_once dirname( __FILE__ ) . '/idea-board-filter.php';

	PluginConfig::init( __FILE__ );

	$plugin = new Plugin();
	$plugin->run();

	$GLOBALS[ 'idea_board_plugin' ] = $plugin;

	do_action( 'idea_board_init' );

	return $plugin;
}

run_idea_board();

/**
 * @return Plugin
 */
function idea_board_plugin() {
	global $idea_board_plugin;

	return $idea_board_plugin;
}