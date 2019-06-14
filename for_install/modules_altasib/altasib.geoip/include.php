<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Andrew N. Popov                  #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2010 ALTASIB             #
#################################################
?>
<?
global $DBType;
IncludeModuleLangFile(__FILE__);

$arClassesList = array(
        // main classes
        "ALX_GeoIP"             => "classes/general/geoip.php",
        // API classes

);

// fix strange update bug
if (method_exists(CModule, "AddAutoloadClasses"))
{

        CModule::AddAutoloadClasses(
                "altasib.geoip",
                $arClassesList
        );
}
else
{
        foreach ($arClassesList as $sClassName => $sClassFile)
        {
                require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.geoip/".$sClassFile);
        }
}

?>
