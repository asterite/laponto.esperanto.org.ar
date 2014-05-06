<?
PHP::requireClasses('File');

/**
 * Resource bundles contain locale-specific objects. When your program needs a
 * locale-specific resource your program can load it from the resource bundle that is appropriate
 * for a language. In this way, you can write program code that is largely independent of the
 * user's locale isolating most, if not all, of the locale-specific information in resource bundles.
 *
 * The resources are obtained from ini files located in a specified path.
 *
 * @package I18N
 */
class ResourceBundle2 {

	/**#@+
	 * @access private
	 */
	var $name;
	var $language_key;
	var $path;
	var $default;
	var $resources;
	/**#@-*/

	/**
	 * Constructs a ResourceBundle. There must be a file containing the properties
	 * for this bundle and it must be located in $path. If not specified, the path
	 * is '_p/resources/'. The name of the file must be {$name}.ini if no
	 * language_key is provided, else {$name}_{$language_key}.ini.
	 * @param string $name the name of the resource bundle
	 * @param string $language_key the language of the resource
	 * @param string $path the path to the resource file
	 */
	function ResourceBundle2($name, $language_key = null, $path = null) {
		if (is_null($path)) {
			$path = '_p/resources/';
		} else {
			PHP::requireClasses('File');
			FileUtils::ensureDir($path);
		}
		if (is_null($language_key)) {
			// Busco el archivo por defecto
			$file = PHP::realPath($path . $name . '.ini');
		} else {
			// Busco el archivo del lenguaje
			$file = PHP::realPath($path . $name . '_' . $language_key . '.ini');
			if (!file_exists($file)) {
				die("<font color='red'>Fatal Error:</font> Cant find Resource Bundle for name <i>$name</i> in path <i>$path</i>");
			}
		}
		if (!file_exists($file)) {
			$die = "<font color='red'>Fatal Error:</font> Cant find Resource Bundle for name <i>$name</i>";
			if (!is_null($language_key)) $die .= ", language <i>$language_key</i>,";
			$die .= " in path <i>$path</i>";
			die($die);
		}
		$this->resources = FileUtils::parseIniFile($file);
		$this->name = $name;
		$this->language_key = $language_key;
		$this->path = $path;
	}

	/**
	 * Gets a property from this bundle.
	 * @param key the key to obtain. If the value for the key is an empty string then the key
	 * itself is returned.
	 * @param $parameters array (optional) associative array containing keywords to replace
	 * in the value returned. The keywords in the ini file must be enclosed in { }.
	 * @return string the property value
	 */
	function get($key, $parameters = null) {
		if (!array_key_exists($key, $this->resources)) {
			$die = "<font color='red'>Fatal Error:</font> Key $key not found in bundle <i>{$this->name}</i>";
			if (!is_null($this->language_key)) $die .= ", language <i>{$this->language_key}</i>,";
			$die .= " in path <i>{$this->path}</i>";
			die($die);
		}
		$value = $this->resources{$key};
		if (!$value) return $key;
		if (!is_null($parameters)) {
			foreach($parameters as $pkey => $pvalue) {
				$value = str_replace('{' . $pkey . '}', $pvalue, $value);
			}
		}
		return $value;
	}

	/**
	 * Alias of get($key, $parameters = null).
	 */
	function _($key, $parameters = null) {
		return $this->get($key, $parameters);
	}

}
?>
