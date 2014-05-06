<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response');
PHP::requireCustom('Film', 'FilmUser', 'DateUtils', 'Display');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

$id = Request::getParameter('id');

$man = new UserEventManager();
$event = $man->getFromKeys(array('id' => $id));

if (!$event or $event->to_user != $user->id) {
	Response::sendRedirect('index.php');
}

if ($event->status == 'new') {
	$man = new UserEventManager();
	$event->status = 'seen';
	$man->update($event);
}
PHP::rootInclude('layout/top.php');
?>
<div id="descripcion">
<div id="subtitulo"><?= __('Miaj mesagxoj') ?></div>
<?= __('pri.vidi.mesagxon') ?>
</div>
<?
PHP::rootInclude('layout/menu.php');
?>
<br/>
<table border="0" cellpadding="6" cellspacing="1" width="100%">
	<tr>
		<td colspan="2" style="background-color:#EEEEEE"><img src="img/<?= $event->event_type ?>.gif"/> <?= __('Mesagxo') ?>...</td>
	</tr>
	<?
	switch($event->event_type) {
		case 'request':
			$manUser = new UserManager();
			$fromUser = $manUser->getFromKeys(array('id' => $event->from_x));
			
			$manFilm = new FilmManager();
			$film = $manFilm->getFromKeys(array('id' => $event->message));
			?>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('De') ?>:</td>
				<td><strong>La Ponto</strong></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('Dato') ?>:</td>
				<td><?= displayLongDate($event->event_date) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td><?= __('Temo') ?>:</td>
				<td><?= __('Mi volas kunlabori en la filmo {y}', array('y' => displayFilm($film))) ?>.</td>
			</tr>			
			<tr style="background-color:#EDF0F8">
				<td colspan="2">
				<?= __('pri.mesagxo.peto') ?>
				<br/><br/>
				<a href="respondi_peton.do.php?id=<?= $event->id ?>&akcepti=on"><?= __('Akcepti lin') ?></a>
				&nbsp; <a href="respondi_peton.do.php?id=<?= $event->id ?>"><?= __('Malakcepti lin') ?></a><br><br style="font-size:4px"/>
				</td>
			</tr> 
			<?
			break;
		case 'film_access_granted':
			$manFilm = new FilmManager();
			$film = $manFilm->getFromKeys(array('id' => $event->from_x));
			?>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('De') ?>:</td>
				<td><strong>La Ponto</strong></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('Dato') ?>:</td>
				<td><?= displayLongDate($event->event_date) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td><?= __('Temo') ?>:</td>
				<td><?= __('La personoj kiuj tradukas {x} permesis vin partopreni la tradukadon!', array('x' => displayFilm($film))) ?> <nobr>;-)</nobr></td>
			</tr>
			<tr style="background-color:#EDF0F8">
				<td colspan="2"><?= __('pri.mesagxo.peto.permesi', array('x' => displayFilm($film))) ?></td>
			</tr>
			<?
			break;
		case 'film_access_denied':
			$manFilm = new FilmManager();
			$film = $manFilm->getFromKeys(array('id' => $event->from_x));
			?>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('De') ?>:</td>
				<td><strong>La Ponto</strong></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('Dato') ?>:</td>
				<td><?= displayLongDate($event->event_date) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td><?= __('Temo') ?>:</td>
				<td><?= __('La personoj kiuj tradukas {x} malpermesis vin partopreni la tradukadon', array('x' => displayFilm($film))) ?>. <nobr>:-(</nobr></td>
			</tr>
			<tr style="background-color:#EDF0F8">
				<td colspan="2"><?= __('pri.mesagxo.peto.malpermesi') ?></td>
			</tr>
			<?
			break;
		case 'film_change_privileges':
			list($user_id, $to) = unserialize($event->message);
			
			$manUser = new UserManager();
			$fromUser = $manUser->getFromKeys(array('id' => $user_id)); 
		
			$manFilm = new FilmManager();
			$film = $manFilm->getFromKeys(array('id' => $event->from_x));
			?>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('De') ?>:</td>
				<td><?= displayUser($fromUser) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('Dato') ?>:</td>
				<td><?= displayLongDate($event->event_date) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td valign="baseline"><?= __('Temo') ?>:</td>
				<td><?= displayPrivilegesChangeSubject($to, $film) ?></td>
			</tr>
			<tr style="background-color:#EDF0F8">
				<td colspan="2">
					<?
					switch($to) {
						case 'normal':
							print __('pri.mesagxo.normala.uzanto');
							break;
						case 'adder':
							print __('pri.mesagxo.invitanto.uzanto');
							break;
						case 'super_user':
							print __('pri.mesagxo.super.uzanto');
							break;
						case 'blocked':
							print __('pri.mesagxo.blokita');
							break;
						case 'unblocked':
							print __('pri.mesagxo.malblokita');
							break;
					}
					?>
				</td>
			</tr>
			<?
			break;
		case 'message':
			list ($subject, $text) = unserialize($event->message);
			
			$manUser = new UserManager();
			$fromUser = $manUser->getFromKeys(array('id' => $event->from_x));
			?>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('De') ?>:</td>
				<td><?= displayUser($fromUser) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('Dato') ?>:</td>
				<td><?= displayLongDate($event->event_date) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td valign="baseline"><?= __('Temo') ?>:</td>
				<td><?= $subject ?></td>
			</tr>
			<tr style="background-color:#EDF0F8">
				<td colspan="2">
					<div><?= $text ?></div><br/>
					<img src="img/pencil.gif" hspace="4" border="0"/><a href="javascript:respondiMesagxon(<?= $fromUser->id ?>, <?= $event->id ?>)"><?= __('Respondi') ?>...</a>
				</td>
			</tr>
			<?
			break;
		case 'explanation':
			list ($film_id, $number, $original_text, $translated_text, $new_text, $explanation) = unserialize($event->message);
			
			$manUser = new UserManager();
			$fromUser = $manUser->getFromKeys(array('id' => $event->from_x)); 
		
			$manFilm = new FilmManager();
			$film = $manFilm->getFromKeys(array('id' => $film_id));
			?>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('De') ?>:</td>
				<td><?= displayUser($fromUser) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('Dato') ?>:</td>
				<td><?= displayLongDate($event->event_date) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td valign="baseline"><?= __('Temo') ?>:</td>
				<td><?= __('Korekto al la subtitolo #{x} de la filmo {y}', array('x' => $number, 'y' => displayFilm($film))) ?>.</td>
			</tr>
			<tr style="background-color:#EDF0F8">
				<td colspan="2">
				<strong><?= __('Originala teksto') ?>:</strong><br/>
				<?= $original_text ?><br/><br/>
				<strong><?= __('Via traduko') ?>:</strong><br/>
				<?= $translated_text ?><br/><br/>
				<strong><?= __('La traduko de {x}', array('x' => displayUser($fromUser))) ?>:</strong><br/>
				<?= $new_text ?><br/><br/>
				<strong><?= __('Lia ekspliko') ?>:</strong><br/>
				<?= $explanation ?><br/><br/>
				<a href="traduki.php?id=<?= $film_id ?>&number=<?= $number ?>"><?= __('Iri al cxi tiu subtitolo') ?></a>
				</td>
			</tr>
			<?
			break;
		case 'alert_evil':
			list ($film_id, $evil_user, $number, $original_text, $translated_text) = unserialize($event->message);
			
			$manUser = new UserManager();
			$fromUser = $manUser->getFromKeys(array('id' => $event->from_x)); 
			
			$manUser = new UserManager();
			$evilUser = $manUser->getFromKeys(array('id' => $evil_user));
		
			$manFilm = new FilmManager();
			$film = $manFilm->getFromKeys(array('id' => $film_id));
			?>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('De') ?>:</td>
				<td><?= displayUser($fromUser) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('Dato') ?>:</td>
				<td><?= displayLongDate($event->event_date) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td valign="baseline"><?= __('Temo') ?>:</td>
				<td><?= __('Rimarko de malbonintenco, subtitolo #{x}, filmo {y}', array('x' => $number, 'y' => displayFilm($film))) ?>.</td>
			</tr>
			<tr style="background-color:#EDF0F8">
				<td colspan="2">
				<strong><?= __('Originala teksto') ?>:</strong><br/>
				<?= $original_text ?><br/><br/>
				<strong><?= __('La traduko de {x}', array('x' => displayUser($evilUser))) ?>:</strong><br/>
				<?= $translated_text ?><br/><br/>
				<a href="traduki.php?id=<?= $film_id ?>&number=<?= $number ?>"><?= __('Iri al cxi tiu subtitolo') ?></a>
				</td>
			</tr>
			<?
			break;
		case 'film_deleted':
			list ($film_name, $kialo) = unserialize($event->message);
					
			$manUser = new UserManager();
			$fromUser = $manUser->getFromKeys(array('id' => $event->from_x));
			?>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('De') ?>:</td>
				<td><?= displayUser($fromUser) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td width="70"><?= __('Dato') ?>:</td>
				<td><?= displayLongDate($event->event_date) ?></td>
			</tr>
			<tr style="background-color:#F8F8F8">
				<td valign="baseline"><?= __('Temo') ?>:</td>
				<td><?= __('Mi forvisxis la filmon {y}', array('y' => $film_name)) ?>.</td>
			</tr>
			<tr style="background-color:#EDF0F8">
				<td colspan="2">
					<?
					if (trim($kialo)) {
						print $kialo;
					} else {
						print __('La mastro ne eksplikis kial li forvisxis la filmon') . '. :-(';
					}
					?>
				</td>
			</tr>
			<?
			break;
	}
	?>
</table>
<br/>
<?
if ($event->event_type != 'request') {
	?>
	<img src="img/delete.gif" hspace="4" border="0"/><a href="remove_event.do.php?id=<?= $event->id ?>"><?= __('Forvisxi cxi tiun mesagxon') ?></a>
	<?
}

PHP::rootInclude('layout/bottom.php');
?>