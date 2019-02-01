<?
$SDEK_ID  = false;

if(self::$requestVals){ // получаем параметры заявки из БД, если они есть
	$ordrVals = self::$requestVals;
	$status=$ordrVals['STATUS'];
	$message=array();
	if($ordrVals['MESSAGE'])
		$message=unserialize($ordrVals['MESSAGE']);
	foreach($message as $key => $sign)
		if(in_array($key,array('service','location','street','house','flat','PVZ','name','phone','email','comment','number')))
			$message[$key]='<br><span style="color:#FF4040">'.$sign.'</span>';
		else{
			$message['troubles'].='<span style="color:#FF4040">'.$sign.' ('.$key.')</span><br>';
		}
	$SDEK_ID = $ordrVals['SDEK_ID'];
	$MESS_ID = $ordrVals['MESS_ID'];
	$ordrVals=unserialize($ordrVals['PARAMS']); // массив значений заявки, если не задан - заполняется по умолчанию из параметров, указанных в опциях и покупателем
	self::$isLoaded = true;

	if(self::$workMode == 'order')
		CDeliverySDEK::setOrderGoods(self::$orderId);
	else
		CDeliverySDEK::setShipmentGoods(intval($_REQUEST["shipment_id"]));

	$naturalGabs = array(
		"D_L" => CDeliverySDEK::$goods['D_L'],
		"D_W" => CDeliverySDEK::$goods['D_W'],
		"D_H" => CDeliverySDEK::$goods['D_H'],
		"W" => CDeliverySDEK::$goods['W']
	);

	if(!$ordrVals['toPay'])
		$ordrVals['toPay'] = 0;
	if(!$ordrVals['deliveryP'])
		$ordrVals['deliveryP'] = 0;

	$cntrCurrency = $ordrVals['currency'];
}else{
	$ordrVals = self::formation();
	$naturalGabs = $ordrVals['GABS'];
}

$orderCity = sqlSdekCity::getBySId($ordrVals['location']);

if($orderCity)
	$cityName = $orderCity['NAME'];
else
	$cityName = "ERROR";
if(!$status)
	$status='NEW';

if(self::$isLoaded)
	self::$isEditable = (!self::$requestVals['OK']);
else
	self::$isEditable = true;

$orignCityId = false; // город-исходник из заказа, для которого рассчитывается доставка - только он есть в таблице соответствий

// Проверка города [если отправляется в ошибочный]
$errCities = sdekHelper::getErrCities();
$multiCity = false;
$multiCityS = false;
if(array_key_exists('many',$errCities) && array_key_exists(self::$orderDescr['properties'][COption::GetOptionString(self::$MODULE_ID,$prop,'location')],$errCities['many'])){
	$multiCity = '&nbsp;&nbsp;<a href="#" class="PropWarning" onclick="return IPOLSDEK_oExport.popup(\'pop-multiCity\',this);"></a>	
	<div id="pop-multiCity" class="b-popup" style="display: none; ">
	<div class="pop-text">'.GetMessage("IPOLSDEK_SOD_MANYCITY").'<div class="close" onclick="$(this).closest(\'.b-popup\').hide();"></div>
</div>';
	$multiCityS = "<select id='IPOLSDEK_ms' onchange='IPOLSDEK_onMSChange(\$(this))'>
	<option value='".$orignCityId."' ".(($ordrVals['location'] == $orignCityId)?"selected":"").">".$errCities['many'][$ordrVals['location']]['takenLbl']."</option>";
	foreach($errCities['many'][$ordrVals['location']]['sdekCity'] as $sdekId => $arAnalog)
		$multiCityS .= "<option value='".$sdekId."' ".(($ordrVals['location'] == $sdekId)?"selected":"").">".$arAnalog['region'].", ".$arAnalog['name']."</option>";
	$multiCityS .= "</select>";
}

$payment = sqlSdekCity::getCityPM($ordrVals['location']); // платежная система

//ТАРИФЫ
$arList = CDeliverySDEK::getListFile();
$arModdedList = CDeliverySDEK::wegihtPVZ($ordrVals['GABS']["W"] * 1000);
$strOfCodes='';

$arTarif = sdekdriver::getExtraTarifs();
$arTarifMode = unserialize(COption::GetOptionString(self::$MODULE_ID,"tarifs","a:{}"));
$hasSelected = false;

foreach($arTarif as $code => $arSign){//тариф
	if($arSign['SHOW'] == 'Y' || $code == $ordrVals['service']){
		$selected = '';
		if(!$hasSelected && $code == $ordrVals['service'])
			$selected='selected';
		elseif(!$hasSelected && !$ordrVals['service']){ //пытаемся угадать тариф
			if(strpos($ordrVals['address'],"#S")){
				if(array_key_exists($cityName,$arList['PVZ']) && $code == 136)
					$selected = 'selected';
			}
			elseif($code == 137)
				$selected = 'selected';
		}

		if($selected)
			$hasSelected = true;

		$highLight = '';
		if($code == 138 || $code == 139)
			$highLight = "style='background-color:#F08192'";
		
		$strOfCodes.="<option $highLight value='$code' $selected>".$arSign['NAME']."</option>";
	}
}

// город-отправитель
$citySenders = unserialize(COption::GetOptionString(self::$MODULE_ID,'addDeparture','a:{}'));
if($citySenders && count($citySenders)){
	$tmpVal = $citySenders;
	$city = sqlSdekCity::getByBId(COption::GetOptionString(self::$MODULE_ID,'departure'));
	$citySenders = array($city['SDEK_ID']=>$city['NAME']." (".GetMessage('IPOLSDEK_LBL_BASIC').")");
	foreach($tmpVal as $cityId){
		$city = sqlSdekCity::getBySId($cityId);
		$citySenders[$city['SDEK_ID']] = $city['NAME']." (".$city['REGION'].")";
	}
}
// безнал
$badPay = (self::$orderDescr['info']['PAYED'] != 'Y');

// ПВЗ
$strOfPSV='';
$arBPVZ = "{";
if(array_key_exists($cityName,$arList['PVZ']))
	foreach($arList['PVZ'][$cityName] as $code => $punkts){
		if(!array_key_exists($code,$arModdedList[$cityName]))
			$arBPVZ .= $code.":true,";
		$selected = ($ordrVals['PVZ'] == $code) ? "selected" : "";
		$strOfPSV.="<option $selected value='".$code."'>".$punkts['Name']." (".$code.")"."</option>";
	}
$arBPVZ .= "}";

//Доп. опции
$exOpts = sdekdriver::getExtraOptions();
if(self::$isLoaded)
	foreach($exOpts as $code => $vals)
		if($ordrVals['AS'][$code] == 'Y')
			$exOpts[$code]['DEF'] = 'Y';
		else
			$exOpts[$code]['DEF'] = 'N';

// Вызов курьера
$allowCourier = (COption::GetOptionString(self::$MODULE_ID,'allowSenders','N') == 'Y');
if(!$ordrVals['courierCity'])
	$ordrVals['courierCity'] = sqlSdekCity::getByBId(sdekHelper::getNormalCity(COption::GetOptionString('sale','location',false)));
else
	$ordrVals['courierCity'] = sqlSdekCity::getBySId($ordrVals['courierCity']);

$citiesSender = sqlSdekCity::select();
$IPOLSDEK_sC = '';
$tmpCts = array();
while($element=$citiesSender->Fetch()){
	$IPOLSDEK_sC .= "{label:'{$element['NAME']} ({$element['REGION']})',value:'{$element['SDEK_ID']}'},";
	$tmpCts[$element['SDEK_ID']] = $element['NAME']." (".$element['REGION'].')';
}
$svdCouriers = sdekOption::senders($_REQUEST['senders']);

$IPOLSDEK_svdC = "";
if($svdCouriers && count($svdCouriers))
	foreach($svdCouriers as $ind => $vals){
		$IPOLSDEK_svdC .= $ind.":{";
		foreach($vals as $name => $value)
			$IPOLSDEK_svdC .= $name.": '".$value."',";
		$IPOLSDEK_svdC .= "cityName: '".$tmpCts[$vals['courierCity']]."',";
		$IPOLSDEK_svdC .= "},";
	}

// Разбиение на города-отправители
$senderWH = 0;
if(COption::GetOptionString(self::$MODULE_ID,'warhouses','N')==='Y'){
	if(self::isConverted() && $workMode == 'order'){
		CDeliverySDEK::countDelivery(array(
				'GOODS'      => CDeliverySDEK::setOrderGoods(self::$orderId),
				'CITY_TO_ID' => $ordrVals['location']
		));
	}
	$senderWH = count(sdekShipmentCollection::$shipments)-1;
	if($senderWH)
		if(strpos($ordrVals['service'],'[')!==0){
			$senderWH = array();
			foreach(sdekShipmentCollection::$shipments as $shipment)
				$senderWH[]= array($shipment->sender,$ordrVals['service']);
		}else
			$senderWH = json_decode($ordrVals['service'],true);
}

// страны и валюты

$arCity  = sqlSdekCity::getBySId($ordrVals['location']);
$country = ($arCity['COUNTRY']) ? $arCity['COUNTRY'] : 'rus';

if(self::$isLoaded)
	$acc = self::defineAuth(array('ID'=>self::$requestVals['ACCOUNT']));
else
	$acc = self::defineAuth(array('COUNTRY'=>$country));

if(!$cntrCurrency){
	$svdCountries = self::zaDEjsonit(json_decode(COption::GetOptionString(self::$MODULE_ID,'countries','{"rus":{"act":"Y"}}'),true));
	$defVal = CCurrency::GetBaseCurrency(); // сейчас считается, что всегда рубли
	$cntrCurrency = false;
	if(array_key_exists($country,$svdCountries) && $svdCountries[$country]['cur'] && $svdCountries[$country]['cur'] != $defVal)
		$cntrCurrency = $svdCountries[$country]['cur'];
}

