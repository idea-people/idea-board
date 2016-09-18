<table>
	<tr>
		<th>Wp Version</th>
		<td><?php echo get_bloginfo( 'version' ) . "\n"; ?></td>
	</tr>
	<tr>
		<th>Language</th>
		<td><?php echo get_locale() . "\n"; ?></td>
	</tr>
	<tr>
		<th>Permalink Structure</th>
		<td><?php echo get_option( 'permalink_structure' ) . "\n"; ?></td>
	</tr>
	<tr>
		<th>Max Upload Size</th>
		<td>
			<?php echo wp_max_upload_size(); ?> BYTE
		</td>
	</tr>
</table>