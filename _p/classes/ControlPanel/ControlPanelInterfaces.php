<?
/**
 * Classes implementing the Browser interface can be passed
 * to a ListPageOptions or to an EditPageOptions.
 *
 * @package ControlPanel
 * @subpackage Interfaces
 * @abstract
 */
class Browser {

	/**
	 * Adds a search in a field by a value.
	 * @param string $search_value the value to search
	 * @param mixed $search_criteria the criteria of the search.
	 * This is where the field(s) of the search are specified.
	 */
	function addSearch($search_value, $search_criteria) {}

	/**
	 * Adds an order by a field.
	 * @param string $field_name the name of the field
	 * @param boolean $ascendent ascendent or descendent order
	 */
	function addOrder($field_name, $ascendent = true) {}

	/**
   * Sets the current page to show. If the number of the current
   * page is bigger than the number of total pages, than
   * the number of the current page will be the number of total
   * pages.
   * @param integer $page the page to show
	 */
	function setCurrentPage($page) {}

	/**
	 * Sets the number of results to show in a page.
	 * after performing the query.
	 * @param integer $page the number of results to show in a page
	 */
	function setResultsPerPage($results_per_page) {}

	/**
	 * Computes all the parameters set in the previous methods
	 * and prepares the objects to return.
	 */
	function query() {}

	/**
	 * Returns the next object in this browser, or false
	 * if there are no more objects.
	 */
	function next() {}

	/**
	 * Returns the name of the fields that represent the
	 * primary keys of the objects handled by this browser.
	 * @return array an array with the keys
	 */
	function getPrimaryKeys() {}

	/**
   * Returns the number of the first result shown.
   * @return integer the number of the first result shown
	 */
	function getFirstResult() {}

	/**
   * Returns the number of the last result shown.
   * @return integer the number of the last result shown
	 */
	function getLastResult() {}

	/**
	 * Returns the total results in the database that matches
	 * the settings, ignoring the "current page / results per page"
	 * settings.
	 * @return integer the total number of results, ignoring the
	 * "current page / results per page" setting
	 */
	function getTotalResults() {}

	/**
   * Returns the total number of pages found, according to the
   * "current page / results per page" settings, if set, or
   * 1 if not.
   * @return integer the total number of pages found
	 */
	function getTotalPages() {}

	/**
	 * Retrieves an object given its keys (or any of them).
	 * The keys not necesarily has to be the object primary keys.
	 * The $keys variable must be an associative array where the keys
	 * are the name of the fields, and the values, the field values.
	 * @param array $keys an associative array where the keys
	 * are the name of the fields, and the values, the field values
	 * @return the object or false if no object was found under those keys
	 */
	function getFromKeys($keys) {}

	/**
	 * Returns the name of the class of the objects
	 * managed by this browser.
	 * @return string the classname of the objects in this browser
	 */
	function getObjectsClassName() {}


}

/**
 * The EditRenderer interface provides a method,
 * renderEdit($name, $value), to format a value to present in an edit row.
 * The renderer should print some sort of an HTMLInput, altough other
 * information may be printed, or multiple inputs. In the last case, each
 * input should have the name attribute prefixed with the $name var.
 * Before a value is shown in a column, it is passed through an EditRenderer
 * that converts the value into a string.
 *
 * @package ControlPanel
 * @subpackage Interfaces
 * @abstract
 */
class EditRenderer {

	/**
	 * Determines if this renderer should span a whole new row with a title
	 * and a value, instead of the value itself. This is usefull for hidden
	 * inputs and the like.
	 * If this method returns false, the rendered value will be outputed
	 * in the EditPage::getHiddenFields() method.
	 * @return boolean true if this renderer should span a new row,
	 * else false
	 */
	function spanRow() {}

	/**
	 * Transforms a value into one or HTML inputs or tags.
	 * @param string $name the name of the input
	 * @param string $value the value to be rendered
	 */
	function renderEdit($name, $value) {}

}

/**
 * The ListRenderer interface provides one single method,
 * renderList($value), to format a value to present in a list column.
 * Before a value is shown in a column, it is passed through a ListRenderer
 * that converts the value into a string.
 * @package ControlPanel
 * @subpackage Interfaces
 * @abstract
 */
class ListRenderer {

	/**
	 * Transforms a value into a string.
	 * @param mixed $value a value
	 * @return mixed the tranformed value
	 */
	function renderList($value) {}

