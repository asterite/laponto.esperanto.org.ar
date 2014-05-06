<?
/**
 * A helper class for rendering tags.
 *
 * @package Common
 */
class Tag {

	/**#@+
	 * @access private
	 */
	var $name;
	var $attributes;
	var $modifiers;
	var $nested_tags;
	var $has_body;
	/**#@-*/

	/**
   * Constructs a Tag with a name.
   * @param string $name the name of the tag
   * @param boolean $has_body true if this tag has body, else false
   */
	function Tag($name, $has_body) {
		$this->name = $name;
		$this->has_body = $has_body;
		$this->attributes = array();
		$this->modifiers = array();
		$this->nested_tags = array();
	}

	/**
   * Sets an attribute to this tag.
   * @param string $name the name of the attribute
   * @param string/integer $value the value of the attribute
   */
	function setAttribute($name, $value) {
		if (isset($value)) {
			$this->attributes[$name] = $value;
		}
	}

	/**
	 * Returns an attribute of this tag.
	 * @param string $name the name of the attribute
	 * @return string the value of the attribute, if any, or null
	 */
	function getAttribute($name) {
		return $this->attributes[$name];
	}

	/**
   * Sets a modifier to this tag.
   * @param string $name the name of the modifier
   */
	function setModifier($name) {
		array_push($this->modifiers, $name);
	}

	/**
   * Adds a nested tag to this tag.
   * @param Tag $tag a tag
   */
	function addNestedTag($tag) {
		array_push($this->nested_tags, $tag);
	}

	/**
   * Adds a nested string to this tag.
   * @param string $string a string
   */
	function addNestedString($string) {
		array_push($this->nested_tags, $string);
	}

	/**
   * Returns this tag, rendered.
   * @return string this tag, rendered
   */
	function toString() {
		$buf = '<' . $this->name;
		foreach ($this->attributes as $name => $value) {
			// Reemplazo las " por ' porque el delimitador que voy a usar es "
			$value = str_replace('"', "'", $value);
			$buf .= " {$name}=\"{$value}\"";
		}
		foreach ($this->modifiers as $modifier) {
			$buf .= " {$modifier}";
		}
		if ($this->has_body) {
			$buf .= '>';
			foreach($this->nested_tags as $tag) {
				if (is_object($tag)) {
					$buf .= $tag->toString();
				} else {
					$buf .= $tag;
				}
			}
			$buf .= '</' . $this->name . '>';
		} else {
			$buf .= '/>';
		}
		return $buf;
	}

}
?>