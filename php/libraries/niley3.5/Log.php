<?php

//namespace niley4;


/**
 * Сбор записей
 *
 * <b>Методы</b>
 * - {@link Log::add} - Добавить запись в список
 * - {@link Log::getAll} - Получить все записи
 * - {@link Log::clear}
 * - {@link Log::write} - Запись в указанный файл
 */
class Log {
    /** лист записей */
    private $logs = array();
    private $filePath = "";


    /**
     * $filePath - сохраним заранее путь к файлу, куда запишем лог
     */
    public function __construct($filePath){
        $this->filePath = $filePath;
		
		//date_default_timezone_set('UTC'); 
    }


    public function getLogPath() {
        return $this->filePath;
    }


    /**
     * Добавить запись в список
     * $message - сообщение о событии
     * $fileTrace - имя файла или путь до него, в котором произошло событие
     */
    public function add($message, $fileTrace = ""){
        if ($fileTrace == "") {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $fileTrace = $trace[0]['file'];
        }
		
        $this->logs[] = array(
            'message'=>$message,
            'file'=>$fileTrace,
            'date'=>date("Y.m.d H:i:s", time())
        );
    }


    /**
     * Получить все записи
     */
    public function getAll(){
        return $this->logs;
    }


    /**
     * Очистить лист
     */
    public function clear(){
        $this->logs = [];
    }


    /**
     * Запись в указанный файл
     */
    public function write(){
        $return = false;

        $path = $this->filePath;

        
		//if (! file_exists("mytest112.php")) {
		//	echo 'not file';
		//	v(__FILE__);
		//	
		//	touch("mytest112.php");
		//}
		
        if (! file_exists($path)) {
            if (touch($path)) {}
            // не удалось создать файл логов
            else {
                echo "Log::write не удалось создать файл логов {$path}<br />";
            }
        }

        if (file_exists($path)) {
            $count = count($this->logs);
            if ($count > 0) {
                $f = fopen($path, 'a');

                if ($f) {
                    for ($i = 0; $i < $count; $i++) {
                        $text = $this->logs[$i]["date"] . " ";
                        $text .= $this->logs[$i]["file"] . " ";
                        $text .= $this->logs[$i]["message"];

                        if (fwrite($f, $text . PHP_EOL)) {
                            $return = true;
                        }
                        // ошибка при записи в файл логов
                        else {
                            echo "Log::write ошибка записи в файл {$path}<br />";
                        }
                    }

                    fclose($f);
                    $this->clear();
                }
                // не удалось открыть файл для дозаписи
                else {
                    echo "Log::write не удалось открыть файл {$path} для дозаписи<br />";
                }
            }
        }
        return $return;
    }
}


