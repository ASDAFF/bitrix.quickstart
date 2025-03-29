<?php

namespace Helper\Media;

use сразу;
use ссылка;

/**
 * Использование:
 * $v = new VideoThumb($link);
 * $v->getVideo(); //Ссылка на видео
 * $v->getTitle(); //Название ролика
 * $v->fetchImage($path) //Скачать самое большое превью ролика
 *
 * Прогнать тест:
 * VideoThumb::RunTest()
 */
class VideoThumb
{
    /** Ссылка на ролик */
    protected $link;

    /** Распарсенные части ссылки */
    protected $link_parts;

    /** Видеохостинг */
    protected $hosting;

    /** Идентификатор видео */
    protected $id;

    /** Картинка */
    protected $image;

    /** Название видео */
    protected $title;

    /** Видео */
    protected $video;

    const YOUTUBE = 'youtube';
    const VIMEO = 'vimeo';
    const RUTUBE = 'rutube';

    /** Регулярки для определения видеохостинга и идентификатора ролика */
    protected $regexp = array(
        self::YOUTUBE => array( //Не используются
            '/[http|https]+:\/\/(?:www\.|)youtube\.com\/watch\?(?:.*)?v=([a-zA-Z0-9_\-]+)/i',
            '/[http|https]+:\/\/(?:www\.|)youtube\.com\/embed\/([a-zA-Z0-9_\-]+)/i',
            '/[http|https]+:\/\/(?:www\.|)youtu\.be\/([a-zA-Z0-9_\-]+)/i'
        ),
        self::VIMEO => array( //Не используются
            '/[http|https]+:\/\/(?:www\.|)vimeo\.com\/([a-zA-Z0-9_\-]+)(&.+)?/i',
            '/[http|https]+:\/\/player\.vimeo\.com\/video\/([a-zA-Z0-9_\-]+)(&.+)?/i'
        ),
        self::RUTUBE => array(
            '/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/embed\/([a-zA-Z0-9_\-]+)/i',
            '/[http|https]+:\/\/(?:www\.|)rutube\.ru\/tracks\/([a-zA-Z0-9_\-]+)(&.+)?/i'
        )
    );

    /** Ссылка на RUtube без идентификатора в адресе */
    protected $regexp_rutube_extra = '/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/([a-zA-Z0-9_\-]+)\//i';

    /** Варианты ссылок, которые поддерживаются */
    protected static $test = array(
        'http://youtube.com/watch?v=ShPq2Dmy6X8',
        'http://www.youtube.com/watch?v=6dwqZw0j_jY&feature=youtu.be',
        'http://www.youtube.com/watch?v=cKZDdG9FTKY&feature=channel',
        'www.youtube.com/watch?v=yZ-K7nCVnBI&playnext_from=TL&videos=osPknwzXEas&feature=sub',
        'http://www.youtube.com/embed/ShPq2Dmy6X8?rel=0',
        'http://youtu.be/ShPq2Dmy6X8',
        'youtu.be/6dwqZw0j_jY',
        'http://www.youtu.be/afa-5HQHiAs',

        'vimeo.com/55028438',
        'http://player.vimeo.com/video/55028438?title=0&byline=0&portrait=0&badge=0&color=e1a931',

        'http://rutube.ru/video/6fd81c1c212c002673280850a1c56415/#.UMQYln9yTWQ',
        'http://rutube.ru/video/dec0a58c8cb4d226abc7b1030bbb63b9/?ref=top',
        'rutube.ru/tracks/6032725.html',
        'http://www.rutube.ru/video/embed/6032725',
    );

    /**
     * @param $link      ссылка на видео
     * @param $autostart сразу определить превью и клип
     */
    function __construct($link = null, $autostart = true)
    {
        if ($link) {
            $this->setLink($link);
            if ($autostart) {
                $this->process();
            }
        }
    }

    /** Видеохостинг */
    public function getHosting()
    {
        return $this->hosting;
    }

    /** Идентификатор видео */
    public function getId()
    {
        return $this->id;
    }

    /** Ссылка на превью */
    public function getImage()
    {
        return $this->image;
    }

    /** Ссылка на видео */
    public function getVideo()
    {
        return $this->video;
    }

    /** Название видео */
    public function getTitle()
    {
        return $this->title;
    }

    /** Задать ссылку на видео */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /** Обработка ссылки. Возвращает идентификатор видеохостинга или false */
    public function process()
    {
        if ($this->cleanLink()) {
            if ($this->maybeYoutube()) {
                return self::YOUTUBE;
            }

            if ($this->maybeVimeo()) {
                return self::VIMEO;
            }

            if ($this->maybeRutube()) {
                return self::RUTUBE;
            }
        }

        return false;
    }

    /** Скачать превью. Если не указать имя файла для записи - функция вернет содержимое файла */
    public function fetchImage($filename = null)
    {
        if (!$url = $this->getImage()) {
            return false;
        }

        if (!$res = $this->fetchPage($url)) {
            return false;
        }

        return $filename
            ? file_put_contents($filename, $res)
            : $res;
    }

