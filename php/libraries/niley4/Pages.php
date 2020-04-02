<?php

namespace niley4;


class Pages extends _Singleton {

    /**
     * разбивание набора строк по страницам на строгое число сообщений на странице
     * возвращает массив,
     * - 0-первое число для запроса LIMIT,
     * - 1-последнее число для запроса
     * - 2-строка со страницами -1- -2- ...
     *
     *
     * $count - сколько всего строк в выборке
     * $countInPage - сколько всего строк на одной странице
     * $nowPage - на какой странице мы сейчас находимся
     * $class - массив стилей
     * 	$class[0] - класс стилей для номера открытой страницы
     * 	$class[1] - класс стилей для номерво закрытых страниц
     * $pathToPage - путь к файлу (путь на сайте), путь к странице (как через меню)
     * $getLink - get-запрос, параметры через &, если в запросе есть параметры помимо страниц
     * $action - варианты работы с окнами
     * 	ahchor - ссылочная зависимость адреса от запроса
     * 	javascript - зависимость javascript
     * $function - место, чтобы описать событие javascript.
     * 	Пишется в формате  "thisFunction(param1, param2" - и скобка не закрывается, т.к. есть обязательные параметры ниже
     *
     * $pageNPAGE	- get-переменная, отвечает за номер страницы
     * $pageINPage	- get-переменная, отвечает за количество строк на странице
     * $totalAnchors - сколько ссылок на листе От и До выбранной, не включая многоточие
     *
     * //коррекция url
     * //переменные inPage и npage добавляются в массив после прохождения данной функции.
     * //Значит, при внедрении в данную функцию их нужно изъять
     * if ($get) {
     * 	$newUrl = "";
     * 	foreach($get as $key=>$val){
     * 		if ($key !== "npage" && $key !== "inPage")
     * 		$newUrl .= $key."=".$val."&";
     * 	}
     * }
     */
	public function num_pages ($count, $countInPage = 25, $nowPage = 1, $class = "", $pathToPage = "", $getLink = "?", $action = "anchor", $function = "func(", $pageNPAGE = "npage", $pageINPage="inPage", $totalAnchors=0) {
		settype ($countInPage, "integer");
		settype ($nowPage, "integer");
		$nowPage_ = $nowPage - 1;

		if (! is_int($countInPage) || $countInPage == 0){
            if (isset($error)) {
                $error->add("Ошибка. Второй параметр не число (или =0)", 'L_pages');
            }
			return false;
		}

		if ( ($count%$countInPage) == 0 ) {
			$integer = $count/$countInPage;
		}
		else {
			$integer = ($count+1)/$countInPage;
		}
		//сколько всего возможно страниц
		$integer = ceil($integer);

		$first_num = $nowPage_*$countInPage;
		$second_num = $countInPage;

		//обработка стилей (если $class - пустой)
		if ( !is_array($class) ) {$class = array();}

		$str = "";
		//ссылки будут отображаться, если количество страниц > 1
		if ($integer > 1) {
		
			
		
			//ограничения сверху и снизу
			$to_limit_top = false;
			$to_limit_bottom = false;
			
			if ($totalAnchors == 0) {
				$i_from = 0;
				$i_to = $integer;
			}
			else {
				//если количество ссылок больше ограничения
				if ($totalAnchors <= $integer){
					//$nowPage_;
					$i_from = $nowPage_ - $totalAnchors + 1;
					$i_to = $nowPage_ + $totalAnchors;
					
					
					if ($i_from <= 0) $i_from = 0;
					if ($i_from > 0) $to_limit_bottom = true;
					
					if ($i_to >= $integer) $i_to = $integer;
					if ($i_to < $integer) $to_limit_top = true;
				}
				else {
					$i_from = 0;
					$i_to = $integer;
				}
			}


			if ($action == "anchor_sides") {
				if ($nowPage_ > 0) $prevPage = $nowPage_;
				else $prevPage = 1;
				$str .= "<a class='{$class[2]}' href='{$pathToPage}{$getLink}{$pageNPAGE}={$prevPage}&{$pageINPage}={$countInPage}'>Предыдущая</a>";
			}
			
			if ($to_limit_bottom) {
				$prevPage = $i_from;
				$str .= "<a class='{$class[2]}' href='{$pathToPage}{$getLink}{$pageNPAGE}={$prevPage}&{$pageINPage}={$countInPage}'>...</a>";
			}

			for ($i=$i_from; $i<$i_to; $i++) {
				$j = $i + 1;
				
				//ahchor - ссылочная зависимость адреса от запроса
				if ($action == "anchor") {
					if ( $nowPage_ == $i ) {
						$str .= "<a class='{$class[0]}' href='{$pathToPage}{$getLink}{$pageNPAGE}={$j}&{$pageINPage}={$countInPage}'>{$j}</a>&nbsp;";
					}
					else {
						$str .= "<a class='{$class[1]}' href='{$pathToPage}{$getLink}{$pageNPAGE}={$j}&{$pageINPage}={$countInPage}'>{$j}</a>&nbsp;";
					}
				}
				
				//javascript - зависимость javascript
				elseif ($action == "javascript") {
					//npage={$j}
					//inPage={$countInPage}
					if ( $nowPage_ == $i ) {
						$str .= "<span class='{$class[0]}' onclick='{$function}\"{$j}\", \"{$countInPage}\")'>{$j}</span>&nbsp;";
					}
					else {
						$str .= "<span class='{$class[1]}' onclick='{$function}\"{$j}\", \"{$countInPage}\")'>{$j}</span>&nbsp;";
					}
				}
				
				//anchor_sides - вариант ссылочной зависимости адреса (с боковыми кнопками Предыдущая, Следующая)
				elseif ($action == "anchor_sides") {
					if ( $nowPage_ == $i ) {
						$str .= "<a class='{$class[0]}' href='{$pathToPage}{$getLink}{$pageNPAGE}={$j}&{$pageINPage}={$countInPage}'>{$j}</a>&nbsp;";
					}
					else {
						$str .= "<a class='{$class[1]}' href='{$pathToPage}{$getLink}{$pageNPAGE}={$j}&{$pageINPage}={$countInPage}'>{$j}</a>&nbsp;";
					}
				}
			}
			
			if ($to_limit_top) {
				$prevPage = $i_to + 1;
				$str .= "<a class='{$class[2]}' href='{$pathToPage}{$getLink}{$pageNPAGE}={$prevPage}&{$pageINPage}={$countInPage}'>...</a>";
			}
			
			if ($action == "anchor_sides") {
				if ($nowPage_ + 2 < $integer) $nextPage = $nowPage_ + 2;
				else $nextPage = $integer;
				$str .= "<a class='{$class[2]}' href='{$pathToPage}{$getLink}{$pageNPAGE}={$nextPage}&{$pageINPage}={$countInPage}'>Следующая</a>";
			}
		}

		$array = array ($first_num, $second_num, $str);
		return $array;
	}
}
