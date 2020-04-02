<?php
// 2020-02-26
// 2020-03-25 - добавление mode=sqlite3


//namespace niley4;

/**
 * Сбор ошибок действия скриптов
 *
 * <b>Методы</b>
 * - {@link Database::setLogObject} - Установить объект для ведения логов
 * - {@link Database::connect}      - Установить соединение с базой
 *
 * - {@link Database::close_connect}- Закрытие соединения
 * - {@link Database::query}        - Выполнить строковый запрос
 * - {@link Database::query_select} - Получить данные после строкового запроса
 * - {@link Database::select}       - Запрос в базу
 * - {@link Database::select_line}  - Получить одну линию
 * - {@link Database::select_column}- Получить один столбец
 * - {@link Database::insert}       - Классическая вставка новой строки
 * - {@link Database::insertSimple} - Упрощенная вставка новой строки
 * - {@link Database::id}           - Получить последний id
 * - {@link Database::update}       - Обновить данные

 */
class Database extends _Singleton {
	
	
	//-----------------------------------------------------------------------------------------------------
    //                                      	РЕЖИМ РАБОТЫ
    //-----------------------------------------------------------------------------------------------------
	
	/**
	 * mode = mysqli, стандартный режим работы
	 * mode = sqlite, режим через sqlite
	 */
	private $mode = "mysqli";
	public function setMode($mode = "mysqli") {
		$this->mode = $mode;
		
		if ($this->mode == "sqlite") {
			//SQLite3::enableExceptions(true);
		}
	}
	public function getMode() {
        return $this->mode;
    }
	
	
    //-----------------------------------------------------------------------------------------------------
    //                                      СОЕДИНЕНИЕ И ЛОГИРОВАНИЕ
    //-----------------------------------------------------------------------------------------------------


    /**
     * Система логирования
     */
    private $logFlag = '';
    protected function addLog($message, $operator='', $query='') {
        if (isset($this->logObject)) {
            if ($operator) {
                $operator = $operator . ": ";
            }

            // запись сообщения в лог
            $this->logObject->add($operator . $message);

            // не каждый запрос записываем. Подрубаем умное логирование
            if ($query && $this->logFlag == 'all') {
                // запись запроса в лог
                $this->logObject->add("query: " . $query);
            }

            $this->logObject->write();
            return true;
        }
        else {
            echo "Ошибка: Не установлен logObject!<br />";
            return false;
        }
    }


    /**
     * Установить объект для ведения логов
     * $logFlag
     *  = ''
     *  = 'all' - все логи (в том числе текст запросов)
     */
    public function setLogObject($logObject, $logFlag = '') {
        $this->logObject = $logObject;
        $this->logFlag = $logFlag;
    }
    public function getLogObject() {
        return $this->logObject;
    }


    /**
     * Система соединения с базой
     */
    private $link = null;


