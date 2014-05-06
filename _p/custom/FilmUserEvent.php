<?
PHP :: requireClasses('DataObjects', 'Date');

class UserEvent {

	var $id;
	var $event_date;
	var $from_x;
	var $to_user;
	var $message;
	var $message_type; 
	var $status;
	
	function UserEvent() {
		$this->event_date = new Date();
		$this->status = 'new';
	}
	
}

class UserEventManager extends DataObjectsManager {

	function _getDataObjectInfo() {
		$table = new Table('subtitolu_user_event');
		$table->addField(new Field('id', SQL_TYPE_INTEGER, true, true));
		$table->addField(new Field('event_date', SQL_TYPE_DATETIME));
		$table->addField(new Field('from_x', SQL_TYPE_INTEGER));
		$table->addField(new Field('to_user', SQL_TYPE_INTEGER));
		$table->addField(new Field('message', SQL_TYPE_STRING));
		$table->addField(new Field('event_type', SQL_TYPE_STRING));
		$table->addField(new Field('status', SQL_TYPE_STRING));
		$doi = new DataObjectInfo($table, 'UserEvent');
		return $doi;
	}

}
?>
