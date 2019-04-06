<?
#################################################
#        Company developer: IPOL
#        Developers: Nikta Egorov
#        Site: http://www.ipolh.com
#        E-mail: om-sv2@mail.ru
#        Copyright (c) 2006-2014 IPOL
#################################################
?>
<?
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

$module_id = "ipol.sdek";
CModule::IncludeModule($module_id);
if(sdekdriver::$MODULE_ID !== $module_id)
	echo "ERROR IN MODULE ID";

CModule::IncludeModule('sale');
CJSCore::Init(array("jquery"));
$isLogged  = sdekdriver::isLogged();
$converted = sdekdriver::isConverted();
$migrated  = sdekdriver::isLocation20();
$ctId      = sdekOption::getCityTypeId();

// НДС
$arNDS = array(
	'VATX'  => GetMessage('IPOLSDEK_NDS_VATX'),
	'VAT0'  => GetMessage('IPOLSDEK_NDS_VAT0'),
	'VAT10' => GetMessage('IPOLSDEK_NDS_VAT10'),
	'VAT18' => GetMessage('IPOLSDEK_NDS_VAT18'),
);

//определяем статусы заказов
$orderState=array(''=>'');
$tmpValue = CSaleStatus::GetList(array("SORT" => "ASC"), array("LID" => LANGUAGE_ID));
while($tmpVal=$tmpValue->Fetch()){
	if(!array_key_exists($tmpVal['ID'],$orderState))
		$orderState[$tmpVal['ID']]=$tmpVal['NAME']." [".$tmpVal['ID']."]";
}
//плательщики
$tmpValue=CSalePersonType::GetList(array('ACTIVE'=>'Y'));
$arPayers=array();
while($payer=$tmpValue->Fetch()){
	$arPayers[$payer['ID']]=array('NAME'=>$payer['NAME']." [".$payer['LID']."]");
		$arPayers[$payer['ID']]['sel']=true;
}
//местоположения
$tmpValue = CSaleOrderProps::GetList(array(),array("IS_LOCATION"=>"Y"));
$locProps = array();
while($element=$tmpValue->Fetch())
	$locProps[$element['CODE']] = $element['NAME'];

// города-отправители
$tmpValue = sqlSdekCity::select();
$senderCitiesJS = '';
$senderCities = array();
while($element=$tmpValue->Fetch()){
	$senderCitiesJS .= "{label:'{$element[NAME]} ({$element[REGION]})',value:'{$element[SDEK_ID]}'},";
	$senderCities[$element['SDEK_ID']] = $element['NAME']." (".$element['REGION'].")";
}

