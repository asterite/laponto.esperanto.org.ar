<?
PHP::requireClasses('Arrays');

/**
 * Represents a ValidationRule.
 *
 * @package JSValidator
 * @abstract
 */
class ValidationRule {

	/**
	 * The name of the form to validate
	 * @access protected
	 */
	var $form_name;

	/**
   * Returns the validation code for the field.
   * @return string the validation code of this rule
   * @access protected
   * @abstract
   */
	function _getValidationCode() {}

	/**
	 * Returns an array containing the auxiliary filenames
	 * containing JavaScript functions. The names are relative
	 * to the "/classes/JSValidator" directory.
	 * It is adviseable that the functions defined in those files
	 * begin with the prefix "_JSValidator_$file_" where $file
	 * is the name of the file that contains the function, without
	 * the extension.
	 * @return string[] the array of auxiliary filenames
	 * @access protected
   * @abstract
	 */
	function _getAuxiliaryFilenames() {}

	/**
	 * Sets the name of the form this field belongs to.
	 * @param string $form_name the name of the form this field belongs to
	 * @access private
	 */
	function _setFormName($form_name) {
		$this->form_name = $form_name;
	}

	/**
	 * Returns a string that is it like:
	 * if ($condition) {
	 *	 $error; // Only if $error is not null
	 *   return false;
	 * }
	 * @param string $condition a condition
	 * @param string $error a javascript code to execute when the condition is met
	 * @return string read method explanation
	 * @static
	 */
	function _buildIf($condition, $error) {
		$string  = "if ($condition) {\n";
		if ($error) {
			$string .= "$error;\n";
		}
		$string .= "return false;\n";
		$string .= '}';
		return $string;
	}

}

/**
 * This class builds JavaScript code to validate fields
 * in forms.
 * It may use resources defined in the validation rules added to it
 * (see the method VaidationRule::_getAuxiliaryFilenames). The resources
 * will only be printed once to avoid duplicated function names.
 */
class JSValidator {

	/**#@+
	 * @access private
	 */
	var $_form_name;
	var $_function_name;
	var $_rules;
	var $_aux_files;
	/**#@-*/

	/**
	 * Constructs a JSValidator for a form, with a function
	 * named $function_name.
	 * @param string $form_name the name of the form
	 * @param string $function_name the name of the function to create
	 */
	function JSValidator($form_name, $function_name) {
		$this->_form_name = $form_name;
		$this->_function_name = $function_name;
		$this->_rules = array();
		$this->_aux_files = array();
	}

	/**
	 * Adds a validation rule to this validator.
	 * @param ValidationRule $rule a ValidationRule instance
	 */
	function addValidationRule($rule) {
		$rule->_setFormName($this->_form_name);
		array_push($this->_rules, $rule);
		$files = $rule->_getAuxiliaryFilenames();
		if (!$files) return;
		foreach($files as $file) {
			if (is_null(Arrays::search($this->_aux_files, $file))) {
				array_push($this->_aux_files, $file);
			}
		}
	}

	/**
	 * Gets the javascript code (including the javascript tag)
	 * containing a function name $function_name (passed in the constructor)
	 * for validating the form. The function returns true if the form
	 * is valid, else false.
	 *
	 * The tipical use is to put "return $function_name" in the onSubmit
	 * attribute of the form to validate.
	 *
	 * @return string the javascript code with the function (including the script tag)
	 */
	function getJSCode() {
		$code  = "\n";
		return $code;
	}

	/**
	 * @access private
	 */
	function _getFunctions() {
		global $application;
		$old_magic_quotes_runtime = get_magic_quotes_runtime();
		set_magic_quotes_runtime(0);
		foreach($this->_aux_files as $file) {
			if (!$application->getAttribute('__JSValidator_used_file_' . $file)) {
				$path = PHP::realPath('_p/classes/JSValidator/' . $file);
				$handle = fopen($path, 'r', 1);
				while($x = fread($handle, 4096)) {
					$contents .= $x;
				}
				$contents .= "\n";
				$application->setAttribute('__JSValidator_used_file_' . $file, true);
			}
		}
		set_magic_quotes_runtime($old_magic_quotes_runtime);
		return $contents;
	}

}
?>