<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response', 'Session');
PHP::requireCustom('Operations', 'Film', 'FilmUser', 'FilmSubtitle');

$film_id = Request::getParameter('film_id');
$number = Request::getParameter('number');
$translated_text = trim(Request::getParameter('translated_text', false)); 
$trust = Request::getParameter('trust');
$comments = Request::getParameter('comments', false);
$explanation = Request::getParameter('explanation');
$type = Request::getParameter('type');
$block = Request::getParameter('block');
$return = Request::getParameter('return');
$small = Request::getParameter('small') == 'yes';

$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $film_id));

if (!$film) {
	die();
}

global $myPart;
$user = UserManager::getRemembered();
if ($user) $myPart = $user->getParticipationInFilm($film->id);
$belongs = ($user and $myPart and !$myPart->blocked);
if (!$belongs) {
	die();
}

$man = new FilmSubtitleManager();
$subtitle = $man->getFromKeys(array('film_id' => $film_id, 'number' => $number));

if (!$subtitle) {
	die();
}

if (!$myPart->super_user) {
  $small = false;
}

PHP::rootInclude('layout/top_html_page_js.php');

if (trim($translated_text)) {
	changeSubtitle($film, $subtitle, $translated_text, $trust, $comments, $explanation, $small);
	
	switch($return) {
		case 'this':
			?>
			parent.window.location = 'traduki.php?id=<?= $film_id ?>&type=<?= $type ?>&number=<?= $number ?>&block=<?= $block ?>';
			<?
			break;
		case 'next':
			?>
			parent.window.location = 'traduki.php?id=<?= $film_id ?>&type=<?= $type ?>&number=<?= $number + 1 ?>&block=<?= $block ?>';
			<?
			break;
		case 'random':
			?>
			parent.window.location = 'traduki.php?id=<?= $film_id ?>&type=<?= $type ?>';
			<?
			break;
	}
} else {
	?>
	alert('<?= __('Vi devas skribi tradukon') ?>.');
	<?
}

PHP::rootInclude('layout/bottom_html_page_js.php');
?>