$arAllOptions = array(
	"logData" => array(
		array("logSDEK",GetMessage("IPOLSDEK_OPT_logSDEK"),false,array("text")), // LEGACY
		array("pasSDEK",GetMessage("IPOLSDEK_OPT_pasSDEK"),false,array("password")), // LEGACY
		array("logged","logged",false,array('text')),//залогинен ли пользователь
	),
	"common" => Array(
		// array("strName",GetMessage("IPOLSDEK_OPT_strName"),false,array("text")),
		array("departure",GetMessage("IPOLSDEK_OPT_depature"),'',array("text")),
		array("termInc",GetMessage("IPOLSDEK_OPT_termInc"),'',array("text",1)),
		array("showInOrders",GetMessage("IPOLSDEK_OPT_showInOrders"),"Y",array("selectbox"),array("Y" => GetMessage('IPOLSDEK_OTHR_ALWAYS'),"N" => GetMessage('IPOLSDEK_OTHR_DELIVERY'))),
		array("realSeller",GetMessage("IPOLSDEK_OPT_realSeller"),"",array("text")),
		array("addDeparture",GetMessage("IPOLSDEK_OPT_addDeparture"),"",array("text")),
	),
	"print" => Array(
		array("prntActOrdr",GetMessage("IPOLSDEK_OPT_prntActOrdr"),"O",array("selectbox"),array("O" => GetMessage('IPOLSDEK_OTHR_ACTSORDRS'),"A" => GetMessage('IPOLSDEK_OTHR_ACTSONLY'))),
		array("numberOfPrints",GetMessage("IPOLSDEK_OPT_numberOfPrints"),"2",array("text",2)),
	),
	"dimensionsDef" => array(//ѓабариты товаров (дефолтные)
		Array("lengthD", GetMessage("IPOLSDEK_OPT_lengthD"), '400', Array("text")),
		Array("widthD", GetMessage("IPOLSDEK_OPT_widthD"), '300', Array("text")),
		Array("heightD", GetMessage("IPOLSDEK_OPT_heightD"), '200', Array("text")),
		Array("weightD", GetMessage("IPOLSDEK_OPT_weightD"), '1000', Array("text")),
		Array("defMode", GetMessage("IPOLSDEK_OPT_defMode"), 'O', array("selectbox"), array('O'=>GetMessage("IPOLSDEK_LABEL_forOrder"),'G'=>GetMessage("IPOLSDEK_LABEL_forGood")))
	),
	"NDS" => array(//НДС
		Array("NDSUseCatalog", GetMessage("IPOLSDEK_OPT_NDSUseCatalog"), 'N', Array("checkbox")),
		Array("NDSGoods", GetMessage("IPOLSDEK_OPT_NDSGoods"), 'VATX', Array("selectbox"), $arNDS),
		Array("NDSDelivery", GetMessage("IPOLSDEK_OPT_NDSDelivery"), 'VATX', Array("selectbox"), $arNDS),
	),
	"status" => Array(
		array("setDeliveryId", GetMessage("IPOLSDEK_OPT_setDeliveryId"),"Y",array("checkbox")),
		array("markPayed", GetMessage("IPOLSDEK_OPT_markPayed"),"N",array("checkbox")),
		array("statusSTORE", GetMessage("IPOLSDEK_OPT_statusSTORE"),false,array("selectbox"),$orderState),
		array("statusTRANZT", GetMessage("IPOLSDEK_OPT_statusTRANZT"),false,array("selectbox"),$orderState),
		array("statusCORIER", GetMessage("IPOLSDEK_OPT_statusCORIER"),false,array("selectbox"),$orderState),
		array("statusPVZ", GetMessage("IPOLSDEK_OPT_statusPVZ"),false,array("selectbox"),$orderState),
		array("statusDELIVD", GetMessage("IPOLSDEK_OPT_statusDELIVD"),false,array("selectbox"),$orderState),
		array("statusOTKAZ", GetMessage("IPOLSDEK_OPT_statusOTKAZ"),false,array("selectbox"),$orderState),
	),
	"orderProps" => Array(//свойства заказа откуда брать
		Array("location", GetMessage("IPOLSDEK_JS_SOD_location"), 'LOCATION', Array("text")),
		Array("name", GetMessage("IPOLSDEK_JS_SOD_name"), 'FIO', Array("text")),
		Array("email", GetMessage("IPOLSDEK_JS_SOD_email"), 'EMAIL', Array("text")),
		Array("phone", GetMessage("IPOLSDEK_JS_SOD_phone"), 'PHONE', Array("text")),
		Array("address", GetMessage("IPOLSDEK_JS_SOD_line"), 'ADDRESS', Array("text")),
		Array("street", GetMessage("IPOLSDEK_JS_SOD_street"), 'STREET', Array("text")),
		Array("house", GetMessage("IPOLSDEK_JS_SOD_house"), 'HOUSE', Array("text")),
		Array("flat", GetMessage("IPOLSDEK_JS_SOD_flat"), 'FLAT', Array("text")),
	),	
	"itemProps" => Array(//свойства товара откуда брать
		Array("articul", GetMessage("IPOLSDEK_OPT_articul"), 'ARTNUMBER', Array("text")),
		Array("getParentArticul", GetMessage("IPOLSDEK_OPT_getParentArticul"), 'Y', Array("checkbox")),
		Array("noVats", GetMessage("IPOLSDEK_OPT_noVats"), 'N', Array("checkbox")),
	),
	"vidjet" => array(
		array("pvzID",GetMessage("IPOLSDEK_OPT_pvzID"),"",array("text")),
		array("pvzPicker",GetMessage("IPOLSDEK_OPT_pvzPicker"),"ADDRESS",array("text")),
		array("buttonName",GetMessage("IPOLSDEK_OPT_buttonName"),"",array("text")),
		array("autoSelOne",GetMessage("IPOLSDEK_OPT_autoSelOne"),"",array("checkbox")),
	),
	"basket" => array(
		array("noPVZnoOrder",GetMessage("IPOLSDEK_OPT_noPVZnoOrder"),"N",array("checkbox")),
		array("hideNal",GetMessage("IPOLSDEK_OPT_hideNal"),"Y",array("checkbox")),
		array("hideNOC",GetMessage("IPOLSDEK_OPT_hideNOC"),"Y",array("checkbox")),
		array("cntExpress",GetMessage("IPOLSDEK_OPT_cntExpress"),"500",array("text")),
		array("mindEnsure",GetMessage("IPOLSDEK_OPT_mindEnsure"),"N",array("checkbox")),
		array("ensureProc",GetMessage("IPOLSDEK_OPT_ensureProc"),"1.5",array("text")),
	),
	"addingService" => array(
		array("addingService",GetMessage("IPOLSDEK_OPT_addingService"),"",array("text")),
		array("tarifs",GetMessage("IPOLSDEK_OPT_tarifs"),"",array("text")),
	),
	"paySystems" => array(
		array("paySystems",GetMessage("IPOLSDEK_OPT_paySystems"),"",array("text")),
	),
	"service"=>array(
		array("last",GetMessage("IPOLSDEK_JS_SOD_last"),false,array("text")),//последня заявка
		array("schet",GetMessage("IPOLSDEK_JS_SOD_schet"),'0',array("text")),//количество заявок
		array("statCync",GetMessage("IPOLSDEK_OPT_statCync"),'0',array("text")),//дата последнего опроса статусов заказов
		array("dostTimeout",GetMessage("IPOLSDEK_OPT_dostTimeout"),'6',array("text")),//таймаут запроса доставки
		array("timeoutRollback",GetMessage("IPOLSDEK_OPT_timeoutRollback"),'15',array("text")),//таймаут запроса доставки
	),
	"warhouses" => array(
		array("warhouses",GetMessage("IPOLSDEK_OPT_warhouses"),false,array('checkbox')),
	),
	// "autoloads" => array( // их нет, но они есть. Чтобы не занулялось при сохранении.
		// array("autoloads",GetMessage("IPOLSDEK_OPT_autoloads"),false,array('checkbox')),
	// ),
	"other" => array(
		array("senders","",false,array("text")),//отправители
		array("allowSenders",GetMessage("IPOLSDEK_OPT_allowSenders"),false,array('checkbox')),
		array("countries",GetMessage("IPOLSDEK_OPT_countries"),'{"rus":{"act":"Y"}}',array('text')),
		array("noteOrderDateCC",GetMessage("IPOLSDEK_OPT_noteOrderDateCC"),'N',array('checkbox')),
	),
);

