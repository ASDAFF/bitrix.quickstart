<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog_user.php");
define("HELP_FILE", "users/user_admin.php");
$entity_id = "USER";

if(!($USER->CanDoOperation('view_subordinate_users') || $USER->CanDoOperation('view_all_users') || $USER->CanDoOperation('edit_all_users') || $USER->CanDoOperation('edit_subordinate_users')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$uid = $USER->GetID();
$handle_subord = (
	($USER->CanDoOperation('edit_subordinate_users') && !$USER->CanDoOperation('edit_all_users'))
	|| ($USER->CanDoOperation('view_subordinate_users') && !$USER->CanDoOperation('view_all_users'))
);
if($handle_subord)
{
	$arUserSubordinateGroups = Array();
	$arUserGroups = CUser::GetUserGroup($uid);
	for ($j = 0, $len = count($arUserGroups); $j < $len; $j++)
	{
		$arSubordinateGroups = CGroup::GetSubordinateGroups($arUserGroups[$j]);
		$arUserSubordinateGroups = array_merge ($arUserSubordinateGroups, $arSubordinateGroups);
	}
	$arUserSubordinateGroups = array_unique($arUserSubordinateGroups);
}

IncludeModuleLangFile(__FILE__);

//authorize as user
if($_REQUEST["action"] == "authorize" && check_bitrix_sessid() && $USER->CanDoOperation('edit_php'))
{
	$USER->Authorize(intval($_REQUEST["ID"]));
	LocalRedirect("user_admin.php?lang=".LANGUAGE_ID);
}

// идентификатор таблицы
$sTableID = "tbl_user";

// инициализация сортировки
$oSort = new CAdminSorting($sTableID, "TIMESTAMP_X", "desc");
// инициализация списка
$lAdmin = new CAdminList($sTableID, $oSort);

$bIntranetEdition = IsModuleInstalled("intranet");//(defined("INTRANET_EDITION") && INTRANET_EDITION == "Y");

// инициализация параметров списка - фильтры
$arFilterFields = Array(
	"find",
	"find_type",
	"find_id",
	"find_timestamp_1",
	"find_timestamp_2",
	"find_last_login_1",
	"find_last_login_2",
	"find_active",
	"find_login",
	"find_name",
	"find_email",
	"find_keywords",
	"find_group_id"
);
if ($bIntranetEdition)
	$arFilterFields[] = "find_intranet_users";
$USER_FIELD_MANAGER->AdminListAddFilterFields($entity_id, $arFilterFields);

$lAdmin->InitFilter($arFilterFields);

//инициализация массива фильтра для GetList
function CheckFilter($FilterArr) // проверка введенных полей
{
	global $strError;
	foreach($FilterArr as $f)
		global $$f;

	$str = "";
	if(strlen(trim($find_timestamp_1))>0 || strlen(trim($find_timestamp_2))>0)
	{
		$date_1_ok = false;
		$date1_stm = MkDateTime(FmtDate($find_timestamp_1,"D.M.Y"),"d.m.Y");
		$date2_stm = MkDateTime(FmtDate($find_timestamp_2,"D.M.Y")." 23:59","d.m.Y H:i");
		if (!$date1_stm && strlen(trim($find_timestamp_1))>0)
			$str.= GetMessage("MAIN_WRONG_TIMESTAMP_FROM")."<br>";
		else $date_1_ok = true;
		if (!$date2_stm && strlen(trim($find_timestamp_2))>0)
			$str.= GetMessage("MAIN_WRONG_TIMESTAMP_TILL")."<br>";
		elseif ($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm)>0)
			$str.= GetMessage("MAIN_FROM_TILL_TIMESTAMP")."<br>";
	}

	if(strlen(trim($find_last_login_1))>0 || strlen(trim($find_last_login_2))>0)
	{
		$date_1_ok = false;
		$date1_stm = MkDateTime(FmtDate($find_last_login_1,"D.M.Y"),"d.m.Y");
		$date2_stm = MkDateTime(FmtDate($find_last_login_2,"D.M.Y")." 23:59","d.m.Y H:i");
		if(!$date1_stm && strlen(trim($find_last_login_1))>0)
			$str.= GetMessage("MAIN_WRONG_LAST_LOGIN_FROM")."<br>";
		else
			$date_1_ok = true;
		if(!$date2_stm && strlen(trim($find_last_login_2))>0)
			$str.= GetMessage("MAIN_WRONG_LAST_LOGIN_TILL")."<br>";
		elseif($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm)>0)
			$str.= GetMessage("MAIN_FROM_TILL_LAST_LOGIN")."<br>";
	}

	$strError .= $str;
	if(strlen($str)>0)
	{
		global $lAdmin;
		$lAdmin->AddFilterError($str);
		return false;
	}

	return true;
}

