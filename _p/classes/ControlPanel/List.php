<?
PHP::requireClasses('ControlPanel/Common', 'Link', 'Paginator', 'Request');
/**
 * The classes in this file help to build a page
 * to list generic data provided from a Browser.
 * You first prepare a list page through the ListPageOptions class
 * and then you can display the page in a template, obtaining the ListPage
 * object from the options.
 *
 * The basic structure of the page is the list itself, in a table
 * where the columns represent the fields of the DataObjects (but may
 * be some methods of it) and the rows group single DataObjects.
 * In each row, links to edit and delete the DataObjects are provided.
 * Links to custom pages may also be provided.
 * A link to an insert page may also be defined.
 *
 * The list may be paginated. You can indicate the number of results per page.
 * A list of pages can be listed to show the current page listed and to access
 * the non-visible pages. Numbers for showing the total number of results, and
 * the range of results shown are also provided.
 *
 * The page also supports for ordering the list by fields of the Objects
 * ascendently or descendently. This links apear in the headers of the table.
 *
 * Another feature is the ability to search in the table for a specified
 * value. The search supports searching in numeric and string fields, and in
 * the template page must be represented by a combo box from where to choose the
 * search field and a text box to input the search value.
 *
 * The last thing is that the page contains a description part to show
 * the title of the page, some instructions and a link to a help page.
 *
 * @package ControlPanel
 */


/**
 * This class maintains the options of a list page, such as the manager
 * from which the DataObjects are obtained, the fields of the DataObjects listed,
 * the number of results per page, search fields, etc.
 * From this class you can obtain a ListPage to use in a template page.
 *
 * @package ControlPanel
 */
class ListPageOptions {

	/**#@+
	 * @access private
	 */
	var $man;
	var $results_per_page;

	var $order_field;
	var $order_mode;

	var $search_fields;
	var $search_fields_count;

	var $list_fields;

	var $insert_page;
	var $insert_page_prefix;
	var $insert_page_sufix;

	var $edit_page;
	var $delete_page;

	var $custom_pages;

	var $extra_params;

	var $description;
	/**#@-*/

	/**
	 * Constructs a ListPageOptions for a particular
	 * DataObjectsManager.
	 * @param Browser $browser a Browser
	 */
	function ListPageOptions($browser) {
		$this->man = $browser;

		$this->search_fields = array();
		$this->list_fields = array();
		$this->custom_pages = array();
		$this->extra_params = array();
		$this->description = new DescriptionProperties();
	}

	/**
	 * Sets the results per page displayed. If not set, all the results
	 * will be displayed in a single page.
	 * @param int $number the number of DataObjects displayed per page
	 */
	function setResultsPerPage($number) {
		$this->results_per_page = $number;
	}

	/**
	 * Sets the default order of the list.
	 * @param string $field the database field name
	 * @param boolean $mode true for ascendent, false for descendent
	 */
	function setDefaultOrder($field, $mode) {
		$this->order_field = $field;
		$this->order_mode = $mode;
	}

	/**
	 * Adds a column to this list page.
	 * @param string $name the name of the column, that will apear in the headers
	 * @param string $call a call to get a variable or invoke a method, in chain.
	 * See method MethodInvoker::chain($object, $call) in file Common.php .
	 * @param ListRenderer $renderer the renderer for the value listed
	 * @param string $order_field (default = null) the name of the field that represents
	 * this field, and provides an order. If null, then no order is allowed for this field
	 * @param string $attributes a string representing attributes for the field. May, for example
	 * be a comma separated list of strings. An R may indicate that a value is Required, etc.
	 * Attributes are interpreted in a template you provide.
	 */
	function addColumn($name, $call, $renderer, $order_field = null, $attributes = null) {
		array_push($this->list_fields, new ListField($name, $call, $renderer, $order_field, $attributes));
	}

	/**
	 * Adds a search field to appear in the search combo box.
	 * @param string $name the descriptive name of the search, that will apear as a value
	 * in the combo box
	 * @param mixed the criteria (interpreted by a Browser in the addSearch() method) of the search.
	 * This is where the field(s) of the search are specified.
	 */
	function addSearch($name, $criteria) {
		array_push($this->search_fields, new SearchField(++$this->search_fields_count, $name, $criteria));
	}

