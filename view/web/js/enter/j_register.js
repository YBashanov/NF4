var Register = {
	buttonClick : false,
	wait : Wait.img(),
	send : function () {
		if (Register.buttonClick == false) {
			Register.buttonClick = true;
			var dataTrue = true;
			
			var button = document.getElementById("buttonSubmit");
			var buttonHTML = button.innerHTML;
			button.innerHTML = Register.wait;
			var error = document.getElementById ("error");
			error.innerHTML = "";

			var login 	= document.getElementById("login");
			var pass 	= document.getElementById("pass");
			var pass2 	= document.getElementById("pass2");
			var mail 	= document.getElementById("mail");
			//var captcha = document.getElementById("captcha");

			var loginErr 	= document.getElementById("loginErr");
			var passErr 	= document.getElementById("passErr");
			var pass2Err 	= document.getElementById("pass2Err");
			var mailErr 	= document.getElementById("mailErr");
			//var captchaErr 	= document.getElementById("captchaErr");
			loginErr.innerHTML = "";
			passErr.innerHTML = "";
			pass2Err.innerHTML = "";
			mailErr.innerHTML = "";
			//captchaErr.innerHTML = "";
			
			var loginVal 	= Regular.sol_symbols_ext(login.value);
			var passVal 	= Regular.sol_symbols_ext(pass.value);
			var pass2Val 	= Regular.sol_symbols_ext(pass2.value);
			var mailVal 	= Regular.mail(mail.value);
			//var captchaVal	= Regular.sol_symbols_ext(captcha.value);
			
			if (login.value.length == 0) {
				loginErr.innerHTML = "Пустое поле";
				dataTrue = false;
			}
			else if (loginVal == false) {
				loginErr.innerHTML = "Неверные символы";
				dataTrue = false;
			}
			
			if (pass.value.length == 0) {
				passErr.innerHTML = "Пустое поле";
				dataTrue = false;
			}
			else if (passVal == false) {
				passErr.innerHTML = "Неверные символы";
				dataTrue = false;
			} 
			
			if (pass2.value.length == 0) {
				pass2Err.innerHTML = "Пустое поле";
				dataTrue = false;
			}
			else if (pass2Val == false) {
				pass2Err.innerHTML = "Неверные символы";
				dataTrue = false;
			}
			else if (passVal.toString() != pass2Val.toString()) {
				pass2Err.innerHTML = "Пароли не совпадают";
				dataTrue = false;
			}
			
			if (mail.value.length == 0) {
				mailErr.innerHTML = "Пустое поле";
				dataTrue = false;
			}
			else if (mailVal == false) {
				mailErr.innerHTML = "Некорректный формат e-mail";
				dataTrue = false;
			}
			
			//if (captcha.value.length == 0) {
			//	captchaErr.innerHTML = "Пустое поле";
			//	dataTrue = false;
			//}
			//else if (captchaVal == false) {
			//	captchaErr.innerHTML = "Неверные символы";
			//	dataTrue = false;
			//}
			//else if (captcha.value.length < 5) {
			//	captchaErr.innerHTML = "Мало символов";
			//	dataTrue = false;
			//}
			//else if (captcha.value.length > 5) {
			//	captchaErr.innerHTML = "Много символов";
			//	dataTrue = false;
			//}
			
			if (dataTrue) {
				HTTP.post (
						base_tpl+"ajax/listen/enter/register_send.php",
					{
						"login": loginVal,
						"pass" : passVal,
						"pass2": pass2Val,
						"mail" : mailVal,
						//"captcha": captchaVal
					},
					function (data){
						if (data) {
							error.innerHTML = "";
							var res = data.split("|");
							if (res[0] == 1) {
								var parent = document.getElementById ("register_form");
								parent.innerHTML = res[1];
							}
							else if (res[0] == 2) {
								loginErr.innerHTML = res[1];
								passErr.innerHTML = res[2];
								pass2Err.innerHTML = res[3];
								mailErr.innerHTML = res[4];
								//captchaErr.innerHTML = res[5];
								button.innerHTML = buttonHTML;
							}
							else if (res[0] == 3) {
								error.innerHTML = res[1];
								button.innerHTML = buttonHTML;
							}
							else if (res[0] == 9) {
								location.href = protocol + server;
								return false;
							}
						}
						else {
							error.innerHTML = "Ошибка сервера. Обратитесь в техподдержку.";
							button.innerHTML = buttonHTML;
						}
						
						//if (res[6] == "changePicture"){
						//	Captcha.changePicture();
						//}
					}
				);
			}
			else {
				button.innerHTML = buttonHTML;
			}
			Register.buttonClick = false;
		}
	},
	
	searchLogin : function () {
		if (Register.buttonClick == false) {
			Register.buttonClick = true;
			var dataTrue = true;

			var login = document.getElementById("login");
			var loginErr = document.getElementById("loginErr");
			loginErr.innerHTML = Register.wait;
			
			var loginVal = Regular.sol_symbols_ext(login.value);
			if (login.value.length == 0) {
				loginErr.innerHTML = "Пустое поле";
				dataTrue = false;
			}
			else if (loginVal == false) {
				loginErr.innerHTML = "Неверные символы";
				dataTrue = false;
			}
			
			if (dataTrue) {
				HTTP.post (
						base_tpl+"ajax/listen/enter/changeLogin.php",
					{
						"login": loginVal
					},
					function (data) {
						if (data) {
							var res = data.split("|");
							if (res[0] == 1) {
								loginErr.innerHTML = "<span class='searchLogin'>свободно</span>";
							}
							else if (res[0] == 2) {
								loginErr.innerHTML = res[1];
							}
						}
						else {
							loginErr.innerHTML = "Ошибка сервера";
						}
					}
				);
			}
			
			Register.buttonClick = false;
		}
	}
}