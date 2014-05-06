<?
PHP::requireClasses('Cookie');
PHP::requireCustom('Language', 'FilmUser');

$user = UserManager::getRemembered();

if ($user) {
  if ($user->language) {
    LanguageManager::setSessionLanguage($user->language);
  } else {
    LanguageManager::setSessionLanguage('eo');
  }
} else {
  $cookie = new Cookie('language');
  if ($cookie->exists()) {
  	LanguageManager::setSessionLanguage($cookie->value);
  } else {
    $lang = explode('-', PHP::getServerParameter('HTTP_ACCEPT_LANGUAGE'));
    $lang[1] = strtoupper($lang[1]);
    
    $theLang = $lang[0] . '_' . $lang[1];
    if (strlen($theLang) > 5) {
      $theLang = substr($theLang, 0, 5);
    }
    
    $cookie->value = $theLang;
  	LanguageManager::setSessionLanguage($cookie->value);
  	$cookie->setTimeout(60*60*24*365*10);
  	$cookie->send();
  }
}
?>