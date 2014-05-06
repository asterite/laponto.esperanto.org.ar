<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response', 'Date');
PHP::requireCustom('Film', 'FilmHistory', 'FilmParticipant', 'FilmUser', 'FilmUserEvent', 'Operations');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

$film_id = Request::getParameter('film_id');
$password = md5(Request::getParameter('pasvorto'));
$kialo = Request::getParameter('kialo');

$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $film_id));

if (!$user or $film->user_id != $user->id) {
	die();
}

PHP::rootInclude('layout/top_html_page_js.php');

if ($user->password != $password) {
	?>
	alert('<?= __('La pasvorto ne estas korekta') ?>.');
	<?
} else {
	// Primero tomo los participantes, así después les aviso
	$manp = $film->getParticipants();
	
	// No quiero enviarle notificación al administrador
	$manp->addWhereField('user_id', '!=', $user->id);
	$manp->query();
	
	$participants = $manp->getArray();
	
	// Luego borro el historial
	$man = new FilmHistoryManager();
	$man->deleteWhere('film_id = :film_id', new QueryReplacement('film_id', $film->id, SQL_TYPE_INTEGER));
	
	// Ahora borro los subtítulos
	$man = new FilmSubtitleManager();
	$man->deleteWhere('film_id = :film_id', new QueryReplacement('film_id', $film->id, SQL_TYPE_INTEGER));
	
	// Finalmente le película
	$man = new FilmManager();
	$man->delete($film);
	
	// Y ahora a enviar los eventos
	$ids = array();
	$events = array();
	foreach($participants as $participant) {
		$event = new UserEvent();
		$event->from_x = $user->id;
		$event->to_user = $participant->user_id;
		$event->event_type = 'film_deleted';
		$event->message = serialize(array($film->name, nl2br(trim($kialo))));
		array_push($events, $event);
		array_push($ids, $event->to_user);
	}
	
	$man = new UserEventManager();
	$man->insertMany($events);
	
	notifyManyUserIdByEmail($ids);
	
	// Y ahora a borrar a los participantes
	$man = new FilmParticipantManager();
	$man->deleteWhere('film_id = :film_id', new QueryReplacement('film_id', $film->id, SQL_TYPE_INTEGER));
	?>
	parent.window.location = 'index.php';
	<?
}

PHP::rootInclude('layout/bottom_html_page_js.php');
?>