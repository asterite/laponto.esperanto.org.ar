<?
require_once('../_p/classes/PHP.php');
PHP::requireClasses('ControlPanel/Action');
PHP::requireCustom('Language');

$man = new LanguageManager();
$man->query();
$languages = $man->getArray();
foreach($languages as $lang) {
	$inis = FileUtils::parseIniFile(PHP::realPath("_p/resources/resources_{$lang->code}.ini"));
	
	$value = Request::getParameter("value_{$lang->code}");
	$value = str_replace("\r\n", "\n", $value);
	$value = str_replace("\n", " ", $value);
	$value = str_replace("\t", "", $value);

	$inis[Request::getParameter('key')] = $value;
	
	FileUtils::writeIniFile(PHP::realPath("_p/resources/resources_{$lang->code}.ini"), $inis);
}

$action = new ActionPage();
$action->goToPage('index.php');
?>