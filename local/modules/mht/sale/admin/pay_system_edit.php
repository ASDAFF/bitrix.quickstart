<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions < "W")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

if (isset($_REQUEST["https_check"]) && $_REQUEST["https_check"] == "Y" && check_bitrix_sessid())
{
	$ob = new CHTTP();
	$ob->http_timeout = 10;
	$res = @$ob->Get("https://".$_SERVER["SERVER_NAME"]."/bitrix/tools/sale_ps_result.php");
	if (!$res || $ob->status != 200)
	{
		$res = "error";
		$text = GetMessage("SPS_HTTPS_CHECK_ERROR");
	}
	else
	{
		$res = "ok";
		$text = GetMessage("SPS_HTTPS_CHECK_SUCCESS");
	}

	header("Content-Type: application/x-javascript; charset=".LANG_CHARSET);
	echo CUtil::PhpToJSObject(array("status" => $res, "text" => $text));
	die();
}

$lheStyle = '
<style type="text/css">
	.bxlhe_frame_hndl_dscr {
		-moz-border-bottom-colors: none;
		-moz-border-left-colors: none;
		-moz-border-right-colors: none;
		-moz-border-top-colors: none;
		background: none repeat scroll 0 0 #FFFFFF;
		border-color: #87919C #959EA9 #9EA7B1;
		border-image: none;
		border-radius: 4px 4px 4px 4px;
		border-style: solid;
		border-width: 1px;
		box-shadow: 0 1px 0 0 rgba(255, 255, 255, 0.3), 0 2px 2px -1px rgba(180, 188, 191, 0.7) inset;
		color: #000000;
		display: inline-block;
		outline: medium none;
		vertical-align: middle;
		!important;
	}
</style>';

$APPLICATION->AddHeadString($lheStyle, true, true);

$ID = IntVal($ID);

ClearVars();
ClearVars("f_");

$path2SystemPSFiles = "/bitrix/modules/sale/payment/";
$path2UserPSFiles = COption::GetOptionString("sale", "path2user_ps_files", BX_PERSONAL_ROOT."/php_interface/include/sale_payment/");
CheckDirPath($_SERVER["DOCUMENT_ROOT"].$path2UserPSFiles);

if (CModule::IncludeModule("fileman"))
	$bFilemanModuleInst = true;

