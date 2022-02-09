<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialnetwork/include.php");

$socialnetworkModulePermissions = $APPLICATION->GetGroupRight("socialnetwork");
if ($socialnetworkModulePermissions < "R")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialnetwork/prolog.php");

$sTableID = "tbl_socnet_group";

$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
	"filter_site_id",
	"filter_subject_id",
	"filter_name",
	"filter_owner_id",
	"filter_owner_user",	
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();
if (strlen($filter_site_id) > 0 && $filter_site_id != "NOT_REF")
	$arFilter["SITE_ID"] = $filter_site_id;
if (strlen($filter_subject_id) > 0 && $filter_subject_id != "NOT_REF")
	$arFilter["SUBJECT_ID"] = $filter_subject_id;
if (strlen($filter_name) > 0)
	$arFilter["~NAME"] = "%".$filter_name."%";
if (intval($filter_owner_id) > 0)
	$arFilter["OWNER_ID"] = $filter_owner_id;
if (strlen($filter_owner_user) > 0)
	$arFilter["%OWNER_USER"] = $filter_owner_user;

if ($lAdmin->EditAction() && $socialnetworkModulePermissions >= "W")
{
	foreach ($FIELDS as $ID => $arFields)
	{
		$DB->StartTransaction();
		$ID = IntVal($ID);

		if (!$lAdmin->IsUpdated($ID))
			continue;

		foreach ($arFields as $key => $value)
		{
			unset($arFields[$key]);
			$arFields[ltrim($key, "=")] = $value;
		}

		if (!CSocNetGroup::Update($ID, $arFields, false))
		{
			if ($ex = $APPLICATION->GetException())
				$lAdmin->AddUpdateError($ex->GetString(), $ID);
			else
				$lAdmin->AddUpdateError(GetMessage("SONET_ERROR_UPDATE"), $ID);

			$DB->Rollback();
		}

		$DB->Commit();
	}
}

if (($arID = $lAdmin->GroupAction()) && $socialnetworkModulePermissions >= "W")
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = Array();
		$dbResultList = CSocNetGroupSubject::GetList(
			array($by => $order),
			$arFilter,
			false,
			false,
			array("ID")
		);
		while ($arResult = $dbResultList->Fetch())
			$arID[] = $arResult['ID'];
	}

	foreach ($arID as $ID)
	{
		if (strlen($ID) <= 0)
			continue;

		switch ($_REQUEST['action'])
		{
			case "delete":
				@set_time_limit(0);

				$DB->StartTransaction();

				if (!CSocNetGroup::Delete($ID))
				{
					$DB->Rollback();

					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(GetMessage("SONET_DELETE_ERROR"), $ID);
				}

				$DB->Commit();

				break;
		}
	}
}

$dbResultList = CSocNetGroup::GetList(
	array($by => $order),
	$arFilter,
	false,
	false,
	array("ID", "SUBJECT_ID", "NAME", "SITE_ID", "OWNER_ID")
);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("SONET_GROUP_NAV")));

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
	array("id"=>"NAME", "content"=>GetMessage("SONET_GROUP_NAME"), "sort"=>"NAME", "default"=>true),
	array("id"=>"SUBJECT_ID", "content"=>GetMessage('SONET_GROUP_SUBJECT_ID'), "sort"=>"SUBJECT_ID", "default"=>true),
	array("id"=>"OWNER_ID", "content"=>GetMessage('SONET_GROUP_OWNER_ID'), "sort"=>"OWNER_ID", "default"=>true),	
));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$arSubjects = array();
$dbSubjectsList = CSocNetGroupSubject::GetList();
while ($arSubject = $dbSubjectsList->Fetch())
	$arSubjects[$arSubject["SITE_ID"]][$arSubject["ID"]] = "[".$arSubject["SITE_ID"]."]&nbsp;".$arSubject["NAME"];

