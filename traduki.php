<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response', 'Session');
PHP::requireCustom('Operations', 'Film', 'FilmUser', 'Display');

$user = UserManager::getRemembered(); 

$id = Request::getParameter('id');
$type = Request::getParameter('type');
$number = Request::getParameter('number');
$block = Request::getParameter('block', $user->block ? $user->block : 5);

global $film, $myPart;
$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $id));
if ($user) $myPart = $user->getParticipationInFilm($film->id);
$belongs = ($user and $myPart and !$myPart->blocked);

if (!$id or !$film) {
	Response::sendRedirect('index.php');
}

$status = $film->getStatus();

// Si no hay subtítulos de esta categoría
if ($status->get($type) == 0 && !$number) {
	Response::sendRedirect('filmo.php?id=' . $id);
}

if ($number) {
	if ($number < 1) {
		$number = 1;
	}
	if ($number > $status->count()) {
		$number = $status->count();
	}
	
	list($subtitles, $selected) = getSubtitleBlock($film, $block, $number);
} else {
	if ($status->get($type) == 0) {
		Response::sendRedirect('filmo.php?id=' . $id);
	}
	list($subtitles, $selected) = getRandomSubtitleBlock($film, $block, $type);	
}

$from_language = $film->getFromLanguage();
$to_language = $film->getToLanguage();

PHP::rootInclude('layout/top.php');
global $html_page;
$html_page->addCSS('traduki.css');
?>
<div id="descripcion">
<?
include('layout/film_header.php');

if ($type) {
	switch($type) {
		case FILM_SUBTITLE_NO_TRUST:
			$theTrust = __('Netradukitaj subtitoloj');
			break;
		case FILM_SUBTITLE_LOW_TRUST:
			$theTrust = __('Subtitoloj tradukitaj kun malalta fido');
			break;
		case FILM_SUBTITLE_MEDIUM_TRUST:
			$theTrust = __('Subtitoloj tradukitaj kun meza fido');
			break;
		case FILM_SUBTITLE_MAX_TRUST:
			$theTrust = __('Subtitoloj tradukitaj kun alta fido');
			break;
	}
	?>
	<div style="margin-bottom:10px"><strong><?= $theTrust ?></strong></div>
	<?
}
?>
<?= __('pri.kiel.traduki') ?>
</div>
<?
global $menu_left;
if ($type) {
	$menu_left = array(
		array(__('Alia subtitolo'), 'traduki.php?id=' . $film->id . '&type='  . $type, 'img/reload.gif'),
		array(__('Reen al {x}', array('x' => $film->name)), 'filmo.php?id=' . $film->id, 'img/film.png'),
	);
} else {
	$menu_left = array(
		array(__('Reen al {x}', array('x' => $film->name)), 'filmo.php?id=' . $film->id, 'img/film.png'),
	);
}
PHP::rootInclude('layout/menu.php');

if ($user and !$belongs) {
	PHP::rootInclude('layout/stranger.php');
	print '<br/>';
}
if ($user and $belongs) { 
	?>
	<div><strong><?= __('Traduku nur la negrigitan tekston') ?></strong>:</div>
	<br/>
	<?
}
?>
<div>
<form action="traduki.php" method="get">
<input type="hidden" name="id" value="<?= $film->id ?>"/>
<input type="hidden" name="type" value="<?= $type ?>"/>
<input type="hidden" name="block" value="<?= $block ?>"/>
<input type="text" name="number" value="<?= $subtitles[$selected]->number ?>" style="width:48px"/> &nbsp <input type="submit" value="<?= __('Iri') ?>..."/> (<?= __('{x} gxis {y}', array('x' => 1, 'y' => $status->count())) ?>)
</form>
<form action="sercxi_subtitolon.php" method="get" target="rapida_sercxo" onSubmit="return malfermiRapidanSercxon()">
<input type="hidden" name="id" value="<?= $film->id ?>"/><?
?><input type="hidden" name="type" value="<?= $type ?>"/><?
?><input type="hidden" name="block" value="<?= $block ?>"/>&nbsp;
<input type="text" name="search" size="10" <?= $from_language->getInputEvents() ?>/>
<input type="submit" value="<?= __('Rapida sercxo') ?>"/>
</form>
</div>
<br/>

