<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request');
PHP::requireCustom('Film', 'Language', 'Display');

$lingvo = Request::getParameter('lingvo');
list($from_language, $to_language) = explode('-', $lingvo); 
$name = Request::getParameter('name');
$page = Request::getParameter('page', 1);

$rpp = 10;

$man = new FilmManager();
if ($from_language) {
	$man->addWhereField('from_language', '=', $from_language);
}
if ($to_language) {
	$man->addWhereField('to_language', '=', $to_language);
}
if ($name) {
	$man->addWhereField('name', 'LIKE', "%$name%");
}
$man->setResultsPerPage($rpp);
$man->setCurrentPage($page);
$man->query();

PHP::rootInclude('layout/top.php');
?>
<div id="descripcion">
<div class="s"><strong><?= __('Sercxrezultoj') ?></strong></div>
<?
if ($from_language or $to_language or $name) {
	?>
	<?= __('Montrigxas filmoj, kiuj') ?>
	<ul>
		<?
		if ($from_language) {
			$man2 = new LanguageManager();
			$lang = $man2->getFromKeys(array('id' => $from_language));
			?>
			<li><?= __('estas tradukataj el la {x}', array('x' => $lang->name)) ?></li>
			<?
		}
		if ($to_language) {
			$man2 = new LanguageManager();
			$lang = $man2->getFromKeys(array('id' => $to_language));
			?>
			<li><?= __('estas tradukataj al la {x}', array('x' => $lang->name)) ?></li>
			<?
		}
		if ($name) {
			?>
			<li><?= __('enhavas {x} en sia nomo', array('x' => "\"$name\"")) ?></li>
			<?
		}
		?>
	</ul>
	<?
} else {
	?>
	<?= __('Montrigxas cxiuj filmoj') ?>.
	<?
}
?>
</div>
<?
PHP::rootInclude('layout/menu.php');	
?>
<div id="resultadosBusqueda">
<?
if ($man->hasNext()) {
	?>
	<div><?= __('Montrigxas {x} al {y} de {z} rezultoj', array('x' => $man->getFirstResult(), 'y' => $man->getLastResult(), 'z' => $man->getTotalResults())) ?>:</div>
	<ul>
	<?
	while($film = $man->next()) {
		?>
		<li><?= displayFilm($film) ?></li>
		<?
	}
	?>
	</ul>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<?
			if ($page > 1) {
				?>
				<td><a href="sercxi_filmon.php?lingvo=<?= $lingvo ?>&name=<?= $name ?>&page=<?= $page-1 ?>"><< <?= __('Antauxaj rezultoj') ?></a></td>
				<?
			}
			
			if ($page < $man->getTotalPages()) {
				?>
				<td align="right"><a href="sercxi_filmon.php?lingvo=<?= $lingvo ?>&name=<?= $name ?>&page=<?= $page+1 ?>"><?= __('Sekvantaj rezultoj') ?> >></a></td>
				<?
			}
			?>
		</tr>
	</table>
	<?
} else {
	print __('Neniu filmo estis trovita') . '.';
}
?>
</div>
<?
PHP::rootInclude('layout/buscador.php');

PHP::rootInclude('layout/bottom.php');
?>
