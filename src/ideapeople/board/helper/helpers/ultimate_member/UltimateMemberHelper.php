<?php
namespace ideapeople\board\helper\helpers\ultimate_member;

use ideapeople\board\helper\core\AbstractHelper;

/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-18
 * Time: 오후 3:21
 */
class UltimateMemberHelper extends AbstractHelper {
	public function run() {
		add_filter( 'idea_board_get_the_author_profile_url', array( $this, 'author_url' ) );
	}

	public function author_url() {
		$u = um_user_profile_url();

		return $u;
	}

	public function get_name() {
		return "ultimate-member/index.php";
	}
}