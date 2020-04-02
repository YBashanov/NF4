<?php
namespace niley4;

class Strings extends _Singleton {

	private $view_error = true;//разрешение/запрещение генерации ошибок-отчетов классом (true/false)
	private $separator = "";
	private $libraries = null;

	public function setSeparator ($separator){
		$this->separator = $separator;
	}
	public function setLibraries ($libraries){
		$this->libraries = $libraries;
	}


	//укорачиватель возможно длинных фраз (чтобы таблица не раздувалась)
	//$len - длина, на которую укорачиваем, не считая прибавленных 2х точек
	public function shorten ($str, $len = 26, $suffix = "<...>", $is_array = false) {
		if ($len > 1) {
			if ((strlen($str) > $len) && ($len != 0)) {
				$wrap_str = wordwrap($str, $len, "|");
				$index = strpos($wrap_str, "|");
				$str1 = substr($wrap_str, 0, $index) . $suffix;

				
				if ($is_array == false) {
					return $str1;
				}
				else {
					$str2 = substr($str, $index);
					return array($str1, $str2);
				}
			}
			else {
				return $str;//без изменений
			}
		}
		return false;
	}


	//укорачиватель возможно длинных фраз (чтобы таблица не раздувалась)
	//$len - длина, на которую укорачиваем, не считая прибавленных 2х точек
	public function shortLetters ($str, $len = 26, $suffix = "<...>") {
		if ($len > 1) {
			if ((strlen($str) > $len) && ($len != 0)) {
				$str = substr($str, 0, $len) . $suffix;

				return $str;
			}
			else {
				return $str;//без изменений
			}
		}
		return false;
	}
	
	
	//$wordNum = 1 - берет только не менее первых 20 символов (10 букв) строки - до пробела
	//$wordNum = 2 - берет 2 слова строки
	public function firstWord ($str, $wordNum = 1, $wordLen = 20) {
		$str = trim ($str);
	
		if ($wordNum == 1) {
			$index = @strpos($str, " ", $wordLen);

			if ($index) {
				return substr($str, 0, $index);
			}
			//нет пробела, т.к - всего одно слово в строке
			else return $str;
		}
		
		elseif ($wordNum == 2) {
			$index1 = @strpos($str, " ");
			
			//если есть первый пробел, смотрим дальше
			if ($index1) {
				$index2 = @strpos($str, " ", $index1+1);
				
				//если есть второй пробел - возвращаем строку
				if ($index2) {
					return substr($str, 0, $index2);
				}
				else return $str;
			}
			//нет пробела, т.к - всего одно слово в строке
			else return $str;
		}
		
		else return false;
	}
	
	
	//возвращает текст, наполненный картинками - для новой админки (2.44)
	//text - весь текст с кодированными картинками
	//type - тип обработки (шаблон на выходе)
		//split		отделить картинки от текста без дальнейших преобразований. Текст - под картинками
		//slide		фотографии выводятся с возможностью открывать их как слайды. Текст - под картинками
		//clip		"сухая" фотка без возможности просмотра. Текст - нет.
		//all		вывод текста и картинок "как есть", в той же последовательности. Картинки - flo_left
		
