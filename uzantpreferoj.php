<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response');
PHP::requireCustom('Film', 'FilmUser', 'DateUtils', 'Esperanto', 'Language');

$user = UserManager::getRemembered();
if (!$user) Response::sendRedirect('index.php');

PHP::rootInclude('layout/top.php');
?>
<div id="descripcion">
<div id="subtitulo"><?= __('Miaj preferoj') ?></div>
<?= __('pri.miaj.preferoj') ?>
</div>
<?
PHP::rootInclude('layout/menu.php');

if (Request::getBoolean('ok')) {
	?>
	<br/>
	<div id="changesOk"><?= __('Sxangxoj pretaj!') ?></div>
	<br/>
	<?
}
?>
<form action="uzantpreferoj.do.php">
	<?= __('Via nomo') ?>:<br/><br style="font-size:4px" />
	<input type="text" name="name" value="<?= $user->name ?>" /><br/><br/>
	
	<?= __('Via retposxtadreso') ?>:<br/><br style="font-size:4px" />
	<input type="text" name="email" value="<?= $user->email ?>" /><br/><br/>
	
	<?= __('Lingvo') ?>:<br/><br style="font-size:4px" />
	<select name="lingvo">
	  <?
	  $theLang = LanguageManager::getSessionLanguage();
	  
	  $man = new LanguageManager();
	  $man->query();
	  
	  while($lang = $man->next()) {
		  if (!$lang->isTranslated()) continue;
		  ?>
		  <option value="<?= $lang->code ?>"
		  <?= $lang->code == $theLang ? 'selected' : '' ?>
		  ><?= $lang->name_language ?></option>
		  <?
	  }
	  ?>
	</select><br/><br/>
	
	<?= __('Kiom da kunteksto montri en subtitolo') ?>:<br/><br style="font-size:4px" />
	<select name="block">
		<?
		for($i = 5; $i <= 25; $i += 2) {
			?>
			<option value="<?= $i ?>"
			<?
			if ($i == $user->block) print 'selected';
			?>
			><?= __('{x} linioj', array('x' => $i)) ?></option>
			<?
		}
		?>
	</select><br/><br/>
	
	<?= __('Aperigi fenestron se vi ricevas novan mesagxon') ?>:<br/><br style="font-size:4px" />
	<select name="notifyNewMessages">
		<option value="on" <?= $user->notify_new_messages ? 'selected' : '' ?>><?= __('Jes') ?></option>
		<option value="" <?= $user->notify_new_messages ? '' : 'selected' ?>><?= __('Ne') ?></option>
	</select><br/><br/>
	
	<?= __('Sendi retposxtmesagxon se vi ricevas novan mesagxon') ?><br/>
	(<?= __('por tio, vi devas indiki vian retposxtadreson') ?>):<br/><br style="font-size:4px" />
	<select name="notifyByEmail">
		<option value="on" <?= $user->notify_by_email ? 'selected' : '' ?>><?= __('Jes') ?></option>
		<option value="" <?= $user->notify_by_email ? '' : 'selected' ?>><?= __('Ne') ?></option>
	</select><br/><br/>
	
	<?= __('Kiom da fido vi havas je viaj tradukoj?') ?><br/><br style="font-size:4px" />
	<select name="trust">
	  <option value="<?= FILM_SUBTITLE_LOW_TRUST ?>" style="color:red" <?= $user->trust == FILM_SUBTITLE_LOW_TRUST ? 'selected' : '' ?>><?= __('Malaltan') ?></option>
	  <option value="<?= FILM_SUBTITLE_MEDIUM_TRUST ?>" style="color:orange" <?= $user->trust == FILM_SUBTITLE_MEDIUM_TRUST ? 'selected' : '' ?>><?= __('Mezan') ?></option>
	  <option value="<?= FILM_SUBTITLE_MAX_TRUST ?>" style="color:green" <?= $user->trust == FILM_SUBTITLE_MAX_TRUST ? 'selected' : '' ?>><?= __('Altan') ?></option>
	</select>
	<br/><br/>
	
	<input type="submit" value="<?= __('Bone') ?>"/>
</form>
<?
PHP::rootInclude('layout/bottom.php');
?>