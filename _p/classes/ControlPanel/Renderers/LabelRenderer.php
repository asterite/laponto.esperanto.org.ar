<?
PHP::requireClasses('Tag');

/**
 * This renderer outputs the value to renderer as is in the list
 * page, or in a span tag in the edit page.
 *
 * @implements ListRenderer, EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class LabelRenderer {

	/**#@+
	 * @access private
	 */
	var $align;
	var $span_class;
	/**#@-*/

	/**
	 * Constructs a LabelRenderer.
	 * @param string $span_class (optional) the class attribute of the span tag
	 * printed by the renderEdit() method
	 */
	function LabelRenderer($align = 'left', $span_class = null) {
		$this->align = $align;
		$this->span_class = $span_class;
	}

	/**
	 * Returns the $value as is.
	 * @return mixed the $value as is
	 */
	function renderList($value) {
		return $value;
	}

	/**
	 * Returns a span tag containing the $value specified.
	 * The id and name attributes of the span tag are $name.
	 * @return a span tag containing the $value specified
	 */
	function renderEdit($name, $value) {
		$span = new Tag('span', true);
		$span->setAttribute('id', $name);
		$span->setAttribute('name', $name);
		$span->setAttribute('class', $this->span_class);
		$span->addNestedString($this->renderList($value));
		return $span->toString();
	}

	function spanRow() {
		return true;
	}

	function getAttributes($value) {
		return array('align' => $this->align);
	}

}
?>