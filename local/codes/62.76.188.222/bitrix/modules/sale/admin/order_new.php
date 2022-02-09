<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

$crmMode = (defined("BX_PUBLIC_MODE") && BX_PUBLIC_MODE && isset($_REQUEST["CRM_MANAGER_USER_ID"]));

if ($crmMode)
{
	CUtil::DecodeUriComponent($_GET);
	CUtil::DecodeUriComponent($_POST);

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/bitrix/themes/.default/sale.css\" />";
}
//double function from sale.ajax.location/process.js
?>
<script>
function getLocation(country_id, region_id, city_id, arParams, site_id)
{
	BX.showWait();

	property_id = arParams.CITY_INPUT_NAME;

	function getLocationResult(res)
	{
		BX.closeWait();

		var obContainer = document.getElementById('LOCATION_' + property_id);
		if (obContainer)
		{
			obContainer.innerHTML = res;
		}
	}

	arParams.COUNTRY = parseInt(country_id);
	arParams.REGION = parseInt(region_id);
	arParams.SITE_ID = site_id;

	var url = '/bitrix/components/bitrix/sale.ajax.locations/templates/.default/ajax.php';
	BX.ajax.post(url, arParams, getLocationResult)
}
</script>
<?

IncludeModuleLangFile(__FILE__);
ClearVars();

$ID = IntVal($ID);
$COUNT_RECOM_BASKET_PROD = 2;

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");

$arStatusList = False;
$arFilter = array("LID" => LANG, "ID" => "N");
$arGroupByTmpSt = false;
if ($saleModulePermissions < "W")
{
	$arFilter["GROUP_ID"] = $GLOBALS["USER"]->GetUserGroupArray();
	$arFilter["PERM_UPDATE"] = "Y";
	$arGroupByTmpSt = array("ID", "NAME", "MAX" => "PERM_UPDATE");
}
$dbStatusList = CSaleStatus::GetList(
		array(),
		$arFilter,
		$arGroupByTmpSt,
		false,
		array("ID", "NAME")
		);
$arStatusList = $dbStatusList->Fetch();

if ($saleModulePermissions == "D" OR ($saleModulePermissions < "W" AND $arStatusList["PERM_UPDATE"] != "Y"))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$errorMessage = "";

/*****************************************************************************/
/********************* ORDER FUNCTIONS ***************************************/
/*****************************************************************************/

if (!empty($_REQUEST["dontsave"]))
{
	CSaleOrder::UnLock($ID);

	LocalRedirect("sale_order.php?lang=".LANG.GetFilterParams("filter_", false));
}

/*
 * clean
 */
function CRMModeOutput($text)
{
	while(@ob_end_clean());
	echo $text;
	die();
}

/*
 * user name
 */
function fGetUserName($USER_ID)
{
	global $lang;
	$user = GetMessage('NEWO_BUYER_NAME_NULL');

	if (intval($USER_ID) > 0)
	{
		$rsUser = CUser::GetByID($USER_ID);
		$arUser = $rsUser->Fetch();

		if (count($arUser) > 1)
		{
			$user = "<a href='javascript:void(0);' onClick=\"window.open('/bitrix/admin/user_search.php?lang=".$lang."&FN=form_order_buyers_form&FC=user_id', '', 'scrollbars=yes,resizable=yes,width=840,height=500,top='+Math.floor((screen.height - 840)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));\">";
			$user .= "(".htmlspecialcharsbx($arUser["LOGIN"]).")";

			if ($arUser["NAME"] != "")
				$user .= " ".htmlspecialcharsbx($arUser["NAME"]);
			if ($arUser["LAST_NAME"] != "")
				$user .= " ".htmlspecialcharsbx($arUser["LAST_NAME"]);

			$user .= "<span class='pencil'>&nbsp;</span></a>";
		}
	}

	return $user;
}

/*
 * get count name, mail, phones in profiles
 */
function fGetCountProfileProps($PERSON_TYPE_ID)
{
	$arResult = array();
	$dbProperties = CSaleOrderProps::GetList(
		array(),
		array("PERSON_TYPE_ID" => $PERSON_TYPE_ID, "ACTIVE" => "Y"),
		array("IS_PHONE", "COUNT" => "ID"),
		false,
		array("IS_PHONE")
	);
	while ($arProperties = $dbProperties->Fetch())
	{
		if ($arProperties["IS_PHONE"] == "Y")
			$arResult["IS_PHONE"] = $arProperties["CNT"];
	}

	$dbProperties = CSaleOrderProps::GetList(
		array(),
		array("PERSON_TYPE_ID" => $PERSON_TYPE_ID, "ACTIVE" => "Y"),
		array("IS_PAYER", "COUNT" => "ID"),
		false,
		array("IS_PAYER")
	);
	while ($arProperties = $dbProperties->Fetch())
	{
		if ($arProperties["IS_PAYER"] == "Y")
			$arResult["IS_PAYER"] = $arProperties["CNT"];
	}

	$dbProperties = CSaleOrderProps::GetList(
		array(),
		array("PERSON_TYPE_ID" => $PERSON_TYPE_ID, "ACTIVE" => "Y"),
		array("IS_EMAIL", "COUNT" => "ID"),
		false,
		array("IS_EMAIL")
	);
	while ($arProperties = $dbProperties->Fetch())
	{
		if ($arProperties["IS_EMAIL"] == "Y")
			$arResult["IS_EMAIL"] = $arProperties["CNT"];
	}

	return $arResult;
}

/*
 * user property (parameters order)
 */
function fGetBuyerType($PERSON_TYPE_ID, $LID, $USER_ID = '', $ORDER_ID = 0, $formVarsSubmit = false)
{
	global $locationZipID, $locationID, $DELIVERY_LOCATION, $DELIVERY_LOCATION_ZIP;
	$resultHtml = "<script>locationZipID = 0;locationID = 0;</script><table width=\"100%\" id=\"order_type_props\" class=\"edit-table\">";

	//select person type
	$arPersonTypeList = array();
	$personTypeSelect = "<select name='buyer_type_id' id='buyer_type_id' OnChange='fBuyerChangeType(this);' >";
	$dbPersonType = CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("ACTIVE" => "Y"));
	while ($arPersonType = $dbPersonType->GetNext())
	{
		if (!in_array($LID, $arPersonType["LIDS"]))
			continue;

		if (!isset($PERSON_TYPE_ID) OR $PERSON_TYPE_ID == "")
			$PERSON_TYPE_ID = $arPersonType["ID"];

		$class = "";
		if (IntVal($arPersonType["ID"]) == IntVal($PERSON_TYPE_ID))
			$class = " selected";

		$personTypeSelect .= "<option value=\"".$arPersonType["ID"]."\" ".$class.">[".$arPersonType["ID"]."] ".$arPersonType["NAME"]."</option>";
	}
	$personTypeSelect .= "</select>";

	$userComment = "";
	$userDisplay = "none";
	if (IntVal($ORDER_ID) > 0)
	{
		$dbOrder = CSaleOrder::GetList(
			array(),
			array("ID" => $ORDER_ID, "ACTIVE" => "Y"),
			false,
			false,
			array()
		);
		$arOrder = $dbOrder->Fetch();
		$userComment = $arOrder["USER_DESCRIPTION"];
		$userDisplay = "table-row";
	}

	if ($formVarsSubmit && $_REQUEST["btnTypeBuyer"] == "btnBuyerNew")
		$userDisplay = "none";
	elseif ($formVarsSubmit && $_REQUEST["btnTypeBuyer"] == "btnBuyerExist")
		$userDisplay = "table-row";

	$resultHtml .= "<tr id=\"btnBuyerExistField\" style=\"display:".$userDisplay."\">
			<td class=\"adm-detail-content-cell-l\" width=\"40%\">".GetMessage("NEWO_BUYER").":</td>
			<td class=\"adm-detail-content-cell-r\" width=\"60%\"><div id=\"user_name\">".fGetUserName($USER_ID)."</div></td></tr>";

	$resultHtml .= "<tr class=\"adm-detail-required-field\">
		<td class=\"adm-detail-content-cell-l\" width=\"40%\">".GetMessage("SOE_PERSON_TYPE").":</td>
		<td class=\"adm-detail-content-cell-r\" width=\"60%\">".$personTypeSelect."</td>
	</tr>";

	$resultHtml .= "<tr id=\"buyer_profile_display\" style=\"display:none\" class=\"adm-detail-required-field\">
		<td class=\"adm-detail-content-cell-l\">".GetMessage("NEWO_BUYER_PROFILE").":</td>
		<td class=\"adm-detail-content-cell-r\"><div id=\"buyer_profile_select\"></div></td>
	</tr>";

	if ($ORDER_ID <= 0)
	{
		$arCountProps = fGetCountProfileProps($PERSON_TYPE_ID);
		$resultHtml .= "<tr id=\"btnBuyerNewField\">";
		if (count($arCountProps) < 3)
		{
			$resultHtml .= "<td colspan=2>
					<table width=\"100%\" class=\"edit-table\" >";
					if (IntVal($arCountProps["IS_EMAIL"]) <= 0)
						$resultHtml .= "<tr class=\"adm-detail-required-field\">
							<td class=\"adm-detail-content-cell-l\" width=\"40%\">".GetMessage("NEWO_BUYER_REG_MAIL")."</td>
							<td class=\"adm-detail-content-cell-r\"><input type=\"text\" name=\"NEW_BUYER_EMAIL\" size=\"30\" value=\"".htmlspecialcharsbx(trim($_REQUEST["NEW_BUYER_EMAIL"]))."\" tabindex=\"1\" /></td>
						</tr>";
					if (IntVal($arCountProps["IS_PAYER"]) <= 0)
						$resultHtml .= "<tr class=\"adm-detail-required-field\">
							<td class=\"adm-detail-content-cell-l\">".GetMessage("NEWO_BUYER_REG_LASTNAME")."</td>
							<td class=\"adm-detail-content-cell-r\"><input type=\"text\" name=\"NEW_BUYER_LAST_NAME\" size=\"30\" value=\"".htmlspecialcharsbx(trim($_REQUEST["NEW_BUYER_LAST_NAME"]))."\" tabindex=\"3\" /></td>
						</tr>
						<tr class=\"adm-detail-required-field\">
							<td class=\"adm-detail-content-cell-l\">".GetMessage("NEWO_BUYER_REG_NAME")."</td>
							<td class=\"adm-detail-content-cell-r\"><input type=\"text\" name=\"NEW_BUYER_NAME\" size=\"30\" value=\"".htmlspecialcharsbx(trim($_REQUEST["NEW_BUYER_NAME"]))."\" tabindex=\"2\" /></td>
						</tr>";
					$resultHtml .= "</table>
				</td>";
		}
		$resultHtml .= "</tr>";
	}

	$arPropValues = array();
	if ($formVarsSubmit)
	{
		$locationIndexForm = "";
		foreach ($_POST as $key => $value)
		{
			if (substr($key, 0, strlen("CITY_ORDER_PROP_")) == "CITY_ORDER_PROP_")
			{
				$arPropValues[IntVal(substr($key, strlen("CITY_ORDER_PROP_")))] = htmlspecialcharsbx($value);
				$locationIndexForm = IntVal(substr($key, strlen("CITY_ORDER_PROP_")));
			}
			if (substr($key, 0, strlen("ORDER_PROP_")) == "ORDER_PROP_")
			{
				if ($locationIndexForm != IntVal(substr($key, strlen("ORDER_PROP_"))))
					$arPropValues[IntVal(substr($key, strlen("ORDER_PROP_")))] = htmlspecialcharsbx($value);
			}
		}
		$userComment = $_POST["USER_DESCRIPTION"];
	}
	elseif ($ORDER_ID == "" AND $USER_ID != "")
	{
		//profile
		$userProfile = array();
		$userProfile = CSaleOrderUserProps::DoLoadProfiles($USER_ID, $PERSON_TYPE_ID);
		$arPropValues = $userProfile[$PERSON_TYPE_ID]["VALUES"];
	}
	elseif ($ORDER_ID != "")
	{
		$dbPropValuesList = CSaleOrderPropsValue::GetList(
			array(),
			array("ORDER_ID" => $ORDER_ID, "ACTIVE" => "Y"),
			false,
			false,
			array("ID", "ORDER_PROPS_ID", "NAME", "VALUE", "CODE")
		);
		while ($arPropValuesList = $dbPropValuesList->Fetch())
		{
			$arPropValues[IntVal($arPropValuesList["ORDER_PROPS_ID"])] = htmlspecialcharsbx($arPropValuesList["VALUE"]);
		}
	}

	//select field (town) for disable
	$arDisableFieldForLocation = array();
	$dbProperties = CSaleOrderProps::GetList(
		array(),
		array("PERSON_TYPE_ID" => $PERSON_TYPE_ID, "ACTIVE" => "Y", ">INPUT_FIELD_LOCATION" => 0),
		false,
		false,
		array("INPUT_FIELD_LOCATION")
	);
	while ($arProperties = $dbProperties->Fetch())
		$arDisableFieldForLocation[] = $arProperties["INPUT_FIELD_LOCATION"];

	$dbProperties = CSaleOrderProps::GetList(
		array("GROUP_SORT" => "ASC", "PROPS_GROUP_ID" => "ASC", "SORT" => "ASC", "NAME" => "ASC"),
		array("PERSON_TYPE_ID" => $PERSON_TYPE_ID, "ACTIVE" => "Y"),
		false,
		false,
		array("*")
	);
	$propertyGroupID = -1;

	while ($arProperties = $dbProperties->Fetch())
	{
		if (IntVal($arProperties["PROPS_GROUP_ID"]) != $propertyGroupID)
		{
			$resultHtml .= "<tr><td colspan=\"2\" style=\"text-align:center;font-weight:bold;font-size:14px;color:rgb(75, 98, 103);\" >".htmlspecialcharsEx($arProperties["GROUP_NAME"])."\n</td>\n</tr>";
			$propertyGroupID = IntVal($arProperties["PROPS_GROUP_ID"]);
		}

		if (IntVal($arProperties["PROPS_GROUP_ID"]) != $propertyGroupID)
			$propertyGroupID = IntVal($arProperties["PROPS_GROUP_ID"]);

		$adit = "";
		$requiredField = "";
		if ($arProperties["REQUIED"] == "Y" || $arProperties["IS_PROFILE_NAME"] == "Y" || $arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y" || $arProperties["IS_PAYER"] == "Y" || $arProperties["IS_ZIP"] == "Y")
		{
			$adit = " class=\"adm-detail-required-field\"";
			$requiredField = " class=\"adm-detail-content-cell-l\"";
		}
		//delete town from location
		if (in_array($arProperties["ID"], $arDisableFieldForLocation))
			$resultHtml .= "<tr style=\"display:none;\" id=\"town_location_".$arProperties["ID"]."\"".$adit.">\n";
		else
			$resultHtml .= "<tr".$adit.">\n";

		if(($arProperties["TYPE"] == "MULTISELECT" || $arProperties["TYPE"] == "TEXTAREA") || ($ORDER_ID <= 0 && $arProperties["IS_PROFILE_NAME"] == "Y") )
			$resultHtml .= "<td valign=\"top\" class=\"adm-detail-content-cell-l\" width=\"40%\">\n";
		else
			$resultHtml .= "<td align=\"right\" width=\"40%\" ".$requiredField.">\n";

		$resultHtml .= htmlspecialcharsEx($arProperties["NAME"]).":</td>";

		$curVal = $arPropValues[IntVal($arProperties["ID"])];

		if($arProperties["IS_EMAIL"] == "Y" || $arProperties["IS_PAYER"] == "Y")
		{
			if(strlen($arProperties["DEFAULT_VALUE"]) <= 0 && IntVal($USER_ID) > 0)
			{
				$rsUser = CUser::GetByID($USER_ID);
				if ($arUser = $rsUser->Fetch())
				{
					if($arProperties["IS_EMAIL"] == "Y")
						$arProperties["DEFAULT_VALUE"] = $arUser["EMAIL"];
					else
					{
						if (strlen($arUser["LAST_NAME"]) > 0)
							$arProperties["DEFAULT_VALUE"] .= $arUser["LAST_NAME"];
						if (strlen($arUser["NAME"]) > 0)
							$arProperties["DEFAULT_VALUE"] .= " ".$arUser["NAME"];
						if (strlen($arUser["SECOND_NAME"]) > 0 AND strlen($arUser["NAME"]) > 0)
							$arProperties["DEFAULT_VALUE"] .= " ".$arUser["SECOND_NAME"];
					}
				}
			}
		}

		$resultHtml .= "<td class=\"adm-detail-content-cell-r\" width=\"60%\">";

		if ($arProperties["TYPE"] == "CHECKBOX")
		{
			$resultHtml .= '<input type="checkbox" class="inputcheckbox" ';
			$resultHtml .= 'name="ORDER_PROP_'.$arProperties["ID"].'" value="Y"';
			$resultHtml .= 'id="ORDER_PROP_'.$arProperties["ID"].'" ';
			if ($curVal=="Y" || !isset($curVal) && $arProperties["DEFAULT_VALUE"]=="Y")
				$resultHtml .= " checked";
			$resultHtml .= '>';
		}
		elseif ($arProperties["TYPE"] == "TEXT")
		{
			$change = "";
			if ($arProperties["IS_ZIP"] == "Y")
			{
				$DELIVERY_LOCATION_ZIP = $curVal;
				$resultHtml .= '<script> locationZipID = \''.$arProperties["ID"].'\';</script>';
				$locationZipID = ((isset($curVal)) ? htmlspecialcharsEx($curVal) : htmlspecialcharsex($arProperties["DEFAULT_VALUE"]));
			}

			if ($arProperties["IS_PAYER"] == "Y" && IntVal($USER_ID) <= 0)
			{
				$resultHtml .= '<div id="BREAK_NAME"';
				if ($ORDER_ID > 0 || ($formVarsSubmit && $_REQUEST["btnTypeBuyer"] != "btnBuyerNew"))
					$resultHtml .= ' style="display:none"';
				$resultHtml .= '>';

				$BREAK_LAST_NAME_TMP = GetMessage('NEWO_BREAK_LAST_NAME');
				if (isset($_REQUEST["BREAK_LAST_NAME"]) && strlen($_REQUEST["BREAK_LAST_NAME"]) > 0)
					$BREAK_LAST_NAME_TMP = htmlspecialcharsbx(trim($_REQUEST["BREAK_LAST_NAME"]));

				$NEWO_BREAK_NAME_TMP = GetMessage('NEWO_BREAK_NAME');
				if (isset($_REQUEST["BREAK_NAME"]) && strlen($_REQUEST["BREAK_NAME"]) > 0)
					$NEWO_BREAK_NAME_TMP = htmlspecialcharsbx(trim($_REQUEST["BREAK_NAME"]));

				$BREAK_SECOND_NAME_TMP = GetMessage('NEWO_BREAK_SECOND_NAME');
				if (isset($_REQUEST["BREAK_SECOND_NAME"]) && strlen($_REQUEST["BREAK_SECOND_NAME"]) > 0)
					$BREAK_SECOND_NAME_TMP = htmlspecialcharsbx(trim($_REQUEST["BREAK_SECOND_NAME"]));

				$resultHtml .= "<div class=\"fio newo_break_active\"><input onblur=\"if (this.value==''){this.value='".GetMessage('NEWO_BREAK_LAST_NAME')."';BX.addClass(this.parentNode,'newo_break_active');}\" onfocus=\"if (this.value=='".GetMessage('NEWO_BREAK_LAST_NAME')."') {this.value='';BX.removeClass(this.parentNode,'newo_break_active');}\" type=\"text\" name=\"BREAK_LAST_NAME\" id=\"BREAK_LAST_NAME\" size=\"30\" value=\"".$BREAK_LAST_NAME_TMP."\" /></div>";
				$resultHtml .= "<div class=\"fio newo_break_active\"><input onblur=\"if (this.value==''){this.value='".GetMessage('NEWO_BREAK_NAME')."';BX.addClass(this.parentNode,'newo_break_active');}\" onfocus=\"if (this.value=='".GetMessage('NEWO_BREAK_NAME')."') {this.value='';BX.removeClass(this.parentNode,'newo_break_active');}\" type=\"text\" name=\"BREAK_NAME\" id=\"BREAK_NAME_BUYER\" size=\"30\" value=\"".$NEWO_BREAK_NAME_TMP."\" /></div>";
				$resultHtml .= "<div class=\"fio newo_break_active\"><input onblur=\"if (this.value==''){this.value='".GetMessage('NEWO_BREAK_SECOND_NAME')."';BX.addClass(this.parentNode,'newo_break_active');}\" onfocus=\"if (this.value=='".GetMessage('NEWO_BREAK_SECOND_NAME')."') {this.value='';BX.removeClass(this.parentNode,'newo_break_active');}\" type=\"text\" name=\"BREAK_SECOND_NAME\" id=\"BREAK_SECOND_NAME\" size=\"30\" value=\"".$BREAK_SECOND_NAME_TMP."\" /></div>";
				$resultHtml .= '</div>';

				$resultHtml .= '<div id="NO_BREAK_NAME"';
				if ($ORDER_ID <= 0)
					$tmpNone = ' style="display:none"';
				if ($formVarsSubmit && $_REQUEST["btnTypeBuyer"] == "btnBuyerExist")
					$tmpNone = ' style="display:block"';
				$resultHtml .= $tmpNone.'>';
			}

			$resultHtml .= '<input type="text" maxlength="250" ';
			$resultHtml .= 'size="30" ';
			$resultHtml .= 'value="'.((isset($curVal)) ? $curVal : $arProperties["DEFAULT_VALUE"]).'" ';
			$resultHtml .= 'name="ORDER_PROP_'.$arProperties["ID"].'" ';
			$resultHtml .= 'id="ORDER_PROP_'.$arProperties["ID"].'" '.$change.'>';

			if ($arProperties["IS_PAYER"] == "Y" && IntVal($USER_ID) <= 0)
				$resultHtml .= '</div>';
		}
		elseif ($arProperties["TYPE"] == "SELECT")
		{
			$resultHtml .= '<select name="ORDER_PROP_'.$arProperties["ID"].'" ';
			$resultHtml .= 'id="ORDER_PROP_'.$arProperties["ID"].'" ';
			$resultHtml .= 'size="5" ';
			$resultHtml .= 'class="typeselect">';
			$dbVariants = CSaleOrderPropsVariant::GetList(
				array("SORT" => "ASC"),
				array("ORDER_PROPS_ID" => $arProperties["ID"]),
				false,
				false,
				array("*")
			);
			while ($arVariants = $dbVariants->Fetch())
			{
				$resultHtml .= '<option value="'.htmlspecialcharsex($arVariants["VALUE"]).'"';
				if ($arVariants["VALUE"] == $curVal || !isset($curVal) && $arVariants["VALUE"] == $arProperties["DEFAULT_VALUE"])
					$resultHtml .= " selected";
				$resultHtml .= '>'.htmlspecialcharsEx($arVariants["NAME"]).'</option>';
			}
			$resultHtml .= '</select>';
		}
		elseif ($arProperties["TYPE"] == "MULTISELECT")
		{
			$resultHtml .= '<select multiple name="ORDER_PROP_'.$arProperties["ID"].'[]" ';
			$resultHtml .= 'id="ORDER_PROP_'.$arProperties["ID"].'" ';
			$resultHtml .= 'size="5" ';
			$resultHtml .= 'class="typeselect" type="multyselect">';

			if (!is_array($curVal))
			{
				if (strlen($curVal) > 0 OR $ORDER_ID != "")
					$curVal = explode(",", $curVal);
				else
					$curVal = explode(",", $arProperties["DEFAULT_VALUE"]);

				$arCurVal = array();
				for ($i = 0; $i < count($curVal); $i++)
					$arCurVal[$i] = Trim($curVal[$i]);
			}
			else
				$arCurVal = $curVal;

			$dbVariants = CSaleOrderPropsVariant::GetList(
				array("SORT" => "ASC"),
				array("ORDER_PROPS_ID" => $arProperties["ID"]),
				false,
				false,
				array("*")
			);
			while ($arVariants = $dbVariants->Fetch())
			{
				$resultHtml .= '<option value="'.htmlspecialcharsex($arVariants["VALUE"]).'"';
				if (in_array($arVariants["VALUE"], $arCurVal))
					$resultHtml .= " selected";
				$resultHtml .= '>'.htmlspecialcharsEx($arVariants["NAME"]).'</option>';
			}
			$resultHtml .= '</select>';
		}
		elseif ($arProperties["TYPE"] == "TEXTAREA")
		{
			$resultHtml .= '<textarea ';
			$resultHtml .= 'rows="4" ';
			$resultHtml .= 'cols="40" ';
			$resultHtml .= 'name="ORDER_PROP_'.$arProperties["ID"].'" ';
			$resultHtml .= 'id="ORDER_PROP_'.$arProperties["ID"].'" type="textarea">';
			$resultHtml .= ((isset($curVal)) ? $curVal : $arProperties["DEFAULT_VALUE"]);
			$resultHtml .= '</textarea>';
		}
		elseif ($arProperties["TYPE"] == "LOCATION")
		{
			$countryID = "";
			$cityID = "";
			$cityList = "";
			$DELIVERY_LOCATION = $arPropValues[IntVal($arProperties["ID"])];
			$locationID = $curVal;
			$tmpLocation = '';

			ob_start();
			$tmpLocation = $GLOBALS["APPLICATION"]->IncludeComponent(
						'bitrix:sale.ajax.locations',
						'',
						array(
							"SITE_ID" => $LID,
							"AJAX_CALL" => "N",
							"COUNTRY_INPUT_NAME" => "ORDER_PROP_".$arProperties["ID"],
							"REGION_INPUT_NAME" => "REGION_ORDER_PROP_".$arProperties["ID"],
							"CITY_INPUT_NAME" => "CITY_ORDER_PROP_".$arProperties["ID"],
							"CITY_OUT_LOCATION" => "Y",
							"ALLOW_EMPTY_CITY" => "Y",
							"LOCATION_VALUE" => $curVal,
							"COUNTRY" => "",
							"ONCITYCHANGE" => "fRecalProduct('', '', 'N');",
							"PUBLIC" => "N",
						),
						null,
						array('HIDE_ICONS' => 'Y')
			);
			$tmpLocation = ob_get_contents();
			ob_end_clean();

			$resultHtml .= '<script>var locationID = \''.$arProperties["ID"].'\';</script>';
			$resultHtml .= $tmpLocation;
		}
		elseif ($arProperties["TYPE"] == "RADIO")
		{
			$dbVariants = CSaleOrderPropsVariant::GetList(
				array("SORT" => "ASC"),
				array("ORDER_PROPS_ID" => $arProperties["ID"]),
				false,
				false,
				array("*")
			);
			$resultHtml .= '<div id="ORDER_PROP_'.$arProperties["ID"].'">';// type="radio"
			while ($arVariants = $dbVariants->Fetch())
			{
				$resultHtml .= '<input type="radio" class="inputradio" ';
				$resultHtml .= 'name="ORDER_PROP_'.$arProperties["ID"].'" ';
				$resultHtml .= 'value="'.htmlspecialcharsex($arVariants["VALUE"]).'"';
				if ($arVariants["VALUE"] == $curVal || !isset($curVal) && $arVariants["VALUE"] == $arProperties["DEFAULT_VALUE"])
					$resultHtml .= " checked";
				$resultHtml .= '>'.htmlspecialcharsEx($arVariants["NAME"]).'<br>';
			}
			$resultHtml .= '</div>';
		}

		if (strlen($arProperties["DESCRIPTION"]) > 0)
		{
			$resultHtml .= "<br><small>".htmlspecialcharsEx($arProperties["DESCRIPTION"])."</small>";
		}
		$resultHtml .= "\n</td>\n</tr>";

	}//end while

	$resultHtml .= "<tr>\n<td valign=\"top\" class=\"adm-detail-content-cell-l\">".GetMessage("SOE_BUYER_COMMENT").":
			</td>
			<td class=\"adm-detail-content-cell-r\">
				<textarea name=\"USER_DESCRIPTION\" rows=\"4\" cols=\"40\">".htmlspecialcharsbx($userComment)."</textarea>
			</td>
		</tr>";

	$resultHtml .= "</table>";
	return $resultHtml;
}