	//возвращает массив
		//0 - полная строка
		//1 - только картинки
		//2 - только текст
	public function explode_img($text, $type = ""){
		$arr = explode("[", $text);
		
		$index_end = 0;
		$string = "";
		$images = "";
		$subtext = "";
		$string_2 = "";

		if (count($arr) > 1) {
			$this->libraries['image']->setSeparator($this->separator);
		
			for ($i=0; $i<count ($arr); $i++) {
				if ($arr[$i]){
					$index_end = strpos($arr[$i], "]", 1);
					$cell = substr($arr[$i], 0, $index_end);
					
					if ($index_end === false) $index_end = -1; //для начальных строк, чтобы лишний раз не отнимала 1 символ
					
					//анализ содержимого ячейки []
					$a_cell = explode("=", $cell);
					if ($a_cell[0] == "image") {
						$nameImg = $a_cell[1];
					}
					elseif ($a_cell[0] == "video") {
						$nameVideo = $a_cell[1];
					}
					else $nameImg = $a_cell[0];

					//формирование ответа
					if ($type == "split") {
						if ($index_end !== -1) {
							$images .= "<img class='split' src='{$this->separator}@/uploads/foto/m_{$nameImg}.jpg' width='150' height='100'/>";
						}
						$subtext .= substr($arr[$i], $index_end+1);
					}
					elseif ($type == "slide") {
						if ($index_end !== -1) {
							$images .= "<div class='clip'>";
								$images .= $this->libraries['image']->slide("@/uploads/foto/", $nameImg, 320, 320);
							$images .="</div>";
						}
						
						$subtext .= substr($arr[$i], $index_end+1);
					}
					elseif ($type == "clip") {
						if ($index_end !== -1) {
							$images .= "<div class='clip'>
								<img class='clip' src='{$this->separator}@/uploads/foto/m_{$nameImg}.jpg' />
							</div>";
						}
					}
					elseif ($type == "all") {
						if ($index_end !== -1) {
							$images .= "<div class='clipImage'>";
								$images .= $this->libraries['image']->slide("@/uploads/foto/", $nameImg, 200);
							$images .="</div>";
						}
						$subtext .= substr($arr[$i], $index_end+1);
						$subtext .= "<div class='cle'></div>";
						
						$string .= $images . $subtext;
						$images = "";
						$subtext = "";
					}
					else {}
				}
			}
			if ($type == "split") $string = $images . $subtext;
			elseif ($type == "slide") $string = $images . $subtext;
		}
		else {
			$subtext = $text;
			$string = $subtext;
		}
		$newArray = array(
			0=>$string,
			1=>$images,
			2=>$subtext
		);
		
		return $newArray;
	}
	
	
	//подставленный в текст скрытый код вида [37] сохраняется, как <image... src='37.jpg' />
	public function explodeMedia_save($text, $base_url){
		$arr = explode("[", $text);
		if (count($arr) > 1) {
			$resultText = "";
			
			for ($i=0; $i<count ($arr); $i++) {
				if ($arr[$i]){

					$arr_2L = explode("]", $arr[$i]);
					
					if (count($arr_2L) > 1) {
						$cell = $arr_2L[0];
						$moduleText = $arr_2L[1]; //остаток текста после ]
						
						//если есть закрывающая, и внутри - тоже что-то есть
						if ($cell) {
							
							//анализ содержимого ячейки []
							$a_cell = explode("=", $cell);
							if ($a_cell[0] == "image") {
								
								$a_width = explode("width", $cell);
								//если есть width
								if ($a_width[1]) {
									$a_nameImg = explode("=", $a_width[0]);
									$nameImg = trim($a_nameImg[1]);
									$width = $a_cell[2];
								}
								else {
									$nameImg = $a_cell[1];
									$width = 200;
								}

								$mediaAdd = "<div class='cle_left'></div><div class='expimg'><img src='{$base_url}uploads/foto/{$nameImg}.jpg' width={$width}/></div>";
							}
							elseif ($a_cell[0] == "video") {
								$nameImg = $a_cell[1];
								$mediaAdd = "<div class='cle_left'></div><div class='expimg'><div id='player{$nameImg}'></div><script>so = new SWFObject('{$base_url}modules/flash/player1/player.swf','mpl','240','180','8');so.addParam('allowfullscreen','true');so.addParam('flashvars','file={$base_url}uploads/video/{$nameImg}.mp4&controlbar=none');so.write('player{$nameImg}');</script></div>";
							}
							elseif ($a_cell[0] == "audio") {
								$nameImg = $a_cell[1];
								$mediaAdd = "<div class='cle_left'></div><div class='expimg'><object type='application/x-shockwave-flash' data='{$base_url}modules/audio/player.swf' id='audioplayer{$nameImg}' height='24' width='290'><param name='movie' value='{$base_url}modules/audio/player.swf'><param name='FlashVars' value='playerID={$nameImg}&amp;soundFile={$base_url}uploads/audio/{$nameImg}.mp3'><param name='quality' value='high'><param name='menu' value='false'><param name='wmode' value='transparent'></object></div>";
							}
							//тоже изображение, что бы ни написали
							else {
								$a_width = explode("width", $cell);
								//если есть width
								if ($a_width[1]) {
									$nameImg = trim($a_width[0]);
									$width = $a_cell[1];
								}
								else {
									$nameImg = $a_cell[0];
									$width = 200;
								}

								$mediaAdd = "<div class='cle_left'></div><div class='expimg'><img src='{$base_url}uploads/foto/{$nameImg}.jpg' width={$width}/></div>";
							}
							$resultText .= $mediaAdd . $moduleText;
						}
						else {
							$resultText .= "[]" . $moduleText;
						}
					}
					else {
						$resultText .= $arr_2L[0];
					}
				}
			}
			
			$text = addslashes (trim ($resultText));
			return $text;
		}
		return $text;
	}


