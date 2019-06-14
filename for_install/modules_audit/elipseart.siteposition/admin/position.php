<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/elipseart.siteposition/include.php");
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/elipseart.siteposition/prolog.php");
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/elipseart.siteposition/colors.php");

$module_id = "elipseart.siteposition";

if(!CModule::IncludeModule($module_id))
	die();

IncludeModuleLangFile(__FILE__);

$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/admin/position.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/region_list.php"));

$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$APPLICATION->AddHeadString('<link href="/bitrix/js/elipseart.siteposition/layout.css" rel="stylesheet" type="text/css">',true);
$APPLICATION->AddHeadString('<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/bitrix/js/elipseart.siteposition/excanvas.min.js"></script><![endif]-->',true);
$APPLICATION->AddHeadScript('/bitrix/js/elipseart.siteposition/jquery.min.js');
$APPLICATION->AddHeadScript('/bitrix/js/elipseart.siteposition/jquery.flot.min.js');
$APPLICATION->AddHeadScript('/bitrix/js/elipseart.siteposition/jquery.flot.selection.min.js');

CEASitePositionHost::UpdateSiteHost();

$sTableID = "tbl_elipseart_siteposition_position";

if($action == "KEYWORD_ALL_UPDATE" && $POST_RIGHT == "W")
{
	//CEASitePositionUpdate::Update(false,false,"N");
	$returnScript = "<script>".$sTableID.".GetAdminList('/bitrix/admin/elipseart.siteposition.position.php?action=KEYWORD_ALL_UPDATE&amp;lang=".LANG."');</script>";
	$keywordUpdateScript = CEASitePositionUpdate::UpdateAll($returnScript);
}

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

$ssDB = CEASitePositionSearchSystem::GetList(array(),array("ACTIVE"=>"Y"));
while($res = $ssDB->Fetch())
{
	$arSearchSystem[] = $res;
}

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

$arFilterFields = Array(
	"find_SITE_ID",
	"find_HOST_ID",
	"find_ID",
	"find_KEYWORD_NAME",
	"find_REGION_ID",
	/*"find_DATE1",*/
	/*"find_DATE2",*/
);

$lAdmin->InitFilter($arFilterFields);

if($lAdmin->IsDefaultFilter())
{
	//$find_DATE1_DAYS_TO_BACK = 90;
	
	//$find_DATE1 = ConvertTimeStamp(time()-86400*($find_DATE1_DAYS_TO_BACK+1), "SHORT");
	
	//$find_DATE2 = ConvertTimeStamp(time()/*-86400*/, "SHORT");
	
	$find_SITE_ID = array(CMainPage::GetSiteByHost());
	
	$graph_type = COption::GetOptionString($module_id, "GRAPH_TYPE");
	if(empty($graph_type))
		$graph_type = "STD";
	
	$graph_ss = COption::GetOptionString($module_id, "GRAPH_SS");
	if(empty($graph_ss))
		$graph_ss = $arSearchSystem[0]["NAME"];
}

if(empty($find_SITE_ID) && !empty($_GET["SITE_ID"]))
	$find_SITE_ID = intval($_GET["SITE_ID"]);
elseif(empty($find_SITE_ID))
	$find_SITE_ID = array(CMainPage::GetSiteByHost());
/*
if(empty($find_DATE1_DAYS_TO_BACK) && !empty($_GET["DATE1_DAYS_TO_BACK"]))
	$find_DATE1_DAYS_TO_BACK = intval($_GET["DATE1_DAYS_TO_BACK"]);
elseif(empty($find_DATE1_DAYS_TO_BACK))
	$find_DATE1_DAYS_TO_BACK = 90;

if(empty($find_DATE1) && !empty($_GET["DATE1"]))
	$find_DATE1 = htmlspecialchars($_GET["DATE1"]);
elseif(empty($find_DATE1))
	$find_DATE1 = ConvertTimeStamp(time()-86400*($find_DATE1_DAYS_TO_BACK+1), "SHORT");;

if(empty($find_DATE2) && !empty($_GET["DATE2"]))
	$find_DATE2 = htmlspecialchars($_GET["DATE2"]);
elseif(empty($find_DATE2))
	$find_DATE2 = ConvertTimeStamp(time(), "SHORT");;
*/
if(isset($graph_type))
{
	if(empty($graph_type))
		$graph_type = "STD";
}
else
	$graph_type=false;

