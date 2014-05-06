<?
require_once('../_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response');
PHP::requireCustom('Film', 'FilmUser', 'FilmSubtitle', 'Subtitle', 'SubtitleFormatFactory', 'Esperanto', 'Unicode');

$id = Request::getParameter('id');

global $film;
$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $id));

if (!$id or !$film) {
	Response::sendRedirect('index.php');
}

$format = strtolower($film->format);
$filename = str_replace(' ', '_', $film->name);
$showNumbers = Request::getBoolean('montriNumerojn');

$toLanguage = $film->getToLanguage();
$encoding = $toLanguage->encoding;
if ($encoding == 'ESPERANTO') {
	$theEncoding = 'ISO-8859-1';
} else {
	$theEncoding = $encoding;
}

$man = $film->getSubtitles();
$man->addOrder('number');
$man->query();

// Este archivo tiene que definir la variable
// $theProvider, que implementa SubtitlesProvider
// Recibe: $man, $showNumbers 
include("index_{$film->format}.do.php");

class MyWriter extends SubtitlesWriter {
	
	var $encoding;
	
	function MyWriter($encoding) {
		$this->encoding = $encoding;
	}
	
	function write($s) {
		print magicUnconversion($s, $this->encoding);
	}
	
}

$factory = SubtitleFormatFactory::getInstance();
$format = $factory->getSubtitleFormat($film->format);
$extension = $format->getExtension();

header("Content-type: text; charset=$theEncoding");
header("Content-disposition: attachment; filename={$filename}.{$extension}");
header("Pragma: no-cache");
header("Expires: 0");

$format->writeSubtitles($theProvider, new MyWriter($encoding));
?>