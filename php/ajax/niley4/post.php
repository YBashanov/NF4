<?php

header('Access-Control-Allow-Origin: *');

include "_ScriptControl.php";
include "_ScriptList.php";
include "{$separator}php/config/site/includesToAjax.php";

/**
 * Формат входящего запроса (POST)
 * Изменение данных
 *
 * [
 *  script => 'games/spy' - путь до файла сценариев
 *  name => 'createTable' - действие в этом файле
 *  data => [] - данные на insert. Если есть id - это update
 * ]
 */

// PHP7
if (!isset($_POST['user_o'])) {
    $_POST['user_o'] = "";
}
if (!isset($_POST['hash_o'])) {
    $_POST['hash_o'] = "";
}



/***/
$prePOST = $_POST;
if (isset($_POST['data']) && $_POST['data']) {
    // особый ключ: нужно привести строку data к массиву до экранирования внутри $regular
    $prePOST['data'] = json_decode($_POST['data'], true);
}
else {
    $_POST['data'] = array();
}

$regular->search($prePOST);
$post = array();

if ($regular->isTrue()) {
    $post = $regular->getResult();

    if ($post['script']) {
        //загрузка файла сценария
        if (@file_exists($scriptList[$post['script']])) {

            // проверка псевдо-кукис на соответствие user_online
            $auth = new Auth();
            $auth->setDB($db);
            $auth->setTable('users_online');
            $auth->setInputArray(['user_o'=>$_POST['user_o'], 'hash_o'=>$_POST['hash_o']]);
            $auth->setTableFields([
                'user_o' => 'id',
                'hash_o' => 'hash',
            ]);
            $global['user_online'] = $auth->check();

            $auth->writeLogs($devLog);

            // получить корневой файл группы сценариев _root.php
            include $scriptList[$post['script']];
        }
    }
}
else {
    $response->setError($regular->getErrors(), 'post.php');
}

echo $response->getResponse();