$arFilter = Array();
if(CheckFilter($arFilterFields))
{
	$arFilter = Array(
		"ID" => $find_id,
		"TIMESTAMP_1" => $find_timestamp_1,
		"TIMESTAMP_2" => $find_timestamp_2,
		"LAST_LOGIN_1" => $find_last_login_1,
		"LAST_LOGIN_2" => $find_last_login_2,
		"ACTIVE" => $find_active,
		"LOGIN" => ($find!='' && $find_type == "login"? $find: $find_login),
		"NAME" => ($find!='' && $find_type == "name"? $find: $find_name),
		"EMAIL" => ($find!='' && $find_type == "email"? $find: $find_email),
		"KEYWORDS" => $find_keywords,
		"GROUPS_ID" => $find_group_id
		);
	if ($bIntranetEdition)
		$arFilter["INTRANET_USERS"] = $find_intranet_users;
	$USER_FIELD_MANAGER->AdminListAddFilter($entity_id, $arFilter);
}

if($handle_subord)
{
	$arFilter["CHECK_SUBORDINATE"] = $arUserSubordinateGroups;
	if($USER->CanDoOperation('edit_own_profile'))
		$arFilter["CHECK_SUBORDINATE_AND_OWN"] = $uid;
}

if (!$USER->CanDoOperation('edit_php'))
{
	$arFilter["NOT_ADMIN"] = true;
}

// обработка редактирования (права доступа!)
if($lAdmin->EditAction())
{
	foreach($FIELDS as $ID=>$arFields)
	{
		$ID = IntVal($ID);

		if(!$USER->IsAdmin())
		{
			$UGroups = CUser::GetUserGroup($ID);
			if(in_array(1, $UGroups)) // not admin can't edit admins
			{
				continue;
			}
			elseif($USER->CanDoOperation('edit_subordinate_users'))
			{
				if(count(array_diff($UGroups, $arUserSubordinateGroups)) > 0)
					continue;
			}
			elseif($USER->CanDoOperation('edit_own_profile'))
			{
				if($USER->GetParam("USER_ID") != $ID)
					continue;
			}
			else
			{
				continue;
			}
		}

		if(!$lAdmin->IsUpdated($ID))
			continue;

		$USER_FIELD_MANAGER->AdminListPrepareFields($entity_id, $arFields);
		$DB->StartTransaction();
		$ob = new CUser;
		if(!$ob->Update($ID, $arFields))
		{
			$lAdmin->AddUpdateError(GetMessage("SAVE_ERROR").$ID.": ".$ob->LAST_ERROR, $ID);
			$DB->Rollback();
		}

		$DB->Commit();
	}
}

