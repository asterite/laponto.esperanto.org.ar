<?
PHP::requireClasses('Tag');

/**
 * This renderer outputs a password input.
 *
 * @implements EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class PasswordRenderer {

	/**#@+
	 * @access private
	 */
	var $size;
	var $maxlength;
	/**#@-*/

	/**
	 * Constructs a password input of the specified size.
	 * @param int $size the size attribute of the password input
	 */
	function TextBoxRenderer($size, $maxlength) {
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
	 * Returns a password input.
	 * The id and name attributes of the password input tag are $name.
	 * @return a password input
	 */
	function renderEdit($name, $value) {
		$input = new Tag('input', false);
		$input->setAttribute('type', 'password');
		$input->setAttribute('id', $name);
		$input->setAttribute('name', $name);
		$input->setAttribute('value', htmlspecialchars($value, ENT_QUOTES));
		$input->setAttribute('size', $this->size);
		$input->setAttribute('maxlength', $this->maxlength);
		return $input->toString();
	}

}
?>