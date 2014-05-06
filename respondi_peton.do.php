<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response', 'Date');
PHP::requireCustom('Operations', 'FilmUser', 'FilmUserEvent', 'Operations');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

$id = Request::getParameter('id');

$man = new UserEventManager();
$event = $man->getFromKeys(array('id' => $id));

if (!$event or $event->to_user != $user->id) {
	Response::sendRedirect('index.php');
}

$accept = Request::getBoolean('akcepti');
if ($accept) {
	$man = new UserManager();
	joinUserToFilm($event->from_x, $event->message, false, false);
	
	$man = new UserEventManager();
	$man->deleteWhere('event_type = :event_type AND from_x = :from_x AND message = :message',
		new QueryReplacement('event_type', 'request', SQL_TYPE_STRING),
		new QueryReplacement('from_x', $event->from_x, SQL_TYPE_STRING),
		new QueryReplacement('message', $event->message, SQL_TYPE_STRING));
} else {
	$man = new UserEventManager();
	$man->delete($event);
	
	// Vamos a ver si quedan más eventos... porque sino,
	// hay que mandarle el evento de que lo rechazaron
	$man = new UserEventManager();
	$man->addWhereField('event_type', '=', 'request');
	$man->addWhereField('from_x', '=', $event->from_x);
	$man->addWhereField('message', '=', $event->message);
	$man->query();
	
	if (!$man->hasNext()) {
		// No hay más, mandarle un mensaje de rechazo al usuario
		$event2 = new UserEvent();
		$event2->from_x = $event->message;
		$event2->to_user = $event->from_x;
		$event2->event_type = 'film_access_denied';
		
		$man->insert($event2);
		
		notifyUserIdByEmail($event2->to_user);
	}
}

Response::sendRedirect('mesagxoj.php');
?>