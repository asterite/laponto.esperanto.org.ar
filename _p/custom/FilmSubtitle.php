<?
PHP::requireClasses('DataObjects');

define('FILM_SUBTITLE_MAX_TRUST', 'max');
define('FILM_SUBTITLE_MEDIUM_TRUST', 'medium');
define('FILM_SUBTITLE_LOW_TRUST', 'low');
define('FILM_SUBTITLE_NO_TRUST', 'not'); // no traducido

class FilmSubtitle {

	var $id;
	var $film_id;
	var $user_id;
	var $number;
	var $from_time;
	var $to_time;
	var $original_text;
	var $translated_text;
	var $trust;
	var $comments;
	
}

class FilmSubtitleManager extends DataObjectsManager {

	function _getDataObjectInfo() {
		$table = new Table('subtitolu_film_subtitle');
		$table->addField(new Field('id', SQL_TYPE_INTEGER, true, true));
		$table->addField(new Field('film_id', SQL_TYPE_INTEGER));
		$table->addField(new Field('user_id', SQL_TYPE_INTEGER));
		$table->addField(new Field('number', SQL_TYPE_INTEGER));
		$table->addField(new Field('from_time', SQL_TYPE_STRING));
		$table->addField(new Field('to_time', SQL_TYPE_STRING));
		$table->addField(new Field('original_text', SQL_TYPE_STRING));
		$table->addField(new Field('translated_text', SQL_TYPE_STRING));
		$table->addField(new Field('trust', SQL_TYPE_STRING));
		$table->addField(new Field('comments', SQL_TYPE_STRING));
		$doi = new DataObjectInfo($table, 'FilmSubtitle');
		return $doi;
	}

}
?>