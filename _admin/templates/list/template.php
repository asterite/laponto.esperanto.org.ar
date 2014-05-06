<?
global $list, $ops;
$list = $ops->getListPage();

PHP::rootInclude('_admin/templates/general/top.php');

PHP::rootInclude('_admin/templates/list/description.php');
PHP::rootInclude('_admin/templates/list/search.php');
PHP::rootInclude('_admin/templates/list/pages.php');
PHP::rootInclude('_admin/templates/list/grid.php');
PHP::rootInclude('_admin/templates/list/pages.php');

PHP::rootInclude('_admin/templates/general/bottom.php');
?>