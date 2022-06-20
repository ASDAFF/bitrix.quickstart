<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if ($this->StartResultCache())
{
    $this->InitComponentTemplate();

    $template = & $this->GetTemplate();
    $templateFolder = $template->GetFolder() ;
    $codePath = $_SERVER["DOCUMENT_ROOT"] . $template->GetFolder() . "/code.php" ;

    /* В папке шаблона должен лежать файл code.php,
        в котором выполняются операции с $arResult */
    if(file_exists( $codePath )){
        include $codePath;
    }

    $this->ShowComponentTemplate();
}
