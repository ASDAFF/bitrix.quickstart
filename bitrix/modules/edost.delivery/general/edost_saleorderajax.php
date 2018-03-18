<?
class CEdostModifySaleOrderAjax {

	// проверка наличия в заказе доставки и наложенного платежа edost
	function CheckOrderDevileryEdostAndEdostPayCod($arOrder) {
//		echo "<pre>".print_r($arOrder, true)."</pre>"; die();

		if (isset($arOrder['PAY_SYSTEM_ID']) && isset($arOrder['PERSON_TYPE_ID']) && isset($arOrder['DELIVERY_ID']) && substr($arOrder['DELIVERY_ID'], 0, 6) == 'edost:') {
			$dbPaySystem = CSalePaySystem::GetList(array('SORT' => 'ASC', 'PSA_NAME' => 'ASC'), array('ACTIVE' => 'Y', 'PERSON_TYPE_ID' => $arOrder['PERSON_TYPE_ID'], 'PSA_HAVE_PAYMENT' => 'Y'));
			while ($arPaySystem = $dbPaySystem->Fetch()) if ($arPaySystem['ID'] == $arOrder['PAY_SYSTEM_ID']) {
				if (substr($arPaySystem['PSA_ACTION_FILE'], -11) == 'edostpaycod') return true;
				break;
			}
		}

		return false;

	}


	// вызывается после обработки платежной системы при расчете заказа в DoCalculateOrder
	function OnSCCalculateOrderPaySystem(&$arOrder) {
//		$_SESSION['EDOST']['help']['arOrder'] = $arOrder;

		if (!(CEdostModifySaleOrderAjax::CheckOrderDevileryEdostAndEdostPayCod($arOrder) && class_exists(CDeliveryEDOST))) return;

		$id = explode(':', $arOrder['DELIVERY_ID']);
		$id = intval($id[1]);
		if ($id == 0) return;

		$tariff = CDeliveryEDOST::GetEdostTariff($id);
		if (isset($tariff['pricecash']) && $tariff['pricecash'] >= 0) {
			$base_currency = CDeliveryEDOST::GetRUB();
			$arOrder['DELIVERY_PRICE'] = roundEx(CCurrencyRates::ConvertCurrency($tariff['pricecash'], $base_currency, $arOrder['CURRENCY']), SALE_VALUE_PRECISION);
		}
		else {
//			$arOrder['DELIVERY_ID'] = false;
			$arOrder['DELIVERY_PRICE'] = 0;
		}

		$arOrder['PRICE_DELIVERY'] = $arOrder['DELIVERY_PRICE'];

	}


	// отмена отправки письма с напоминанием об оплате заказа, если выбран наложенный платеж edost
	function OnSCOrderRemindSendEmail($OrderID, &$eventName, &$arFields)
	{

		if ($eventName == 'SALE_ORDER_REMIND_PAYMENT') {
			$arOrder = CSaleOrder::GetByID($OrderID);
			if (CEdostModifySaleOrderAjax::CheckOrderDevileryEdostAndEdostPayCod($arOrder)) return false;
		}

		return true;

	}


	// установка статуса нового заказа, если выбран наложенный платеж edost
	function OnSCBeforeOrderAdd(&$arOrder)
	{
//		echo "<br><pre>".print_r($arOrder, true)."</pre>"; die();

		if (CEdostModifySaleOrderAjax::CheckOrderDevileryEdostAndEdostPayCod($arOrder)) {
			$status = COption::GetOptionString('edost.delivery', 'codstatus', '');
			if ($status != '') $arOrder['STATUS_ID'] = $status;
		}

	}


