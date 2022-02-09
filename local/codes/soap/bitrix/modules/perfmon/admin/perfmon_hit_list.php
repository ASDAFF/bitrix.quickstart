<?
define("ADMIN_MODULE_NAME", "perfmon");
define("PERFMON_STOP", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/perfmon/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/perfmon/prolog.php");

IncludeModuleLangFile(__FILE__);

$RIGHT = $APPLICATION->GetGroupRight("perfmon");
if($RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$sTableID = "tbl_perfmon_hit_list";
$oSort = new CAdminSorting($sTableID, "PAGE_TIME", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$FilterArr = Array(
	"find",
	"find_type",
	"find_script_name",
	"find_id",
	"find_is_admin",
	"find_request_method",
);

$lAdmin->InitFilter($FilterArr);

$arFilter = Array(
	"=SCRIPT_NAME" => ($find!="" && $find_type == "script_name"? $find: $find_script_name),
	"=ID" => ($find!="" && $find_type == "id"? $find: $find_id),
	"=IS_ADMIN" => $find_is_admin,
	"=REQUEST_METHOD" => $find_request_method,
);
foreach($arFilter as $key=>$value)
	if(!$value)
		unset($arFilter[$key]);

$lAdmin->AddHeaders(array(
	array(
		"id" => "ID",
		"content" => GetMessage("PERFMON_HIT_ID"),
		"sort" => "ID",
		"align" => "right",
	),
	array(
		"id" => "DATE_HIT",
		"content" => GetMessage("PERFMON_HIT_DATE_HIT"),
		"sort" => "DATE_HIT",
		"align" => "right",
	),
	array(
		"id" => "IS_ADMIN",
		"content" => GetMessage("PERFMON_HIT_IS_ADMIN"),
		"sort" => "IS_ADMIN",
	),
	array(
		"id" => "REQUEST_METHOD",
		"content" => GetMessage("PERFMON_HIT_REQUEST_METHOD"),
		"sort" => "REQUEST_METHOD",
	),
	array(
		"id" => "SERVER_NAME",
		"content" => GetMessage("PERFMON_HIT_SERVER_NAME"),
		"sort" => "SERVER_NAME",
	),
	array(
		"id" => "SERVER_PORT",
		"content" => GetMessage("PERFMON_HIT_SERVER_PORT"),
		"sort" => "SERVER_PORT",
	),
	array(
		"id" => "SCRIPT_NAME",
		"content" => GetMessage("PERFMON_HIT_SCRIPT_NAME"),
		"sort" => "SCRIPT_NAME",
	),
	array(
		"id" => "REQUEST_URI",
		"content" => GetMessage("PERFMON_HIT_REQUEST_URI2"),
		"sort" => "REQUEST_URI",
		"default" => true,
	),
	array(
		"id" => "PAGE_TIME",
		"content" => GetMessage("PERFMON_HIT_PAGE_TIME"),
		"sort" => "PAGE_TIME",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "COMPONENTS",
		"content" => GetMessage("PERFMON_HIT_COMPONENTS"),
		"sort" => "COMPONENTS",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "COMPONENTS_TIME",
		"content" => GetMessage("PERFMON_HIT_COMPONENTS_TIME"),
		"sort" => "COMPONENTS_TIME",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "INCLUDED_FILES",
		"content" => GetMessage("PERFMON_HIT_INCLUDED_FILES"),
		"sort" => "INCLUDED_FILES",
		"align" => "right",
	),
	array(
		"id" => "MEMORY_PEAK_USAGE",
		"content" => GetMessage("PERFMON_HIT_MEMORY_PEAK_USAGE"),
		"sort" => "MEMORY_PEAK_USAGE",
		"align" => "right",
	),
	array(
		"id" => "CACHE_SIZE",
		"content" => GetMessage("PERFMON_HIT_CACHE_SIZE"),
		"sort" => "CACHE_SIZE",
		"align" => "right",
	),
	array(
		"id" => "QUERIES",
		"content" => GetMessage("PERFMON_HIT_QUERIES"),
		"sort" => "QUERIES",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "QUERIES_TIME",
		"content" => GetMessage("PERFMON_HIT_QUERIES_TIME"),
		"sort" => "QUERIES_TIME",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "PROLOG_TIME",
		"content" => GetMessage("PERFMON_HIT_PROLOG_TIME"),
		"sort" => "PROLOG_TIME",
		"align" => "right",
	),
	array(
		"id" => "AGENTS_TIME",
		"content" => GetMessage("PERFMON_HIT_AGENTS_TIME"),
		"sort" => "AGENTS_TIME",
		"align" => "right",
	),
	array(
		"id" => "WORK_AREA_TIME",
		"content" => GetMessage("PERFMON_HIT_WORK_AREA_TIME"),
		"sort" => "WORK_AREA_TIME",
		"align" => "right",
	),
	array(
		"id" => "EPILOG_TIME",
		"content" => GetMessage("PERFMON_HIT_EPILOG_TIME"),
		"sort" => "EPILOG_TIME",
		"align" => "right",
	),
	array(
		"id" => "EVENTS_TIME",
		"content" => GetMessage("PERFMON_HIT_EVENTS_TIME"),
		"sort" => "EVENTS_TIME",
		"align" => "right",
	),
));

$arSelectedFields = $lAdmin->GetVisibleHeaderColumns();
if(!is_array($arSelectedFields) || (count($arSelectedFields) < 1))
	$arSelectedFields = array(
		"ID",
		"DATE_HIT",
		"REQUEST_URI",
		"INCLUDED_FILES",
		"MEMORY_PEAK_USAGE",
		"QUERIES",
	);
$arSelectedFields[] = "ID";
$arSelectedFields[] = "SQL_LOG";
$arSelectedFields[] = "SERVER_NAME";
$arSelectedFields[] = "SERVER_PORT";

$arNumCols = array(
	"INCLUDED_FILES" => 0,
	"MEMORY_PEAK_USAGE" => 0,
	"CACHE_SIZE" => 0,
	"QUERIES_TIME" => 4,
	"PAGE_TIME" => 4,
	"PROLOG_TIME" => 4,
	"AGENTS_TIME" => 4,
	"WORK_AREA_TIME" => 4,
	"EPILOG_TIME" => 4,
	"EVENTS_TIME" => 4,
	"COMPONENTS_TIME" => 4,
);

$cData = new CPerfomanceHit;
$rsData = $cData->GetList(array($by => $order), $arFilter, false, false, $arSelectedFields);

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PERFMON_HIT_PAGE")));

$max_display_url = COption::GetOptionInt("perfmon", "max_display_url");
while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_NAME, $arRes);
	$row->AddViewField("IS_ADMIN", $f_IS_ADMIN=="Y"? GetMessage("MAIN_YES"): GetMessage("MAIN_NO"));
	$row->AddViewField("DATE_HIT", str_replace(" ", "&nbsp;", $f_FULL_DATE_HIT));

	foreach($arNumCols as $column_name => $precision)
	{
		if($_REQUEST["mode"] == "excel")
			$row->AddViewField($column_name, number_format($arRes[$column_name], $precision, ".", ""));
		else
			$row->AddViewField($column_name, str_replace(" ", "&nbsp;", number_format($arRes[$column_name], $precision, ".", " ")));
	}

	if($arRes["SQL_LOG"] === "Y")
	{
		$url = str_replace(
			array("show_sql_stat_immediate=Y", "show_sql_stat=Y", "show_page_exec_time=Y", "&&"),
			array("", "", "", "&"),
			$arRes["REQUEST_URI"]
		);
		if(strpos($url, "?")===false)
			$url .= "?";
		if(strpos($url, "=")!==false)
			$url .= "&";
		$url .= "show_sql_stat=Y&show_page_exec_time=Y&show_sql_stat_immediate=Y";

		switch($arRes["SERVER_PORT"])
		{
			case "443":
				$url = "https://".$arRes["SERVER_NAME"].$url;
				break;
			case "80":
				$url = "http://".$arRes["SERVER_NAME"].$url;
				break;
			default:
				$url = "http://".$arRes["SERVER_NAME"].":".$arRes["SERVER_PORT"].$url;
		}

		$row->AddViewField("REQUEST_URI", '<a href="'.htmlspecialcharsbx($url).'" title="'.htmlspecialcharsbx($url).'">&gt;&gt;</a>&nbsp;<a href="perfmon_sql_list.php?lang='.LANGUAGE_ID.'&amp;set_filter=Y&amp;find_hit_id='.$f_ID.'" title="'.$f_REQUEST_URI.'">'.(strlen($f_REQUEST_URI)>$max_display_url? substr($f_REQUEST_URI, 0, $max_display_url)."...": $f_REQUEST_URI).'</a> ');
		if($f_QUERIES > 0)
			$row->AddViewField("QUERIES", '<a href="perfmon_sql_list.php?lang='.LANGUAGE_ID.'&amp;set_filter=Y&amp;find_hit_id='.$f_ID.'">'.$f_QUERIES.'</a>');
		if($f_COMPONENTS > 0)
			$row->AddViewField("COMPONENTS", '<a href="perfmon_comp_list.php?lang='.LANGUAGE_ID.'&amp;set_filter=Y&amp;find_hit_id='.$f_ID.'">'.$f_COMPONENTS.'</a>');
	}
	else
	{
		$row->AddViewField("REQUEST_URI", '<a href="'.$f_REQUEST_URI.'" title="'.$f_REQUEST_URI.'">'.(strlen($f_REQUEST_URI)>$max_display_url? substr($f_REQUEST_URI, 0, $max_display_url)."...": $f_REQUEST_URI).'</a> ');
	}
}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
	)
);

