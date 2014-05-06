<?
PHP::requireCustom('Utils');
global $film;
?>
<div style="border: 1px dashed black;padding:10px">
<strong><?= __('Preferoj de {x}', array('x' => $film->format)) ?></strong>:<br/><br/>
<?
$addHours = '00';
$addMinutes = '00';
$addSeconds = '00';
$addMilliseconds = '000';
if ($film->download_options) {
	$obj = unserialize($film->download_options);
	$addHours = twoDigits($obj->addHours);
	$addMinutes = twoDigits($obj->addMinutes);
	$addSeconds = twoDigits($obj->addSeconds);
	$addMilliseconds = threeDigits($obj->addMilliseconds);
}
?>
<div class="s"><?= __('Aldoni la jenan tempon al cxiu subtitolo') ?>:</div>
<select name="invert_sign">
	<option value="">+</option>
	<option value="on">-</option>
</select>
<input type="text" name="add_hours" size="3" maxlength="2" value="<?= $addHours ?>"/><?= __('H (horo)') ?>:
<input type="text" name="add_minutes" size="3" maxlength="2" value="<?= $addMinutes ?>"/><?= __('M (minuto)') ?>:
<input type="text" name="add_seconds" size="3" maxlength="2" value="<?= $addSeconds ?>"/><?= __('S (segundo)') ?>,
<input type="text" name="add_milliseconds" size="3" maxlength="3" value="<?= $addMilliseconds ?>"/><?= __('m (milisegundo)') ?>
<br/><br/>
<?
$user = UserManager::getRemembered();
if ($user and $user->id == $film->user_id) {
	?>
	<input type="checkbox" name="save_options"/> <?= __('Konservi cxi tiujn opciojn') ?><br/><br/>
	<?
}
?>
</div>
<br/>