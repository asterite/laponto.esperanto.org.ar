<?
PHP::requireClasses('HTMLPage');

global $html_page, $context_path;
$html_page->setTitle('[Arauquén] Panel de Control');
$html_page->addCSS("$context_path/_admin/css/abm.css");
$html_page->addCSS("$context_path/_admin/css/list.css");
$html_page->addCSS("$context_path/_admin/css/edit.css");
$html_page->addCSS("$context_path/_admin/css/multilingual.css");
$html_page->addJS("$context_path/_admin/js/scripts.js");
$html_page->setBodyAttribute('background', "$context_path/_admin/images/bkg.gif");
$html_page->begin();
?>

<div id="tooltip" style="position:absolute;visibility:hidden"></div>

<table valign="top" class="abmBackgroundColor" width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td colspan="3" height="17"><img src="<?= $context_path ?>/_admin/images/blank.gif"  width="1" height="17"></td>
	</tr>
	<tr>
		<td align="center" width="20"><img src="<?= $context_path ?>/_admin/images/blank.gif"  width="20" height="1"></td>
		<td valign="top">