<?
PHP::requireClasses('File', 'Date');

/**
 * Class for caching both the content outputed in a page or variables and objects.
 * In order to work, a section in the web.ini file (see WebApplication.php)
 * under the name "cache" must be defined, with the following
 * parameters:
 *
 * <ul>
 *   <li>dir: the dir (relative to the DOCUMENT ROOT, for example '_p/_cache/')
 *     where the cache will be saved</li>
 *   <li>disabled: if true, cache never will be used.</li>
 * </ul>
 *
 * Usage for caching output:
 *
 * <code>
 * $cahce = new Cache("KEY");
 * // if there's a timeout
 * // $seconds = 3600;
 * // $cache->setTimeout($seconds);
 * if ($cache->found()) {
 * 	 print $cache->contents(); // Retrieve the contents from the cache
 * } else {
 *   $cache->begin();
 *   // Do complex calculation, maybe a query to a database,
 *   // and then output the contents
 *   $cache->end();
 * }
 * </code>
 *
 * Usage for caching variables or objects:
 *
 * <code>
 * $cahce = new Cache("KEY");
 * // if there's a timeout
 * // $seconds = 3600;
 * // $cache->setTimeout($seconds);
 * if ($cache->found()) {
 * 	 $object = $cache->contents(); // Retrieve the object from the cache
 * } else {
 *   $object = calculate_object(); // Perhaps a time consuming calculation
 *   $cache->put($object); // Put the object in the cache
 * }
 * </code>
 *
 * @package Cache
 */
class Cache {

	/**#@+
	 * @access private
	 */
	var $_key;
	var $_dir;
	var $_filename;
	var $_time;
	/**#@-*/

	/**
	 * Creates a piece of cache, provided the cache key.
	 * @param string $key the key of the cache piece
	 */
	function Cache($key) {
		$this->_key = $key;
		$this->_dir= $this->_getDir();
		$this->_filename = $this->_dir.$this->_key.'.cache';
	}

	/**
	 * @access private
	 */
	function _getDir() {
		global $application;

		$cache = $application->getIniSection('cache');
		if (!$cache) {
			print 'Fatal Error: the [cache] entry was not found on the web.ini file';
			die();
		}
		$dir = $cache->getParameter('dir');
		if (!$dir) {
			print 'Fatal Error: the dir parameter was not found on the [cache] entry in the web.ini file';
			die();
		}

		$dir = $application->getDocumentRoot() . $dir;
		FileUtils::ensureDir($dir);
		return $dir;
	}

	/**
	 * @access private
	 */
	function _disabled() {
		global $application;

		$cache = $application->getIniSection('cache');
		if (!$cache) {
			print 'Fatal Error: the [cache] entry was not found on the web.ini file';
			die();
		}
		$disabled = $cache->getParameter('disabled') === 'true';
		return $disabled;
	}

	/**
	 * Flushes this cache key. Only the file under the key name
	 * will be flushed.
	 */
	function flushKey() {
		if (Cache::_disabled()) return false;
		if (file_exists($this->_filename)) {
			unlink($this->_filename);
		}
	}

	/**
	 * Flushes this cache key as a pattern. All files where
	 * an ocurrence of key in the name is found will be flushed.
	 */
	function flushPattern() {
		if (Cache::_disabled()) return false;
		$handle = opendir($this->_dir);
		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..') {
		    if (strpos($file, $this->_key) !== false) {
					unlink($this->_dir.$file);
		    }
		  }
	  }
		closedir($handle);
	}

	/**
	 * Set the number of seconds from now on
	 * that this piece of cache will be used.
	 * @param integer $time the time to flush this cache, in seconds
	 */
	function setTimeout($time) {
		$this->_time = $time;
	}

	/**
	 * Facility method to set the cache expiration date for tomorrow,
	 * at 00:00 hours.
	 */
	function setExpiresTomorrow() {
		$now = new Date();
		$tomorrow = new Date();
		$tomorrow->addDays(1);
		$tomorrow->hour = 0;
		$tomorrow->minute = 0;
		$tomorrow->second = 0;
		$this->setTimeout($tomorrow->compareTo($now));
	}

	/**
	 * Sets the expiration date of this cache.
	 * @param Date $date a Date object
	 */
	function setExpirationDate($date) {
		$now = new Date();
		$this->setTimeout($date->compareTo($now));
	}

	/**
	 * Determines if a cache was found for this cache key.
	 * @return boolean true if the cache was found and it is not expired,
	 * else false
	 */
	function found() {
		if (Cache::_disabled()) return false;
		if (file_exists($this->_filename)) {
			// Abro el archivo para ver cuando expira
			$a = file($this->_filename);
			// Si es una x, no expira nunca
			if (trim($a[0]) == 'x') {
				return true;
			} else {
				$expiration = (int) $a[0];
				return $expiration > time();
			}
		} else {
			return false;
		}
	}

 	/**
 	 * Gets the contents of the cache (if any).
 	 * @return mixed the contents of the cache if any, or false
 	 */
	function contents() {
		if (Cache::_disabled()) return false;
		if (file_exists($this->_filename)) {
			$a = file($this->_filename);
			for ($i = 1; $i < sizeof($a); $i++) {
				$contents .= $a[$i];
			}
			return unserialize($contents);
		} else {
			return false;
		}
	}

	/**
	 * Starts caching the content of the page.
	 */
	function begin() {
		if (Cache::_disabled()) return false;
		ob_start();
	}

	/**
	 * Stops caching the content of the page, and
	 * stores it, printing the contents.
	 */
	function end() {
		if (Cache::_disabled()) return false;
		$contents = ob_get_contents();
		// Para evitar problemas de compatibilidad entre sistemas operativos
		$contents = str_replace("\r\n", "\r", $contents);
		$this->_saveCache($contents);
		ob_end_flush();
	}

	/**
	 * Saves a variables or an object into the cache.
	 * @param mixed $var the variable or object to cache
	 */
	function put($var) {
		if (Cache::_disabled()) return false;
		$this->_saveCache($var);
	}

	/**
	 * @access private
	 */
	function _saveCache($contents) {
		$handle = fopen($this->_filename, 'w');
		// Primero escribo el momento en que este cache
		// deja de ser valido, o x si no existe ese momento
		if (isset($this->_time)) {
			fwrite($handle, time() + $this->_time);
		} else {
			fwrite($handle, 'x');
		}
		fwrite($handle, "\r\n");
		fwrite($handle, serialize($contents));
		fclose($handle);
	}

}
?>