$aTabs = array(
	array(
		"DIV" => "edit1",
		"TAB" => GetMessage("SPSN_TAB_PAYSYS"),
		"ICON" => "sale",
		"TITLE" => GetMessage("SPSN_TAB_PAYSYS_DESCR"),
	),
	array(
		"DIV" => "edit2",
		"TAB" => GetMessage("PPE_PERSON_TYPES"),
		"ICON" => "sale",
		"TITLE" => GetMessage("PPE_PERSON_TYPES"),
	),
	array(
		"DIV" => "edit3",
		"TAB" => GetMessage("SPS_DELIVERY_HANDLERS"),
		"ICON" => "sale",
		"TITLE" => GetMessage("SPS_DELIVERY_HANDLERS_DESC"),
	),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);


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

	//delivery for pay system
	if (is_set($_POST["DELIVERY_HANDLERS"]) && is_array($_POST["DELIVERY_HANDLERS"]))
	{
		CSaleDelivery2PaySystem::UpdatePaySystem($ID, $_POST["DELIVERY_HANDLERS"]);
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

						$arPsActFields = LocalGetPSActionParams($pathToAction.'/.description.php');
						$arPSActionParams = CSalePaySystemAction::UnSerializeParams($arPSAction["PARAMS"]);

						foreach ($arActFields as $val)
						{
							$val = Trim($val);
							$fieldName = $val."_".$arPersonType["ID"];

							if (empty($arPsActFields[$val]))
								continue;

							$typeTmp = ${"TYPE_".$fieldName};
							$valueTmp = ${"VALUE1_".$fieldName};
							if (strlen($typeTmp) <= 0)
								$valueTmp = ${"VALUE2_".$fieldName};

							if ($arPsActFields[$val]['TYPE'] == 'FILE' && $typeTmp != 'FILE')
								continue;

							if ($typeTmp == 'FILE')
							{
								$valueTmp = array();
								if (array_key_exists("VALUE1_".$fieldName, $_FILES))
								{
									if ($_FILES["VALUE1_".$fieldName]["error"] == 0)
									{
										$imageFileError = CFile::CheckImageFile($_FILES["VALUE1_".$fieldName]);

										if (is_null($imageFileError))
											$valueTmp = $_FILES["VALUE1_".$fieldName];
										else
											$errorMessage1 .= $imageFileError . ".<br>";
									}
								}

								if (trim($_POST[$fieldName."_del"]) == 'Y')
								{
									if (intval($arPSActionParams[$val]['VALUE']) == 0)
										continue;

									$valueTmp['old_file'] = $arPSActionParams[$val]['VALUE'];
									$valueTmp['del'] = trim($_POST[$fieldName."_del"]);
								}

								if (empty($valueTmp))
								{
									$typeTmp  = $arPSActionParams[$val]['TYPE'];
									$valueTmp = $arPSActionParams[$val]['VALUE'];
								}
							}

							$arParams[$val] = array(
								"TYPE"  => $typeTmp,
								"VALUE" => $valueTmp
							);

							if ($arParams[$val]['TYPE'] == 'FILE' && is_array($arParams[$val]['VALUE']))
							{
								$arParams[$val]['VALUE']['MODULE_ID'] = 'sale';
								CFile::SaveForDB($arParams[$val], 'VALUE', 'sale/paysystem/field');
							}
						}
					}

					if(isset($_POST["TARIF_".$arPersonType["ID"]]))
						$arTarif = $_POST["TARIF_".$arPersonType["ID"]];
					else
						$arTarif = array();
					//add logotip
					$arPicture = array();
					if(array_key_exists("LOGOTIP_".$arPersonType["ID"], $_FILES) && $_FILES["LOGOTIP_".$arPersonType["ID"]]["error"] == 0)
						$arPicture = $_FILES["LOGOTIP_".$arPersonType["ID"]];
					elseif ($actionID <= 0)
					{
						$logo = "";

						if (file_exists($_SERVER["DOCUMENT_ROOT"].${"ACTION_FILE_".$arPersonType["ID"]}."/logo.png"))
							$logo = $_SERVER["DOCUMENT_ROOT"].${"ACTION_FILE_".$arPersonType["ID"]}."/logo.png";
						elseif (file_exists($_SERVER["DOCUMENT_ROOT"].${"ACTION_FILE_".$arPersonType["ID"]}."/logo.jpg"))
							$logo = $_SERVER["DOCUMENT_ROOT"].${"ACTION_FILE_".$arPersonType["ID"]}."/logo.jpg";
						elseif (file_exists($_SERVER["DOCUMENT_ROOT"].${"ACTION_FILE_".$arPersonType["ID"]}."/logo.gif"))
							$logo = $_SERVER["DOCUMENT_ROOT"].${"ACTION_FILE_".$arPersonType["ID"]}."/logo.gif";

						$arPicture = CFile::MakeFileArray($logo);
					}

					$arPicture["old_file"] = $arPSAction["LOGOTIP"];
					$arPicture["del"] = trim($_POST["LOGOTIP_".$arPersonType["ID"]."_del"]);

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
							"LOGOTIP" => $arPicture
						);

					if(!empty($arTarif) && is_array($arTarif))
						$arFields["TARIF"] = CSalePaySystemsHelper::prepareTarifForSaving($arFields["ACTION_FILE"], $arTarif);

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

					if (strlen($errorMessage1) <= 0)
					{
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

	if (strlen($errorMessage) <= 0)
	{
		if (strlen($apply) > 0)
			LocalRedirect("sale_pay_system_edit.php?ID=".$ID."&lang=".LANG."&".$tabControl->ActiveTabParam());
		else
			LocalRedirect("sale_pay_system.php?lang=".LANG);
	}
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

	window.frames["hidden_action_frame_" + ind].location.replace('/bitrix/admin/sale_pay_system_get.php?lang=<?= htmlspecialcharsbx($lang) ?>&file='+escape(psAction)+'&divInd='+ind+'&exist='+exist+'&psid=<?=$ID?>');
}


