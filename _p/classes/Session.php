<?
/**
 * Provides a way to identify a user across more than one page request or visit to a
 * Web site and to store information about that user.
 *
 * If an object is stored in the session, before it is retrieved the file that contains
 * the definition of the class of the object must be included.
 *
 * @package Common
 * @static
 */
class Session {

	/**
	 * Binds a variable to this session, using the name specified.
	 * @param string $name the name of the variable
	 * @param midex $value the value to bind
	 * @static
	 */
	function setAttribute($name, $value) {
		session_start();
		PHP::setSessionParameter($name, $value);
		session_write_close();
	}

	/**
	 * Sets an item of an array in session.
	 * This methods gets the attribute, modifies it and the saves it.
	 * @param $array_name string the array in session to modifiy
	 * @param $index mixed the index of the array
	 * @param $value mixed the value to put or replace in the array
	 * @static
	 */
	function setArrayItem($array_name, $index, $value) {
		$array = Session::getAttribute($array_name);
		if (!is_array($array)) $array = array();
		$array{$index} = $value;
		Session::setAttribute($array_name, $array);
	}

	/**
	 * Retrieves an item of an array in session.
	 * @param $array_name string the array in session
	 * @param $index mixed the index of the array
	 * @static
	 */
	function getArrayItem($array_name, $index) {
		$array = Session::getAttribute($array_name);
		if (is_array($array)) {
			return $array{$index};
		}
		return null;
	}

	/**
	 * Returns the variable bound with the specified name in this session,
	 * or null if no object is bound under the name.
	 * @param string $name the name of the variable
	 * @static
	 */
	function getAttribute($name) {
		session_start();
		$param = PHP::getSessionParameter($name);
		session_write_close();
		return $param;
	}

	/**
	 * Removes the variable bound with the specified name from this session.
	 * @param string $name the name of the variable
	 * @static
	 */
	function removeAttribute($name) {
		session_start();
		PHP::setSessionParameter($name, null);
		session_write_close();
	}

	/**
	 * Returns the id of the current session.
	 * @return string the id of the current session
	 * @static
	 */
	function getId() {
		session_start();
		$id = session_id();
		session_write_close();
		return $id;
	}

	/**
	 * Destroys the current session.
	 * @static
	 */
	function destroy() {
		session_start();
		session_destroy();
		session_write_close();
	}

}
?>