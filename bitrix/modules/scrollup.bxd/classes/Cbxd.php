<?php

class Cbxd{

    private static  $result = "";

    function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
    {
        $MODULE_ID = basename(dirname(__FILE__));
        $aMenu = array(
            "parent_menu" => "global_menu_settings",
            "section" => $MODULE_ID,
            "sort" => 50,
            "text" => "Bitrix Debug",
            "title" => 'Bitrix Debug Settings',
            "url" => "scrollup.bxd_bxd_admin.php",
            "icon" => "bxd_menu_icon",
            "page_icon" => "bxd_page_icon",
            "items_id" => $MODULE_ID."_items",
            "more_url" => array(),
            "items" => array()
        );

        $aModuleMenu[] = $aMenu;
    }

    function OnProlog(){
        global $APPLICATION;
        $APPLICATION->ShowViewContent("bxd_debug");
    }

    function OnBeforeProlog(){
        $optionJquery = COption::GetOptionString("scrollup.bxd", "SBXD_JQUERY", "false");

        $tmpOptionsArray = array(
            'js' => '/bitrix/js/scrollup.bxd/script.min.js',
            'css' => '/bitrix/themes/.default/scrollup.bxd.min.css'
        );

        if($optionJquery == "true"){
            $tmpOptionsArray['rel'] = array('jquery');
        }

        CJSCore::RegisterExt('bxd', $tmpOptionsArray);
        CJSCore::RegisterExt('bxd_css', Array('css' => '/bitrix/themes/.default/scrollup.bxd.min.css'));

        if(defined('ADMIN_SECTION')){
            CJSCore::Init(array('bxd_css'));
        }

        if($GLOBALS['APPLICATION']->GetCurPage() == '/bitrix/admin/scrollup.bxd_bxd_admin.php' && !empty($_POST)){
            COption::SetOptionString("scrollup.bxd", "SBXD_JQUERY", $_POST["SBXD_JQUERY"]);
            COption::SetOptionString("scrollup.bxd", "SBXD_GROUPS", implode(",", $_POST["SBXD_GROUPS"]));
        }
    }

    static function debug($_, $inPlace = false, $saveToFile = false){
        if(empty($_)) return;

        self::$result = '<kbd id="bxd-r"><kbd>';

        Cbxd::recursive($_);

        $backtrace = debug_backtrace();
        self::$result .= '</kbd><b class="bxd-d">' . $backtrace[2]["file"] . ', ' . $backtrace[2]["line"] . '</b></kbd>';

        if($saveToFile){
            file_put_contents($_SERVER["DOCUMENT_ROOT"]."/bxd_log.txt", self::$result);
        } else {
            if($inPlace){
                CJSCore::Init(array('bxd'));
                echo self::$result;
            } else {
                global $APPLICATION;
                CJSCore::Init(array('bxd'));
                $APPLICATION->AddViewContent("bxd_debug", self::$result, 1);
            }
        }
    }

    static function c($_)
    {
        $_ = json_encode($_);

        $backtrace = debug_backtrace();
        $cp = $backtrace[2]["file"] . ", " . $backtrace[2]["line"];

        $js = <<<JSCODE
            \n<script>
                if (! window.console) console = {};
                console.log = console.log || function(name, data){};
                console.log('$cp');
                console.log($_);
            </script>
JSCODE;

        echo $js;
    }


    static function l(){
        if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bxd_log.txt")){
            $content = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/bxd_log.txt");
            global $APPLICATION;
            CJSCore::Init(array('bxd'));
            $APPLICATION->AddViewContent("bxd_debug", $content, 1);
        }
    }

    private function recursive($_, $__ = NULL){
        switch(gettype($_)){
            case "boolean":
                self::$result .= Cbxd::li("B", 0, $_?"true":"false", true, $__);
                break;
            case "integer":
                self::$result .= Cbxd::li("I", strlen((string)$_), $_, true, $__);
                break;
            //because of historical reason double == float
            case "double":
                self::$result .= Cbxd::li("F", strlen((string)$_), $_, true, $__);
                break;
            case "string":
                self::$result .= Cbxd::li("S", strlen($_), $_, true, $__);
                break;
            case "NULL":
                self::$result .= Cbxd::li("N", 0, $_, true, $__);
                break;
            case "object":
            case "array":
                $type = gettype($_) == "array" ? "A" : "O";
                $count = gettype($_) == "array" ? count($_) : count(get_object_vars($_));
                self::$result .= Cbxd::li($type, $count, "", false, $__);
                self::$result .= "<kbd>";
                    foreach($_ as $k => $v){
                        //recursive call
                        Cbxd::recursive($v, $k);
                    }
                self::$result .= "</kbd>";
                self::$result .= "</b>";
                break;
        }
    }

    private function li($type, $count, $data, $close = true, $name = "..."){
        $count = $count == 0 ? "" : $count;

        $tmp = "";
        if($type == "A" || $type == "O"){
            $tmp .= "<b class=\"bxd-i\"><div><h2>{$name}</h2><i>{$type}</i><s>{$count}</s></div>";
            $name = $name == "" ? "0" : $name;
        } else {
            $tmp .= "<b><h2>{$name}</h2><i>{$type}</i><s>{$count}</s><p>{$data}</p>";
            $name = $name == "" ? "..." : $name;
        }

        $tmp .= $close ? "</b>" : "";
        return $tmp;
    }
}

