<?
// Atirbutos aceptados:
// Required: el campo es requerido
// DateComboBoxes: el campo va a ser validado con la regla DateComboBoxesRule
// Number: el campo es un número. Sintaxis: Number; [<opcion1>][:<valor1>]; [<opcion2>][:<valor2>], etc.
//         donde las opciones pueden ser Min:<min> para el valor mínimo, Max:<max> para el valor máximo,
//         MinExclusive para indicar que el valor mínimo es exclusive, MaxExclusive para indicar
//         que el valor máximo es exclusive, AcceptFloat para indicar que el campo acepta reales,
//         Required para indicar que, además, el campo es requerido.
//         Ejemplo: Number; Min:10; Max:20; MaxExclusive; Required
// Email: el campo es validado segun las reglas de email. Sintaxis: Email[; Required]
//        Required indica que, además, el campo es requerido.

global $edit, $context_path;

global $html_page;
$validator = $edit->getJSValidator('edit', 'validate');
?>

<table width="100%" border="0" cellpadding="4" cellspacing="1">
	<form name="edit" action="<?= $edit->getActionPage() ?>" method="post" onSubmit="return validate()" enctype="multipart/form-data">

	<?
	print $edit->getHiddenFields();
	?>

	<th colspan="2" class="editFormHeader" align="left">
		Traduku:
	</th>

	<?
	foreach($edit->getEditRows() as $row) {
		if ($row->isSeparator()) {
			?>
			<td colspan="2" class="editSeparator" align="left"><?= $row->getName() ?></td>
			<?
		} else {
			?>
			<tr>
				<td class="editFieldName">
					<table border="0" cellpadding="2" cellspacing="1">
						<tr>
							<td valign="baseline"><img src="<?= $context_path ?>/interfaco/images/bullet_items.gif" vspace="2"></td>
							<td valign="baseline" class="editFieldName"><?= $row->getName() ?>
							<?
							$attributes = $row->getAttributes();
							if ($attributes) {
								$attributes = explode(',', $attributes);
								foreach($attributes as $attribute) {
									$attribute = trim($attribute);
									switch($attribute) {
										case 'Required':
											PHP::requireClasses('JSValidator/RequiredRule');
											$rule = new RequiredRule($row->getInputName());
											$rule->setOnRequiredError('alert("El campo ' . $row->getName() . ' es requerido")');
											$validator->addValidationRule($rule);
											print '(*) ';
											break;
										case 'DateComboBoxes':
											PHP::requireClasses('JSValidator/DateComboBoxesRule');
											$rule = new DateComboBoxesRule($row->getInputName() . '_day', $row->getInputName() . '_month', $row->getInputName() . '_year');
											$rule->setOnDateError('alert("El campo ' . $row->getName() . ' no corresponde a una fecha válida")');
											$validator->addValidationRule($rule);
											break;
									}

									if (substr($attribute, 0, 6) == 'Number') {
										$number = array();
										$opts = explode(';', $attribute);
										foreach($opts as $option) {
											list($key, $value) = explode(':', $option);
											$key = trim($key); $value = trim($value);
											switch($key) {
												case 'Required': $number['Required'] = true; break;
												case 'Min': $number['Min'] = $value; break;
												case 'Max': $number['Max'] = $value; break;
												case 'MinExclusive': $number['MinExclusive'] = true; break;
												case 'MaxExclusive': $number['MaxExclusive'] = true; break;
												case 'AcceptFloat': $number['AcceptFloat'] = true; break;
											}
										}

										PHP::requireClasses('JSValidator/NumberRule');
										$rule = new NumberRule($row->getInputName(), $number['AcceptFloat'], $number['Required']);
										$rule->setOnNaNError('alert("El campo ' . $row->getName() . ' debe ser un número")');
										if ($number['Required']) {
											$rule->setOnRequiredError('alert("El campo ' . $row->getName() . ' es requerido")');
											print '(*) ';
										}
										if (isset($number['Min']) and !isset($number['Max'])) {
											$rule->setMinimum($number['Min'], !$number['MinExclusive']);
											if ($number['MinExclusive']) {
												$msg = 'El campo ' . $row->getName() . ' debe ser mayor a ' . $number['Min'];
											} else {
												$msg = 'El campo ' . $row->getName() . ' debe ser mayor o igual a ' . $number['Min'];
											}
											$rule->setOnRangeError('alert("' . $msg . '")');
										}
										if (!isset($number['Min']) and isset($number['Max'])) {
											$rule->setMaximum($number['Max'], !$number['MaxExclusive']);
											if ($number['MaxExclusive']) {
												$msg = 'El campo ' . $row->getName() . ' debe ser menor a ' . $number['Max'];
											} else {
												$msg = 'El campo ' . $row->getName() . ' debe ser menor o igual a ' . $number['Max'];
											}
											$rule->setOnRangeError('alert("' . $msg . '")');
										}
										if (isset($number['Min']) and isset($number['Max'])) {
											$rule->setMinimum($number['Min'], !$number['MinExclusive']);
											$rule->setMaximum($number['Max'], !$number['MaxExclusive']);
											if ($number['MinExclusive']) {
												$msg = 'El campo ' . $row->getName() . ' debe ser mayor a ' . $number['Min'];
											} else {
												$msg = 'El campo ' . $row->getName() . ' debe ser mayor o igual a ' . $number['Min'];
											}
											if ($number['MaxExclusive']) {
												$msg .= ' y menor a ' . $number['Max'];
											} else {
												$msg .= ' y menor o igual a ' . $number['Max'];
											}
											$rule->setOnRangeError('alert("' . $msg . '")');
										}
										if (!$number['AcceptFloat']) {
											$rule->setOnFloatError('alert("El campo ' . $row->getName() . ' debe ser un entero")');
										}
										$validator->addValidationRule($rule);
									}

									if (substr($attribute, 0, 5) == 'Email') {
										$email = array();
										$opts = explode(';', $attribute);
										foreach($opts as $option) {
											list($key, $value) = explode(':', $option);
											$key = trim($key); $value = trim($value);
											switch($key) {
												case 'Required': $email['Required'] = true; break;
											}
										}
										PHP::requireClasses('JSValidator/EmailRule');
										$rule = new EmailRule($row->getInputName(), $email['Required']);
										$rule->setOnEmailError('alert("El campo ' . $row->getName() . ' no parece ser un email válido")');
										if ($email['Required']) {
											$rule->setOnRequiredError('alert("El campo ' . $row->getName() . ' es requerido")');
											print '(*) ';
										}
										$validator->addValidationRule($rule);
									}
								}
							}
							?>
							:</td>
						</tr>
					</table>
				</td>
				<td class="editFieldValue"><?= $row->getValue() ?></td>
			</tr>
			<?
		}
	}
	?>

	<tr>
		<td align="right" class="editFormHeader" colspan="2">
			<?
			if ($edit->hasCancelPage()) {
				global $_template_cancel_button;
				if (!$_template_cancel_button) $_template_cancel_button = 'CANCELAR';
				?>
				<button onClick="<?= $edit->getCancelPage() ?>" class="editCancelButton"><?= $_template_cancel_button ?></button>&nbsp;
				<?
			}

			global $_template_end_button;
			if (!$_template_end_button) $_template_end_button = 'FINALIZAR';
			?>
			<input type="submit" value="<?= $_template_end_button ?>" class="editOkButton">
		</td>
	</tr>
</table>

<?
$html_page->addHeader($validator->getJSCode());
?>