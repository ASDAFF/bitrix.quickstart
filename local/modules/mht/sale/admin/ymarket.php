<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions < "W")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("sale");
$arYMSettings = array();
$bSaved = false;

if(isset($_POST["YANDEX_MARKET_ON"]))
	CSaleYMHandler::setActivity(true);
elseif(isset($_POST["YANDEX_MARKET_OFF"]))
	CSaleYMHandler::setActivity(false);

if (isset($_REQUEST["https_check"]) && $_REQUEST["https_check"] == "Y" && check_bitrix_sessid())
{
	$ob = new CHTTP();
	$ob->http_timeout = 10;


	if (!@$ob->Get("https://".$_SERVER["SERVER_NAME"].$APPLICATION->GetCurPage()))
	{
		$res = "error";
		$text = GetMessage("SALE_YM_CHECK_HTTPS_ERROR");
	}
	else
	{
		$res = "ok";
		$text = GetMessage("SALE_YM_CHECK_HTTPS_SUCCESS");
	}

	header("Content-Type: application/x-javascript; charset=".LANG_CHARSET);
	echo CUtil::PhpToJSObject(array("status" => $res, "text" => $text));
	die();
}
else if($REQUEST_METHOD=="POST" && check_bitrix_sessid())
{
	if(isset($_POST["YMSETTINGS"]) && is_array($_POST["YMSETTINGS"]) &&!empty($_POST["YMSETTINGS"]))
	{
		$arYMSettings = $_POST["YMSETTINGS"];
		CSaleYMHandler::saveSettings($arYMSettings);
		$bSaved = true;
	}
}


$siteList = array();
$rsSites = CSite::GetList($by = "sort", $order = "asc", Array());

while($arRes = $rsSites->Fetch())
	$siteList[$arRes['ID']] = $arRes['NAME'];

$arTabs = array();

foreach ($siteList as $siteId => $siteName)
{
	$arTabs[] = array(
		"DIV" => "sale_ym_edit_".$siteId,
		"TAB" => $siteName." (".$siteId.")",
		"TITLE" => $siteName." (".$siteId.")",
		"SITE_ID" => $siteId
	);
}

$tabControl = new CAdminTabControl("tabControl", $arTabs);

$APPLICATION->SetTitle(GetMessage("SALE_YM_TITLE"));

$checkStyle = '
	<style type="text/css">
		.https_check_success {
			font-weight: bold;
			color: green;
		}

		.https_check_fail {
			font-weight: bold;
			color: red;
		}
	</style>';

$statuses = array(
	"CANCELED" => GetMessage("SALE_YM_F_CANCELED"),
	"ALLOW_DELIVERY" => GetMessage("SALE_YM_F_DELIVERY"),
	"PAYED" => GetMessage("SALE_YM_F_PAY"),
	"DEDUCTED" => GetMessage("SALE_YM_F_OUT"),
);

$saleStatusIterator = CSaleStatus::GetList(Array("SORT" => "ASC"), Array("LID" => LANGUAGE_ID), false, false, Array("ID", "NAME", "SORT"));

while ($row = $saleStatusIterator->GetNext())
{
	$statuses[$row["ID"]] = "{$row["NAME"]} [{$row['ID']}]";
}

$outYandexStatuses = array(
	"DELIVERY" => GetMessage("SALE_YM_Y_STATUS_DELIVERY")." [DELIVERY]",
	"CANCELLED" => GetMessage("SALE_YM_Y_STATUS_CANCELLED")." [CANCELLED]",
	"PICKUP" => GetMessage("SALE_YM_Y_STATUS_PICKUP")." [PICKUP]",
	"DELIVERED" => GetMessage("SALE_YM_Y_STATUS_DELIVERED")." [DELIVERED]",
);

$APPLICATION->AddHeadString($checkStyle, true, true);

$requiredOrderProperties = array(
	"FIO",
	"EMAIL",
	"PHONE",
	"ZIP",
	"CITY",
	"LOCATION",
	"ADDRESS"
);

require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/prolog_admin_after.php");

if($bSaved)
	CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("SALE_YM_SETTINGS_SAVED"), "TYPE"=>"OK"));

?>
<form method="post" action="<?=$APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>" name="ymform">
<?

