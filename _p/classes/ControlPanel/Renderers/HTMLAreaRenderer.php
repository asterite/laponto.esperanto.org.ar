<?
PHP::requireClasses('HTMLArea', 'Tag');

/**
 * This renderer outputs an HTMLArea (from interactivetools.com).
 * See HTMLArea.php.
 *
 * @implements EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class HTMLAreaRenderer {

	/**#@+
	 * @access private
	 */
	var $width;
	var $height;
	var $config;
	/**#@-*/

	/**
	 * Constructs an HTMLAreaRenderer with the specified width and height.
	 * @param int $width the width of the HTMLArea
	 * @param int $height the height of the HTMLArea
	 * @param HTMLAreaConfig $config the configuration of the HTMLArea
	 */
	function HTMLAreaRenderer($width, $height, $config = null) {
		$this->width = $width;
		$this->height = $height;
		$this->config = $config;
	}

	/**
	 * @return boolean true
	 */
	function spanRow() {
		return true;
	}

	/**
	 * Constructs an HTMLArea, set its contents to be $value, and
	 * the returns its HTML code.
	 * @return the HTMLArea
	 */
	function renderEdit($name, $value) {
		$area = new HTMLArea($name, $this->width, $this->height);
		$area->setContents($value);
		if (!is_null($this->config)) {
			$this->config->configureHTMLArea($area);
		}
		return $area->getHTMLCode();
	}

}

/**
 * Interface.
class HTMLAreaConfig {

	function configureHTMLArea(&$html_area);

}
*/
?>