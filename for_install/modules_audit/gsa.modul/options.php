<?php
global $MESS;
include($_SERVER["DOCUMENT_ROOT"].BX_ROOT.'/modules/gsa.modul/lang/ru/all.php');

if(!$USER->IsAdmin())
	return;

$module_id = 'gsa.modul';
$errors ="";
CModule::IncludeModule($module_id);
CModule::IncludeModule("catalog");
$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);


$arAllOptions = Array(
	array("user_name", GetMessage("GSA_EMAIL"), array("text")),
	array("user_pass", GetMessage("GSA_PASS"), array("password")),
);



$aTabs = array(
	array(
		"DIV" => "edit1", "TAB" => GetMessage("GSA_SETTING_ACC"), "ICON" => "pull_path", "TITLE" => GetMessage("GSA_GSET_MOD"),
	),
	array(
		"DIV" => "edit2", "TAB" => GetMessage("GSA_SETTING_EXP"), "ICON" => "pull_path", "TITLE" => GetMessage("GSA_GSET_MOD"),
	),
	array(
		"DIV" => "edit3", "TAB" => GetMessage("GSA_SETTING_SYN"), "ICON" => "pull_path", "TITLE" => GetMessage("GSA_GSET_SYN"),
	),
	array(
		"DIV" => "edit4", "TAB" => GetMessage("GSA_SETTING_STA"), "ICON" => "pull_path", "TITLE" => GetMessage("GSA_GSET_STA"),
	),
	array(
		"DIV" => "edit5", "TAB" => GetMessage("GSA_SETTING_SHO"), "ICON" => "pull_path", "TITLE" => GetMessage("GSA_GSET_SHO"),
	)
);