/*
 * paysystem
 */
function fBuyerDelivery($PERSON_TYPE_ID, $PAY_SYSTEM_ID)
{
	$resultHtml = "<table width=\"100%\">";
	$resultHtml .= "<tr class=\"adm-detail-required-field\">\n<td class=\"adm-detail-content-cell-l\" width=\"40%\">".GetMessage("SOE_PAY_SYSTEM").":</td><td class=\"adm-detail-content-cell-r\" width=\"60%\">";

	$arPaySystem = CSalePaySystem::DoLoadPaySystems($PERSON_TYPE_ID);

	$resultHtml .= "<select name=\"PAY_SYSTEM_ID\" id=\"PAY_SYSTEM_ID\">\n";
	$resultHtml .= "<option value=\"\">(".GetMessage("SOE_SELECT").")</option>";
	foreach ($arPaySystem as $key => $val)
	{
		$resultHtml .= "<option value=\"".$key."\"";
		if ($key == IntVal($PAY_SYSTEM_ID))
			$resultHtml .= " selected";
		$resultHtml .= ">".$val["NAME"]." [".$key."]</option>";
	}
	$resultHtml .= "</select>";
	$resultHtml .= "</td>\n</tr>";
	$resultHtml .= "</table>";

	return $resultHtml;
}

/*
 * user profile
 */
function fUserProfile($USER_ID, $BUYER_TYPE = '', $default = '')
{
	$userProfileSelect = "<select name=\"user_profile\" id=\"user_profile\" onChange=\"fChangeProfile(this);\">";
	$userProfileSelect .= "<option value=\"0\">".GetMessage("NEWO_BUYER_PROFILE_NEW")."</option>";
	$userProfile = CSaleOrderUserProps::DoLoadProfiles($USER_ID, $BUYER_TYPE);
	$i = "";
	foreach($userProfile as $key => $val)
	{
		if ($default == "" AND $i == "")
		{
			$userProfileSelect .= "<option selected value=\"".$key."\">".$val["NAME"]."</option>";
			$i = $key;
		}
		elseif ($default == $key)
			$userProfileSelect .= "<option selected value=\"".$key."\">".$val["NAME"]."</option>";
		else
			$userProfileSelect .= "<option value=\"".$key."\">".$val["NAME"]."</option>";
	}
	$userProfileSelect .= "</select>";

	return $userProfileSelect;
}

/*
 * user balance
 */
function fGetPayFromAccount($USER_ID, $CURRENCY)
{
	$arResult = array("PAY_MESSAGE" => GetMessage("NEWO_PAY_FROM_ACCOUNT_NO"));
	$dbUserAccount = CSaleUserAccount::GetList(
	array(),
	array(
		"USER_ID" => $USER_ID,
		"CURRENCY" => $CURRENCY,
		)
	);
	if ($arUserAccount = $dbUserAccount->GetNext())
	{
		if (DoubleVal($arUserAccount["CURRENT_BUDGET"]) > 0)
		{
			$arResult["PAY_BUDGET"] = SaleFormatCurrency($arUserAccount["CURRENT_BUDGET"], $CURRENCY);
			$arResult["PAY_MESSAGE"] = str_replace("#MONEY#", $arResult["PAY_BUDGET"], GetMessage("NEWO_PAY_FROM_ACCOUNT_YES"));
			$arResult["CURRENT_BUDGET"] = $arUserAccount["CURRENT_BUDGET"];
		}
	}

	return $arResult;
}

/*
 * delivery
 */
function fGetDelivery($location, $locationZip, $weight, $price, $currency, $siteId, $defaultDelivery)
{
	$arResult = array();
	$delivery = "<select name=\"DELIVERY_ID\" id=\"DELIVERY_ID\" OnChange=\"fChangeDelivery();\">";
	$delivery .= "<option value=\"\">".GetMessage('NEWO_DELIVERY_NO')."</option>";

	$arDelivery = CSaleDelivery::DoLoadDelivery($location, $locationZip, $weight, $price, $currency, $siteId);
	$price = 0;
	$description = "";
	$error = "";
	if (count($arDelivery) > 0)
	{
		foreach($arDelivery as $val)
		{
			if (isset($val["PROFILES"]))
			{
				foreach($val["PROFILES"] as $k => $v)
				{
					$currency = $v["CURRENCY"];
					$selected = "";
					if ($v["ID"] == $defaultDelivery)
					{
						$selected = " selected=\"selected\"";

						if (floatval($v["DELIVERY_PRICE"]) <= 0)
						{
							$error = "<div class='error'>".GetMessage('NEWO_DELIVERY_ERR')."</div>";
							$v["DELIVERY_PRICE"] = 0;
							$val["DESCRIPTION"] = "";
						}
						$price = $v["DELIVERY_PRICE"];
						$description = $val["DESCRIPTION"];
					}

					$delivery .= "<option".$selected." value=\"".$v["ID"]."\">".$val["TITLE"]." (".$v["TITLE"].") [".$v["ID"]."]</option>";
				}
			}
			else
			{
				$currency = $val["CURRENCY"];
				$selected = "";
				if ($val["ID"] == $defaultDelivery)
				{
					$selected = " selected=\"selected\"";
					$price = $val["PRICE"];
					$description = $val["DESCRIPTION"];
				}

				$delivery .= "<option".$selected." value=\"".$val["ID"]."\">".$val["NAME"]." [".$val["ID"]."]</option>";
			}
		}
	}
	$delivery .= "</select>";

	$arResult["DELIVERY"] = $delivery;
	$arResult["DELIVERY_DEFAULT"] = $defaultDelivery;
	$arResult["DELIVERY_DEFAULT_PRICE"] = $price;
	$arResult["DELIVERY_DEFAULT_DESCRIPTION"] = $description;
	$arResult["DELIVERY_DEFAULT_ERR"] = $error;
	$arResult["CURRENCY"] = $currency;

	return $arResult;
}

/*
 * cupons
 */
function fGetCupon($CUPON)
{
	$arCupon = array();
	if (isset($CUPON) AND $CUPON != "")
	{
		$cupons = explode(",", $CUPON);
		foreach($cupons as $val)
		{
			if (strlen(trim($val)) > 0)
				$arCupon[] = trim($val);
		}
	}

	return $arCupon;
}

/*
 * get ID, ZIP location
 */
function fGetLocationID($PERSON_TYPE_ID)
{
	$dbProperties = CSaleOrderProps::GetList(
		array("SORT" => "ASC"),
		array("PERSON_TYPE_ID" => $PERSON_TYPE_ID),
		false,
		false,
		array("TYPE", "IS_ZIP", "ID", "SORT")
	);

	$arResult = array();
	while ($arProperties = $dbProperties->Fetch())
	{
		if ($arProperties["TYPE"] == "TEXT")
		{
			if ($arProperties["IS_ZIP"] == "Y")
			{
				$arResult["LOCATION_ZIP_ID"] = $arProperties["ID"];
			}
		}
		elseif ($arProperties["TYPE"] == "LOCATION")
		{
			$arResult["LOCATION_ID"] = $arProperties["ID"];
		}
	}//end while

	return $arResult;
}

/*
 * array product busket
 */
function fGetUserShoppingCart($arProduct, $LID, $currency)
{
	$arOrderProductPrice = array();
	$i = 0;

	foreach($arProduct as $key => $val)
	{
		$arSortNum[] = $val['PRICE_DEFAULT'];
		$arProduct[$key]["PRODUCT_ID"] = $key;
	}
	array_multisort($arSortNum, SORT_DESC, $arProduct);

	foreach($arProduct as $key => $val)
	{
		$val["QUANTITY"] = str_replace(",", ".", $val["QUANTITY"]);
		$val["PRICE"] = str_replace(",", ".", $val["PRICE"]);

		if (!isset($val["BUSKET_ID"]) OR $val["BUSKET_ID"] == "")
		{
			$arOrderProductPrice[$i] = $val;
			$arOrderProductPrice[$i]["LID"] = $LID;
			$arOrderProductPrice[$i]["PRODUCT_ID"] = IntVal($val["PRODUCT_ID"]);
			$arOrderProductPrice[$i]["CAN_BUY"] = "Y";
			if ($val["CALLBACK_FUNC"] == "Y")
			{
				$arOrderProductPrice[$i]["CALLBACK_FUNC"] = '';
				$arOrderProductPrice[$i]["DISCOUNT_PRICE"] = 0;
			}
		}
		else
		{
			$arOrderProductPrice[$i]["ID"] = trim($val["BUSKET_ID"]);
			$arOrderProductPrice[$i]["PRODUCT_ID"] = IntVal($val["PRODUCT_ID"]);
			$arOrderProductPrice[$i]["NAME"] = htmlspecialcharsback($val["NAME"]);
			$arOrderProductPrice[$i]["CAN_BUY"] = "Y";
			$arOrderProductPrice[$i]["PRICE"] = trim($val["PRICE"]);
			$arOrderProductPrice[$i]["NOTES"] = trim($val["NOTES"]);
			$arOrderProductPrice[$i]["CURRENCY"] = trim($val["CURRENCY"]);
			$arOrderProductPrice[$i]["QUANTITY"] = trim($val["QUANTITY"]);
			$arOrderProductPrice[$i]["WEIGHT"] = trim($val["WEIGHT"]);
			$arOrderProductPrice[$i]["VAT_RATE"] = trim($val["VAT_RATE"]);
			$arOrderProductPrice[$i]["DISCOUNT_PRICE"] = trim($val["DISCOUNT_PRICE"]);
			$arOrderProductPrice[$i]["CATALOG_XML_ID"] = trim($val["CATALOG_XML_ID"]);
			$arOrderProductPrice[$i]["PRODUCT_XML_ID"] = trim($val["PRODUCT_XML_ID"]);

			if ($val["CALLBACK_FUNC"] == "Y")
				$arOrderProductPrice[$i]["DISCOUNT_PRICE"] = 0;

			$arNewProps = array();
			if (is_array($val["PROPS"]))
			{
				foreach($val["PROPS"] as $k => $v)
				{
					if ($v["NAME"] != "" AND $v["VALUE"] != "")
						$arNewProps[$k] = $v;
				}
			}
			else
				$arNewProps = array("NAME" => "", "VALUE" => "", "CODE" => "", "SORT" => "");

			$arOrderProductPrice[$i]["PROPS"] = $arNewProps;
		}
		$i++;
	}//endforeach $arProduct

	return $arOrderProductPrice;
}

/*
 * get template recomendet & busket product
 */
function fGetFormatedProduct($USER_ID, $LID, $arData, $currency, $type = '')
{
	global $crmMode;
	$result = "";

	if (!is_array($arData["ITEMS"]) || count($arData["ITEMS"]) <= 0)
		return $result;

	$result = "<table width=\"100%\">";
	foreach ($arData["ITEMS"] as $items)
	{
		if ($items["MODULE"] == "catalog" && CModule::IncludeModule('catalog') && CModule::IncludeModule('iblock'))
		{
			$imgCode = 0;
			if (!isset($items["DETAIL_PICTURE"]) || !isset($items["PREVIEW_PICTURE"]))
			{
				$dbProduct = CIBlockElement::GetList(array(), array("ID" => $items["PRODUCT_ID"]), false, false, array('DETAIL_PICTURE', 'PREVIEW_PICTURE'));
				$arProduct = $dbProduct->GetNext();
				$items["DETAIL_PICTURE"] = $arProduct["DETAIL_PICTURE"];
				$items["PREVIEW_PICTURE"] = $arProduct["PREVIEW_PICTURE"];
			}

			if ($items["DETAIL_PICTURE"] > 0)
				$imgCode = $items["DETAIL_PICTURE"];
			elseif ($items["PREVIEW_PICTURE"] > 0)
				$imgCode = $items["PREVIEW_PICTURE"];

			$items["NAME"] = htmlspecialcharsex($items["NAME"]);
			$items["EDIT_PAGE_URL"] = htmlspecialcharsex($items["EDIT_PAGE_URL"]);
			$items["CURRENCY"] = htmlspecialcharsex($items["CURRENCY"]);

			if ($imgCode > 0)
			{
				$arFile = CFile::GetFileArray($imgCode);
				$arImgProduct = CFile::ResizeImageGet($arFile, array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);
			}

			if (is_array($arImgProduct))
			{
				$imgUrl = $arImgProduct["src"];
				$imgProduct = "<a href=\"".$items["EDIT_PAGE_URL"]."\" target=\"_blank\"><img src=\"".$imgUrl."\" alt=\"\" title=\"".$items["NAME"]."\" ></a>";
			}
			else
				$imgProduct = "<div class='no_foto'>".GetMessage('NO_FOTO')."</div>";

			$arCurFormat = CCurrencyLang::GetCurrencyFormat($items["CURRENCY"]);
			$priceValutaFormat = str_replace("#", '', $arCurFormat["FORMAT_STRING"]);

			$currentTotalPrice = ($items["PRICE"] + $items["DISCOUNT_PRICE"]);

			$discountPercent = 0;
			if ($items["DISCOUNT_PRICE"] > 0)
				$discountPercent = IntVal(($items["DISCOUNT_PRICE"] * 100) / $currentTotalPrice);

			$ar_res = CCatalogProduct::GetByID($items["PRODUCT_ID"]);
			$balance = FloatVal($ar_res["QUANTITY"]);

			$arParams = array();
			$arParams["id"] = $items["PRODUCT_ID"];
			$arParams["name"] = $items["NAME"];
			$arParams["url"] = $items["DETAIL_PAGE_URL"];
			$arParams["urlEdit"] = $items["EDIT_PAGE_URL"];
			$arParams["urlImg"] = $imgUrl;
			$arParams["price"] = FloatVal($items["PRICE"]);
			$arParams["priceBase"] = FloatVal($currentTotalPrice);
			$arParams["priceBaseFormat"] = CurrencyFormatNumber(FloatVal($currentTotalPrice), $items["CURRENCY"]);
			$arParams["priceFormated"] = CurrencyFormatNumber(FloatVal($items["PRICE"]), $items["CURRENCY"]);
			$arParams["valutaFormat"] = $priceValutaFormat;
			$arParams["priceDiscount"] = FloatVal($items["DISCOUNT_PRICE"]);
			$arParams["priceTotalFormated"] = SaleFormatCurrency($currentTotalPrice, $items["CURRENCY"]);
			$arParams["discountPercent"] = $discountPercent;
			$arParams["summaFormated"] = CurrencyFormatNumber($items["PRICE"], $items["CURRENCY"]);
			$arParams["quantity"] = 1;
			$arParams["module"] = "catalog";
			$arParams["currency"] = $items["CURRENCY"];
			$arParams["weight"] = 0;
			$arParams["vatRate"] = 0;
			$arParams["priceType"] = "";
			$arParams["balance"] = $balance;
			$arParams["catalogXmlID"] = "";
			$arParams["productXmlID"] = "";
			$arParams["callback"] = "CatalogBasketCallback";
			$arParams["orderCallback"] = "CatalogBasketOrderCallback";
			$arParams["cancelCallback"] = "CatalogBasketCancelCallback";
			$arParams["payCallback"] = "CatalogPayOrderCallback";

			$result .= "<tr id='more_".$type."_".$items["ID"]."'>
							<td class=\"tab_img\" >".$imgProduct."</td>
							<td class=\"tab_text\">
								<div class=\"order_name\"><a href=\"".$items["EDIT_PAGE_URL"]."\" target=\"_blank\" title=\"".$items["NAME"]."\">".$items["NAME"]."</a></div>
								<div class=\"order_price\">
									".GetMessage('NEWO_SUBTAB_PRICE').": <b>".SaleFormatCurrency($items["PRICE"], $currency)."</b>
								</div>";

			$arResult = CSaleProduct::GetProductSku($USER_ID, $LID, $items["PRODUCT_ID"], $items["NAME"]);

			$arResult["POPUP_MESSAGE"] = array(
				"PRODUCT_ADD" => GetMEssage('NEWO_POPUP_TO_BUSKET'),
				"PRODUCT_ORDER" => GetMEssage('NEWO_POPUP_TO_ORDER'),
				"PRODUCT_NOT_ADD" => GetMEssage('NEWO_POPUP_DONT_CAN_BUY'),
				"PRODUCT_PRICE_FROM" => GetMessage('NEWO_POPUP_FROM')
			);

			if (count($arResult["SKU_ELEMENTS"]) <= 0)
				$result .= "<a href=\"javascript:void(0);\" class=\"get_new_order\" onClick=\"fAddToBusketMoreProduct('".$type."', ".CUtil::PhpToJSObject($arParams).");return false;\"><span></span>".GetMessage('NEWO_SUBTAB_ADD_BUSKET')."</a><br>";
			else
				$result .= "<a href=\"javascript:void(0);\" class=\"get_new_order\" onClick=\"fAddToBusketMoreProductSku(".CUtil::PhpToJsObject($arResult['SKU_ELEMENTS']).", ".CUtil::PhpToJsObject($arResult['SKU_PROPERTIES']).", 'busket', ".CUtil::PhpToJsObject($arResult["POPUP_MESSAGE"]).");\"><span></span>".GetMessage('NEWO_SUBTAB_ADD_BUSKET')."</a><br>";

			if (!$crmMode)
			{
				if (count($arResult["SKU_ELEMENTS"]) > 0)
				{
					$result .= "<a href=\"javascript:void(0);\" class=\"get_new_order\" onClick=\"fAddToBusketMoreProductSku(".CUtil::PhpToJsObject($arResult['SKU_ELEMENTS']).", ".CUtil::PhpToJsObject($arResult['SKU_PROPERTIES']).", 'neworder', ".CUtil::PhpToJsObject($arResult["POPUP_MESSAGE"]).");\"><span></span>".GetMessage('NEWO_SUBTAB_ADD_ORDER')."</a>";
				}
				else
				{
					$url = "/bitrix/admin/sale_order_new.php?lang=".LANG."&user_id=".$USER_ID."&LID=".$LID."&product[]=".$items["PRODUCT_ID"];
					$result .= "<a href=\"".$url."\" target=\"_blank\" class=\"get_new_order\"><span></span>".GetMessage('NEWO_SUBTAB_ADD_ORDER')."</a>";
				}
			}

			$result .= "</td></tr>";
		}
	}

	if ($arData["CNT"] > 2 && $arData["CNT"] != count($arData["ITEMS"]))
	{
		$result .= "<tr><td colspan='2' align='right' class=\"more_product\">";
		if ($type == "busket")
			$result .= "<a href='javascript:void(0);' onClick='fGetMoreBusket(\"Y\");' class=\"get_more\">".GetMessage('NEWO_SUBTAB_MORE')."<span></span></a>";
		elseif ($type == "viewed")
			$result .= "<a href='javascript:void(0);' onClick='fGetMoreViewed(\"Y\");' class=\"get_more\">".GetMessage('NEWO_SUBTAB_MORE')."<span></span></a>";
		else
			$result .= "<a href='javascript:void(0);' onClick='fGetMoreRecom();' class=\"get_more\">".GetMessage('NEWO_SUBTAB_MORE')."<span></span></a>";
		$result .= "</td></tr>";
	}

	$result .= "</table>";

	return $result;
}

function fDeleteDoubleProduct($arShoppingCart = array(), $arDelete = array(), $showAll = 'N')
{
	global $COUNT_RECOM_BASKET_PROD;
	$arResult = array();
	$arShoppingCartTmp = array();

	if (count($arShoppingCart) > 0)
	{
		foreach($arShoppingCart as $key => $val)
			if (!in_array($val["PRODUCT_ID"], $arDelete))
				$arShoppingCartTmp[] = $val;
	}

	if (count($arShoppingCartTmp) > 0)
	{
		$i = 0;
		foreach($arShoppingCartTmp as $key => $val)
		{
			if (!isset($val["PRODUCT_ID"]))
				$val["PRODUCT_ID"] = $val["ID"];

			if (!isset($val["EDIT_PAGE_URL"]) || $val["EDIT_PAGE_URL"] == "")
			{
				if ($val["MODULE"] == "catalog" && CModule::IncludeModule('catalog'))
				{
					$res = CIBlockElement::GetList(array(), array("ID" => $val["PRODUCT_ID"]), false, false, array('IBLOCK_ID', 'IBLOCK_SECTION_ID'));
					if ($arCat = $res->Fetch())
					{
						if ($arCat["IBLOCK_ID"] > 0 && $arCat["IBLOCK_SECTION_ID"] > 0)
							$val["EDIT_PAGE_URL"] = "/bitrix/admin/iblock_element_edit.php?ID=".$val["PRODUCT_ID"]."&type=catalog&lang=".LANG."&IBLOCK_ID=".$arCat["IBLOCK_ID"]."&find_section_section=".$arCat["IBLOCK_SECTION_ID"];
					}
				}
			}

			$arResult["ITEMS"][] = $val;
			$i++;
			if ($i >= $COUNT_RECOM_BASKET_PROD && $showAll == "N")
				break;
		}
	}
	else
	{
		$arResult["CNT"] = 0;
		$arResult["ITEMS"] = array();
	}

	if ($showAll == "Y")
		$arResult["CNT"] = count($arResult["ITEMS"]);
	else
		$arResult["CNT"] = count($arShoppingCartTmp);

	return $arResult;
}


/*****************************************************************************/
/**************************** SAVE ORDER *************************************/
/*****************************************************************************/
$bVarsFromForm = false;

