<?php
//контроль скриптов ajax, параметры которых переданы через POST
if (! isset ($separator)) {
	$separator = "../../../";
}
if (! isset ($sepToCore)) {
    $sepToCore = "../../../../../";
}

//проверка отправки форм с помощью сессий - (сервер)
session_start();

//подтверждение того, что данный файл может вызываться асинхронно (для неотображения ошибок)
$isAjax = true;

include "{$sepToCore}php/config/site/ajax.vars.php";
include "{$sepToCore}php/config/site/includesToAjax.php";
