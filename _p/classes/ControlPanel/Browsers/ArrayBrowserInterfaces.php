<?
/**
 * This interface defines the search criteria for the ArrayBrowser.
 *
 * @package ControlPanel
 * @subpackage Browsers
 * @abstract
 */
class ArrayBrowserSearchCriteria {

	/**
	 * Returns true if $object matches the search $search_value.
	 */
	function matchesSearch($object, $search_value) {}

}
?>