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
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/admin/stat.php"));
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

if($action == "KEYWORD_UPDATE" && intval($_GET["ID"]) > 0 && $POST_RIGHT == "W")
	CEASitePositionUpdate::Update(intval($_GET["ID"]),false,"N");

if($action == "KEYWORD_REUPDATE" && intval($_GET["ID"]) > 0 && $POST_RIGHT == "W")
	CEASitePositionUpdate::Update(intval($_GET["ID"]),"reupdate","N");

$sTableID = "tbl_elipseart_siteposition_stat";
$oSort = new CAdminSorting($sTableID, "DATE", "desc");
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

function CheckFilter()
{
	global $FilterArr, $lAdmin;
	//foreach ($FilterArr as $f) global $$f;
		
	return count($lAdmin->arFilterErrors) == 0;
}

$arFilterFields = Array(
	"find_ID",
	//"find_DATE1",
	//"find_DATE2",
);

$lAdmin->InitFilter($arFilterFields);

if($lAdmin->IsDefaultFilter())
{
	//if($_GET["ID"] > 0)
	//	$find_ID = intval($_GET["ID"]);
	//$find_DATE1_DAYS_TO_BACK = 90;
	
	//$find_DATE1 = ConvertTimeStamp(time()-86400*$find_DATE1_DAYS_TO_BACK, "SHORT");
	
	//$find_DATE2 = ConvertTimeStamp(time()/*-86400*/, "SHORT");
	/*
	$graph_type = COption::GetOptionString($module_id, "GRAPH_TYPE");
	if(empty($graph_type))
		$graph_type = "STD";
	*/
}

foreach($arSearchSystem as $val)
	$graph_ss[] = $val["NAME"];

if($_GET["ID"] > 0)
	$find_ID = intval($_GET["ID"]);

$def_option = "&ID=".$find_ID;
/*
if(empty($find_DATE1_DAYS_TO_BACK) && !empty($_GET["DATE1_DAYS_TO_BACK"]))
	$find_DATE1_DAYS_TO_BACK = intval($_GET["DATE1_DAYS_TO_BACK"]);
elseif(empty($find_DATE1_DAYS_TO_BACK))
	$find_DATE1_DAYS_TO_BACK = 90;

if(empty($find_DATE1) && !empty($_GET["DATE1"]))
	$find_DATE1 = htmlspecialchars($_GET["DATE1"]);
elseif(empty($find_DATE1))
	$find_DATE1 = ConvertTimeStamp(time()-86400*$find_DATE1_DAYS_TO_BACK, "SHORT");;

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
{
	$graph_type = COption::GetOptionString($module_id, "GRAPH_TYPE");
}

$arSettings = array("saved_graph_type");
InitFilterEx($arSettings, $sTableID."_settings", "get");
if($graph_type === false)
	$graph_type = $saved_graph_type;
$saved_graph_type = $graph_type;
InitFilterEx($arSettings, $sTableID."_settings", "set");

//if(!CEASitePosition::CheckKey($arSearchSystem))
	//$lAdmin->AddFilterError(GetMessage("KEY_TO_ERROR"));

//CEASitePosition::CheckDate($lAdmin, array("find_DATE1"=>$find_DATE1, "find_DATE2"=>$find_DATE2));

$arFilter = array();

if(CheckFilter())
{
	$arFilter["KEYWORD_ID"] = $find_ID;
	/*
	$date_end = explode("-",ConvertDateTime($find_DATE2, "YYYY-MM-DD"));
	$date_end = mktime(0,0,0,$date_end[1],$date_end[2],$date_end[0]);
	$date_end = ConvertTimeStamp($date_end+86400, "SHORT");
	
	$date_beg = explode("-",ConvertDateTime($find_DATE2, "YYYY-MM-DD"));
	$date_beg = mktime(0,0,0,$date_beg[1],$date_beg[2],$date_beg[0]);
	$date_beg = ConvertTimeStamp($date_beg-86400*$find_DATE1_DAYS_TO_BACK, "SHORT");
	
	$arFilter[">DATE"] = ConvertDateTime($date_beg, "YYYY-MM-DD");
	$arFilter["<DATE"] = ConvertDateTime($date_end, "YYYY-MM-DD");
	*/
}

foreach($arFilter as $key => $value)
	if(empty($value))
		unset($arFilter[$key]);

