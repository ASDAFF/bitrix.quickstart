<?
namespace Rinsvent\Fastauth\Event;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Functions
{
    static function showForm(){
        global $APPLICATION, $USER;
        \CJSCore::Init();
        \CJSCore::Init(array("jquery","ajax", 'json', 'ls', 'session', 'popup', 'pull'));
        $APPLICATION->AddHeadScript("/bitrix/js/rinsvent.fastauth/rinsvent.fastauth.js");
        $APPLICATION->SetAdditionalCSS("/bitrix/css/rinsvent.fastauth/rinsvent.fastauth.css");

        $isUseFa = $APPLICATION->get_cookie("UF_RINSVENT_FA_USE",false);

        if($isUseFa == "Y"){
            $strOption = "";
            if($USER->IsAuthorized()){
                $strOption = "{condition:\"AUTH\"}";
            }
            $APPLICATION->AddHeadString("
                <script>
                    $(function(){
                        $('body').rinsventFastauth(".$strOption.");
                    });
                </script>
            ");
        }
    }
}