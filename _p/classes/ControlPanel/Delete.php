<?
PHP::requireClasses('ControlPanel/Action');

/**
 * This class extends the ActionPage class to add
 * delete specific functionality. A method is provided,
 * delete(), to delete a specified DataObject from
 * a DataObjectsManager. To do so, request parameters
 * named exactly as the primary keys of the DataObject
 * must be found in the request.
 * This parameters are provided by the ListPage class.
 *
 * @package ControlPanel
 */
class DeletePage extends ActionPage {

	/**
	 * Constructs a DeletePage.
	 */
	function DeletePage() {
		$this->ActionPage();
	}

	/**
	 * Deletes the "request found" DataObject for the given
	 * DataObjectsManager.
	 * @return mixed the value returned by the DataObjectsManager::delete()
	 * method.
	 */
	function delete($man) {
		$doi = $man->getDataObjectInfo();
		$class_name = $doi->getDataObjectClassName();
		$do = new $class_name();
		$keys = $man->getPrimaryKeys();
		$delete_keys = array();
		foreach($keys as $key) {
			$value = Request::getParameter($key);
			$do->$key = $value;
		}
		return $man->delete($do);
	}

}
?>