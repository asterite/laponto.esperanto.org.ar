<?
PHP :: requireClasses('DataObjects');
PHP :: requireCustom('FilmSubtitle', 'FilmParticipant', 'Language');

class Film {

	var $id;
	var $user_id;
	var $name;
	var $format;
	var $status;
	var $rules;
	var $download_options;
	var $from_language;
	var $to_language;
	var $subtitles_extra_info;

	function getStatus() {
  	if (!$this->status) {
  	  $this->status = new FilmStatus();

    	$db = DataObjectsManager::getDatabase();
    	$conn = $db->openConnection();
    	$ps = $conn->prepareStatement('SELECT trust as t, SUM(1) as s FROM ' .
    	        'subtitolu_film_subtitle WHERE film_id = :film_id GROUP BY trust');
    	$ps->setInteger('film_id', $this->id);
    	$rs = $ps->executeQuery();

    	while($rs->next()) {
      	$t = $rs->getString('t');
      	$s = $rs->getInteger('s');
      	$this->status->translations[$t] = $s;
    	}
    }
    return $this->status;
	}

	function getParticipants() {
		$man = new FilmParticipantManager();
		$man->addWhereField('film_id', '=', $this->id);
		return $man;
	}

	function getFullParticipants($order, $ascendent) {
		$asc = $ascendent ? 'ASC' : 'DESC';

		$db = DataObjectsManager::getDatabase();
		$conn = $db->openConnection();
		$ps = $conn->prepareStatement("SELECT SUM(s.user_id = p.user_id) as points, u.id as user_id, u.nickname as nickname, u.name, p.can_add as can_add, p.super_user as super_user, p.blocked as blocked FROM subtitolu_user as u, subtitolu_film_participant as p, subtitolu_film_subtitle as s WHERE u.id = p.user_id AND p.film_id = s.film_id AND p.film_id = :film_id AND s.trust != :trust GROUP BY u.id ORDER BY $order $asc");
		$ps->setInteger('film_id', $this->id, SQL_TYPE_INTEGER);
		$ps->setString('trust', FILM_SUBTITLE_NO_TRUST);
		$rs = $ps->executeQuery();

		$users = array();
		while($rs->next()) {
			$user = new FullParticipant();
			$user->user_id = $rs->getInteger('user_id');
			$user->nickname = $rs->getString('nickname');
			$user->name = $rs->getString('name');
			$user->points = $rs->getInteger('points');
			$user->can_add = $rs->getBoolean('can_add');
			$user->super_user = $rs->getBoolean('super_user');
			$user->blocked = $rs->getBoolean('blocked');
			array_push($users, $user);
		}

		return $users;
	}

	function getSubtitles() {
		$man = new FilmSubtitleManager();
		$man->addWhereField('film_id', '=', $this->id);
		return $man;
	}

	function getFromLanguage() {
		$man = new LanguageManager();
		return $man->getFromKeys(array('id' => $this->from_language));
	}

	function getToLanguage() {
		$man = new LanguageManager();
		return $man->getFromKeys(array('id' => $this->to_language));
	}

}

class FilmManager extends DataObjectsManager {

	function _getDataObjectInfo() {
		$table = new Table('subtitolu_film');
		$table->addField(new Field('id', SQL_TYPE_INTEGER, true, true));
		$table->addField(new Field('user_id', SQL_TYPE_INTEGER));
		$table->addField(new Field('name', SQL_TYPE_STRING));
		$table->addField(new Field('format', SQL_TYPE_STRING));
		$table->addField(new Field('rules', SQL_TYPE_STRING));
		$table->addField(new Field('download_options', SQL_TYPE_STRING));
		$table->addField(new Field('from_language', SQL_TYPE_INTEGER));
		$table->addField(new Field('to_language', SQL_TYPE_INTEGER));
		$table->addField(new Field('subtitles_extra_info', SQL_TYPE_STRING));
		$table->addField(new Field('public', SQL_TYPE_BOOLEAN));
		$doi = new DataObjectInfo($table, 'Film');
		return $doi;
	}

}

class FilmStatus {

	var $translations;

	function FilmStatus() {
  	$this->translations = array();
  	$this->translations[FILM_SUBTITLE_MAX_TRUST] = 0;
  	$this->translations[FILM_SUBTITLE_MEDIUM_TRUST] = 0;
  	$this->translations[FILM_SUBTITLE_LOW_TRUST] = 0;
  	$this->translations[FILM_SUBTITLE_NO_TRUST] = 0;
	}

	function get($type) {
		return $this->translations[$type];
	}

	function countNotEmpty() {
		$c = 0;
		$c += $this->translations[FILM_SUBTITLE_MAX_TRUST];
		$c += $this->translations[FILM_SUBTITLE_MEDIUM_TRUST];
		$c += $this->translations[FILM_SUBTITLE_LOW_TRUST];
		return $c;
	}

	function count() {
		$c = 0;
		$c += $this->translations[FILM_SUBTITLE_MAX_TRUST];
		$c += $this->translations[FILM_SUBTITLE_MEDIUM_TRUST];
		$c += $this->translations[FILM_SUBTITLE_LOW_TRUST];
		$c += $this->translations[FILM_SUBTITLE_NO_TRUST];
		return $c;
	}

	function getPercentCompleted() {
		$c = $this->count();
		if ($c == 0) return 0;
		$p = 0;
		$p += 100 * $this->translations[FILM_SUBTITLE_MAX_TRUST] / $c;
		$p += 66 * $this->translations[FILM_SUBTITLE_MEDIUM_TRUST] / $c;
		$p += 33 * $this->translations[FILM_SUBTITLE_LOW_TRUST] / $c;

		return round($p, 2);
	}

}
?>

