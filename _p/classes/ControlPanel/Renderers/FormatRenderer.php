<?
PHP::requireClasses('ControlPanel/Renderers/LabelRenderer');

/**
 * This is a LabelRenderer that outputs a string in a specified format.
 * The formatting is done using the sprintf function.
 *
 * @implements ListRenderer, EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class FormatRenderer extends LabelRenderer {

	/** @access private */
	var $format;

	/**
	 * Constructs a FormatRenderer with the specified format.
	 * @param string $format the format to use in the sprintf function
	 * @param string $span_class (optional) the class attribute of the span tag
	 * printed by the renderEdit() method
	 */
	function FormatRenderer($format, $span_class = null) {
		$this->LabelRenderer($span_class);
		$this->format = $format;
	}

	function renderList($value) {
		return parent::renderList(sprintf($this->format, $value));
	}

	function renderEdit($name, $value) {
		return parent::renderEdit($name, sprintf($this->format, $value));
	}

}
?>