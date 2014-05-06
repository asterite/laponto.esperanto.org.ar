<?
global $list;
$search_properties = $list->getSearchProperties();

if ($list->hasInsertPage() || $search_properties->hasOptions()) {
	?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<?
			if ($list->hasInsertPage()) {
				global $list_insert_button;
				if (!$list_insert_button) $list_insert_button = 'Crear Nuevo';
				?>
				<td valign="middle" width="100%"><button class="listInsertButton" onClick="window.location = '<?= $list->getLinkInsertPage() ?>'"><?= $list_insert_button ?></button></td>
				<?
			}

			if ($search_properties->hasOptions()) {
				?>
				<form name="search" method="get">
					<td align="right">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="100%" align="right">
									<?
									print $search_properties->getHiddenInputs();
									?>
									<select name="<?= $search_properties->getSelectInputName() ?>">
										<?
										$options = $search_properties->getOptions();
										foreach($options as $option) {
											?>
											<option value="<?= $option->value ?>"
											<?
											if ($option->is_selected) print ' selected';
											?>
											><?= $option->name ?>
											<?
										}
										?>
									</select>
								</td>
								<td>&nbsp;<input type="text" name="<?= $search_properties->getTextInputName() ?>" value="<?= $search_properties->getTextInputValue() ?>"></td>
								<td>&nbsp;<input type="submit" class="listSearchButton" value="Buscar"></td>
							</tr>
						</table>
					</td>
				</form>
				<?
			}
			?>
		</tr>
	</table>
	<br>
	<?
}
?>