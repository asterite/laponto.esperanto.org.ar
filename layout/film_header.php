<?
global $film, $context_path;

$from_language = $film->getFromLanguage();
$to_language = $film->getToLanguage();

$status = $film->getStatus();
?>
<div id="titulo"><?
if ($film->public) {
  ?><img src="<?= $context_path ?>/img/unlocked.gif" style="position:relative;top:2px"/><?
} else {
	?><img src="<?= $context_path ?>/img/locked.gif" style="position:relative;top:2px"/><?
}
?><?= $film->name ?> (<?= __('{x}% preta', array('x' => $status->getPercentCompleted())) ?>)</div>
<div id="subtitulo"><?= $from_language->name ?> <img src="<?= $context_path ?>/img/arrow_right.gif" style="position:relative;top:3px;padding-left:4px;padding-right:4px"/> <?= $to_language->name ?></div>