<?
PHP::requireClasses('HTMLPage', 'Request', 'Tag', 'File');

/**
 * This class represents a WYSISYG HTMLArea (from interactivetools.com)
 * that can be configured from php with methods instead of having
 * to write the javascript code each time you want to add a textarea.
 * Some parameters may be specified in the web.ini file (see WebApplication.php)
 * to avoid harcoding essential parameters. The parameters must be
 * grouped in the [HTMLArea] section and are the following:
 *
 * <ul>
 *   <li>editor_dir: The directory where the editor.js and other files
 *       are located. If not specified, it is assumed to be in the
 *       current directory.</li>
 *   <li>body_style: The css style of the HTMLArea</li>
 *   <li>stylesheet: The path to the stylesheet that the editor will use</li>
 * </ul>
 *
 * The above parameters help to provide common funcionality to all the
 * HTMLAreas in a web page.
 *
 * <b>Note</b>: in order to use this class you must also use the HTMLPage class
 * because a javascript string is added to the header part of the html page.
 *
 * @package HTMLArea
 */
class HTMLArea {

	/**#@+
	 * @access private
	 */
	var $name;
	var $width;
	var $height;
	var $contents;
	var $debug;

	var $font_names;
	var $font_sizes;
	var $font_styles;

	var $body_style;

	var $toolbar;
	/**#@-*/

	/**
	 * Constructs an HTMLArea with a name, width and height.
	 * @param string $name the name of the textarea
	 * @param integer $width the width of the textarea
	 * @param integer $height the height of the textarea
	 */
	function HTMLArea($name, $width, $height) {
		$this->name = $name;
		$this->width = $width;
		$this->height = $height;

		$this->font_names = array();
		$this->font_sizes = array();
		$this->font_styles = array();
	}

	/**
	 * Sets the initial contents of the HTMLArea.
	 * @param string $contents the contents of the HTMLArea
	 */
	function setContents($contents) {
		$this->contents = $contents;
	}

	/**
	 * Enables debug mode of the HTMLArea.
	 */
	function enableDebug() {
		$this->debug = true;
	}

	/**
	 * Adds a font name to display in the font names combo box.
	 * If no font names are added, the defaults defined in the editor.js
	 * file are used.
	 * @param string $user_name the name that will se the user
	 * @param string $real_name the real name of the font or fonts
	 */
	function addFontName($user_name, $real_name) {
		array_push($this->font_names, "'$user_name': '$real_name'");
	}

	/**
	 * Adds a font size to display in the font sizes combo box.
	 * If no font sizes are added, the defaults defined in the editor.js
	 * file are used.
	 * @param string $user_size the size that will se the user
	 * @param string $real_size the real size of the font
	 */
	function addFontSize($user_size, $real_size) {
		array_push($this->font_sizes, "'$user_size': '$real_size'");
	}

	/**
	 * Adds a font style to display in the font styles combo box.
	 * If no font styles are added, the defaults defined in the editor.js
	 * file are used.
	 * @param string $name the name that will se the user
	 * @param string $class_name the real name of the class
	 * @param string $class_style the style of the class
	 */
	function addFontStyle($name, $class_name, $class_style = '') {
		array_push($this->font_styles, "{ name: '$name', className: '$class_name', classStyle: '$class_style' }");
	}

	/**
	 * Sets the style of the body.
	 * @param string @style the style of the body
	 */
	function setBodyStyle($style) {
		$this->body_style = str_replace('\'', '"', $style);
	}

	/**
	 * Sets the toolbar of this HTMLArea.
	 * See the config.toolbar var of the HTMLArea object in the
	 * interactivetools.com documentation.
	 * If no toolbar is set, the default defined in the editor.js
	 * file is used.
	 * @param string $toolbar the toolbar
	 */
	function setToolbar($toolbar) {
		$this->toolbar = $toolbar;
	}

	/**
	 * Gets the HTML and the javascript code to
	 * activate it. At this moment, if the HTMLArea was not already
	 * loaded, it loads (adds header information to the HTMLPage).
	 * @return string the html code for the HTML Area
	 */
	function getHTMLCode() {
		// Cargo el HTMLArea con el metodo _load sólo si ya no fue cargado
		global $application;
		if (!$application->getAttribute('__HTMLArea_loaded')) {
			HTMLArea::_load();
			$application->setAttribute('__HTMLArea_loaded', true);
		}

		// Primero imprimo el textarea
		$tag = new Tag('textarea', true);
		$tag->setAttribute('name', $this->name);
		$tag->addNestedString($this->contents);
		$x = $tag->toString();

		// Ahora el javascript que lo activa
		$x .= "";

		return $x;
	}

