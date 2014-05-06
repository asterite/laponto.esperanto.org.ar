<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Response');
PHP::requireCustom('Film', 'FilmUser', 'DateUtils', 'Display');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

PHP::rootInclude('layout/top.php');
?>
<div id="descripcion">
<div id="subtitulo"><?= __('Mia konto') ?></div>
<?= __('pri.mia.konto') ?>
</div>
<?
global $menu_left;
$menu_left = array(array(__('Krei filmon'), 'krei_filmon.php', 'img/crear.gif'));
PHP::rootInclude('layout/menu.php');
?>
<div id="listadoDeMisPeliculas">
<div><?= __('Filmoj, en kiuj vi kunlaboras') ?>:</div>
<ul>
<?
$man = new FilmManager();
$man->addOrder('name');
$man->addJoin('subtitolu_film', 'subtitolu_film_participant', 'id', 'film_id');
$man->addWhereField('subtitolu_film_participant.user_id', '=', $user->id, SQL_TYPE_INTEGER);
$man->query();

if ($man->hasNext()) {
	while($film = $man->next()) {
		?>
		<li><?= displayFilm($film) ?></li>
		<?
	}
} else {
	?>
	<li><?= __('Neniu_filmo') ?>.</li>
	<?
}
?>
</ul>
</div>
<?
PHP::rootInclude('layout/buscador.php');
PHP::rootInclude('layout/bottom.php');
?>
