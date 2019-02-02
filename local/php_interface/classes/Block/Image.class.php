<?php
/**
 * User: graymur
 * Date: 27.02.13
 * Time: 17:19
 */

namespace Cpeople\Classes\Block;

class Image extends File
{
    private $thumbFunc = 'cp_get_thumb_url';

    public function getImageUrl()
    {
        return $this->fetchSrc();
    }

    public function getThumbUrl($options)
    {
        return call_user_func($this->thumbFunc, $this->fetchSrc(), $options);
    }
}
