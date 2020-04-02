var Error = {
	add : function(text, file){
		if (file == undefined) file = "";
		HTTP.post (
            base_tpl+"errorWrite.php",
			{
				"text":text,
				"file":file
			},
			function (data){}
		);
	}
};