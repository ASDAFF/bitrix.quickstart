<?
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock;

Loc::loadMessages(__FILE__);

define("RINFASTAUTH", "Y");

if (!Loader::includeModule("iblock"))
{
    return false;
}

class RinsventImportpictureInclude
{
    function OnProlog(){
        global $APPLICATION,$USER;
        $mod_id = "rinsvent.fastauth";
        if (IsModuleInstalled($mod_id))
        {
            $pathJS = '/bitrix/js/rinsvent.fastauth';
            $pathCSS = '/bitrix/css/rinsvent.fastauth';
            CJSCore::RegisterExt("rinsvent_fastauth", array(
                'js' => $pathJS.'/script.js',
                'css' => $pathCSS.'/style.css'
            ));
        }
    }
}