if(isset($graph_ss))
{
	if(empty($graph_ss))
		$graph_ss = $arSearchSystem[0]["NAME"];
}
else
	$graph_ss=false;

$arSettings = array("saved_graph_type", "saved_graph_ss");
InitFilterEx($arSettings, $sTableID."_settings", "get");
if($graph_type === false)
	$graph_type = $saved_graph_type;
$saved_graph_type = $graph_type;
if($graph_ss === false)
	$graph_ss = $saved_graph_ss;
$saved_graph_ss = $graph_ss;
InitFilterEx($arSettings, $sTableID."_settings", "set");

//if(!CEASitePosition::CheckKey($arSearchSystem))
//	$lAdmin->AddFilterError(GetMessage("KEY_TO_ERROR"));

//CEASitePosition::CheckDate($lAdmin, array("find_DATE1"=>$find_DATE1, "find_DATE2"=>$find_DATE2));

$arFilter = array();

if(CheckFilter())
{
	$arFilter["SITE_ID"] = $find_SITE_ID;
	
	$arFilter["HOST_ID"] = $find_HOST_ID;
	
	$arFilter["ID"] = $find_ID;
	
	$arFilter["NAME"] = $find_KEYWORD_NAME;
	
	$arFilter["REGION_ID"] = $find_REGION_ID;
}

foreach($arFilter as $key => $value)
	if(empty($value))
		unset($arFilter[$key]);

if(empty($by))
	$by = "SORT";
if(empty($order))
	$order = "ASC";

if(!$arFilter || !count($arFilter) > 0)
	$arFilter["ACTIVE"] = "Y";
	
$arFilter["SEARCH_NAME"] = array($graph_ss);

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
    	
    	if($ID == 0 && IntVal($_REQUEST['ID']) > 0)
    		$ID = IntVal($_REQUEST['ID']);
    	
    	if($ID > 0)
    	{
			switch($_REQUEST['action'])
			{
				case "update":
					@set_time_limit(0);
					$DB->StartTransaction();
					if(!CEASitePositionUpdate::Update($ID,false,"N"))
					{
						$DB->Rollback();
						$lAdmin->AddGroupError(GetMessage("EASP_UPD_POS_ERROR"), $ID);
					}
					$DB->Commit();
				break;
	    
				case "reupdate":
					@set_time_limit(0);
					$DB->StartTransaction();
					if(!CEASitePositionUpdate::Update($ID,"reupdate","N"))
					{
						$DB->Rollback();
						$lAdmin->AddGroupError(GetMessage("EASP_REUPD_POS_ERROR"), $ID);
					}
					$DB->Commit();
				break;
			}
		}
	}
}

$lAdmin->BeginPrologContent();

if(!CEASitePosition::CheckKey($arSearchSystem))
	CAdminMessage::ShowMessage(GetMessage("KEY_TO_ERROR"));

if($keywordUpdateScript)
	echo $keywordUpdateScript;

$keyNum = COption::GetOptionString($module_id, "LIST_TOP_SIZE");
if(empty($keyNum))
	$keyNum = 10;

$i = 0;
$rsData = CEASitePositionKeyword::GetList(
	array(
		$by => $order
	),
	$arFilter,
	true,
	true,
	$keyNum
);
while($res = $rsData->Fetch())
{
	foreach($arSearchSystem as $ss)
	{
		if(
			!empty($res["POSITION_".$ss["NAME"]]) && $res["POSITION_".$ss["NAME"]]>=0
			&&
			!empty($res["LAST_POSITION_".$ss["NAME"]]) && $res["LAST_POSITION_".$ss["NAME"]]>=0
		)
		{
			$arParamValid = true;
		}
	}
	
	$arParam["KEYWORD_ID"][] = $res["ID"];
	$arrLegend["NAME"] = $res["NAME"]; 
	$arrLegend["COLOR"] = $arColor[$i];
	$arLegend[] = $arrLegend;
	
	++$i;
}

$arParam["SEARCH_NAME"] = $arFilter["SEARCH_NAME"];

