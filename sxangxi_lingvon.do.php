<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response', 'Cookie');
PHP::requireCustom('Language');

$leng = Request::getParameter('lingvo');
$lang = LanguageManager::setSessionLanguage($leng);

$cookie = new Cookie('language');
$cookie->value = $lang;
$cookie->setTimeout(60*60*24*365*10);
$cookie->send();

$referer = PHP::getServerParameter('HTTP_REFERER');
Response::sendRedirect($referer);
?>