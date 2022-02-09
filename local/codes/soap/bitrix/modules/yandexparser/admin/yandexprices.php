<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$TYPE_ID = $_REQUEST['id'];
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/yandexparser/include.php");

$sTableID = "tbl_options2"; // ID таблицы
$oSort = new CAdminSorting($sTableID, "ID", "desc"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка
 
// сохранение отредактированных элементов
//if($lAdmin->EditAction())
//{
//	// пройдем по списку переданных элементов
//	foreach($FIELDS as $ID=>$arFields)
//	{
//		if(!$lAdmin->IsUpdated($ID))
//			continue;
//		
//		// сохраним изменения каждого элемента
//		$ID = IntVal($ID);
//		$cData = new CRubric;
//		if(($rsData = $cData->GetByID($ID)) && ($arData = $rsData->Fetch()))
//		{
//			foreach($arFields as $key=>$value)
//				$arData[$key]=$value;
//			if(!$cData->Update($ID, $arData))
//			{
//				$lAdmin->AddGroupError(GetMessage("rub_save_error")." ".$cData->LAST_ERROR, $ID);
//				$DB->Rollback();
//			}
//		}
//		else
//		{
//			$lAdmin->AddGroupError(GetMessage("rub_save_error")." ".GetMessage("rub_no_rubric"), $ID);
//			$DB->Rollback();
//		}
//		$DB->Commit();
//	}
//}

// обработка одиночных и групповых действий
//if(($arID = $lAdmin->GroupAction()))
//{
//	// если выбрано "Для всех элементов"
//	if($_REQUEST['action_target']=='selected')
//	{
//		$rsData = CKudinConfig::GetAll();
//		while($arRes = $rsData->Fetch())
//			$arID[] = $arRes['ID'];
//	}
//
//	// пройдем по списку элементов
//	foreach($arID as $ID)
//	{
//		if(strlen($ID)<=0)
//			continue;
//	   	$ID = IntVal($ID);
//
//		switch($_REQUEST['action'])
//		{
//		case "delete":
//                        CPropertyTypes::RemoveByID($ID);
//			break;
//                    
//                default:
//                break;
//		}
//
//	}
//}
 
$rsData = yandexPrices::GetList(array($_REQUEST['by'] => $_REQUEST['order']),array());
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint('Цены Яндекс.Маркет'));
 
$lAdmin->AddHeaders(array(
//  array("id"    =>"ID",
//    "content"  =>"ID",
//    "sort"     =>"id",
//    "default"  =>true, 
//  ),
  array(  "id"    =>"ITEM_ID",
    "content"  =>'Товар', 
    "sort"     =>"ITEM_ID",
    "default"  =>true,
  ),
  array(  "id"    =>"SHOP_NAME",
    "content"  =>'Название магазина',
    "sort"     =>"SHOP_NAME",
    "default"  =>true,
  ), 
//      array(  "id"    =>"URL",
//    "content"  =>'Ссылка',
//    "sort"     =>"URL",
//    "default"  =>true, 
//  ),  
  array(  "id"    =>"PRICE",
    "content"  =>'Цена',
    "sort"     =>"PRICE", 
    "default"  =>true,
  ),
  array(  "id"    =>"DELIVERY",
    "content"  =>'Цена доставки',
    "sort"     =>"DELIVERY", 
    "default"  =>true,
  ), 
));


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



while($arRes = $rsData->NavNext(true, "f_")):
    
    
   $arRes['ITEM_ID'] = _makeName($arRes['ITEM_ID']);


// создаем строку. результат - экземпляр класса CAdminListRow
  $row =&$lAdmin->AddRow($f_ID, $arRes); 

  // далее настроим отображение значений при просмотре и редаткировании списка
//  $row->AddViewField("ID", '<a href="property_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_ID.'</a>');
//  $row->AddInputField("NAME", array("size"=>40));
//  $row->AddInputField("SORT", array("size"=>10)); 
//  $row->AddInputField("ACTIVE", array("size"=>10)); 
//  
  // формируем контекстное меню
//  $arActions = Array();
//  
//  $arActions[] = array(
//      "ICON"=>"edit",
//      "DEFAULT"=>true,
//      "TEXT"=>'Редактировать',
//      "ACTION"=>$lAdmin->ActionRedirect("property_edit.php?ID=".$f_ID)
//  );
//  
//  $arActions[] = array("SEPARATOR"=>true);
 
//  $arActions[] = array(
//      "ICON"=>"delete",
//      "TEXT"=>'Удалить',
//      "ACTION"=>"if(confirm('Удаляем?')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
//  );
    
//  $row->AddActions($arActions);
  
endwhile;
//
//$lAdmin->AddFooter(
//  array(
// //   array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
//    array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"), // счетчик выбранных элементов
//  )
//);

// групповые действия
//$lAdmin->AddGroupActionTable(Array(
 // "delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE")
//  ));

$APPLICATION->SetTitle('Цены Яндекс.Маркет');

//$aContext = array(
//      array(
//        "TEXT"=>'Добавить характеристику',
//        "LINK"=>"property_edit.php?property_type=".$_REQUEST['id']."&lang=".LANG,
//        "TITLE"=>'Добавить характеристику',
//        "ICON"=>"property_add",
//      )
//);
$lAdmin->AddAdminContextMenu();
$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); 

$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
 