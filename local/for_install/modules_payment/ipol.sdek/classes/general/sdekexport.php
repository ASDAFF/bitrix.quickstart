<?
IncludeModuleLangFile(__FILE__);

/*
	onGoodsToRequest
	onParseAddress
*/

class sdekExport extends sdekHelper{
	static $workMode    = false;
	static $orderId     = false;
	static $shipmentID  = '';

	static $orderDescr  = false;
	static $requestVals = false;
	static $isLoaded    = false; // выставляются в orderDetail
	static $isEditable  = false;

	static $locStreet   = false; // улица берется из местоположения

	static $subRequests = false;

	function getAllProfiles(){
		return array('pickup','courier');
	}

	public function loadExportWindow($workMode){
		self::$workMode = $workMode;
		if($workMode == 'order'){
			self::$orderId = $_REQUEST['ID'];
			$reqId = self::$orderId;
		}else{
			self::$orderId    = $_REQUEST['order_id'];
			self::$shipmentID = $_REQUEST['shipment_id'];
			$reqId = self::$shipmentID;
		}

		self::$orderDescr = self::getOrderDescr();

		if(
			COption::GetOptionString(self::$MODULE_ID,'showInOrders','Y') == 'N' &&
			!self::$orderDescr['info']['DELIVERY_SDEK']
		)
			return;

		self::$requestVals = sdekdriver::GetByOI($reqId,$workMode);

		if(self::noSendings())
			include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::$MODULE_ID."/orderDetail.php");
		else
			self::showExisted();
	}

	//получаем город заказа по его id
	static $optCity = false;
	static $arTmpArLocation=false;
	public function getOrderCity($id){ // используетс€ только в orderDetail.php
		if(!self::$optCity)
			self::$optCity = COption::GetOptionString(self::$MODULE_ID,'location',false);
		if(!is_array(self::$arTmpArLocation)) self::$arTmpArLocation=array();

		$oCity=CSaleOrderPropsValue::GetList(array(),array('ORDER_ID'=>$id,'CODE'=>self::$optCity))->Fetch();
		if($oCity['VALUE']){
			if(is_numeric($oCity['VALUE'])){
				if(in_array($oCity['VALUE'],self::$arTmpArLocation))
					$oCity=self::$arTmpArLocation[$oCity['VALUE']];
				else{
					$cityId = self::getNormalCity($oCity['VALUE']);
					$tmpCity=CSaleLocation::GetList(array(),array("ID"=>$cityId,"CITY_LID"=>'ru'))->Fetch();
					if(!$tmpCity)
						$tmpCity=CSaleLocation::GetByID($cityId);
					self::$arTmpArLocation[$oCity['VALUE']]=($tmpCity['CITY_NAME_LANG'])?$tmpCity['CITY_NAME_LANG']:$tmpCity['CITY_NAME'];
					$oCity=str_replace(GetMessage('IPOLSDEK_LANG_YO_S'),GetMessage('IPOLSDEK_LANG_YE_S'),self::$arTmpArLocation[$oCity['VALUE']]);
				}
			}
			else
				$oCity=$oCity['VALUE'];
		}
		else
			$oCity=false;

		return $oCity;
	}

	public function checkCityLocation($cityId){
		if(self::isLocation20()){
			$streetType = \Bitrix\Sale\Location\TypeTable::getList(array('filter'=>array('=CODE'=>'STREET')))->Fetch();
			if(strlen($cityId) >= 10)
				$arFilter = array('=CODE' => $cityId,'NAME.LANGUAGE_ID' => LANGUAGE_ID);
			else
				$arFilter = array('=ID' => $cityId,'NAME.LANGUAGE_ID' => LANGUAGE_ID);
			$location = \Bitrix\Sale\Location\LocationTable::getList(array('select' => array('ID','TYPE_ID','LBL'=>'NAME.NAME'),'filter' => $arFilter))->Fetch();
			if($location['TYPE_ID'] == $streetType['ID'])
				return $location['LBL'];
		}
		return false;
	}

	// получение информации о заказе
	public function getOrderDescr($oId=false,$mode=false){
		$arOrderDescr = array('info'=>array(),'properties'=>array());
		if(!$oId)
			$oId = self::$orderId;
		if(!$mode)
			$mode = (self::$workMode) ? self::$workMode : 'order';

		if(self::isConverted()){
			// информаци€ о заказе
			$orderInfo = Bitrix\Sale\Order::load($oId);
			$arUChecks = array("COMMENTS","PAY_SYSTEM_ID","PAYED","PRICE","SUM_PAID","PRICE_DELIVERY");
			if($mode == 'order'){
				$ds = $orderInfo->getDeliverySystemId();
				foreach($ds as $id){
					$arOrderDescr['info']['DELIVERY_SDEK'] = (bool)self::defineDelivery($id);
					if($arOrderDescr['info']['DELIVERY_SDEK'])
						break;
				}
				$arUChecks[]="ACCOUNT_NUMBER";
			}else{
				if(!self::$shipmentID)
					self::$shipmentID = intval($_REQUEST["shipment_id"]);
				$shipment = self::getShipmentById(self::$shipmentID);

				if($shipment){
					$arOrderDescr['info']['DELIVERY_SDEK'] = (bool)self::defineDelivery($shipment['DELIVERY_ID']);
					$arOrderDescr['info']['ACCOUNT_NUMBER'] = $shipment['ACCOUNT_NUMBER'];
				}
			}

			foreach($arUChecks as $code)
				$arOrderDescr['info'][$code] = $orderInfo->getField($code);
			// свойства
			$arProps = $orderInfo->loadPropertyCollection()->getArray();
			foreach($arProps['properties'] as $arProp){
				$val = array_pop($arProp['VALUE']);
				if($val)
					$arOrderDescr['properties'][$arProp['CODE']] = $val;
			}
		}else{
			// информаци€ о заказе
			$order = CSaleOrder::getById($oId);
			$arOrderDescr['info']['DELIVERY_SDEK'] = (strpos($order['DELIVERY_ID'],'sdek:') === 0);
			$arUChecks = array("COMMENTS","PAY_SYSTEM_ID","PAYED","ACCOUNT_NUMBER","PRICE","SUM_PAID","PRICE_DELIVERY");
			foreach($arUChecks as $code)
				$arOrderDescr['info'][$code] = $order[$code];
			// свойства
			$orderProps=CSaleOrderPropsValue::GetOrderProps($oId);
			while($orderProp=$orderProps->Fetch())
				$arOrderDescr['properties'][$orderProp['CODE']] = $orderProp['VALUE'];
		}

		return $arOrderDescr;
	}

	function formation(){ // рассчитывает массив свойств для заказа
		$arFormation = array();

		// безнал
		$paySys = unserialize(COption::getOptionString(self::$MODULE_ID,'paySystems',''));
		if(
			in_array(self::$orderDescr['info']['PAY_SYSTEM_ID'],$paySys) ||
			(self::$workMode == 'order' && self::$orderDescr['info']['PAYED'] == 'Y')
		)
			$arFormation['isBeznal'] = 'Y';

		// тариф
		if(self::$orderDescr['properties']['IPOLSDEK_CNTDTARIF'])
			$arFormation['service'] = self::$orderDescr['properties']['IPOLSDEK_CNTDTARIF'];

		// реальный отправитель
		$arFormation['realSeller'] = ($rs = COption::GetOptionString(self::$MODULE_ID,'realSeller','')) ? $rs : '';

		// город-отправитель
		$sender = sqlSdekCity::getByBId(COption::GetOptionString(self::$MODULE_ID,'departure'));
		if($sender)
			$arFormation['departure'] = $sender['SDEK_ID'];

		// свойства
		$arProps = array();
		if(IsModuleInstalled('ipol.kladr')){
			$propCode = COption::GetOptionString(self::$MODULE_ID,'address','');
			if($propCode && self::$orderDescr['properties'][$propCode]){
				$containment = explode(",",self::$orderDescr['properties'][$propCode]);
				if(is_numeric($containment[0])) $start = 2;
				else $start = 1;		
				if($containment[$start]){ self::$orderDescr['properties']['address'] = ''; $arProps['street'] = trim($containment[$start]);}
				if($containment[($start+1)]){ $containment[($start+1)] = trim($containment[($start+1)]); $arProps['house'] = trim(substr($containment[($start+1)],strpos($containment[($start+1)]," ")));}
				if($containment[($start+2)]){ $containment[($start+2)] = trim($containment[($start+2)]); $arProps['flat']  = trim(substr($containment[($start+2)],strpos($containment[($start+2)]," ")));}
			}
		}

		foreach(array('location','name','email','phone','address','street','house','flat') as $prop){
			if(!$arProps[$prop] || $prop=='location'){
				$propCode = COption::GetOptionString(self::$MODULE_ID,$prop,'');
				if($prop!='location' && (!self::$locStreet || $prop != 'street'))
					$arProps[$prop] = ($propCode)?self::$orderDescr['properties'][$propCode]:false;
				elseif($prop == 'street')
					$arProps[$prop] = self::$locStreet;
				elseif($propCode){
					self::$locStreet = self::checkCityLocation(self::$orderDescr['properties'][$propCode]);
					self::$orderDescr['properties'][$propCode] = sdekHelper::getNormalCity(self::$orderDescr['properties'][$propCode]);
					$src = sdekExport::getCity(self::$orderDescr['properties'][$propCode]);
					$orignCityId = $src;
					if(!$arProps[$prop]){
						$arProps[$prop]=sdekExport::getCity(self::$orderDescr['properties'][$propCode]);
						$cityName = sdekExport::getOrderCity(self::$orderId);
					}
				}
			}
		}

		foreach(array('location','name','email','phone') as $prop){
			$arFormation[$prop] = $arProps[$prop];
			unset($arProps[$prop]);
		}

		// комментарий
		if(self::$orderDescr['info']['COMMENTS'])
			$arFormation['comment'] = self::$orderDescr['info']['COMMENTS'];

		// к оплате за доставку
		$arFormation['deliveryP'] = self::$orderDescr['info']['PRICE_DELIVERY'];

		foreach($arProps as $prop => $value)
			$arFormation[$prop] = $value;

		// ПВЗ
		$PVZprop = COption::GetOptionString(self::$MODULE_ID,'pvzPicker',false);
		$PVZprop = (array_key_exists($PVZprop,self::$orderDescr['properties'])) ? self::$orderDescr['properties'][$PVZprop] : false;
		$arFormation['PVZ'] = ($PVZprop && strpos($PVZprop,"#S")) ? substr($PVZprop,strpos($PVZprop,"#S")+2):false;

		// габариты
		if(self::$workMode == 'order')
			CDeliverySDEK::setOrderGoods(self::$orderId);
		else
			CDeliverySDEK::setShipmentGoods(intval($_REQUEST["shipment_id"]));

		$left = CDeliverySDEK::$orderPrice - self::$orderDescr['info']['SUM_PAID'];
		$left = ($left < 0 || $left < 0.1) ? 0 : $left;
		$arFormation['toPay'] = (self::$workMode == 'order') ? $left : CDeliverySDEK::$orderPrice;

		$arFormation['GABS'] = array(
			"D_L" => CDeliverySDEK::$goods['D_L'],
			"D_W" => CDeliverySDEK::$goods['D_W'],
			"D_H" => CDeliverySDEK::$goods['D_H'],
			"W" => CDeliverySDEK::$goods['W']
		);
		
		// НДС
		$arFormation['NDSGoods']    = COption::GetOptionString(self::$MODULE_ID,'NDSGoods','VATX');
		$arFormation['NDSDelivery'] = COption::GetOptionString(self::$MODULE_ID,'NDSDelivery','VATX');

		return $arFormation;
	}

	function parseAddress(&$fields,$forse=false){
		$parsed = false;
		foreach(GetModuleEvents(self::$MODULE_ID, "onParseAddress", true) as $arEvent){
			ExecuteModuleEventEx($arEvent,Array(&$fields));
			$parsed = true;
		}
		if(!$parsed && $forse){
			$arAdress=array();
			$adrStr=explode(',',$fields['address']);
			$arDictionary = array(
				'STREET'   => array('len' => 20,'clr' => false),
				'HOUSE'    => array('len' => 2, 'clr' => true),
				'ENTRANCE' => array('len' => 3, 'clr' => true),
				'KORP'     => array('len' => 3, 'clr' => true),
				'FLOOR'    => array('len' => 3, 'clr' => true),
				'FLAT'     => array('len' => 5, 'clr' => true),
				'CITY'	   => array('len' => 3, 'clr' => false)
			);
			foreach($adrStr as $key => $addr){
				$addr = trim($addr);
				if(!$addr) unset($adrStr[$key]);
				if($key == 0 && is_numeric($addr)) unset($adrStr[$key]); // индекс

				foreach($arDictionary as $key => $descr)
					if(!$arAdress[$key])
						for($i=1;$i<$descr['len'];$i++)
							if(self::strps($addr,GetMessage('IPOLSDEK_ADRSUFFER_'.$key.$i))!==false){
								$arAdress[$key]=($descr['clr']) ? self::ctAdr($addr,strlen(GetMessage('IPOLSDEK_ADRSUFFER_'.$key.$i))) : $addr;
								unset($adrStr[$key]);
							}

				if(!$arAdress['HOUSE']){//дом
					if(self::strps($addr,GetMessage('IPOLSDEK_ADRSUFFER_HOUSE2'))!==false&&self::strps($addr,GetMessage('IPOLSDEK_ADRSUFFER_HOUSE2'))<2)
						{$arAdress['HOUSE']=self::ctAdr($addr,2);unset($adrStr[$key]);}
					if(self::strps($addr,GetMessage('IPOLSDEK_ADRSUFFER_HOUSE3'))===0&&self::strps($addr,GetMessage('IPOLSDEK_ADRSUFFER_HOUSE3'))<2)
						{$arAdress['HOUSE']=self::ctAdr($addr,2);unset($adrStr[$key]);}
				}
			}

			if(count($adrStr)==1 && !$arAdress['STREET'])
				$arAdress['STREET']=trim(array_pop($adrStr));
			elseif(count($adrStr)==1&&!$arAdress['CITY']){
				$needle=array_pop($adrStr);
				if(!$arAdress['HOUSE']&&preg_match('/[\d]+/',$needle))
					$arAdress['HOUSE']=$needle;
				else
					$arAdress['CITY']=$needle;
			}

			if(count($adrStr)==2&&!$arAdress['STREET']&&!$arAdress['CITY']){
				$arAdress['STREET']=trim(array_pop($adrStr));
				if(!$arAdress['HOUSE'])
					$arAdress['HOUSE']=array_pop($adrStr);
				else
					$arAdress['CITY']=array_pop($adrStr);
			}

			if(count($adrStr)>3&&!$arAdress['STREET'])
				$arAdress['STREET']=implode(', ',$addr);

			if($arAdress['KORP'])
				$arAdress['HOUSE'] .= "/".$arAdress['KORP'];

			$fields['street'] = $arAdress['STREET'];
			$fields['house']  = $arAdress['HOUSE'];
			$fields['flat']   = $arAdress['FLAT'];			
		}
	}

	// для парсинга адреса
	function ctAdr($wt,$n){return trim(substr(trim($wt),$n));}
	function strps($wr,$wat){return strpos(strtolower($wr),strtolower($wat));}

	function loadGoodsPack($packs){ // рассовывает упаковки по товарам
		CDeliverySDEK::$goods = array();
		foreach($packs as $pack){
			$arGabs = explode(' x ',$pack['gabs']);
			if(count($arGabs) != 3) continue;
			CDeliverySDEK::$goods[] = array(
				'D_W' => $arGabs[0],
				'D_L' => $arGabs[1],
				'D_H' => $arGabs[2],
				'W'   => $pack['weight']
			);
		}
	}

	// расчет габаритов товаров по указанным параметрам
	function countGoods($params){
		$arGCatalog = array();
		if(!cmodule::includeModule('catalog')) return;
		if(!count($params['goods'])){
			echo "G{0,0,0,}G";
			return;
		}
		$gC = CCatalogProduct::GetList(array(),array('ID'=>array_keys($params['goods'])));
		while($element=$gC->Fetch())
			$arGCatalog[$element['ID']] = array(
				'WEIGHT' => $element['WEIGHT'],
				'LENGTH' => $element['LENGTH'],
				'WIDTH'  => $element['WIDTH'],
				'HEIGHT' => $element['HEIGHT']
			);

		$arGoods = array();
		foreach($params['goods'] as $goodId => $cnt)
			$arGoods[$goodId] = array(
				'ID'		    => $goodId,
				'PRODUCT_ID'    => $goodId,
				'QUANTITY'      => $cnt,
				'CAN_BUY'       => 'Y',
				'DELAY'         => 'N',
				'SET_PARENT_ID' => false,
				'WEIGHT'		=> $arGCatalog[$goodId]['WEIGHT'],
				'DIMENSIONS' 	=> array(
					'LENGTH' => $arGCatalog[$goodId]['LENGTH'],
					'WIDTH'  => $arGCatalog[$goodId]['WIDTH'],
					'HEIGHT' => $arGCatalog[$goodId]['HEIGHT']
				),
			);
		CDeliverySDEK::setGoods($arGoods);
		echo "G{".CDeliverySDEK::$goods['D_L'].",".CDeliverySDEK::$goods['D_W'].",".CDeliverySDEK::$goods['D_H'].",}G";
	}

	function getAllTarifsToCount($arParams){
		$tarifs = self::getTarifList(array('fSkipCheckBlocks'=>true));
		$tarifDescr = self::getExtraTarifs();
		$rezTarifs = array();
		foreach($tarifs as $type => $arTarifs){
			$arTarifs = self::arrVals($arTarifs);
			foreach($arTarifs as $id){
				if($tarifDescr[$id]['SHOW'] == 'N') continue;
				$rezTarifs[$type][$id]=$tarifDescr[$id]['NAME'];
			}
		}

		if($arParams['isdek_action'])
			echo json_encode(self::zajsonit($rezTarifs));
		else
			return $rezTarifs;
	}

	// перерасчет доставки
	public function extCountDeliv($arParams){
		if(!$arParams['orderId'] || !$arParams['cityTo'] || !$arParams['tarif'])
			return false;
		self::setCalcData($arParams);
		$arBlockedTarifs = array();
		$curProfile = false;
		foreach(self::getAllProfiles() as $tarifName)
			if(!in_array($arParams['tarif'],CDeliverySDEK::getTarifList(array('type'=>$tarifName,'answer'=>'array','fSkipCheckBlocks'=>true))))
				$arBlockedTarifs[] = $tarifName;
			else
				$curProfile = $tarifName;
		$dost = sdekdriver::getDelivery(true);
		if($dost && $dost['ACTIVE'] && self::checkProfileActive($curProfile)){
			$arReturn = CDeliverySDEK::countDelivery(array('CITY_TO_ID'=>$arParams['cityTo'],'FORBIDDEN' => $arBlockedTarifs));
			$arReturn['price']       = strip_tags($arReturn['price']);
			if(self::diffPrice($arReturn['price']))
				$arReturn['sourcePrice'] = CDeliverySDEK::$lastCnt;
			$arReturn['tarif']       = $arParams['tarif'];
		}else{
			$cityTo = self::getCity($arParams['cityTo'],true);
			CDeliverySDEK::$sdekCity = $cityTo['SDEK_ID'];
			CDeliverySDEK::setAuth(self::defineAuth(array('COUNTRY'=>($arCity['COUNTRY']) ? $arCity['COUNTRY'] : 'rus')));
			$_arReturn = CDeliverySDEK::calculateDost($arParams['tarif']);
			if($_arReturn['success'])
				$arReturn = array(
					'success'     => true,
					'termMin'     => $_arReturn['termMin'],
					'termMax'     => $_arReturn['termMax'],
					'sourcePrice' => $_arReturn['price'],
					'tarif'		  => $arParams['tarif']
				);
			else
				$arReturn = array('success' => false);
		}

		if($arParams['isdek_action'])
			echo json_encode(self::zajsonit($arReturn));
		else
			return $arReturn;
	}

	private function diffPrice($got){
		return (CDeliverySDEK::$lastCnt != floatval(str_replace(" ","",$got)));
	}

	// установка габаритов дл€ расчета доставки
	private function setCalcData($arParams){
		if(!array_key_exists('packs',$arParams) || !$arParams['packs']){
			if(!array_key_exists('GABS',$arParams)){
				if($arParams['mode'] == 'order')
					CDeliverySDEK::setOrderGoods($arParams['orderId']);
				else
					CDeliverySDEK::setShipmentGoods($arParams['shipment'],$arParams['orderId']);
			}else
				CDeliverySDEK::$goods = $arParams['GABS'];
		}else
			self::loadGoodsPack($arParams['packs']);

		CDeliverySDEK::$sdekSender = ($arParams['cityFrom']) ? $arParams['cityFrom'] : self::getHomeCity();
		CDeliverySDEK::$preSet = $arParams['tarif'];
		define('IPOLSDEK_NOCACHE',true);
	}

	// св€зка заказов / отгрузок
	public function noSendings(){
		self::$subRequests = array();
		if(!self::isConverted() || self::$requestVals || !self::canShipment())
			return true;
		if(self::$workMode == 'shipment'){
			$req = sdekdriver::GetByOI(self::$orderId,'order');
			if($req)
				self::$subRequests = array($req);
		}else{
			$shipments = Bitrix\Sale\Shipment::getList(array('filter'=>array('ORDER_ID' => self::$orderId)));
			$unsended = array();
			while($element=$shipments->Fetch()){
				$req = sdekdriver::GetByOI($element['ID'],'shipment');
				if($req)
					self::$subRequests[]=$req;
				else
					$unsended[] = $element['ID'];
			}
			if(count(self::$subRequests))
				self::$subRequests['unsended'] = $unsended;
		}
		return !(bool)count(self::$subRequests);
	}

	// Отображение окна отправленной заявки иного режима
	public function showExisted(){
		CJSCore::Init(array("jquery"));
		$unsended = false;
		if(array_key_exists('unsended',self::$subRequests)){
			$unsended = self::$subRequests['unsended'];
			unset(self::$subRequests['unsended']);
		}
		?>
			<style>
				.IPOLSDEK_sendedTable{
					background-color: #FFFFFF;
					border: 1px solid #DCE7ED;
					width: 100%;
					margin: 5px 0px;
					padding: 5px;
				}
			</style>
			<script>
			var IPOLSDEK_existedInfo = {
				load: function(){
					if($('#IPOLSDEK_btn').length) return;
					$('.adm-detail-toolbar').find('.adm-detail-toolbar-right').prepend("<a href='javascript:void(0)' onclick='IPOLSDEK_existedInfo.showWindow()' class='adm-btn' id='IPOLSDEK_btn'><?=GetMessage('IPOLSDEK_JSC_SOD_BTNAME')?></a>");
				},
				// окно
				wnd: false,
				showWindow: function(){
					if(!IPOLSDEK_existedInfo.wnd){
						var html=$('#IPOLSDEK_wndOrder').html();
						$('#IPOLSDEK_wndOrder').html('');
						IPOLSDEK_existedInfo.wnd = new BX.CDialog({
							title: "<?=GetMessage('IPOLSDEK_JSC_SOD_WNDTITLE')?>",
							content: html,
							icon: 'head-block',
							resizable: true,
							draggable: true,
							height: '350',
							width: '400',
							buttons: []
						});
					}
					IPOLSDEK_existedInfo.wnd.Show();
				},
				print: function(oId){
					$('#IPOLSDEK_print_'+oId).attr('disabled','true');
					$('#IPOLSDEK_print_'+oId).val('<?=GetMessage("IPOLSDEK_JSC_SOD_LOADING")?>');
					$.ajax({
						url  : "/bitrix/js/<?=self::$MODULE_ID?>/ajax.php",
						type : 'POST',
						data : {
							isdek_action : 'printOrderInvoice',
							oId  : oId,
							mode : '<?=(self::$workMode == 'shipment') ? 'order' : 'shipment'?>'
						},
						dataType : 'json',
						success  : function(data){
							$('#IPOLSDEK_print_'+oId).removeAttr('disabled');
							$('#IPOLSDEK_print_'+oId).val('<?=GetMessage("IPOLSDEK_JSC_SOD_PRNTSH")?>');
							if(data.result == 'ok')
								window.open('/upload/<?=self::$MODULE_ID?>/'+data.file);
							else
								alert(data.error);
						}
					});
				},

				curDelete: false,
				delete: function(oId,status){
					if(IPOLSDEK_existedInfo.curDelete != false)
						return;
					$('#IPOLSDEK_delete_'+oId).attr('disabled','true');
					IPOLSDEK_existedInfo.curDelete = oId;
					if(status == 'NEW' || status == 'ERROR' || status == 'DELETE'){
						if(confirm("<?=GetMessage('IPOLSDEK_JSC_SOD_IFDELETE')?>"))
							$.post(
								"/bitrix/js/<?=self::$MODULE_ID?>/ajax.php",
								{isdek_action:'delReqOD',oid:oId,mode:'<?=(self::$workMode == 'order') ? 'shipment' : 'order'?>'},
								function(data){
									IPOLSDEK_existedInfo.onDelete(data);
								}
							);
					}else{
						if(status == 'OK'){
							if(confirm("<?=GetMessage('IPOLSDEK_JSC_SOD_IFKILL')?>"))
								$.post(
									"/bitrix/js/<?=self::$MODULE_ID?>/ajax.php",
									{isdek_action:'killReqOD',oid:oId,mode:'<?=(self::$workMode == 'order') ? 'shipment' : 'order'?>'},
									function(data){
										if(data.indexOf('GD:')===0)
											IPOLSDEK_existedInfo.onDelete(data.substr(3));
										else{
											alert(data);
											$('#IPOLSDEK_print_'+IPOLSDEK_existedInfo.curDelete).removeAttr('disabled');
										}
									}
								);
						}
					}
				},
				onDelete: function(data){
					alert(data);
					$('#IPOLSDEK_sT_'+IPOLSDEK_existedInfo.curDelete).replaceWith('');
					if($('.IPOLSDEK_sendedTable').length == 0)
						document.location.reload();
					IPOLSDEK_existedInfo.curDelete = false;
				}
			};
			$(document).ready(IPOLSDEK_existedInfo.load);
			</script>
			<div style='display:none' id='IPOLSDEK_wndOrder'>
				<div><?=GetMessage('IPOLSDEK_JSC_NOWND_'.self::$workMode)?></div>
				<?foreach(self::$subRequests as $request){?>
					<table class='IPOLSDEK_sendedTable' id='IPOLSDEK_sT_<?=$request['ORDER_ID']?>'>
						<tr>
							<?if(self::$workMode == 'shipment'){?>
								<td><?=GetMessage("IPOLSDEK_JSC_SOD_order")?></td>
								<td>
									<a target='_blank' href='/bitrix/admin/sale_order_detail.php?ID=<?=$request[
								"ORDER_ID"]?>'><?=$request['ORDER_ID']?></a>
							<?}else{?>
								<td><?=GetMessage("IPOLSDEK_JSC_SOD_shipment")?></td>
								<td>
									<a target='_blank' href='/bitrix/admin/sale_order_shipment_edit.php?order_id=<?=self::$orderId?>&shipment_id=<?=$request[
								"ORDER_ID"]?>'><?=$request[
								"ORDER_ID"]?></a>
							<?}?>
							</td>
						</tr>
						<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_STATUS')?></td><td><?=$request['STATUS']?></td></tr>
						<tr><td colspan='2'><small><?=GetMessage('IPOLSDEK_JS_SOD_STAT_'.$request['STATUS'])?></small></td></tr>
						<?if($request['SDEK_ID']){?><tr><td><?=GetMessage('IPOLSDEK_JS_SOD_SDEK_ID')?></td><td><?=$request['SDEK_ID']?></td></tr><?}?>
						<?if($request['MESS_ID']){?><tr><td><?=GetMessage('IPOLSDEK_JS_SOD_MESS_ID')?></td><td><?=$request['MESS_ID']?></td></tr><?}?>
						<tr><td colspan='2'><hr></td></tr>
						<tr><td colspan='2'>
							<?if(in_array($request['STATUS'],array('OK','ERROR','NEW','DELETD'))){?>
							<input id='IPOLSDEK_delete_<?=$request['ORDER_ID']?>' value="<?=GetMessage('IPOLSDEK_JSC_SOD_DELETE')?>" onclick="IPOLSDEK_existedInfo.delete(<?=$request['ORDER_ID']?>,'<?=$request['STATUS']?>'); return false;" type="button">&nbsp;&nbsp;
							<?}?>
							<?if($request['STATUS'] == 'OK'){?>
							<input id='IPOLSDEK_print_<?=$request['ORDER_ID']?>' value="<?=GetMessage('IPOLSDEK_JSC_SOD_shtrih')?>" onclick="IPOLSDEK_existedInfo.print(<?=$request['ORDER_ID']?>); return false;" type="button">&nbsp;&nbsp;
							<?}?>
							<?if($request['SDEK_ID']){?><a href="http://www.edostavka.ru/track.html?order_id=<?=$request['SDEK_ID']?>" target="_blank"><?=GetMessage('IPOLSDEK_JSC_SOD_FOLLOW')?></a><?}?>
						</td></tr>
					</table>
				<?}?>
				<?if($unsended){?>
				<div>
					<?=GetMessage('IPOLSDEK_JSC_NOWND_noSended')?>
					<?foreach($unsended as $shipmintId){?><a target='_blank' href='/bitrix/admin/sale_order_shipment_edit.php?order_id=<?=self::$orderId?>&shipment_id=<?=$shipmintId?>'><?=$shipmintId?></a>&nbsp;
					<?}?>
				</div>
				<?}?>
			</div>
		<?
	}

	function formatCurrency($params){
		if(cmodule::includeModule('currency')){
			$from = ($params['FROM']) ? $params['FROM'] : CCurrency::GetBaseCurrency();
			$into = ($params['TO'])   ? $params['TO']   : CCurrency::GetBaseCurrency();
			if(
				COption::GetOptionString(self::$MODULE_ID,'noteOrderDateCC','N') == 'Y' &&
				array_key_exists('orderId',$params) &&
				$params['orderId'] &&
				cmodule::includeModule('sale')
			){
				$orderSelf = CSaleOrder::GetById($params['orderId']);
				$date = $orderSelf['DATE_INSERT'];
			}else
				$date = false;
			$itog = CCurrencyRates::ConvertCurrency($params['SUM'],$from,$into,$date);
			if($params['FORMAT'])
				$itog = CCurrencyLang::CurrencyFormat($itog,$params['TO'],true);
			if(array_key_exists('isdek_action',$params) && $params['isdek_action'] == __function__)
				echo json_encode(self::zajsonit(array('VALUE' => $itog, 'WHERE' => $params['WHERE'])));
			else
				return $itog;
		} 
	}

	// LEGACY
	function countAlltarifs($arParams){
		return array();
	}
	function htmlTaritfList($params){
		echo "Update the module";
	}
}
?>