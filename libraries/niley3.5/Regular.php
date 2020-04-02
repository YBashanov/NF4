<?php
//namespace niley4;

/**
Класс, проверяющий переменные с использованием регулярных выражений
1.12.11 - доработан. Возвращает string вместо true
5.06.12 - mail (для удобства)
5.06.12 - добавлена проверка string на NULL (уменьшает пропускающую способность класса)
12.02.13 - экранирование символов
10.09.12 - floats ()
2013-04 - изменения благодаря Кад - проверка на важные слова (link, script...)
2013-05-28 - переход на версию 2.41
2013-06-03 - errorIn (ошибки внутри класса), show()
2013-06-20 - доработки
2014-01-24 - функция mail - везде есть точки
2014-10-02 - функция set, с возможностью просматривать какие именно ошибки фильтрации были допущены!
2016-07-19 - Переименовал класс в Reg. Удален лишний код. Остались только наиболее важные паттерны
    Нет зависимости от класса Error
2017-07-16 - javadoc, переименован в Regular
2018-10-09 - search расширена типом integer (для рекурсивного вызова)
*/


/**
 * Обновленные регулярные выражения
 *
 * <b>Методы</b>
 * - {@link Regular::setPattern} - Создать новый паттерн или переопределить существующий
 * - {@link Regular::search} - Автоматически устанавливает проверку на каждый элемент массива POST.
 * - {@link Regular::isTrue} - Получить результат проверки (true/false)
 * - {@link Regular::getErrors} - Получить ошибки проверки
 * - {@link Regular::getResult} - Получить проверяемый массив, в котором неудавшиеся проверки заполнены false
 */
class Regular extends _Singleton{

    /***/
    private $seterror = array();
    /***/
    private $seterrorKeys = array();
    /**переписанный массив*/
    private $setresult = array();
    /**результат проверки*/
    private $sendTrue = true;


    /**паттерны*/
	private $patterns = [
		"num" => "/[0-9]+/",
		"float" => "/[0-9.,]+/",
		"string" => "/[\wА-Яабвгдеёжзийклмнопрстуфхцчшщъыьэюя\-$]+/",
		"login" => "/[\w-\.\/]+/",
		"mail" => "/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{1,6})+$/",
		"url" => "/[\w\s-_.?\/:=&#\"]+/",
		"ext" => ""
	];


	/**
     * Создать новый паттерн или переопределить существующий
     *
     * <b>Параметры</b>
     * - name - ключ к новому паттерну
     * - pattern - сам паттерн
     *
     * <b>Возвращает</b>
     * результат операции - true/false
	 */
	public function setPattern($name, $pattern){
		if (isset($name) && isset($pattern)){
			$this->patterns[$name] = $pattern;
			return true;
		}
		else return false;
	}


