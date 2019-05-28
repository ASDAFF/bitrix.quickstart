<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

global $APPLICATION;

IncludeModuleLangFile(__FILE__);

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
	$APPLICATION->AuthForm(GetMessage("BUYER_PE_ACCESS_DENIED"));

if(!CBXFeatures::IsFeatureEnabled('SaleAccounts'))
{
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
	ShowError(GetMessage("SALE_FEATURE_NOT_ALLOW"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

ClearVars();
$ID = (isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0);

$USER_ID = 0;
$PERSON_TYPE = 0;
$profileName = '';
$arErrors = array();
if($arProfile = CSaleOrderUserProps::GetByID($ID))
{
	$USER_ID = intval($arProfile["USER_ID"]);
	$PERSON_TYPE = intval($arProfile["PERSON_TYPE_ID"]);
	$profileName = $arProfile["NAME"];
}
else
	$arErrors[] = GetMessage("BUYER_PE_NO_PROFILE");

/*****************************************************************************/
/**************************** SAVE PROFILE ***********************************/
/*****************************************************************************/
if ($_SERVER['REQUEST_METHOD'] == "POST" && $saleModulePermissions >= "U" && check_bitrix_sessid() && !empty($arProfile))
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
		$arOrderProps["ID"] = intval($arOrderProps["ID"]);
		$curVal = trim($_REQUEST["CODE_".$arOrderProps["ID"]]);

		if ($arOrderProps["TYPE"]=="LOCATION")
		{
			$curVal = trim($_REQUEST["LOCATION_".$arOrderProps["ID"]]);
		}

		if ($arOrderProps["TYPE"] == "MULTISELECT")
		{
			$curVal = "";
			if (is_array($_REQUEST["CODE_".$arOrderProps["ID"]]))
			{
				foreach ($_REQUEST["CODE_".$arOrderProps["ID"]] as $key => $val)
				{
					$curVal .= trim($val);
					if ($key < (count($_REQUEST["CODE_".$arOrderProps["ID"]]) - 1))
						$curVal .= ",";
				}
			}
		}

		if (
			($arOrderProps["IS_LOCATION"]=="Y" || $arOrderProps["IS_LOCATION4TAX"]=="Y")
			&& !strlen($curVal)
			||
			($arOrderProps["IS_ZIP"] == "Y" && strlen($curVal) <= 0)
			||
			($arOrderProps["IS_PROFILE_NAME"]=="Y" || $arOrderProps["IS_PAYER"]=="Y")
			&& strlen($curVal) <= 0
			||
			$arOrderProps["REQUIED"]=="Y"
			&& $arOrderProps["TYPE"]=="LOCATION"
			&& !strlen($curVal)
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

	if (isset($_REQUEST["save"]) && strlen($_REQUEST["save"]) > 0 && empty($arErrors))
		LocalRedirect("/bitrix/admin/sale_buyers_profile.php?lang=".LANGUAGE_ID."&USER_ID=".$USER_ID);
	elseif (isset($_REQUEST["apply"]) && strlen($_REQUEST["apply"]) > 0 && empty($arErrors))
		LocalRedirect("/bitrix/admin/sale_buyers_profile_edit.php?id=".$ID."&lang=".LANGUAGE_ID);
}


/*****************************************************************************/
/***************************** FORM EDIT *************************************/
/*****************************************************************************/

if($USER_ID > 0)
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
$tabControl = new CAdminForm("buyers_profile_edit", $aTabs, false);
$tabControl->SetShowSettings(false);

$APPLICATION->SetTitle(str_replace("#NAME#", $profileName, GetMessage("BUYER_PE_TITLE")));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$link = urlencode($APPLICATION->GetCurPage())."?mode=settings";
$aMenu = array();
$aMenu = array(
	array(
		"TEXT" => GetMessage("BUYER_PE_LIST_PROFILE"),
		"LINK" => "/bitrix/admin/sale_buyers_profile.php?USER_ID=".$USER_ID."&lang=".LANGUAGE_ID
	)
);

$context = new CAdminContextMenu($aMenu);
$context->Show();

if (!empty($arErrors))
	CAdminMessage::ShowMessage(implode("<br>", $arErrors));

$tabControl->BeginEpilogContent();
echo bitrix_sessid_post();?>
<input type="hidden" name="id" value="<?echo $ID?>">
<?
$tabControl->EndEpilogContent();

$urlForm = "";
if ($ID > 0)
	$urlForm = "&id=".$ID;

$tabControl->Begin(array(
		"FORM_ACTION" => $APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID.$urlForm
));

//TAB EDIT PROFILE
$tabControl->BeginNextFormTab();

if(!empty($arProfile) && !empty($arUser))
{
	$dbPersonType = CSalePersonType::GetList(array(), Array("ACTIVE" => "Y", "ID" => $PERSON_TYPE));
	$arPersonType = $dbPersonType->GetNext();
	$LID = $arPersonType["LID"];

	$arFilterProps = array("PERSON_TYPE_ID" => $PERSON_TYPE, "ACTIVE" => "Y", "USER_PROPS" => "Y", "UTIL" => "N");

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
		$arProperties["ID"] = intval($arProperties["ID"]);
		$curVal = $userProfile[$ID]["VALUES"][$arProperties["ID"]];
		$fieldValue = (($curVal!="") ? $curVal : $arProperties["DEFAULT_VALUE"]);

		if (intval($arProperties["PROPS_GROUP_ID"]) != $propertyGroupID)
			$tabControl->AddSection("SECTION_".$arProperties["PROPS_GROUP_ID"], $arProperties["GROUP_NAME"]);

		$shure = false;
		if ($arProperties["REQUIED"] == "Y" || $arProperties["IS_PROFILE_NAME"] == "Y" || $arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y" || $arProperties["IS_PAYER"] == "Y" || $arProperties["IS_ZIP"] == "Y")
			$shure = true;

		/*fields*/
		if ($arProperties["TYPE"] == "TEXT")
		{
			$tabControl->AddEditField("CODE_".$arProperties["ID"], $arProperties["NAME"].":", $shure, array("size"=>30, "maxlength"=>255), $fieldValue);
		}
		elseif ($arProperties["TYPE"] == "CHECKBOX")
		{
			$checked = ($fieldValue == "Y") ? true : false;

			$tabControl->AddCheckBoxField("CODE_".$arProperties["ID"], $arProperties["NAME"].":", $shure, "Y", $checked);
		}
		elseif ($arProperties["TYPE"] == "SELECT")
		{
			$tabControl->BeginCustomField("CODE_".$arProperties["ID"], $arProperties["NAME"], $shure);
			?>
			<tr<? ($shure) ? " class=\"adm-detail-required-field\"" : "" ?>>
				<td width="40%">
					<?echo htmlspecialcharsbx($arProperties["NAME"]);?>:
				</td>
				<td width="60%">
					<select name="<?echo "CODE_".$arProperties["ID"];?>">
					<?
					$dbVariants = CSaleOrderPropsVariant::GetList(
						array("SORT" => "ASC"),
						array("ORDER_PROPS_ID" => $arProperties["ID"]),
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
			$tabControl->EndCustomField("CODE_".$arProperties["ID"]);
		}
		elseif ($arProperties["TYPE"] == "MULTISELECT")
		{
			$tabControl->BeginCustomField("CODE_".$arProperties["ID"], $arProperties["NAME"], $shure);
			?>
			<tr<? ($shure) ? " class=\"adm-detail-required-field\"" : "" ?>>
				<td width="40%">
					<?echo htmlspecialcharsbx($arProperties["NAME"]);?>:
				</td>
				<td width="60%">
					<select multiple size="5" name="<?echo "CODE_".$arProperties["ID"];?>[]">
					<?
					if (strlen($fieldValue) > 0)
					{
						$curVal = explode(",", $fieldValue);

						$arCurVal = array();
						$curValCount = count($curVal);
						for ($i = 0; $i < $curValCount; $i++)
							$arCurVal[$i] = trim($curVal[$i]);
					}

					$dbVariants = CSaleOrderPropsVariant::GetList(
						array("SORT" => "ASC"),
						array("ORDER_PROPS_ID" => intval($arProperties["ID"])),
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
			$tabControl->EndCustomField("CODE_".$arProperties["ID"]);
		}

		elseif ($arProperties["TYPE"] == "TEXTAREA")
			$tabControl->AddTextField("CODE_".$arProperties["ID"],$arProperties["NAME"].":", $fieldValue, array("cols" => "30", "rows" => "5"), $shure);

		elseif ($arProperties["TYPE"] == "RADIO")
		{

			$tabControl->BeginCustomField("CODE_".$arProperties["ID"], $arProperties["NAME"], $shure);
			?>
			<tr<? ($shure) ? " class=\"adm-detail-required-field\"" : "" ?>>
				<td width="40%">
					<?echo htmlspecialcharsEx($arProperties["NAME"]);?>:
				</td>
				<td width="60%">
			<?
			$dbVariants = CSaleOrderPropsVariant::GetList(
					array("SORT" => "ASC"),
					array("ORDER_PROPS_ID" => $arProperties["ID"]),
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
				<input <?echo $selected?> id="radio_<?echo $arVariants["ID"];?>" type="radio" name="CODE_<?echo $arProperties["ID"];?>" value="<?echo htmlspecialcharsex($arVariants["VALUE"]);?>" />
				<label for="radio_<?echo $arVariants["ID"];?>"><?echo htmlspecialcharsEx($arVariants["NAME"])?></label><br />
			<?
			}
			?>
				</td>
			</tr>
			<?
			$tabControl->EndCustomField("CODE_".$arProperties["ID"]);
		}
		elseif ($arProperties["TYPE"] == "LOCATION")
		{
			$tabControl->BeginCustomField("CODE_".$arProperties["ID"], $arProperties["NAME"], $shure);
		?>
			<tr<? ($shure) ? " class=\"adm-detail-required-field\"" : "" ?>>
				<td width="40%">
					<?echo htmlspecialcharsEx($arProperties["NAME"]);?>:
				</td>
				<td width="60%">
					<?
					CSaleLocation::proxySaleAjaxLocationsComponent(
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
						array(
							"ID" => "",
							"CODE" => $fieldValue,
							"PROVIDE_LINK_BY" => "id",
						)
					);
					?>
				</td>
			</tr>
		<?
			$tabControl->EndCustomField("CODE_".$arProperties["ID"]);
		}
	}

	$tabControl->Buttons(array("back_url"=>"/bitrix/admin/sale_buyers_profile.php?lang=".LANGUAGE_ID."&USER_ID=".$USER_ID));
	$tabControl->Show();
}

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');