$tabControl = new CAdminTabControl("tabControl", $aTabs);
if(strlen($_POST['Update'])>0 && check_bitrix_sessid())
{
	if(strlen($_POST['Update'])>0) {

		/*foreach($arAllOptions as $arOption)
		{			
			$name=$arOption[0];
			$val=$_REQUEST[$name];
			// echo $name."---".$val."<br/>";
			COption::SetOptionString("gsa.modul", $name, $val, $arOption[1]);
		}*/
		COption::SetOptionString("gsa.modul","cookie",'');

		//GSA IBLOCK NAMES
		COption::SetOptionString("gsa.modul", "IBLOCK_GSA_NAMES", serialize($_POST["IBLOCKGSANAME"]));

		//infoblocks settings update
		$iblocksstring = serialize($_POST['IBLOCKS']);
		COption::SetOptionString("gsa.modul", "IBLOCKS", $iblocksstring);

		$iblockphotosstring = serialize($_POST['IBLOCK_PHOTOS']);
		COption::SetOptionString("gsa.modul", "IBLOCK_PHOTOS", $iblockphotosstring);

		$iofferphotosstring = serialize($_POST['OFFER_PHOTOS']);
		COption::SetOptionString("gsa.modul", "OFFER_PHOTOS", $iofferphotosstring);

		COption::SetOptionString("gsa.modul", "PRICE_TYPE", $_POST['PRICE_TYPE']);

		$profileId = COption::GetOptionString("gsa.modul", "PROFILE_ID"); //текущий профиль из настроек

		//DELIVERY
		if (count($_POST["delivery"])>0) {
			COption::SetOptionString("gsa.modul", "DELIVERY", serialize($_POST["delivery"]));
		}

		//STATUS
		if(count($_POST['status'])>0 || count($_POST['status_order'])>0)
		{
			$arr['status'] = $_POST["status"];
			$arr['status_order'] = $_POST['status_order'];			
			COption::SetOptionString("gsa.modul", "STATUS", serialize($arr));			
		}

		//SI - ShopInfo
		if(count($_POST['SI'])>0)
		{
			$_POST['SI']['Description'] = strip_tags($_POST['SI']['Description']);
			//тупо сейвим
			COption::SetOptionString("gsa.modul", "SI", serialize($_POST["SI"]));
			//обновляем на той стороне			
			if(strlen($_POST['SI']['Name'])>0 && strlen($_POST['SI']['Description'])>0)
				cGSA::ShopInfoUpdate($_POST["SI"]);
		}
		//периодичность выгрузки в секундах
		$_POST['period'] = intval($_POST['period'])*60;				//конертируем в секунды
		COption::SetOptionString("gsa.modul", "period", intval($_POST["period"]));
		$period = COption::GetOptionString("gsa.modul", "period");
		$period = intval($period/60);
		if(intval($period)<=60) $period=60 ;			//не чаще чем пол часа
		cGsa::deliverySync();



		if (count($_POST["IBLOCKS"]) && count($_POST["PRICE_TYPE"]))
		{
			//creating VARS var
			$setupVars = "IBLOCK_ID=&SETUP_FILE_NAME=%2Fupload%2Fgsa_export.txt";
			foreach ($_POST['IBLOCKS'] as $key => $value)
				$setupVars .= "&IBLOCKS[".$key."]=".intval($value);
			$setupVars.="&PRICE_TYPE=".intval($_POST["PRICE_TYPE"]);

			if (count($_POST['IBLOCK_PHOTOS']))
				foreach ($_POST['IBLOCK_PHOTOS'] as $key => $value)
					$setupVars .= "&IBLOCK_PHOTOS[".$key."]=".$value;

			if (count($_POST['OFFER_PHOTO']))
				foreach ($_POST['OFFER_PHOTOS'] as $key => $value)
					$setupVars .= "&OFFER_PHOTOS[".$key."]=".$value;




			//creating-updating profile
			if ($profileId)			//обновляем профиль
			{
				$arFields = array(
					"FILE_NAME" => "gsa",
					"NAME" => "GSAExportProfile",
					"IN_MENU" => "N",
					"DEFAULT_PROFILE" => "N",
					"IN_AGENT" => "Y",
					"IN_CRON" => "N",
					"NEED_EDIT" => "N",
					"SETUP_VARS" => $setupVars,
					);
				CCatalogExport::Update($profileId,$arFields);
			}
			else		//создаем профиль
			{
				$arFields = array(
					"FILE_NAME" => "gsa",
					"NAME" => "GSAExportProfile",
					"IN_MENU" => "N",
					"DEFAULT_PROFILE" => "N",
					"IN_AGENT" => "Y",
					"IN_CRON" => "N",
					"NEED_EDIT" => "N",
					"SETUP_VARS" => $setupVars,
					);

				$code = CCatalogExport::Add($arFields);
				COption::SetOptionString("gsa.modul", "PROFILE_ID", $code);

				$acode = CAgent::AddAgent(
				    "CCatalogExport::PreGenerateExport(".intval($code).");",  // имя функции
				    "catalog",                // идентификатор модуля
				    "N",                      // агент не критичен к кол-ву запусков
				    $period,                   // интервал запуска - 1 сутки
				    "",                       // дата первой проверки - текущее
				    "Y",                      // агент активен
				    "",                       // дата первого запуска - текущее
				    30);
				COption::SetOptionString("gsa.modul", "AGENT_ID", $acode);
			}

		}
		else
		{ //если нельзя создать профиль - не выбран тип цены или инфоблог для выгрузки
			if ($profileId)
			{
				CCatalogExport::Delete($profileId);
				COption::SetOptionString("gsa.modul", "PROFILE_ID", "", "Профиль выгрузки");
				unset($profileId);

				$agentId = COption::GetOptionString("gsa.modul", "AGENT_ID");
				CAgent::Delete($agentId);
				COption::SetOptionString("gsa.modul", "AGENT_ID", "");
				unset($agentId);
			}
			$errors .= "<p style='color:red;'>".GetMessage("GSA_NO_INFO_SELECTED")."</p>";
		}

		//чистим лишнее
		if ($profileId) {
			$tp = CCatalogExport::GetList(array(),array("FILE_NAME"=>"gsa", "NAME" => "GSAExportProfile", "!ID"=>$profileId));
			while ($res = $tp->Fetch())
				CCatalogExport::Delete($res['ID']);
		}


	}

}

if(cGsa::getAuth()) 
{
	cGsa::DeleteCatalog();
	$tmp1 = cGSA::ReadAllCat();
	if(!isset($tmp1->Catalogues) || count($tmp1->Catalogues)==0) cGsa::setCatalog();
}
?>



<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($mid)?>&lang=<?echo LANG?>">
<?php echo bitrix_sessid_post()?>
<?php
	$tabControl->Begin();
	$tabControl->BeginNextTab();
?>
	<tr>
		<td colspan="2">
			<p><?=GetMessage("GSA_TYPE_ACC_INFO")?> <a target="_blank" href="http://www.getshopapp.com">www.getshopapp.com</a></p>
		</td>
	</tr>

