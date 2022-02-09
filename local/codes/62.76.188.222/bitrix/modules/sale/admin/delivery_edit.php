<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions < "W")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

ClearVars();

$ID = IntVal($ID);

$strError = "";
$bInitVars = false;
if ((strlen($save)>0 || strlen($apply)>0) && $REQUEST_METHOD=="POST" && $saleModulePermissions=="W" && check_bitrix_sessid())
{
	$LID = Trim($LID);
	if (strlen($LID)<=0)
		$strError .= GetMessage("ERROR_NO_LID")."<br>";

	$NAME = Trim($NAME);
	if (strlen($NAME)<=0)
		$strError .= GetMessage("ERROR_NO_NAME")."<br>";

	$PRICE = str_replace(",", ".", $PRICE);
	$PRICE = DoubleVal($PRICE);
	if ($PRICE<0)
		$strError .= GetMessage("ERROR_NO_PRICE")."<br>";

	$CURRENCY = Trim($CURRENCY);
	if (strlen($CURRENCY)<=0)
		$strError .= GetMessage("ERROR_NO_CURRENCY")."<br>";

	$ORDER_PRICE_FROM = str_replace(",", ".", $ORDER_PRICE_FROM);
	$ORDER_PRICE_TO = str_replace(",", ".", $ORDER_PRICE_TO);
	$ORDER_CURRENCY = Trim($ORDER_CURRENCY);
	if ((DoubleVal($ORDER_PRICE_FROM)>0 || DoubleVal($ORDER_PRICE_TO)>0) && strlen($ORDER_CURRENCY)<=0)
		$strError .= GetMessage("ERROR_PRICE_NO_CUR")."<br>";

	if ($ACTIVE!="Y") $ACTIVE = "N";

	$SORT = IntVal($SORT);
	if ($SORT<=0) $SORT = 100;

	$arLocation = array();
	if (isset($LOCATION1) && is_array($LOCATION1) && count($LOCATION1)>0)
	{
		for ($i = 0; $i<count($LOCATION1); $i++)
		{
			if (IntVal($LOCATION1[$i])>0)
			{
				$arLocation[] = array(
					"LOCATION_ID" => IntVal($LOCATION1[$i]),
					"LOCATION_TYPE" => "L"
					);
			}
		}
	}

	if (isset($LOCATION2) && is_array($LOCATION2) && count($LOCATION2)>0)
	{
		for ($i = 0; $i<count($LOCATION2); $i++)
		{
			if (IntVal($LOCATION2[$i])>0)
			{
				$arLocation[] = array(
					"LOCATION_ID" => IntVal($LOCATION2[$i]),
					"LOCATION_TYPE" => "G"
					);
			}
		}
	}

	if (!is_array($arLocation) || count($arLocation)<=0)
		$strError .= GetMessage("ERROR_NO_LOCATION")."<br>";

	if (strlen($strError)<=0)
	{
		unset($arFields);
		$arFields = array(
			"NAME" => $NAME,
			"LID" => $LID,
			"PERIOD_FROM" => $PERIOD_FROM,
			"PERIOD_TO" => $PERIOD_TO,
			"PERIOD_TYPE" => $PERIOD_TYPE,
			"WEIGHT_FROM" => $WEIGHT_FROM,
			"WEIGHT_TO" => $WEIGHT_TO,
			"ORDER_PRICE_FROM" => $ORDER_PRICE_FROM,
			"ORDER_PRICE_TO" => $ORDER_PRICE_TO,
			"ORDER_CURRENCY" => $ORDER_CURRENCY,
			"ACTIVE" => $ACTIVE,
			"PRICE" => $PRICE,
			"CURRENCY" => $CURRENCY,
			"SORT" => $SORT,
			"DESCRIPTION" => $DESCRIPTION,
			"LOCATIONS" => $arLocation
			);

		if ($ID>0)
		{
			if (!CSaleDelivery::Update($ID, $arFields))
				$strError .= GetMessage("ERROR_EDIT_DELIVERY")."<br>";
		}
		else
		{
			$ID = CSaleDelivery::Add($arFields);
			if ($ID<=0)
				$strError .= GetMessage("ERROR_ADD_DELIVERY")."<br>";
		}
	}

	if (strlen($strError)>0) $bInitVars = True;

	if (strlen($save)>0 && strlen($strError)<=0)
		LocalRedirect("sale_delivery.php?lang=".LANG.GetFilterParams("filter_", false));
}

