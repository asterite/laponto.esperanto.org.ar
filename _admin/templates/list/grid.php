<table border="0" cellpadding="4" cellspacing="1" width="100%">
	<tr>
		<?
		global $list, $context_path;

		$headers = $list->getListHeaders();
		foreach($headers as $header) {
			?>
			<td class="listgridHeader" nowrap>
				<?= $header->getName() ?>&nbsp;
				<?
				if ($header->allowsOrder()) {
					if ($header->isAscendentOrder()) {
						?>
						<img src='/_admin/images/grid_order_up_active.gif' width='6' height='5'>
						<?
					} else {
						?>
						<a onMouseover="showtip(this,event,'Orden Creciente')"
							onMouseout="hidetip()"
							href="<?= $header->getLinkAscendentOrder() ?>"><img
							src='/_admin/images/grid_order_up.gif' width='6' height='5' border='0'></a>
						<?
					}
					if ($header->isDescendentOrder()) {
						?>
						<img src='/_admin/images/grid_order_down_active.gif' width='6' height='5'>
						<?
					} else {
						?>
						<a onMouseover="showtip(this,event,'Orden Decreciente')"
							onMouseout="hidetip()" href="<?= $header->getLinkDescendentOrder() ?>"><img
							src='/_admin/images/grid_order_down.gif' width='6' height='5' border='0'></a>
						<?
					}
				}
				?>
			</td>
			<?
		}

		if ($list->hasOptions()) {
			?>
			<td class="listgridHeader" nowrap>Opciones</td>
			<?
		}
		?>
	</tr>
	<?
	//Colores a switchear
	$class1 = 'listGridEvenRow';
	$class2 = 'listGridUnevenRow';

	$rows = $list->getListRows();
	foreach($rows as $row) {
		$class = $class == $class2 ? $class1 : $class2;
		?>
		<tr class="<?= $class ?>">
			<?
			$cells = $row->getCells();
			foreach($cells as $cell) {
				?>
				<td
				<?
				$attributes = $cell->getAttributes();
				if ($attributes) foreach($attributes as $name => $value) print $name . '="' . $value . '" ';
				?>
				><?= $cell->getValue() ?>&nbsp;</td>
				<?
			}

			if ($list->hasOptions()) {
				?>
				<td align="center">
				<?
				if ($row->hasEditPage()) {
					?>
					<a onMouseover="showtip(this,event,'Editar')"
						onMouseout="hidetip()"
						href="<?= $row->getLinkEditPage() ?>"><img src="<?= $context_path ?>/_admin/images/grid_option_edit.gif"
						width="14" height="15" border="0"></a>
					<?
				}
				if ($row->hasDeletePage()) {
					?>
					<a onMouseover="showtip(this,event,'Eliminar')"
						onMouseout="hidetip()"
						onClick="return confirm('Está seguro que desea eliminar el registro?')"
						href="<?= $row->getLinkDeletePage() ?>"><img
						src="<?= $context_path ?>/_admin/images/grid_option_delete.gif" width="14" height="15" border="0"></a>
					<?
				}
				foreach($row->getCustomPages() as $custom) {
					?>
					<a class="<?= $class ?>" onMouseover="showtip(this,event,'<?= $custom->tooltip ?>')"
						onMouseout="hidetip()" href="<?= $custom->getLink() ?>"
						target="<?= $custom->getTarget() ?>"><?= $custom->getName() ?></a>
					<?
				}
				?>
				</td>
				<?
			}
			?>
		</tr>
		<?
	}

	if (sizeof($rows) == 0) {
		?>
		<tr class="<?= $class2 ?>">
			<td class="listGridEntry" align="center" colspan="<?= sizeof($headers) + 1 ?>">
				No se encontraron registros
			</td>
		</tr>
		<?
	}
	?>
</table>