	/**
	 * Sets the URI of the insert page.
	 * If this method is not called, the link to the insert
	 * page MUST NOT appear in the template page.
	 * @param string $page the URI of the insert page.
	 * @param string $page_prefix (default = '') the string to prepend to the page
	 * @param string $page_sufix (default = '') the string to append to the page
	 */
	function setInsertPage($page, $page_prefix = '', $page_sufix = '') {
		$this->insert_page = $page;
		$this->insert_page_prefix = $page_prefix;
		$this->insert_page_sufix = $page_sufix;
	}

	/**
	 * Sets the URI of the edit page.
	 * If this method is not called, the link to the edit
	 * page MUST NOT appear in the template page.
	 * @param string $page the URI of the edit page.
	 */
	function setEditPage($page) {
		$this->edit_page = $page;
	}

	/**
	 * Sets the URI of the delete page.
	 * If this method is not called, the link to the delete
	 * page MUST NOT appear in the template page.
	 * @param string $page the URI of the delete page.
	 */
	function setDeletePage($page) {
		$this->delete_page = $page;
	}

	/**
	 * Adds a link to a custom page, listed in each row.
	 * @param string $name the name to show in the list
	 * @param string $link the URL of the custom page
	 * @param string $target the HTML target where to open the page
	 * @param string $link_prefix the string to prepend to the link
	 * @param string $link_sufix the string to append to the link
	 */
	function addCustomPage($name, $link, $target = '_self', $link_prefix = '', $link_sufix = '') {
		array_push($this->custom_pages, new CustomPage($name, $link, $target, $link_prefix, $link_sufix));
	}

	/**
	 * Sets the title of this page, to show in the description
	 * part of the page.
	 * @param string $title the title of this page
	 */
	function setTitle($title) {
		$this->description->_title = $title;
	}

	/**
	 * Sets the instructions on how to use this page, to show in the description
	 * part of the page.
	 * @param string $instructions the instructions on how to use this page
	 */
	function setInstructions($instructions) {
		$this->description->_instructions = $instructions;
	}

	/**
	 * Sets the link to a page where a more detailed explanation on how to
	 * use this page will be shown.
	 * If not set, the page wont have a help page.
	 * @param string $help_page an URL or URI to the help page
	 */
	function setHelpPage($help_page) {
		$this->description->_help_page = $help_page;
	}

	/**
	 * Maybe a list page recieves a request parameter from another page.
	 * To preserve the parameter name and value in the list page, invoke
	 * this method. This is necessary because when the user performs a search
	 * or indicates an order to the list, those extra parameters would be lost.
	 * If added in this method, those parameters are preserved.
	 */
	function addParameter($name, $value) {
		$this->extra_params[$name] = $value;
	}

	/**
	 * Same as addParameter but the value is taken from request
	 */
	function addExtraParameter($name) {
		$this->extra_params[$name] = Request::getParameter($name);
	}

	/**
	 * Builds a link to the specified page, adding the parameters of the current page,
	 * the order values, and the extra parameters added.
	 */
	function buildShortLink($page) {
		$link = new Link($page);
		foreach($this->extra_params as $name => $value) {
			$link->setParameter($name, $value);
		}
		return $link->toString();
	}

	/**
	 * Builds a link to the specified page, adding the parameters of the current page,
	 * the search values, the order values, and the extra parameters added.
	 */
	function buildFullLink($page) {
		$link = new Link($page);
		$link->setParameter(LIST_PARAM_PAGE, $this->_getPage());
		$link->setParameter(LIST_PARAM_ORDER_FIELD, $this->_getOrderField());
		$link->setParameter(LIST_PARAM_ORDER_MODE, $this->_getOrderMode());
		$link->setParameter(LIST_PARAM_SEARCH_FIELD, $this->_getSearchField());
		$link->setParameter(LIST_PARAM_SEARCH_VALUE, $this->_getSearchValue());
		foreach($this->extra_params as $name => $value) {
			$link->setParameter($name, $value);
		}
		return $link->toString();
	}

