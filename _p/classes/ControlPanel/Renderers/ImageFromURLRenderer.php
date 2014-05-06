<?
PHP::requireClasses('Tag');

class ImageFromURLRenderer {
	
	var $width;
	var $height;

	function ImageFromURLRenderer($width = 0, $height = 0) {
		$this->width = $width;
		$this->height = $height;
	}

	/**
	 * Returns the $value as is.
	 * @return mixed the $value as is
	 */
	function renderList($value) {
		$img = new Tag('img', false);
		if($this->width > 0) $img->setAttribute('width', $this->width);
		if($this->height > 0) $img->setAttribute('height', $this->height);
		$img->setAttribute('src', $value);
		return $img->toString();
	}

	/**
	 * Returns a span tag containing the $value specified.
	 * The id and name attributes of the span tag are $name.
	 * @return a span tag containing the $value specified
	 */
	function renderEdit($name, $value) {
		if (!$value) return "";
		
		$img = new Tag('img', false);
		$img->setAttribute('id', $name);
		$img->setAttribute('name', $name);
		if($this->width > 0) $img->setAttribute('width', $this->width);
		if($this->height > 0) $img->setAttribute('height', $this->height);
		$img->setAttribute('src', $value);
		return $img->toString();
	}

	function spanRow() {
		return true;
	}

	function getAttributes($value) {
		return array('align' => 'center');
	}

}
?>