if(CSaleYMHandler::isActive(false))
{

	$tabControl->Begin();

	foreach($arTabs as $arTab)
	{
		$tabControl->BeginNextTab();
		$siteSetts = CSaleYMHandler::getSettingsBySiteId($arTab["SITE_ID"]);

		$arDeliveryFilter = array(
			"LID" => $arTab["SITE_ID"],
			"ACTIVE" => "Y"
		);

		$dbDeliveryList = CSaleDelivery::GetList(
			array("NAME" => "ASC"),
			$arDeliveryFilter,
			false,
			false,
			array("ID", "NAME")
		);

		$arDeliveryList=array();
		while ($arDelivery = $dbDeliveryList->Fetch())
			$arDeliveryList[$arDelivery["ID"]] = $arDelivery["NAME"];

		$dbResultList = CSalePersonType::GetList(
			"NAME",
			"ASC",
			array(
				"LID" => $arTab["SITE_ID"],
				"ACTIVE" => "Y"
			)
		);

		$arPersonTypes = array();
		while ($arPT = $dbResultList->Fetch())
			$arPersonTypes[$arPT['ID']] = $arPT['NAME'];

		if(isset($siteSetts["PERSON_TYPE"]) && array_key_exists($siteSetts["PERSON_TYPE"], $arPersonTypes))
		{
			$personTypeId= $siteSetts["PERSON_TYPE"];
		}
		else
		{
			reset($arPersonTypes);
			$personTypeId = key($arPersonTypes);
		}

		$arPropFilter = array(
			"PERSON_TYPE_ID" => $personTypeId,
			"ACTIVE" => "Y"
		);

		$dbOrderProps = CSaleOrderProps::GetList(
			array("ID" => "ASC"),
			$arPropFilter,
			false,
			false,
			array("ID", "CODE", "NAME", "TYPE", "REQUIED", "IS_LOCATION", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "SORT", "IS_PHONE")
		);

		$orderPropsList = array();
		while ($arOrderProps = $dbOrderProps->Fetch())
			$orderPropsList[$arOrderProps["CODE"]] = $arOrderProps["NAME"];

		?>
			<tr>
				<td width="40%" class="adm-detail-valign-top"><span class="adm-required-field"><?=GetMessage("SALE_YM_CAMPAIGN_ID")?>:</span></td>
				<td width="60%" >
					<input type="text" name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][CAMPAIGN_ID]" size="45" maxlength="255" value="<?=isset($siteSetts["CAMPAIGN_ID"]) ? htmlspecialcharsbx($siteSetts["CAMPAIGN_ID"]) : ""?>">
					<?=BeginNote();?>
						<?=GetMessage("SALE_YM_CAMPAIGN_ID_HELP")?>
					<?=EndNote();?>

				</td>
			</tr>
			<tr>
				<td width="40%"><span class="adm-required-field"><?=GetMessage("SALE_YM_YANDEX_URL")?>:</span></td>
				<td width="60%"><input type="text" name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][YANDEX_URL]" size="45" maxlength="255" value="<?=isset($siteSetts["YANDEX_URL"]) ? htmlspecialcharsbx($siteSetts["YANDEX_URL"]) : "https://api.partner.market.yandex.ru/v2/"?>"></td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-valign-top"><span class="adm-required-field"><?=GetMessage("SALE_YM_YANDEX_TOKEN")?>:</span></td>
				<td width="60%">
					<input type="text" name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][YANDEX_TOKEN]" size="45" maxlength="255" value="<?=isset($siteSetts["YANDEX_TOKEN"]) ? htmlspecialcharsbx($siteSetts["YANDEX_TOKEN"]) : ""?>">
					<br><small><?=GetMessage("SALE_YM_YANDEX_TOKEN_HELP")?></small>
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-valign-top"><span class="adm-required-field"><?=GetMessage("SALE_YM_OAUTH_TOKEN")?>:</span></td>
				<td width="60%">
					<input type="text" name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][OAUTH_TOKEN]" size="45" maxlength="255" value="<?=isset($siteSetts["OAUTH_TOKEN"]) ? htmlspecialcharsbx($siteSetts["OAUTH_TOKEN"]) : ""?>">
					<br><small><?=GetMessage("SALE_YM_OAUTH_TOKEN_HELP")?></small>
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-valign-top"><span class="adm-required-field"><?=GetMessage("SALE_YM_OAUTH_CLIENT_ID")?>:</span></td>
				<td width="60%">
					<input type="text" name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][OAUTH_CLIENT_ID]" size="45" maxlength="255" value="<?=isset($siteSetts["OAUTH_CLIENT_ID"]) ? htmlspecialcharsbx($siteSetts["OAUTH_CLIENT_ID"]) : ""?>">
					<br><small><?=GetMessage("SALE_YM_OAUTH_CLIENT_ID_HELP")?></small>
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-valign-top"><span class="adm-required-field"><?=GetMessage("SALE_YM_OAUTH_LOGIN")?>:</span></td>
				<td width="60%">
					<input type="text" name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][OAUTH_LOGIN]" size="45" maxlength="255" value="<?=isset($siteSetts["OAUTH_LOGIN"]) ? htmlspecialcharsbx($siteSetts["OAUTH_LOGIN"]) : ""?>">
					<br><small><?=GetMessage("SALE_YM_OAUTH_LOGIN_HELP")?></small>
				</td>
			</tr>
			<tr>
				<td width="40%"><?=GetMessage("SALE_YM_PAYER_TYPE")?>:</td>
				<td width="60%">
					<select name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][PERSON_TYPE]" onchange="this.form.submit();">
						<?foreach ($arPersonTypes as $ptId => $ptName):?>
							<option value="<?=$ptId?>"<?=$personTypeId == $ptId ? " selected" : ""?>><?=htmlspecialcharsbx($ptName)?></option>
						<?endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-valign-top"><?=GetMessage("SALE_YM_AUTH_TYPE")?>:</td>
				<td width="60%">
					<select name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][AUTH_TYPE]">
						<option value="HEADER"<?=isset($siteSetts["AUTH_TYPE"]) && $siteSetts["AUTH_TYPE"] == "HEADER" ? " selected" : ""?>>HEADER</option>
						<option value="URL"<?=isset($siteSetts["AUTH_TYPE"]) && $siteSetts["AUTH_TYPE"] == "URL" ? " selected" : ""?>>URL</option>
					</select>
					<br><small><?=GetMessage("SALE_YM_AUTH_TYPE_HELP")?></small>
				</td>
			</tr>
			<tr>
				<td width="40%"><?=GetMessage("SALE_YM_DATA_FORMAT")?>:</td>
				<td width="60%">
					<select name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][DATA_FORMAT]" disabled>
						<option value="<?=CSaleYMHandler::JSON?>" selected>JSON</option>
						<option value="<?=CSaleYMHandler::XML?>">XML</option>
					</select>
					<br><small><?=GetMessage("SALE_YM_DATA_FORMAT_HELP")?></small>
				</td>
			</tr>
			<tr>
				<td width="40%"><?=GetMessage("SALE_YM_LOG_LEVEL")?>:</td>
				<td width="60%">
					<select name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][LOG_LEVEL]">
						<? $logLevel = isset($siteSetts["LOG_LEVEL"]) && $siteSetts["LOG_LEVEL"] ? $siteSetts["LOG_LEVEL"] : CSaleYMHandler::LOG_LEVEL_ERROR; ?>
						<option value="<?=CSaleYMHandler::LOG_LEVEL_ERROR?>"<?=$logLevel == CSaleYMHandler::LOG_LEVEL_ERROR ? " selected" : ""?>><?=GetMessage("SALE_YM_LOG_LEVEL_ERROR")?></option>
						<option value="<?=CSaleYMHandler::LOG_LEVEL_INFO?>"<?=$logLevel == CSaleYMHandler::LOG_LEVEL_INFO ? " selected" : ""?>><?=GetMessage("SALE_YM_LOG_LEVEL_INFO")?></option>
						<option value="<?=CSaleYMHandler::LOG_LEVEL_DEBUG?>"<?=$logLevel == CSaleYMHandler::LOG_LEVEL_DEBUG ? " selected" : ""?>><?=GetMessage("SALE_YM_LOG_LEVEL_DEBUG")?></option>
						<option value="<?=CSaleYMHandler::LOG_LEVEL_DISABLE?>"<?=$logLevel == CSaleYMHandler::LOG_LEVEL_DISABLE ? " selected" : ""?>><?=GetMessage("SALE_YM_LOG_LEVEL_DISABLE")?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="40%" class="adm-detail-valign-top"><?=GetMessage("SALE_YM_CHECK_HTTPS")?>:</td>
				<td width="60%">
					<input
						id="https_check_button"
						type="button"
						value="<?=GetMessage("SALE_YM_CHECK_HTTPS_BUT")?>"
						title="<?=GetMessage("SALE_YM_CHECK_HTTPS_TITLE")?>"
						onclick="
							var checkHTTPS = function(){
								BX.showWait();
								BX.ajax.post('<?=$APPLICATION->GetCurPage()?>', '<?=CUtil::JSEscape(bitrix_sessid_get())."&https_check=Y"?>', function (result){
									BX.closeWait();
									var res = eval( '('+result+')' );
									BX('https_check_result_<?=CUtil::JSEscape($arTab["SITE_ID"])?>').innerHTML = '&nbsp;' + res['text'];

									BX.removeClass(BX('https_check_result_<?=CUtil::JSEscape($arTab["SITE_ID"])?>'), 'https_check_success');
									BX.removeClass(BX('https_check_result_<?=CUtil::JSEscape($arTab["SITE_ID"])?>'), 'https_check_fail');

									if (res['status'] == 'ok')
										BX.addClass(BX('https_check_result_<?=CUtil::JSEscape($arTab["SITE_ID"])?>'), 'https_check_success');
									else
										BX.addClass(BX('https_check_result_<?=CUtil::JSEscape($arTab["SITE_ID"])?>'), 'https_check_fail');
								});
							};
							checkHTTPS();"
						/>
					<span id="https_check_result_<?=CUtil::JSEscape($arTab["SITE_ID"])?>"></span>
					<br><small><?=GetMessage("SALE_YM_CHECK_HTTPS_HELP")?></small>
				</td>
			</tr>


			<tr>
				<td width="40%" class="adm-detail-valign-top"><?echo GetMessage("SALE_YM_OUTLETS")?>:</td>
				<td width="60%" id="OUTLETS_IDS_<?=htmlspecialcharsbx($arTab["SITE_ID"])?>"><?
					if(isset($siteSetts["OUTLETS_IDS"]) && is_array($siteSetts["OUTLETS_IDS"]))
					{
						foreach ($siteSetts["OUTLETS_IDS"] as $outletId)
						{
							?><input type="text" name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][OUTLETS_IDS][]" size="10" value="<?=htmlspecialcharsbx($outletId)?>"><br><?
						}
					}
				?>
				<input type="text" name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][OUTLETS_IDS][]" size="10" value=""><br>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="button" value="<?=GetMessage("SALE_YM_OUTLETS_ADD_BUT")?>" onclick="addOutletIdField('YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][OUTLETS_IDS][]','<?=htmlspecialcharsbx($arTab["SITE_ID"])?>');">
					<br><small><?=GetMessage("SALE_YM_OUTLETS_HELP")?></small>
				</td>
			</tr>

			<tr class="heading"><td colspan="2"><?=GetMessage("SALE_YM_PAYSYSTEMS")?></td></tr>
			<tr>
				<td width="40%"><?=GetMessage("SALE_YM_YANDEX")?>:</td>
				<td width="60%"><?=makeSelectorFromPaySystems("YMSETTINGS[".htmlspecialcharsbx($arTab["SITE_ID"])."][PAY_SYSTEMS][YANDEX]", $siteSetts["PAY_SYSTEMS"]["YANDEX"], $personTypeId)?></td>
			</tr>
			<tr>
				<td width="40%"><?=GetMessage("SALE_YM_SHOP_PREPAID")?>:</td>
				<td width="60%"><?=makeSelectorFromPaySystems("YMSETTINGS[".htmlspecialcharsbx($arTab["SITE_ID"])."][PAY_SYSTEMS][SHOP_PREPAID]", $siteSetts["PAY_SYSTEMS"]["SHOP_PREPAID"], $personTypeId)?></td>
			</tr>
			<tr>
				<td width="40%"><?=GetMessage("SALE_YM_CASH_ON_DELIVERY")?>:</td>
				<td width="60%"><?=makeSelectorFromPaySystems("YMSETTINGS[".htmlspecialcharsbx($arTab["SITE_ID"])."][PAY_SYSTEMS][CASH_ON_DELIVERY]", $siteSetts["PAY_SYSTEMS"]["CASH_ON_DELIVERY"], $personTypeId)?></td>
			</tr>
			<tr>
				<td width="40%"><?=GetMessage("SALE_YM_CARD_ON_DELIVERY")?>:</td>
				<td width="60%"><?=makeSelectorFromPaySystems("YMSETTINGS[".htmlspecialcharsbx($arTab["SITE_ID"])."][PAY_SYSTEMS][CARD_ON_DELIVERY]", $siteSetts["PAY_SYSTEMS"]["CARD_ON_DELIVERY"], $personTypeId)?></td>
			</tr>
			<tr class="heading"><td colspan="2"><?=GetMessage("SALE_YM_DELIVERY")?></td></tr>
			<?foreach ($arDeliveryList as $deliveryId => $deliveryName):
				$selected = isset($siteSetts["DELIVERIES"][$deliveryId]) ? $siteSetts["DELIVERIES"][$deliveryId] : '';
			?>
				<tr>
					<td width="40%"><?=htmlspecialcharsbx($deliveryName)?>:</td>
					<td width="60%">
						<select name="YMSETTINGS[<?=htmlspecialcharsbx($arTab["SITE_ID"])?>][DELIVERIES][<?=$deliveryId?>]">
							<option value=""><?=GetMessage("SALE_YM_NOT_USE")?></option>
							<option value="DELIVERY"<?=$selected == "DELIVERY" ? "selected" : ""?>><?=GetMessage("SALE_YM_DELIVERY_DELIVERY")?></option>
							<option value="PICKUP"<?=$selected == "PICKUP" ? "selected" : ""?>><?=GetMessage("SALE_YM_DELIVERY_PICKUP")?></option>
							<!--<option value="POST"><?=GetMessage("SALE_YM_DELIVERY_POST")?></option>-->
						</select>
					</td>
				</tr>
			<?endforeach;?>
			<?
				if(!isset($siteSetts["STATUS_IN"]))
				{
					$siteSetts["STATUS_IN"] = array(
						"UNPAID" => "N",
						"PROCESSING" => "N",
						"CANCELLED" => "CANCELED"
					);
				}
			?>
			<tr class="heading"><td colspan="2"><?=GetMessage("SALE_YM_STATUS_IN")?></td></tr>
			<tr><td><?=GetMessage("SALE_YM_Y_STATUS_UNPAID")." [UNPAID]"?></td><td><?=getSelectHtml("YMSETTINGS[".htmlspecialcharsbx($arTab["SITE_ID"])."][STATUS_IN][UNPAID]", $statuses, $siteSetts["STATUS_IN"]["UNPAID"])?></td></tr>
			<tr><td><?=GetMessage("SALE_YM_Y_STATUS_PROCESSING")." [PROCESSING]"?></td><td><?=getSelectHtml("YMSETTINGS[".htmlspecialcharsbx($arTab["SITE_ID"])."][STATUS_IN][PROCESSING]", $statuses, $siteSetts["STATUS_IN"]["PROCESSING"])?></td></tr>
			<tr><td><?=GetMessage("SALE_YM_Y_STATUS_CANCELLED")." [CANCELLED]"?></td><td><?=getSelectHtml("YMSETTINGS[".htmlspecialcharsbx($arTab["SITE_ID"])."][STATUS_IN][CANCELLED]", $statuses, $siteSetts["STATUS_IN"]["CANCELLED"])?></td></tr>

			<?
			if(!isset($siteSetts["STATUS_OUT"]))
			{
				$siteSetts["STATUS_OUT"] = array(
					"CANCELED" => "CANCELLED",
					"ALLOW_DELIVERY" => "DELIVERY",
					"F" => "DELIVERED"
				);
			}
			?>
			<tr class="heading"><td colspan="2"><?=GetMessage("SALE_YM_STATUS_OUT")?></td></tr>
			<?foreach($statuses as $statusId => $statusName):?>
				<tr><td><?=$statusName?></td><td><?=getSelectHtml("YMSETTINGS[".htmlspecialcharsbx($arTab["SITE_ID"])."][STATUS_OUT][".$statusId."]", $outYandexStatuses, $siteSetts["STATUS_OUT"][$statusId])?></td></tr>
			<?endforeach;?>

			<tr class="heading"><td colspan="2"><?=GetMessage("SALE_YM_ORDER_PROPS_MAP")?></td></tr>
			<?foreach($requiredOrderProperties as $orderPropertyId):?>
				<tr>
					<td width="40%"><?=GetMessage("SALE_YM_ORDER_PROPS_".$orderPropertyId)?>:</td>
					<td width="60%">
						<?=getSelectHtml(
							"YMSETTINGS[".htmlspecialcharsbx($arTab["SITE_ID"])."][ORDER_PROPS][".$orderPropertyId."]",
							$orderPropsList, isset($siteSetts["ORDER_PROPS"][$orderPropertyId]) ? $siteSetts["ORDER_PROPS"][$orderPropertyId] : $orderPropertyId,
							false
							)
						?>
					</td>
				</tr>
			<?endforeach;?>
	<?

	}
	$tabControl->Buttons(array(
		"btnSave" => true,
		"btnApply" => false
	));
	echo '<input type="submit" name="YANDEX_MARKET_OFF" value="'.GetMessage("SALE_YM_OFF").'" title="'.GetMessage("SALE_YM_OFF_TITLE").'" onclick="return confirm(\''.GetMessage("SALE_YM_OFF_CONFIRM").'\')"/>';
	?>
	<?=bitrix_sessid_post();?>
	<?$tabControl->End();?>
	<script>
		function addOutletIdField(name, siteId)
		{
			BX('OUTLETS_IDS_'+siteId).appendChild(
				BX.create('input', {
					props: {
						name: name
					},
					attrs: {
						type: 'text',
						size: '10'
					}
				})
			);
			BX('OUTLETS_IDS_'+siteId).appendChild(
				BX.create('br')
			);
		}
	</script>
	<?
}
else //If integration with yandex market is not active
{
	echo BeginNote();
	echo GetMessage("SALE_YM_OFF_TEXT");
	echo EndNote();
	echo '<input type="submit" name="YANDEX_MARKET_ON" value="'.GetMessage("SALE_YM_ON").'" title="'.GetMessage("SALE_YM_ON_TITLE").'" onclick="return confirm(\''.GetMessage("SALE_YM_ON_CONFIRM").'\')"/>';
}
	?>
	</form>
	<?

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");

