<?
PHP::requireClasses('JSValidator');

/**
 * This validation rule is used to indicate that a field is required.
 *
 * @package JSValidator
 * @subpackage Rules
 */
class RequiredRule extends ValidationRule {

	/**#@+
	 * @access private
	 */
	var $field_name;
	var $is_required;
	var $on_required_error;
	/**#@-*/

	/**
	 * Constructs a RequiredRule.
	 * @param string $field_name the field name to validate
	 * @param boolean $is_required is this field required?
	 */
	function RequiredRule($field_name, $is_required = true) {
		$this->field_name = $field_name;
		$this->is_required = $is_required;
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
	 * Sets the javascript code to execute if the field is required and not fulfilled
	 * @param string $error a javascript code
	 */
	function setOnRequiredError($error) {
		$this->on_required_error = $error;
	}

	/**
	 * @access private
	 */
	function _getAuxiliaryFilenames() {
		return array('string.js');
	}

	/**
	 * Returns the validation code. To add more validation codes,
	 * override the getRequiredValidationCode() method.
	 * @return string the validation code
	 * @final
	 */
	function _getValidationCode() {
		$value = $this->_getFullName() . '.value';
		$code = $this->_getRequiredValidationCode();
		if ($this->is_required) {
			$string .= $this->_buildIf("_JSValidator_string_isEmpty($value)", $this->on_required_error);
			if ($code) {
				$string .= "\n" . $code;
			}
		} else {
			if ($code) {
				$string .= "\nif (!_JSValidator_string_isEmpty($value)) {\n";
				$string .= $code . "\n";
				$string .= '}';
			}
		}
		return $string;
	}

	/**
	 * Override this method to add extra validation plus this required
	 * validation.
	 * @return a validation code
	 */
	function _getRequiredValidationCode() {}

}
?>