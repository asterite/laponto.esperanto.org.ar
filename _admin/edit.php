<?
require_once('../_p/classes/PHP.php');
PHP::requireClasses('ControlPanel/Edit');
requireRenderers('TextBox', 'Label', 'TextArea');
PHP::requireCustom('Language');

include('man.inc');

$ops = new EditPageOptions($man);

if ($ops->isInEditMode()) {
	$ops->addRow('Clave', 'key', 'key', new LabelRenderer());
} else {
	$ops->addRow('Clave', 'key', 'key', new TextBoxRenderer(50, 255), null, 'Required');
}

foreach($languages as $lang) {
	$ops->addRow($lang->name, 'value["'.$lang->code.'"]', "value_{$lang->code}", new TextAreaRenderer(80, 10));
}

$ops->setActionPage('edit.do.php');
$ops->setCancelPage('javascript: history.back()');

$ops->setTitle('Traducciones');

PHP::rootInclude('_admin/templates/edit/template.php');
?>