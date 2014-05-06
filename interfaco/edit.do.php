<?
require_once('../_p/classes/PHP.php');
PHP::requireClasses('ControlPanel/Action');
PHP::requireCustom('Language');

$id = Request::getParameter('id');

$man = new LanguageManager();
$lang = $man->getFromKeys(array('id' => $id));

if (!$lang or $lang->code == 'eo') die();
	
$inis = FileUtils::parseIniFile(PHP::realPath("_p/resources/resources_{$lang->code}.ini"));
	
$value = Request::getParameter("value_{$lang->code}");
$value = str_replace("\r\n", "\n", $value);
$value = str_replace("\n", " ", $value);
$value = str_replace("\t", "", $value);

$inis[Request::getParameter('key')] = $value;

FileUtils::writeIniFile(PHP::realPath("_p/resources/resources_{$lang->code}.ini"), $inis);

$action = new ActionPage();
$action->addExtraParameter('id', $id);
$action->goToPage('lingvo.php');
?>