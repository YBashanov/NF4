/*
1.2 - 2014-10-02 (добавление set)
*/

var Regular = new Object();

Regular.thisFile = "Regular.js";
Regular.calls = 0;

Regular.num = function (string, callback, callError) {
	Regular.calls++;
	var r_string = string.match(/[0-9]+/);

	if ( r_string != string ) {
		if (typeof callError == "function")
			callError();
			
		return false;
	}
	else {
		if (typeof callback == "function")
			callback();

		return r_string;
	}
}

Regular.floats = function (string, callback, callError) {
	Regular.calls++;
	var r_string = string.match(/[0-9.,-]+/);

	if ( r_string != string ) {
		if (typeof callError == "function")
			callError();
			
		return false;
	}
	else {
		if (typeof callback == "function")
			callback();

		return r_string;
	}
}

Regular.eng_num = function (string, callback, callError) {
	Regular.calls++;
	var r_string = string.match(/[A-Za-z0-9]+/);

	if ( r_string != string ) {
		if (typeof callError == "function")
			callError();
			
		return false;
	}
	else {
		if (typeof callback == "function")
			callback();

		return r_string;
	}
}

Regular.rus_num = function (string, callback, callError) {
	Regular.calls++;
	var r_string = string.match(/[А-Яабвгдеёжзийклмнопрстуфхцчшщъыьэюя0-9]+/);

	if ( r_string != string ) {
		if (typeof callError == "function")
			callError();
			
		return false;
	}
	else {
		if (typeof callback == "function")
			callback();

		return r_string;
	}
}

Regular.eng_num_sol = function (string, callback, callError) {
	Regular.calls++;
	var r_string = string.match(/[A-Za-z0-9\s-_\.\/]+/);

	if ( r_string != string ) {
		if (typeof callError == "function")
			callError();
			
		return false;
	}
	else {
		if (typeof callback == "function")
			callback();

		return r_string;
	}
}

Regular.rus_num_sol = function (string, callback, callError) {
	Regular.calls++;
	var r_string = string.match(/[А-Яабвгдеёжзийклмнопрстуфхцчшщъыьэюя0-9\s-_.]+/);

	if ( r_string != string ) {
		if (typeof callError == "function")
			callError();
			
		return false;
	}
	else {
		if (typeof callback == "function")
			callback();

		return r_string;
	}
}

Regular.sol_symbols_min = function (string, callback, callError) {
	Regular.calls++;
	var r_string = string.match(/[A-Za-zА-Яабвгдеёжзийклмнопрстуфхцчшщъыьэюя0-9\s-_.]+/);

	if ( r_string != string ) {
		if (typeof callError == "function")
			callError();
			
		return false;
	}
	else {
		if (typeof callback == "function")
			callback();

		return r_string;
	}
}

