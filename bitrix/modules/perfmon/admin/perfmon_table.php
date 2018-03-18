<?
define("ADMIN_MODULE_NAME", "perfmon");
define("PERFMON_STOP", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/perfmon/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/perfmon/prolog.php");

IncludeModuleLangFile(__FILE__);

$obTable = new CPerfomanceTable;
$obTable->Init($table_name);

$RIGHT = $APPLICATION->GetGroupRight("perfmon");
if($RIGHT=="D" || !$obTable->IsExists())
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if(
	$_SERVER["REQUEST_METHOD"] === "GET"
	&& isset($_GET["ajax_tooltip"]) && $_GET["ajax_tooltip"] === "y"
	&& isset($_GET["find_type"])
	&& isset($_GET["find"])
	&& check_bitrix_sessid()
)
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

	$rsData = $obTable->GetList(array("*"), array("=".$_GET["find_type"] => $_GET["find"]));
	$arData = $rsData->Fetch();
	if($arData)
	{
		?><table class="list"><?
		?><tr><td align="left" colspan="2"><b><?echo htmlspecialcharsex($table_name)?></b></td></tr><?
		foreach($arData as $key => $value)
		{
			?><tr><td align="left"><?echo htmlspecialcharsex($key)?></td><td align="left">&nbsp;<?echo htmlspecialcharsex($value)?></td></tr><?
		}
		?></table><?
	}
	else
	{
		?>no data found<?
	}
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
}

$arFields = $obTable->GetTableFields();
$arUniqueIndexes = $obTable->GetUniqueIndexes();
$sTableID = "tbl_perfmon_table".md5($table_name);
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

if($lAdmin->GroupAction() && $RIGHT>="W")
{
	switch($_REQUEST['action'])
	{
	case "delete":
		//Gather columns from request
		$arRowPK = is_array($_REQUEST["pk"])? $_REQUEST["pk"]: array();
		if(count($arRowPK))
		{
			foreach($arUniqueIndexes as $arIndexColumns)
			{
				$arMissed = array_diff($arIndexColumns, array_keys($arRowPK));
				if(count($arMissed) == 0)
				{
					$strSql = "delete from ".$table_name." WHERE 1=1 ";
					foreach($arRowPK as $column => $value)
						$strSql .= " AND ".$column."='".$DB->ForSQL($value)."'";
					$DB->Query($strSql);
					break;
				}
			}
		}
		break;
	}
}

$FilterArr = array(
	"find",
	"find_type",
);
foreach($arFields as $FIELD_NAME=>$FIELD_TYPE)
{
	if($FIELD_TYPE!="unknown")
		$FilterArr[] = "find_".$FIELD_NAME;
}

$lAdmin->InitFilter($FilterArr);

$arFilter = array();
foreach($arFields as $FIELD_NAME=>$FIELD_TYPE)
{
	if($FIELD_TYPE!="unknown")
	{
		if(
			isset($find_type) && $find_type==$FIELD_NAME
			&& isset($find) && strlen($find)
		)
			$arFilter["=%".$FIELD_NAME] = $find;
		elseif(
			isset($GLOBALS["find_".$FIELD_NAME]) && strlen($GLOBALS["find_".$FIELD_NAME])
		)
			$arFilter["=%".$FIELD_NAME] = $GLOBALS["find_".$FIELD_NAME];
	}
}

$arHeaders = array();
foreach($arFields as $FIELD_NAME=>$FIELD_TYPE)
{
	$arHeaders[$FIELD_NAME] = array(
		"id" => $FIELD_NAME,
		"content" => $FIELD_NAME,
		"sort" => $FIELD_NAME,
		"default" => true,
	);
	if($FIELD_TYPE=="int" || $FIELD_TYPE=="datetime" || $FIELD_TYPE=="date" || $FIELD_TYPE=="double")
		$arHeaders[$FIELD_NAME]["align"] = "right";
}

$lAdmin->AddHeaders($arHeaders);

