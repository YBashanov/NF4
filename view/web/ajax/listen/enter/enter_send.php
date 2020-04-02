<?php
$separator = "../../../";
include "{$separator}ajax/_ScriptControl.php";

$thisFile = "enter/enter_send"; //для error


if (! $global['authorization']) {
	if ($dbTrue) {
		$regular->search($_POST);

        if ($regular->isTrue()) {
            $regResult = $regular->getResult();

			//проверяем пару логин-пароль - сначала логин! (т.к. он уникальный)
			$where = "`login`='{$regResult['login']}' AND NOT(`deleted`)";
			//$where = "`login`='{$regResult['login']}' AND `deleted`=NULL";
			//$where = "`login`='{$regResult['login']}'";
			//$where = "true";
			$what = "`id`, `hash`, `pass`";
			//$what = "*";
			$usersLogin = $db->select_line ("{$config['prefix']}users", $where, $what);
//v($usersLogin);
			if ($usersLogin) {
				//$pass_md5
				$hash = $usersLogin['hash'];
				include "{$separator}ajax/listen/enter/key_pass.php";

				//запрос еще раз
				$where = "`login`='{$regResult['login']}' AND `pass`='{$pass_md5}' AND NOT(`deleted`)";
				$what = "`id`, `status`, `login`, `hash`, `pass`, `time_create`, `time_update`";
				$user = $db->select_line ("{$config['prefix']}users", $where, $what);

				if ($user) {
					//аккаунт уже активирован
					if ($user['status'] != 0) {

						//активация аккаунта - дополнительные кукис!
						$hash = md5 ($user['id'] . substr($user['hash'], 2, 10) . substr($user['pass'], 2, 16) . $user['login']);
						$cookie->set_cookie('id', $user['id']);
						$cookie->set_cookie('login', $user['login']);
						$cookie->set_cookie('hash', $hash);
						
						echo "1|Успешно";
						
						//дополнительные действия с базой
						//1. подтверждение входа
						$data = array (
							"time_update"=>$time,
						);
						$where = "`id`='{$user['id']}' AND NOT(`deleted`)";
						$db->update ("{$config['prefix']}users", $data, $where);
					}
					//аккаунт еще не активирован
					else {
						//проверим, вышло ли время активации
						if ( ($user['time_create'] + $var['timeToActivate']) > $time) {
							echo "2|Учетная запись не активирована! Инструкция по<Br>
								<nobr>активации выслана на e-mail, указанный при регистрации</nobr>";

							$errorLog->add ("Попытка входа по неактивированному аккаунту");
						}
						else {
							echo "2|Время активации вышло! Зарегистрируйтесь снова";
							//необходимо удалить аккаунт
							$data = array ("deleted"=>1);
							$where = "`id`='{$user['id']}'";
							$db->update ("{$config['prefix']}users", $data, $where);

							$errorLog->add ("Активация аккаунта вышла, пользователь опоздал");
						}
					}
				}
				//если неверно - записываем очередность в Кукис
				else {
					echo "2|Неверная пара логин - пароль";
					$errorLog->add ("Логин подошел, пароль не подошел");
				}
			}
			else {
				echo "2|Неверная пара логин - пароль.";
				$errorLog->add ("Неверный логин");
			}
		}
		//на самом деле - неверные символы не пропустила regular
		else {
			echo "2|Попробуйте снова|";
			$errorLog->add ("Введение некорректных символов минуя javascript");
		}
	}
	else {
		echo "2|Нет разрешения пользоваться базой данных. Смотри config";
	}
}
else {
	echo "9|";
	$errorLog->add ("Попытка входа авторизованным аккаунтом");
}
$errorLog->write();