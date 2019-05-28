<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");
$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
$bReadOnly = $saleModulePermissions < "W";
if ($bReadOnly)
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
CModule::IncludeModule('sale');
IncludeModuleLangFile(__FILE__);

$ID = '';
if (isset($_REQUEST['ID']))
	$ID = $DB->ForSql($_REQUEST['ID'], 1);

$arLangList = array();
$arLangIDs = array();
$rsLangs = CLangAdmin::GetList(
	($b="sort"),
	($o="asc"),
	array("ACTIVE" => "Y")
);
while ($arLang = $rsLangs->Fetch())
{
	$arLangIDs[] = $arLang["LID"];
	$arLangList[$arLang["LID"]] = $arLang["NAME"];
}

$arStatusGroups = array();
$arStatusGroupIDs = array();
$arSaleManagerGroups = array();
$rsSaleManagerGroups = $APPLICATION->GetGroupRightList(array("MODULE_ID" => "sale", "G_ACCESS" => "U"));
while ($arSaleManagerGroup = $rsSaleManagerGroups->Fetch())
{
	$arSaleManagerGroup["GROUP_ID"] = intval($arSaleManagerGroup["GROUP_ID"]);
	if (2 >= $arSaleManagerGroup["GROUP_ID"])
		continue;
	$arSaleManagerGroups[] = $arSaleManagerGroup["GROUP_ID"];
}
if (!empty($arSaleManagerGroups))
{
	$rsGroups = CGroup::GetListEx(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array('ID' => $arSaleManagerGroups),
		false,
		false,
		array('ID', 'NAME')
	);
	while ($arGroup = $rsGroups->Fetch())
	{
		$arGroup['ID'] = intval($arGroup['ID']);
		$arStatusGroups[$arGroup['ID']] = $arGroup['NAME'];
		$arStatusGroupIDs[] = $arGroup['ID'];
	}
}

$arErrors = array();
$bVarsFromForm = false;
$arFields = array();
$arPerms = array();
$arLangName = array();

$arPermFields = array(
	"GROUP_ID" => 0,
	"PERM_VIEW" => 'N',
	"PERM_CANCEL" => 'N',
	"PERM_MARK" => 'N',
	"PERM_DELIVERY" => 'N',
	"PERM_DEDUCTION" => 'N',
	"PERM_PAYMENT" => 'N',
	"PERM_STATUS" => 'N',
	"PERM_STATUS_FROM" => 'N',
	"PERM_UPDATE" => 'N',
	"PERM_DELETE" => 'N'
);
$arPermFieldKeys = array_keys($arPermFields);
$arPermFieldKeysCut = $arPermFieldKeys;
unset($arPermFieldKeysCut[0]);
$arPermFieldKeysCut = array_values($arPermFieldKeysCut);

$arLangFields = array(
	'LID' => '',
	'NAME' => '',
	'DESCRIPTION' => ''
);

