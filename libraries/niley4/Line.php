<?php

namespace niley4;

/**
 * класс, обрабатывающий адресную строку
 * 24.04.12 - переработан для andromed
 * 25.05.12 - вызывается с помощью init
 * 2012-12-07 - доработка Line_inquiryFast - теперь может вызываться в include без обработки линии
 * 2012-12-23 - добавилось свойство page - для автоматического распознавания страницы
 * 2012-12-23 - добавлен separator (пока как возможность). Уже реализовано в старой версии набора классов!
 * 2013-05-28 - добавлена регистрация ошибок в классе Error - из-за версии 2.41
 * 2013-07-23 - в связи с обработкой ЧПУ (get)
 * 2013-11-11 - новое представление адресной строки. Теперь переменные разделяются слэшами (как в самом первом фреймворке) - мы к этому вернулись.
 * Благодаря этому доступна более удобная раобта с ЧПУ (а также в комплексе с функционалом SEO - удобное управление редиректом)
 * 2013-12-10 - доработка раздела progressive: теперь на странице index можно просматривать новости (листать)
 * 2014-04-23 - перенес рег.выражения в верхнюю часть класса (не все)
 * 2014-08-20 - важный апдейт для языков (для меню)
 * 2019-12-16 - перенос в версию 4
 *
 * Классы:
 * - L_line (Line_inquiry)
 * - Line_inquiryFast
 */
class Line extends _Singleton {

    private $trueError = true;
    private static $progressive = true; //активация работы класса через index.php
    //simple от progressive отличает то, что вне класса вызывается функция $line->prog_created_line();
    // при simple эта функция вызывается внутри класса!

    //натуральный request_uri
    public $request_uri = '';
    //полученная строка после index.php
    public $line = '';
    //проверенная строка
    public $preg_line = '';
    //обработанная строка
    public $array_line = array();
    //количество управляющих последовательностей ../ для ajax
    public $sequence = '';
    //страница (для схемы domain.ru/page/?param=123)
    public $page = '';
    //разделитель
    public $separator = '';

    public $get = "";
    public $s_get = "";

    //рег. выражение общее, для всей строки
    private $reg_line_common 	= "/[A-Za-zА-Яабвгдеёжзийклмнопрстуфхцчшщъыьэюя0-9=_?&.,+\/\-]+/";
    //рег. выражение общее, для get-массива
    private $reg_line_get 		= "/[A-Za-zА-Яабвгдеёжзийклмнопрстуфхцчшщъыьэюя0-9=.+&\-_]+/";


    //зарезервированные слова, которые не являются определителями страниц
    private $reserved_strings = array("ru", "en", "de");
    private $is_language = false;//по умолчанию - без использования языка
    private $languageDefaultName = "ru";
    private $languageName = "";

    private static $error = null;
    private static $pagePrefix = "";
    private static $thisObject = null;



    //создание линии
    private function created_line(){
        $this->line();
        $this->check_line();
        $this->processing_line();
        $this->escape_sequences();//для ajax
    }





    //progressive = true ---------------------------------------------------------------------------------------------------------



    //создание линии
    public function prog_created_line(){
        $this->prog_line();
        $this->prog_check_line();
        $this->prog_processing_line();
        $this->prog_escape_sequences();//для ajax
    }

    //устанавливаем факт использования языка
    public function setIsLanguage($is_language){
        $this->is_language = $is_language;
    }
    public function setDefaultLanguage($languageDefaultName){
        $this->languageDefaultName = $languageDefaultName;
    }
    public function getLanguageName(){
        return $this->languageName;
    }

    private function prog_line(){
        $line = $_SERVER['REQUEST_URI'];

        if (self::$pagePrefix) {
            $line = substr($line, strlen(self::$pagePrefix)+1, strlen($line)-strlen(self::$pagePrefix)-1);
        }

        //вычисления
        $position = strpos($line, 'index.php');
        $len_index = strlen('index.php');

        if ($position == false) {
            $position = 0;
            $len_index = 0;
        }

        $len_line = strlen($line);
        //полученная строка
        $string = substr($line, ($position+$len_index), ($len_line-($position+$len_index)));

        //убираем лишние
        $string = preg_replace("/\/+/", "/", $string);

        if ($position == false) $this->request_uri = $string;
        else $this->request_uri = '/index.php' . $string;

        //сохранение параметра
        $this->line = urldecode($string);

        return $this->line;
    }

