<?php

namespace niley4;


/**
 * Сбор ошибок действия скриптов
 *
 * <b>Методы</b>
 * - {@link File::read} - Чтение файла по частям
 * - {@link File::scanDir} - Получить список файлов (директорий), находящихся в директории
 */
class File extends _Singleton {


    public function create($path) {
        if (file_exists($path)) {
            // $this->addLog("Данный файл уже создан: {$path}", __FILE__);
            return false;
        }

        touch($path);
        return true;
    }


    /**
     * Чтение файла по частям (альтернатива file_get_contents)
     */
    public function read($path) {
        if (! file_exists($path)) {
            $this->addLog("Данного файла не существует: {$path}", __FILE__);
            return null;
        }

        $lines = [];
        $handle = fopen($path, "r");

        while(!feof($handle)) {
            $lines[] = trim(fgets($handle));
        }

        fclose($handle);
        return $lines;
    }


    /**
     * Запись в файл
     *
     * $path - путь к файлу
     * $a_text - текстовоая строка или массив (в случае построчной записи в файл)
     *      Если пустая, то при дозаписи не переносится на новую строку
     * $flag
     *      '' - построчная запись в файл
     *      'ADD_TO_END' - дозапись в конец файла
     *      'ADD_TO_START' - дозапись в конец файла
     */
    public function write($path, $a_text = '', $flag = ''){
        $f = null;

        // построчная запись в конец файла
        if ($flag == '') {
            $f = fopen($path, 'a');

            for ($i=0; $i<count($a_text); $i++) {
                fwrite($f, $a_text[$i] . PHP_EOL);
            }

            fclose($f);
        }
        // дозапись в конец файла
        elseif ($flag == 'ADD_TO_END') {
            $f = fopen($path, 'a');
            $toWriteText = $a_text ? $a_text . PHP_EOL : $a_text;
            fwrite($f, $toWriteText);
            fclose($f);
        }
        elseif ($flag == 'ADD_TO_START') {
            $f = fopen($path, 'w');
            $toWriteText = $a_text ? $a_text . PHP_EOL : $a_text;
            fwrite($f, $toWriteText);
            fclose($f);
        }
        else {
            $this->addLog("Указан неверный flag", __FILE__);
        }
    }


    /**
     * Чтение содержимого файла через file_get_contents
     */
    public function file_get_contents($path) {
        if (! file_exists($path)) {
            $this->addLog("Данного файла не существует: {$path}", __FILE__);
            return null;
        }

        return @file_get_contents($path);
    }


    /**
     * Получить список файлов, находящихся в директории
     * $filesDirs = 'all', показывать ли только файлы
     *      'all'  - выводить всё (default)
     *      'files'- только файлы
     *      'dirs' - только каталоги
     * $sort - сортировка, 0 (default) или 1, по умолчанию - вывод файлов в алфавитном порядке
     */
    public function scanDir($dirPath, $filesDirs = 'all', $sort = 0) {
        if (! file_exists($dirPath)) {
            $this->addLog("Данной директории не существует: {$dirPath}", __FILE__);
            return null;
        }

        $list = scandir($dirPath);

        // если директории не существует
        if (!$list) return false;

        // удаляем . и .. (я думаю редко кто использует)
        if ($sort == 0) {
            unset($list[0], $list[1]);
        }
        else {
            unset($list[count($list)-1], $list[count($list)-1]);
        }

        if ($filesDirs == 'all') {}
        elseif ($filesDirs == 'files') {
            foreach ($list as $key=>$val) {
                $index = strpos($val, ".");

                if (! $index) {
                    unset($list[$key]);
                }
            }
        }
        elseif ($filesDirs == 'dirs') {
            foreach ($list as $key=>$val) {
                $index = strpos($val, ".");

                if ($index) {
                    unset($list[$key]);
                }
            }
        }

        // удаляем два первых пробела прямой сортировкой
        sort($list);

        return $list;
    }
}


