<?
define("ADMIN_MODULE_NAME", "perfmon");

/*.require_module 'standard';.*/
/*.require_module 'hash';.*/
/*.require_module 'bitrix_main_include_prolog_admin_before';.*/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

if(!CModule::IncludeModule('perfmon'))
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	echo CAdminMessage::ShowMessage(GetMessage("PERFMON_ROW_EDIT_MODULE_ERROR"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$RIGHT = $APPLICATION->GetGroupRight("perfmon");
if($RIGHT <= "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$obTable = new CPerfomanceTable;
$obTable->Init($table_name);
if(!$obTable->IsExists())
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	echo CAdminMessage::ShowMessage(GetMessage("PERFMON_ROW_EDIT_TABLE_ERROR", array(
			"#TABLE_NAME#" => htmlspecialcharsbx($table_name),
	)));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$isAdmin = $USER->CanDoOperation('edit_php');
$arUniqueIndexes = $obTable->GetUniqueIndexes();
$arFilter = array();
$strWhere = "";

$arRowPK = is_array($_REQUEST["pk"])? $_REQUEST["pk"]: array();
if(count($arRowPK))
{
	foreach($arUniqueIndexes as $arIndexColumns)
	{
		$arMissed = array_diff($arIndexColumns, array_keys($arRowPK));
		if(count($arMissed) == 0)
		{
			$strWhere = "WHERE 1 = 1";
			foreach($arRowPK as $column => $value)
			{
				$arFilter["=".$column] = $value;
				if($value != "")
					$strWhere .= " AND ".$column." = '".$DB->ForSQL($value)."'";
				else
					$strWhere .= " AND (".$column." = '' OR ".$column." IS NULL)";
			}
			break;
		}
	}
}

if(empty($arFilter))
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	echo CAdminMessage::ShowMessage(GetMessage("PERFMON_ROW_EDIT_PK_ERROR"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$arFields = $obTable->GetTableFields(false, true);

$rsRecord = $obTable->GetList(array_keys($arFields), $arFilter, array());
$arRecord = $rsRecord->Fetch();
if(!$arRecord)
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	echo CAdminMessage::ShowMessage(GetMessage("PERFMON_ROW_EDIT_NOT_FOUND"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$obSchema = new CPerfomanceSchema;
$arChildren = $obSchema->GetChildren($table_name);
$arParents = $obSchema->GetParents($table_name);

$aTabs = array(
	array(
		"DIV" => "edit",
		"TAB" => GetMessage("PERFMON_ROW_EDIT_TAB"),
		"ICON"=>"main_user_edit",
		"TITLE"=>GetMessage("PERFMON_ROW_EDIT_TAB_TITLE", array("#TABLE_NAME#" => $table_name)),
	),
	array(
		"DIV" => "cache",
		"TAB" => GetMessage("PERFMON_ROW_CACHE_TAB"),
		"ICON"=>"main_user_edit",
		"TITLE"=>GetMessage("PERFMON_ROW_CACHE_TAB_TITLE"),
	),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$strError = '';

if($_SERVER["REQUEST_METHOD"] === "POST" && check_bitrix_sessid() && $isAdmin)
{
	$arToUpdate = array();
	$arFields = $obTable->GetTableFields(false, true);
	foreach($arFields as $Field => $arField)
	{
		if(!in_array($Field, $arIndexColumns))
			$arToUpdate[$Field] = $_POST[$Field];
	}

	$strUpdate = $DB->PrepareUpdate($table_name, $arToUpdate);
	if(strlen($strUpdate))
	{
		$res = $DB->Query("
			update ".$table_name."
			set ".$strUpdate."
			".$strWhere."
		");
	}
	else
	{
		$res = true;
	}

	if($res)
	{
		if($_POST["clear_managed_cache"] === "Y")
		{
			$CACHE_MANAGER->CleanAll();
			$stackCacheManager->CleanAll();
		}

		if($apply != "")
		{
			LocalRedirect($APPLICATION->GetCurPageParam()."&".$tabControl->ActiveTabParam());
		}
		else
		{
			LocalRedirect("perfmon_table.php?lang=".LANGUAGE_ID."&table_name=".urlencode($table_name));
		}
	}
	else
	{
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
		echo CAdminMessage::ShowMessage(GetMessage("PERFMON_ROW_EDIT_SAVE_ERROR"));
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
		die();
	}
}

$APPLICATION->SetTitle(GetMessage("PERFMON_ROW_EDIT_TITLE", array("#TABLE_NAME#" => $table_name)));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
	array(
		"TEXT" => $table_name,
		"TITLE" => GetMessage("PERFMON_ROW_EDIT_MENU_LIST_TITLE"),
		"LINK" => "perfmon_table.php?lang=".LANGUAGE_ID."&table_name=".urlencode($table_name),
		"ICON" => "btn_list",
	)
);
$context = new CAdminContextMenu($aMenu);
$context->Show();

if($strError)
	CAdminMessage::ShowMessage(array(
		"MESSAGE" => GetMessage("admin_lib_error"),
		"DETAILS" => $strError,
		"TYPE" => "ERROR",
	));

?>
<script>
function AdjustHeight()
{
	var TEXTS = BX.findChildren(BX('editform'), {tag: /^(textarea)$/i}, true);
	if(TEXTS)
	{
		for(var i = 0; i < TEXTS.length; i++)
		{
			var TEXT = TEXTS[i];
			if (TEXT.scrollHeight > TEXT.clientHeight)
			{
				var dy = TEXT.offsetHeight - TEXT.clientHeight
				var newHeight = TEXT.scrollHeight + dy;
				TEXT.style.height = newHeight + 'px';
			}
		}
	}
}
BX.ready(function(){AdjustHeight();});
</script>
<form method="POST" action="<?echo $APPLICATION->GetCurPageParam()?>"  enctype="multipart/form-data" name="editform" id="editform">
<?
$tabControl->Begin();

$tabControl->BeginNextTab();
	foreach($arFields as $Field => $arField)
	{
		if(
			(
				$arField["type"] === "string"
				|| $arField["type"] === "int"
			)
			&& array_key_exists($Field, $arParents)
			&& $DB->TableExists($arParents[$Field]["PARENT_TABLE"])
		)
		{
			$rs = $DB->Query(
				$DB->TopSql("
					select distinct ".$arParents[$Field]["PARENT_COLUMN"]."
					from ".$arParents[$Field]["PARENT_TABLE"]."
					order by 1
				", 21)
			);
			$arSelect = array(
				"REFERENCE" => array(),
				"REFERENCE_ID" => array(),
			);
			while($ar = $rs->Fetch())
			{
				$arSelect["REFERENCE"][] = $ar[$arParents[$Field]["PARENT_COLUMN"]];
				$arSelect["REFERENCE_ID"][] = $ar[$arParents[$Field]["PARENT_COLUMN"]];
			}
			if(count($arSelect["REFERENCE"]) < 21)
			{
				$arFields[$Field]["SELECT"] = $arSelect;
			}
			///TODO lookup window
		}

		///TODO visual editor for FIELD FIELD_TYPE couple
	}

	foreach($arFields as $Field => $arField)
	{
		if(in_array($Field, $arIndexColumns))
		{
		?>
			<tr>
				<td width="40%"><?echo htmlspecialcharsbx($Field)?>:</td>
				<td width="60%"><?echo htmlspecialcharsex($arRecord[$Field]);?></td>
			</tr>
		<?
		}
		elseif($arField["type"] === "datetime")
		{
		?>
			<tr>
				<td width="40%"><?echo htmlspecialcharsbx($Field)?>:</td>
				<td width="60%"><?echo CAdminCalendar::CalendarDate($Field, $arRecord["FULL_".$Field], 20, true)?>
		<?
		}
		elseif($arField["type"] === "date")
		{
		?>
			<tr>
				<td width="40%"><?echo htmlspecialcharsbx($Field)?>:</td>
				<td width="60%"><?echo CAdminCalendar::CalendarDate($Field, $arRecord["SHORT_".$Field], 10, false)?>
		<?
		}
		elseif(
			isset($arField["SELECT"])
		)
		{
		?>
			<tr>
				<td width="40%"><?echo htmlspecialcharsbx($Field)?>:</td>
				<td width="60%"><?
					echo SelectBoxFromArray($Field, $arField["SELECT"], $arRecord[$Field], $arField["nullable"]? "(null)": "");
				?></td>
			</tr>
		<?
		}
		elseif(
			$arField["type"] === "string"
			&& $arField["length"]== 1
			&& ($arField["default"] === "Y" || $arField["default"] === "N")
			&& ($arRecord[$Field] === "Y" || $arRecord[$Field] === "N")
			&& !$arField["nullable"]
		)
		{
		?>
			<tr>
				<td width="40%"><label
					for="<?echo htmlspecialcharsbx($Field)?>"
				><?echo htmlspecialcharsbx($Field)?></label>:</td>
				<td width="60%"><input
					type="hidden"
					name="<?echo htmlspecialcharsbx($Field)?>"
					value="N"
				><input
					type="checkbox"
					name="<?echo htmlspecialcharsbx($Field)?>"
					id="<?echo htmlspecialcharsbx($Field)?>"
					value="Y"
					<?if($arRecord[$Field] === "Y") echo 'checked="checked"'?>
				></td>
			</tr>
		<?
		}
		elseif(
			$arField["type"] === "string"
			&& $arField["length"] > 0
			&& $arField["length"] <= 100
		)
		{
		?>
			<tr>
				<td width="40%"><?echo htmlspecialcharsbx($Field)?>:</td>
				<td width="60%"><input
					type="text"
					maxsize="<?echo $arField["length"]?>"
					size="<?echo min($arField["length"], 35)?>"
					name="<?echo htmlspecialcharsbx($Field)?>"
					value="<?echo htmlspecialcharsbx($arRecord[$Field])?>"
				></td>
			</tr>
		<?
		}
		elseif(
			$arField["type"] === "string"
		)
		{
		?>
			<tr>
				<td width="40%" class="adm-detail-valign-top" style="padding-top:14px"><?echo htmlspecialcharsbx($Field)?>:</td>
				<td width="60%"><textarea
					style="width:100%"
					rows="1"
					name="<?echo htmlspecialcharsbx($Field)?>"
				><?echo htmlspecialcharsex($arRecord[$Field])?></textarea>
				</td>
			</tr>
		<?
		}
		elseif(
			$arField["type"] === "int"
			|| $arField["type"] === "double"
		)
		{
		?>
			<tr>
				<td width="40%"><?echo htmlspecialcharsbx($Field)?>:</td>
				<td width="60%"><input
					type="text"
					maxsize="20"
					size="15"
					name="<?echo htmlspecialcharsbx($Field)?>"
					value="<?echo htmlspecialcharsbx($arRecord[$Field])?>"
				></td>
			</tr>
		<?
		}
		///TODO Oracle CLOB edit
		/*else
		{
		?>
			<tr>
				<td width="40%"><?echo htmlspecialcharsbx($Field)?>:</td>
				<td width="60%"><?echo htmlspecialcharsbx(print_r($arField,1))?></td>
			</tr>
		<?
		}*/
	}
$tabControl->BeginNextTab();
		?>
			<tr>
				<td width="40%"><label
					for="clear_managed_cache"
				><?echo GetMessage("PERFMON_ROW_CACHE_CLEAR")?></label>:</td>
				<td width="60%"><input
					type="checkbox"
					name="clear_managed_cache"
					id="clear_managed_cache"
					value="Y"
				></td>
			</tr>
		<?
?>
<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?echo LANGUAGE_ID?>">
<?
$tabControl->Buttons(
	array(
		"disabled" => !$isAdmin,
		"back_url" => "perfmon_table.php?lang=".LANGUAGE_ID."&table_name=".urlencode($table_name),
	)
);
$tabControl->End();
?>
</form>

<?
$tabControl->ShowWarnings("editform", $message);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>