<?
/**
 * This renderer implements the Decorator pattern.
 * If the value to render is null, then a string representing "null" be rendered.
 * If the value to render is not null, then the decorated renderer is used to render
 * the value.
 *
 * @implements ListRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class NullRenderer {

	/**#@+
	 * @access private
	 */
	var $list_renderer;
	var $null;
	/**#@-*/

	/**
	 * Constructs a NullRenderer.
	 * @param ListRenderer $list_renderer the renderer to use when the value to render
	 * is not null
	 * @param string $null the value to output when the value to render is null
	 */
	function NullRenderer($list_renderer, $null) {
		$this->list_renderer = $list_renderer;
		$this->null = $null;
	}

	/**
	 * If $value is null, returns the $null variable set in the constructor, else
	 * calls the decorated renderer renderList($value) method.
	 */
	function renderList($value) {
		if (is_null($value)) {
			return $this->null;
		} else {
			return $this->list_renderer->renderList($value);
		}
	}

	/**
	 * If $value is null returns 'center', else it relys on the decorated renderer.
	 */
	function getAttributes($value) {
		if (is_null($value)) {
			return array('align' => 'center');
		} else {
			return $this->list_renderer->getAttributes($value);
		}
	}

}
?>