<?php


function template($data, $separator = '../'){
    global $base_url;
    global $global;
    if (!isset($data)) {
		$data = array();
	}

	

    $return = "<!DOCTYPE html>
    <html>
    <xml:namespace ns='urn:schemas-microsoft-com:vml' prefix='v'/>
    <head>
    <title>{$data['title']}</title>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
    <base href='/' >
    {$data['css']}
    {$data['server_js']}
    </head>
    <body>
    <table class='external_' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='lateral'></td>
            <td>
                <table class='internal_' cellpadding='0' cellspacing='0' align='center'>
                    <tr>
                        <td class='template'>
                            <div class='page'>
                                <div class='body'>{$data['content']}</div>
                            </div>
                            <div class='copyright'>{$data['copyright']}</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class='lateral lat_right'></td>
        </tr>
    </table>
    {$data['js_bottom']}
    </body>
    </html>";

    return $return;
}
