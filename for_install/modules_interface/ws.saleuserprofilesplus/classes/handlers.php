<?
namespace WS\SaleUserProfilesPlus;

class Handlers{
    static function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu){
        $MODULE_ID = basename(dirname(__FILE__));
        if (file_exists($path = dirname(__FILE__).'/admin')) {
            if ($dir = opendir($path)) {
                while(false !== $item = readdir($dir)) {
                    if (in_array($item,array('.','..','menu.php'))) {
                        continue;
                    }

                    if (!file_exists($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$MODULE_ID.'_'.$item)) {
                        file_put_contents($file,'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.$MODULE_ID.'/admin/'.$item.'");?'.'>');
                    }
                }
            }
        }

    }
}
?>