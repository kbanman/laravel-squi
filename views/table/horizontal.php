<table<?php echo $table->attributes(); ?>>
	<thead>
		<tr<?php echo $table->header->get_attributes($table->header_data); ?>>
<?php foreach ($table->columns as $col): ?>
			<th<?php echo $table->header->get_attributes($col); ?>><?php echo $col->get_value($table->header_data); ?></th>
<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
<?php foreach ($table->rows as $row): ?>
		<tr<?php echo $table->row->get_attributes($row); ?>>
<?php foreach ($table->columns as $col): ?>
			<td<?php echo $col->get_attributes($row); ?>><?php echo $col->get_value($row); ?></td>
<?php endforeach; ?>
		</tr>
<?php endforeach; if ( ! count($table->rows) and $table->empty_message): ?>
		<tr class="norecords">
			<td colspan="<?php echo count($table->columns); ?>"><?php echo $table->empty_message; ?></td>
		</tr>
<?php endif; ?>
	</tbody>
<?php if ($table->footer): ?>
	<tfoot>
		<tr<?php echo $table->footer->get_attributes($table->header_data); ?>>
<?php foreach ($table->columns as $col): ?>
			<td<?php echo $table->footer->get_attributes($col); ?>><?php echo $col->get_value($table->footer_data); ?></td>
<?php endforeach; ?>
		</tr>
	</tfoot>
<?php endif; ?>
</table>