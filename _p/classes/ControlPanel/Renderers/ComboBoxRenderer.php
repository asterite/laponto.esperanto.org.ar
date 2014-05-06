<?
PHP::requireClasses('Tag', 'DataObjects', 'ControlPanel/Common', 'Arrays');

/**
 * This renderer outputs a combo box containig the values of an array.
 *
 * @implements EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class ComboBoxRenderer {

	/**#@+
	 * @access private
	 */
	var $objects;
	var $size;
	var $multiple;
	/**#@-*/

	/**
	 * Constructs a ComboBoxRenderer.
	 * @param $objects an associative array where the keys will be used as the values
	 * of the combo box and the values will be used as the options displayed.
	 * @param integer $size (default = 1) the size of the combo box (the size attribute of the select tag)
	 * @param boolean $multiple (default = false) determines if this combo box allows multiple selections
	 * to be made. If that is the case, the name of the select tag will end in brackets ([])
	 */
	function ComboBoxRenderer($objects, $size = 1, $multiple = false) {
		$this->objects = $objects;
		$this->size = $size;
		$this->multiple = $multiple;
	}

	/**
	 * @return boolean true
	 */
	function spanRow() {
		return true;
	}

	/**
	 * Returns the combo box with the options in the array.
	 * In case the combo box allows multiple selections, $value can be an array of values.
	 */
	function renderEdit($name, $value) {
		$select = new Tag('select', true);
		if ($this->multiple) {
			$select->setAttribute('id', $name.'[]');
			$select->setAttribute('name', $name.'[]');
			$select->setModifier('multiple');
		} else {
			$select->setAttribute('id', $name);
			$select->setAttribute('name', $name);
		}

		if (sizeof($this->objects) > $this->size) {
			$select->setAttribute('size', $this->size);
		}

		foreach($this->objects as $key => $nested) {
			$option = new Tag('option', true);
			$option->setAttribute('value', $key);
			$option->addNestedString($nested);
			if (is_array($value)) {
				if (!is_null(Arrays::search($value, $key))) {
					$option->setModifier('selected');
				}
			} else {
				if ($key == $value) {
					$option->setModifier('selected');
				}
			}
			$select->addNestedTag($option);
		}

		return $select->toString();
	}

}
?>