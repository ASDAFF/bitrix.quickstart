<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

IncludeModuleLangFile(__FILE__);

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
	$APPLICATION->AuthForm(GetMessage("BUYER_PE_ACCESS_DENIED"));

if(!CBXFeatures::IsFeatureEnabled('SaleAccounts'))
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	ShowError(GetMessage("SALE_FEATURE_NOT_ALLOW"));

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

ClearVars();
$ID = IntVal($_REQUEST["id"]);


$arErrors = array();
if($arProfile = CSaleOrderUserProps::GetByID($ID))
{
	$USER_ID = IntVal($arProfile["USER_ID"]);
	$PERSON_TYPE = IntVal($arProfile["PERSON_TYPE_ID"]);
	$profileName = $arProfile["NAME"];
}
else
	$arErrors[] = GetMessage("BUYER_PE_NO_PROFILE");


/*****************************************************************************/
/**************************** SAVE PROFILE ***********************************/
/*****************************************************************************/
if ($REQUEST_METHOD == "POST" && $saleModulePermissions >= "U" && check_bitrix_sessid() && !empty($arProfile))
{
	$CODE_PROFILE_NAME = trim($_REQUEST["CODE_PROFILE_NAME"]);
	if (strlen($CODE_PROFILE_NAME) > 0)
		$profileName = $CODE_PROFILE_NAME;

	$arOrderPropsValues = array();
	$dbProperties = CSaleOrderProps::GetList(
			array("GROUP_SORT" => "ASC", "PROPS_GROUP_ID" => "ASC", "SORT" => "ASC", "NAME" => "ASC"),
			array("PERSON_TYPE_ID" => $PERSON_TYPE, "ACTIVE" => "Y", "USER_PROPS" => "Y", "UTIL" => "N"),
			false,
			false,
			array("*")
	);
	while ($arOrderProps = $dbProperties->Fetch())
	{
		$curVal = trim($_REQUEST["CODE_".IntVal($arOrderProps["ID"])]);

		if ($arOrderProps["TYPE"]=="LOCATION")
		{
			$curVal = trim($_REQUEST["LOCATION_".IntVal($arOrderProps["ID"])]);
		}

		if ($arOrderProps["TYPE"] == "MULTISELECT")
		{
			$curVal = "";
			if (is_array($_REQUEST["CODE_".IntVal($arOrderProps["ID"])]))
			{
				foreach ($_REQUEST["CODE_".IntVal($arOrderProps["ID"])] as $key => $val)
				{
					$curVal .= trim($val);
					if ($key < (count($_REQUEST["CODE_".IntVal($arOrderProps["ID"])]) - 1))
						$curVal .= ",";
				}
			}
		}

		if (
			($arOrderProps["IS_LOCATION"]=="Y" || $arOrderProps["IS_LOCATION4TAX"]=="Y")
			&& IntVal($curVal) <= 0
			||
			($arOrderProps["IS_ZIP"] == "Y" && strlen($curVal) <= 0)
			||
			($arOrderProps["IS_PROFILE_NAME"]=="Y" || $arOrderProps["IS_PAYER"]=="Y")
			&& strlen($curVal) <= 0
			||
			$arOrderProps["REQUIED"]=="Y"
			&& $arOrderProps["TYPE"]=="LOCATION"
			&& IntVal($curVal) <= 0
			||
			$arOrderProps["REQUIED"]=="Y"
			&& ($arOrderProps["TYPE"]=="TEXT" || $arOrderProps["TYPE"]=="TEXTAREA" || $arOrderProps["TYPE"]=="RADIO" || $arOrderProps["TYPE"]=="SELECT")
			&& strlen($curVal) <= 0
			||
			($arOrderProps["REQUIED"]=="Y"
			&& $arOrderProps["TYPE"]=="MULTISELECT"
			&& strlen($curVal) <= 0)
			)
		{
			$arErrors[] = str_replace("#NAME#", $arOrderProps["NAME"], GetMessage("BUYER_PE_EMPTY_PROPS"));
		}

		$arOrderPropsValues[$arOrderProps["ID"]] = $curVal;
	}

	if (count($arErrors) <= 0)
		CSaleOrderUserProps::DoSaveUserProfile($USER_ID, $ID, $profileName, $PERSON_TYPE, $arOrderPropsValues, $arErrors);

	if (isset($_REQUEST["save"]) && strlen($_REQUEST["save"]) > 0 && count($arErrors) <= 0)
		LocalRedirect("/bitrix/admin/sale_buyers_profile.php?lang=".LANG."&ID=".$USER_ID);
	elseif (isset($_REQUEST["apply"]) && strlen($_REQUEST["apply"]) > 0 && count($arErrors) <= 0)
		LocalRedirect("/bitrix/admin/sale_buyers_profile_edit.php?id=".$ID."&lang=".LANG);
}