if ($REQUEST_METHOD == "POST" && $save_order_data == "Y" && empty($dontsave) AND $saleModulePermissions >= "U" AND check_bitrix_sessid())
{
	$ID = IntVal($ID);

	//buyer type, new or exist
	$btnNewBuyer = "N";
	if ($btnTypeBuyer == "btnBuyerNew")
		$btnNewBuyer = "Y";

	if (strlen($LID) <= 0)
		$errorMessage .= GetMessage("SOE_EMPTY_SITE")."<br>";

	$BASE_LANG_CURRENCY = CSaleLang::GetLangCurrency($LID);

	$str_PERSON_TYPE_ID = IntVal($buyer_type_id);
	if ($str_PERSON_TYPE_ID <= 0)
		$errorMessage .= GetMessage("SOE_EMPTY_PERS_TYPE")."<br>";

	if (($str_PERSON_TYPE_ID > 0) && !($arPersonType = CSalePersonType::GetByID($str_PERSON_TYPE_ID)))
		$errorMessage .= GetMessage("SOE_PERSON_NOT_FOUND")."<br>";

	$str_STATUS_ID = trim($STATUS_ID);
	if (strlen($str_STATUS_ID) > 0)
	{
		if ($saleModulePermissions < "W")
		{
			$dbStatusList = CSaleStatus::GetList(
				array(),
				array(
					"GROUP_ID" => $GLOBALS["USER"]->GetUserGroupArray(),
					"PERM_STATUS" => "Y",
					"ID" => $str_STATUS_ID
				),
				array("ID", "MAX" => "PERM_STATUS"),
				false,
				array("ID")
			);
			if (!$dbStatusList->Fetch())
				$errorMessage .= str_replace("#STATUS_ID#", $str_STATUS_ID, GetMessage("SOE_NO_STATUS_PERMS"))."<br>";
		}
	}

	$str_PAY_SYSTEM_ID = IntVal($PAY_SYSTEM_ID);
	if ($str_PAY_SYSTEM_ID <= 0)
		$errorMessage .= GetMessage("SOE_PAYSYS_EMPTY")."<br>";
	if (($str_PAY_SYSTEM_ID > 0) && !($arPaySys = CSalePaySystem::GetByID($str_PAY_SYSTEM_ID, $str_PERSON_TYPE_ID)))
		$errorMessage .= GetMessage("SOE_PAYSYS_NOT_FOUND")."<br>";

	if (count($_POST["PRODUCT"]) <= 0)
		$errorMessage .= GetMessage("SOE_EMPTY_ITEMS")."<br>";

	if (isset($DELIVERY_ID) AND $DELIVERY_ID != "")
	{
		$str_DELIVERY_ID = trim($DELIVERY_ID);
		$PRICE_DELIVERY = FloatVal($PRICE_DELIVERY);
	}

	$arCupon = fGetCupon($_POST["CUPON"]);

	$str_ADDITIONAL_INFO = trim($_POST["ADDITIONAL_INFO"]);
	$str_COMMENTS = trim($_POST["COMMENTS"]);

	$profileName = "";
	if (isset($user_profile) && $user_profile != "" && $btnNewBuyer == "N")
		$userProfileID = IntVal($user_profile);

	//array field send mail
	$FIO = "";
	$rsUser = CUser::GetByID($user_id);
	if($arUser = $rsUser->Fetch())
	{
		if ($arUser["LAST_NAME"] != "")
			$FIO .= $arUser["LAST_NAME"]." ";
		if ($arUser["NAME"] != "")
			$FIO .= $arUser["NAME"];
	}

	$arUserEmail = array("PAYER_NAME" => $FIO, "USER_EMAIL" => $arUser["EMAIL"]);

	if ($BREAK_NAME == GetMessage('NEWO_BREAK_NAME'))
		$BREAK_NAME = "";
	if ($BREAK_LAST_NAME == GetMessage('NEWO_BREAK_LAST_NAME'))
		$BREAK_LAST_NAME = "";
	if ($BREAK_SECOND_NAME == GetMessage('NEWO_BREAK_SECOND_NAME'))
		$BREAK_SECOND_NAME = "";

	//create a new user
	if ($btnNewBuyer == "Y" && strlen($errorMessage) <= 0)
	{
		if (strlen($NEW_BUYER_EMAIL) <= 0)
		{
			$emailId = '';
			$dbProperties = CSaleOrderProps::GetList(
				array("ID" => "ASC"),
				array("PERSON_TYPE_ID" => $str_PERSON_TYPE_ID, "ACTIVE" => "Y", "IS_EMAIL" => "Y"),
				false,
				false,
				array("ID")
			);
			while ($arProperties = $dbProperties->Fetch())
			{
				if ($emailId == '')
					$emailId = $arProperties["ID"];

				if ($arProperties["REQUIED"] == "Y")
					$emailId = $arProperties["ID"];
			}
			$NEW_BUYER_EMAIL = ${"ORDER_PROP_".$emailId};
		}

		if (strlen($NEW_BUYER_EMAIL) <= 0)
			$errorMessage .= GetMessage("NEWO_BUYER_REG_ERR_MAIL");

		//take default value PHONE for register user
		$dbOrderProps = CSaleOrderProps::GetList(
			array(),
			array("PERSON_TYPE_ID" => $str_PERSON_TYPE_ID, "ACTIVE" => "Y", "CODE" => "PHONE"),
			false,
			false,
			array("ID")
		);
		$arOrderProps = $dbOrderProps->Fetch();
		$NEW_BUYER_PHONE = "";
		if (count($arOrderProps) > 0)
			$NEW_BUYER_PHONE = trim($_POST["ORDER_PROP_".$arOrderProps["ID"]]);

		$NEW_BUYER_SECOND_NAME = '';
		if (strlen($_POST["NEW_BUYER_NAME"]) <= 0 && strlen($_POST["NEW_BUYER_LAST_NAME"]) <= 0)
		{
			$NEW_BUYER_NAME = trim($_POST["BREAK_NAME"]);
			$NEW_BUYER_LAST_NAME = trim($_POST["BREAK_LAST_NAME"]);
			$NEW_BUYER_SECOND_NAME = trim($_POST["BREAK_SECOND_NAME"]);
		}

		if (strlen($NEW_BUYER_NAME) <= 0 || strlen($NEW_BUYER_LAST_NAME) <= 0)
			$errorMessage .= GetMessage("NEWO_BUYER_REG_ERR_NAME")."<br>";

		$NEW_BUYER_FIO = $NEW_BUYER_LAST_NAME." ".$NEW_BUYER_NAME." ".$NEW_BUYER_SECOND_NAME;

		if (strlen($errorMessage) <= 0)
		{
			$userRegister = array(
				"NAME" => $NEW_BUYER_NAME,
				"LAST_NAME" => $NEW_BUYER_LAST_NAME,
				"SECOND_NAME" => $NEW_BUYER_SECOND_NAME,
				"PERSONAL_MOBILE" => $NEW_BUYER_PHONE
			);

			$arPersonal = array("PERSONAL_MOBILE" => $NEW_BUYER_PHONE);

			$user_id = CSaleUser::DoAutoRegisterUser($NEW_BUYER_EMAIL, $userRegister, $LID, $arErrors, $arPersonal);
			if (count($arErrors) > 0)
			{
				foreach($arErrors as $val)
					$errorMessage .= $val["TEXT"];
			}
			else
			{
				$userProfileID = 0;
				$rsUser = CUser::GetByID($user_id);
				$arUser = $rsUser->Fetch();

				$userNew = str_replace("#FIO#", "(".$arUser["LOGIN"].")".(($arUser["NAME"] != "") ? " ".$arUser["NAME"] : "").(($arUser["LAST_NAME"] != "") ? " ".$arUser["LAST_NAME"] : ""), GetMessage("NEWO_BUYER_REG_OK"));
			}
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		//property order
		$arOrderPropsValues = array();
		$dbOrderProps = CSaleOrderProps::GetList(
			array("SORT" => "ASC"),
			array("PERSON_TYPE_ID" => $str_PERSON_TYPE_ID, "ACTIVE" => "Y"),
			false,
			false,
			array("ID", "NAME", "TYPE", "REQUIED", "IS_LOCATION", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "CODE", "SORT")
		);
		while ($arOrderProps = $dbOrderProps->Fetch())
		{
			if(!is_array(${"ORDER_PROP_".$arOrderProps["ID"]}))
				//$curVal = trim(${"ORDER_PROP_".$arOrderProps["ID"]});
				$curVal = trim($_POST["ORDER_PROP_".$arOrderProps["ID"]]);
			else
				//$curVal = ${"ORDER_PROP_".$arOrderProps["ID"]};
				$curVal = trim($_POST["ORDER_PROP_".$arOrderProps["ID"]]);

			if ($arOrderProps["TYPE"]=="LOCATION")
			{
				//$curVal = ${"CITY_ORDER_PROP_".$arOrderProps["ID"]};
				$curVal = $_POST["CITY_ORDER_PROP_".$arOrderProps["ID"]];
				$DELIVERY_LOCATION = IntVal($curVal);
			}
			if ($arOrderProps["IS_PAYER"] == "Y")
			{
				if (strlen($curVal) <= 0)
					$curVal = $NEW_BUYER_FIO;

				$arUserEmail["PAYER_NAME"] = trim($curVal);
			}
			if ($arOrderProps["IS_EMAIL"] == "Y")
			{
				$arUserEmail["USER_EMAIL"] = trim($curVal);
			}

			if ($arOrderProps["IS_PROFILE_NAME"] == "Y")
			{
				$profileName = "";
				if (isset($userProfileID))
					$profileName = $curVal;
			}

			if (
				($arOrderProps["IS_LOCATION"]=="Y" || $arOrderProps["IS_LOCATION4TAX"]=="Y")
				&& IntVal($curVal) <= 0
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
				&& empty($curVal))
				)
			{
				$errorMessage .= str_replace("#NAME#", $arOrderProps["NAME"], GetMessage("SOE_EMPTY_PROP"))."<br>";
			}

			if ($arOrderProps["TYPE"] == "MULTISELECT")
			{
				$curVal = "";
				for ($i = 0; $i < count($_POST["ORDER_PROP_".$arOrderProps["ID"]]); $i++)
				{
					if ($i > 0)
						$curVal .= ",";

					//$curVal .= ${"ORDER_PROP_".$arOrderProps["ID"]}[$i];
					$curVal .= $_POST["ORDER_PROP_".$arOrderProps["ID"]][$i];
				}
			}

			$arOrderPropsValues[$arOrderProps["ID"]] = $curVal;
		}
	}

	$str_USER_ID = IntVal($user_id);
	if ($str_USER_ID <= 0 && strlen($errorMessage) <= 0)
	{
		$str_USER_ID = "";
		$errorMessage .= GetMessage("SOE_EMPTY_USER")."<br>";
	}
	elseif ($str_USER_ID > 0 && strlen($errorMessage) <= 0)
	{
		$rsUser = CUser::GetByID($str_USER_ID);
		if (!$rsUser->Fetch())
			$errorMessage .= GetMessage("NEWO_ERR_EMPTY_USER")."<br>";
	}

	foreach ($_POST["PRODUCT"] as $key => $val)
	{
		if ((!isset($val["ID"]) || IntVal($val["ID"]) <= 0) && $val["MODULE"] == 'catalog')
		{
			CModule::IncludeModule('catalog');
			if ($arCatalogProduct = CCatalogProduct::GetByID($key))
			{
				if ($arCatalogProduct["CAN_BUY_ZERO"]!="Y" && ($arCatalogProduct["QUANTITY_TRACE"]=="Y" && doubleval($arCatalogProduct["QUANTITY"])<=0))
					$errorMessage .= str_replace("#NAME#", $val['NAME'], GetMessage("NEWO_ERR_PRODUCT_NULL_BALANCE"));
			}
		}
	}

	//saving
	if (strlen($errorMessage) <= 0)
	{
		//send new user mail
		if ($btnNewBuyer == "Y" && strlen($userNew) > 0)
			CUser::SendUserInfo($str_USER_ID, $LID, $userNew, true);

		$arShoppingCart = array();
		$arOrderProductPrice = fGetUserShoppingCart($_POST["PRODUCT"], $LID, $BASE_LANG_CURRENCY);
		$arShoppingCart = CSaleBasket::DoGetUserShoppingCart($LID, $str_USER_ID, $arOrderProductPrice, $arErrors, $arCupon);
		$arErrors = array();
		$arWarnings = array();

		if (count($arShoppingCart) > 0)
		{
			foreach($arOrderProductPrice as $key => $val)
			{
				if ($val["NAME"] != $arShoppingCart[$key]["NAME"] AND $val["PRODUCT_ID"] == $arShoppingCart[$key]["PRODUCT_ID"])
					$arShoppingCart[$key]["NAME"] = $val["NAME"];

				if ($val["NOTES"] != $arShoppingCart[$key]["NOTES"] AND $val["PRODUCT_ID"] == $arShoppingCart[$key]["PRODUCT_ID"])
					$arShoppingCart[$key]["NOTES"] = $val["NOTES"];
			}
		}

		//parameters order
		$arOrder = CSaleOrder::DoCalculateOrder(
			$LID,
			$str_USER_ID,
			$arShoppingCart,
			$str_PERSON_TYPE_ID,
			$arOrderPropsValues,
			$str_DELIVERY_ID,
			$str_PAY_SYSTEM_ID,
			array(),
			$arErrors,
			$arWarnings);

		//change delivery price
		if (DoubleVal($arOrder["DELIVERY_PRICE"]) != $PRICE_DELIVERY)
		{
			$arOrder["PRICE"] = ($arOrder["PRICE"] - $arOrder["DELIVERY_PRICE"]) + $PRICE_DELIVERY;
			$arOrder["DELIVERY_PRICE"] = $PRICE_DELIVERY;
			$arOrder["PRICE_DELIVERY"] = $PRICE_DELIVERY;
		}

		if (count($arShoppingCart) <= 0 && count($arOrderProductPrice) > 0)
			$errorMessage .= GetMessage('NEWO_ERR_BUSKET_NULL')."<br>";
		else
		{
			if (count($arWarnings) > 0)
			{
				foreach ($arWarnings as $val)
					$errorMessage .= $val["TEXT"]."<br>";
			}
			if (count($arErrors) > 0)
			{
				foreach ($arErrors as $val)
					$errorMessage .= $val["TEXT"]."<br>";
			}
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		//another order parametrs
		$arAdditionalFields = array(
			"USER_DESCRIPTION" => $_POST["USER_DESCRIPTION"],
			"ADDITIONAL_INFO" => $str_ADDITIONAL_INFO,
			"COMMENTS" => $str_COMMENTS,
		);

		if (count($arOrder) > 0)
		{
			$arErrors = array();
			$OrderNewSendEmail = false;

			$arOldOrder = CSaleOrder::GetByID($ID);

			if ($ID <= 0 || $arOldOrder["STATUS_ID"] == $str_STATUS_ID)
				$arAdditionalFields["STATUS_ID"] = $str_STATUS_ID;

			$tmpID = CSaleOrder::DoSaveOrder($arOrder, $arAdditionalFields, $ID, $arErrors, $arCupon);

			//delete from busket
			if ($tmpID > 0)
			{
				$arFuserItems = CSaleUser::GetList(array("USER_ID" => intval($user_id)));
				$FUSER_ID = $arFuserItems["ID"];
				foreach($_POST["PRODUCT"] as $key => $val)
				{
					if (!isset($val["BUSKET_ID"]) && intVal($val["BUSKET_ID"]) <= 0)
					{
						$dbBasket = CSaleBasket::GetList(
							array(),
							array("ORDER_ID" => "NULL", "PRODUCT_ID" => $key, "FUSER_ID" => $FUSER_ID, "LID" => $LID),
							false,
							false,
							array("ID")
						);
						$arBasket = $dbBasket->GetNext();
						CSaleBasket::Delete($arBasket["ID"]);
					}
				}
			}

			if ($ID <= 0)
				$OrderNewSendEmail = true;
			else
			{
				if ($arOldOrder["STATUS_ID"] != $str_STATUS_ID)
					CSaleOrder::StatusOrder($ID, $str_STATUS_ID);
			}

			$ID = $tmpID;

			if ($ID > 0 AND count($arErrors) <= 0)
			{
				$CANCELED = trim($_POST["CANCELED"]);
				$REASON_CANCELED = trim($_POST["REASON_CANCELED"]);
				if ($CANCELED != "Y")
					$CANCELED = "N";
				$arOrder2Update = Array();

				if ($arOldOrder["CANCELED"] != $CANCELED)
				{
					$bUserCanCancelOrder = CSaleOrder::CanUserCancelOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());

					$errorMessageTmp = "";

					if (!$bUserCanCancelOrder)
						$errorMessageTmp .= GetMessage("SOD_NO_PERMS2CANCEL").". ";

					if (strlen($errorMessageTmp) <= 0)
					{
						if (!CSaleOrder::CancelOrder($ID, $CANCELED, $REASON_CANCELED))
						{
							if ($ex = $APPLICATION->GetException())
							{
								if ($ex->GetID() != "ALREADY_FLAG")
									$errorMessageTmp .= $ex->GetString();
							}
							else
								$errorMessageTmp .= GetMessage("ERROR_CANCEL_ORDER").". ";
						}
					}

					if ($errorMessageTmp != "")
						$arErrors[] = $errorMessageTmp;
				}
				else
				{
					if($arOldOrder["REASON_CANCELED"] != $REASON_CANCELED)
						$arOrder2Update["REASON_CANCELED"] = $REASON_CANCELED;
				}
			}

			if ($ID > 0 AND count($arErrors) <= 0)
			{
				$PAYED = trim($_POST["PAYED"]);
				if ($PAYED != "Y")
					$PAYED = "N";
				$PAY_VOUCHER_NUM = trim($_POST["PAY_VOUCHER_NUM"]);
				$PAY_VOUCHER_DATE = trim($_POST["PAY_VOUCHER_DATE"]);
				$PAY_FROM_ACCOUNT = trim($_POST["PAY_FROM_ACCOUNT"]);
				$PAY_FROM_ACCOUNT_BACK = trim($_POST["PAY_FROM_ACCOUNT_BACK"]);

				if ($arOldOrder["PAYED"] != $PAYED)
				{
					$bUserCanPayOrder = CSaleOrder::CanUserChangeOrderFlag($ID, "P", $GLOBALS["USER"]->GetUserGroupArray());
					$errorMessageTmp = "";

					if (!$bUserCanPayOrder)
						$errorMessageTmp .= GetMessage("SOD_NO_PERMS2PAYFLAG").". ";

					if (strlen($errorMessageTmp) <= 0)
					{
						$arAdditionalFields = array(
							"PAY_VOUCHER_NUM" => ((strlen($PAY_VOUCHER_NUM) > 0) ? $PAY_VOUCHER_NUM : False),
							"PAY_VOUCHER_DATE" => ((strlen($PAY_VOUCHER_DATE) > 0) ? $PAY_VOUCHER_DATE : False)
						);

						$bWithdraw = true;
						$bPay = true;
						if ($PAY_CURRENT_ACCOUNT == "Y")
						{
							$dbUserAccount = CSaleUserAccount::GetList(
							array(),
							array(
								"USER_ID" => $arOrder["USER_ID"],
								"CURRENCY" => $arOrder["CURRENCY"],
								)
							);
							if ($arUserAccount = $dbUserAccount->Fetch())
							{
								if (DoubleVal($arUserAccount["CURRENT_BUDGET"]) >= $arOrder["PRICE"])
									$bPay = false;
							}
						}
						if ($PAYED == "N" && $PAY_FROM_ACCOUNT_BACK != "Y")
							$bWithdraw = false;

						if (!CSaleOrder::PayOrder($ID, $PAYED, $bWithdraw, $bPay, 0, $arAdditionalFields))
						{
							if ($ex = $APPLICATION->GetException())
							{
								if ($ex->GetID() != "ALREADY_FLAG")
									$errorMessageTmp .= $ex->GetString();
							}
							else
								$errorMessageTmp .= GetMessage("ERROR_PAY_ORDER").". ";
						}

						if ($errorMessageTmp != "")
							$arErrors[] = $errorMessageTmp;
					}
				}
				else
				{
					if($arOldOrder["PAY_VOUCHER_NUM"] != $PAY_VOUCHER_NUM)
						$arOrder2Update["PAY_VOUCHER_NUM"] = ((strlen($PAY_VOUCHER_NUM) > 0) ? $PAY_VOUCHER_NUM : False);
					if($arOldOrder["PAY_VOUCHER_DATE"] != $PAY_VOUCHER_DATE)
						$arOrder2Update["PAY_VOUCHER_DATE"] = ((strlen($PAY_VOUCHER_DATE) > 0) ? $PAY_VOUCHER_DATE : False);
				}
			}

			if ($ID > 0 AND count($arErrors) <= 0)
			{
				$ALLOW_DELIVERY = trim($_POST["ALLOW_DELIVERY"]);
				if ($ALLOW_DELIVERY != "Y")
					$ALLOW_DELIVERY = "N";
				$DELIVERY_DOC_NUM = trim($_POST["DELIVERY_DOC_NUM"]);
				$DELIVERY_DOC_DATE = trim($_POST["DELIVERY_DOC_DATE"]);

				if ($arOldOrder["ALLOW_DELIVERY"] != $ALLOW_DELIVERY)
				{
					$bUserCanDeliverOrder = CSaleOrder::CanUserChangeOrderFlag($ID, "D", $GLOBALS["USER"]->GetUserGroupArray());
					$errorMessageTmp = "";

					if (!$bUserCanDeliverOrder)
						$errorMessageTmp .= GetMessage("SOD_NO_PERMS2DELIV").". ";

					if (strlen($errorMessageTmp) <= 0)
					{
						$arAdditionalFields = array(
							"DELIVERY_DOC_NUM" => ((strlen($DELIVERY_DOC_NUM) > 0) ? $DELIVERY_DOC_NUM : False),
							"DELIVERY_DOC_DATE" => ((strlen($DELIVERY_DOC_DATE) > 0) ? $DELIVERY_DOC_DATE : False)
						);

						if (!CSaleOrder::DeliverOrder($ID, $ALLOW_DELIVERY, 0, $arAdditionalFields))
						{
							if ($ex = $APPLICATION->GetException())
							{
								if ($ex->GetID() != "ALREADY_FLAG")
									$errorMessageTmp .= $ex->GetString();
							}
							else
								$errorMessageTmp .= GetMessage("ERROR_DELIVERY_ORDER").". ";
						}
					}

					if ($errorMessageTmp != "")
						$arErrors[] = $errorMessageTmp;
				}
				else
				{
					if($arOldOrder["DELIVERY_DOC_NUM"] != $DELIVERY_DOC_NUM)
						$arOrder2Update["DELIVERY_DOC_NUM"] = ((strlen($DELIVERY_DOC_NUM) > 0) ? $DELIVERY_DOC_NUM : False);
					if($arOldOrder["DELIVERY_DOC_DATE"] != $DELIVERY_DOC_DATE)
						$arOrder2Update["DELIVERY_DOC_DATE"] = ((strlen($DELIVERY_DOC_DATE) > 0) ? $DELIVERY_DOC_DATE : False);
				}
			}

			if ($ID > 0 AND count($arErrors) <= 0)
			{
				if(!empty($arOrder2Update))
					CSaleOrder::Update($ID, $arOrder2Update);
			}

			if ($ID > 0 AND count($arErrors) <= 0)
			{
				//profile saving
				$str_USER_ID = IntVal($str_USER_ID);

				if (isset($userProfileID))
				{
					CSaleOrderUserProps::DoSaveUserProfile($str_USER_ID, $userProfileID, $profileName, $str_PERSON_TYPE_ID, $arOrderPropsValues, $arErrors);
				}
				unset($user_profile);
				//send new order mail
				if ($OrderNewSendEmail)
				{
					$strOrderList = "";
					foreach ($arOrder["BASKET_ITEMS"] as $val)
					{
						$strOrderList .= $val["NAME"]." - ".$val["QUANTITY"]." ".GetMessage("SOA_SHT").": ".SaleFormatCurrency($val["PRICE"], $BASE_LANG_CURRENCY);
						$strOrderList .= "\n";
					}

					//send mail
					$arFields = Array(
						"ORDER_ID" => $ID,
						"ORDER_DATE" => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", $LID))),
						"ORDER_USER" => $arUserEmail["PAYER_NAME"],
						"PRICE" => SaleFormatCurrency($arOrder["PRICE"], $BASE_LANG_CURRENCY),
						"BCC" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
						"EMAIL" => $arUserEmail["USER_EMAIL"],
						"ORDER_LIST" => $strOrderList,
						"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
						"DELIVERY_PRICE" => $arOrder["DELIVERY_PRICE"],
					);
					$eventName = "SALE_NEW_ORDER";

					$bSend = true;
					$db_events = GetModuleEvents("sale", "OnOrderNewSendEmail");
					while ($arEvent = $db_events->Fetch())
						if (ExecuteModuleEventEx($arEvent, Array($ID, &$eventName, &$arFields))===false)
							$bSend = false;

					if($bSend)
					{
						$event = new CEvent;
						$event->Send($eventName, $LID, $arFields, "N");
					}
				}
			}
			else
			{
				foreach($arErrors as $val)
					$errorMessage .= $val["TEXT"]."<br>";
			}
		}
		elseif (count($arErrors) > 0)
		{
			foreach($arErrors as $val)
				$errorMessage .= $val["TEXT"]."<br>";
		}
		else
		{
			$errorMessage .= GetMessage("SOE_SAVE_ERROR")."<br>";
		}
	}//end if save

	unset($location);
	unset($BTN_SAVE_BUYER);
	unset($buyertypechange);
	unset($userId);
	unset($user_id);

	if (strlen($errorMessage) <= 0 AND $ID > 0)
	{
		if ($crmMode)
			CRMModeOutput($ID);

		if (isset($save) AND strlen($save) > 0)
		{
			CSaleOrder::UnLock($ID);
			LocalRedirect("/bitrix/admin/sale_order.php?lang=".LANG."&LID=".CUtil::JSEscape($LID));
		}

		if (isset($apply) AND strlen($apply) > 0)
			LocalRedirect("/bitrix/admin/sale_order_new.php?lang=".LANG."&ID=".$ID."&LID=".CUtil::JSEscape($LID));
	}
	if (strlen($errorMessage) > 0)
		$bVarsFromForm = true;
}

if (!empty($dontsave))
{
	CSaleOrder::UnLock($ID);
	if ($crmMode)
		CRMModeOutput($ID);

	LocalRedirect("/bitrix/admin/sale_order.php?lang=".LANG."&LID=".CUtil::JSEscape($LID).GetFilterParams("filter_", false));
}

/*****************************************************************************/
/************** Processing of requests from the proxy ************************/
/*****************************************************************************/

