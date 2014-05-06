<?
/**
 * An object that can be compared to other objects.
 *
 * @package Common
 * @subpackage Interfaces
 * @abstract
 */
class Comparable {

	/**
	 * Compares this object to another $object.
	 * @return integer positive if this object is greater the $object,
	 * negative if this object is lesser the $object or zero if the objects
	 * are equal.
	 */
	function compareTo($object) {}

}

/**
 * An object that can tested for equality with other objects.
 * @abstract
 */
class Equalable {

	/**
	 * Tests equality between this object and another $object.
	 * @return boolean true if the objects are equal, else false
	 */
	function equalsTo($object) {}

}
?>