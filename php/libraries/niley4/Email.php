<?php

namespace niley4;


/**
 * protocol = mail
 * mailtype = text (html в разработке)
 */
class Email extends _Singleton {
    private $from_name = "";
    private $from_to = "";
    private $reply_name = "";
    private $reply_to = "";
    private $mail_to = "";
    private $mail_to_error = "bjork-xamlo@yandex.ru";
    private $theme = "";
    private $message = "";
    private $pathToFile = "";
    

    /**
     * очистка установленных параметров - для повторной отправки письма
     */
    public function clear() {
        $this->from_name = "";
        $this->from_to = "";
        $this->reply_name = "";
        $this->reply_to = "";
        $this->mail_to = "";
        $this->mail_to_error = "bjork-xamlo@yandex.ru";
        $this->theme = "";
        $this->message = "";
        $this->pathToFile = "";
    }


    /**
     * from_name - название сайта отправителя
     * from_to - адрес отправителя (настоящий) - должен совпадать с адресом хоста отправления
     */
    public function from($from_name, $from_to) {
        $this->from_name = $from_name;
        $this->from_to = $from_to;
    }


    /**
     * reply_name - название сайта отправителя при нажатии "Ответить"
     * reply_to - адрес отправителя для ответа
     */
    public function reply_to($reply_name, $reply_to) {
        $this->reply_name = $reply_name;
        $this->reply_to = $reply_to;
    }


    /**
     * mail_to - адрес получателя
     */
    public function to($mail_to) {
        $this->mail_to = $mail_to;
    }


    /**
     * mail_to_error - адрес получаения ошибок. Не должен совпадать с адресом получателя
     */
    public function to_error($mail_to_error) {
        $this->mail_to_error = $mail_to_error;
    }


    /**
     * тема письма
     */
    public function subject($theme) {
        $this->theme = $theme;
    }


    /**
     * текстовое сообщение
     */
    public function message($message) {
        $this->message = $message;
    }


    /**
     * путь до прикрепленного файла
     */
    public function attach($pathToFile) {
        $this->pathToFile = $pathToFile;
    }



    /**
     * Отправка сообщения с вложением
     */
    public function sendWithAttach() {
        $subject = $this->_prep_q_encoding($this->theme);
        // не работает на сайте gyrnal.ru
        $from = $this->_prep_q_encoding($this->from_name, true) . ' <'.$this->from_to.'>';
        $attachment = $this->_getAttachment($this->pathToFile);


$headers = 'User-Agent:*
Date: Tue, 2 Dec 2020 14:48:16 +0300
From: '.$this->from_name.' <'.$this->from_to.'>
Reply-To: '.$this->reply_name.' <'.$this->reply_to.'>
Mime-Version: 1.0
Content-Type: multipart/mixed; boundary="B_ATC_5fc76a0524a56"';


$body = '--B_ATC_5fc76a0524a56
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit

'.$this->message.'


--B_ATC_5fc76a0524a56
Content-type: image/jpeg; name="myjpeg.jpg"
Content-Disposition: attachment;
Content-Transfer-Encoding: base64

' . $attachment . '

--B_ATC_5fc76a0524a56--';


        if (mail($this->mail_to, $subject, $body, $headers, "-f {$this->mail_to_error}")) {
            return true;
        }
        else {
            return false;
        }
    }


    /**
     * получить файл в виде base64 кодирования для прикрепления к письму
     */
    private function _getAttachment($filename) {
        $fp = fopen($filename, 'r');
        $filesize = filesize($filename) + 1;
        $attachment = chunk_split(base64_encode(fread($fp, $filesize)));
        fclose($fp);

        return $attachment;
    }


    /**
     * Q-кодирование
     */
    private function _prep_q_encoding($str, $from = FALSE)
    {
        $str = str_replace(array("\r", "\n"), array('', ''), $str);

        // Line length must not exceed 76 characters, so we adjust for
        // a space, 7 extra characters =??Q??=, and the charset that we will add to each line
        $limit = 75 - 7 - strlen($this->charset);

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
                $output .= $temp.$this->crlf;
                $temp = '';
            }

            // Add the character to our temporary line
            $temp .= $char;
        }

        $str = $output.$temp;

        // wrap each line with the shebang, charset, and transfer encoding
        // the preceding space on successive lines is required for header "folding"
        $str = trim(preg_replace('/^(.*)$/m', ' =?'.$this->charset.'?Q?$1?=', $str));

        return $str;
    }
}