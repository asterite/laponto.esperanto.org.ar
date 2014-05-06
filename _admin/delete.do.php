<?
require_once('../_p/classes/PHP.php');
PHP::requireClasses('ControlPanel/Delete');
PHP::requireCustom('Language');

$man = new LanguageManager();
$man->query();
$languages = $man->getArray();
foreach($languages as $lang) {
	$inis = FileUtils::parseIniFile(PHP::realPath("_p/resources/resources_{$lang->code}.ini"));
	unset($inis[Request::getParameter('key')]);
	FileUtils::writeIniFile(PHP::realPath("_p/resources/resources_{$lang->code}.ini"), $inis);
}

$delete = new DeletePage();
$delete->goToPage('index.php');
?>