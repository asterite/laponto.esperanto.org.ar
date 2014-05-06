<?
global $list;
$description = $list->getDescriptionProperties();

global $title;
$title = $description->getTitle();
global $instructions;
$instructions = $description->getInstructions();
global $help_page;
$help_page = $description->getHelpPage();

PHP::rootInclude('_admin/templates/general/description.php');
?>