<form<?php echo $form->attributes(); ?>>
<?php echo $form->before; ?>
<?php if ($form->legend !== false): ?>
	<legend><?php echo $form->legend; ?></legend>
<?php endif; ?>
	<table class="form">
		<tbody>
<?php foreach ($fields as $field): ?>
			<tr>
				<th><?php echo $field->label; ?></th>
				<td><?php echo $field; 
				if ($error = $field->error()): ?>
				<span class="error"><?php echo $error; ?></span>
				<?php endif; ?></td>
			</tr>
<?php endforeach; 
if ( ! empty($buttons)): ?>
			<tr class="form-buttons">
				<td colspan="2">
<?php foreach ($buttons as $button): ?>
					<?php echo $button."\n"; ?>
<?php endforeach; ?>
				</td>
			</tr>
<?php endif; ?>
		</tbody>
	</table>
<?php echo $form->after; ?>
</form>