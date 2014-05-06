<?
PHP::requireClasses('Tag');

/**
 * This renderers renders an image depending on a boolean value.
 * The sources of the images can be defined in the constructor.
 *
 * @implements ListRenderer, EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class BooleanImageRenderer  {

	/**#@+
	 * @access private
	 */
	var $true;
	var $false;
	/**#@-*/

	/**
	 * Constructs a BooleanImageRenderer.
	 * @param string $true the source of the image to render if the $value is true
	 * @param string $false the  source of the image to render if the $value is false
	 */
	function BooleanImageRenderer($true, $false, $span_class = null) {
		$this->true = $true;
		$this->false = $false;
	}

	function renderList($value) {
		$tag = new Tag('image', false);
		$tag->setAttribute('src', $value ? $this->true : $this->false);
		return $tag->toString();
	}

	function renderEdit($name, $value) {
		$tag = new Tag('image', false);
		$tag->setAttribute('id', $name);
		$tag->setAttribute('name', $name);
		$tag->setAttribute('src', $value ? $this->true : $this->false);
		return $tag->toString();
	}

	function spanRow() {
		return true;
	}

	function getAttributes($value) {
		return array('align' => 'center');
	}

}
?>