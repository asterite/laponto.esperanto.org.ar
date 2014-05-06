<?
require_once('_p/classes/PHP.php');
PHP::requireClasses('Request');
PHP::requireCustom('FilmUser', 'Language');

$operacion = Request::getParameter('operacion');
$nickname = Request::getParameter('nickname');
$password = Request::getParameter('password');
$password2 = Request::getParameter('password2');
$recordame = Request::getBoolean('recordame');

if ($operacion == 'login') {
	$man = new UserManager();
	if ($do = $man->getFromNicknameAndPassword($nickname, $password)) {
		UserManager::login($do, $recordame);
		?>
		<script>
    parent.window.location = "hejmo.php";
		</script>
		<?
	} else {
		?>
		<script>
    alert("<?= __('Kromnomo aux pasvorto nevalidaj') ?>");
		</script>
		<?
	}
} else {
	$error = false;
	if (!trim($nickname)) {
		$error = __('Vi devas elekti kromnomon') . '.';
	} else if (!trim($password)) {
		$error = __('Vi devas elekti pasvorton') . '.';
	} else if ($password != $password2) {
		$error = __('La pasvortoj ne estas la samaj') . '.';
	} else {
		$man = new UserManager();
		$other = $man->getFromKeys(array('nickname' => $nickname));
		
		if ($other) {
			$error = __('Jam ekzistas alia uzanto kun tiu kromono') . '. ' . __('Bonvolu elekti alian') . '.';
		} else {
			$do = new User();
			$do->nickname = $nickname;
			$do->password = md5($password);
			$do->language = LanguageManager::getSessionLanguage();
			$do->block = 5;
			
			$man->insert($do);
			
			UserManager::login($do, $recordame);
			?>
			<script>
      window.location = "hejmo.php";
		  </script>
			<?
		}
	}
	
	if ($error) {
		?>
		<script>
    alert("<?= $error ?>");
		</script>
		<?
	}
}
?>