<?
/*print_r($arAllOptions);*/
foreach($arAllOptions as $arOption):
	$val = COption::GetOptionString("gsa.modul", $arOption[0]);
	$type = $arOption[2];
?>



<tr>
	<td width="40%" nowrap >
		<label for="<?echo htmlspecialcharsbx($arOption[0])?>"><?echo $arOption[1]?>:</label>
	<td width="60%">
		<?if($type[0]=="password"):?>
			<input type="password" maxlength="255" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($arOption[0])?>" id="<?echo htmlspecialcharsbx($arOption[0])?>">
		<?elseif($type[0]=="text"):?>
			<input type="text" maxlength="255" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($arOption[0])?>" id="<?echo htmlspecialcharsbx($arOption[0])?>">
		<?endif?>		
	</td>
</tr>
<?endforeach?>

<tr>
	<td width="40%" nowrap >
		<label for="Писать лог">Писать лог:</label>
	<td width="60%">
		<?
			$write_log_checked='';
			$write_log_status = COption::getOptionString("gsa.modul", "write_log");	
			if(intval($write_log_status)==1) $write_log_checked = 'checked="checked"';			
		?>
		<input type="checkbox" id="write_log" onclick="saveLog();" <?=$write_log_checked?>>
		(<a href="/gsa_main_log.txt" target="_blank">посмотреть лог-файл</a>)
		<script>
		function saveLog()
		{ 
			var is_log=0;
			var remember = document.getElementById('write_log');
			 if (remember.checked){
			    is_log = 1;
			  }/*else{
			    alert("You didn't check it! Let me check it for you.")
			  }*/

			  BX.ajax.post(
			    '/bitrix/tools/ajax_gsa.php?action=WriteLog',
			    {is_log: is_log},
			    DEMOResponse
			   );
		}
		</script>
	</td>
</tr>
<tr>
	<td colspan="2">
	<center>	  
		<input type="button" id="enterAuth" value="Войти" class="adm-btn-save" <?if(cGsa::getAuth()) echo "disabled";?> onclick="enterAuthF();return false;">
		<input type="button" id="exitAuth"  value="Выйти" class="adm-btn-save"  <?if(!cGsa::getAuth()) echo "disabled";?> onclick="exitAuthF();return false;">
	</center>
	<script>
	<?if(!cGsa::getAuth()):?>
	window.onload = function ()
	{		
		document.getElementById('tab_cont_edit2').style.display='none';
		document.getElementById('tab_cont_edit3').style.display='none';
		document.getElementById('tab_cont_edit4').style.display='none';
		document.getElementById('tab_cont_edit5').style.display='none';
		document.getElementById('buttonsGSA').style.display='none';	
	}
	<?endif;?>
	function enterAuthF()
	{
		var user_name = document.getElementById('user_name').value;
		var user_pass = document.getElementById('user_pass').value;
		if(user_name.length==0 || user_pass==0) 
		{
			alert("Поля Эл.почта и Пароль не должны быть пустыми!");
			return false;
		}
		BX.ajax.post(
			    '/bitrix/tools/ajax_gsa.php?action=enterAuth',
			    {user_name: user_name,
			     user_pass:user_pass},
			    function(data)
			    {

			    	if(data=='0')
			    	{
			    		alert('Неверно задана Эл. почта или Пароль. Проверьте, пожалуйста, настройки');
			    		return false;
			    	}
			    	alert('Вход в магазин произведен успешно');
			    	document.getElementById('enterAuth').disabled = true;
			    	document.getElementById('exitAuth').disabled = false;
			    	window.location.reload();
			    }
			   );
	}
	
	function exitAuthF()
	{
		document.getElementById('enterAuth').disabled = false;
		document.getElementById('exitAuth').disabled = true;
		//document.getElementById('user_name').value = "";
		//document.getElementById('user_pass').value = "";
		BX.ajax.post('/bitrix/tools/ajax_gsa.php?action=exitAuth',{test:"1"});
		alert("Вы успешно вышли из магазина");
		window.location.reload();
	}
	</script>
	</td>
</tr>





<?
	$arBlocks = COption::GetOptionString("gsa.modul", "IBLOCKS");
	$ariPhotos = COption::GetOptionString("gsa.modul", "IBLOCK_PHOTOS");
	$aroPhotos = COption::GetOptionString("gsa.modul", "OFFER_PHOTOS");
	$pricetype = COption::GetOptionString("gsa.modul", "PRICE_TYPE");
	$arDelivery = COption::GetOptionString("gsa.modul", "DELIVERY");
	$arStatus = COption::GetOptionString("gsa.modul", "STATUS");	
	$arSI = COption::GetOptionString("gsa.modul", "SI");
	$period = COption::GetOptionString("gsa.modul", "period");
	if(!$period || intval($period)==0) $period = 360;
	$period = $period/60;