    /**
     * Установить соединение с базой
     * $config = array(
     *    'host'
     *    'user'
     *    'pass'
     *    'db' (для sqlite только db)
     *    'character_set' => ''
     *    'prefix' => ''
     * );
     */
    public function connect($config){
		if (!isset($this->logObject)) {
            echo "Предупреждение: Не установлен logObject! Используй метод db->setLogObject(logObject)<br />";
        }
		
		if ($this->mode == "mysqli") {
			return $this->connectMysqli($config);
		}
		elseif ($this->mode == "sqlite") {
			return $this->connectSqlite($config);
		}
	}
	
	
	/**
	 * connect для mysqli
	 */
	private function connectMysqli($config) {
        if (! isset($config['character_set'])) {
            $config['character_set'] = "utf8";
        }

        $isValid = true;
        if (! isset($config['host'])) {
            $this->addLog("connect: Отсутствует параметр host");
            $isValid = false;
        }
        if (! isset($config['user'])) {
            $this->addLog("connect: Отсутствует параметр user");
            $isValid = false;
        }
        if (! isset($config['pass'])) {
            $this->addLog("connect: Отсутствует параметр pass");
            $isValid = false;
        }
        if (! isset($config['db'])) {
            $this->addLog("connect: Отсутствует параметр db");
            $isValid = false;
        }

        if ($isValid) {
            $link = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
            if (!$link) {
                $this->addLog("connect: Невозможно подключиться к серверу БД");
                return null;
            }

            if (@mysqli_select_db($link, $config['db'])) {}
            else {
                $this->addLog("connect: Невозможно открыть БД {$config['db']}. Ошибка: " . mysqli_error($link));
                return null;
            }

            if (@mysqli_query($link, "SET CHARACTER SET '{$config['character_set']}'")) {}
            else {
                $this->addLog("connect: Невозможно перевести кодировку. Ошибка: " . mysqli_error($link)
                    . ", кодировка - " . $config['character_set']);
                return null;
            }

            if (@mysqli_query($link, "SET NAMES '{$config['character_set']}'")) {}
            else {
                echo "Connect: Невозможно установить имена кодировки.";
                $this->addLog("Connect: Невозможно установить имена кодировки. Ошибка: " . mysqli_error($link)
                    . ", кодировка - " . $config['character_set']);
                return null;
            }

            $this->link = $link;

            return $link;
        }
        else {
            $this->addLog("connect: Подключение отменено из-за отсутствия параметров");
        }
        return null;
    }

	
	/**
	 * connect для sqlite
	 */
	private function connectSqlite($config) {
        $isValid = true;
		if (! isset($config['db'])) {
            $this->addLog("connect: Отсутствует параметр db, необходимо указать путь до файла");
            $isValid = false;
        }

        if ($isValid) {
			try {
				$link = new SQLite3($config['db']);
			}
			catch (Exception $e) {
				$this->addLog($e->getMessage(), "connect");
				return null;
			}
            if (!$link) {
                $this->addLog("connect: Невозможно создать/подключиться к sqlite БД");
                return null;
            }
			$link->enableExceptions(true);
			
            $this->link = $link;

            return $link;
        }
        else {
            $this->addLog("connect: Подключение отменено из-за отсутствия параметров");
        }
        return null;
	}
	
	
    /**
     * Закрытие соединения
     */
    public function close_connect(){
		if ($this->mode == "mysqli") {
			return $this->close_connectMysqli();
		}
		elseif ($this->mode == "sqlite") {
			return $this->close_connectSqlite();
		}
	}
	
	
	/**
	 * 
	 */
	private function close_connectMysqli() {
        if (isset($this->link)){
            if ( @mysqli_close ($this->link)) {
                return true;
            }
            else {
                $this->addLog("close_connect: Невозможно отключиться от сервера. Ошибка: ".mysqli_error($this->link));
                return false;
            }
        }
        else {
            $this->addLog("close_connect: link не является ресурсом");
            return false;
        }
    }
	
	
	/**
	 * 
	 */
	private function close_connectSqlite() {
		if (isset($this->link)){
            $return = $this->link->close();
			
			if ($return) {
				return true;
			}
			else {
                $this->addLog("close_connect: Невозможно отключиться от сервера");
                return false;
            }
        }
        else {
            $this->addLog("close_connect: link не является ресурсом");
            return false;
        }
	}
	
	
    /**
     * Уничтожение объекта
     */
    public function __destruct(){
        $this->close_connect();
    }


    //-----------------------------------------------------------------------------------------------------
    //                                          ВЫПОЛНЕНИЕ ЗАПРОСОВ
    //-----------------------------------------------------------------------------------------------------


    /**
     * Все запросы выполняются здесь
     * (удаленный) $operator - select, update - для ведения логов, использовался для замера времени
     */
    public function query ($query, $operator = '') {
		if ($this->mode == "mysqli") {
			return $this->queryMysqli($query, $operator);
		}
		elseif ($this->mode == "sqlite") {
			return $this->querySqlite($query, $operator);
		}
	}
	
	
	private function queryMysqli($query, $operator) {
		if (!isset($link)) {
            $link = $this->link;
        }
		
		$result = @mysqli_query($link, $query); //resource, true, false
		$error = @mysqli_error($link);

        if ($error) {
            $this->addLog($error, $operator, $query);
        }

		return $result;
	}
	
	
	private function querySqlite($query, $operator) {
		if (!isset($link)) {
            $link = $this->link;
        }

        if (isset($link)) {
			try {
				$result = $link->query($query);
				return $result;
			}
			catch (Exception $e) {
				$this->addLog($e->getMessage(), $operator, $query);
				return null;
			}
        }
        return null;
	}


