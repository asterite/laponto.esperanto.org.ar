<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response', 'Date');
PHP::requireCustom('Operations', 'Film', 'FilmHistory', 'FilmUser', 'FilmSubtitle');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

$film_id = Request::getParameter('film_id');
$user_id = Request::getParameter('user_id');

$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $film_id));

if ($film->user_id != $user->id) {
	Response::sendRedirect('index.php');
}

// Primero borro el historial
$man = new FilmHistoryManager();
$man->deleteWhere('user_id = :user_id AND film_id = :film_id',
	new QueryReplacement('user_id', $user_id, SQL_TYPE_INTEGER),
	new QueryReplacement('film_id', $film_id, SQL_TYPE_INTEGER));
	
// Y ahora a arreglar los subtítulos
$man = new FilmSubtitleManager();
$man->addWhereField('user_id', '=', $user_id);
$man->query();

while($sub = $man->next()) {
	// Tengo que revertir el cambio... A ver cuál es el último
	$man2 = new FilmHistoryManager();
	$man2->addWhereField('film_id', '=', $film->id);
	$man2->addWhereField('number', '=', $sub->number);
	$man2->addOrder('date', false);
	$man2->setShowOnly(1);
	$man2->query();
	
	$old = $sub->trust;
	if ($history = $man2->next()) {
		// Si hay uno próximo, actualizo el subtítulo con sus datos
		$sub->user_id = $history->user_id;
		$sub->translated_text = $history->the_text;
		$sub->trust = $history->the_trust;
	} else {
		// Si no había, reseteo el subtítulo
		$sub->user_id = false;
		$sub->translated_text = false;
		$sub->trust = FILM_SUBTITLE_NO_TRUST;
	}
	
	$man3 = new FilmSubtitleManager();
	$man3->update($sub);
}

$man4 = new FilmManager();
$man4->update($film);

Response::sendRedirect('filmo.php?id=' . $film_id);
?>