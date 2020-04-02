var HTTP = new Object();

//пробуем использовать все функции, создающие XMLHttpRequest
HTTP._factories = [
	function() {return new XMLHttpRequest();},
	function() {return new ActiveObject("Msxml2.XMLHTTP");},
	function() {return new ActiveObject("Microsoft.XMLHTTP");}
];

//Когда будет обнаружена работоспособная функция, она будет сохранена здесь
HTTP._factory = null;

//создает и возвращает новый объект XMLHttpRequest
HTTP.newRequest = function() {
	if (HTTP._factory != null) 
		return HTTP._factory();

	for (var i = 0; i<HTTP._factories.length; i++) {
		try {
			var factory = HTTP._factories[i];
			var request = factory();
			if (request != null) {
				HTTP._factory = factory;
				return request;
			}
		}
		catch (e) {
			continue;
		}
	}
	
	//если так и не удалось
	HTTP._factory = function() {
		throw new Error ("Объект XMLHttpRequest не поддерживается");
	}
	HTTP._factory();
}


//использует объект request для получения содержимого по заданному
//URL-адресу методом GET. Получив ответ, передает его (в виде простого текста) 
//указанной функции обратного вызова
HTTP.getText = function (url, callback) {
	var request = HTTP.newRequest();
	//зарегистрировать обработчик события
	request.onreadystatechange = function() {
		if (request.readyState == 4 && request.status == 200) {
			callback (request.responseText);
		}
	}
	request.open ("GET", url);
	request.send (null);
}

HTTP.getXML = function (url, callback) {
	var request = HTTP.newRequest();
	request.onreadystatechange = function() {
		if (request.readyState == 4 && request.status == 200) {
			callback (request.responseXML);
		}
	}
	request.open ("GET", url);
	request.send (null);
}

//head - возможность проверить только заголовки
HTTP.getHeaders = function (url, callback, errorHandler) {
	var request = HTTP.newRequest();
	request.onreadystatechange = function() {
		if (request.readyState == 4 ) {
			if (request.status == 200) {
				callback (HTTP.parseHeaders (request));
			}
			else {
				if (errorHandler) 
					errorHandler (request.status, request.statusText);
				else
					callback (null);
			}
		}
	}
	request.open ("HEAD", url);
	request.send (null);
}
//анализирует заголовки ответа, полученные в XMLHttpRequest 
// и возвращает имена и значения в виде свойств нового объекта
HTTP.parseHeaders = function (request) {
	var headerText = request.getAllResponseHeaders();//текст от сервера
	var headers = {}; //возвращаемое значение
	var ls = /^\s*/; //удаляем начальные пробелы
	var ts = /\s*$/; //удаляем конечные пробелы
	
	//разбить заголовки на строки
	var lines = headerText.split("\n");
	//цикл по всем строкам
	for (var i = 0; i < lines.length; i++) {
		var line = lines[i];
		if (line.length == 0)
			continue; //пропустить пустые строки
		
		//разбить каждую строку по первому двоеточию и удалить лишние пробелы
		var pos = line.indexOf (":");
		var name = line.substring (0, pos).replace (ls, "").replace (ts, "");
		var value = line.substring (pos+1).replace (ls, "").replace (ts, "");
		//сохранить пару имя-значение в виде свойства объекта
		headers[name] = value;
	}
	return headers;
}

//POST
HTTP.post = function (url, values, callback, errorHandler) {
	var request = HTTP.newRequest();
	request.onreadystatechange = function() {
		if (request.readyState == 4 ) {
			if (request.status == 200) {
				callback (HTTP._getResponse (request));
			}
			else {
				if (errorHandler) 
					errorHandler (request.status, request.statusText);
				else
					callback (null);
			}
		}
	}
	request.open("POST", url);

	//этот заголовок сообщает серверу, как интерпретировать тело запроса
	request.setRequestHeader ("Content-Type", "application/x-www-form-urlencoded");
	
	//вставить в тело запроса имена и значения свойств объекта
	request.send (HTTP.encodeFormData (values));
}

//интерпретирует имена и значения свойств объекта, как если бы они были значениями элементов формы
// использует формат application/x-www-form-urlencoded
HTTP.encodeFormData = function (data) {
	var pairs =[];
	var regexp = /%20/g; //закодированный пробел
	for (var name in data) {
		var value = data[name].toString();
		
		//чтобы пробелы правильно преобразовались
		var pair = encodeURIComponent(name).replace(regexp, "+") + "=" +
					encodeURIComponent(value).replace(regexp, "+");
		pairs.push(pair);
	}
	
	//объединить все пары в строку, разделяя символами &
	return pairs.join('&');
}

//определяет форму представления ответа
HTTP._getResponse = function (request) {
	switch (request.getResponseHeader ("Content-Type")) {
		case "text/xml":
			return request.responseXML;
		case "text/json":
		case "text/javascript":
		case "application/javascript":
		case "application/x-javascript":
			//только если добропорядочность сервера не вызывает сомнений! 112
			//return eval (request.responseText);
			return request.responseText;
		default:
			return request.responseText;
	}
}

//GET
//усовершенствованная функция (getText)
HTTP.get = function (url, callback, options) {
	var request = HTTP.newRequest();
	var n = 0;
	var timer;
	if (options.timeout) {
		timer = setTimeout (function(){
				request.abort();
				if (options.timeoutHandler)
					options.timeoutHandler (url);
			},
			options.timeout);
	}
	
	request.onreadystatechange = function() {
		if (request.readyState == 4) {
			if (timer) clearTimeout (timer);
			if (request.status == 200) {
				callback (HTTP._getResponse (request));
			}
			else {
				if (options.errorHandler)
					options.errorHandler (request.status, request.statusText);
				else
					callback (null);
			}
		}
		else if (options.progressHandler) {
			options.progressHandler (++n);
		}
	}
	var target = url;
	if (options.parameters)
		target += "?" + HTTP.encodeFormData (options.parameters)
	request.open ("GET", target);
	request.send (null);
}