<?php
//GET['name'] - название файла
//просто открываем файл в браузере

$separator = "../../../../";
$name = $_GET['name'];

if ( $name !== false ) {

    // подтянуть настроечный массив
    include "settingList.php";


    //тут проверка на существование такого файла в базе
    if ($settingList[$name]) {

        $filename = "{$separator}view/downloads/{$settingList[$name]['filename']}";

        if (file_exists($filename)) {

            header('Content-Description: File Transfer');
            //header('Content-Type: application/x-zip-compressed');
            if ($settingList[$name]['type'] == "pdf") {
                header('Content-Type: application/pdf');
            }
            elseif ($settingList[$name]['type'] == "doc") {
                header('Content-Type: application/msword');
            }
            header('Content-Disposition: attachment; filename='.basename($filename));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            ob_clean();
            flush();
            readfile($filename);
            die;

            //header('Conten-Type: application/pdf');
            //header("Location: {$filename}");
        }
        else die('Файл не найден');
    }
    else header("Location: /");
}
else header("Location: /");