if($converted){
	$arAllOptions['common'][]= array("shipments",GetMessage("IPOLSDEK_OPT_shipments"),'N',array("checkbox"));
		// старусы отгрузок
	$stShipment = array(''=>'');
	$dbStatuses = CSaleStatus::GetList(array('SORT' => 'asc'),array('TYPE'=>'D','LID'=>'ru'),false,false,array('ID','TYPE','NAME'));
	while($arStatus = $dbStatuses->Fetch())
		$stShipment[$arStatus['ID']] = $arStatus['NAME']." [{$arStatus['ID']}]";
	foreach($arAllOptions["status"] as $option)
		if(strpos($option[0],'status') === 0){
			$arAllOptions["status"][]=array('stShipment'.substr($option[0],6),$option[1],false,array("selectbox"),$stShipment);
		}
}

if($isLogged){
	$import = (COption::GetOptionString($module_id,'importMode','N') === 'Y');
	$autoloads = (COption::GetOptionString($module_id,'autoloads','N' ) === 'Y'); // AUTO

	$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("IPOLSDEK_TAB_FAQ"), "TITLE" => GetMessage("IPOLSDEK_TAB_TITLE_FAQ")),
		array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_SET"), "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
		array("DIV" => "edit3", "TAB" => GetMessage("IPOLSDEK_TAB_LIST"), "TITLE" => GetMessage("IPOLSDEK_TAB_TITLE_LIST")),
		array("DIV" => "edit4", "TAB" => GetMessage("IPOLSDEK_TAB_RIGHTS"), "TITLE" => GetMessage("IPOLSDEK_TAB_TITLE_RIGHTS")),
		array("DIV" => "edit5", "TAB" => GetMessage("IPOLSDEK_TAB_CITIES"), "TITLE" => GetMessage("IPOLSDEK_TAB_TITLE_CITIES")),
	);
	if($import)
		$aTabs[] = array("DIV" => "edit6", "TAB" => GetMessage("IPOLSDEK_TAB_IMPORT"), "TITLE" => GetMessage("IPOLSDEK_TAB_TITLE_IMPORT"));
	if($autoloads) // AUTO
		$aTabs[] = array("DIV" => "edit".(($import)?'7':'6'), "TAB" => GetMessage("IPOLSDEK_TAB_AUTOLOADS"), "TITLE" => GetMessage("IPOLSDEK_TAB_TITLE_AUTOLOADS"));
	foreach(GetModuleEvents($module_id,"onTabsBuild",true) as $arEvent)
		ExecuteModuleEventEx($arEvent,Array(&$arTabs));
	$divId = count($aTabs);
	if(count($arTabs))
		foreach($arTabs as $tabName => $path)
			$aTabs[]=array("DIV" => "edit".(++$divId), "TAB" => $tabName, "TITLE" => $tabName);
}else
	$aTabs = array(array("DIV" => "edit1", "TAB" => GetMessage("IPOLSDEK_TAB_LOGIN"), "TITLE" => GetMessage("IPOLSDEK_TAB_TITLE_LOGIN")));

