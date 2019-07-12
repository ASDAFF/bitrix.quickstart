<?php
/**
 * Created by PhpStorm.
 * User: aleksander
 * Date: 27.02.2017
 * Time: 16:01
 */

IncludeModuleLangFile(__FILE__);

$MODULE_ID = 'okshop';
$MODULE_CODE = 'okshop';
$moduleSort = 10000;

$aMenu = array(
    "parent_menu" => "global_menu_krayt", // поместим в раздел "Контент"
    "sort"        => $moduleSort,
    "section"     => $MODULE_ID,             // вес пункта меню
    "url"         => '/bitrix/admin/k_tp_page.php?lang=' . LANGUAGE_ID,
    "text"        => GetMessage("K_TP_PAGE"),       // текст пункта меню
    "title"       => GetMessage("K_TP_PAGE"),  // текст всплывающей подсказки
);

$aModuleMenu[] = $aMenu;

return $aModuleMenu;