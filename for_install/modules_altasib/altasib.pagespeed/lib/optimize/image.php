<?
namespace Altasib\Pagespeed\Optimize;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Image
{
    public function lazyloadInit(){
        \CJSCore::Init(array("altasib_pagespeed_lazy_load"));
    }

    public function lazyLoadChangeContent(&$content){
        $images = Page::getImagesPathHtml($content);
        foreach ($images as $image) {
            $content = str_replace($image, 'src="" data-' . $image, $content);
        }
    }
    public function onPrologHandler(){
        self::lazyloadInit();
    }
    public function onEndBufferContentHandler(&$content){
        self::lazyLoadChangeContent($content);
    }
}