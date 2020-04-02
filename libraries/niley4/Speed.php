<?php
namespace niley4;

/**
 * Тестирование скорости участков кода
 *
 * <b>Методы</b>
 * - {@link Speed::start} - обнуление показателей
 * - {@link Speed::point} - точка, где можно вывести значение показателей
 * - {@link Speed::results} - отобразить результаты
 *
 *
 * Подключение
 * include "{$separator}php/libraries/niley4/Speed.php";
 */
class Speed extends _Singleton{

    private $startTime = 0;
    private $points = [];

    /**
     * обнуление показателей
     */
    public function start(){
        $this->startTime = microtime();

        $this->points = [];
        $this->points[] = "0";

    }


    /**
     * точка, где можно вывести значение показателей
     */
    public function point(){
        $pointTime = microtime();

        $deltaTime = $pointTime - $this->startTime;

        $this->points[] = $deltaTime;
    }


    /**
     * отобразить результаты
     *
     * $mode - режим отображения
     * - echo (default) - чистый echo
     * - var_dump - через чистый var_dump
     * - pre - var_dump обрамляется тегами <pre>
     * - return - массив результатов вернется без отображения
     */
    public function results($mode = "echo"){
        $count = count($this->points);
        if ($count > 0) {
            if ($mode == "pre") {
                echo "<pre>";
                for ($i=0; $i<$count; $i++) {
                    var_dump($this->points[$i]);
                }
                echo "</pre>";
            }
            elseif ($mode == "var_dump") {
                for ($i=0; $i<$count; $i++) {
                    var_dump($this->points[$i]);
                }
            }
            elseif ($mode == "echo") {
                for ($i=0; $i<$count; $i++) {
                    echo $this->points[$i];
                    echo "\n";
                }
            }
        }
        return $this->points;
    }
}