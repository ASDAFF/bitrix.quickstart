<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions < "W")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

$ID = IntVal($ID);

ClearVars();
ClearVars("f_");

$path2SystemPSFiles = "/bitrix/modules/sale/payment/";
$path2UserPSFiles = COption::GetOptionString("sale", "path2user_ps_files", BX_PERSONAL_ROOT."/php_interface/include/sale_payment/");
CheckDirPath($_SERVER["DOCUMENT_ROOT"].$path2UserPSFiles);

$bFilemanModuleInst = False;
if (IsModuleInstalled("fileman"))
	$bFilemanModuleInst = True;

$errorMessage = "";
$bInitVars = false;
if ($_SERVER["REQUEST_METHOD"] == "POST"
	&& (strlen($save) > 0 || strlen($apply) > 0)
	&& $saleModulePermissions == "W"
	&& check_bitrix_sessid())
{
	$NAME = Trim($NAME);
	if (strlen($NAME) <= 0)
		$errorMessage .= GetMessage("ERROR_NO_NAME")."<br>";

	$ACTIVE = (($ACTIVE == "Y") ? "Y" : "N");
	$SORT = ((IntVal($SORT) > 0) ? IntVal($SORT) : 100);

	if (strlen($errorMessage) <= 0)
	{
		$arFields = array(
				"NAME" => $NAME,
				"ACTIVE" => $ACTIVE,
				"SORT" => $SORT,
				"DESCRIPTION" => $DESCRIPTION
			);
		if(strlen($LID) > 0)
			$arFields["LID"] = $LID;
		if(strlen($CURRENCY) > 0)
			$arFields["CURRENCY"] = $CURRENCY;

		if ($ID > 0)
		{
			if (!CSalePaySystem::Update($ID, $arFields))
			{
				if ($ex = $APPLICATION->GetException())
					$errorMessage .= $ex->GetString().".<br>";
				else
					$errorMessage .= GetMessage("ERROR_EDIT_PAY_SYS").".<br>";
			}
		}
		else
		{
			$ID = CSalePaySystem::Add($arFields);
			if ($ID <= 0)
			{
				if ($ex = $APPLICATION->GetException())
					$errorMessage .= $ex->GetString().".<br>";
				else
					$errorMessage .= GetMessage("ERROR_ADD_PAY_SYS").".<br>";
			}
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		$arPersonTypes = array();

		$dbPersonType = CSalePersonType::GetList(
				array("SORT" => "ASC", "NAME" => "ASC"),
				Array()
			);
		while ($arPersonType = $dbPersonType->GetNext())
		{
			$errorMessage1 = "";
			$arPersonTypes[] = IntVal($arPersonType["ID"]);

			$actionID = 0;
			$dbPSAction = CSalePaySystemAction::GetList(
					array(),
					array("PAY_SYSTEM_ID" => $ID, "PERSON_TYPE_ID" => $arPersonType["ID"])
				);
			if ($arPSAction = $dbPSAction->Fetch())
			{
				$actionID = IntVal($arPSAction["ID"]);
			}

			if (${"PS_EXISTS_".$arPersonType["ID"]} != "Y")
			{
				if ($actionID > 0)
				{
					if (!CSalePaySystemAction::Delete($actionID))
					{
						if ($ex = $APPLICATION->GetException())
							$errorMessage1 .= $ex->GetString().".<br>";
						else
							$errorMessage1 .= str_replace("#PLAT#", $arPersonType["NAME"], GetMessage("SPS_ERROR_DELETE")).".<br>";
					}
				}
			}
			else
			{
				${"NAME_".$arPersonType["ID"]} = Trim(${"NAME_".$arPersonType["ID"]});
				if (strlen(${"NAME_".$arPersonType["ID"]}) <= 0)
					$errorMessage1 .= str_replace("#PLAT#", $arPersonType["NAME"], GetMessage("SPS_EMPTY_NAME")).".<br>";

				${"ACTION_FILE_".$arPersonType["ID"]} = Trim(${"ACTION_FILE_".$arPersonType["ID"]});
				if (strlen(${"ACTION_FILE_".$arPersonType["ID"]}) <= 0)
					$errorMessage1 .= str_replace("#PLAT#", $arPersonType["NAME"], GetMessage("SPS_EMPTY_SCRIPT")).".<br>";

				if (strlen(${"ACTION_FILE_".$arPersonType["ID"]}) > 0)
				{
					${"ACTION_FILE_".$arPersonType["ID"]} = str_replace("\\", "/", ${"ACTION_FILE_".$arPersonType["ID"]});
					while (substr(${"ACTION_FILE_".$arPersonType["ID"]}, strlen(${"ACTION_FILE_".$arPersonType["ID"]}) - 1, 1) == "/")
						${"ACTION_FILE_".$arPersonType["ID"]} = substr(${"ACTION_FILE_".$arPersonType["ID"]}, 0, strlen(${"ACTION_FILE_".$arPersonType["ID"]}) - 1);

					$pathToAction = $_SERVER["DOCUMENT_ROOT"].${"ACTION_FILE_".$arPersonType["ID"]};
					if (!file_exists($pathToAction))
						$errorMessage1 .= str_replace("#PLAT#", $arPersonType["NAME"], GetMessage("SPS_NO_SCRIPT")).".<br>";
				}

				if (strlen($errorMessage1) <= 0)
				{
					$arParams = array();

					if (strlen(${"PS_ACTION_FIELDS_LIST_".$arPersonType["ID"]}) > 0)
					{
						$arActFields = explode(",", ${"PS_ACTION_FIELDS_LIST_".$arPersonType["ID"]});
						for ($i = 0; $i < count($arActFields); $i++)
						{
							$arActFields[$i] = Trim($arActFields[$i]);

							$typeTmp = ${"TYPE_".$arActFields[$i]."_".$arPersonType["ID"]};
							$valueTmp = ${"VALUE1_".$arActFields[$i]."_".$arPersonType["ID"]};
							if (strlen($typeTmp) <= 0)
								$valueTmp = ${"VALUE2_".$arActFields[$i]."_".$arPersonType["ID"]};

							$arParams[$arActFields[$i]] = array(
									"TYPE" => $typeTmp,
									"VALUE" => $valueTmp
								);
						}
					}

					$arFields = array(
							"PAY_SYSTEM_ID" => $ID,
							"PERSON_TYPE_ID" => $arPersonType["ID"],
							"NAME" => ${"NAME_".$arPersonType["ID"]},
							"ACTION_FILE" => ${"ACTION_FILE_".$arPersonType["ID"]},
							"NEW_WINDOW" => ( (${"NEW_WINDOW_".$arPersonType["ID"]}=="Y") ? "Y" : "N" ),
							"PARAMS" => CSalePaySystemAction::SerializeParams($arParams),
							"HAVE_PREPAY" => "N",
							"HAVE_RESULT" => "N",
							"HAVE_ACTION" => "N",
							"HAVE_PAYMENT" => "N",
							"HAVE_RESULT_RECEIVE" => "N",
							"ENCODING" => trim(${"ENCODING_".$arPersonType["ID"]}),
						);

					$pathToAction = $_SERVER["DOCUMENT_ROOT"].${"ACTION_FILE_".$arPersonType["ID"]};
					$pathToAction = str_replace("\\", "/", $pathToAction);
					while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
						$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);

					if (file_exists($pathToAction))
					{
						if (is_dir($pathToAction))
						{
							if (file_exists($pathToAction."/pre_payment.php"))
								$arFields["HAVE_PREPAY"] = "Y";
							if (file_exists($pathToAction."/result.php"))
								$arFields["HAVE_RESULT"] = "Y";
							if (file_exists($pathToAction."/action.php"))
								$arFields["HAVE_ACTION"] = "Y";
							if (file_exists($pathToAction."/payment.php"))
								$arFields["HAVE_PAYMENT"] = "Y";
							if (file_exists($pathToAction."/result_rec.php"))
								$arFields["HAVE_RESULT_RECEIVE"] = "Y";
						}
						else
						{
							$arFields["HAVE_PAYMENT"] = "Y";
						}
					}

					if ($actionID > 0)
					{
						if (!CSalePaySystemAction::Update($actionID, $arFields))
						{
							if ($ex = $APPLICATION->GetException())
								$errorMessage1 .= $ex->GetString().".<br>";
							else
								$errorMessage1 .= str_replace("#PLAT#", $arPersonType["NAME"], GetMessage("SPS_ERROR_UPDATE")).".<br>";
						}
					}
					else
					{
						if (!CSalePaySystemAction::Add($arFields))
						{
							if ($ex = $APPLICATION->GetException())
								$errorMessage1 .= $ex->GetString().".<br>";
							else
								$errorMessage1 .= str_replace("#PLAT#", $arPersonType["NAME"], GetMessage("SPS_ERROR_ADD")).".<br>";
						}
					}
				}
			}

			$errorMessage .= $errorMessage1;
		}

		$dbPSAction = CSalePaySystemAction::GetList(
				array(),
				array("PAY_SYSTEM_ID" => $ID, "!PERSON_TYPE_ID" => $arPersonTypes)
			);
		while ($arPSAction = $dbPSAction->Fetch())
		{
			if (!CSalePaySystemAction::Delete($arPSAction["ID"]))
			{
				if ($ex = $APPLICATION->GetException())
					$errorMessage1 .= $ex->GetString().".<br>";
				else
					$errorMessage1 .= str_replace("#PLAT#", $arPersonType["NAME"], GetMessage("SPS_ERROR_DELETE")).".<br>";
			}
		}
	}

	if (strlen($errorMessage) > 0)
		$bInitVars = True;

	if (strlen($save) > 0 && strlen($errorMessage) <= 0)
		LocalRedirect("sale_pay_system.php?lang=".LANG.GetFilterParams("filter_", false));
}