if (isset($ORDER_AJAX) AND $ORDER_AJAX == "Y" AND check_bitrix_sessid())
{
	/*
	* mestopolojenie
	*/
	if (isset($location) AND !isset($product) AND !isset($locationZip))
	{
		$location = IntVal($location);
		$tmpLocation = "";

		ob_start();
		$GLOBALS["APPLICATION"]->IncludeComponent(
				'bitrix:sale.ajax.locations',
				'',
				array(
					"SITE_ID" => $LID,
					"AJAX_CALL" => "Y",
					"COUNTRY_INPUT_NAME" => "ORDER_PROP_".$locid,
					"REGION_INPUT_NAME" => "REGION_ORDER_PROP_".$locid,
					"CITY_INPUT_NAME" => "CITY_ORDER_PROP_".$locid,
					"CITY_OUT_LOCATION" => "Y",
					"ALLOW_EMPTY_CITY" => "Y",
					"LOCATION_VALUE" => $location,
					"COUNTRY" => "",
					"ONCITYCHANGE" => "fRecalProduct('', '', 'N');",
				),
				null,
				array('HIDE_ICONS' => 'Y')
		);
		$tmpLocation = ob_get_contents();
		ob_end_clean();

		$arData = array();
		if (IntVal($locid) > 0)
		{
			$arData["status"] = "ok";
			$arData["prop_id"] = $locid;
			$arData["location"] = $tmpLocation;
		}
		$result = CUtil::PhpToJSObject($arData);

		CRMModeOutput($result);
	}

	/*
	* change type buyer
	*/
	if (isset($buyertypechange))
	{
		if (!isset($ID) OR $ID == "") $ID = "";
		if (!isset($paysystemid) OR $paysystemid == "") $paysystemid = "";

		$arData = array();
		$arData["status"] = "ok";
		$arData["buyertype"] = fGetBuyerType($buyertypechange, $LID, $userId, $ID);
		$arData["buyerdelivery"] = fBuyerDelivery($buyertypechange, $paysystemid);
		$arLocation = fGetLocationID($buyertypechange);

		$arData["location_id"] = $arLocation["LOCATION_ID"];
		$arData["location_zip_id"] = $arLocation["LOCATION_ZIP_ID"];

		$result = CUtil::PhpToJSObject($arData);

		CRMModeOutput($result);
	}

	/*
	* get locationId for geting delivery
	*/
	if (isset($persontypeid))
	{
		$persontypeid = IntVal($persontypeid);

		$arData = array();
		$arLocation = fGetLocationID($persontypeid);

		$arData["location_id"] = $arLocation["LOCATION_ID"];
		$arData["location_zip_id"] = $arLocation["LOCATION_ZIP_ID"];

		$result = CUtil::PhpToJSObject($arData);

		CRMModeOutput($result);
	}

	/*
	* take a list profile and user busket
	*/
	if (isset($userId) AND isset($buyerType) AND (!isset($profileDefault) OR $profileDefault == ""))
	{
		$id = IntVal($id);
		$userId = IntVal($userId);
		$buyerType = IntVal($buyerType);
		$LID = trim($LID);
		$currency = trim($currency);

		$arFuserItems = CSaleUser::GetList(array("USER_ID" => $userId));
		$fuserId = $arFuserItems["ID"];
		$arData = array();
		$arErrors = array();

		$arData["status"] = "ok";
		$arData["userProfileSelect"] = fUserProfile($userId, $buyerType);
		$arData["userName"] = fGetUserName($userId);

		$arShoppingCart = CSaleBasket::DoGetUserShoppingCart($LID, $userId, $fuserId, $arErrors, array());
		$arShoppingCart = fDeleteDoubleProduct($arShoppingCart, array(), 'N');
		$arData["userBasket"] = fGetFormatedProduct($userId, $LID, $arShoppingCart, $currency, 'busket');

		$arViewed = array();
		$dbViewsList = CSaleViewedProduct::GetList(
				array("DATE_VISIT"=>"DESC"),
				array("FUSER_ID" => $fuserId, ">PRICE" => 0, "!CURRENCY" => ""),
				false,
				array('nTopCount' => 10),
				array('ID', 'PRODUCT_ID', 'LID', 'MODULE', 'NAME', 'DETAIL_PAGE_URL', 'PRICE', 'CURRENCY', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
			);
		while ($arViews = $dbViewsList->Fetch())
			$arViewed[] = $arViews;

		$arViewedResult = fDeleteDoubleProduct($arViewed, $arFilterRecomendet, 'N');
		$arData["viewed"] = fGetFormatedProduct($userId, $LID, $arViewedResult, $currency, 'viewed');


		$result = CUtil::PhpToJSObject($arData);

		CRMModeOutput($result);
	}

	/*
	* script autocomplite profile
	*/
	if (isset($userId) AND isset($buyerType) AND isset($profileDefault))
	{
		$userId = IntVal($userId);
		$buyerType = IntVal($buyerType);
		$profileDefault = IntVal($profileDefault);

		$userProfile = array();
		$userProfile = CSaleOrderUserProps::DoLoadProfiles($userId, $buyerType);
		if ($profileDefault != "" AND $profileDefault != "0")
			$arPropValuesTmp = $userProfile[$profileDefault]["VALUES"];

		$dbVariants = CSaleOrderProps::GetList(
			array("SORT" => "ASC"),
			array(
					"PERSON_TYPE_ID" => $buyerType,
					"USER_PROPS" => "Y",
					"ACTIVE" => "Y"
				)
		);
		while ($arVariants = $dbVariants->Fetch())
		{
			if (isset($arPropValuesTmp[$arVariants["ID"]]))
				$arPropValues[$arVariants["ID"]] = $arPropValuesTmp[$arVariants["ID"]];
			else
				$arPropValues[$arVariants["ID"]] = $arVariants["DEFAULT_VALUE"];

			if($arVariants["IS_EMAIL"] == "Y" || $arVariants["IS_PAYER"] == "Y")
			{
				if(strlen($arPropValues[$arVariants["ID"]]) <= 0 && IntVal($userId) > 0)
				{
					$rsUser = CUser::GetByID($userId);
					if ($arUser = $rsUser->Fetch())
					{
						if($arVariants["IS_EMAIL"] == "Y")
							$arPropValues[$arVariants["ID"]] = $arUser["EMAIL"];
						else
						{
							if (strlen($arUser["LAST_NAME"]) > 0)
								$arPropValues[$arVariants["ID"]] .= $arUser["LAST_NAME"];
							if (strlen($arUser["NAME"]) > 0)
								$arPropValues[$arVariants["ID"]] .= " ".$arUser["NAME"];
							if (strlen($arUser["SECOND_NAME"]) > 0 AND strlen($arUser["NAME"]) > 0)
								$arPropValues[$arVariants["ID"]] .= " ".$arUser["SECOND_NAME"];
						}
					}
				}
			}

		}

		$scriptExec = "<script language=\"JavaScript\">";
		foreach ($arPropValues as $key => $val):
			$val = CUtil::JSEscape(htmlspecialcharsback($val));
			$scriptExec .= "var el = document.getElementById(\"ORDER_PROP_".$key."\");\n";
			$scriptExec .= "if(el)\n{\n";
			$scriptExec .= "var elType = el.getAttribute('type');\n";
			$scriptExec .= "if (elType == \"text\" || elType == \"textarea\" || elType == \"select\")\n";
			$scriptExec .= "{";
				$scriptExec .= "el.value = '".$val."';\n";
			$scriptExec .= "}";
			$scriptExec .= "else if (elType == \"location\")\n";
			$scriptExec .= "{";
			$scriptExec .= "BX.ajax.post('/bitrix/admin/sale_order_new.php', '".bitrix_sessid_get()."&ORDER_AJAX=Y&locid=".$key."&propID=".$buyerType."&LID=".CUtil::JSEscape($LID)."&location=".$val."', fLocationResult);\n";
			$scriptExec .= "}";
			$scriptExec .= "else if (elType == \"radio\")\n";
			$scriptExec .= "{";
				$scriptExec .= "elRadio = el.getElementsByTagName(\"input\");\n";
				$scriptExec .= "for (var i = 0; i < elRadio.length; i++)\n";
				$scriptExec .= "{";
					$scriptExec .= "if (elRadio[i].value == '".$val."')\n";
					$scriptExec .= "{";
						$scriptExec .= "elRadio[i].checked = true;\n";
					$scriptExec .= "}";
					$scriptExec .= "else {";
						$scriptExec .= "elRadio[i].checked = false;\n";
					$scriptExec .= "}";
				$scriptExec .= "}";
			$scriptExec .= "}";
			$scriptExec .= "else if (elType == \"checkbox\")\n";
			$scriptExec .= "{";
				if ($val == "Y")
				{
					$scriptExec .= "el.checked = true;\n";
				}
				else
				{
					$scriptExec .= "el.checked = false;\n";
				}
			$scriptExec .= "}";
			$scriptExec .= "else if (elType == \"multyselect\")\n";
			$scriptExec .= "{";
				if ($val != "")
				{
					$selectedVal = explode(",", $val);
					foreach ($selectedVal as $k => $v):
						$scriptExec .= "el.value = '".trim($v)."';\n";
					endforeach;
				}
				else
				{
					$scriptExec .= "el.selectedIndex = -1;";
				}
			$scriptExec .= "}\n";
			$scriptExec .= "}\n";
		endforeach;
		$scriptExec .= "fRecalProduct('', '', 'N');</script>";

		echo $scriptExec;
		die();
	}

	/*
	* get more busket
	*/
	if (isset($getmorebasket) && $getmorebasket == "Y")
	{
		$userId = IntVal($userId);
		$arFuserItems = CSaleUser::GetList(array("USER_ID" => intval($userId)));
		$fuserId = $arFuserItems["ID"];
		$arErrors = array();

		$arOrderProduct = CUtil::JsObjectToPhp($arProduct);
		$arShoppingCart = CSaleBasket::DoGetUserShoppingCart($LID, $userId, $fuserId, $arErrors, array());
		$arShoppingCart = fDeleteDoubleProduct($arShoppingCart, $arOrderProduct, $showAll);

		$result = fGetFormatedProduct($userId, $LID, $arShoppingCart, $CURRENCY, 'busket');

		CRMModeOutput($result);
	}

	/*
	* get more viewed
	*/
	if (isset($getmoreviewed) && $getmoreviewed == "Y")
	{
		$userId = IntVal($userId);
		$arFuserItems = CSaleUser::GetList(array("USER_ID" => intval($userId)));
		$fuserId = $arFuserItems["ID"];
		$arErrors = array();

		$arOrderProduct = CUtil::JsObjectToPhp($arProduct);
		$arViewed = array();
		$dbViewsList = CSaleViewedProduct::GetList(
				array("DATE_VISIT"=>"DESC"),
				array("FUSER_ID" => $fuserId, ">PRICE" => 0, "!CURRENCY" => ""),
				false,
				array('nTopCount' => 10),
				array('ID', 'PRODUCT_ID', 'LID', 'MODULE', 'NAME', 'DETAIL_PAGE_URL', 'PRICE', 'CURRENCY', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
			);
		while ($arViews = $dbViewsList->Fetch())
			$arViewed[] = $arViews;

		$arViewedCart = fDeleteDoubleProduct($arViewed, $arOrderProduct, $showAll);

		$result = fGetFormatedProduct($userId, $LID, $arViewedCart, $CURRENCY, 'viewed');

		CRMModeOutput($result);
	}

	/*
	* recal order
	*/
	if (isset($product) AND isset($user_id))
	{
		$result = "";
		$id = IntVal($id);
		$userId = IntVal($userId);
		$paySystemId = IntVal($paySystemId);
		$buyerTypeId = IntVal($buyerTypeId);
		$location = IntVal($location);
		$locationID = IntVal($locationID);
		$locationZip = IntVal($locationZip);
		$locationZipID = IntVal($locationZipID);
		$WEIGHT_UNIT = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', "", $LID));
		$WEIGHT_KOEF = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, $LID));
		$arDelivery = array();
		$recomMore = ($recomMore == "Y") ? "Y" : "N";

		$arOrderProduct = CUtil::JsObjectToPhp($product);

		$arCupon = fGetCupon($cupon);
		$arOrderProductPrice = fGetUserShoppingCart($arOrderProduct, $LID, $currency);
		$arShoppingCart = CSaleBasket::DoGetUserShoppingCart($LID, $user_id, $arOrderProductPrice, $arErrors, $arCupon);

		$arOrderPropsValues = array();
		if ($locationID != "" AND $location != "")
			$arOrderPropsValues[$locationID] = $location;
		if ($locationZipID != "" AND $locationZip != "")
			$arOrderPropsValues[$locationZipID] = $locationZip;

		//enable/disable town for location
		$dbProperties = CSaleOrderProps::GetList(
				array("SORT" => "ASC"),
				array("ID" => $locationID, "ACTIVE" => "Y", ">INPUT_FIELD_LOCATION" => 0),
				false,
				false,
				array("INPUT_FIELD_LOCATION")
			);
		if ($arProperties = $dbProperties->Fetch())
			$bDeleteFieldLocationID = $arProperties["INPUT_FIELD_LOCATION"];

		$rsLocationsList = CSaleLocation::GetList(
						array(),
						array("ID" => $location),
						false,
						false,
						array("ID", "CITY_ID")
					);
		$arCity = $rsLocationsList->GetNext();
		if (IntVal($arCity["CITY_ID"]) <= 0)
			$bDeleteFieldLocation = "Y";
		else
			$bDeleteFieldLocation = "N";

		$arOrder = CSaleOrder::DoCalculateOrder(
			$LID,
			$user_id,
			$arShoppingCart,
			$buyerTypeId,
			$arOrderPropsValues,
			$deliveryId,
			$paySystemId,
			array(),
			$arErrors,
			$arWarnings);

		$orderDiscount = 0;
		$arData = array();
		$arFilterRecomendet = array();
		$priceBaseTotal = 0;

		if (count($arOrder["BASKET_ITEMS"]) > 0)
		{
			foreach ($arOrder["BASKET_ITEMS"] as $val)
			{
				$arCurFormat = CCurrencyLang::GetCurrencyFormat($val["CURRENCY"]);
				$priceBase = $val["PRICE"] + $val["DISCOUNT_PRICE"];
				$priceDiscountPercent = IntVal(($val["DISCOUNT_PRICE"] * 100) / $priceBase);

				$arData[$val["PRODUCT_ID"]]["PRICE_BASE"] = CurrencyFormatNumber($priceBase, $val["CURRENCY"]);
				$arData[$val["PRODUCT_ID"]]["DISCOUNT_REPCENT"] = $priceDiscountPercent;
				$arData[$val["PRODUCT_ID"]]["DISCOUNT_PRICE"] = $val["DISCOUNT_PRICE"];
				$arData[$val["PRODUCT_ID"]]["PRICE"] = $val["PRICE"];
				$arData[$val["PRODUCT_ID"]]["PRICE_DISPLAY"] = CurrencyFormatNumber($val["PRICE"], $val["CURRENCY"]);
				$arData[$val["PRODUCT_ID"]]["QUANTITY"] = $val["QUANTITY"];
				$arData[$val["PRODUCT_ID"]]["DISCOUNT_PRICE_DISPLAY"] = CurrencyFormatNumber($val["DISCOUNT_PRICE"], $val["CURRENCY"]);
				$arData[$val["PRODUCT_ID"]]["SUMMA_DISPLAY"] = CurrencyFormatNumber(($val["PRICE"] * $val["QUANTITY"]), $val["CURRENCY"]);
				$arData[$val["PRODUCT_ID"]]["CURRENCY"] = $val["CURRENCY"];

				$balance = 0;
				if ($val["MODULE"] == "catalog" && CModule::IncludeModule('catalog'))
				{
					$ar_res = CCatalogProduct::GetByID($val["PRODUCT_ID"]);
					$balance = FloatVal($ar_res["QUANTITY"]);
				}
				$arData[$val["PRODUCT_ID"]]["BALANCE"] = $balance;
				$orderDiscount += $val["DISCOUNT_PRICE"] * $val["QUANTITY"];
				$arFilterRecomendet[] = $val["PRODUCT_ID"];

				$priceBaseTotal += ($arOrderProduct[$val["PRODUCT_ID"]]["PRICE_DEFAULT"] * $val["QUANTITY"]);
			}
		}
		$arData[0]["ORDER_ERROR"] = "N";

		//change delivery price
		$deliveryChangePrice = false;
		if ($delpricechange == "Y")
		{
			$arOrder["PRICE"] = ($arOrder["PRICE"] - $arOrder["DELIVERY_PRICE"]) + $deliveryPrice;
			$arOrder["DELIVERY_PRICE"] = $deliveryPrice;
			$arOrder["PRICE_DELIVERY"] = $deliveryPrice;
			$deliveryChangePrice = true;
			$arDelivery["DELIVERY_DEFAULT_PRICE"] = $deliveryPrice;
			$arDelivery["DELIVERY_DEFAULT"] = "";
			$arDelivery["DELIVERY_DEFAULT_ERR"] = "";
			$arDelivery["DELIVERY_DEFAULT_DESCRIPTION"] = "";
			$arData[0]["DELIVERY"] = "";
		}
		else
			$arDelivery = fGetDelivery($location, $locationZip, $arOrder["ORDER_WEIGHT"], $arOrder["ORDER_PRICE"], $currency, $LID, $deliveryId);

		$arData[0]["ORDER_ID"] = $id;
		$arData[0]["DELIVERY"] = $arDelivery["DELIVERY"];
		if (isset($arOrder["PRICE_DELIVERY"]) && floatval($arOrder["PRICE_DELIVERY"]) > 0)
		{
			$arData[0]["DELIVERY_PRICE"] = $arOrder["PRICE_DELIVERY"];
			$arData[0]["DELIVERY_PRICE_FORMAT"] = SaleFormatCurrency($arOrder["PRICE_DELIVERY"], $currency);
		}
		else
		{
			if ($arDelivery["CURRRENCY"] != $currency)
				$arDelivery["DELIVERY_DEFAULT_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arDelivery["DELIVERY_DEFAULT_PRICE"], $arDelivery["CURRENCY"], $currency), SALE_VALUE_PRECISION);

			$arData[0]["DELIVERY_PRICE"] = $arDelivery["DELIVERY_DEFAULT_PRICE"];
			$arData[0]["DELIVERY_PRICE_FORMAT"] = SaleFormatCurrency($arDelivery["DELIVERY_DEFAULT_PRICE"], $currency);
		}
		$arData[0]["DELIVERY_DEFAULT"] = $arDelivery["DELIVERY_DEFAULT"];

		if (strlen($arDelivery["DELIVERY_DEFAULT_ERR"]) > 0)
		{
			$arData[0]["DELIVERY_DESCRIPTION"] = $arDelivery["DELIVERY_DEFAULT_ERR"];
			$arData[0]["ORDER_ERROR"] = "Y";
		}
		else
			$arData[0]["DELIVERY_DESCRIPTION"] = $arDelivery["DELIVERY_DEFAULT_DESCRIPTION"];

		if (!isset($arOrder["ORDER_PRICE"]) OR $arOrder["ORDER_PRICE"] == "" )
			$arOrder["ORDER_PRICE"] = 0;
		if (!isset($arOrder["PRICE"]) OR $arOrder["PRICE"] == "")
			$arOrder["PRICE"] = 0;
		if (!isset($arOrder["DISCOUNT_VALUE"]) OR $arOrder["DISCOUNT_VALUE"] == "")
			$arOrder["DISCOUNT_VALUE"] = 0;

		$arData[0]["CURRENCY_FORMAT"] = trim(str_replace("#", '', $arCurFormat["FORMAT_STRING"]));
		$arData[0]["PRICE_TOTAL"] = SaleFormatCurrency($priceBaseTotal, $currency);
		$arData[0]["PRICE_WITH_DISCOUNT_FORMAT"] = SaleFormatCurrency($arOrder["ORDER_PRICE"], $currency);
		$arData[0]["PRICE_WITH_DISCOUNT"] = roundEx($arOrder["ORDER_PRICE"]);
		$arData[0]["PRICE_TAX"] = SaleFormatCurrency(DoubleVal($arOrder["TAX_VALUE"]), $currency);
		$arData[0]["PRICE_WEIGHT_FORMAT"] = roundEx(DoubleVal($arOrder["ORDER_WEIGHT"]/$WEIGHT_KOEF), SALE_VALUE_PRECISION)." ".$WEIGHT_UNIT;
		$arData[0]["PRICE_WEIGHT"] = roundEx(DoubleVal($arOrder["ORDER_WEIGHT"]/$WEIGHT_KOEF), SALE_VALUE_PRECISION);
		$arData[0]["PRICE_TO_PAY"] = SaleFormatCurrency($arOrder["PRICE"], $currency);
		$arData[0]["PRICE_TO_PAY_DEFAULT"] = FloatVal($arOrder["PRICE"]);
		$tmpPay = fGetPayFromAccount($user_id, $currency);
		$arData[0]["PAY_ACCOUNT"] = $tmpPay["PAY_MESSAGE"];
		$arData[0]["PAY_ACCOUNT_CAN_BUY"] = $tmpPay["PAY_BUDGET"];
		$arData[0]["PAY_ACCOUNT_DEFAULT"] = FloatVal($tmpPay["CURRENT_BUDGET"]);
		$arData[0]["DISCOUNT_VALUE"] = $arOrder["DISCOUNT_VALUE"];
		$arData[0]["DISCOUNT_VALUE_FORMATED"] = SaleFormatCurrency($arOrder["DISCOUNT_VALUE"], $currency);
		$arData[0]["DISCOUNT_PRODUCT_VALUE"] = $orderDiscount;
		$arData[0]["LOCATION_TOWN_ID"] = IntVal($bDeleteFieldLocationID);
		$arData[0]["LOCATION_TOWN_ENABLE"] = $bDeleteFieldLocation;

		//recomendet
		$recomendetProduct = "";
		$arProductIdInBasket = array();
		$arData[0]["RECOMMENDET_CALC"] = "N";
		if ($recommendet == "Y")
		{
			$arRecomendet = CSaleProduct::GetRecommendetProduct($userId, $LID, $arFilterRecomendet);
			$arRecomendetProduct = fDeleteDoubleProduct($arRecomendet, $arFilterRecomendet, $recomMore);

			$recomendetProduct = fGetFormatedProduct($user_id, $LID, $arRecomendetProduct, $currency, 'recom');
			$arData[0]["RECOMMENDET_CALC"] = "Y";
		}
		$arData[0]["RECOMMENDET_PRODUCT"] = $recomendetProduct;

		$result = CUtil::PhpToJSObject($arData);

		CRMModeOutput($result);
	}
}//end ORDER_AJAX=Y


/*****************************************************************************/
/**************************** FORM ORDER *************************************/
/*****************************************************************************/

//date order
$str_DATE_UPDATE = Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", $lang)));
$str_DATE_INSERT = Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", $lang)));
$str_PRICE = 0;
$str_DISCOUNT_VALUE = 0;

if (isset($ID) && $ID > 0)
{
	$dbOrder = CSaleOrder::GetList(
		array("ID" => "DESC"),
		array("ID" => $ID),
		false,
		false,
		array()
	);
	if (!($arOrderOldTmp = $dbOrder->ExtractFields("str_")))
		LocalRedirect("sale_order.php?lang=".LANG.GetFilterParams("filter_", false));
	$LID = $str_LID;
}
if (!isset($str_TAX_VALUE) OR $str_TAX_VALUE == "")
	$str_TAX_VALUE = 0;

if (IntVal($str_PERSON_TYPE_ID) <= 0)
{
	$str_PERSON_TYPE_ID = 0;
	$arFilter = array();
	$arFilter["ACTIVE"] = "Y";
	if(strlen($LID) > 0)
		$arFilter["LID"] = $LID;
	$dbPersonType = CSalePersonType::GetList(array("ID" => "ASC"), $arFilter);
	if($arPersonType = $dbPersonType->Fetch())
		$str_PERSON_TYPE_ID = $arPersonType["ID"];
}

$arFuserItems = CSaleUser::GetList(array("USER_ID" => intval($str_USER_ID)));
$FUSER_ID = $arFuserItems["ID"];

/*
 * form select site
 */
if ((!isset($LID) OR $LID == "") AND (defined('BX_PUBLIC_MODE') OR BX_PUBLIC_MODE == 1) )
{
	$arSitesShop = array();
	$arSitesTmp = array();
	$rsSites = CSite::GetList($by="id", $order="asc", Array("ACTIVE" => "Y"));
	while ($arSite = $rsSites->Fetch())
	{
		$site = COption::GetOptionString("sale", "SHOP_SITE_".$arSite["ID"], "");
		if ($arSite["ID"] == $site)
		{
			$arSitesShop[] = array("ID" => $arSite["ID"], "NAME" => $arSite["NAME"]);
		}
		$arSitesTmp[] = array("ID" => $arSite["ID"], "NAME" => $arSite["NAME"]);
	}

	$rsCount = count($arSitesShop);
	if ($rsCount <= 0)
	{
		$arSitesShop = $arSitesTmp;
		$rsCount = count($arSitesShop);
	}

	if ($rsCount === 1)
	{
		$LID = $arSitesShop[0]["ID"];
	}
	elseif ($rsCount > 1)
	{
?>
		<div id="select_lid">
			<form action="" name="select_lid">
				<div style="margin:10px auto;text-align:center;">
					<div><?=GetMessage("NEWO_SELECT_SITE")?></div><br />
					<select name="LID" onChange="fLidChange(this);">
						<option selected="selected" value=""><?=GetMessage("NEWO_SELECT_SITE")?></option>
						<?
						foreach ($arSitesShop as $key => $val)
						{
						?>
							<option value="<?=$val["ID"]?>"><? echo $val["NAME"]." (".$val["ID"].")";?></option>
						<?
						}
						?>
					</select>
				</div>
				<script>
					function fLidChange(el)
					{
						BX.showWait();
						BX.ajax.post("/bitrix/admin/sale_order_new.php", "<?=bitrix_sessid_get()?>&ORDER_AJAX=Y&lang=<?=LANGUAGE_ID?>&LID=" + el.value, fLidChangeResult);
					}
					function fLidChangeResult(result)
					{
						fLidChangeDisableButtons(false);
						BX.closeWait();
						if (result.length > 0)
						{
							document.getElementById("select_lid").innerHTML = result;
						}
					}
					function fLidChangeDisableButtons(val)
					{
						var btn = document.getElementById("btn-save");
						if (btn)
							btn.disabled = val;
						btn = document.getElementById("btn-cancel");
						if (btn)
							btn.disabled = val;
					}
					BX.ready(function(){ fLidChangeDisableButtons(true); });
				</script>
			</form>
		</div>
<?
		die();
	}
	else
	{
		echo "<div style=\"margin:10px auto;text-align:center;\">";
		echo GetMessage("NEWO_NO_SITE_SELECT");
		echo "<div>";
		die();
	}
}

if (!isset($str_CURRENCY) OR $str_CURRENCY == "")
	$str_CURRENCY = CSaleLang::GetLangCurrency($LID);

if (isset($ID) && $ID > 0)
	$title = GetMessage("SOEN_TAB_ORDER_TITLE");
else
	$title = GetMessage("SOEN_TAB_ORDER_NEW_TITLE");

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("SOEN_TAB_ORDER"), "ICON" => "sale", "TITLE" => $title),
);
$tabControl = new CAdminForm("form_order_buyers", $aTabs, false, true);

if (isset($ID) && $ID > 0)
	$APPLICATION->SetTitle(str_replace("#ID#", $ID, GetMessage("NEWO_TITLE_EDIT")));
elseif (isset($LID) && $LID != "")
{
	$siteName = $LID;
	$dbSite = CSite::GetByID($LID);
	if($arSite = $dbSite->Fetch())
		$siteName = $arSite["NAME"]." (".$LID.")";
	$APPLICATION->SetTitle(str_replace("#LID#", $siteName, GetMessage("NEWO_TITLE_ADD")));
}
else
	$APPLICATION->SetTitle(GetMessage("NEWO_TITLE_DEFAULT"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array();
$aMenu = array(
	array(
		"ICON" => "btn_list",
		"TEXT" => GetMessage("SOE_TO_LIST"),
		"LINK" => "/bitrix/admin/sale_order.php?lang=".LANGUAGE_ID
	)
);
$link = urlencode(DeleteParam(array("mode")));
$link = urlencode($GLOBALS["APPLICATION"]->GetCurPage())."?mode=settings".($link <> "" ? "&".$link: "");

$bUserCanViewOrder = CSaleOrder::CanUserViewOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());
$bUserCanDeleteOrder = CSaleOrder::CanUserDeleteOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());
$bUserCanCancelOrder = CSaleOrder::CanUserCancelOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());
$bUserCanPayOrder = CSaleOrder::CanUserChangeOrderFlag($ID, "P", $GLOBALS["USER"]->GetUserGroupArray());
$bUserCanDeliverOrder = CSaleOrder::CanUserChangeOrderFlag($ID, "D", $GLOBALS["USER"]->GetUserGroupArray());

if ($bUserCanViewOrder && $ID > 0)
{
	$aMenu[] = array(
		"TEXT" => GetMessage("NEWO_DETAIL"),
		"TITLE"=>GetMessage("NEWO_DETAIL_TITLE"),
		"LINK" => "/bitrix/admin/sale_order_detail.php?ID=".$ID."&lang=".LANGUAGE_ID.GetFilterParams("filter_")
	);
}

if ($ID > 0)
{
	$aMenu[] = array(
		"TEXT" => GetMessage("NEWO_TO_PRINT"),
		"TITLE"=>GetMessage("NEWO_TO_PRINT_TITLE"),
		"LINK" => "/bitrix/admin/sale_order_print.php?ID=".$ID."&lang=".LANGUAGE_ID.GetFilterParams("filter_")
	);
}

if (($saleModulePermissions == "W" || $str_PAYED != "Y") && $bUserCanDeleteOrder && $ID > 0)
{
	$aMenu[] = array(
			"TEXT" => GetMessage("NEWO_ORDER_DELETE"),
			"TITLE"=>GetMessage("NEWO_ORDER_DELETE_TITLE"),
			"LINK" => "javascript:if(confirm('".GetMessage("NEWO_CONFIRM_DEL_MESSAGE")."')) window.location='sale_order.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get().urlencode(GetFilterParams("filter_"))."'",
			"WARNING" => "Y"
		);
}

//delete context menu for remote query
if (!defined('BX_PUBLIC_MODE') || BX_PUBLIC_MODE != 1)
{
	$context = new CAdminContextMenu($aMenu);
	$context->Show();
}


/*********************************************************************/
/********************  BODY  *****************************************/
/*********************************************************************/

CAdminMessage::ShowMessage($errorMessage);

echo "<div id=\"form_content\">";
$tabControl->BeginEpilogContent();

if (isset($_REQUEST["user_id"]) && IntVal($_REQUEST["user_id"]) > 0)
{
	$str_USER_ID = IntVal($_REQUEST["user_id"]);
}
?>

<?=bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="LID" value="<?=htmlspecialcharsbx($LID)?>">
<input type="hidden" name="ID" value="<?=$ID?>">
<input type="hidden" name="save_order_data" value="Y">
<?if (isset($_REQUEST["user_id"]) && IntVal($_REQUEST["user_id"]) > 0):?>
	<input type="hidden" name="user_id" value="<?=IntVal($_REQUEST["user_id"])?>">
<?endif;?>
<?if (isset($_REQUEST["product"]) && count($_REQUEST["product"]) > 0)
{
	foreach ($_REQUEST["product"] as $val)
	{
		if(IntVal($val) > 0)
		{
			?><input type="hidden" name="product[]" value="<?=IntVal($val)?>"><?
		}
	}
}

$tabControl->EndEpilogContent();

if (!isset($LID) || $LID == "")
{
	$rsSites = CSite::GetList($by="id", $order="asc", Array("ACTIVE" => "Y", "DEF" => "Y"));
	$arSite = $rsSites->Fetch();
	$LID = $arSite["ID"];
}

$urlForm = "";
if (isset($ID) AND $ID != "")
{
	$urlForm = "&ID=".$ID."&LID=".CUtil::JSEscape($LID);
	CSaleOrder::Lock($ID);
}
$tabControl->Begin(array(
		"FORM_ACTION" => $APPLICATION->GetCurPage()."?lang=".LANG.$urlForm
));

//TAB ORDER
$tabControl->BeginNextFormTab();

$tabControl->AddSection("NEWO_TITLE_STATUS", GetMessage("NEWO_TITLE_STATUS"));

$tabControl->BeginCustomField("ORDER_STATUS", GetMessage("SOE_STATUS"), true);
?>
	<tr class="adm-detail-required-field">
		<td width="40%"><?=GetMessage("SOE_STATUS")?>:</td>
		<td width="60%">
			<?
			$arFilter = array("LID" => LANGUAGE_ID);
			$arGroupByTmp = false;

			if ($saleModulePermissions < "W")
			{
				$arFilter["GROUP_ID"] = $GLOBALS["USER"]->GetUserGroupArray();
				$arFilter["PERM_STATUS_FROM"] = "Y";
				if (strlen($str_STATUS_ID) > 0)
					$arFilter["ID"] = $str_STATUS_ID;
				$arGroupByTmp = array("ID", "NAME", "MAX" => "PERM_STATUS_FROM");
			}
			$dbStatusList = CSaleStatus::GetList(
				array(),
				$arFilter,
				$arGroupByTmp,
				false,
				array("ID", "NAME", "SORT")
			);

			if ($dbStatusList->GetNext())
			{
			?>
				<select name="STATUS_ID" id="STATUS_ID">
					<?
					$arFilter = array("LID" => LANG);
					$arGroupByTmp = false;
					if ($saleModulePermissions < "W")
					{
						$arFilter["GROUP_ID"] = $GLOBALS["USER"]->GetUserGroupArray();
						$arFilter["PERM_STATUS"] = "Y";
					}
					$dbStatusListTmp = CSaleStatus::GetList(
						array("SORT" => "ASC"),
						$arFilter,
						$arGroupByTmp,
						false,
						array("ID", "NAME", "SORT")
					);
					while($arStatusListTmp = $dbStatusListTmp->GetNext())
					{
						?><option value="<?echo $arStatusListTmp["ID"] ?>"<?if ($arStatusListTmp["ID"]==$str_STATUS_ID) echo " selected"?>>[<?echo $arStatusListTmp["ID"] ?>] <?echo $arStatusListTmp["NAME"] ?></option><?
					}
					?>
				</select>
				<?
			}
			else
			{
				$arStatusLand = CSaleStatus::GetLangByID($str_STATUS_ID, LANGUAGE_ID);
				echo htmlspecialcharsEx("[".$str_STATUS_ID."] ".$arStatusLand["NAME"]);
			}
			?>
			<input type="hidden" name="user_id" id="user_id" value="<?=$str_USER_ID?>" onChange="fUserGetProfile(this);" >
		</td>
	</tr>
<?
$tabControl->EndCustomField("ORDER_STATUS");

