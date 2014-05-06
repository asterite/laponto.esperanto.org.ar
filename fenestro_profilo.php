<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request');
PHP::requireCustom('Film', 'FilmUser', 'FilmUserEvent', 'Display');

$id = Request::getParameter('id');
$man = new UserManager();
$other = $man->getFromKeys(array('id' => $id));

if (!$id or !$other) {
	?>
	
	<?
}

PHP::rootInclude('layout/top_html_page.php');
?>
<iframe id="l" name="l" style="display:none"></iframe>
<div id="popupContent">
<span style="border-bottom:1px dashed black"><?= __('Profilo de {x}', array('x' => "<strong>{$other->nickname}</strong>")) ?></span><br/><br/>
<strong><?= __('Nomo') ?></strong>: <?= $other->name ? $other->name : '?' ?><br/><br/>
<strong><?= __('Kunlaborado en filmoj') ?>:</strong>
<?
$total_points = 0;

$db = DataObjectsManager::getDatabase();
$conn = $db->openConnection();
$ps = $conn->prepareStatement("SELECT SUM(s.trust != :trust) as film_points, SUM(s.user_id = :user_id AND s.trust != :trust) as user_points, f.id as film_id, f.name as film_name FROM subtitolu_film_subtitle s, subtitolu_film f WHERE f.id = s.film_id GROUP BY s.film_id ORDER BY f.name asc");
$ps->setInteger('user_id', $other->id);
$ps->setString('trust', FILM_SUBTITLE_NO_TRUST);
$rs = $ps->executeQuery();

if ($rs->size() == 0) {
	?>
	<ul><li><em><?= __('Cxi tiu uzanto ankoraux ne kunlaboris') ?>.</li></ul>
	<?
} else {
	?>
	<ul>
		<?
		while($rs->next()) {
  		$user_points = $rs->getInteger('user_points');
  		if ($user_points == 0) continue;
			$film_name = $rs->getString('film_name');
			$film_points = $rs->getInteger('film_points');
			$total_points += $user_points; 
			?>
			<li><?= $film_name ?>: <?= displayPercentage($film_points, $user_points) ?></li>
			<?
			
		}
		?>
	</ul>
	<?
}
?>
<strong><?= __('Poentoj') ?>:</strong> <?= $total_points ?><br/><br/>
<?
if ($user = UserManager::getRemembered() and $user->id != $other->id) {
	$eventId = Request::getParameter('eventId');
	if ($eventId) {
		$man = new UserEventManager();
		$event = $man->getFromKeys(array('id' => $eventId));
		list($subject, $text) = unserialize($event->message);
		$subject = 'Re: ' . $subject;
	} else {
		$subject = '';
	}
	?>
	<div id="message">
	<div class="s"><strong><?= __('Sendi mesagxon') ?></strong></div>
	<form action="sendi_mesagxon.do.php" method="post" target="l">
	<input type="hidden" name="de" value="<?= $user->id ?>"/>
	<input type="hidden" name="al" value="<?= $other->id ?>"/>
	<div class="s"><?= __('Temo') ?>:</div>
	<div class="s"><input type="text" name="temo" size="40" value="<?= str_replace('"', "'", $subject) ?>"></div>
	<div class="s"><?= __('Mesagxo') ?>:</div>
	<div class="s"><textarea rows="4" cols="38" name="mesagxo"></textarea></div>
	<input type="submit" value="<?= __('Sendi') ?>"/>
	</form>
	</div>
	<?
}
?>
</div>
<?
PHP::rootInclude('layout/bottom_html_page.php');
?>