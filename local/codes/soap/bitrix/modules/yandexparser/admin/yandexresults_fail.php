<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$TYPE_ID = $_REQUEST['id'];
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/yandexparser/include.php");

CModule::IncludeModule('iblock');

$sTableID = "tbl_options3"; // ID таблицы
$oSort = new CAdminSorting($sTableID, "ID", "desc"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка
   
$ids = yandexPrices::GetAllSuccessId();

$arSelect = Array("ID", "NAME", "ACTIVE", "IBLOCK_ID", "PROPERTY_yandexdate",
                  "CATALOG_GROUP_1"); 
$arFilter = Array("IBLOCK_ID"=>1,
                  "ID" => $ids );

$rsData =  CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
 $rsData = false;
 
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint('Успешно обработанные'));

$lAdmin->AddHeaders(array(
  array("id"    =>"ID",
    "content"  =>"ID",
    "sort"     =>"id",
    "default"  =>true, 
  ),
  array(  "id"    =>"NAME",
    "content"  =>'Наименование товара', 
    "sort"     =>"NAME",
    "default"  =>true,
  ),
  array(  "id"    =>"SECTION_CODE1",
    "content"  =>'Путь категории по каталогу', 
    "sort"     =>"SECTION_CODE",
    "default"  =>true,
  ),
   array(  "id"    =>"CATALOG_PRICE_1",
    "content"  =>'Стоимость товара', 
    "sort"     =>"CATALOG_PRICE_1",
    "default"  =>true,
  ),
   array(  "id"    =>"PROPERTY_YANDEXDATE_VALUE",
    "content"  =>'Дата обновления Я.М.', 
    "sort"     =>"PROPERTY_yandexdate",
    "default"  =>true,
  ),
   array(  "id"    =>"SECTION_CODE4",
    "content"  =>'Дата обновления цены', 
    "sort"     =>"SECTION_CODE",
    "default"  =>true,
  ),
   array(  "id"    =>"LINK",
    "content"  =>'Ссылка на редактирование цены', 
    "sort"     =>"LINK",
    "default"  =>true,
  ),
     
    
));

 



while($arRes = $rsData->NavNext(true, "f_")):
     
   $arRes['PROPERTY_YANDEXDATE_VALUE'] = substr($arRes['PROPERTY_YANDEXDATE_VALUE'], 0, 10);
  
   $row =&$lAdmin->AddRow($f_ID, $arRes); 
 
endwhile;
 
$APPLICATION->SetTitle('Не обработаные');
  
$lAdmin->AddAdminContextMenu();
$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); 

$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
 