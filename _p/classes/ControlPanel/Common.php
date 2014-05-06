<?
/** The parameter for the page of the list */
define('LIST_PARAM_PAGE', '_page');
/** The parameter for the order field of the list */
define('LIST_PARAM_ORDER_FIELD', '_order_field');
/** The parameter for the order mode of the list */
define('LIST_PARAM_ORDER_MODE', '_order_mode');
/** The parameter for the search field of the list */
define('LIST_PARAM_SEARCH_FIELD', '_search_field');
/** The parameter for the search value of the list */
define('LIST_PARAM_SEARCH_VALUE', '_search_value');

/**
 * Requires the specified renderers for inclusion.
 * Example:
 * <code>
 * requireRenderer('TextBox', 'TextArea');
 * </code>
 * requires the TextBoxRenderer and TextAreaRenderer located in
 * _p/ControlPanel/Renderers/
 */
function requireRenderers($renderers) {
	$args = func_get_args();
	foreach($args as $arg) PHP::requireClasses("ControlPanel/Renderers/{$arg}Renderer");
}

/**
 * This class provides a static method that invokes
 * object methods recursively, or get them variables
 * recursively, or both actions mixed.
 * See method instructions.
 *
 * @package ControlPanel
 * @static
 */
class MethodInvoker {

	/**
	 * Returns a call on the object.
	 * A call is something of the form:
	 * object->method1()->method2()->...->methodN()
	 * or:
	 * object->var1->var2->...->varN
	 * or mixed:
	 * object->method1()->var1->method2()->method3()->var2->...->etc.
	 * PHP does not support chain method invokation, but this method
	 * does it.
	 * Call must begin with the first var or method invokation,
	 * not with the "->" string.
	 * Returns the last var requested, or the return value of the last
	 * method invoked.
	 * If $call is null, then the object is returned as is.
	 * If a method does not exist, null is returned.
	 * @param string $call see method description
	 * @static
	 */
	function chain(&$object, $call) {
		if (is_null($call)) return $object;

		$value = $object;
		
		$members = explode('->', $call);
		
		foreach($members as $member) {
			if (!is_object($value)) return null;
			if (strpos($member, ')') == strlen($member) - 1) {
				$method = substr($member, 0, strpos($member, '('));
				if (method_exists($value, $method)) {
					eval("\$value = \$value->$member;");
				} else {
					return null;
				}
			} else {
				eval("\$value = \$value->$member;");
			}
		}
		
		return $value;
	}

}

/** @access private */
class DescriptionProperties {

	var $_title;
	var $_instructions;
	var $_help_page;

	function hasTitle() {
		return $this->_title;
	}

	function getTitle() {
		return $this->_title;
	}

	function hasInstructions() {
		return $this->_instructions;
	}

	function getInstructions() {
		return $this->_instructions;
	}

	function hasHelpPage() {
		return $this->_help_page;
	}

	function getHelpPage() {
		return $this->_help_page;
	}

}
?>