<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions < "W")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

$ID = $DB->ForSql($ID, 1);

ClearVars();

$langCount = 0;
$arSysLangs = Array();
$arSysLangNames = Array();
$db_lang = CLangAdmin::GetList(($b="sort"), ($o="asc"), array("ACTIVE" => "Y"));
while ($arLang = $db_lang->Fetch())
{
	$arSysLangs[$langCount] = $arLang["LID"];
	$arSysLangNames[$langCount] = htmlspecialcharsbx($arLang["NAME"]);
	$langCount++;
}


$strError = "";
$bInitVars = false;
if ((strlen($save)>0 || strlen($apply)>0) && $REQUEST_METHOD=="POST" && $saleModulePermissions=="W" && check_bitrix_sessid())
{
	$SORT = IntVal($SORT);
	if ($SORT <= 0)
		$SORT = 100;

	$NEW_ID = $DB->ForSql($NEW_ID, 1);

	if (strlen($ID) <= 0 && strlen($NEW_ID) <= 0)
		$strError .= GetMessage("ERROR_NO_CODE")."<br>";

	if (strlen($ID) <= 0 && strlen($NEW_ID) > 0)
	{
		if ($arStatus_tmp = CSaleStatus::GetByID($NEW_ID, LANG))
			$strError .= GetMessage("ERROR_CODE_EXISTS1")." \"".$NEW_ID."\" ".GetMessage("ERROR_CODE_EXISTS2")."<br>";
	}

	for ($i = 0; $i < count($arSysLangs); $i++)
	{
		${"NAME_".$arSysLangs[$i]} = Trim(${"NAME_".$arSysLangs[$i]});
		if (strlen(${"NAME_".$arSysLangs[$i]}) <= 0)
			$strError .= GetMessage("ERROR_NO_NAME")." [".$arSysLangs[$i]."] ".$arSysLangNames[$i].".<br>";
	}

	if (strlen($strError)<=0)
	{
		unset($arFields);

		if (strlen($ID) <= 0)
			$arFields["ID"] = $NEW_ID;
		else
			$arFields["ID"] = $ID;

		$arFields["SORT"] = $SORT;

		for ($i = 0; $i < count($arSysLangs); $i++)
		{
			$arFields["LANG"][] = array(
				"LID" => $arSysLangs[$i],
				"NAME" => ${"NAME_".$arSysLangs[$i]},
				"DESCRIPTION" => ${"DESCRIPTION_".$arSysLangs[$i]}
				);
		}

		$arPerms = array();

		$arSaleManagerGroups = array();
		$dbSaleManagerGroups = $APPLICATION->GetGroupRightList(array("MODULE_ID" => "sale", "G_ACCESS" => "U"));
		while ($arSaleManagerGroup = $dbSaleManagerGroups->Fetch())
			$arSaleManagerGroups[] = IntVal($arSaleManagerGroup["GROUP_ID"]);

		$dbGroups = CGroup::GetList(
				($b = "c_sort"),
				($o = "asc"),
				array("ANONYMOUS" => "N")
			);
		while ($arGroup = $dbGroups->Fetch())
		{
			$arGroup["ID"] = IntVal($arGroup["ID"]);
			if ($arGroup["ID"] == 1 || $arGroup["ID"] == 2 || !in_array($arGroup["ID"], $arSaleManagerGroups))
				continue;

			$arPerms[] = array(
					"GROUP_ID" => $arGroup["ID"],
					"PERM_VIEW" => ((${"PM_DELETE_".$arGroup["ID"]} == "Y" || ${"PM_UPDATE_".$arGroup["ID"]} == "Y" || ${"PM_PAYMENT_".$arGroup["ID"]} == "Y" || ${"PM_DELIVERY_".$arGroup["ID"]} == "Y" || ${"PM_CANCEL_".$arGroup["ID"]} == "Y") ? "Y" : ${"PM_VIEW_".$arGroup["ID"]}),
					"PERM_CANCEL" => ${"PM_CANCEL_".$arGroup["ID"]},
					"PERM_DELIVERY" => ${"PM_DELIVERY_".$arGroup["ID"]},
					"PERM_PAYMENT" => ${"PM_PAYMENT_".$arGroup["ID"]},
					"PERM_STATUS" => ${"PM_STATUS_".$arGroup["ID"]},
					"PERM_STATUS_FROM" => ${"PM_STATUS_FROM_".$arGroup["ID"]},
					"PERM_UPDATE" => ${"PM_UPDATE_".$arGroup["ID"]},
					"PERM_DELETE" => ${"PM_DELETE_".$arGroup["ID"]}
				);
		}

		$arFields["PERMS"] = $arPerms;

		if (strlen($ID)>0)
		{
			if (!CSaleStatus::Update($ID, $arFields))
			{
				$strError .= GetMessage("ERROR_EDIT_STATUS")."<br>";
				if ($ex = $APPLICATION->GetException())
					$strError .= $ex->GetString().".<br>";
			}
		}
		else
		{
			$ID = CSaleStatus::Add($arFields);
			if (strlen($ID)<=0)
			{
				$strError .= GetMessage("ERROR_ADD_STATUS")."<br>";
				if ($ex = $APPLICATION->GetException())
					$strError .= $ex->GetString().".<br>";
			}
			else
				CSaleStatus::CreateMailTemplate($ID);
		}
	}

	if (strlen($strError)>0) $bInitVars = True;

	if (strlen($save)>0 && strlen($strError)<=0)
		LocalRedirect("sale_status.php?lang=".LANG.GetFilterParams("filter_", false));
}

