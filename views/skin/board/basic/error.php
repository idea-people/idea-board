<?php ?>
<h2><?php echo bloginfo( 'name' ) . " ERROR" ?></h2>

<hr/>

<ul>
	<?php foreach ( $error->get_error_codes() as $error_code ) : ?>
		<li><?php echo $error->get_error_message( $error_code ); ?></li>
	<?php endforeach; ?>
</ul>

<div class="idea-board-buttons">
	<a href="javascript:history.back();" class="idea-board-button">
		<?php _e_idea_board( 'List' ); ?>
	</a>
</div>