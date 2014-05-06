<?
global $list;
$pages_properties = $list->getPagesProperties();
?>
<table width="100%" border="0" cellpadding="0">
	<tr>
		<td><a class="listPagesLabel">Montri&#285;as <?= $pages_properties->getFirstResult() ?>
			al <?= $pages_properties->getLastResult() ?></a>
			<a class="listCurrentPage">de <?= $pages_properties->getTotalResults() ?>
			tradukoj</a></td>
		<td align="right">
			<span class="listPagesLabel">Pa&#285;oj:</span>
			<?
			if ($pages_properties->hasPreviousPages()) {
				?>
				<a href="<?= $pages_properties->getLinkForPage(1) ?>" class="listPageLink"><<</a>
				<?
			}

			for ($i = $pages_properties->getFirstPage(); $i <= $pages_properties->getLastPage(); $i++) {
				if ($i == $pages_properties->getCurrentPage()) {
					?>
					<a class="listCurrentPage"><?= $i ?></a>
					<?
				} else {
					?>
					<a href="<?= $pages_properties->getLinkForPage($i) ?>" class="listPageLink"><?= $i ?></a>
					<?
				}
				if ($i != $pages_properties->getLastPage()) {
					print ' - ';
				}
			}

			if ($pages_properties->hasNextPages()) {
				?>
				<a href="<?= $pages_properties->getLinkForPage($pages_properties->getTotalPages()) ?>" class="listPageLink">>></a>
				<?
			}
			?>
		</td>
	</tr>
</table>