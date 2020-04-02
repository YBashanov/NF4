<?php

class MailFunctions {


    // получить верстку с поднятыми копейками
    function getMoneyFormat($price)
    {
        $price_arr = ["0", "00"];
        $cent = "00";

        if ($price) {
            $price_arr = explode(".", $price);
        }

        $rub = $price_arr[0];
        if (count($price_arr) > 1) {
            $cent = $price_arr[1];
        }

        else {
            $cent = $cent . "";
            $length = strlen($cent);
            if ($length == 1) {
                $cent .= "0";
            }
        }

        $rub = $this->_setMoneyStyle($rub);

        return "<span>{$rub}</span><span><sup> {$cent}</sup></span>";
    }


    // установить пробелы между тройками цифр
    function _setMoneyStyle($rub) {
        $newRub = "";
        $rub = $rub . "";

        $length = strlen($rub);
        $tri = 0;
        for ($i = ($length - 1); $i >= 0; $i--) {
            $tri++;
            $newRub = $rub[$i] . $newRub;

            if ($tri == 3) {
                $newRub = " " . $newRub;
                $tri = 0;
            }
        }
        return $newRub;
    }
}