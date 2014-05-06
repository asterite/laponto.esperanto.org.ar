<?
global $title, $instructions, $help_page, $context_path;

if ($title || $instructions || $help_page) {
	?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<?
		if ($title || $help_page) {
			?>
			<tr>
				<?
				if ($title) {
					?>
					<td valign="top" align="left" class="abmTitle"><!--
						--><img src="<?= $context_path ?>/_admin/images/mini_icon_2.gif">
						<?= $title ?></td>
					<?
				}
				if ($help_page) {
					?>
					<td valign="middle" align="right"><!--
						--><a href="javascript: openHelpPopup('<?= $help_page ?>')"><!--
						--><img src="<?= $context_path ?>/_admin/images/help_icon.gif" border="0"></a></td>
					<?
				}
				?>
			</tr>
			<?
		}
		?>
		<tr>
			<td colspan="2" valign="top" height="1" class="abmInstructionsSeparators"><img src="<?= $context_path ?>/_admin/images/blank.gif" width="5" height="1"></td>
		</tr>
		<?
		if ($instructions) {
			?>
			<tr>
				<td colspan="2" valign="top" class="abmInstructions">
					<table border="0" cellpadding="0" cellspacing="0" width="98%" align="center">
						<tr>
							<td valign="top" class="abmInstructions">
								<br style="font-size:4px">
								<?= $instructions ?><br>
								<br style="font-size:6px">
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top" height="1" class="abmInstructionsSeparators"><img src="<?= $context_path ?>/_admin/images/blank.gif" width="5" height="1"></td>
			</tr>
			<?
		}
		?>
	</table>

	<br style="font-size:20px">
	<?
}
?>