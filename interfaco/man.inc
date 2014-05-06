<?
PHP::requireClasses('ControlPanel/Browsers/ArrayBrowser', 'Request');
PHP::requireCustom('Language');

$id = Request::getParameter('id');

global $theName, $theCode, $thePercentage;

$man = new LanguageManager();
$man->query();
$languages = $man->getArray();

$inis = array();
foreach($languages as $lang) {
  if ($id == $lang->id) {
    $theCode = $lang->code;
    $theName = $lang->name;
  }
	$inis[$lang->code] = FileUtils::parseIniFile(PHP::realPath("_p/resources/resources_{$lang->code}.ini"));
}

$count = 0;
$array = array();
foreach($inis[$lang->code] as $key => $value) {
	$ini = new stdClass();
	$ini->key = $key;
	$ini->value = array();
	foreach($languages as $lang) {
		$ini->value[$lang->code] = $inis[$lang->code][$key];
		if ($lang->code == $theCode) {
  		if (trim($ini->value[$lang->code])) {
    		$count++;
  		}
		}
		if ($lang->code == 'eo') {
  		if (!trim($ini->value[$lang->code])) {
    		$ini->value[$lang->code] = $key;
  		}
		}
	}
	$ini->translated = trim($ini->value[$theCode]);
	array_push($array, $ini);
}


$thePercentage = round(100 * $count / sizeof($inis[$theCode]), 2);
$man = new ArrayBrowser($array, 'stdClass', array('key'));
?>