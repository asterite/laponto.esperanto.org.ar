<?
global $edit, $ops;
$edit = $ops->getEditPage();

PHP::rootInclude('_admin/templates/general/top.php');

PHP::rootInclude('_admin/templates/edit/description.php');
PHP::rootInclude('_admin/templates/edit/form.php');

PHP::rootInclude('_admin/templates/general/bottom.php');
?>