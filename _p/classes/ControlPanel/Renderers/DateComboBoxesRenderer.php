<?
PHP::requireClasses('Date', 'Tag', 'ControlPanel/Renderers/ComboBoxRenderer');

/**
 * This renderer outputs three combo boxes corresponding to
 * a day combo box (values 1 through 31),
 * a month combo box (values 1 through 12) and
 * an year combo box (range specified in constructor).
 * Since this renderers outputs three inputs, their names and ids
 * are {$name}_day, {$name}_month and {$name}_year respectively.
 *
 * @implements EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class DateComboBoxesRenderer {

	/**#@+
	 * @access private
	 */
	var $begin_year;
	var $end_year;
	/**#@-*/

	/**
	 * Constructs a DateComboBoxesRenderer.
	 * @param int $begin_year (default: 1970) the minimum year shown in the year combo box.
	 * @param int $end_year (default: 2030) the maximum year shown in the year combo box.
	 */
	function DateComboBoxesRenderer($begin_year = 1970, $end_year = 2030) {
		$this->begin_year = $begin_year;
		$this->end_year = $end_year;
	}

	/**
	 * @return boolean true
	 */
	function spanRow() {
		return true;
	}

	/**
	 * Returns the three combo boxes.
	 * Their ids and names are {$name}_day, {$name}_month and {$name}_year,
	 * respectively.
	 */
	function renderEdit($name, $value) {
		$days = array();
		for ($i = 1; $i <= 31; $i++) $days{$i} = $i;
		$daysc = new ComboBoxRenderer($days);

		$months = array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
			7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');

		/*
		for ($i = 1; $i <= 12; $i++) {
			$months{$i} = $i;
		}
		*/

		$monthsc = new ComboBoxRenderer($months);

		$years = array();
		for ($i = $this->begin_year; $i <= $this->end_year; $i++) $years{$i} = $i;
		$yearsc = new ComboBoxRenderer($years);

		return $daysc->renderEdit("{$name}_day", $value->day) .
					 $monthsc->renderEdit("{$name}_month", $value->month) .
					 $yearsc->renderEdit("{$name}_year", $value->year);
	}

}
?>