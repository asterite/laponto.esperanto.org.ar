<?
PHP::requireClasses('Request', 'JSValidator', 'JSValidator/RequiredRule');
PHP::requireCustom('Language');

list($from_language, $to_language) = explode('-', Request::getParameter('lingvo')); 
$name = Request::getParameter('name');

$man = new LanguageManager();
$man->addOrder('name');
$man->query();

$languages = $man->getArray();
?>
<form name="sercxi_filmon" action="sercxi_filmon.php" method="get" onSubmit="return testiSercxon()">
<div id="buscadorPeliculas">
<div class="s"><strong><?= __('Sercxi filmojn') ?></strong></div>
<div class="s"><?= __('Lingvo') ?>:</div>
<select name="lingvo" class="s">
<option value="-"
<?
if (!$from_language and !$to_language) print 'selected="selected"';
?>
><?= __('Ajna') ?> --> <?= __('Ajna') ?></option>
<option value="">----------------------------------</option>
<option value="-1"
<?
if (!$from_language and $to_language == 1) print 'selected="selected"';
?>
><?= __('Ajna') ?> --> Esperanto</option>
<option value="1-"
<?
if ($from_language == 1 and !$to_language) print 'selected="selected"';
?>
>Esperanto --> <?= __('Ajna') ?></option>
<option value="">----------------------------------</option>
<?
foreach($languages as $lang) {
	if ($lang->code == 'eo') continue;
	?>
	<option value="<?= $lang->id ?>-1"
	<?
	if ($from_language == $lang->id and $to_language == 1) print 'selected="selected"';
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
	<option value="1-<?= $lang->id ?>"
	<?
	if ($from_language == 1 and $to_language == $lang->id) print 'selected="selected"';
	?>
	>Esperanto --> <?= $lang->name ?></option>
	<?
}
?>
</select>
<div class="s"><?= __('Parto de la nomo de la filmo') ?>:</div>
<div class="s"><input type="text" name="name" value="<?= htmlspecialchars($name) ?>"/></div>
<div align="right"><input type="submit" value="<?= __('Sercxi') ?>"/></div>
</div>
</form>
<div style="clear:left"></div>
<?
$validator = new JSValidator('sercxi_filmon', 'testiSercxon');

$rule = new RequiredRule('lingvo');
$rule->setOnRequiredError('alert("' . __('Vi devas elekti la lingvojn') . '.")');
$validator->addValidationRule($rule);

global $html_page;
$html_page->addHeader($validator->getJSCode());
?>