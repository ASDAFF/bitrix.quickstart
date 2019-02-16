<?php
/**
 * Created by Graymur
 * Date: 14.12.2014
 * Time: 12:39
 */

namespace Traits;

trait HasVideo
{
    public function getVideoUrl()
    {
        return $this->getPropValue('VIDEO');
    }

    public function getVideoType()
    {
        $retval = false;
        $video = $this->getPropValue('VIDEO');

        if ($video && preg_match('/youtu/i', $video))
        {
            $retval = 'youtube';
        }
        else if ($video && preg_match('/vimeo/i', $video))
        {
            $retval = 'vimeo';
        }

        return $retval;
    }

    public function getVideoId()
    {
        $retval = false;

        switch ($this->getVideoType())
        {
            case 'youtube':

                preg_match(
                    "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/",
                    $this->getVideoUrl(),
                    $m
                );

                $retval = $m[1];

                break;

            case 'vimeo':

                preg_match(
                    "#(https?://)?(www.)?(player.)?vimeo.com/([a-z]*/)*([0-9]{6,11})[?]?.*#",
                    $this->getVideoUrl(),
                    $m
                );

                $retval = $m[5];

                break;
        }

        return $retval;
    }
}