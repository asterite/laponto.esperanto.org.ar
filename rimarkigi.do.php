<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response', 'Date');
PHP::requireCustom('Film', 'FilmSubtitle', 'FilmUser', 'FilmUserEvent', 
	'Language', 'Operations');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

$film_id = Request::getParameter('film_id');
$sub_id = Request::getParameter('sub_id');

$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $film_id));

$man = new FilmSubtitleManager();
$subtitle = $man->getFromKeys(array('id' => $sub_id));

$event = new UserEvent();
$event->from_x = $user->id;
$event->to_user = $film->user_id;
$event->event_type = 'alert_evil';
$event->message = serialize(array($film->id, $subtitle->user_id, $subtitle->number, nl2br(trim($subtitle->original_text)), nl2br(trim($subtitle->translated_text))));

$man = new UserEventManager();
$man->insert($event);

notifyUserIdByEmail($event->to_user);

PHP::rootInclude('layout/top_html_page_js.php');
?>
var a = parent.document.getElementById('alertOwner');
a.innerHTML = '<span style="color:green"><?= __('Preta. Vi jam rimarkigis la mastron pri cxi tiu barbarajxo.') ?><br/><?= __('Dankon') ?> :-)</span>'
<?
PHP::rootInclude('layout/bottom_html_page_js.php');
?>