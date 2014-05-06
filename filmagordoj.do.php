<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response');
PHP::requireCustom('Film', 'FilmUser', 'Display');

$id = Request::getParameter('id');

global $film;
$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $id));

if (!$id or !$film) {
	Response::sendRedirect('index.php');
}

$user = UserManager::getRemembered();
if (!$user or $user->id != $film->user_id) {
	Response::sendRedirect('index.php');
}

PHP::rootInclude('layout/top_html_page_js.php');

$film->name = Request::getParameter('name');
$film->public = Request::getBoolean('publika');

list($from_language, $to_language) = explode('-', Request::getParameter('lingvo'));
$film->from_language = $from_language;
$film->to_language = $to_language;

if (!trim($film->name)) {
	?>
	alert('<?= __('Vi devas tajpi la nomon de la filmo') ?>.');
	<?
} else {
  $man = new FilmManager();
  $man->update($film);
  ?>
  parent.window.location = 'filmo.php?id=<?= $film->id ?>'; 
  <?   
}

PHP::rootInclude('layout/bottom_html_page_js.php');
?>