if($arParamValid /*&& $find_DATE1_DAYS_TO_BACK > 1*/)
{
	?>
	<h2><?=GetMessage("STAT_GRAPH_POSITION")?> <?=GetMessage("STAT_SS_".$arParam["SEARCH_NAME"][0])?></h2>
	<div class="graph">
		<?
		include($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/elipseart.siteposition.position_dynamic_graph.php");
		?>
	</div>
	<?
}
else
{
	CAdminMessage::ShowMessage(GetMessage("STAT_NOT_ENOUGH_DATA"));
}

$lAdmin->EndPrologContent();

for($x=0;$x<count($arSearchSystem);++$x)
{
	$arFilter["SEARCH_NAME"][] = $arSearchSystem[$x]["NAME"];
}

$arData = array();
$rsData = CEASitePositionKeyword::GetList(
	array(
		$by => $order
	),
	$arFilter,
	true,
	true,
	false
);
while($res = $rsData->Fetch())
{
	$res["REGION_NAME"] = GetMessage("REG_".$res["REGION_CODE"]);
	/*foreach($arSearchSystem as $ss)
	{
		if($res["POSITION_".$ss["NAME"]] == "")
			$res["POSITION_".$ss["NAME"]] = "na";
	}*/
	
	$arData[] = $res;
}
$rs = new CDBResult;
$rs->InitFromArray($arData);
$rsData = new CAdminResult($rs, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("STAT_KEYWORD")));

$AdminHeaders[] = array(
	"id" => "ID",
	"content" => GetMessage("STAT_ID"),
	"sort" => "ID",
	"default" => true,
);
$AdminHeaders[] = array(
	"id" => "SITE_ID",
	"content" => GetMessage("STAT_SITE"),
	"sort" => "SITE_ID",
	"default" => true,
);
$AdminHeaders[] = array(
	"id" => "HOST_NAME",
	"content" => GetMessage("STAT_HOST"),
	"sort" => "HOST_NAME",
	"default" => true,
);
$AdminHeaders[] = array(
	"id" => "NAME",
	"content" => GetMessage("STAT_KEYWORD_NAME"),
	"sort" => "NAME",
	"default" => true,
);
$AdminHeaders[] = array(
	"id" => "REGION_NAME",
	"content" => GetMessage("STAT_REGION"),
	"sort" => "REGION_CODE",
	"default" => true,
);
foreach($arSearchSystem as $ss)
{
	$AdminHeaders[] = array(
		"id" => "POSITION_".$ss["NAME"],
		"content" => GetMessage("STAT_SS_".$ss["NAME"]),
		"sort" => "POSITION_".$ss["NAME"],
		"default" => true,
	);
}
$AdminHeaders[] = array(
	"id" => "SORT",
	"content" => GetMessage("STAT_SORT"),
	"sort" => "SORT",
	"default" => true,
);

$lAdmin->AddHeaders($AdminHeaders);

while($arRes = $rsData->NavNext(true, "f_")):

	$row =& $lAdmin->AddRow($f_ID, $arRes);
	
	$row->AddViewField("NAME", '<a href="elipseart.siteposition.stat.php?lang='.LANG.'&set_default=Y&ID='.$f_ID.'">'.$f_NAME.'</a>');
	
	foreach($arSearchSystem as $ss)
	{
		eval("\$f_POSITION = \$f_POSITION_".$ss["NAME"].";");
		eval("\$f_LAST_POSITION = \$f_LAST_POSITION_".$ss["NAME"].";");
		
		if(!empty($f_POSITION) && !empty($f_LAST_POSITION) && $f_POSITION < $f_LAST_POSITION)
			$CHANGE = ' <img src="/bitrix/images/elipseart.siteposition/up.gif" width="10" height="11" /> +'.($f_LAST_POSITION - $f_POSITION);
		elseif(!empty($f_POSITION) && !empty($f_LAST_POSITION) && $f_POSITION > $f_LAST_POSITION)
			$CHANGE = '<img src="/bitrix/images/elipseart.siteposition/down.gif" width="10" height="11" /> &ndash;'.($f_POSITION - $f_LAST_POSITION);
		else
			$CHANGE = '';
			
		if($f_POSITION > 0)
			$PositionValue = "<table><tr><td width=\"25\">".$f_POSITION."</td><td width=\"40\">".$CHANGE."</td></tr></table>";
		elseif($f_POSITION == "na")
			$PositionValue = '<img src="/bitrix/images/elipseart.siteposition/na.gif" width="18" height="11" />';
		else
			$PositionValue = "&mdash;";//">100";
			
		$row->AddViewField("POSITION_".$ss["NAME"], $PositionValue);
	}
	
	$row->AddViewField("SORT", $f_SORT);
	
	$arActions = Array();
	
	$arActions[] = array(
		"ICON" => "elipseart_siteposition_icon_stat",
		"DEFAULT" => true,
		"TEXT" => GetMessage("STAT_VIEW_GRAPH"),
		"ACTION" => $lAdmin->ActionRedirect("elipseart.siteposition.stat.php?lang=".LANG."&set_default=Y&ID=".$f_ID)
	);
	
	if($POST_RIGHT == "W")
	{
		$arActions[] = array(
			"ICON" => "elipseart_siteposition_icon_upd",
			"DEFAULT" => false,
			"TEXT" => GetMessage("STAT_KEYWORD_UPDATE"),
			"ACTION" => $lAdmin->ActionDoGroup(0, "", "action=update&ID=".$f_ID)
		);
		$arActions[] = array(
			"ICON" => "elipseart_siteposition_icon_upd",
			"DEFAULT" => false,
			"TEXT" => GetMessage("STAT_KEYWORD_REUPDATE"),
			"ACTION" => $lAdmin->ActionDoGroup(0, "", "action=reupdate&ID=".$f_ID)
		);
	}
	
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
		"update"=>GetMessage("STAT_KEYWORD_UPDATE"),
		"reupdate"=>GetMessage("STAT_KEYWORD_REUPDATE"),
	));
}

