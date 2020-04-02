<?php

function template($data, $separator = '../'){
    global $base_url;
    global $global;
    global $path_tpl;
    if (! is_array($data))	$data = array();

    $return = "<!DOCTYPE html>
    <html>
    <xml:namespace ns='urn:schemas-microsoft-com:vml' prefix='v'/>
    <head>
    <title>{$data['title']}</title>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
    <base href='/' >
    {$data['css']}
    <link href='{$path_tpl}templates/personal/style/personal/font.css' rel='stylesheet' type='text/css' />
    <link href='{$path_tpl}templates/personal/style/personal/menu.css' rel='stylesheet' type='text/css' />
    <link href='{$path_tpl}templates/personal/style/personal/structure.css' rel='stylesheet' type='text/css' />
    <link href='{$path_tpl}templates/personal/style/personal/table.css' rel='stylesheet' type='text/css' />
    {$data['server_js']}
    </head>
    <body>
    <table class='external_' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='personal'>
                <div class='head'>
                    {$data['head']}
                </div>
                <div class='body'>
                    <div class='menuP'>
                        {$data['menuPersonal']}
                        {$data['sort']}
                    </div>
                    <div class='contentP'>
                        {$data['content']}
                    </div>
                    <div class='cle'></div>
                </div>
                <div class='bottom'>{$data['copyright']}</div>
            </td>
        </tr>
    </table>
    {$data['js_bottom']}
    </body>
    </html>";
    return $return;
}
