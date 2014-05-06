<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Date');
PHP::requireCustom('FilmUser', 'FilmUserEvent', 'Operations');

PHP::rootInclude('layout/top_html_page_js.php');

$user = UserManager::getRemembered();
if ($user) {
	$from = Request::getParameter('de');
	$to = Request::getParameter('al');
	$subject = trim(Request::getParameter('temo'));
	$text = trim(Request::getParameter('mesagxo'));
	
	if ($subject and $text) {
		$event = new UserEvent();
		$event->from_x = $from;
		$event->to_user = $to;
		$event->message = serialize(array($subject, nl2br($text)));
		$event->event_type = 'message';
		
		$man = new UserEventManager();
		$man->insert($event);
		
		notifyUserIdByEmail($event->to_user);
		?>
		parent.document.getElementById('message').innerHTML = '<strong><?= __('Mesagxo sendita!') ?></strong>';
		<?
	} else {
		?>
		alert('<?= __('Vi devas plenigi cxiujn tekstkampojn') ?>.');
		<?
	}
}

PHP::rootInclude('layout/bottom_html_page_js.php');
?>