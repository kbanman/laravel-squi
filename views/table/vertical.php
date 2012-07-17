<table<?php echo $table->attributes(); ?>>
	<tbody>
<?php foreach ($table->columns as $col): ?>
		<tr>
			<th<?php echo $table->header->get_attributes($col); ?>><?php echo $col->get_value($table->header_data); ?></th>
<?php foreach ($table->rows as $row): ?>
			<td<?php echo $col->get_attributes($row); ?>><?php echo $col->get_value($row); ?></td>
<?php endforeach; ?>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>