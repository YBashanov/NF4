<?php


/**
 * Словарь регионов
 *
 *
 * <b>Методы</b>
 *
 * {@link Regions::getData()} - получить весь словарь
 *
 * {@link Regions::getLabel()} - получить label по значению
 */
class Regions {
    function __constructor(){}


    // value это index
    private $data = [
        ["value" => "101700", "label" => "Москва"],
        ["value" => "190700", "label" => "Санкт-Петербург"],
        ["value" => "656700", "label" => "Алтайский край"],
        ["value" => "675700", "label" => "Амурская область"],
        ["value" => "163700", "label" => "Архангельская область"],
        ["value" => "414700", "label" => "Астраханская область"],
        ["value" => "308700", "label" => "Белгородская область"],
        ["value" => "241700", "label" => "Брянская область"],
        ["value" => "600700", "label" => "Владимирская область"],
        ["value" => "400700", "label" => "Волгоградская область"],
        ["value" => "160700", "label" => "Вологодская область"],
        ["value" => "394700", "label" => "Воронежская область"],
        ["value" => "679700", "label" => "Еврейская автономная область"],
        ["value" => "672700", "label" => "Забайкальский край"],
        ["value" => "153700", "label" => "Ивановская область"],
        ["value" => "664700", "label" => "Иркутская область"],
        ["value" => "360700", "label" => "Кабардино-Балкарская республика"],
        ["value" => "236700", "label" => "Калининградская область"],
        ["value" => "248700", "label" => "Калужская область"],
        ["value" => "683700", "label" => "Камчатский край"],
        ["value" => "369700", "label" => "Карачаево-Черкесская республика"],
        ["value" => "650700", "label" => "Кемеровская область"],
        ["value" => "610700", "label" => "Кировская область"],
        ["value" => "156700", "label" => "Костромская область"],
        ["value" => "350700", "label" => "Краснодарский край"],
        ["value" => "660700", "label" => "Красноярский край"],
        ["value" => "640700", "label" => "Курганская область"],
        ["value" => "305700", "label" => "Курская область"],
        ["value" => "398700", "label" => "Липецкая область"],
        ["value" => "685700", "label" => "Магаданская область"],
        ["value" => "144700", "label" => "Московская область"],
        ["value" => "183700", "label" => "Мурманская область"],
        ["value" => "166100", "label" => "Ненецкий автономный округ"],
        ["value" => "603700", "label" => "Нижегородская область"],
        ["value" => "173700", "label" => "Новгородская область"],
        ["value" => "630700", "label" => "Новосибирская область"],
        ["value" => "644700", "label" => "Омская область"],
        ["value" => "460700", "label" => "Оренбургская область"],
        ["value" => "302700", "label" => "Орловская область"],
        ["value" => "440700", "label" => "Пензенская область"],
        ["value" => "614700", "label" => "Пермский край"],
        ["value" => "690700", "label" => "Приморский край"],
        ["value" => "180700", "label" => "Псковская область"],
        ["value" => "385700", "label" => "Республика Адыгея"],
        ["value" => "649700", "label" => "Республика Алтай"],
        ["value" => "450700", "label" => "Республика Башкортостан"],
        ["value" => "670700", "label" => "Республика Бурятия"],
        ["value" => "367700", "label" => "Республика Дагестан"],
        ["value" => "386700", "label" => "Республика Ингушетия"],
        ["value" => "358700", "label" => "Республика Калмыкия"],
        ["value" => "185700", "label" => "Республика Карелия"],
        ["value" => "167700", "label" => "Республика Коми"],
        ["value" => "295700", "label" => "Республика Крым"],
        ["value" => "424700", "label" => "Республика Марий Эл"],
        ["value" => "430700", "label" => "Республика Мордовия"],
        ["value" => "677700", "label" => "Республика Саха (Якутия)"],
        ["value" => "362700", "label" => "Республика Северная Осетия-Алания"],
        ["value" => "421700", "label" => "Республика Татарстан"],
        ["value" => "667700", "label" => "Республика Тыва"],
        ["value" => "655400", "label" => "Республика Хакасия"],
        ["value" => "344700", "label" => "Ростовская область"],
        ["value" => "390700", "label" => "Рязанская область"],
        ["value" => "443700", "label" => "Самарская область"],
        ["value" => "410700", "label" => "Саратовская область"],
        ["value" => "693700", "label" => "Сахалинская область"],
        ["value" => "620700", "label" => "Свердловская область"],
        ["value" => "214700", "label" => "Смоленская область"],
        ["value" => "355700", "label" => "Ставропольский край"],
        ["value" => "392700", "label" => "Тамбовская область"],
        ["value" => "170700", "label" => "Тверская область"],
        ["value" => "634700", "label" => "Томская область"],
        ["value" => "300700", "label" => "Тульская область"],
        ["value" => "625700", "label" => "Тюменская область"],
        ["value" => "426700", "label" => "Удмуртская республика"],
        ["value" => "432700", "label" => "Ульяновская область"],
        ["value" => "680700", "label" => "Хабаровский край"],
        ["value" => "628700", "label" => "Ханты-Мансийский автономный округ"],
        ["value" => "454700", "label" => "Челябинская область"],
        ["value" => "364700", "label" => "Чеченская республика"],
        ["value" => "428700", "label" => "Чувашская республика"],
        ["value" => "689700", "label" => "Чукотский автономный округ"],
        ["value" => "629100", "label" => "Ямало-Ненецкий автономный округ"],
        ["value" => "150700", "label" => "Ярославская область"]
    ];


    /**
     * Получить весь словарь
     */
    function getData() {
        return $this->data;
    }


    /**
     * Получить label по известному значению value
     */
    function getLabel($value) {
        $count = count($this->data);

        for ($i=0; $i<$count; $i++) {
            $item = $this->data[$i];

            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
    }
}