<?php
$thisFile = "html/t_enter";
//контент для верхней части

if (! $global['authorization']) {

$in = "<link href='{$path_tpl}templates/personal/style/enter/t_register.css' rel='stylesheet' type='text/css' />

    <div class='register_form' id='register_form'>
        <div class='headForm'>
            Регистрация
        </div>
        <div class='longLine'></div>

        <form onsubmit='Register.send();return false'>
            <div class='underinput'></div>
            <div class='block'>
                <div class='name'>Логин</div>
                <input class='text' id='login' type='text' onkeyup='Register.searchLogin()'/>
                <div class='error' id='loginErr'></div>
            </div>
            <div class='cle_left'></div>
            <div class='block'>
                <div class='name'>Пароль</div>
                <input class='text' id='pass' type='password' />
                <div class='error' id='passErr'></div>
            </div>
            <div class='cle_left'></div>
            <div class='block'>
                <div class='name'>Пароль (повтор)</div>
                <input class='text' id='pass2' type='password' />
                <div class='error' id='pass2Err'></div>
            </div>
            <div class='cle_left'></div>
            <div class='block'>
                <div class='name'>E-mail</div>
                <input class='text' id='mail' type='text' />
                <div class='error' id='mailErr'></div>
            </div>
            <div class='cle_left'></div>
            <div class='block'>
                <div id='buttonSubmit' class='block'>
                    <input type='submit' class='submit' value='Отправить'/>
                </div>
                <div class='error' id='error'></div>
            </div>
            <div class='cle_left'></div>

            <div>
                <div class='register'><a href='{$base_url}enter/'>Войти в систему</a></div>
                <div class='remember'><a href='{$base_url}remember/'>Забыли пароль?</a></div>
            </div>
            <div class='cle_left'></div>
            <div class='cle_right'></div>
        </form>

        <div class='cle_left'></div>
    </div>

    <script src='{$path_tpl}js/classes/Wait.js'></script>
    <script src='{$path_tpl}js/classes/Regular.js'></script>
    <script src='{$path_tpl}js/classes/HTTP.js'></script>
    <script src='{$path_tpl}js/enter/j_register.js'></script>
    ";

}
$data['content'] = $in;