$bDelete = false;
$arPKColumns = array();
$arSelectedFields = $lAdmin->GetVisibleHeaderColumns();
if(!is_array($arSelectedFields) || (count($arSelectedFields) < 1))
{
	$arSelectedFields = array(
		"*",
	);
	$bDelete = count($arUniqueIndexes) > 0;
	$arPKColumns = array_shift($arUniqueIndexes);
}
else
{
	foreach($arUniqueIndexes as $arIndexColumns)
	{
		$arMissed = array_diff($arIndexColumns, $arSelectedFields);
		if(count($arMissed) == 0)
		{
			$bDelete = true;
			$arPKColumns = $arIndexColumns;
			break;
		}
	}
}

$bDelete = $bDelete && $RIGHT >= "W";

$obSchema = new CPerfomanceSchema;
$arChildren = $obSchema->GetChildren($table_name);
$arParents = $obSchema->GetParents($table_name);

$rsData = $obTable->GetList($arSelectedFields, $arFilter, array($by => $order), Array("nPageSize"=>CAdminResult::GetNavSize($sTableID)));

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PERFMON_TABLE_PAGE")));

$max_display_url = COption::GetOptionInt("perfmon", "max_display_url");
while($arRes = $rsData->Fetch()):

	$row =& $lAdmin->AddRow($arRes["ID"], $arRes);

	$arRowPK = array();
	foreach($arFields as $FIELD_NAME => $FIELD_TYPE)
	{
		if(strlen($arRes[$FIELD_NAME]) > 0)
		{
			if($FIELD_TYPE=="int")
			{
				if($_REQUEST["mode"] == "excel")
					$val = number_format($arRes[$FIELD_NAME], 0, "", "");
				else
					$val = str_replace(" ", "&nbsp;", number_format($arRes[$FIELD_NAME], 0, "", " "));
			}
			elseif($FIELD_TYPE=="double")
			{
				if($_REQUEST["mode"] == "excel")
					$val = number_format($arRes[$FIELD_NAME], 2, ".", "");
				else
					$val = str_replace(" ", "&nbsp;", number_format($arRes[$FIELD_NAME], 2, ".", " "));
			}
			elseif($FIELD_TYPE=="datetime")
			{
				$val = str_replace(" ", "&nbsp;", $arRes["FULL_".$FIELD_NAME]);
			}
			elseif($FIELD_TYPE=="date")
			{
				$val = str_replace(" ", "&nbsp;", $arRes["SHORT_".$FIELD_NAME]);
			}
			else
			{
				$val = htmlspecialcharsbx($arRes[$FIELD_NAME]);
			}

			if(array_key_exists($FIELD_NAME, $arParents) && $DB->TableExists($arParents[$FIELD_NAME]["PARENT_TABLE"]))
				$val = '<a onmouseover="addTimer(this)" onmouseout="removeTimer(this)" href="perfmon_table.php?set_filter=Y&table_name='.$arParents[$FIELD_NAME]["PARENT_TABLE"].'&find='.urlencode($arRes[$FIELD_NAME]).'&find_type='.$arParents[$FIELD_NAME]["PARENT_COLUMN"].'">'.$val.'</a>';

			$row->AddViewField($FIELD_NAME, $val);
		}

		if($bDelete && in_array($FIELD_NAME, $arPKColumns))
		{
			$arRowPK[] = urlencode("pk[".$FIELD_NAME."]")."=".urlencode($arRes[$FIELD_NAME]);
		}
	}

	$arActions = array();
	if($bDelete && (count($arPKColumns) == count($arRowPK)))
	{
		$arActions[] = array(
			"ICON" => "edit",
			"DEFAULT" => true,
			"TEXT" => GetMessage("MAIN_EDIT"),
			"ACTION" => $lAdmin->ActionRedirect("perfmon_row_edit.php?lang=".LANGUAGE_ID."&table_name=".urlencode($table_name)."&".implode("&", $arRowPK)),
		);
		$arActions[] = array(
			"ICON" => "delete",
			"DEFAULT" => false,
			"TEXT" => GetMessage("MAIN_DELETE"),
			"ACTION" => $lAdmin->ActionDoGroup($arRes["ID"], "delete", "table_name=".urlencode($table_name)."&".implode("&", $arRowPK)),
		);
	}

	if(count($arChildren))
	{
		$arActions[] = array("SEPARATOR"=>true);
		foreach($arChildren as $arChild)
			if(CPerfomanceTable::IsExists($arChild["CHILD_TABLE"]))
				$arActions[] = array(
					"ICON" => "",
					"DEFAULT" => false,
					"TEXT" => $arChild["CHILD_TABLE"].".".$arChild["CHILD_COLUMN"]." = ".$arChild["PARENT_COLUMN"],
					"ACTION" => $lAdmin->ActionRedirect("perfmon_table.php?set_filter=Y&table_name=".$arChild["CHILD_TABLE"]."&find=".urlencode($arRes[$arChild["PARENT_COLUMN"]])."&find_type=".$arChild["CHILD_COLUMN"]),
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

$sLastTables = CUserOptions::GetOption("perfmon", "last_tables", "");
if(strlen($sLastTables) > 0)
	$arLastTables = array_flip(explode(",", $sLastTables));
else
	$arLastTables = array();
unset($arLastTables[strtolower($table_name)]);
$arLastTables[strtolower($table_name)] = true;
if(count($arLastTables) > 10)
	array_shift($arLastTables);
CUserOptions::SetOption("perfmon", "last_tables", implode(",", array_keys($arLastTables)));

unset($arLastTables[$table_name]);
if(count($arLastTables) > 0)
{
	$ar = array(
		"MENU" => array(),
	);
	ksort($arLastTables);
	foreach($arLastTables as $table => $flag)
	{
		if($DB->TableExists($table))
			$ar["MENU"][] = array(
				"TEXT" => $table,
				"ACTION" => $lAdmin->ActionRedirect("perfmon_table.php?table_name=".$table),
			);
		else
			unset($arLastTables[$table]);
	}
	$ar["TEXT"] = GetMessage("PERFMON_TABLE_RECENTLY_BROWSED", array("#COUNT#" => count($arLastTables)));
	$aContext[] = $ar;
}

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("PERFMON_TABLE_ALT_TITLE", array("#TABLE_NAME#" => $table_name)));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$arFilter = array();
foreach($arFields as $FIELD_NAME => $FIELD_TYPE)
	if($FIELD_TYPE!="unknown")
		$arFilter[$FIELD_NAME] = $FIELD_NAME;
$oFilter = new CAdminFilter($sTableID."_filter", $arFilter);

CJSCore::Init(array("ajax", "popup"));
?>
<script>
var toolTipCache = new Array;

function drawTooltip(result, _this)
{
	if(!_this) _this = this;

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
	toolTipCache[_this.href] = result;
}

function sendRequest()
{
	if(this.toolTip)
		this.toolTip.show();
	else if(toolTipCache[this.href])
		drawTooltip(toolTipCache[this.href], this);
	else
		BX.ajax.get(
			this.href + '&sessid=' + BX.message('bitrix_sessid') + '&ajax_tooltip=y',
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
<input type="hidden" value="<?echo htmlspecialcharsbx($table_name)?>" name="table_name">
<?$oFilter->Begin();?>
<tr>
	<td><b><?=GetMessage("PERFMON_TABLE_FIND")?>:</b></td>
	<td>
		<input type="text" size="25" name="find" value="<?echo htmlspecialcharsbx($find)?>" title="<?=GetMessage("PERFMON_TABLE_FIND")?>">
		<?
		$arr = array(
			"reference" => array_keys($arFilter),
			"reference_id" => array_keys($arFilter),
		);
		echo SelectBoxFromArray("find_type", $arr, $find_type, "", "");
		?>
	</td>
</tr>
<?foreach($arFields as $FIELD_NAME => $FIELD_TYPE):?>
	<?if($FIELD_TYPE!="unknown"):?>
		<tr>
			<td><?echo htmlspecialcharsbx($FIELD_NAME)?></td>
			<td><input type="text" name="find_<?echo htmlspecialcharsbx($FIELD_NAME)?>" size="47" value="<?echo htmlspecialcharsbx(${"find_".$FIELD_NAME})?>"></td>
		</tr>
	<?endif?>
<?endforeach?>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>

<?$lAdmin->DisplayList();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
