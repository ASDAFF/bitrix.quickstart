<?php
/*
 * Copyright (c) 2025 Created by ASDAFF asdaff.asad@yandex.ru
 */

namespace Helper\Media;
class RutubeVideo
{
    function __construct($video)
    {
        $this->video = $video;
    }

    private function Prefix()
    {
        if (preg_match('/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/([a-zA-Z0-9_\-]+)/i', $this->video, $matches)
        ) {
            return $matches[1];
        } else {
            return false;
        }
    }

    public function GetPreview()
    {
        $prefix = $this->Prefix();
        if ($prefix) {
            $preview = 'https://rutube.ru/api/video/' . $prefix . '/thumbnail/?redirect=1';
            return $preview;
        } else {
            return false;
        }
    }

    /**
     * @return false|string
     */
    public function GetLink()
    {
        $prefix = $this->Prefix();
        if ($prefix) {
            $video = 'https://rutube.ru/play/embed/' . $prefix . '/';
            return $video;
        } else {
            return false;
        }
    }
}