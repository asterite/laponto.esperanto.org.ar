<?
/**
 * A simple Tree. All elements can have at most one parent.
 * The order of the elements is the same as the order they
 * where addded to this Tree.
 *
 * @package Tree
 */
class Tree {

	/**#@+
	 * @access private
	 */
	var $elements;
	var $roots;
	/**#@-*/

	/**
	 * Constructs an empty Tree.
	 */
	function Tree() {
		$this->elements = array();
	}

	/**
	 * Adds an element to this tree.
	 * @param mixed $id the id of the element
	 * @param mixed $element the element to add
	 * @param mixed $parent_id the id of the parent element
	 */
	function add($id, &$element, $parent_id = null) {
		array_push($this->elements, new TreeElement($id, $element, $parent_id));
	}

	/**
	 * Determines if this Tree is empty.
	 * @return boolean true if this Tree is empty, else false
	 */
	function isEmpty() {
		return sizeof($this->elements);
	}

	/**
	 * Gets the root elements of this Tree.
	 * @return TreeElement[] an array of TreeElements
	 */
	function getRoots() {
		$this->_build();
		return $this->roots;
	}

	/**#@+
	 * @access private
	 */
	function _build() {
		$this->roots = array();
		for($i=0; $i < sizeof($this->elements); $i++) {
			if (is_null($this->elements[$i]->parent_id)) {
				array_push($this->roots, $this->elements[$i]);
			}
		}
		foreach($this->roots as $key => $root) {
			$this->__build($this->roots[$key]);
		}
	}

	function __build(&$element) {
		for($i=0; $i < sizeof($this->elements); $i++) {
			if ($this->elements[$i]->parent_id == $element->id) {
				$element->addChild($this->elements[$i]->id, $this->elements[$i]->element);
			}
		}
		if ($element->hasChilds()) {
			foreach($element->childs as $key => $node) {
				$this->__build($element->childs[$key]);
			}
		}
	}
	/**#@-*/

}

/**
 * Represents an element of a Tree.
 * You can obtain this from a Tree.
 */
class TreeElement {

	/** The id of the element */
	var $id;
	/** The real element */
	var $element;

	/** @access private */
	var $parent_id;
	/** @access private */
	var $childs;

	/**
	 * Determines if this element has childs.
	 * @return boolean true if this element has childs
	 */
	function hasChilds() {
		return count($this->childs);
	}

	/**
	 * Returns the childs of this element.
	 * @return TreeElement[] an array of TreeElements
	 */
	function &getChilds() {
		return $this->childs;
	}

	/**
	 * @access private
	 */
	function TreeElement($id, &$element, $parent_id) {
		$this->id = $id;
		$this->element = $element;
		$this->parent_id = $parent_id;
		$this->childs = array();
	}

	/**
	 * @access private
	 */
	function addChild($id, &$element) {
		$this->childs[$id] = new TreeElement($id, $element, $this->id);
	}

}
?>