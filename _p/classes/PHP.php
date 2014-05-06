<?
/**
 * The path to the context, with a leading slash and without a trailing slash
 * Empty if the application context path is root.
 */
global $context_path;
$context_path = '';

/**
 * Main class of the PHP framework.
 *
 * Since PHP dosen't support includes relative to the context root path,
 * this class is a helper to do that.
 *
 * Also, this class provides methods to avoid incompatibilities between
 * diferent versiones of PHP.
 *
 * If a section in the web.ini file (see WebApplication.php) is defined under
 * the name "PHP_pre_filters", then pre filters can be simmulated.
 *
 * If a section is defined under the name "PHP_post_filters",
 * then post filters can be simmulated.
 *
 * The names are the pages under the filters, and the values are the pages that act as filters, separated
 * by commas.
 * For example:
 *
 * <pre>
 * web.ini:
 * [PHP_pre_filters]
 * /index.php = /filter.php, /filter2.php
 * </pre>
 *
 * When calling /index.php, the pages /filter.php and /filter2.php will be included.
 *
 *
 * Another example:
 *
 * <pre>
 * web.ini:
 * [PHP_pre_filters]
 * /_secret/* = /secret_filter.php
 * </pre>
 *
 * When calling any page under the directory /_secret/, the file /secret_filter.php
 * will be included.
 *
 *
 *
 * Note that this only works on main files (not included files).
 *
 * <b>You MUST include this file in all your main files (that is, not included files)
 * in order to other classes and files work well. Also, you must call</b>
 *
 * <code>
 * PHP::end()
 * </code>
 *
 * <b>at the end of the page if you plan to use post filters
 * (and any other features that may be added later in time).</b>
 *
 * Note that files included via this class's methods looses the global
 * variables defined in super scripts (because the includes are made inside
 * methods scope). To aquire those variables use the global
 * keyword. (This realy helps to determine which variables come from a super
 * script and which does not).
 *
 * This class buffers all contents so that using session, headers or any of the
 * kind will never bring trouble (and if the content can be compressed, it
 * will be compressed and sent to the browser!).
 *
 * @package Common
 * @author Ary Manzana <asterite@hotmail.com>
 * @static
 */
class PHP {

	/**
	 * Includes a file located in the context root. The file may
	 * be in a directory.
	 * @param string $file the file
	 * @static
	 */
	function rootInclude($file) {
		$file = PHP::realPath($file);
		include($file);
	}

	/**
	 * Includes once a file located in the context root. The file may
	 * be in a directory.
	 * @param string $file the file
	 * @static
	 */
	function rootIncludeOnce($file) {
		$file = PHP::realPath($file);
		include_once($file);
	}

	/**
	 * Requires a file located in the context root. The file may
	 * be in a directory.
	 * @param string $file the file
	 * @static
	 */
	function rootRequire($file) {
		$file = PHP::realPath($file);
		require($file);
	}

	/**
	 * Requires once a file located in the context root. The file may
	 * be in a directory.
	 * @param string $file the file
	 * @static
	 */
	function rootRequireOnce($file) {
		$file = PHP::realPath($file);
		require_once($file);
	}

	/**
	 * Calls PHP::requireClasses('_p/classes/' . $path . '.php');
	 * for each $path supplied (the arguments of the method).
	 * @static
	 */
	function requireClasses($path) {
		global $___required_classes;
		$args = func_get_args();
		foreach($args as $arg) {
			if (!$___required_classes[$arg]) {
				PHP::rootRequireOnce('_p/classes/' . $arg . '.php');
				$___required_classes[$arg] = true;
			}
		}
	}

	/**
	 * Calls PHP::requireCustom('_p/custom/' . $path . '.php');
	 * for each $path supplied (the arguments of the method).
	 * @static
	 */
	function requireCustom($path) {
		$args = func_get_args();
		foreach($args as $arg) {
			if (!$___required_custom[$arg]) {
				PHP::rootRequireOnce('_p/custom/' . $arg . '.php');
				$___required_custom[$arg] = true;
			}
		}
	}

	/**
	 * Given a file relative to the document root, for example: '/this/path/file.pgp'
	 * and located in some other file, for example: '/other/path/file.pgp',
	 * this method returns the path translated to '../../other/path/file.php'.
	 * @param string $file a file relative to the document root
	 * @return string see method description
	 * @static
	*/
	function realPath($file) {
		if (substr($file, 0, 1) == '/') $file = substr($file, 1);
		$uri = PHP::getServerParameter('SCRIPT_NAME');
		$slashes = substr_count($uri, '/') - 1;
		global $context_path;
		$context_path_slashes = substr_count($context_path, '/');
		for ($i = 0; $i < $slashes - $context_path_slashes; $i++) {
			$file = '../' . $file;
		}
		return $file;
	}

	/**
	 * Gets a variable from the $_SERVER or the $HTTP_SERVER_VARS variables,
	 * depending on which is defined.
	 * @param string $name the name of an attribute associated to the server var
	 * @return mixed the value of the attribute
	 * @static
	 */
	function getServerParameter($name) {
		$var = $_SERVER[$name];
		if (isset($var)) {
			return $var;
		}
		global $HTTP_SERVER_VARS;
		$var =  $HTTP_SERVER_VARS[$name];
		return $var;
	}