if ($ID > 0)
{
	$dbPaySystem = CSalePaySystem::GetList(array("SORT" => "ASC"), array("ID" => $ID));
	$dbPaySystem->ExtractFields("str_");
}

if ($bInitVars)
{
	$DB->InitTableVarsForEdit("b_sale_pay_system", "", "str_");
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

$APPLICATION->SetTitle(($ID > 0) ? GetMessage("SALE_EDIT_RECORD", array("#ID#" => $ID)) : GetMessage("SALE_NEW_RECORD"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

/*********************************************************************/
/********************  BODY  *****************************************/
/*********************************************************************/
?>

<?
$aMenu = array(
		array(
				"TEXT" => GetMessage("SPSN_2FLIST"),
				"LINK" => "/bitrix/admin/sale_pay_system.php?lang=".LANG.GetFilterParams("filter_"),
				"ICON" => "btn_list"
			)
	);

if ($ID > 0 && $saleModulePermissions >= "W")
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
			"TEXT" => GetMessage("SPSN_NEW_PAYSYS"),
			"LINK" => "/bitrix/admin/sale_pay_system_edit.php?lang=".LANG.GetFilterParams("filter_"),
			"ICON" => "btn_new"
		);

	$aMenu[] = array(
			"TEXT" => GetMessage("SPSN_DELETE_PAYSYS"),
			"LINK" => "javascript:if(confirm('".GetMessage("SPSN_DELETE_PAYSYS_CONFIRM")."')) window.location='/bitrix/admin/sale_pay_system.php?action=delete&ID[]=".$ID."&lang=".LANG."&".bitrix_sessid_get()."#tb';",
			"WARNING" => "Y",
			"ICON" => "btn_delete"
		);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?if(strlen($errorMessage)>0)
	echo CAdminMessage::ShowMessage(Array("DETAILS"=>$errorMessage, "TYPE"=>"ERROR", "MESSAGE"=>GetMessage("SPSN_ERROR"), "HTML"=>true));?>

<script language="JavaScript">
<!--
function SetActLinkText(ind, flag)
{
	var paySysActSwitch = document.getElementById("pay_sys_switch_" + ind);
	if (flag)
	{
		paySysActSwitch.innerHTML = "<br><?= GetMessage("SPS_HIDE_PROPS") ?>";
	}
	else
	{
		paySysActSwitch.innerHTML = "<?= GetMessage("SPS_SHOW_PROPS") ?>";
	}
}

function ShowHideStatus(ind)
{
	var paySysActDIV = document.getElementById("pay_sys_act_" + ind);
	eval("var flag = paySysActVisible_" + ind + ";");
	if (flag)
	{
		paySysActDIV.style["display"] = "none";
		eval("paySysActVisible_" + ind + " = false;");
	}
	else
	{
		paySysActDIV.style["display"] = "block";
		eval("paySysActVisible_" + ind + " = true;");
	}
	SetActLinkText(ind, !flag);
}

function ActionFileChange(ind, exist)
{
	ind = parseInt(ind);

	var paySysActDIV = document.getElementById("pay_sys_act_" + ind);
	paySysActDIV.style["backgroundColor"] = "#F1F1F1";
	paySysActDIV.innerHTML = '<font color="#FF0000"><?= GetMessage("SPS_WAIT") ?></font>';

	var oActionFile = document.forms['pay_sys_form'].elements["ACTION_FILE_" + ind];
	var psAction = oActionFile[oActionFile.selectedIndex].value;

	var curDescr = "";
	for (i = 0; i < arActionPaths.length; i++)
	{
		if (arActionPaths[i] == psAction)
		{
			curDescr = arActionDescrs[i];
			break;
		}
	}
	var paySysActDescrDIV = document.getElementById("pay_sys_act_descr_" + ind);
	paySysActDescrDIV.innerHTML = '' + curDescr + '';

	window.frames["hidden_action_frame_" + ind].location.replace('/bitrix/admin/sale_pay_system_get.php?lang=<?= htmlspecialcharsbx($lang) ?>&file='+escape(psAction)+'&divInd='+ind+'&exist='+exist);
}


var arUserFieldsList = new Array("ID", "LOGIN", "NAME", "SECOND_NAME", "LAST_NAME", "EMAIL", "LID", "PERSONAL_PROFESSION", "PERSONAL_WWW", "PERSONAL_ICQ", "PERSONAL_GENDER", "PERSONAL_FAX", "PERSONAL_MOBILE", "PERSONAL_STREET", "PERSONAL_MAILBOX", "PERSONAL_CITY", "PERSONAL_STATE", "PERSONAL_ZIP", "PERSONAL_COUNTRY", "WORK_COMPANY", "WORK_DEPARTMENT", "WORK_POSITION", "WORK_WWW", "WORK_PHONE", "WORK_FAX", "WORK_STREET", "WORK_MAILBOX", "WORK_CITY", "WORK_STATE", "WORK_ZIP", "WORK_COUNTRY");
var arUserFieldsNameList = new Array("<?= GetMessage("SPS_USER_ID") ?>", "<?= GetMessage("SPS_USER_LOGIN") ?>", "<?= GetMessage("SPS_USER_NAME") ?>", "<?= GetMessage("SPS_USER_SECOND_NAME") ?>", "<?= GetMessage("SPS_USER_LAST_NAME") ?>", "EMail", "<?= GetMessage("SPS_USER_SITE") ?>", "<?= GetMessage("SPS_USER_PROF") ?>", "<?= GetMessage("SPS_USER_WEB") ?>", "<?= GetMessage("SPS_USER_ICQ") ?>", "<?= GetMessage("SPS_USER_SEX") ?>", "<?= GetMessage("SPS_USER_FAX") ?>", "<?= GetMessage("SPS_USER_PHONE") ?>", "<?= GetMessage("SPS_USER_ADDRESS") ?>", "<?= GetMessage("SPS_USER_POST") ?>", "<?= GetMessage("SPS_USER_CITY") ?>", "<?= GetMessage("SPS_USER_STATE") ?>", "<?= GetMessage("SPS_USER_ZIP") ?>", "<?= GetMessage("SPS_USER_COUNTRY") ?>", "<?= GetMessage("SPS_USER_COMPANY") ?>", "<?= GetMessage("SPS_USER_DEPT") ?>", "<?= GetMessage("SPS_USER_DOL") ?>", "<?= GetMessage("SPS_USER_COM_WEB") ?>", "<?= GetMessage("SPS_USER_COM_PHONE") ?>", "<?= GetMessage("SPS_USER_COM_FAX") ?>", "<?= GetMessage("SPS_USER_COM_ADDRESS") ?>", "<?= GetMessage("SPS_USER_COM_POST") ?>", "<?= GetMessage("SPS_USER_COM_CITY") ?>", "<?= GetMessage("SPS_USER_COM_STATE") ?>", "<?= GetMessage("SPS_USER_COM_ZIP") ?>", "<?= GetMessage("SPS_USER_COM_COUNTRY") ?>");

var arOrderFieldsList = new Array("ID", "DATE_INSERT", "DATE_INSERT_DATE", "SHOULD_PAY", "CURRENCY", "PRICE", "LID", "PRICE_DELIVERY", "DISCOUNT_VALUE", "USER_ID", "PAY_SYSTEM_ID", "DELIVERY_ID", "TAX_VALUE");
var arOrderFieldsNameList = new Array("<?= GetMessage("SPS_ORDER_ID") ?>", "<?= GetMessage("SPS_ORDER_DATETIME") ?>", "<?= GetMessage("SPS_ORDER_DATE") ?>", "<?= GetMessage("SPS_ORDER_PRICE") ?>", "<?= GetMessage("SPS_ORDER_CURRENCY") ?>", "<?= GetMessage("SPS_ORDER_SUM") ?>", "<?= GetMessage("SPS_ORDER_SITE") ?>", "<?= GetMessage("SPS_ORDER_PRICE_DELIV") ?>", "<?= GetMessage("SPS_ORDER_DESCOUNT") ?>", "<?= GetMessage("SPS_ORDER_USER_ID") ?>", "<?= GetMessage("SPS_ORDER_PS") ?>", "<?= GetMessage("SPS_ORDER_DELIV") ?>", "<?= GetMessage("SPS_ORDER_TAX") ?>");

var arPropFieldsList = new Array();
var arPropFieldsNameList = new Array();


function PropertyTypeChange(pkey, ind, cVal)
{
	var oType = document.forms["pay_sys_form"].elements["TYPE_" + pkey + "_" + ind];
	var oValue1 = document.forms["pay_sys_form"].elements["VALUE1_" + pkey + "_" + ind];
	var oValue2 = document.forms["pay_sys_form"].elements["VALUE2_" + pkey + "_" + ind];

	eval("var cur_type = ''; if (typeof(param_" + pkey + "_type_" + ind + ") == 'string') cur_type = param_" + pkey + "_type_" + ind + ";");
	eval("var cur_val = ''; if (typeof(param_" + pkey + "_value_" + ind + ") == 'string') cur_val = param_" + pkey + "_value_" + ind + ";");

	var value1_length = oValue1.length;
	while (value1_length > 0)
	{
		value1_length--;
		oValue1.options[value1_length] = null;
	}
	value1_length = 0;

	var typeVal = oType[oType.selectedIndex].value;
	if (typeVal == "USER")
	{
		oValue2.style["display"] = "none";
		oValue1.style["display"] = "block";

		for (i = 0; i < arUserFieldsList.length; i++)
		{
			var newoption = new Option(arUserFieldsNameList[i], arUserFieldsList[i], false, false);
			oValue1.options[value1_length] = newoption;

			if ((typeVal == cur_type && cur_val == arUserFieldsList[i]) || cVal == arUserFieldsList[i])
				oValue1.selectedIndex = value1_length;

			value1_length++;
		}
	}
	else
	{
		if (typeVal == "ORDER")
		{
			oValue2.style["display"] = "none";
			oValue1.style["display"] = "block";

			for (i = 0; i < arOrderFieldsList.length; i++)
			{
				var newoption = new Option(arOrderFieldsNameList[i], arOrderFieldsList[i], false, false);
				oValue1.options[value1_length] = newoption;

				if ((typeVal == cur_type && cur_val == arOrderFieldsList[i]) || cVal == arOrderFieldsList[i])
					oValue1.selectedIndex = value1_length;

				value1_length++;
			}
		}
		else
		{
			if (typeVal == "PROPERTY")
			{
				oValue2.style["display"] = "none";
				oValue1.style["display"] = "block";

				for (i = 0; i < arPropFieldsList[ind].length; i++)
				{
					var newoption = new Option(arPropFieldsNameList[ind][i], arPropFieldsList[ind][i], false, false);
					oValue1.options[value1_length] = newoption;

					if ((typeVal == cur_type && cur_val == arPropFieldsList[ind][i]) || cVal == arPropFieldsList[ind][i])
						oValue1.selectedIndex = value1_length;

					value1_length++;
				}
			}
			else
			{
				oValue1.style["display"] = "none";
				oValue2.style["display"] = "block";

				oValue2.value = cur_val;
			}
		}
	}
}

function InitActionProps(pkey, ind)
{
	var oType = document.forms["pay_sys_form"].elements["TYPE_" + pkey + "_" + ind];
	eval("var cur_type = ''; if (typeof(param_" + pkey + "_type_" + ind + ") == 'string') cur_type = param_" + pkey + "_type_" + ind + ";");

	for (i = 0; i < oType.options.length; i++)
	{
		if (oType.options[i].value == cur_type)
		{
			oType.selectedIndex = i;
			break;
		}
	}
	PropertyTypeChange(pkey, ind);
}
//-->
</script>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="pay_sys_form">
<?echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANG ?>">
<input type="hidden" name="ID" value="<?echo $ID ?>">
<?=bitrix_sessid_post()?>
<?
$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("SPSN_TAB_PAYSYS"), "ICON" => "sale", "TITLE" => GetMessage("SPSN_TAB_PAYSYS_DESCR")),
		array("DIV" => "edit2", "TAB" => GetMessage("PPE_PERSON_TYPES"), "ICON" => "sale", "TITLE" => GetMessage("PPE_PERSON_TYPES")),
	);

