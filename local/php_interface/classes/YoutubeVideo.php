<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 04.06.2018
 * Time: 0:50
 */


/**
 * Class YoutubeVideo
 *
 * Класс для работы с видео Youtube
 */
class YoutubeVideo
{
    function __construct($video)
    {
        $this->video = $video;
    }

    private function Prefix()
    {
        if (preg_match('/[http|https]+:\/\/(?:www\.|)youtube\.com\/watch\?(?:.*)?v=([a-zA-Z0-9_\-]+)/i', $this->video, $matches)
            || preg_match('/(?:www\.|)youtube\.com\/embed\/([a-zA-Z0-9_\-]+)/i', $this->video, $matches)
            || preg_match('/(?:www\.|)youtu\.be\/([a-zA-Z0-9_\-]+)/i', $this->video, $matches)
        ) {
            return $matches[1];
        } else {
            return false;
        }
    }

    public function GetImage()
    {
        $prefix = $this->Prefix();
        if ($prefix)
        {
            $image = 'http://img.youtube.com/vi/' . $prefix . '/0.jpg';
            $arFile = CFile::MakeFileArray($image); // функция Битрикс
            return $arFile;
        }
        else
        {
            return false;
        }
    }

    public function GetLink()
    {
        $prefix = $this->Prefix();
        if ($prefix)
        {
            $video = 'https://www.youtube.com/embed/' . $prefix . '';
            return $video;
        }
        else
        {
            return false;
        }
    }
}
