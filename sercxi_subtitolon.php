<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request');
PHP::requireCustom('Film', 'Display');

$id = Request::getParameter('id');
$type = Request::getParameter('type');
$block = Request::getParameter('block');
$search = Request::getParameter('search');
$page = Request::getParameter('page', 1);
$man = new FilmManager();
$film = $man->getFromKeys(array('id' => $id));

if (!$id or !$film) {
	?>
	
	<?
}

$rpp = 10;

$man = $film->getSubtitles();
$man->addWhereField('original_text', 'LIKE', "%{$search}%");
$man->addWhereField('trust', '<>', FILM_SUBTITLE_NO_TRUST);
$man->addOrder('number');
$man->setResultsPerPage($rpp);
$man->setCurrentPage($page);
$man->query();

PHP::rootInclude('layout/top_html_page.php');
?>
<div id="popupContent">
<div><strong><?= __('Sercxrezultoj') ?>:</strong> "<?= $search ?>"</div><br/>
<?
if ($man->getTotalResults() > 0) {
	?>
	<div><?= __('Montrigxas {x} al {y} de {z} rezultoj', array('x' => $man->getFirstResult(), 'y' => $man->getLastResult(), 'z' => $man->getTotalResults())) ?>:</div><br/>
	<?
	while($sub = $man->next()) {
		$style = "color: " . colorForTrust($sub->trust);
		?>
		<div class="s"><a href="javascript:goToSubtitle(<?= $sub->number ?>)"><?= $sub->number?>.</a></div>
		<div class="searchResultOriginal"><?= $sub->original_text ?></div>
		<div class="searchResultTranslated" style="<?= $style ?>"><?= $sub->translated_text ?></div>
		<br/>
		<?
	}
	?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<?
			if ($page > 1) {
				?>
				<td><a href="sercxi_subtitolon.php?id=<?= $id ?>&type=<?= $type ?>&search=<?= $search ?>&number=<?= $number ?>&block=<?= $block ?>&page=<?= $page-1 ?>"><< <?= __('Antauxaj rezultoj') ?></a></td>
				<?
			}
			
			if ($page < $man->getTotalPages()) {
				?>
				<td align="right"><a href="sercxi_subtitolon.php?id=<?= $id ?>&type=<?= $type ?>&search=<?= $search ?>&number=<?= $number ?>&block=<?= $block ?>&page=<?= $page+1 ?>"><?= __('Sekvantaj rezultoj') ?> >></a></td>
				<?
			}
			?>
		</tr>
	</table>
	<?
} else {
	print __('Rezultoj ne estis trovitaj') . '.';
}
?>
</div>


<?
PHP::rootInclude('layout/bottom_html_page.php');
?>