	/**
	 * Gets the ListPage to render in the template page for the current options.
	 * @return ListPage the list page
	 */
	function getListPage() {

		$list = new ListPage();

		// Descripcion
		$list->_description_properties = &$this->description;

		// Resultados por pagina
		$list->_results_per_page = &$this->results_per_page;

		// Tiene alguna opcion entre editar, borrar o custom?
		$list->_has_options = $this->edit_page || $this->delete_page || sizeof($this->custom_pages) > 0;

		// Busqueda
		if ($this->_getSearchField() && !is_null($this->_getSearchValue())) {
			foreach($this->search_fields as $search) {
				if ($search->id == $this->_getSearchField()) {
					$this->man->addSearch($this->_getSearchValue(), $search->criteria);
					break;
				}
			}
		}
		// Orden
		if ($this->_getOrderField()) {
			$this->man->addOrder($this->_getOrderField(), $this->_getOrderMode());
		}
		// Paginado
		if ($this->results_per_page) {
			$this->man->setCurrentPage($this->_getPage());
			$this->man->setResultsPerPage($this->results_per_page);
		}
		// Hago el query del manager
		$this->man->query();

		// Averiguo los headers
		$list->_headers = array();
		foreach($this->list_fields as $field) {
			$header = new ListHeader();
			$header->_name = $field->name;
			if (!($field->order_field)) {
				$header->_allows_order = false;
			} else {
				$header->_allows_order = true;
				$header->_link_ascendent = $this->_getHeaderLink($field->order_field, 1);
				$header->_link_descendent = $this->_getHeaderLink($field->order_field, 0);
				if ($this->_getOrderField() == $field->order_field) {
					$header->_ascendent = $this->_getOrderMode();
					$header->_descendent = !$header->_ascendent;
				}
			}
			array_push($list->_headers, $header);
		}

		// Averiguo las filas
		$list->_rows = array();
		while($do = $this->man->next()) {
			$row = new ListRow();

			// Asigno a la fila el DataObject
			$row->_do = $do;

			// Averiguo las celdas
			foreach($this->list_fields as $field) {
				$value = MethodInvoker::chain($do, $field->call);
				$rendered_value = $field->renderer->renderList($value);
				$cell = new ListCell();
				$cell->_value = $rendered_value;
				$cell->_attributes = $field->renderer->getAttributes($value);
				array_push($row->_cells, $cell);
			}

			// Armo los links de edit y delete, si los hay
			$keys = $this->man->getPrimaryKeys();
			$link = new Link();
			foreach($keys as $key) {
				$link->setParameter($key, $do->$key);
			}
			foreach($this->extra_params as $name => $value) {
				$link->setParameter($name, $value);
			}
			// Custom
			foreach($this->custom_pages as $custom) {
				$custom2 = $custom;
				$link->setPage($custom->_link);
				$custom2->_link = htmlspecialchars($custom->_prefix . $link->toString() . $custom->_sufix, ENT_QUOTES);
				array_push($row->_custom_pages, $custom2);
			}
			$link->setParameter(LIST_PARAM_PAGE, $this->_getPage());
			$link->setParameter(LIST_PARAM_ORDER_FIELD, $this->_getOrderField());
			$link->setParameter(LIST_PARAM_ORDER_MODE, $this->_getOrderMode());
			$link->setParameter(LIST_PARAM_SEARCH_FIELD, $this->_getSearchField());
			$link->setParameter(LIST_PARAM_SEARCH_VALUE, $this->_getSearchValue());
			// Edit
			if ($this->edit_page) {
				$link->setPage($this->edit_page);
				$row->_link_edit_page = $link->toString();
			}
			// Delete
			if ($this->delete_page) {
				$link->setPage($this->delete_page);
				$row->_link_delete_page = $link->toString();
			}

			array_push($list->_rows, $row);
		}

		// Armo el link de insert
		if ($this->insert_page) {
			$list->_link_insert_page = htmlspecialchars($this->insert_page_prefix . $this->buildShortLink($this->insert_page) . $this->insert_page_sufix, ENT_QUOTES);
		}

		// Armo las propiedades de busqueda
		$list->_search_properties = new SearchProperties();
		$list->_search_properties->_hidden_fields = '<input type="hidden" name="'.LIST_PARAM_ORDER_FIELD.'" value="'.$this->_getOrderField().'">';
		$list->_search_properties->_hidden_fields .= '<input type="hidden" name="'.LIST_PARAM_ORDER_MODE.'" value="'.$this->_getOrderMode().'">';
		foreach($this->extra_params as $name => $value) {
			$list->_search_properties->_hidden_fields .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
		}
		foreach($this->search_fields as $field) {
			$option = new SearchOption();
			$option->name = $field->name;
			$option->value = $field->id;
			$option->is_selected = $this->_getSearchField() == $option->value;
			array_push($list->_search_properties->_options, $option);
		}
		$list->_search_properties->_search_value = $this->_getSearchValue();

		// Armo las propiedades de paginado
		$list->_pages_properties = new PagesProperties();

		$list->_pages_properties->_first_result = $this->man->getFirstResult();
		$list->_pages_properties->_last_result = $this->man->getLastResult();
		$list->_pages_properties->_total_results = $this->man->getTotalResults();
		$list->_pages_properties->_total_pages = $this->man->getTotalPages();
		$list->_pages_properties->_current_page = $this->_getPage();
		$pag = new Paginator($list->_pages_properties->_total_pages, $this->_getPage(), 7);
		$list->_pages_properties->_first_page = $pag->getFirstPage();
		$list->_pages_properties->_last_page = $pag->getLastPage();
		$list->_pages_properties->_static_link = '?'.LIST_PARAM_ORDER_FIELD.'='.$this->_getOrderField().
			'&'.LIST_PARAM_ORDER_MODE.'='.$this->_getOrderMode().
			'&'.LIST_PARAM_SEARCH_FIELD.'='.$this->_getSearchField().
			'&'.LIST_PARAM_SEARCH_VALUE.'='.$this->_getSearchValue();
		foreach($this->extra_params as $name => $value) {
			$list->_pages_properties->_static_link .= '&'.$name.'='.$value;
		}

		return $list;
	}

