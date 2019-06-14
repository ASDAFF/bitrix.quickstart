<?
namespace Altasib\Starterkit\Debug;

class Functions
{
    public static $pathToModule = false;

    public static function getPathToModule(){
        if(!self::$pathToModule){
            self::$pathToModule = dirname(dirname(dirname(__FILE__)));
        }
        return self::$pathToModule;
    }

    public static function clearPre(&$content)
    {
        global $USER;
        if(!self::isDev()){
            if(!$USER->IsAdmin()){
                if(strpos($content,'<pre class="debug"') !== false){
                    $content = preg_replace("#<pre class=\"debug\">(.*?)</pre>#is","",$content);
                }
            }
        }
    }

    public static function changeDevStatus()
    {
        global $APPLICATION;

        if(isset($_REQUEST["PDA"]) && $_REQUEST["PDA"] == "Y"){
            $_SESSION["DEV"] = "Y";
            LocalRedirect($APPLICATION->GetCurDir());
        }

        if(isset($_REQUEST["DEV"]) && $_REQUEST["DEV"] == "Y"){
            $_SESSION["DEV"] = "Y";
            LocalRedirect($APPLICATION->GetCurDir());
        }

        if(isset($_REQUEST["DEV"]) && $_REQUEST["DEV"] == "N"){
            $_SESSION["DEV"] = "N";
            LocalRedirect($APPLICATION->GetCurDir());
        }

    }

    public static function isDev(){
        return isset($_SESSION["DEV"]) && $_SESSION["DEV"] == "Y" ? true : false;
    }

    public static function devTaskOnEpilog()
    {
        if(self::isDev()){
            //задача для dev режима
            // Используюте свои файлы которые надо положить в корне модуля
            if(file_exists(self::getPathToModule() . "/custom/devTaskOnEpilog.php")){
                include_once (self::getPathToModule() . "/custom/devTaskOnEpilog.php");
            }
        }

    }

    public static function devTaskOnPageStart(){
        if(self::isDev()) {
            //задача для dev режима(например переопределение шаблона)
            if(file_exists(self::getPathToModule() ."/custom/devTaskOnPageStart.php")){
                include_once ( self::getPathToModule() . "/custom/devTaskOnPageStart.php");
            }
        }
    }

    public static function checkSendMail($to_addr){
        $subject = "Тест*Test";
        $header = "***Тест***Test***";
        $message = "Тестовое письмо\n\rЕсли пришло,\n\rто все ОК!!!\n\rTest message";
        var_dump(mail($to_addr, $subject, $message, $header));
    }
}

?>