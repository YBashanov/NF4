<?php


/**
 * Class Sorts
 * Дополнение к базе данных при mode=files
 */
class Sorts {
    static $context = null;

    static function setSortContext($context) {
        self::$context = $context;
    }


    /**
     * Вспомогательная функция сортировки для метода usort
     */
    static function orderBy($a, $b){
        $ctx = self::$context;

        if ($a[$ctx['key']] == $b[$ctx['key']]) {
            return 0;
        }
        if ($ctx['value'] == "ASC") {
            return ($a[$ctx['key']] < $b[$ctx['key']]) ? -1 : 1;
        }
        else {
            return ($a[$ctx['key']] > $b[$ctx['key']]) ? -1 : 1;
        }
    }
}