    /**
     * Получить адекватные данные после запроса SELECT
     *
     * (через ручной запрос db->queqy)
     *
     * <b>Параметры</b>
     * - $queryResult - данные, полученные через {@link Database::query}
     * - [$key] - ключ или массив ключей (уникальный столбец для отображения полной информации)
     * - [$mysql_num] - default=false, ассоциативный массив или числовой
     *      Если не указан, будет простая выборка (одной строки), как при select_line
     */
    public function query_select($queryResult, $key = "", $mysql_num = false){
		if ($this->mode == "mysqli") {
			return $this->query_selectMysqli($queryResult, $key, $mysql_num);
		}
		elseif ($this->mode == "sqlite") {
			return $this->query_selectSqlite($queryResult, $key, $mysql_num);
		}
	}
	
	
	private function query_selectMysqli($queryResult, $key, $mysql_num) {
        $result = [];

        if ($key) {
            if ($mysql_num == false) {
                while ($row = @mysqli_fetch_assoc($queryResult)) {
                    if (is_array($key)) {
                        $r = '';
                        foreach ($key as $v) {
                            $r .= $row[$v];
                        }
                    }
                    else {
                        $r = $row[$key];
                    }
                    $result[$r] = $row;
                }
                return $result;
            }
            else {
                $r = -1;
                while ($row = @mysqli_fetch_assoc($queryResult)) {
                    if (is_array($key)){
                        foreach ($key as $v){
                            $r++;
                        }
                    }
                    else {
                        $r++;
                    }
                    $result[$r] = $row;
                }
                return $result;
            }
        }
        else {
            return @mysqli_fetch_assoc($queryResult);
        }
    }

	
	private function query_selectSqlite($queryResult, $key, $mysql_num) {
		$result = [];

        if ($queryResult) {
            if ($key) {
                if ($mysql_num == false) {
                    while ($row = $queryResult->fetchArray(SQLITE3_ASSOC)) {
                        if (is_array($key)) {
                            $r = '';
                            foreach ($key as $v) {
                                $r .= $row[$v];
                            }
                        } else {
                            $r = $row[$key];
                        }
                        $result[$r] = $row;
                    }
                    return $result;
                }
                else {
                    $r = -1;
                    while ($row = $queryResult->fetchArray(SQLITE3_NUM)) {
                        if (is_array($key)) {
                            foreach ($key as $v) {
                                $r++;
                            }
                        } else {
                            $r++;
                        }
                        $result[$r] = $row;
                    }
                    return $result;
                }
            }
            else {
                return $queryResult->fetchArray();
            }
        }
        return null;
	}

	
    //------------------------------------------------------------------------------------------------------
    //                                               ОПЕРАТОРЫ
    //------------------------------------------------------------------------------------------------------


    /**
     * возвращает полный двумерный массив таблицы, первой мерой которого является выбранный столбец $key
     * (как правило, выбирается уникальный столбец для отображения более полной таблицы данных)
     *
     * $mysql_num - если надо вернуть числовой массив (числа в 1м уровне массива)
     */
	public function select($table, $where = 'true', $what = '*', $key = 'id', $mysql_num = false) {
		if ($this->mode == "mysqli") {
			return $this->selectMysqli($table, $where, $what, $key, $mysql_num);
		}
		elseif ($this->mode == "sqlite") {
			return $this->selectSqlite($table, $where, $what, $key, $mysql_num);
		}
	}
	
	
	private function selectMysqli($table, $where, $what, $key, $mysql_num) {
		$query = "SELECT {$what} FROM {$table} WHERE {$where}";
		$result = [];

		$res = $this->query($query, 'select');
		if ($res) {
			if ($mysql_num == false) {
				while ( $row = @mysqli_fetch_assoc($res) ) {
					if (is_array($key)){
						$r = '';
						foreach ($key as $v){
							$r .= $row[$v];
						}
					}
					else {
						$r = $row[$key];
					}
					$result[$r] = $row;
				}
			}
			else {
				$r = -1;
				while ( $row = @mysqli_fetch_assoc($res) ) {
					if(is_array($key)){
						foreach ($key as $v){
							$r++;
						}
					}
					else {
						$r++;
					}
					$result[$r] = $row;
				}
			}
		}
		return $result;
	}

	
	private function selectSqlite($table, $where, $what, $key, $mysql_num) {
		$query = "SELECT {$what} FROM {$table}";
		if ($where !== 'true') {
			$query .= " WHERE {$where}";
		}
		
		$result = [];

		$res = $this->query($query, 'select');
		if ($res) {
			if ($mysql_num == false) {
				while ( $row = $res->fetchArray(SQLITE3_ASSOC) ) {
					if (is_array($key)){
						$r = '';
						foreach ($key as $v){
							$r .= $row[$v];
						}
					}
					else {
						//try { 
							$r = $row[$key];
						//}
						//catch (e){
						//	v();
						//}
					}
					$result[$r] = $row;
				}
			}
			else {
				$r = -1;
				while ( $row = $res->fetchArray(SQLITE3_NUM) ) {
					if(is_array($key)){
						foreach ($key as $v){
							$r++;
						}
					}
					else {
						$r++;
					}
					$result[$r] = $row;
				}
			}
            return $result;
		}
		return null;
	}
	