// обработка действий групповых и одиночных
if(($arID = $lAdmin->GroupAction()) && ($USER->CanDoOperation('edit_all_users') || $USER->CanDoOperation('edit_subordinate_users')))
{
	if($_REQUEST['action_target']=='selected')
	{
		$arID = Array();
		$rsData = CUser::GetList($by, $order, $arFilter);
		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['ID'];
	}

	$gr_id = intval($_REQUEST['groups']);
	$struct_id = intval($_REQUEST['UF_DEPARTMENT']);

	foreach($arID as $ID)
	{
		$ID = IntVal($ID);
		if($ID <= 1)
			continue;

		$UGroups = CUser::GetUserGroup($ID);
		if(in_array(1, $UGroups) && !$USER->CanDoOperation('edit_php')) // not admin can't edit admins
			continue;

		if(!$USER->CanDoOperation('edit_all_users') && $USER->CanDoOperation('edit_subordinate_users') && count(array_diff($UGroups, $arUserSubordinateGroups))>0)
			continue;

		switch($_REQUEST['action'])
		{
			case "delete":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!CUser::Delete($ID))
				{
					$DB->Rollback();
					if($ex = $APPLICATION->GetException())
						$err .= '<br>'.$ex->GetString();
					$lAdmin->AddGroupError(GetMessage("DELETE_ERROR").$err, $ID);
				}
				$DB->Commit();
				break;
			case "activate":
			case "deactivate":
				$ob = new CUser();
				$arFields = Array("ACTIVE"=>($_REQUEST['action']=="activate"?"Y":"N"));
				if(!$ob->Update($ID, $arFields))
					$lAdmin->AddGroupError(GetMessage("MAIN_EDIT_ERROR").$ob->LAST_ERROR, $ID);
				break;
			case "add_group":
			case "remove_group":
				if($gr_id <= 0)
					continue;
				if($gr_id == 1 && !$USER->CanDoOperation('edit_php')) // not admin can't edit admins
					continue;
				if ($USER->CanDoOperation('edit_subordinate_users') && !$USER->CanDoOperation('edit_all_users') && !in_array($gr_id, $arUserSubordinateGroups))
					continue;
				$newGroups = array_diff($UGroups, array(2));
				if($_REQUEST['action']=="add_group")
					$newGroups[] = $gr_id;
				else
					$newGroups = array_diff($newGroups, array($gr_id));
				CUser::SetUserGroup($ID, $newGroups);
				break;
			case "add_structure":
			case "remove_structure":
				if($struct_id <= 0)
					continue;

				$dbUser = CUser::GetByID($ID);
				$arUser = $dbUser->Fetch();
				$arDep = $arUser['UF_DEPARTMENT'];
				if(!is_array($arDep))
					$arDep = array();

				if($_REQUEST['action']=="add_structure")
					$arDep[] = $struct_id;
				else
					$arDep = array_diff($arDep, array($struct_id));

				$ob = new CUser();
				$arFields = Array("UF_DEPARTMENT"=>$arDep);
				if(!$ob->Update($ID, $arFields))
					$lAdmin->AddGroupError(GetMessage("MAIN_EDIT_ERROR").$ob->LAST_ERROR, $ID);

				break;
			case "intranet_deactivate":
				$ob = new CUser();
				$arFields = Array("LAST_LOGIN"=>false);
				if(!$ob->Update($ID, $arFields))
					$lAdmin->AddGroupError(GetMessage("MAIN_EDIT_ERROR").$ob->LAST_ERROR, $ID);
				break;
		}
	}
}

// заголовок списка
$arHeaders = array(
	array("id"=>"LOGIN", 			"content"=>GetMessage("LOGIN"), "sort"=>"login", "default"=>true),
	array("id"=>"ACTIVE", 			"content"=>GetMessage('ACTIVE'),	"sort"=>"active", "default"=>true, "align" => "center"),
	array("id"=>"TIMESTAMP_X",		"content"=>GetMessage('TIMESTAMP'), "sort"=>"timestamp_x", "default"=>true),
	array("id"=>"NAME",				"content"=>GetMessage("NAME"), "sort"=>"name",	"default"=>true),
	array("id"=>"LAST_NAME", 		"content"=>GetMessage("LAST_NAME"), "sort"=>"last_name", "default"=>true),
	array("id"=>"SECOND_NAME", 		"content"=>GetMessage("SECOND_NAME"), "sort"=>"second_name"),
	array("id"=>"EMAIL", 			"content"=>GetMessage('EMAIL'), "sort"=>"email", "default"=>true),
	array("id"=>"LAST_LOGIN", 		"content"=>GetMessage("LAST_LOGIN"), "sort"=>"last_login", "default"=>true),
	array("id"=>"DATE_REGISTER", 	"content"=>GetMessage("DATE_REGISTER"), "sort"=>"date_register"),
	array("id"=>"ID",				"content"=>"ID", 	"sort"=>"id", "default"=>true, "align"=>"right"),
	array("id"=>"PERSONAL_BIRTHDAY", 	"content"=>GetMessage("PERSONAL_BIRTHDAY"), "sort"=>"personal_birthday"),
	array("id"=>"PERSONAL_PROFESSION",	"content"=>GetMessage("PERSONAL_PROFESSION"), "sort"=>"personal_profession"),
	array("id"=>"PERSONAL_WWW", 		"content"=>GetMessage("PERSONAL_WWW"), "sort"=>"personal_www"),
	array("id"=>"PERSONAL_ICQ", 		"content"=>GetMessage("PERSONAL_ICQ"), "sort"=>"personal_icq"),
	array("id"=>"PERSONAL_GENDER", 	"content"=>GetMessage("PERSONAL_GENDER"), "sort"=>"personal_gender"),
	array("id"=>"PERSONAL_PHONE", 	"content"=>GetMessage("PERSONAL_PHONE"), "sort"=>"personal_phone"),
	array("id"=>"PERSONAL_MOBILE", 	"content"=>GetMessage("PERSONAL_MOBILE"), "sort"=>"personal_mobile"),
	array("id"=>"PERSONAL_CITY", 		"content"=>GetMessage("PERSONAL_CITY"), "sort"=>"personal_city"),
	array("id"=>"PERSONAL_STREET", 	"content"=>GetMessage("PERSONAL_STREET"), "sort"=>"personal_street"),
	array("id"=>"WORK_COMPANY", 		"content"=>GetMessage("WORK_COMPANY"), "sort"=>"work_company"),
	array("id"=>"WORK_DEPARTMENT", 	"content"=>GetMessage("WORK_DEPARTMENT"), "sort"=>"work_department"),
	array("id"=>"WORK_POSITION", 		"content"=>GetMessage("WORK_POSITION"), "sort"=>"work_position"),
	array("id"=>"WORK_WWW", 			"content"=>GetMessage("WORK_WWW"), "sort"=>"work_www"),
	array("id"=>"WORK_PHONE", 		"content"=>GetMessage("WORK_PHONE"), "sort"=>"work_phone"),
	array("id"=>"WORK_CITY", 		"content"=>GetMessage("WORK_CITY"), "sort"=>"work_city"),
	array("id"=>"XML_ID", 			"content"=>GetMessage("XML_ID"), "sort"=>"xml_id"),
	array("id"=>"EXTERNAL_AUTH_ID",	"content"=>GetMessage("EXTERNAL_AUTH_ID")),
);

