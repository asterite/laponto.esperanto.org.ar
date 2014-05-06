<?
require_once('../_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response');
PHP::requireCustom('Film', 'FilmUser', 'Display');

$id = Request::getParameter('id');

global $film;
$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $id));

if (!$id or !$film) {
	Response::sendRedirect('index.php');
}

PHP::rootInclude('layout/top.php');
?>
<div id="descripcion">
<?
include('../layout/film_header.php');
?>
<?= __('pri.elsxuti.subtitolojn') ?> 
</div>
<?
global $menu_left;
$menu_left = array(
	array(__('Reen al {x}', array('x' => $film->name)), '../filmo.php?id=' . $film->id, '../img/film.png'),
);
PHP::rootInclude('layout/menu.php');	
?>
<form action="index.do.php" method="post">
<input type="hidden" name="id" value="<?= $film->id ?>"/>
<div class="s"><?= __('Antauxmeti la nombron de la subtitolo en cxiu subtitolo') ?>:</div>
<select name="montriNumerojn">
	<option value=""><?= __('Ne') ?></option>
	<option value="on"><?= __('Jes') ?></option>
</select><br/><br/>
<?
include("index_{$film->format}.php");
?>
<input type="submit" value="<?= __('Elsxuti') ?>"/>
</form>
<?
PHP::rootInclude('layout/bottom.php');
?>
