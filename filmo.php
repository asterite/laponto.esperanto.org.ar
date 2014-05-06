<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request', 'Response');
PHP::requireCustom('Film', 'FilmUser', 'Display');

$id = Request::getParameter('id');

global $film;
$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $id));

if (!$id or !$film) {
	Response::sendRedirect('index.php');
}

global $myPart;
$user = UserManager::getRemembered();
if ($user) $myPart = $user->getParticipationInFilm($film->id);
$belongs = ($user and $myPart and !$myPart->blocked);

PHP::rootInclude('layout/top.php');

$status = $film->getStatus();
?>
<div id="descripcion">
<?
include('layout/film_header.php');
?>
<div class="s"><?= __('Tradukante subtitolon, oni decidas kiom oni fidas sian tradukon') ?>:</div>
<div class="s">&nbsp; - <span style="color:green"><?= __('Alta fido') ?></span>: <?= __('pri.alta.fido') ?>.</div>
<div class="s">&nbsp; - <span style="color:orange"><?= __('Meza fido') ?></span>: <?= __('pri.meza.fido') ?>.</div>
<div class="s">&nbsp; - <span style="color:red"><?= __('Malalta fido') ?></span>: <?= __('pri.malalta.fido') ?>.</div>
<?= __('Por klare montri kiun dubon oni havas, oni povas aldoni komenton al la traduko') ?>.
</div>
<?
global $menu_left;
if ($user->id == $film->user_id) {
	$menu_left = array(
		array(__('Elsxuti la subtitolojn'), 'download/index.php?id=' . $film->id, 'img/download.gif'),
		array(__('Agordoj'), 'filmagordoj.php?id=' . $film->id, 'img/preferencias.gif'),
		array(__('Forvisxi la filmon'), 'forvisxi_filmon.php?id=' . $film->id, 'img/delete.gif')
	);
} else {
	$menu_left = array(
		array(__('Elsxuti la subtitolojn') . '...', 'download/index.php?id=' . $film->id, 'img/download.gif')
	);
}
PHP::rootInclude('layout/menu.php');	

if ($user and !$belongs) {
	PHP::rootInclude('layout/stranger.php');
}

if ($belongs) {
	?>
	<br class="s"/>
	<div id="rules">
	<div class="s"><strong><?= __('Reguloj de la tradukado de cxi tiu filmo') ?>.</strong></div>
	<?
	$rules = $film->rules ? $film->rules : '<em>' . __('Reguloj de tradukado ne estis difinitaj') . '.</em>';
	?>
	<div id="rules_text">
	<?= nl2br(trim($rules)) ?>
	<?
	if ($user->id == $film->user_id) {
		?>
		<br/><br/>
		<a href="javascript:sxangxiRegulojn()"><?= __('Sxangxi la regulojn') ?></a>
		<?
	}
	?>
	</div>
	<div id="rules_form" style="display:none">
	<form action="sxangxi_regulojn.do.php" method="post">
	<input type="hidden" name="film_id" value="<?= $film->id ?>"/>
	<div class="s"><textarea name="reguloj" cols="80" rows="8"><?= $film->rules ?></textarea></div>
	<input type="submit" value="<?= __('Sxangxi la regulojn') ?>"/>
	</form>
	</div>
	</div>
	<?
}
?>
<br/>
<div><?= __('Stato de la tradukado') ?> (<?= __('{x} entute', array('x' => $status->count())) ?>):</div>
<ul>
	<li><span style="color:green"><?= __('Alta fido') ?></span>: <?= $status->get(FILM_SUBTITLE_MAX_TRUST) ?>
		<?
		if ($belongs and $status->get(FILM_SUBTITLE_MAX_TRUST) > 0) {
			?> 
			&nbsp; <a href="traduki.php?id=<?= $film->id ?>&type=<?= FILM_SUBTITLE_MAX_TRUST ?>"><?= __('Kontroli hazardan') ?></a>
			<?
		}
		?>
	</li>
	<li><span style="color:orange"><?= __('Meza fido') ?></span>: <?= $status->get(FILM_SUBTITLE_MEDIUM_TRUST) ?>
		<?
		if ($belongs and $status->get(FILM_SUBTITLE_MEDIUM_TRUST) > 0) {
			?> 
			&nbsp; <a href="traduki.php?id=<?= $film->id ?>&type=<?= FILM_SUBTITLE_MEDIUM_TRUST ?>"><?= __('Traduki hazardan') ?></a>
			<?
		}
		?>
	</li> 
	<li><span style="color:red"><?= __('Malalta fido') ?></span>: <?= $status->get(FILM_SUBTITLE_LOW_TRUST) ?>
		<?
		if ($belongs and $status->get(FILM_SUBTITLE_LOW_TRUST) > 0) {
			?> 
			&nbsp; <a href="traduki.php?id=<?= $film->id ?>&type=<?= FILM_SUBTITLE_LOW_TRUST ?>"><?= __('Traduki hazardan') ?></a>
			<?
		}
		?>
	</li>
	<li><?= __('Ne tradukitaj') ?>: <?= $status->get(FILM_SUBTITLE_NO_TRUST) ?>
		<?
		if ($belongs and $status->get(FILM_SUBTITLE_NO_TRUST) > 0) {
			?> 
			&nbsp; <a href="traduki.php?id=<?= $film->id ?>&type=<?= FILM_SUBTITLE_NO_TRUST ?>"><?= __('Traduki hazardan') ?></a>
			<?
		}
		?>
	</li>
