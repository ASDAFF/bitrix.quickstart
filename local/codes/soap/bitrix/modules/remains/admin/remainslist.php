<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$TYPE_ID = $_REQUEST['id'];
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/remains/include.php");
 
if($UNSET = $_REQUEST['unset']){
    
    $UNSET = intval($UNSET);
    
    if($UNSET){
        $matching = new matching();
        $matching->Update($UNSET, array('ITEM_ID'=>0));
         
        $availability = new availability();
        $obj = $availability->GetList(array(),array('MATCHING_ID'=>$UNSET));
        while($r = $obj->Fetch()){
            $availability->Update($r['ID'], array('ITEM_ID'=>0));
        }  
        LocalRedirect('/bitrix/admin/remainslist.php'); 
    }
    
}


function _makeName($itemId){
    
    CModule::IncludeModule("iblock");

    static $cachearr = array();
    
    if($cachearr[$itemId])
        return $cachearr[$itemId];


    $res = CIBlockElement::GetByID($itemId);
    if($ar_res = $res->GetNext()) 
         $cachearr[$itemId] = "[ $itemId ] {$ar_res['NAME']}"; 
    else  
         $cachearr[$itemId] = "[ $itemId ] "; 
         
    return $cachearr[$itemId];
 
}


CModule::IncludeModule('iblock');

$sTableID = "tbl_options35"; // ID таблицы
$oSort = new CAdminSorting($sTableID, "ID", "DESC"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка
  
$matching = new matching();
 
$rsData = $matching->GetList(array($by=>$order), array('!ITEM_ID'=>0));
 
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();  
$lAdmin->NavText($rsData->GetNavPrint($s));
  
$lAdmin->AddHeaders(array(
  array("id"    =>"ID",
    "content"  =>"ID",
    "sort"     =>"ID", 
    "default"  =>true, 
  ),
  array(  "id"    =>"ITEM_ID",
    "content"  =>'Товар (на сайте)', 
    "sort"     =>"ITEM_ID",
    "default"  =>true,
  ),
  
   array(  "id"    =>"SUPPLIER_ID",
    "content"  =>'Поставщик', 
    "sort"     =>"SUPPLIER_ID",
    "default"  =>true,
  ),
      
     
  array(  "id"    =>"NAME",
    "content"  =>'Товар', 
    "sort"     =>"NAME",
    "default"  =>true,
  ), 
      
));
  
while($arRes = $rsData->NavNext(true, "f_")):
     
 $arRes['ITEM_ID'] = _makeName($arRes['ITEM_ID']);

 $arRes['SUPPLIER_ID'] = _makeName($arRes['SUPPLIER_ID']);
 
   $row =&$lAdmin->AddRow($f_ID, $arRes);
 
   $arActions = array();
    $arActions[] = array( 
            "ICON" => "view",
            "TEXT" => 'Разорвать связь',
            "TITLE" => 'Разорвать связь',
            "ACTION" => $lAdmin->ActionRedirect(htmlspecialcharsbx('?unset=' . $arRes['ID'])),
            "ONCLICK" => "",
    );
 
		$row->AddActions($arActions);
   
   endwhile;
  
$APPLICATION->SetTitle($s);
 
$lAdmin->AddAdminContextMenu();
$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); 
 
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
 