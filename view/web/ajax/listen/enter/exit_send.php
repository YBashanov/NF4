<?php
$separator = "../../../";
include "{$separator}ajax/_ScriptControl.php";

$thisFile = "enter/exit_send"; //для error

if ($global['authorization']) {

	if ($dbTrue) {
		//проверяем существование данного пользователя
		$where = "`login`='{$_COOKIE['login']}' AND NOT(`deleted`)";
		$what = "`id`, `hash`, `pass`, `status`";
		$user = $db->select_line ("{$config['prefix']}users", $where, $what);

		if ($user) {

			//аккаунт уже активирован
			if ($user['status'] != 0) {
			
				//проверка, что это действительно наш аккаунт
				$hash = md5 ($_COOKIE['id'] . substr($user['hash'], 2, 10) . substr($user['pass'], 2, 16) . $_COOKIE['login']);
				if ($hash == $_COOKIE['hash']) {
					$cookie->delete_cookie('id');
					$cookie->delete_cookie('login');
					$cookie->delete_cookie('hash');
					
					echo "1|Успешно";
				}
				//выйти пытается не тот пользователь, либо кукис поддельные
				else {
					echo "2|Не вышло";
					$errorLog->add ("выйти пытается не тот пользователь, либо кукис поддельные");
				}
			}
			//аккаунт не активирован. Странно, что он пытается выйти
			else {
				echo "2|Не вышло";
				$errorLog->add ("аккаунт не активирован (status=0). Странно, что он пытается выйти");
			}
		}
		//данного пользователя не существует - куки поддельный
		else {
			echo "2|Не вышло";
			$errorLog->add ("данного пользователя не существует - куки поддельный");
		}
	}
	else {
		echo "2|Нет разрешения пользоваться базой данных. Смотри config";
	}
}
//пользователь уже не авторизован (неоткуда выходить)
else {
	echo "2|Не вышло";
	$errorLog->add ("Попытка совершить выход неавторизованным пользователем");
};
$errorLog->write();