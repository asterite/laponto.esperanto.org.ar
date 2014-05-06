<?
PHP::requireClasses('Date');

/**
 * Defines an object to provide client request information.
 * The methods defined in this class are all static.
 * @package Common
 * @static.
 */
class Request {

	/**
	 * Returns the value of a request parameter,
	 * or $default if the parameter does not exist.
	 * @param string $name the name of the parameter
	 * @param string $default the default value to return if no parameter was found under $name
	 * @return string the value of the parameter, or $default if not found
	 * @static
	 */
	function getParameter($name, $default = null) {
		$value = PHP::getRequestParameter($name);
		if (!isset($value)) return $default;
		if (is_array($value)) return $value;
		if ($value === '') return $default;
		$value = str_replace("\\\\", "\\", $value);
		$value = str_replace("\\'", "'", $value);
		$value = str_replace("\\\"", "\"", $value);
		return $value;
	}

	/**
	 * Returns true if the request parameter value is 'on', else false.
	 * @return boolean true if the parameter is 'on', else false
	 * @static
	 */
	function getBoolean($name) {
		return Request::getParameter($name) == 'on';
	}

	/**
	 * Fills an object with properties in request.
	 * @param object $object the object to fill
	 * @param string[] $parameters the name of the parameters
	 * @static
	 */
	function fillObject(&$object, $parameters) {
		foreach($parameters as $parameter) {
			$object->$parameter = Request::getParameter($parameter);
		}
	}

	/**
	 * Returns a Date object for the specified year, month and day parameters
	 * obtained from the request.
	 * @return Date a Date
	 * @static
	 */
	function getDate($year, $month, $day) {
		return Date::getDate(Request::getParameter($year), Request::getParameter($month), Request::getParameter($day));
	}

	/**
	 * Calls Request::getDate("{$name}_year", "{$name}_month", "{$name}_day").
	 */
	function getSDate($name) {
		return Request::getDate("{$name}_year", "{$name}_month", "{$name}_day");
	}

	/**
	 * Returns a Date object for the specified hour, minute and second parameters
	 * obtained from the request.
	 * @return Date a Date
	 * @static
	 */
	function getTime($hour, $minute, $second) {
		return Date::getTime(Request::getParameter($hour), Request::getParameter($minute), Request::getParameter($second));
	}


	/**
	 * Calls Request::getDate("{$name}_hour", "{$name}_minute", "{$name}_second").
	 */
	function getSTime($name) {
		return Request::getTime("{$name}_hour", "{$name}_minute", "{$name}_second");
	}

	/**
	 * Returns a Date object for the specified year, month, day, hour, minute and second parameters
	 * obtained from the request.
	 * @return Date a Date
	 * @static
	 */
	function getDateTime($year, $month, $day, $minute, $hour, $second) {
		return Date::getDateTime(Request::getParameter($year), Request::getParameter($month), Request::getParameter($day),
			Request::getParameter($hour), Request::getParameter($minute), Request::getParameter($second));
	}

	/**
	 * Calls Request::getDate("{$name}_year", "{$name}_month", "{$name}_day", "{$name}_hour", "{$name}_minute", "{$name}_second").
	 */
	function getSDateTime($name) {
		return Request::getDateTime("{$name}_year", "{$name}_month", "{$name}_day", "{$name}_hour", "{$name}_minute", "{$name}_second");
	}

}
?>