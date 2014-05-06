<?
PHP::requireClasses('HTMLPage');
PHP::requireCustom('Language');

global $html_page, $context_path, $page_title;
$html_page->setIsXHTML(true);
if ($page_title) {
	$html_page->setTitle("La Ponto - $page_title");
} else {
	$html_page->setTitle('La Ponto');
}
$html_page->addCSS("$context_path/sub.css");
$html_page->addJS("$context_path/sub.js");
$html_page->begin();
?>