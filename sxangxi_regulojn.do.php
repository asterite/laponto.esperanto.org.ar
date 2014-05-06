<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response', 'Date');
PHP::requireCustom('Operations', 'Film', 'FilmUser');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

$film_id = Request::getParameter('film_id');
$rules = Request::getParameter('reguloj');

$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $film_id));

// Sólo el dueño puede cambiar esto...
if (!$film or $film->user_id != $user->id) {
	Response::sendRedirect('index.php');
}

$film->rules = $rules;

$man = new FilmManager();
$man->update($film);

Response::sendRedirect('filmo.php?id=' . $film_id);
?>