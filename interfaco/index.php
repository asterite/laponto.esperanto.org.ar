<?
require_once('../_p/classes/PHP.php');
PHP::requireCustom('Language');

$man = new LanguageManager();
$man->addOrder('name', true);
$man->query();
?>
<h2>Traduki la interfacon de La Ponto</h2>
<div><strong>Elektu lingvon:</strong></div>
<br/>
<table border="1">
<?
while($lang = $man->next()) {
  $ini = $lang->getIni();
  $count = 0;
  foreach($ini as $key => $value) {
    if (trim($ini[$key])) $count++;
  }
  $p = round(100 * $count / sizeof($ini), 2);
  ?>
  <tr><td width="100"><a href="lingvo.php?id=<?= $lang->id ?>"><?= $lang->name ?></a></td><td align="right">%<?= $p ?></td></tr>
  <?
}
?>
</table>