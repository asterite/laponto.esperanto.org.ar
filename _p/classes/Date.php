<?
/**
 * This class represents a Date in time.
 * @package Common
 */
class Date {

	/**
	 * The year of this date
	 * @var integer
	 */
	var $year;
	/**
	 * The month of this date
	 * @var integer
	 */
	var $month;
	/**
	 * The day of this date
	 * @var integer
	 */
	var $day;

	/**
	 * The hours of this date
	 * @var integer
	 */
	var $hour;
	/**
	 * The minutes of this date
	 * @var integer
	 */
	var $minute;
	/**
	 * The seconds of this date
	 * @var integer
	 */
	var $second;

	/**
	 * Constructs a Date. If $date is null then the current date
	 * will be used. Else, all of this timestamps are valid:
	 * <ul>
	 *   <li>'Y-m-d H:i:s'</li>
	 *   <li>'Y-m-d'</li>
	 *   <li>'H:i:s'</li>
	 *   <li>'YmdHis'</li>
	 * </ul>
	 * That is, the formats are common date/time database formats.
	 * @param string $date the timestamp to use, or nothing to get
	 * the current date.
	 */
	function Date($date = null) {
		if (is_null($date)) {
			$date = date('Y-m-d H:i:s');
		}
		if (preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $date)) {
			list($this->year, $this->month, $this->day, $this->hour, $this->minute, $this->second) =
				sscanf($date, '%04u-%02u-%02u %02u:%02u:%02u');
		} elseif (preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
			list($this->year, $this->month, $this->day) =
				sscanf($date, '%04u-%02u-%02u');
			$this->minute = 0;
			$this->hour = 0;
			$this->second = 0;
    } elseif (preg_match('/\d{2}:\d{2}:\d{2}/', $date)) {
			list($this->hour, $this->minute, $this->second) =
				sscanf($date, '%02u:%02u:%02u');
			$this->year = 0;
			$this->month = 0;
			$this->day = 0;
    } elseif (preg_match('/\d{14}/',$date)) {
    	list($this->year, $this->month, $this->day, $this->hour, $this->minute, $this->second) =
				sscanf($date, '%04u%02u%02u%02u%02u%02u');
  	}
	}

	/**
	 * Returns a Date object for the specified year, month and day values.
	 * @param mixed $year the year as an integer or string
	 * @param mixed $month the month as an integer or string
	 * @param mixed $day the day as an integer or string
	 * @return Date a Date object
	 * @static
	 */
	function getDate($year, $month, $day) {
		return new Date(sprintf('%04d-%02d-%02d', $year, $month, $day));
	}

	/**
	 * Returns a Date object for the specified hour, minute and second values.
	 * @param mixed $hour the hour as an integer or string
	 * @param mixed $minute the minute as an integer or string
	 * @param mixed $second the second as an integer or string
	 * @return Date a Date object
	 * @static
	 */
	function getTime($hour, $minute, $second) {
		return new Date(sprintf('%02d:%02d:%02d', $hour, $minute, $second));
	}

	/**
	 * Returns a Date object for the specified year, month, day, hour, minute and second values.
	 * @param mixed $year the year as an integer or string
	 * @param mixed $month the month as an integer or string
	 * @param mixed $day the day as an integer or string
	 * @param mixed $hour the hour as an integer or string
	 * @param mixed $minute the minute as an integer or string
	 * @param mixed $second the second as an integer or string
	 * @return Date a Date object
	 * @statci
	 */
	function getDateTime($year, $month, $day, $minute, $hour, $second) {
		return new Date(sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, $second));
	}

	/**
	 * Adds $years years to this date.
	 * @param integer $years the number of years to add
	 */
	function addYears($years) {
		$this->year += $years;
		$this->_recalculate();
	}

	/**
	 * Adds $months months to this date.
	 * @param integer $months the number of months to add
	 */
	function addMonths($months) {
		$this->month += $months;
		$this->_recalculate();
	}

	/**
	 * Adds $days days to this date.
	 * @param integer $days the number of days to add
	 */
	function addDays($days) {
		$this->day += $days;
		$this->_recalculate();
	}

	/**
	 * Adds $hours hours to this date.
	 * @param integer $hours the number of hours to add
	 */
	function addHours($hours) {
		$this->hour += $hours;
		$this->_recalculate();
	}

	/**
	 * Adds $minutes minutes to this date.
	 * @param integer $minutes the number of minutes to add
	 */
	function addMinutes($minutes) {
		$this->minute += $minutes;
		$this->_recalculate();
	}

	/**
	 * Adds $seconds seconds to this date.
	 * @param integer $seconds the number of seconds to add
	 */
	function addSeconds($seconds) {
		$this->second += $seconds;
		$this->_recalculate();
	}

	/**
	 * @access private
	 */
	function _recalculate($seconds = null) {
		if ($seconds == null) $seconds = $this->getSeconds();
		$date = date('Y-m-d H:i:s', $seconds);
		list($this->year, $this->month, $this->day, $this->hour, $this->minute, $this->second) =
				sscanf($date, '%04u-%02u-%02u %02u:%02u:%02u');
	}

	/**
	 * Sets the number of seconds elapsed since the Unix Epoch (January 1 1970 00:00:00 GMT)
	 * of this date.
	 * @param integer the seconds
	 */
	function setSeconds($seconds) {
		$this->_recalculate($seconds);
	}

	/**
	 * Returns the number of seconds elapsed since the Unix Epoch (January 1 1970 00:00:00 GMT)
	 * @return integer the seconds
	 */
	function getSeconds() {
		return mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
	}

	/**
	 * Compares this date to another date. Returns the difference of seconds
	 * between this date and the other date.
	 * @param Date $date a Date object
	 * @return the difference of seconds between this date and the other date
	 */
	function compareTo($date) {
		return $this->getSeconds() - $date->getSeconds();
	}

	/**
	 * Determines if this date equals another date.
	 */
	function equals($date) {
		return $this->compareTo($date) === 0;
	}

	/**
	 * Formats this date with the date() function provided with PHP.
	 * @param string $format the format according to date()
	 * return the formatted date
	 */
	function format($format) {
		if ($this->year == 0 and $this->month == 0 and $this->day == 0) {
			$value = $this;
			$value->year = 1970;
			$value->month = 1;
			$value->day = 1;
			$timestamp = $value->getSeconds();
			return date($format, $timestamp);
		} else {
			return date($format, $this->getSeconds());
		}
	}

}
?>