<?php
namespace niley4;

//СЕССИИ ДОЛЖНЫ БЫТЬ ВКЛЮЧЕНЫ!

	//генератор случайного 20значного числа.
	//-сгенерировать число
	//-сохранить его в сессии
	//-послать число вместе с запросом В СКРИПТ обработки запроса
	//-сравнить POST и Сессию 
		//если числа равны - запрос выполняется
		
	//УСЛОВИЕ: при выполненном или не выполненном запросе число перезаписывается в Сессию

class Generator extends _Singleton {

	//генерация 20-значного числа
	public function gen20_return ($num=20){
		$number='';
		for ( $i=1; $i<=$num; $i++ ) {
			$a = rand (0,9);
			$number .= $a;
		};
		return $number;
	}


	//необходимо наличие функции gen20_return()
	//запись 20-значного числа в сессию
	public function gen20_construct ($num=20){
		$_SESSION['gen20'] = $this->gen20_return ($num);

		//self::$error->add(2, "Проверка сессий. gen20={$_SESSION['gen20']}", 'l_numeric_generator');
		return $_SESSION['gen20'];
	}


	//уничтожение сессионной переменной gen20
	public function gen20_destroy(){
		$_SESSION['gen20'] = false;
	}
	
	
	//генерация 20-значного числа - цифры и буквы
	public function gen20_extended ($num=20){
		$number = "";
		for ($i=0; $i<$num; $i++) {
			$a = rand (97, 132);
			if ($a > 122) {
				$a = $a - 75;
			};
			$number .= chr ($a);
		};
		return $number;
	}
}
