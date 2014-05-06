<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response');
PHP::requireCustom('FilmUser', 'Operations', 'Language');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

PHP::rootInclude('layout/top_html_page_js.php');

global $application;
$root = $application->getDocumentRoot();

$name = Request::getParameter('name');
$rules = Request::getParameter('rules');
$file = Request::getParameter('file');
$kodo = Request::getParameter('kodo');
$publika = Request::getBoolean('publika');
list($from_language, $to_language) = explode('-', Request::getParameter('lingvo'));

// Alguno de los dos idiomas tiene que ser Esperanto
if ($from_language != 1 and $to_language != 1) die();
// Los idiomas tienen que ser distintos
if ($from_language == $to_language) die();

if (!trim($name)) {
	?>
	alert('<?= __('Vi devas tajpi la nomon de la filmo') ?>.');
	<?
} else {
	if ($from_language == $to_language) {
		?>
		alert('<?= __('La du lingvoj devas esti malsamaj') ?>.');
		<?
	} else {
		if ($from_language == 1 and $kodo == 'ISO-8859-1') {
			$kodo = 'ESPERANTO';
		}
		
		$new_file = $root . 'uploaded.subtitles';
		
		if (move_uploaded_file($file['tmp_name'], $new_file)) {
			$film = createFilm($new_file, $name, $rules, $from_language, $to_language, $kodo, $publika);
			unlink($new_file);
			
			if ($film) {
				?>
				parent.window.location = 'filmo.php?id=<?= $film->id ?>'; 
				<?
			} else {
				?>
				alert('<?= __('La formato de la subtitoloj ne estas komprenata') ?>.');
				<?
			}
		} else {
			?>
			alert('<?= __('La dosiero ne povas esti alsxutanta') ?>.');
			<?
		}
	}
}

PHP::rootInclude('layout/bottom_html_page_js.php');
?>