<?php
namespace niley4;


/**
 * Общение между серверами
 *
 * <b>Методы</b>
 * - {@link Curl::get} - Отправка get-запроса
 * - {@link Curl::post} - Отправка post-запроса
 */
class Curl extends _Singleton{
    /**
     * Отправка get-запроса
     *
     * $url - строка запроса
     *
     * Возвращает html
     */
    public function get($url) {
        $resource = curl_init();

        curl_setopt_array($resource, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 60,
        ));
        $html = curl_exec($resource);
        curl_close($resource);

        return $html;
    }


    /**
     * Отправка post-запроса
     *
     * $url - строка запроса
     * $postArray - ассоц.массив
     * $headers - числовой массив, обычное перечисление заголовков (хотя по умолчанию уже стоит application/x-www-form-urlencoded)
     * $getResponseHeaders - вставить ли заголовки в ответ. Default=false
     *
     * Возвращает $html ИЛИ [headers=>[], body=>""]
     */
    public function post($url, $postArray = array(), $headers = array(), $getResponseHeaders = false) {
        $resource = curl_init();

        curl_setopt_array($resource, array(
            // TRUE для использования обычного HTTP POST.
            // Данный метод POST использует обычный application/x-www-form-urlencoded, обычно используемый в HTML-формах.
            // поэтому заголовки не указываем
            CURLOPT_POST => true,
            CURLOPT_URL => $url, // URL текущего запроса к сервису
            CURLOPT_HEADER => $getResponseHeaders, // указать сервису включить заголовки в ответ
            CURLOPT_HTTPHEADER => $headers, // массив заголовков для отправки запроса
            CURLOPT_RETURNTRANSFER => true, // возврат будет в curl_exec вместо прямого вывода в браузер
            CURLOPT_POSTFIELDS => $postArray, // тело
            CURLOPT_SSL_VERIFYPEER => false, // остановить проверку сертификата
            CURLOPT_TIMEOUT => 60, // максимальное время для выполнения curl-функций
        ));

        $response = curl_exec($resource);

        if ($getResponseHeaders) {
            $headerSize = curl_getinfo($resource, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize); // отделяем заголовки ответа
            $body = substr($response, $headerSize); // сохраняем непосредственно тело ответа

            curl_close($resource);
            return array(
                "headers" => $headers,
                "body" => $body,
            );
        }
        else {
            curl_close($resource);
            return $response;
        }
    }
}


