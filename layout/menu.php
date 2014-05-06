<?
PHP::requireCustom('Esperanto');
global $menu_left, $context_path;
?>
<table id="menu" width="100%" cellpadding="0" cellpsacing="0" border="0">
	<tr><?
		if ($menu_left) {
			?><td><?
				foreach($menu_left as $menu) {
					?><img src="<?= $menu[2] ?>" hspace="4" border="0" style="position:relative;top:2px"/><a href="<?= $menu[1] ?>"><?= $menu[0] ?></a>
					<?
				}
			?></td><?
		}
		$user = UserManager::getRemembered();
		if (!$user) {
			$user = UserManager::getRemembered();
		}
		if ($user) {
			global $ne_aperigi_fenestro;
			$events = $user->countNewEvents();
			$previous = Session::getAttribute('user_previous_events');
			if (!$previous) $previous = 0;
			if ($events > 0) {
				if ($user->notify_new_messages and $events > $previous and !$ne_aperigi_fenestro) {
					global $html_page;
					$html_page->appendToBodyAttribute('onLoad', "montriNovanMesagxon()");
				}
			}
			Session::setAttribute('user_previous_events', $events);
			?>
			<td align="right">
				<img src="<?= $context_path ?>/img/home.gif" hspace="4" border="0" style="position:relative;top:2px"/><a href="<?= $context_path ?>/hejmo.php"><?= __('Mia konto') ?></a>
				<?
				if ($events == 0) {
					?>
					<img src="<?= $context_path ?>/img/message.gif" hspace="4" border="0" style="position:relative;top:2px"/><a href="<?= $context_path ?>/mesagxoj.php"><?= __('Miaj mesagxoj') ?></a>
					<?
				} else {
					?>
					<img src="<?= $context_path ?>/img/message.gif" hspace="4" border="0" style="position:relative;top:2px"/><a href="<?= $context_path ?>/mesagxoj.php"><strong><?= __('Miaj mesagxoj') ?> (<?= $events ?>)</strong></a>
					<?
				}
				?>
				<img src="<?= $context_path ?>/img/preferencias.gif" hspace="4" border="0" style="position:relative;top:2px"/><a href="<?= $context_path ?>/uzantpreferoj.php"><?
				if (!$user->name or !$user->email) print '<strong>';
				print __('Miaj preferoj');
				if (!$user->name or !$user->email) print '</strong>';
				?></a>
			</td>
			<?
		} else {
			?>
			<td align="right"><img src="<?= $context_path ?>/img/home.gif" hspace="4" border="0"/><a href="<?= $context_path ?>/"><?= __('Reen al la cxefa pagxo') ?></a></td>
			<?
		}
		?>		
	</tr>
</table>
<?/*
<div id="missingData">
<?= xASombreros('Vi ankoraux ne indikis vian retposxtadreson, por ke la pagxo povu rimarkigi vin kiam nova mesagxon alvenas.<br/><a href="uzantpreferoj.php">Claku cxi tie</a>.') ?>
</div>
<br/>
*/?>