<?
global $edit;
$description = $edit->getDescriptionProperties();

global $title;
$title = $description->getTitle();
global $instructions;
$instructions = $description->getInstructions();
global $help_page;
$help_page = $description->getHelpPage();

PHP::rootInclude('interfaco/templates/general/description.php');
?>