    /** Проверка и подготовка ссылки и частей */
    protected function cleanLink()
    {
        if (!preg_match('/^(http|https)\:\/\//i', $this->link)) {
            $this->link = 'http://' . $this->link;
        }

        if (!$this->link_parts = parse_url($this->link)) {
            return false;
        }

        return true;
    }

    /** Проверка YOUTUBE */
    protected function maybeYoutube()
    {
        $h = str_replace('www.', '', $this->link_parts['host']);
        $p = isset($this->link_parts['path']) ? $this->link_parts['path'] : false;

        if ('youtube.com' == $h) {
            parse_str($this->link_parts['query'], $q);

            if ('/watch' == $p && !empty($q['v'])) {
                return $this->foundYoutube($q['v']);
            }
            if (0 === strpos($p, '/embed/')) {
                return $this->foundYoutube(str_replace('/embed/', '', $p));
            }
        } elseif ('youtu.be' == $h) {
            return $this->foundYoutube(trim($p, '/'));
        }

        return false;
    }

    /** Проверка VIMEO */
    protected function maybeVimeo()
    {
        $h = str_replace('www.', '', $this->link_parts['host']);
        $p = isset($this->link_parts['path']) ? $this->link_parts['path'] : false;

        if ('vimeo.com' == $h) {
            return $this->foundVimeo(trim($p, '/'));
        } elseif ('player.vimeo.com' == $h && 0 === strpos($p, '/video/')) {
            return $this->foundVimeo(str_replace('/video/', '', $p));
        }

        return false;
    }

    /** Проверка RUTUBE */
    protected function maybeRutube($html = null)
    {
        $link = $html ?: $this->link;

        foreach ($this->regexp[self::RUTUBE] as $regexp) {
            if (preg_match($regexp, $link, $matches)) {
                return $this->foundRutube($matches[1]);
            }
        }

        // Проверка на особенную ссылку RUtube`a
//    if (is_null($html) && preg_match($this->regexp_rutube_extra, $this->link, $matches)) {
//      $html = $this->fetchPage($matches[0]);
//      if ($r = $this->maybeRutube($html)) {
//        return $r;
//      }
//    }

        return false;
    }

    /** Обработка YOUTUBE */
    protected function foundYoutube($id)
    {
        if (empty($id) || strlen($id) != 11) {
            return false;
        }

        $this->hosting = self::YOUTUBE;
        $this->id = $id;
        $this->image = 'http://img.youtube.com/vi/' . $id . '/0.jpg';
        $this->video = 'http://www.youtube.com/embed/' . $id;

        $this->getYoutubeInfo($id);

        return true;
    }

    /** Обработка VIMEO */
    protected function foundVimeo($id)
    {
        if (empty($id) || !is_numeric($id)) {
            return false;
        }

        $this->hosting = self::VIMEO;
        $this->id = $id;
        $this->video = 'http://player.vimeo.com/video/' . $id;

        $this->getVimeoInfo($id);

        return true;
    }

    /** Обработка RUTUBE */
    protected function foundRutube($id)
    {
        $this->hosting = self::RUTUBE;
        $this->id = $id;
        $this->video = 'http://rutube.ru/video/embed/' . $id;

        $this->getRutubeInfo($id);

        return true;
    }

    /** Парсинг XML от RUTUBE и определение превьюхи */
    protected function getRutubeInfo($id)
    {
        if (@$xml = simplexml_load_file("http://rutube.ru/cgi-bin/xmlapi.cgi?rt_mode=movie&rt_movie_id=" . $id . "&utf=1")) {
            $this->title = (string)$xml->title;
            $this->image = (string)$xml->thumbnail_url;
        }
    }

    /** Парсинг XML от VIMEO и определение превьюхи */
    protected function getVimeoInfo($id)
    {
        if (@$xml = simplexml_load_file('http://vimeo.com/api/v2/video/' . $id . '.xml')) {
            $this->title = (string)$xml->video->title;
            $this->image = (string)$xml->video->thumbnail_large ?: $xml->video->thumbnail_medium;
        }
    }

    /** Получение названия ролика */
    protected function getYoutubeInfo($id)
    {
        if (@$xml = simplexml_load_file('http://gdata.youtube.com/feeds/api/videos/' . $id)) {
            $this->title = (string)$xml->title;
        }
    }

    /** Скачивание страницы с помощью CURL */
    protected function fetchPage($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);

        return curl_exec($ch);
    }

    /** Прогоняем тест по видам URL */
    public static function RunTest($links = null)
    {
        if (!is_array($links)) {
            $links = static::$test;
        }

        foreach ($links as $link) {
            $v = new static($link);
            echo "<h1>$link</h1>\n"
                . "<h3>" . $v->getHosting() . "</h3>"
                . "<b>Видео:</b> " . $v->getVideo() . "<br />\n"
                . "<b>Название:</b> " . $v->getTitle() . "<br />\n"
                . "<b>Картинка:</b> " . $v->getImage() . "<hr />\n";
        }
    }
}