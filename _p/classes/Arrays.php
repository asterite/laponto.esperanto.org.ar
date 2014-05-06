<?
/**
 * Provides static functions to work with arrays, for avoiding
 * compatibilities problems with php versions.
 * Also provides searching methods for objects in arrays.
 *
 * @package Common
 * @static
 */
class Arrays {

	/**
	 * Searches a value in the array. Returns the index
	 * of the value in the array, or NULL if not found.
	 *
	 * If the value to search is an object, it must implement the
	 * Equalable interface.
	 *
	 * @param array $array the array to search
	 * @param mixed $value the value to look for
	 * @return mixed the index of the value in the array, or NULL if
	 * not found
	 */
	function search($array, $value) {
		if (is_object($value)) {
			return Arrays::_searchObject($array, $value);
		} else {
			$search = array_search($value, $array);
			if ($search === false or is_null($search)) {
				return null;
			}
			return $search;
		}
	}

	/**
	 * Pushes a value (primitive or object) into the array if it is not already in it.
	 *
	 * If it is an object, it must implement the
	 * Equalable interface.
	 *
	 * This method is useful for creating sets.
	 */
	function pushIfNotExists(&$array, $value) {
		if (is_null(Arrays::search($array, $value))) {
			array_push($array, $value);
		}
	}

	/**
	 * @access private
	 */
	function _searchObject($array, $value) {
		foreach($array as $key => $object) {
			if ($object->equals($value)) {
				return $key;
			}
		}
		return null;
	}

	/**
	 * @access private
	 */
	function _pushObjectIfNotExists(&$array, $value) {
		if (is_null(Arrays::searchObject($array, $value))) {
			array_push($array, $value);
		}
	}

	/**
	 * Sorts Comparable objects.
	 */
	function sortObjects(&$array) {
		usort($array, array('Arrays', '_naturalCompare'));
	}

	/**
	 * @access private
	 */
	function _naturalCompare($a, $b) {
		return $a->compareTo($b);
	}

}
?>