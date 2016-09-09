<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */
use ideapeople\board\setting\Setting;
use ideapeople\board\Editor;
use ideapeople\board\Skins;
use ideapeople\board\CommonUtils;
use ideapeople\board\Capability;
use ideapeople\util\wp\TermUtils;

$board       = Setting::get_board();
$board_skins = Skins::get_board_skins();
?>
<style>
	.form-wrap .idea-board-admin-table label {
		display: inline-block;
		font-style: normal;
	}

	.form-wrap .idea-board-admin-table th label {
		font-weight: bold;
	}

	.term-description-wrap, .term-slug-wrap, .term-parent-wrap {
		display: none;
	}

	.form-table th {
		width: 100px;
	}

	.form-table td p:first-child {
		margin-top: 0;
	}

	.form-table td p em {
		display: block;
	}

	.form-table .role_row {
		visibility: hidden;
	}

	.form-table .role_row p {
		clear: both;
		float: left;
		width: 100%;
	}

	.form-table .role_row label {
		width: 90px;
		display: inline-block;
	}

	.form-table .block {
		width: 70%;
	}

	#col-left {
		width: 65%;
	}

	#col-right {
		width: 35%;
	}

	#tag-name {
		width: 250px;
	}
</style>
<table class="form-table idea-board-admin-table" style="margin-bottom: 10px;">
	<tbody>

	<tr>
		<th>게시판 설정</th>
		<td>
			<p>
				<label for="board_categories" class="block">
					<?php _e( '카테고리' ); ?>
					<input type="text" name="board_categories" id="board_categories" class="block"
					       value="<?php echo Setting::get_categories( $board->term_id, '' ); ?>"/>
					<em>,로 구분하여 입력해 주세요.</em>
				</label>
			</p>

			<p>
				<label for="board_editor" class="block">
					<?php _e( '에디터' ); ?>
					<select name="board_editor" id="board_editor">
						<?php foreach ( Editor::get_editors() as $key => $editor ) { ?>
							<option value="<?php echo $key ?>"
								<?php echo $key == Setting::get_editor( $board->term_id ) ? 'selected' : '' ?>><?php echo $editor[ 'name' ] ?></option>
						<?php } ?>
					</select>
				</label>
			</p>
			<p>
				<label for="board_use_page" class="block">
					<?php _e( '페이지에 자동 추가' ); ?>
					<select name="board_use_page[]" id="board_use_page" class="chosen-select" multiple>
						<?php
						$pages = get_pages();
						foreach ( $pages as $page ) { ?>
							<option value="<?php echo $page->ID ?>"
								<?php echo in_array( $page->ID, Setting::get_use_pages() ) ? 'selected' : '' ?>>
								<?php echo $page->post_title ?>
							</option>
						<?php } ?>
					</select>
				</label>
			</p>
		</td>
	</tr>
	<tr>
		<th>스킨</th>
		<td>
			<p>
				<select name="board_skin" id="board_skin">
					<?php foreach ( $board_skins[ 'board' ] as $skin ) : ?>
						<option
							value="<?php echo $skin[ 'name' ]; ?>"
							<?php echo $skin[ 'name' ] == Setting::get_skin( $board->term_id ) ? 'selected' : '' ?>>
							<?php echo $skin[ 'name' ]; ?>
						</option>
					<?php endforeach; ?>
				</select>
				<label for="board_skin"><?php _e( '게시판 스킨을 사용하겠습니다.' ); ?></label>
			</p>
			<p>
				<label for="board_use_comment_skin">
					<input type="checkbox" name="board_use_comment_skin" id="board_use_comment_skin" value="1"
						<?php echo Setting::get_use_comment_skin( $board->term_id, false ) == 1 ? 'checked' : '' ?> />아이디어보드
					게시판 댓글 스킨 사용여부
					<em>기타 워드프레스 댓글 플러그인이나 테마 댓글폼을 이용할경우 체크 해제해주세요.</em>
				</label>
			</p>
		</td>
	</tr>

	<tr>
		<th>게시글</th>
		<td>
			<p class="form-required">
				<label for="board_post_per_page">
					<input type="number" name="board_post_per_page" id="board_post_per_page" class="small-text"
					       value="<?php echo Setting::get_post_per_page( $board->term_id, 10 ); ?>"
					       required="required"/>
					<?php _e( '개씩 목록에 표시합니다.' ); ?>
				</label>
			</p>

			<p>
				<label for="max_upload_cnt">
					<input type="number" name="max_upload_cnt" id="max_upload_cnt" class="small-text"
					       value="<?php echo Setting::get_max_upload_cnt( $board->term_id, 3 ); ?>"
					       required="required"/>
					<?php _e( '개의 파일을 업로드할수 있습니다.' ); ?>
				</label>
			</p>

			<p><input type="checkbox" name="board_use_secret" id="board_use_secret" value="1"
					<?php echo Setting::get_use_secret( $board->term_id, false ) == 1 ? 'checked' : '' ?> />
				<label for="board_use_secret"><?php _e( '비밀글 쓰기를 사용하겠습니다.' ); ?></label>
			</p>

			<p><input type="checkbox" name="board_only_secret" id="board_only_secret" value="1"
					<?php echo Setting::is_only_secret( $board->term_id, false ) == 1 ? 'checked' : '' ?> />
				<label for="board_only_secret"><?php _e( '무조건 비밀글 쓰기로 사용하겠습니다.' ); ?></label>
			</p>
		</td>
	</tr>
	<tr>
		<th>토론</th>
		<td>
			<p>
				<input type="checkbox" name="board_use_comment" id="board_use_comment" value="1"
					<?php echo Setting::get_use_comment( $board->term_id, false ) == 1 ? 'checked' : '' ?> />
				<label for="board_use_comment"><?php _e( '댓글을 쓸 수 있게 합니다.' ); ?></label>
			</p>

			<p>
				<input type="checkbox" name="board_comment_moderation" id="board_comment_moderation" value="1"
					<?php echo Setting::get_comment_moderation( $board->term_id, false ) == 1 ? 'checked' : '' ?> />
				<label for="board_comment_moderation"><?php _e( '댓글은 수동으로 승인돼야 합니다.' ); ?></label>
			</p>

			<p>
				<input type="checkbox" name="board_comment_whitelist" id="board_comment_whitelist" value="1"
					<?php echo Setting::get_comment_whitelist( $board->term_id, false ) == 1 ? 'checked' : '' ?> />
				<label for="board_comment_whitelist"><?php _e( '댓글을 쓴 사람이 예전에 댓글이 승인된 적이 있어야 합니다' ); ?></label>
			</p>
		</td>
	</tr>

	<tr>
		<th>권한</th>
		<td class="role_row">
			<p>
				<label for="roles[list][]"><?php _e( '목록보기' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[list][]', Setting::get_role( $board->term_id, 'list', Capability::get_default_cap( 'list' ) ) ); ?>
			</p>

			<p>
				<label for="roles[read][]"><?php _e( '읽기' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[read][]', Setting::get_role( $board->term_id, 'read', Capability::get_default_cap( 'read' ) ) ); ?>
			</p>

			<p>
				<label for="roles[write][]"><?php _e( '쓰기' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[edit][]', Setting::get_role( $board->term_id, 'edit', Capability::get_default_cap( 'edit' ) ) ); ?>
			</p>

			<p>
				<label for="roles[delete][]"><?php _e( '삭제' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[delete][]', Setting::get_role( $board->term_id, 'delete', Capability::get_default_cap( 'delete' ) ) ); ?>
			</p>

			<p>
				<label for="roles[file_down][]"><?php _e( '파일다운' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[file_down][]', Setting::get_role( $board->term_id, 'file_down', Capability::get_default_cap( 'file_down' ) ) ); ?>
			</p>

			<p>
				<label for="roles[notice_edit][]"><?php _e( '공지글쓰기' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[notice_edit][]', Setting::get_role( $board->term_id, 'notice_edit', Capability::get_default_cap( 'notice_edit' ) ) ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th>기본글</th>
		<td>
			<?php wp_editor( Setting::get_basic_content(), 'board_basic_content' ); ?>
		</td>
	</tr>
	</tbody>
</table>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.jquery.min.js"></script>
<script>
	(function ($) {
		$(document).ready(function () {
			$('.chosen-select').chosen({width: "80%"});
			$('.form-table .role_row').css('visibility', 'visible');
		});
	})(jQuery);
</script>