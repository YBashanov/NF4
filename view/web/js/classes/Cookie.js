/*
 * некорректно работает в Safari: куки с запятыми не записываются
**/

if (! Cookie) var Cookie = {
	thisFile : "Cookie.js",

	//timeLive - время жизни, в секундах. Если не указано или = false, устанавливается до закрытия браузера
	//rewrite=true - если существуют, переписать принудительно под тем же именем (=false - если существуют, пропускаем)
	set_cookie : function (name, val, timeLive, rewrite){
		if (timeLive == undefined) timeLive = false;
		if (rewrite == undefined) rewrite = false;
		
		function _set_cookie(name, val, timeLive){
			if (timeLive == false) {
				document.cookie = ""+name+"="+val+"; path=/;";
			}
			else {
				var cookie_date = new Date();
				cookie_date.setSeconds(timeLive+cookie_date.getSeconds());
				document.cookie = ""+name+"="+val+"; expires="+cookie_date.toUTCString()+"; path=/;";
			}
		}
		
		if (this.is_cookie_set()){
			if (rewrite == false) {
				if (this.get_cookie(name)) {
					return false;
				}
				else {
					_set_cookie(name, val, timeLive);
					return true;
				}
			}
			else {
				_set_cookie(name, val, timeLive);
				return true;
			}
		}
	},
	
	//проверить, установлены ли куки с таким именем
	//если имя не задано - возвращает все
	get_cookie : function (name) {
		if (this.is_cookie_set()){
			if (name == undefined){
				return document.cookie;
			}
			else {
				var s_parent = document.cookie;
				var s_split = s_parent.split("; ");
				var s_split_td = {};
				var value = false;
				var is_set = false;
				
				for(var i=0; i<s_split.length; i++){
					s_split_td[i] = s_split[i].split("=");
					if (s_split_td[i][0] == name) {
						value = s_split_td[i][1];
						is_set = true;
						break;
					}
				}
				
				if (is_set == true) {
					return value;
				}
				else {
					return false;
				}
			}
		}
		else {
		    return false;
        }
	},
	
	delete_cookie : function (name) {
		if (this.is_cookie_set()){
			var cookie_date = new Date();  // Текущая дата и время
			cookie_date.setTime(cookie_date.getTime() - 1);
			document.cookie = ""+name+"=; expires=" + cookie_date.toUTCString();
		}
	},
	
	//проверим на включение в браузере
	is_cookie_set : function(){
		if (navigator && navigator.cookieEnabled){
			return true;
		}
		else {
			//Error.add("Cookie отключены", this.thisFile);
			return false;
		}
	}
};