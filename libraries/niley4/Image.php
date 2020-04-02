<?php
namespace niley4;


/**
 * Работа с изображениями
 *
 * <b>Методы</b>
 * - {@link Response::clearResponse} - Очистить массив ответа
 */
class Image extends _Singleton {

    /**
     * Получить изображение, преобразованное в base64
     */
    public function getBase64($src){
        $imageSize = getimagesize($src);
        $imageData = base64_encode(file_get_contents($src));
        $imageSrc = "data:{$imageSize['mime']};base64,{$imageData}";

        return $imageSrc;
    }
}
