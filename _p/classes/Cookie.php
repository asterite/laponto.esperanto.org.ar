<?
/**
 * Interface class to handle cookies.
 *
 * The value of a Cookie can be anything since its value is serialized
 * before sending it (and proprly unserialized when it is read).
 *
 * @package Common
 */
class Cookie {

	/**#@+
	 * @access private
	 */
	var $expire;
	var $name;
	/**#@-*/

	/** The value of this cookie. */
	var $value;
	/** The path of this cookie. '/' by defualt. */
	var $path;
	/** The domain of this cookie. Nothing, by default. */
	var $domain;
	/** ¿Is this cookie secure? No, by default. */
	var $secure;

	/**
	 * Constructs a Cookie. If the cookie already exists in the user's browser,
	 * then its value is obtained from it. Else, an empty cookie
	 * is created, ready to be sent.
	 */
	function Cookie($name) {
		$this->name = $name;
		$this->value = PHP::getCookieParameter($name);
		if ($this->value) {
			$this->value = str_replace('\\\'', '\'', $this->value);
			$this->value = str_replace('\"', '"', $this->value);
			$this->value = unserialize($this->value);
		}
		$this->path = '/';
		$this->domain = '';
		$this->expire = 0;
		$this->secure = 0;
	}

	/**
	 * Determines if this cookie already exists in the user's browser.
	 * @return boolean true if the cookie exists, else false
	 */
	function exists() {
		return $this->value;
	}

	/**
	 * Sets the number of seconds left until this cookie expires, in seconds.
	 * @param integer $seconds the seconds
	 */
	function setTimeout($seconds) {
		$this->expire = time() + $seconds;
	}

	/**
	 * Sets an expiration date for this cookie.
	 * @param Date $date a Date
	 */
	function setExpirationDate($date) {
		$now = new Date();
		$this->setTimeout($date->compareTo($now));
	}

	/**
	 * Sends this cookie if its value exists.
	 * @return boolean true if the cookie was sent, else false
	 */
	function send() {
		if ($this->value) {
			return setcookie($this->name, serialize($this->value), $this->expire, $this->path, $this->domain, $this->secure);
		}
		return false;
	}

	/**
	 * Deletes this cookie from the user's browser.
	 */
	function delete() {
		setcookie($this->name, '');
		$this->value = '';
	}

}
?>