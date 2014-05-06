<?
global $list, $ops;
$list = $ops->getListPage();

PHP::rootInclude('interfaco/templates/general/top.php');

PHP::rootInclude('interfaco/templates/list/description.php');
PHP::rootInclude('interfaco/templates/list/search.php');
PHP::rootInclude('interfaco/templates/list/pages.php');
PHP::rootInclude('interfaco/templates/list/grid.php');
PHP::rootInclude('interfaco/templates/list/pages.php');

PHP::rootInclude('interfaco/templates/general/bottom.php');
?>