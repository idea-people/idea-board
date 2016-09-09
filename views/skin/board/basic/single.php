<?php
use ideapeople\board\setting\Setting;
use ideapeople\board\Post;
use ideapeople\board\Rewrite;
use ideapeople\board\CommonUtils;

$board              = Setting::get_board();
$ID                 = get_the_ID();
$post               = Post::get_post();
$title              = Post::get_the_title();
$content            = Post::the_content( null, false, false );
$author_name        = Post::get_the_author_nicename();
$read_cnt           = Post::get_the_read_cnt();
$reg_date           = Post::get_the_date( 'Y-m-d' );
$attachments        = Post::get_attachments();
$custom_fields      = Setting::get_custom_fields();
$custom_fields_html = Setting::get_the_custom_field( $custom_fields, 'single' );

?>
<article id="#idea-board-post-<?php echo $ID; ?>">
	<header class="idea-board-entry-header">
		<h2><?php echo $title; ?></h2>

		<div class="idea-board-meta">
			<ul>
				<li class="user_nm">
					<span class="t1">작성자</span>
					<span class="t2"><?php echo $author_name; ?></span>
				</li>
				<li class="reg_date">
					<span class="t1">작성일</span>
					<span class="t2"><?php echo $reg_date; ?></span>
				</li>
				<li class="read_cnt">
					<span class="t1">조회수</span>
					<span class="t2"><?php echo $read_cnt; ?></span>
				</li>
			</ul>
		</div>
	</header>

	<div class="idea-board-entry-content">
		<?php echo $custom_fields_html; ?>

		<?php echo $content; ?>
	</div>

	<?php Post::the_file_list(); ?>

	<div class="idea-board-buttons">
		<a href="<?php echo Rewrite::list_link() ?>" class="idea-board-button">목록</a>
		<a href="<?php echo Rewrite::edit_link() ?>" class="idea-board-button">수정</a>
		<a href="<?php echo Rewrite::delete_link() ?>" class="idea-board-button">삭제</a>
		<a href="<?php echo Rewrite::reply_link() ?>" class="idea-board-button">답글</a>
	</div>
</article>