	// сохранение данных по выбранному через модуль упаковки ящику (после подтверждения заказа)
	function OnSCOrderOneStepComplete($ID, $arOrder)
	{
//		echo "<br>ID: <b>$ID</b><pre>"; print_r($arOrder); echo "</pre>"; die;

		// модуль упаковки (есть и активен)
		if (class_exists(CEdostPackage) && class_exists(CDeliveryEDOST) && COption::GetOptionString("edost.package", "package_activate", 'Y') == 'Y') {
			$ORDER_ID = $arOrder['ID'];

			$db_props = CSaleOrderProps::GetList(array("SORT" => "ASC"), array("PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"], "CODE" => "PACKAGE"));
			if ($arProps = $db_props->Fetch()) {
				$new_value = CDeliveryEDOST::GetEdostPackage();
				$arFields = array(
					"ORDER_ID" => $ORDER_ID,
					"ORDER_PROPS_ID" => $arProps["ID"],
					"NAME" => $arProps['NAME'],
					"CODE" => $arProps['CODE'],
					"VALUE" => $new_value
				);
				CSaleOrderPropsValue::Add($arFields);
			}
		}

	}


	// вызывается перед расчетом доставки
	function OnSCOrderOneStepOrderPropsHandler(&$arResult, &$arUserResult)
	{
//		echo '<br><pre style="font-size: 12px">'; print_r($arResult); echo "</pre>";
//		echo '<br><pre style="font-size: 12px">'; print_r($arUserResult); echo "</pre>";

        $arResult['edost']['delivery_number'] = 0;
		$arResult['edost']['office'] = array(29, 30, 36, 9, 37, 46); // тарифы с блокировкой поля 'ADDRESS'
		$arResult['edost']['delivery_edost_id'] = '';
		$arResult['edost']['delivery_id'] = isset($arUserResult['DELIVERY_ID']) ? $arUserResult['DELIVERY_ID'] : '';
		$arResult['edost']['error'] = isset($arResult['ERROR']) ? $arResult['ERROR'] : array();

		// настройки модуля eDost
		$ar = array('show_pickpoint_map', 'hide_err', 'show_msg', 'send_zip', 'hide_payment', 'sort_ascending');
		$arResult['edost']['config'] = array();
		foreach ($ar as $v) $arResult['edost']['config'][$v] = COption::GetOptionString('edost.delivery', $v, '');
//		echo '<br><pre style="font-size: 12px">'; print_r($arResult['edost']); echo "</pre>";

		// получение ZIP (в битрикс ошибка и при первой загрузке не заполняется поле $arUserResult["DELIVERY_LOCATION_ZIP"]) !!!
		if ($arResult['edost']['config']['send_zip'] == 'Y' && $arUserResult['DELIVERY_LOCATION_ZIP'] === false) {
			$db_props = CSaleOrderProps::GetList(array('SORT' => 'ASC'), array('PERSON_TYPE_ID' => $arUserResult['PERSON_TYPE_ID'], 'CODE' => 'ZIP', 'IS_ZIP' => 'Y'));
			if ($arProps = $db_props->Fetch()) {
				$dbUserPropsValues = CSaleOrderUserPropsValue::GetList(array('SORT' => 'ASC'), array('USER_PROPS_ID' => $arUserResult['PROFILE_ID'], 'ORDER_PROPS_ID' => $arProps['ID']), false, false, array('VALUE'));
				if ($arUserPropsValues = $dbUserPropsValues->Fetch()) $arUserResult['DELIVERY_LOCATION_ZIP'] = $arUserPropsValues['VALUE'];
			}
		}

	}


	// вызывается после расчета доставки
	function OnSCOrderOneStepDeliveryHandler(&$arResult, &$arUserResult)
	{
//		echo '<br>DELIVERY:<br> <pre style="font-size: 12px">'; print_r($arResult["DELIVERY"]); echo "</pre>";

		// загрузка констант модуля eDost
		$arResult['edost']['sort_id'] = array(29,36,43,37,38,31,32,33,34,35);
		$arResult['edost']['delivery_pricelist'] = false;
		if (class_exists(CDeliveryEDOST)) {
			if (defined('DELIVERY_EDOST_SORT')) $arResult['edost']['sort_id'] = explode(',', DELIVERY_EDOST_SORT);
			if (defined('DELIVERY_EDOST_PRICELIST')) $arResult['edost']['delivery_pricelist'] = (DELIVERY_EDOST_PRICELIST == 'Y' ? true : false);
		}


		if (empty($arResult["DELIVERY"])) return;


		// сортировка тарифов eDost
		if (isset($arResult["DELIVERY"]["edost"]) && class_exists(CDeliveryEDOST)) {
			if ($arResult['edost']['config']['sort_ascending'] == 'Y') {
				// по возрастанию
				$edost_delivery_sort = array();
				$n = count($arResult["DELIVERY"]["edost"]["PROFILES"]);
				for ($i = 0; $i < $n; $i++) {
					$k = -1;
					$p = -1;
					foreach ($arResult["DELIVERY"]["edost"]["PROFILES"] as $profile_id => $arProfile) {
						$edost_tariff = CDeliveryEDOST::GetEdostTariff($profile_id);
						$price = ($edost_tariff['priceinfo'] > 0 ? $edost_tariff['priceinfo'] + $edost_tariff['price'] : $edost_tariff['price']);
						if ($p == -1 || $price < $p) {
							$k = $profile_id;
							$p = $price;
						}
					}
					if ($k != -1) {
						$edost_delivery_sort[$k] = $arResult["DELIVERY"]["edost"]["PROFILES"][$k];
						unset($arResult["DELIVERY"]["edost"]["PROFILES"][$k]);
					}
   	         	}
   	         	$arResult["DELIVERY"]["edost"]["PROFILES"] = $edost_delivery_sort;
            }
            else {
				// по кодам
				$edost_delivery = $arResult["DELIVERY"]["edost"]["PROFILES"];
				$edost_delivery_sort = array();
				for ($i = 0; $i < count($arResult['edost']['sort_id']); $i++) {
					for ($i2 = 1; $i2 >= 0; $i2--) {
						$key = $arResult['edost']['sort_id'][$i]*2 - $i2;

						if (is_array($edost_delivery[$key])) {
							$edost_delivery_sort[$key] = $edost_delivery[$key];
							unset($edost_delivery[$key]);
						}
					}
				}
				if (count($edost_delivery_sort) > 0) $arResult["DELIVERY"]["edost"]["PROFILES"] = $edost_delivery_sort + $edost_delivery;
			}
		}


		// количество тарифов доставки
		foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
			if ($delivery_id !== 0 && intval($delivery_id) <= 0) {
				foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile) $arResult['edost']['delivery_number']++;
			}
			else {
				$arResult['edost']['delivery_number']++;
			}


		// удаление тарифа "стоимость доставки будет предоставлена позже", если есть другие способы доставки
		if (isset($arResult["DELIVERY"]["edost"]["PROFILES"]["0"]) && $arResult['edost']['delivery_number'] > 1 && $arResult['edost']['config']['hide_err'] == 'Y') {
			$arResult['edost']['delivery_number']--;
			unset($arResult["DELIVERY"]["edost"]);
		}


		// поиск первой и выделенной доставки
		$delivery_id_now = array('id' => '-1', 'profile' => '-1');
		$delivery_id_first = $delivery_id_now;
		$delivery_id_post = $arResult['edost']['delivery_id'];
		foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
			if ($delivery_id !== 0 && intval($delivery_id) <= 0) {
				foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile) {
					if ($delivery_id_first['id'] == -1) $delivery_id_first = array('id' => $delivery_id, 'profile' => $profile_id);

					if (isset($arProfile["CHECKED"]) && $arProfile["CHECKED"] == 'Y') {
						$delivery_id_now = array('id' => $delivery_id, 'profile' => $profile_id);

						if ($delivery_id_post != $delivery_id.':'.$profile_id) $delivery_id_post = '';
					}
				}
			}
			else {
				if ($delivery_id_first['id'] == -1) $delivery_id_first = array('id' => $delivery_id, 'profile' => '-1', 'module_id' => $arDelivery["ID"]);

				if (isset($arDelivery["CHECKED"]) && $arDelivery["CHECKED"] == 'Y') {
					$delivery_id_now = array('id' => $delivery_id, 'profile' => '-1', 'module_id' => $arDelivery["ID"]);

					if ($delivery_id_post != $arDelivery["ID"]) $delivery_id_post = '';
				}
			}
		if ( $delivery_id_now['id'] != '-1') $arResult['edost']['delivery_id_now'] = $delivery_id_now;
