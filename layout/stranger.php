<div id="stranger">
<?
global $film, $myPart, $context_path;

$user = UserManager::getRemembered();

if ($myPart and $myPart->blocked) {
	?>
	<span style="color:red"><?= __('Vi ne povas traduki subtitolojn de cxi tiu filmo, cxar vi estis blokita') ?>.</span>.
	<?
} else {
	?>
	<?= __('Vi ne partoprenas la tradukadon de cxi tiu filmo') ?>.
	<?
	$man = new UserEventManager();
	$man->addWhereField('from_x', '=', $user->id);
	$man->addWhereField('message', '=', $film->id);
	$man->addWhereField('event_type', '=', 'request');
	$man->query();
	
	if ($man->hasNext()) {
		?>
		<br/><?= __('Sed vi jam petis partopreni, atendu la respondon') ?>...
		<?
	} else {
  	if ($film->public) {
    	if ($user) {
      	?>
      	<br/><a href="<?= $context_path ?>/partopreni.do.php?film_id=<?= $film->id ?>"><?= __('Claku cxi tie por partopreni!') ?></a>
      	<?
    	} else {
      	?>
      	b
      	<?
    	}
  	} else {
  		?>
  		<br/><a href="<?= $context_path ?>/peti_partopreni.do.php?film_id=<?= $film->id ?>"><?= __('Petu partopreni!') ?></a>
  		<?	
		}
	}
}
?>
</div>