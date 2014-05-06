<?
PHP::requireClasses('Tag');

/**
 * This renderer outputs a checkbox that may be checked if the
 * value to renderer is true.
 *
 * @implements EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class CheckBoxRenderer {

	/**
	 * Constructs a CheckBoxRenderer.
	 */
	function CheckBoxRenderer() {}

	/**
	 * Returns an input of type checkbox, checked if $value is true.
	 * The id and name attributes of the checkbox input tag are $name.
	 * @return an input of type checkbox, checked if $value is true
	 */
	function renderEdit($name, $value) {
		$input = new Tag('input', false);
		$input->setAttribute('type', 'checkbox');
		$input->setAttribute('id', $name);
		$input->setAttribute('name', $name);
		if ($value) $input->setModifier('checked');
		return $input->toString();
	}

	function spanRow() {
		return true;
	}

}
?>