<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response');
PHP::requireCustom('FilmUser', 'FilmUserEvent');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

$id = Request::getParameter('id');

if ($id) {
	if (!is_array($id)) {
		$id = array($id);
	} 
	
	foreach($id as $i) {
		$man = new UserEventManager();
		$event = $man->getFromKeys(array('id' => $i));
		
		if ($event and $event->to_user == $user->id) {
			$man = new UserEventManager();
			$man->delete($event);
		}
	}
}

Response::sendRedirect('mesagxoj.php');
?>