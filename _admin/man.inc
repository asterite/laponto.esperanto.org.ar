<?
PHP::requireClasses('ControlPanel/Browsers/ArrayBrowser');
PHP::requireCustom('Language');

$man = new LanguageManager();
$man->query();
$languages = $man->getArray();

$inis = array();
foreach($languages as $lang) {
	//if ($lang->code != 'eo' and $lang->code != 'es') continue;
	$inis[$lang->code] = FileUtils::parseIniFile(PHP::realPath("_p/resources/resources_{$lang->code}.ini"));
}

$array = array();
foreach($inis[$lang->code] as $key => $value) {
	$ini = new stdClass();
	$ini->key = $key;
	$ini->value = array();
	foreach($languages as $lang) {
		$ini->value[$lang->code] = $inis[$lang->code][$key];
	}
	array_push($array, $ini);
}

$man = new ArrayBrowser($array, 'stdClass', array('key'));
?>