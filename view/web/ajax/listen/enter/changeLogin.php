<?php
$separator = "../../../";
include "{$separator}ajax/_ScriptControl.php";

$thisFile = "enter/changeLogin"; //для error

if (! $global['authorization']) {

	if ($dbTrue) {
		$regular->search($_POST, array());
		if ($regular->isTrue()) {
            $regResult = $regular->getResult();

			//проверяем логин на уникальность
			$where = "`login`='{$regResult['login']}' AND NOT(`deleted`)";
			$what = "`id`, `status`, `time_create`";
			$usersLogin = $db->select_line ("{$config['prefix']}users", $where, $what);

			if ($usersLogin) {
				if ($usersLogin['status'] == 0) {
					if ( ($usersLogin['time_create'] + $ajaxVars['timeToActivate']) > $time) {
						echo "2|Используется|";
						
						//$errorLog->add ("Данный логин используется, но не активирован");
					}
					//если активация данного аккаунта не использована,
					else {
						echo "1||";
						//необходимо удалить аккаунт
						$data = array ("deleted"=>1);
						$where = "`id`='{$usersLogin['id']}'";
						$db->update ("{$config['prefix']}users", $data, $where);
						
						//$errorLog->add ("Удаление неактивного аккаунта - данный логин набирал другой пользователь");
					}
				}
				else {
					echo "2|Используется|";
					//$errorLog->add ("Данный логин используется (активирован)");
				}
			}
			else echo "1|Пусто|";
		}
		//на самом деле - неверные символы не пропустила regular
		else {
			echo "2|Неверные символы|";
			$errorLog->add ("Введение некорректных символов минуя javascript");
		}
	}
	else {
		echo "2|Нет разрешения пользоваться базой данных. Смотри config";
	}
}
else {
	echo "9|";
	$errorLog->add ("Попытка подбора логина авторизованным аккаунтом");
}

$errorLog->write();