<?
PHP::requireClasses('Arrays');

/**
 * This class helps to build links like "page?name1=value1&name2=value2".
 * Also provides a static method to ensure the protocol of a link.
 * @package Common
 */
class Link {

	/**#@+
	 * @access private
	 */
	var $page;
	var $params;
	/**#@-*/

	/**
	 * Constructs a Link for a page.
	 * @param string $page the page
	 */
	function Link($page = '') {
		$this->page = $page;
		$this->params = array();
	}

	/**
	 * Sets the page of the link.
	 * @param string $page the page
	 */
	function setPage($page) {
		$this->page = $page;
	}

	/**
	 * Adds a parameter to the link.
	 * This allowsto send multiple parameter values under
	 * the same name.
	 * @param string $name the name of the parameter
	 * @param string $value the value of the parameter
	 */
	function addParameter($name, $value) {
		if (!$this->params[$name]) {
			$this->params[$name] = array();
		}
		array_push($this->params[$name], $value);
	}

	/**
	 * Sets a parameter to the link.
	 * @param string $name the name of the parameter
	 * @param string $value the value of the parameter
	 */
	function setParameter($name, $value) {
		$this->params[$name] = array();
		array_push($this->params[$name], $value);
	}

	/**
	 * Removes a parameter from the link.
	 * @param string $name the name of the parameter
	 */
	function removeParameter($name) {
		$this->params[$name] = array();
	}

	/**
	 * Returns the formed link.
	 * @return string the link
	 */
	function toString() {
		$link = $this->page;
		if (sizeof($this->params) == 0) {
			return $link;
		} else {
			$link .= '?';
			foreach($this->params as $key => $values) {
				foreach ($values as $value) {
					$link .= $key . '=' . urlencode($value);
					$link .= '&';
				}
			}
			return substr($link, 0, strlen($link) - 1);
		}
	}

	/**
	 * Checks if the link starts with a valid protocol
	 * ('file', 'ftp', 'gopher', 'http', 'https', 'mailto', 'news', 'telnet', 'wais').
	 * If not, then the link is added the specified protocol.
	 * If $link is an empty string, then an empty string is returned
	 * @param string $link the link to ensure
	 * @param string $protocol (default = 'http') the protocol to use if the link
	 * does not start with a valid protocol
	 * @return the link, ensured
	 * @static
	 */
	function ensureProtocol($link, $protocol = 'http') {
		if (strlen(trim($link)) == 0) return '';
		// Me fijo si el link empieza con un protocolo válido.
		// Si no es así, es MUY probable que el protocolo se http
		$protocols = array('file', 'ftp', 'gopher', 'http', 'https', 'mailto', 'news', 'telnet', 'wais');
		$parts = explode(':', $link);
		if (is_null(Arrays::search($protocols, $parts[0]))) {
			$link = $protocol . '://' . $link;
		}
		return $link;
	}

}
?>