if (
	'POST' == $_SERVER['REQUEST_METHOD']
	&& !$bReadOnly
	&& check_bitrix_sessid()
	&& (
		(isset($_POST['save']) && '' != $_POST['save'])
		|| (isset($_POST['apply']) && '' != $_POST['apply'])
	)
)
{
	$NEW_ID = '';
	if (isset($_POST['NEW_ID']))
		$NEW_ID = $DB->ForSql($_POST['NEW_ID'], 1);

	$arLangName = array_fill_keys($arLangIDs, $arLangFields);
	foreach ($arLangIDs as &$strLangID)
	{
		$arLangName[$strLangID]['LID'] = $strLangID;
		if (isset($_REQUEST['NAME_'.$strLangID]))
			$arLangName[$strLangID]['NAME'] = trim($_REQUEST['NAME_'.$strLangID]);
		if (isset($_REQUEST['DESCRIPTION_'.$strLangID]))
			$arLangName[$strLangID]['DESCRIPTION'] = trim($_REQUEST['DESCRIPTION_'.$strLangID]);
		if ('' == $arLangName[$strLangID]['NAME'])
		{
			$arErrors[] = GetMessage("ERROR_NO_NAME")." [".$strLangID."] ".htmlspecialcharsex($arLangList[$strLangID]);
			$bVarsFromForm = true;
		}
	}
	if (isset($strLangID))
		unset($strLangID);

	if (!empty($arStatusGroupIDs))
	{
		$arPerms = array_fill_keys($arStatusGroupIDs, $arPermFields);
		foreach ($arStatusGroupIDs as &$intOneGroupID)
		{
			$boolViewPerm = false;
			$arPerms[$intOneGroupID]['GROUP_ID'] = $intOneGroupID;
			foreach ($arPermFieldKeys as &$strKey)
			{
				if ('GROUP_ID' == $strKey)
					continue;
				if (isset($_REQUEST[$strKey.'_'.$intOneGroupID]) && 'Y' == $_REQUEST[$strKey.'_'.$intOneGroupID])
				{
					$boolViewPerm = true;
					$arPerms[$intOneGroupID][$strKey] = 'Y';
				}
			}
			if (isset($strKey))
				unset($strKey);
			if ($boolViewPerm)
			{
				$arPerms[$intOneGroupID]['PERM_VIEW'] = 'Y';
			}
			$arPerms[$intOneGroupID]['EXT'] = ($boolViewPerm ? 'Y' : 'N');
		}
		if (isset($intOneGroupID))
			unset($intOneGroupID);
	}

	$arFields = array(
		'ID' => ('' != $ID ? $ID : $NEW_ID),
		'SORT' => (isset($_POST['SORT']) ? $_POST['SORT'] : 100),
		'LANG' => array_values($arLangName),
		'PERMS' => array_values($arPerms),
	);

	if (!$bVarsFromForm)
	{
		if ('' != $ID)
		{
			if (!CSaleStatus::Update($ID, $arFields))
			{
				$strError = GetMessage("ERROR_EDIT_STATUS").": ";
				if ($ex = $APPLICATION->GetException())
					$strError .= $ex->GetString();
				$arErrors[] = $strError;
				$bVarsFromForm = true;
			}
		}
		else
		{
			$ID = CSaleStatus::Add($arFields);
			if ('' == $ID)
			{
				$strError = GetMessage("ERROR_ADD_STATUS").": ";
				if ($ex = $APPLICATION->GetException())
					$strError .= $ex->GetString();
				$arErrors[] = $strError;
				$bVarsFromForm = true;
			}
			else
			{
				CSaleStatus::CreateMailTemplate($ID);
			}
		}
	}

	if (!$bVarsFromForm)
	{
		if (isset($_POST['save']) && '' != $_POST['save'])
		{
			LocalRedirect("sale_status.php?lang=".LANGUAGE_ID.GetFilterParams("filter_", false));
		}
	}
}

$arDefaultValues = array(
	'ID' => '',
	'SORT' => '',
);

$arSelect = array_keys($arDefaultValues);
$arStatus = $arDefaultValues;
$arStatusLangs = array();
$arStatusPerms = array();

if ('' != $ID)
{
	$rsStatus = CSaleStatus::GetList(
		array(),
		array('ID' => $ID),
		false,
		false,
		$arSelect
	);
	if (!($arStatus = $rsStatus->Fetch()))
	{
		$ID = '';
		$arStatus = $arDefaultValues;
		$arStatusLangs = array_fill_keys($arLangIDs, $arLangFields);
		if (!empty($arStatusGroupIDs))
		{
			$arStatusPerms = array_fill_keys($arStatusGroupIDs, $arPermFields);
			foreach ($arStatusGroupIDs as &$intOneGroupID)
			{
				$arStatusPerms[$intOneGroupID]['GROUP_ID'] = $intOneGroupID;
			}
			if (isset($intOneGroupID))
				unset($intOneGroupID);
		}
	}
	else
	{
		$arStatusLangs = array_fill_keys($arLangIDs, $arLangFields);
		$rsStatusLangs = CSaleStatus::GetList(
			array(),
			array('ID' => $ID),
			false,
			false,
			array('ID', 'LID', 'NAME', 'DESCRIPTION')
		);
		while ($arOneLang = $rsStatusLangs->Fetch())
		{
			if (!isset($arStatusLangs[$arOneLang['LID']]))
				continue;
			$arStatusLangs[$arOneLang['LID']] = $arOneLang;
		}

		if (!empty($arStatusGroupIDs))
		{
			$arStatusPerms = array_fill_keys($arStatusGroupIDs, $arPermFields);
			foreach ($arStatusGroupIDs as &$intOneGroupID)
			{
				$arStatusPerms[$intOneGroupID]['GROUP_ID'] = $intOneGroupID;
			}
			if (isset($intOneGroupID))
				unset($intOneGroupID);
			$rsPerms = CSaleStatus::GetList(
				array(),
				array('ID' => $ID, 'GROUP_ID' => $arStatusGroupIDs),
				false,
				false,
				array('*')
			);
			while ($arOnePerm = $rsPerms->Fetch())
			{
				$arOnePerm['GROUP_ID'] = intval($arOnePerm['GROUP_ID']);
				$strSelected = 'N';
				foreach ($arOnePerm as $key => $value)
				{
					if ('GROUP_ID' == $key || 'PERM_VIEW' == $key)
						continue;
					if ('Y' == $value)
					{
						$strSelected = 'Y';
						break;
					}
				}
				$arOnePerm['EXT'] = $strSelected;
				$arStatusPerms[$arOnePerm['GROUP_ID']] = $arOnePerm;
			}
		}
	}
}
else
{
	$arStatusLangs = array_fill_keys($arLangIDs, $arLangFields);
	if (!empty($arStatusGroupIDs))
	{
		$arStatusPerms = array_fill_keys($arStatusGroupIDs, $arPermFields);
		foreach ($arStatusGroupIDs as &$intOneGroupID)
		{
			$arStatusPerms[$intOneGroupID]['GROUP_ID'] = $intOneGroupID;
		}
		if (isset($intOneGroupID))
			unset($intOneGroupID);
	}
}