var arUserFieldsList = new Array(
	"ID",
	"LOGIN",
	"NAME",
	"SECOND_NAME",
	"LAST_NAME",
	"EMAIL",
	"LID",
	"PERSONAL_PROFESSION",
	"PERSONAL_WWW",
	"PERSONAL_ICQ",
	"PERSONAL_GENDER",
	"PERSONAL_FAX",
	"PERSONAL_MOBILE",
	"PERSONAL_STREET",
	"PERSONAL_MAILBOX",
	"PERSONAL_CITY",
	"PERSONAL_STATE",
	"PERSONAL_ZIP",
	"PERSONAL_COUNTRY",
	"WORK_COMPANY",
	"WORK_DEPARTMENT",
	"WORK_POSITION",
	"WORK_WWW",
	"WORK_PHONE",
	"WORK_FAX",
	"WORK_STREET",
	"WORK_MAILBOX",
	"WORK_CITY",
	"WORK_STATE",
	"WORK_ZIP",
	"WORK_COUNTRY"
);
var arUserFieldsNameList = new Array(
	"<?= GetMessageJS("SPS_USER_ID") ?>",
	"<?= GetMessageJS("SPS_USER_LOGIN") ?>",
	"<?= GetMessageJS("SPS_USER_NAME") ?>",
	"<?= GetMessageJS("SPS_USER_SECOND_NAME") ?>",
	"<?= GetMessageJS("SPS_USER_LAST_NAME") ?>",
	"EMail",
	"<?= GetMessageJS("SPS_USER_SITE") ?>",
	"<?= GetMessageJS("SPS_USER_PROF") ?>",
	"<?= GetMessageJS("SPS_USER_WEB") ?>",
	"<?= GetMessageJS("SPS_USER_ICQ") ?>",
	"<?= GetMessageJS("SPS_USER_SEX") ?>",
	"<?= GetMessageJS("SPS_USER_FAX") ?>",
	"<?= GetMessageJS("SPS_USER_PHONE") ?>",
	"<?= GetMessageJS("SPS_USER_ADDRESS") ?>",
	"<?= GetMessageJS("SPS_USER_POST") ?>",
	"<?= GetMessageJS("SPS_USER_CITY") ?>",
	"<?= GetMessageJS("SPS_USER_STATE") ?>",
	"<?= GetMessageJS("SPS_USER_ZIP") ?>",
	"<?= GetMessageJS("SPS_USER_COUNTRY") ?>",
	"<?= GetMessageJS("SPS_USER_COMPANY") ?>",
	"<?= GetMessageJS("SPS_USER_DEPT") ?>",
	"<?= GetMessageJS("SPS_USER_DOL") ?>",
	"<?= GetMessageJS("SPS_USER_COM_WEB") ?>",
	"<?= GetMessageJS("SPS_USER_COM_PHONE") ?>",
	"<?= GetMessageJS("SPS_USER_COM_FAX") ?>",
	"<?= GetMessageJS("SPS_USER_COM_ADDRESS") ?>",
	"<?= GetMessageJS("SPS_USER_COM_POST") ?>",
	"<?= GetMessageJS("SPS_USER_COM_CITY") ?>",
	"<?= GetMessageJS("SPS_USER_COM_STATE") ?>",
	"<?= GetMessageJS("SPS_USER_COM_ZIP") ?>",
	"<?= GetMessageJS("SPS_USER_COM_COUNTRY") ?>"
);