</ul>
<div><a href="traduki.php?id=<?= $film->id ?>&number=1"><?= __('Kontroli la tutan liston') ?></a></div>
<?
$notEmpty = $status->countNotEmpty(); 
$participants = $film->getFullParticipants('points', false);

foreach($participants as $p) {
	if ($p->user_id == $user->id) {
		$myp = $p;
		break;
	}
}
?>
<br/><br/>
<div class="s"><strong><?= __('Kunlaborantoj kaj kiom ili kunlaboris') ?>:</strong></div>
<table border="0" cellpadding="4" cellspacing="1">
	<tr style="background-color:#CECECE">
		<th width="175"><?= __('Nomo') ?></th>
		<th width="125"><?= __('Kiom li/sxi helpis') ?></th>
		<th width="100"><?= __('Tipo') ?></th>
		<?
		if ($myp->super_user) {
			?>
			<th width="175"><?= __('Konverti al') ?>...</th>
			<?
		}
		if ($user->id == $film->user_id) {
			?>
			<th width="175"><?= __('Protekto') ?></th>
			<?
		}
		?>
	</tr>
	<?
	foreach($participants as $p) {
		$style = "background-color:#F8F8F8;";
		?>
		<tr style="<?= $style ?>">
			<td><?= displayUser($p) ?></td>
			<td><?= displayPercentage($notEmpty, $p->points) ?></td>
			<td><?
				if ($p->blocked) {
					?><span style="color:red"><?= __('Blokita') ?></span><?
				} else {
					if ($p->super_user) {
						if ($p->user_id == $film->user_id) {
							print __('Mastro');
						} else {
							print __('Super uzanto');
						}
					} else if ($p->can_add) {
						print __('Invitanto');
					} else {
						print __('Normala');
					}
				}
				?></td>
			<?
			if ($myp->super_user) {
				?><td><?
					if ($p->user_id == $user->id or $p->user_id == $film->user_id or $p->blocked) {
						print '&nbsp;';
					} else { 
						if ($p->super_user) {
							?><a href="sxangxi_tipon.do.php?film_id=<?= $film->id ?>&user_id=<?= $p->user_id ?>&to=adder"><?= __('Invitanto') ?></a>
							&nbsp; <a href="sxangxi_tipon.do.php?film_id=<?= $film->id ?>&user_id=<?= $p->user_id ?>&to=normal"><?= __('Normala') ?></a><?
						} else if ($p->can_add) {
							?><a href="sxangxi_tipon.do.php?film_id=<?= $film->id ?>&user_id=<?= $p->user_id ?>&to=super_user"><?= __('Super uzanto') ?></a>
							&nbsp; <a href="sxangxi_tipon.do.php?film_id=<?= $film->id ?>&user_id=<?= $p->user_id ?>&to=normal"><?= __('Normala') ?></a><?
						} else {
							?><a href="sxangxi_tipon.do.php?film_id=<?= $film->id ?>&user_id=<?= $p->user_id ?>&to=super_user"><?= __('Super uzanto') ?></a>
							&nbsp; <a href="sxangxi_tipon.do.php?film_id=<?= $film->id ?>&user_id=<?= $p->user_id ?>&to=adder"><?= __('Invitanto') ?></a><?
						}
					}
					?></td><?
			}
			if ($user->id == $film->user_id) {
				if ($p->user_id != $film->user_id) {
  				$m = __('Cxu vi vere volas malfari la sxangxojn de {uzanto}?', array('uzanto' => $p->nickname));
					?><td><a href="javascript:malfariSxangxojn(<?= $p->user_id ?>, '<?= $m ?>', <?= $film->id ?>)" style="color:red"><?= __('Malfari sxangxojn') ?></a><?
					if ($p->blocked) {
  					$m = __('Cxu vi vere volas malbloki la uzanton {uzanto}?', array('uzanto' => $p->nickname));
						?>&nbsp; <a href="javascript:malblokiUzanton(<?= $p->user_id ?>, '<?= $m ?>', <?= $film->id ?>)" style="color:green"><?= __('Malbloki') ?></a><?
					} else {
  					$m = __('Cxu vi vere volas bloki la uzanton {uzanto}?', array('uzanto' => $p->nickname));
						?>&nbsp; <a href="javascript:blokiUzanton(<?= $p->user_id ?>, '<?= $m ?>', <?= $film->id ?>)" style="color:red"><?= __('Bloki') ?></a><?
					}
					?></td><?
				} else {
					?><td>&nbsp;</td><?
				}
			}
			?></tr><?
	}
	?>
</table>
<br/>
<strong><?= __('Kio estas la tipo de uzanto?') ?></strong>
<ul>
	<li><strong><?= __('Mastro') ?></strong>: <?= __('pri.mastro') ?></li>
	<li><strong><?= __('Super uzanto') ?></strong>: <?= __('pri.super.uzanto') ?></li>
	<li><strong><?= __('Invitanto') ?></strong>: <?= __('pri.invitanto') ?></li>
	<li><strong><?= __('Normala') ?></strong>: <?= __('pri.normala') ?></li>
</ul>
<?
PHP::rootInclude('layout/bottom.php');
?>
