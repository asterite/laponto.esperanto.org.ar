<?
require_once('../_p/classes/PHP.php');
PHP::requireClasses('ControlPanel/Edit');
requireRenderers('TextBox', 'Label', 'TextArea');
PHP::requireCustom('Language');

include('man.inc');

global $theCode, $theName;

$ops = new EditPageOptions($man);

$ops->addRow('Frazo', 'key', 'key', new LabelRenderer());
$ops->addRow('Esperanto', 'value["eo"]', "value_eo", new TextAreaRenderer(80, 10, true));
$ops->addRow($theName, 'value["' . $theCode . '"]', "value_" . $theCode, new TextAreaRenderer(80, 10));

$ops->setActionPage('edit.do.php');
$ops->setCancelPage('javascript: history.back()');

global $theName, $thePercentage;
$ops->setTitle('Tradukoj de la ' . $theName . ' lingvo % ' . $thePercentage);

$ops->addExtraParameter('id', Request::getParameter('id'));

PHP::rootInclude('interfaco/templates/edit/template.php');
?>