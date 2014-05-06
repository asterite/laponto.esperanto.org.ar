<?
PHP :: requireClasses('DataObjects', 'Cookie', 'Session');
PHP :: requireCustom('FilmSubtitle', 'FilmParticipant', 'FilmUserEvent', 'OnlineUsers');

define('USER_SESSION_NAME', '_user');

class User {

	var $id;
	var $nickname;
	var $password;
	var $name;
	var $block;
	var $notify_new_messages;
	var $email;
	var $notify_by_email;
	var $trust;
	var $language;
	
	function countNewEvents() {
		$db = DataObjectsManager::getDatabase();
		$conn = $db->openConnection();
		$ps = $conn->prepareStatement('SELECT COUNT(*) AS c FROM subtitolu_user_event WHERE to_user = :user AND status = :status');
		$ps->setInteger('user', $this->id);
		$ps->setString('status', 'new');
		$rs = $ps->executeQuery();
		$rs->next();		
		return $rs->getInteger('c');
	}
	
	function getEvents() {
		$man = new UserEventManager();
		$man->addWhereField('to_user', '=', $this->id);
		$man->query();
		
		return $man;
	}
	
	function getParticipationInFilm($film_id) {
		$man = new FilmParticipantManager();
		$man->addWhereField('user_id', '=', $this->id);
		$man->addWhereField('film_id', '=', $film_id);
		$man->query();
		
		return $man->next();
	}

}

class UserManager extends DataObjectsManager {

	function _getDataObjectInfo() {
		$table = new Table('subtitolu_user');
		$table->addField(new Field('id', SQL_TYPE_INTEGER, true, true));
		$table->addField(new Field('nickname', SQL_TYPE_STRING));
		$table->addField(new Field('password', SQL_TYPE_STRING));
		$table->addField(new Field('name', SQL_TYPE_STRING));
		$table->addField(new Field('block', SQL_TYPE_INTEGER));
		$table->addField(new Field('notify_new_messages', SQL_TYPE_BOOLEAN));
		$table->addField(new Field('email', SQL_TYPE_STRING));
		$table->addField(new Field('notify_by_email', SQL_TYPE_BOOLEAN));
		$table->addField(new Field('trust', SQL_TYPE_STRING));
		$table->addField(new Field('language', SQL_TYPE_STRING));
		$doi = new DataObjectInfo($table, 'User');
		return $doi;
	}
	
	function getFromNicknameAndPassword($nickname, $password) {
		$password = md5($password);
		return $this->getFromKeys(array('nickname' => $nickname, 'password' => $password));
	}

	function login($jugador, $recordar = false) {
		Session::setAttribute(USER_SESSION_NAME, $jugador);
		
		if ($recordar) {
			$cookie = new Cookie('userinfo');
			$cookie->setTimeout(60*60*24*365);
			$cookie->value = $jugador->id;
			$cookie->send();
			Session::removeAttribute('userForgetMe');
		}		
	}
	
	function getRemembered() {
		if (Session::getAttribute('userForgetMe')) {
			return false;
		}
		
		if ($user = UserManager::getLogged()) return $user;
		
		$cookie = new Cookie('userinfo');
		if ($cookie->exists() and trim($cookie->value)) {
			$man = new UserManager();
			$do = $man->getFromKeys(array('id' => $cookie->value));
			UserManager::login($do);
			return $do;
		} else {
			return false;
		}
	}

	function logoff() {
		$user = UserManager::getLogged();
		OnlineUsers::logoff($user->id);
		
		Session::removeAttribute(USER_SESSION_NAME);
		$cookie = new Cookie('userinfo');
		$cookie->setTimeout(1);
		$cookie->send();
		Session::setAttribute('userForgetMe', true);
	}

	function getLogged() {
		return Session::getAttribute(USER_SESSION_NAME);
	}

}

$user = UserManager::getLogged();
if ($user) {
	OnlineUsers::refresh($user->id);
} else {
	OnlineUsers::refresh();
}
?>