<?
function createFilm($filename, $name, $rules, $from_language, $to_language, $encoding, $public) {
	PHP::requireCustom('Film', 'SubtitleFormatFactory', 'FilmUser');
	
	$user = UserManager::getRemembered();
	
	$factory = SubtitleFormatFactory::getInstance();
	$format = $factory->getSuitableSubtitleFormat($filename, $encoding);
	
	if (!$format) {
		return false;
	}
	
	$film = new Film();
	$film->user_id = $user->id;
	$film->name = $name;
	$film->rules = $rules;
	$film->public = $public;
	$film->from_language = $from_language;
	$film->to_language = $to_language;
	$film->format = $format->getName();	
	
	$man = new FilmManager();
	$man->insert($film);
	
	$format->addListener(new CreateFilmSubtitleFormatListener($film));
	$format->readSubtitles($filename, $encoding);
	
	joinUserToFilm($user->id, $film->id, true, true, false);
	UserManager::login($user);
	
	return $film;
}

/**
 * Devuelve un array donde en el primer
 * elemento se encuentra un arreglo de $block subtítulos,
 * de los cuales uno es el subtítulo a traducir, y
 * en el segundo elemento se indica cuál (con un número).
 * 
 * El subtítulo se elige al azar.
 */
function getRandomSubtitleBlock($film, $block, $type) {
	PHP::requireCustom('FilmSubtitle');
	
	$status = $film->getStatus();
	$total = $status->count();
	$specific = $status->get($type);
	
	srand();
	$selected = rand(0, $specific - 1);
	
	$man = new FilmSubtitleManager();
	$man->addWhereField('film_id', '=', $film->id);
	$man->addWhereField('trust', '=', $type);
	$man->setStartFrom($selected);
	$man->setShowOnly(1);
	$man->query();
	
	$subtitle = $man->next();
	
	return getSubtitleBlock($film, $block, $subtitle->number);
}

/**
 * Devuelve un array donde en el primer
 * elemento se encuentra un arreglo de $block subtítulos,
 * de los cuales uno es el subtítulo a traducir, y
 * en el segundo elemento se indica cuál (con un número).
 * 
 * El subtítulo se indica con el número.
 */
function getSubtitleBlock($film, $block, $number) {
	$status = $film->getStatus();
	$total = $status->count();
	
	$selected = $number - 1;
	
	if ($selected < (int)(($block / 2))) {
		$start = 0;
		$to_return = $selected;
	} else if ($selected > $total - ($block - 1)) {
		$start = $total - $block;
		$to_return = $block - ($total - $selected);
	} else {
		$start = $selected - (int)(($block / 2));
		$to_return = (int) ($block / 2);
	}
	
	$man = new FilmSubtitleManager();
	$man->addWhereField('film_id', '=', $film->id);
	$man->setStartFrom($start);
	$man->setShowOnly($block);
	$man->addOrder('number');
	$man->query();
	
	return array($man->getArray(), $to_return);
}

function changeSubtitle(&$film, &$subtitle, $new_text, $new_trust, $comments, $explanation, $small = false) {
	PHP::requireClasses('Date');
	PHP::requireCustom('Film', 'FilmUser', 'FilmSubtitle', 'FilmHistory');
	
	$os = $subtitle;
	
	$user = UserManager::getLogged();
	
	// Si no hay texto, la confianza vuelve a baja
	/*
	if (!trim($new_text)) {
		$new_trust = FILM_SUBTITLE_NO_TRUST;
	}
	*/
	
	// Sólo si se hace cambio...
	if ($os->translated_text == $new_text and $os->trust == $new_trust
		and $os->comments == $comments) {
		return;
	}
	
	$man = new FilmManager();
	$man->update($film);
	
	// Actualizar el historial
	$history = new FilmHistory();
	$history->film_id = $os->film_id;
	$history->user_id = $user->id;
	$history->date = new Date();
	$history->number = $os->number;
	$history->the_text = $new_text;
	$history->the_trust = $new_trust;
	
	$man = new FilmHistoryManager();
	$man->insert($history);
	
	// Cambiar los puntos (Sólo si cambió el texto y el usuario...)
	if (!$small and $user->id != $os->user_id and $os->translated_text != $new_text) {
		$man = new FilmParticipantManager();
		$me = $man->getFromKeys(array('film_id' => $film->id, 'user_id' => $user->id));
		
		// Primero los míos
		// Sólo si la confianza existe
		if ($new_trust != FILM_SUBTITLE_NO_TRUST) {
			$me->points++;
			
			$man = new FilmParticipantManager();
			$man->update($me);
		}
		
		// Luego los suyos (si había uno antes)
		if ($subtitle->user_id) {
			$other = $man->getFromKeys(array('film_id' => $film->id, 'user_id' => $os->user_id));
			$other->points--;
			
			$man = new FilmParticipantManager();
			$man->update($other);				
		}
		
		// Cambio el usuario si cambió el texto...
		if ($new_trust == FILM_SUBTITLE_NO_TRUST) {
			$subtitle->user_id = false;
		} else {
			$subtitle->user_id = $user->id;
		}	
	}
	
	// Si hay explicación...
	if (trim($explanation)) {
		$event = new UserEvent();
		$event->from_x = $user->id;
		$event->to_user = $os->user_id;
		$event->event_type = 'explanation';
		$event->message = serialize(array($film->id, $os->number, nl2br(trim($os->original_text)), nl2br(trim($os->translated_text)), nl2br(trim($new_text)), nl2br(trim($explanation))));
		
		$man = new UserEventManager();
		$man->insert($event);
		
		notifyUserByEmail($os);
	}
	
	// Actualizar el subtítulo
	$subtitle->translated_text = $new_text;
	$subtitle->trust = $new_trust;
	$subtitle->comments = $comments;
	
	$man = new FilmSubtitleManager();
	$man->update($subtitle);
}