if (strlen($ID)>0)
{
	if (!($arStatus = CSaleStatus::GetByID($ID, LANG)))
	{
		$ID = "";
		$str_ID = "";
	}
	else
	{
		foreach ($arStatus as $key => $value)
		{
			${"str_".$key} = htmlspecialcharsbx($value);
		}
	}
}

if ($bInitVars)
{
	$DB->InitTableVarsForEdit("b_sale_status", "", "str_");
	$str_NEW_ID = $DB->ForSql($NEW_ID, 1);
}

if(strlen($ID) > 0)
	$sDocTitle = GetMessage("SALE_EDIT_RECORD", array("#ID#"=>$ID));
else
	$sDocTitle = GetMessage("SALE_NEW_RECORD");
$APPLICATION->SetTitle($sDocTitle);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

/*********************************************************************/
/********************  BODY  *****************************************/
/*********************************************************************/
?>

<?
$aMenu = array(
		array(
				"TEXT" => GetMessage("SSEN_2FLIST"),
				"ICON" => "btn_list",
				"LINK" => "/bitrix/admin/sale_status.php?lang=".LANG.GetFilterParams("filter_")
			)
	);

if (strlen($ID) > 0 && $saleModulePermissions >= "W")
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
			"TEXT" => GetMessage("SSEN_NEW_STATUS"),
			"ICON" => "btn_new",
			"LINK" => "/bitrix/admin/sale_status_edit.php?lang=".LANG.GetFilterParams("filter_")
		);

	$aMenu[] = array(
			"TEXT" => GetMessage("SSEN_DELETE_STATUS"),
			"ICON" => "btn_delete",
			"LINK" => "javascript:if(confirm('".GetMessage("SSEN_DELETE_STATUS_CONFIRM")."')) window.location='/bitrix/admin/sale_status.php?action=delete&ID[]=".UrlEncode($ID)."&lang=".LANG."&".bitrix_sessid_get()."#tb';",
		);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?CAdminMessage::ShowMessage($strError);?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="fform">
<?echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANG ?>">
<input type="hidden" name="ID" value="<?echo $ID ?>">
<?=bitrix_sessid_post()?>

<?
$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("SSEN_TAB_STATUS"), "ICON" => "sale", "TITLE" => GetMessage("SSEN_TAB_STATUS_DESCR"))
	);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>

