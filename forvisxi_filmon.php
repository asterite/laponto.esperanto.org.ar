<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response');
PHP::requireCustom('Film', 'FilmUser', 'Display');

$id = Request::getParameter('id');

global $film;
$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $id));

if (!$id or !$film) {
	Response::sendRedirect('index.php');
}

$user = UserManager::getRemembered();
if (!$user or $user->id != $film->user_id) {
	Response::sendRedirect('index.php');
}

PHP::rootInclude('layout/top.php');
?>
<div id="descripcion">
<?
include('layout/film_header.php');
?>
<div style="color:red;font-weight:bold"><?= __('Forvisxi la filmon') ?></div>
</div>
<?
global $menu_left;
$menu_left = array(
	array(__('Reen al {x}', array('x' => $film->name)), 'filmo.php?id=' . $film->id, 'img/film.png'),
);
PHP::rootInclude('layout/menu.php');
?>
<div style="color:red"><?= __('Se vi forvisxas la filmon, cxio estos perdita kaj nemalperdebla') ?>.</div>
<div><?= __('Cxiuj partoprenantoj de la filmo ricevos mesagxon, kiu diros al ili ke la filmo estis forvisxita') ?>.</div><br/>
<form action="forvisxi_filmon.do.php" method="post" target="l">
<input type="hidden" name="film_id" value="<?= $film->id ?>"/>
<div class="s"><?= __('Por forvisxi la filmon, tajpu vian pasvorton') ?>:</div>
<div><input type="password" name="pasvorto"/></div><br/>
<div class="s"><?= __('Kial vi forvisxas la filmon?') ?>:</div>
<div><textarea name="kialo" rows="6" cols="50"/></textarea></div>(<?= __('cxi tiun mesagxon ricevos la partoprenantoj') ?>)<br/><br/>
<div><input type="submit" value="<?= __('Forvisxi por cxiam') ?>"/></div>
</form>
<?
PHP::rootInclude('layout/bottom.php');
?>