	/**
	 * Returns the attributes of the cell in which the value will be rendered.
	 * @param mixed $value the value that is to be rendered
	 * @return array an associative array with attributes
	 */
	function getAttributes($value) {}

}

/**
 * This interface can be obtained from an EditPageOptions.
 * Use it to render the template edit page.
 * @abstract
 */
class EditPage {

	/**#@+
	 * @access private
	 */
	var $_hidden_fields;
	var $_edit_rows;
	var $_validation_rules;
	var $_action_page;
	var $_cancel_page;
	var $_description_properties;
	/**#@-*/

	function EditPage() {
		$this->_edit_rows = array();
		$this->_validation_rules = array();
	}

	/**
	 * Returns the hidden fields that MUST be printed
	 * in the form.
	 * Among the hidden fields returned are the ones that correspond
	 * to the page, order field, orde mode, search field and
	 * search value of the previous list page.
	 * @return string the hidden fields of the edit form
	 */
	function getHiddenFields() {
		return $this->_hidden_fields;
	}

	/**
	 * Returns an array containing the EditRows of the form.
	 * @return EditRow[] the edit rows
	 */
	function getEditRows() {
		return $this->_edit_rows;
	}

	/**
	 * Returns the JSValidator to validate the edit form.
	 * @param string $form_name the name of the form to validate
	 * @param string $function_name the name of a function to be called in the onSubmit
	 * attribute of the form
	 * @return the JSValidator for the specified form
	 */
	function getJSValidator($form_name, $function_name) {
		$validator = new JSValidator($form_name, $function_name);
		foreach($this->_validation_rules as $rule) {
			$validator->addValidationRule($rule);
		}
		return $validator;
	}

	/**
	 * Returns the URL that MUST be printed in the action attribute
	 * of the form.
	 * @return string the URL that the form will redirect when submited
	 */
	function getActionPage() {
		return $this->_action_page;
	}

	/**
	 * Determines if the edit form has a cancel page.
	 * @return boolean true if the edit form has a cancel page, else false
	 */
	function hasCancelPage() {
		return $this->_cancel_page;
	}

	/**
	 * Returns the URL that MUST be printed in the action of the cancel
	 * button of the edit form.
	 * @return string the URL that the form will redirect when canceled
	 */
	function getCancelPage() {
		return $this->_cancel_page;
	}

	/**
	 * Returns the DescriptionProperties object containing all
	 * the information relative to the description of the page,
	 * such as the title and instructions.
	 * @return DescriptionProperties the description properties
	 */
	function getDescriptionProperties() {
		return $this->_description_properties;
	}

}

/**
 * Represents a single row of the edit page.
 * You can obtain an instance of this class from an
 * EditPage.
 * @abstract
 */
class EditRow {

	/**
	 * Gets the name of the input in this row.
	 * You use this generally when attributes are present.
	 * @return string the name of the input in this row
	 */
	function getInputName() {}

	/**
	 * Gets the descriptive name of this row.
	 * @return string the name of this row
	 */
	function getName() {}

	/**
	 * Gets the rendered value of this row.
	 * @return string the rendered value of this row
	 */
	function getValue() {}

	/**
	 * Gets the attributes of this row.
	 * @return string the attributes of this row
	 */
	function getAttributes() {}

	/**
	 * True if this row is actually a separator (the label is
	 * the name of this row).
	 */
	function isSeparator() {}

}

/**
 * This interface can be obtained from a ListPageOptions.
 * Use it to render the template list page.
 * @abstract
 */
class ListPage {

	/**#@+
	 * @access private
	 */
	var $_headers;
	var $_rows;
	var $_search_properties;
	var $_pages_properties;
	var $_has_options;
	var $_link_insert_page;
	var $_results_per_page;
	var $_description_properties;
	/**#@-*/

	/**
	 * Returns an array containing the headers of the list
	 * as ListHeader objects.
	 * @return ListHeader[] the headers of the list
	 */
	function getListHeaders() {
		return $this->_headers;
	}

	/**
	 * Returns an array containing the rows of the list
	 * as ListRow objects.
	 * @return ListRow[] the rows of the list
	 */
	function getListRows() {
		return $this->_rows;
	}

	/**
	 * Returns the SearchProperties object containing all
	 * the information relative to the "search part" of the page.
	 * @return SearchProperties the search properties
	 */
	function getSearchProperties() {
		return $this->_search_properties;
	}