if(IntVal($ID) > 0)
{
	$arSitesShop = array();
	$rsSites = CSite::GetList($by="id", $order="asc", Array("ACTIVE" => "Y"));
	while ($arSite = $rsSites->Fetch())
	{
		$site = COption::GetOptionString("sale", "SHOP_SITE_".$arSite["ID"], "");
		if ($arSite["ID"] == $site)
		{
			$arSitesShop[$arSite["ID"]] = array("ID" => $arSite["ID"], "NAME" => $arSite["NAME"]);
		}
	}

	if (count($arSitesShop) > 1)
	{
		$tabControl->BeginCustomField("ORDER_SITE", GetMessage("ORDER_SITE"), true);
		?>
		<tr>
			<td width="40%">
				<?= GetMessage("ORDER_SITE") ?>:
			</td>
			<td width="60%"><?=htmlspecialcharsbx($arSitesShop[$str_LID]["NAME"])." (".$str_LID.")"?>
			</td>
		</tr>
		<?
		$tabControl->EndCustomField("ORDER_SITE");
	}

	$tabControl->BeginCustomField("ORDER_CANCEL", GetMessage("SOE_CANCELED"), true);
	?>
	<tr>
		<td width="40%">
			<?= GetMessage("SOE_CANCELED") ?>:
		</td>
		<td width="60%">
			<input type="checkbox"<?if (!$bUserCanCancelOrder) echo " disabled";?> name="CANCELED" id="CANCELED" value="Y"<?if ($str_CANCELED == "Y") echo " checked";?>>&nbsp;<label for="CANCELED"><?=GetMessage("SO_YES")?></label>
			<?if(strlen($str_DATE_CANCELED) > 0)
			{
				echo "&nbsp;(".$str_DATE_CANCELED.")";
			}
			?>
		</td>
	</tr>
	<tr>
		<td width="40%" valign="top">
			<?= GetMessage("SOE_CANCEL_REASON") ?>:
		</td>
		<td width="60%" valign="top">
			<textarea name="REASON_CANCELED"<?if (!$bUserCanCancelOrder) echo " disabled";?> rows="2" cols="40"><?= $str_REASON_CANCELED ?></textarea>
		</td>
	</tr>
	<?
	$tabControl->EndCustomField("ORDER_CANCEL");
}

$tabControl->AddSection("NEWO_TITLE_BUYER", GetMessage("NEWO_TITLE_BUYER"));

$tabControl->BeginCustomField("NEWO_BUYER", GetMessage("NEWO_BUYER"), true);
?>

<?if ($ID <= 0):?>
<tr>
	<td width="40%" align="right">
		<a onClick="fButtonCurrent('btnBuyerNew')" href="javascript:void(0);" id="btnBuyerNew" class="adm-btn<?if ($_REQUEST["btnTypeBuyer"] == 'btnBuyerNew' || !isset($_REQUEST["btnTypeBuyer"])) echo ' adm-btn-active';?>"><?=GetMessage("NEWO_BUYER_NEW")?></a>
	</td>
	<td width="60%" align="left"><a onClick="fButtonCurrent('btnBuyerExist')" href="javascript:void(0);" id="btnBuyerExist" class="adm-btn<? if ($_REQUEST["btnTypeBuyer"] == 'btnBuyerExist') echo ' adm-btn-active';?>"><?=GetMessage("NEWO_BUYER_SELECT")?></a>
		<?
		$typeBuyerTmp = "btnBuyerNew";
		if ($bVarsFromForm && isset($_REQUEST["btnTypeBuyer"]))
			$typeBuyerTmp = htmlspecialcharsbx($_REQUEST["btnTypeBuyer"]);
		?>

		<input type="hidden" name="btnTypeBuyer" id="btnTypeBuyer" value="<?=$typeBuyerTmp?>" />
	</td>
</tr>
<?endif?>

<tr>
	<td id="buyer_type_change" colspan="2">
		<?=fGetBuyerType($str_PERSON_TYPE_ID, $LID, $str_USER_ID, $ID, $bVarsFromForm);?>

		<script>
		function fButtonCurrent(el)
		{
			if (el == 'btnBuyerNew')
			{
				BX.removeClass(BX("btnBuyerExist"), 'adm-btn-active');
				BX.addClass(BX("btnBuyerNew"), 'adm-btn-active');

				BX("btnBuyerExistField").style.display = 'none';
				BX("btnBuyerNewField").style.display = 'table-row';
				BX("btnTypeBuyer").value = 'btnBuyerNew';
				BX("buyer_profile_display").style.display = 'none';

				if (BX("BREAK_NAME"))
				{
					BX("BREAK_NAME").style.display = 'block';
					BX("NO_BREAK_NAME").style.display = 'none';
				}
			}
			else if (el == 'btnBuyerExist' || el == 'btnBuyerExistRemote')
			{
				BX.addClass(BX("btnBuyerExist"), 'adm-btn-active');
				BX.removeClass(BX("btnBuyerNew"), 'adm-btn-active');

				BX("btnBuyerExistField").style.display = 'table-row';
				if(BX("btnBuyerNewField"))
					BX("btnBuyerNewField").style.display = 'none';
				if(BX("btnTypeBuyer"))
					BX("btnTypeBuyer").value = 'btnBuyerExist';

				if (BX("BREAK_NAME"))
				{
					BX("BREAK_NAME").style.display = 'none';
					BX("NO_BREAK_NAME").style.display = 'block';
				}

				if (el == 'btnBuyerExist')
					window.open('/bitrix/admin/user_search.php?lang=<?=$lang?>&FN=form_order_buyers_form&FC=user_id', '', 'scrollbars=yes,resizable=yes,width=840,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 840)/2-5));
			}
		}

		var orderID = '<?=$ID?>';
		var orderPaySysyemID = '<?=$str_PAY_SYSTEM_ID?>';

		function fBuyerChangeType(el)
		{
			var userId = "";

			if (BX("user_id").value != "")
				userId = BX("user_id").value;

			BX.showWait();
			BX.ajax.post('/bitrix/admin/sale_order_new.php', '<?=bitrix_sessid_get()?>&ORDER_AJAX=Y&paysystemid=' + orderPaySysyemID + '&ID=' + orderID + '&LID=<?=CUtil::JSEscape($LID)?>&buyertypechange=' + el.value + '&userId=' + userId, fBuyerChangeTypeResult);
		}
		function fBuyerChangeTypeResult(res)
		{
			BX.closeWait();
			var rss = eval( '('+res+')' );

			if (rss["status"] == "ok")
			{
				var userEl = document.getElementById("user_id");
				var orderID = '<?=$ID?>';

				locationID = rss["location_id"];
				locationZipID = rss["location_zip_id"];

				document.getElementById("buyer_type_change").innerHTML = rss["buyertype"];
				document.getElementById("buyer_type_delivery").innerHTML = rss["buyerdelivery"];
				if (userEl.value != "" && (orderID == '' || orderID == 0))
				{
					fUserGetProfile(userEl);
				}
				else
				{
					fRecalProduct('', '', 'N');
				}
			}
		}
		function fChangeProfile(el)
		{
			var userId = document.getElementById("user_id").value;
			var buyerType = document.getElementById("buyer_type_id").value;

			if (userId != "" && buyerType != "")
			{
				fGetExecScript(userId, buyerType, el.value);
			}
			else
			{
				BX.closeWait();
			}
		}
		function fLocationResult(result)
		{
			var res = eval( '('+result+')' );

			if (res["status"] == "ok")
			{
				document.getElementById("LOCATION_CITY_ORDER_PROP_" + res["prop_id"]).innerHTML = res["location"];
				fRecalProduct('', '', 'N');
			}
		}
		//////////
		function fUserGetProfile(el)
		{
			var userId = el.value;
			var buyerType = document.getElementById("buyer_type_id").value;
			document.getElementById("buyer_profile_display").style.display = "none";

			if (userId != "" && buyerType != "")
			{
				BX.showWait();
				BX.ajax.post('/bitrix/admin/sale_order_new.php', '<?=bitrix_sessid_get()?>&ORDER_AJAX=Y&id=<?=$ID?>&LID=<?=CUtil::JSEscape($LID)?>&currency=<?=$str_CURRENCY?>&userId=' + userId + '&buyerType=' + buyerType, fUserGetProfileResult);
			}
		}
		function fUserGetProfileResult(res)
		{
			var rs = eval( '('+res+')' );
			if (rs["status"] == "ok")
			{
				BX.closeWait();
				document.getElementById("buyer_profile_display").style.display = "table-row";
				document.getElementById("buyer_profile_select").innerHTML = rs["userProfileSelect"];
				document.getElementById("user_name").innerHTML = rs["userName"];

				if (rs["viewed"].length > 0)
				{
					document.getElementById("buyer_viewed").innerHTML = rs["viewed"];
					fTabsSelect('buyer_viewed', 'tab_3');
				}
				else
				{
					document.getElementById("buyer_viewed").innerHTML = '';
					BX('tab_3').style.display = "none";
					BX('buyer_viewed').style.display = "none";

					if (BX('tab_1').style.display == "block")
						fTabsSelect('user_recomendet', 'tab_1');
					else if (BX('tab_2').style.display == "block")
						fTabsSelect('user_basket', 'tab_2');

				}
				if (rs["userBasket"].length > 0)
				{
					document.getElementById("user_basket").innerHTML = rs["userBasket"];
					fTabsSelect('user_basket', 'tab_2');
				}
				else
				{
					document.getElementById("user_basket").innerHTML = '';
					BX('tab_2').style.display = "none";
					BX('user_basket').style.display = "none";

					if (BX('tab_1').style.display == "block")
						fTabsSelect('user_recomendet', 'tab_1');
					else if (BX('tab_3').style.display == "block")
						fTabsSelect('buyer_viewed', 'tab_3');

				}
				var profile = document.getElementById("user_profile");
				fChangeProfile(profile);
			}
			else
			{
				BX.closeWait();
			}
		}
		function fGetExecScript(userId, buyerType, profileDefault)
		{
			BX.ajax({
				url: '/bitrix/admin/sale_order_new.php',
				method: 'POST',
				data : '<?=bitrix_sessid_get()?>&ORDER_AJAX=Y&LID=<?=CUtil::JSEscape($LID)?>&userId=' + userId + '&buyerType=' + buyerType + '&profileDefault=' + profileDefault,
				dataType: 'html',
				timeout: 10,
				async: true,
				processData: true,
				scriptsRunFirst: true,
				emulateOnload: true,
				start: true,
				cache: false
			});
			BX.closeWait();
		}
		</script>
	</td>
</tr>
<?
$tabControl->EndCustomField("NEWO_BUYER");

$tabControl->AddSection("BUYER_DELIVERY", GetMessage("SOE_DELIVERY"));

$tabControl->BeginCustomField("DELIVERY_SERVICE", GetMessage("NEWO_DELIVERY_SERVICE"), true);
$arDeliveryOrder = fGetDelivery($locationID, $locationZipID, $productWeight, ($str_PRICE-$str_PRICE_DELIVERY), $str_CURRENCY, $LID, $str_DELIVERY_ID);
?>
	<tr>
		<td class="adm-detail-content-cell-l" width="40%">
			<?=GetMessage("SOE_DELIVERY_COM")?>:
		</td>
		<td width="60%" class="adm-detail-content-cell-r">
			<div id="DELIVERY_SELECT"><?=$arDeliveryOrder["DELIVERY"]; ?></div>
			<div id="DELIVER_ID_DESC"><?=$arDeliveryOrder["DELIVERY_DEFAULT_DESCRIPTION"]?></div>
		</td>
	</tr>
	<tr>
		<td class="adm-detail-content-cell-l">
			<?=GetMessage("SOE_DELIVERY_PRICE")?>:
		</td>
		<td class="adm-detail-content-cell-r">
			<?
				$deliveryPrice = roundEx($str_PRICE_DELIVERY, SALE_VALUE_PRECISION);;

				if ($bVarsFromForm)
					$deliveryPrice = roundEx($PRICE_DELIVERY, SALE_VALUE_PRECISION);
			?>
			<input type="text" onChange="fChangeDeliveryPrice();" name="PRICE_DELIVERY" id="DELIVERY_ID_PRICE" size="10" maxlength="20" value="<?=$deliveryPrice;?>" >
			<input type="hidden" name="change_delivery_price" value="N" id="change_delivery_price">
			<script>
				function fChangeDeliveryPrice()
				{
					document.getElementById("change_delivery_price").value = "Y";
					fRecalProduct('', '', 'N');
				}

				function fChangeDelivery()
				{
					document.getElementById("change_delivery_price").value = "N";
					fRecalProduct('', '', 'N');
				}
			</script>
		</td>
	</tr>
<?
$tabControl->EndCustomField("DELIVERY_SERVICE");

if(IntVal($ID) > 0)
{
	$tabControl->BeginCustomField("ORDER_ALLOW_DELIVERY", GetMessage("SOE_DELIVERY_ALLOWED"), true);
	?>
	<tr>
		<td width="40%">
			<?= GetMessage("SOE_DELIVERY_ALLOWED") ?>:
		</td>
		<td width="60%">
			<input type="checkbox" name="ALLOW_DELIVERY" id="ALLOW_DELIVERY"<?if (!$bUserCanDeliverOrder) echo " disabled";?> value="Y"<?if ($str_ALLOW_DELIVERY == "Y") echo " checked";?>>&nbsp;<label for="ALLOW_DELIVERY"><?=GetMessage("SO_YES")?></label>
			<?if(strlen($str_DATE_ALLOW_DELIVERY) > 0)
			{
				echo "&nbsp;(".$str_DATE_ALLOW_DELIVERY.")";
			}
			?>
		</td>
	</tr>
	<tr>
		<td width="40%">
			<?= GetMessage("SOE_DEL_VOUCHER_NUM") ?>:
		</td>
		<td width="60%">
			<input type="text" name="DELIVERY_DOC_NUM" value="<?= $str_DELIVERY_DOC_NUM ?>" size="20" maxlength="20">
		</td>
	</tr>
	<tr>
		<td width="40%">
			<?= GetMessage("SOE_DEL_VOUCHER_DATE") ?>:
		</td>
		<td width="60%">
			<?= CalendarDate("DELIVERY_DOC_DATE", $str_DELIVERY_DOC_DATE, "form_order_buyers_form", "10", "class=\"typeinput\""); ?>
		</td>
	</tr>
	<?
	$tabControl->EndCustomField("ORDER_ALLOW_DELIVERY");
}

$tabControl->AddSection("BUYER_PAYMENT", GetMessage("SOE_PAYMENT"));

$tabControl->BeginCustomField("BUYER_PAY_SYSTEM", GetMessage("SOE_PAY_SYSTEM"), true);
?>
<tr>
	<td id="buyer_type_delivery" colspan="2">
		<?=fBuyerDelivery($str_PERSON_TYPE_ID, $str_PAY_SYSTEM_ID);?>
	</td>
</tr>
<?
$tabControl->EndCustomField("BUYER_PAY_SYSTEM");

if(IntVal($ID) > 0)
{
	$tabControl->BeginCustomField("ORDER_PAYED", GetMessage("SOE_ORDER_PAID"), true);
	?>
	<tr>
		<td width="40%" valign="top">
			<?= GetMessage("SOE_ORDER_PAID") ?>:
		</td>
		<td width="60%">
			<input type="checkbox"<?if (!$bUserCanPayOrder) echo " disabled";?> name="PAYED" id="PAYED" value="Y"<?if ($str_PAYED == "Y") echo " checked";?> onchange="BX.show(BX('ORDER_PAYED_MORE'))">&nbsp;<label for="PAYED"><?=GetMessage("SO_YES")?></label>
			<?if(strlen($str_DATE_PAYED) > 0)
			{
				echo "&nbsp;(".$str_DATE_PAYED.")";
			}
			?><div id="ORDER_PAYED_MORE" style="display:none;"><?
			$arPayDefault = fGetPayFromAccount($str_USER_ID, $str_CURRENCY);
			if($str_PAYED == "Y")
			{
				?>
				<input type="checkbox" name="PAY_FROM_ACCOUNT_BACK" id="PAY_FROM_ACCOUNT_BACK" value="Y"/>&nbsp;<label for="PAY_FROM_ACCOUNT_BACK"><?=GetMessage('SOD_PAY_ACCOUNT_BACK')?></label>
				<?
			}
			else
			{
				$buyerCanPay = "none";
				if (DoubleVal($arPayDefault["PAY_BUDGET"]) > 0):
					$buyerCanPay = "block";
				endif;
				?>
				<span id="buyerCanBuy" style="display:<?=$buyerCanPay?>">
					<input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y" <?if ($PAY_CURRENT_ACCOUNT == "Y") echo " checked";?><?if (!$bUserCanPayOrder) echo " disabled";?>/>&nbsp;<label for="PAY_CURRENT_ACCOUNT"><?=GetMessage("NEWO_CURRENT_ACCOUNT")?> (<span id="PAY_CURRENT_ACCOUNT_DESC"><?=$arPayDefault["PAY_MESSAGE"]?></span>)</label>
				</span>
				<?
			}
			?>
			</div>
		</td>
	</tr>
	<tr>
		<td width="40%">
			<?= GetMessage("SOE_VOUCHER_NUM") ?>:
		</td>
		<td width="60%">
			<input type="text" name="PAY_VOUCHER_NUM" value="<?= $str_PAY_VOUCHER_NUM ?>" size="20" maxlength="20">
		</td>
	</tr>
	<tr>
		<td width="40%">
			<?= GetMessage("SOE_VOUCHER_DATE") ?>:
		</td>
		<td width="60%">
			<?= CalendarDate("PAY_VOUCHER_DATE", $str_PAY_VOUCHER_DATE, "form_order_buyers_form", "10", "class=\"typeinput\"".((!$bUserCanPayOrder) ? " disabled" : "")); ?>
		</td>
	</tr>
	<?
	$tabControl->EndCustomField("ORDER_PAYED");
}

$tabControl->AddSection("NEWO_COMMENTS", GetMessage("NEWO_COMMENTS"));
$tabControl->BeginCustomField("NEWO_COMMENTS_A", GetMessage("NEWO_COMMENTS"), true);
?>
<tr>
	<td width="40%" valign="top"><?=GetMessage("SOE_COMMENT")?>:<br /><small><?=GetMessage("SOE_COMMENT_NOTE")?></small></td>
	<td width="60%">
		<textarea name="COMMENTS" cols="40" rows="5"><?=$str_COMMENTS?></textarea>
	</td>
</tr>
<?
$tabControl->EndCustomField("NEWO_COMMENTS_A");

$tabControl->BeginCustomField("NEWO_TITLE_ORDER", GetMessage("NEWO_TITLE_ORDER"), true);
?>
<tr>
	<td colspan="2" valign="top">
		<table width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="88%" align="left" class="heading" ><?=GetMessage("NEWO_TITLE_ORDER")?></td>
				<td align="right" nowrap>
					<a title="<?=GetMessage("SOE_ADD_ITEMS")?>" onClick="AddProductSearch(1);" class="adm-btn adm-btn-green adm-btn-add"  style="white-space:nowrap;" href="javascript:void(0);"><?=GetMessage("SOE_ADD_ITEMS")?></a>
				</td>
			</tr>
		</table>
	</td>
</tr>
<?
$tabControl->EndCustomField("NEWO_TITLE_ORDER");

$tabControl->BeginCustomField("BASKET_CONTAINER", GetMessage("NEWO_BASKET_CONTAINER"), true);
?>
<tr>
	<td colspan="2" id="ID_BASKET_CONTAINER">
		<?
		if(!empty($_REQUEST["productDelay"]) || !empty($_REQUEST["productSub"]) || !empty($_REQUEST["productNA"]))
		{
			echo BeginNote();
			echo GetMessage("NEWO_PRODUCTS_MES")."<br />";
			if(!empty($_REQUEST["productSub"]))
			{
				$dbItem = CIBlockElement::GetList(Array(), Array("ID" => $_REQUEST["productSub"]), false, false, Array("ID", "NAME", "IBLOCK_ID", "IBLOCK_SECTION_ID"));
				while($arItem = $dbItem->Fetch())
					echo "<b>"."<a href=\"/bitrix/admin/iblock_element_edit.php?ID=".$arItem["ID"]."&type=catalog&lang=".LANG."&IBLOCK_ID=".$arItem["IBLOCK_ID"]."&find_section_section=".$arItem["IBLOCK_SECTION_ID"]."\">".htmlspecialcharsbx($arItem["NAME"])."</a></b> (".GetMessage("NEWO_PRODUCTS_SUB").")<br />";
			}
			if(!empty($_REQUEST["productDelay"]))
			{
				$dbItem = CIBlockElement::GetList(Array(), Array("ID" => $_REQUEST["productDelay"]), false, false, Array("ID", "NAME", "IBLOCK_ID", "IBLOCK_SECTION_ID"));
				while($arItem = $dbItem->Fetch())
					echo "<b>"."<a href=\"/bitrix/admin/iblock_element_edit.php?ID=".$arItem["ID"]."&type=catalog&lang=".LANG."&IBLOCK_ID=".$arItem["IBLOCK_ID"]."&find_section_section=".$arItem["IBLOCK_SECTION_ID"]."\">".htmlspecialcharsbx($arItem["NAME"])."</a></b> (".GetMessage("NEWO_PRODUCTS_DELAY").")<br />";
			}
			if(!empty($_REQUEST["productNA"]))
			{
				$dbItem = CIBlockElement::GetList(Array(), Array("ID" => $_REQUEST["productNA"]), false, false, Array("ID", "NAME", "IBLOCK_ID", "IBLOCK_SECTION_ID"));
				while($arItem = $dbItem->Fetch())
					echo "<b>"."<a href=\"/bitrix/admin/iblock_element_edit.php?ID=".$arItem["ID"]."&type=catalog&lang=".LANG."&IBLOCK_ID=".$arItem["IBLOCK_ID"]."&find_section_section=".$arItem["IBLOCK_SECTION_ID"]."\">".htmlspecialcharsbx($arItem["NAME"])."</a></b> (".GetMessage("NEWO_PRODUCTS_NA").")<br />";
			}
			echo EndNote();
		}
		?>
		<script language="JavaScript">
			var arProduct = [];
			var arProductEditCountProps = [];
		</script>
		<?
		$arCurFormat = CCurrencyLang::GetCurrencyFormat($str_CURRENCY);
		$CURRENCY_FORMAT = trim(str_replace("#", '', $arCurFormat["FORMAT_STRING"]));

		$arBasketItem = array();
		if ((isset($PRODUCT) AND count($PRODUCT) > 0) AND $bVarsFromForm)
		{
			foreach ($PRODUCT as $key => $val)
			{
				foreach ($val as $k => $v)
				{
					if (!is_array($v))
						$val[$k] = htmlspecialcharsbx($v);
					else
					{
						foreach ($v as $kp => $vp)
						{
							foreach ($vp as $kkp => $vvp)
							{
								$val[$k][$kp][$kkp] = htmlspecialcharsbx($vvp);
							}
						}
					}
				}
				$val["PRODUCT_ID"] = $key;
				$arBasketItem[] = $val;
			}
		}
		elseif (isset($ID) AND $ID > 0)
		{
			$dbBasket = CSaleBasket::GetList(
				array("NAME" => "ASC"),
				array("ORDER_ID" => $ID),
				false,
				false,
				array("ID", "PRODUCT_ID", "PRODUCT_PRICE_ID", "PRICE", "CURRENCY", "WEIGHT", "QUANTITY", "NAME", "MODULE", "CALLBACK_FUNC", "NOTES", "DETAIL_PAGE_URL", "DISCOUNT_PRICE", "DISCOUNT_VALUE", "ORDER_CALLBACK_FUNC", "CANCEL_CALLBACK_FUNC", "PAY_CALLBACK_FUNC", "CATALOG_XML_ID", "PRODUCT_XML_ID", "VAT_RATE")
			);
			while ($arBasket = $dbBasket->GetNext())
			{
				$arBasket["PROPS"] = Array();
				$dbBasketProps = CSaleBasket::GetPropsList(
						array("SORT" => "ASC", "NAME" => "ASC"),
						array("BASKET_ID" => $arBasket["ID"]),
						false,
						false,
						array("ID", "BASKET_ID", "NAME", "VALUE", "CODE", "SORT")
					);
				while ($arBasketProps = $dbBasketProps->GetNext())
				{
					$arBasket["PROPS"][$arBasketProps["ID"]] = $arBasketProps;
				}

				$arBasketItem[$arBasket["ID"]] = $arBasket;
			}
		}

		foreach ($arBasketItem as $key => $val)
		{
			if ($val["MODULE"] == "catalog" && CModule::IncludeModule('catalog'))
			{
				$res = CIBlockElement::GetList(array(), array("ID" => $val["PRODUCT_ID"]), false, false, array('IBLOCK_ID', 'IBLOCK_SECTION_ID'));
				if ($arCat = $res->Fetch())
				{
					if ($arCat["IBLOCK_ID"] > 0 && $arCat["IBLOCK_SECTION_ID"] > 0)
						$arBasketItem[$key]["EDIT_PAGE_URL"] = "/bitrix/admin/iblock_element_edit.php?ID=".$val["PRODUCT_ID"]."&type=catalog&lang=".LANG."&IBLOCK_ID=".$arCat["IBLOCK_ID"]."&find_section_section=".$arCat["IBLOCK_SECTION_ID"];
				}
			}
		}

		$ORDER_TOTAL_PRICE = 0;
		$ORDER_PRICE_WITH_DISCOUNT = 0;
		$productCountAll = 0;
		$productWeight = 0;
		$arFilterRecomendet = array();
		$WEIGHT_UNIT = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', "", $LID));
		$WEIGHT_KOEF = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, $LID));

		$QUANTITY_FACTORIAL = COption::GetOptionString('sale', 'QUANTITY_FACTORIAL', "N");
		if (!isset($QUANTITY_FACTORIAL) OR $QUANTITY_FACTORIAL == "")
			$QUANTITY_FACTORIAL = 'N';

		//edit form props
		$formTemplate = '
					<input id="FORM_BASKET_PRODUCT_ID" name="BASKET_PRODUCT_ID" value="" type="hidden">
					<table class="edit-table" style="background-color:rgb(245, 249, 249); border: 1px solid #B8C1DD; width: 600px;font-size:12px;" >
					<tr style="background-color:rgb(224, 232, 234);color:#525355;font-weight:bold;text-align:center;">
						<td colspan="2" align="center">
						<table width="100%">
						<tr>
							<td align="center">'.GetMessage("SOE_BASKET_EDIT").'</td>
							<td width="10"><a href="javascript:void(0);" onClick="SaleBasketEditTool.PopupHide();" style="color:#525355;float:right;margin-right:5px;font-weight:normal;text-decoration:none;font-size:12px;">&times;<a></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="40%">&nbsp;</td>
						<td align="left" width="60%">
						<div id="basketError" style="display:none;">
							<table class="message message-error" border="0" cellpadding="0" cellspacing="0" style="border:2px solid #FF0000;color:#FF0000">
								<tr>
									<td>
										<table class="content" border="0" cellpadding="0" cellspacing="0" style="margin:4px;">
											<tr>
												<td valign="top"><div class="icon-error"></div></td>
												<td>
													<span class="message-title" style="font-weight:bold;">'.GetMessage("SOE_BASKET_ERROR").'</span><br>
													<div class="empty" style="height: 5px;"></div><div id="basketErrorText"></div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div></td>
					</tr>
					<tr id="FORM_NEWPROD_CODE" class="adm-detail-required-field">
						<td class="field-name" align="right">'.GetMessage("SOE_ITEM_ID").':</td>
						<td><input size="10" id="FORM_PROD_BASKET_ID" name="FORM_PROD_BASKET_ID" type="text" value="" tabindex="1"></td>
					</tr>
					<tr class="adm-detail-required-field">
						<td class="field-name" align="right">'.GetMessage("SOE_ITEM_NAME").':</td>
						<td><input size="40" id="FORM_PROD_BASKET_NAME" name="FORM_PROD_BASKET_NAME" type="text" value="" tabindex="2"></td>
					</tr>
					<tr>
						<td class="field-name" align="right">'.GetMessage("SOE_ITEM_PATH").':</td>
						<td><input id="FORM_PROD_BASKET_DETAIL_URL" name="FORM_BASKET_CATALOG_XML_ID" value="" size="40" type="text" tabindex="3"></td>
					</tr>
					<tr>
						<td class="field-name" align="right">'.GetMessage("SOE_BASKET_CATALOG_XML").':</td>
						<td><input id="FORM_BASKET_CATALOG_XML" name="FORM_BASKET_CATALOG_XML" value="" size="40" type="text" tabindex="4"></td>
					</tr>
					<tr>
						<td class="field-name" align="right">'.GetMessage("SOE_BASKET_PRODUCT_XML").':</td>
						<td><input id="FORM_PROD_BASKET_PRODUCT_XML" name="FORM_PROD_BASKET_PRODUCT_XML" value="" size="40" type="text" tabindex="5"></td>
					</tr>
					<tr>
						<td class="field-name" align="right">'.GetMessage("SOE_ITEM_DESCR").':</td>
						<td><input name="FORM_PROD_BASKET_NOTES" id="FORM_PROD_BASKET_NOTES" size="40" maxlength="250" value="" type="text" tabindex="6"></td>
					</tr>
					<tr>
						<td class="field-name" align="right" valign="top" width="40%">'.GetMessage("SOE_ITEM_PROPS").':</td>
						<td width="60%">
							<table id="BASKET_PROP_TABLE" class="internal" border="0" cellpadding="3" cellspacing="1" style="width: 390px;">
								<tr class="heading" style="border-collapse:collapse;background-color:#E7EAF5;color:#525355;">
									<td align="center">'.GetMessage("SOE_IP_NAME").'</td>
									<td align="center">'.GetMessage("SOE_IP_VALUE").'</td>
									<td align="center">'.GetMessage("SOE_IP_CODE").'</td>
									<td align="center">'.GetMessage("SOE_IP_SORT").'</td>
								</tr>
							</table>

							<input value="'.GetMessage("SOE_PROPERTY_MORE").'" onclick="BasketAddPropSection()" type="button">
						</td>
					</tr>
					<tr>
						<td class="field-name" align="right">'.GetMessage("SALE_F_QUANTITY").':</td>
						<td><input name="FORM_PROD_BASKET_QUANTITY" id="FORM_PROD_BASKET_QUANTITY" size="10" maxlength="20" value="1" type="text" tabindex="7"></td>
					</tr>
					<tr>
						<td class="field-name" align="right">'.GetMessage("SALE_F_PRICE").':</td>
						<td><input name="FORM_PROD_BASKET_PRICE" id="FORM_PROD_BASKET_PRICE" size="10" maxlength="20" value="1" type="text" tabindex="8"> ('.$CURRENCY_FORMAT.')</td>
					</tr>
					<tr>
						<td class="field-name" align="right">'.GetMessage("SOE_WEIGHT").':</td>
						<td><input name="FORM_PROD_BASKET_WEIGHT" id="FORM_PROD_BASKET_WEIGHT" size="10" maxlength="20" value="0" type="text" tabindex="9"> ('.GetMessage("SOE_GRAMM").')</td>
					</tr>
					<tr>
						<td colspan="2" align="center"><br><input name="btn1" value="'.GetMessage("SOE_APPLY").'" onclick="SaveProduct();" type="button"> <input name="btn2" value="'.GetMessage("SALE_CANCEL").'" onclick="SaleBasketEditTool.PopupHide();" type="button"></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					</table>';
		$formTemplate = CUtil::JSEscape($formTemplate);
	?>
	<br>
	<table cellpadding="3" cellspacing="1" border="0" width="100%" class="internal" id="BASKET_TABLE">
		<tr class="heading">
			<td></td>
			<td><?echo GetMessage("SALE_F_PHOTO")?></td>
			<td><?echo GetMessage("SALE_F_NAME")?></td>
			<td><?echo GetMessage("SALE_F_QUANTITY")?></td>
			<td><?echo GetMessage("SALE_F_BALANCE")?></td>
			<td><?echo GetMessage("SALE_F_PROPS")?></td>
			<td><?echo GetMessage("SALE_F_PRICE")?></td>
			<td><?echo GetMessage("SALE_F_SUMMA")?></td>
		</tr>
		<tr></tr>
	<?
	foreach($arBasketItem as $val)
	{
		$productImg = "";
		if (CModule::IncludeModule('iblock'))
		{
			$rsProductInfo = CIBlockElement::GetByID($val["PRODUCT_ID"]);
			$arProductInfo = $rsProductInfo->GetNext();

			if($arProductInfo["PREVIEW_PICTURE"] != "")
				$productImg = $arProductInfo["PREVIEW_PICTURE"];
			elseif($arProductInfo["DETAIL_PICTURE"] != "")
				$productImg = $arProductInfo["DETAIL_PICTURE"];
		}

		if ($productImg != "")
		{
			$arFile = CFile::GetFileArray($productImg);
			$productImg = CFile::ResizeImageGet($arFile, array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);
			$val["PICTURE"] = $productImg;
		}

		$propsProd = "";
		$countProp = 0;
		if (is_array($val["PROPS"]))
		{
			foreach($val["PROPS"] as $valProd)
			{
				$countProp++;
				$propsProd .= "<input type=\"hidden\" name=\"PRODUCT[".$val["PRODUCT_ID"]."][PROPS][".$countProp."][NAME]\" id=\"PRODUCT_PROPS_NAME_".$val["PRODUCT_ID"]."_".$countProp."\" value=\"".$valProd["NAME"]."\" />";
				$propsProd .= "<input type=\"hidden\" name=\"PRODUCT[".$val["PRODUCT_ID"]."][PROPS][".$countProp."][VALUE]\" id=\"PRODUCT_PROPS_VALUE_".$val["PRODUCT_ID"]."_".$countProp."\" value=\"".$valProd["VALUE"]."\" />";
				$propsProd .= "<input type=\"hidden\" name=\"PRODUCT[".$val["PRODUCT_ID"]."][PROPS][".$countProp."][CODE]\" id=\"PRODUCT_PROPS_CODE_".$val["PRODUCT_ID"]."_".$countProp."\" value=\"".$valProd["CODE"]."\" />";
				$propsProd .= "<input type=\"hidden\" name=\"PRODUCT[".$val["PRODUCT_ID"]."][PROPS][".$countProp."][SORT]\" id=\"PRODUCT_PROPS_SORT_".$val["PRODUCT_ID"]."_".$countProp."\" value=\"".$valProd["SORT"]."\" />";
			}
		}
		$val["QUANTITY"] = $QUANTITY_FACTORIAL == 'Y' ? FloatVal($val["QUANTITY"]) : IntVal($val["QUANTITY"]);

		$productCountAll += $val["QUANTITY"];
		$productWeight += ($val["WEIGHT"] * $val["QUANTITY"]);
		$ORDER_TOTAL_PRICE += ($val["PRICE"] + $val["DISCOUNT_PRICE"]) * $val["QUANTITY"];
		$ORDER_PRICE_WITH_DISCOUNT += $val["PRICE"] * $val["QUANTITY"];

		$arFilterRecomendet[] = $val["PRODUCT_ID"];
	?>
		<tr id="BASKET_TABLE_ROW_<?=$val["PRODUCT_ID"]?>" onmouseover="fMouseOver(this);" onmouseout="fMouseOut(this);">
			<td class="action">
				<?
				$arActions = array();
				$arActions[] = array("ICON"=>"view", "TEXT"=>GetMessage("SOE_JS_EDIT"), "ACTION"=>"ShowProductEdit(".$val["PRODUCT_ID"].");", "DEFAULT"=>true);
				$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("SOE_JS_DEL"), "ACTION"=>"DeleteProduct(this, ".$val["PRODUCT_ID"].");fEnableSub();");
				?>
				<div class="adm-list-table-popup" onClick="this.blur();BX.adminList.ShowMenu(this, <?=CUtil::PhpToJsObject($arActions)?>);"></div>
			</td>
			<td class="photo">
				<?if (is_array($val["PICTURE"])):?>
					<img src="<?=$val["PICTURE"]["src"]?>" alt="" width="80" border="0" />
				<?else:?>
					<div class="no_foto"><?=GetMessage('NO_FOTO');?></div>
				<?endif?>
			</td>
			<td class="order_name">
				<div id="product_name_<?=$val["PRODUCT_ID"]?>">
					<?if (strlen($val["EDIT_PAGE_URL"]) > 0):?>
						<a href="<?echo $val["EDIT_PAGE_URL"]?>" target="_blank">
					<?endif?>
						<?=trim($val["NAME"])?>
					<?if (strlen($val["EDIT_PAGE_URL"]) > 0):?>
						</a>
					<?endif?>
				</div>

				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][ID]"                   id="BUSKET_<?=$val["ID"]?>" value="<?=$val["ID"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][CURRENCY]"             id="CURRENCY_<?=$val["PRODUCT_ID"]?>" value="<?=$val["CURRENCY"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][BUSKET_ID]"            id="BUSKET_<?=$val["PRODUCT_ID"]?>" value="<?=$val["ID"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][CALLBACK_FUNC]"        id="CALLBACK_FUNC_<?=$val["PRODUCT_ID"]?>" value="<?=$val["CALLBACK_FUNC"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][ORDER_CALLBACK_FUNC]"  id="ORDER_CALLBACK_FUNC_<?=$val["PRODUCT_ID"]?>" value="<?=$val["ORDER_CALLBACK_FUNC"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][CANCEL_CALLBACK_FUNC]" id="CANCEL_CALLBACK_FUNC_<?=$val["PRODUCT_ID"]?>" value="<?=$val["CANCEL_CALLBACK_FUNC"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][PAY_CALLBACK_FUNC]"    id="PAY_CALLBACK_FUNC_<?=$val["PRODUCT_ID"]?>" value="<?=$val["PAY_CALLBACK_FUNC"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][DISCOUNT_PRICE]"       id="PRODUCT[<?=$val["PRODUCT_ID"]?>][DISCOUNT_PRICE]" value="<?=$val["DISCOUNT_PRICE"]?>" >
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][VAT_RATE]"             id="PRODUCT[<?=$val["PRODUCT_ID"]?>][VAT_RATE]" value="<?=$val["VAT_RATE"]?>" >
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][WEIGHT]"               id="PRODUCT[<?=$val["PRODUCT_ID"]?>][WEIGHT]" value="<?=$val["WEIGHT"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][MODULE]"               id="PRODUCT[<?=$val["PRODUCT_ID"]?>][MODULE]" value="<?=$val["MODULE"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][NOTES]"                id="PRODUCT[<?=$val["PRODUCT_ID"]?>][NOTES]" value="<?=$val["NOTES"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][CATALOG_XML_ID]"       id="PRODUCT[<?=$val["PRODUCT_ID"]?>][CATALOG_XML_ID]" value="<?=$val["CATALOG_XML_ID"]?>" >
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][PRODUCT_XML_ID]"       id="PRODUCT[<?=$val["PRODUCT_ID"]?>][PRODUCT_XML_ID]" value="<?=$val["PRODUCT_XML_ID"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][MODULE]"               id="PRODUCT[<?=$val["PRODUCT_ID"]?>][MODULE]" value="<?=$val["MODULE"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][DETAIL_PAGE_URL]"      id="PRODUCT[<?=$val["PRODUCT_ID"]?>][DETAIL_PAGE_URL]" value="<?=$val["DETAIL_PAGE_URL"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][NAME]"                 id="PRODUCT[<?=$val["PRODUCT_ID"]?>][NAME]" value="<?=  $val["NAME"]?>" />
				<input type="hidden" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][PRICE_DEFAULT]"        id="PRODUCT[<?=$val["PRODUCT_ID"]?>][PRICE_DEFAULT]" value="<?=  $val["PRICE"]?>" />
				<span id="product_props_<?=$val["PRODUCT_ID"]?>"><?=$propsProd?></span>
				<script language="JavaScript">
					arProduct[<?=$val["PRODUCT_ID"]?>] = '<?=$val["PRODUCT_ID"]?>';
					arProductEditCountProps[<?=$val["PRODUCT_ID"]?>] = <?=$countProp?>;
				</script>
			</td>
			<td id="DIV_QUANTITY_<?=$val["PRODUCT_ID"]?>" class="order_count">
				<div>
					<input maxlength="7" onChange="fRecalProduct(<?=$val["PRODUCT_ID"]?>, '', 'N');" type="text" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][QUANTITY]" id="PRODUCT[<?=$val["PRODUCT_ID"]?>][QUANTITY]" value="<?=$val["QUANTITY"]?>" size="4" >
				</div>
			</td>
			<td class="balance_count">
				<?
				$balance = "0";
				if ($val["MODULE"] == "catalog" && CModule::IncludeModule('catalog'))
				{
					$ar_res = CCatalogProduct::GetByID($val["PRODUCT_ID"]);
					$balance = FloatVal($ar_res["QUANTITY"]);
				}
				?>
				<div id="DIV_BALANCE_<?=$val["PRODUCT_ID"]?>"><?=$balance?></div>
			</td>
			<td class="props">
				<div id="PRODUCT_PROPS_USER_<?=$val["PRODUCT_ID"]?>">
				<?
				if (is_array($val["PROPS"]))
				{
					foreach($val["PROPS"] as $vv)
					{
						if(strlen($vv["VALUE"]) > 0 && $vv["CODE"] != "CATALOG.XML_ID" && $vv["CODE"] != "PRODUCT.XML_ID")
							echo $vv["NAME"].": ".$vv["VALUE"]."<br />";
					}
				}
				?>
				</div>
			</td>
			<td class="order_price" nowrap>
				<?
				$priceBase = ($val["DISCOUNT_PRICE"] + $val["PRICE"]);
				$priceDiscount = 0;
				if ($priceBase > 0)
					$priceDiscount = IntVal(($val["DISCOUNT_PRICE"] * 100) / $priceBase);
				?>
				<div id="DIV_PRICE_<?=$val["PRODUCT_ID"]?>" class="edit_price">
					<span class="default_price_product" id="default_price_<?=$val["PRODUCT_ID"]?>"><span class="formated_price" id="formated_price_<?=$val["PRODUCT_ID"]?>" onclick="fEditPrice(<?=$val["PRODUCT_ID"]?>, 'on');"><?=CurrencyFormatNumber($val["PRICE"], $str_CURRENCY);?></span></span><span class="edit_price_product" id="edit_price_<?=$val["PRODUCT_ID"]?>">
						<input maxlength="9" onchange="fRecalProduct('<?=$val["PRODUCT_ID"]?>', 'price', 'N');" onblur="fEditPrice('<?=$val["PRODUCT_ID"]?>', 'exit');" type="text" name="PRODUCT[<?=$val["PRODUCT_ID"]?>][PRICE]" id="PRODUCT[<?=$val["PRODUCT_ID"]?>][PRICE]" value="<?=FloatVal($val["PRICE"])?>" size="5" >
					</span><span id='currency_price_product' class='currency_price'><?=$CURRENCY_FORMAT?></span>
					<a href="javascript:void(0);" onclick="fEditPrice(<?=$val["PRODUCT_ID"]?>, 'on');"><span class="pencil"></span></a>
				</div>
				<div id="DIV_PRICE_OLD_<?=$val["PRODUCT_ID"]?>" class="base_price" style="display:none;"><?=CurrencyFormatNumber($val["PRICE"] + $val["DISCOUNT_PRICE"], $str_CURRENCY);?> <span><?=$CURRENCY_FORMAT?></span></div>

				<?if ($priceDiscount > 0):?>
					<div class="base_price" id="DIV_BASE_PRICE_WITH_DISCOUNT_<?=$val["PRODUCT_ID"]?>"><?=CurrencyFormatNumber($priceBase, $str_CURRENCY);?> <span><?=$CURRENCY_FORMAT?></span></div>
					<div class="discount" id="DIV_DISCOUNT_<?=$val["PRODUCT_ID"]?>">(<?=getMessage('NEWO_PRICE_DISCOUNT')." ".$priceDiscount?>%)</div>
				<?endif;?>
				<div class="base_price_title"><?=GetMessage('NEWO_DASE_PRICE');?></div>
			</td>
			<td id="DIV_SUMMA_<?=$val["PRODUCT_ID"]?>" class="product_summa" nowrap>
				<div><?=CurrencyFormatNumber(($val["QUANTITY"] * $val["PRICE"]), $str_CURRENCY);?> <span><?=$CURRENCY_FORMAT?></span></div>
			</td>
		</tr>
	<?
	}//end foreach $arBasketItem
	if ($ORDER_TOTAL_PRICE == $ORDER_PRICE_WITH_DISCOUNT)
		$ORDER_PRICE_WITH_DISCOUNT = 0;
	?>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" align="left" colspan="2">
		<br>
		<div class="set_cupon">
			<?=GetMessage("NEWO_BASKET_CUPON")?>:
			<input type="text" name="CUPON" id="CUPON" value="<?=htmlspecialcharsbx($CUPON)?>" />
			<a href="javascript:void(0)" onClick="fRecalProduct('', '', 'N');"><?=GetMessage("NEWO_CUPON_RECALC")?></a>
			<div><?=GetMessage("NEWO_CUPON_DESC")?></div>

		</div>
		<div style="float:right">
			<script>
				function fMouseOver(el)
				{
					el.className = 'tr_hover';
				}
				function fMouseOut(el)
				{
					el.className = '';
				}
				function fEditPrice(item, type)
				{
					if (type == 'on')
					{
						BX('DIV_PRICE_' + item).className = 'edit_price edit_enable';
						BX('PRODUCT['+item+'][PRICE]').focus();
					}
					if (type == 'exit')
					{
						BX('DIV_PRICE_' + item).className = 'edit_price';
					}
				}
				function AddProductSearch(index)
				{
					var quantity = 1;
					var BUYER_ID = document.form_order_buyers_form.user_id.value;
					var BUYER_CUPONS = document.getElementById("CUPON").value;

					window.open('/bitrix/admin/sale_product_search.php?lang=<?=LANGUAGE_ID?>&LID=<?=CUtil::JSEscape($LID)?>&func_name=FillProductFields&index=' + index + '&QUANTITY=' + quantity + '&BUYER_ID=' + BUYER_ID + '&BUYER_COUPONS=' + BUYER_CUPONS, '', 'scrollbars=yes,resizable=yes,width=840,height=550,top='+parseInt((screen.height - 500)/2-14)+',left='+parseInt((screen.width - 840)/2-5));
				}
			</script>
			<?
			$productAddBool = COption::GetOptionString('sale', 'SALE_ADMIN_NEW_PRODUCT', 'N');
			?>
			<?if ($productAddBool == "Y"):?>
				<a title="<?=GetMessage("SOE_NEW_ITEMS")?>" onClick="ShowProductEdit('', 'Y');" class="adm-btn adm-btn-green" href="javascript:void(0);"><?=GetMessage("SOE_NEW_ITEMS")?></a>
			<?endif;?>
			<a title="<?=GetMessage("SOE_ADD_ITEMS")?>" onClick="AddProductSearch(1);" class="adm-btn adm-btn-green adm-btn-add" href="javascript:void(0);"><?=GetMessage("SOE_ADD_ITEMS")?></a>
		</div>

