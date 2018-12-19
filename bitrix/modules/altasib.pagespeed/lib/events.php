<?
namespace Altasib\Pagespeed;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Events
{
    public static function onPageStartHandler()
    {
        \Bitrix\Main\Loader::includeModule("altasib.pagespeed");
    }
}