<?
PHP::requireClasses('JSValidator/RequiredRule');

/**
 * This validator rules checks for a valid email.
 *
 * @package JSValidator
 * @subpackage Rules
 */
class EmailRule extends RequiredRule {

	/**
	 * @access private
	 */
	var $on_email_error;

	/**
	 * Constructs an EmailRule.
	 * @param string $field_name the field name to validate
	 * @param boolean $is_required is this field required?
	 */
	function EmailRule($field_name, $is_required = true) {
		$this->RequiredRule($field_name, $is_required);
	}

	/**
	 * Sets the javascript code to execute if the field is an invalid email.
	 * @param string $error a javascript code
	 */
	function setOnEmailError($error) {
		$this->on_email_error = $error;
	}

	/**
	 * @access private
	 */
	function _getAuxiliaryFilenames() {
		$files = parent::_getAuxiliaryFilenames();
		array_push($files, 'email.js');
		return $files;
	}

	function _getRequiredValidationCode() {
		$value = $this->_getFullName() . '.value';
		$string = $this->_buildIf("!_JSValidator_email_isValid($value)", $this->on_email_error);
		return $string;
	}

}
?>