$aTabs1 = Array();
$i = -1;
$arPersonTypeList = array();
$dbPersonType = CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array());
while ($arPersonType = $dbPersonType->Fetch())
{
	$i++;
	$aTabs1[] = array("DIV" => "editP".($i + 2), "TAB" => (htmlspecialcharsEx($arPersonType["NAME"])." (".implode(", ", $arPersonType["LIDS"]).")"), "ICON" => "sale", "TITLE" => str_replace("#PLTYPE#", htmlspecialcharsEx($arPersonType["NAME"]), GetMessage("SPSN_TAB_PAYSYS_DESCR_1")));
	$arPersonTypeList[$i] = $arPersonType;
}

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>

<?
$tabControl->BeginNextTab();
?>
	<?if ($ID>0):?>
		<input type="hidden" name="LID" value="<?= $str_LID ?>">
		<input type="hidden" name="CURRENCY" value="<?= $str_CURRENCY?>">
		<tr>
			<td width="40%">ID:</td>
			<td width="60%"><?= $ID ?></td>
		</tr>
	<?endif;?>
	<tr class="adm-detail-required-field">
		<td width="40%"><?= GetMessage("F_NAME") ?>:</td>
		<td width="60%">
			<input type="text" name="NAME" value="<?= $str_NAME ?>" size="40">
		</td>
	</tr>
	<tr>
		<td width="40%"><?= GetMessage("F_ACTIVE");?>:</td>
		<td width="60%">
			<input type="checkbox" name="ACTIVE" value="Y" <?if ($str_ACTIVE == "Y") echo "checked"?>>
		</td>
	</tr>
	<tr>
		<td width="40%"><?= GetMessage("F_SORT");?>:</td>
		<td width="60%">
			<input type="text" name="SORT" value="<?= $str_SORT ?>" size="5">
		</td>
	</tr>
	<tr>
		<td width="40%" valign="top"><?= GetMessage("F_DESCRIPTION");?>:</td>
		<td width="60%" valign="top">
			<textarea rows="5" cols="40" name="DESCRIPTION"><?= $str_DESCRIPTION;?></textarea>
		</td>
	</tr>
