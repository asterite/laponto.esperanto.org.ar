<?
/**
 * The DateRenderer renders a Date into a specified format.
 *
 * @implements ListRenderer, EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class DateRenderer {

	/**#@+
	 * @access private
	 */
	var $format;
	var $span_class;
	/**#@-*/

	/**
	 * Constructs a DateRenderer.
	 * @param string $format format to render the date, the same as the date() function of PHP.
	 * @param string $span_class (optional) the class attribute of the span tag
	 * printed by the renderEdit() method
	 */
	function DateRenderer($format, $span_class = null) {
		$this->format = $format;
		$this->span_class = $span_class;
	}

	function renderList($value) {
		if (is_null($value)) {
			return null;
		} else {
			return $value->format($this->format);
		}
	}

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
		return array('align' => 'center');
	}

}
?>