<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

use ideapeople\board\Capability;
use ideapeople\board\CommonUtils;
use ideapeople\board\Editor;
use ideapeople\board\PluginConfig;
use ideapeople\board\setting\Setting;
use ideapeople\board\Skins;
use ideapeople\util\wp\MetaUtils;

$board = Setting::get_board();

$board_skins = Skins::get_board_skins();
?>
<link rel="stylesheet" href="<?php echo PluginConfig::$plugin_url ?>/assets/css/idea-board-admin-setting.css">
<table class="form-table idea-board-admin-table">
	<tbody>
	<?php if ( $board->name ) : ?>
		<tr>
			<th>
				<strong><?php _e_idea_board( 'ShortCode' ); ?></strong>
			</th>
			<td><em>[idea_board name="<?php echo $board->name ?>"]</em></td>
		</tr>
	<?php endif; ?>
	<tr>
		<th><?php _e_idea_board( 'Forum Setting' ) ?></th>
		<td>
			<p>
				<label for="board_categories" class="block">
					<?php _e_idea_board( 'Category' ); ?>
					<input type="text" name="board_categories" id="board_categories" class="block"
					       value="<?php echo Setting::get_categories( $board->term_id, '' ); ?>"/>
					<em><?php _e_idea_board( 'Please enter separated by ,' ) ?></em>
				</label>
			</p>

			<p>
				<label for="board_editor" class="block">
					<?php _e_idea_board( 'Editor' ); ?>
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
					<?php _e_idea_board( 'Automatically added to the page' ); ?>
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
		<th><?php _e_idea_board( 'Theme' ) ?></th>
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
				<label for="board_skin"><?php _e_idea_board( 'We will use skins Board' ); ?></label>
			</p>
			<p>
				<label for="board_use_comment_skin">
					<input type="checkbox" name="board_use_comment_skin" id="board_use_comment_skin" value="1"
						<?php echo Setting::get_use_comment_skin( $board->term_id, false ) == 1 ? 'checked' : '' ?> />
					<?php _e_idea_board( 'Whether to use bulletin board comments skins' ); ?>
					<em><?php _e_idea_board( 'If you use other WordPress plugin or theme Comments Comments Form Please check off' ); ?>
						.</em>
				</label>
			</p>
		</td>
	</tr>

	<tr>
		<th><?php _e_idea_board( 'Posts' ) ?></th>
		<td>
			<p class="form-required">
				<label for="board_post_per_page">
					<input type="number" name="board_post_per_page" id="board_post_per_page" class="small-text"
					       value="<?php echo Setting::get_post_per_page( $board->term_id, 10 ); ?>"
					       required="required"/>
					<?php _e_idea_board( 'Display the list' ); ?>
				</label>
			</p>

			<p>
				<label for="max_upload_cnt">
					<input type="number" name="max_upload_cnt" id="max_upload_cnt" class="small-text"
					       value="<?php echo Setting::get_max_upload_cnt( $board->term_id, 3 ); ?>"
					       required="required"/>
					<?php _e_idea_board( 'You can upload files' ); ?>
				</label>
			</p>

			<p><input type="checkbox" name="board_use_secret" id="board_use_secret" value="1"
					<?php echo Setting::get_use_secret( $board->term_id, false ) == 1 ? 'checked' : '' ?> />
				<label for="board_use_secret"><?php _e_idea_board( 'I will use the Secret Writing' ); ?></label>
			</p>

			<p><input type="checkbox" name="board_only_secret" id="board_only_secret" value="1"
					<?php echo Setting::is_only_secret( $board->term_id, false ) == 1 ? 'checked' : '' ?> />
				<label for="board_only_secret"><?php _e_idea_board( 'Only secret write mode' ); ?></label>
			</p>
		</td>
	</tr>
	<tr>
		<th><?php _e_idea_board( 'discussion' ) ?></th>
		<td>
			<p>
				<input type="checkbox" name="board_use_comment" id="board_use_comment" value="1"
					<?php echo Setting::get_use_comment( $board->term_id, true ) == 1 ? 'checked' : '' ?> />
				<label for="board_use_comment"><?php _e_idea_board( 'It allows you to write a comment' ); ?></label>
			</p>

			<p>
				<input type="checkbox" name="board_comment_moderation" id="board_comment_moderation" value="1"
					<?php echo Setting::get_comment_moderation( $board->term_id, false ) == 1 ? 'checked' : '' ?> />
				<label
					for="board_comment_moderation"><?php _e_idea_board( 'Comments should be approved must be manually' ); ?></label>
			</p>

			<p>
				<input type="checkbox" name="board_comment_whitelist" id="board_comment_whitelist" value="1"
					<?php echo Setting::get_comment_whitelist( $board->term_id, false ) == 1 ? 'checked' : '' ?> />
				<label
					for="board_comment_whitelist"><?php _e_idea_board( 'This person must have been written comments on the comments previously approved' ); ?></label>
			</p>
		</td>
	</tr>
	<tr>
		<th><?php _e_idea_board( 'Notification' ) ?></th>
		<td>
			<p>
				<input type="checkbox" name="board_noti_post_admin"
				       id="board_noti_post_admin" value="1"
					<?php echo Setting::get_noti_post_admin( $board->term_id, true ) ? 'checked' : '' ?> />
				<label
					for="board_noti_post_admin">
					<?php _e_idea_board( 'Notification to the administrator when a new article has been registered' ); ?>
				</label>
			</p>

			<p>
				<input type="checkbox" name="board_noti_post_comment_author"
				       id="board_noti_post_comment_author" value="1"
					<?php echo Setting::get_noti_post_comment_author( $board->term_id, true ) ? 'checked' : '' ?> />
				<label
					for="board_noti_post_comment_author">
					<?php _e_idea_board( 'Notify authors when writing comments have been registered' ); ?>
				</label>
			</p>

			<p>
				<input type="checkbox" name="board_noti_post_reply_author"
				       id="board_noti_post_reply_author" value="1"
					<?php echo Setting::get_noti_post_reply_author( $board->term_id, true ) ? 'checked' : '' ?> />
				<label
					for="board_noti_post_reply_author">
					<?php _e_idea_board( 'Notify authors when writing this reply was registered' ); ?>
				</label>
			</p>

			<p>
				<input type="checkbox" name="board_noti_comment_comment_author"
				       id="board_noti_comment_comment_author" value="1"
					<?php echo Setting::get_noti_comment_comment_author( $board->term_id, true ) ? 'checked' : '' ?> />
				<label
					for="board_noti_comment_comment_author">
					<?php _e_idea_board( 'Notification to the author of the comment in the comments when comments have been registered' ); ?>
				</label>
			</p>

		</td>
	</tr>
	<tr>
		<th><?php _e_idea_board( 'Authority' ) ?></th>
		<td class="role_row">
			<p>
				<label for="roles[list][]"><?php _e_idea_board( 'List' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[list][]', Setting::get_role( $board->term_id, 'list', Capability::get_default_cap( 'list' ) ) ); ?>
			</p>

			<p>
				<label for="roles[read][]"><?php _e_idea_board( 'Read' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[read][]', Setting::get_role( $board->term_id, 'read', Capability::get_default_cap( 'read' ) ) ); ?>
			</p>

			<p>
				<label for="roles[write][]"><?php _e_idea_board( 'Write' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[edit][]', Setting::get_role( $board->term_id, 'edit', Capability::get_default_cap( 'edit' ) ) ); ?>
			</p>

			<p>
				<label for="roles[reply][]"><?php _e_idea_board( 'Reply' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[reply][]', Setting::get_role( $board->term_id, 'reply', Capability::get_default_cap( 'reply' ) ) ); ?>
			</p>

			<p>
				<label for="roles[delete][]"><?php _e_idea_board( 'Delete' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[delete][]', Setting::get_role( $board->term_id, 'delete', Capability::get_default_cap( 'delete' ) ) ); ?>
			</p>

			<p>
				<label for="roles[file_down][]"><?php _e_idea_board( 'FileDown' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[file_down][]', Setting::get_role( $board->term_id, 'file_down', Capability::get_default_cap( 'file_down' ) ) ); ?>
			</p>

			<p>
				<label for="roles[notice_edit][]"><?php _e_idea_board( 'Write Notice' ); ?></label>
				<?php echo CommonUtils::role_as_select_box( 'roles[notice_edit][]', Setting::get_role( $board->term_id, 'notice_edit', Capability::get_default_cap( 'notice_edit' ) ) ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th><?php _e_idea_board( 'Post Base Content' ) ?></th>
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