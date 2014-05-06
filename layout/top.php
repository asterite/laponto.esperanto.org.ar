<?
PHP::requireClasses('Session');
PHP::requireCustom('FilmUser', 'Language');

PHP::rootInclude('layout/top_html_page.php');
global $context_path;

$user = UserManager::getRemembered();
?>
<iframe id="l" name="l" style="display:none"></iframe>
<div id="content">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td id="subtiwiki"><nobr><a href="/">La Ponto</a></nobr></td>
		<td id="beta" nowrap="nowrap">(beta) &nbsp; 
		  <?
		  if (!$user) {
  		  ?>
  		  <select name="lingvo" 
    		  onChange="sxangxiLingvon('<?= $context_path ?>', this.value)">
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
    		</select>
    		<?
		  }
		  ?>
		 </td>
		<td id="userMenu"><?
			if (!$user) {
				$user = UserManager::getRemembered();
			}
			if ($user) {
				global $html_page;
				$html_page->appendToBodyAttribute('onUnload', 'kvazauxElsaluti();');
				?><?= __('Bonvenon') ?>, <?= $user->nickname ?> | <a href="<?= $context_path ?>/elsaluti.do.php"><?= __('Elsaluti') ?></a><?
			}
			?>
		</td>
	</tr>
</table>
