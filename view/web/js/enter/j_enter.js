var Enter = {
	buttonClick : false,
	send : function () {
		if (Enter.buttonClick == false) {
			Enter.buttonClick = true;
			var dataTrue = true;

			var button = document.getElementById("buttonSubmit");
			var buttonHTML = button.innerHTML;
			button.innerHTML = Wait.img();
			var error = document.getElementById ("error");
			error.innerHTML = "";

			var login 	= document.getElementById("login");
			var pass 	= document.getElementById("pass");
			var loginErr 	= document.getElementById("loginErr");
			var passErr 	= document.getElementById("passErr");
			loginErr.innerHTML = "";
			passErr.innerHTML = "";
			
			var loginVal 	= Regular.sol_symbols_ext(login.value);
			var passVal 	= Regular.sol_symbols_ext(pass.value);

			if (login.value.length == 0) {
				loginErr.innerHTML = "Пустое поле";
				dataTrue = false;
			}
			else if (loginVal == false) {
				loginErr.innerHTML = "Некорректные символы";
				dataTrue = false;
			}
			
			if (pass.value.length == 0) {
				passErr.innerHTML = "Пустое поле";
				dataTrue = false;
			}
			else if (passVal == false) {
				passErr.innerHTML = "Некорректные символы";
				dataTrue = false;
			}

			if (dataTrue) {
				HTTP.post (
                    base_tpl+"ajax/listen/enter/enter_send.php",
					{
						"login": loginVal,
						"pass" : passVal
					},
					function (data){
						button.innerHTML = buttonHTML;

						if (data) {
							var res = data.split("|");
							if (res[0] == 1) {
								error.innerHTML = "<span class='green'>" + res[1] + "</span>";
								location.href = base_url + "personal/";
								return false;
							}
							else if (res[0] == 2) {
								error.innerHTML = res[1];
							}
							else if (res[0] == 9) {
								location.href = base_url + "enter/";
								return false;
							}
						}
						else {
							return false;
						}
					}
				);
			}
			else {
				button.innerHTML = buttonHTML;
			}
			Enter.buttonClick = false;
		}
	}
}