<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response');
PHP::requireCustom('Film', 'FilmUser', 'DateUtils', 'Language');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

$user->name = Request::getParameter('name', false);
$user->email = Request::getParameter('email', false);
$user->language = Request::getParameter('lingvo');
$user->block = Request::getParameter('block');
$user->notify_new_messages = Request::getBoolean('notifyNewMessages');
$user->notify_by_email = Request::getBoolean('notifyByEmail');
$user->trust = Request::getParameter('trust');

$man = new UserManager();
$man->update($user);

UserManager::login($user);

Response::sendRedirect('uzantpreferoj.php?ok=on');
?>