$rsRatings = CRatings::GetList(array('ID' => 'ASC'), array('ACTIVE' => 'Y', 'ENTITY_ID' => 'USER'));
while ($arRatingsTmp = $rsRatings->GetNext())
	$arHeaders[] = array("id"=>"RATING_".$arRatingsTmp['ID'], "content"=>htmlspecialcharsbx($arRatingsTmp['NAME']), "sort"=>"RATING_".$arRatingsTmp['ID']);

$USER_FIELD_MANAGER->AdminListAddHeaders($entity_id, $arHeaders);
$lAdmin->AddHeaders($arHeaders);

// инициализация списка - выборка данных
$rsData = CUser::GetList($by, $order, $arFilter, array(
	"SELECT" => $lAdmin->GetVisibleHeaderColumns(),
	"NAV_PARAMS"=> array("nPageSize"=>CAdminResult::GetNavSize($sTableID)),
));

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

// установке параметров списка
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PAGES")));
while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arRes);
	$USER_FIELD_MANAGER->AddUserFields($entity_id, $arRes, $row);
	$row->AddViewField("ID", "<a href='user_edit.php?lang=".LANGUAGE_ID."&ID=".$f_ID."' title='".GetMessage("MAIN_EDIT_TITLE")."'>".$f_ID."</a>");
	$own_edit = ($USER->CanDoOperation('edit_own_profile') && ($USER->GetParam("USER_ID") == $f_ID));
	$edit = ($USER->CanDoOperation('edit_subordinate_users') || $USER->CanDoOperation('edit_all_users'));
	$can_edit = (IntVal($f_ID)>1 && ($own_edit || $edit));
	if($f_ID == 1 || $own_edit || !$can_edit)
		$row->AddCheckField("ACTIVE", false);
	else
		$row->AddCheckField("ACTIVE");

	if ($can_edit && $edit)
	{
		$row->AddInputField("LOGIN");
		$row->AddInputField("NAME");
		$row->AddInputField("LAST_NAME");
		$row->AddInputField("SECOND_NAME");
		$row->AddViewField("EMAIL", TxtToHtml($arRes["EMAIL"])); $row->AddInputField("EMAIL");
		$row->AddInputField("PERSONAL_PROFESSION");
		$row->AddViewField("PERSONAL_WWW", TxtToHtml($arRes["PERSONAL_WWW"]));
		$row->AddInputField("PERSONAL_WWW");
		$row->AddInputField("PERSONAL_ICQ");
		$row->AddInputField("PERSONAL_GENDER");
		$row->AddInputField("PERSONAL_PHONE");
		$row->AddInputField("PERSONAL_MOBILE");
		$row->AddInputField("PERSONAL_CITY");
		$row->AddInputField("PERSONAL_STREET");
		$row->AddInputField("WORK_COMPANY");
		$row->AddInputField("WORK_DEPARTMENT");
		$row->AddInputField("WORK_POSITION");
		$row->AddViewField("WORK_WWW", TxtToHtml($arRes["WORK_WWW"]));
		$row->AddInputField("WORK_WWW");
		$row->AddInputField("WORK_PHONE");
		$row->AddInputField("WORK_CITY");
		$row->AddInputField("XML_ID");
	}
	else
	{
		$row->AddViewField("EMAIL", TxtToHtml($arRes["EMAIL"]));
		$row->AddViewField("PERSONAL_WWW", TxtToHtml($arRes["PERSONAL_WWW"]));
		$row->AddViewField("WORK_WWW", TxtToHtml($arRes["WORK_WWW"]));
	}

	$arActions = Array();
	$arActions[] = array("ICON"=>$can_edit ? "edit" : "view", "TEXT"=>GetMessage($can_edit ? "MAIN_ADMIN_MENU_EDIT" : "MAIN_ADMIN_MENU_VIEW"), "LINK"=> "user_edit.php?ID=".$f_ID, "DEFAULT"=>true);
	if($can_edit && $edit)
	{
		$arActions[] = array("ICON"=>"copy", "TEXT"=>GetMessage("MAIN_ADMIN_ADD_COPY"), "LINK"=>"user_edit.php?COPY_ID=".$f_ID);
		if (!$own_edit)
			$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION"=>"if(confirm('".GetMessage('CONFIRM_DEL_USER')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
	}
	if($USER->CanDoOperation('edit_php'))
	{
		$arActions[] = array("SEPARATOR"=>true);
		$arActions[] = array("ICON"=>"", "TEXT"=>GetMessage("MAIN_ADMIN_AUTH"), "TITLE"=>GetMessage("MAIN_ADMIN_AUTH_TITLE"), "LINK"=>"user_admin.php?ID=".$f_ID."&action=authorize&".bitrix_sessid_get());
	}

	$row->AddActions($arActions);
}

// "подвал" списка
$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

$aContext = Array();
// показ формы с кнопками добавления, ...

if ($USER->CanDoOperation('edit_subordinate_users') || $USER->CanDoOperation('edit_all_users'))
{
	$groups = CGroup::GetDropDownList("AND ID<>2");
	$sGr = '';
	while($gr = $groups->Fetch())
		$sGr .= '<option value="'.$gr["REFERENCE_ID"].'">'.htmlspecialcharsex($gr["REFERENCE"]).'</option>'."\n";

	$ar = Array(
		"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
		"activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
		"deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
		"add_group"=>GetMessage("MAIN_ADMIN_LIST_ADD_GROUP"),
		"remove_group"=>GetMessage("MAIN_ADMIN_LIST_REM_GROUP"),
		"groups"=>array(
			"type"=>"html",
			"value"=>'<div id="bx_user_groups" style="display:none"><select name="groups"><option>'.GetMessage("MAIN_ADMIN_LIST_GROUP").'</option>'.$sGr.'</select></div>',
		),
	);

	//for Intranet editions: structure group operations and last authorization time
	if($bIntranetEdition)
	{
		$arUserFields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('USER', 0, LANGUAGE_ID);
		$arUserField = $arUserFields['UF_DEPARTMENT'];
		if(is_array($arUserField))
		{
			$arUserField['MULTIPLE'] = 'N';
			$arUserField['SETTINGS']['LIST_HEIGHT'] = 1;

			$sStruct = call_user_func_array(
				array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtml"),
				array(
					$arUserField,
					array(
						"NAME" => $arUserField["FIELD_NAME"],
						"VALUE" => "",
					),
				)
			);

			$ar["add_structure"] = GetMessage("MAIN_ADMIN_LIST_ADD_STRUCT");
			$ar["remove_structure"] = GetMessage("MAIN_ADMIN_LIST_REM_STRUCT");
			$ar["structure"] = array(
				"type"=>"html",
				"value"=>'<div id="bx_user_structure" style="display:none">'.$sStruct.'</div>',
			);
		}

		$ar["intranet_deactivate"] = GetMessage("MAIN_ADMIN_LIST_INTRANET_DEACTIVATE");
	}

	$arParams = array("select_onchange"=>"document.getElementById('bx_user_groups').style.display = (this.value == 'add_group' || this.value == 'remove_group'? 'block':'none');".(isset($ar["structure"])? "document.getElementById('bx_user_structure').style.display = (this.value == 'add_structure' || this.value == 'remove_structure'? 'block':'none');":""));

	$lAdmin->AddGroupActionTable($ar, $arParams);

	$aContext[] = array(
		"TEXT"	=> GetMessage("MAIN_ADD_USER"),
		"LINK"	=> "user_edit.php?lang=".LANGUAGE_ID,
		"TITLE"	=> GetMessage("MAIN_ADD_USER_TITLE"),
		"ICON"	=> "btn_new"
	);
}
$lAdmin->AddAdminContextMenu($aContext);

// проверка на вывод только списка (в случае списка, скрипт дальше выполняться не будет)
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("TITLE"));

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$arFindFields = array(
		GetMessage('MAIN_FLT_USER_ID'),
		GetMessage('MAIN_FLT_MOD_DATE'),
		GetMessage('MAIN_FLT_AUTH_DATE'),
		GetMessage('MAIN_FLT_ACTIVE'),
		GetMessage('MAIN_FLT_LOGIN'),
		GetMessage('MAIN_FLT_EMAIL'),
		GetMessage('MAIN_FLT_FIO'),
		GetMessage('MAIN_FLT_PROFILE_FIELDS'),
		GetMessage('MAIN_FLT_USER_GROUP')
	);
