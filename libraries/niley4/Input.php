<?php
namespace niley4;

class Input extends _Singleton {
	public function checked ($bool){
		if ($bool == true) 
			return "checked='checked'";
		else 
			return "";
	}
}