if ($arDelivery) 	$arDelivery = unserialize($arDelivery);
if($arStatus)		$arStatus = unserialize($arStatus);
if ($ariPhotos) 	$ariPhotos = unserialize($ariPhotos);
if ($aroPhotos) 	$aroPhotos = unserialize($aroPhotos);
if ($arBlocks) 		$arBlocks = unserialize($arBlocks);
if ($arSI) 			$arSI = unserialize($arSI);

//print_r($arStatus);

if (count($arBlocks) <= 0) {
	$errors .= "<p style='color:red;'>".GetMessage("GSA_DONT_SELECT_IBLOCK")."<p>";
}

if (!$pricetype) {
	$errors .= "<p style='color:red;'>".GetMessage("GSA_DONT_SELECT_PRICETYPE")."<p>";
}

?>

<?$tabControl->BeginNextTab();?>


<?if (CModule::IncludeModule('iblock') && CModule::IncludeModule("catalog")):?>

	<?
		$profileId = COption::GetOptionString("gsa.modul", "PROFILE_ID");
		if (!$profileId) {
			$errors .= "<p style='color:red;'>".GetMessage("GSA_NOPROFILESELECTED")."</p>";
		}
		if ($profileId && $arProfile = CCatalogExport::GetByID($profileId)):
	?>
		<tr>
			<td>
				<?=GetMessage("GSA_USINGPROFILE")?> <?=$arProfile["NAME"]?>
			</td>
		</tr>
	<?endif?>
	<tr>
		<td><?=GetMessage("GSA_SELECTIBLOCK")?></td>
	</tr>
	<?
		$iblockGsaNames = COption::GetOptionString("gsa.modul", "IBLOCK_GSA_NAMES");
		$iblockGsaNames = ($iblockGsaNames) ? unserialize($iblockGsaNames) : "";
		$db_res = CIBlock::GetList(Array("iblock_type"=>"asc", "name"=>"asc"));
	?>
	<tr>
		<th style="text-align:left;"><?=GetMessage("GSA_TH_IBLOCK")?></th>
		<th style="text-align:left;"><?=GetMessage("GSA_TH_GROUP")?></th>
		<th style="text-align:left;"><?=GetMessage("GSA_TH_OFFER")?></th>
		<th style="text-align:left;"><?=GetMessage("GSA_TH_DOPIMG")?></th>
		<th style="text-align:left;"><?=GetMessage("GSA_TH_DOPIMGOFFER")?></th>
	</tr>


