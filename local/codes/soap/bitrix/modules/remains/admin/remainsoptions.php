<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

if (  (!isset($_REQUEST['id']) && !isset($_REQUEST['section'])) 
      ||
      (isset($_REQUEST['id']) && !isset($_REQUEST['section']))   ){

$RIGHT = $APPLICATION->GetGroupRight("remains");
if($RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$APPLICATION->SetTitle(GetMessage("form_index_title"));

define('BX_ADMIN_FORM_MENU_OPEN', 1);

if($_REQUEST["mode"] == "list")
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
else
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$show = isset($_REQUEST['id']) ? "menu_kudin_options_iblock_".$_REQUEST['id'] : "menu_kudin_options";

$adminPage->ShowSectionIndex($show, "remains");

if($_REQUEST["mode"] == "list")
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
else
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
die();
}



$SECTION_ID = intval($_REQUEST['section']);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/properties/include.php"); // инициализация модуля

$sTableID = "tbl_options"; // ID таблицы
$oSort = new CAdminSorting($sTableID, "ID", "desc"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка

// сохранение отредактированных элементов
if($lAdmin->EditAction())
{
	// пройдем по списку переданных элементов
	foreach($FIELDS as $ID=>$arFields)
	{
		if(!$lAdmin->IsUpdated($ID))
			continue;
		
		// сохраним изменения каждого элемента
		$ID = IntVal($ID);
		$cData = new CRubric;
		if(($rsData = $cData->GetByID($ID)) && ($arData = $rsData->Fetch()))
		{
			foreach($arFields as $key=>$value)
				$arData[$key]=$value;
			if(!$cData->Update($ID, $arData))
			{
				$lAdmin->AddGroupError(GetMessage("rub_save_error")." ".$cData->LAST_ERROR, $ID);
				$DB->Rollback();
			}
		}
		else
		{
			$lAdmin->AddGroupError(GetMessage("rub_save_error")." ".GetMessage("rub_no_rubric"), $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}

// обработка одиночных и групповых действий
if(($arID = $lAdmin->GroupAction()))
{
	// если выбрано "Для всех элементов"
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = CKudinConfig::GetAll();
		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['ID'];
	}

	// пройдем по списку элементов
	foreach($arID as $ID)
	{
		if(strlen($ID)<=0)
			continue;
	   	$ID = IntVal($ID);

		switch($_REQUEST['action'])
		{
		case "delete":
                        CPropertyTypes::RemoveByID($ID);
			break;
                    
                default:
                break;
		}

	}
}

$rsData = CPropertyTypes::GetList(array(),array('SECTION_ID'=>$SECTION_ID));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint('Группы характеристик каталога'));



$lAdmin->AddHeaders(array(
  array("id"    =>"ID",
    "content"  =>"ID",
    "sort"     =>"id",
    "default"  =>true,
  ),
  array(  "id"    =>"NAME",
    "content"  =>'Имя',
    "sort"     =>"name",
    "default"  =>true,
  ),
  array(  "id"    =>"SORT",
    "content"  =>'Сортировка',
    "sort"     =>"sort",
    "default"  =>true,
  ),
  array(  "id"    =>"ACTIVE",
    "content"  =>'Активность',
    "sort"     =>"active",
    "default"  =>true,
  ),
));



while($arRes = $rsData->NavNext(true, "f_")):
  // создаем строку. результат - экземпляр класса CAdminListRow
  $row =& $lAdmin->AddRow($f_ID, $arRes); 

  // далее настроим отображение значений при просмотре и редаткировании списка
  $row->AddViewField("ID", '<a href="property_types_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_ID.'</a>');
  $row->AddInputField("NAME", array("size"=>40));
  $row->AddInputField("SORT", array("size"=>10)); 
  $row->AddInputField("ACTIVE", array("size"=>10)); 
  
  // формируем контекстное меню
  $arActions = Array();
  
  $arActions[] = array(
      "ICON"=>"edit",
      "DEFAULT"=>true,
      "TEXT"=>'Редактировать',
      "ACTION"=>$lAdmin->ActionRedirect("property_types_edit.php?ID=".$f_ID)
  );
  
  $arActions[] = array("SEPARATOR"=>true);
 
  $arActions[] = array(
      "ICON"=>"delete",
      "TEXT"=>'Удалить',
      "ACTION"=>"if(confirm('Удаляем?')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
  );
    
  $row->AddActions($arActions);
  
endwhile;


$lAdmin->AddFooter(
  array(
    array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
    array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"), // счетчик выбранных элементов
  )
);

// групповые действия
$lAdmin->AddGroupActionTable(Array(
  "delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE")
  ));

$APPLICATION->SetTitle('Характеристики каталога');

$aContext = array(
      array(
        "TEXT"=>'Добавить группу характеристик',
        "LINK"=>"property_types_edit.php?section_id=".$SECTION_ID."&lang=".LANG,
        "TITLE"=>'Добавить группу характеристик',
        "ICON"=>"properties_group_add",
      )
);
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); 

$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