	/**
	 * Returns the PagesProperties object containing all
	 * the information relative to the "pages part" of the page.
	 * @return PagesProperties the pages properties
	 */
	function getPagesProperties() {
		return $this->_pages_properties;
	}

	/**
	 * Determines if the list page has an edit page, a delete page or
	 * a custom page in each row.
	 * @return boolean true if the list page has an edit page, a delete page or
	 * a custom page in each row, else false
	 */
	function hasOptions() {
		return $this->_has_options;
	}

	/**
	 * Determines if this page has a link to an insert page.
	 * @return boolean true if this page has a link to an insert page, else
	 * false
	 */
	function hasInsertPage() {
		return $this->_link_insert_page;
	}

	/**
	 * Returns the URI of the insert page.
	 * @return string the URI of the insert page
	 */
	function getLinkInsertPage() {
		return $this->_link_insert_page;
	}

	/**
	 * Returns the number of results per page displayed in the list.
	 * @return int the number of results per page displayed in the list
	 */
	function getResultsPerPage() {
		return $this->_results_per_page;
	}

	/**
	 * Returns the DescriptionProperties object containing all
	 * the information relative to the description of the page,
	 * such as the title and instructions.
	 * @return DescriptionProperties the description properties
	 */
	function getDescriptionProperties() {
		return $this->_description_properties;
	}

}

/**
 * Contains the search properties of a list page.
 * Indicates the way to build the form where the combo box
 * and text input of the search will be printed.
 * You can obtain an instance of this class from a
 * ListPage.
 * @abstract
 */
class SearchProperties {

	/**#@+
	 * @access private
	 */
	var $_options;
	var $_hidden_fields;
	var $_search_value;

	function SearchProperties() {
		$this->_options = array();
		$this->_hidden_fields = array();
	}
	/**#@-*/

	/**
	 * Gets the name that MUST be assigned to the
	 * name attribute of the combo box where all the possible
	 * search fields are listed.
	 * @return string the name of the search select input
	 */
	function getSelectInputName() {
		return LIST_PARAM_SEARCH_FIELD;
	}

	/**
	 * Gets the name that MUST be assigned to the
	 * name attribute of the text input where the search value
	 * may be entered.
	 * @return string the name of the search text input
	 */
	function getTextInputName() {
		return LIST_PARAM_SEARCH_VALUE;
	}

	/**
	 * Gets the name that MUST be assigned to the
	 * value attribute of the text input where the search
	 * value may be entered. The value of the text input will be the
	 * value of the last search.
	 * @return string the value of the search text input
	 */
	function getTextInputValue() {
		return $this->_search_value;
	}

	/**
	 * Returns the hidden inputs that MUST be printed
	 * to preserve the order field and mode (and other extra parameters)
	 * after the search is done.
	 * @return string the hidden fields
	 */
	function getHiddenInputs() {
		return $this->_hidden_fields;
	}

	/**
	 * Returns the search options as a SearchOption array,
	 * containing the values to display in the combo box.
	 * @return SearchOption[] the values to display in the combo box
	 */
	function getOptions() {
		return $this->_options;
	}

	/**
	 * Determines if the search has options in the search field combo box.
	 * If not, then the search part of the page MUST NOT be printed.
	 * @return boolean true if the search has options, else false
	 */
	function hasOptions() {
		return sizeof($this->_options) > 0;
	}

}

/**
 * Contains the pages properties of a list page.
 * Indicates the way to build the list of all available pages.
 * To avoid breaking the page with excesive pages, only 7 pages
 * are shown in the list.
 * You can obtain an instance of this class from a
 * ListPage.
 * @abstract
 */
class PagesProperties {

	/**#@+
	 * @access private
	 */
	var $_first_page;
	var $_last_page;
	var $_total_pages;
	var $_current_page;

	var $_first_result;
	var $_last_result;
	var $_total_results;

	var $_static_link;
	/**#@-*/

	/**
	 * Retutns the first page (not allways 1) of the pages list.
	 * @return int the first page of the pages list
	 */
	function getFirstPage() {
		return $this->_first_page;
	}

	/**
	 * Retutns the last page (not allways the total number of pages) of the pages list.
	 * @return int the last page of the pages list
	 */
	function getLastPage() {
		return $this->_last_page;
	}