Regular.sol_symbols_ext = function (string, callback, callError) {
	Regular.calls++;
	var r_string = string.match(/[A-Za-zА-Яабвгдеёжзийклмнопрстуфхцчшщъыьэюя0-9\s-_.,:;!?()\[\]<>\/*+=«»&#№%@|\"]+/);

	if ( r_string != string ) {
		if (typeof callError == "function")
			callError();
			
		return false;
	}
	else {
		if (typeof callback == "function")
			callback();

		return r_string;
	}
}

Regular.reg_mail = function (string, callback, callError) {
	Regular.calls++;
	var r_string = string.match(/([A-Za-z0-9_\-\.]+)([@])([A-Za-z0-9\-\.]+)\.([A-Za-z]{1,6})/);
	
	if (r_string instanceof Array)
		r_string = r_string[0];

	if ( r_string != string ) {
		if (typeof callError == "function")
			callError();
			
		return false;
	}
	else {
		if (typeof callback == "function")
			callback();

		return r_string;
	}
}

//2013-10-02
Regular.ext = function(string, callback, callError) {
	Regular.calls++;
	if (
		string.indexOf("style") === -1 && 
		string.indexOf("script") === -1 &&
		string.indexOf("link") === -1 &&
		string.indexOf("meta") === -1
	) {
		return string;
	}
	else {
		if (Error) Error.add("ext: в строке есть данные фразы: style, script, link, meta, Вызов: "+	Regular.calls, Regular.thisFile);
		return false;
	}
}

Regular.mail = function (string, callback, callError) {
	return Regular.reg_mail(string, callback, callError);
}

// jQuery:
//автоматически устанавливает проверку на каждый val() элемента.
//Важное условие! проверяемые элементы должны иметь свойство val()
//array 		- массив элементов-оберток jquery
//arrayErr 		- массив элементов-оберток jquery (элементы-ошибки)
//a_regNames 	- массив строк-названий пользовательских reg-функций (строки)
Regular.seterror = {};
Regular.setresult = {};	//для ассоциативного массива
Regular.setnumresult = {}; //для числового массива
Regular.set = function(array, arrayErr, a_regNames){
	if (a_regNames == undefined) a_regNames = [];
	
	if (arrayErr == undefined) {
		alert("Regular.set: не задан массив группы ошибок");
		return false;
	}
	
	var thiserror = {};
	var thisresult = {};
	var thisnumresult = {};

	if (array) {
		var sendTrue = true;
		var arrayThisText = "";
	
		for (var i=0; i<array.length; i++){
			arrayThisText = "";
			if (a_regNames[i] == undefined) a_regNames[i] = "ext";
			
			if (a_regNames[i] == "num") 			{ arrayThisText = Regular.num(array[i].val());}
			else if (a_regNames[i] == "floats") 	{ arrayThisText = Regular.floats(array[i].val());}
			else if (a_regNames[i] == "eng_num") 	{ arrayThisText = Regular.eng_num(array[i].val());}
			else if (a_regNames[i] == "rus_num") 	{ arrayThisText = Regular.rus_num(array[i].val());}
			else if (a_regNames[i] == "eng_num_sol"){ arrayThisText = Regular.eng_num_sol(array[i].val());}
			else if (a_regNames[i] == "rus_num_sol"){ arrayThisText = Regular.rus_num_sol(array[i].val());}
			else if (a_regNames[i] == "sol_symbols_min"){ arrayThisText = Regular.sol_symbols_min(array[i].val());}
			else if (a_regNames[i] == "sol_symbols_ext"){ arrayThisText = Regular.sol_symbols_ext(array[i].val());}
			else if (a_regNames[i] == "mail") 		{ arrayThisText = Regular.mail(array[i].val());	}
			else { arrayThisText = Regular.ext(array[i].val());}
			
			
			if (array[i].val()){
				if (arrayThisText == false) {
					sendTrue = false;
					
					if (a_regNames[i] == "num") 			{arrayErr[i].html(Language.getText("NumOnly"));}
					else if (a_regNames[i] == "floats") 	{arrayErr[i].html(Language.getText("NumOnly"));}
					else if (a_regNames[i] == "eng_num") 	{arrayErr[i].html(Language.getText("SymbolsNot"));}
					else if (a_regNames[i] == "rus_num") 	{arrayErr[i].html(Language.getText("SymbolsNot"));}
					else if (a_regNames[i] == "eng_num_sol"){arrayErr[i].html(Language.getText("SymbolsNot"));}
					else if (a_regNames[i] == "rus_num_sol"){arrayErr[i].html(Language.getText("SymbolsNot"));}
					else if (a_regNames[i] == "sol_symbols_min"){arrayErr[i].html(Language.getText("SymbolsNot"));}
					else if (a_regNames[i] == "sol_symbols_ext"){arrayErr[i].html(Language.getText("SymbolsNot"));}
					else if (a_regNames[i] == "mail") 		{arrayErr[i].html(Language.getText("FormatMailNot"));}
					else {arrayErr[i].html(Language.getText("SymbolsNot"));}
				}
			}
			else {
				sendTrue = false;
				arrayErr[i].html(Language.getText("FieldEmpty"));
			}
			

			if (sendTrue == false){
				thiserror[i] = {
					"key" : array[i].attr("id"), 
					"regfunction" : a_regNames[i],
					"val" : array[i].val()
				};
			}
			thisresult[array[i].attr("id")] = arrayThisText;
			thisnumresult[i] = arrayThisText;
		}
		
		Regular.seterror = thiserror;
		Regular.setresult = thisresult;
		Regular.setnumresult = thisnumresult;
		return sendTrue;
	}
	else {
		alert("Regular.set: не задан массив группы");
		return false;
	}
};

//по подобию класса в php
Regular.getError = function(){
	return Regular.seterror;
};
//по подобию класса в php
Regular.getResult = function(is_numeral){
	if (is_numeral == undefined) is_numeral = false;
	
	if (is_numeral == false) return Regular.setresult;
	else return Regular.setnumresult;
};