    //проверка полученной строки
    private function prog_check_line(){
        if ( $this->line == '' ) {}
        else {
            //регулярное выражение
            preg_match($this->reg_line_common, $this->line, $preg_array);

            //длина полученного выражения
            $len_preg = strlen($preg_array[0]);
            //длина строки запроса
            $len_line = strlen($this->line);
            //сравнение введенной и полученной строки (при отбрасывании всех лишних символов,
            //длины строк должны быть равны
            if ( $len_preg != $len_line ) {}
            else {
                //регулярное выражение на проверку на символы ////
                preg_match("/[\/]+/", $preg_array[0], $preg_array2);

                //это сравнение нельзя пропускать
                if ( $preg_array[0] == $preg_array2[0] ) {}
                else {
                    //сохранение параметра
                    $this->preg_line = $preg_array[0];
                }
            }
        }
    }

    //обработка проверенной строки
    //возвращает массив
    //формат: [0]=>'' (пустая строка), [1]=>'значение1', ...
    private function prog_processing_line($line = false){
        if ($line == false) {
            $thisline = $this->preg_line;
        }
        //для повторного вызова при редиректе
        else {
            $thisline = $line;
        }
        $a_questionMark = explode("?", $thisline);

        if (count($a_questionMark) > 2) {
            if ($this->trueError) {
                self::$error->add(0, "В URL более 1 знака вопроса (".count($a_questionMark)." знаков), неправильно прописан адрес!", get_class($this));
            }
        }
        else {
            $array = explode("/", $a_questionMark[0]);
            $this->array_line = $array;

            //$array[0] всегда пустой

            //проверка страницы и языка
            if ($array[1]) {
                //если вместо первой ячейки массива будет зарезервированная строка, значит - эта страница index!
                $is_index = false;
                //необходимо устроить проверку на зарезеврированные символы простой php-функцией
                for ($i=0; $i<count($this->reserved_strings); $i++) {
                    if ($array[1] == $this->reserved_strings[$i]) {
                        $is_index = true;
                        break;
                    }
                }

                if ($this->is_language == true) {
                    //инициализирован ли язык
                    $is_init_language = false;
                    for ($i=0; $i<count($this->reserved_strings); $i++) {
                        //чтобы не было лишнего цикла, мы сразу угадываем ячейку массива, где хранится инфа о языке
                        //это если язык отпечатывается в самом конце массива. А по идее, если он распределен по массиву, тут требуется проход по каждой ячейке
                        // т.е. еще один for, только еще по $array
                        if ($array[count($array)-2] == $this->reserved_strings[$i]) {
                            $this->languageName = $array[count($array)-2];
                            $is_init_language = true;
                            break;
                        }
                    }

                    if ($is_init_language == false) {
                        if ($this->languageName == "") $this->languageName = $this->languageDefaultName;
                    }
                }

                if ($is_index == true) $this->page = "";
                else $this->page = $array[1];
            }
            else {
                $this->page = "";
                if ($this->is_language == true) {
                    if ($this->languageName == "") $this->languageName = $this->languageDefaultName;
                }
            }
        }
    }

    //вычисление уровней папок возврата - количество управляющих последовательностей ../ для ajax
    private function prog_escape_sequences(){
        $sequence = '../';
        $count = count($this->array_line)-1;

        if (! $count ) $count = 0;
        if ( $count != 0 ){

            $str = '';
            for($i=0; $i<$count; $i++){
                $str .= $sequence;
            }
            $this->sequence = $str;
            return true;
        }
        else {
            $this->sequence = '';
            return '';
        }
    }





    //progressive = false ---------------------------------------------------------------------------------------------------------



