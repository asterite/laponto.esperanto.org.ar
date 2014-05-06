<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response', 'Date');
PHP::requireCustom('Film', 'FilmUser', 'FilmUserEvent', 'Operations');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

$film_id = Request::getParameter('film_id');

$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $film_id));

if (!$film or !$film->public) Response::sendRedirect('index.php');

joinUserToFilm($user->id, $film->id, 0, 0, false);

Response::sendRedirect('filmo.php?id=' . $film_id);
?>