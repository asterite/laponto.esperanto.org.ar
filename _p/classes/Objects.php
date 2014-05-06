<?
/**
 * Static methods to deal with objects.
 *
 * @package Common
 */
class Objects {

	/**
	 * Copies properties from $o1 to $o2. The properties names
	 * are specified in the $properties array.
	 * @param $o1 the object to copy from
	 * @param $o2 the object to copy to
	 * @param string[] $properties an array of property names
	 */
	function copyProperties(&$o1, &$o2, $properties) {
		foreach($properties as $property) {
			$o2->$property = $o1->$property;
		}
	}

}
?>