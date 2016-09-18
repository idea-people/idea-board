<?php ?>
<?php
use ideapeople\board\setting\Setting;

$boards = Setting::get_boards();
?>

<ul>
	<?php foreach ( $boards as $board ): ?>
		<li>
			<a href="#"><?php echo $board->name ?></a>
		</li>
	<?php endforeach; ?>
</ul>
