<?
PHP::requireClasses('Tag');

/**
 * This is a LabelRenderer with a limit to the characters shown.
 * The limit is imposed to some characters count, and the "..." is appended
 * to the value to render.
 *
 * @implements ListRenderer, EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class LimitLabelRenderer {

	/**#@+
	 * @access private
	 */
	var $limit;
	var $span_class;
	/**#@-*/

	/**
	 * Constructs a LimitLabelRenderer.
	 * @param int $limit the limit of characters
	 * @param string $span_class (optional) the class attribute of the span tag
	 * printed by the renderEdit() method
	 */
	function LimitLabelRenderer($limit = null, $span_class = null) {
		$this->limit = $limit;
		$this->span_class = $span_class;
	}

	/**
	 * Returns the $value limited.
	 * @return mixed the $value limited
	 */
	function renderList($value) {
		$this->_limit($value);
		return $value;
	}

	/**
	 * Returns a span tag containing the $value specified, limited.
	 * The id and name attributes of the span tag are $name.
	 * @return a span tag containing the $value specified, limited
	 */
	function renderEdit($name, $value) {
		$this->_limit($value);

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
		return null;
	}

	function _limit(&$value) {
		if (!is_null($this->limit) && trim($value) != '') {
			if (strlen($value) > $this->limit) {
				$value = substr($value, 0, $this->limit) . '...';
			}
		}
	}

}
?>