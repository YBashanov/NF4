<?php

namespace niley4;

/**
 * Генерация разных последовательностей случайных чисел
 *
 * <b>Методы</b>
 * - {@link Random::gen20_num} - генерация большого числа
 * - {@link Random::gen20_str} - генерация большой строки
 */
class Random extends _Singleton{

    /**
     * генерация 20-значного числа
     *
     * <b>Параметры</b>
     * - length - количество символов, default=20
     */
    public function gen20_num ($length=20){
        $number="";
        for ( $i=1; $i<=$length; $i++ ) {
            $a = rand (0,9);
            $number .= $a;
        };
        return $number;
    }


    /**
     * генерация 20-значной строки. Цифры и буквы
     *
     * <b>Параметры</b>
     * - length - количество символов, default=20
     */
    public function gen20_str ($length=20){
        $number = "";
        for ($i=0; $i<$length; $i++) {
            $a = rand (97, 132);
            if ($a > 122) {
                $a = $a - 75;
            };
            $number .= chr ($a);
        };
        return $number;
    }
}