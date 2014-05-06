<?
PHP::requireClasses('Cache');

define('USER_IDLE_TIME', 60*3);
define('USER_EXPIRATION_TIME', 60*15);

define('USER_OFFLINE', 0);
define('USER_IDLE', 1);
define('USER_ONLINE', 2);

global $_online_users_info;

class OnlineUsers {
	
	function getOnlineStatus($user_id) {
		global $_online_users_info;
		
		$now = time();
		foreach($_online_users_info as $info) {
			if ($info->user_id == $user_id) {
				if ($now - $info->time < USER_IDLE_TIME) {
					return USER_ONLINE;
				} else {
					return USER_IDLE;
				}
			}
		}
		return USER_OFFLINE;
	}
	
	function logoff($user_id) {
		$cache = new Cache('ONLINE_USERS');
		if ($cache->found()) {
			$oldies = $cache->contents();
		} else {
			$oldies = array();
		}
		
		$retain = array();
		foreach($oldies as $old) {
			if ($old->user_id != $user_id) {
				array_push($retain, $old);
			}
		}		
		$cache->put($retain);
		
		global $_online_users_info;
		$_online_users_info = $retain;
	}
	
	function refresh($user_id = false) {
		$cache = new Cache('ONLINE_USERS');
		if ($cache->found()) {
			$oldies = $cache->contents();
		} else {
			$oldies = array();
		}

		$retain = array();
		$now = time();
		foreach($oldies as $old) {
			if ($now - $old->time < USER_EXPIRATION_TIME && $old->user_id != $user_id) {
				array_push($retain, $old);
			}
		}
		
		if ($user_id) {
			array_push($retain, new OnlineUserInfo($user_id, $now));
		}
		
		$cache->put($retain);
		
		global $_online_users_info;
		$_online_users_info = $retain;		
	}
	
}

class OnlineUserInfo {
	
	var $user_id;
	var $time;
	
	function OnlineUserInfo($user_id, $time) {
		$this->user_id = $user_id;
		$this->time = $time;
	}
	
}
?>