<?
$tabControl->EndTab();
	// Get arrays of PS actions
	$arUserPSActions = array();
	$arSystemPSActions = array();

	function LocalGetPSActionDescr($fileName)
	{
		$psTitle = "";
		$psDescription = "";

		if (file_exists($fileName) && is_file($fileName))
			include($fileName);

		return array($psTitle, $psDescription);
	}

	function LocalGetPSActionDescr_old($fileName)
	{
		if (!file_exists($fileName))
			return false;

		$handle = fopen($fileName, "r");
		$contents = fread($handle, filesize($fileName));
		fclose($handle);

		$rep_title = "";
		$rep_descr = "";

		$arMatches = array();
		if (preg_match("#<title_".LANGUAGE_ID."[^>]*>([^<]*?)</title_".LANGUAGE_ID."[\s]*>#i", $contents, $arMatches))
		{
			$arMatches[1] = Trim($arMatches[1]);
			if (strlen($arMatches[1])>0) $rep_title = $arMatches[1];
		}
		if (strlen($rep_title)<=0
			&& preg_match("#<title[^>]*>([^<]*?)</title[\s]*>#i", $contents, $arMatches))
		{
			$arMatches[1] = Trim($arMatches[1]);
			if (strlen($arMatches[1])>0) $rep_title = $arMatches[1];
		}
		if (strlen($rep_title)<=0)
			$rep_title = basename($strPathFull, ".php");

		$arMatches = array();
		if (preg_match("#<description_".LANGUAGE_ID."[^>]*>([^<]*?)</description_".LANGUAGE_ID."[\s]*>#i", $contents, $arMatches))
		{
			$arMatches[1] = Trim($arMatches[1]);
			if (strlen($arMatches[1])>0) $rep_descr = $arMatches[1];
		}
		if (strlen($rep_descr)<=0
			&& preg_match("#<description[^>]*>([^<]*?)</description[\s]*>#i", $contents, $arMatches))
		{
			$arMatches[1] = Trim($arMatches[1]);
			if (strlen($arMatches[1])>0) $rep_descr = $arMatches[1];
		}

		return array($rep_title, $rep_descr);
	}

	$handle = @opendir($_SERVER["DOCUMENT_ROOT"].$path2UserPSFiles);
	if ($handle)
	{
		while (false !== ($dir = readdir($handle)))
		{
			if ($dir == "." || $dir == "..")
				continue;
			$title = "";
			$description = "";

			if (is_dir($_SERVER["DOCUMENT_ROOT"].$path2UserPSFiles.$dir))
			{
				$newFormat = "Y";
				list($title, $description) = LocalGetPSActionDescr($_SERVER["DOCUMENT_ROOT"].$path2UserPSFiles.$dir."/.description.php");
				if (strlen($title) <= 0)
					$title = $dir;
				else
					$title .= " (".$dir.")";
			}
			elseif (is_file($_SERVER["DOCUMENT_ROOT"].$path2UserPSFiles.$dir))
			{
				$newFormat = "N";
				list($title, $description) = LocalGetPSActionDescr_old($_SERVER["DOCUMENT_ROOT"].$path2UserPSFiles.$dir);
				if (strlen($title) <= 0)
					$title = $dir;
				else
					$title .= " (".$dir.")";
			}

			if(strlen($title) > 0)
			{
				$arUserPSActions[] = array(
						"PATH" => $path2UserPSFiles.$dir,
						"TITLE" => $title,
						"DESCRIPTION" => $description,
						"NEW_FORMAT" => $newFormat
					);
			}
		}
		@closedir($handle);
	}

	$handle = @opendir($_SERVER["DOCUMENT_ROOT"].$path2SystemPSFiles);
	if ($handle)
	{
		while (false !== ($dir = readdir($handle)))
		{
			if ($dir == "." || $dir == "..")
				continue;

			if (is_dir($_SERVER["DOCUMENT_ROOT"].$path2SystemPSFiles.$dir))
			{
				$newFormat = "Y";
				list($title, $description) = LocalGetPSActionDescr($_SERVER["DOCUMENT_ROOT"].$path2SystemPSFiles.$dir."/.description.php");
				if (strlen($title) <= 0)
					$title = $dir;
				else
					$title .= " (".$dir.")";
			}
			elseif (is_file($_SERVER["DOCUMENT_ROOT"].$path2SystemPSFiles.$dir))
			{
				$newFormat = "N";
				list($title, $description) = LocalGetPSActionDescr_old($_SERVER["DOCUMENT_ROOT"].$path2SystemPSFiles.$dir);
				if (strlen($title) <= 0)
					$title = $dir;
				else
					$title .= " (".$dir.")";
			}

			$arSystemPSActions[] = array(
					"PATH" => $path2SystemPSFiles.$dir,
					"TITLE" => $title,
					"DESCRIPTION" => $description,
					"NEW_FORMAT" => $newFormat
				);
		}
		@closedir($handle);
	}
	
	$tabControl->BeginNextTab();
	?>
	<tr>
		<td colspan="2">
	<?
	$tabControl1 = new CAdminViewTabControl("tabControl1", $aTabs1);
	$tabControl1->Begin();

	for ($tpInd = 0; $tpInd < count($arPersonTypeList); $tpInd++)
	{
		$arPersonType = $arPersonTypeList[$tpInd];

		$tabControl1->BeginNextTab();
		?>
		<script language="JavaScript">
		<!--
		arPropFieldsList[<?= $arPersonType["ID"] ?>] = new Array();
		arPropFieldsNameList[<?= $arPersonType["ID"] ?>] = new Array();
		<?
		$dbOrderProps = CSaleOrderProps::GetList(
				array("SORT" => "ASC", "NAME" => "ASC"),
				array("PERSON_TYPE_ID" => $arPersonType["ID"]),
				false,
				false,
				array("ID", "CODE", "NAME", "TYPE", "SORT")
			);
		$i = -1;
		while ($arOrderProps = $dbOrderProps->Fetch())
		{
			$i++;
			?>
			arPropFieldsList[<?= $arPersonType["ID"] ?>][<?= $i ?>] = '<?= CUtil::JSEscape(((strlen($arOrderProps["CODE"])>0) ? $arOrderProps["CODE"] : $arOrderProps["ID"])) ?>';
			arPropFieldsNameList[<?= $arPersonType["ID"] ?>][<?= $i ?>] = '<?= CUtil::JSEscape($arOrderProps["NAME"]) ?>';
			<?
			if ($arOrderProps["TYPE"] == "LOCATION")
			{
				$i++;
				?>
				arPropFieldsList[<?= $arPersonType["ID"] ?>][<?= $i ?>] = '<?= CUtil::JSEscape(((strlen($arOrderProps["CODE"])>0) ? $arOrderProps["CODE"] : $arOrderProps["ID"])."_COUNTRY") ?>';
				arPropFieldsNameList[<?= $arPersonType["ID"] ?>][<?= $i ?>] = '<?= CUtil::JSEscape($arOrderProps["NAME"]." (".GetMessage("SPS_JCOUNTRY").")") ?>';
				<?

				$i++;
				?>
				arPropFieldsList[<?= $arPersonType["ID"] ?>][<?= $i ?>] = '<?= CUtil::JSEscape(((strlen($arOrderProps["CODE"])>0) ? $arOrderProps["CODE"] : $arOrderProps["ID"])."_CITY") ?>';
				arPropFieldsNameList[<?= $arPersonType["ID"] ?>][<?= $i ?>] = '<?= CUtil::JSEscape($arOrderProps["NAME"]." (".GetMessage("SPS_JCITY").")") ?>';
				<?
			}
		}
		?>
		//-->
		</script>
		<?
		unset($f_ID); unset($f_NAME); unset($f_ACTION_FILE); unset($f_NEW_WINDOW);

		$dbPaySystemAction = CSalePaySystemAction::GetList(
				array("NAME" => "ASC"),
				array("PAY_SYSTEM_ID" => $ID, "PERSON_TYPE_ID" => $arPersonType["ID"])
			);
		$arPaySystemAction = $dbPaySystemAction->ExtractFields("f_");

		if ($bInitVars)
		{
			$DB->InitTableVarsForEdit("b_sale_pay_system_action", "", "f_", "_".$arPersonType["ID"]);
		}
		?>
		<table cellspacing="5" cellpadding="0" border="0" width="100%">
		<tr>
			<td width="40%" align="right">
				<?= GetMessage("SPS_EXISTS") ?>:
			</td>
			<td width="60%">
				<input type="hidden" name="ID_<?= $arPersonType["ID"] ?>" value="<?= $f_ID ?>">
				<input type="checkbox" name="PS_EXISTS_<?= $arPersonType["ID"] ?>" value="Y"<?if ($arPaySystemAction) echo " checked";?>>
			</td>
		</tr>
		<?if(COption::GetOptionString("sale", "show_paysystem_action_id", "N") == "Y" && IntVal($arPaySystemAction["ID"]) > 0):?>
		<tr>
			<td width="40%" align="right"><?= GetMessage("SPS_ACTION_ID") ?>:</td>
			<td width="60%">
				<?= IntVal($arPaySystemAction["ID"]) ?>
			</td>
		</tr>
		<?endif;?>
		<tr class="adm-detail-required-field">
			<td width="40%" class="adm-detail-content-cell-l"><?= GetMessage("SPS_NAME") ?>:</td>
			<td width="60%">
				<input type="text" name="NAME_<?= $arPersonType["ID"] ?>" value="<?= $f_NAME ?>" size="60">
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%" valign="top" class="adm-detail-content-cell-l"><?= GetMessage("SPS_ACT_FILE") ?>:</td>
			<td width="60%">
				<? 
				$f_ACTION_FILE = str_replace("\\", "/", $f_ACTION_FILE);
				while (strpos($f_ACTION_FILE, "//") !== false)
					$f_ACTION_FILE = str_replace("//", "/", $f_ACTION_FILE);
				while (substr($f_ACTION_FILE, strlen($f_ACTION_FILE) - 1, 1) == "/")
					$f_ACTION_FILE = substr($f_ACTION_FILE, 0, strlen($f_ACTION_FILE) - 1);
				?>
				<script language="JavaScript">
				<!--
				arActionPaths = new Array();
				arActionDescrs = new Array();
				<?
				for ($i = 0; $i < count($arUserPSActions); $i++)
				{
					?>
					arActionPaths[<?= $i ?>] = '<?= $arUserPSActions[$i]["PATH"] ?>';
					arActionDescrs[<?= $i ?>] = '<?= CUtil::JSEscape($arUserPSActions[$i]["DESCRIPTION"]) ?>';
					<?
				}
				for ($i = 0; $i < count($arSystemPSActions); $i++)
				{
					?>
					arActionPaths[<?= $i + count($arUserPSActions) ?>] = '<?= $arSystemPSActions[$i]["PATH"] ?>';
					arActionDescrs[<?= $i + count($arUserPSActions) ?>] = '<?= CUtil::JSEscape($arSystemPSActions[$i]["DESCRIPTION"]) ?>';
					<?
				}
				?>
				//-->
				</script>
				<select name="ACTION_FILE_<?= $arPersonType["ID"] ?>" OnChange="ActionFileChange(<?= $arPersonType["ID"] ?>, 'Y')">
					<option value=""><?= GetMessage("SPS_NO_ACT_FILE") ?></option>
					<option value="">---- <?= GetMessage("SPS_ACT_USER") ?> ----</option>
					<?
					for ($i = 0; $i < count($arUserPSActions); $i++)
					{
						?><option value="<?= htmlspecialcharsbx($arUserPSActions[$i]["PATH"]) ?>"<?if ($f_ACTION_FILE == $arUserPSActions[$i]["PATH"]) echo " selected";?>><?= htmlspecialcharsEx($arUserPSActions[$i]["TITLE"]) ?></option><?
					}
					?>
					<option value="">---- <?= GetMessage("SPS_ACT_SYSTEM") ?> ----</option>
					<?
					for ($i = 0; $i < count($arSystemPSActions); $i++)
					{
						?><option value="<?= htmlspecialcharsbx($arSystemPSActions[$i]["PATH"]) ?>"<?if ($f_ACTION_FILE == $arSystemPSActions[$i]["PATH"]) echo " selected";?>><?= htmlspecialcharsEx($arSystemPSActions[$i]["TITLE"]) ?></option><?
					}
					?>
				</select>
				<div id="pay_sys_act_descr_<?= $arPersonType["ID"] ?>"></div>
			</td>
		</tr>
		<tr>
			<td width="40%" align="right"><?= GetMessage("SPS_NEW_WINDOW") ?>:</td>
			<td width="60%">
				<input type="checkbox" name="NEW_WINDOW_<?= $arPersonType["ID"] ?>" value="Y"<?if ($f_NEW_WINDOW == "Y") echo " checked";?>>
			</td>
		</tr>
		<tr>
			<td width="40%" align="right"><?= GetMessage("SPS_ENCODING") ?>:</td>
			<td width="60%">
				<select name="ENCODING_<?= $arPersonType["ID"] ?>">
					<option value="" <? if ($f_ENCODING == "") echo "selected"?>></option>
					<option value="windows-1251" <? if ($f_ENCODING == "windows-1251") echo "selected"?>>windows-1251</option>
					<option value="utf-8" <? if ($f_ENCODING == "utf-8") echo "selected"?>>utf-8</option>
					<option value="iso-8859-1" <? if ($f_ENCODING == "iso-8859-1") echo "selected"?>>iso-8859-1</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top" align="center" colspan="2">
				<script language="JavaScript">
				<!--
				var paySysActVisible_<?= $arPersonType["ID"] ?> = true;
				<?
				if ($bInitVars)
				{
					$arActFields = explode(",", ${"PS_ACTION_FIELDS_LIST_".$arPersonType["ID"]});
					for ($i = 0; $i < count($arActFields); $i++)
					{
						$arActFields[$i] = Trim($arActFields[$i]);
						$valueTmp = ${"VALUE1_".$arActFields[$i]."_".$arPersonType["ID"]};
						if (strlen($typeTmp) <= 0)
							$valueTmp = ${"VALUE2_".$arActFields[$i]."_".$arPersonType["ID"]};
						?>
						var param_<?= $arActFields[$i] ?>_type_<?= $arPersonType["ID"] ?> = '<?= CUtil::JSEscape(${"TYPE_".$arActFields[$i]."_".$arPersonType["ID"]}) ?>';
						var param_<?= $arActFields[$i] ?>_value_<?= $arPersonType["ID"] ?> = '<?= CUtil::JSEscape($valueTmp) ?>';
						<?
					}
				}
				else
				{
					$arCorrespondence = CSalePaySystemAction::UnSerializeParams($arPaySystemAction["PARAMS"]);
					foreach ($arCorrespondence as $key => $value)
					{
						?>
						var param_<?= $key ?>_type_<?= $arPersonType["ID"] ?> = '<?= CUtil::JSEscape($value["TYPE"]) ?>';
						var param_<?= $key ?>_value_<?= $arPersonType["ID"] ?> = '<?= CUtil::JSEscape($value["VALUE"]) ?>';
						<?
					}
				}
				?>
				//-->
				</script>
				<div id="pay_sys_act_<?= $arPersonType["ID"] ?>" style="display: block; background-color: #E4EDF3;"></div>
				<a href="javascript:ShowHideStatus(<?= $arPersonType["ID"] ?>);" id="pay_sys_switch_<?= $arPersonType["ID"] ?>"><br><?= GetMessage("SPS_HIDE_PROPS") ?></a>
				<iframe style="width:0px; height:0px; border: 0px" name="hidden_action_frame_<?= $arPersonType["ID"] ?>" src="" width="0" height="0"></iframe>
			</td>
		</tr>

		<tr>
			<td valign="top" align="right" colspan="2">
				<script language="JavaScript">
				<!--
				ActionFileChange(<?= $arPersonType["ID"] ?>);
				//-->
				</script>
				<input type="hidden" name="PS_ACTION_FIELDS_LIST_<?= $arPersonType["ID"] ?>" value="">
			</td>
		</tr>
		</table>
		<?
	}
	$tabControl1->End();
	?>
	</td></tr>
	<?
	$tabControl->EndTab();

$tabControl->Buttons(
		array(
				"disabled" => ($saleModulePermissions < "W"),
				"back_url" => "/bitrix/admin/sale_pay_system.php?lang=".LANG.GetFilterParams("filter_")
			)
	);
$tabControl->End();
?>
</form>
<?require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");?>