<?
$tabControl->BeginNextTab();
?>

	<tr class="adm-detail-required-field">
		<td width="40%">
			<?echo GetMessage("SALE_CODE")?><?if (strlen($ID)<=0):?> (1 <?echo GetMessage("SALE_CODE_LEN")?>)<?endif;?>:
		</td>
		<td width="60%">
			<?if (strlen($ID)>0):?>
				<b><?echo $ID ?></b>
			<?else:?>
				<input type="text" name="NEW_ID" value="<?echo $str_NEW_ID ?>" size="3" maxlength="1">
			<?endif;?>
		</td>
	</tr>

	<tr>
		<td>
			<?echo GetMessage("SALE_SORT")?>:
		</td>
		<td>
			<input type="text" name="SORT" value="<?echo $str_SORT ?>" size="10">
		</td>
	</tr>

	<?
	for ($i = 0; $i<count($arSysLangs); $i++):
		$arStatusLang = CSaleStatus::GetLangByID($ID, $arSysLangs[$i]);
		$str_NAME = htmlspecialcharsbx($arStatusLang["NAME"]);
		$str_DESCRIPTION = htmlspecialcharsbx($arStatusLang["DESCRIPTION"]);
		if ($bInitVars)
		{
			$str_NAME = htmlspecialcharsbx(${"NAME_".$arSysLangs[$i]});
			$str_DESCRIPTION = htmlspecialcharsbx(${"DESCRIPTION_".$arSysLangs[$i]});
		}
		?>
		<tr class="heading">
			<td colspan="2">[<?echo $arSysLangs[$i];?>] <?echo $arSysLangNames[$i];?>:</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td>
				<?echo GetMessage("SALE_NAME")?>:
			</td>
			<td>
				<input type="text" name="NAME_<?echo $arSysLangs[$i] ?>" value="<?echo $str_NAME ?>" size="30">
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?echo GetMessage("SALE_DESCR")?>:
			</td>
			<td valign="top">
				<textarea name="DESCRIPTION_<?echo $arSysLangs[$i] ?>" cols="35" rows="3"><?echo $str_DESCRIPTION ?></textarea>
			</td>
		</tr>
	<?endfor;?>

	<tr class="heading">
		<td colspan="2">
			<?= GetMessage("SSEN_ACCESS_PERMS") ?>:
		</td>
	</tr>

	<tr>
		<td colspan="2" align="center">

			<script language="JavaScript">
			<!--
			function PMOnClick(id)
			{
				var chkView = eval("document.fform.PM_VIEW_" + id);
				var chkCancel = eval("document.fform.PM_CANCEL_" + id);
				var chkDelivery = eval("document.fform.PM_DELIVERY_" + id);
				var chkPayment = eval("document.fform.PM_PAYMENT_" + id);
				var chkStatus = eval("document.fform.PM_STATUS_" + id);
				var chkStatusFrom = eval("document.fform.PM_STATUS_FROM_" + id);
				var chkUpdate = eval("document.fform.PM_UPDATE_" + id);
				var chkDelete = eval("document.fform.PM_DELETE_" + id);

				chkView.disabled = chkDelete.checked || chkUpdate.checked || chkPayment.checked || chkDelivery.checked || chkCancel.checked;
				if (chkView.disabled)
					chkView.checked = true;
			}
			//-->
			</script>

			<table class="internal">
			<tr class="heading">
				<td><?= GetMessage("SSEN_USER_GROUP") ?></td>
				<td><?= GetMessage("SSEN_PERM_VIEW") ?></td>
				<td><?= GetMessage("SSEN_PERM_CANCEL") ?></td>
				<td><?= GetMessage("SSEN_PERM_DELIVRY") ?></td>
				<td><?= GetMessage("SSEN_PERM_PAY") ?></td>
				<td><?= GetMessage("SSEN_PERM_STATUS") ?></td>
				<td><?= GetMessage("SSEN_PERM_STATUS_FROM") ?></td>
				<td><?= GetMessage("SSEN_PERM_EDIT") ?></td>
				<td><?= GetMessage("SSEN_PERM_DELETE") ?></td>
			</tr>

			<?
			$arPermsMatrix = array();
			$dbPermsMatrix = CSaleStatus::GetPermissionsList(array(), array("STATUS_ID" => $ID), false, false, array());
			while ($arPM = $dbPermsMatrix->Fetch())
			{
				$arPermsMatrix[$arPM["GROUP_ID"]] = array(
					"PERM_VIEW" => $arPM["PERM_VIEW"],
					"PERM_CANCEL" => $arPM["PERM_CANCEL"],
					"PERM_DELIVERY" => $arPM["PERM_DELIVERY"],
					"PERM_PAYMENT" => $arPM["PERM_PAYMENT"],
					"PERM_STATUS" => $arPM["PERM_STATUS"],
					"PERM_STATUS_FROM" => $arPM["PERM_STATUS_FROM"],
					"PERM_UPDATE" => $arPM["PERM_UPDATE"],
					"PERM_DELETE" => $arPM["PERM_DELETE"]
				);
			}


			$arSaleManagerGroups = array();
			$dbSaleManagerGroups = $APPLICATION->GetGroupRightList(array("MODULE_ID" => "sale", "G_ACCESS" => "U"));
			while ($arSaleManagerGroup = $dbSaleManagerGroups->Fetch())
				$arSaleManagerGroups[] = IntVal($arSaleManagerGroup["GROUP_ID"]);

			$dbGroups = CGroup::GetList(
					($b = "c_sort"),
					($o = "asc"),
					array("ANONYMOUS" => "N")
				);
			while ($arGroup = $dbGroups->Fetch())
			{
				$arGroup["ID"] = IntVal($arGroup["ID"]);

				if ($arGroup["ID"] == 1 || $arGroup["ID"] == 2 || !in_array($arGroup["ID"], $arSaleManagerGroups))
					continue;

				$str_PM_VIEW = "N";
				$str_PM_CANCEL = "N";
				$str_PM_DELIVERY = "N";
				$str_PM_PAYMENT = "N";
				$str_PM_STATUS = "N";
				$str_PM_STATUS_FROM = "N";
				$str_PM_UPDATE = "N";
				$str_PM_DELETE = "N";

				if (isset($arPermsMatrix[$arGroup["ID"]]))
				{
					$str_PM_VIEW = $arPermsMatrix[$arGroup["ID"]]["PERM_VIEW"];
					$str_PM_CANCEL = $arPermsMatrix[$arGroup["ID"]]["PERM_CANCEL"];
					$str_PM_DELIVERY = $arPermsMatrix[$arGroup["ID"]]["PERM_DELIVERY"];
					$str_PM_PAYMENT = $arPermsMatrix[$arGroup["ID"]]["PERM_PAYMENT"];
					$str_PM_STATUS = $arPermsMatrix[$arGroup["ID"]]["PERM_STATUS"];
					$str_PM_STATUS_FROM = $arPermsMatrix[$arGroup["ID"]]["PERM_STATUS_FROM"];
					$str_PM_UPDATE = $arPermsMatrix[$arGroup["ID"]]["PERM_UPDATE"];
					$str_PM_DELETE = $arPermsMatrix[$arGroup["ID"]]["PERM_DELETE"];
				}

				if ($bInitVars)
				{
					$str_PM_VIEW = ${"PM_VIEW_".$arGroup["ID"]};
					$str_PM_CANCEL = ${"PM_CANCEL_".$arGroup["ID"]};
					$str_PM_DELIVERY = ${"PM_DELIVERY_".$arGroup["ID"]};
					$str_PM_PAYMENT = ${"PM_PAYMENT_".$arGroup["ID"]};
					$str_PM_STATUS = ${"PM_STATUS_".$arGroup["ID"]};
					$str_PM_STATUS_FROM = ${"PM_STATUS_FROM_".$arGroup["ID"]};
					$str_PM_UPDATE = ${"PM_UPDATE_".$arGroup["ID"]};
					$str_PM_DELETE = ${"PM_DELETE_".$arGroup["ID"]};
				}
				?>
				<tr>
					<td><?= htmlspecialcharsbx($arGroup["NAME"]) ?></td>
					<td align="center"><input type="checkbox"<?if ($str_PM_VIEW == "Y") echo "checked";?> name="PM_VIEW_<?= $arGroup["ID"] ?>" OnClick="PMOnClick(<?= $arGroup["ID"] ?>)" value="Y"></td>
					<td align="center"><input type="checkbox"<?if ($str_PM_CANCEL == "Y") echo "checked";?> name="PM_CANCEL_<?= $arGroup["ID"] ?>" OnClick="PMOnClick(<?= $arGroup["ID"] ?>)" value="Y"></td>
					<td align="center"><input type="checkbox"<?if ($str_PM_DELIVERY == "Y") echo "checked";?> name="PM_DELIVERY_<?= $arGroup["ID"] ?>" OnClick="PMOnClick(<?= $arGroup["ID"] ?>)" value="Y"></td>
					<td align="center"><input type="checkbox"<?if ($str_PM_PAYMENT == "Y") echo "checked";?> name="PM_PAYMENT_<?= $arGroup["ID"] ?>" OnClick="PMOnClick(<?= $arGroup["ID"] ?>)" value="Y"></td>
					<td align="center"><input type="checkbox"<?if ($str_PM_STATUS == "Y") echo "checked";?> name="PM_STATUS_<?= $arGroup["ID"] ?>" OnClick="PMOnClick(<?= $arGroup["ID"] ?>)" value="Y"></td>
					<td align="center"><input type="checkbox"<?if ($str_PM_STATUS_FROM == "Y") echo "checked";?> name="PM_STATUS_FROM_<?= $arGroup["ID"] ?>" OnClick="PMOnClick(<?= $arGroup["ID"] ?>)" value="Y"></td>
					<td align="center"><input type="checkbox"<?if ($str_PM_UPDATE == "Y") echo "checked";?> name="PM_UPDATE_<?= $arGroup["ID"] ?>" OnClick="PMOnClick(<?= $arGroup["ID"] ?>)" value="Y"></td>
					<td align="center"><input type="checkbox"<?if ($str_PM_DELETE == "Y") echo "checked";?> name="PM_DELETE_<?= $arGroup["ID"] ?>" OnClick="PMOnClick(<?= $arGroup["ID"] ?>)" value="Y"></td>
				</tr>
				<script language="JavaScript">
				<!--
				PMOnClick(<?= $arGroup["ID"] ?>);
				//-->
				</script>
				<?
			}
			?>

			</table>
		</td>
	</tr>

<?
$tabControl->EndTab();
?>

<?
$tabControl->Buttons(
		array(
				"disabled" => ($saleModulePermissions < "W"),
				"back_url" => "/bitrix/admin/sale_status.php?lang=".LANG.GetFilterParams("filter_")
			)
	);
?>

<?
$tabControl->End();
?>

</form>
<?require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");?>