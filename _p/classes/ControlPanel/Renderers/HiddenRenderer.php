<?
PHP::requireClasses('Tag');

/**
 * This renderer outputs a hidden input.
 *
 * @implements EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class HiddenRenderer {

	/**
	 * Constructs a HiddenRenderer.
	 */
	function HiddenRenderer() {
	}

	/**
	 * @return boolean false
	 */
	function spanRow() {
		return false;
	}

	/**
	 * Returns a hidden input.
	 * The id and name attributes of the hidden input tag are $name.
	 * @return a hidden input
	 */
	function renderEdit($name, $value) {
		$input = new Tag('input', false);
		$input->setAttribute('id', $name);
		$input->setAttribute('name', $name);
		$input->setAttribute('value', $value);
		$input->setAttribute('type', 'hidden');
		return $input->toString();
	}

}
?>