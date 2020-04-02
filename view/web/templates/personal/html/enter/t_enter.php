<?php
$thisFile = "html/t_enter";
//контент для верхней части

$in = "<link href='{$path_tpl}templates/personal/style/enter/t_enter.css' rel='stylesheet' type='text/css' />";

if (! $global['authorization']) {

    $in .= "
    <div class='enter_form'>
        <div class='headForm'>
            Вход в личный кабинет
        </div>
        <div class='longLine'></div>

        <form onsubmit='Enter.send();return false'>
            <div class='block'>
                <div class='name'>Логин</div>
                <input class='text' id='login' type='text' />
                <div class='error' id='loginErr'></div>
            </div>
            <div class='cle_left'></div>

            <div class='block'>
                <div class='name'>Пароль</div>
                <input class='text' id='pass' type='password' />
                <div class='error' id='passErr'></div>
            </div>
            <div class='cle_left'></div>

            <div id='buttonSubmit' class='block center'>
                <input type='submit' class='submit' value='Войти' onclick='Enter.send()'/>
            </div>

            <div class='error center' id='error'></div>
            <div class='cle_left'></div>

            <div>
                <div class='register'><a href='{$base_url}registration/'>Регистрация</a></div>
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
    <script src='{$path_tpl}js/enter/j_enter.js'></script>
    ";

}
$data['content'] = $in;