if(empty($by))
	$by = "DATE";
if(empty($order))
	$order = "DESC";

$arFilter["SEARCH_NAME"] = $graph_ss;

$lAdmin->BeginPrologContent();

if(!CEASitePosition::CheckKey($arSearchSystem))
	CAdminMessage::ShowMessage(GetMessage("KEY_TO_ERROR"));

if($find_ID > 0)
{	
	$rsData = CEASitePosition::GetList(
		array(
			"DATE" => "DESC"
		),
		$arFilter,
		false
	);
	if($rsData->SelectedRowsCount() > 2)
		$arParamValid = true;
	
	if($res = $rsData->Fetch())
		$keywordName = $res["NAME"];
	
	$arColor = array(
		"#ff000a",
		"#2f64de",
		"#ffa614"
	);
	
	$i = 0;
	foreach($arSearchSystem as $val)
	{
		$arrLegend["NAME"] = GetMessage("STAT_SS_".$val["NAME"]); 
		$arrLegend["COLOR"] = $arColor[$i];
		$arLegend[] = $arrLegend;
		++$i;
	}
	
	$arParam["KEYWORD_ID"] = $find_ID;
	$arParam["SEARCH_NAME"] = $arFilter["SEARCH_NAME"];
	
	if($arParamValid /*&& $find_DATE1_DAYS_TO_BACK > 1*/)
	{
		?>
		<h2><?
		if(!empty($keywordName))
			echo GetMessage("STAT_GRAPH_POSITION_FULL")." \"".$keywordName."\"";
		else
			echo GetMessage("STAT_GRAPH_POSITION");
		?></h2>
		<div class="graph">
			<?
			include($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/elipseart.siteposition.stat_dynamic_graph.php");
			?>
		</div>
		<?
	}
	else
	{
		CAdminMessage::ShowMessage(GetMessage("STAT_NOT_ENOUGH_DATA"));
	}
}
else
{
	CAdminMessage::ShowMessage(GetMessage("STAT_NOT_ENOUGH_DATA"));
}

$lAdmin->EndPrologContent();

if($find_ID > 0)
{
	$arDataN = array();
	$arrDataN = array();
	$rsDataN = array();
	
	$rsData = CEASitePosition::GetList(
		array(
			$by => $order
		),
		$arFilter,
		false
	);
	while($res = $rsData->fetch())
	{
		$date = explode(" ",$res["DATE"]);
		$res["DATE"] = $date[0];
		$rsDataN[] = $res;
	}
	
	$i = 0;
	foreach($rsDataN as $key=>$val)
	{
		$val["REGION_NAME"] = GetMessage("REG_".$val["REGION_CODE"]);
		if(!$arDataN[$val["DATE"]])
			$arDataN[$val["DATE"]] = $val;
		$arDataN[$val["DATE"]]["POSITION_".$val["SEARCH_SYSTEM"]] = $val["POSITION"];
		unset($arDataN[$val["DATE"]]["POSITION"]);
		unset($arDataN[$val["DATE"]]["SEARCH_SYSTEM"]);
		unset($arDataN[$val["DATE"]]["SEARCH_ID"]);
		
		++$i;
	}
	
	foreach($arDataN as $key=>$val)
	{
		foreach($arSearchSystem as $ss)
		{
			if(!isset($val["POSITION_".$ss["NAME"]]))
				$val["POSITION_".$ss["NAME"]] = "";
		}
		$arrDataN[] = $val;
	}
}
else
{
	$arrDataN = array();
}

$rs = new CDBResult;
$rs->InitFromArray($arrDataN);
$rsData = new CAdminResult($rs, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("STAT_KEYWORD")));
/*
$AdminHeaders[] = array(
	"id" => "ID",
	"content" => GetMessage("STAT_ID"),
	"sort" => "ID",
	"default" => true,
);*/
$AdminHeaders[] = array(
	"id" => "SITE_ID",
		"content" => GetMessage("STAT_SITE"),
		"sort" => "",
		"default" => true,
);
$AdminHeaders[] = array(
	"id" => "HOST_NAME",
		"content" => GetMessage("STAT_HOST"),
		"sort" => "",
		"default" => true,
);
$AdminHeaders[] = array(
	"id" => "NAME",
		"content" => GetMessage("STAT_KEYWORD_NAME"),
		"sort" => "",
		"default" => true,
);
$AdminHeaders[] = array(
	"id" => "REGION_NAME",
		"content" => GetMessage("STAT_REGION"),
		"sort" => "",
		"default" => true,
);
foreach($arSearchSystem as $ss)
{
	$AdminHeaders[] = array(
		"id" => "POSITION_".$ss["NAME"],
		"content" => GetMessage("STAT_SS_".$ss["NAME"]),
		"sort" => "",//"POSITION_".$ss["NAME"],
		"default" => true,
	);
}
$AdminHeaders[] = array(
	"id" => "DATE",
		"content" => GetMessage("STAT_DATE"),
		"sort" => "",//"DATE",
		"default" => true,
);

