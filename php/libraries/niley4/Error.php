<?php

namespace niley4;


/**
 * Сбор ошибок действия скриптов
 *
 * <b>Методы</b>
 * - {@link Error::add} - Добавить ошибку в список
 * - {@link Error::getErrors} - Получить лист ошибок
 * - {@link Error::clear} - Очистить лист ошибок
 * - {@link Error::write} - Запись в базу
 */
class Error extends _Singleton{

    /**лист ошибок*/
    public $errors = array();


    /**
     * Добавить ошибку в список
     */
    public function add($message, $classWhichCalled=''){
        $this->errors[] = array(
            'message'=>$message,
            'date'=>date("Y.m.d H:i:s", time()),
            'classWhichCalled'=>$classWhichCalled
        );
    }


    /**
     * Получить лист ошибок
     */
    public function getErrors(){
        return $this->errors;
    }


    /**
     * Очистить лист ошибок
     */
    public function clear(){
        $this->errors = [];
    }


    /**
     * Запись лога ошибок в базу данных
     */
    public function write($db='', $config=array()){
        if (!isset($config['prefix'])) {
            $config['prefix'] = "";
        }

        if (is_object($db)){
            if (count($this->errors) > 0) {
                $insert = array();

                for ($i = 0; $i < count($this->errors); $i++) {
                    $item = $this->errors[$i];
                    $insert[] = array(
                        'time' => $item['date'],
                        'message' => $item['message'],
                        'classWhichCalled' => $item['classWhichCalled'],
                    );
                }
                if ($db->insert("{$config['prefix']}errors", $insert)) {
                    $this->clear();
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                // $this->add('Массив ошибок пустой', get_class($this));
                return false;
            }
        }
        else {
            $this->add('Класс баз данных не подключен для занесения ошибок в базу', get_class($this));
            return false;
        }
    }
}


