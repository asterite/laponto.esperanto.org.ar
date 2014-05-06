<?
PHP::requireClasses('JSValidator');

/**
 * This rule is for validating that three combo boxes where
 * each of them contain a day, a month and an year respectively,
 * forms a valid date.
 *
 * @package JSValidator
 * @subpackage Rules
 */
class DateComboBoxesRule extends ValidationRule {

	/**#@+
	 * @access private
	 */
	var $year;
	var $month;
	var $day;
	var $on_date_error;
	/**#@-*/

	/**
	 * Constructs a DateComboBoxesRule for three combo boxes.
	 * @param string $day the name of the day combo box
	 * @param string $month the name of the month combo box
	 * @param string $year the name of the year combo box
	 */
	function DateComboBoxesRule($day, $month, $year) {
		$this->year = $year;
		$this->month = $month;
		$this->day = $day;
	}

	/**
	 * Sets the javascript code to execute if the three combo boxes
	 * don't form a valid date.
	 * @param string $error a javascript code
	 */
	function setOnDateError($error) {
		$this->on_date_error = $error;
	}

	/**
	 * @access private
	 */
	function _getFullNameAndValue($combo_name) {
		return "document.{$this->form_name}.elements['{$combo_name}'].value";
	}

	/**
	 * @access private
	 */
	function _getAuxiliaryFilenames() {
		return array('date.js');
	}

	function _getValidationCode() {
		$year = $this->_getFullNameAndValue($this->year);
		$month = $this->_getFullNameAndValue($this->month);
		$day = $this->_getFullNameAndValue($this->day);
		return 	$this->_buildIf("!_JSValidator_date_isValidDate($day, $month, $year)", $this->on_date_error);
	}

}
?>