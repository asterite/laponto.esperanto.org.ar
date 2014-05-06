<?
require_once('_p/classes/PHP.php');
PHP::rootInclude('layout/top_html_page.php');
?>
<div id="popupContent">
<div id="message">
<?= __('Vi havas nova(j)n mesagxo(j)n!') ?><br/><br/>
<strong><a href="javascript:iriAlMesagxoj();"><?= __('Mi volas vidi ilin') ?></a></strong><br/><br/>
<a href="javascript:self.close()"><?= __('Mi estas tro okupita tradukante, ne gxenu min') ?></a><br/><br/>
<span style="font-size:11px">(<?= __('Cxi tiu fenestro aperas cxar vi tion elektis en viaj preferoj') ?>)</span>
</div>
</div>

<?
PHP::rootInclude('layout/bottom_html_page.php');
?>