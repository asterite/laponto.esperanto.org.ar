<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response', 'Date');
PHP::requireCustom('Operations', 'Film', 'FilmUser', 'FilmUserEvent',
	'Operations');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

$film_id = Request::getParameter('film_id');
$user_id = Request::getParameter('user_id');
$to = Request::getParameter('to', '');

$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $film_id));

// No se le pueden cambiar los privilegios al dueño de la película
if (!$film or $film->user_id == $user_id) {
	Response::sendRedirect('index.php');
}

$participant = $user->getParticipationInFilm($film->id);
if (!$participant->super_user) {
	Response::sendRedirect('index.php');
}

$man = new FilmParticipantManager();
$man->addWhereField('film_id', '=', $film->id);
$man->addWhereField('user_id', '=', $user_id);
$man->query();
$p = $man->next();

switch($to) {
	case 'super_user':
		$p->can_add = true;
		$p->super_user = true;
		break;
	case 'adder':
		$p->can_add = true;
		$p->super_user = false;
		break;
	case 'normal':
		$p->can_add = false;
		$p->super_user = false;
		break;
	default:
		Response::sendRedirect('index.php');
}

$man->update($p);

$event = new UserEvent();
$event->from_x = $film->id;
$event->to_user = $user_id;
$event->event_type = 'film_change_privileges';
$event->message = serialize(array($user->id, $to));

$man = new UserEventManager();
$man->insert($event);

notifyUserIdByEmail($event->to_user);

Response::sendRedirect('filmo.php?id=' . $film_id);
?>