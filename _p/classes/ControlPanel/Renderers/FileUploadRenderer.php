<?
PHP::requireClasses('Tag');

/**
 * This renderer outputs a file input.
 *
 * @implements EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class FileUploadRenderer {

	/**
	 * Constructs a FileUploadRenderer.
	 */
	function FileUploadRenderer() {
	}

	/**
	 * @return boolean true
	 */
	function spanRow() {
		return true;
	}

	/**
	 * Returns a file input.
	 * The id and name attributes of the file input tag are $name.
	 * @return a file input
	 */
	function renderEdit($name, $value) {
		$input = new Tag('input', false);
		$input->setAttribute('id', $name);
		$input->setAttribute('name', $name);
		$input->setAttribute('type', 'file');
		return $input->toString();
	}

}
?>