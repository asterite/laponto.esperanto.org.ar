<?
PHP::requireClasses('JSValidator');

/**
 * Javascript rule to validate that at least one option amongst an array of options
 * is selected.
 *
 * @package JSValidator
 * @subpackage Rules
 */
class AtLeastOneOptionRule extends ValidationRule {

	/**#@+
	 * @access private
	 */
	var $field_name;
	var $on_options_error;
	/**#@-*/

	/**
	 * Constructs an AtLeastOneOptionRule.
	 * @param string $field_name the field name to validate
	 */
	function AtLeastOneOptionRule($field_name) {
		$this->field_name = $field_name;
	}

	/**
	 * Return the full name of the field validated by this rule.
	 * @return string the full name of the field validated by this rule
	 * @access private
	 */
	function _getFullName() {
		return "document.{$this->form_name}.elements['{$this->field_name}']";
	}

	/**
	 * Sets the javascript code to execute if no option is selected.
	 * @param string $error a javascript code
	 */
	function setOnOptionsError($error) {
		$this->on_options_error = $error;
	}

	/**
	 * @acces private
	 */
	function _getAuxiliaryFilenames() {
		return array('at_least_one_option.js');
	}

	function _getValidationCode() {
		$options = $this->_getFullName();
		$string = $this->_buildIf("!_JSValidator_at_least_one_option_is_checked($options)", $this->on_options_error);
		return $string;
	}

}
?>