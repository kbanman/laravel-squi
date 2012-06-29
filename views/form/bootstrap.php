<form<?php echo $form->attributes(); ?>>
<?php echo $form->before;
if ($form->fieldset): ?>
	<fieldset>
<?php endif; ?>
<?php if ($form->legend !== false): ?>
		<legend><?php echo $form->legend; ?></legend>
<?php endif;
foreach ($fields as $field): ?>
		<div class="control-group">
			<?php echo $field->label."\n"; ?>
			<div class="controls">
				<?php 
				echo $field."\n";
				if ($error = $field->error()): ?>
				<span class="error"><?php echo $error; ?></span>
				<?php endif; ?>
			</div>
		</div>
<?php endforeach;
if ( ! empty($buttons)): ?>
		<div class="form-actions">
<?php foreach ($buttons as $button): ?>
				<?php echo $button; ?>
<?php endforeach; ?>

		</div>
<?php endif;
if ($form->fieldset): ?>
	</fieldset>
<?php endif; ?>
<?php echo $form->after; ?>
</form>