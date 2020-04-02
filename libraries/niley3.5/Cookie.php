<?php
//namespace niley4;


/**
 * Class Cookie
 * @package niley4
 */
class Cookie extends _Singleton {
    private $domain = null;
    private $path = "/";


    //смена значения конкретной триггерной сессионной переменной
    //если такой переменной еще нет - создаем и присваиваем значение
    //$timeLive - если не определена, устанавливается до закрытия браузера
    public function set_cookie($name, $var, $timeLive = false, $rewrite = false){
        if ($rewrite == false) {
            if ($this->get_cookie($name)){
                return false;
            }
            else {
                $this->_set_cookie($name, $var, $timeLive);
                return true;
            }
        }
        else {
            $this->_set_cookie($name, $var, $timeLive);
            return true;
        }
    }


    //вспомогательный метод
    private function _set_cookie($name, $var, $timeLive){
        if ($timeLive == false) {
            @setcookie($name, $var, 0, $this->path, $this->domain);
        }
        else {
            $timeLive = time() + $timeLive;
            @setcookie($name, $var, $timeLive, $this->path, $this->domain);
        }
    }


    //возвращает значение конкретной триггерной сессионной переменной
    public function get_cookie($name = false){
        if ($name == false) {
            return $_COOKIE;
        }
        else {
            if ( ! isset ($_COOKIE[$name]) ) {
                return null;
            }
            else return $_COOKIE[$name];
        }
    }


    //удаляет существующую кукис
    public function delete_cookie($name){
        if ( isset ($_COOKIE[$name]) ) {
            $time = time() - 1036800;
            @setcookie($name, '', $time, $this->path, $this->domain);
            return true;
        }
        else return false;
    }
}