/*****************************************************************************/
/***************************** FORM EDIT *************************************/
/*****************************************************************************/

if(IntVal($USER_ID) > 0)
{
	$dbUser = CUser::GetByID($USER_ID);
	if($arUser = $dbUser->Fetch())
	{
		$userFIO = $arUser["NAME"];
		if (strlen($arUser["LAST_NAME"]) > 0)
		{
			if (strlen($userFIO) > 0)
				$userFIO .= " ";
			$userFIO .= $arUser["LAST_NAME"];
		}
	}
	else
		$arErrors[] = GetMessage("BUYER_PE_NO_USER");
}
else
	$arErrors[] = GetMessage("BUYER_PE_NO_USER");

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("BUYER_PE_TAB_PROFILE"), "ICON" => "sale", "TITLE" => GetMessage("BUYER_PE_TAB_PROFILE_TITLE")),
);
$tabControl = new CAdminForm("form_edit_profile", $aTabs, false);

$APPLICATION->SetTitle(str_replace("#NAME#", $profileName, GetMessage("BUYER_PE_TITLE")));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$link = urlencode($GLOBALS["APPLICATION"]->GetCurPage())."?mode=settings";
$aMenu = array();
$aMenu = array(
	array(
		"TEXT" => GetMessage("BUYER_PE_LIST_PROFILE"),
		"LINK" => "/bitrix/admin/sale_buyers_profile.php?ID=".$USER_ID."&lang=".LANGUAGE_ID
	)
);
$aMenu[] = array(
	"TEXT"=>GetMessage("BUYER_PE_FIELD"),
	"TITLE"=>GetMessage("BUYER_PE_FIELD_TITLE"),
	"LINK"=>"javascript:".$tabControl->GetName().".ShowSettings('".htmlspecialcharsbx(CUtil::addslashes($link))."')",
	"ICON"=>"btn_settings",
);

$context = new CAdminContextMenu($aMenu);
$context->Show();

if (count($arErrors) > 0)
	CAdminMessage::ShowMessage(implode("<br>", $arErrors));

$tabControl->BeginEpilogContent();
?>

<? echo bitrix_sessid_post();?>
<input type="hidden" name="id" value="<?echo $ID?>">
<?
$tabControl->EndEpilogContent();

$urlForm = "";
if ($ID > 0)
	$urlForm = "&id=".$ID;

$tabControl->Begin(array(
		"FORM_ACTION" => $APPLICATION->GetCurPage()."?lang=".LANG.$urlForm
));

//TAB EDIT PROFILE
$tabControl->BeginNextFormTab();

