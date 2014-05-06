<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response', 'JSValidator', 'JSValidator/RequiredRule');
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

$from_language = $film->getFromLanguage();
$to_language = $film->getToLanguage();

$status = $film->getStatus();
?>
<div id="descripcion">
<?
include('layout/film_header.php');
?>
<div><?= __('Agordoj de la filmo.') ?></div>
</div>
<?
global $menu_left;
$menu_left = array(
	array(__('Reen al {x}', array('x' => $film->name)), 'filmo.php?id=' . $film->id, 'img/film.png'),
);
PHP::rootInclude('layout/menu.php');
?>
<form name="sxangxi_filmon" action="filmagordoj.do.php" target="l" method="post" onSubmit="return testi_sxangxon()">
<input type="hidden" name="id" value="<?= $film->id ?>"/>
<div class="s"><?= __('Nomo de la filmo') ?>:</div>
<div><input type="text" name="name" size="40" value="<?= $film->name ?>"/></div><br/>
<div class="s">Publika: <a href="javascript:montriPublikhelpon()">?</a></div>
<div><select name="publika">
	<option value="on" <?= $film->public ? 'selected' : '' ?>><?= __('Jes') ?></option>
	<option value="" <?= $film->public ? '' : 'selected' ?>><?= __('Ne') ?></option>
</select></div><br/>
<div class="s"><?= __('En kiu lingvo estas la filmo kaj al kiu lingvo vi tradukos gxin') ?>:</div>
<div><select name="lingvo" onChange="sxangxiKodon(this)">
<?
$man = new LanguageManager();
$man->addOrder('name');
$man->query();

$languages = $man->getArray();

foreach($languages as $lang) {
	if ($lang->code == 'eo') continue;
	?>
	<option id="<?= $lang->encoding ?>" value="<?= $lang->id ?>-1"
	<?
	if ($to_language->code == 'eo' and $lang->code == $from_language->code) {
  	print 'selected';
	}
	?>
	><?= $lang->name ?> --> Esperanto</option>
	<?
}
?>
<option value="">----------------------------------</option>
<?
foreach($languages as $lang) {
	if ($lang->code == 'eo') continue;
	?>
	<option id="ISO-8859-1" value="1-<?= $lang->id ?>"
	<?
	if ($from_language->code == 'eo' and $lang->code == $to_language->code) {
  	print 'selected';
	}
	?>
	>Esperanto --> <?= $lang->name ?></option>
	<?
}
?>
</select></div><br/>
<input type="submit" value="<?= __('Bone') ?>"/>
</form>
<?
$validator = new JSValidator('sxangxi_filmon', 'testi_sxangxon');

$rule = new RequiredRule('lingvo');
$rule->setOnRequiredError('alert("' . __('Vi devas elekti la lingvojn') . '.")');
$validator->addValidationRule($rule);

global $html_page;
$html_page->addHeader($validator->getJSCode());

PHP::rootInclude('layout/bottom.php');
?>