<table border="0" cellpadding="0" cellspacing="0">
	<?
	for($i = 0; $i < $block; $i++) {
		$ot = nl2br($subtitles[$i]->original_text);
		$tt = nl2br($subtitles[$i]->translated_text);
		$s = $i == 0 ? 'P' : ($i == $block - 1 ? 'U' : 'M');
		// TODO: muy feo, pero anda
		$oriRTL = $from_language->rtl ? 'rtl " dir="rtl' : '';
		$traRTL = $to_language->rtl ? 'rtl " dir="rtl' : '';
		?>
		<tr valign="baseline">
			<td class="num<?= $s ?>">
			<a href="traduki.php?id=<?= $film->id ?>&block=<?= $block ?>&number=<?= $subtitles[$i]->number ?>&type=<?= $type ?>"><?= $subtitles[$i]->number ?>.</a></td>
			<td class="asep"></td>
			<td class="ori<?= $s ?><?= $oriRTL ?>"><?
				if ($i == $selected) {
				?><strong><?= $ot ?></strong><br/><?
			} else {
				$style = '';
				if (trim($subtitles[$i]->translated_text)) {
					$style = 'style="color: #959595;font-style: italic;"';
				}
				?><a <?= $style ?> href="traduki.php?id=<?= $film->id ?>&block=<?= $block ?>&number=<?= $subtitles[$i]->number ?>&type=<?= $type ?>"><?= $ot ?></a><?
			}
			?></td>
			<td class="asep"><?
			if (trim($subtitles[$i]->comments)) {
				?>
				<div class="fc1"><div class="fc2" onMouseOver="aperigiKomentojn(<?= $subtitles[$i]->number ?>)" onMouseOut="kasxiKomentojn(<?= $subtitles[$i]->number ?>)"><img src="img/comment.png"/></div>
				<div class="fc3" id="fc<?= $subtitles[$i]->number ?>" style="display:none">
				<strong><?= __('Komentoj') ?>:</strong><br/>
				<?= trim($subtitles[$i]->comments) ?>
				</div>
				</div>
				<?
			}
			?></td>
			<td class="tra<?= $s ?><?= $traRTL ?>"><?
				if (trim($tt)) {
					$style = "color: " . colorForTrust($subtitles[$i]->trust);
					if ($i == $selected) { 
						?><span style="<?= $style ?>;font-weight:bold"><?= $tt ?></span><?
					} else {
						?><a style="<?= $style ?>" href="traduki.php?id=<?= $film->id ?>&block=<?= $block ?>&number=<?= $subtitles[$i]->number ?>&type=<?= $type ?>"><?= $tt ?></a><?
					}
				} else {
					print '&nbsp;';
				}
			?></td>
			<td></td>
		</tr>
		<?
	}
	?>
</table>