if ($bIntranetEdition)
	$arFindFields[] = GetMessage("F_FIND_INTRANET_USERS");

$USER_FIELD_MANAGER->AddFindFields($entity_id, $arFindFields);
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	$arFindFields
);

$oFilter->Begin();
?>
<tr>
	<td><b><?=GetMessage("MAIN_FLT_SEARCH")?></b></td>
	<td nowrap>
		<input type="text" size="25" name="find" value="<?echo htmlspecialcharsbx($find)?>" title="<?=GetMessage("MAIN_FLT_SEARCH_TITLE")?>">
		<select name="find_type">
			<option value="login"<?if($find_type=="login") echo " selected"?>><?=GetMessage('MAIN_FLT_LOGIN')?></option>
			<option value="email"<?if($find_type=="email") echo " selected"?>><?=GetMessage('MAIN_FLT_EMAIL')?></option>
			<option value="name"<?if($find_type=="name") echo " selected"?>><?=GetMessage('MAIN_FLT_FIO')?></option>
		</select>
	</td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_F_ID")?></td>
	<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_F_TIMESTAMP").":"?></td>
	<td><?echo CalendarPeriod("find_timestamp_1", htmlspecialcharsbx($find_timestamp_1), "find_timestamp_2", htmlspecialcharsbx($find_timestamp_2), "find_form","Y")?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_F_LAST_LOGIN").":"?></td>
	<td><?echo CalendarPeriod("find_last_login_1", htmlspecialcharsbx($find_last_login_1), "find_last_login_2", htmlspecialcharsbx($find_last_login_2), "find_form","Y")?></td>
