<?
/**
 * Validation rule that evaluates a condition for one or more fields.
 * RequiredRules must be applied to each field tested in the condition before
 * applying this rule.
 *
 * @package JSValidator
 * @subpackage Rules
 */
class ConditionRule extends ValidationRule {

	/**#@+
	 * @access private
	 */
	var $fields;
	var $condition;
	/**#@-*/

	/**
	 * Constructs a ConditionRule for some fields.
	 * The condition can be any javascript condition such as >, <, ==, !=, >=, <=. Javascript functions
	 * may also be used, and user defined functions that reside in the current script.
	 * @param string[] $fields the name of the fields
	 * @para string $condition a condition where the fields must be enclosed in braces (e.g. {field}).
	 */
	function ConditionRule($fields, $condition) {
		$this->fields = $fields;
		$this->condition = $condition;
	}

	/**
	 * Sets the javascript command to execute when the condition is not met.
	 * @param string $error a javascript sentence
	 */
	function setOnConditionError($error) {
		$this->on_condition_error = $error;
	}

	function _getValidationCode() {
		$real_condition = $this->condition;
		foreach($this->fields as $field) {
			$real_condition = str_replace('{' . $field . '}', $this->_getFullName($field), $real_condition);
		}
		return $this->_buildIf("!({$real_condition})", $this->on_condition_error);
	}

	/**
	 * @access private
	 */
	function _getFullName($field) {
		return "document.{$this->form_name}.elements['{$field}'].value";
	}

}
?>