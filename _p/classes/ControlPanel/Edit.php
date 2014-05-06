<?
/**
 * The classes in this file help to build a page
 * to insert or edit a generic Object, provided its Browser.
 *
 * The page consists of a form, where in each row a title and an
 * input are presented. The value of the input is obtained from the Object,
 * if the EditPage is in edit mode (See method inEditMode()).
 *
 * The page also contains a description part to show the title of the page,
 * some instructions and a link to a help page.
 *
 * The class EditPageOptions prepare the options to show in the form,
 * while the interface EditPage is provided to merge it in the template.
 *
 * @package ControlPanel
 */

PHP::requireClasses('ControlPanel/Common', 'Request', 'JSValidator', 'Tag');

/**
 * This class maintains the options of an edit page, such as the manager
 * from which the DataObject is obtained, the fields of the DataObject to edit,
 * and validation rules for the fields.
 * From this class you can obtain an EditPage to use in a template page.
 *
 * @package ControlPanel
 */
class EditPageOptions {

	/**#@+
	 * @access private
	 */
	var $man;
	var $overriden_object;

	var $edit_fields;
	var $hidden_fields;

	var $validation_rules;
	var $action_page;
	var $cancel_page;

	var $description;
	/**#@-*/

	/**
	 * Constructs an EditPageOptions for a particular
	 * DataObjectsManager.
	 * @param Browser $browser the Browser from which the Object
	 * and its properties are obtained
	 */
	function EditPageOptions($browser) {
		$this->man = $browser;

		$this->edit_fields = array();
		$this->hidden_fields = array();
		$this->validation_rules = array();
		$this->description = new DescriptionProperties();

		// Estos parametros vienen del listado
		$this->addExtraParameter(LIST_PARAM_PAGE);
		$this->addExtraParameter(LIST_PARAM_ORDER_FIELD);
		$this->addExtraParameter(LIST_PARAM_ORDER_MODE);
		$this->addExtraParameter(LIST_PARAM_SEARCH_FIELD);
		$this->addExtraParameter(LIST_PARAM_SEARCH_VALUE);
	}

	/**
	 * Override the object to edit or insert by the one specified.
	 */
	function overrideObject($object) {
		$this->overriden_object = $object;
	}

	/**
	 * Determines if the user is editing a DataObject rather than creating
	 * it. This is done by getting the request parameters that match the
	 * primary keys of the DataObject. If the parameters exist, then it is an edit page.
	 * Else, it is a creation page.
	 * For example, if the DataObject has a primary key whose field is "id"
	 * and in the request a parameter named "id" is found with a value, the DataObject
	 * is retrived with that "id" (through the manager specified in the setManager()
	 * method).
	 * @return boolean true if the user is editing a DataObject, false
	 * if the user is creating an object
	 */
	function isInEditMode() {
		$keys = $this->man->getPrimaryKeys();
		foreach($keys as $key) {
			$value = Request::getParameter($key);
			if (!$value) return false;
		}
		return true;
	}

	/**
	 * Adds a hidden field to be outputed in the form.
	 * @param string $name the name of the parameter
	 * @param string $value the value of the parameter
	 */
	function addHiddenField($name, $value = null) {
		$this->hidden_fields[$name] = $value;
	}

	/**
	 * Adds an extra parameter that may be coming from the list page.
	 * This method just calls
	 * addHiddenField($name, Request::getParameter($name))
	 * @param string $name the name of a parameter
	 */
	function addExtraParameter($name) {
		$this->addHiddenField($name, Request::getParameter($name));
	}

	/**
	 * Adds a row for editing a property of the DataObject,
	 * optionaly with a default value.
	 * @param string $name the name of the row, as a title
	 * @param string $call a call to get a variable or invoke a method, in chain.
	 * See method MethodInvoker::chain($object, $call) in file Common.php .
	 * @param string $input_name the name of the input that will be printed
	 * @param EditRenderer $renderer the renderer for the value listed
	 * @param mixed $default_value the default value for the field (in the insert page)
	 * @param string $attributes custom attributes understood by your template
	 */
	function addRow($name, $call, $input_name, $renderer, $default_value = null, $attributes = null) {
		array_push($this->edit_fields, new EditField($name, $call, $input_name, $renderer, $default_value, $attributes));
	}

	/**
	 * Adds a separator.
	 * @param string $label the text to show in the separator
	 * @param string $attributes custom attributes understood by your template
	 */
	function addSeparator($label, $attributes = null) {
		array_push($this->edit_fields, new EditSeparator($label, $attributes));
	}

	/**
	 * Adds a validation rule to apply before submiting the form.
	 * @param ValidationRule $rule a ValidationRule. See JSValidator.php .
	 */
	function addValidationRule($rule) {
		array_push($this->validation_rules, $rule);
	}

	/**
	 * Sets the URL that the form will be redirected when submited.
	 * This method MUST allways be invoked and is here as a setter
	 * for the clarity of the constructor.
	 * @param string $page an URL
	 */
	function setActionPage($page) {
		$this->action_page = $page;
	}