	/**
	 * Retutns the total number of pages in the list.
	 * @return int the total number of pages in the list
	 */
	function getTotalPages() {
		return $this->_total_pages;
	}

	/**
	 * Retutns the number of the current page in the list.
	 * @return int the number of the current page in the list
	 */
	function getCurrentPage() {
		return $this->_current_page;
	}

	/**
	 * Determines if the first page shown in the pages list is greater than 1.
	 * @return boolean true if the first page shown in the pages
	 * list is greater than 1, else false
	 */
	function hasPreviousPages() {
		return $this->getFirstPage() > 1;
	}

	/**
	 * Determines if the last page shown in the pages list is lesser than
	 * the total number of pages.
	 * @return boolean true if the last page shown in the pages list is lesser than
	 * the total number of pages, else false
	 */
	function hasNextPages() {
		return $this->getLastPage() < $this->getTotalPages();
	}

	/**
	 * Returns the number of the first result shown in the list.
	 * @return int the number of the first result shown in the list
	 */
	function getFirstResult() {
		return $this->_first_result;
	}

	/**
	 * Returns the number of the last result shown in the list.
	 * @return int the number of the last result shown in the list
	 */
	function getLastResult() {
		return $this->_last_result;
	}

	/**
	 * Returns the total number of the results (shown and not shown in the list).
	 * @return int the total number of the results
	 */
	function getTotalResults() {
		return $this->_total_results;
	}

	/**
	 * Returns the link to a page in the list.
	 * @return int the number of page to link
	 */
	function getLinkForPage($number) {
		return $this->_static_link.'&'.LIST_PARAM_PAGE.'='.$number;
	}

}

/**
 * Represents a single option in the search combo box.
 * You can obtain an instance of this class from a
 * SearchProperties.
 * @abstract
 */
class SearchOption {

	/**#@+
	 * @access private
	 */
	var $name;
	var $value;
	var $is_selected;
	/**#@-*/

	/**
	 * Returns the value that must be placed in the
	 * value attribute of the option tag.
	 * @return the value that must be placed in the
	 * value attribute of the option tag
	 */
	function getName() {
		return $this->name;
	}

	/**
	 * Returns the value that must be placed inside the
	 * the option tag.
	 * @return the value that must be placed inside the
	 * the option tag
	 */
	function getValue() {
		return $this->value;
	}

	/**
	 * Determines if this option is selected.
	 * @return boolean true if this option is selected, else false
	 */
	function isSelected() {
		return $this->is_selected;
	}

}

/**
 * Represents a single header of the list page.
 * You can obtain an instance of this class from a
 * ListPage.
 * @abstract
 */
class ListHeader {

	/**#@+
	 * @access private
	 */
	var $_name;
	var $_ascendent;
	var $_descendent;
	var $_link_ascendent;
	var $_link_descendent;
	var $_allows_order;
	/**#@-*/

	/**
	 * Returns the name of the header. That is, the title.
	 * @return string the name of the header
	 */
	function getName() {
		return $this->_name;
	}

	/**
	 * Returns the link to order the list by the field represented by
	 * this header, in an ascendent order.
	 * @return the link to order the list in an ascendent order
	 */
	function getLinkAscendentOrder() {
		return $this->_link_ascendent;
	}

	/**
	 * Returns the link to order the list by the field represented by
	 * this header, in a descendent order.
	 * @return the link to order the list in a descendent order
	 */
	function getLinkDescendentOrder() {
		return $this->_link_descendent;
	}

	/**
	 * Determines if the current order of the list page
	 * is the one denoted by this header, in an ascendent order.
	 * @return boolean true if the current order of the list page
	 * is the one denoted by this header, in an ascendent order, else false
	 */
	function isAscendentOrder() {
		return $this->_ascendent;
	}

	/**
	 * Determines if the current order of the list page
	 * is the one denoted by this header, in a descendent order.
	 * @return boolean true if the current order of the list page
	 * is the one denoted by this header, in a descendent order, else false
	 */
	function isDescendentOrder() {
		return $this->_descendent;
	}

	/**
	 * Determines if this header contains the links to order the list page.
	 * @return boolean true if this header allows order, else false
	 */
	function allowsOrder() {
		return $this->_allows_order;
	}

}

/**
 * Represents a single row of the list page.
 * You can obtain an instance of this class from a
 * ListPage.
 * @abstract
 */