<script language="JavaScript">
	var currencyBase = '<?=CSaleLang::GetLangCurrency($LID);?>';
	var orderWeight = '<?=$productWeight?>';
	var orderPrice = '<?=$str_PRICE?>';

	window.onload = function () {
		<?
		if ($bVarsFromForm)
		{
			echo "fRecalProduct('', '', 'N');";
		}
		?>
	};
	function fEnableSub()
	{
		if (document.getElementById('tbl_sale_order_edit'))
			document.getElementById('tbl_sale_order_edit').style.zIndex  = 10000;
	}
	function pJCFloatDiv()
	{
		var _this = this;
		this.floatDiv = null;
		this.x = this.y = 0;

		this.Show = function(div, left, top)
		{
			var zIndex = parseInt(div.style.zIndex);
			if(zIndex <= 0 || isNaN(zIndex))
				zIndex = 1100;
			div.style.zIndex = zIndex;
			div.style.left = left + "px";
			div.style.top = top + "px";

			if(jsUtils.IsIE())
			{
				var frame = document.getElementById(div.id+"_frame");
				if(!frame)
				{
					frame = document.createElement("IFRAME");
					frame.src = "javascript:''";
					frame.id = div.id+"_frame";
					frame.style.position = 'absolute';
					frame.style.zIndex = zIndex-1;
					document.body.appendChild(frame);
				}
				frame.style.width = div.offsetWidth + "px";
				frame.style.height = div.offsetHeight + "px";
				frame.style.left = div.style.left;
				frame.style.top = div.style.top;
				frame.style.visibility = 'visible';
			}
		}
		this.Close = function(div)
		{
			if(!div)
				return;
			var frame = document.getElementById(div.id+"_frame");
			if(frame)
				frame.style.visibility = 'hidden';
		}
	}
	var pjsFloatDiv = new pJCFloatDiv();

	function SaleBasketEdit()
	{
		var _this = this;
		this.active = null;

		this.PopupShow = function(div, pos)
		{
			this.PopupHide();
			if(!div)
				return;
			if (typeof(pos) != "object")
				pos = {};

			this.active = div.id;
			div.ondrag = jsUtils.False;

			jsUtils.addEvent(document, "keypress", _this.OnKeyPress);

			div.style.width = div.offsetWidth + 'px';
			div.style.visibility = 'visible';

			var res = jsUtils.GetWindowSize();
			pos['top'] = parseInt(res["scrollTop"] + res["innerHeight"]/2 - div.offsetHeight/2);
			pos['left'] = parseInt(res["scrollLeft"] + res["innerWidth"]/2 - div.offsetWidth/2);
			if(pos['top'] < 5)
				pos['top'] = 5;
			if(pos['left'] < 5)
				pos['left'] = 5;

			pjsFloatDiv.Show(div, pos["left"], pos["top"]);
		}

		this.PopupHide = function()
		{
			var div = document.getElementById(_this.active);
			if(div)
			{
				pjsFloatDiv.Close(div);
				div.parentNode.removeChild(div);
			}
			this.active = null;
			jsUtils.removeEvent(document, "keypress", _this.OnKeyPress);
		}

		this.OnKeyPress = function(e)
		{
			if(!e) e = window.event
			if(!e) return;
			if(e.keyCode == 27)
				_this.PopupHide();
		},

		this.IsVisible = function()
		{
			return (document.getElementById(this.active).style.visibility != 'hidden');
		}
	}

	check_ctrl_enter = function(e)
	{
		if(!e)
			e = window.event;

		if((e.keyCode == 13 || e.keyCode == 10) && e.ctrlKey)
		{
			alert('submit!');
		}
	}
	SaleBasketEditTool = new SaleBasketEdit();

	function ShowProductEdit(id, newElement)
	{
		var div = document.createElement("DIV");
		div.id = "product_edit";
		div.style.visible = 'hidden';
		div.style.position = 'absolute';
		div.innerHTML = '<?=$formTemplate?>';

		document.body.appendChild(div);
		SaleBasketEditTool.PopupShow(div);

		if (id != "")
		{
			document.getElementById('FORM_NEWPROD_CODE').style.display = 'none'
			document.getElementById('FORM_BASKET_PRODUCT_ID').value = id;
			document.getElementById('FORM_PROD_BASKET_ID').value = id;
			document.getElementById('FORM_PROD_BASKET_NAME').value = document.getElementById('PRODUCT[' + id + '][NAME]').value;
			document.getElementById('FORM_PROD_BASKET_DETAIL_URL').value = document.getElementById('PRODUCT[' + id + '][DETAIL_PAGE_URL]').value;
			document.getElementById('FORM_PROD_BASKET_NOTES').value = document.getElementById('PRODUCT[' + id + '][NOTES]').value;
			document.getElementById('FORM_BASKET_CATALOG_XML').value = document.getElementById('PRODUCT[' + id + '][CATALOG_XML_ID]').value;
			document.getElementById('FORM_PROD_BASKET_PRODUCT_XML').value = document.getElementById('PRODUCT[' + id + '][PRODUCT_XML_ID]').value;
			document.getElementById('FORM_PROD_BASKET_PRICE').value = document.getElementById('PRODUCT[' + id + '][PRICE]').value;
			document.getElementById('FORM_PROD_BASKET_WEIGHT').value = document.getElementById('PRODUCT[' + id + '][WEIGHT]').value;
			document.getElementById('FORM_PROD_BASKET_QUANTITY').value = document.getElementById('PRODUCT[' + id + '][QUANTITY]').value;
		}
		if (id != "" && arProductEditCountProps[id])
		{
			propCnt = parseInt(arProductEditCountProps[id]);
			for (i=1; i <= propCnt; i++)
			{
				if(document.getElementById("PRODUCT_PROPS_NAME_" + id + "_" + i))
				{
					nameProp = document.getElementById("PRODUCT_PROPS_NAME_" + id + "_" + i).value;
					codeProp = document.getElementById("PRODUCT_PROPS_CODE_" + id + "_" + i).value;
					valueProp = document.getElementById("PRODUCT_PROPS_VALUE_" + id + "_" + i).value;
					sortProp = document.getElementById("PRODUCT_PROPS_SORT_" + id + "_" + i).value;

					BasketAddPropSection(i, nameProp, codeProp, valueProp, sortProp);
				}
			}
		}
		else if (id != "")
			arProductEditCountProps[id] = 0;
	}

	function SaveProduct()
	{
		var error = '';

		prod_id = document.getElementById('FORM_PROD_BASKET_ID').value;
		prod_id = parseInt(prod_id);

		if(prod_id.length <= 0 || isNaN(prod_id))
			error += '<?=GetMessage("SOE_NEW_ERR_PROD_ID")?><br />';
		if(document.getElementById('FORM_PROD_BASKET_NAME').value.length <= 0)
			error += '<?=GetMessage("SOE_NEW_ERR_PROD_NAME")?><br />';

		if(error.length > 0)
		{
			document.getElementById('basketError').style.display = 'block';
			document.getElementById('basketErrorText').innerHTML = error;
		}
		else
		{
			if (!arProductEditCountProps[prod_id])
				arProductEditCountProps[prod_id] = 0;
			propCnt = parseInt(arProductEditCountProps[prod_id]);

			var propsHTML = "";
			var props = "";
			if(propCnt > 0)
			{
				for (i=1; i <= propCnt; i++)
				{
					if (document.getElementById('FORM_PROD_PROP_' + prod_id + '_NAME_' + i))
					{
						propName = BX.util.htmlspecialchars(document.getElementById('FORM_PROD_PROP_' + prod_id + '_NAME_' + i).value);
						propCode = BX.util.htmlspecialchars(document.getElementById('FORM_PROD_PROP_' + prod_id + '_CODE_' + i).value);
						propValue = BX.util.htmlspecialchars(document.getElementById('FORM_PROD_PROP_' + prod_id + '_VALUE_' + i).value);
						propSort = BX.util.htmlspecialchars(document.getElementById('FORM_PROD_PROP_' + prod_id + '_SORT_' + i).value);

						if (propName != "" && propValue != "")
						{
							//basket visible props
							if(document.getElementById('FORM_PROD_PROP_' + prod_id + '_NAME_' + i).value.length > 0)
							{
								if(propCode != "CATALOG.XML_ID" && propCode != "PRODUCT.XML_ID")
									propsHTML += propName + ': ' + propValue + '<br />';
							}

							props += "<input type=\"hidden\" name=\"PRODUCT[" + prod_id + "][PROPS]["+i+"][NAME]\" id=\"PRODUCT_PROPS_NAME_" + prod_id + "_" + i + "\" value=\"" + propName + "\" />";
							props += "<input type=\"hidden\" name=\"PRODUCT[" + prod_id + "][PROPS]["+i+"][CODE]\" id=\"PRODUCT_PROPS_CODE_" + prod_id + "_" + i + "\" value=\"" + propCode + "\" />";
							props += "<input type=\"hidden\" name=\"PRODUCT[" + prod_id + "][PROPS]["+i+"][VALUE]\" id=\"PRODUCT_PROPS_VALUE_" + prod_id + "_" + i + "\" value=\"" + propValue + "\" />";
							props += "<input type=\"hidden\" name=\"PRODUCT[" + prod_id + "][PROPS]["+i+"][SORT]\" id=\"PRODUCT_PROPS_SORT_" + prod_id + "_" + i + "\" value=\"" + propSort + "\" />";
						}
						else
						{
							arProductEditCountProps[prod_id] = propCnt - 1;
						}
					}
				}

				if (document.getElementById('PRODUCT_PROPS_USER_' + prod_id))//&& propsHTML.length > 0
				{
					document.getElementById('PRODUCT_PROPS_USER_' + prod_id).innerHTML = propsHTML;
					document.getElementById('product_props_' + prod_id).innerHTML = props;
				}
			}

			if (document.getElementById('FORM_BASKET_PRODUCT_ID').value != "")
			{
				document.getElementById('PRODUCT[' + prod_id + '][DETAIL_PAGE_URL]').value = document.getElementById('FORM_PROD_BASKET_DETAIL_URL').value;
				document.getElementById('product_name_' + prod_id).innerHTML = BX.util.htmlspecialchars(document.getElementById('FORM_PROD_BASKET_NAME').value);
				document.getElementById('PRODUCT[' + prod_id + '][NAME]').value = document.getElementById('FORM_PROD_BASKET_NAME').value;
				document.getElementById('PRODUCT[' + prod_id + '][NOTES]').value = document.getElementById('FORM_PROD_BASKET_NOTES').value;
				document.getElementById('PRODUCT[' + prod_id + '][CATALOG_XML_ID]').value = document.getElementById('FORM_BASKET_CATALOG_XML').value;
				document.getElementById('PRODUCT[' + prod_id + '][PRODUCT_XML_ID]').value = document.getElementById('FORM_PROD_BASKET_PRODUCT_XML').value;
				document.getElementById('PRODUCT[' + prod_id + '][QUANTITY]').value = document.getElementById('FORM_PROD_BASKET_QUANTITY').value;

				if (document.getElementById('PRODUCT[' + prod_id + '][PRICE]').value != document.getElementById('FORM_PROD_BASKET_PRICE').value)
				{
					document.getElementById('PRODUCT[' + prod_id + '][PRICE]').value = document.getElementById('FORM_PROD_BASKET_PRICE').value;
					document.getElementById('CALLBACK_FUNC_' + prod_id).value = "Y";
				}

				if (document.getElementById('PRODUCT[' + prod_id + '][WEIGHT]').value != document.getElementById('FORM_PROD_BASKET_WEIGHT').value)
				{
					document.getElementById('PRODUCT[' + prod_id + '][WEIGHT]').value = document.getElementById('FORM_PROD_BASKET_WEIGHT').value;
					document.getElementById('CALLBACK_FUNC_' + prod_id).value = "Y";
				}
			}
			else
			{
				var arParamsTmp = [];
				arParamsTmp['id'] = prod_id;
				arParamsTmp['name'] = document.getElementById('FORM_PROD_BASKET_NAME').value;
				arParamsTmp['price'] = document.getElementById('FORM_PROD_BASKET_PRICE').value;
				arParamsTmp['priceFormated'] = document.getElementById('FORM_PROD_BASKET_PRICE').value;
				arParamsTmp['summaFormated'] = 1;
				arParamsTmp['priceType'] = document.getElementById('FORM_PROD_BASKET_NOTES').value;
				arParamsTmp['priceDiscount'] = 0;
				arParamsTmp['quantity'] = document.getElementById('FORM_PROD_BASKET_QUANTITY').value;
				arParamsTmp['url'] = document.getElementById('FORM_PROD_BASKET_DETAIL_URL').value;
				arParamsTmp['urlImg'] = '';
				arParamsTmp['vatRate'] = 0;
				arParamsTmp['weight'] = document.getElementById('FORM_PROD_BASKET_WEIGHT').value;
				arParamsTmp['currency'] = '<?=$str_CURRENCY?>';
				arParamsTmp['module'] = '';
				arParamsTmp['urlEdit'] = '';
				arParamsTmp['callback'] = '';
				arParamsTmp['orderCallback'] = '';
				arParamsTmp['cancelCallback'] = '';
				arParamsTmp['payCallback'] = '';
				arParamsTmp['catalogXmlID'] = document.getElementById('FORM_BASKET_CATALOG_XML').value;
				arParamsTmp['productXmlID'] = document.getElementById('FORM_PROD_BASKET_PRODUCT_XML').value;

				FillProductFields('', arParamsTmp, '');

				if (propsHTML.length > 0)
				{
					document.getElementById('PRODUCT_PROPS_USER_' + prod_id).innerHTML = propsHTML;
					document.getElementById('product_props_' + prod_id).innerHTML = props;
				}
			}
			fRecalProduct('', '', 'N');
			SaleBasketEditTool.PopupHide();
		}
	}

	function BasketAddPropSection(id, nameProp, codeProp, valueProp, sortProp)
	{
		var error = '';

		if (!nameProp)
			nameProp = "";
		if (!codeProp)
			codeProp = "";
		if (!valueProp)
			valueProp = "";
		if (!sortProp)
			sortProp = "";
		if (!id)
			id = "";

		prod_id = document.getElementById('FORM_PROD_BASKET_ID').value;
		prod_id = parseInt(prod_id);

		if(prod_id.length <= 0 || isNaN(prod_id))
			error += '<?=GetMessage("SOE_NEW_ERR_PROD_ID")?><br />';

		if(error.length > 0)
		{
			document.getElementById('basketError').style.display = 'block';
			document.getElementById('basketErrorText').innerHTML = error;
		}
		else
		{
			if (id == '')
			{
				if (!arProductEditCountProps[prod_id])
					arProductEditCountProps[prod_id] = 0;

				countProp = parseInt(arProductEditCountProps[prod_id]);
				countProp = countProp + 1;
				arProductEditCountProps[prod_id] = countProp;
			}
			else
			{
				countProp = id;
			}

			var oTbl = document.getElementById("BASKET_PROP_TABLE");
			if (!oTbl)
				return;
			var oRow = oTbl.insertRow(-1);
			var oCell = oRow.insertCell(-1);
			oCell.innerHTML = '<input type="text" maxlength="250" size="20" name="FORM_PROD_PROP_' + prod_id + '_NAME_' + countProp + '" id="FORM_PROD_PROP_' + prod_id + '_NAME_' + countProp + '" value="'+BX.util.htmlspecialchars(nameProp)+'" />';
			var oCell = oRow.insertCell(-1);
			oCell.innerHTML = '<input type="text" maxlength="250" size="20" name="FORM_PROD_PROP_' + prod_id + '_VALUE_' + countProp + '" id="FORM_PROD_PROP_' + prod_id + '_VALUE_' + countProp + '" value="'+BX.util.htmlspecialchars(valueProp)+'" />';
			var oCell = oRow.insertCell(-1);
			oCell.innerHTML = '<input type="text" maxlength="250" size="3" name="FORM_PROD_PROP_' + prod_id + '_CODE_' + countProp + '" id="FORM_PROD_PROP_' + prod_id + '_CODE_' + countProp + '" value="'+BX.util.htmlspecialchars(codeProp)+'" />';
			var oCell = oRow.insertCell(-1);
			oCell.innerHTML = '<input type="text" maxlength="10" size="2" name="FORM_PROD_PROP_' + prod_id + '_SORT_' + countProp + '" id="FORM_PROD_PROP_' + prod_id + '_SORT_' + countProp + '" value="'+BX.util.htmlspecialchars(sortProp)+'" />';
		}
	}

	function FillProductFields(index, arParams, iblockID)
	{
		var ID = arParams['id'];

		if (!BX('BASKET_TABLE_ROW_' + ID))
		{
			var oTbl = document.getElementById("BASKET_TABLE");
			if (!oTbl)
				return;

			var oRow = oTbl.insertRow(1);
			oRow.setAttribute('id','BASKET_TABLE_ROW_' + ID);
			//oRow.setAttribute('style','font-style:italic');
			oRow.setAttribute('onmouseout','fMouseOut(this);');
			oRow.setAttribute('onmouseover','fMouseOver(this);');

			var oCellAction = oRow.insertCell(-1);
				oCellAction.setAttribute('class', 'action');
			var oCellPhoto = oRow.insertCell(-1);
				oCellPhoto.setAttribute('class','photo');
			var oCellName = oRow.insertCell(-1);
				oCellName.setAttribute('class','order_name');
			var oCellQuantity = oRow.insertCell(-1);
				oCellQuantity.setAttribute('class','order_count');
				oCellQuantity.setAttribute('id','DIV_QUANTITY_' + ID);
			var oCellBalance = oRow.insertCell(-1);
				oCellBalance.setAttribute('class','balance_count');
			var oCellPROPS = oRow.insertCell(-1);
				oCellPROPS.setAttribute('class','props');
			var oCellPrice = oRow.insertCell(-1);
				oCellPrice.setAttribute('class','order_price');
				oCellPrice.setAttribute('align','center');
				oCellPrice.setAttribute('nowrap','nowrap');
			var oCellSumma = oRow.insertCell(-1);
				oCellSumma.setAttribute('id','DIV_SUMMA_' + ID);
				oCellSumma.setAttribute('class','product_summa');
				oCellSumma.setAttribute('nowrap','nowrap');

			for (key in arParams)
			{
				if (key == "id")
				{
					ID = arParams[key];
				}
				if (key == "name")
				{
					var name = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "price")
				{
					var price = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "priceFormated")
				{
					var priceFormated = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "priceBase")
				{
					var priceBase = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "priceBaseFormat")
				{
					var priceBaseFormat = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "priceType")
				{
					var priceType = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "currency")
				{
					var currency = arParams[key];
				}
				else if (key == "priceDiscount")
				{
					var priceDiscount = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "quantity")
				{
					var quantity = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "summaFormated")
				{
					var summaFormated = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "weight")
				{
					var weight = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "vatRate")
				{
					var vatRate = arParams[key];
				}
				else if (key == "module")
				{
					var module = arParams[key];
				}
				else if (key == "valutaFormat")
				{
					var valutaFormat = arParams[key];
				}
				else if (key == "catalogXmlID")
				{
					var catalogXmlID = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "productXmlID")
				{
					var productXmlID = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "url")
				{
					var url = BX.util.htmlspecialchars(arParams[key]);
				}
				else if (key == "urlImg")
				{
					var urlImg = arParams[key];
				}
				else if (key == "urlEdit")
				{
					var urlEdit = arParams[key];
				}
				else if (key == "balance")
				{
					var balance = arParams[key];
				}
				else if (key == "priceTotalFormated")
				{
					var priceTotalFormated = arParams[key];
				}
				else if (key == "discountPercent")
				{
					var discountPercent = arParams[key];
				}
				else if (key == "callback")
				{
					var callback = arParams[key];
				}
				else if (key == "orderCallback")
				{
					var orderCallback = arParams[key];
				}
				else if (key == "cancelCallback")
				{
					var cancelCallback = arParams[key];
				}
				else if (key == "payCallback")
				{
					var payCallback = arParams[key];
				}
			}

			var hiddenField = "<div id=\"product_name_" + ID + "\">";

			if (urlEdit.length > 0)
				hiddenField = hiddenField + "<a href=\""+urlEdit+"\" target=\"_blank\">";
			hiddenField = hiddenField + name;
			if (urlEdit.length > 0)
				hiddenField = hiddenField + "</a>";
			hiddenField = hiddenField + "</div>";

			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][CALLBACK_FUNC]\" id=\"CALLBACK_FUNC_" + ID + "\" value=\"" + callback + "\" />\n";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][ORDER_CALLBACK_FUNC]\" id=\"ORDER_CALLBACK_FUNC_" + ID + "\" value=\"" + orderCallback + "\" />\n";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][CANCEL_CALLBACK_FUNC]\" id=\"CANCEL_CALLBACK_FUNC_" + ID + "\" value=\"" + cancelCallback + "\" />\n";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][PAY_CALLBACK_FUNC]\" id=\"PAY_CALLBACK_FUNC_" + ID + "\" value=\"" + payCallback + "\" />\n";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][CURRENCY]\" id=\"CURRENCY_" + ID + "\" value=\"" + currency + "\" />";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][DISCOUNT_PRICE]\" id=\"PRODUCT[" + ID + "][DISCOUNT_PRICE]\" value=\"" + priceDiscount + "\" />";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][WEIGHT]\" id=\"PRODUCT[" + ID + "][WEIGHT]\" value=\"" + weight + "\" />";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][VAT_RATE]\" id=\"PRODUCT[" + ID + "][VAT_RATE]\" value=\"" + vatRate + "\" />";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][MODULE]\" id=\"PRODUCT[" + ID + "][MODULE]\" value=\"" + module + "\" />";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"BUSKET_" +  ID + "\" id=\"BUSKET_" + ID + "\" value=\"\" />";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][NOTES]\" id=\"PRODUCT[" + ID + "][NOTES]\" value=\"" + priceType + "\" />";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][CATALOG_XML_ID]\" id=\"PRODUCT[" + ID + "][CATALOG_XML_ID]\" value=\"" + catalogXmlID + "\" />";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][PRODUCT_XML_ID]\" id=\"PRODUCT[" + ID + "][PRODUCT_XML_ID]\" value=\"" + productXmlID + "\" />";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][DETAIL_PAGE_URL]\" id=\"PRODUCT[" + ID + "][DETAIL_PAGE_URL]\" value=\"" + url + "\" />";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][NAME]\" id=\"PRODUCT[" + ID + "][NAME]\" value=\"" + name + "\" />";
			hiddenField = hiddenField + "<input type=\"hidden\" name=\"PRODUCT[" + ID + "][PRICE_DEFAULT]\" id=\"PRODUCT[" + ID + "][PRICE_DEFAULT]\" value=\"" + priceBase + "\" />";
			hiddenField = hiddenField + "<span id=\"product_props_" + ID + "\"></span>";

			var imgSrc = "&nbsp;";
			if (urlImg != "")
				imgSrc = "<img src=\""+urlImg+"\" alt=\"\" width=\"80\" border=\"0\" />";
			else
				imgSrc = "<div class='no_foto'><?=GetMessage('NO_FOTO');?></div>";

			var actonHtml = "<div onclick=\"this.blur();BX.adminList.ShowMenu(this, ";
			actonHtml = actonHtml + "[{'ICON':'view','TEXT':'<?=GetMessage("SOE_JS_EDIT")?>','ACTION':'ShowProductEdit("+ID+");','DEFAULT':true},{'ICON':'delete','TEXT':'<?=GetMessage("SOE_JS_DEL")?>','ACTION':'DeleteProduct(this, "+ID+");fEnableSub();'}]);\" class=\"adm-list-table-popup\"></div>";

			oCellAction.innerHTML = actonHtml;
			oCellPhoto.innerHTML = imgSrc;
			oCellName.innerHTML = hiddenField;
			oCellQuantity.innerHTML = "<div><input maxlength=\"7\" onChange=\"fRecalProduct(" + ID + ", '', 'N');\" type=\"text\" name=\"PRODUCT[" + ID + "][QUANTITY]\" id=\"PRODUCT[" + ID + "][QUANTITY]\" value=\"" + quantity + "\" size=\"4\"></div>";
			oCellPROPS.innerHTML = "<div id=\"PRODUCT_PROPS_USER_" + ID + "\"></div>";

			var priceColumn = "";
			if (!valutaFormat) valutaFormat = '<?=$CURRENCY_FORMAT?>';

			priceColumn += "<div id=\"DIV_PRICE_"+ID+"\" class=\"edit_price\">";
			priceColumn += "<span class=\"default_price_product\" id=\"default_price_"+ID+"\">";
			priceColumn += "<span class=\"formated_price\" id=\"formated_price_"+ID+"\" onclick=\"fEditPrice("+ID+", 'on');\">" + priceFormated + "</span>";
			priceColumn += "</span>";
			priceColumn += "<span class=\"edit_price_product\" id=\"edit_price_"+ID+"\">";
			priceColumn += "<input maxlength=\"9\" onblur=\"fEditPrice('" + ID + "', 'exit');\" onclick=\"fEditPrice('" + ID + "', 'on');\" onchange=\"fRecalProduct('" + ID + "', 'price', 'N');\" type=\"text\" name=\"PRODUCT[" + ID + "][PRICE]\" id=\"PRODUCT[" + ID + "][PRICE]\" value=\"" + price + "\" size=\"5\" >";
			priceColumn += "</span>";
			priceColumn += "<span id='currency_price_product' class='currency_price'>"+valutaFormat+"</span>";
			priceColumn += "<a href=\"javascript:void(0);\" onclick=\"fEditPrice(" + ID + ", 'on');\"><span class=\"pencil\"></span></a>";
			priceColumn += "</div>";
			priceColumn += "<div id=\"DIV_PRICE_OLD_"+ID+"\" class=\"base_price\" style=\"display:none;\">" + priceBaseFormat + " <span>"+valutaFormat+"</span></div>";

			priceColumn += "<div id=\"DIV_BASE_PRICE_WITH_DISCOUNT_"+ID+"\" class=\"base_price\">";
			priceColumn = priceColumn + priceBaseFormat + "<span>"+valutaFormat+"</span>";
			priceColumn += "</div>";

			priceColumn += "<div id=\"DIV_DISCOUNT_"+ID+"\" class=\"discount\">";
			if (priceDiscount > 0)
				priceColumn += "(<?=getMessage('NEWO_PRICE_DISCOUNT')?> "+discountPercent+"%)";
			priceColumn += "</div>";
			priceColumn += "<div class=\"base_price_title\"><?=GetMessage('NEWO_DASE_PRICE');?></div>";

			oCellPrice.innerHTML = priceColumn;
			oCellSumma.innerHTML = "<div>" + summaFormated + "<span>"+valutaFormat+"</span></div>";

			if (!balance) balance = 0;
			oCellBalance.innerHTML = "<div id=\"DIV_BALANCE_"+ID+"\">" + balance + "</div>";

			//array product in busket
			arProduct[ID] = ID;
		}
		else
		{
			var quantity = parseFloat(BX("PRODUCT[" + ID + "][QUANTITY]").value) + 1;
			BX("PRODUCT[" + ID + "][QUANTITY]").value = quantity;
		}
		fRecalProduct(BX("PRODUCT[" + ID + "][QUANTITY]"), ID, 'Y');
	}

	function DeleteProduct(el, id)
	{
		if (confirm('<?=GetMessage('SALE_CONFIRM_DELETE')?>'))
		{
			var trDel = document.getElementById("BASKET_TABLE_ROW_" + id).sectionRowIndex;
			var oTbl = document.getElementById("BASKET_TABLE");
			oTbl.deleteRow(trDel);
			delete arProduct[id];

			fRecalProduct('', '', 'Y');

			fGetMoreBusket('');
			fGetMoreViewed('');
		}

		return false;
	}

	function fRecalProduct(id, type, recommendet)
	{
		var location = '';
		var locationZip = '';
		var paySystemId = '';
		var deliveryId = '';
		var buyerTypeId = '';
		var cupon = '';
		var user_id = 0;
		if (BX('user_id'))
			user_id = BX('user_id').value;

		var productData = "{";
		var j = 0;

		if (type != "" && type == "price")
			document.getElementById('CALLBACK_FUNC_' + id).value = "Y";

		for(var i in arProduct)
		{
			if (j > 0)
				productData = productData + ",";

			discount = '';
			if (document.getElementById('PRODUCT[' + i + '][DISCOUNT_PRICE]'))
				discount = document.getElementById('PRODUCT[' + i + '][DISCOUNT_PRICE]').value;

			var taxOrder = '<?=$str_TAX_VALUE?>';

			var pr = BX('PRODUCT[' + i + '][PRICE]').value.replace(',', '.');
			pr = parseFloat(pr)
			prOld = parseFloat(BX('PRODUCT[' + i + '][PRICE_DEFAULT]').value)

			if(isNaN(pr) || pr <= 0)
			{
				BX('PRODUCT[' + i + '][PRICE]').value = BX('PRODUCT[' + i + '][PRICE_DEFAULT]').value;
			}

			productData = productData + "'" + i + "':{'BUSKET_ID':'" + BX('BUSKET_' + i).value + "', \n\
						'CALLBACK_FUNC':'" + BX('CALLBACK_FUNC_' + i).value + "',\n\
						'ORDER_CALLBACK_FUNC':'" + BX('ORDER_CALLBACK_FUNC_' + i).value + "',						\n\
						'CANCEL_CALLBACK_FUNC':'" + BX('CANCEL_CALLBACK_FUNC_' + i).value + "',\n\
						'PAY_CALLBACK_FUNC':'" + BX('PAY_CALLBACK_FUNC_' + i).value + "',\n\
						'QUANTITY':'" + BX('PRODUCT[' + i + '][QUANTITY]').value + "',\n\
						'CURRENCY':'" + BX('CURRENCY_' + i).value + "',\n\
						'PRICE':'" + BX('PRODUCT[' + i + '][PRICE]').value + "',\n\
						'PRICE_DEFAULT':'" + BX('PRODUCT[' + i + '][PRICE_DEFAULT]').value + "',\n\
						'WEIGHT':'" + BX('PRODUCT[' + i + '][WEIGHT]').value + "',\n\
						'MODULE':'" + BX('PRODUCT[' + i + '][MODULE]').value + "',\n\
						'VAT_RATE':'" + BX('PRODUCT[' + i + '][VAT_RATE]').value + "',\n\
						'TAX_VALUE':'" + taxOrder + "',\n\
						'DISCOUNT_PRICE':'" + discount + "'}";
			j++;
		}
		productData = productData + "}";

		if (BX('CITY_ORDER_PROP_' + locationID))
		{
			var selectedIndex = BX('CITY_ORDER_PROP_' + locationID).selectedIndex;
			var selectedOption = BX('CITY_ORDER_PROP_' + locationID).options;
		}
		else if (BX('ORDER_PROP_' + locationID))
		{
			var selectedIndex = BX('ORDER_PROP_' + locationID).selectedIndex;
			var selectedOption = BX('ORDER_PROP_' + locationID).options;
		}

		if (locationID > 0 && selectedIndex > 0)
			location = selectedOption[selectedIndex].value;

		if (BX('ORDER_PROP_' + locationZipID))
			locationZip = BX('ORDER_PROP_' + locationZipID).value;

		deliveryId = document.getElementById('DELIVERY_ID').value;
		deliveryPrice = parseFloat(document.getElementById('DELIVERY_ID_PRICE').value);
		if(isNaN(deliveryPrice))
			deliveryPrice = 0;

		paySystemId = document.getElementById('PAY_SYSTEM_ID').value;
		buyerTypeId = document.getElementById('buyer_type_id').value;
		cupon = document.getElementById('CUPON').value;

		var deliveryPriceChange = document.getElementById("change_delivery_price").value;
		var recomMore = document.getElementById('recom_more').value;

		dateURL = '<?=bitrix_sessid_get()?>&ORDER_AJAX=Y&id=<?=$ID?>&LID=<?=CUtil::JSEscape($LID)?>&recomMore='+recomMore+'&recommendet='+recommendet+'&delpricechange='+deliveryPriceChange+'&user_id=' + user_id + '&cupon=' + cupon + '&currency=' + currencyBase + '&deliveryId=' + deliveryId + '&paySystemId=' + paySystemId + '&deliveryPrice=' + deliveryPrice + '&buyerTypeId=' + buyerTypeId + '&locationID=' + locationID + '&location=' + location + '&locationZipID=' + locationZipID + '&locationZip=' + locationZip + '&product=' + productData;

		BX.showWait();
		BX.ajax.post('/bitrix/admin/sale_order_new.php', dateURL, fRecalProductResult);
	}
	function fRecalProductResult(result)
	{
		BX.closeWait();
		if (result.length > 0)
		{
			var res = eval( '('+result+')' );

			var changePriceProduct = "N";
			for(var i in res)
			{
				if (i > 0)
				{
					BX('PRODUCT[' + i + '][PRICE]').value = res[i]["PRICE"];
					BX('formated_price_' + i).innerHTML = res[i]["PRICE_DISPLAY"];

					if (res[i]["DISCOUNT_REPCENT"] > 0)
					{
						BX('DIV_DISCOUNT_' + i).innerHTML = '(<?=GetMessage('NEWO_PRICE_DISCOUNT')?> '+res[i]["DISCOUNT_REPCENT"]+'%)';
						BX('DIV_BASE_PRICE_WITH_DISCOUNT_' + i).innerHTML = res[i]["PRICE_BASE"]+" <span>"+res[0]["CURRENCY_FORMAT"]+"</span>";
					}
					else
					{
						prOld = parseFloat(BX('PRODUCT[' + i + '][PRICE_DEFAULT]').value);

						if (res[i]["PRICE"] == prOld)
						{
							if (BX('DIV_BASE_PRICE_WITH_DISCOUNT_' + i))
								BX('DIV_BASE_PRICE_WITH_DISCOUNT_' + i).innerHTML = '';
						}
						else
						{
							changePriceProduct = "Y";
							BX.show(BX('DIV_PRICE_OLD_'+i));
							if(BX('DIV_BASE_PRICE_WITH_DISCOUNT_'+i))
								BX.hide(BX('DIV_BASE_PRICE_WITH_DISCOUNT_'+i));
						}

						if (BX('DIV_DISCOUNT_' + i))
							BX('DIV_DISCOUNT_' + i).innerHTML = '';
					}

					BX('DIV_SUMMA_' + i).innerHTML = "<div>" + res[i]["SUMMA_DISPLAY"] + " <span>"+res[0]["CURRENCY_FORMAT"]+"</span></div>";
					BX('PRODUCT[' + i + '][QUANTITY]').value = res[i]["QUANTITY"];
					BX('DIV_BALANCE_' + i).value = res[i]["BALANCE"];
					BX('currency_price_product').innerHTML = res[0]["CURRENCY_FORMAT"];
					BX('PRODUCT[' + i + '][DISCOUNT_PRICE]').value = res[i]["DISCOUNT_PRICE"];
					BX('CURRENCY_' + i).value = res[i]["CURRENCY"];
				}
			}

			BX('DELIVER_ID_DESC').innerHTML = res[0]["DELIVERY_DESCRIPTION"];
			BX('DELIVERY_ID_PRICE').value = res[0]["DELIVERY_PRICE"];
			if (res[0]["DELIVERY"].length > 0)
					BX('DELIVERY_SELECT').innerHTML = res[0]["DELIVERY"];

			if (res[0]["ORDER_ERROR"] == "N")
			{

				if (BX('town_location_'+res[0]["LOCATION_TOWN_ID"]))
				{
					if (res[0]["LOCATION_TOWN_ENABLE"] == 'Y')
						BX('town_location_'+res[0]["LOCATION_TOWN_ID"]).style.display = 'table-row';
					else
						BX('town_location_'+res[0]["LOCATION_TOWN_ID"]).style.display = 'none';
				}

				BX('ORDER_TOTAL_PRICE').innerHTML = res[0]["PRICE_TOTAL"];

				if (res[0]["DISCOUNT_PRODUCT_VALUE"] > 0)
				{
					BX('ORDER_PRICE_WITH_DISCOUNT_DESC_VISIBLE').style.display = 'table-row';
					BX('ORDER_PRICE_WITH_DISCOUNT').innerHTML = res[0]["PRICE_WITH_DISCOUNT_FORMAT"];
				}
				else
				{
					if (changePriceProduct == 'N')
						BX('ORDER_PRICE_WITH_DISCOUNT_DESC_VISIBLE').style.display = 'none';
					else
					{
						BX('ORDER_PRICE_WITH_DISCOUNT_DESC_VISIBLE').style.display = 'table-row';
						BX('ORDER_PRICE_WITH_DISCOUNT').innerHTML = res[0]["PRICE_WITH_DISCOUNT_FORMAT"];
					}
				}

				if (parseInt(res[0]["ORDER_ID"]) > 0)
				{
					if (parseFloat(res[0]["PAY_ACCOUNT_DEFAULT"]) >= parseFloat(res[0]["PRICE_TO_PAY_DEFAULT"]))
					{
						BX('PAY_CURRENT_ACCOUNT_DESC').innerHTML = res[0]["PAY_ACCOUNT"];
						BX('buyerCanBuy').style.display = 'block';
					}
					else
						BX('buyerCanBuy').style.display = 'none';
				}

				BX('ORDER_DELIVERY_PRICE').innerHTML = res[0]["DELIVERY_PRICE_FORMAT"];
				BX('ORDER_TAX_PRICE').innerHTML = res[0]["PRICE_TAX"];
				BX('ORDER_WAIGHT').innerHTML = res[0]["PRICE_WEIGHT_FORMAT"];
				BX('ORDER_PRICE_ALL').innerHTML = res[0]["PRICE_TO_PAY"];

				if (parseFloat(res[0]["DISCOUNT_VALUE"]) > 0)
				{
					BX('ORDER_DISCOUNT_PRICE_VALUE').style.display = "table-row";
					BX('ORDER_DISCOUNT_PRICE_VALUE_VALUE').innerHTML = res[0]["DISCOUNT_VALUE_FORMATED"];
				}

				if (res[0]["RECOMMENDET_CALC"] == "Y")
				{
					if (res[0]["RECOMMENDET_PRODUCT"].length == 0)
					{
						BX('tab_1').style.display = "none";
						BX('user_recomendet').style.display = "none";

						if (BX('user_basket').style.display == "block")
							fTabsSelect('user_basket', 'tab_2');
						else if (BX('buyer_viewed').style.display == "block")
							fTabsSelect('buyer_viewed', 'tab_3');
						else if (BX('tab_2').style.display == "block")
							fTabsSelect('user_basket', 'tab_2');
						else if (BX('tab_3').style.display == "block")
							fTabsSelect('buyer_viewed', 'tab_3');
					}
					else
						BX('user_recomendet').innerHTML = res[0]["RECOMMENDET_PRODUCT"];
				}

				orderWeight = res[0]["PRICE_WEIGHT"];
				orderPrice = res[0]["PRICE_WITH_DISCOUNT"];

				fGetMoreBusket('');
				fGetMoreViewed('');

			}
		}
	}

	/*
	* click on recommendet More
	*/
	function fGetMoreRecom()
	{
		BX('recom_more').value = "Y";
		fRecalProduct('', '', 'Y');
	}

	/*
	* click on busket more
	*/
	function fGetMoreBusket(showAll)
	{
		recalcViewed = showAll;

		if (showAll == "Y")
			BX('recom_more_busket').value = "Y";

		showAll = BX('recom_more_busket').value;
		var userId = BX('user_id').value;
		var productData = "{";
		for(var i in arProduct)
			productData = productData + "'"+i+"':'"+i+"',";
		productData = productData + "}";

		BX.ajax.post('/bitrix/admin/sale_order_new.php', '<?=bitrix_sessid_get()?>&ORDER_AJAX=Y&showAll='+showAll+'&arProduct='+productData+'&getmorebasket=Y&CURRENCY=<?=$str_CURRENCY?>&LID=<?=CUtil::JSEscape($LID)?>&userId=' + userId, fGetMoreBusketResult);
	}
	function fGetMoreBusketResult(res)
	{
		if (res.length > 0)
			document.getElementById('user_basket').innerHTML = res;
		else
		{
			BX('tab_2').style.display = "none";
			BX('user_basket').style.display = "none";

			if (BX('tab_1').style.display == "block")
				fTabsSelect('user_recomendet', 'tab_1');
			else if (BX('tab_3').style.display == "block")
				fTabsSelect('buyer_viewed', 'tab_3');
		}

		if (recalcViewed != "R")
			fGetMoreViewed('R');
	}

	/*
	* click on busket more
	*/
	function fGetMoreViewed(showAll)
	{
		recalcBasket = showAll;

		if (showAll == "Y")
			BX('recom_more_viewed').value = "Y";

		showAll = BX('recom_more_viewed').value;
		var userId = BX('user_id').value;
		var productData = "{";
		for(var i in arProduct)
			productData = productData + "'"+i+"':'"+i+"',";
		productData = productData + "}";

		BX.ajax.post('/bitrix/admin/sale_order_new.php', '<?=bitrix_sessid_get()?>&ORDER_AJAX=Y&showAll='+showAll+'&arProduct='+productData+'&getmoreviewed=Y&CURRENCY=<?=$str_CURRENCY?>&LID=<?=CUtil::JSEscape($LID)?>&userId=' + userId, fGetMoreViewedResult);
	}
	function fGetMoreViewedResult(res)
	{
		if (res.length > 0)
			BX('buyer_viewed').innerHTML = res;
		else
		{
			BX('tab_3').style.display = "none";
			BX('buyer_viewed').style.display = "none";

			if (BX('tab_1').style.display == "block")
				fTabsSelect('user_recomendet', 'tab_1');
			else if (BX('tab_2').style.display == "block")
				fTabsSelect('user_basket', 'tab_2');
		}

		if (recalcBasket != "R")
			fGetMoreBusket('R');
	}

	/*
	* add to order from recommendet & busket
	*/
	function fAddToBusketMoreProduct(type, params)
	{
		FillProductFields(0, params, 0);

		if (type == 'busket')
			fGetMoreBusket('');
		if (type == 'viewed')
			fGetMoreViewed('');

		return false;
	}
