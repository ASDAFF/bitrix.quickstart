<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

if(!CBXFeatures::IsFeatureEnabled('SaleRecurring'))
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	ShowError(GetMessage("SALE_FEATURE_NOT_ALLOW"));

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

IncludeModuleLangFile(__FILE__);

$errorMessage = "";
$bVarsFromForm = false;

ClearVars();

$ID = IntVal($ID);

$simpleForm = COption::GetOptionString("sale", "lock_catalog", "Y");
$bSimpleForm = (($simpleForm=="Y") ? True : False);

if ($bSimpleForm)
{
	if ($ID > 0)
	{
		if ($arRecurring = CSaleRecurring::GetByID($ID))
		{
			if ($arRecurring["MODULE"] != "catalog"
				|| $arRecurring["CALLBACK_FUNC"] != "CatalogRecurringCallback")
			{
				$bSimpleForm = False;
			}
		}
	}
}

if ($REQUEST_METHOD=="POST" && strlen($Update)>0 && $saleModulePermissions >= "U" && check_bitrix_sessid())
{
	if ($ID <= 0 && $saleModulePermissions < "W")
		$errorMessage .= GetMessage("SRE_NO_PERMS2ADD").".<br>";

	$NEXT_DATE = Trim($NEXT_DATE);
	if (strlen($NEXT_DATE) <= 0)
		$errorMessage .= GetMessage("SRE_EMPTY_NEXT").".<br>";

	if ($saleModulePermissions >= "W")
	{
		$USER_ID = IntVal($USER_ID);
		if ($USER_ID <= 0)
			$errorMessage .= GetMessage("SRE_EMPTY_USER").".<br>";

		$MODULE = Trim($MODULE);
		if (strlen($MODULE) <= 0)
			$errorMessage .= GetMessage("SRE_EMPTY_MODULE").".<br>";

		$PRODUCT_ID = IntVal($PRODUCT_ID);
		if ($PRODUCT_ID <= 0)
			$errorMessage .= GetMessage("SRE_EMPTY_PRODUCT").".<br>";

		$CALLBACK_FUNC = Trim($CALLBACK_FUNC);
		if (strlen($CALLBACK_FUNC) <= 0)
			$errorMessage .= GetMessage("SRE_EMPTY_CALLBACK").".<br>";
	}

	$ORDER_ID = IntVal($ORDER_ID);
	if ($ORDER_ID <= 0)
		$errorMessage .= GetMessage("SRE_EMPTY_BASE_ORDER").".<br>";

	if (strlen($errorMessage) <= 0)
	{
		$CANCELED = (($CANCELED == "Y") ? "Y" : "N");
		$PRIOR_DATE = Trim($PRIOR_DATE);
		$REMAINING_ATTEMPTS = IntVal($REMAINING_ATTEMPTS);
		$SUCCESS_PAYMENT = (($SUCCESS_PAYMENT == "Y") ? "Y" : "N");

		$arFields = array(
				"CANCELED" => $CANCELED,
				"PRIOR_DATE" => ((strlen($PRIOR_DATE) > 0) ? $PRIOR_DATE : False),
				"NEXT_DATE" => $NEXT_DATE,
				"DESCRIPTION" => ((strlen($DESCRIPTION) > 0) ? $DESCRIPTION : False),
				"CANCELED_REASON" => ((strlen($CANCELED_REASON) > 0) ? $CANCELED_REASON : False),
				"ORDER_ID" => $ORDER_ID,
				"REMAINING_ATTEMPTS" => $REMAINING_ATTEMPTS,
				"SUCCESS_PAYMENT" => $SUCCESS_PAYMENT
			);
		if ($saleModulePermissions >= "W")
		{
			$arFields["USER_ID"] = $USER_ID;
			$arFields["MODULE"] = $MODULE;
			$arFields["PRODUCT_ID"] = $PRODUCT_ID;
			$arFields["PRODUCT_NAME"] = $PRODUCT_NAME;
			$arFields["PRODUCT_URL"] = $PRODUCT_URL;
			$arFields["CALLBACK_FUNC"] = $CALLBACK_FUNC;
		}

		if ($ID > 0)
		{
			$res = CSaleRecurring::Update($ID, $arFields);
		}
		else
		{
			$ID = CSaleRecurring::Add($arFields);
			$res = ($ID > 0);
		}

		if (!$res)
		{
			$bVarsFromForm = true;
			if ($ex = $APPLICATION->GetException())
				$errorMessage .= $ex->GetString().".<br>";
			else
				$errorMessage .= GetMessage("SRE_ERROR_SAVING").".<br>";
		}
		else
		{
			if (strlen($apply)<=0)
				LocalRedirect("/bitrix/admin/sale_recurring_admin.php?lang=".LANG.GetFilterParams("filter_", false));
		}
	}
	else
	{
		$bVarsFromForm = true;
	}
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

if ($ID > 0)
	$APPLICATION->SetTitle(GetMessage("SRE_TITLE_UPDATE"));
else
	$APPLICATION->SetTitle(GetMessage("SRE_TITLE_ADD"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");


$dbRecurring = CSaleRecurring::GetList(
		array(),
		array("ID" => $ID),
		false,
		false,
		array("ID", "USER_ID", "MODULE", "PRODUCT_ID", "PRODUCT_NAME", "PRODUCT_URL", "PRODUCT_PRICE_ID", "RECUR_SCHEME_TYPE", "RECUR_SCHEME_LENGTH", "WITHOUT_ORDER", "PRICE", "CURRENCY", "ORDER_ID", "CANCELED", "CALLBACK_FUNC", "DESCRIPTION", "TIMESTAMP_X", "PRIOR_DATE", "NEXT_DATE", "REMAINING_ATTEMPTS", "SUCCESS_PAYMENT", "USER_LOGIN", "USER_NAME", "USER_LAST_NAME", "CANCELED_REASON")
	);
if (!$dbRecurring->ExtractFields("str_"))
{
	if ($saleModulePermissions < "W")
		$errorMessage .= GetMessage("SRE_NO_PERMS2ADD").".<br>";
	$ID = 0;
	$str_CANCELED = "N";
	$str_REMAINING_ATTEMPTS = (Defined("SALE_PROC_REC_ATTEMPTS") ? SALE_PROC_REC_ATTEMPTS : 3);
	$str_SUCCESS_PAYMENT = "Y";
}

if ($bVarsFromForm)
	$DB->InitTableVarsForEdit("b_sale_recurring", "", "str_");

?>

<?
$aMenu = array(
		array(
				"TEXT" => GetMessage("SREN_2FLIST"),
				"ICON" => "btn_list",
				"LINK" => "/bitrix/admin/sale_recurring_admin.php?lang=".LANG.GetFilterParams("filter_")
			)
	);

if ($ID > 0 && $saleModulePermissions >= "U")
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
			"TEXT" => GetMessage("SREN_NEW_RECURR"),
			"ICON" => "btn_new",
			"LINK" => "/bitrix/admin/sale_recurring_edit.php?lang=".LANG.GetFilterParams("filter_")
		);

	if ($saleModulePermissions >= "W")
	{
		$aMenu[] = array(
				"TEXT" => GetMessage("SREN_DELETE_RECURR"), 
				"ICON" => "btn_delete",
				"LINK" => "javascript:if(confirm('".GetMessage("SREN_DELETE_RECURR_CONFIRM")."')) window.location='/bitrix/admin/sale_recurring_admin.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."#tb';",
				"WARNING" => "Y"
			);
	}
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?if(strlen($errorMessage)>0)
	echo CAdminMessage::ShowMessage(Array("DETAILS"=>$errorMessage, "TYPE"=>"ERROR", "MESSAGE"=>GetMessage("SRE_ERROR"), "HTML"=>true));?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="frecurring_edit">
<?echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANG ?>">
<input type="hidden" name="ID" value="<?echo $ID ?>">
<?=bitrix_sessid_post()?>

<?
$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("SREN_TAB_RECURR"), "ICON" => "sale", "TITLE" => GetMessage("SREN_TAB_RECURR_DESCR"))
	);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>

