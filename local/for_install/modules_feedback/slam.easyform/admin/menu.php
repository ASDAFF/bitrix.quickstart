<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);
$modules = 'slam.easyform';

if (!function_exists('__slam_easyform_menu_gen')) {
    function __slam_easyform_menu_gen($modules)
    {
        $menu = array();
        if (Loader::IncludeModule($modules)) {
            $i = 0;
            $tblObj = new Slam\Easyform\EasyformTable();
            $queryObj = $tblObj->query();
            $rsData = $queryObj
                ->setSelect(array('NAME'))
                ->setOrder(array('NAME' => 'ASC'))
                ->setGroup(array('NAME'))->exec();
            while ($arRes = $rsData->Fetch()) {
                $i++;
                $menu[] = array(
                    "text" => htmlspecialcharsbx($arRes["NAME"]),
                    "url" => "slam_easyform.php?set_filter=Y&amp;adm_filter_applied=0&amp;W_ID=" . $i . "&amp;FINDBY%5BNAME%5D=" . urlencode($arRes["NAME"]) . "&amp;lang=" . LANG,
                    "more_url" => array(
                        "slam_easyform.php?W_ID=" . $i,
                    ),
                );
            }
        }
        return $menu;
    }
}

if ($APPLICATION->GetGroupRight($modules) != "D") {
    $menuForms = __slam_easyform_menu_gen($modules);
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "section" => $modules,
        "sort" => 1,
        "text" => Loc::GetMessage("SLAM_EASYFORM_MODULE"),
        "title" => Loc::GetMessage("SLAM_EASYFORM_MODULE"),
        "icon" => "easyform_menu_icon",
        "page_icon" => "easyform_page_icon",
        "items_id" => "menu_easyform",
        "url" => "/bitrix/admin/slam_easyform.php",
        "more_url" => array(
            "slam_easyform.php&amp;lang=" . LANG
        ),
        "dynamic" => false,
        "items" => $menuForms

    );
    return $aMenu;
}
return false;
