<?
require_once('_p/classes/PHP.php');
PHP::requireCustom('FilmUser', 'OnlineUsers');

$user = UserManager::getRemembered();
OnlineUsers::logoff($user->id);
?>