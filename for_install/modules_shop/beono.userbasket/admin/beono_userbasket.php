<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
define("ADMIN_MODULE_NAME", "beono.userbasket");
global $MESS;
include(GetLangFileName($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/".constant('ADMIN_MODULE_NAME')."/lang/", "/options.php"));

if ($APPLICATION->GetGroupRight('sale') < "U") {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

if (method_exists("CModule", "IncludeModuleEx") && CModule::IncludeModuleEx("beono.userbasket") == MODULE_DEMO_EXPIRED) {	
	CAdminMessage::ShowMessage(GetMessage('BEONO_MODULE_USERBASKET_ERROR_EXPIRED'));
}

CModule::IncludeModule('beono.userbasket');
CModule::IncludeModule('sale');
CModule::IncludeModule('iblock');

// + filter

if ($_GET['find_user']) {
	$arResult['USER'] = trim($_GET['find_user']);
}

if ($_GET['find_product']) {
	$arResult['PRODUCT'] = trim($_GET['find_product']);
}

if ($_GET['find_product_name']) {
	$arResult['PRODUCT_NAME'] = trim($_GET['find_product_name']);
}

if($_GET['find_date_from_DAYS_TO_BACK']) {
	$arResult['DAYS_AGO'] = intval($_GET['find_date_from_DAYS_TO_BACK']);	
} else if($_GET['find_date_from']) {
	$arResult['DATE_FROM'] = $_GET['find_date_from'];		
}

if($_GET['find_date_to']) {
	$arResult['DATE_TO'] = $_GET['find_date_to'];	
}

if ($_GET['set_filter'] != 'Y' && !$arResult['DAYS_AGO'] && !$arResult['DATE_FROM'] && !$arResult['DATE_TO']) {
	$arResult['DAYS_AGO'] = "1";
}

if ($_GET['del_filter'] == 'Y') {
	$arResult['DAYS_AGO'] = "0";
}

if (in_array($_GET['find_delayed'], array('Y', 'N'))) {
	$arResult['DELAY'] = $_GET['find_delayed'];
}

// - filter

if($_REQUEST['mode']=='list' || $_REQUEST['mode']=='frame') {
	CFile::DisableJSFunction(true);
}

$oSort = new CAdminSorting('beono_userbasket_table', "ID", "desc");
$lAdmin = new CAdminList("beono_userbasket_table", $oSort);
if($lAdmin->EditAction()) {}

if(($arID = $lAdmin->GroupAction()))
{
	switch($_REQUEST['action'])
	{
		case "add2group":	

			$arResult['ITEMS'] = BeonoUserBasket::getBasketItems();	
			$arUsers = array();
			foreach ($arResult['ITEMS'] as $arBasketItem) {				
				if ($arBasketItem['USER_ID'] && ($_REQUEST['action_target'] == 'selected' || in_array($arBasketItem['ID'], $arID))) {
					$arUsers[] = $arBasketItem['USER_ID'];
				}
			}
			$arUsers = array_unique($arUsers);			
			
			$obGroup = new CGroup;
			$arFields = Array(
				"ACTIVE"	=> "Y",
				"C_SORT"	=> 100,
				"NAME"		=> htmlspecialchars(strip_tags($_POST['new_group_name'])),
				"USER_ID"	=> $arUsers,
				//"STRING_ID"	=> "beono.userbasket"
			);
			/*
			// check if group exists
			$rsGroups = CGroup::GetList ($by = "c_sort", $order = "asc", Array ("STRING_ID" => "beono.userbasket"));			
			if ($arGroup = $rsGroups->Fetch()) {
				
				$arExistingUsers = CGroup::GetGroupUser($arGroup['ID']);
				
				if (!empty($arExistingUsers)) {
					$arUsers = array_merge($arUsers, $arExistingUsers);
					$arUsers = array_unique($arUsers);
					$arFields['USER_ID'] = $arUsers;
				}
				
				$obGroup->Update($arGroup['ID'], $arFields);
			} else {*/
				$NEW_GROUP_ID = $obGroup->Add($arFields);
			//}
			
			if ($obGroup->LAST_ERROR) {
				$lAdmin->AddGroupError($obGroup->LAST_ERROR);				
			} else {
				$lAdmin->AddActionSuccessMessage(GetMessage('BEONO_USERBASKET_ADD2GROUP_OK'));
			}			
			
			break;
		case "show_email":
					
			$arResult['ITEMS'] = BeonoUserBasket::getBasketItems();	
			$arEmails = array();
			foreach ($arResult['ITEMS'] as $arBasketItem) {				
				if ($arBasketItem['EMAIL'] && ($_REQUEST['action_target'] == 'selected' || in_array($arBasketItem['ID'], $arID))) {
					$arEmails[] = $arBasketItem['EMAIL'];
				}
			}
			if ($arEmails) {
				$arEmails = array_unique($arEmails);
				$message = implode(', ', $arEmails);
			}
			$lAdmin->onLoadScript= '
						var Dialog = new BX.CDialog({
			            title: " ",
			            head: "",
			            content: "<textarea style=\"width:100%; height:190px\">'.$message.'</textarea>",
			            resizable: false,
			            height: "198",
			            width: "400"});	
			            Dialog.Show();				
					';
			break;
		case 'delete':
			@set_time_limit(0);
			foreach ($arID as $ID) {
				if(intval($ID) > 0 && !CSaleBasket::Delete($ID)) {
					$lAdmin->AddGroupError(GetMessage("BEONO_USERBASKET_ERROR_DELETE"), $ID);
				}
			}
			break;
	}
	
}

$lAdmin->AddHeaders(array(
	array("id" => "ID", "content" => GetMessage("BEONO_USERBASKET_LIST_ID"), "sort" => "", "default" => true,),
	array("id" => "USER", "content" => GetMessage("BEONO_USERBASKET_LIST_USER"), "sort" => "", "default" => true,),
	array("id" => "NAME", "content" => GetMessage("BEONO_USERBASKET_LIST_NAME"), "sort" => '', "default" => true,),	
	array("id" => "PRODUCT_ID", "content" => GetMessage("BEONO_USERBASKET_LIST_PRODUCT_ID"), "sort" => "", "default" => true,),	
	array("id" => "PRODUCT_PRICE_ID", "content" => GetMessage("BEONO_USERBASKET_LIST_PRODUCT_PRICE_ID"), "sort" => "", "default" => true,),
	array("id" => "PRICE", "content" => GetMessage("BEONO_USERBASKET_LIST_PRICE"), "sort" => "", "default" => true,),
	array("id" => "QUANTITY", "content" => GetMessage("BEONO_USERBASKET_LIST_QUANTITY"), "sort" => "", "default" => true,),
	array("id" => "LID", "content" => GetMessage("BEONO_USERBASKET_LIST_LID"), "sort" => "", "default" => true,),
	array("id" => "DELAY", "content" => GetMessage("BEONO_USERBASKET_LIST_DELAY"), "sort" => "", "default" => true,),
	array("id" => "CAN_BUY", "content" => GetMessage("BEONO_USERBASKET_LIST_CAN_BUY"), "sort" => "", "default" => true,),
	array("id" => "DATE_INSERT", "content" => GetMessage("BEONO_USERBASKET_LIST_DATE_INSERT"), "sort" => "", "default" => true,),
	array("id" => "PROPS", "content" => GetMessage("BEONO_USERBASKET_LIST_PROPS"), "sort" => "", "default" => false),
	//array("id" => "ORDER_ID", "content" => GetMessage("BEONO_USERBASKET_LIST_ORDER_ID"), "sort" => "", "default" => true,),
));

$lAdmin->AddAdminContextMenu();
$arResult['VISIBLE_COLUMNS'] = $lAdmin->GetVisibleHeaderColumns();

$arResult['ITEMS'] = BeonoUserBasket::getBasketItems();

$rs = new CDBResult;
$rs->InitFromArray($arResult['ITEMS']);	
$arResult['ITEMS'] = $rs;

$rsData = new CAdminResult($arResult['ITEMS'], 'beono_userbasket_table');
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(''));

while($arItem = $rsData->GetNext()) {

	$row =& $lAdmin->AddRow($arItem['ID'], $arItem);
	
	if ($arItem['USER_ID']) {
		$row->AddViewField("USER", '<nobr><a title="'.trim($arItem['USER_NAME']).'" href="/bitrix/admin/user_edit.php?ID='.$arItem['USER_ID'].'" target="_blank">'.$arItem['USER'].'</a> ['.$arItem['USER_ID'].']</nobr>');
	} 
	
	if ($arItem['DETAIL_PAGE_URL']) {
		$row->AddViewField("NAME", '<a href="'.$arItem['DETAIL_PAGE_URL'].'" target="_blank">'.$arItem['NAME'].'</a>');
	}
	
	if ($arItem['PRICE']) {
		$row->AddViewField("PRICE", '<nobr>'.$arItem['PRICE'].'</nobr>');
	}
	
	if ($arItem['PROPS']) {
		$row->AddViewField("PROPS", $arItem['~PROPS']);
	}
}

$lAdmin->AddFooter(
  array(
    array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=> $rsData->SelectedRowsCount()),
    array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
  )
);
$lAdmin->AddGroupActionTable(Array("delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"), "show_email" => GetMessage("BEONO_USERBASKET_SHOWEMAIL"), 
"add2group" => array('name' => GetMessage("BEONO_USERBASKET_ADD2GROUP"), 'value' => 'add2group', 'action' => 'beono_userbasket_add2group();')), array('disable_action_target' => false));
$lAdmin->CheckListMode();
$lAdmin->InitFilter(Array('find_date_from_DAYS_TO_BACK', 'find_user', 'find_product', 'find_product_name'));
$APPLICATION->SetTitle(GetMessage("BEONO_USERBASKET_PAGETITLE"));
$APPLICATION->AddHeadString('<script type="text/javascript">
function beono_userbasket_add2group () {
	var gr_name;
	if (gr_name = prompt("'.GetMessage('BEONO_USERBASKET_GROUPNAME').'", "")) {
		var gr = document.createElement("input"); 
		gr.setAttribute("type", "hidden");
		gr.setAttribute("value", gr_name);
		gr.setAttribute("name", "new_group_name");
		document.forms["form_beono_userbasket_table"].appendChild(gr);
		document.forms["form_beono_userbasket_table"].submit();
	}
}
</script>');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPageParam("", array('find_user', 'find_product', 'find_product_name', 'find_date_from', 'find_date_to', 'find_date_from_DAYS_TO_BACK'));?>">
<?
$find_date_from_DAYS_TO_BACK = $arResult['DAYS_AGO'];
$oFilter = new CAdminFilter("beono_userbasket_table_filter", array(GetMessage('BEONO_USERBASKET_USER'), GetMessage('BEONO_USERBASKET_PRODUCT'), GetMessage('BEONO_USERBASKET_LIST_NAME'), GetMessage('BEONO_USERBASKET_LIST_DELAY')));
$oFilter->Begin();
?>
<tr>
	<td><?=GetMessage('BEONO_USERBASKET_LIST_DATE_INSERT');?>:</td>
	<td><?=CalendarPeriod("find_date_from", htmlspecialchars($arResult['DATE_FROM']), "find_date_to", htmlspecialchars($arResult['DATE_TO']), "find_form", "Y");?></td>
</tr>
<tr>
	<td><?=GetMessage('BEONO_USERBASKET_USER');?>:</td>
	<td><?=FindUserID("find_user", $arResult['USER'], '', 'find_form', "10");?></td>
</tr>
<tr>
	<td><?=GetMessage('BEONO_USERBASKET_PRODUCT');?>:</td>
	<td><input type="text" size="10" name="find_product" value="<?=htmlspecialchars($arResult['PRODUCT'])?>" /></td>
</tr>
<tr>
	<td><?=GetMessage('BEONO_USERBASKET_LIST_NAME');?>:</td>
	<td><input type="text" size="10" name="find_product_name" value="<?=htmlspecialchars($arResult['PRODUCT'])?>" /></td>
</tr>
<tr>
	<td><?=GetMessage('BEONO_USERBASKET_LIST_DELAY');?>:</td>
	<td>
		<select name="find_delayed">
			<option value=""></option>
			<option value="Y" <?if($_GET['find_delayed']=='Y'):?>selected="selected"<?endif;?>><?=GetMessage('MAIN_YES');?></option>
			<option value="N" <?if($_GET['find_delayed']=='N'):?>selected="selected"<?endif;?>><?=GetMessage('MAIN_NO');?></option>
		</select>
	</td>
</tr>
<?
$oFilter->Buttons(array("table_id"=> 'beono_userbasket_table', "url"=> $APPLICATION->GetCurPageParam("", array('find_user', 'find_product')), "form"=>"find_form"));
$oFilter->End();?>
</form>
<?$lAdmin->DisplayList();?>
<?=BeginNote();?><?=GetMessage('BEONO_USERBASKET_NOTE');?><?=EndNote();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>