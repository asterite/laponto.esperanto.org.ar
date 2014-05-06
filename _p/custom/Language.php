<?
PHP::requireClasses('DataObjects', 'File', 'Session');

global $_resources_;

function __($key, $parameters = null) {
	global $_resources_;
	if (!$_resources_) {
		PHP::requireClasses('ResourceBundle2');
		$_resources_ = new ResourceBundle2('resources', LanguageManager::getSessionLanguage());
	}
	return $_resources_->get($key, $parameters);
}

class Language {

	var $id;
	var $code;
	var $name;
	var $name_language;
	var $encoding;
	var $rtl;
	var $translated;

	function getInputEvents() {
		switch($this->code) {
			case 'eo':
				return 'onKeyUp="xAlUtf8(this)"';
			default:
				'';
		}
	}

	function getIni() {
  	global $context_path;
  	return FileUtils::parseIniFile(PHP::realPath('_p/resources/resources_' . $this->code . '.ini'));
	}

	function isTranslated() {
  	$ini = $this->getIni();
  	foreach($ini as $key => $value) {
      if (!trim($ini[$key])) {
        return false;
      }
    }
    return true;
	}

}

class LanguageManager extends DataObjectsManager {

	function _getDataObjectInfo() {
		$table = new Table('subtitolu_language');
		$table->addField(new Field('id', SQL_TYPE_INTEGER, true, true));
		$table->addField(new Field('code', SQL_TYPE_STRING));
		$table->addField(new Field('name', SQL_TYPE_STRING));
		$table->addField(new Field('name_language', SQL_TYPE_STRING));
		$table->addField(new Field('encoding', SQL_TYPE_STRING));
		$table->addField(new Field('rtl', SQL_TYPE_BOOLEAN));
		$table->addField(new Field('translated', SQL_TYPE_BOOLEAN));
		$doi = new DataObjectInfo($table, 'Language');
		return $doi;
	}

	function getDefaultLanguage() {
		return 'eo';
	}

	function setSessionLanguage($language) {
  	$man = new LanguageManager();
		$man->addWhereField('translated', '=', true);
		$man->addWhereField('code', '=', $language);
		$man->query();

		if ($man->hasNext()) {
		  Session::setAttribute(LANGUAGE_SESSION_NAME, $language);
		  return $language;
	  } else {
  	  return 'eo';
	  }
	}

	function getSessionLanguage() {
		$lang = Session::getAttribute(LANGUAGE_SESSION_NAME);
		if (!$lang) {
			$lang = LanguageManager::getDefaultLanguage();
			LanguageManager::setSessionLanguage($lang);
		}
		return $lang;
	}

	function getFromKeys($keys) {
		// Traigo las claves
		$do_keys = array_keys($keys);

		if (!is_null(Arrays::search($do_keys, 'id'))) {
			$id = $keys['id'];

			PHP::requireClasses('Cache');
			$cache = new Cache('_OBJECT_LANGUAGE_ID_' . $id);

			if ($cache->found()) {
				return $cache->contents();
			} else {
				$section = parent::getFromKeys($keys);
				$cache->put($section);
				return $section;
			}
		} else {
			return parent::getFromKeys($keys);
		}
	}

}
?>