	/**#@+
	 * @access private
	 */
	function _getPage() {
		/**
		 * @BUG: No se porque a veces no vuelve el parametro "_page" de la pagina de edicion.
		 */
		$page = Request::getParameter(LIST_PARAM_PAGE, 1);
		if ($page < 1) $page = 1;
		return $page;
	}

	function _getOrderField() {
		return Request::getParameter(LIST_PARAM_ORDER_FIELD, $this->order_field);
	}

	function _getOrderMode() {
		return Request::getParameter(LIST_PARAM_ORDER_MODE, $this->order_mode);
	}

	function _getSearchField() {
		return Request::getParameter(LIST_PARAM_SEARCH_FIELD);
	}

	function _getSearchValue() {
		return Request::getParameter(LIST_PARAM_SEARCH_VALUE);
	}

	function _getHeaderLink($field, $mode) {
		$link =  '?'.LIST_PARAM_ORDER_FIELD.'='.$field.
			'&'.LIST_PARAM_ORDER_MODE.'='.$mode.
			'&'.LIST_PARAM_PAGE.'='.$this->_getPage().
			'&'.LIST_PARAM_SEARCH_FIELD.'='.$this->_getSearchField().
			'&'.LIST_PARAM_SEARCH_VALUE.'='.$this->_getSearchValue();
		foreach($this->extra_params as $name => $value) {
			$link .= '&'.$name.'='.$value;
		}
		return $link;
	}
	/**#@-*/

}

/** @access private */
class ListPage {

	var $_headers;
	var $_rows;
	var $_search_properties;
	var $_pages_properties;
	var $_has_options;
	var $_link_insert_page;
	var $_results_per_page;
	var $_description_properties;

	function getListHeaders() {
		return $this->_headers;
	}

	function getListRows() {
		return $this->_rows;
	}

	function getSearchProperties() {
		return $this->_search_properties;
	}

	function getPagesProperties() {
		return $this->_pages_properties;
	}

	function hasOptions() {
		return $this->_has_options;
	}

	function hasInsertPage() {
		return $this->_link_insert_page;
	}

	function getLinkInsertPage() {
		return $this->_link_insert_page;
	}

	function getResultsPerPage() {
		return $this->_results_per_page;
	}

	function getDescriptionProperties() {
		return $this->_description_properties;
	}

}

/** @access private */
class SearchProperties {

	var $_options;
	var $_hidden_fields;
	var $_search_value;

	function SearchProperties() {
		$this->_options = array();
		$this->_hidden_fields = array();
	}

	function getSelectInputName() {
		return LIST_PARAM_SEARCH_FIELD;
	}