//		echo '<br>delivery_number: '.$arResult['edost']['delivery_number'];
//		echo '<br>delivery_id_now: '.$delivery_id_now['id'].':'.$delivery_id_now['profile'].' ('.$delivery_id_now['module_id'].')';
//		echo '<br>delivery_id_first: '.$delivery_id_first['id'].':'.$delivery_id_first['profile'].' ('.$delivery_id_first['module_id'].')';


		// выделить первую, если перед расчетом доставки ничего не было выделено, не найдено выделенной или магазин изменил выделение
		if ( $delivery_id_first['id'] != '-1' && ($delivery_id_post == '' || $delivery_id_now['id'] == '-1') && !($delivery_id_now['id'] == $delivery_id_first['id'] && $delivery_id_now['profile'] == $delivery_id_first['profile']) ) {
			if (isset($arResult['edost']['error'])) $arResult["ERROR"] = $arResult['edost']['error'];

	        $arResult['edost']['delivery_id_now'] = $delivery_id_first;

			// сброс выделения
			if ($delivery_id_now['profile'] != '-1') unset($arResult["DELIVERY"][$delivery_id_now['id']]["PROFILES"][$delivery_id_now['profile']]["CHECKED"]);
			else if ($delivery_id_now['id'] != '-1') unset($arResult["DELIVERY"][$delivery_id_now['id']]["CHECKED"]);
			$arUserResult["DELIVERY_ID"] = '';

			// выделение первой доставки и пересчет стоимости
			if ($delivery_id_first['profile'] != '-1') {
				$arResult["DELIVERY"][$delivery_id_first['id']]["PROFILES"][$delivery_id_first['profile']]["CHECKED"] = 'Y';
				$arUserResult["DELIVERY_ID"] = $delivery_id_first['id'].':'.$delivery_id_first['profile'];

				$arOrderTmpDel = array(
					"PRICE" => $arResult["ORDER_PRICE"],
					"WEIGHT" => $arResult["ORDER_WEIGHT"],
					"LOCATION_FROM" => COption::GetOptionInt('sale', 'location'),
					"LOCATION_TO" => $arUserResult["DELIVERY_LOCATION"],
					"LOCATION_ZIP" => $arUserResult["DELIVERY_LOCATION_ZIP"],
				);

				// новые параметры битрикс 14
				$ar = array('MAX_DIMENSIONS' => 'MAX_DIMENSIONS', 'DIMENSIONS' => 'ORDER_DIMENSIONS', 'ITEMS_DIMENSIONS' => 'ITEMS_DIMENSIONS', 'ITEMS' => 'BASKET_ITEMS');
				foreach ($ar as $key => $v) if (isset($arResult[$v])) $arOrderTmpDel[$key] = $arResult[$v];

				$arDeliveryPrice = CSaleDeliveryHandler::CalculateFull($delivery_id_first['id'], $delivery_id_first['profile'], $arOrderTmpDel, $arResult["BASE_LANG_CURRENCY"]);

				if ($arDeliveryPrice["RESULT"] == "ERROR") $arResult["ERROR"][] = $arDeliveryPrice["TEXT"];
				else $arResult["DELIVERY_PRICE"] = roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);
			}
			else {
				$arResult["DELIVERY"][$delivery_id_first['id']]["CHECKED"] = 'Y';
				$arUserResult["DELIVERY_ID"] = $delivery_id_first['module_id'];

				$arDeliv = CSaleDelivery::GetByID($delivery_id_first['module_id']);
				$arResult["DELIVERY_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arDeliv["PRICE"], $arDeliv["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
			}

			if(DoubleVal($arResult["DELIVERY_PRICE"]) > 0) $arResult["DELIVERY_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DELIVERY_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
		}

	}


	// вызывается после обработки платежных систем
	function OnSCOrderOneStepPaySystemHandler(&$arResult, &$arUserResult)
	{
//		echo '<br><b>DELIVERY:</b><pre style="font-size: 12px">'; print_r($arResult["DELIVERY"]); echo "</pre>"; echo '<br><b>PAY_SYSTEM:</b><pre style="font-size: 12px">'; print_r($arResult["PAY_SYSTEM"]); echo "</pre>";
//		echo '<br><b>USER_PROPS_N:</b><pre style="font-size: 12px">'; print_r($arResult["ORDER_PROP"]["USER_PROPS_N"]); echo "</pre>";

		$arResult['edost']['javascript'] = '';

		// warning (предупреждения модуля edost)
		if (class_exists(CDeliveryEDOST)) {
			$warning = CDeliveryEDOST::GetEdostWarning();
			if ($warning != '') {
				$arResult['edost']['warning'] = '<span id="edost_warning" style="color: #FF0000; font-weight: bold;">'.$warning.'</span>';

				// вывод ошибки при подтверждении заказа, если перед оформлением была выбрана почта (наземная) и есть warning по индексу
				if ($arUserResult["CONFIRM_ORDER"] == 'Y')
					if ($arResult['edost']['delivery_id'] == 'edost:3' || $arResult['edost']['delivery_id'] == 'edost:4') $arResult["ERROR"][] = $warning;
			}
		}


		// поле ADDRESS (для сохранения данных по выбранному пункту выдачи)
		$address_id = -1;
		$address = '';
		$address_type = '';
		$address_readonly = false;
		foreach ($arResult["ORDER_PROP"]["USER_PROPS_Y"] as $key => $arProps)
			if ($arProps["CODE"] == 'ADDRESS' || ToUpper($arProps["CODE"]) == 'ADDRESS') {
				$address_type = $arProps['TYPE'];
				if ($address_type == 'TEXT' || $address_type == 'TEXTAREA') {
					$address_id = $key;
					$address = $arProps['VALUE'];
				}
				break;
			}
		if ($address_id != -1) $arResult['edost']['javascript'] .= '<input type="hidden" value="ORDER_PROP_'.$address_id.'" id="address_input">';


		// загрузка выбранного офиса из POST
		$edost_office = array(-1, -1);
		if (isset($_REQUEST['edost_office']) && $_REQUEST['edost_office'] != '') {
			$r = explode('-', substr($_REQUEST['edost_office'], 0, 5));
			if (isset($r[0]) && $r[0] >= 0 && isset($r[1]) && $r[1] >= 0) $edost_office = $r;
		}


		$base_currency = (class_exists(CDeliveryEDOST) ? CDeliveryEDOST::GetRUB() : 'RUB');
		if (is_array($arResult['DELIVERY']['edost']) && class_exists(CDeliveryEDOST)) {
			foreach ($arResult['DELIVERY']['edost']['PROFILES'] as $profile_id => $arProfile) {
				$edost_tariff = CDeliveryEDOST::GetEdostTariff($profile_id);
				$id = ceil($profile_id / 2);

//				echo '<br><pre style="font-size: 12px">'; print_r($arProfile); echo "</pre>";
//				echo '<br><pre style="font-size: 12px">'; print_r($edost_tariff); echo "</pre>";
//				echo '<br><pre style="font-size: 12px">'; print_r($edost_tariff['office']); echo "</pre>";

				// доставка оплачивается магазину (включается в итого)
				$price = $edost_tariff['price'];
				if ($profile_id == 0) $price = '';
				else if ($price == 0) $price = GetMessage('SALE_DH_EDOST_FREE');
				else $price = SaleFormatCurrency(roundEx(CCurrencyRates::ConvertCurrency($edost_tariff['price'], $base_currency, $arResult['BASE_LANG_CURRENCY']), SALE_VALUE_PRECISION), $arResult['BASE_LANG_CURRENCY']);

				// доставка оплачивается самостоятельно при получении (НЕ включается в итого)
                if ($edost_tariff['priceinfo'] > 0) {
					$price_info = SaleFormatCurrency(roundEx(CCurrencyRates::ConvertCurrency($edost_tariff['priceinfo'], $base_currency, $arResult['BASE_LANG_CURRENCY']), SALE_VALUE_PRECISION), $arResult['BASE_LANG_CURRENCY']);
					$arResult['DELIVERY']['edost']['PROFILES'][$profile_id]['priceinfo'] = $price_info;

					$s = $arResult['DELIVERY']['edost']['PROFILES'][$profile_id]['DESCRIPTION'];
					$s1 = str_replace('%price_info%', $price_info, GetMessage('SALE_DH_EDOST_PRICE_INFO'));
					$s2 = ($edost_tariff['price'] > 0 ? str_replace('%price%', $price, GetMessage('SALE_DH_EDOST_PRICE_INFO2')) : '');
					$arResult['DELIVERY']['edost']['PROFILES'][$profile_id]['DESCRIPTION'] = $s1 . ($s1 != '' && $s2 != '' ? '<br>' : '') . $s2 . (($s1 != '' || $s2 != '') && $s != '' ? '<br>' : '') . $s;

					$arResult['DELIVERY']['edost']['PROFILES'][$profile_id]['price_backup'] = $price;
					$price = '';
				}

				$arResult['DELIVERY']['edost']['PROFILES'][$profile_id]['price'] = $price;

                // срок доставки
				$arResult['DELIVERY']['edost']['PROFILES'][$profile_id]['day'] = $edost_tariff['day'];


				if ($address_id == -1) continue;


				// PickPoint
				if ($arResult['edost']['config']['show_pickpoint_map'] == 'Y' && ($id == 29 || $id == 30)) {
					$pickpointmap = $edost_tariff['pickpointmap'];

					if ($edost_office[0] != $id && isset($_SESSION['EDOST']['address'][$id]) && isset($_SESSION['EDOST']['delivery_edost_id']) && $_SESSION['EDOST']['delivery_edost_id'] != $id) $office_address = $_SESSION['EDOST']['address'][$id];
					else $office_address = $address;

					$s = '';
					if (isset($_SESSION['EDOST']['location']) && $_SESSION['EDOST']['location'] != $arUserResult['DELIVERY_LOCATION']) $_SESSION['EDOST']['address'][$id] = '';
					else {
						if (strpos($office_address, GetMessage('SALE_DH_EDOST_OFFICE_HEAD_PICKPOINT')) === 0)
							$s = str_replace(GetMessage('SALE_DH_EDOST_OFFICE_HEAD_PICKPOINT'), GetMessage('SALE_DH_EDOST_OFFICE_TITLE3'), $office_address);
						else if (strpos($office_address, GetMessage('SALE_DH_EDOST_OFFICE_HEAD_PICKPOINT2')) === 0)
							$s = str_replace(GetMessage('SALE_DH_EDOST_OFFICE_HEAD_PICKPOINT2'), GetMessage('SALE_DH_EDOST_OFFICE_TITLE5'), $office_address);
					}

					if ($s != '') {
						$s = str_replace(', '.GetMessage('SALE_DH_EDOST_PICKPOINT_ADDR'), '<br>'.GetMessage('SALE_DH_EDOST_PICKPOINT_ADDR'), $s);
						$_SESSION['EDOST']['address'][$id] = $office_address;
					}
					else $arResult['DELIVERY']['edost']['PROFILES'][$profile_id]['onclick'] = "PickPoint.open(EdostPickPoint,{city:'".$pickpointmap."', ids:null}); edost_SubmitActive('set'); submitForm();";

					if ($arProfile['CHECKED'] == 'Y') {
						$address_readonly = true;
						if ($s != '') $edost_address = $office_address;
						else {
							$edost_address = '';
							$s = GetMessage('SALE_DH_EDOST_PICKPOINT');
						}
					}

					if ($s != '') $arResult['DELIVERY']['edost']['PROFILES'][$profile_id]['office'] = '<a style="color: rgb(221, 0, 0); text-decoration: none;" href="#" id="EdostPickPointRef" onclick="PickPoint.open(EdostPickPoint,{city:\''.$pickpointmap.'\', ids:null}); return false;">'.$s.'</a>';
				}


				// офисы
				$office_number = isset($edost_tariff['office']) ? count($edost_tariff['office']) : 0;
				if ($office_number > 0) {
					if (isset($_SESSION['EDOST']['address'][$id]) && isset($_SESSION['EDOST']['delivery_edost_id']) && $_SESSION['EDOST']['delivery_edost_id'] != $id) $office_address = $_SESSION['EDOST']['address'][$id];
					else $office_address = $address;

					if ($id == 36) $office_head = GetMessage('SALE_DH_EDOST_OFFICE_HEAD_BOXBERRY');
					else if ($id == 9 || $id == 37) $office_head = GetMessage('SALE_DH_EDOST_OFFICE_HEAD_CDEK');
					else if ($id == 46) $office_head = GetMessage('SALE_DH_EDOST_OFFICE_HEAD_DPD');
					else $office_head = GetMessage('SALE_DH_EDOST_OFFICE_HEAD');

					if (in_array($id, array(36, 9, 37, 46))) $s = GetMessage('SALE_DH_EDOST_OFFICE_TITLE3'); // до пункта выдачи
					else $s = GetMessage('SALE_DH_EDOST_OFFICE_TITLE2'); // до склада
					$s = '<td>'.$s.':</td><td style="padding-left: 5px;">';

					if ($office_number != 1) $s .= '<select id="edost_office_'.$profile_id.'" onchange="edost_SetOffice('.$profile_id.');">';
					foreach ($edost_tariff['office'] as $office_id => $office) {
						$office_data = $office_head.($office['code'] != '' ? GetMessage('SALE_DH_EDOST_OFFICE_CODE').$office['code'] : '').', '.
							GetMessage('SALE_DH_EDOST_OFFICE_ADDRESS').$office['address'].', '.
							GetMessage('SALE_DH_EDOST_OFFICE_TEL').$office['tel'].', '.
							GetMessage('SALE_DH_EDOST_OFFICE_SCHEDULE').$office['schedule'];

						$s2 = $id.'-'.$office_id.'|'.$office['id']; //.'|'.$office_data;

						if ($office_id == 0) $office_data_now = $office_data;

						if ($office_number == 1) {
							$s .= '<b>'.$office['name'].'</b>'.'<input type="hidden" id="edost_office_'.$profile_id.'" value="'.$s2.'">';
						}
						else {
							if ( ($edost_office[0] < 0 && strpos($office_address, $office['address']) > 0) || ($edost_office[0] == $id && $edost_office[1] == $office_id) ) {
								$v = 'selected="selected"';
	                            $office_data_now = $office_data;
							}
							else $v = '';
							$s .= '<option '.$v.' value="'.$s2.'">'.$office['name'].'</option>';
						}
					}
					if ($office_number != 1) $s .= '</select>';

					$s .= '</td>';

					if ($arProfile['CHECKED'] == 'Y') {
						$address_readonly = true;
						$edost_address = $office_data_now;
						$_SESSION['EDOST']['address'][$id] = $office_data_now;
					}

					// ссылка на карту
					if (isset($edost_tariff['office'][0]['id']) && $edost_tariff['office'][0]['id'] != '')
						$s .= '<td style="padding-left: 10px;"><a href="#" style="cursor: pointer; text-decoration: none; font-size: 11px;" onclick="edost_OpenMap('.$profile_id.'); return false;" >'.GetMessage("SALE_DH_EDOST_OFFICE_MAP").'</a></td>';

					$s = '<table class="edost_office_table" style="display: inline; margin: 0px;" border="0" cellspacing="0" cellpadding="0"><tr style="padding: 0px; margin: 0px;">'.$s.'</tr></table>';

					$arResult['DELIVERY']['edost']['PROFILES'][$profile_id]['onclick'] = 'edost_SetOffice('.$profile_id.');';
					$arResult['DELIVERY']['edost']['PROFILES'][$profile_id]['office'] = $s;
				}


				if ($arProfile['CHECKED'] == 'Y') $arResult['edost']['delivery_edost_id'] = $id;
			}


            // сброс выборанной доставки и обнуление ее стоимости в заказе (режим прайслиста, без возможности выбора доставки)
			if ($arResult['edost']['delivery_pricelist']) {
				$arUserResult['DELIVERY_ID'] = '';

				$arResult['DELIVERY_PRICE'] = 0;
				$arResult['DELIVERY_PRICE_FORMATED'] = '';

				if (isset($arResult['edost']['delivery_id_now'])) {
					$delivery_id_now = $arResult['edost']['delivery_id_now'];
					if ($delivery_id_now['profile'] != '-1') unset($arResult['DELIVERY'][$delivery_id_now['id']]['PROFILES'][$delivery_id_now['profile']]['CHECKED']);
					else if ($delivery_id_now['id'] != '-1') unset($arResult['DELIVERY'][$delivery_id_now['id']]['CHECKED']);
                }
			}


			// изменение названия тарифа "стоимость доставки будет предоставлена позже" и вывод ошибки
			if (isset($arResult['DELIVERY']['edost']['PROFILES'][0])) {
				$edost_tariff = CDeliveryEDOST::GetEdostTariff(0);

				if (isset($edost_tariff['new_name']) && $edost_tariff['new_name'] != '')
					$arResult['DELIVERY']['edost']['PROFILES'][0]['TITLE'] = $edost_tariff['new_name'];

			 	if ($arResult['edost']['config']['hide_err'] != 'Y' && $edost_tariff['error'] != '')
			 		$arResult['DELIVERY']['edost']['PROFILES'][0]['TITLE'] .= '<br><font color="#FF0000">'.$edost_tariff['error'].'</font>';
			}
		}

//		echo '<br><pre style="font-size: 12px">'; print_r($arResult["DELIVERY"]); echo "</pre>";

        // сохранение и восстановление адреса из сессии
        $id_last = (isset($_SESSION['EDOST']['delivery_edost_id']) && $_SESSION['EDOST']['delivery_edost_id'] > 0) ? $_SESSION['EDOST']['delivery_edost_id'] : 0;
        $id = (isset($arResult['edost']['delivery_edost_id']) && $arResult['edost']['delivery_edost_id'] > 0) ? $arResult['edost']['delivery_edost_id'] : 0;

        if (in_array($id_last, $arResult['edost']['office']) ||
        	strpos($address, GetMessage('SALE_DH_EDOST_OFFICE_HEAD_BOXBERRY')) === 0 ||
        	strpos($address, GetMessage('SALE_DH_EDOST_OFFICE_HEAD_CDEK')) === 0 ||
        	strpos($address, GetMessage('SALE_DH_EDOST_OFFICE_HEAD_PICKPOINT')) === 0 ||
			strpos($address, GetMessage('SALE_DH_EDOST_OFFICE_HEAD_PICKPOINT2')) === 0) {

			if (!in_array($id, $arResult['edost']['office'])) $edost_address = isset($_SESSION['EDOST']['address'][0]) ? $_SESSION['EDOST']['address'][0] : '';
		}
		else $_SESSION['EDOST']['address'][0] = $address;

		// сохранение нового адреса в поле ADDRESS
		if ($address_id != -1 && isset($edost_address)) {
			$arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id]['VALUE'] = $edost_address;
			$address = $edost_address;
		}


		$_SESSION['EDOST']['delivery_edost_id'] = $arResult['edost']['delivery_edost_id']; // активная служба доставки
		$_SESSION['EDOST']['location'] = $arUserResult['DELIVERY_LOCATION']; // текущее местоположение доставки


		// запретить выбор доставки, если есть только одна служба
		if ($arResult['edost']['delivery_number'] == 1) $arResult['edost']['delivery_pricelist'] = true;


		// добавление наценок наложенного платежа
		foreach ($arResult['PAY_SYSTEM'] as $key => $arPaySystem) {
			// наложенный платеж edost (название обработчика заканчивается на 'edostpaycod')
			if (substr($arPaySystem['PSA_ACTION_FILE'], -11) != 'edostpaycod') continue;

			$id_delivery_edost = (substr($arUserResult['DELIVERY_ID'], 0, 6) == 'edost:' ? substr($arUserResult['DELIVERY_ID'], 6, 3) : 0);
			$edost_tariff = ($id_delivery_edost > 0 && class_exists(CDeliveryEDOST) ? CDeliveryEDOST::GetEdostTariff($id_delivery_edost) : '');

			// удаление наложенного платежа, если он недоступен для выбранного способа доставки
			if ($id_delivery_edost == 0 || !isset($edost_tariff['pricecash']) || $edost_tariff['pricecash'] == -1) {
				unset($arResult['PAY_SYSTEM'][$key]);
				break;
            };

			$edost_id = ceil($id_delivery_edost / 2);

			$ar = GetMessage('SALE_DH_EDOST_NALOZ_DATA');
			if (is_array($ar)) foreach ($ar as $v) if (in_array($edost_id, $v['tariff'])) {
				if (isset($v['name'])) $arPaySystem['PSA_NAME'] = $v['name'];
				if (isset($v['description'])) $arPaySystem['DESCRIPTION'] = $v['description'];

				if ($edost_id == 29 && isset($v['description2']) && strpos($address, GetMessage('SALE_DH_EDOST_OFFICE_HEAD_PICKPOINT2')) === 0) $arPaySystem['DESCRIPTION'] = $v['description2'];
			}

			$shipping_price = ($edost_tariff['pricecash'] > 0 ? $edost_tariff['pricecash'] - $edost_tariff['price'] : 0);

			$arPaySystem['DESCRIPTION'] .= ($shipping_price == 0) ? '' : (($arPaySystem['DESCRIPTION'] == '') ? "" : "<br />").GetMessage('SALE_DELIV_EDOST_NALOZ_PLUS')." <b>".SaleFormatCurrency( roundEx(CCurrencyRates::ConvertCurrency($shipping_price, $base_currency, $arResult['BASE_LANG_CURRENCY']), SALE_VALUE_PRECISION), $arResult['BASE_LANG_CURRENCY'])."</b>";

			if ($edost_tariff['transfer'] > 0) $arPaySystem['DESCRIPTION'] .=
				(($arPaySystem['DESCRIPTION'] == '') ? "" : "<br />").
				"<font color=\"#FF0000\">".GetMessage("SALE_DELIV_EDOST_NALOZ_TRANSFER").
				"<b>".SaleFormatCurrency(roundEx(CCurrencyRates::ConvertCurrency($edost_tariff['transfer'], $base_currency, $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION), $arResult["BASE_LANG_CURRENCY"])."</b></font>".
				"<br>".GetMessage("SALE_DELIV_EDOST_NALOZ_TOTAL")."<b>".SaleFormatCurrency(roundEx(CCurrencyRates::ConvertCurrency($edost_tariff['transfer'] + $shipping_price, $base_currency, $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION), $arResult["BASE_LANG_CURRENCY"])."</b>";

			// добавление наценки за наложенный платеж (в стоимоть доставки и итоговую стоимость заказа)
			if ($arPaySystem['CHECKED'] == 'Y') {
				$arResult['DELIVERY_PRICE'] += roundEx(CCurrencyRates::ConvertCurrency($shipping_price, $base_currency, $arResult['BASE_LANG_CURRENCY']), SALE_VALUE_PRECISION);
				$arResult['DELIVERY_PRICE_FORMATED'] = SaleFormatCurrency($arResult['DELIVERY_PRICE'], $arResult['BASE_LANG_CURRENCY']);
			}

			$arResult['PAY_SYSTEM'][$key] = $arPaySystem;
		}


		// выделение первого способа оплаты, если нет ни одного выделенного
		$id = -1;
		foreach ($arResult['PAY_SYSTEM'] as $key => $arPaySystem) {
			if ($id == -1) $id = $key;
			if ($arPaySystem["CHECKED"] == 'Y') {
				$id = -1;
				break;
			}
		}
		if ($id != -1) {
			$arResult['PAY_SYSTEM'][$id]['CHECKED'] = 'Y';
			$arUserResult['PAY_SYSTEM_ID'] = $id;
		}


		// удалить все способы оплаты, если нет способов доставки
		if ($arResult['edost']['config']['hide_payment'] == 'Y' && $arResult['edost']['delivery_number'] <= 0) $arResult['PAY_SYSTEM'] = array();




		// javascript - офисы
		if ($address_id != -1) $arResult['edost']['javascript'] .= '
		<input type="hidden" value="" id="edost_office" name="edost_office">

		<script type="text/javascript">
			function edost_OpenMap(n, id) {
				var E = document.getElementById("edost_office_" + n);
				if (E) {
					var office = E.value.split("|");
					if (office[1] != undefined) window.open("http://www.edost.ru/office.php?c=" + office[1], "_blank");
				}
			}

			function edost_SetOffice(n) {
				var E = document.getElementById("edost_office_" + n);
				if (E) {
					var office = E.value.split("|");
					if (office[0] != undefined) {
						var E2 = document.getElementById("edost_office");
						if (E2) E2.value = office[0];
					}

					if (document.getElementById("ID_DELIVERY_edost_" + n).checked) submitForm();
				}
			}
		</script>';


		// javascript - PickPoint
		if ($arResult['edost']['config']['show_pickpoint_map'] == 'Y' && $address_id != -1) $arResult['edost']['javascript'] .= '
		<input type="hidden" value="" id="edost_submit_active">

		<script type="text/javascript">
			function edost_SubmitActive(n) {
				var E = document.getElementById("edost_submit_active");
				if (E) {
					if (n == "set") E.value = "Y";
					else if (E.value == "Y") return true; else return false;
				}
			}

			function EdostPickPoint(rz) {
				if (edost_SubmitActive("get") == true) return false;

//				alert(rz[\'name\'] + "  |  " + rz[\'id\'] + "  |  " + rz[\'address\']);

				var s = "";
				if (rz[\'name\'].substr(0, 3) == "'.GetMessage("SALE_DH_EDOST_PICKPOINT_IDCODE").'") s = "'.GetMessage("SALE_DH_EDOST_OFFICE_HEAD_PICKPOINT").' "; else s = "'.GetMessage("SALE_DH_EDOST_OFFICE_HEAD_PICKPOINT2").' ";

				var s2 = rz[\'name\'];
				var i = s2.indexOf(":");
				if (i > 0) s2 = s2.substr(i + 1).replace(/^\s+/g, "");
//				if (s2.substr(0, 3) == "'.GetMessage("SALE_DH_EDOST_PICKPOINT_IDCODE").'") s2 = s2.substr(5);
//				if (s2.substr(0, 9) == "'.GetMessage("SALE_DH_EDOST_PICKPOINT_IDP").'") s2 = s2.substr(10);

				rz[\'name\'] = s + s2 + ", '.GetMessage("SALE_DH_EDOST_PICKPOINT_CODE").': " + rz[\'id\'];

				var i = rz[\'address\'].indexOf("'.GetMessage("SALE_DH_EDOST_RUSSIAN_FEDERATION_L").'");
				if (i > 0) rz[\'address\'] = rz[\'address\'].substr(i + 20 + 2);
				rz[\'address\'] = "'.GetMessage("SALE_DH_EDOST_PICKPOINT_ADDR").' " + rz[\'address\'];

				document.getElementById(document.getElementById("address_input").value).'.($address_type == 'TEXTAREA' ? 'innerHTML' : 'value').' = rz[\'name\'] + ", " + rz[\'address\'];

				var E = document.getElementById("EdostPickPointRef");
				if (E) E.innerHTML = "'.GetMessage("SALE_DH_EDOST_PICKPOINT_WAIT").'";

				var E = document.getElementById("edost_office");
				if (E) E.value = "29-1";

				var E = document.getElementById("ID_DELIVERY_edost_57");
				if (E) if (!E.checked) E.checked = "checked";

		        submitForm();
			}
		</script>';


		// javascript - блокировка поля ADDRESS для PickPoint и офисов
		if ($address_id != -1) $arResult['edost']['javascript'] .= '
		<script language=javascript>
			var E = document.getElementById(document.getElementById("address_input").value);
			if (E) {'.
				($address_readonly == true ? '
				E.readOnly = true; E.style.color = "#707070"; E.style.backgroundColor = "#E0E0E0";' : '
				E.readOnly = false; E.style.color = "#000000"; E.style.backgroundColor = "#FFFFFF";').'
			}
		</script>';

	}

}
?>