if ($ID>0)
{
	$db_delivery = CSaleDelivery::GetList(Array("SORT"=>"ASC"), Array("ID"=>$ID));
	$db_delivery->ExtractFields("str_");

	$arDeliveryDescription = CSaleDelivery::GetByID($ID);
	$str_DESCRIPTION = $arDeliveryDescription["DESCRIPTION"];
}
else
{
	$str_ACTIVE = 'Y';
}

if ($bInitVars)
{
	$DB->InitTableVarsForEdit("b_sale_delivery", "", "str_");
}

$sDocTitle = ($ID>0) ? str_replace("#ID#", $ID, GetMessage("SALE_EDIT_RECORD")) : GetMessage("SALE_NEW_RECORD");
$APPLICATION->SetTitle($sDocTitle);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

/*********************************************************************/
/********************  BODY  *****************************************/
/*********************************************************************/
?>
<?
$aMenu = array(
		array(
				"TEXT" => GetMessage("SDEN_2FLIST"),
				"LINK" => "/bitrix/admin/sale_delivery.php?lang=".LANG.GetFilterParams("filter_"),
				"ICON" => "btn_list"
			)
	);

if ($ID > 0 && $saleModulePermissions >= "W")
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
			"TEXT" => GetMessage("SDEN_NEW_DELIVERY"),
			"LINK" => "/bitrix/admin/sale_delivery_edit.php?lang=".LANG.GetFilterParams("filter_"),
			"ICON" => "btn_new"
		);

	$aMenu[] = array(
			"TEXT" => GetMessage("SDEN_DELETE_DELIVERY"),
			"LINK" => "javascript:if(confirm('".GetMessage("SDEN_DELETE_DELIVERY_CONFIRM")."')) window.location='/bitrix/admin/sale_delivery.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."#tb';",
			"ICON" => "btn_delete"
		);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?if(strlen($strError)>0)
	echo CAdminMessage::ShowMessage(Array("DETAILS"=>$strError, "TYPE"=>"ERROR", "MESSAGE"=>GetMessage("SDEN_ERROR"), "HTML"=>true));?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="form1">
<?echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANG ?>">
<input type="hidden" name="ID" value="<?echo $ID ?>">
<?=bitrix_sessid_post()?>

<?
$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("SDEN_TAB_DELIVERY"), "ICON" => "sale", "TITLE" => GetMessage("SDEN_TAB_DELIVERY_DESCR"))
	);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>

