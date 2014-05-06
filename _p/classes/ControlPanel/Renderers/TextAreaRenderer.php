<?
PHP::requireClasses('Tag');

/**
 * This renderer outputs a textarea.
 *
 * @implements EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class TextAreaRenderer {

	/**#@+
	 * @access private
	 */
	var $rows;
	var $cols;
	/**#@-*/

	/**
	 * Constructs a TextAreaRenderer with the specified
	 * columns and rows.
	 * @param int $cols the number of columns
	 * @param int $rows the number of rows
	 */
	function TextAreaRenderer($cols, $rows) {
		$this->rows = $rows;
		$this->cols = $cols;
	}

	/**
	 * @return boolean true
	 */
	function spanRow() {
		return true;
	}

	/**
	 * Returns a textarea.
	 * The id and name attributes of the textarea tag are $name.
	 * @return a textarea
	 */
	function renderEdit($name, $value) {
		$input = new Tag('textarea', true);
		$input->setAttribute('id', $name);
		$input->setAttribute('name', $name);
		$input->setAttribute('cols', $this->cols);
		$input->setAttribute('rows', $this->rows);
		$input->addNestedString($value);
		return $input->toString();
	}

}
?>