    /**
     * Автоматически устанавливает проверку на каждый элемент массива POST.
     * Записывает также результаты в private vars
     *
     * <b>Параметры</b>
     * - array - массив элементов (может быть POST)
     * - [a_regName] - массив-список ключей паттернов для привязки конкретного шаблона к каждому элементу проверяемого массива.
     * Может быть числовым/ассоциативным. Вложенность - совпадает с вложенностью проверяемого массива. Default - все элементы ext,
     * массив числовой (при a_type = 'ASSOC' массив ассоциативный)
     * - [a_type] - тип массива-списка ключей паттернов. Default='NUM' (a_regName числовой), при='ASSOC' a_regName ассоциативный
     * - [level] - уровень вложенности проверяемого массива
     *
     * Варианты a_regName:
     * - num - только цифры
     * - float - цифры, точка, запятая
     * - string - любой алфавитно-цифровой символ, включая подчеркивание + рус
     * - login - любой алфавитно-цифровой символ, включая подчеркивание и точка
     * - mail - формат email
     * - url - формат url
     * - ext - только экранирование
     * - style/script - особая метка паттерна, получающаяся, если в тексте есть слова style/script/link/meta
     *
     * <b>Возвращает</b>
     * измененный массив/false
     */
	public function search($array, $a_regName = array(), $a_type = 'NUM', $level = 0){
        if (gettype($a_regName) == 'string'){
            $a_regName = false;
        }

		if ($array) {
            $error = array();
            $errorKeys = "";
            $result = array();

            $i = 0;

            if (gettype($array) == "array") {
                if ($level === 0) {
                    $this->sendTrue = true;
                }

                foreach ($array as $key => $val) {
                    $valPreg = "";
                    $a_regName_val = "ext";
                    if ($a_type == 'NUM') {
                        if (isset($a_regName[$i]) && $a_regName[$i]) {
                            $a_regName_val = $a_regName[$i];
                        }
                    }
                    elseif ($a_type == 'ASSOC') {
                        if ($a_regName[$key]) {
                            $a_regName_val = $a_regName[$key];
                        }
                    }

                    $type = gettype($val);
                    if ($type == 'string' || $type == 'integer' || $type == 'double') {

                        $valPreg = $this->_preg_match($this->patterns[$a_regName_val], $val);

                        if ($valPreg === false) {
                            $error[$i] = array(
                                "key" => $key,
                                "patternName" => $a_regName_val,
                                "patternVal" => $this->patterns[$a_regName_val],
                                "val" => $val
                            );
                            $errorKeys .= $key . ", ";
                            $this->sendTrue = false;
                        }
                        else if (
                            strpos($valPreg, "style") !== false ||
                            strpos($valPreg, "script") !== false ||
                            strpos($valPreg, "link") !== false ||
                            strpos($valPreg, "meta") !== false
                        ) {
                            $error[$i] = array(
                                "key" => $key,
                                "patternName" => 'style/script',
                                "patternVal" => '',
                                "val" => $val
                            );
                            $errorKeys .= $key . ", ";
                            $this->sendTrue = false;
                        }

                        $result[$key] = $valPreg;
                        $i++;
                    }
                    else if ($type == 'boolean') {
                        if ($val) {
                            $result[$key] = true;
                        }
                        else {
                            $result[$key] = false;
                        }
                    }
                    else if ($type == 'array') {
                        $newLevel = $level + 1;
                        $result[$key] = $this->search($val, $a_regName_val, $a_type, $newLevel);
                    }
                }

                $errorKeys = substr($errorKeys, 0, strlen($errorKeys) - 2);
                $this->seterrorKeys[$level] = $errorKeys;
                $this->seterror[$level] = $error;

                if ($level === 0) {
                    $this->setresult = $result;
                }

                return $result;
            }
            else {
                $error[$i] = array(
                    "key" => '',
                    "patternName" => '',
                    "patternVal" => '',
                    "val" => 'проверяемый аргумент не является массивом'
                );
                $this->seterror[] = $error;
                $this->sendTrue = false;
                return false;
            }
		}
		else {
            if (count($array) == 0) {
                $this->sendTrue = true;
                return [];
            }
            else {
                $this->sendTrue = false;
                return false;
            }
		}
	}


    /**
     * получить результат проверки (true/false)
     */
    public function isTrue(){
        return $this->sendTrue;
    }


    /**
     * Получить ошибки проверки
     *
     * <b>Параметры</b>
     * - [keys] (default=false) - флаг. Если true, возвращает только ключи
     *
     * <b>Возвращает</b>
     * массив элементов вида:
     *
     * [
     * - key - ключ элемента проверяемого массива
     * - patternName - ключ паттерна (default=ext)
     * - patternVal - сам паттерн
     * - val - значение элемента проверяемого массива
     *
     * ]
     */
	public function getErrors($keys = false){
		if ($keys == false) return $this->seterror;
		else return $this->seterrorKeys;
	}


    /**
     * Получить проверяемый массив, в котором неудавшиеся проверки заполнены false
     */
	public function getResult(){
		return $this->setresult;
	}


    /**
     * запускает проверку соответствия содержимого в проверяемом массиве паттерну
     *
     * <b>Параметры</b>
     * - pattern - проверочный паттерн
     * - string - значение элемента проверяемого массива
     *
     * <b>Возвращает</b>
     * экранированную строку ИЛИ false
     */
	private function _preg_match($pattern, $string){
		// если это только экранирование (нет паттерна)
		if ($pattern === "") {
			return $this->ext($string);
		}
		else {
			if ($string === NULL)
				return false;

			if ($pattern === NULL)
				return false;

			preg_match($pattern, $string, $preg_array);

			if ($string === @$preg_array[0] && $string !== false) {
				$string = $this->ext($string);
				return $string;
			}
            else {
				return false;
			}
		}
	}


    /**
     * Добавляет экранирование
     *
     * <b>Возвращает</b> экранированную строку
     */
	private function ext($string){
		if ($string || $string === 0 || $string === "0") {
            $string = addslashes (trim ($string));
        }
		else {
            $string = "";
        }
		return $string;
	}
}
