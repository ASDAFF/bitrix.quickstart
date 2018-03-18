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

$sTableID = "tbl_perfmon_comp_list";
$oSort = new CAdminSorting($sTableID, "NN", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$FilterArr = Array(
	"find",
	"find_type",
	"find_component_name",
	"find_hit_id",
	"find_cache_type",
	"find_hit_script_name",
);

$lAdmin->InitFilter($FilterArr);

$arFilter = Array(
	"COMPONENT_NAME" => ($find!="" && $find_type == "component_name"? $find: $find_component_name),
	"=HIT_ID" => ($find!="" && $find_type == "hit_id"? $find: $find_hit_id),
	"CACHE_TYPE" => $find_cache_type,
	"HIT_SCRIPT_NAME" => $find_hit_script_name,
);
foreach($arFilter as $key=>$value)
	if(!$value)
		unset($arFilter[$key]);

$lAdmin->AddHeaders(array(
	array(
		"id" => "ID",
		"content" => GetMessage("PERFMON_COMP_ID"),
		"sort" => "ID",
		"align" => "right",
	),
	array(
		"id" => "HIT_ID",
		"content" => GetMessage("PERFMON_COMP_HIT_ID"),
		"sort" => "HIT_ID",
		"align" => "right",
	),
	array(
		"id" => "NN",
		"content" => GetMessage("PERFMON_COMP_NN"),
		"sort" => "NN",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "COMPONENT_NAME",
		"content" => GetMessage("PERFMON_COMP_COMPONENT_NAME"),
		"sort" => "COMPONENT_NAME",
		"default" => true,
	),
	array(
		"id" => "COMPONENT_TIME",
		"content" => GetMessage("PERFMON_COMP_COMPONENT_TIME"),
		"sort" => "COMPONENT_TIME",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "QUERIES",
		"content" => GetMessage("PERFMON_COMP_QUERIES"),
		"sort" => "QUERIES",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "QUERIES_TIME",
		"content" => GetMessage("PERFMON_COMP_QUERIES_TIME"),
		"sort" => "QUERIES_TIME",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "CACHE_TYPE",
		"content" => GetMessage("PERFMON_COMP_CACHE_TYPE"),
		"sort" => "CACHE_TYPE",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "CACHE_SIZE",
		"content" => GetMessage("PERFMON_COMP_CACHE_SIZE"),
		"sort" => "CACHE_SIZE",
		"align" => "right",
	),
));

$arSelectedFields = $lAdmin->GetVisibleHeaderColumns();
if(!is_array($arSelectedFields) || (count($arSelectedFields) < 1))
	$arSelectedFields = array(
		"ID",
		"HIT_ID",
		"NN",
		"CACHE_TYPE",
		"COMPONENT_NAME",
		"COMPONENT_TIME",
		"QUERIES",
		"QUERIES_TIME",
	);
$arSelectedFields[] = "ID";

$arNumCols = array(
	"CACHE_SIZE" => 0,
	"COMPONENT_TIME" => 4,
	"QUERIES" => 0,
	"QUERIES_TIME" => 4,
);


if(isset($arFilter["CACHE_TYPE"]) && $arFilter["CACHE_TYPE"]=="N")
	$arFilter["CACHE_TYPE"] = array(false, "N");

$cData = new CPerfomanceComponent;
$rsData = $cData->GetList(array($by => $order), $arFilter, false, false, $arSelectedFields);

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PERFMON_COMP_PAGE")));

$max_display_url = COption::GetOptionInt("perfmon", "max_display_url");
while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_NAME, $arRes);

	foreach($arNumCols as $column_name => $precision)
	{
		if($_REQUEST["mode"] == "excel")
			$row->AddViewField($column_name, number_format($arRes[$column_name], $precision, ".", ""));
		else
			$row->AddViewField($column_name, str_replace(" ", "&nbsp;", number_format($arRes[$column_name], $precision, ".", " ")));
	}

	if($f_QUERIES > 0)
		$row->AddViewField("QUERIES", '<a href="perfmon_sql_list.php?lang='.LANGUAGE_ID.'&amp;set_filter=Y&amp;find_component_id='.$f_ID.'">'.$f_QUERIES.'</a>');

	$row->AddViewField("HIT_ID", '<a href="perfmon_hit_list.php?lang='.LANGUAGE_ID.'&amp;set_filter=Y&amp;find_id='.$f_HIT_ID.'">'.$f_HIT_ID.'</a>');

	if($f_CACHE_TYPE == "A")
		$row->AddViewField("CACHE_TYPE", GetMessage("PERFMON_COMP_CACHE_TYPE_AUTO"));
	elseif($f_CACHE_TYPE == "Y")
		$row->AddViewField("CACHE_TYPE", GetMessage("PERFMON_COMP_CACHE_TYPE_YES"));
	else
		$row->AddViewField("CACHE_TYPE", GetMessage("PERFMON_COMP_CACHE_TYPE_NO"));
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

$APPLICATION->SetTitle(GetMessage("PERFMON_COMP_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		"find_component_name" => GetMessage("PERFMON_COMP_COMPONENT_NAME"),
		"find_hit_id" => GetMessage("PERFMON_COMP_HIT_ID"),
		"find_hit_script_name" => GetMessage("PERFMON_COMP_HIT_SCRIPT_NAME"),
		"find_cache_type" => GetMessage("PERFMON_COMP_CACHE_TYPE"),
	)
);
?>

<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>
<tr>
	<td><b><?=GetMessage("PERFMON_COMP_FIND")?>:</b></td>
	<td>
		<input type="text" size="25" name="find" value="<?echo htmlspecialcharsbx($find)?>" title="<?=GetMessage("PERFMON_COMP_FIND")?>">
		<?
		$arr = array(
			"reference" => array(
				GetMessage("PERFMON_COMP_COMPONENT_NAME"),
				GetMessage("PERFMON_COMP_HIT_ID"),
			),
			"reference_id" => array(
				"component_name",
				"hit_id",
			)
		);
		echo SelectBoxFromArray("find_type", $arr, $find_type, "", "");
		?>
	</td>
</tr>
<tr>
	<td><?=GetMessage("PERFMON_COMP_COMPONENT_NAME")?></td>
	<td><input type="text" name="find_component_name" size="47" value="<?echo htmlspecialcharsbx($find_component_name)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("PERFMON_COMP_HIT_ID")?></td>
	<td><input type="text" name="find_hit_id" size="47" value="<?echo htmlspecialcharsbx($find_hit_id)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("PERFMON_COMP_HIT_SCRIPT_NAME")?></td>
	<td><input type="text" name="find_hit_script_name" size="47" value="<?echo htmlspecialcharsbx($find_hit_script_name)?>"></td>
</tr>
<tr>
	<td><?echo GetMessage("PERFMON_COMP_CACHE_TYPE")?>:</td>
	<td><?
		$arr = array(
			"reference"=>array(GetMessage("PERFMON_COMP_CACHE_TYPE_YES"), GetMessage("PERFMON_COMP_CACHE_TYPE_NO"), GetMessage("PERFMON_COMP_CACHE_TYPE_AUTO")),
			"reference_id"=>array("Y", "N", "A"),
		);
		echo SelectBoxFromArray("find_cache_type", $arr, htmlspecialcharsbx($find_cache_type), GetMessage("MAIN_ALL"));
	?></td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>

<?$lAdmin->DisplayList();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
