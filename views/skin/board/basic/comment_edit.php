<?php
use ideapeople\board\Comment;

$comment_ID = get_query_var( 'comment_ID' );
$comment    = get_comment( $comment_ID );

function my_wp_get_current_commenter( $commenter = array() ) {
	$comment_ID = get_query_var( 'comment_ID' );
	$comment    = get_comment( $comment_ID );

	$commenter[ 'comment_author' ]       = $comment->comment_author;
	$commenter[ 'comment_author_email' ] = $comment->comment_author_email;
	$commenter[ 'comment_author_url' ]   = $comment->comment_author_url;

	return $commenter;
}

add_filter( 'wp_get_current_commenter', 'my_wp_get_current_commenter' );

function my_comment_form_default_fields( $fields ) {
	$fields[ 'idea-board-prev' ] = \ideapeople\board\Button::read_button();

	return $fields;
}

add_filter( 'comment_form_default_fields', 'my_comment_form_default_fields' );

comment_form( array(
	'id_form'        => 'idea-board-comment-form',
	'class_form'     => 'idea-board-reset idea-board-comment-form idea-board-validate',
	'title_reply'    => __( 'Comments' ) . ' ' . __( 'Edit' ),
	'label_submit'   => __( 'Comments' ) . ' ' . __( 'Edit' ),
	'action'         => admin_url( '/admin-ajax.php' ) . '?mode=edit&action=idea_handle_comment_submission',
	'comment_author' => esc_attr( $comment->comment_author )
), get_query_var( 'pid' ) );

?>
<script>
	(function ($) {
		$(document).ready(function () {
			$('#comment').val('<?php echo esc_attr($comment->comment_content); ?>');
			$('#comment_password').val('<?php echo Comment::get_comment_password($comment_ID); ?>');
		});
	})(jQuery);
</script>