	//код вида <image... src='37.jpg' /> преобразуется в специальный код [37]
	public function explodeMedia_show($text, $base_url){
		$arr = explode("<div class='cle_left'></div><div class='expimg'>", $text);
		if (count($arr) > 1) {
			$resultText = "";

			for ($i=0; $i<count ($arr); $i++) {
				if ($arr[$i]){
					
					$isImg = strpos($arr[$i], "<img src=");
					
					if ($isImg !== false) {
						//так у изображений - задняя окантовка
						$arr_2L = explode("/></div>", $arr[$i]);

						if (count($arr_2L) > 1) {
							$cell = $arr_2L[0];
							$moduleText = $arr_2L[1]; //остаток текста после ]

							//если есть закрывающая, и внутри - тоже что-то есть
							if ($cell) {
								
								//анализ содержимого ячейки []
								$a_cell = explode("{$base_url}uploads/foto/", $cell);

								if ($a_cell[1]) {
									$a_width = explode("width=", $a_cell[1]);
									if ($a_width[1] != 200) $thisWidth = " width=".$a_width[1];
									else $thisWidth = "";
								
									$idImage = explode(".jpg", $a_cell[1]);
									$resultText .= "[".$idImage[0]."{$thisWidth}]";
								}
							}
							
							if ($moduleText) {
								$resultText .= $moduleText;
							}
						}
						else {
							$resultText .= $arr_2L[0];
						}
					}
					//если это не изображение - дальше
					else {
						$isVideo = strpos($arr[$i], "new SWFObject");
						
						if ($isVideo !== false) {
							$arr_2L = explode("</script></div>", $arr[$i]);
							
							if (count($arr_2L) > 1) {
								$cell = $arr_2L[0];
								$moduleText = $arr_2L[1];

								//если есть закрывающая, и внутри - тоже что-то есть
								if ($cell) {
									
									//анализ содержимого ячейки []
									$a_cell = explode("{$base_url}uploads/video/", $cell);

									if ($a_cell[1]) {
										$idImage = explode(".mp4", $a_cell[1]);
										
										$resultText .= "[video=".$idImage[0]."]";
									}
								}
								
								if ($moduleText) {
									$resultText .= $moduleText;
								}							
							}
							else {
								$resultText .= $arr_2L[0];
							}
						}
						//если это не видео - дальше
						else {
							$isAudio = strpos($arr[$i], "<param name='movie' value='");
							
							if ($isAudio !== false) {
								$arr_2L = explode("</object></div>", $arr[$i]);
								
								if (count($arr_2L) > 1) {
									$cell = $arr_2L[0];
									$moduleText = $arr_2L[1];

									//если есть закрывающая, и внутри - тоже что-то есть
									if ($cell) {
										
										//анализ содержимого ячейки []
										$a_cell = explode("{$base_url}uploads/audio/", $cell);

										if ($a_cell[1]) {
											$idImage = explode(".mp3", $a_cell[1]);
											
											$resultText .= "[audio=".$idImage[0]."]";
										}
									}
									
									if ($moduleText) {
										$resultText .= $moduleText;
									}							
								}
								else {
									$resultText .= $arr_2L[0];
								}
							}
							//ничего не подходит по критериям - просто записываем как текст
							else {
								$resultText .= $arr[$i];
							}
						}
					}
				}
			}
			$text = $resultText;
			return $text;
		}
		return $text;
	}
	
	
	//метод Умный поиск, v1.0
	//перевод строки в несколько вариантов
	//RASKLAD - раскладка клавиатуры меняется (сам определяет язык)
	//TRANSLIT - транслит по образцу (сам определяет язык)
	private $rasklad_ru = array("й","ц","у","к","е","н","г","ш","щ","з","х","ъ","ф","ы","в","а","п","р","о","л","д","ж","э","я","ч","с","м","и","т","ь","б","ю",".","1","2","3","4","5","6","7","8","9","0","-"," ");
	private $rasklad_en = array("q","w","e","r","t","y","u","i","o","p","[","]","a","s","d","f","g","h","j","k","l",";","'","z","x","c","v","b","n","m",",",".","/","1","2","3","4","5","6","7","8","9","0","-"," ");
	private $translit_ru = array("а","б","в","г","д","е","ё","ж","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ц","ч","ш","щ","ъ","ы","ь","э","ю","я","1","2","3","4","5","6","7","8","9","0","-"," ");
	private $translit_en = array("a","b","v","g","d","e","jo","zh","z","i","j","k","l","m","n","o","p","r","s","t","u","f","h","ts","ch","sh","sch","'","y","'","e","ju","ja","1","2","3","4","5","6","7","8","9","0","-"," ");
	public function smartSearch ($string, $variantNumber = false) {
		if ($variantNumber == false) {
			return $string;
		}
		else {
			//rasklad
			if ($variantNumber == "RASKLAD"){
				//разбиваем на массив букв
				$letters = preg_split("/()/u", $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
				$newString = "";
				
				if (in_array($letters[0], $this->rasklad_ru)) {
					for($i=0; $i<count($letters); $i++){
						$keynumber = array_search($letters[$i], $this->rasklad_ru);
						
						if ($keynumber !== false) {
							$newString .= $this->rasklad_en[$keynumber];
						}
					}
					
					return $newString;
				}
				elseif (in_array($letters[0], $this->rasklad_en)) {
					for($i=0; $i<count($letters); $i++){
						$keynumber = array_search($letters[$i], $this->rasklad_en);
						
						if ($keynumber !== false) {
							$newString .= $this->rasklad_ru[$keynumber];
						}
					}
					
					return $newString;
				}
				else {
					if ($this->view_error == true){
						$error->add(2, "Hет такого значения в массивах RASKLAD", get_class($this));
					}
					return false;
				}
			}
			
			//translit
			elseif ($variantNumber == "TRANSLIT"){
				//разбиваем на массив букв
				$letters = preg_split("/()/u", $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
				$newString = "";
				
				if (in_array($letters[0], $this->translit_ru)) {
					for($i=0; $i<count($letters); $i++){
						$keynumber = array_search($letters[$i], $this->translit_ru);
						
						if ($keynumber !== false) {
							$newString .= $this->translit_en[$keynumber];
						}
					}
					
					return $newString;
				}
				elseif (in_array($letters[0], $this->translit_en)) {
					for($i=0; $i<count($letters); $i++){
						$keynumber = array_search($letters[$i], $this->translit_en);
						
						if ($keynumber !== false) {
							$newString .= $this->translit_ru[$keynumber];
						}
					}
					
					return $newString;
				}
				else {
					if ($this->view_error == true){
						$error->add(2, "Hет такого значения в массивах TRANSLIT", get_class($this));
					}
					return false;
				}
			}
			
			else {
				if ($this->view_error == true){
					$error->add(1, "Hет такого значения константы!", get_class($this));
				}
				return false;
			}
		}
	}
	
	
	//добавляет слэш в конце строки. Удобно для ссылок в нашей системе
	//2014-07-29
	//добавляет только если отсутствует знак вопроса
	public function addSlash ($href) {
		$href = trim($href);
		$index = strpos($href, "?");
		if (! $index) {
			$subhref = substr($href, strlen($href)-1, 1);
			if ($subhref != "/") {
				$href .= "/";
			}
		}
		
		return $href;
	}
}
