<?
global $edit, $ops;
$edit = $ops->getEditPage();

PHP::rootInclude('interfaco/templates/general/top.php');

PHP::rootInclude('interfaco/templates/edit/description.php');
PHP::rootInclude('interfaco/templates/edit/form.php');

PHP::rootInclude('interfaco/templates/general/bottom.php');
?>