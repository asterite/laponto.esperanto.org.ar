<?
PHP::requireClasses('ControlPanel/Renderers/ComboBoxRenderer');

/**
 * This renderer outputs a combo box containig the objects of a data objects manager.
 *
 * @implements EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class ManagerComboBoxRenderer extends ComboBoxRenderer {

	/**
	 * Constructs a ManagerComboBoxRenderer.
	 * @param $man the DataObjectsManager from where to list the objects
	 * @param $value_field the field that will be used as the value of an option
	 * @param $name_field the field that will be used as the name of an option
	 */
	function ManagerComboBoxRenderer($man, $value_field, $name_field) {
		$objects = array();
		$man->query();
		while($obj = $man->next()) {
			$value = MethodInvoker::chain($obj, $value_field);
			$name = MethodInvoker::chain($obj, $name_field);
			$objects{$value} = $name;
		}
		parent::ComboBoxRenderer($objects);
	}

}
?>