</script>
	</td>
</tr>
<tr>
	<td colspan="2"><br>
		<input type="hidden" name="recom_more" id="recom_more" value="N" >
		<input type="hidden" name="recom_more_busket" id="recom_more_busket" value="N" >
		<input type="hidden" name="recom_more_viewed" id="recom_more_viewed" value="N" >
		<table width="100%" class="order_summary">
			<tr>
				<td valign="top" id="itog_tabs" class="load_product">
					<table width="100%" class="itog_header"><tr><td><?=GetMessage('NEWO_SUBTAB_RECOM_REQUEST');?></td></tr></table>
					<br>
					<div id="tabs">
						<?
						$displayNone = "block";
						$displayNoneBasket = "block";
						$displayNoneViewed = "block";

						$arRecomendet = CSaleProduct::GetRecommendetProduct($str_USER_ID, $LID, $arFilterRecomendet);
						$arRecomendetResult = fDeleteDoubleProduct($arRecomendet, $arFilterRecomendet, 'N');
						if (count($arRecomendetResult["ITEMS"]) <= 0)
							$displayNone = "none";

						$arShoppingCart = CSaleBasket::DoGetUserShoppingCart($LID, $str_USER_ID, $FUSER_ID, $arErrors, $arCupon);
						$arShoppingCart = fDeleteDoubleProduct($arShoppingCart, $arFilterRecomendet, 'N');
						if (count($arShoppingCart["ITEMS"]) <= 0)
							$displayNoneBasket = "none";

						$arViewed = array();
						$dbViewsList = CSaleViewedProduct::GetList(
								array("DATE_VISIT"=>"DESC"),
								array("FUSER_ID" => $arFuserItems["ID"], ">PRICE" => 0, "!CURRENCY" => "", "LID" => $str_LID),
								false,
								array('nTopCount' => 10),
								array('ID', 'PRODUCT_ID', 'LID', 'MODULE', 'NAME', 'DETAIL_PAGE_URL', 'PRICE', 'CURRENCY', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
							);
						while ($arViews = $dbViewsList->Fetch())
							$arViewed[] = $arViews;

						$arViewedResult = fDeleteDoubleProduct($arViewed, $arFilterRecomendet, 'N');
						if (count($arViewedResult["ITEMS"]) <= 0)
							$displayNoneViewed = "none";

						$tabBasket = "tabs";
						$tabViewed = "tabs";

						if ($displayNoneBasket == 'none' && $displayNone == 'none' && $displayNoneViewed == 'block')
							$tabViewed .= " active";
						if ($displayNoneBasket == 'block' && $displayNone == 'none')
							$tabBasket .= " active";
						?>
						<div id="tab_1" style="display:<?=$displayNone?>" class="tabs active" onClick="fTabsSelect('user_recomendet', this);"><?=GetMessage('NEWO_SUBTAB_RECOMENET')?></div>
						<div id="tab_2" style="display:<?=$displayNoneBasket?>" class="<?=$tabBasket?>" onClick="fTabsSelect('user_basket', this);"><?=GetMessage('NEWO_SUBTAB_BUSKET')?></div>
						<div id="tab_3" style="display:<?=$displayNoneViewed?>" class="<?=$tabViewed?>" onClick="fTabsSelect('buyer_viewed', this);"><?=GetMessage('NEWO_SUBTAB_LOOKED')?></div>

						<?
						if ($displayNone == 'block')
						{
							$displayNoneBasket = 'none';
							$displayNoneViewed = 'none';
						}
						if ($displayNoneBasket == 'block')
						{
							$displayNone = 'none';
							$displayNoneViewed = 'none';
						}
						if ($displayNoneViewed == 'block')
						{
							$displayNone = 'none';
							$displayNoneBasket = 'none';
						}
						?>
						<div id="user_recomendet" class="tabstext active" style="_display:<?=$displayNone?>">
							<? echo fGetFormatedProduct($str_USER_ID, $LID, $arRecomendetResult, $str_CURRENCY, 'recom');?>
						</div>

						<div id="user_basket" class="tabstext active" style="display:<?=$displayNoneBasket?>">
						<?
							if (count($arShoppingCart["ITEMS"]) > 0)
								echo fGetFormatedProduct($str_USER_ID, $LID, $arShoppingCart, $str_CURRENCY, 'busket');
						?>
						</div>

						<div id="buyer_viewed" class="tabstext active" style="display:<?=$displayNoneViewed?>">
						<?
							if (count($arViewedResult["ITEMS"]) > 0)
								echo fGetFormatedProduct($str_USER_ID, $LID, $arViewedResult, $str_CURRENCY, 'viewed');
						?>

						</div>
					</div>
					<script>
					function fTabsSelect(tabText, el)
					{
						BX('tab_1').className = "tabs";
						BX('tab_2').className = "tabs";
						BX('tab_3').className = "tabs";

						BX(el).className = "tabs active";
						BX(el).style.display = 'block';

						BX('user_recomendet').className = "tabstext";
						BX('user_basket').className = "tabstext";
						BX('buyer_viewed').className = "tabstext";
						BX('user_recomendet').style.display = 'none';
						BX('user_basket').style.display = 'none';
						BX('buyer_viewed').style.display = 'none';

						BX(tabText).style.display = 'block';
						BX(tabText).className = "tabstext active";
					}
					</script>
				</td>

				<td valign="top" class="summary">
					<div class="order-itog">
					<table width="100%">
					<tr>
					<td class="title">
						<?echo GetMessage("NEWO_TOTAL_PRICE")?>
					</td>
					<td nowrap class="title">
						<div id="ORDER_TOTAL_PRICE" style="white-space:nowrap;">
							<?=SaleFormatCurrency($ORDER_TOTAL_PRICE, $str_CURRENCY);?>
						</div>
					</td>
					</tr>
					<tr class="price" style="display:<?echo (($ORDER_PRICE_WITH_DISCOUNT > 0) ? 'table-row' : 'none');?>" id="ORDER_PRICE_WITH_DISCOUNT_DESC_VISIBLE">
						<td id="ORDER_PRICE_WITH_DISCOUNT_DESC" class="title" >
							<div><?echo GetMessage("NEWO_TOTAL_PRICE_WITH_DISCOUNT_MARGIN")?></div>
						</td>
						<td nowrap>
							<div id="ORDER_PRICE_WITH_DISCOUNT">
									<?=SaleFormatCurrency($ORDER_PRICE_WITH_DISCOUNT, $str_CURRENCY);?>
							</div>
						</td>
					</tr>
					<tr>
					<td class="title">
						<?echo GetMessage("NEWO_TOTAL_DELIVERY")?>
					</td>
					<td nowrap>
						<div id="ORDER_DELIVERY_PRICE" style="white-space:nowrap;">
							<?=SaleFormatCurrency($deliveryPrice, $str_CURRENCY);?>
						</div>
					</td>
					</tr>
					<tr>
					<td class="title">
						<?echo GetMessage("NEWO_TOTAL_TAX")?>
					</td>
					<td nowrap>
						<div id="ORDER_TAX_PRICE" style="white-space:nowrap;">
							<?=SaleFormatCurrency($str_TAX_VALUE, $str_CURRENCY);?>
						</div>
					</td>
					</tr>
					<tr>
					<td class="title">
						<?echo GetMessage("NEWO_TOTAL_WEIGHT")?>
					</td>
					<td nowrap>
						<div id="ORDER_WAIGHT" style="white-space:nowrap;">
							<?=roundEx(DoubleVal($productWeight/$WEIGHT_KOEF), SALE_VALUE_PRECISION)." ".$WEIGHT_UNIT;?>
						</div>
					</td>
					</tr>
					<tr>
					<td class="title">
						<?echo GetMessage("NEWO_TOTAL_PAY_ACCOUNT2")?>
					</td>
					<td nowrap>
						<div id="ORDER_PAY_FROM_ACCOUNT" style="white-space:nowrap;">
							<?=SaleFormatCurrency(roundEx($str_SUM_PAID, SALE_VALUE_PRECISION), $str_CURRENCY);?>
						</div>
					</td>
					</tr>
					<tr class="price" style="display:<?echo (($str_DISCOUNT_VALUE > 0) ? 'table-row' : 'none');?>" id="ORDER_DISCOUNT_PRICE_VALUE">
						<td class="title" >
							<?echo GetMessage("NEWO_TOTAL_DISCOUNT_PRICE_VALUE")?>
						</td>
						<td nowrap>
							<div id="ORDER_DISCOUNT_PRICE_VALUE_VALUE" style="white-space:nowrap;">
									<?=SaleFormatCurrency($str_DISCOUNT_VALUE, $str_CURRENCY);?>
							</div>
						</td>
					</tr>
					<tr class="itog">
					<td class='ileft'>
						<div><?echo GetMessage("NEWO_TOTAL_TOTAL")?></div>
					</td>
					<td class='iright' nowrap>
						<div id="ORDER_PRICE_ALL" style="white-space:nowrap;">
							<?=SaleFormatCurrency($str_PRICE, $str_CURRENCY);?>
						</div>
					</td>
					</tr>
					</table>
					</div>
				</td>
			</tr>
		</table>
	</td>
</tr>
<?
$tabControl->EndCustomField("BASKET_CONTAINER");


if (!defined('BX_PUBLIC_MODE') || BX_PUBLIC_MODE != 1)
{
	$tabControl->Buttons(array("back_url"=>"/bitrix/admin/sale_order_new.php?lang=".LANGUAGE_ID."&ID=".$ID."&dontsave=Y"));
}

$tabControl->Show();

//order busket user by manadger
if (isset($_REQUEST["user_id"]) && IntVal($_REQUEST["user_id"]) > 0 && !$bVarsFromForm)
{
	$str_USER_ID = IntVal($_REQUEST["user_id"]);

	$arParams = array();
	echo "<script>";
	echo "window.onload = function () {";
	echo "fUserGetProfile(BX(\"user_id\"));\n";

	if (isset($_REQUEST["product"]) && count($_REQUEST["product"]) > 0)
	{
		foreach ($_REQUEST["product"] as $val)
		{
			$val = IntVal($val);

			if (CModule::IncludeModule('catalog') && CModule::IncludeModule('iblock'))
			{
				$res = CIBlockElement::GetList(array(), array("ID" => $val), false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "PREVIEW_PICTURE", "DETAIL_PICTURE", "NAME", "DETAIL_PAGE_URL"));
				if($arItems = $res->Fetch())
				{
					$productImg = "";
					if($arItems["PREVIEW_PICTURE"] != "")
						$productImg = $arItems["PREVIEW_PICTURE"];
					elseif($arItems["DETAIL_PICTURE"] != "")
						$productImg = $arItems["DETAIL_PICTURE"];

					$ImgUrl = "";
					if ($productImg != "")
					{
						$arFile = CFile::GetFileArray($productImg);
						$productImg = CFile::ResizeImageGet($arFile, array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);
						$ImgUrl = $productImg["src"];
					}

					$arBuyerGroups = CUser::GetUserGroup($str_USER_ID);
					$arPrice = CCatalogProduct::GetOptimalPrice($val, 1, $arBuyerGroups, "N", array(), $LID);

					$arCurFormat = CCurrencyLang::GetCurrencyFormat($arPrice["PRICE"]["CURRENCY"]);
					$priceValutaFormat = str_replace("#", '', $arCurFormat["FORMAT_STRING"]);

					if (!is_array($arPrice["DISCOUNT"]) || count($arPrice["DISCOUNT"]) <= 0)
					{
						$arPrice["DISCOUNT_PRICE"] = 0;
						$price = $arPrice["PRICE"]["PRICE"];
					}
					else
					{
						$price = $arPrice["DISCOUNT_PRICE"];
					}

					$summaFormated = CurrencyFormatNumber($price, $arPrice["PRICE"]["CURRENCY"]);
					$currentTotalPriceFormat = CurrencyFormatNumber($price, $arPrice["PRICE"]["CURRENCY"]);

					$balance = 0;
					$weight = 0;

					if($ar_res = CCatalogProduct::GetByID($val))
					{
						$balance = FloatVal($ar_res["QUANTITY"]);
						$weight = FloatVal($ar_res["WEIGHT"]);
					}

					$discountPercent = 0;
					if ($arPrice["DISCOUNT_PRICE"] > 0)
					{
						$discountPercent = IntVal((($arPrice["PRICE"]["PRICE"]-$arPrice["DISCOUNT_PRICE"]) * 100) / $arPrice["PRICE"]["PRICE"]);
						$priceDiscount = $arPrice["PRICE"]["PRICE"] - $arPrice["DISCOUNT_PRICE"];
					}

					$urlEdit = "/bitrix/admin/iblock_element_edit.php?ID=".$arItems["ID"]."&type=catalog&lang=".LANG."&IBLOCK_ID=".$arItems["IBLOCK_ID"]."&find_section_section=".IntVal($arItems["IBLOCK_SECTION_ID"]);

					$arParams = array(
						'id' => $val,
						'name' => CUtil::JSEscape($arItems["NAME"]),
						'url' => CUtil::JSEscape($arItems["DETAIL_PAGE_URL"]),
						'urlImg' => CUtil::JSEscape($ImgUrl),
						'urlEdit' => CUtil::JSEscape($urlEdit),
						'price' => CUtil::JSEscape($price),
						'priceFormated' => CUtil::JSEscape($price),
						'priceBase' => CUtil::JSEscape($arPrice["PRICE"]["PRICE"]),
						'priceBaseFormat' => CUtil::JSEscape($arPrice["PRICE"]["PRICE"]),
						'valutaFormat' => CUtil::JSEscape($priceValutaFormat),
						'priceDiscount' => CUtil::JSEscape($priceDiscount),
						'summaFormated' => CUtil::JSEscape($summaFormated),
						'priceTotalFormated' => CUtil::JSEscape($currentTotalPriceFormat),
						'discountPercent' => CUtil::JSEscape($discountPercent),
						'balance'  => CUtil::JSEscape($balance),
						'quantity' => '1',
						'module' => 'catalog',
						'currency' => CUtil::JSEscape($arPrice["PRICE"]["CURRENCY"]),
						'weight' => $weight,
						'vatRate' => DoubleVal('0'),
						'priceType' => '',
						'catalogXmlID' => '',
						'productXmlID' => '',
						'callback' => 'CatalogBasketCallback',
						'orderCallback' => 'CatalogBasketOrderCallback',
						'cancelCallback' => 'CatalogBasketCancelCallback',
						'payCallback' => 'CatalogPayOrderCallback'
					);
					$arParams = CUtil::PhpToJSObject($arParams);

					echo "FillProductFields(0, ".$arParams.", 0);\n";
				}
			}
		}//end foreach
	}//end if
	echo "fButtonCurrent('btnBuyerExistRemote');";
	echo "};";
	echo "</script>";
}
echo "</div>";//end div for form
?>