//Restore defaults
if ($USER->IsAdmin() && $_SERVER["REQUEST_METHOD"]=="GET" && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
    COption::RemoveOption($module_id);

//Save options
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()){
	if(strlen($RestoreDefaults)>0)
		COption::RemoveOption($module_id);
	else{
		// blockPVZ
		if($_REQUEST['noPVZnoOrder'] == 'Y' && COption::GetOptionString($module_id,'noPVZnoOrder','N') == 'N'){
			if($converted){
				RegisterModuleDependences("sale", "OnSaleOrderBeforeSaved", $module_id, "CDeliverySDEK", "noPVZNewTemplate");
				RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", $module_id, "CDeliverySDEK", "noPVZOldTemplate");
			}else
				RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", $module_id, "CDeliverySDEK", "noPVZOldTemplate");
		}elseif((!array_key_exists('noPVZnoOrder',$_REQUEST) || $_REQUEST['noPVZnoOrder'] == 'N') && COption::GetOptionString($module_id,'noPVZnoOrder','N') == 'Y'){
			if($converted){
				UnRegisterModuleDependences("sale", "OnSaleOrderBeforeSaved", $module_id, "CDeliverySDEK", "noPVZNewTemplate");
				UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", $module_id, "CDeliverySDEK", "noPVZOldTemplate");
			}else
				UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", $module_id, "CDeliverySDEK", "noPVZOldTemplate");
		}
		
		foreach($_REQUEST['addDeparture'] as $key => $place)
			if(!$place)
				unset($_REQUEST['addDeparture'][$key]);
		foreach(array('paySystems','addingService','tarifs','addDeparture') as $opt)
			$_REQUEST[$opt] = ($_REQUEST[$opt]) ? serialize($_REQUEST[$opt]) : 'a:0:{}';
		$_REQUEST['dostTimeout']  = (floatval($_REQUEST['dostTimeout']) > 0) ? $_REQUEST['dostTimeout']  : 6;
		$_REQUEST['cntExpress']   = (floatval($_REQUEST['cntExpress']) > 0) ? $_REQUEST['cntExpress']  : 0;
		setSenders();
		$_REQUEST['countries']    = json_encode(sdekOption::zajsonit($_REQUEST['countries']));
		$_REQUEST['ensureProc']    = floatval(str_replace(array(',',' '),array('.',''),$_REQUEST['ensureProc']));

		$arNumReq = array('numberOfPrints','termInc','lengthD','widthD','heightD','weightD');
		foreach($arNumReq as $key){
			$_REQUEST[$key] = intval($_REQUEST[$key]);
			if($_REQUEST[$key] <= 0 && $key!='termInc')
				unset($_REQUEST[$key]);
		}
		foreach($arAllOptions as $aOptGroup){
			foreach($aOptGroup as $option){
				__AdmSettingsSaveOption($module_id, $option);
			}
		}
	}

	if($_REQUEST["back_url_settings"] <> "" && $_REQUEST["Apply"] == "")
		 echo '<script type="text/javascript">window.location="'.CUtil::addslashes($_REQUEST["back_url_settings"]).'";</script>';

	sdekOption::clearCache(true);
}

