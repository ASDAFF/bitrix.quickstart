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

if(
	$_SERVER["REQUEST_METHOD"] === "GET"
	&& isset($_GET["ajax_tooltip"]) && $_GET["ajax_tooltip"] === "y"
	&& isset($_GET["sql_id"])
	&& check_bitrix_sessid()
)
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

	$rsData = CPerfomanceSQL::GetBacktraceList($_GET["sql_id"]);
	$arData = $rsData->Fetch();
	if($arData)
	{
		?><table class="list"><?
		?><tr>
			<td align="left"><b><?echo GetMessage("PERFMON_SQL_FILE")?></b></td>
			<td align="left"><b><?echo GetMessage("PERFMON_SQL_LINE_NUMBER");?></b></td>
			<td align="left"><b><?echo GetMessage("PERFMON_SQL_FUNCTION");?></b></td>
		</tr><?
		do {
			?><tr>
				<td align="left">&nbsp;<?echo htmlspecialcharsex($arData["FILE_NAME"])?></td>
				<td align="right">&nbsp;<?echo htmlspecialcharsex($arData["LINE_NO"])?></td>
				<?if($arData["CLASS_NAME"]):?>
					<td align="left">&nbsp;<?echo htmlspecialcharsex($arData["CLASS_NAME"]."::".$arData["FUNCTION_NAME"])?></td>
				<?else:?>
					<td align="left">&nbsp;<?echo htmlspecialcharsex($arData["FUNCTION_NAME"])?></td>
				<?endif;?>
			</tr><?
		} while ($arData = $rsData->Fetch());
		?></table><?
	}
	else
	{
		?>no backtrace found<?
	}
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
}

$sTableID = "tbl_perfmon_sql_list";
$oSort = new CAdminSorting($sTableID, "NN", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$FilterArr = Array(
	"find",
	"find_type",
	"find_hit_id",
	"find_component_id",
	"find_query_time",
	"find_suggest_id",
);

$lAdmin->InitFilter($FilterArr);

$arFilter = Array(
	"=HIT_ID" => ($find!="" && $find_type == "hit_id"? $find: $find_hit_id),
	"=COMPONENT_ID" => ($find!="" && $find_type == "component_id"? $find: $find_component_id),
	">=QUERY_TIME" => floatval($find_query_time),
	"=SUGGEST_ID" => intval($find_suggest_id),
);
foreach($arFilter as $key=>$value)
	if(!$value)
		unset($arFilter[$key]);

$lAdmin->AddHeaders(array(
	array(
		"id" => "ID",
		"content" => GetMessage("PERFMON_SQL_ID"),
		"sort" => "ID",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "HIT_ID",
		"content" => GetMessage("PERFMON_SQL_HIT_ID"),
		"sort" => "HIT_ID",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "NN",
		"content" => GetMessage("PERFMON_SQL_NN"),
		"sort" => "NN",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "QUERY_TIME",
		"content" => GetMessage("PERFMON_SQL_QUERY_TIME"),
		"sort" => "QUERY_TIME",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "MODULE_NAME",
		"content" => GetMessage("PERFMON_SQL_MODULE_NAME"),
		"sort" => "MODULE_NAME",
	),
	array(
		"id" => "COMPONENT_NAME",
		"content" => GetMessage("PERFMON_SQL_COMPONENT_NAME"),
		"sort" => "COMPONENT_NAME",
	),
	array(
		"id" => "SQL_TEXT",
		"content" => GetMessage("PERFMON_SQL_SQL_TEXT"),
		//"sort" => "SQL_TEXT",
		"default" => true,
	),
));

$arSelectedFields = $lAdmin->GetVisibleHeaderColumns();
if(!is_array($arSelectedFields) || (count($arSelectedFields) < 1))
	$arSelectedFields = array(
		"ID",
		"HIT_ID",
		"NN",
		"QUERY_TIME",
		"SQL_TEXT",
	);

$cData = new CPerfomanceSQL;
$rsData = $cData->GetList($arSelectedFields, $arFilter, array($by => $order), false, array("nPageSize"=>CAdminResult::GetNavSize($sTableID)));

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PERFMON_SQL_PAGE")));

while($arRes = $rsData->NavNext(true, "f_")):
	$arRes["SQL_TEXT"] = CPerfomanceSQL::Format($arRes["SQL_TEXT"]);
	$row =& $lAdmin->AddRow($f_NAME, $arRes);

	if($_REQUEST["mode"] == "excel")
		$row->AddViewField("QUERY_TIME", number_format($f_QUERY_TIME, 6, ".", ""));
	else
		$row->AddViewField("QUERY_TIME", str_replace(" ", "&nbsp;", number_format($f_QUERY_TIME, 6, ".", " ")));

	if(class_exists("geshi") && $f_SQL_TEXT)
	{
		$obGeSHi = new GeSHi($arRes["SQL_TEXT"], 'sql');
		$html = $obGeSHi->parse_code();
	}
	else
	{
		$html = str_replace(
			array(" ", "\n"),
			array(" &nbsp;", "<br>"),
			htmlspecialcharsbx($arRes["SQL_TEXT"])
		);
	}

	$html = '<span onmouseover="addTimer(this)" onmouseout="removeTimer(this)" id="'.$f_ID.'_sql_backtrace">'.$html.'</span>';

	$row->AddViewField("SQL_TEXT", $html);
	$row->AddViewField("HIT_ID", '<a href="perfmon_hit_list.php?lang='.LANGUAGE_ID.'&amp;set_filter=Y&amp;find_id='.$f_HIT_ID.'">'.$f_HIT_ID.'</a>');

	$arActions = array();
	if($DBType == "mysql" || $DBType == "oracle")
	{
		$arActions[] = array(
			//"ICON"=>"delete",
			"DEFAULT" => "Y",
			"TEXT" => GetMessage("PERFMON_SQL_EXPLAIN"),
			"ACTION" => 'jsUtils.OpenWindow(\'perfmon_explain.php?lang='.LANG.'&ID='.$f_ID.'\', 600, 500);',
		);
	}
	if(count($arActions))
		$row->AddActions($arActions);
endwhile;

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
	)
);

