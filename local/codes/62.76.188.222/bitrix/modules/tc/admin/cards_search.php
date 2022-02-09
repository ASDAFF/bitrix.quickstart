<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$TYPE_ID = $_REQUEST['id'];
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tc/include.php");

CModule::IncludeModule('iblock');

$sTableID = "tbl_cards"; // ID таблицы 
$oSort = new CAdminSorting($sTableID, "ID", "desc"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка
  
 $tcCards = new tcCards();
$rsData = $tcCards->GetList(array($_REQUEST['by']=>$_REQUEST['order']),array());

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint('Бонусные карты'));

$lAdmin->AddHeaders(array(
  array("id"    =>"id",
    "content"  =>"ID",
    "sort"     =>"id",
    "default"  =>false, 
  ),
  array(  "id"    =>"vladelec",
    "content"  =>'Владелец', 
    "sort"     =>"vladelec",
    "default"  =>true,
  ),
  array(  "id"    =>"ostatok",
    "content"  =>'Остаток', 
    "sort"     =>"ostatok",
    "default"  =>false,
  ), 
        array(  "id"    =>"summa",
    "content"  =>'Сумма', 
    "sort"     =>"summa",
    "default"  =>false,
  ),
      array(  "id"    =>"procent",
    "content"  =>'Процент', 
    "sort"     =>"procent",
    "default"  =>false,
  ),     array(  "id"    =>"nomer",
    "content"  =>'Номер', 
    "sort"     =>"nomer",
    "default"  =>false,
  ),
      array(  "id"    =>"tipsidki",
    "content"  =>'Тип скидки', 
    "sort"     =>"tipsidki",
    "default"  =>false,
  ) 
));

  
while($arRes = $rsData->Fetch()):
    
   $arRes['PROPERTY_YANDEXDATE_VALUE'] = substr($arRes['PROPERTY_YANDEXDATE_VALUE'], 0, 10);
   
   $row =&$lAdmin->AddRow($f_ID, $arRes, 'iblock_element_edit.php?IBLOCK_ID=1&type=catalog&ID=260&lang=ru&find_section_section=-1&WF=Y', 'sd');

   $row->AddViewField("NAME", '<a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=1&type=catalog&ID=' . $arRes['ID'] . '&lang=ru&force_catalog=&filter_section=0">' . $arRes['NAME'].'</a>');
  
   endwhile;
 
$APPLICATION->SetTitle('Бонусные карты');
 
$lAdmin->AddAdminContextMenu();
$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); 
 
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
 