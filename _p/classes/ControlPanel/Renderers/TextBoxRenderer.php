<?
PHP::requireClasses('Tag');

/**
 * This renderer outputs a textbox.
 *
 * @implements EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class TextBoxRenderer {

	/**#@+
	 * @access private
	 */
	var $size;
	var $maxlength;
	/**#@-*/

	/**
	 * Constructs a textbox of the specified size.
	 * @param int $size the size attribute of the textbox
	 * @param int $maxlength (optional) the maxlength attribute of the textbox
	 */
	function TextBoxRenderer($size, $maxlength = null) {
		$this->size = $size;
		$this->maxlength = $maxlength;
	}

	/**
	 * @return boolean true
	 */
	function spanRow() {
		return true;
	}

	/**
	 * Returns a textbox input.
	 * The id and name attributes of the textbox input tag are $name.
	 * @return a textbox input
	 */
	function renderEdit($name, $value) {
		$input = new Tag('input', false);
		$input->setAttribute('type', 'text');
		$input->setAttribute('id', $name);
		$input->setAttribute('name', $name);
		$input->setAttribute('value', htmlspecialchars($value, ENT_QUOTES));
		$input->setAttribute('size', $this->size);
		$input->setAttribute('maxlength', $this->maxlength);
		return $input->toString();
	}

}
?>