if ($bVarsFromForm)
{
	$arStatus = $arFields;
	$arStatusLangs = array();
	$arStatusPerms = $arFields['PERMS'];

	foreach($arFields['LANG'] as $val)
		$arStatusLangs[$val["LID"]] = $val;
}

if('' != $ID)
	$APPLICATION->SetTitle(GetMessage("SALE_EDIT_RECORD", array("#ID#"=>$ID)));
else
	$APPLICATION->SetTitle(GetMessage("SALE_NEW_RECORD"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$APPLICATION->AddHeadScript('/bitrix/js/sale/status_perms.js');

$aMenu = array(
	array(
		"TEXT" => GetMessage("SSEN_2FLIST"),
		"ICON" => "btn_list",
		"LINK" => "/bitrix/admin/sale_status.php?lang=".LANGUAGE_ID.GetFilterParams("filter_")
	)
);

if ('' != $ID && !$bReadOnly)
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
		"TEXT" => GetMessage("SSEN_NEW_STATUS"),
		"ICON" => "btn_new",
		"LINK" => "/bitrix/admin/sale_status_edit.php?lang=".LANGUAGE_ID.GetFilterParams("filter_")
	);

	$aMenu[] = array(
		"TEXT" => GetMessage("SSEN_DELETE_STATUS"),
		"ICON" => "btn_delete",
		"LINK" => "javascript:if(confirm('".GetMessageJS("SSEN_DELETE_STATUS_CONFIRM")."')) window.location='/bitrix/admin/sale_status.php?action=delete&ID[]=".urlencode($ID)."&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."#tb';",
	);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

if (!empty($arErrors))
	CAdminMessage::ShowMessage(implode('<br>', $arErrors));
?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="fform">
<?echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANGUAGE_ID; ?>">
<input type="hidden" name="ID" value="<? echo $ID; ?>">
<? echo bitrix_sessid_post();

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("SSEN_TAB_STATUS"), "ICON" => "sale", "TITLE" => GetMessage("SSEN_TAB_STATUS_DESCR"))
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();

