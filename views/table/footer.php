	<tfoot>
		<tr>
<?php foreach ($table->columns as $col): ?>
			<td><?php echo $col->get_value($table->footer_data); ?></td>
<?php endforeach; ?>
		</tr>
	</tfoot>