$aContext = array(
);
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("PERFMON_HIT_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		"find_script_name" => GetMessage("PERFMON_HIT_SCRIPT_NAME"),
		"find_id" => GetMessage("PERFMON_HIT_ID"),
		"find_is_admin" => GetMessage("PERFMON_HIT_IS_ADMIN"),
		"find_request_method" => GetMessage("PERFMON_HIT_REQUEST_METHOD"),
	)
);
?>

<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>
<tr>
	<td><b><?=GetMessage("PERFMON_HIT_FIND")?>:</b></td>
	<td>
		<input type="text" size="25" name="find" value="<?echo htmlspecialcharsbx($find)?>" title="<?=GetMessage("PERFMON_HIT_FIND")?>">
		<?
		$arr = array(
			"reference" => array(
				GetMessage("PERFMON_HIT_SCRIPT_NAME"),
				GetMessage("PERFMON_HIT_ID"),
			),
			"reference_id" => array(
				"script_name",
				"id",
			)
		);
		echo SelectBoxFromArray("find_type", $arr, $find_type, "", "");
		?>
	</td>
</tr>
<tr>
	<td><?=GetMessage("PERFMON_HIT_SCRIPT_NAME")?></td>
	<td><input type="text" name="find_script_name" size="47" value="<?echo htmlspecialcharsbx($find_script_name)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("PERFMON_HIT_ID")?></td>
	<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
</tr>
<tr>
	<td><?echo GetMessage("PERFMON_HIT_IS_ADMIN")?>:</td>
	<td><?
		$arr = array("reference"=>array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id"=>array("Y","N"));
		echo SelectBoxFromArray("find_is_admin", $arr, htmlspecialcharsbx($find_is_admin), GetMessage("MAIN_ALL"));
	?></td>
</tr>
<tr>
	<td><?echo GetMessage("PERFMON_HIT_REQUEST_METHOD")?>:</td>
	<td><?
		$arr = array("reference"=>array(), "reference_id"=>array());
		$rsMethods = CPerfomanceHit::GetList(array("REQUEST_METHOD"=>"ASC"), array(), true, false, array("REQUEST_METHOD"));
		while($arMethod = $rsMethods->Fetch())
		{
			$arr["reference"][] = $arMethod["REQUEST_METHOD"];
			$arr["reference_id"][] = $arMethod["REQUEST_METHOD"];
		}
		echo SelectBoxFromArray("find_request_method", $arr, htmlspecialcharsbx($find_is_admin), GetMessage("MAIN_ALL"));
	?></td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>

<?$lAdmin->DisplayList();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
