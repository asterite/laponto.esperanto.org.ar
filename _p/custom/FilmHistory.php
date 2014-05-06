<?
PHP::requireClasses('DataObjects');

class FilmHistory {

	var $id;
	var $film_id;
	var $user_id;
	var $date;
	var $number;
	var $the_text;
	var $the_trust;
	
}

class FilmHistoryManager extends DataObjectsManager {

	function _getDataObjectInfo() {
		$table = new Table('subtitolu_film_history');
		$table->addField(new Field('id', SQL_TYPE_INTEGER, true, true));
		$table->addField(new Field('film_id', SQL_TYPE_INTEGER));
		$table->addField(new Field('user_id', SQL_TYPE_INTEGER));
		$table->addField(new Field('date', SQL_TYPE_DATETIME));
		$table->addField(new Field('number', SQL_TYPE_INTEGER));
		$table->addField(new Field('the_text', SQL_TYPE_STRING));
		$table->addField(new Field('the_trust', SQL_TYPE_STRING));
		$doi = new DataObjectInfo($table, 'FilmHistory');
		return $doi;
	}

}
?>