function setSenders(){
	if(array_key_exists('senders',$_REQUEST))
		foreach($_REQUEST['senders'] as $key => $sender){
			if(
				!$sender["senderName"]    ||
				!$sender["courierCity"]   ||
				!$sender["courierStreet"] ||
				!$sender["courierHouse"]  ||
				!$sender["courierFlat"]   ||
				!$sender["courierPhone"]  ||
				!$sender["courierName"]
			)
				unset($_REQUEST['senders'][$key]);
			else
				foreach($sender as $k => $v)
					$_REQUEST['senders'][$key][$k] = str_replace("'",'"',$v);
		}
	else
		$_REQUEST['senders'] = false;
	sdekOption::senders($_REQUEST['senders']);
}

function ShowParamsHTMLByArray($arParams){
	global $module_id;
	global $senderCities;
	foreach($arParams as $Option){
		if($Option[3][0]!='selectbox'){
			switch($Option[0]){
				case 'departure':
					$cityDef = COption::GetOptionString('sale','location',false);
					if(!$cityDef){
						$arCites = array();
						$cites = CSite::GetList($by="sort",$order="desc");
						$similar = true;
						$oldOp = 'none';
						while($cite=$cites->Fetch()){
							$op = COption::GetOptionString('sale','location',false,$cite['LID']);
							if($op)
								$arCites[$cite['LID']] = $op;
							if($similar && $oldOp != 'none' && $oldOp != $op)
								$similar = false;
							$oldOp = $op;
						}
						if(!count($arCites))
							echo "<tr><td colspan='2'>".GetMessage('IPOLSDEK_LABEL_NOCITY')."</td><tr>";
						elseif($similar)
							sdekOption::printSender(array_pop($arCites));
						else{
							$strSel = "<select name='departure'>";
							$seltd = COption::GetOptionString($module_id,'departure','');
							foreach($arCites as $cite => $city){
								$SDEKcity = sdekOption::getSDEKCity($city);
								if(!$SDEKcity)
									$strSel .= "<option value='' disabled>".GetMessage('IPOLSDEK_LABEL_NOSDEKCITYSHORT')." [$cite]</option>";
								else
									$strSel .= "<option ".(($seltd == $SDEKcity['BITRIX_ID'])?'selected':'')." value='".$SDEKcity['BITRIX_ID']."'>".$SDEKcity['NAME']." [$cite]</option>";
							}
							echo "<tr><td>".GetMessage('IPOLSDEK_OPT_depature')."</td><td>".$strSel."</select></td><tr>";
						}
					}else
						sdekOption::printSender($cityDef);
				break;
				case 'addDeparture': 
					echo "<td style='vertical-align:top;'>".GetMessage('IPOLSDEK_OPT_'.$Option[0])."</td><td><div id='IPOLSDEK_{$Option[0]}Place'>";
					$svd = unserialize(COption::GetOptionString($module_id,$Option[0],'a:{}'));
					if($svd && count($svd))
						foreach($svd as $index => $city)
							echo "<div><input type='text' value='{$senderCities[$city]}' class='IPOLSDEK_{$Option[0]}'><input type='hidden' name='{$Option[0]}[$index]' value='$city'>&nbsp;<a href='javascript:void(0)' style='color:red;' onclick='IPOLSDEK_setups.base.depature.delete($(this))'>X</a></div>";
					else
						echo "<div><input type='text' class='IPOLSDEK_{$Option[0]}'><input type='hidden' name='{$Option[0]}[$index]' name='{$Option[0]}[]'></div>";
					echo "</div><br><input type='button' onclick='IPOLSDEK_setups.base.depature.add()' value='".GetMessage("IPOLSDEK_LABEL_".$Option[0])."'></td>";
				break;
				default: __AdmSettingsDrawRow($module_id, $Option); break;
			}
		}else{
			$optVal=COption::GetOptionString($module_id,$Option['0'],$Option['2']);
			$str='';
			foreach($Option[4] as $key => $val){
				$chkd='';
				if($optVal==$key)
					$chkd='selected';
				$str.='<option '.$chkd.' value="'.$key.'">'.$val.'</option>';
			}
			echo '<tr>
					<td width="50%" class="adm-detail-content-cell-l">'.$Option[1].'</td>  
					<td width="50%" class="adm-detail-content-cell-r"><select name="'.$Option['0'].'">'.$str.'</select></td>
				</tr>';
		}
	}
}
function showOrderOptions(){//должна вызываться после получения плательщиков
	global $module_id;
	global $arPayers;
	$arNomatterProps=array('street'=>true,'house'=>true,'flat'=>true);
	foreach($GLOBALS['arAllOptions']['orderProps'] as $orderProp){
		$value=COption::getOptionString($module_id,$orderProp[0],$orderProp[2]);
		if(!trim($value)){
			$showErr=true;
			if($orderProp[0]=='address'&&COption::getOptionString($module_id,'street',$orderProp[2])){
				unset($arNomatterProps['street']);
				$showErr=false;
			}
		}
		else
			$showErr=false;

		$arError=array(
			'noPr'=>false,
			'unAct'=>false,
			'str'=>false,
		);

		if(!array_key_exists($orderProp[0],$arNomatterProps)&&$value){
			foreach($arPayers as $payId =>$payerInfo)
				if($payerInfo['sel']){
					if($curProp=CSaleOrderProps::GetList(array(),array('PERSON_TYPE_ID'=>$payId,'CODE'=>$value))->Fetch()){
						if($curProp['ACTIVE']!='Y')
							$arError['unAct'].="<br>".$payerInfo['NAME'];
					}
					else
						$arError['noPr'].="<br>".$payerInfo['NAME'];
				}
			if($arError['noPr']){
				$arError['str']=GetMessage('IPOLSDEK_LABEL_noPr')." <a href='#' class='PropHint' onclick='return IPOLSDEK_setups.popup(\"pop-noPr_".$orderProp[0]."\",$(this));'></a> ";?>
				<div id="pop-noPr_<?=$orderProp[0]?>" class="b-popup" style="display: none; ">
					<div class="pop-text"><?=GetMessage('IPOLSDEK_LABEL_Sign_noPr')?><br><br><?=substr($arError['noPr'],4)?></div>
					<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
				</div>
			<?}
			if($arError['unAct']){
				$arError['str'].=GetMessage('IPOLSDEK_LABEL_unAct')." <a href='#' class='PropHint' onclick='return IPOLSDEK_setups.popup(\"pop-unAct_".$orderProp[0]."\",$(this));'></a>";?>
				<div id="pop-unAct_<?=$orderProp[0]?>" class="b-popup" style="display: none; ">
					<div class="pop-text"><?=GetMessage('IPOLSDEK_LABEL_Sign_unAct')?><br><br><?=substr($arError['unAct'],4)?></div>
					<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
				</div>
			<?}
			
			if($arError['str'])
				$showErr=true;
		}
		elseif(array_key_exists($orderProp[0],$arNomatterProps))
			$showErr=false;
		
		$styleTdStr = ($orderProp[0] == 'street')?'style="border-top: 1px solid #BCC2C4;"':'';
	?>
		<tr>
			<td width="50%" <?=$styleTdStr?> class="adm-detail-content-cell-l"><?=$orderProp[1]?><?=($orderProp[0]=='address')?" <a href='#' class='PropHint' onclick='return IPOLSDEK_setups.popup(\"pop-address\",$(this));'></a>":''?></td>
			<td width="50%" <?=$styleTdStr?> class="adm-detail-content-cell-r">
				<?if($orderProp[0] != 'location'){?>
					<input type="text" size="" maxlength="255" value="<?=$value?>" name="<?=$orderProp[0]?>">
				<?}else{
					global $locProps;
					if($showErr && !$arError['str']) // не выводить "выберите свойство"
						$showErr = false;
					// Местоположение выбирается автоматически из свойств типа "Местоположение"
					if(count($locProps)==0){
						$showErr = true;
						$arError['str'] = GetMessage('IPOLSDEK_LABEL_noLoc');
					}elseif(count($locProps)==1){
						$key = array_pop(array_keys($locProps));
					?>
						<input type='hidden' value="<?=$key?>" name="<?=$orderProp[0]?>">
						<?=array_pop($locProps)?> [<?=$key?>]
					<?}else{?>
						<select name="<?=$orderProp[0]?>">
							<?foreach($locProps as $code => $name){?>
								<option value='<?=$code?>' <?=($value==$code)?"selected":""?>><?=$name." [".$code."]"?></option>
							<?}?>
						</select>
					<?}
				}?>
				&nbsp;&nbsp;<span class='errorText' <?if(!$showErr){?>style='display:none'<?}?>><?=($arError['str'])?$arError['str']:GetMessage('IPOLSDEK_LABEL_shPr')?></span>
			</td>
		</tr>
	<?}
}