function makeSelectorFromPaySystems($psTypeYandex, $psIdValue, $personTypeId)
{
	static $arPaySystems = array();

	if(!isset($arPaySystems[$personTypeId]))
	{
		$arPaySystems[$personTypeId] = array();
		$dbResultList = CSalePaySystem::GetList(
			array("NAME" => "ASC"),
			array(
				"ACTIVE" => "Y",
				"PSA_PERSON_TYPE_ID" => $personTypeId,
			),
			false,
			false,
			array("ID", "NAME")
		);

		while($arPS = $dbResultList->Fetch())
			$arPaySystems[$personTypeId][$arPS['ID']] = $arPS['NAME'];
	}

	$result = '<select name="'.$psTypeYandex.'">'.
		'<option value="">'.GetMessage("SALE_YM_NOT_USE").'</option>';

	foreach ($arPaySystems[$personTypeId] as $psId => $psName)
	{
		$result.= '<option value="'.
			$psId.'"'.
			($psIdValue == $psId ? ' selected ': '').'>'.
			htmlspecialcharsbx($psName).
			'</option>';
	}

	$result .= '</select>';

	return $result;
}

function getSelectHtml($name, array $data, $selected = "", $bShowNotUse = true)
{
	if(!is_array($data) || empty($data))
		return "";

	$result = '<select name="'.htmlspecialcharsbx($name).'">';

	if($bShowNotUse)
		$result .= '<option value="">'.GetMessage("SALE_YM_NOT_USE").'</option>';

	foreach($data as $value => $title)
		$result .= '<option value="'.$value.'"'.($selected == $value ? " selected" : "").'>'.$title.'</option>';

	$result .= '</select>';

	return $result;
}
?>