<?php
/*
Plugin Name: idea-board
Plugin URI: http://www.ideapeople.co.kr
Description:
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