	/**
	 * This function get a parameter from request and strips
	 * out the <p> and </p> tags from the beginning and ending
	 * of the string. This only works if the parameter was previously
	 * sent by an HTMLArea in a form, and only if an enter causes a
	 * <br> to be inserted in the HTMLArea.
	 * @param string $name the name of the parameter
	 * @return string the request value
	 */
	function getRequestParameter($name) {
		$string = trim(Request::getParameter($name));
		$length = strlen($string);
		if (strcasecmp(substr($string, 0, 3), '<p>') === 0 &&
			strcasecmp(substr($string, $length - 4, $length), '</P>') === 0) {
			$string = substr($string, 3, $length - 7);
		}
		if ($string == '&nbsp;') return false;
		
		// Hacerlo válido XHTML
		$string = str_replace('<BR>', '<br/>', $string);
		$string = str_replace('<BR/>', '<br/>', $string);
		$string = str_replace('<P>', '<p>', $string);
		$string = str_replace('</P>', '</p>', $string);
		$string = str_replace('<U>', '<u>', $string);
		$string = str_replace('</U>', '</u>', $string);
		$string = str_replace('<EM>', '<em>', $string);
		$string = str_replace('</EM>', '</em>', $string);
		$string = str_replace('<STRONG>', '<strong>', $string);
		$string = str_replace('</STRONG>', '</strong>', $string);
		$string = str_replace('<FONT', '<span', $string);
		$string = str_replace('</FONT>', '</span>', $string);
		$string = str_replace('<SPAN', '<span', $string);
		$string = str_replace('</SPAN>', '</span>', $string);
		$string = str_replace('<A', '<a', $string);
		$string = str_replace('</A>', '</a>', $string);
		$string = str_replace('target=_blank', 'target="_blank"', $string);
		$string = str_replace('target=_self', 'target="_self"', $string);
		$string = str_replace('&nbsp;', ' ', $string);
		
		// ESTO ES POR AHORA... 
		$string = str_replace('class=no_existo', 'class="no_existo"', $string);
		$string = str_replace('class=destacado', 'class="destacado"', $string);
		$string = str_replace('class=palabraEsperanto', 'class="palabraEsperanto"', $string);
		$string = str_replace('class=preguntaEsperanto', 'class="preguntaEsperanto"', $string);
		
		return $string;
	}

	/**#@+
	 * @access private
	 */
	function _getConfig() {
		if ($this->width) $x .= "config.width = '{$this->width}';";
		if ($this->width) $x .= "config.height = '{$this->height}';";
		if ($this->body_style) $x .= " config.bodyStyle = '{$this->body_style}';";
		if ($this->debug) $x .= "config.debug = 1;";

		if ($this->toolbar) {
			$x .= "config.toolbar = [ {$this->toolbar} ];";
		}

		if (sizeof($this->font_names) > 0) {
			$x .= "config.fontnames = {";
			$i = 0;
			foreach($this->font_names as $font) {
				$x .= "$font";
				if (++$i != sizeof($this->font_names)) $x .= ',';
			}
			$x .= "};";
		}

		if (sizeof($this->font_sizes) > 0) {
			$x .= "config.fontsizes = {";
			$i = 0;
			foreach($this->font_sizes as $font) {
				$x .= "$font";
				if (++$i != sizeof($this->font_sizes)) $x .= ',';
			}
			$x .= "};";
		}

		if (sizeof($this->font_styles) > 0) {
			$x .= "config.fontstyles = [";
			$i = 0;
			foreach($this->font_styles as $font) {
				$x .= "$font";
				if (++$i != sizeof($this->font_styles)) $x .= ',';
			}
			$x .= "];";
		}

		$stylesheet = $this->_getStylesheet();

		if ($stylesheet) {
			$x .= "config.stylesheet = '{$stylesheet}';";
		}

		return $x;
	}

	function _getEditorDir() {
		global $application;
		$section = $application->getIniSection('HTMLArea');
		if ($section) {
			$dir = $section->getParameter('editor_dir');
			FileUtils::ensureDir($dir);
			return $dir;
		}
		return '';
	}

	function _getStylesheet() {
		global $application;
		$section = $application->getIniSection('HTMLArea');
		if ($section) {
			return $section->getParameter('stylesheet');
		}
		return null;
	}

	function _load() {
		global $html_page;

		$x .= "<script language=\"Javascript1.2\">\n";
		$x .= "<!--\n";
		$x .= "_editor_url = '" . HTMLArea::_getEditorDir() . "';";
		$x .= "var win_ie_ver = parseFloat(navigator.appVersion.split('MSIE')[1]);";
		$x .= "if (navigator.userAgent.indexOf('Mac')        >= 0) { win_ie_ver = 0; }";
		$x .= "if (navigator.userAgent.indexOf('Windows CE') >= 0) { win_ie_ver = 0; }";
		$x .= "if (navigator.userAgent.indexOf('Opera')      >= 0) { win_ie_ver = 0; }";
		$x .= "if (win_ie_ver >= 5.5) {";
		$x .= "document.write('<scr' + 'ipt src=\"' +_editor_url+ 'editor.js\"');";
		$x .= "document.write(' language=\"Javascript1.2\"></scr' + 'ipt>');";
		$x .= "} else { document.write('<scr'+'ipt>function editor_generate() { return false; }</scr'+'ipt>'); }\n";
		$x .= "// -->\n";
		$x .= "</script>";

		$html_page->addHeader($x);
	}
	/**#@-*/

}
?>