    /**
     * выбрать только одну строку, первое в списке совпадений
     * ИЛИ
     * уникальную строку в зависимости от $where
     */
	public function select_line($table, $where = 'true', $what = '*'){
		if ($this->mode == "mysqli") {
			return $this->select_lineMysqli($table, $where, $what);
		}
		elseif ($this->mode == "sqlite") {
			return $this->select_lineSqlite($table, $where, $what);
		}
	}
		
		
	private function select_lineMysqli ($table, $where, $what) {
		$query = "SELECT {$what} FROM {$table} WHERE {$where}";

		$res = $this->query($query, 'select_line');

		return @mysqli_fetch_assoc($res);
	}
	
	
	private function select_lineSqlite ($table, $where, $what) {
		$query = "SELECT {$what} FROM {$table}";
		if ($where !== 'true') {
			$query .= " WHERE {$where}";
		}

		$res = $this->query($query, 'select_line');

		if ($res) {
			return $res->fetchArray(SQLITE3_ASSOC);
		}
		return null;
	}


    /**
     * возвращает числовой массив со значениями всего столбца
     * (удобно для поиска конкретной информации в известном столбце)
     * $q_array - название столбца
     */
	public function select_column($table, $where = 'true', $what='*', $q_array='id'){
		if ($this->mode == "mysqli") {
			return $this->select_columnMysqli($table, $where, $what, $q_array);
		}
		elseif ($this->mode == "sqlite") {
			return $this->select_columnSqlite($table, $where, $what, $q_array);
		}
	}
	
	
	private function select_columnMysqli ($table, $where, $what, $q_array) {
		$query = "SELECT {$what} FROM {$table} WHERE {$where}";
	
        $ret_arr = [];

		$res = $this->query($query, 'select_column');

		if ( is_array($q_array) ) {
			$j=0;//номер строки в ТБД
			while ( $row = mysqli_fetch_assoc($res) ) {
				for ( $i=0; $i<count($q_array); $i++ ) {
					$ret_arr[$q_array[$i]][$j] = $row[$q_array[$i]];
				}
				$j++;
			}
		}
		else {
			$j=0;//номер строки в ТБД
			while ( $row = mysqli_fetch_assoc($res) ) {
					$ret_arr[$j] = $row[$q_array];
				$j++;
			}
		}
		return $ret_arr;
	}

	
	private function select_columnSqlite ($table, $where, $what, $q_array) {
		$query = "SELECT {$what} FROM {$table}";
		if ($where !== 'true') {
			$query .= " WHERE {$where}";
		}
	
        $ret_arr = [];

		$res = $this->query($query, 'select_column');
        if ($res) {
            if ( is_array($q_array) ) {
                $j=0;//номер строки в ТБД
                while ( $row = $res->fetchArray(SQLITE3_ASSOC) ) {
                    for ( $i=0; $i<count($q_array); $i++ ) {
                        $ret_arr[$q_array[$i]][$j] = $row[$q_array[$i]];
                    }
                    $j++;
                }
            }
            else {
                $j=0;//номер строки в ТБД
                while ( $row = $res->fetchArray(SQLITE3_ASSOC) ) {
                    $ret_arr[$j] = $row[$q_array];
                    $j++;
                }
            }
            return $ret_arr;
        }
        return null;
	}


    /**
     * $data - ассоц.массив(поля-значения). Если сложная вставка - data - числовой массив ассоц.массивов
     * $tail - окончание запроса, в случае сложных запросов
     */
	public function insert($table, $data, $tail=''){
		if ($this->mode == "mysqli") {
			return $this->insertMysqli($table, $data, $tail);
		}
		elseif ($this->mode == "sqlite") {
			return $this->insertSqlite($table, $data, $tail);
		}
	}	
	
	
	private function insertMysqli ($table, $data, $tail) {
		$data = $this->arrayToArrForInsert($data);
		$query = "INSERT INTO {$table} ({$data['fields']}) VALUES {$data['values']} " . $tail;

		if (!$this->query($query, 'insert')){
			return false;
		}
		return true;
	}
	
	
	private function insertSqlite ($table, $data, $tail) {
		$data = $this->arrayToArrForInsert($data);
		$query = "INSERT INTO {$table} ({$data['fields']}) VALUES {$data['values']} " . $tail;

		if (!$this->query($query, 'insert')){
			return false;
		}
		return true;
	}


