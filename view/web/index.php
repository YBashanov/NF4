<?php
if (!isset($separator)) {
    $separator = './';
}

// это модуль именно бэка, т.е. не тот, что отдаёт web-морду
include "{$separator}php/config/site/includes.php";

include "{$separator}php/config/site/header.vars.php";

include "{$path_tpl}data/_include_db.php";
include "{$path_tpl}data/_include_languages.php";
include "{$path_tpl}data/_include_templates.php";
include "{$path_tpl}data/_content_management.php";

//echo __FILE__;
if (function_exists("template")) {
    echo template($data, $separator);
}