<div class="sale_popup_form" id="popup_form_sku_order" style="display:none;">
	<table width="100%">
		<tr><td></td></tr>
		<tr>
			<td><small><span id="listItemPrice"></span>&nbsp;<span id="listItemOldPrice"></span></small></td>
		</tr>
		<tr>
			<td><hr></td>
		</tr>
	</table>

	<table width="100%" id="sku_selectors_list">
		<tr>
			<td colspan="2"></td>
		</tr>
	</table>

	<span id="prod_order_button"></span>
	<input type="hidden" value="" name="popup-params-product" id="popup-params-product" >
	<input type="hidden" value="" name="popup-params-type" id="popup-params-type" >
</div>
	<script>
			var wind = new BX.PopupWindow('popup_sku', this, {
				offsetTop : 10,
				offsetLeft : 0,
				autoHide : true,
				closeByEsc : true,
				closeIcon : true,
				titleBar : true,
				draggable: {restrict:true},
				titleBar: {content: BX.create("span", {html: '', 'props': {'className': 'sale-popup-title-bar'}})},
				content : document.getElementById("popup_form_sku_order"),

				buttons: [
					new BX.PopupWindowButton({
						text : '<?=GetMessageJS('NEWO_POPUP_CAN_BUY_NOT');?>',
						id : "popup_sku_save",
						events : {
							click : function() {
								if (BX('popup-params-product').value.length > 0)
								{
									if (BX('popup-params-type').value == 'neworder')
									{
										window.location = BX('popup-params-product').value;
									}
									else
									{
										var res = eval( '('+BX('popup-params-product').value+')' );
										FillProductFields(0, res, 0);
									}

									wind.close();
								}
							}
						}
					}),
					new BX.PopupWindowButton({
						text : '<?=GetMessageJS('NEWO_POPUP_CLOSE');?>',
						id : "popup_sku_cancel",
						events : {
							click : function() {
								wind.close();
							}
						}
					})
				]
			});
			function fAddToBusketMoreProductSku(arSKU, arProperties, type, message)
			{
				BX.message(message);
				wind.show();
				buildSelect("sku_selectors_list", 0, arSKU, arProperties, type);
				var properties_num = arProperties.length;
				var lastPropCode = arProperties[properties_num-1].CODE;
				addHtml(lastPropCode, arSKU, type);
			}
			function buildSelect(cont_name, prop_num, arSKU, arProperties, type)
			{
				var properties_num = arProperties.length;
				var lastPropCode = arProperties[properties_num-1].CODE;

				for (var i = prop_num; i < properties_num; i++)
				{
					var q = BX('prop_' + i);
					if (q)
						q.parentNode.removeChild(q);
				}

				var select = BX.create('SELECT', {
					props: {
						name: arProperties[prop_num].CODE,
						id :  arProperties[prop_num].CODE
					},
					events: {
						change: (prop_num < properties_num-1)
							? function() {
								buildSelect(cont_name, prop_num + 1, arSKU, arProperties, type);
								if (this.value != "null")
									BX(arProperties[prop_num+1].CODE).disabled = false;
								addHtml(lastPropCode, arSKU, type);
							}
							: function() {
								if (this.value != "null")
									addHtml(lastPropCode, arSKU, type)
							}
					}
				});
				if (prop_num != 0) select.disabled = true;

				var ar = [];
				select.add(new Option(arProperties[prop_num].NAME, 'null'));

				for (var i = 0; i < arSKU.length; i++)
				{
					if (checkSKU(arSKU[i], prop_num, arProperties) && !BX.util.in_array(arSKU[i][prop_num], ar))
					{
						select.add(new Option(
								arSKU[i][prop_num],
								prop_num < properties_num-1 ? arSKU[i][prop_num] : arSKU[i]["ID"]
						));
						ar.push(arSKU[i][prop_num]);
					}
				}

				var cont = BX.create('tr', {
					props: {id: 'prop_' + prop_num},
					children:[
						BX.create('td', {html: arProperties[prop_num].NAME + ': '}),
						BX.create('td', { children:[
							select
						]}),
					]
				});

				var tmp = BX.findChild(BX(cont_name), {tagName:'tbody'}, false, false);

				tmp.appendChild(cont);

				if (prop_num < properties_num-1)
					buildSelect(cont_name, prop_num + 1, arSKU, arProperties, type);
			}

			function checkSKU(SKU, prop_num, arProperties)
			{
				for (var i = 0; i < prop_num; i++)
				{
					code = BX.findChild(BX('popup_sku'), {'attr': {name: arProperties[i].CODE}}, true, false).value;
					if (SKU[i] != code)
						return false;
				}
				return true;
			}
			function addHtml(lastPropCode, arSKU, type)
			{
				var selectedSkuId = BX(lastPropCode).value;
				var btnText = '';

				BX('popup-window-titlebar-popup_sku').innerHTML = '<span class="sale-popup-title-bar">'+arSKU[0]["PRODUCT_NAME"]+'</span>';
				BX("listItemPrice").innerHTML = BX.message('PRODUCT_PRICE_FROM')+" "+arSKU[0]["MIN_PRICE"];
				BX("listItemOldPrice").innerHTML = '';

				for (var i = 0; i < arSKU.length; i++)
				{
					if (arSKU[i]["ID"] == selectedSkuId)
					{
						BX('popup-window-titlebar-popup_sku').innerHTML = '<span class="sale-popup-title-bar">'+arSKU[i]["NAME"]+'</span>';

						if (arSKU[i]["DISCOUNT_PRICE"] != "")
						{
							BX("listItemPrice").innerHTML = arSKU[i]["DISCOUNT_PRICE_FORMATED"]+" "+arSKU[i]["VALUTA_FORMAT"];
							BX("listItemOldPrice").innerHTML = arSKU[i]["PRICE_FORMATED"]+" "+arSKU[i]["VALUTA_FORMAT"];
							summaFormated = arSKU[i]["DISCOUNT_PRICE_FORMATED"];
							price = arSKU[i]["DISCOUNT_PRICE"];
							priceFormated = arSKU[i]["DISCOUNT_PRICE_FORMATED"];
							priceDiscount = arSKU[i]["PRICE"] - arSKU[i]["DISCOUNT_PRICE"];
						}
						else
						{
							BX("listItemPrice").innerHTML = arSKU[i]["PRICE_FORMATED"]+" "+arSKU[i]["VALUTA_FORMAT"];
							BX("listItemOldPrice").innerHTML = "";
							summaFormated = arSKU[i]["PRICE_FORMATED"];
							price = arSKU[i]["PRICE"];
							priceFormated = arSKU[i]["PRICE_FORMATED"];
							priceDiscount = 0;
						}

						if (arSKU[i]["CAN_BUY"] == "Y")
						{
							var arParams = "{'id' : '"+arSKU[i]["ID"]+"',\n\
							'name' : '"+arSKU[i]["NAME"]+"',\n\
							'url' : '',\n\
							'urlEdit' : '"+arSKU[i]["URL_EDIT"]+"',\n\
							'urlImg' : '"+arSKU[i]["ImageUrl"]+"',\n\
							'price' : '"+price+"',\n\
							'priceFormated' : '"+priceFormated+"',\n\
							'valutaFormat' : '"+arSKU[i]["VALUTA_FORMAT"]+"',\n\
							'priceDiscount' : '"+priceDiscount+"',\n\
							'priceBase' : '"+arSKU[i]["PRICE"]+"',\n\
							'priceBaseFormat' : '"+arSKU[i]["PRICE_FORMATED"]+"',\n\
							'priceTotalFormated' : '"+arSKU[i]["DISCOUNT_PRICE"]+"',\n\
							'discountPercent' : '"+arSKU[i]["DISCOUNT_PERCENT"]+"',\n\
							'summaFormated' : '"+summaFormated+"',\n\
							'quantity' : '1','module' : 'catalog',\n\
							'currency' : '"+arSKU[i]["CURRENCY"]+"',\n\
							'weight' : '0','vatRate' : '0','priceType' : '',\n\
							'balance' : '0','catalogXmlID' : '','productXmlID' : '','callback' : 'CatalogBasketCallback','orderCallback' : 'CatalogBasketOrderCallback','cancelCallback' : 'CatalogBasketCancelCallback','payCallback' : 'CatalogPayOrderCallback'}";

							BX('popup-params-type').value = type;

							if (type != 'neworder')
							{
								message = BX.message('PRODUCT_ADD');
								BX('popup-params-product').value = arParams;
							}
							else
							{
								message = BX.message('PRODUCT_ORDER');
								BX('popup-params-product').value = "/bitrix/admin/sale_order_new.php?lang=<?=LANG?>&user_id="+arSKU[i]["USER_ID"]+"&LID="+arSKU[i]["LID"]+"&product[]="+arSKU[i]["ID"];
							}
						}
						else
						{
							BX('popup-params-product').value = '';
							message = BX.message('PRODUCT_NOT_ADD');
						}

						BX.findChild(BX('popup_sku_save'), {'attr': {class: 'popup-window-button-text'}}, true, false).innerHTML = message;
					}

					if (arSKU[i]["ID"] == selectedSkuId)
						break;
				}
			}
	</script>

<?
require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");
?>