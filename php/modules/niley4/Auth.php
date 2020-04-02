<?php


/**
 * Модуль
 *
 * Проверяет входные куки на regular, и сравнивает их с полями таблицы
 *
 * <b>Методы</b>
 * - {@link Auth::setDB} - Указать ссылку на базу данных
 * - {@link Auth::setTable} - Указать название таблицы
 * - {@link Auth::setInputArray} - Указать ссылку на массив
 * - {@link Auth::setTableFields} - ..какое поле массива соответствует какому полю таблицы
 * - {@link Auth::check} - Запустить проверку
 * - {@link Auth::getErrors} - Получить все ошибки
 * - {@link Auth::writeLogs} - Записать ошибки в лог
 *
 * Пример использования
 *     $auth = new Auth();
 *     $auth->setDB($db);
 *     $auth->setTable('users');
 *     $auth->setInputArray($_COOKIE);
 *     $auth->setTableFields([
 *         'id' => 'id',
 *         'hash' => 'hash',
 *     ]);
 *     $global['user'] = $auth->check();
 *
 *
 *
 *     $auth = new Auth();
 *     $auth->setDB($db);
 *     $auth->setTable('users_online');
 *     $auth->setInputArray($_COOKIE);
 *     $auth->setTableFields([
 *         'user_id' => 'id',
 *         'hash' => 'hash',
 *     ]);
 *     $global['user'] = $auth->check();
 */
class Auth {
    /**
     * массив возможных ошибок
     */
    private $errors = array();
    /**
     * ссылка на БД
     */
    private $db;
    /**
     * таблица БД, в которой хранятся исходные данные
     */
    private $table;
    /**
     * ссылка на входной массив, откуда будут браться данные для проверки
     */
    private $inputArray;
    /**
     * массив соответствий
     */
    private $matchArray;


    /**
     * Указать ссылку на базу данных
     */
    public function setDB($db) {
        $this->db = $db;
    }


    /**
     * Указать название таблицы, в которой хранятся исходные данные
     */
    public function setTable($table) {
        $this->table = $table;
    }


    /**
     * Указать ссылку на массив (например, на $_COOKIE), откуда будут браться данные для проверки
     */
    public function setInputArray($array){
        $this->inputArray = $array;
    }


    /**
     * Указать соответствия:
     * какое поле массива соответствует какому полю таблицы
     *
     * <b>Формат</b>
     * [
     *  - (ключ вход.массива => ключ поля таблицы)
     *  - keyArray1 => tableField1,
     *  - keyArray2 => tableField2...
     * ]
     */
    public function setTableFields($matchArray){
        $this->matchArray = $matchArray;
    }


    /**
     * Запустить проверку.
     *
     * Метод фиксирует возможные ошибки и вызывает метод расчета
     *
     * <b>Возвращает</b>
     * запрос из базы по установленным параметрам (например, пользователя), или null
     */
    public function check(){
        if ($this->db) {
            if ($this->table) {
                if ($this->inputArray) {
                    if ($this->matchArray) {
                        return $this->goCheck();
                    }
                    else {
                        $this->setError("Не указаны соответствия");
                    }
                }
                else {
                    $this->setError("Не указан входной массив");
                }
            }
            else {
                $this->setError("Не указана таблица БД");
            }
        }
        else {
            $this->setError("Не указана БД");
        }
        return null;
    }


    /**
     * Логика проверки, запрос в базу
     * Возвращает пользователя из базы
     */
    private function goCheck(){
        $where = "NOT(`deleted`)";
        $matchArray = $this->matchArray;
        $inputArray = $this->inputArray;

        //собираем where
        foreach ($matchArray as $key=>$val) {
            if (isset($inputArray[$key])) {
                $where .= " AND `{$val}`='$inputArray[$key]'";
            }
        }

        //надо делать вызов по полям, указанным в machArraay
        $result = $this->db->select_line($this->table, $where, '*');

        if ($result) {
            return $result;
        }
        else {
            return null;
        }
    }


    /**
     * Записать ошибку
     */
    private function setError($message){
        $this->errors[] = [
            'message' => $message
        ];
    }


    /**
     * Получить все ошибки
     */
    public function getErrors(){
        return $this->errors;
    }


    /**
     * Записать ошибки в лог
     */
    public function writeLogs($logObject = null) {
        if (isset($logObject)) {
            $errors = $this->getErrors();

            for ($i = 0; $i < count($errors); $i++) {
                $logObject->add($errors[$i]['message']);
            }
            $logObject->write();
        }

    }
}


