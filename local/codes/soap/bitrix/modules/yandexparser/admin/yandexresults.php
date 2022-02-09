<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$TYPE_ID = $_REQUEST['id'];
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/yandexparser/include.php");

CModule::IncludeModule('iblock');

$sTableID = "tbl_options1"; // ID таблицы
$oSort = new CAdminSorting($sTableID, "ID", "desc"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка
  
 
$ids = yandexPrices::GetAllSuccessId();

$arSelect = Array("ID", "NAME", "ACTIVE", "IBLOCK_ID", "PROPERTY_yandexdate",
    "CODE", "DETAIL_PAGE_URL", 
                  "CATALOG_GROUP_1"); 
$arFilter = Array("IBLOCK_ID"=>1,
                  "ID" => $ids );

$rsData =  CIBlockElement::GetList(Array($_REQUEST['by'] => $_REQUEST['order']), $arFilter, false, false, $arSelect);

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint('Успешно обработанные'));

$lAdmin->AddHeaders(array(
  array("id"    =>"ID",
    "content"  =>"ID",
    "sort"     =>"id",
    "default"  =>false, 
  ),
  array(  "id"    =>"NAME",
    "content"  =>'Наименование товара', 
    "sort"     =>"NAME",
    "default"  =>true,
  ),
  array(  "id"    =>"CODE",
    "content"  =>'Путь категории по каталогу', 
    "sort"     =>"CODE",
    "default"  =>false,
  ),
   array(  "id"    =>"CATALOG_PRICE_1",
    "content"  =>'Стоимость товара', 
    "sort"     =>"CATALOG_PRICE_1",
    "default"  =>false,
  ),
   array(  "id"    =>"PROPERTY_YANDEXDATE_VALUE",
    "content"  =>'Дата обновления Я.М.', 
    "sort"     =>"PROPERTY_yandexdate",
    "default"  =>false,
  ),
   array(  "id"    =>"SECTION_CODE4",
    "content"  =>'Дата обновления цены', 
    "sort"     =>"SECTION_CODE",
    "default"  =>false,
  ),  
));

 

while($arRes = $rsData->Fetch()):
    
   $arRes['PROPERTY_YANDEXDATE_VALUE'] = substr($arRes['PROPERTY_YANDEXDATE_VALUE'], 0, 10);
   
   $row =&$lAdmin->AddRow($f_ID, $arRes, 'iblock_element_edit.php?IBLOCK_ID=1&type=catalog&ID=260&lang=ru&find_section_section=-1&WF=Y', 'sd');

   $row->AddViewField("NAME", '<a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=1&type=catalog&ID=' . $arRes['ID'] . '&lang=ru&force_catalog=&filter_section=0">' . $arRes['NAME'].'</a>');
  
   endwhile;
 
$APPLICATION->SetTitle('Успешно обработаные');
 
$lAdmin->AddAdminContextMenu();
$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); 

?>

<script src="/js/libs/jquery-1.8.2.min.js"></script>
 
<?

$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
 