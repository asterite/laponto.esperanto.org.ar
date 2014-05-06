<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Response', 'JSValidator', 'JSValidator/RequiredRule');
PHP::requireCustom('FilmUser', 'Language');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

$man = new LanguageManager();
$man->addOrder('name');
$man->query();

$languages = $man->getArray();

PHP::rootInclude('layout/top.php');
?>
<div id="descripcion">
<div id="titulo"><?= __('Krei filmon') ?></div>
<?= __('pri.krei.filmon') ?>
</div>
<?
PHP::rootInclude('layout/menu.php');
?>
<form name="krei_filmon" action="krei_filmon.do.php" target="l" method="post" enctype="multipart/form-data" onSubmit="return testi_kreadon()">
<div class="s"><?= __('Nomo de la filmo') ?>:</div>
<div><input type="text" name="name" size="40"/></div><br/>
<div class="s"><?= __('En kiu lingvo estas la filmo kaj al kiu lingvo vi tradukos gxin') ?>:</div>
<div><select name="lingvo" onChange="sxangxiKodon(this)">
<?
foreach($languages as $lang) {
	if ($lang->code == 'eo') continue;
	?>
	<option id="<?= $lang->encoding ?>" value="<?= $lang->id ?>-1"><?= $lang->name ?> --> Esperanto</option>
	<?
}
?>
<option value="">----------------------------------</option>
<?
foreach($languages as $lang) {
	if ($lang->code == 'eo') continue;
	?>
	<option id="ISO-8859-1" value="1-<?= $lang->id ?>">Esperanto --> <?= $lang->name ?></option>
	<?
}
?>
</select></div><br/>
<div class="s"><?= __('Subtitol-dosiero') ?>:</div>
<div><input type="file" name="file"/></div><br/>
<div class="s"><?= __('Literkodo de la dosiero') ?> (<a href="javascript:montriLiterkodhelpon()"><?= __('kiel mi povas scii?') ?></a>):</div>
<div><select id="kodo" name="kodo">
	<option value="UTF-8">UTF-8</option>
	<option value="ISO-8859-1" selected>ISO-8859-1</option>
	<option value="ISO-8859-8">ISO-8859-8</option>
	<option value="CP1250">CP-1250</option>
	<option value="CP1251">CP-1251</option>
	<option value="CP1252">CP-1252</option>
</select></div><br/>
<div class="s"><?= __('Publika') ?>: <a href="javascript:montriPublikhelpon()">?</a></div>
<div><select name="publika">
	<option value="on" selected><?= __('Jes') ?></option>
	<option value=""><?= __('Ne') ?></option>
</select></div><br/>
<div class="s"><?= __('Traduk-reguloj') ?>:</div>
<textarea rows="5" cols="50" name="rules"></textarea><br/><br/>
<input type="submit" value="<?= __('Bone') ?>"/>
</form>

<?
$validator = new JSValidator('krei_filmon', 'testi_kreadon');

$rule = new RequiredRule('lingvo');
$rule->setOnRequiredError('alert("' . __('Vi devas elekti la lingvojn') . '.")');
$validator->addValidationRule($rule);

global $html_page;
$html_page->addHeader($validator->getJSCode());

PHP::rootInclude('layout/bottom.php');
?>