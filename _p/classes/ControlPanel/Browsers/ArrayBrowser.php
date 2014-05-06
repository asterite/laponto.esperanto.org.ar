<?
/**
 * This class implements the Browser interface for arrays
 * of objects. The browser browses an array of objects.
 * When ordering the array of objects, if the type of the field
 * of order is a primitive, the <, > or = is used. If it is an object,
 * it must implement the Comparable interface defined in the Common package.
 *
 * @implements Browser
 *
 * @package ControlPanel
 * @subpackage Browsers
 */
class ArrayBrowser {

	/**#@+
	 * @access private
	 */
	var $objects;
	var $class_name;
	var $primary_keys;

	var $search_value;
	var $search_criteria;

	var $order_field;
	var $order_ascendent;
	var $current_page;
	var $results_per_page;

	var $total_results;
	var $results;
	var $counter;
	/**#@-*/

	/**
	 * Constructs an ArrayBrowser.
	 * @param array $array_of_objects the array of objects
	 * @param string $class_name the name of the class of the objects in the array
	 * @param array $primary_keys an array containing the primary keys of
	 * the objects in the array
	 */
	function ArrayBrowser($array_of_objects, $class_name, $primary_keys) {
		$this->objects = $array_of_objects;
		$this->class_name = $class_name;
		$this->primary_keys = $primary_keys;
		$this->counter = 0;
	}

	/**
	 * @param ArrayBrowserSearchCriteria $search_criteria
	 */
	function addSearch($search_value, $search_criteria) {
		$this->search_value = $search_value;
		$this->search_criteria = $search_criteria;
	}

	function addOrder($field_name, $ascendent = true) {
		$this->order_field = $field_name;
		$this->order_ascendent = $ascendent;
	}

	function setCurrentPage($page) {
		$this->current_page = $page;
		if ($this->current_page < 1) $this->current_page = 1;
	}

	function setResultsPerPage($results_per_page) {
		if ($results_per_page > 0) $this->results_per_page = $results_per_page;
	}

	function query() {
		$this->results = $this->objects;
		if ($this->search_value) {
			$this->results =& $this->_search($this->results);
		}
		$this->total_results = sizeof($this->results);
		if ($this->order_field) {
			$this->_order($this->results);
		}
		if ($this->results_per_page) {
			$this->results = array_slice($this->results, $this->getFirstResult() - 1, $this->results_per_page);
		}
	}

	function next() {
		if ($this->counter < sizeof($this->results)) {
			return $this->results{$this->counter++};
		} else {
			return false;
		}
	}

	function getPrimaryKeys() {
		return $this->primary_keys;
	}

	function getFirstResult() {
		if (!$this->results_per_page) {
			return $this->getTotalResults() == 0 ? 0 : 1;
		}
		return $this->getTotalResults() == 0 ? 0 : ($this->current_page - 1)*$this->results_per_page + 1;
	}

	function getLastResult() {
		if ($this->getTotalResults() == 0) {
			return 0;
		}
		if (!$this->results_per_page) {
			return $this->getTotalResults();
		}
		$a = $this->getFirstResult() + $this->results_per_page - 1;
		if ($a > $this->getTotalResults()) return $this->getTotalResults();
		return $a;
	}

	function getTotalResults() {
		return $this->total_results;
	}

	function getTotalPages() {
		if ($this->results_per_page) {
			$x = ceil($this->getTotalResults() / $this->results_per_page);
			if ($x == 0) $x = 1;
			return $x;
		} else {
			return 1;
		}
	}

	function getFromKeys($keys) {
		foreach($this->objects as $object) {
			$found = true;
			foreach($keys as $name => $value) {
				if ($object->$name != $value) {
					$found = false;
					break;
				}
			}
			if ($found) return $object;
		}
		return null;
	}

	function getObjectsClassName() {
		return $this->class_name;
	}

	/**#@+
	 * @access private
	 */
	function &_search(&$objects) {
		$results = array();
		foreach($objects as $object) {
			if ($this->search_criteria->matchesSearch($object, $this->search_value)) {
				array_push($results, $object);
			}
		}
		return $results;
	}

	function _order(&$objects) {
		usort($objects, array($this, '_compare'));
	}

	function _compare($a, $b) {
		$field_name = $this->order_field;

		if (is_object($a->$field_name)) {
			$aa = $a->$field_name;
			$bb = $b->$field_name;
			return $aa->compareTo($bb) * ($this->order_ascendent ? 1 : -1);
		}

		if (is_string($a->$field_name)) {
			return strcasecmp($a->$field_name, $b->$field_name);
		}

		if ($a->$field_name < $b->$field_name) {
			return -1 * ($this->order_ascendent ? 1 : -1);
		} else if ($a->$field_name > $b->$field_name) {
			return 1 * ($this->order_ascendent ? 1 : -1);
		} else {
			return 0;
		}
	}
	/**#@-*/

}

/**
 * This search criteria matches when the search value is a substring
 * of the object searched (case-insensitive).
 *
 * Only applicable to scalar values.
 *
 * @implements ArrayBrowserSearchCriteria
 */
class ArrayBrowserSubstringSearchCriteria {

	/** @access private */
	var $search_fields;

	/**
	 * Constructs a SubstringSearchCriteria.
	 * @param mixed $search_fields (...) the search fields in where to look
	 * for the search value.
	 */
	function ArrayBrowserSubstringSearchCriteria($search_fields) {
		$this->search_fields = func_get_args();
	}

	function matchesSearch($object, $search_value) {
		foreach($this->search_fields as $search_field) {
			if (strpos(strtolower($object->$search_field), strtolower($search_value)) !== false) {
				return true;
			}
		}
		return false;
	}

}

/**
 * This search criteria matches when the search value is the same as the object searched.
 *
 * Only applicable to scalar values.
 *
 * @implements ArrayBrowserSearchCriteria
 */
class ArrayBrowserSameValueSearchCriteria {

	/** @access private */
	var $search_field;

	/**
	 * Constructs a SameValueSearchCriteria.
	 * @param string $search_field the search field in where to look for
	 * the search value
	 */
	function ArrayBrowserSameValueSearchCriteria($search_field) {
		$this->search_field = $search_field;
	}

	function matchesSearch($object, $search_value) {
		$search_field = $this->search_field;
		return $object->$search_field == $search_value;
	}

}

/**
 * This search criteria matches a Date value, given its format.
 *
 * Only applicable to Date objets.
 *
 * @implements ArrayBrowserSearchCriteria
 */
class ArrayBrowserSameDateSearchCriteria {

	/**#@+
	 * @access private
	 */
	var $search_field;
	var $format;
	/**#@-*/

	/**
	 * Constructs this criteria.
	 * @param string $search_field the search field in where to look for
	 * the search value
	 * @param string $format format of the search value, the same as the date() function of PHP.
	 */
	function ArrayBrowserSameDateSearchCriteria($search_field, $format) {
		$this->search_field = $search_field;
		$this->format = $format;
	}

	function matchesSearch($object, $search_value) {
		$search_field = $this->search_field;
		$timestamp = $object->$search_field->getSeconds();
		$format = date($this->format, $timestamp);
		return $format == $search_value;
	}

}
?>