<?
while ($res = $db_res->Fetch())
{
	$test = CCatalogSKU::GetInfoByOfferIBlock($res["ID"]);

	if (!is_array($test) || (is_array($test) && $test["SKU_PROPERTY_ID"] == "0"))  {
	
		$ioffersid = false;
		$ioffersname = false;
		$iblockid = $res["ID"];
		$iblockname = $res["NAME"];
		$gsaName =  ($iblockGsaNames[$iblockid]) ? $iblockGsaNames[$iblockid] : "";

		$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$iblockid, "PROPERTY_TYPE" => "F"));
		$iblockPropString = "<select name='IBLOCK_PHOTOS[]'>";
		$many = 0;
		while ($prop_fields = $properties->GetNext())
		{
		  	(is_array($ariPhotos) && in_array($iblockid."-".$prop_fields["ID"], $ariPhotos)) ? $sel = "selected" : $sel ="";
		  	$iblockPropString .= "<option ".$sel." value='".$iblockid."-".$prop_fields["ID"]."'>".$prop_fields["NAME"]."</option>";
		  	$many++;
		}
		if ($many) {
			$iblockPropString .= "</select>";
		} else {
			$iblockPropString = GetMessage("GSA_NOTYPEFILE");
		}

	$arIblockInfo = CCatalog::GetByID($iblockid);
	if (!empty($arIblockInfo))
	{
		$arOffers = CCatalogSKU::GetInfoByProductIBlock($iblockid);
		if ($arOffers["IBLOCK_ID"]) {
			$arOffersInfo = CCatalog::GetByID($arOffers["IBLOCK_ID"]);
			if ($arOffersInfo) {
				$ioffersid = $arOffersInfo["ID"];
				$ioffersname = $arOffersInfo["NAME"];

				$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$ioffersid, "PROPERTY_TYPE" => "F"));
				$offersPropString = "<select name='OFFER_PHOTOS[]'>";
				$many = 0;
				while ($prop_fields = $properties->GetNext())
				{
					(is_array($ariPhotos) && in_array($ioffersid."-".$prop_fields["ID"], $aroPhotos)) ? $sel = "selected" : $sel ="";
				  	$offersPropString .= "<option ".$sel." value='".$ioffersid."-".$prop_fields["ID"]."'>".$prop_fields["NAME"]."</option>";
				  	$many++;
				}
				if ($many) {
					$offersPropString .= "</select>";
				} else {
					$offersPropString = GetMessage("GSA_NOTYPEFILE");
				}

			}
		}
		?>
			<tr>
				<td style="text-align:left;"> <input type="checkbox" <?if (is_array($arBlocks) && in_array($iblockid, $arBlocks)) echo "checked";?> name="IBLOCKS[]" value="<?=$iblockid?>" id="iblock<?=$iblockid?>"><label for="iblock<?=$iblockid?>"><?=$iblockname." [".$iblockid."]"?></label></td>
				<td style="text-align:left;"><input name="IBLOCKGSANAME[<?=$iblockid?>]" type="text" value="<?=$gsaName?>"></td>
				<td>
					<?if ($ioffersid && $ioffersname):?>
						<?=$ioffersname?>
					<?endif?>
				</td>
				<td><?=$iblockPropString?></td>
				<td><?=$offersPropString?></td>
			</tr>

		<?
	}
	}
}
?>

<!-- тип цен -->
<?
$dbPriceType = CCatalogGroup::GetList(
        array("SORT" => "ASC"),
        array(),
        false,
        false,
        array("NAME_LANG", "ID")
    );
?>
<br>
<tr>
	<td colspan="5">
		<br><?=GetMessage("GSA_SELECTPRICE")?><br>
	</td>
</tr>
<tr>
	<td colspan="5">
		<select name="PRICE_TYPE">
		<?
			while ($arPriceType = $dbPriceType->Fetch())
			{
			    ($pricetype == $arPriceType["ID"]) ? $sel = "selected" : $sel = "";
			    echo "<option ".$sel." value=".$arPriceType["ID"].">".$arPriceType["NAME_LANG"]."</option>";
			}
		?>
		</select>
		<br/><br/>
		<?=GetMessage("GSA_PERIODSECS")?> <input type="text" name="period" value="<?=intval($period)?>">
	</td>
</tr>



<?endif?>

<?$tabControl->BeginNextTab();?>
<?CJSCore::Init(array('ajax'));?>
<tr>
	<td>
		<?
			$arVariants = cGSA::getDeliveryMethods();
			echo "<table>
					<tr>
						<td width='30%'><h3>Служба доставки Битрикс</h3></td>
						<td width='40%'><h3>Тип доставки GetShopApp</h3></td>
					</tr>";
			foreach ($arVariants as $value) {

				$seln = ($_POST["delivery"][$value["ID"]] == "n" || $arDelivery[$value["ID"]] == "n") ? "selected" : "";
				$selc = ($_POST["delivery"][$value["ID"]] == "c" || $arDelivery[$value["ID"]] == "c") ? "selected" : "";
				$selp = ($_POST["delivery"][$value["ID"]] == "p" || $arDelivery[$value["ID"]] == "p") ? "selected" : "";
				$sels = ($_POST["delivery"][$value["ID"]] == "s" || $arDelivery[$value["ID"]] == "s") ? "selected" : "";
				echo '<tr><td>'.$value['Name'].'</td><td><select name="delivery['.$value["ID"].']"> <option '.$seln.' value="n">'.GetMessage("GSA_DONTEXPORT").'</option> <option '.$selc.' value="c">'.GetMessage("GSA_COURIER").'</option> <option '.$selp.' value="p">'.GetMessage("GSA_POST").'</option> <option '.$sels.' value="s">'.GetMessage("GSA_SAM").'</option> </select></td></tr>';
			}
			echo "</table>";

		?>
	</td>
</tr>

<?if (count($arDelivery) > 0):?>
	<!--<tr>
		<td>
			<a class="deliverclick" href="">Синхронизация методов доставки</a>
		</td>
	</tr>
	<tr>
		<td id="deliverresult">
			Результат:
		</td>
	</tr>-->
