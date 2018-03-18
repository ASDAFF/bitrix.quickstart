<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/elipseart.siteposition/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/elipseart.siteposition/prolog.php");

IncludeModuleLangFile(__FILE__);

$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/admin/keyword.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/region_list.php"));

global $DB;
global $APPLICATION;

$POST_RIGHT = $APPLICATION->GetGroupRight("elipseart.siteposition");
if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));	

CEASitePositionHost::UpdateSiteHost();

$sTableID = "tbl_elipseart_siteposition_keyword";
$oSort = new CAdminSorting($sTableID, "SORT", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$ref = $ref_id = array();
$rs = CSite::GetList(($v1="sort"), ($v2="asc"));
while ($ar = $rs->Fetch())
{
	$ref[] = "[".$ar["ID"]."] ".$ar["NAME"];
	$ref_id[] = $ar["ID"];
}
$arSiteDropdown = array("reference" => $ref, "reference_id" => $ref_id);

$ref = $ref_id = $arRegion = array();
$rs = CEASitePositionRegion::GetList();
while ($ar = $rs->Fetch())
{
	$region_name = GetMessage("REG_".$ar["CODE"]);
	if(!empty($region_name))
	{
		$arRegion[$ar["ID"]] = $region_name;
	}
}
asort($arRegion);
foreach($arRegion as $key=>$val)
{
	$ref[] = "[".$key."] ".$val;
	$ref_id[] = $key;
}
$arRegionDropdown = array("reference" => $ref, "reference_id" => $ref_id);

$ref = $ref_id = array();
$rs = CEASitePositionHost::GetList();
while ($ar = $rs->Fetch())
{
	$ref[] = "[".$ar["ID"]."] ".$ar["NAME"];
	$ref_id[] = $ar["ID"];
}
$arHostDropdown = array("reference" => $ref, "reference_id" => $ref_id);

function CheckFilter()
{
	global $FilterArr, $lAdmin;
	//foreach ($FilterArr as $f) global $$f;
		
	return count($lAdmin->arFilterErrors) == 0;
}

$FilterArr = Array(
	"find_SITE_ID",
	"find_HOST_ID",
	"find_ID",
	"find_KEYWORD_NAME",
	"find_ACTIVE",
	"find_REGION_ID",
);

$lAdmin->InitFilter($FilterArr);

if($lAdmin->IsDefaultFilter())
{
	$find_SITE_ID = array(CMainPage::GetSiteByHost());
}

$arFilter = array();

if(CheckFilter())
{
	$arFilter["SITE_ID"] = $find_SITE_ID;
	
	$arFilter["HOST_ID"] = $find_HOST_ID;
	
	$arFilter["ID"] = $find_ID;
	
	$arFilter["NAME"] = $find_KEYWORD_NAME;
	
	$arFilter["ACTIVE"] = $find_ACTIVE;
	
	$arFilter["REGION_ID"] = $find_REGION_ID;
}

foreach($arFilter as $key => $value)
	if(empty($value))
		unset($arFilter[$key]);

if($lAdmin->EditAction() && $POST_RIGHT == "W")
{
	foreach($FIELDS as $ID => $arFields)
	{
		if(!$lAdmin->IsUpdated($ID))
			continue;
		
		$DB->StartTransaction();
		$ID = IntVal($ID);
		$rsData = CEASitePositionKeyword::GetList(array(),array("ID"=>$ID),false,false,1);
		if($arData = $rsData->Fetch())
		{
			foreach($arFields as $key => $value)
				$arData[$key] = $value;
			if(!CEASitePositionKeyword::Update($ID, $arData["HOST_ID"], $arData["REGION_ID"], $arData))
			{
				$lAdmin->AddGroupError(GetMessage("EASP_SAVE_ERROR")." ".$cData->LAST_ERROR, $ID);
				$DB->Rollback();
			}
		}
		else
		{
			$lAdmin->AddGroupError(GetMessage("EASP_SAVE_ERROR")." ".GetMessage("EASP_ERROR_NO_KEYWORD"), $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}

if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT == "W")
{
	if($_REQUEST["action_target"] == "selected")
	{
		$rsData = CEASitePositionKeyword::GetList(array($by=>$order), $arFilter);
		while($arRes = $rsData->Fetch())
		{
			$arID[] = $arRes['ID'];
		}
	}

	foreach($arID as $ID)
	{
		if(strlen($ID)<=0)
			continue;
		
		$ID = IntVal($ID);
    
		switch($_REQUEST['action'])
		{
			case "delete":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!CEASitePositionKeyword::Delete($ID))
				{
					$DB->Rollback();
					$lAdmin->AddGroupError(GetMessage("EASP_DEL_ERROR"), $ID);
				}
				$DB->Commit();
			break;
    
			case "activate":
			case "deactivate":
				$rsData = CEASitePositionKeyword::GetList(array(),array("ID"=>$ID),false,false,1);
				if($arData = $rsData->Fetch())
				{
					$arData["ACTIVE"] = ($_REQUEST['action'] == "activate" ? "Y" : "N");
					if(!CEASitePositionKeyword::Update($ID, $arData["HOST_ID"], $arData["REGION_ID"], $arData))
						$lAdmin->AddGroupError(GetMessage("EASP_SAVE_ERROR").$cData->LAST_ERROR, $ID);
				}
				else
					$lAdmin->AddGroupError(GetMessage("EASP_SAVE_ERROR")." ".GetMessage("EASP_ERROR_NO_KEYWORD"), $ID);
			break;
		}
	}
}

if(empty($by))
	$by = "SORT";
if(empty($order))
	$order = "ASC";

$lAdmin->BeginPrologContent();

if(!CEASitePosition::CheckKey($arSearchSystem))
	CAdminMessage::ShowMessage(GetMessage("KEY_TO_ERROR"));
	
$lAdmin->EndPrologContent();

$arData = array();
$rsData = CEASitePositionKeyword::GetList(
	array(
		$by => $order
	),
	$arFilter,
	false,
	false,
	false
);
while($res = $rsData->Fetch())
{
	$res["REGION_NAME"] = GetMessage("REG_".$res["REGION_CODE"]);
	$arData[] = $res;
}

$rs = new CDBResult;
$rs->InitFromArray($arData);
$rsData = new CAdminResult($rs, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("STAT_KEYWORD")));

$lAdmin->AddHeaders(array(
	array(
		"id" => "ID",
		"content" => "ID",
		"sort" => "ID",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "NAME",
		"content" => GetMessage("STAT_KEYWORD_NAME"),
		"sort" => "NAME",
		"default" => true,
	),
	array(
		"id" => "ACTIVE",
		"content" => GetMessage("STAT_ACTIVE"),
		"sort" => "ACTIVE",
		"align" => "center",
		"default" => true,
	),
	array(
		"id" => "SORT",
		"content" => GetMessage("STAT_SORT"),
		"sort" => "SORT",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "SITE_ID",
		"content" => GetMessage("STAT_SITE"),
		"sort" => "SITE_ID",
		"default" => true,
	),
	array(
		"id" => "HOST_NAME",
		"content" => GetMessage("STAT_HOST"),
		"sort" => "HOST_NAME",
		"default" => true,
	),
	array(
		"id" => "REGION_NAME",
		"content" => GetMessage("STAT_REGION"),
		"sort" => "REGION_NAME",
		"default" => true,
	),
));

while($arRes = $rsData->NavNext(true, "f_")):

	$row =& $lAdmin->AddRow($f_ID, $arRes);
	
	if ($POST_RIGHT == "W")
		$row->AddViewField("NAME", !empty($f_NAME) ? '<a href="elipseart.siteposition.keyword_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_NAME.'</a>' : '');
	else
		$row->AddViewField("NAME", $f_NAME);
	
	$row->AddViewField("ACTIVE", $f_ACTIVE == "Y" ? GetMessage("YES") : GetMessage("NO"));
	if ($POST_RIGHT == "W")
		$row->AddCheckField("ACTIVE");
	
	$row->AddViewField("SORT", $f_SORT);
	if ($POST_RIGHT == "W")
		$row->AddInputField("SORT", array("size"=>5));
	
	$row->AddViewField("SITE_ID", $f_SITE_ID);
	
	$row->AddViewField("HOST_NAME", $f_HOST_NAME);
	
	$row->AddViewField("REGION_NAME", $f_REGION_NAME);
	
	$arActions = Array();
	
	if ($POST_RIGHT == "W")
		$arActions[] = array(
			"ICON"=>"edit",
			"DEFAULT"=>true,
			"TEXT"=>GetMessage("EDIT"),
			"ACTION"=>$lAdmin->ActionRedirect("elipseart.siteposition.keyword_edit.php?ID=".$f_ID."&lang=".LANG)
		);
	
	if ($POST_RIGHT == "W")
		$arActions[] = array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("DELETE"),
			"ACTION"=>"if(confirm('".GetMessage('DELETE')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
		);
		
	$arActions[] = array("SEPARATOR"=>true);
			
	if(is_set($arActions[count($arActions)-1], "SEPARATOR"))
		unset($arActions[count($arActions)-1]);
		
	$row->AddActions($arActions);
endwhile;

$lAdmin->AddFooter(
	array(
		array(
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $rsData->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),
	)
);

if($POST_RIGHT == "W")
{
	$lAdmin->AddGroupActionTable(Array(
		"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
		"activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
		"deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
	));
}

$aContext = array();

if($POST_RIGHT == "W")
{
	$aContext[] = array(
		"TEXT" => GetMessage("KEYWORD_ADD"),
		"LINK" => "elipseart.siteposition.keyword_edit.php?lang=".LANG,
		"TITLE" => GetMessage("KEYWORD_ADD_TITLE"),
		"ICON" => "btn_new",
	);
}

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("STAT_POSITION_LIST"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?
$oFilter = new CAdminFilter(
        $sTableID."_filter",
        array(
			//"find_SITE_ID" => GetMessage("STAT_SITE"),
			"find_HOST_NAME" => GetMessage("STAT_HOST"),
			"find_ID" => GetMessage("STAT_ID"),
			"find_KEYWORD_NAME" => GetMessage("STAT_KEYWORD_NAME"),
			"find_ACTIVE" => GetMessage("STAT_ACTIVE"),
			"find_REGION_ID" => GetMessage("STAT_REGION"),
        )
);
$oFilter->Begin();?>
	<tr>
		<td><?echo GetMessage("STAT_SITE")?>:</td>
		<td><?echo SelectBoxMFromArray("find_SITE_ID[]", $arSiteDropdown, $find_SITE_ID, "", "");?></td>
	</tr>
	<tr>
		<td><?=GetMessage("STAT_HOST")?>:</td>
		<td><?echo SelectBoxMFromArray("find_HOST_ID[]", $arHostDropdown, $find_HOST_ID, "", "");?></td>
	</tr>
	<tr>
		<td><?=GetMessage("STAT_ID")?>:</td>
		<td>
			<input type="text" name="find_ID" size="47" value="<?echo htmlspecialchars($find_ID)?>" />
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("STAT_KEYWORD_NAME")?>:</td>
		<td>
			<input type="text" name="find_KEYWORD_NAME" size="47" value="<?echo htmlspecialchars($find_KEYWORD_NAME)?>" />
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("STAT_ACTIVE")?>:</td>
		<td>
			<?
			$arr = array(
				"reference" => array(
					GetMessage("YES"),
					GetMessage("NO"),
				),
				"reference_id" => array(
					"Y",
					"N",
				)
			);
			echo SelectBoxFromArray("find_ACTIVE", $arr, $find_ACTIVE, "", "");
			?>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("STAT_REGION")?>:</td>
		<td><?echo SelectBoxMFromArray("find_REGION_ID[]", $arRegionDropdown, $find_REGION_ID, "", "");?></td>
	</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
$oFilter->End();
?>
</form>
<?

$lAdmin->DisplayList();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>