<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response');
PHP::requireCustom('Film', 'FilmUser', 'DateUtils', 'Display');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

global $ne_aperigi_fenestro;
$ne_aperigi_fenestro = Request::getBoolean('naf');

PHP::rootInclude('layout/top.php');
?>
<div id="descripcion">
<div id="subtitulo"><?= __('Miaj mesagxoj') ?></div>
<?= __('pri.miaj.mesagxoj') ?>
</div>
<?
PHP::rootInclude('layout/menu.php');
?>
<form id="theMessages" name="theMessages" action="remove_event.do.php" method="post">
<table border="0" cellpadding="4" cellspacing="1" width="100%">
	<?
	$man = $user->getEvents();
	$man->addOrder('event_date', false);
	$man->query();
	while($event = $man->next()) {
		if ($event->status == 'new') {
			$style = 'background-color:#EBEBEB;font-weight:bold';
		} else {
			$style = 'background-color:#F8F8F8;';
		}
		?>
		<tr style="<?= $style ?>">
			<td width="20" align="center"><img src="img/<?= $event->event_type ?>.gif"/></td>
			<?
			switch($event->event_type) {
				case 'request':
					$manUser = new UserManager();
					$fromUser = $manUser->getFromKeys(array('id' => $event->from_x));
					
					$manFilm = new FilmManager();
					$film = $manFilm->getFromKeys(array('id' => $event->message));
					?>
					<td width="150"><?= __('De {x}', array('x' => displayUser($fromUser))) ?></td> 
					<td width="354"><?= __('Mi volas kunlabori en la filmo {y}', array('y' => displayFilm($film))) ?>.</td>
					<?
					break;
				case 'film_access_granted':
					$manFilm = new FilmManager();
					$film = $manFilm->getFromKeys(array('id' => $event->from_x));
					?>
					<td width="150"><?= __('De {x}', array('x' => '<strong>La Ponto</strong>')) ?></td> 
					<td width="354"><?= __('La personoj kiuj tradukas {x} permesis vin partopreni la tradukadon!', array('x' => displayFilm($film))) ?> <nobr>;-)</nobr></td>
					<?
					break;
				case 'film_access_denied':
					$manFilm = new FilmManager();
					$film = $manFilm->getFromKeys(array('id' => $event->from_x));
					?>
					<td width="150"><?= __('De {x}', array('x' => '<strong>La Ponto</strong>')) ?></td> 
					<td width="354"><?= __('La personoj kiuj tradukas {x} malpermesis vin partopreni la tradukadon', array('x' => displayFilm($film))) ?>. <nobr>:-(</nobr></td>
					<?
					break;
				case 'film_change_privileges':
					list($user_id, $to) = unserialize($event->message);
					
					$manUser = new UserManager();
					$fromUser = $manUser->getFromKeys(array('id' => $user_id)); 
				
					$manFilm = new FilmManager();
					$film = $manFilm->getFromKeys(array('id' => $event->from_x));
					?>
					<td width="150"><?= __('De {x}', array('x' => displayUser($fromUser))) ?></td> 
					<td width="354"><?= displayPrivilegesChangeSubject($to, $film) ?></td>
					<?
					break;
				case 'message':
					list ($subject, $text) = unserialize($event->message);
					
					$manUser = new UserManager();
					$fromUser = $manUser->getFromKeys(array('id' => $event->from_x));
					?>
					<td width="150"><?= __('De {x}', array('x' => displayUser($fromUser))) ?></td> 
					<td width="354"><?= $subject ?></td>
					<?
					break;
				case 'explanation':
					list ($film_id, $number, $original_text, $translated_text, $new_text, $explanation) = unserialize($event->message);
					
					$manUser = new UserManager();
					$fromUser = $manUser->getFromKeys(array('id' => $event->from_x)); 
				
					$manFilm = new FilmManager();
					$film = $manFilm->getFromKeys(array('id' => $film_id));
					?>
					<td width="150"><?= __('De {x}', array('x' => displayUser($fromUser))) ?></td> 
					<td width="354"><?= __('Korekto al la subtitolo #{x} de la filmo {y}', array('x' => $number, 'y' => displayFilm($film))) ?>.</td>
					<?
					break;
				case 'alert_evil':
					list ($film_id, $evil_user, $number, $original_text, $translated_text) = unserialize($event->message);
					
					$manUser = new UserManager();
					$fromUser = $manUser->getFromKeys(array('id' => $event->from_x)); 
				
					$manFilm = new FilmManager();
					$film = $manFilm->getFromKeys(array('id' => $film_id));
					?>
					<td width="150"><?= __('De {x}', array('x' => displayUser($fromUser))) ?></td> 
					<td width="354"><?= __('Rimarko de malbonintenco, subtitolo #{x}, filmo {y}', array('x' => $number, 'y' => displayFilm($film))) ?>.</td>
					<?
					break;
				case 'film_deleted':
					list ($film_name, $why) = unserialize($event->message);
					
					$manUser = new UserManager();
					$fromUser = $manUser->getFromKeys(array('id' => $event->from_x));
					?>
					<td width="150"><?= __('De {x}', array('x' => displayUser($fromUser))) ?></td> 
					<td width="354"><?= __('Mi forvisxis la filmon {y}', array('y' => $film_name)) ?>.</td>
					<?
					break;
			}
			?>
			<td width="120" align="center"><?= displayShortDate($event->event_date) ?></td>
			<td width="30" align="center"><a href="vidi_mesagxon.php?id=<?= $event->id ?>"><?= __('Vidi') ?></a></td>
			<td width="20" align="center"><?
				if ($event->event_type != 'request') {
					?><input type="checkbox" name="id[]" value="<?= $event->id ?>"/><?
				} else {
					print '&nbsp;';
				}
				?></td>
		</tr>
		<?
	}
	
	if ($man->getTotalResults() == 0) {
		?>
		<tr><td><?= __('Vi ne havas mesagxojn') ?>.</td></tr>
		<?
	}
	?>
</table>
</form>
<?
if ($man->getTotalResults() > 0) {
	?>
	<br/>
	<div style="width:100%;text-align:right"><img src="img/delete.gif" hspace="4" border="0"/><a href="javascript:forvisxiElektitajn()"><?= __('Forvisxi elektitajn') ?></a></div>
	<?
}

PHP::rootInclude('layout/bottom.php');
?>