<?endif?>


<?$tabControl->BeginNextTab();?>
<tr>
	<td>
		<!-- <h3><?=GetMessage("GSA_PAYSTATUS")?></h3> -->		
		<table>		
			<tr>
				<td width='30%'><h3>Статусы Битрикс</h3></td>
				<td width='40%'><h3>Статусы GetShopApp (оплата)</h3></td>
				<td width='40%'><h3>Статусы GetShopApp (заказа)</h3></td>
			</tr>
		<?
			$arVariants = CSaleStatus::GetList(array(),array("LID" => "ru"));
			$stat = cGSA::getStatusList();
			$stato = cGSA::getStatusOrder();

			while($row =  $arVariants->Fetch())
			{
				//статус оплаты
				$str_stat="<select name='status[".$row['ID']."]'>
							<option value=''>-</option>";
				foreach($stat AS $key=>$value)
					if(is_array($arStatus['status']) && $arStatus['status'][$row['ID']]==$key)
						$str_stat .= "<option value='".$key."' selected='selected'>".$value."</option>";
					else
						$str_stat .= "<option value='".$key."'>".$value."</option>";
				$str_stat.="</select>";


				//статус заказа
				$str_stato="<select name='status_order[".$row['ID']."]'>
							<option value=''>-</option>";
				foreach($stato AS $key=>$value)
					if(is_array($arStatus['status_order']) && $arStatus['status_order'][$row['ID']]==$key)
						$str_stato .= "<option value='".$key."' selected='selected'>".$value."</option>";
					else
						$str_stato .= "<option value='".$key."'>".$value."</option>";
				$str_stato.="</select>";
				



				echo "	<tr>
							<td>".$row['NAME']."<br/><i>".$row['DESCRIPTION']."</i></td>
							<td>".$str_stat."</td>
							<td>".$str_stato."</td>
						</tr>";
			}
		?>
		</table>
	</td>
</tr>


<?$tabControl->BeginNextTab();?>
<tr>
	<td>
		<h3><?=GetMessage("GSA_SHOPSET")?></h3>
		<table>
			<tr>
				<td width="10%"><?=GetMessage("GSA_SHOPNAME")?></td><td width="90%"><input style="width:50%" type="text" name="SI[Name]" value="<?=$arSI['Name']?>"></td>
			</tr>
			<tr>
				<td><?=GetMessage("GSA_SHOPDESCR")?></td><td><textarea name="SI[Description]" style="width:50%; resize: none;"><?=$arSI['Description']?></textarea></td>
			</tr>
			<tr>
				<td><?=GetMessage("GSA_DELDESCR")?></td><td><input style="width:50%" type="text" name="SI[DeliveryDescription]" value="<?=$arSI['DeliveryDescription']?>"></td>
			</tr>
			<tr>
				<td><?=GetMessage("GSA_PAYDESCR")?></td><td><input style="width:50%" type="text" name="SI[PaymentDescription]" value="<?=$arSI['PaymentDescription']?>"></td>
			</tr>
			<input type="hidden" name="SI[Locale]" value="ru_RU">
		</table>
	</td>
</tr>





<?$tabControl->Buttons();?>
<div id="buttonsGSA">
	<p><?=$errors?></p>
	<input type="submit" name="Update" <?if ($MOD_RIGHT<'W') echo "disabled" ?> value="<?echo GetMessage('MAIN_SAVE')?>" class="adm-btn-save">
	<input type="reset" name="reset" value="<?=GetMessage("FORM_RESET")?>">
</div>
<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>

</div>



<script>
   window.BXDEBUG = true;
function DEMOLoad(){
   // BX.hide(BX("block"));
   // BX.show(BX("process"));
   BX.ajax.post(
    '/ajax.php',
    {text: 'Hello'},
    DEMOResponse
   );
}
function DEMOResponse (data){
   BX.debug('AJAX-DEMOResponse ', data);
   BX("deliverresult").innerHTML = data;
   // BX.show(BX("block"));
   // BX.hide(BX("process"));

}

BX.ready(function(){

   // BX.hide(BX("block"));
   // BX.hide(BX("process"));

    BX.bindDelegate(
      document.body, 'click', {className: 'deliverclick' },
      function(e){
         if(!e)
            e = window.event;

         DEMOLoad();
         return BX.PreventDefault(e);
      }
   );

});

</script>
