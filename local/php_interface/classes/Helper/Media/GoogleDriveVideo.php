<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 04.04.2019
 * Time: 14:50
 */

namespace Helper\Media;

/**
 * Class GoogleDriveVideo
 *
 * Класс для работы с видео Google Drive
 *
 * Использование:
 * $driveGoogle = new GoogleDriveVideo($arItem["PROPERTIES"]["VIDEO"]["VALUE"]);
 * $imgGoogle = $driveGoogle->GetPreview();
 * $linkGoogle = $driveGoogle->GetLink();
 *
 */

class GoogleDriveVideo
{
    function __construct($video)
    {
        $this->video = $video;
    }

    private function Prefix()
    {
        if (preg_match('/(?:www\.|)drive\.google\.com\/open\?(?:.*)?id=([a-zA-Z0-9_\-]+)/i', $this->video, $matches)
            || preg_match('/(?:www\.|)drive\.google\.com\/file\/d\/([a-zA-Z0-9_\-]+)/i', $this->video, $matches)
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
            $preview = 'https://lh3.google.com/u/0/d/' . $prefix . '=w480-h360-p-k-nu-iv1';
            return $preview;
        } else {
            return false;
        }
    }

    public function GetLink()
    {
        $prefix = $this->Prefix();
        if ($prefix) {
            $video = 'https://drive.google.com/file/d/' . $prefix . '/preview';
            return $video;
        } else {
            return false;
        }
    }
}
