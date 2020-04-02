var Wait = {
	waitWorld : "Подождите",
	waitSymbol : ".",
	waitInterval : false,

	plus : "<div class='plus'>+</div>",
	minus : "<div class='minus'>-</div>",
	
	img : function (width) {
		if (width == undefined) width = 20;
		return "<img src='"+base_tpl+"/templates/default/image/wait/wait.gif' width='"+width+"'/>";
	},
	
	backgImg : function (element, path_to_img) {
		if (element) {
			if (path_to_img == undefined) path_to_img = "wait/input_wait.png";

			element.style.backgroundImage = "url("+base_tpl+"/templates/default/image/"+path_to_img+")";
			element.style.backgroundPosition = "0px 0px";

			var i=0;
			Wait.waitInterval = setInterval(function(){
				element.style.backgroundPosition = i+"px 0px";
				i++;
			}, 50);
		}
		else return false;
	},
	
	backImgStop : function (element) {
		clearInterval(Wait.waitInterval);
		Wait.waitInterval = false;
		if (element) {
			element.style.backgroundImage = "url()";
			element.style.backgroundPosition = "0px 0px";
		}
	},
	
	text : function (str){
		if (str == undefined) str = "Подождите...";
		return str;
	},
	
	td : function (str, className){
		if (className == undefined) className = "";
		return "<div class='td tdSum"+className+"'>"+str+"</div><div class='cle'></div>";
	},
	
	//подождите... бегут точки
	start : function(element){
		var i = 0;
		var remember = Wait.waitWorld;
		Wait.waitInterval = setInterval(function(){
			remember += Wait.waitSymbol;
			element.innerHTML = remember;
			i++;
			if (i >= 4) {
				remember = Wait.waitWorld;
				i = 0;
			}
		}, 500);
	},
	stop : function(){
		clearInterval(Wait.waitInterval);
		Wait.waitInterval = false;
	}
}