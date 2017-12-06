Описание
Пункты меню из элементов и разделов инфоблока.

Решение позволяет построить древовидное меню из разделов и элементов инфоблока, используя общую сортировку.

Может использоваться, например, для отображения в меню разделов услуг и самих услуг, структуры компании и её сотрудников и т.д. 

Используется как замена стандартному компоненту bitrix:menu.sections

Краткий пример использования:
1) в папке создаете файл .left.menu_ext.php
2) размещаете в нем код:
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

$aMenuLinksExt = $APPLICATION->IncludeComponent(
    "millcom:menu",
    "",
    Array(
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "A",
        "DEPTH_LEVEL" => "1",
        "IBLOCK_ID" => "1",
        "IBLOCK_TYPE" => "info",
        "SORT" => "Y",
    )
);

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>
3) на индексной странице этой директории вызываете стандартный компонент Меню и ставите галочку на подключение menu_ext.php
Все. 