class ListRow {

	/**#@+
	 * @access private
	 */
	var $_cells;
	var $_link_edit_page;
	var $_link_delete_page;
	var $_custom_pages;
	var $_do;

	function ListRow() {
		$this->_cells = array();
		$this->_custom_pages = array();
	}
	/**#@-*/

	/**
	 * Returns the cells of the cells of this row in an array.
	 * @return ListCell[] the cells of this row
	 */
	function getCells() {
		return $this->_cells;
	}

	/**
	 * Returns the link to the edit page.
	 * @return the link to the edit page
	 */
	function getLinkEditPage() {
		return $this->_link_edit_page;
	}

	/**
	 * Returns the link to the delete page.
	 * @return the link to the delete page
	 */
	function getLinkDeletePage() {
		return $this->_link_delete_page;
	}

	/**
	 * Determines if the list page has an edit page.
	 * @return boolean true if the list page has an edit page, else false
	 */
	function hasEditPage() {
		return $this->_link_edit_page;
	}

	/**
	 * Determines if the list page has a delete page.
	 * @return boolean true if the list page has a delete page, else false
	 */
	function hasDeletePage() {
		return $this->_link_delete_page;
	}

	/**
	 * Returns the custom pages that will server as extra links in the row.
	 * @return CustomPage[] the custom pages
	 */
	function getCustomPages() {
		return $this->_custom_pages;
	}

	/**
	 * Returns the DataObject shown in this row.
	 * @return mixed the DataObject shown in this row
	 */
	function getDataObject() {
		return $this->_do;
	}

}

/**
 * Represents a list cell from a list row.
 * @abstract
 */
class ListCell {

	/**#@+
	 * @access private
	 */
	var $_value;
	var $_attributes;
	/**#@-*/

	/**
	 * Returns the value of this cell.
	 * @return string the value of this cell
	 */
	function getValue() {
		return $this->_value;
	}

	/**
	 * Returns the attributes of this cell.
	 * @return array an associative array of attribute name and values
	 * or null if no attributes are specified for this cell
	 */
	function getAttributes() {
		return $this->_attributes;
	}

}

/**
 * Represents a link to a custom page, that will be
 * listed in each row.
 * If obtained from a ListRow, the link is added some
 * parameters, like the primary keys of the DataObject list.
 * Extra variables can be setted to this class, so when obtained from
 * a ListPage, those variables still exist.
 * @abstract
 */
class CustomPage {

	/**#@+
	 * @access private
	 */
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
	/**#@-*/

	/**
	 * Returns the name of this custom page.
	 */
	function getName() {
		return $this->_name;
	}

	/**
	 * When obtained from a ListPage, returns the link to the custom page.
	 */
	function getLink() {
		return $this->_link;
	}

	/**
	 * Returns the target of this custom page.
	 */
	function getTarget() {
		return $this->_target;
	}

}

/**
 * The DescriptionProperties interface contains the description
 * info for a ListPage or an EditPage.
 * It supports for a title, instructions an a link for help.
 * @abstract
 */
class DescriptionProperties {

	/**#@+
	 * @access private
	 */
	var $_title;
	var $_instructions;
	var $_help_page;
	/**#@-*/

	/**
	 * Determines if the page has a title to show.
	 * @param boolean true if the page has a title to showe
	 */
	function hasTitle() {
		return $this->_title;
	}

	/**
	 * Returns the title to show in the description of the page.
	 * @param string the title to show in the description of the page
	 */
	function getTitle() {
		return $this->_title;
	}

	/**
	 * Determines if the page has instructions to show.
	 * @param boolean true if the page has instructions to showe
	 */
	function hasInstructions() {
		return $this->_instructions;
	}

	/**
	 * Returns the instruction on how to use the page.
	 * @param string the instruction on how to use the page
	 */
	function getInstructions() {
		return $this->_instructions;
	}

	/**
	 * Determines if the page has a link to provide a more
	 * detailed explanation on how the page works.
	 * @return boolean true if the page has a link to a help page,
	 * else false.
	 */
	function hasHelpPage() {
		return $this->_help_page;
	}

	/**
	 * Returns the link to provide a more detailed explanation on
	 * how the page works.
	 * @return string the link to the help page
	 */
	function getHelpPage() {
		return $this->_help_page;
	}

}
?>