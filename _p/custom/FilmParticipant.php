<?
PHP :: requireClasses('DataObjects');

class FilmParticipant {

	var $id;
	var $film_id;
	var $user_id;
	var $can_add;
	var $super_user;
	var $blocked;

}

class FilmParticipantManager extends DataObjectsManager {

	function _getDataObjectInfo() {
		$table = new Table('subtitolu_film_participant');
		$table->addField(new Field('id', SQL_TYPE_INTEGER, true, true));
		$table->addField(new Field('film_id', SQL_TYPE_INTEGER));
		$table->addField(new Field('user_id', SQL_TYPE_INTEGER));
		$table->addField(new Field('can_add', SQL_TYPE_BOOLEAN));
		$table->addField(new Field('super_user', SQL_TYPE_BOOLEAN));
		$table->addField(new Field('blocked', SQL_TYPE_BOOLEAN));
		$doi = new DataObjectInfo($table, 'FilmParticipant');
		return $doi;
	}

}

class FullParticipant {
	
	var $user_id;
	var $nickname;
	var $name;
	var $points;
	var $can_add;
	var $super_user;
	var $blocked;
	
}
?>

