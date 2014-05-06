<?
/**
 * An interface to draw (simple) images.
 *
 * @package Image
 */
class Image {

	/**#@+
	 * @access private
	 */
	var $image;
	var $color;
	var $font;
	/**#@-*/

	/**
	 * Creates an image.
	 * @param integer $width the width of the image
	 * @param integer $height the height of the image
	 */
	function Image($width, $height) {
		if (function_exists('imagecreatetruecolor')) {
			$this->image = imagecreatetruecolor($width, $height);
		} else {
			$this->image = imagecreate($width, $height);
		}
	}

	/**
	 * Sets the current color for all drawing operations.
	 * @param integer $r the red value, 0 to 255
	 * @param integer $g the green value, 0 to 255
	 * @param integer $b the blue value, 0 to 255
	 * @param integer $alpha the alpha value, 0 (opaque) to 127 (transparent)
	 */
	function setColor($r, $g, $b, $alpha = false) {
		if ($alpha and function_exists('imagecolorallocatealpha')) {
			$this->color = imagecolorallocatealpha($this->image, $r, $g, $b, $alpha);
		} else {
			$this->color = imagecolorallocate($this->image, $r, $g, $b);
		}
	}


	/**
	 * Sets the font size (1 to 5) for all string drawing operations.
	 * @param integer the font size
	 */
	function setFontSize($font_number) {
		$this->font = $font_number;
	}

	function drawArc($center_x, $center_y, $width, $height, $start_degrees, $end_degrees, $filled) {
		imagearc($this->image, $center_x, $center_y, $width, $height, $start_degrees, $end_degrees, $this->color);
	}

	function fillArc($center_x, $center_y, $width, $height, $start_degrees, $end_degrees) {
		imagefilledarc($this->image, $center_x, $center_y, $width, $height, $start_degrees, $end_degrees, $this->color);
	}

	function drawChar($char, $x, $y, $vertically) {
		imagechar($this->image, $this->font, $x, $y, $char, $this->color);
	}

	function drawVerticalChar($char, $x, $y) {
		imagecharup($this->image, $this->font, $x, $y, $char, $this->color);
	}

	function drawString($string, $x, $y) {
		imagestring($this->image, $this->font, $x, $y, $string, $this->color);
	}

	function drawVerticalString($string, $x, $y) {
		imagestringup($this->image, $this->font, $x, $y, $string, $this->color);
	}

	function drawEllipse($center_x, $center_y, $width, $height) {
		imageellipse($this->image, $center_x, $center_y, $width, $height, $this->color);
	}

	function fillEllipse($center_x, $center_y, $width, $height) {
		imagefilledellipse($this->image, $center_x, $center_y, $width, $height, $this->color);
	}

	function drawLine($x1, $y1, $width, $height) {
		$x2 = $x1 + $width;
		$y2 = $y1 + $height;
		imageline($this->image, $x1, $y1, $x2, $y2, $this->color);
	}

	function drawPixel($x, $y) {
		imagesetpixel($this->image, $x, $y, $this->color);
	}

	function drawRectangle($x1, $y1, $width, $height) {
		$x2 = $x1 + $width;
		$y2 = $y1 + $height;
		$this->_ensure($x1, $y1, $x2, $y2);
		imagerectangle($this->image, $x1, $y1, $x2, $y2, $this->color);
	}

	function fillRectangle($x1, $y1, $width, $height) {
		$x2 = $x1 + $width;
		$y2 = $y1 + $height;
		$this->_ensure($x1, $y1, $x2, $y2);
		imagefilledrectangle($this->image, $x1, $y1, $x2, $y2, $this->color);
	}

	function drawPolygon($points_array) {
		imagepolygon($this->image, $points_array, $this->color);
	}

	function fillPolygon($points_array) {
		imagefilledpolygon($this->image, $points_array, $this->color);
	}

	function floodFill($x, $y) {
		imagefill($this->image, $x, $y, $this->color);
	}

	function floodFillToBorder($x, $y, $br, $bg, $bb) {
		$border = imagecolorallocate($br, $bg, $bb);
		imagefilltoborder($this->image, $x, $y, $border);
	}

	function rotate($angle) {
		imagerotate($this->image, $angle, $this->color);
	}

	function copy($to, $to_x, $to_y, $from_x, $from_y, $from_width, $from_height, $merge_pct = false) {
		if ($merge_pct) {
			imagecopymerge($this->image, $to->image, $to_x, $to_y, $from_x, $from_y, $from_width, $from_height, $merge_pct);
		} else {
			imagecopy($this->image, $to->image, $to_x, $to_y, $from_x, $from_y, $from_width, $from_height);
		}
	}

	/**
	 * Clears this image to the color set.
	 */
	function clear() {
		$this->fillRectangle(0, 0, $this->getWidth() - 1, $this->getHeight() - 1);
	}

	function getColorAt($x, $y) {
		return imagecolorat($this->image, $x, $y);
	}

	function getWidth() {
		return imagesx($this->image);
	}

	function getHeight() {
		return imagesy($this->image);
	}

	function destroy() {
		imagedestroy($this->image);
	}

	/**
	 * Outputs this image to the browser or to a filename.
	 * @param string $filename the filename to output the image to, or null to output it
	 * to the browser
	 */
	function output($filename = false) {
		if ($filename) {
			imagepng($this->image, $filename);
		} else {
			imagepng($this->image);
		}
	}

	/**
	 * @access private
	 */
	function _ensure(&$x1, &$y1, &$x2, &$y2) {
		if ($x1 > $x2) {
			$temp = $x1;
			$x1 = $x2;
			$x2 = $temp;
		}
		if ($y1 > $y2) {
			$temp = $y1;
			$y1 = $y2;
			$y2 = $temp;
		}
	}

}
?>