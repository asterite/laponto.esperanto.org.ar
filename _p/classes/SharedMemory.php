<?
define('__SHM__KEY', 554387);

global $__shm__id;
if (function_exists('shm_attach')) {
	$__shm__id = shm_attach(1);
} else {
	$__shm__id = null;
}

/**
 * This class is a facade to store and retrieve variables in shared memory.
 * @package Common
 * @static
 */
class SharedMemory {

	/**
	 * Determines if the SharedMemory module is available.
	 * @return boolean should return true for unix systems and false for windows systems.
	 */
	function isAvailable() {
		global $__shm__id;
		return !is_null($__shm__id);
	}

	/**
	 * Stores a variables in shared memory.
	 * @param string $key a key
	 * @param string $value a value
	 * @static
	 */
	function put($key, $value) {
		if (!SharedMemory::isAvailable()) return;
		$array = SharedMemory::_get(__SHM__KEY);
		if (!is_array($array)) $array = array();
		$array{$key} = $value;
		SharedMemory::_put(__SHM__KEY, $array);
	}

	/**
	 * Retrieves a value from shared memory given a key.
	 * @param string $key a key
	 * @static
	 */
	function get($key) {
		if (!SharedMemory::isAvailable()) return;
		$array = SharedMemory::_get(__SHM__KEY);
		return is_array($array) ? $array{$key} : null;
	}

	/**
	 * Removes a value from shared memory given a key.
	 * @param string $key a key
	 * @static
	 */
	function remove($key) {
		$array = SharedMemory::_get(__SHM__KEY);
		if (is_array($array)) {
			$array{$key} = null;
			SharedMemory::_put(__SHM__KEY, $array);
		}
	}

	/**
	 * Returns all the keys stored in shared memory.
	 * @param string[] the keys
	 * @static
	 */
	function keys() {
		$array = SharedMemory::_get(__SHM__KEY);
		if (!is_array($array)) $array = array();
		return array_keys($array);
	}

	/**#@+
	 * @access private
	 */
	function _put($int, $value) {
		global $__shm__id;
		shm_put_var($__shm__id, $int, $value);
	}

	function _get($int) {
		global $__shm__id;
		return shm_get_var($__shm__id, $int);
	}
	/**#@-*/

}
?>