<?
$tabControl->BeginNextTab();
?>

	<?if ($ID>0):?>
		<tr>
			<td width="40%">ID:</td>
			<td width="60%"><?echo $ID ?></td>
		</tr>
	<?endif;?>

	<tr class="adm-detail-required-field">
		<td width="40%"><?echo GetMessage("F_NAME") ?>:</td>
		<td width="60%"><input type="text" name="NAME" value="<?echo $str_NAME ?>" size="40"></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td width="40%"><?echo GetMessage("F_LANG") ?>:</td>
		<td width="60%"><?echo CLang::SelectBox("LID", $str_LID, "")?></td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_PERIOD_FROM") ?>:</td>
		<td width="60%">
			<?echo GetMessage("SALE_FROM")?>
			<input type="text" name="PERIOD_FROM" value="<?echo $str_PERIOD_FROM ?>" size="3">
			<?echo GetMessage("SALE_TO")?>
			<input type="text" name="PERIOD_TO" value="<?echo $str_PERIOD_TO ?>" size="3">
			<?
			$arPerType = array(
				"D" => GetMessage("PER_DAY"),
				"H" => GetMessage("PER_HOUR"),
				"M" => GetMessage("PER_MONTH")
				);
			?>
			<select name="PERIOD_TYPE">
				<?foreach ($arPerType as $key => $value):?>
					<option value="<?echo $key ?>" <?if ($key==$str_PERIOD_TYPE) echo "selected"?>><?echo $value ?></option>
				<?endforeach;?>
			</select>

		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_WEIGHT")?> (<?echo GetMessage('WEIGHT_G')?>):</td>
		<td width="60%">
			<?echo GetMessage("SALE_FROM")?>
			<input type="text" name="WEIGHT_FROM" value="<?echo $str_WEIGHT_FROM ?>" size="7">
			<?echo GetMessage("SALE_TO")?>
			<input type="text" name="WEIGHT_TO" value="<?echo $str_WEIGHT_TO ?>" size="7">
		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_ORDER_PRICE")?>:</td>
		<td width="60%">
			<?echo GetMessage("SALE_FROM")?>
			<input type="text" name="ORDER_PRICE_FROM" value="<?echo $str_ORDER_PRICE_FROM ?>" size="10">
			<?echo GetMessage("SALE_TO")?>
			<input type="text" name="ORDER_PRICE_TO" value="<?echo $str_ORDER_PRICE_TO ?>" size="10">
			<?echo CCurrency::SelectBox("ORDER_CURRENCY", $str_ORDER_CURRENCY, "", false, "", "")?>
		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_ACTIVE")?>:</td>
		<td width="60%">
			<input type="checkbox" name="ACTIVE" value="Y" <?if ($str_ACTIVE=="Y") echo "checked";?>>
		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_PRICE");?>:</td>
		<td width="60%">
			<input type="text" name="PRICE" value="<?echo $str_PRICE ?>" size="10">
			<?echo CCurrency::SelectBox("CURRENCY", $str_CURRENCY, "", false, "", "")?>
		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_SORT") ?>:</td>
		<td width="60%">
			<input type="text" name="SORT" value="<?echo $str_SORT ?>" size="40">
		</td>
	</tr>
	<tr>
		<td width="40%" valign="top"><?echo GetMessage("F_DESCRIPTION");?>:</td>
		<td width="60%" valign="top">
			<textarea rows="3" cols="40" name="DESCRIPTION"><?echo $str_DESCRIPTION;?></textarea>
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td width="40%" valign="top"><?echo GetMessage("F_LOCATION1");?>:</td>
		<td width="60%" valign="top">
			<select name="LOCATION1[]" size="5" multiple>
				<?$db_vars = CSaleLocation::GetList(Array("COUNTRY_NAME_LANG"=>"ASC", "REGION_NAME_LANG"=>"ASC", "CITY_NAME_LANG"=>"ASC"), array(), LANG)?>
				<?
				$arLOCATION1 = array();
				if ($bInitVars)
				{
					$arLOCATION1 = $LOCATION1;
				}
				else
				{
					$db_location = CSaleDelivery::GetLocationList(Array("DELIVERY_ID" => $ID, "LOCATION_TYPE" => "L"));
					while ($arLocation = $db_location->Fetch())
					{
						$arLOCATION1[] = $arLocation["LOCATION_ID"];
					}
				}
				?>
				<?while ($vars = $db_vars->Fetch()):
					$locationName = $vars["COUNTRY_NAME"];

					if (strlen($vars["REGION_NAME"]) > 0)
					{
						if (strlen($locationName) > 0)
							$locationName .= " - ";
						$locationName .= $vars["REGION_NAME"];
					}
					if (strlen($vars["CITY_NAME"]) > 0)
					{
						if (strlen($locationName) > 0)
							$locationName .= " - ";
						$locationName .= $vars["CITY_NAME"];
					}
				?>
					<option value="<?echo $vars["ID"]?>"<?if (is_array($arLOCATION1) && in_array(IntVal($vars["ID"]), $arLOCATION1)) echo " selected"?>><?echo htmlspecialcharsbx($locationName)?></option>
				<?endwhile;?>
			</select>
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td width="40%" valign="top"><?echo GetMessage("F_LOCATION2");?>:</td>
		<td width="60%" valign="top">
			<select name="LOCATION2[]" size="5" multiple>
				<?$db_vars = CSaleLocationGroup::GetList(Array("NAME"=>"ASC"), array(), LANG)?>
				<?
				$arLOCATION2 = array();
				if ($bInitVars)
				{
					$arLOCATION2 = $LOCATION2;
				}
				else
				{
					$db_location = CSaleDelivery::GetLocationList(Array("DELIVERY_ID" => $ID, "LOCATION_TYPE" => "G"));
					while ($arLocation = $db_location->Fetch())
					{
						$arLOCATION2[] = $arLocation["LOCATION_ID"];
					}
				}
				?>
				<?while ($vars = $db_vars->Fetch()):?>
					<option value="<?echo $vars["ID"]?>"<?if (is_array($arLOCATION2) && in_array(IntVal($vars["ID"]), $arLOCATION2)) echo " selected"?>><?echo htmlspecialcharsbx($vars["NAME"])?></option>
				<?endwhile;?>
			</select>
		</td>
	</tr>

<?
$tabControl->EndTab();
?>

<?
$tabControl->Buttons(
		array(
				"disabled" => ($saleModulePermissions < "W"),
				"back_url" => "/bitrix/admin/sale_delivery.php?lang=".LANG.GetFilterParams("filter_")
			)
	);
?>

<?
$tabControl->End();
?>

</form>
<?require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");?>