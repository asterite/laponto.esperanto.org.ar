<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Response');
PHP::requireCustom('Film', 'FilmUser');

if (UserManager::getRemembered()) {
	Response::sendRedirect('hejmo.php');
}

PHP::rootInclude('layout/top.php');
?>
<div id="descripcionIndex">
<?= __('pri.la.ponto') ?>
</div>
<br/>
<form action="ensaluti.do.php" target="l" method="post">
<div id="registracion">
<div class="s"><strong><?= __('Registritaj uzantoj') ?></strong></div>
<div class="s"><label for="operacionLogin"><input type="radio" id="operacionLogin" name="operacion" value="login" onClick="kasxiPasvorton()" checked="checked"/><?= __('Ensaluto') ?></label> <label for="operacionRegistro"><input type="radio" name="operacion" id="operacionRegistro" value="registro" onClick="montriPasvorton()"/><?= __('Mi estas nova') ?></label></div>
<div class="s"><?= __('Kromnomo') ?>:</div>
<div class="s"><input type="text" name="nickname" style="width:160px"/></div>
<div class="s"><?= __('Pasvorto') ?>:</div>
<div class="s"><input type="password" name="password" style="width:160px"/></div>
<div id="repetirClave" class="s" style="display:none"><?= __('Retajpu la pasvorton') ?>:</div>
<div id="repetirClave2" class="s" style="display:none"><input type="password" name="password2" size="14" style="width:160px"/></div>
<div><input type="checkbox" checked="checked" name="recordame"/> <?= __('Memoru min') ?></div>
<div align="right"><input type="submit" value="<?= __('Eniri') ?>"/></div>
</div>
</form>
<div id="intro">
<?= __('pri.la.ponto.longa') ?>
</div>
<?
PHP::rootInclude('layout/buscador.php');
PHP::rootInclude('layout/bottom.php');
?>
