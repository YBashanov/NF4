var Exits = {
	buttonClick : false,
	send : function (e) {
		if (Exits.buttonClick == false) {
			Exits.buttonClick = true;

			var button = document.getElementById("buttonSubmit");
			var html = button.innerHTML;
			button.innerHTML = Wait.img();

			HTTP.post (
                base_tpl+"ajax/listen/enter/exit_send.php",
				{},
				function (data){
					var res = data.split("|");
					if (res[0] == 1) {
						button.innerHTML = "<span class='green'>" + res[1] + "</span>";
                        location.href = base_url + "enter/";
						return false;
					}
					else if (res[0] == 2) {
						button.innerHTML = "<span class='red'>" + res[1] + "</span>";
					}
					else if (res[0] == 9) {
                        location.href = base_url + "enter/";
						return false;
					}
				}
			);
			Exits.buttonClick = false;
		}
	}
}