<?
$tabControl->BeginNextTab();
?>

	<?if ($ID > 0):?>
		<tr>
			<td width="40%">ID:</td>
			<td width="60%"><?=$ID?></td>
		</tr>
		<tr>
			<td><?echo GetMessage("SRE_TIMESTAMP")?></td>
			<td><?=$str_TIMESTAMP_X?></td>
		</tr>
	<?endif;?>
	<tr class="adm-detail-required-field">
		<td width="40%"><?echo GetMessage("SRE_USER1")?></td>
		<td width="60%"><?
			if ($ID>0 && $str_USER_ID>0)
				$user_name = "[<a title=\"".GetMessage("SRE_USER_PROFILE")."\" href=\"/bitrix/admin/user_edit.php?lang=".LANGUAGE_ID."&ID=".$str_USER_ID."\">".$str_USER_ID."</a>] (".$str_USER_LOGIN.") ".$str_USER_NAME." ".$str_USER_LAST_NAME;

			if ($saleModulePermissions>="W"):
				echo FindUserID("USER_ID", $str_USER_ID, $user_name, "frecurring_edit");
			else:
				echo $user_name;
			endif;
			?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("SRE_CANCELED")?></td>
		<td>
			<input type="checkbox" name="CANCELED" value="Y"<?if ($str_CANCELED=="Y") echo " checked"?>>
		</td>
	</tr>
	<tr>
		<td valign="top"><?echo GetMessage("SRE_CANCEL_REASON")?></td>
		<td valign="top">
			<textarea name="CANCELED_REASON" rows="2" cols="40"><?= $str_CANCELED_REASON ?></textarea>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SRE_MODULE")?></td>
		<td>
			<?if ($saleModulePermissions >= "W"):?>
				<script language="JavaScript">
				<!--
				function ModuleChange()
				{
					var m = document.frecurring_edit.MODULE;
					if (!m)
						return;

					if (m.tagName.toUpperCase() == "SELECT")
					{
						if (m[m.selectedIndex].value == "catalog")
						{
							document.getElementById("cat_prod_button").disabled = false;
						}
						else
						{
							document.getElementById("cat_prod_button").disabled = true;
						}
					}
					else
					{
						if (m.value == "catalog")
						{
							document.getElementById("cat_prod_button").disabled = false;
						}
						else
						{
							document.getElementById("cat_prod_button").disabled = true;
						}
					}
				}
				//-->
				</script>

				<?if ($bSimpleForm):?>
					<input type="hidden" name="MODULE" value="catalog">
					<input type="hidden" name="CALLBACK_FUNC" value="CatalogRecurringCallback">
					<?= GetMessage("SRE_MODULE_CATALOG") ?>
				<?else:?>
					<select name="MODULE" OnChange="ModuleChange()">
						<?
						$dbModuleList = CModule::GetList();
						while ($arModuleList = $dbModuleList->Fetch())
						{
							?><option value="<?= $arModuleList["ID"] ?>"<?if ($str_MODULE == $arModuleList["ID"]) echo " selected";?>><?= htmlspecialcharsEx($arModuleList["ID"]) ?></option><?
						}
						?>
					</select>
				<?endif;?>
			<?else:?>
				
				<?= $str_MODULE ?>
				
			<?endif;?>
		</td>
	</tr>
	<?if (!$bSimpleForm):?>
		<?if ($saleModulePermissions >= "W"):?>
			<tr class="adm-detail-required-field">
				<td><?echo GetMessage("SRE_CALLBACK")?></td>
				<td><input type="text" name="CALLBACK_FUNC" size="30" maxlength="30" value="<?=$str_CALLBACK_FUNC?>"></td>
			</tr>
		<?endif;?>
	<?endif;?>
	<tr class="adm-detail-required-field">
		<td><?echo GetMessage("SRE_PRODUCT")?></td>
		<td>
			<?if ($saleModulePermissions >= "W"):?>
				<script language="JavaScript">
				<!--
				function FillProductFields(index, arParams, iblockID)
				{
					if (arParams["id"])
						document.frecurring_edit.PRODUCT_ID.value = arParams["id"];

					if (arParams["name"])
						document.frecurring_edit.PRODUCT_NAME.value = arParams["name"];

					if (arParams["url"])
						document.frecurring_edit.PRODUCT_URL.value = arParams["url"];
				}
				//-->
				</script>
				<input name="PRODUCT_ID" value="<?= $str_PRODUCT_ID ?>" size="5" type="text">&nbsp;<input type="button" value="..." id="cat_prod_button" onClick="window.open('sale_product_search.php?func_name=FillProductFields', '', 'scrollbars=yes,resizable=yes,width=800,height=500,top='+Math.floor((screen.height - 500)/2-14)+',left='+Math.floor((screen.width - 800)/2-5));">
				<script language="JavaScript">
				<!--
				ModuleChange();
				//-->
				</script>
			<?else:?>
				[<?= $str_PRODUCT_ID ?>] <?= $str_PRODUCT_NAME ?>
			<?endif;?>
		</td>
	</tr>
	<?if ($saleModulePermissions >= "W"):?>
		<tr>
			<td><?echo GetMessage("SRE_PRODUCT_NAME")?></td>
			<td>
				<input type="text" name="PRODUCT_NAME" size="30" maxlength="250" value="<?= $str_PRODUCT_NAME; ?>">
			</td>
		</tr>
		<tr>
			<td><?echo GetMessage("SRE_PRODUCT_URL")?></td>
			<td>
				<input type="text" name="PRODUCT_URL" size="30" maxlength="250" value="<?= $str_PRODUCT_URL; ?>">
			</td>
		</tr>
	<?endif;?>
	<tr>
		<td valign="top"><?echo GetMessage("SRE_LAST_DATE")?>:</td>
		<td valign="top">
			<?= CalendarDate("PRIOR_DATE", $str_PRIOR_DATE, "frecurring_edit", "20", "class=\"typeinput\""); ?>
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td valign="top"><?echo GetMessage("SRE_NEXT_DATE")?>:</td>
		<td valign="top">
			<?= CalendarDate("NEXT_DATE", $str_NEXT_DATE, "frecurring_edit", "20", "class=\"typeinput\""); ?>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SRE_LAST_SUCCESS")?></td>
		<td>
			<input type="checkbox" name="SUCCESS_PAYMENT" value="Y"<?if ($str_SUCCESS_PAYMENT=="Y") echo " checked"?>>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SRE_STEPS")?></td>
		<td>
			<input type="text" name="REMAINING_ATTEMPTS" size="5" maxlength="5" value="<?= $str_REMAINING_ATTEMPTS ?>">
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><?echo GetMessage("SRE_BASE_ORDER")?></td>
		<td>
			<input type="text" name="ORDER_ID" size="10" maxlength="10" value="<?= $str_ORDER_ID ?>">
		</td>
	</tr>
	<tr>
		<td valign="top"><?echo GetMessage("SRE_DESCRIPTION")?></td>
		<td valign="top">
			<textarea name="DESCRIPTION" rows="2" cols="40"><?= $str_DESCRIPTION ?></textarea>
		</td>
	</tr>

<?
$tabControl->EndTab();
?>

<?
$tabControl->Buttons(
		array(
				"disabled" => ($saleModulePermissions < "U"),
				"back_url" => "/bitrix/admin/sale_recurring_admin.php?lang=".LANG.GetFilterParams("filter_")
			)
	);
?>

<?
$tabControl->End();
?>
</form>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
