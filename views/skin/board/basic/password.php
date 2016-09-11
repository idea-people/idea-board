<?php
use ideapeople\board\Button;
use ideapeople\board\Post;

$title         = Post::get_the_title();
$password_form = Post::get_the_password_form();
?>
<h2><?php echo $title; ?></h2>

<div class="idea-board-password-form">
	<?php echo $password_form; ?>
</div>

<div class="idea-board-buttons">
	<?php echo Button::prev_button() ?>
</div>

<script>jQuery('input[type="password"]').focus();</script>