    /**
     * простая вставка
     * fields - строка. Столбцы через запятую - `name`, `price`, `cost`
     * values - строка. Строки через запятую, в скобках! -
     *
     * Сложная вставка - values - (баннеры, 40, 34), (баннеры, 40, 34), (баннеры, 40, 34)
     */
	public function insertSimple($table, $fields, $values){
		if ($this->mode == "sqlite") {
			$this->addLog("Для данного режима - метод не поддерживается", "insertSimple");
			return false;
		}
	
		$query = "INSERT INTO {$table} ({$fields}) VALUES {$values}";

		if (!$this->query($query, 'insertSimple')){
			return false;
		}
		return true;
	}


    /**
     * Получение последней созданной id
     */
	public function id(){
		if ($this->mode == "sqlite") {
			$this->addLog("Для данного режима - метод не поддерживается", "id()");
			return null;
		}
		
		
		$link = $this->link;
		$last_id = mysqli_insert_id($link);
		return $last_id;
	}


    /**
     * обновление данных в базе
     */
	public function update($table, $data, $where){
		if ($this->mode == "mysqli") {
			return $this->updateMysqli($table, $data, $where);
		}
		elseif ($this->mode == "sqlite") {
			return $this->updateSqlite($table, $data, $where);
		}
	}	
	
	
	private function updateMysqli($table, $data, $where) {
		$data = $this->arrayToStrForUpdate($data);
		if ($data === ''){
			return false;
		}
		$query = "UPDATE {$table} SET {$data} WHERE {$where}";

		if (! $this->query($query, 'update')){
			return false;
		}
		return true;
	}
	
	
	private function updateSqlite($table, $data, $where) {
		$data = $this->arrayToStrForUpdate($data);
		if ($data === ''){
			return false;
		}
		$query = "UPDATE {$table} SET {$data} WHERE {$where}";

		if (! $this->query($query, 'update')){
			return false;
		}
		return true;
	}
	


    /**
     * преобразование ассоц.массива в массив строк - для insert
     */
    private function arrayToArrForInsert($array){
        $result = array('fields'=>'', 'values'=>'');
        $fieldNeed = true;
        $needBrekes = false;
        foreach ($array as $id=>$row){
            if (is_array($row)){
                $result['values'] .= '(';
                foreach ($row as $key=>$value){
                    if ($fieldNeed){
                        $key = addslashes($key);
                        $result['fields'] .= "`{$key}`,";
                    }
                    // тут надо экранировать кавычки - исправлено 27.04.11
                    $value = addslashes($value);

                    // $value = addslashes((string)$value);
                    $result['values'] .= "'{$value}',";
                }
                // ошибка исправлена - 27.04.11
                // было -2, стало -1 ->уменьшение строки запроса для insert было слишком большим!
                // Это - для больших Insert'ов (в несколько циклов)
                $result['values'] = $result['values']!==''?substr($result['values'], 0, -1):$result['values'];
                $result['values'] .= '),';

                if ($result['values'] === '(),'){
                    return false;
                }
            }
            else {
                $id = addslashes($id);
                $row = addslashes($row);
                $result['fields'] .= "`{$id}`,";
                $result['values'] .= "'{$row}',";
                $needBrekes = true;
            }
            $fieldNeed = false;
        }
        if ($result['fields'] === '' || $result['values'] === ''){
            return false;
        }
        $result['fields'] = substr($result['fields'], 0, -1);
        $result['values'] = substr($result['values'], 0, -1);
        if ($needBrekes){
            $result['values'] = "({$result['values']})";
        }
        return $result;
    }


    /**
     * функция для update
     */
	private function arrayToStrForUpdate($array, $exc = array()){
		$result = '';
		foreach ($array as $key=>$value){
			if (is_array($value) || is_array($key)){
				return false;
			}
			if (!in_array($key, $exc)){
				$result .= "`{$key}`='{$value}', ";
			}
		}
		$result = $result!==''?substr($result, 0, -2):$result;
		return $result;
	}
}