$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>

<script>
	var IPOLSDEK_setups = {
		ajax: function(params){
			var ajaxParams = {
				type : 'POST',
				url  : "/bitrix/js/<?=$module_id?>/ajax.php",
			};
			if(typeof(params.data) != 'undefined')
				ajaxParams.data = params.data;
			if(typeof(params.dataType) != 'undefined')
				ajaxParams.dataType = params.dataType;
			if(typeof(params.success) != 'undefined')
				ajaxParams.success = params.success;
			$.ajax(ajaxParams);
		},

		copyObj: function(obj){
			if(obj == null || typeof(obj) != 'object')
				return obj;
			if(obj.constructor == Array)
				return [].concat(obj);
			var temp = {};
			for(var key in obj)
				temp[key] = IPOLSDEK_setups.copyObj(obj[key]);
			return temp;
		},

		inArray: function(wat,arr){
			return arr.filter(function(item){return item == wat}).length;
		},

		isEmpty: function(obj){
			if(typeof(obj) == 'object')
				for(var i in obj)
					return false;
			return true;
		},

		popup: function(code, info){
			$('.b-popup').hide();

			var LEFT = $(info).offset().left;		
			var obj = $('#'+code);

			LEFT -= parseInt(parseInt(obj.css('width'))/2);

			obj.css({
				top: ($(info).position().top+15)+'px',
				left: LEFT,
				display: 'block'
			});

			return false;
		},

		reload: function(){
			window.location.reload();
		},

		page: function(wat){
			return (typeof(IPOLSDEK_setups[wat]) !== 'undefined');
		}
	};
	$(document).ready(function(){
		for(var i in IPOLSDEK_setups)
			if(typeof(IPOLSDEK_setups[i]) == 'object' && typeof(IPOLSDEK_setups[i].ready) == 'function')
				IPOLSDEK_setups[i].ready();
	});
