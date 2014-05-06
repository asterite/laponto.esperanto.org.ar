<?
PHP::requireClasses('ControlPanel/Renderers/LabelRenderer');

/**
 * This renderers renders values depending on a boolean value.
 * This values can be defined in the constructor.
 * If in list, this renderer returns the rendered value as is.
 * If in edit, this renderer returns the rendered value nested in a span tag.
 *
 * @implements ListRenderer, EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class BooleanLabelRenderer {

	/**#@+
	 * @access private
	 */
	var $true;
	var $false;
	var $span_class;
	/**#@-*/

	/**
	 * Constructs a BooleanLabelRenderer.
	 * @param string $true the value to render if the $value is true
	 * @param string $false the value to render if the $value is false
	 * @param string $span_class (optional) the class attribute of the span tag
	 * printed by the renderEdit() method
	 */
	function BooleanLabelRenderer($true, $false, $span_class = null) {
		$this->true = $true;
		$this->false = $false;
		$this->span_class = $span_class;
	}

	/**
	 * Returns the value $true  if $value is true, else the value $false,
	 * both $true and $false specified in the constructor.
	 * @return string the value $true  if $value is true,  else the value $false,
	 * both $true and $false specified in the constructor.
	 */
	function renderList($value) {
		return $value ? $this->true : $this->false;
	}

	/**
	 * Renders the value of renderList(), but nested in a span tag.
	 * The id and name attributes of the span tag are $name.
	 * @return a span tag containing the value rendered by renderList()
	 */
	function renderEdit($name, $value) {
		$label = new LabelRenderer($this->span);
		return $label->renderEdit($name, $this->renderList($value));
	}

	function spanRow() {
		return true;
	}

	function getAttributes($value) {
		return null;
	}

}
?>