CJSCore::Init(array("jquery"));
?>
<?=sdekdriver::getModuleExt('packController')?>
<link href="/bitrix/js/<?=self::$MODULE_ID?>/jquery-ui.css?<?=mktime()?>" type="text/css"  rel="stylesheet" />
<link href="/bitrix/js/<?=self::$MODULE_ID?>/jquery-ui.structure.css?<?=mktime()?>" type="text/css"  rel="stylesheet" />

<script src='/bitrix/js/<?=self::$MODULE_ID?>/jquery-ui.js?<?=mktime()?>' type='text/javascript'></script>
<style type='text/css'>
	.PropWarning{
		background: url('/bitrix/images/<?=self::$MODULE_ID?>/trouble.png') no-repeat transparent;
		background-size: contain;
		display: inline-block;
		height: 12px;
		position: relative;
		width: 12px;
	}
	.PropWarning:hover{
		background: url('/bitrix/images/<?=self::$MODULE_ID?>/trouble.png') no-repeat transparent !important;
		background-size: contain !important;
	}
	.PropHint { 
		background: url('/bitrix/images/<?=self::$MODULE_ID?>/hint.gif') no-repeat transparent;
		display: inline-block;
		height: 12px;
		position: relative;
		width: 12px;
	}
	.PropHint:hover{background: url('/bitrix/images/<?=self::$MODULE_ID?>/hint.gif') no-repeat transparent !important;}
	.b-popup { 
		background-color: #FEFEFE;
		border: 1px solid #9A9B9B;
		box-shadow: 0px 0px 10px #B9B9B9;
		display: none;
		font-size: 12px;
		padding: 19px 13px 15px;
		position: absolute;
		top: 38px;
		width: 300px;
		z-index: 12;
	}
	.b-popup .pop-text { 
		margin-bottom: 10px;
		color:#000;
	}
	.pop-text i {color:#AC12B1;}
	.b-popup .close { 
		background: url('/bitrix/images/<?=self::$MODULE_ID?>/popup_close.gif') no-repeat transparent;
		cursor: pointer;
		height: 10px;
		position: absolute;
		right: 4px;
		top: 4px;
		width: 10px;
	}
	#IPOLSDEK_wndOrder{
		width: 100%;
	}
	#IPOLSDEK_allTarifs{
		border-collapse: collapse;
		width: 100%;
	}
	#IPOLSDEK_allTarifs td{
		border: 1px dashed black;
		padding: 3px;
	}
	#IPOLSDEK_tarifWarning{
		display:none;
	}
	#IPOLSDEK_tarifWarning span{
		font-size: 10px;
	}
	#IPOLSDEK_service{
		max-width: 315px;
	}
	.IPOLSDEK_gabInput{
		width: 28px;
	}
	#IPOLSDEK_gabsPlace{
		min-height: 27px;
	}
	.IPOLSDEK_badInput{
		background-color: #FFBEBE !important;
	}