	/**
	 * Sets the URL that the form will be redirected when canceled.
	 * If not set, then no cancel page will be provided.
	 * @param string $page an URL
	 */
	function setCancelPage($page) {
		$this->cancel_page = $page;
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
	 * Gets the EditPage to render in the template page for the current options.
	 * @return EditPage the edit page
	 */
	function getEditPage() {

		$edit = new EditPage();
		$edit->_action_page = &$this->action_page;
		$edit->_cancel_page = &$this->cancel_page;

		// Descripcion
		$edit->_description_properties = &$this->description;

		// Primero averiguo los keys de manager para
		// ver si estoy en insert o edit
		$keys = $this->man->getPrimaryKeys();
		$edit_keys = array();

		// Si hice override del objeto, de ahi saco las claves
		if ($this->overriden_object) {
			foreach($keys as $key) {
				$value = $this->overriden_object->$key;
				if ($value) $edit_keys[$key] = $value;
			}
		} else {
			// Sino las saco de request
			foreach($keys as $key) {
				$value = Request::getParameter($key);
				if ($value) $edit_keys[$key] = $value;
			}
		}

		// Ahora creo al data object
		if (sizeof($edit_keys) > 0 or $this->overriden_object) {
			// Desde los keys, si estan (MODO EDICION) o si se hizo override del objeto
			$do = $this->overriden_object ? $this->overriden_object : $this->man->getFromKeys($edit_keys, true);
			foreach($edit_keys as $name => $value) {
				$this->addHiddenField($name, $value);
			}
		} else {
			// Con el nombre de la clase del Objeto (MODO INSERCION)
			$class_name = $this->man->getObjectsClassName();
			$do = new $class_name();
		}
		
		// Armo los hidden fields
		foreach($this->hidden_fields as $name => $value) {
			$tag = new Tag('input', false);
			$tag->setAttribute('type', 'hidden');
			$tag->setAttribute('name', $name);
			$tag->setAttribute('value', $value);
			$edit->_hidden_fields .= $tag->toString();
		}

		// Armo las filas
		foreach($this->edit_fields as $field) {
			$row = new EditRow();
			$row->_input_name = $field->input_name;
			$row->_attributes = $field->attributes;
			$row->_name = $field->name;
			// Es un separador
			if (get_class($field) == 'editseparator') {
				$row->_is_separator = true;
				array_push($edit->_edit_rows, $row);
			} else {
				// Si estoy en MODO EDICION
				if (sizeof($edit_keys) > 0 or $this->overriden_object) {
					// Obtengo el valor invocando el metodo o la propiedad
					$value = MethodInvoker::chain($do, $field->call);
				} else {
					// Le seteo el valor por defecto
					$value = $field->default_value;
				}
				$row->_value = $field->renderer->renderEdit($field->input_name, $value);

				// Tengo que abrir una nueva columna ?
				if ($field->renderer->spanRow()) {
					array_push($edit->_edit_rows, $row);
				} else {
					// Entonces lo sumo a los campos ocultos
					$edit->_hidden_fields .= $row->_value;
				}
			}
		}

		// Las validaciones JavaScript
		$edit->_validation_rules = &$this->validation_rules;

		return $edit;
	}

}

/** @access private */
class EditPage {

	var $_hidden_fields;
	var $_edit_rows;
	var $_validation_rules;
	var $_action_page;
	var $_cancel_page;
	var $_description_properties;

	function EditPage() {
		$this->_edit_rows = array();
		$this->_validation_rules = array();
	}

	function getHiddenFields() {
		return $this->_hidden_fields;
	}

	function getEditRows() {
		return $this->_edit_rows;
	}

	function getJSValidator($form_name, $function_name) {
		$validator = new JSValidator($form_name, $function_name);
		foreach($this->_validation_rules as $rule) {
			$validator->addValidationRule($rule);
		}
		return $validator;
	}

	function getActionPage() {
		return $this->_action_page;
	}

	function hasCancelPage() {
		return $this->_cancel_page;
	}

	function getCancelPage() {
		return $this->_cancel_page;
	}

	function getDescriptionProperties() {
		return $this->_description_properties;
	}

}

/** @access private */
class EditRow {

	var $_name;
	var $_input_name;
	var $_value;
	var $_attributes;
	var $_is_separator;

	function getInputName() {
		return $this->_input_name;
	}

	function getName() {
		return $this->_name;
	}

	function getValue() {
		return $this->_value;
	}

	function getAttributes() {
		return $this->_attributes;
	}

	function isSeparator() {
		return $this->_is_separator;
	}

}

/** @access private */
class EditField {

	var $name;
	var $input_name;
	var $call;
	var $renderer;
	var $default_value;
	var $attributes;

	function EditField($name, $call, $input_name, $renderer, $default_value, $attributes) {
		$this->name = $name;
		$this->call = $call;
		$this->input_name = $input_name;
		$this->renderer = $renderer;
		$this->default_value = $default_value;
		$this->attributes = $attributes;
	}

}

class EditSeparator {

	var $name;
	var $attributes;

	function EditSeparator($name, $attributes) {
		$this->name = $name;
		$this->attributes = $attributes;
	}

}
?>