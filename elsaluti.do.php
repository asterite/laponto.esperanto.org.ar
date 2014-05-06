<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Response');
PHP::requireCustom('FilmUser');

UserManager::logoff($do);

Response::sendRedirect('index.php');
?>