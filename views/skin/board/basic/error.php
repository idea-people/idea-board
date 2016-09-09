<?php ?>
<h2><?php echo bloginfo( 'name' ) . " 오류" ?></h2>

<hr/>

<ul>
	<?php foreach ( $error->get_error_codes() as $error_code ) : ?>
		<li><?php echo $error->get_error_message( $error_code ); ?></li>
	<?php endforeach; ?>
</ul>

<div class="idea-board-buttons">
	<a href="javascript:history.back();" class="idea-board-button">목록</a>
</div>