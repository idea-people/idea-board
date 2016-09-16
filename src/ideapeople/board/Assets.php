<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-29
 * Time: 오전 9:02
 */

namespace ideapeople\board;

class Assets {
	public function public_euqueue_scripts() {
		wp_enqueue_script(
			'nice-file-input'
			, PluginConfig::$plugin_url . 'assets/js/lib/jquery.nicefileinput.min.js'
			, array( 'jquery' )
			, PluginConfig::$plugin_version );

		wp_register_script( PluginConfig::$plugin_name, PluginConfig::$plugin_url . 'assets/js/idea-board.js', array(
			'jquery',
			'nice-file-input'
		), PluginConfig::$plugin_version );

		wp_localize_script( PluginConfig::$plugin_name, PluginConfig::$plugin_name, array() );

		wp_register_script( 'jquery-validate',
			'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.1/jquery.validate.min.js',
			array(
				'jquery',
				'jquery-form'
			) );

		switch ( get_locale() ) {
			case 'ko_KR':
				wp_register_script( 'jquery-validate-ko', PluginConfig::$plugin_url . 'assets/js/lib/jquery-validate/localization/messages_ko.js', array( 'jquery-validate' ) );
				break;
		}

		wp_enqueue_script( 'jquery-validate' );
		wp_enqueue_script( 'jquery-validate-ko' );
		wp_enqueue_script( PluginConfig::$plugin_name );
	}

	public function public_euqueue_styles() {
		wp_enqueue_style( 'font-awesome-1', 'https://use.fontawesome.com/d58ee26700.css', 998 );
		wp_enqueue_style( PluginConfig::$plugin_name, PluginConfig::$plugin_url . 'assets/css/idea-board.css', 999 );
		wp_enqueue_style( PluginConfig::$plugin_name . '_mobile', PluginConfig::$plugin_url . 'assets/css/idea-board-mobile.css', 1000 );
	}

	public function admin_enqueue_scripts() {

	}

	public function admin_enqueue_styles() {

	}
}