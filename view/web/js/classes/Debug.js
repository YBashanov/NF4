/**
 * 2015-12-30 - добавлен глобальный метод e(String) - выводит в консоль ошибку
 * 2016-01-12 - метод e продублирован методом err (т.к. мы иногда используем внутри событий переменную e)
 * 2016-07-14 - добавлен поиск в функции aa
 * 2016-11-29 - открытие логирования только через ключ debug=true (можно просто debug)
 * 2017-03-15
 * 2018-11-19 - удалено лишнее, вынесено в отдельный модуль (+ react)
 */
var Debug={
    get: function(key){var get = {};var url = location.href;var s_url = url.split("?");if (s_url[1]) {var _get = s_url[1].split("&");for (var i = 0; i < _get.length; i++) {var s_get = _get[i].split("=");get[s_get[0]] = decodeURI(s_get[1]);}}if (key){return get[key];}else {return get;}},
    consoleMode : function() {var _debug = Debug.get('debug');try {if (_debug) {if (_debug === "false") {if (Cookie) {Cookie.delete_cookie("debug");}_debug = false;}else {if (Cookie) {Cookie.set_cookie("debug", 1);}_debug = 1;}}else {if (Cookie) {_debug = Cookie.get_cookie("debug");}}}catch (e){if (_debug) {console.error('Не подключен файл Cookie.js, Нет объекта Cookie');}}return _debug;}
};
var c, с, e, е, v;
if (Debug.consoleMode()) {
    var cons = console;
    c = с = v = console.log;
    e = е = console.error;
}
else {
    c = с = v = function(str){};
    e = е = function(str){};
}