$lAdmin->AddHeaders($AdminHeaders);

while($arRes = $rsData->NavNext(true, "f_")):

	$row =& $lAdmin->AddRow($f_ID, $arRes);
	
	foreach($arSearchSystem as $ss)
	{
		eval("\$f_POSITION = \$f_POSITION_".$ss["NAME"].";");
			
		if($f_POSITION > 0)
			$PositionValue = $f_POSITION;
		elseif($f_POSITION == "na")
			$PositionValue = '<img src="/bitrix/images/elipseart.siteposition/na.gif" width="18" height="11" />';
		else
			$PositionValue = "&mdash;";//">100";
			
		$row->AddViewField("POSITION_".$ss["NAME"], $PositionValue);
	}
	
	$date = $f_DATE;
	$format = "YYYY-MM-DD";
	$new_format = CSite::GetDateFormat("SHORT");
	$new_date = $DB->FormatDate($date, $format, $new_format);
	$row->AddViewField("DATE", $new_date);
	
	$arActions = Array();
	
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

$lAdmin->AddGroupActionTable(Array(
	//"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
	//"activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
	//"deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
));

$aContext = array(
	array(
		"TEXT" => GetMessage("STAT_GRAPH_TYPE"),
		"MENU" => array(
			array(
				"TEXT" => GetMessage("STAT_GRAPH_TYPE_STD"),
				"ACTION" => $lAdmin->ActionDoGroup(0, "", "graph_type=STD".$def_option),
				"ICON" => ($graph_type == "STD" ? "checked" : ""),
			),
			array(
				"TEXT" => GetMessage("STAT_GRAPH_TYPE_TOP10"),
				"ACTION" => $lAdmin->ActionDoGroup(0, "", "graph_type=TOP10".$def_option),
				"ICON" => ($graph_type == "TOP10" ? "checked" : ""),
			),
		),
	),
);

if($POST_RIGHT == "W")
{
	$aContext[] = array(
		"TEXT" => GetMessage("STAT_KEYWORD_UPDATE"),
		"LINK" => "javascript:void(0);",
		"LINK_PARAM" => "onclick = \"".$sTableID.".GetAdminList('/bitrix/admin/elipseart.siteposition.stat.php?action=KEYWORD_UPDATE&amp;lang=".LANG."&amp;ID=".$find_ID."');\"",
		"ICON" => "elipseart_siteposition_icon_upd",
	);
	$aContext[] = array(
		"TEXT" => GetMessage("STAT_KEYWORD_REUPDATE"),
		"LINK" => "javascript:void(0);",
		"LINK_PARAM" => "onclick = \"".$sTableID.".GetAdminList('/bitrix/admin/elipseart.siteposition.stat.php?action=KEYWORD_REUPDATE&amp;lang=".LANG."&amp;ID=".$find_ID."');\"",
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
			//"find_ID" => GetMessage("STAT_ID"),
        )
);
$oFilter->Begin();?>
	<?/*<tr>
		<td><?echo GetMessage("STAT_PERIOD")." (".FORMAT_DATE."):"?></td>
		<td><?echo CalendarPeriod("find_DATE1", $find_DATE1, "find_DATE2", $find_DATE2, "find_form", "Y")?></td>
	</tr>*/?>
	<tr>
		<td><?=GetMessage("STAT_ID")?>:</td>
		<td>
			<input type="text" name="find_ID" size="47" value="<?echo htmlspecialchars($find_ID)?>" />
		</td>
	</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage()));
$oFilter->End();
?>
</form>
<?

$lAdmin->DisplayList();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>