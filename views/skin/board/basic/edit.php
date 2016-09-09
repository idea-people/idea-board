<?php
use ideapeople\board\Button;
use ideapeople\board\setting\Setting;
use ideapeople\board\Capability;
use ideapeople\board\Editor;
use ideapeople\board\Post;
use ideapeople\board\Rewrite;
use ideapeople\board\PluginConfig;
use ideapeople\board\CommonUtils;
use ideapeople\util\http\Request;

$board      = Setting::get_board();
$categories = Setting::get_categories_array();
$use_secret = Setting::get_use_secret();

$post          = Post::get_post();
$ID            = Post::get_post()->ID;
$post_title    = Post::get_post()->post_title;
$content       = Post::get_post()->post_content ? Post::get_post()->post_content : Setting::get_basic_content();
$post_password = Post::get_post()->post_password;
$cate          = Post::get_category();
$user_name     = Post::get_user_name();
$user_email    = Post::get_user_email();
$attachments   = Post::get_attachments();
$input_count   = Setting::get_max_upload_cnt() - count( $attachments );
$is_secret     = Post::is_secret();
$is_notice     = Post::is_notice();

$post_page_id   = CommonUtils::get_post_page_id();
$page_permalink = CommonUtils::get_post_page_link();

$parent = Request::getParameter( 'parent', 0 );

$action_url = Rewrite::edit_ajax_link();

$custom_fields      = Setting::get_custom_fields();
$custom_fields_html = Setting::get_the_custom_field( $custom_fields, 'edit' );

?>
<form action="<?php echo $action_url; ?>" method="post" enctype="multipart/form-data">
	<table class="idea-board-table">
		<colgroup>
			<col style="width:15%;">
		</colgroup>
		<tbody>
		<?php if ( ! empty( $categories ) ) : ?>
			<tr>
				<th scope="col">
					<label for="meta_input[idea_board_category]">카테고리</label>
				</th>
				<td>
					<select name="meta_input[idea_board_category]" id="meta_input[idea_board_category]">
						<?php foreach ( $categories as $category ) : ?>
							<option value="<?php echo $category; ?>"
								<?php echo $category == $cate ? 'selected' : '' ?>><?php echo $category; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( $use_secret ): ?>
			<tr>
				<th><label for="meta_input[idea_board_is_secret]">비밀글</label></th>
				<td>
					<input
						type="checkbox"
						name="meta_input[idea_board_is_secret]"
						id="meta_input[idea_board_is_secret]"
						value="1"
						<?php echo $is_secret ? 'checked' : '' ?> />
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( Capability::current_user_can( 'notice_edit' ) ) { ?>
			<tr>
				<th><label for="meta_input[idea_board_is_notice]">공지사항</label></th>
				<td>
					<input type="checkbox" name="meta_input[idea_board_is_notice]" id="meta_input[idea_board_is_notice]"
					       value="-1"
						<?php echo $is_notice ? 'checked' : '' ?> />
				</td>
			</tr>
		<?php } ?>

		<tr>
			<th scope="col">
				<label for="post_title">제목</label>
			</th>
			<td>
				<input type="text" name="post_title" size="30" id="post_title"
				       value="<?php echo esc_attr( $post_title ); ?>"/>
			</td>
		</tr>
		<tr>
			<th scope="col"><label for="meta_input[idea_board_email]"><?php _e( '이메일' ); ?></label></th>
			<td>
				<input type="text" name="meta_input[idea_board_email]" id="meta_input[idea_board_email]"
				       value="<?php echo $user_email; ?>"/>
			</td>
		</tr>
		<?php if ( ! is_user_logged_in() ) : ?>
			<tr>
				<th><label for="meta_input[idea_board_user_name]">작성자</label></th>
				<td>
					<input type="text" name="meta_input[idea_board_user_name]" id="meta_input[idea_board_user_name]"
					       value="<?php echo $user_name; ?>"/>
				</td>
			</tr>
			<tr>
				<th><label for="post_password">패스워드</label></th>
				<td>
					<input type="password" name="post_password" id="post_password"
					       value="<?php echo $post_password ?>"/>
				</td>
			</tr>
		<?php endif; ?>

		<?php echo $custom_fields_html; ?>

		<tr>
			<th>내용</th>
			<td>
				<?php echo Editor::get_the_editor( array(
					'name'    => 'idea_board_post_content',
					'content' => $content
				) ); ?>
			</td>
		</tr>

		<tr>
			<th>파일첨부</th>
			<td>
				<div class="idea-board-file-help">
					<span class="n1">파일첨부는 <?php echo $input_count; ?>개 까지 가능합니다.</span>
				</div>

				<?php echo Post::the_file_list( true ); ?>

				<div class="idea-board-file-inputs">
					<?php for ( $i = 0; $i < $input_count; $i ++ ) { ?>
						<input type="file" name="files[]">
					<?php } ?>
				</div>
			</td>
		</tr>

		<?php do_action( 'idea_board_edit_end', $board, $post ); ?>

		</tbody>
	</table>

	<div class="idea-board-buttons">
		<?php echo Button::list_button(); ?>
		<input type="submit" value="저장">
	</div>

	<input type="hidden" name="parent" value="<?php echo $parent ?>"/>
	<input type="hidden"
	       name="tax_input[<?php echo PluginConfig::$board_tax; ?>][]"
	       value="<?php echo $board->term_id; ?>"/>
	<input type="hidden" name="ID" value="<?php echo $ID ?>"/>
	<input type="hidden" name="return_url" value="<?php echo $page_permalink; ?>"/>
	<input type="hidden" name="action" value="idea_board_edit_post"/>
	<input type="hidden" name="meta_input[idea_board_page_id]" value="<?php echo $post_page_id; ?>"/>

	<?php do_action( 'idea_board_nonce', $board, $post ); ?>

	<?php wp_nonce_field( PluginConfig::$idea_board_edit_nonce_action, PluginConfig::$idea_board_edit_nonce_name ); ?>
</form>