function joinUserToFilm($user_id, $film_id, $can_add, $super_user, $send_event = true) {
	PHP::requireCustom('FilmParticipant');
	
	$man = new FilmParticipantManager();
	$man->addWhereField('user_id', '=', $user_id);
	$man->addWhereField('film_id', '=', $film_id);
	$man->query();
	
	if (!$man->hasNext()) {
		$fp = new FilmParticipant();
		$fp->film_id = $film_id;
		$fp->user_id = $user_id;
		$fp->points = 0;
		$fp->can_add = $can_add;
		$fp->super_user = $super_user;
		
		$man->insert($fp);
		
		// Ahora el evento
		if ($send_event) {
			$event = new UserEvent();
			$event->from_x = $film_id;
			$event->to_user = $user_id;
			$event->event_type = 'film_access_granted';
			
			$man = new UserEventManager();
			$man->insert($event);
			
			notifyUserIdByEmail($event->to_user);
		}
	}
}

/**
 * Notifica al usuario de id $id por e-mail que tiene un nuevo
 * mensaje, si es que así lo indicó.
 */ 
function notifyUserIdByEmail($id) {
	PHP::requireCustom('FilmUser');
	$man = new UserManager();
	$user = $man->getFromKeys(array('id' => $id));
	notifyUserByEmail($user);
}

function notifyManyUserIdByEmail($array) {
	$emails = array();
	
	foreach($array as $id) {
		$man = new UserManager();
		$user = $man->getFromKeys(array('id' => $id));
		if ($user->notify_by_email and trim($user->email)) {
			array_push($emails, $user->email);
		}
	}
	
	notifyManyEmails($emails);
}

/**
 * Notifica al usuario $user por e-mail que tiene un nuevo
 * mensaje, si es que así lo indicó.
 */ 
function notifyUserByEmail($user) {
	if (!$user->notify_by_email or !trim($user->email)) return;
	
	$emails = array();
	array_push($emails, $user->email);
	
	notifyManyEmails($emails);
}

function notifyManyEmails($emails) {
	PHP::requireClasses('Mailer');
	
	$mail = new Mailer();
	$mail->From = "laponto@esperanto.org.ar";
	$mail->FromName = "La Ponto";
	$mail->Subject = "Vi havas novan mesagxon";	
	$mail->Body = 
		'Por vidi la novan mesagxon, klaku ' .
		'<a href="http://laponto.esperanto.org.ar/mesagxoj.php?naf=on">cxi tie</a> aux ' .
		'iru al la jena adreso:<br/><br/>' .
		'http://laponto.esperanto.org.ar/mesagxoj.php?naf=on';
		
	if (sizeof($emails) == 1) {
		$mail->AddAddress($emails[0]);
	} else {
		foreach($emails as $email) {
			$mail->AddBCC($email);
		}	
	}
	
	$mail->IsHtml(true);
	
	$mail->Send();
}

/**
 * Cuenta las líneas de un texto (que puede tener cero, una o dos líneas).
 */
function countLines($text) {
	$text = trim($text);
	if (!$text) return 0;
	return substr_count($text, "\n") + 1;
}

class CreateFilmSubtitleFormatListener {
	
	var $film;
	var $number;
	var $man;
	var $subtitles;

	function CreateFilmSubtitleFormatListener($film) {
		$this->film = $film;
	}
	
	function readingStarted() {
		$this->number = 1;
		$this->subtitles = array();
		$this->man = new FilmSubtitleManager();
	}
	
	function subtitleRead($subtitle) {
		$filmSubtitle = new FilmSubtitle();
		$filmSubtitle->film_id = $this->film->id;
		$filmSubtitle->number = $this->number ? $this->number : '1';
		$filmSubtitle->from_time = $subtitle->from ? $subtitle->from : '';
		$filmSubtitle->to_time = $subtitle->to ? $subtitle->to : '';
		$filmSubtitle->original_text = $subtitle->text ? $subtitle->text : '';
		$filmSubtitle->translated_text = $subtitle->translatedText ? $subtitle->translatedText : '';
		$filmSubtitle->trust = $subtitle->translatedText ? FILM_SUBTITLE_LOW_TRUST : FILM_SUBTITLE_NO_TRUST;
		
		array_push($this->subtitles, $filmSubtitle);
		
		if (sizeof($this->subtitles) == 100) {
			$this->man->insertMany($this->subtitles);
			$this->subtitles = array();			
		}
		
		$this->number++;
	}
	
	function readingFinished($subtitlesExtraInfo) {
		if (sizeof($this->subtitles) > 0) {
			$this->man->insertMany($this->subtitles);
		}
		
		// A ver la información extra
		$this->film->subtitles_extra_info = $subtitlesExtraInfo; 
		
		$man = new FilmManager();
		$man->update($this->film);
	}
	
}
?>