var arOrderFieldsList = new Array(
	"ID",
	"ACCOUNT_NUMBER",
	"DATE_INSERT",
	"DATE_INSERT_DATE",
	"DATE_PAY_BEFORE",
	"SHOULD_PAY",
	"CURRENCY",
	"PRICE",
	"LID",
	"PRICE_DELIVERY",
	"DISCOUNT_VALUE",
	"USER_ID",
	"PAY_SYSTEM_ID",
	"DELIVERY_ID",
	"TAX_VALUE"
);

var arOrderFieldsNameList = new Array(
	"<?= GetMessage("SPS_ORDER_ID") ?>",
	"<?= GetMessage("SPS_ORDER_ACCOUNT_NUMBER") ?>",
	"<?= GetMessage("SPS_ORDER_DATETIME") ?>",
	"<?= GetMessage("SPS_ORDER_DATE") ?>",
	"<?= GetMessage("SPS_ORDER_PAY_BEFORE") ?>",
	"<?= GetMessage("SPS_ORDER_PRICE") ?>",
	"<?= GetMessage("SPS_ORDER_CURRENCY") ?>",
	"<?= GetMessage("SPS_ORDER_SUM") ?>",
	"<?= GetMessage("SPS_ORDER_SITE") ?>",
	"<?= GetMessage("SPS_ORDER_PRICE_DELIV") ?>",
	"<?= GetMessage("SPS_ORDER_DESCOUNT") ?>",
	"<?= GetMessage("SPS_ORDER_USER_ID") ?>",
	"<?= GetMessage("SPS_ORDER_PS") ?>",
	"<?= GetMessage("SPS_ORDER_DELIV") ?>",
	"<?= GetMessage("SPS_ORDER_TAX") ?>"
);

var arPropFieldsList = new Array();
var arPropFieldsNameList = new Array();


