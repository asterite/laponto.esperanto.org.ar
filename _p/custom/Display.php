<?
function displayShortDate($date) {
	PHP::requireClasses('Date');
	$now = new Date();
	
	if ($now->year == $date->year and 
		$now->month == $date->month and
		$now->day == $date->day) {
			return $date->format('H:i');
	} else {
		return __('{x} de {y}', array('x' => $date->day, 'y' => __(monthName($date->month))));
	}
}

function colorForTrust($trust) {
	switch($trust) {
		case FILM_SUBTITLE_LOW_TRUST:
			return "red";
		case FILM_SUBTITLE_MEDIUM_TRUST:
			return "orange";
		case FILM_SUBTITLE_MAX_TRUST:
			return "green";
	}
}

function displayLongDate($date) {
	PHP::requireCustom('DateUtils');
	return dateNormalFormat($date);
}

function displayPrivilegesChangeSubject($to, $film) {
	switch($to) {
		case 'normal':
			return __('Nun vi estas normala uzanto en la filmo {x}', array('x' => displayFilm($film)));
		case 'adder':
			return __('Nun vi estas invitanto en la filmo {x}', array('x' => displayFilm($film)));
		case 'super_user':
			return __('Nun vi estas super uzanto en la filmo {x}', array('x' => displayFilm($film)));
		case 'blocked':
			return __('Vi estis blokita en la filmo {x}', array('x' => displayFilm($film))); 
		case 'unblocked':
			return __('Vi estis malblokita en la filmo {x}', array('x' => displayFilm($film)));
	}
}

function displayUser($user) {
	$id = $user->id ? $user->id : $user->user_id;
	$status = OnlineUsers::getOnlineStatus($id);
	switch($status) {
		case USER_OFFLINE:
			$img = 'offline.gif';
			break;
		case USER_IDLE:
			$img = 'idle.gif';
			break;
		case USER_ONLINE:
			$img = 'online.gif';
			break;
	}
	return '<img src="img/' . $img  . '" border="0"/> <a href="javascript:montriProfilon(' . $id . ')">' . $user->nickname . '</a>';
}

function displayFilm($film) {
	if ($film) {
		global $context_path;
		$from = $film->getFromLanguage();
		$to = $film->getToLanguage();
		$status = $film->getStatus();
		$s = '';
		if ($film->public) {
  		$s .= '<img src="' . $context_path . '/img/unlocked.gif" style="position:relative;top:4px"/>';
		} else {
  		$s .= '<img src="' . $context_path . '/img/locked.gif" style="position:relative;top:4px"/>';
		}
		$s .= '<a href="' . $context_path . '/filmo.php?id=' . $film->id . '">' . $film->name . '</a> (' . $from->name . ' <img src="' . $context_path . '/img/arrow_right_small.gif"/> ' . $to->name . ', ' . __('{x}% preta', array('x' => $status->getPercentCompleted())) . ')';
		return $s;
	} else {
		return '??? (la filmo estis forvisxita)';
	}		
}

function displayPercentage($notEmpty, $points) {
	if ($notEmpty == 0) {
		return '0%';
	} else {
		return round(100 * $points / $notEmpty, 2) . '%';
	}
}

/**
 * Dado un texto, le agrega formato lindo:
 * - convierte en links a los links (con <a ...)
 * - pone caritas
 */
function niceFormat($s) {
	global $context_path;
	$s = str_replace(';-)', getImageTag("$context_path/img/wink_smile.gif"), $s);
	$s = str_replace(';)', getImageTag("$context_path/img/wink_smile.gif"), $s);
	$s = str_replace(':)', getImageTag("$context_path/img/regular_smile.gif"), $s);
	$s = str_replace(':-)', getImageTag("$context_path/img/regular_smile.gif"), $s);
	$s = str_replace(':D', getImageTag("$context_path/img/teeth_smile.gif"), $s);
	$s = str_replace(':-D', getImageTag("$context_path/img/teeth_smile.gif"), $s);
	return $s;
}

/**
 * Devuelve un tag de imagen con el attributo src especificado.
 */
function getImageTag($src) {
	return '<img src="' . $src . '"/>';
}
?>