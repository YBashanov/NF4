<?php
$separator = "../../../";
include "{$separator}ajax/_ScriptControl.php";

$thisFile = "enter/register_send"; //для error


if (!$global['authorization']) {
    if ($dbTrue) {

        $regular->search($_POST);
        if ($regular->isTrue()) {
            $regResult = $regular->getResult();

            //проверяем логин на уникальность
            //$where = "`login`='{$regResult['login']}' AND NOT(`deleted`)";
            $where = "`login`='{$regResult['login']}'";
            $what = "`id`, `status`, `time_create`";
            $usersLogin = $db->select_line("{$config['prefix']}users", $where, $what);


            //удаление до сих пор не активированного аккаунта
            if ($usersLogin) {
                if ($usersLogin['status'] == 0 && ($usersLogin['time_create'] + $ajaxVars['timeToActivate']) > $time) {}
                //если активация данного аккаунта не использована
                elseif ($usersLogin['status'] == 0 && ($usersLogin['time_create'] + $ajaxVars['timeToActivate']) < $time) {
                    //необходимо удалить аккаунт
                    $data = array("deleted" => 1);
                    $where = "`id`='{$usersLogin['id']}'";
                    $db->update("{$config['prefix']}users", $data, $where);

                    $usersLogin = false;

                    $errorLog->add("Удаление неактивного аккаунта - данный логин захотел другой пользователь");
                }
            }

            if (!$usersLogin) {

                //проверяем mail уникальность
                //$where = "`mail`='{$regResult['mail']}' AND NOT(`deleted`)";
                $where = "`mail`='{$regResult['mail']}'";
                $what = "`id`";
                $usersMail = $db->select_line("{$config['prefix']}users", $where, $what);

                if (!$usersMail) {

                    //проверяем, была ли регистрация данного пользователя (сравниваем с кукис)
                    //сначала запрашиваем из таблицы users
                    //$table = "{$config['prefix']}users_online AS uo";
                    //$table .= " LEFT JOIN {$config['prefix']}users AS u ON uo.`id`=u.`u_online_id`";
                    //$where = "uo.`id`='{$global['user_online']['id']}'";
                    //$where .= " AND NOT(u.`deleted`)";
                    //$what = "
					//		uo.`id` AS uo_id,
					//		uo.`time_create`,
					//		u.`id` AS u_id
					//	";
                    //$usersOnlineId = $db->select_line($table, $where, $what);
$usersOnlineId['u_id'] = null;
                    //если нет такой строчки - все ОК, данный users_online_id чист
                    if (
                        $usersOnlineId['u_id'] == NULL ||
                        $usersOnlineId['u_id'] && ($usersOnlineId['time_create'] + $var['nextRegisterSec']) < $time
                    ) {

                        $hash = $random->gen20_num();
                        //$pass_md5
                        include "{$separator}ajax/listen/enter/key_pass.php";

                        $data = array(
                            'status' => 0,
                            'hash' => $hash,
                            'login' => $regResult['login'],
                            'pass' => $pass_md5,
                            'mail' => $regResult['mail'],
                            'time_create' => $time
                        );

                        if ($db->insert("{$config['prefix']}users", $data)) {
                            if ($global['server'] == "internet") {
                                // отправка на mail
                                include "{$separator}ajax/listen/enter/key_activate.php";
                                include "{$separator}../../php/libraries/niley4/SendMailSmtp_v1.1.php";
                                include "{$separator}../../php/data/_mail/toClient_registerSend.php";

                                $mailSMTP = new SendMailSmtp('andrey.nilz@yandex.ru', 'clg58slzdgazx', 'ssl://smtp.yandex.ru', 465, "utf-8");
                                $mailResult = $mailSMTP->send(
                                    $regResult['mail'],
                                    $mailText['theme'],
                                    $mailText['mess'],
                                    array($mailText['from_info'], $mailText['from_mail'])
                                );

                                if ($mailResult) {
                                    include "{$separator}templates/personal/html/enter/t_registration_Ok.php";
                                    echo "1|{$in}|";
                                }
                                else {
                                    echo "3|Ошибка сервера!<br>Регистрация прошла, но письмо не отправлено|";
                                    $errorLog->add("Регистрация успешно, но письмо не отправлено");
                                }
                            }
                            // локальная обработка - без отправки email
                            else {
                                include "{$separator}templates/personal/html/enter/t_registration_Ok.php";
                                $in .= " Ссылка: {$base_url}activate/?code={$pass_md5}&log={$regResult['login']}";
                                echo "1|{$in}|";
                            }
                        }
                        else {
                            echo "3|Ошибка сервера!<br>Попробуйте снова|";
                            $errorLog->add("insert: Не удалось добавить строку в таблицу");
                        }
                    }
                    else {
                        $nextRegister = ($usersOnlineId['time_create'] + $var['nextRegisterSec']) - $time;
                        $nextRegister = round($nextRegister / 3600);
                        echo "3|Вы уже регистрировались.<br>
								Повторная регистрация возможна только через {$nextRegister} час(ов)<br>
								Также Вы можете попробовать с другого браузера|";
                        $errorLog->add("Повторная регистрация с того же компьютера");
                    }
                }
                else {
                    echo "2||||Используется|||";
                    $errorLog->add("Данный E-mail используется");
                }
            }
            else {
                echo "2|Используется||||||";
                $errorLog->add("Данный логин используется");
            }
        } //на самом деле - неверные символы не пропустила regular
        else {
            echo "3|Попробуйте снова|";
            $errorLog->add("Введение некорректных символов минуя javascript");
        }

    }
    else {
        echo "3|Нет разрешения пользоваться базой данных. Смотри config";
    }
}
else {
    echo "9|";
    $errorLog->add("Попытка регистрации авторизованным аккаунтом");
}
$errorLog->write();