</tr>
<tr>
	<td><?echo GetMessage("F_ACTIVE")?></td>
	<td><?
		$arr = array("reference"=>array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id"=>array("Y","N"));
		echo SelectBoxFromArray("find_active", $arr, htmlspecialcharsbx($find_active), GetMessage('MAIN_ALL'));
		?>
	</td>
</tr>
<tr>
	<td><?echo GetMessage("F_LOGIN")?></td>
	<td><input type="text" name="find_login" size="47" value="<?echo htmlspecialcharsbx($find_login)?>"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_F_EMAIL")?></td>
	<td><input type="text" name="find_email" value="<?echo htmlspecialcharsbx($find_email)?>" size="47"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("F_NAME")?></td>
	<td><input type="text" name="find_name" value="<?echo htmlspecialcharsbx($find_name)?>" size="47"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_F_KEYWORDS")?></td>
	<td><input type="text" name="find_keywords" value="<?echo htmlspecialcharsbx($find_keywords)?>" size="47"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr valign="top">
	<td><?echo GetMessage("F_GROUP")?><br><img src="/bitrix/images/main/mouse.gif" width="44" height="21" border="0" alt=""></td>
	<td><?
	$z = CGroup::GetDropDownList("AND ID!=2");
	echo SelectBoxM("find_group_id[]", $z, $find_group_id, "", false, 10);
	?></td>
</tr>
<?
if ($bIntranetEdition)
{
	?>
	<tr>
		<td><?echo GetMessage("F_FIND_INTRANET_USERS")?>:</td>
		<td><?
			$arr = array("reference"=>array(GetMessage("MAIN_YES")), "reference_id"=>array("Y"));
			echo SelectBoxFromArray("find_intranet_users", $arr, htmlspecialcharsbx($find_intranet_users), GetMessage('MAIN_ALL'));
			?>
		</td>
	</tr>
	<?
}
?>
<?
$USER_FIELD_MANAGER->AdminListShowFilter($entity_id);
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>
<?
// место для вывода списка
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>