foreach($arSearchSystem as $val)
{
	$arMenu[] = array(
		"TEXT" => GetMessage("STAT_SS_".$val["NAME"]),
		"ACTION" => $lAdmin->ActionDoGroup(0, "", "graph_ss=".$val["NAME"]),
		"ICON" => ($graph_ss == $val["NAME"] ? "checked" : ""),
	);
}

$aContext = array(
	array(
		"TEXT" => GetMessage("STAT_GRAPH_TYPE"),
		"MENU" => array(
			array(
				"TEXT" => GetMessage("STAT_GRAPH_TYPE_STD"),
				"ACTION" => $lAdmin->ActionDoGroup(0, "", "graph_type=STD"),
				"ICON" => ($graph_type == "STD" ? "checked" : ""),
			),
			array(
				"TEXT" => GetMessage("STAT_GRAPH_TYPE_TOP10"),
				"ACTION" => $lAdmin->ActionDoGroup(0, "", "graph_type=TOP10"),
				"ICON" => ($graph_type == "TOP10" ? "checked" : ""),
			),
		),
	),
	array(
		"TEXT" => GetMessage("STAT_SEARCH_SYSTEM"),
		"MENU" => $arMenu,
	),
);

if($POST_RIGHT == "W")
{
	$aContext[] = array(
		"TEXT" => GetMessage("STAT_KEYWORD_ALL_UPDATE"),
		"LINK" => "javascript:void(0);",
		"LINK_PARAM" => "onclick = \"".$sTableID.".GetAdminList('/bitrix/admin/elipseart.siteposition.position.php?action=KEYWORD_ALL_UPDATE&amp;lang=".LANG."');\"",
		"ICON" => "elipseart_siteposition_icon_upd",
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
			//"find_STAT_SITE" => GetMessage("STAT_SITE"),
			"find_HOST_NAME" => GetMessage("STAT_HOST"),
			"find_ID" => GetMessage("STAT_ID"),
			"find_KEYWORD_NAME" => GetMessage("STAT_KEYWORD_NAME"),
			"find_REGION_ID" => GetMessage("STAT_REGION"),
        )
);
$oFilter->Begin();?>
	<?/*<tr>
		<td><?echo GetMessage("STAT_PERIOD")." (".FORMAT_DATE."):"?></td>
		<td><?echo CalendarPeriod("find_DATE1", $find_DATE1, "find_DATE2", $find_DATE2, "find_form", "Y")?></td>
	</tr>*/?>
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
		<td><?echo GetMessage("STAT_REGION")?>:</td>
		<td><?echo SelectBoxMFromArray("find_REGION_ID[]", $arRegionDropdown, $find_REGION_ID, "", "");?></td>
	</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage()));
$oFilter->End();
?>
</form>
<?

$lAdmin->DisplayList();



require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>