if(!empty($arProfile) && !empty($arUser))
{
	$dbPersonType = CSalePersonType::GetList(array(), Array("ACTIVE" => "Y", "ID" => $PERSON_TYPE));
	$arPersonType = $dbPersonType->GetNext();
	$LID = $arPersonType["LID"];


	$arFilterProps = array("PERSON_TYPE_ID" => $PERSON_TYPE, "ACTIVE" => "Y");
	if ($saleModulePermissions >= "U" && $saleModulePermissions < "W")
	{
		$arFilterProps["USER_PROPS"] = "Y";
		$arFilterProps["UTIL"] = "N";
	}

	$tabControl->AddViewField("CODE_USER", GetMessage("BUYER_PE_USER").":", "[<a href=\"/bitrix/admin/user_edit.php?ID=".$arUser["ID"]."&lang=".LANGUAGE_ID."\">".$arUser["ID"]."</a>] (".$arUser["LOGIN"].") ".$userFIO);
	$tabControl->AddEditField("CODE_PROFILE_NAME", GetMessage("BUYER_PE_PROFILE_NAME").":", false, array("size"=>30, "maxlength"=>255), htmlspecialcharsEx($profileName));

	$propertyGroupID = "";
	$dbProperties = CSaleOrderProps::GetList(
			array("GROUP_SORT" => "ASC", "PROPS_GROUP_ID" => "ASC", "SORT" => "ASC", "NAME" => "ASC"),
			$arFilterProps,
			false,
			false,
			array("*")
	);
	$userProfile = CSaleOrderUserProps::DoLoadProfiles($USER_ID, $PERSON_TYPE);
	$curVal = "";
	while ($arProperties = $dbProperties->Fetch())
	{
		$curVal = $userProfile[$ID]["VALUES"][IntVal($arProperties["ID"])];
		$fieldValue = (($curVal!="") ? $curVal : $arProperties["DEFAULT_VALUE"]);

		if (IntVal($arProperties["PROPS_GROUP_ID"]) != $propertyGroupID)
			$tabControl->AddSection("SECTION_".$arProperties["PROPS_GROUP_ID"], $arProperties["GROUP_NAME"]);

		$shure = false;
		if ($arProperties["REQUIED"] == "Y" || $arProperties["IS_PROFILE_NAME"] == "Y" || $arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y" || $arProperties["IS_PAYER"] == "Y" || $arProperties["IS_ZIP"] == "Y")
			$shure = true;

		/*fields*/
		if ($arProperties["TYPE"] == "TEXT")
			$tabControl->AddEditField("CODE_".IntVal($arProperties["ID"]), $arProperties["NAME"].":", $shure, array("size"=>30, "maxlength"=>255), $fieldValue);

		elseif ($arProperties["TYPE"] == "CHECKBOX")
			$tabControl->AddCheckBoxField("CODE_".IntVal($arProperties["ID"]), $arProperties["NAME"].":", $shure, "Y", $fieldValue);

		elseif ($arProperties["TYPE"] == "SELECT")
		{
			$tabControl->BeginCustomField("CODE_".IntVal($arProperties["ID"]), $arProperties["NAME"], $shure);
			?>
			<tr<? ($shure) ? " class=\"adm-detail-required-field\"" : "" ?>>
				<td width="40%">
					<?echo htmlspecialcharsbx($arProperties["NAME"]);?>:
				</td>
				<td width="60%">
					<select name="<?echo "CODE_".IntVal($arProperties["ID"]);?>">
					<?
					$dbVariants = CSaleOrderPropsVariant::GetList(
						array("SORT" => "ASC"),
						array("ORDER_PROPS_ID" => IntVal($arProperties["ID"])),
						false,
						false,
						array("*")
					);
					while ($arVariants = $dbVariants->Fetch())
					{
						$selected = "";
						if ($arVariants["VALUE"] == $fieldValue)
							$selected .= " selected";
					?>
						<option <?echo $selected;?> value="<?echo htmlspecialcharsbx($arVariants["VALUE"]);?>"><?echo htmlspecialcharsbx($arVariants["NAME"]);?></option>
					<?
					}
					?>
					</select>
				</td>
			</tr>
			<?
			$tabControl->EndCustomField("CODE_".IntVal($arProperties["ID"]));
		}

		elseif ($arProperties["TYPE"] == "MULTISELECT")
		{
			$tabControl->BeginCustomField("CODE_".IntVal($arProperties["ID"]), $arProperties["NAME"], $shure);
			?>
			<tr<? ($shure) ? " class=\"adm-detail-required-field\"" : "" ?>>
				<td width="40%">
					<?echo htmlspecialcharsbx($arProperties["NAME"]);?>:
				</td>
				<td width="60%">
					<select multiple size="5" name="<?echo "CODE_".IntVal($arProperties["ID"]);?>[]">
					<?
					if (strlen($fieldValue) > 0)
					{
						$curVal = explode(",", $fieldValue);

						$arCurVal = array();
						for ($i = 0; $i < count($curVal); $i++)
							$arCurVal[$i] = trim($curVal[$i]);
					}

					$dbVariants = CSaleOrderPropsVariant::GetList(
						array("SORT" => "ASC"),
						array("ORDER_PROPS_ID" => IntVal($arProperties["ID"])),
						false,
						false,
						array("*")
					);
					while ($arVariants = $dbVariants->Fetch())
					{
						$selected = "";
						if (in_array($arVariants["VALUE"], $arCurVal))
							$selected .= " selected";
					?>
						<option <?echo $selected;?> value="<?echo htmlspecialcharsbx($arVariants["VALUE"]);?>"><?echo htmlspecialcharsbx($arVariants["NAME"]);?></option>
					<?
					}
					?>
					</select>
				</td>
			</tr>
			<?
			$tabControl->EndCustomField("CODE_".IntVal($arProperties["ID"]));
		}

		elseif ($arProperties["TYPE"] == "TEXTAREA")
			$tabControl->AddTextField("CODE_".IntVal($arProperties["ID"]),$arProperties["NAME"].":", $fieldValue, array("cols" => "30", "rows" => "5"), $shure);

		elseif ($arProperties["TYPE"] == "RADIO")
		{

			$tabControl->BeginCustomField("CODE_".IntVal($arProperties["ID"]), $arProperties["NAME"], $shure);
			?>
			<tr<? ($shure) ? " class=\"adm-detail-required-field\"" : "" ?>>
				<td width="40%">
					<?echo htmlspecialcharsEx($arProperties["NAME"]);?>:
				</td>
				<td width="60%">
			<?
			$dbVariants = CSaleOrderPropsVariant::GetList(
					array("SORT" => "ASC"),
					array("ORDER_PROPS_ID" => IntVal($arProperties["ID"])),
					false,
					false,
					array("*")
			);
			while ($arVariants = $dbVariants->Fetch())
			{
				$selected = "";
				if ($arVariants["VALUE"] == $fieldValue)
					$selected .= " checked";
			?>
				<input <?echo $selected?> id="radio_<?echo $arVariants["ID"];?>" type="radio" name="CODE_<?echo IntVal($arProperties["ID"]);?>" value="<?echo htmlspecialcharsex($arVariants["VALUE"]);?>" />
				<label for="radio_<?echo $arVariants["ID"];?>"><?echo htmlspecialcharsEx($arVariants["NAME"])?></label><br />
			<?
			}
			?>
				</td>
			</tr>
			<?
			$tabControl->EndCustomField("CODE_".IntVal($arProperties["ID"]));
		}

		elseif ($arProperties["TYPE"] == "LOCATION")
		{
			$tabControl->BeginCustomField("CODE_".IntVal($arProperties["ID"]), $arProperties["NAME"], $shure);
		?>
			<tr<? ($shure) ? " class=\"adm-detail-required-field\"" : "" ?>>
				<td width="40%">
					<?echo htmlspecialcharsEx($arProperties["NAME"]);?>:
				</td>
				<td width="60%">
		<?
			$GLOBALS["APPLICATION"]->IncludeComponent(
							'bitrix:sale.ajax.locations',
							'',
							array(
								"SITE_ID" => $LID,
								"AJAX_CALL" => "N",
								"COUNTRY_INPUT_NAME" => "COUNTRY_".$arProperties["ID"],
								"REGION_INPUT_NAME" => "REGION_".$arProperties["ID"],
								"CITY_INPUT_NAME" => "LOCATION_".$arProperties["ID"],
								"CITY_OUT_LOCATION" => "Y",
								"ALLOW_EMPTY_CITY" => "Y",
								"LOCATION_VALUE" => $fieldValue,
								"COUNTRY" => "",
								"ONCITYCHANGE" => "",
								"PUBLIC" => "N",
							),
							null,
							array('HIDE_ICONS' => 'Y')
				);


		?>
				</td>
			</tr>
		<?
			$tabControl->EndCustomField("CODE_".IntVal($arProperties["ID"]));

		}

	}

	$tabControl->Buttons(array("back_url"=>"sale_order.php?lang=".LANGUAGE_ID));

	$tabControl->Show();
}

require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");
?>