while ($arGroup = $dbResultList->NavNext(true, "f_"))
{
		$arMembers = array();

		$arResult["Users"] = false;
		$dbRequests = CSocNetUserToGroup::GetList(
			array("USER_LAST_NAME" => "ASC", "USER_NAME" => "ASC"),
			array(
				"GROUP_ID" => $arGroup["ID"],
				"<=ROLE" => SONET_ROLES_USER,
				"USER_ACTIVE" => "Y"
			),
			false,
			false,
			array("ID", "USER_ID", "ROLE", "USER_NAME", "USER_LAST_NAME", "USER_LOGIN")
		);
		while ($arRequests = $dbRequests->Fetch())
		{
			$arTmpUser = array(
					"ID" => $arRequests["USER_ID"],
					"NAME" => $arRequests["USER_NAME"],
					"LAST_NAME" => $arRequests["USER_LAST_NAME"],
					"LOGIN" => $arRequests["USER_LOGIN"]
				);
			$arMembers[$arRequests["USER_ID"]] = CUser::FormatName(GetMessage("USER_NAME_TEMPLATE"), $arTmpUser, true, false);
		}


	$row =& $lAdmin->AddRow($f_ID, $arGroup);

	$row->AddField("ID", $f_ID);
	$row->AddInputField("NAME", array("size" => "35"));
	$row->AddSelectField("SUBJECT_ID", $arSubjects[$arGroup["SITE_ID"]], array());
	$row->AddSelectField("OWNER_ID", $arMembers, array());	

	$arActions = Array();
	if ($socialnetworkModulePermissions >= "U")
	{
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("SONET_DELETE_ALT"), "ACTION"=>"if(confirm('".GetMessage('SONET_DELETE_CONF')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
	}

	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array(
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $dbResultList->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),
	)
);

$lAdmin->AddGroupActionTable(
	array(
		"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
	)
);

$lAdmin->CheckListMode();


/****************************************************************************/
/***********  MAIN PAGE  ****************************************************/
/****************************************************************************/
$APPLICATION->SetTitle(GetMessage("SONET_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("SONET_FILTER_SUBJECT_ID"),
		GetMessage("SONET_GROUP_NAME"),
		GetMessage("SONET_OWNER_USER"),
		GetMessage("SONET_OWNER_ID"),
	)
);

$oFilter->Begin();
?>
	<tr>
		<td><?echo GetMessage("SONET_FILTER_SITE_ID")?></td>
		<td><?echo CSite::SelectBox("filter_site_id", $filter_site_id, GetMessage("SONET_SPT_ALL")) ?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("SONET_FILTER_SUBJECT_ID")?>:</td>
		<td>
			<select name="filter_subject_id">
				<option value="NOT_REF"><?= htmlspecialcharsex(GetMessage("SONET_SPT_ALL")); ?></option>
				<?
				foreach ($arSubjects as $subj_site_id=>$arSiteSubjects)
				{
					foreach ($arSiteSubjects as $subject_id=>$sSubjectName)
					{
						?><option value="<?= $subject_id ?>"<?if ($filter_subject_id == $subject_id) echo " selected"?>><?= htmlspecialcharsex($sSubjectName) ?></option><?
					}
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SONET_GROUP_NAME")?>:</td>
		<td><input type="text" name="filter_name" value="<?echo htmlspecialcharsbx($filter_name)?>" size="40"><?=ShowFilterLogicHelp()?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("SONET_OWNER_USER")?>:</td>
		<td>
			<input type="text" name="filter_owner_user" size="50" value="<?= htmlspecialcharsEx($filter_owner_user) ?>">&nbsp;<?=ShowFilterLogicHelp()?>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SONET_OWNER_ID")?>:</td>
		<td>
			<input type="text" name="filter_owner_id" size="5" value="<?= htmlspecialcharsEx($filter_owner_id) ?>">
		</td>
	</tr>


<?
$oFilter->Buttons(
	array(
		"table_id" => $sTableID,
		"url" => $APPLICATION->GetCurPage(),
		"form" => "find_form"
	)
);
$oFilter->End();
?>
</form>

<?
$lAdmin->DisplayList();
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>