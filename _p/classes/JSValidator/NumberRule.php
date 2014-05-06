<?
PHP::requireClasses('JSValidator/RequiredRule');

/**
 * This validator applies various rules relative to numbers
 * over a single field. The check that it allways performd by
 * default is that the field value corresponds to a number.
 * To perform conditional rules over more than one field, user
 * the ConditionRule class.
 *
 * @package JSValidator
 * @subpackage Rules
 */
class NumberRule extends RequiredRule {

	/**#@+
	 * @access private
	 */
	var $on_nan_error;
	var $on_range_error;
	var $min;
	var $min_inclusive;
	var $max;
	var $max_inclusive;
	var $accept_float;
	var $on_float_error;
	/**#@-*/

	/**
	 * Constructs a NumberRule.
	 * @param string $field_name the field name to validate
	 * @param boolean $accept_float does this field accept float values?
	 * @param boolean $is_required is this field required?
	 */
	function NumberRule($field_name, $accept_float = false, $is_required = true) {
		$this->RequiredRule($field_name, $is_required);
		$this->accept_float = $accept_float;
	}

	/**
	 * Sets the minimum value this field can contain.
	 * @param number $value a value
	 * @param inclusive true: the number is inclusive, false: exclusive
	 */
	function setMinimum($value, $inclusive = true) {
		$this->min = $value;
		$this->min_inclusive = $inclusive;
	}

	/**
	 * Sets the maximum value this field can contain.
	 * @param number $value a value
	 * @param inclusive true: the number is inclusive, false: exclusive
	 */
	function setMaximum($value, $inclusive = true) {
		$this->max = $value;
		$this->max_inclusive = $inclusive;
	}

	/**
	 * Sets the javascript code to execute if the field is not a number (nan).
	 * @param string $error a javascript code
	 */
	function setOnNaNError($error) {
		$this->on_nan_error = $error;
	}

	/**
	 * Sets the javascript code to execute if the field is not in range minimum - maximum.
	 * @param string $error a javascript code
	 */
	function setOnRangeError($error) {
		$this->on_range_error = $error;
	}

	/**
	 * Sets the javascript code to execute if the field does not
	 * accepts float and the field has a float value
	 * @param string $error a javascript code
	 */
	function setOnFloatError($error) {
		$this->on_float_error = $error;
	}

	/**
	 * @access private
	 */
	function _getAuxiliaryFilenames() {
		$files = parent::_getAuxiliaryFilenames();
		array_push($files, 'number.js');
		return $files;
	}

	function _getRequiredValidationCode() {
		$value = $this->_getFullName() . '.value';
		$string = $this->_buildIf("isNaN($value)", $this->on_nan_error);
		if (isset($this->min)) {
			if ($this->min_inclusive) {
				$string .= "\n" . $this->_buildIf("$value < {$this->min}", $this->on_range_error);
			} else {
				$string .= "\n" . $this->_buildIf("$value <= {$this->min}", $this->on_range_error);
			}
		}
		if (isset($this->max)) {
			if ($this->max_inclusive) {
				$string .= "\n" . $this->_buildIf("$value > {$this->max}", $this->on_range_error);
			} else {
				$string .= "\n" . $this->_buildIf("$value >= {$this->max}", $this->on_range_error);
			}
		}
		if (!$this->accept_float) {
			$string .= "\n" . $this->_buildIf("_JSValidator_number_isFloat($value)", $this->on_float_error);
		}
		return $string;
	}

}
?>