$tabControl->BeginNextTab();
?>
	<tr class="adm-detail-required-field">
		<td width="40%"><? echo GetMessage("SALE_CODE"); ?><? echo ('' != $ID ? ' (1 '.GetMessage("SALE_CODE_LEN").')' : ''); ?>:</td>
		<td width="60%">
			<?if ('' != $ID)
			{
				?><b><?echo $ID ?></b><?
			}
			else
			{
				?><input type="text" name="NEW_ID" value="<? echo htmlspecialcharsbx($arStatus['ID']); ?>" size="3" maxlength="1"><?
			}?>
		</td>
	</tr>
	<tr>
		<td width="40%"><? echo GetMessage("SALE_SORT"); ?>:</td>
		<td width="60%"><input type="text" name="SORT" value="<? echo intval($arStatus['SORT']); ?>" size="10"></td>
	</tr>
	<?
	foreach ($arLangIDs as &$strLangID)
	{
		?><tr class="heading">
			<td colspan="2">[<? echo htmlspecialcharsex($strLangID); ?>] <? echo htmlspecialcharsex($arLangList[$strLangID]); ?></td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><? echo GetMessage("SALE_NAME"); ?>:</td>
			<td width="60%"><input type="text" name="NAME_<? echo htmlspecialcharsbx($strLangID); ?>" value="<? echo htmlspecialcharsbx($arStatusLangs[$strLangID]['NAME']); ?>" size="30"></td>
		</tr>
		<tr>
			<td width="40%" valign="top"><? echo GetMessage("SALE_DESCR"); ?>:</td>
			<td width="60%">
				<textarea name="DESCRIPTION_<? echo htmlspecialcharsbx($strLangID); ?>" cols="35" rows="3"><? echo htmlspecialcharsbx($arStatusLangs[$strLangID]['DESCRIPTION']); ?></textarea>
			</td>
		</tr><?
	}
	if (isset($strLangID))
		unset($strLangID);
	?><tr class="heading">
		<td colspan="2"><? echo GetMessage("SSEN_ACCESS_PERMS"); ?></td>
	</tr>
	<?
	if (empty($arStatusGroupIDs))
	{
		?><tr>
		<td colspan="2" style="text-align: center;"><?
			echo GetMessage('SSEN_PERM_GROUPS_ABSENT');
			if (!$bReadOnly)
			{
				?><br /><?
				echo GetMessage(
					'SSEN_PERM_GROUPS_SET',
					array(
						'#LINK#' => 'settings.php?lang='.LANGUAGE_ID.'&mid=sale&back_url_settings='.urlencode($APPLICATION->GetCurPageParam()).'&tabControl_active_tab=edit4'
					)
				);
			}
		?></td>
		</tr><?
	}
	else
	{
		?>
		<tr><td colspan="2">
		<table class="internal">
		<tr class="heading">
			<td><? echo GetMessage("SSEN_USER_GROUP"); ?></td>
			<?
			foreach ($arPermFieldKeys as &$strKey)
			{
				if ('GROUP_ID' == $strKey)
					continue;
				?><td><? echo GetMessage('SSEN_'.$strKey); ?></td><?
			}
			if (isset($strKey))
				unset($strKey);
			?>
			<td><? echo GetMessage('SSEN_ALL_PERM'); ?></td>
		</tr><?
		$intAllCount = count($arPermFieldKeysCut);
		foreach ($arStatusPerms as &$arOneGroup)
		{
			$intCurrentCount = 0;
			$intGroupID = $arOneGroup['GROUP_ID'];
			?><tr><td><? echo htmlspecialcharsex($arStatusGroups[$intGroupID]); ?></td><?
			foreach ($arPermFieldKeysCut as &$strKey)
			{
				$strCode = htmlspecialcharsbx($strKey.'_'.$intGroupID);
				if ('Y' == $arOneGroup[$strKey])
					$intCurrentCount++;
				$strDisabled = '';
				if ('PERM_VIEW' == $strKey && 'Y' == $arOneGroup['EXT'])
					$strDisabled = ' disabled';
				?><td style="text-align: center;">
					<input type="hidden" name="<? echo $strCode; ?>" id="<? echo $strCode; ?>_N" value="N" />
					<input type="checkbox" name="<? echo $strCode; ?>" id="<? echo $strCode; ?>" value="Y" <? echo ('Y' == $arOneGroup[$strKey] ? 'checked ' : '').$strDisabled; ?>/>
				</td><?
			}
			if (isset($strKey))
				unset($strKey);
			?><td style="text-align: center;">
				<input type="checkbox" id="PERM_ALL_<? echo $intGroupID; ?>" value="Y" <? echo ($intCurrentCount == $intAllCount ? 'checked' : ''); ?>/>
			</td></tr><?
		}
		if (isset($arOneGroup))
			unset($arOneGroup);
		?></table>
		</td></tr><?
	}
$tabControl->EndTab();

$tabControl->Buttons(
	array(
		"disabled" => ($bReadOnly),
		"back_url" => "/bitrix/admin/sale_status.php?lang=".LANGUAGE_ID.GetFilterParams("filter_")
	)
);

$tabControl->End();
?>
</form><?
if (!$bReadOnly && !empty($arStatusGroupIDs))
{
	$arStatusPerm = array(
		'GROUPS' => $arStatusGroupIDs,
		'PERM_LIST' => $arPermFieldKeysCut,
		'PERM_VIEW' => 'PERM_VIEW',
		'PERM_ALL' => 'PERM_ALL',
	);
	?><script type="text/javascript">
	var obStatusPerms = new JCSaleStatusPerms(<? echo CUtil::PhpToJSObject($arStatusPerm); ?>);
	</script><?
}
?><?require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");?>