    //записывает строку, переданную после значения index.php в адресной строке
    //строка может быть в любом формате! (фильтры - в др.методе)
    private function line()
    {
        $line = $_SERVER['REQUEST_URI'];
        //вычисления
        $position = strpos($line, '/?');

        //1. анализ url - $this->line
        if ($position) {
            $len_index = strlen('/?');
            $len_line = strlen($line);
            //полученная строка
            $string = substr($line, ($position+$len_index), ($len_line-($position+$len_index)));
            //сохранение параметра
            $this->line = urldecode ($string);
        }
        else {
            $this->line = "";
        }

        //2. анализ страницы - $this->page
        //если есть префикс - забираем его, оставляем чистую строку
        if (self::$pagePrefix) {
            $line = substr($line, strlen(self::$pagePrefix)+1, strlen($line)-strlen(self::$pagePrefix)-1);
        }
        //вычисления разделов между слэшами - перед знаком вопроса
        $explode = explode('/', $line);

        $this->separator($explode);
        if (isset($explode[1]) && $explode[1] != "") {
            preg_match("/[A-Za-z0-9=_\-]+/", $explode[1], $preg_array);

            if ($explode[1] == $preg_array[0]) {
                $this->page = $preg_array[0];
            }
            else {
                $this->page = "";
                if ($this->trueError) {
                    //эту ошибку можно закомментировать - незачем придумывать дополнительный раздел.
                    // Надо просто иметь в виду, что она возникла вследствие нестандартного пользования структурой
                    //self::$error->add(1, "После первого слэша содержатся недопустимые символы. Если это id=xxx в Index, то можно сделать дополнительный раздел news, где и будет работа с get[id]", get_class($this));
                }
            }
        }
        else {
            $this->page = "";
            //self::$error->add(3, "После первого слэша ничего нет", get_class($this));
        }

        //3. вычисление раздела после второго слэша - если между слэшами /pages/
        //если страница занята pages, показываем третий уровень
        if ($this->page == "pages") {
            if (isset($explode[2]) && $explode[2] != "" && $explode[2] != "?") {
                preg_match("/[A-Za-z0-9._\-?&=]+/", $explode[2], $preg_array2);
                if ($explode[2] == $preg_array2[0]) {
                    $FileName = $preg_array2[0];

                    $expFN = explode(".php", $FileName);
                    if (count($expFN) > 1) {
                        preg_match("/[A-Za-z0-9._\-]+/", $expFN[0], $preg_array3);
                        if ($expFN[0] == $preg_array3[0]) {
                            $this->page = $preg_array3[0];
                        }
                        else {
                            $this->page = "";
                            if ($this->trueError)
                                self::$error->add(1, "После второго слэша название файла содержит недопустимые символы", get_class($this));
                        }
                    }
                    else {
                        $this->page = "";
                        if ($this->trueError)
                            self::$error->add(1, "После второго слэша название файла заканчивается не на .php", get_class($this));
                    }
                }
                else {
                    $this->page = "";
                    if ($this->trueError)
                        self::$error->add(1, "После второго слэша недопустимые символы", get_class($this));
                }
            }
            else {
                $this->page = "";
                if ($this->trueError)
                    self::$error->add(1, "Между слэшами pages, второй элемент пустой, либо знак вопроса.", get_class($this));
            }
        }
        elseif ($this->page == "ru") {
            echo 444;
            exit();
        }
    }

    //необходимо доработать, т.к. нет проверки на наличие пустых мест между слэшами
    private function separator($explode){
        $count = count($explode) - 2;
        if ($count == 0) {
            $this->separator = "./";
        }
        elseif ($count > 0) {
            $separator = "";
            for ($i=0; $i<$count; $i++) {
                $separator .= "../";
            }
            $this->separator = $separator;
        }
        else {
            if ($this->trueError)
                self::$error->add(1, "separator (!) Число слэшей меньше двух, длина массива меньше нуля!", get_class($this));
        }
    }

