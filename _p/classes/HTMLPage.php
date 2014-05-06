<?
PHP::requireClasses('Tag');

/**#@+
 * @access private
 */
define('__RESOURCE_TYPE_JS', 1);
define('__RESOURCE_TYPE_CSS', 2);
/**#@-*/

/**
 * This class acts as a buffer to accumulate information about
 * and HTML page header and body tag along in the page. For that purpouse
 * a global variable named "html_page" exists in the page. You can obtain
 * it and add resources on demand to the page (like javascript and css external files)
 * or even set an attribute of the body tag.
 * Just call the start() method, print all the HTML that should be printed
 * after the body tag, and finnaly call the end() method to output the
 * resulting HTML page. In the meantime, resources can silently be added to
 * the HTML page.
 * This is very usefull to maintain the standard structure of an html page.
 * For example, with this class you can maintain all your javascript tags
 * with code between the header tags using the addHeader() method.
 *
 * @package Common
 */
class HTMLPage {

	/**#@+
	 * @access private
	 */
	var $xhtml;
	var $title;
	var $metas;
	var $resources;
	var $body_tag;
	var $raw_headers;

	var $html_page;
	/**#@-*/

	/**
	 * This constructor is called once per html page and
	 * the singleton instance is saved in the global $html_page var.
	 */
	function HTMLPage() {
		$this->resources = array();
		$this->metas = array();
		$this->body_tag = new Tag('body', true);
		$this->xhtml = false;
	}
	
	function setIsXHTML($value) {
		$this->xhtml = $value;
	}

	/**
	 * Sets the title of this HTMLPage.
	 * @param string $title the title of the page
	 */
	function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the current title of this HTMLPage.
	 * This method is very usefull for sections in the html
	 * page where you want to append a text to the current title.
	 * @return string the title of this HTMLPage
	 */
	function getTitle() {
		return $this->title;
	}

	/**
	 * Shortcut method to append some text to the title of the page.
	 * @param string $text the text to append
	 */
	function appendToTitle($text) {
		$title = $this->getTitle();
		$title .= $text;
		$this->setTitle($title);
	}

	/**
	 * Adds a JavaScript link in the head of the html page, if it is not
	 * alread present in this page.
	 * @param string $path the path to the JavaScript file
	 */
	function addJS($path) {
		$this->resources[$path] = __RESOURCE_TYPE_JS;
	}

	/**
	 * Adds a CSS link in the head of the html page, if it is not
	 * alread present in this page.
	 * @param string $path the path to the CSS file
	 */
	function addCSS($path) {
		$this->resources[$path] = __RESOURCE_TYPE_CSS;
	}

	/**
	 * Sets the value of a meta tag.
	 * @param string $name the name attribute of the tag
	 * @param string $content the content attribute of the tag
	 */
	function setMeta($name, $content) {
		$this->metas[$name] = $content;
	}

	/**
	 * Sets an attribute of the body tag.
	 * @param string $name the name of the attribute
	 * @param string $value the value of the attribute
	 */
	function setBodyAttribute($name, $value) {
		$this->body_tag->setAttribute($name, $value);
	}

	/**
	 * Gets an attribute of the body tag.
	 * @param string $name the name of the attribute
	 * @return string $value the value of the attribute
	 */
	function getBodyAttribute($name) {
		return $this->body_tag->getAttribute($name);
	}

	/**
	 * Shortcut method to append some text to an already
	 * existing body tag attribute. If the attribute is not
	 * present, then it is created.
	 * @param string $name the name of the attribute
	 * @return string $value the value to append to the attribute
	 */
	function appendToBodyAttribute($name, $value) {
		$new_value = $this->getBodyAttribute($name);
		$new_value .= $value;
		$this->setBodyAttribute($name, $new_value);
	}

	/**
	 * Adds a raw string to appear between the header tag
	 * of the HTML page.
	 * @param string $header a string
	 */
	function addHeader($header) {
		$this->raw_headers .= $header;
	}

	/**
	 * Invoke this method to indicate where the html page should begin.
	 * That is, where the content after the body tag begins.
	 */
	function begin() {
		ob_start();
	}

	/**
	 * Prints the resulting HTML page.
	 * @param boolean $call_php_end if true, this method will call PHP::end()
	 * at the end of its execution
	 */
	function end($call_php_end = true) {
		$buffer = ob_get_contents();
		ob_end_clean();

		global $html_page;
		$html_page->body_tag->addNestedString($buffer);
		if ($this->xhtml) {
			$x .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
		} else {
			$this->body_tag->setAttribute('topmargin', '0');
			$this->body_tag->setAttribute('leftmargin', '0');
			$this->body_tag->setAttribute('marginwidth', '0');
			$this->body_tag->setAttribute('marginheight', '0');
		}
		$x .= "<html>\n";
		$x .= "<head>\n";
		if ($html_page->title) {
			$x .= "<title>{$html_page->title}</title>\n";
		}
		$x .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"/>\n";
		foreach($html_page->metas as $name => $content) {
			$x .= "<meta name=\"{$name}\" content=\"{$content}\"/>\n";
		}
		foreach($html_page->resources as $path => $type) {
			$x .= $html_page->_getRenderedResource($path, $type) . "\n";
		}
		if ($html_page->raw_headers) {
			$x .= $html_page->raw_headers . "\n";
		}
		$x .= "</head>\n";
		$x .= $html_page->body_tag->toString() . "\n";
		$x .= "</html>";

		print $x;

		if ($call_php_end) {
			PHP::end();
		}
	}

	/**
	 * @access private
	 */
	function _getRenderedResource($path, $type) {
		switch($type) {
			case __RESOURCE_TYPE_JS:
				return "<script src=\"$path\" type=\"text/javascript\"></script>";
			case __RESOURCE_TYPE_CSS:
				return "<link href=\"$path\" rel=\"stylesheet\" type=\"text/css\"/>";
		}
	}

}

global $html_page;
$html_page = new HTMLPage();
?>