	/**
	 * Gets a variable from the $_REQUEST, $HTTP_GET_VARS, $HTTP_POST_VARS or
	 * $HTTP_POST_FILES variables, depending on which is defined.
	 * @param string $name the name of an attribute associated to the request var
	 * @return mixed the value of the attribute
	 * @static
	 */
	function getRequestParameter($name) {
		$var = $_REQUEST[$name];
		if (isset($var)) {
			return $var;
		}
		global $HTTP_GET_VARS;
		$var = $HTTP_GET_VARS[$name];
		if (isset($var)) {
			return $var;
		}
		global $HTTP_POST_VARS;
		$var = $HTTP_POST_VARS[$name];
		if (isset($var)) {
			return $var;
		}
		global $HTTP_POST_FILES;
		$var = $HTTP_POST_FILES[$name];
		return $var;
	}

	/**
	 * Gets a variable from the $_SESSION or the $HTTP_SESSION_VARS variables,
	 * depending on which is defined.
	 * @param string $name the name of an attribute associated to the session var
	 * @return mixed the value of the attribute
	 * @static
	*/
	function getSessionParameter($name) {
		$var = $_SESSION[$name];
		if (isset($var)) {
			return $var;
		}
		global $HTTP_SESSION_VARS;
		$var =  $HTTP_SESSION_VARS[$name];
		return $var;
	}

	/**
	 * Gets a variable from the $_COOKIE or the $HTTP_COOKIE_VARS variables,
	 * depending on which is defined.
	 * @param string $name the name of an attribute associated to the cookie var
	 * @return mixed the value of the attribute
	 * @static
	 */
	function getCookieParameter($name) {
		$var = $_COOKIE[$name];
		if (isset($var)) {
			return $var;
		}
		global $HTTP_COOKIE_VARS;
		$var =  $HTTP_COOKIE_VARS[$name];
		return $var;
	}

	/**
	 * Sets a variable in the $_SESSION and $HTTP_SESSION_VARS variables.
	 * @param string $name the name of an attribute
	 * @param mixed $value the value of an attribute
	 * @static
	 */
	function setSessionParameter($name, $value) {
		if (is_null($value)) {
			session_unregister($name);
		} else {
			$_SESSION[$name] = $value;
			global $HTTP_SESSION_VARS;
			$HTTP_SESSION_VARS[$name] = $value;
		}
	}

	/**
	 * @access private
	 */
	function _checkFilters($pre_or_post) {
		$uri = PHP::getServerParameter('SCRIPT_NAME');
		global $application;
		$filters = $application->getIniSection("PHP_{$pre_or_post}_filters");
		if ($filters) {
			foreach($filters->getParameters() as $page => $filter) {
				$filter = explode(',', $filter);
				if ($uri == $page) {
					global $_php_buffer;
					foreach($filter as $f) {
						PHP::rootInclude(trim($f));
					}
				} else {
					if (substr($page, strlen($page) - 1) == '*') {
						$page2 = substr($page, 0, strlen($page) - 1);
						if (substr($uri, 0, strlen($page2)) == $page2) {
							foreach($filter as $f) {
								PHP::rootInclude(trim($f));
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Returns the buffer contents of the page.
	 * Usefull in post filter (together with PHP::cleanBufferContents())
	 * for performing transofrmations to the contents of the page.
	 *
	 * Example:
	 * <code>
	 * // to_uppercase_filter.php
	 * $contents = PHP::getBufferContents();
	 * PHP::cleanBufferContents();
	 * // Upper case the whole page
	 * print strtoupper($contents);
	 * </code>
	 *
	 * @return string the contents of the buffer
	 */
	function getBufferContents() {
		return ob_get_contents();
	}

	/**
	 * Cleans the contents of the buffer of the page.
	 */
	function cleanBufferContents() {
		ob_clean();
	}

	/**
	 * This function must be called at the end of the script for
	 * post filters to work.
	 */
	function end() {
		PHP::_checkFilters('post');
	}

}

global $___required_classes;
global $___required_custom;
$___required_classes = array();
$___required_custom = array();

/**#@+
 * @access private
 */
function _PHP_end_get_encoding() {
	// Mejor lo deshabilito para poder tener el "framework" en los foros
	//$encoding = PHP::getServerParameter('HTTP_ACCEPT_ENCODING');
	//if (strpos($encoding, 'x-gzip') !== false) return "x-gzip";
	//if (strpos($encoding, 'gzip') !== false) return "gzip";
	return false;
}

function _PHP_end($buffer) {
	$encoding = _PHP_end_get_encoding();
	if ($encoding) {
		$size = strlen($buffer);
		$crc = crc32($buffer);
		$buffer = gzcompress($buffer, 9);
		$buffer = substr($buffer, 0, strlen($buffer) - 4);
		$buffer .= pack('V',$crc);
		$buffer .= pack('V',$size);
		$buffer = "\x1f\x8b\x08\x00\x00\x00\x00\x00" . $buffer;
	}
	return $buffer;
}
/**#@-*/

$encoding = _PHP_end_get_encoding();
if ($encoding) header("Content-Encoding: $encoding");

ob_start('_PHP_end');

PHP::requireClasses('WebApplication');
PHP::_checkFilters('pre');
?>