<div id="mas_contexto" style="clear:left">
<img src="img/view.gif" hspace="4" border="0"/><a href="traduki.php?id=<?= $film->id ?>&block=<?= $block + 4 ?>&number=<?= $subtitles[$selected]->number ?>&type=<?= $type ?>"><?= __('Plian kunteston') ?></a>
</div>
<?
if ($belongs) {
	?>
	<form action="traduki.do.php" method="post" target="l">
	<input type="hidden" name="film_id" value="<?= $film->id ?>"/>
	<input type="hidden" name="number" value="<?= $subtitles[$selected]->number ?>"/>
	<input type="hidden" name="type" value="<?= $type ?>"/>
	<input type="hidden" name="block" value="<?= $block ?>"/>
	<div class="s"><?= __('Traduko') ?>:</div>
	<table border="0" cellpadding="0" cellspacing="0">
		<tr valign="top">
			<td>
				<textarea name="translated_text" 
					style="width:394px; height:34px; margin-top:<?= $offset ?>px"
					<?= $to_language->getInputEvents() ?>><?= $subtitles[$selected]->translated_text ?></textarea>
			</td>
			<?
			if ($subtitles[$selected]->trust != FILM_SUBTITLE_NO_TRUST and 
				$subtitles[$selected]->user_id != $film->user_id and 
				$subtitles[$selected]->user_id != $user->id and
				$user->id != $film->user_id) {
				?>
				<td width="10">&nbsp;</td>
				<td id="alertOwner">
					<?= __('Cxu vi pensas ke iu intence fusxis cxi tiun tradukon?') ?><br/>
					<a href="rimarkigi.do.php?film_id=<?= $film->id ?>&sub_id=<?= $subtitles[$selected]->id ?>" target="l"><?= __('Rimarkigu la mastron pri cxi tiu situacio') ?></a>
				</td>
				<?
			}
			?>
		</tr>
	</table>
	<?
	$other_user_subtitle = false;
	
	if ($subtitles[$selected]->user_id) {
		if ($user->id == $subtitles[$selected]->user_id) {
			?>
			<div>(<?= __('La nuna traduko estas via') ?>)</div>
			<?
		} else {
  		$other_user_subtitle = true;
  		
			$man = new UserManager();
			$other = $man->getFromKeys(array('id' => $subtitles[$selected]->user_id));
			?>
			<div>(<?= __('La nuna traduko estas de {x}', array('x' => displayUser($other))) ?>)</div>
			<?
		}
	}
	?>
	<br/>
	<?
	$the_trust = $subtitles[$selected]->trust;
	if ($the_trust == FILM_SUBTITLE_NO_TRUST) {
		$the_trust = $user->trust;
	}
	?>
	<?= __('Kiom da fido vi havas je cxi tiu traduko?') ?> &nbsp; <select name="trust">
	  <option value="<?= FILM_SUBTITLE_LOW_TRUST ?>" style="color:red" <?= $the_trust == FILM_SUBTITLE_LOW_TRUST ? 'selected' : '' ?>><?= __('Malaltan') ?></option>
	  <option value="<?= FILM_SUBTITLE_MEDIUM_TRUST ?>" style="color:orange" <?= $the_trust == FILM_SUBTITLE_MEDIUM_TRUST ? 'selected' : '' ?>><?= __('Mezan') ?></option>
	  <option value="<?= FILM_SUBTITLE_MAX_TRUST ?>" style="color:green" <?= $the_trust == FILM_SUBTITLE_MAX_TRUST ? 'selected' : '' ?>><?= __('Altan') ?></option>
	</select>
	<?
	if ($other_user_subtitle && $myPart->super_user) {
  	?>
  	<br/>
  	<?= __('Malgranda sxangxo') ?> <acronym title="<?= __('Se jes, la traduko restos de {uzanto}', array('uzanto' => $other->nickname)) ?>">?</acronym>: <input type="radio" name="small" value="yes" checked="checked"><?= __('Jes') ?> <input type="radio" name="small" value="no"><?= __('Ne') ?>
  	<?
  }
  ?>
	<br/>
	<br/>
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<div class="s"><?= __('Komentoj') ?>:</div>
				<textarea name="comments" style="width:400px; height:60px""><?= $subtitles[$selected]->comments ?></textarea>
			</td>
			<td width="20">&nbsp;</td>
			<?
			if ($subtitles[$selected]->user_id and $user->id != $subtitles[$selected]->user_id) {
				?>
				<td>
					<div class="s"><?= __('Ekspliko por {x}', array('x' => displayUser($other))) ?> (<?= __('nedeviga') ?>):</div>
					<textarea name="explanation" style="width:320px; height:60px""></textarea>
				</td>
				<?
			}
			?>
		</tr>
	</table>
	<br/>
	<div class="s"><?= __('Post klaki "Bone"') ?>:
	<select name="return">
		<?
		if ($number != $status->count()) {
			?>
			<option value="next"><?= __('Iri al la sekvanta subtitolo') ?></option>
			<?
		}
		
		if ($type) {
			?>
			<option value="random"><?= __('Iri al alia hazarda subtitolo') ?></option>
			<?
		}
		?>
		<option value="this"><?= __('Reveni al cxi tiu subtitolo') ?></option>
	</select></div>
	<br/>
	<input type="submit" value="<?= __('Bone') ?>"/>
	</form>
	<?
}
PHP::rootInclude('layout/bottom.php');
?>