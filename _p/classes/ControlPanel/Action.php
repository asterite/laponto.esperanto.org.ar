<?
PHP::requireClasses('ControlPanel/Common', 'Link', 'Request', 'Response');

/**
 * This class is used to redirect the page after an action, such as in
 * an edit action page or a delete action page. The purpouse is to gather
 * the parameters that concern to the list page (such as the page, order
 * field, order mode, etc.) in a single class.
 * The EditPage and ActionPage both forward those parameters to keep them
 * safe in the list page.
 * The general usage of this class is:
 *
 * <code>
 * $action = new ActionPage();
 * $action->goToPage('list.php');
 * </code>
 *
 * @package ControlPanel
 */
class ActionPage {

	/** @access private */
	var $link;

	/**
	 * Constructs an ActionPage.
	 */
	function ActionPage() {
		$this->link = new Link();

		$this->addExtraParameter(LIST_PARAM_PAGE);
		$this->addExtraParameter(LIST_PARAM_ORDER_FIELD);
		$this->addExtraParameter(LIST_PARAM_ORDER_MODE);
		$this->addExtraParameter(LIST_PARAM_SEARCH_FIELD);
		$this->addExtraParameter(LIST_PARAM_SEARCH_VALUE);
	}

	/**
	 * Adds a parameter to forward.
	 * @param string $name the parameter name
	 * @param string $value the parameter value
	 */
	function addParameter($name, $value) {
		$this->link->addParameter($name, $value);
	}

	/**
	 * Adds an extra parameter that may be coming from the list page.
	 * This method just calls addParameter($name, Request::getParameter($name))
	 * @param string $name the name of a parameter
	 */
	function addExtraParameter($name) {
		$this->addParameter($name, Request::getParameter($name));
	}

	/**
	 * Redirects to the specified page, with all the parameters set.
	 * @param string $page an URL or URI
	 */
	function goToPage($page) {
		$this->link->setPage($page);
		Response::sendRedirect($this->link->toString());
	}

}
?>