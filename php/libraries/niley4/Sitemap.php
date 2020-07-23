<?php
namespace niley4;


/**
 * 2020-06-20
 * Создание карты сайта
 *
 * <b>Методы</b>
 * - {@link Sitemap::setPattern} -
 */
class Sitemap extends _Singleton{
    public $File;
    public $xmlText = "";
    public $sitemapPath = "";
    public $i = 0;
    public $time;
    public $pr = '0.5'; // приоритет


    public static function init($sitemapPath) {
        $instance = parent::init();

        $instance->sitemapPath = $sitemapPath;
        $instance->File = File::init();

        return $instance;
    }


    public function start() {
        $this->time = time();
        $this->i = 0;

        $this->addLog("Sitemap: start");

        // проверяется, пустой ли файл (создан ли файл)
        // файл создался - значит его не было раньше
        if ($this->File->create($this->sitemapPath)) {

            $this->xmlText = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
                .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            return 'empty';
        }
        // файл существовал ранее
        else {
            $this->xmlText = '';

            // из файла убирается последняя строка </urlset>
            $arrayText = $this->File->read($this->sitemapPath);

            // определяем, что </urlset> есть
            $element = $arrayText[count($arrayText) - 2];
            if ($element == "</urlset>") {

                // перезаписываем всё в файл, но без двух последних элементов
                array_pop($arrayText);
                array_pop($arrayText);

                $this->File->write($this->sitemapPath, '', 'ADD_TO_START');
                $this->File->write($this->sitemapPath, $arrayText);
            }
            else {
                $this->addLog("Sitemap: ошибка! в конце не оказалось тега </urlset>");
            }

            return 'not empty';
        }
    }


    public function stop () {
        $this->xmlText .= '</urlset>';
        $this->addLog("Sitemap: stop, Добавлено {$this->i} страниц");

        $this->File->write($this->sitemapPath, $this->xmlText, 'ADD_TO_END');
    }


    /**
     * Создать 1 блок <url>...</url> в sitemap
     *
     * $url - полный урл страницы
     * $time - время обновления (не знаю, как вычислять)
     * $priority - приоритет
     * $changefreq - явное обозначение обновления страницы (weekly, monthly)
     *
     * $sm->loc(BuildUrl('pages', $data), strtotime(empty($data['time']) ? $data['date0'] : $data['time']), 0.5, 'weekly');
     */
    public function setPage($url, $time=null, $priority=null, $changefreq=null){
        if (! $time) {
            $time = $this->time;
        }

        // проверить урл на двойной слэш. Если есть - не пропускаем
        $isDoubleSlash = strpos($url, "//", 9);

        if (! $isDoubleSlash) {
            $this->xmlText .= "<url>
<loc>" . htmlspecialchars($url, ENT_QUOTES) . "</loc>
<lastmod>" . date('Y-m-d', $time) . "</lastmod>
<changefreq>" . ($changefreq ? $changefreq : $this->getFreq($time)) . "</changefreq>
<priority>" . str_replace(',', '.', ($priority ? floatval($priority) : $this->pr)) . "</priority>
</url>\n";

            $this->i++;
        }
        else {
            $this->addLog("Sitemap->setPage: в урле есть двойной слэш: {$url}");
        }
    }


    private function getFreq($time){
        $t = time() - $time;

        if ($t<5400) return 'hourly'; // 60*60*1.5
        elseif ($t<129600)  return 'daily';   // 60*60*24*1.5
        elseif ($t<604800)  return 'weekly';   // 60*60*24*7 - периодичность обновления страницы "еженедельно"
        elseif ($t<2592000) return 'monthly';   // 60*60*24*7
        else return 'yearly';
    }
}