</style>
<script>
<?=sdekdriver::getModuleExt('mask_input')?>
var IPOLSDEK_oExport = {
	orderId  : "<?=self::$orderId?>",
	shipment : "<?=self::$shipmentID?>",
	mode     : "<?=self::$workMode?>",
	status   : "<?=$status?>",
	badPVZ   : <?=$arBPVZ?>,
	senderCities : [<?=$IPOLSDEK_sC?>],
	goodsPrice   : <?=$ordrVals['toPay']?>,
	delivPrice   : <?=$ordrVals['deliveryP']?>,

	ajax: function(params){
		var ajaxParams = {
			type  : 'POST',
			url   : "/bitrix/js/<?=self::$MODULE_ID?>/ajax.php",
			error : function(a,b,c){console.log('export '+b,c);}
		};
		if(typeof(params.data) != 'undefined')
			ajaxParams.data = params.data;
		if(typeof(params.dataType) != 'undefined')
			ajaxParams.dataType = params.dataType;
		if(typeof(params.success) != 'undefined')
			ajaxParams.success = params.success;
		$.ajax(ajaxParams);
	},

	isEmpty: function(obj){
		if(typeof(obj) == 'object')
			for(var i in obj)
				return false;
		return true;
	},

	load: function(){
		if($('#IPOLSDEK_btn').length) return;
		$('.adm-detail-toolbar').find('.adm-detail-toolbar-right').prepend("<a href='javascript:void(0)' onclick='IPOLSDEK_oExport.showWindow()' class='adm-btn' id='IPOLSDEK_btn'><?=GetMessage('IPOLSDEK_JSC_SOD_BTNAME')?></a>");
		var btn = $('#IPOLSDEK_btn');
		switch(IPOLSDEK_oExport.status){
			case 'NEW'    : break;
			case 'ERROR'  : btn.css('color','#F13939'); break;
			default       : btn.css('color','#3A9640'); break;
		}
	},

	// окно
	wnd: false,
	showWindow: function(){
		var savButStat='';
		if(IPOLSDEK_oExport.status!='ERROR' && IPOLSDEK_oExport.status!='NEW')
			savButStat='style="display:none"';
		var delButStat='';
		if(IPOLSDEK_oExport.status !='OK' && IPOLSDEK_oExport.status !='ERROR' && IPOLSDEK_oExport.status !='DELETD' )
			delButStat='style="display:none"';
		var prntButStat='style="display:none"';
		if(IPOLSDEK_oExport.status =='OK')
			prntButStat='';

		if(!IPOLSDEK_oExport.wnd){
			var html=$('#IPOLSDEK_wndOrder').parent().html();
			$('#IPOLSDEK_wndOrder').parent().html('');
			IPOLSDEK_oExport.wnd = new BX.CDialog({
				title: "<?=GetMessage('IPOLSDEK_JSC_SOD_WNDTITLE')?>",
				content: html,
				icon: 'head-block',
				resizable: true,
				draggable: true,
				height: '500',
				width: '505',
				buttons: [
					'<input type=\"button\" value=\"<?=GetMessage('IPOLSDEK_JSC_SOD_SAVESEND')?>\"  '+savButStat+'onclick=\"IPOLSDEK_oExport.send(\'saveAndSend\')\"/>', // сохранить и отправить
					'<input id=\"IPOLSDEK_allTarifsBtn\" type=\"button\" value=\"<?=GetMessage('IPOLSDEK_JSC_SOD_ALLTARIFS')?>\"  '+savButStat+'onclick=\"IPOLSDEK_oExport.allTarifs.show()\"/>', // все тарифы
					'<input type=\"button\" value=\"<?=GetMessage('IPOLSDEK_JSC_SOD_DELETE')?>\" '+delButStat+' onclick=\"IPOLSDEK_oExport.delete()\"/>', // удалить
					'<input type=\"button\" id=\"IPOLSDEK_PRINT\" value=\"<?=GetMessage('IPOLSDEK_JSC_SOD_PRNTSH')?>\" '+prntButStat+' onclick="IPOLSDEK_oExport.print(\''+IPOLSDEK_oExport.orderId+'\'); return false;"/>', // печать штрихкода
					'<input type=\"button\" value=\"<?=GetMessage('IPOLSDEK_JS_SOD_PACKS')?>\"  onclick="IPOLSDEK_packs.wnd.open(); return false;"/>', // места
					<?if($SDEK_ID){?>'<a href="http://www.edostavka.ru/track.html?order_id=<?=$SDEK_ID?>" target="_blank"><?=GetMessage('IPOLSDEK_JSC_SOD_FOLLOW')?></a>'<?}?> // отслеживание
				]
			});
			$('#IPOLSDEK_courierTimeBeg').mask("29:59");
			$('#IPOLSDEK_courierTimeEnd').mask("29:59");
			// $('#IPOLSDEK_courierPhone').mask("99999999999");

			$( "#IPOLSDEK_cSSelector" ).autocomplete({
			  source: IPOLSDEK_oExport.senderCities,
			  select: function(ev,ui){IPOLSDEK_oExport.courier.changeCity(2,ui);}
			});
		}
		IPOLSDEK_oExport.onCodeChange($('#IPOLSDEK_service'),true);
		IPOLSDEK_oExport.checkPay();
		IPOLSDEK_oExport.courier.handle();
		IPOLSDEK_oExport.onRecheck(true);
		IPOLSDEK_oExport.wnd.Show();
		<?if($cntrCurrency){?>
			IPOLSDEK_oExport.currency.init();
		<?}?>
	},

	// события
		//изменилась услуга: проверяем, не самовывоз ли, скрываем/показываем соответствующие поля доставки
	onCodeChange: function(wat,ifDef){
		if(wat.val() == 138 || wat.val() == 139) $('#IPOLSDEK_tarifWarning').css('display','table-row');
		else $('#IPOLSDEK_tarifWarning').css('display','');
		$('#IPOLSDEK_wndOrder').find('.IPOLSDEK_notSV').css('display','none');
		$('#IPOLSDEK_timeFrom').closest('tr').css('display','none');
		$('#IPOLSDEK_wndOrder').find('.IPOLSDEK_SV').css('display','none');
		switch(IPOLSDEK_oExport.defineTarifs(wat.val())){
			case 'courier': $('#IPOLSDEK_wndOrder').find('.IPOLSDEK_notSV').css('display','');
							$('#IPOLSDEK_timeFrom').closest('tr').css('display','none');
							break;
			case 'pickup' : $('#IPOLSDEK_wndOrder').find('.IPOLSDEK_SV').css('display',''); break;
		}

		<?if($allowCourier){?>
		if(IPOLSDEK_oExport.isToDoor(wat.val())){
			$('#IPOLSDEK_courierHeader').css('display','');
			if(IPOLSDEK_oExport.courier.request)
				IPOLSDEK_oExport.courier.handle();		
		}else{
			$('#IPOLSDEK_courierHeader').css('display','none');
			if(IPOLSDEK_oExport.courier.request && typeof(ifDef) == 'undefined')
				IPOLSDEK_oExport.courier.handle();
		}
		<?}else{?>
		$('#IPOLSDEK_courierHeader').css('display','none');
		<?}?>
		
		if(typeof(ifDef) == 'undefined')
			IPOLSDEK_oExport.onRecheck();
		else
			IPOLSDEK_oExport.onRecheck(true);
		IPOLSDEK_oExport.onPVZChange();
	},
		// Изменился город назначения (если в списке ошибочных)
	onMSChange: function(wat){
		$('#IPOLSDEK_location').val(wat.val());
		IPOLSDEK_oExport.onRecheck();
	},
		// Изменился ПВЗ - надо проверить его доступность
	onPVZChange: function(wat){
		if(typeof(wat) == 'undefined')
			wat = $('#IPOLSDEK_PVZ');
		if(typeof(IPOLSDEK_oExport.badPVZ[wat.val()]) != 'undefined')
			$('#IPOLSDEK_oExport.badPVZ').css('display','inline');
		else
			$('#IPOLSDEK_oExport.badPVZ').css('display','none');
	},
		// Изменился город-отправитель: пересчитываем все.
	onDepartureChange: function(){
		IPOLSDEK_oExport.onRecheck();
	},
		// Изменение условий - проверка стоимости / сроков
	onRecheck: function(isNoAlert){
		var reqParams = IPOLSDEK_oExport.getInputsRecheck();

		if(typeof(reqParams) != 'object' || typeof(reqParams.cityTo) == 'undefined'){
			alert(reqParams);
			return false;
		}

		IPOLSDEK_oExport.ajax({
			data     : reqParams,
			dataType : 'json',
			success  : function(data){
				if(typeof data.success != 'undefined'){
					var dayLbl = data.termMin + "-" + data.termMax + " <?=GetMessage("IPOLSDEK_JS_SOD_HD_DAY")?>";
					if(data.termMin == data.termMax) dayLbl = data.termMax + " <?=GetMessage("IPOLSDEK_JS_SOD_HD_DAY")?>";
					var text = "<?=GetMessage("IPOLSDEK_JSC_SOD_NEWCONDITIONS_1")?>" + dayLbl;
					if(typeof(data.price) != 'undefined')
						text+="<?=GetMessage("IPOLSDEK_JSC_SOD_NEWCONDITIONS_2")?>" + data.price;
					if(typeof(data.sourcePrice) != 'undefined')
						text+="\n\n<?=GetMessage("IPOLSDEK_JSC_SOD_PriceInLK")?>"+data.sourcePrice;
					$('#IPOLSDEK_newPrDel').html(data.price);
				}else{
					var text = '';
					for(var i in data)
						text += data[i]+" ("+i+") \n";
					$('#IPOLSDEK_newPrDel').html('<?=GetMessage("IPOLSDEK_JS_SOD_noDost")?>');
				}
				if(typeof(isNoAlert) == 'undefined')
					alert(text);
			}
		});
	},

	// Данные для отправки / проверки
	getInputsRecheck: function(params){
		// var city = $('#IPOLSDEK_location').val();
		var city = $('#IPOLSDEK_cityTo').val();
		if(!city)
			return '<?=GetMessage("IPOLSDEK_JSC_SOD_NOCITY")?>';

		var tarif = $('#IPOLSDEK_service').val();
		if(!tarif)
			return '<?=GetMessage("IPOLSDEK_JSC_SOD_NOTARIF")?>';

		var GABS = {
			'D_L' : $('#IPOLSDEK_GABS_D_L').val(),
			'D_W' : $('#IPOLSDEK_GABS_D_W').val(),
			'D_H' : $('#IPOLSDEK_GABS_D_H').val(),
			'W'   : $('#IPOLSDEK_GABS_W').val(),
		};
		var packs = $('#IPOLSDEK_PLACES').val();
		if(packs)
			packs = JSON.parse(packs);

		if(typeof(params) == 'undefined')
			params = {};

		var cityFrom = (params.cityFrom) ? params.cityFrom : false;
		if(!cityFrom){
			if(IPOLSDEK_oExport.courier.request)
				cityFrom = $('#IPOLSDEK_courierCity').val();
			if(!cityFrom)
				cityFrom = $('#IPOLSDEK_departure').val();
		}

		return {
			isdek_action : 'extCountDeliv',
			orderId  : (params.orderId) ? params.orderId : IPOLSDEK_oExport.orderId,
			mode     : (params.mode) ? params.mode : IPOLSDEK_oExport.mode,
			shipment : (params.shipment) ? params.shipment : IPOLSDEK_oExport.shipment,
			cityTo   : city,
			cityFrom : cityFrom,
			tarif    : (params.tarif) ? params.tarif : tarif,
			GABS     : (params.GABS) ? params.GABS : GABS,
			packs    : (params.packs) ? params.packs : packs,		
		};
	},

	getInputs: function(){
		var dO={};

		var profile = IPOLSDEK_oExport.defineTarifs($('#IPOLSDEK_service').val());
		var isCourierCall = (IPOLSDEK_oExport.isToDoor($('#IPOLSDEK_service').val()) && IPOLSDEK_oExport.courier.request);

		if($('#IPOLSDEK_isBeznal').attr('checked'))
			dO['isBeznal']='Y';

		var reqFields = {
			'service'   	 : {need: true},
			'realSeller'	 : {need: false},
			'departure'		 : {need: true,check: ($('#IPOLSDEK_departure').length && !isCourierCall)},
			'location'  	 : {need: true},
			'name'     		 : {need: true},
			'email'     	 : {need: false},
			'phone'     	 : {need: true},
			'comment'    	 : {need: false},
			'NDSGoods'    	 : {need: false},
			'NDSDelivery'    : {need: false},
			'toPay'			 : {need: true, check: (typeof(dO['isBeznal']) == 'undefined' || dO['isBeznal'] != 'Y')},
			'deliveryP'		 : {need: true, check: (typeof(dO['isBeznal']) == 'undefined' && dO['isBeznal'] != 'Y')},
			'street'      	 : {need: true,check: (profile == 'courier')},
			'house'      	 : {need: true,check: (profile == 'courier')},
			'flat'       	 : {need: true,check: (profile == 'courier')},
			'PVZ'            : {need: true,check: (profile == 'pickup')},
			'courierDate'    : {need: true,check: isCourierCall},
			'courierTimeBeg' : {need: true,check: isCourierCall},
			'courierTimeEnd' : {need: true,check: isCourierCall},
			'courierCity' 	 : {need: true,check: isCourierCall},
			'courierStreet'  : {need: true,check: isCourierCall},
			'courierHouse' 	 : {need: true,check: isCourierCall},
			'courierFlat' 	 : {need: true,check: isCourierCall},
			'courierPhone' 	 : {need: true,check: isCourierCall},
			'courierName'	 : {need: true,check: isCourierCall},
			'courierComment' : {need: false,check: isCourierCall},
		};

		for(var i in reqFields){
			if(typeof(reqFields[i].need) == 'undefined') continue;
			if(typeof(reqFields[i].check) != 'undefined' && !reqFields[i].check) continue;
			dO[i]=$('#IPOLSDEK_'+i).val();
			if(!dO[i] && reqFields[i].need)
				return $('#IPOLSDEK_'+i).closest('tr').children('td').html();
		}

		dO['AS'] = {};
		$('[id^="IPOLSDEK_AS_"]').each(function(){
			if($(this).attr('checked'))
				dO['AS'][$(this).val()]='Y';
		});

		var packs = $('#IPOLSDEK_PLACES').val();
		if(packs){
			packs = JSON.parse(packs);
			dO['packs'] = packs;
		}

		$('[id^="IPOLSDEK_GABS_"]').each(function(){
			if(typeof dO['GABS'] == 'undefined') dO['GABS'] = {};
			dO['GABS'][$(this).attr('id').substr(14)]=$(this).val();
		});

		if($("#IPOLSDEK_currency").val())
			dO['currency'] = $('#IPOLSDEK_currency').val();

		return dO;
	},

	// кнопки
		// Сохранение и отправка
	send: function(){
		var dataObject=IPOLSDEK_oExport.getInputs();
		if(typeof dataObject != 'object'){if(dataObject)alert('<?=GetMessage('IPOLSDEK_JSC_SOD_ZAPOLNI')?> "'+dataObject+'"');return;}
		dataObject['isdek_action'] = 'saveAndSend';
		dataObject['orderId']  = IPOLSDEK_oExport.orderId;
		dataObject['mode']     = IPOLSDEK_oExport.mode;
		dataObject['shipment'] = IPOLSDEK_oExport.shipment;
		$('[onclick^="IPOLSDEK_oExport.send("]').each(function(){$(this).css('display','none')});
		IPOLSDEK_oExport.ajax({
			data    : dataObject,
			success : function(data){
				alert(data);
				IPOLSDEK_oExport.wnd.Close();
			}
		});
	},
		// Удаление
	delete: function(){
		var oId = (IPOLSDEK_oExport.mode == 'shipment') ? IPOLSDEK_oExport.shipment : IPOLSDEK_oExport.orderId;
		if(IPOLSDEK_oExport.status == 'NEW' || IPOLSDEK_oExport.status == 'ERROR' || IPOLSDEK_oExport.status == 'DELETE'){
			if(confirm("<?=GetMessage('IPOLSDEK_JSC_SOD_IFDELETE')?>"))
				IPOLSDEK_oExport.ajax({
					data    : {isdek_action:'delReqOD',oid:oId,mode:IPOLSDEK_oExport.mode},
					success : function(data){
						alert(data);
						document.location.reload();
					}
				});
		}else{
			if(IPOLSDEK_oExport.status == 'OK'){
				if(confirm("<?=GetMessage('IPOLSDEK_JSC_SOD_IFKILL')?>"))
					IPOLSDEK_oExport.ajax({
						data    : {isdek_action:'killReqOD',oid:oId,mode:IPOLSDEK_oExport.mode},
						success : function(data){
							if(data.indexOf('GD:')===0){
								alert(data.substr(3));
								document.location.reload();
							}
							else
								alert(data);
						}
					});
			}
		}
	},
		// печать
	print: function(){
		$('#IPOLSDEK_PRINT').attr('disabled','true');
		$('#IPOLSDEK_PRINT').val('<?=GetMessage("IPOLSDEK_JSC_SOD_LOADING")?>');
		IPOLSDEK_oExport.ajax({
			data    : {
				isdek_action : 'printOrderInvoice',
				oId : IPOLSDEK_oExport.orderId
			},
			dataType : 'json',
			success : function(data){
				$('#IPOLSDEK_PRINT').removeAttr('disabled');
				$('#IPOLSDEK_PRINT').val('<?=GetMessage("IPOLSDEK_JSC_SOD_PRNTSH")?>');
				if(data.result == 'ok'){
					for(var i in data.files)
						window.open('/upload/<?=self::$MODULE_ID?>/'+data.files[i]);
				}else
					alert(data.error);
			}
		});
	},

	// служебные
		// тариф: ПВЗ, Почтомат или курьер
	defineTarifs: function(val){
		val = parseInt(val);

		var arPVZ = [<?=sdekHelper::getTarifList(array('type'=>'pickup','answer'=>'string','fSkipCheckBlocks'=>true))?>];

		if(arPVZ.indexOf(val) !== -1)
			return 'pickup';
		return 'courier';
	},
		// тариф: до двери или склада
	isToDoor: function(val){
		var dT = [<?=sdekHelper::getDoorTarifs(true)?>];
		for(var i = 0; i < dT.length; i++) 
			if(dT[i] == val) return true;
		return false;
	},
		// проверка на оплачен / не оплачен при безнале
	checkPay: function(){
		if($('#IPOLSDEK_isBeznal').attr('checked')){
			<?if($badPay){?>$('#IPOLSDEK_notPayed').css('display','inline');<?}?>
			$('#IPOLSDEK_toPay').attr('disabled','disabled');
			$('#IPOLSDEK_deliveryP').attr('disabled','disabled');
			$('#IPOLSDEK_NDSGoods').attr('disabled','disabled');
			$('#IPOLSDEK_NDSDelivery').attr('disabled','disabled');
			$('#IPOLSDEK_toPay').val('0');
			$('#IPOLSDEK_deliveryP').val('0');
		}else{
			<?if($badPay){?>$('#IPOLSDEK_notPayed').css('display','none');<?}?>
			$('#IPOLSDEK_toPay').removeAttr('disabled');
			$('#IPOLSDEK_deliveryP').removeAttr('disabled');
			$('#IPOLSDEK_NDSGoods').removeAttr('disabled');
			$('#IPOLSDEK_NDSDelivery').removeAttr('disabled');
			$('#IPOLSDEK_toPay').val(IPOLSDEK_oExport.goodsPrice);
			$('#IPOLSDEK_deliveryP').val(IPOLSDEK_oExport.delivPrice);
		}
	},
		// управление сервисными свойствами
	serverShow: function(){
		$(".IPOLSDEK_detOrder").css("display","");
		IPOLSDEK_oExport.gabs.label();
	},
		// всплывающие подсказки
	popup: function (code, info){
		var offset = $(info).position().top;
		var obj;
		if(code == 'next') 	obj = $(info).next();
		else  				obj = $('#'+code);

		var LEFT = (parseInt($('#IPOLSDEK_wndOrder').width())-parseInt(obj.width()))/2;
		obj.css({
			top: (offset+15)+'px',
			left: LEFT,
			display: 'block'
		});	
		return false;
	},

	checkFloat: function(wat){
		var val = parseFloat(wat.val().replace(',','.'));
		wat.val((isNaN(val)) ? 0 : val);
	},

	// Дополнительные окна и функционал
		// Все тарифы
	allTarifs: {
		lang: {<?foreach(sdekExport::getAllProfiles() as $profile){?>
			<?=$profile?> : '<?=GetMessage("IPOLSDEK_DELIV_".strtoupper($profile)."_TITLE")?>',
		<?}?>},

		wnd: false,

		countData: false,

		availTarifs : false,
		tarifDescr  : false,

		curMode: false,

		show: function(){
			var wndContent = "<table id='IPOLSDEK_allTarifs'></table><div id='IPOLSDEK_allTarAjax' style='text-align:center;border:none;padding-top: 10px;'><img src='/bitrix/images/<?=self::$MODULE_ID?>/ajax.gif'></div>";

			$('#IPOLSDEK_allTarifsBtn').attr('disabled','disabled');

			if(!IPOLSDEK_oExport.allTarifs.wnd){
				IPOLSDEK_oExport.allTarifs.wnd = new BX.CDialog({
					title: "<?=GetMessage('IPOLSDEK_JSC_SOD_ALLTARIFS')?>",
					content: wndContent,
					icon: 'head-block',
					resizable: true,
					draggable: true,
					height: '300',
					width: '550',
					buttons: []
				});
			}else
				$('#IPOLSDEK_allTarifs').parent().html(wndContent);

			var packs = $('#IPOLSDEK_PLACES').val();
			if(packs)
				packs = JSON.parse(packs);

			IPOLSDEK_oExport.allTarifs.countData = {
				isdek_action : 'htmlTaritfList',
				orderId  : IPOLSDEK_oExport.orderId,
				mode     : IPOLSDEK_oExport.mode,
				shipment : IPOLSDEK_oExport.shipment,
				cityTo   : $('#IPOLSDEK_cityTo').val(),
				cityFrom : (IPOLSDEK_oExport.courier.request) ? $('#IPOLSDEK_courierCity').val() : 0,
				GABS	 : {
					'D_L' : $('#IPOLSDEK_GABS_D_L').val(),
					'D_W' : $('#IPOLSDEK_GABS_D_W').val(),
					'D_H' : $('#IPOLSDEK_GABS_D_H').val(),
					'W'   : $('#IPOLSDEK_GABS_W').val(),
				},
				packs: packs,
			};

			IPOLSDEK_oExport.ajax({
				data     : {'isdek_action':'getAllTarifsToCount'},
				dataType : 'json',
				success  :function(data){
					$('#IPOLSDEK_allTarifsBtn').removeAttr('disabled');
					if(IPOLSDEK_oExport.isEmpty(data))
						alert('<?=GetMessage('IPOLSDEK_JSC_SOD_noTarifs')?>');
					else{
						IPOLSDEK_oExport.allTarifs.availTarifs = data;
						IPOLSDEK_oExport.allTarifs.tarifDescr  = {};
						for(var i in IPOLSDEK_oExport.allTarifs.availTarifs)
							for(var j in IPOLSDEK_oExport.allTarifs.availTarifs[i])
								IPOLSDEK_oExport.allTarifs.tarifDescr[j] = IPOLSDEK_oExport.allTarifs.availTarifs[i][j];
						IPOLSDEK_oExport.allTarifs.wnd.Show();
						IPOLSDEK_oExport.allTarifs.closer();
						IPOLSDEK_oExport.allTarifs.carnage(true);
					}
				},
			});
		},

		carnage: function(isStart){
			if(typeof(isStart) == 'undefined' && !IPOLSDEK_oExport.allTarifs.curMode)
				return;

			if(!IPOLSDEK_oExport.allTarifs.curMode){
				IPOLSDEK_oExport.allTarifs.curMode = IPOLSDEK_oExport.allTarifs.getFirstTafirType();
				$('#IPOLSDEK_allTarifs').append("<tr><td colspan='4' style='text-align:center;font-weight:bold;'>"+IPOLSDEK_oExport.allTarifs.lang[IPOLSDEK_oExport.allTarifs.curMode]+"</td></tr>");
			}

			if(IPOLSDEK_oExport.isEmpty(IPOLSDEK_oExport.allTarifs.availTarifs[IPOLSDEK_oExport.allTarifs.curMode])){
				delete(IPOLSDEK_oExport.allTarifs.availTarifs[IPOLSDEK_oExport.allTarifs.curMode]);
				IPOLSDEK_oExport.allTarifs.curMode = IPOLSDEK_oExport.allTarifs.getFirstTafirType();
				if(!IPOLSDEK_oExport.allTarifs.curMode){
					$('#IPOLSDEK_allTarAjax').css('display','none');
					IPOLSDEK_oExport.allTarifs.closer(true);
				}else
					$('#IPOLSDEK_allTarifs').append("<tr><td colspan='4' style='text-align:center;font-weight:bold;'>"+IPOLSDEK_oExport.allTarifs.lang[IPOLSDEK_oExport.allTarifs.curMode]+"</td></tr>");
			}

			if(IPOLSDEK_oExport.allTarifs.curMode){
				var curTarif = false;
				for(var i in IPOLSDEK_oExport.allTarifs.availTarifs[IPOLSDEK_oExport.allTarifs.curMode]){
					curTarif = i;
					delete(IPOLSDEK_oExport.allTarifs.availTarifs[IPOLSDEK_oExport.allTarifs.curMode][i]);
					var reqParams = IPOLSDEK_oExport.getInputsRecheck();
						reqParams.tarif = curTarif;
					IPOLSDEK_oExport.ajax({
						data: reqParams,
						dataType: 'json',
						success: function(data){console.log(data);
							if(data.success){
								var curPrice = '';
								if(typeof(data.price) != 'undefined'){
									curPrice = data.price;
									if(typeof(data.sourcePrice) != 'undefined')
										curPrice += '<a href="#" class="PropWarning" onclick="return false;" title="<?=GetMessage('IPOLSDEK_JSC_SOD_PriceInLK')?> '+data.sourcePrice+'">';
								}else
									if(typeof(data.sourcePrice) != 'undefined')
										curPrice = data.sourcePrice+' <a href="#" class="PropWarning" onclick="return false;" title="<?=GetMessage('IPOLSDEK_JSC_SOD_PriceONLYInLK')?>">';
								$('#IPOLSDEK_allTarifs').append("<tr id='IPOLSDEK_tarifsTable_"+data.tarif+"'><td>"+IPOLSDEK_oExport.allTarifs.tarifDescr[data.tarif]+"</td><td style='text-align:center;'>"+curPrice+"</td><td style='text-align:center;'>"+((data['termMin'] == data['termMax'])?data['termMin']:data['termMin']+" - "+data['termMax'])+" <?=GetMessage('IPOLSDEK_JS_SOD_HD_DAY')?></td><td><input type='button' value='<?=GetMessage('IPOLSDEK_FRNT_CHOOSE')?>' onclick='IPOLSDEK_oExport.allTarifs.select(\""+data.tarif+"\");'></td></tr>");
							}
							IPOLSDEK_oExport.allTarifs.carnage();
						}
					});
					break;
				}
			}

		},

		getFirstTafirType: function(){
			for(var i in IPOLSDEK_oExport.allTarifs.availTarifs)
				return i;
			return false;
		},

		select: function(wat){
			if(!$('#IPOLSDEK_service option[value="'+wat+'"]').length)
				$('#IPOLSDEK_service').append('<option value="'+wat+'">'+$('#IPOLSDEK_tarifsTable_'+wat).children(':first').html()+'</option>');
			$('#IPOLSDEK_service').val(wat);
			IPOLSDEK_oExport.onCodeChange($('#IPOLSDEK_service'),true);
			IPOLSDEK_oExport.allTarifs.wnd.Close();
		},

		closer: function(doShow){
			var handler = $('#IPOLSDEK_allTarifs').closest('.bx-core-adm-dialog').find('.bx-core-adm-icon-close');
			if(typeof(doShow) == 'undefined')
				handler.css('visibility','hidden');
			else
				handler.css('visibility','visible');
		}
	},
		// оформление курьера
	courier: {
		request: <?if(!$allowCourier) echo 'true';
						elseif($ordrVals['courierDate']) echo 'false';
						else echo 'true';
					?>, // проверка в php! Обратное, так как при загрузке работает

		handle: function(){
			if(IPOLSDEK_oExport.courier.request){
				$("[onclick='IPOLSDEK_oExport.courier.handle()']").html('<?=GetMessage('IPOLSDEK_JS_SOD_HD_SHOWCOURIER')?>');
				$('.IPOLSDEK_courierInfo').css('display','none');
				IPOLSDEK_oExport.courier.request = false;
				$('#IPOLSDEK_departure').removeAttr('disabled');
			}else{
				<?if(COption::GetOptionString(self::$MODULE_ID,'allowSenders','N') == 'N'){?> return; <?}?>
				if($('#IPOLSDEK_courierHeader').css('display')!='none'){
					$("[onclick='IPOLSDEK_oExport.courier.handle()']").html('<?=GetMessage('IPOLSDEK_JS_SOD_HD_NOSHOWCOURIER')?>');
					$('.IPOLSDEK_courierInfo').css('display','');
					IPOLSDEK_oExport.courier.request = true;
					$('#IPOLSDEK_departure').attr('disabled','disabled');
				}
			}
		},

		changeCity: function(mode,val){
			if(mode == 1){
				$('#IPOLSDEK_cSSelector').parent().css('display','block');
				$('#IPOLSDEK_cSLabel').parent().css('display','none');
				$('#IPOLSDEK_cSSelector').val('');
			}else{
				$('#IPOLSDEK_cSLabel').parent().css('display','block');
				$('#IPOLSDEK_cSSelector').parent().css('display','none');
				$('#IPOLSDEK_cSLabel').html(val.item.label);
				$('#IPOLSDEK_courierCity').val(val.item.value);
				IPOLSDEK_oExport.onRecheck();
			}		
		},

		svdCrrs: {<?=$IPOLSDEK_svdC?>},

		selectProfile: function(val){
			var Vals = '';
			if(val === '')
				Vals = {senderName:"",cityName:"",courierCity:'',courierStreet:'',courierHouse:'',courierFlat:'',courierPhone:'',courierName:''};
			else
				Vals = IPOLSDEK_oExport.courier.svdCrrs[val];
			for(var i in Vals)
				$('#IPOLSDEK_'+i).val(Vals[i]);
			$('#IPOLSDEK_cSLabel').html(Vals.cityName);
			IPOLSDEK_oExport.courier.onTimeChange();
			IPOLSDEK_oExport.onRecheck();
		},

		onDateChange: function(){
			var curDate = new Date('<?=date('Y')?>','<?=(date('m')-1)?>','<?=date('d')?>');
			var selDate = $('#IPOLSDEK_courierDate').val().split('.');
			selDate = new Date(selDate[2],selDate[1]-1,selDate[0]);

			if(selDate < curDate)	
				$('#IPOLSDEK_courierDateError').css('display','table-cell');
			else
				$('#IPOLSDEK_courierDateError').css('display','none');
			if(selDate.valueOf() == curDate.valueOf() && !$('#IPOLSDEK_courierTimeBeg').val()){
				var cT = new Date();
				var aT = new Date(cT.getTime() + 900000); // +15 min
				if(aT.getHours() < 15){
					$('#IPOLSDEK_courierTimeBeg').val(aT.getHours()+":"+aT.getMinutes());
					aT = new Date(cT.getTime() + 11700000); // +3h 15m
					$('#IPOLSDEK_courierTimeEnd').val(aT.getHours()+":"+aT.getMinutes());
				}
			}

			IPOLSDEK_oExport.courier.onTimeChange();
		},

		onTimeChange: function(){
			var start = $('#IPOLSDEK_courierTimeBeg').val();
			var end = $('#IPOLSDEK_courierTimeEnd').val();
			if(start || end){
				var check = IPOLSDEK_oExport.courier.timeCheck(start,end,$('#IPOLSDEK_courierDate').val());
				if(check === true){
					$('.IPOLSDEK_badInput').removeClass('IPOLSDEK_badInput');
					$('#IPOLSDEK_courierTimeOK').val(true);
					$('#IPOLSDEK_courierTimeError').html('');
				}else{
					if(check.error == 'start' || check.error == 'both')
						$('#IPOLSDEK_courierTimeBeg').addClass('IPOLSDEK_badInput');
					if(check.error == 'end' || check.error == 'both')
						$('#IPOLSDEK_courierTimeEnd').addClass('IPOLSDEK_badInput');
					$('#IPOLSDEK_courierTimeOK').val(false);
					$('#IPOLSDEK_courierTimeError').html(check.text);
				}
			}else{
				$('.IPOLSDEK_badInput').removeClass('IPOLSDEK_badInput');
				$('#IPOLSDEK_courierTimeOK').val(true);
				$('#IPOLSDEK_courierTimeError').html('');
			}
		},

		timeCheck: function(start,end,day){
			if(!start)
				return {
					'error' : 'start',
					'text'  : '<?=GetMessage('IPOLSDEK_JS_TIME_fillStart')?>',	
				}
			if(!end)
				return {
					'error' : 'end',
					'text'  : '<?=GetMessage('IPOLSDEK_JS_TIME_fillEnd')?>',
				}
			start = start.split(':');
			start[0] = parseInt(start[0]);
			start[1] = parseInt(start[1]);
			if(start[0] < 9)
				return {
					'error' : 'start',
					'text'  : '<?=GetMessage('IPOLSDEK_JS_TIME_badStart')?>',
				}
			end   = end.split(':');
			end[0] = parseInt(end[0]);
			end[1] = parseInt(end[1]);
			if(end[0] > 18 || (end[0] == 18 && end[1]))
				return {
					'error' : 'end',
					'text'  : '<?=GetMessage('IPOLSDEK_JS_TIME_badEnd')?>',
				}
			if((end[0] - start[0]) * 60 + end[1] - start[1] < 180)
				return {
					'error' : 'both',
					'text'  : '<?=GetMessage('IPOLSDEK_JS_TIME_badBoth')?>',
				}
			if(typeof(day) != 'undefined' && day == '<?=date('d.m.Y')?>' && start[0] > 14)
				return {
					'error' : 'start',
					'text'  : '<?=GetMessage('IPOLSDEK_JS_TIME_bad15')?>',
				}
			return true;
		}
	},
		// Управление габаритами упаковки
	gabs:{
			//кнопка "Изменить"
		change: function(){
			var GABS = {
				D_L: $('#IPOLSDEK_GABS_D_L').val() * 10, // СДЭК-овские - в см
				D_W: $('#IPOLSDEK_GABS_D_W').val() * 10,
				D_H: $('#IPOLSDEK_GABS_D_H').val() * 10
			};
			var htmlCG  = "<input type='text' class='IPOLSDEK_gabInput' id='IPOLSDEK_GABS_D_L_new' value='"+GABS.D_L+"'> <?=GetMessage("IPOLSDEK_mm")?>&nbsp;x&nbsp;";
				htmlCG += "<input type='text' class='IPOLSDEK_gabInput' id='IPOLSDEK_GABS_D_W_new' value='"+GABS.D_W+"'> <?=GetMessage("IPOLSDEK_mm")?>&nbsp;x&nbsp;";
				htmlCG += "<input type='text' class='IPOLSDEK_gabInput' id='IPOLSDEK_GABS_D_H_new' value='"+GABS.D_H+"'> <?=GetMessage("IPOLSDEK_mm")?>,";
				htmlCG += "<input type='text' style='width:20px' id='IPOLSDEK_GABS_W_new' value='"+$('#IPOLSDEK_GABS_W').val()+"'> <?=GetMessage("IPOLSDEK_kg")?>";
				htmlCG += " <a href='javascript:void(0)' onclick='IPOLSDEK_oExport.gabs.accept()'>OK</a>";
			$('#IPOLSDEK_natGabs').css('display','none');
			$('#IPOLSDEK_gabsPlace').parents('tr').css('display','table-row');
			$('#IPOLSDEK_gabsPlace').html(htmlCG);
		},
		// принятие изменений в кнопке "Изменить"
		accept: function(){
			var ar = ['D_L','D_W','D_H','W'];
			var GABS = {'mode':'mm'};
			for(var i in ar){
				IPOLSDEK_oExport.checkFloat($('#IPOLSDEK_GABS_'+ar[i]+'_new'));
				GABS[ar[i]] = $('#IPOLSDEK_GABS_'+ar[i]+'_new').val();
			}

			IPOLSDEK_oExport.gabs.write(GABS);

			IPOLSDEK_oExport.onRecheck();
		},
		// установка изменений согласно GABS
		write: function(GABS){
			if(GABS.mode == 'mm'){
				var GABSmm = GABS;
				var GABScm = {
					'D_L'  : GABS.D_L / 10,
					'D_W'  : GABS.D_W / 10,
					'D_H'  : GABS.D_H / 10
				}
			}else{
				var GABSmm =  {
					'D_L'  : GABS.D_L * 10,
					'D_W'  : GABS.D_W * 10,
					'D_H'  : GABS.D_H * 10
				};
				var GABScm = GABS;
			}

			var htmlCG  = GABSmm.D_L + " <?=GetMessage("IPOLSDEK_mm")?> x " + GABSmm.D_W + " <?=GetMessage("IPOLSDEK_mm")?> x " + GABSmm.D_H + " <?=GetMessage("IPOLSDEK_mm")?>, " + GABS.W + " <?=GetMessage("IPOLSDEK_kg")?> <a href='javascript:void(0)' onclick='IPOLSDEK_oExport.gabs.change()'> <?=GetMessage('IPOLSDEK_STT_CHNG')?></a>";
			$('#IPOLSDEK_gabsPlace').html(htmlCG);
			$('#IPOLSDEK_GABS_D_L').val(GABScm.D_L);
			$('#IPOLSDEK_GABS_D_W').val(GABScm.D_W);
			$('#IPOLSDEK_GABS_D_H').val(GABScm.D_H);
			$('#IPOLSDEK_GABS_W').val(GABS.W);
			$('#IPOLSDEK_gabsPlace').parents('tr').css('display','table-row');
			$('#IPOLSDEK_VWeightPlace').html((GABScm.D_L*GABScm.D_W*GABScm.D_H) / 5000);
			IPOLSDEK_oExport.gabs.changeStat = true;
			IPOLSDEK_oExport.serverShow();
		},
		// окончание работы управления упаковками
		onPackHandlerEnd: function(){
			$('#IPOLSDEK_PLACES').val('');
			if(IPOLSDEK_packs.saveObj.cnt == 1){
				var gabs = [1,1,1,1];
				for(var i in IPOLSDEK_packs.saveObj)
					if(!isNaN(parseInt(i))){
						gabs = IPOLSDEK_packs.saveObj[i].gabs.split(' x ');
						gabs.push(IPOLSDEK_packs.saveObj[i].weight);
						continue;
					}

				IPOLSDEK_oExport.gabs.write({
					'D_L'  : gabs[0],
					'D_W'  : gabs[1],
					'D_H'  : gabs[2],
					'W'    : gabs[3],
					'mode' : 'cm'
				});
			}else{
				if(IPOLSDEK_packs.saveObj){
					delete IPOLSDEK_packs.saveObj.cnt;
					$('#IPOLSDEK_PLACES').val(JSON.stringify(IPOLSDEK_packs.saveObj));
				}
				IPOLSDEK_oExport.serverShow();
				IPOLSDEK_oExport.onRecheck();
			}
		},
		// проверяет, что именно показывать при открытии и редактировании
		changeStat: <?=(sdekHelper::isEqualArrs($naturalGabs,$ordrVals['GABS']) ? "false" : "true")?>,
		label: function(){
			// заданы упаковки
			if($('#IPOLSDEK_PLACES').val()){
				$('#IPOLSDEK_gabsPlace').closest('tr').css('display','none');
				$('#IPOLSDEK_natGabs').css('display','none');
				$('#IPOLSDEK_PLACES').closest('tr').css('display','');
			}else{
				if(IPOLSDEK_oExport.gabs.changeStat){
					$('#IPOLSDEK_gabsPlace').closest('tr').css('display','table-row');
					$('#IPOLSDEK_natGabs').css('display','none');
					$('#IPOLSDEK_PLACES').closest('tr').css('display','none');
				}else{
					$('#IPOLSDEK_gabsPlace').closest('tr').css('display','none');
					$('#IPOLSDEK_natGabs').css('display','inline');
					$('#IPOLSDEK_PLACES').closest('tr').css('display','none');				
				}
			}
		}
	},
	<?if($senderWH){?>
	// города-отправители
		senderWH: {
			wnd: false,
			show: function(){
				if(!IPOLSDEK_oExport.senderWH.wnd){
					IPOLSDEK_oExport.senderWH.wnd = new BX.CDialog({
						title: "<?=GetMessage('IPOLSDEK_JS_SOD_senderWH_HEADER')?>",
						content: "<div id='IPOLSDEK_senderWH_table'></div>",
						icon: 'head-block',
						resizable: true,
						draggable: true,
						height: '300',
						width: '450',
						buttons: []
					});
					$('#IPOLSDEK_senderWH_table').html($('#IPOLSDEK_senderWHcontent').html());
					$('#IPOLSDEK_senderWHcontent').html('');
				}
				IPOLSDEK_oExport.senderWH.wnd.Show();
			},
		},
	<?}?>
	// валюты
	currency:{
		goal: '<?=$cntrCurrency?>',

		getFormat: function(sum,from,to,where){
			IPOLSDEK_oExport.ajax({
				data    : {isdek_action:'formatCurrency',SUM:sum,FROM:from,TO:to,WHERE:where,FORMAT:'Y',orderId:IPOLSDEK_oExport.orderId},
				dataType: 'JSON',
				success : function(data){
					$('#'+data.WHERE).html(data.VALUE);
				}
			});
		},

		init: function(){
			$('#IPOLSDEK_toPay').on('change',IPOLSDEK_oExport.currency.onChange);
			$('#IPOLSDEK_deliveryP').on('change',IPOLSDEK_oExport.currency.onChange);
		},

		onChange: function(e){
			var val = $(e.currentTarget).val();
			var id  = $(e.currentTarget).attr('id') + 'Format';
			$('#'+id).html('');
			IPOLSDEK_oExport.currency.getFormat(val,0,IPOLSDEK_oExport.currency.goal,id);
		}
	}
};