</script>

<?if($isLogged){?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?echo LANG?>">
	<?
	$tabControl->Begin();
	$tabControl->BeginNextTab();
	include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$module_id."/optionsInclude/faq.php");
	$tabControl->BeginNextTab();
	include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$module_id."/optionsInclude/setups.php");
	$tabControl->BeginNextTab();
	include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$module_id."/optionsInclude/table.php");
	$tabControl->BeginNextTab();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
	$tabControl->BeginNextTab();
	include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$module_id."/optionsInclude/errCities.php");
	if($import){
		$tabControl->BeginNextTab();
		include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$module_id."/optionsInclude/import.php");
	}
	if($autoloads){
		$tabControl->BeginNextTab();
		include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$module_id."/optionsInclude/autoloads.php");
	}
	if(count($arTabs))
		foreach($arTabs as $tabName => $path){
			$tabControl->BeginNextTab();
			include_once($_SERVER['DOCUMENT_ROOT'].$path);
		}
	$tabControl->Buttons();
	?>
	<div align="left">
		<input type="hidden" name="Update" value="Y">
		<input type="submit" <?if(!$USER->IsAdmin())echo " disabled ";?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
	</div>
	<?$tabControl->End();?>
	<?=bitrix_sessid_post();?>
</form>
<?}
else{
	$tabControl->Begin();
	$tabControl->BeginNextTab();
	include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$module_id ."/optionsInclude/login.php");
	$tabControl->End();
}
?>