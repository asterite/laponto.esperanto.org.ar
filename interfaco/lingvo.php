<?
require_once('../_p/classes/PHP.php');
PHP::requireClasses('Request', 'ControlPanel/List');
PHP::requireCustom('Language');
requireRenderers('Label', 'BooleanImage');

include('man.inc');

$ops = new ListPageOptions($man);
$ops->setResultsPerPage(25);
$ops->setDefaultOrder('key', true);

$ops->addColumn('Frazo', 'key', new LabelRenderer(), 'key');
$ops->addColumn('Tradukita', 'translated', new BooleanImageRenderer("$context_path/interfaco/images/si.gif", "$context_path/interfaco/images/no.gif"));

$ops->setEditPage('edit.php');

global $theName, $thePercentage;
$ops->setTitle('Tradukoj de la ' . $theName . ' lingvo % ' . $thePercentage);

$ops->addExtraParameter('id', $id);


PHP::rootInclude('interfaco/templates/list/template.php');
?>