$(document).ready(IPOLSDEK_oExport.load);
</script>
<div style='display:none'>
	<table id='IPOLSDEK_wndOrder'>
		<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_STATUS')?></td><td><?=$status?></td></tr>
		<tr><td colspan='2'><small><?=GetMessage('IPOLSDEK_JS_SOD_STAT_'.$status)?></small><?=$message['number']?></td></tr>
		<?if($SDEK_ID){?><tr><td><?=GetMessage('IPOLSDEK_JS_SOD_SDEK_ID')?></td><td><?=$SDEK_ID?></td></tr><?}?>
		<?if($MESS_ID){?><tr><td><?=GetMessage('IPOLSDEK_JS_SOD_MESS_ID')?></td><td><?=$MESS_ID?></td></tr><?}?>
		<?if($senderWH){?><tr><td colspan='2'><a href='javascript:void(0)' onclick='IPOLSDEK_oExport.senderWH.show()'><?=GetMessage('IPOLSDEK_JS_SOD_senderWH_TITLE')?></a></td></tr>
		<?}?>
	<?//Заявка?>
		<tr class='heading'><td colspan='2'><?=GetMessage('IPOLSDEK_JS_SOD_HD_PARAMS')?></td></tr>
		<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_number')?></td><td><?=(self::$orderDescr['info']['ACCOUNT_NUMBER'])?self::$orderDescr['info']['ACCOUNT_NUMBER']:self::$orderId?></td></tr>
		<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_service')?></td><td>
			<select id='IPOLSDEK_service' onchange='IPOLSDEK_oExport.onCodeChange($(this))'><?=$strOfCodes?></select>
			<?=$message['service']?>
		</td></tr>
		<tr id='IPOLSDEK_tarifWarning'><td colspan='2'><span><?=GetMessage('IPOLSDEK_JS_SOD_WRONGTARIF')?></span></td></tr>
		<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_realSeller')?> <a href='#' class='PropHint' onclick='return IPOLSDEK_oExport.popup("pop-realSeller",this);'></a></td><td><input type='text' id='IPOLSDEK_realSeller' value='<?=$ordrVals['realSeller']?>'></td></tr>
		<?// Города-отправители?>
		<?if($citySenders || (self::$isLoaded && array_key_exists('departure',$ordrVals))){?>
		<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_departure')?></td><td>
			<?if(self::$isLoaded && array_key_exists('departure',$ordrVals) && !$citySenders[$ordrVals['departure']]){?>
				<span style='color:red'><?=GetMessage('IPOLSDEK_ERR_SENDERCITYNOTFOUND');?></span><br>
			<?}
			if($citySenders){?>
				<select id='IPOLSDEK_departure' onchange='IPOLSDEK_oExport.onDepartureChange($(this))'>
					<?foreach($citySenders as $id => $name){?>
						<option value="<?=$id?>" <?=(array_key_exists('departure',$ordrVals) && $ordrVals['departure'] == $id)?'selected':''?>><?=$name?></option>
					<?}?>
				</select>
			<?}?>
		</td></tr>
		<?}?>
	<?//Ошибки?>
		<?if(count($message['troubles'])){?>
			<tr class='heading'><td colspan='2'><?=GetMessage('IPOLSDEK_JS_SOD_HD_ERRORS')?></td></tr>
			<tr><td colspan='2'><?=$message['troubles']?></td></tr>
		<?}?>
	<?//Отправитель?>
		<tr id='IPOLSDEK_courierHeader'><td colspan='2' style='text-align:center;border-top: 1px dashed black'><a href='javascript:void(0)' onclick='IPOLSDEK_oExport.courier.handle()'></a>&nbsp;<a class='PropHint' onclick="return IPOLSDEK_oExport.popup('pop-sender',this);" href='javascript:void(0)'></a></td></tr>
		<?
		if($svdCouriers && count($svdCouriers)){?>
			<tr class='IPOLSDEK_courierInfo'><td><?=GetMessage('IPOLSDEK_JS_SOD_courierSender')?></td><td><select onchange='IPOLSDEK_oExport.courier.selectProfile($(this).val())'><option></option>
			<?foreach($svdCouriers as $ind => $vals){?>
				<option value='<?=$ind?>'><?=$vals['senderName']?></option>
			<?}?>
			</select>&nbsp;<a class='PropHint' onclick="return IPOLSDEK_oExport.popup('pop-courierSender',this);" href='javascript:void(0)'></a></td></tr>
		<?}?>
			<?// Дата?>
		<tr class='IPOLSDEK_courierInfo'><td><?=GetMessage('IPOLSDEK_JS_SOD_courierDate')?></td><td>
			<div class="adm-input-wrap adm-input-wrap-calendar">
				<input class="adm-input adm-input-calendar" disabled id='IPOLSDEK_courierDate' disabled type="text" name="IPOLSDEK_courierDate" style='width:148px;' value="<?=$ordrVals['courierDate']?>">
				<span class="adm-calendar-icon" style='right:0px'onclick="BX.calendar({node:this, field:'IPOLSDEK_courierDate', form: '', bTime: false, bHideTime: true,callback_after: IPOLSDEK_oExport.courier.onDateChange});"></span>
			</div>
		</td></tr>
		<tr class='IPOLSDEK_courierInfo'><td colspan='2' id='IPOLSDEK_courierDateError' style='font-size:small;color:red;display:none'><?=GetMessage('IPOLSDEK_JS_SOD_badDate')?></td></tr>
			<?// Время?>
		<tr class='IPOLSDEK_courierInfo'><td><?=GetMessage('IPOLSDEK_JS_SOD_courierTime')?></td><td><input id='IPOLSDEK_courierTimeBeg' type='text' value='<?=$ordrVals['courierTimeBeg']?>' style='width:56px' onchange='IPOLSDEK_oExport.courier.onTimeChange()'> - <input id='IPOLSDEK_courierTimeEnd' type='text' value='<?=$ordrVals['courierTimeEnd']?>' style='width:56px' onchange='IPOLSDEK_oExport.courier.onTimeChange()'><input type='hidden' id='IPOLSDEK_courierTimeOK'></td></tr>
		<tr class='IPOLSDEK_courierInfo'><td colspan='2' id='IPOLSDEK_courierTimeError' style='font-size:small;color:red'></td></tr>
			<?// Прочее курьер?>
		<tr class='IPOLSDEK_courierInfo'><td><?=GetMessage('IPOLSDEK_JS_SOD_courierCity')?></td><td>
			<div><span id='IPOLSDEK_cSLabel'><?=$ordrVals['courierCity']['NAME']." ({$ordrVals['courierCity']['REGION']})"?></span><br><a href='javascript:void(0)' onclick='IPOLSDEK_oExport.courier.changeCity(1)'><?=GetMessage("IPOLSDEK_STT_CHNG")?></a></div>
			<div style='display:none'><input id='IPOLSDEK_cSSelector' type='text' value=''></div>
			<input type='hidden' id='IPOLSDEK_courierCity' value='<?=$ordrVals['courierCity']['SDEK_ID']?>'>
		</td></tr>
		<tr class='IPOLSDEK_courierInfo'><td><?=GetMessage('IPOLSDEK_JS_SOD_courierStreet')?></td><td><input id='IPOLSDEK_courierStreet' type='text' value='<?=str_replace("'",'"',$ordrVals['courierStreet'])?>'></td></tr>
		<tr class='IPOLSDEK_courierInfo'><td><?=GetMessage('IPOLSDEK_JS_SOD_courierHouse')?></td><td><input id='IPOLSDEK_courierHouse' type='text' value='<?=str_replace("'",'"',$ordrVals['courierHouse'])?>'></td></tr>
		<tr class='IPOLSDEK_courierInfo'><td><?=GetMessage('IPOLSDEK_JS_SOD_courierFlat')?></td><td><input id='IPOLSDEK_courierFlat' type='text' value='<?=str_replace("'",'"',$ordrVals['courierFlat'])?>'></td></tr>
		<tr class='IPOLSDEK_courierInfo'><td><?=GetMessage('IPOLSDEK_JS_SOD_courierPhone')?></td><td><input id='IPOLSDEK_courierPhone' type='text' value='<?=$ordrVals['courierPhone']?>'></td></tr>
		<tr class='IPOLSDEK_courierInfo'><td><?=GetMessage('IPOLSDEK_JS_SOD_courierName')?></td><td><input id='IPOLSDEK_courierName' type='text' value='<?=str_replace("'",'"',$ordrVals['courierName'])?>'></td></tr>
		<tr class='IPOLSDEK_courierInfo'><td><?=GetMessage('IPOLSDEK_JS_SOD_courierComment')?></td><td><input id='IPOLSDEK_courierComment' type='text' value='<?=str_replace("'",'"',$ordrVals['courierComment'])?>'></td></tr>
	<?//Адрес?>
		<tr class='heading'><td colspan='2'><?=GetMessage('IPOLSDEK_JS_SOD_HD_ADDRESS')?></td></tr>
		<tr>
			<td>
				<?=GetMessage('IPOLSDEK_JS_SOD_location')?>
				<?=$multiCity?>
			</td>
			<td>
				<?=($multiCityS)?$multiCityS:$cityName?>
				<input id='IPOLSDEK_location' type='hidden' value="<?=$ordrVals['location']?>"><?=$message['location']?>
				<input id='IPOLSDEK_cityTo' type='hidden' value="<?=$orderCity['BITRIX_ID']?>">
			</td>
		</tr>
		<tr class='IPOLSDEK_notSV'><td><?=GetMessage('IPOLSDEK_JS_SOD_street')?></td><td>
			<?if($ordrVals['street']){?>
				<input id='IPOLSDEK_street' type='text' value="<?=$ordrVals['street']?>">
			<?}else{?>
				<textarea id='IPOLSDEK_street'><?=$ordrVals['address']?></textarea>
			<?}?>
			<?=$message['street']?>
		</td></tr>
		<tr class='IPOLSDEK_notSV'><td><?=GetMessage('IPOLSDEK_JS_SOD_house')?></td><td><input id='IPOLSDEK_house' type='text' value="<?=(self::$locStreet && $ordrVals['address'] && !$ordrVals['house'])?$ordrVals['address']:$ordrVals['house']?>"><?=$message['house']?></td></tr>
		<tr class='IPOLSDEK_notSV'><td><?=GetMessage('IPOLSDEK_JS_SOD_flat')?></td><td><input id='IPOLSDEK_flat' type='text' value="<?=$ordrVals['flat']?>"><?=$message['flat']?></td></tr>
		<tr class='IPOLSDEK_SV'><td><?=GetMessage('IPOLSDEK_JS_SOD_PVZ')?></td>
			<td>
			<?if($strOfPSV){?><select id='IPOLSDEK_PVZ' onchange='IPOLSDEK_oExport.onPVZChange($(this))'><?=$strOfPSV?></select><?}
			else{?><span id='IPOLSDEK_deliveryPoint_noSV'><?=GetMessage('IPOLSDEK_JS_SOD_NOSVREG')?></span><?}?>
			<?=$message['deliveryPoint']?>
			</td>
		</tr>
		<tr class='IPOLSDEK_SV'><td colspan='2'><span id='IPOLSDEK_badPVZ' style='display:none'><?=GetMessage('IPOLSDEK_JS_SOD_BADPVZ')?></span></td></tr>
	<?//Получатель?>
		<tr class='heading'><td colspan='2'><?=GetMessage('IPOLSDEK_JS_SOD_HD_RESIEVER')?></td></tr>
		<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_name')?></td><td><input id='IPOLSDEK_name' type='text' value="<?=$ordrVals['name']?>"><?=$message['name']?></td></tr>
		<tr><td valign="top"><?=GetMessage('IPOLSDEK_JS_SOD_phone')?></td><td><input id='IPOLSDEK_phone' type='text' value="<?=$ordrVals['phone']?>"></td></tr>
		<tr><td valign="top"><?=GetMessage('IPOLSDEK_JS_SOD_email')?></td><td><input id='IPOLSDEK_email' type='text' value="<?=$ordrVals['email']?>"></td></tr>
		<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_comment')?></td><td><textarea id='IPOLSDEK_comment'><?=$ordrVals['comment']?></textarea><?=$message['comment']?></td></tr>
		<tr><td colspan='2'>
			<?foreach(array('realSeller','sender','courierSender','GABARITES') as $hintCode){?>
				<div id="pop-<?=$hintCode?>" class="b-popup" >
					<div class="pop-text"><?=GetMessage("IPOLSDEK_JSC_SOD_HELPER_$hintCode")?></div>
					<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
				</div>
			<?}?>
		</td></tr>
	<?// Оплата?>
		<tr class='heading'><td colspan='2'><?=GetMessage('IPOLSDEK_JS_SOD_HD_PAYMENT')?></td></tr>
		<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_isBeznal')?></td><td>
			<?if($payment === true || floatval($payment) >= floatval(self::$orderDescr['info']['PRICE'])){?>
				<input type='checkbox' id='IPOLSDEK_isBeznal' value='Y' <?=($ordrVals['isBeznal']=='Y')?'checked':''?> onchange='IPOLSDEK_oExport.checkPay()'>
			<?}else{?>
				<input type='checkbox' id='IPOLSDEK_isBeznal' value='Y' checked disabled onchange='IPOLSDEK_oExport.checkPay()'><br>
				<?
					if(!$payment)
						echo GetMessage("IPOLSDEK_JS_SOD_NONALPAY");
					else
						echo str_replace("#VALUE#",$payment,GetMessage("IPOLSDEK_JS_SOD_TOOMANY"));
			}?>
			&nbsp;&nbsp;<span id='IPOLSDEK_notPayed' style='color:red;display:none'><?=GetMessage("IPOLSDEK_JS_SOD_NOTPAYED")?></span>
		</td></tr>
		<?if(self::$orderDescr['info']['SUM_PAID'] > 0){?>
			<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_paid')?></td><td><?=self::$orderDescr['info']['SUM_PAID']?> <?=GetMessage('IPOLSDEK_JSC_SOD_RUB')?></td></tr>
		<?}?>
		<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_toPay')?></td><td>
			<input type='text' id='IPOLSDEK_toPay' value="<?=$ordrVals['toPay']?>" size='10' style='text-align: right' onchange='IPOLSDEK_oExport.checkFloat($(this))'>&nbsp;<?=GetMessage('IPOLSDEK_JSC_SOD_RUB')?>
			<?if($cntrCurrency){?>
				&nbsp;&nbsp;&nbsp;<span id='IPOLSDEK_toPayFormat'><?=self::formatCurrency(array('SUM'=>$ordrVals['toPay'],'TO'=>$cntrCurrency,'FORMAT'=>'Y','orderId'=>$orderId))?></span>
			<?}?>
		</td></tr>
		<tr>
			<td><?=GetMessage('IPOLSDEK_JS_SOD_NDSGoods')?></td>
			<td>
				<select id='IPOLSDEK_NDSGoods'>
					<?foreach(array('VATX','VAT0','VAT10','VAT18') as $ndsVats){?>
						<option value='<?=$ndsVats?>' <?=($ordrVals['NDSGoods'] == $ndsVats) ? 'selected' : ''?>><?=GetMessage('IPOLSDEK_NDS_'.$ndsVats)?></option>
					<?}?>
				</select>
			</td>
		</tr>
		<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_deliveryP')?></td><td>
			<input type='text' id='IPOLSDEK_deliveryP' value="<?=$ordrVals['deliveryP']?>" size='10' style='text-align: right' onchange='IPOLSDEK_oExport.checkFloat($(this))'>&nbsp;<?=GetMessage('IPOLSDEK_JSC_SOD_RUB')?>
			<?if($cntrCurrency){?>
				&nbsp;&nbsp;&nbsp;<span id='IPOLSDEK_deliveryPFormat'><?=self::formatCurrency(array('SUM'=>$ordrVals['deliveryP'],'TO'=>$cntrCurrency,'FORMAT'=>'Y','orderId'=>$orderId))?></span>
			<?}?>
		</td></tr>
		<tr>
			<td><?=GetMessage('IPOLSDEK_JS_SOD_NDSDelivery')?></td>
			<td>
				<select id='IPOLSDEK_NDSDelivery'>
					<?foreach(array('VATX','VAT0','VAT10','VAT18') as $ndsVats){?>
						<option value='<?=$ndsVats?>' <?=($ordrVals['NDSDelivery'] == $ndsVats) ? 'selected' : ''?>><?=GetMessage('IPOLSDEK_NDS_'.$ndsVats)?></option>
					<?}?>
				</select>
			</td>
		</tr>
	<?//Доп. параметры?>
		<tr class='heading'><td colspan='2'><?=GetMessage('IPOLSDEK_AS')?></td></tr>
		<?foreach($exOpts as $id => $option)
			if($option['SHOW']=="Y" || $option['DEF']=="Y"){
			?>
			<tr><td><?=GetMessage("IPOLSDEK_AS_".$id."_NAME")?></td><td><input id='IPOLSDEK_AS_<?=$id?>' <?=($option['DEF']=="Y")?"checked":""?> type='checkbox' value='<?=$id?>'></td></tr>
		<?}?>
		
	<?// О заказе?>
		<tr class='heading'><td colspan='2'><a onclick='IPOLSDEK_oExport.serverShow()' href='javascript:void(0)'><?=GetMessage('IPOLSDEK_JS_SOD_ABOUT')?></td></tr>
			<?// Габариты родные?>
		<tr class='IPOLSDEK_detOrder' style='display:none'>	
			<td><?=GetMessage('IPOLSDEK_JS_SOD_GABARITES')?> <a href='#' class='PropHint' onclick='return IPOLSDEK_oExport.popup("pop-GABARITES",this);'></a></td>
			<td>
				<?=($naturalGabs['D_L'])*10?><?=GetMessage("IPOLSDEK_mm")?> x <?=($naturalGabs['D_W'])*10?><?=GetMessage("IPOLSDEK_mm")?> x <?=($naturalGabs['D_H'])*10?><?=GetMessage("IPOLSDEK_mm")?>, <?=$naturalGabs['W']?><?=GetMessage("IPOLSDEK_kg")?> 
				<?if(!self::$isLoaded || $status == 'NEW' || $status == 'ERROR'){?>
					<a <?=(sdekHelper::isEqualArrs($naturalGabs,$ordrVals['GABS'])?"":"style='display:none'")?> href='javascript:void(0)' id='IPOLSDEK_natGabs' onclick='IPOLSDEK_oExport.gabs.change()'><?=GetMessage('IPOLSDEK_STT_CHNG')?></a>
				<?}?>
				<input id='IPOLSDEK_GABS_D_L' type='hidden' value="<?=$ordrVals['GABS']['D_L']?>">
				<input id='IPOLSDEK_GABS_D_W' type='hidden' value="<?=$ordrVals['GABS']['D_W']?>">
				<input id='IPOLSDEK_GABS_D_H' type='hidden' value="<?=$ordrVals['GABS']['D_H']?>">
				<input id='IPOLSDEK_GABS_W'   type='hidden' value="<?=$ordrVals['GABS']['W']?>">
			</td>
		</tr>
			<?// Габариты заданные?>
		<tr class='IPOLSDEK_detOrder' style='display:none'>	
			<td><?=GetMessage('IPOLSDEK_JS_SOD_CGABARITES')?></td>
			<td>
				<div id='IPOLSDEK_gabsPlace'>
					<?=($ordrVals['GABS']['D_L'])*10?><?=GetMessage("IPOLSDEK_mm")?> x <?=($ordrVals['GABS']['D_W'])*10?><?=GetMessage("IPOLSDEK_mm")?> x <?=($ordrVals['GABS']['D_H'])*10?><?=GetMessage("IPOLSDEK_mm")?>, <?=$ordrVals['GABS']['W']?><?=GetMessage("IPOLSDEK_kg")?> 
					<?if(!self::$isLoaded || $status == 'NEW' || $status == 'ERROR'){?>
					<a href='javascript:void(0)' onclick='IPOLSDEK_oExport.gabs.change()'><?=GetMessage('IPOLSDEK_STT_CHNG')?></a>
					<?}?>
				</div>
			</td>
		</tr>
			<?// Габариты упаковки?>
		<tr class='IPOLSDEK_detOrder' style='display:none'>	
			<td colspan="2" style='text-align:center'><?=GetMessage('IPOLSDEK_JS_SOD_PACKS_GIVEN')?><input type='hidden' id='IPOLSDEK_PLACES' value='<?=(array_key_exists('packs',$ordrVals) && is_array($ordrVals['packs'])) ? json_encode($ordrVals['packs']) : false?>'></td>
		</tr>
		<tr class='IPOLSDEK_detOrder' style='display:none'>	
			<td><?=GetMessage('IPOLSDEK_JS_SOD_VWEIGHT')?></td>
			<td>
				<span id='IPOLSDEK_VWeightPlace'><?=($ordrVals['GABS']['D_L']*$ordrVals['GABS']['D_W']*$ordrVals['GABS']['D_H']/5000)?></span><?=GetMessage("IPOLSDEK_kg")?>
			</td>
		</tr>		
		<tr class='IPOLSDEK_detOrder' style='display:none'>	
			<td><?=GetMessage('IPOLSDEK_JS_SOD_SDELPRICE')?></td>
			<td><?=self::$orderDescr['info']['PRICE_DELIVERY']?></td>
		</tr>		
		<tr class='IPOLSDEK_detOrder' style='display:none'>	
			<td><?=GetMessage('IPOLSDEK_JS_SOD_NDELPRICE')?></td>
			<td id='IPOLSDEK_newPrDel'></td>
		</tr>
			<?// Аккаунт?>
		<tr class='IPOLSDEK_detOrder' style='display:none'>	
			<td><?=GetMessage('IPOLSDEK_JSC_SOD_ACCOUNT')?></td>
			<td><?=($acc['LABEL'])?$acc['LABEL']:$acc['ACCOUNT']?><input type='hidden' id='IPOLSDEK_currency' value='<?=$cntrCurrency?>'></td>
		</tr>
	</table>
</div>
<?if($senderWH){?>
	<div id='IPOLSDEK_senderWHcontent' style='display:none'>
		<table id='IPOLSDEK_senderWH'>
			<tr><td colspan='3'><small><?=GetMessage('IPOLSDEK_JS_SOD_senderWH_HINT')?></small></td></tr>
	<?
		foreach($senderWH as $ind => $descr){
			$sender = sqlSdekCity::getBySId($descr[0]);
	?>
			<tr><th><?=$sender['NAME']?></th><th><?=$sender['REGION']?></th><th><?=$descr[1]?></th></tr>
	<?
		if(array_key_exists($ind,sdekShipmentCollection::$shipments))
			foreach(sdekShipmentCollection::$shipments[$ind]->goods as $goodCol){?>
				<tr><td colspan='2'><?=$goodCol['NAME']?> (ID:<?=$goodCol['PRODUCT_ID']?>)</td><td><?=$goodCol['QUANTITY']?></td></tr>
			<?}
	}
	?>
		</table>	
	</div>
<?}?>