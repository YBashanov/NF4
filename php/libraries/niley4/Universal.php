<?php

/**
 * Проверка на существование элемента и его содержимого
 *
 * Проверяет: массив, число, строку.
 * $key - ключ массива (если проверяем массив)
 *
 * <b>Возвращает</b> значение элемента, если оно есть, или null
 */
function verify($variable = null, $key = null) {
    if (is_array($variable)) {
        if (isset($variable)) {
            if (isset($variable[$key])) {
                return $variable[$key];
            }
        }
    }
    else if (is_numeric($variable)) {
        if (isset($variable)) {
            return $variable;
        }
    }
    else if (is_string($variable)) {
        if (isset($variable)) {
            return $variable;
        }
    }
    else {
        if (isset($variable)) {
            return $variable;
        }
    }
	return null;
}

