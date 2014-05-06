<?
PHP::requireClasses('Session');

define('_FORM_REMINDER_SESSION', '_FORM_REMINDER');

/**
 * When a user submits a form and some information is invalid, you go back to the form
 * page with an error message and you must fill the valid fields of the form. This class
 * is a helper to do that. In case of an error in the information, you must redirect to
 * the form page. First you create a form reminder, set the values to remind
 * and then save it under a key. In the form page you load it with that key. It will
 * only load if the page from where the submition came is the same as the page of the form!
 */
class FormReminder {

	var $values;

	/**
	 * Constructs a FormReminder.
	 */
	function FormReminder() {
		$this->values = array();
	}

	/**
	 * Sets a value to remind under a name.
	 */
	function set($name, $value) {
		$this->values[$name] = $value;
	}

	/**
	 * Gets a value reminded.
	 */
	function get($name) {
		return $this->values[$name];
	}

	/**
	 * Saves this reminder under a key.
	 */
	function save($key) {
		$referer = PHP::getServerParameter('HTTP_REFERER');
		$y = parse_url($referer);
		Session::setAttribute(_FORM_REMINDER_SESSION . $key, array($this, $y['path']));
	}

	/**
	 * Tries to load a reminder with a key. Returns the reminder on success, or
	 * false if the referer of the current page isn't the form's page.
	 */
	function load($key) {
		list($f, $x) = Session::getAttribute(_FORM_REMINDER_SESSION . $key);
		$referer = PHP::getServerParameter('HTTP_REFERER');
		if (!$referer) return false;
		$y = parse_url($referer);
		return $y['path'] == $x ? $f : false;
	}

	/**
	 * Removes a reminder with a key.
	 */
	function clear($key) {
		Session::removeAttribute(_FORM_REMINDER_SESSION . $key);
	}

}
?>