<?
/**
 * This renderer implements the Decorator pattern.
 * This renderer prints a comment to the right of the renderer decorated.
 *
 * @implements EditRenderer
 *
 * @package ControlPanel
 * @subpackage Renderers
 */
class CommentsRenderer {

	/**#@+
	 * @access private
	 */
	var $edit_renderer;
	var $comments;
	/**#@-*/

	/**
	 * Constructs a CommentsRenderer.
	 * @param EidthRenderer $edit_renderer the renderer to use
	 * @param string $comments the comments for the field
	 */
	function CommentsRenderer($edit_renderer, $comments) {
		$this->edit_renderer = $edit_renderer;
		$this->comments = $comments;
	}

	/**
	 * Renders the value with the EditRenderer passed in the constructor,
	 * and to the right of it it prints the comments.
	 */
	function renderEdit($name, $value) {
		return '
			<table border="0" cellpadding="0" cellspacing="0" class="editFieldValue">
				<tr>
					<td>' . $this->edit_renderer->renderEdit($name, $value) . '</td>
					<td nowrap width="4">&nbsp;</td>
					<td>' . $this->comments . '</td>
				</tr>
			</table>';
	}

	function spanRow() {
		return $this->edit_renderer->spanRow();
	}

}
?>