    //проверка полученной строки
    private function check_line(){
        if ( $this->line == '' ) {}
        else {
            //регулярное выражение
            preg_match("/[A-Za-zА-Яабвгдеёжзийклмнопрстуфхцчшщъыьэюя0-9=_&\-\s]+/", $this->line, $preg_array);
            //длина полученного выражения
            $len_preg = strlen($preg_array[0]);
            //длина строки запроса
            $len_line = strlen($this->line);
            //сравнение введенной и полученной строки (при отбрасывании всех лишних символов,
            //длины строк должны быть равны
            if ( $len_preg != $len_line ) {
                self::$error->add(1, "get-параметры: Есть недопустимые символы (после Вопроса)", get_class($this));
            }
            else {
                //регулярное выражение на проверку на символы ////
                preg_match("/[\/]+/", $preg_array[0], $preg_array2);
                //это сравнение нельзя пропускать
                if ( $preg_array[0] == $preg_array2[0] ) {
                    if ($this->trueError)
                        self::$error->add(1, "get-параметры: Не пропускаем ни одного символа косой черты!", get_class($this));
                }
                else {
                    //сохранение параметра
                    $this->preg_line = $preg_array[0];
                }
            }
        }
    }

    //обработка проверенной строки
    //возвращает массив
    //формат: [id]=>'', [name]=>'значение1', ...
    private function processing_line(){
        $array_GET = explode("&", $this->preg_line);
        $array = array();
        for ($i = 0; $i<count($array_GET); $i++) {
            $keyANDval = explode("=", $array_GET[$i]);
            $array[$keyANDval[0]] = $keyANDval[1];
        }
        $this->array_line = $array;
    }


    //2014-04-03 - для пользования редиректом и удобной настройкой через админку
    public function analisPage($line = false) {
        $this->prog_processing_line($line);
        return $this->page;
    }

    /**
     * анализирует GET на наличие лишних символов
     * необходим для преобразования глобального массива GET в простой массив get
     *
     * Возвращает ассоциативный массив:
     * ->get - массив с GET-параметрами
     * ->getString - часть урла после знака ?
     */
    public function analisGet($line = false){
        //при наличии GET
        $array1 = array();
        $s_get = "";
        if ($_GET) {
            $str = "";
            foreach($_GET as $key=>$val) {
                $str .= $key."=".$val."&";
            }
            preg_match($this->reg_line_get, $str, $preg_array);

            if ($str == $preg_array[0]) {
                $array1 = $_GET;
            }

            $s_get .= $str;
        }

        //при наличии line
        $array2 = array();
        if ($line) {
            $str = explode('?', $line);

            //анализ $str - на конце может быть слэш, его надо убрать
            //не работает, если слешей несколько
            $str[1] = str_replace("/", "", $str[1]);

            preg_match($this->reg_line_get, $str[1], $preg_array);

            if ($str[1] == $preg_array[0]) {
                $strgets = explode('&', $str[1]);
                $array = array();
                for ($i=0; $i<count($strgets); $i++){
                    $arr = explode('=', $strgets[$i]);
                    $array[$arr[0]] = $arr[1];
                }
                $array2 = $array;
            }

            $s_get .= $str[1];
        }
        $result = array_merge ($array1, $array2);
        $this->get = $result;
        $this->s_get = $s_get;

        return array(
            "get"=>$this->get,
            "getString"=>$this->s_get
        );
    }

    //вычисление уровней папок возврата - количество управляющих последовательностей ../ для ajax
    private function escape_sequences(){
        $sequence = '../';
        $count = count($this->array_line)-1;

        if (! $count ) $count = 0;
        if ( $count != 0 ){

            $str = '';
            for($i=0; $i<$count; $i++){
                $str .= $sequence;
            }
            $this->sequence = $str;
            return true;
        }
        else {
            $this->sequence = '';
            return '';
        }
    }


    /**
     * Преобразовать строку (url), пришедшую с клиента на сервер в ассоц.массив (вида ключ-значение)
     * Массив очищенный от лишних (не существующих) переменных
     */
    public function getArray($string) {
        $resArray = array();

        if ($string) {
            $a_string = explode("&", $string);

            $length = count($a_string);
            for ($i=0; $i<$length; $i++) {
                $item = $a_string[$i];

                if ($item) {
                    $a_item = explode("=", $item);

                    $resArray[$a_item[0]] = $a_item[1];
                }
            }
        }

        return $resArray;
    }
}
