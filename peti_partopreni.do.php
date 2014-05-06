<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response', 'Date');
PHP::requireCustom('Film', 'FilmUser', 'FilmUserEvent', 'Operations');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

$film_id = Request::getParameter('film_id');

$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $film_id));

if (!$film) Response::sendRedirect('index.php');

$man = $film->getParticipants();
$man->addWhereField('can_add', '=', true);
$man->query();

$ids = array();
$events = array();

while($participant = $man->next()) {
	$event = new UserEvent();
	$event->from_x = $user->id;
	$event->to_user = $participant->user_id;
	$event->message = $film->id;
	$event->event_type = 'request';
	array_push($events, $event);
	array_push($ids, $event->to_user);
}

$man = new UserEventManager();
$man->insertMany($events);

notifyManyUserIdByEmail($ids);

Response::sendRedirect('filmo.php?id=' . $film_id);
?>