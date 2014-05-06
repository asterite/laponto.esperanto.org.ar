<?
/**
 * This renderer outputs a value according to the some other key value.
 * The key-value pairs are specified in an array.
 * This renderer is usefull only when the elements to render are finite and known.
 * Example:
 * <code>
 *  // The value 1 will be rendered as 'One' and the value 2 as 'Two'
 *  $renderer = ArrayReferenceRenderer(array(1 => 'One', 2 => 'Two'))
 * </code>
 *
 * @implements ListRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class ArrayReferenceRenderer {

	/**
	 * @access private
	 */
	var $array;

	/**
	 * Constructs an ArrayReferenceRenderer from an array of key-value pairs.
	 * @param $array array the associative array
	 */
	function ArrayReferenceRenderer($array) {
		$this->array = $array;
	}

	function renderList($value) {
		return $this->array{$value};
	}

	function getAttributes($value) {
		return null;
	}

}
?>