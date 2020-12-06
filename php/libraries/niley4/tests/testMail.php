<?php

function v($some) {
    echo '<pre>';
    var_dump($some);
    echo '</pre>';
}




/**
 * Q-кодирование
 */
function _prep_q_encoding($str, $from = FALSE) {
    $charset = "utf-8";
    $crlf	 = "\n";

    $str = str_replace(array("\r", "\n"), array('', ''), $str);

    // Line length must not exceed 76 characters, so we adjust for
    // a space, 7 extra characters =??Q??=, and the charset that we will add to each line
    $limit = 75 - 7 - strlen($charset);

    // these special characters must be converted too
    $convert = array('_', '=', '?');

    if ($from === TRUE)
    {
        $convert[] = ',';
        $convert[] = ';';
    }

    $output = '';
    $temp = '';

    for ($i = 0, $length = strlen($str); $i < $length; $i++)
    {
        // Grab the next character
        $char = substr($str, $i, 1);
        $ascii = ord($char);

        // convert ALL non-printable ASCII characters and our specials
        if ($ascii < 32 OR $ascii > 126 OR in_array($char, $convert))
        {
            $char = '='.dechex($ascii);
        }

        // handle regular spaces a bit more compactly than =20
        if ($ascii == 32)
        {
            $char = '_';
        }

        // If we're at the character limit, add the line to the output,
        // reset our temp variable, and keep on chuggin'
        if ((strlen($temp) + strlen($char)) >= $limit)
        {
            $output .= $temp.$crlf;
            $temp = '';
        }

        // Add the character to our temporary line
        $temp .= $char;
    }

    $str = $output.$temp;

    // wrap each line with the shebang, charset, and transfer encoding
    // the preceding space on successive lines is required for header "folding"
    $str = trim(preg_replace('/^(.*)$/m', ' =?'.$charset.'?Q?$1?=', $str));

    return $str;
}

function attachment() {
    $filename = __DIR__ . "/test.jpg";

    $fp = fopen($filename, 'r');
    $filesize = filesize($filename) + 1;
    $attachment = chunk_split(base64_encode(fread($fp, $filesize)));
    fclose($fp);

    return $attachment;
}




$subject = _prep_q_encoding("Тема сообщения");
$from = _prep_q_encoding("Журнал «Образование и наука в России и за рубежом»", true) . ' <gyrnal@srv122-h-st.jino.ru>';

$headers = 'User-Agent:*
Date: Tue, 2 Dec 2020 14:48:16 +0300
';

//$headers .= 'From: gyrnal';
//$headers .= 'From: Журнал <gyrnal@srv122-h-st.jino.ru>';
$headers .= 'From: ' . $from;

$headers .= '
Subject: ' . $subject . '
Reply-To: gyrnal@bk.ru
Return-Path: bjorkss@mail.ru
Mime-Version: 1.0
Content-Type: multipart/mixed; charset=UTF-8
Content-Type: multipart/mixed; boundary="B_ATC_5fc76a0524a56"';

$myImage = attachment();
$body = '--B_ATC_5fc76a0524a56
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit

текст письма здесь
текстовое письмо, а ниже будет прикрепленная картинка


--B_ATC_5fc76a0524a56
Content-type: image/jpeg; name="myjpeg.jpg"
Content-Disposition: attachment;
Content-Transfer-Encoding: base64

' . $myImage . '

--B_ATC_5fc76a0524a56--';


v(mail("bjorkss@mail.ru", "", $body, $headers));





