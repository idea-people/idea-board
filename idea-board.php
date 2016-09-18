<?php
/*
Plugin Name: IDEA BOARD
Plugin URI: http://www.ideapeople.co.kr
Description: This plugin helps you to add simply a forum for WordPress
Version: 0.2.2
Author: ideapeople
Author URI: http://www.ideapeople.co.kr
Text Domain: idea-board
*/

use ideapeople\board\Plugin;
use ideapeople\board\PluginConfig;

require_once dirname( __FILE__ ) . '/vendor/wp-session-manager/wp-session-manager.php';

function run_idea_board() {
	$loader = require_once dirname( __FILE__ ) . '/vendor/autoload.php';
	$loader->add( 'ideapeople\\', dirname( __FILE__ ) . '/src/' );

	require_once dirname( __FILE__ ) . '/idea-board-functions.php';

	PluginConfig::init( __FILE__ );

	$plugin = new Plugin();
	$plugin->run();

	do_action( 'idea_board_init' );

	return $plugin;
}

run_idea_board();