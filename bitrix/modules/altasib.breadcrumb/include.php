<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Eremchenko Alexey                #
#   Site: http://www.altasib.ru                 #
#   E-mail: info@altasib.ru                     #
#   Copyright (c) 2006-2014 ALTASIB             #
#################################################
?>
<?
global $DBType;
IncludeModuleLangFile(__FILE__);

$arClassesList = array(
);
// fix strange update bug
if (method_exists(CModule, "AddAutoloadClasses"))
{
        CModule::AddAutoloadClasses(
                "altasib.breadcrumb",
                $arClassesList
        );
}

?>
