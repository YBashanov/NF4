<?php
namespace niley4;
/*
Пример использования

$resp = new Response(); //создание экземпляра, а не статическое обращение (чтобы не было перетирания при
                        //одновременном обращении к объекту
$resp->clearResponse(); //чистим от предыдущих запросов

$resp->setSuccess(true); //при очистке - false, поэтому надо success задать явно (в случае норм.ответа)
$resp->setData($table->attributes); //сюда массив объектов или объект
$resp->setAction("auth"); //Действие клиента в случае ошибки - авторизоваться снова

$resp->setError("текст", __CLASS__); //если ошибка - пишем ее

return $resp->getResponse(); // самое последнее действие - возвращаем клиенту ответ
*/





/**
 * Формирует ответ определенного формата (ответ сервера клиенту)
 *
 * <b>Методы</b>
 * - {@link Response::clearResponse} - Очистить массив ответа
 * - {@link Response::setSuccess} - Установить успешность/неуспешность формирования ответа
 * - {@link Response::setData} - Записать в ответ данные
 * - {@link Response::setAction} - Действие клиента в случае ошибки (серверная action-команда)
 * - {@link Response::setError} - Добавить ошибку в массив ошибок ответа
 * - {@link Response::getResponse} - Получить сформированный json
 *
 * <b>Формат ответа</b>
 * - success - true/false
 * - data - Array
 * - error - Array
 * - errorToDb - Array
 * - place - Array
 */
class Response extends _Singleton {
    /**
     * Массив ответа серверу
     */
    private $response = [
        //успешный-неуспешный ответ
        "success"=>false,
        //данные
        "data"=>[],
        //действие при неуспешном ответе сервера, серверная action-команда
        "actions"=>[],

        "error"=>[],
        "place"=>[]
    ];


    /**
     * Очистить массив ответа
     */
    public function clearResponse(){
        $this->response['success'] = false;
        $this->response['data'] = [];
        $this->response['actions'] = [];
        $this->response['error'] = [];
        $this->response['place'] = [];
    }


    /**
     * Установить успешность/неуспешность формирования ответа
     */
    public function setSuccess($bool = true){
        $this->response['success'] = $bool;
    }


    /**
     * Записать в ответ данные
     */
    public function setData($data){
        // нужно сделать добавление ключей в массив, а не перезапись массива
        if (empty($this->response['data'])) {
            $this->response['data'] = $data;
        }
        else {
            $this->response['data'] = array_merge($this->response['data'], $data);
        }
    }


    /**
     * Записать действие для клиента (серверная action-команда)
     */
    public function setAction($actionName){
        array_push($this->response['actions'], $actionName);
    }


    /**
     * Добавить ошибку в массив ошибок ответа
     *
     * <b>Параметры</b>
     * - errorToUser - (массив) текст ошибки для вывода пользователю
     * - place - __CLASS__ - место где произошла ошибка
     * - errorToDb - текст ошибки для записи в базу
     *
     * <b>Пример</b>
     *
     * $response->setError($error->getErrors(), $post['script'] . '/' . $post['name']);
     */
    public function setError($errorToUser, $place = ""){
        array_push($this->response['error'], $errorToUser);
        array_push($this->response['place'], $place);
    }


    /**
     * Добавить сразу несколько ошибок сюда
     */
    public function setErrors($errors, $keyMessage='', $keyPlace='') {
        for ($i=0; $i<count($errors); $i++) {
            if ($errors[$i][$keyMessage]) {
                $place = "";
                if ($errors[$i][$keyPlace]) {
                    $place = $errors[$i][$keyPlace];
                };

                $this->setError($errors[$i][$keyMessage], $place);
            }
        }
    }


    /**
     * Получить сформированный json
     */
    public function getResponse(){
        return json_encode($this->response);
    }
}
