<?
require_once('../_p/classes/PHP.php');
PHP::requireClasses('ControlPanel/List');
requireRenderers('Label');
PHP::requireCustom('Language');

include('man.inc');

$ops = new ListPageOptions($man);
$ops->setResultsPerPage(100);
$ops->setDefaultOrder('key', true);

$ops->addSearch('Clave', new ArrayBrowserSubstringSearchCriteria('key'));

$ops->addColumn('Clave', 'key', new LabelRenderer(), 'key');

/*
foreach($languages as $lang) {
	$ops->addColumn($lang->name, 'value["'.$lang->code.'"]', new LabelRenderer());
}
*/

$ops->setInsertPage('edit.php');
$ops->setEditPage('edit.php');
$ops->setDeletePage('delete.do.php');

$ops->setTitle('Traducciones');

PHP::rootInclude('_admin/templates/list/template.php');
?>