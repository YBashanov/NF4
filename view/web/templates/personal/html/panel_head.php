<?php

$in = "";
$in .= "
<div class='headpanel'>
	<div class='panelname'>Панель управления cайом: <a class='sitename' target='_blank' href='{$base_url}'>{$global['domainName']}</a></div>
	<div class='panelname'>Вы вошли как: <b>{$global['user']['login']}</b>, права доступа: <b>{$headerVars['users_status'][$global['user']['status']]}</b></div>
	{$data['exit']}
</div>";

$data['head'] = $in;
