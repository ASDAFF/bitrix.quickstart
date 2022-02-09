<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$TYPE_ID = $_REQUEST['id'];
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/remains/include.php");

CModule::IncludeModule('iblock');

$sTableID = "tbl_options34"; // ID таблицы
$oSort = new CAdminSorting($sTableID, "ID", "DESC"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка
  
 $remainsLog = new remainsLog();
 
 $arF = array();
 $s = 'Все запуски';
 if($_REQUEST['f']){
      $arF = array('TYPE'=>$_REQUEST['f']);
 if($_REQUEST['f'] == 1)
     $s = 'Успешно обработанные';
 else
     $s = 'Проблемные запуски';
      
 }
 
$rsData = $remainsLog->GetList(array($by=>$order), $arF);
 
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();  
$lAdmin->NavText($rsData->GetNavPrint($s));
  
$lAdmin->AddHeaders(array(
  array("id"    =>"ID",
    "content"  =>"ID",
    "sort"     =>"ID", 
    "default"  =>true, 
  ),
  array(  "id"    =>"DATE",
    "content"  =>'Дата', 
    "sort"     =>"DATE",
    "default"  =>true,
  ),
  
   array(  "id"    =>"STR",
    "content"  =>'Описание', 
    "sort"     =>"STR",
    "default"  =>true,
  ),
        array(  "id"    =>"N1",
    "content"  =>'Обработано товаров', 
    "sort"     =>"N1",
    "default"  =>true,
  ),
        array(  "id"    =>"N2",
    "content"  =>'Товаров без соответствия', 
    "sort"     =>"N2",
    "default"  =>true,
  ),
        array(  "id"    =>"N3",
    "content"  =>'Товаров с соответсвием', 
    "sort"     =>"N3",
    "default"  =>true,
  ),
     
  array(  "id"    =>"TIME",
    "content"  =>'Время выполнение скрипта', 
    "sort"     =>"TIME",
    "default"  =>true,
  ), 
      
));
 

while($arRes = $rsData->NavNext(true, "f_")):
     
   $arRes['PROPERTY_YANDEXDATE_VALUE'] = substr($arRes['PROPERTY_YANDEXDATE_VALUE'], 0, 10);
  $arRes['TIME'] = $arRes['TIME']; 
   $row =&$lAdmin->AddRow($f_ID, $arRes);
endwhile;
  
$APPLICATION->SetTitle($s);
 
$lAdmin->AddAdminContextMenu();
$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); 

?>

<script src="/js/libs/jquery-1.8.2.min.js"></script>

<div style="margin-bottom: 10px; margin-left: 6px;">
 <a href="/bitrix/admin/remainslog.php">Все запуски</a> &nbsp 
 <a href="/bitrix/admin/remainslog.php?f=1">Успешные Запуски</a>  &nbsp 
  <a href="/bitrix/admin/remainslog.php?f=2">Проблемные запуски</a> 
 
</div> 
 
<? 
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
 