	function getTextInputName() {
		return LIST_PARAM_SEARCH_VALUE;
	}

	function getTextInputValue() {
		return $this->_search_value;
	}

	function getHiddenInputs() {
		return $this->_hidden_fields;
	}

	function getOptions() {
		return $this->_options;
	}

	function hasOptions() {
		return sizeof($this->_options) > 0;
	}

}

/** @access private */
class PagesProperties {

	var $_first_page;
	var $_last_page;
	var $_total_pages;
	var $_current_page;

	var $_first_result;
	var $_last_result;
	var $_total_results;

	var $_static_link;

	function getFirstPage() {
		return $this->_first_page;
	}

	function getLastPage() {
		return $this->_last_page;
	}

	function getTotalPages() {
		return $this->_total_pages;
	}

	function getCurrentPage() {
		return $this->_current_page;
	}

	function hasPreviousPages() {
		return $this->getFirstPage() > 1;
	}

	function hasNextPages() {
		return $this->getLastPage() < $this->getTotalPages();
	}

	function getFirstResult() {
		return $this->_first_result;
	}

	function getLastResult() {
		return $this->_last_result;
	}

	function getTotalResults() {
		return $this->_total_results;
	}

	function getLinkForPage($number) {
		return $this->_static_link.'&'.LIST_PARAM_PAGE.'='.$number;
	}

}

/** @access private */
class SearchOption {

	var $name;
	var $value;
	var $is_selected;

	function getName() {
		return $this->name;
	}

	function getValue() {
		return $this->value;
	}

	function isSelected() {
		return $this->is_selected;
	}

}

/** @access private */
class ListHeader {

	var $_name;
	var $_ascendent;
	var $_descendent;
	var $_link_ascendent;
	var $_link_descendent;
	var $_allows_order;

	function getName() {
		return $this->_name;
	}

	function getLinkAscendentOrder() {
		return $this->_link_ascendent;
	}

	function getLinkDescendentOrder() {
		return $this->_link_descendent;
	}

	function isAscendentOrder() {
		return $this->_ascendent;
	}

	function isDescendentOrder() {
		return $this->_descendent;
	}

	function allowsOrder() {
		return $this->_allows_order;
	}

}

/** @access private */
class ListRow {

	var $_cells;
	var $_link_edit_page;
	var $_link_delete_page;
	var $_custom_pages;
	var $_do;

	function ListRow() {
		$this->_cells = array();
		$this->_custom_pages = array();
	}

	function getCells() {
		return $this->_cells;
	}

	function getLinkEditPage() {
		return $this->_link_edit_page;
	}

	function getLinkDeletePage() {
		return $this->_link_delete_page;
	}

	function hasEditPage() {
		return $this->_link_edit_page;
	}

	function hasDeletePage() {
		return $this->_link_delete_page;
	}

	function getCustomPages() {
		return $this->_custom_pages;
	}

	function getDataObject() {
		return $this->_do;
	}

}

/** @access private */
class ListCell {

	var $_value;
	var $_attributes;

	function getValue() {
		return $this->_value;
	}

	function getAttributes() {
		return $this->_attributes;
	}

}

/**
 * @access private
 */
class SearchField {

	var $id;
	var $name;
	var $criteria;

	function SearchField($id, $name, $criteria) {
		$this->id = $id;
		$this->name = $name;
		$this->criteria = $criteria;
	}

}

/**
 * @access private
 */
class ListField {

	var $name;
	var $order_field;
	var $call;
	var $renderer;
	var $default_value;
	var $attributes;

	function ListField($name, $call, $renderer, $order_field, $attributes) {
		$this->name = $name;
		$this->call = $call;
		$this->renderer = $renderer;
		$this->order_field = $order_field;
		$this->attributs = $attributes;
	}

}

/** @access private */
class CustomPage {

	var $_name;
	var $_link;
	var $_target;
	var $_prefix;
	var $_sufix;

	function CustomPage($name, $link, $target, $link_prefix, $link_sufix) {
		$this->_name = $name;
		$this->_link = $link;
		$this->_target = $target;
		$this->_prefix = $link_prefix;
		$this->_sufix = $link_sufix;
	}

	function getName() {
		return $this->_name;
	}

	function getLink() {
		return $this->_link;
	}

	function getTarget() {
		return $this->_target;
	}

}
?>