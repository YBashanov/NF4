<?php

//namespace niley4;


/**
 * Реализация синглтона
 *
 * Родительский класс для остальных классов
 *
 * <b>Методы</b>
 * - {@link _Singleton::init}
 *
 * - {@link _Singleton::setLogObject} - Обозначить объект для ведения логов
 */
class _Singleton{
    /**singleton*/
    protected static $instances = array();


    /**
     * Получить объект singleton
     * */
    public static function init(){
        $class = get_called_class();

        if (! isset(self::$instances[$class])){
//v("not {$class}");
            self::$instances[$class] = new $class();
        }

        return self::$instances[$class];
    }


    protected function __construct() { }

    protected function __wakeup() { }

    protected function __clone() { }

    protected function __set_state() { }


    //-----------------------------------------------------------------------------------------------------
    //                                      СИСТЕМА ЛОГИРОВАНИЯ
    //-----------------------------------------------------------------------------------------------------


    /**
     * Объект для ведения и записи логов
     */
    protected $logObject = null;


    /**
     * Обозначить объект для ведения логов
     */
    public function setLogObject($logObject) {
        $this->logObject = $logObject;
    }


    /**
     * Записать лог в logObject (если logObject обозначен)
     *
     * Использование:
     * $this->addLog($message, __FILE__);
     */
    protected function addLog($message, $fileTrace = "") {
        if (isset($this->logObject)) {
            $this->logObject->add($message, $fileTrace);
            $this->logObject->write();
            return true;
        }
        // не установлен logObject
        else {
            return false;
        }
    }
}