$aContext = array(
);
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("PERFMON_SQL_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		"find_hit_id" => GetMessage("PERFMON_SQL_HIT_ID"),
		"find_component_id" => GetMessage("PERFMON_SQL_COMPONENT_ID"),
		"find_query_time" => GetMessage("PERFMON_SQL_QUERY_TIME"),
	)
);

CJSCore::Init(array("ajax", "popup"));
?>
<script>
var toolTipCache = new Array;

function drawTooltip(result, _this)
{
	if(!_this) _this = this;

	if(result != 'no backtrace found')
	{
		_this.toolTip = BX.PopupWindowManager.create(
			'table_tooltip_' + (parseInt(Math.random() * 100000)), _this,
			{
				autoHide: true,
				closeIcon: true,
				closeByEsc: true,
				content: result
			}
		);

		_this.toolTip.show();
	}

	toolTipCache[_this.id] = result;
}

function sendRequest()
{
	if(this.toolTip)
		this.toolTip.show();
	else if(toolTipCache[this.id])
		drawTooltip(toolTipCache[this.id], this);
	else
		BX.ajax.get(
			'perfmon_sql_list.php?ajax_tooltip=y' + '&sessid=' + BX.message('bitrix_sessid') + '&sql_id=' + this.id,
			BX.proxy(drawTooltip, this)
		);
}

function addTimer(p_href)
{
	p_href.timerID = setTimeout(BX.proxy(sendRequest, p_href), 1000);
}

function removeTimer(p_href)
{
	if(p_href.timerID)
	{
		clearTimeout(p_href.timerID);
		p_href.timerID = null;
	}
}
</script>

<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>
<tr>
	<td><b><?=GetMessage("PERFMON_SQL_FIND")?>:</b></td>
	<td>
		<input type="text" size="25" name="find" value="<?echo htmlspecialcharsbx($find)?>" title="<?=GetMessage("PERFMON_SQL_FIND")?>">
		<?
		$arr = array(
			"reference" => array(
				GetMessage("PERFMON_SQL_HIT_ID"),
				GetMessage("PERFMON_SQL_COMPONENT_ID"),
			),
			"reference_id" => array(
				"hit_id",
				"component_id",
			)
		);
		echo SelectBoxFromArray("find_type", $arr, $find_type, "", "");
		?>
	</td>
</tr>
<tr>
	<td><?=GetMessage("PERFMON_SQL_HIT_ID")?></td>
	<td><input type="text" name="find_hit_id" size="47" value="<?echo htmlspecialcharsbx($find_hit_id)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("PERFMON_SQL_COMPONENT_ID")?></td>
	<td><input type="text" name="find_component_id" size="47" value="<?echo htmlspecialcharsbx($find_component_id)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("PERFMON_SQL_QUERY_TIME")?></td>
	<td><input type="text" name="find_query_time" size="7" value="<?echo htmlspecialcharsbx($find_query_time)?>"></td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>

<?$lAdmin->DisplayList();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>