function PropertyTypeChange(pkey, ind, cVal)
{
	var oType = document.forms["pay_sys_form"].elements["TYPE_" + pkey + "_" + ind];
	var oValue1 = document.forms["pay_sys_form"].elements["VALUE1_" + pkey + "_" + ind];
	var oValue2 = document.forms["pay_sys_form"].elements["VALUE2_" + pkey + "_" + ind];

	eval("var cur_type = ''; if (typeof(param_" + pkey + "_type_" + ind + ") == 'string') cur_type = param_" + pkey + "_type_" + ind + ";");
	eval("var cur_val = ''; if (typeof(param_" + pkey + "_value_" + ind + ") == 'string') cur_val = param_" + pkey + "_value_" + ind + ";");

	var typeVal = oType[oType.selectedIndex].value;

	if (typeVal != "SELECT" && typeVal != "RADIO")
	{
		var value1_length = oValue1.length;
		while (value1_length > 0)
		{
			value1_length--;
			oValue1.options[value1_length] = null;
		}
		value1_length = 0;
	}

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
			else if (typeVal == "SELECT")
			{
				oValue1.value = cur_val;
			}
			else if (typeVal == "SELECT" || typeVal == "RADIO")
			{
				document.getElementById('VALUE1_'+cur_val+'_'+ind).checked  = true;
			}
			else if (typeVal == "FILE")
			{
				if (cur_val)
				{
					BX(pkey + '_' + ind + '_preview_img').setAttribute('src', cur_val);
					BX.adminFormTools.modifyCheckbox(BX(pkey + '_' + ind + '_del'));
				}
				else
				{
					BX(pkey + '_' + ind + '_preview').style.display = "none";
				}

				BX.adminFormTools.modifyFile(oValue1);
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
	if (document.forms["pay_sys_form"].elements["TYPE_" + pkey + "_" + ind])
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
}

function setTarifValues(personTypeId)
{
	var oTarif = window["tarif_"+personTypeId];

	if(!oTarif)
		return;

	for(var i in oTarif)
	{
		var tarifInputObj = document.forms["pay_sys_form"].elements["TARIF_"+personTypeId+"["+i+"]"];

		if(tarifInputObj)
			tarifInputObj.value = oTarif[i];
	}
}

function setLHEClass(lheDivId)
{
	BX.ready(
		function(){
			var lheDivObj = BX(lheDivId);

			if(lheDivObj)
				BX.addClass(lheDivObj, 'bxlhe_frame_hndl_dscr');
	});
}
//-->
</script>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="pay_sys_form" enctype="multipart/form-data">
<?echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANG ?>">
<input type="hidden" name="ID" value="<?echo $ID ?>">
<?=bitrix_sessid_post()?>
<?

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

$tabControl->Begin();
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
			<?=wrapDescrLHE("DESCRIPTION", htmlspecialcharsback($str_DESCRIPTION), "hndl_dscr_".$ID);?>
			<script language="JavaScript">setLHEClass('bxlhe_frame_hndl_dscr_<?=$ID?>'); </script>
		</td>
	</tr>
<?
$tabControl->EndTab();
	// Get arrays of PS actions
	$arUserPSActions = array();
	$arSystemPSActions = array();

	function LocalGetPSActionParams($fileName)
	{
		$arPSCorrespondence = array();

		if (file_exists($fileName) && is_file($fileName))
			include($fileName);

		return $arPSCorrespondence;
	}

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

	foreach($arPersonTypeList as $arPersonType)
	{
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
				arPropFieldsList[<?= $arPersonType["ID"] ?>][<?= $i ?>] = '<?= CUtil::JSEscape(((strlen($arOrderProps["CODE"])>0) ? $arOrderProps["CODE"] : $arOrderProps["ID"])."_REGION") ?>';
				arPropFieldsNameList[<?= $arPersonType["ID"] ?>][<?= $i ?>] = '<?= CUtil::JSEscape($arOrderProps["NAME"]." (".GetMessage("SPS_JREGION").")") ?>';
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
				$ij = 0;
				foreach($arUserPSActions as $val)
				{
					?>
					arActionPaths[<?= $ij ?>] = '<?= $val["PATH"] ?>';
					arActionDescrs[<?= $ij ?>] = '<?= CUtil::JSEscape($val["DESCRIPTION"]) ?>';
					<?
					$ij++;
				}
				foreach($arSystemPSActions as $val)
				{
					?>
					arActionPaths[<?=$ij?>] = '<?= $val["PATH"] ?>';
					arActionDescrs[<?=$ij?>] = '<?= CUtil::JSEscape($val["DESCRIPTION"]) ?>';
					<?
					$ij++;
				}
				?>
				//-->
				</script>
				<select name="ACTION_FILE_<?= $arPersonType["ID"] ?>" OnChange="ActionFileChange(<?= $arPersonType["ID"] ?>, 'Y')">
					<option value=""><?= GetMessage("SPS_NO_ACT_FILE") ?></option>
					<option value="">---- <?= GetMessage("SPS_ACT_USER") ?> ----</option>
					<?
					foreach($arUserPSActions as $val)
					{
						?><option value="<?= htmlspecialcharsbx($val["PATH"]) ?>"<?if ($f_ACTION_FILE == $val["PATH"]) echo " selected";?>><?= htmlspecialcharsEx($val["TITLE"]) ?></option><?
					}
					?>
					<option value="">---- <?= GetMessage("SPS_ACT_SYSTEM") ?> ----</option>
					<?
					foreach($arSystemPSActions as $val)
					{
						?><option value="<?= htmlspecialcharsbx($val["PATH"]) ?>"<?if ($f_ACTION_FILE == $val["PATH"]) echo " selected";?>><?= htmlspecialcharsEx($val["TITLE"]) ?></option><?
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
			<td width="40%"><?=GetMessage('SPS_LOGOTIP')?></td>
			<td width="60%">
				<div><input type="file" name="LOGOTIP_<?=$arPersonType["ID"]?>"></div>
				<?if ($arPaySystemAction["LOGOTIP"] > 0):?>
					<br>
					<?
					$arLogotip = CFile::GetFileArray($arPaySystemAction["LOGOTIP"]);
					echo CFile::ShowImage($arLogotip, 150, 150, "border=0", "", false);
					?>
					<div style="margin-top:10px;">
					<input type="checkbox" name="LOGOTIP_<?=$arPersonType["ID"]?>_del" value="Y" id="LOGOTIP_<?=$arPersonType["ID"]?>_del" >
					<label for="LOGOTIP_<?=$arPersonType["ID"]?>_del"><?=GetMessage("SPS_LOGOTIP_DEL");?></label>
					</div>
				<?endif;?>
			</td>
		</tr>
		<tr>
			<td valign="top" align="center" colspan="2">
				<script language="JavaScript">
				<!--
				var paySysActVisible_<?= $arPersonType["ID"] ?> = true;
				<?
				$arCorrespondence = CSalePaySystemAction::UnSerializeParams($arPaySystemAction["PARAMS"]);
				if ($bInitVars)
				{
					$arActFields = explode(",", ${"PS_ACTION_FIELDS_LIST_".$arPersonType["ID"]});
					foreach ($arActFields as $val)
					{
						$val = Trim($val);

						$typeTmp  = ${"TYPE_".$val."_".$arPersonType["ID"]};
						$valueTmp = ${"VALUE1_".$val."_".$arPersonType["ID"]};
						if (strlen($typeTmp) <= 0)
							$valueTmp = ${"VALUE2_".$val."_".$arPersonType["ID"]};
						if ($typeTmp == 'FILE')
						{
							$valueTmp = $arCorrespondence[$val]["VALUE"];
							if (intval($valueTmp) > 0)
							{
								$arFile = CFile::GetFileArray($valueTmp);
								$valueTmp = $arFile['SRC'];
							}
						}
						?>
						var param_<?= $val ?>_type_<?= $arPersonType["ID"] ?> = '<?= CUtil::JSEscape($typeTmp) ?>';
						var param_<?= $val ?>_value_<?= $arPersonType["ID"] ?> = '<?= CUtil::JSEscape($valueTmp) ?>';
						<?
					}
				}
				else
				{
					foreach ($arCorrespondence as $key => $value)
					{
						if ($value['TYPE'] == 'FILE')
						{
							$arFile = CFile::GetFileArray($value['VALUE']);
							$value['VALUE'] = $arFile['SRC'];
						}
						?>
						var param_<?= $key ?>_type_<?= $arPersonType["ID"] ?> = '<?= CUtil::JSEscape($value["TYPE"]) ?>';
						var param_<?= $key ?>_value_<?= $arPersonType["ID"] ?> = '<?= CUtil::JSEscape($value["VALUE"]) ?>';
						<?
					}
				}
				?>
				<? $arTarif = CSalePaySystemAction::UnSerializeParams($arPaySystemAction["TARIF"]);?>
				var tarif_<?= $arPersonType["ID"] ?> = <?=CUtil::PhpToJsObject($arTarif)?>;

				//-->
				</script>
				<div id="pay_sys_act_<?= $arPersonType["ID"] ?>" style="display: block; background-color: #E4EDF3;"></div><br>
				<a href="javascript:ShowHideStatus(<?= $arPersonType["ID"] ?>);" id="pay_sys_switch_<?= $arPersonType["ID"] ?>"><?= GetMessage("SPS_HIDE_PROPS") ?></a><br>
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

	$tabControl->BeginNextTab();
	?>
			<tr>
				<td width="40%">
					<?=GetMessage('SPS_DELIVERY_HANDLERS')?>:
				</td>
				<td width="60%">
					<select multiple="multiple" size="5" name="DELIVERY_HANDLERS[]">
						<?
							$arDeliveryId = array();
							$arDeliverySid = array();
							$dbRes = CSaleDelivery2PaySystem::GetList(array("PAYSYSTEM_ID" => $ID));

							while ($arRes = $dbRes->Fetch())
							{
								$deliveryId = $arRes["DELIVERY_ID"];

								if(isset($arRes["DELIVERY_PROFILE_ID"]) && strlen($arRes["DELIVERY_PROFILE_ID"]) > 0)
									$deliveryId .=":".$arRes["DELIVERY_PROFILE_ID"];

								$arDeliveryId[] = $deliveryId;
							}

							$rsDeliveryServicesList = CSaleDeliveryHandler::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array());
							$arDeliveryServicesList = array();

							while ($arDeliveryService = $rsDeliveryServicesList->Fetch())
							{
								if (!is_array($arDeliveryService) || !is_array($arDeliveryService["PROFILES"]))
									continue;

								foreach ($arDeliveryService["PROFILES"] as $profile_id => $arDeliveryProfile)
								{
									$delivery_id = $arDeliveryService["SID"].":".$profile_id;
										?><option
											value="<?= htmlspecialcharsbx($delivery_id)?>"
											<?if(
													is_array($arDeliveryId)
													&&
													(
														in_array($delivery_id, $arDeliveryId)
														||
														in_array($arDeliveryService["SID"], $arDeliveryId)
														||
														empty($arDeliveryId)
													)
												)
												{
													echo " selected";
												}
											?>>
											[<?=htmlspecialcharsbx($delivery_id)?>] <?=htmlspecialcharsbx($arDeliveryService["NAME"].": ".$arDeliveryProfile["TITLE"])?>
										</option><?
								}
							}
							/*Old Delivery*/
							$dbDelivery = CSaleDelivery::GetList(
										array("SORT"=>"ASC", "NAME"=>"ASC"),
										array(
												"ACTIVE" => "Y",
											)
								);

							while ($arDelivery = $dbDelivery->GetNext()):?>
								<option value="<?= $arDelivery["ID"]?>" <?if (is_array($arDeliveryId) && in_array($arDelivery["ID"], $arDeliveryId)) echo " selected"?>>[<?= $arDelivery["ID"]?>] <?= $arDelivery["NAME"]?></option>
							<?endwhile;?>
					</select>
				</td>
			</tr>
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
<script type="text/javascript">
	function psToggleNextSiblings(obj, siblNumber, hide)
	{
		if(!obj.nextElementSibling)
			return false;

		nextObj = obj.nextElementSibling;

		for(var i=0; i<siblNumber; i++)
		{
			if(nextObj.style.display == 'none' && !hide)
				nextObj.style.display = '';
			else
				nextObj.style.display = 'none';

			if(nextObj.nextElementSibling)
				nextObj = nextObj.nextElementSibling;
			else
				break;
		}

		return true;
	}

	function psDeleteObjAndNextSiblings(obj, siblNumber, parentsCount)
	{
		if(!obj)
			return false;

		firstObj = obj;

		if(parentsCount && parentsCount > 0)
		{
			for (var i = 0; i < parentsCount; i++)
			{
				if(firstObj.parentNode)
					firstObj = firstObj.parentNode;
				else
					return false;
			}
		}

		newNextObj = false;
		nextObj = firstObj;

		for(var i=0; i<=siblNumber; i++)
		{
			if(nextObj.nextElementSibling)
				newNextObj = nextObj.nextElementSibling;

			nextObj.parentNode.removeChild(nextObj);

			if(newNextObj)
				nextObj = newNextObj;
			else
				break;
		}

		return true;
	}
</script>
<?
require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");

function wrapDescrLHE($inputName, $content = '', $divId = false)
{
	ob_start();
	$ar = array(
		'inputName' => $inputName,
		'height' => '160',
		'width' => '100%',
		'content' => $content,
		'bResizable' => true,
		'bManualResize' => true,
		'bUseFileDialogs' => false,
		'bFloatingToolbar' => false,
		'bArisingToolbar' => false,
		'bAutoResize' => true,
		'bSaveOnBlur' => true,
		'toolbarConfig' => array(
			'Bold', 'Italic', 'Underline', 'Strike',
			'CreateLink', 'DeleteLink',
			'Source', 'BackColor', 'ForeColor'
		)
	);

	if($divId)
		$ar['id'] = $divId;

	$LHE = new CLightHTMLEditor;
	$LHE->Show($ar);
	$sVal = ob_get_contents();
	ob_end_clean();

	return $sVal;
}

?>
