<?php
$thisFile = "html/t_exits";
//контент для верхней части


$in = "
<link href='{$path_tpl}templates/personal/style/enter/t_exit.css' rel='stylesheet' type='text/css' />
<div class='exit_form'>
	<div id='buttonSubmit' class='block' title='Выйти из системы'>
		<button class='submit' onclick='Exits.send()'>Выход</button>
	</div>
	<div class='error' id='error'></div>
</div>
<script src='{$path_tpl}js/classes/Wait.js'></script>
<script src='{$path_tpl}js/classes/HTTP.js'></script>
<script src='{$path_tpl}js/enter/j_exit.js'></script>
";

$data['exit'] = $in;
