<?php//namespace niley4;/** * */class AuthorizationPersonal extends _Singleton{    /**     * Проверка авторизации в админке     */    public function checkAuth() {        global $global, $db, $config;        //есть ли авторизация в АДМИНКЕ        //необходимо изменить этот файл - сделать проверку cookies        $global['authorization'] = false;        $global['user'] = null;        //v($_COOKIE);        //точно нет авторизации        if ( !isset ($_COOKIE['hash']) || $_COOKIE['hash'] == false ) {            $global['authorization'] = false;        }        //может и есть        else {            $where = "NOT(`deleted`) AND `login`='{$_COOKIE['login']}' AND `id`='{$_COOKIE['id']}'";            $where .= " ORDER BY `id` DESC";            $globaluser = $db->select_line ("{$config['prefix']}users", $where, '*');            $hash = md5 ($globaluser['id'] . substr($globaluser['hash'], 2, 10) . substr($globaluser['pass'], 2, 16) . $globaluser['login']);            if (! $globaluser) {                //нет авторизации                $global['authorization'] = false;            }            else {                if ($_COOKIE['hash'] == $hash) {                    //есть авторизация                    $global['user'] = $globaluser;                    $global['authorization'] = true;                }                else {                    //нет авторизации                    $global['authorization'] = false;                }            }        }    }}