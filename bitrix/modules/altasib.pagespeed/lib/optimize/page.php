<?
namespace Altasib\Pagespeed\Optimize;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Page
{
    /*
    \Bitrix\Main\Loader::includeModule("altasib.pagespeed");
    \Altasib\Pagespeed\Optimize\Page::test();
    */
    public function test(){

        global $APPLICATION;
        //p("http://" . $_SERVER["HTTP_HOST"] . $APPLICATION->GetCurPage());
        $currentPage = file_get_contents("http://" . $_SERVER["HTTP_HOST"]);
        p(self::getImages($currentPage));
        p(self::getCss($currentPage));
        p(self::getJs($currentPage));
    }

    public function getImages($content){
        $pattern = '/<img.*?src=[\'\"](.*?)[\'\"].*?>/is';

        return self::getGroupsByPattern(
            $pattern,
            $content
        );
    }

    public function getImagesPathHtml($content){
        $pattern = '/<img.*?(src=[\'\"].*?[\'\"]).*?>/is';

        return self::getGroupsByPattern(
            $pattern,
            $content
        );
    }


    public function getCss($content){
        $pattern = '/<link.*?href=[\'\"](.*?\.css.*?)[\'\"].*?>/is';

        return self::getGroupsByPattern(
            $pattern,
            $content
        );
    }

    public function getJs($content){
        $pattern = '/<script.*?src=[\'\"](.*?\.js.*?)[\'\"].*?>/is';

        return self::getGroupsByPattern(
            $pattern,
            $content
        );
    }

    public function getGroupsByPattern($pattern,$content){

        preg_match_all(
            $pattern,
            $content,
            $matches
        );

        if(isset($matches[1]) && is_array($matches[1]) && count($matches[1]) > 0){
            return $matches[1];
        }

        return false;
    }

}