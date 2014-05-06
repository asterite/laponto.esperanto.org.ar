<?
PHP::requireClasses('File');

/**
 * This class provides static methods to access the properties and settings
 * of the currently running web application.
 * Configuration options can be setted up in the file "web.ini", that
 * must be located in '_p/web.ini'.
 * The only instance of this class is saved in the global var $application.
 *
 * @package Common
 */
class WebApplication {

	/**#@+
	 * @access private
	 */
	var $_sections;
	var $_attributes;
	var $_include_dir;
	/**#@-*/

	/**
	 * This constructor is called once per web application and
	 * the singleton instance is saved in the global $application var.
	 */
	function WebApplication() {
		$this->_attributes = array();
		$sections = FileUtils::parseIniFile(PHP::realPath('_p/web.ini'), true);
		foreach($sections as $section_name => $values) {
			$section = new IniSection($section_name);
			if (is_array($values)) {
				foreach($values as $key => $value) {
					$section->addParameter($key, $value);
				}
			}
			$this->_sections[$section_name] = $section;
		}
	}

	/**
	 * Gets an ini section from the web.ini file.
	 * @param string $name the name of the section
	 * @return IniSection the ini section if found, or null
	 */
	function getIniSection($name) {
		return $this->_sections[$name];
	}

	/**
	 * Shortcut method to obtain a single parameter in a section of the web.ini file.
	 * Dies if the section or the parameter don't exist.
	 * @param string $section the name of the section
	 * @param string $name the name of the parameter
	 * @return string the parameter
	 */
	function getIniParameter($section, $name) {
		global $application;
		$s = $application->getIniSection($section);
		if (!$s) die('Fatal Error: the section [' . $section . '] dosen\'t exists in the web.ini file');
		$p = $s->getParameter($name);
		if (!$p) die('Fatal Error: the parmeter ' . $name . ' in the section [' . $section . '] dosen\'t exist in the web.ini file');
		return $p;
	}

	/**
	 * Binds an attribute to the global $application variable.
	 * @param string $name the name of the attribute
	 * @param midex $value the value of the attribute
	 */
	function setAttribute($name, $value) {
		$this->_attributes[$name] = $value;
	}

	/**
	 * Gets an attributes previously binded to the global $application variable.
	 * @param string $name the name of the attribute
	 * @return mixed the value of the attribute
	 */
	function getAttribute($name) {
		return $this->_attributes[$name];
	}

	/**
	 * Returns the document root of the application, as defined in the web.ini
	 * in the [base] section as the document_root parameter.
	 * @return string the document root
	 */
	function getDocumentRoot() {
		$base = $this->_getBase();
		$root = $base->getParameter('document_root');
		if (!$root) {
			print 'Fatal Error: the document_root parameter was not found on the [base] entry in the web.ini file';
			die();
		}
		PHP::requireClasses('File');
		FileUtils::ensureDir($root);
		return $root;
	}

	/**
	 * Returns the url of the application, as defined in the web.ini
	 * in the [base] section as the url parameter.
	 * @return string the document root
	 */
	function getURL() {
		$base = $this->_getBase();
		$url = $base->getParameter('url');
		if (!$url) {
			print 'Fatal Error: the url parameter was not found on the [base] entry in the web.ini file';
			die();
		}
		return $url;
	}

	/** @access private */
	function _getBase() {
		$base = $this->getIniSection('base');
		if (!$base) {
			print 'Fatal Error: the [base] entry was not found on the web.ini file';
			die();
		}
		return $base;
	}

}

/**
 * This class represents a section of an ini file.
 * @package Common
 */
class IniSection {

	/**#@+
	 * @access private
	 */
	var $_name;
	var $_parameters;
	/**#@-*/

	/**
	 * Constructs an IniSection with a name. You obtain an instance
	 * of an IniSection by invoking getIniSection on an instance
	 * of a WebApplication.
	 * @param string $name the name of the section
	 */
	function IniSection($name) {
		$this->_name = $name;
		$this->_parameters = array();
	}

	/**
	 * Adds a parameter to this section.
	 * @param string $name the name of the parameter
	 * @param string $value the value of the parameter
	 */
	function addParameter($name, $value) {
		$this->_parameters[$name] = $value;
	}

	/**
	 * Gets a parameter of this section.
	 * @param string $name the name of the parameter
	 * @return string the value of the parameter or null if not found
	 */
	function getParameter($name) {
		return $this->_parameters[$name];
	}

	/**
	 * Returns an associative array with the names and values of this section.
	 */
	function getParameters() {
		return $this->_parameters;
	}

}

global $application;
$application = new WebApplication();
?>