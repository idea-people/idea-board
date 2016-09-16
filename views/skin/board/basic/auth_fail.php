<?php
use ideapeople\board\Button;
use ideapeople\board\Rewrite;

?>
<p><?php _e_idea_board( 'You do not have permission' ); ?>.</p>

<div class="idea-board-buttons">
	<?php echo Button::prev_button(); ?>
</div>