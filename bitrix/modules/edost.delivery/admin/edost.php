<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/include.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/prolog.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/general/admin_tool.php'); // функции для работы с заказами

IncludeModuleLangFile(__FILE__);


if ($APPLICATION->GetGroupRight('sale') == 'D') {
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
	return;
}


//$develop = true; // режим разработки !!!!!
if (isset($develop)) require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/edost.delivery/admin/doc.php');


// дынные из языковых файлов
$setting_data = GetMessage('EDOST_ADMIN_SETTING');
$sign = GetMessage('EDOST_ADMIN_SIGN');
$info_107 = GetMessage('EDOST_ADMIN_107_INFO');
$flag = GetMessage('EDOST_ADMIN_ORDER_FLAG');
$button = GetMessage('EDOST_ADMIN_BUTTON');
$rename = GetMessage('EDOST_ADMIN_RENAME');


// загрузка настроек модуля
$ar = COption::GetOptionString('edost.delivery', 'document_setting', '');
$ar = ($ar != '' ? explode('|', $ar) : array());
$setting = array(
	'complete_status' => 'none', // статус выполненого заказа
	'cod' => 'N', // код способа оплаты наложенным платежом (по умолчанию 'N' - не задан)
	'show_order_id' => 'Y', // выводить в правом-верхнем углу бланка номер заказа
	'info_color' => 'BBB', // цвет служебных данных (номер заказа)
	'insurance_107' => 'N', // печатать опись для отправлений 'со страховкой' (по умолчанию опись печатается только при наложке)
	'browser' => 'ie', // 'ie', 'firefox', 'opera', 'chrome', 'yandex'
	'docs_disable' => '7a', // заблокированные бланки (можно распечатать только вручную)
	'duplex' => 'Y', // 'Y' - 'back' печатать в обратном порядке
	'show_status' => 'N,P', // статусы заказов, доступных для поиска
	'show_allow_delivery' => '', // 'Y' - показывать только разрешенные к доставке заказы
	'hide_deducted' => '', // 'Y' - скрывать откруженные заказы (для bitrix от 12.5)
	'deducted' => '', // 'Y' - после печати бланков, разрешить отгрузку заказа (для bitrix от 12.5)
	'hide_unpaid' => 'Y', // 'Y' - скрывать не оплаченные заказы (без наложенного платежа)
	'hide_without_doc' => 'N', // 'Y' - скрывать заказы без документов
	'duplex_x' => '0', // поправка для обратной стороны бланка по горизонтальи в миллиметрах
//	'window_mode' => '0', // '0' - открывать документы списком в разных окнах, '1' - открывать в одном окне с кнопками переключения, '2' - каждый заказ в новом окне с кнопаками переключения
//	'history' => 0, // ключ кэша, в котором хранятся заказы из прошлых распечаток
//	'label' => 'Y', // вывод наклеек на отдельной странице
//	'label_list' => '', // коды бланков наклеек
//	'compact' => 'N', // компактное размещение бланков на листе (экономия бумаги)
);
$i = 0;
foreach ($setting as $k => $v) {
	$setting[$k] = (isset($ar[$i]) ? $ar[$i] : $v);
	$i++;
}
$show_status = ($setting['show_status'] != '' ? explode(',', $setting['show_status']) : false);
$docs_disable = ($setting['docs_disable'] != '' ? explode(',', $setting['docs_disable']) : false);
//echo '<br><b>setting:</b><pre style="font-size: 12px">'.print_r($setting, true).'</pre>';


// загрузка локальных настроек из cookie
$ar = (isset($_COOKIE['edost_admin']) && $_COOKIE['edost_admin'] != '' ? explode('|', preg_replace("/[^0-9a-z_|-]/i", "", $_COOKIE['edost_admin'])) : array());
$setting_cookie = array(
	'filter_days' => '', // заказы оформленные за последние 'filter_days' дней
	'docs_active' => '' // активные документы для ручной печати
);
$i = 0;
foreach ($setting_cookie as $k => $v) {
	$setting_cookie[$k] = (isset($ar[$i]) ? $ar[$i] : $v);
	$i++;
}
if (!isset($setting_data['filter_days'][$setting_cookie['filter_days']])) $setting_cookie['filter_days'] = 5;
$setting_cookie['docs_active'] = ($setting_cookie['docs_active'] != '' ? explode('-', $setting_cookie['docs_active']) : array());
//echo '<br><b>setting_cookie:</b><pre style="font-size: 12px">'.print_r($setting_cookie, true).'</pre>';


// проверка на доступность отгрузки заказов (появилась в bitrix 12.5)
$ar = explode('.', SM_VERSION);
$deducted_enabled = (isset($ar[1]) && ($ar[0] >= 14 || ($ar[0] == 12 && $ar[1] >= 5)) ? true : false);
if ($deducted_enabled && $setting['deducted'] == 'Y') {
	global $USER;
	$user_groups = $USER->GetUserGroupArray();
}

// статусы заказов магазина
$order_status = array('none' => $setting_data['status_no_change']);
$ar = CSaleStatus::GetList(array('SORT' => 'ASC'), array('LID' => LANGUAGE_ID), false, false, array('ID', 'NAME'));
while ($v = $ar->Fetch()) $order_status[htmlspecialcharsbx($v['ID'])] = htmlspecialcharsbx($v['NAME']);
if (!isset($order_status[$setting['complete_status']])) $setting['complete_status'] = 'none'; // отключение изменения статуса, если такого статуса не существует

// способы оплаты магазина
$pay_system = array();
$ar = CSalePaySystem::GetList(array('SORT' => 'ASC', 'PSA_NAME' => 'ASC'), array('ACTIVE' => 'Y'), false, false, array('ID', 'NAME', 'PSA_ACTION_FILE'));
while ($v = $ar->Fetch()) {
	$pay_system[$v['ID']] = htmlspecialcharsbx($v['NAME']);
	if ($setting['cod'] == 'N' && substr($v['PSA_ACTION_FILE'], -11) == 'edostpaycod') $setting['cod'] = $v['ID']; // включение по умолчанию наложенного платежа edost
}


// название для кнопки 'Создать почтовые бланки' (меняется в зависимости от настроек)
$button_print = $button['print']['name'];
if ($setting['complete_status'] == 'none' && $setting['deducted'] == 'Y') $button_print .= $button['print']['deducted'];
else if ($setting['complete_status'] != 'none' && $setting['deducted'] != 'Y') $button_print .= $button['print']['status'].' ['.$order_status[$setting['complete_status']].']';
else if ($setting['complete_status'] != 'none' && $setting['deducted'] == 'Y') $button_print .= $button['print']['status_deducted'].' ['.$order_status[$setting['complete_status']].']';


// загрузка шаблонов документов (из кэша или с сервера edost)
$docs = array();
$cache = new CPHPCache();
if ($cache->InitCache((isset($_POST['update_docs']) && $_POST['update_docs'] == 'Y' ? 1 : 86400), 'sale|11.0.0|edost_delivery|doc', '/')) if (!isset($develop)) $docs = $cache->GetVars();
if (!is_array($docs) || count($docs) == 0) {
	if (!isset($develop)) {
		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/edost.delivery/classes/general/delivery_edost.php');
		$id = COption::GetOptionString('edost.delivery', 'id', '');
		$ps = COption::GetOptionString('edost.delivery', 'ps', '');
		if ($id != '' && $ps != '') {
			$ar = array();
			$ar[] = 'id='.$id;
			$ar[] = 'p='.$ps;
			$ar[] = 'browser='.$setting['browser'];
			$ar[] = 'charset='.urlencode(SITE_CHARSET);

			$edost_CALC = new edost_class();
			$data = $edost_CALC->ManualPOST('http://www.edostzip.ru/doc.php', implode('&', $ar), false);
			$data = stristr($data, 'doc_data:', false);
			$data = substr($data, 9);
		}
	}

	// распарсивание ответа сервера
	$ar = explode('|', $data);
	$key = array('id', 'data', 'data2', 'name', 'size', 'quantity', 'mode', 'cod', 'delivery', 'length', 'space');
	$key_count = count($key);
	$i = 0;
	$k = '';
	foreach ($ar as $v)	{
		if ($v == 'end') {
			$i = 0;
			$k = '';
			continue;
		}

		if ($i == 0 && $v != '' && strlen($v) < 10) $k = $v;

		if ($i >= $key_count) continue;

		if ($k != '') {
			if ($key[$i] == 'insurance' || $key[$i] == 'cod') $docs[$k][$key[$i]] = ($v == 1 ? true : false);
			else if ($key[$i] == 'delivery') $docs[$k][$key[$i]] = ($v != '' ? explode(',', $v) : false);
			else if ($key[$i] == 'size') $docs[$k][$key[$i]] = explode('x', $v);
			else if ($key[$i] == 'length' || $key[$i] == 'space') {
				$v = explode(',', $v);
				foreach ($v as $p) if ($p != '') {
					if ($key[$i] == 'length') {
						$p = explode('=', $p);
						if (isset($p[1]) && intval($p[1]) > 0) $docs[$k][$key[$i]][$p[0]] = intval($p[1]);
					}
					else $docs[$k][$key[$i]][$p] = '';
				}
			}
			else $docs[$k][$key[$i]] = $v;
		}
		$i++;
	}

	if (count($docs) > 5) {
		$cache->StartDataCache();
		$cache->EndDataCache($docs);
	}
}
$show = (count($docs) > 0 ? true : false);
//echo '<br><b>docs:</b><pre style="font-size: 12px">'.print_r($docs, true).'</pre>';


// загрузка параметров со страницы "Настройка печатных форм" - bitrix/admin/sale_report_edit.php
$ar = '';
$n = IntVal(COption::GetOptionInt('sale', 'reports_count'));
if (!($n > 0)) $ar = COption::GetOptionString('sale', 'reports');
else for ($i = 1; $i <= $n; $i++) $ar .= COption::GetOptionString('sale', 'reports'.$i);
$shop = unserialize($ar);

// реквизиты  отправителя
$ar = array_fill_keys(array('INDEX', 'COMPANY_NAME', 'ADDRESS', 'CITY', 'INN', 'KSCH', 'RSCH_BANK', 'RSCH', 'BIK'), '');
foreach ($ar as $k => $v) $ar[$k] = (isset($shop[$k]['TYPE']) && $shop[$k]['TYPE'] == '' ? $shop[$k]['VALUE'] : '');
$shop_field = array(
	'company_name' => $ar['COMPANY_NAME'],
	'company_address' => $ar['ADDRESS'].($ar['ADDRESS'] != '' && $ar['CITY'] != '' ? ', ' : '').$ar['CITY'],
	'company_zip' => $ar['INDEX'],
	'company_inn' => $ar['INN'],
	'company_ksch' => $ar['KSCH'],
	'company_bank' => $ar['RSCH_BANK'],
	'company_rsch' => $ar['RSCH'],
	'company_bik' => $ar['BIK'],
);
$shop_warning = ($shop_field['company_name'] != '' && $shop_field['company_address'] != '' && $shop_field['company_zip'] != '' ? false : true);

// паспортные данные отправителя
$passport = explode('|', COption::GetOptionString('edost.delivery', 'document_passport', $setting_data['passport'][0]['default'].'|||||')); // паспорт|серия|№|выдан день и месяц|выдан год|кто выдал
foreach ($passport as $k => $v) $shop_field['passport_'.$k] = $v;

// ключи свойств покупателя
$ar = array_fill_keys(array('BUYER_COMPANY_NAME', 'BUYER_FIRST_NAME', 'BUYER_SECOND_NAME', 'BUYER_LAST_NAME', 'BUYER_ADDRESS', 'BUYER_CITY', 'BUYER_INDEX'), '');
foreach ($ar as $k => $v) $ar[$k] = (isset($shop[$k]['TYPE']) && $shop[$k]['TYPE'] == 'PROPERTY' ? $shop[$k]['VALUE'] : '');
$user_field_key = array(
	'company' => $ar['BUYER_COMPANY_NAME'],
	'name_1' => $ar['BUYER_FIRST_NAME'],
	'name_2' => $ar['BUYER_SECOND_NAME'],
	'name_3' => $ar['BUYER_LAST_NAME'],
	'address' => $ar['BUYER_ADDRESS'],
	'city' => $ar['BUYER_CITY'],
	'zip' => $ar['BUYER_INDEX'],
);
if ($user_field_key['name_1'] == '' && $user_field_key['name_2'] == '' && $user_field_key['name_3'] == '') $user_field_key['name_1'] = 'FIO';
if ($user_field_key['address'] == '') $user_field_key['address'] = 'ADDRESS';
if ($user_field_key['city'] == '') $user_field_key['city'] = 'CITY';
if ($user_field_key['zip'] == '') $user_field_key['zip'] = 'ZIP';




// данные из POST и GET
$ajax = (isset($_POST['ajax']) && $_POST['ajax'] == 'Y' ? true : false);
$update_allow_delivery = (isset($_POST['update_allow_delivery']) && $_POST['update_allow_delivery'] == 'Y' ? true : false);
$update_status = (isset($_POST['update_status']) && $_POST['update_status'] == 'Y' ? true : false);
$print = (isset($_GET['mode']) ? true : false);

$ar = array('mode' => '', 'id' => '', 'doc' => '');
foreach ($ar as $k => $v) if ($print) $ar[$k] = (isset($_GET[$k]) ? $_GET[$k] : ''); else $ar[$k] = (isset($_POST[$k]) ? $_POST[$k] : '');
$mode = ($ar['mode'] != '' ? preg_replace("/[^a-z|]/i", "", substr($ar['mode'], 0, 20)) : '');
$orders_id = ($ar['id'] != '' ? explode('|', preg_replace("/[^0-9|]/i", "", $ar['id'])) : false);
$docs_active = ($ar['doc'] != '' ? explode('|', preg_replace("/[^0-9a-z|]/i", "", $ar['doc'])) : false);


$currency = CCurrencyLang::GetCurrencyFormat('RUB');
$decimals = $currency['DECIMALS'];




// загрузка заказов
if ($print || ($ajax && !$update_allow_delivery)) {
	$filter = array('ID' => is_array($orders_id) ? $orders_id : 0);
	$allow_delivery = false;
}
else {
	$filter = array('DATE_INSERT_FROM' => GetTime(time() - 86400*$setting_cookie['filter_days']));
	$allow_delivery = true;
}

$ar = CSaleOrder::GetList(array('ID' => 'ASC'), $filter, false, array('nTopCount' => 500), array());

$orders = array();
while ($v = $ar->Fetch()) {
	$edost_id = -1;
	$insurance = false;
	$delivery = explode(':', $v['DELIVERY_ID']);
	if (isset($delivery[1]) && $delivery[0] == 'edost') {
		$edost_id = ceil($delivery[1] / 2);
		$insurance = ($edost_id*2 == $delivery[1] ? true : false);
	}
	$cod = ($v['PAY_SYSTEM_ID'] == $setting['cod'] ? true : false);
	if ($cod) $insurance = true;

	if ($allow_delivery) {
		if ($v['CANCELED'] == 'Y') continue;
		if (!($v['ALLOW_DELIVERY'] == 'Y' || $setting['show_allow_delivery'] != 'Y')) continue;
		if (!$cod) if (!($v['PAYED'] == 'Y' || $setting['hide_unpaid'] != 'Y')) continue;
		if ($deducted_enabled) if (!($v['DEDUCTED'] != 'Y' || $setting['hide_deducted'] != 'Y')) continue;
		if (!(!is_array($show_status) || in_array($v['STATUS_ID'], $show_status))) continue;
		if (!in_array($edost_id, array(1, 2))) continue;
	}

	$v['DELIVERY_EDOST_ID'] = $edost_id;
	$v['INSURANCE'] = ($insurance ? 'Y' : 'N');
	$v['COD'] = ($cod ? 'Y' : 'N');

	$orders[] = $v;
}

foreach ($orders as $order_key => $order) {
	$edost_id = $order['DELIVERY_EDOST_ID'];
	$insurance = ($order['INSURANCE'] == 'Y' ? true : false);
	$cod = ($order['COD'] == 'Y' ? true : false);

	// получение названия тарифа доставки по коду
	if (intval($order['DELIVERY_ID']) > 0) {
		// настраиваемые службы доставки
		$ar = CSaleDelivery::GetByID($order['DELIVERY_ID']);
		$s = $ar['NAME'];
	}
	else {
		// автоматизированные службы доставки
		$id = explode(':', $order['DELIVERY_ID']);
		if (isset($id[1])) {
			$db = CSaleDeliveryHandler::GetBySID($id[0]);
			if ($ar = $db->GetNext()) {
				$company = (isset($ar['NAME']) ? $ar['NAME'] : '');
				$s = (isset($ar['PROFILES'][$id[1]]['TITLE']) ? $ar['PROFILES'][$id[1]]['TITLE'] : '');
				$s = $company.($company != '' ? ' (' : '').$s.($company != '' ? ')' : '');
			}
		}
	}
	foreach ($rename as $v) $s = str_replace($v[0], $v[1], $s);
	$order['DELIVERY_NAME'] = $s;

	// получение названия способа оплаты по коду
	$s = $pay_system[$order['PAY_SYSTEM_ID']];
	foreach ($rename as $v) $s = str_replace($v[0], $v[1], $s);
	$order['PAY_SYSTEM_NAME'] = $s;

	// сокращенное наименование статуса заказа
	$order['STATUS_NAME_SHORT'] = (strlen($order_status[$order['STATUS_ID']]) > 20 ? substr($order_status[$order['STATUS_ID']], 0, 20).'...' : $order_status[$order['STATUS_ID']]);

	// разбивка даты оформления заказа на дату (25.01.2014) и время (10:45:00)
	$ar = explode(' ', $order['DATE_INSERT']);
	if (count($ar) == 2) $order['DATE_INSERT'] = $ar[0].'<br><span class="low">'.$ar[1].'</span>';


	// получение списка документов
	$ar = array();
	foreach ($docs as $doc) if (isset($doc['mode']) && $doc['mode'] != '') {
		if (is_array($docs_active)) {
			// ручная печать (список документов передан в параметрах)
			if (!in_array($doc['id'], $docs_active)) continue;
		}
		else {
			// выбор по параметрам заказа и настройкам
			if (in_array($doc['id'], $docs_disable)) continue;
			if (is_array($doc['delivery']) && !($edost_id >= 0 && in_array($edost_id, $doc['delivery']))) continue;
			if ($doc['id'] == '107' && $setting['insurance_107'] == 'Y') {
				if (!$insurance) continue;
			}
            else if ($doc['cod'] && !$cod) continue;
		}

		$ar[] = $doc['id'];
	}

	if ($allow_delivery && $setting['hide_without_doc'] == 'Y' && count($ar) == 0) {
		unset($orders[$order_key]);
		continue;
	}

	$order['DOCS'] = $ar;


	// свойства заказа
	$props = array();
	$location = 0;
	$ar = CSaleOrderPropsValue::GetOrderProps($order['ID']);
	while ($v = $ar->Fetch()) {
		$props[$v['CODE']] = $v['VALUE'];
		if ($v['TYPE'] == 'LOCATION' && $v['IS_LOCATION'] == 'Y') $location = intval($v['VALUE']);
	}

	if ($location > 0) $location = CSaleLocation::GetByID($location);
	$city = (isset($location['CITY_NAME']) ? $location['CITY_NAME'] : '');
	$region = (isset($location['REGION_NAME']) ? $location['REGION_NAME'] : '');
	$country = (isset($location['COUNTRY_NAME']) ? $location['COUNTRY_NAME'] : '');


	// поля для заполнения документов
	$field = array();

	$ar = $user_field_key;
	foreach ($ar as $k => $v) if ($v != '') $ar[$k] = (isset($props[$v]) ? $props[$v] : '');

	$s = $ar['name_3'].($ar['name_1'] != '' && $ar['name_3'] != '' ? ' ' : '').$ar['name_1'].($ar['name_2'] != '' && ($ar['name_1'] != '' || $ar['name_3']) ? ' ' : '').$ar['name_2'];
	$field['user_name'] = $s.($s != '' && $ar['company'] != '' ? ', ' : '').$ar['company'];

	$s = (strlen($ar['zip']) == 6 ? $ar['zip'] : '');
	$field['user_zip'] = $s;
	for ($i = 1; $i <= 6; $i++) $field['user_zip_'.$i] = ($s == '' ? 'n' : $s{$i-1});

	if ($ar['city'] == $city || $ar['city'] == $region || $ar['city'] == $country || $ar['city'] == $city.' ('.$region.')') $ar['city'] = '';
	if ($city == $sign['msk'] || $city == $sign['spb']) $region = '';

	$s = $ar['city'];
	$s .= ($s != '' && $city != '' ? ', ' : '').$city;
	if ($region != '') $s .= ($s != '' ? ' (' : '').$region.($s != '' ?	 ')' : '');
	$field['user_address_short'] = $s;

	$s = array();
	if ($ar['address'] != '') $s[] = $ar['address'];
	if ($ar['city'] != '') $s[] = $ar['city'];
	if ($city != '') $s[] = $city;
	if ($region != '') $s[] = $region;
	$field['user_address'] = str_replace(array(',', '.'), array(', ', '. '), implode(', ', $s));


	// стоимость заказа для объявленной ценности и наложенного платежа
	$price = CCurrencyRates::ConvertCurrency($order['PRICE'], $order['CURRENCY'], 'RUB');
	$delivery_price = CCurrencyRates::ConvertCurrency($order['PRICE_DELIVERY'], $order['CURRENCY'], 'RUB');
	if (!$cod && $insurance) $price -= $delivery_price; // вычесть из заказа стоимость доставки, если нет наложки
	$price = round($price, $decimals);
	$price_format = number_format($price, $decimals, ',', '');

	$order['TOTAL_FORMATED'] = SaleFormatCurrency($order['PRICE'], 'RUB');
	$order['PRICE'] = $price;
	$order['PRICE_FORMATED'] = $price_format;
	$order['PRICE_DELIVERY_FORMATED'] = SaleFormatCurrency($delivery_price, 'RUB');

	$value = $value2 = $rub = $kop = '';
	if ($insurance) {
		$rub = floor($price);
		$kop = round(($price - $rub)*100);
		if ($kop < 1) $kop = '00';
		$value = $rub.' ('.Number2Word_Rus($rub, 'N').') '.$sign['rub'].' '.$kop . $sign['kop'];
		$value2 = Number2Word_Rus($rub, 'N') . $sign['rub'] . ' ' . $kop . $sign['kop'];
		$value = str_replace(' )', ')', $value);
	}
	$field['insurance_full'] = $value;
	$field['insurance'] = $rub;
	$field['insurance_v'] = ($rub != '' ? 'V' : '');

	$field['inventory_v'] = (in_array('107', $order['DOCS']) ? 'V' : '');

	$field['normal_v'] = ($rub == '' ? 'V' : '');

	if (!$cod) $value = $value2 = $rub = $kop = '';

	$field['cod_full'] = $value;
	$field['cod_full_string'] = $value2;
	$field['cod'] = $rub;
	$field['cod2'] = $kop;
	$field['cod_v'] = ($rub != '' ? 'V' : '');

	$order['FIELD'] = $field;


	// товары
	$ar = CSaleBasket::GetList(array('NAME' => 'ASC', 'ID' => 'ASC'), array('ORDER_ID' => $order['ID']), false, false, array('NAME', 'PRODUCT_ID', 'QUANTITY', 'DELAY', 'CAN_BUY', 'PRICE', 'WEIGHT'));

	$items = array();
	$items_list = array();
	$items_list_short = array();
	$i = 0;
	$hint = false;
	while ($v = $ar->Fetch()) if ($v['CAN_BUY'] == 'Y' && $v['DELAY'] == 'N' && isset($v['QUANTITY']) && $v['QUANTITY'] > 0) {
		$i++;
		$items[] = $v;

		$s = $v['NAME'];
		if (strlen($v['NAME']) > 20) {
			$s = substr($v['NAME'], 0, 18).'...';
			$hint = true;
		}

		$items_list[] .= str_replace(array('"', "'"), array('&quot;', '&quot;'), $i.'. '.$v['NAME'].($v['QUANTITY'] > 1 ? ' (<b>'.intval($v['QUANTITY']).$sign['quantity'].'</b>)' : '').' - '.SaleFormatCurrency($v['PRICE'], 'RUB'));
		$items_list_short[] .= $i.'. '.$s.($v['QUANTITY'] > 1 ? ' ('.intval($v['QUANTITY']).$sign['quantity'].')' : '').' - '.SaleFormatCurrency($v['PRICE'], 'RUB');
	}

	$n = count($items);
	if ($n > 0) {
		if ($hint || $n > 3) $order['HINT'] = implode('<br>', $items_list);
		$s = ($n > 3 ? '<br>... '.$sign['total2'].' '.draw_string('item2', $n) : '');
		if ($n > 3) array_splice($items_list_short, 2);
		$order['ITEMS_STRING'] = implode('<br>', $items_list_short).$s;

		// распределение стоимости заказа по товарам (чтобы итого в описи совпадало с объявленной ценностью)
		$p = 0;
		$items_count = 0;
		for ($i = 0; $i < count($items); $i++) {
			$items_count += $items[$i]['QUANTITY'];
			$items[$i]['PRICE_MODIFIED'] = round($items[$i]['QUANTITY']*$items[$i]['PRICE'], $decimals);
			$p += $items[$i]['PRICE_MODIFIED'];
		}
		$order['ITEMS_COUNT'] = $items_count;
		$n = ceil(($price - $p) / $n);
		if ($n > 1) $n--;

		$p = 0;
		for ($i = 0; $i < count($items); $i++) {
			$items[$i]['PRICE_MODIFIED'] += $n;
			$p += $items[$i]['PRICE_MODIFIED'];
		}
		$items[0]['PRICE_MODIFIED'] += $price - $p;

		for ($i = 0; $i < count($items); $i++)
			$items[$i]['PRICE_MODIFIED_FORMATED'] = number_format($items[$i]['PRICE_MODIFIED'], $decimals, ',', '');
	}
	$order['ITEMS'] = $items;


	$orders[$order_key] = $order;
}
//echo '<br><b>orders:</b><pre style="font-size: 12px">'.print_r($orders, true).'</pre>';




// страница с документами на печать
if ($print) {
	if (count($orders) == 0) die();

	$mode = explode('|', $mode);
	if (!isset($mode[1])) $mode[1] = 'normal';

	$pages = array();
    $order_count = 0;
	foreach ($orders as $order) {
		$edost_id = $order['DELIVERY_EDOST_ID'];
		$insurance = ($order['INSURANCE'] == 'Y' ? true : false);
		$cod = ($order['COD'] == 'Y' ? true : false);

		$field = array_merge($shop_field, $order['FIELD']);

		// номер заказа в углу документа
		if ($setting['show_order_id'] == 'Y') $s = $sign['order'].(strlen($order['ID']) == 6 ? '0' : '').$order['ID']; else $s = '';
		$field['id'] = $s;
		$field['info_color'] = ' color: #'.$setting['info_color'].';';

		// описание отправки для описи
		$s = '';
		if (in_array($edost_id, array(1, 2))) {
			$s = ($edost_id == 1 ? $info_107[1] : $info_107[0]);
			if ($insurance) $s .= ' '.($cod ? $info_107[3] : $info_107[2]);
		}
		$field['107_info'] = $s;

		// галочка "выплатить наличными деньгами", если в настройках не задан номер расчетного счета
		$field['cash'] = ($shop_field['company_rsch'] == '' ? 'V' : '');


		// ключи в бланках для замены
		$field_key = array_keys($field);
		for ($i = 0; $i < count($field_key); $i++) $field_key[$i] = '%'.$field_key[$i].'%';
//		echo '<br><b>props:</b><pre style="font-size: 12px">'.print_r($field, true).'</pre>';


		// заполнение бланков
		$add_order = false;
		foreach ($order['DOCS'] as $doc_key) for ($q = 1; $q <= $docs[$doc_key]['quantity']; $q++) {
			$doc = $docs[$doc_key];
			if (!($doc['mode'] == $mode[1] || ($doc['mode'] == 'duplex' && ($mode[1] == 'front' || $mode[1] == 'back')))) continue;
			$add_order = true;

			// заполнение полей
			$page = ($doc['mode'] == 'duplex' && $mode[1] == 'back' ? $doc['data2'] : $doc['data']);
			draw_field($field_key, $field, $doc, $page);

			// поправка для двухсторонних документов
			$x = $doc['size'][2] + $setting['duplex_x'];
			$page = str_replace('%left%', $x, $page);

			// проверка на наличие в бланке списка товаров
			if (!isset($docs[$doc['id'].'_item'])) {
				$pages[] = array('size' => $doc['size'], 'data' => $page);
				continue;
			}

			// заполнение списка товаров
			$p = $page;
			$item_i = 0;
			$item_s = '';
            $item_doc = $docs[$doc['id'].'_item'];
			$top = 0;
			$list = 1;

			$n = count($order['ITEMS']) - 1;
			for ($i = 0; $i <= $n; $i++) {
				$item_i++;

				$s = $item_doc['data'];

				$f = array(
					'item_top' => $top,
					'item_i' => $i + 1,
					'item_name' => $order['ITEMS'][$i]['NAME'],
					'item_quantity' => intval($order['ITEMS'][$i]['QUANTITY']) . $sign['quantity'],
					'item_price' => $order['ITEMS'][$i]['PRICE_MODIFIED_FORMATED'] . $sign['rub'],
				);

				$f_key = array_keys($f);
				for ($i2 = 0; $i2 < count($f_key); $i2++) $f_key[$i2] = '%'.$f_key[$i2].'%';

				draw_field($f_key, $f, $item_doc, $s, true);
				$item_s .= $s;

				$top += $item_doc['size'][1];

				if ($i == $n || $item_i >= $item_doc['size'][0]) {
					$p = str_replace('%items_table%', $item_s, $p);
					$p = str_replace('%list%', ($i != $n || $list != 1 ? $sign['list'] . $list : ''), $p);
					$p = str_replace('%items_total%', ($i == $n ? draw_string('item', $order['ITEMS_COUNT']).', '.$order['PRICE_FORMATED'] . $sign['rub'] : ''), $p);

					if ($i != $n) {
						$pages[] = array('size' => $doc['size'], 'data' => $p);

						$p = $page;
						$item_i = 0;
						$item_s = '';
						$top = 0;
						$list++;
					}
				}
			}

			$pages[] = array('size' => $doc['size'], 'data' => $p);
		}

		if ($add_order) $order_count++;
	}
//	echo '<br><b>props:</b><pre style="font-size: 12px">'.print_r($pages, true).'</pre>';

	// 'back' печатать в обратном порядке
	if ($mode[1] == 'back' && $setting['duplex'] == 'Y') $pages = array_reverse($pages);

	// распределение бланков по страницам
	$s = '';
	$y = 0;
	$n = count($pages) - 1;
	$page_count = ($n >= 0 ? 1 : 0);
	for ($i = 0; $i <= $n; $i++) {
		$y += $pages[$i]['size'][1];
                                                                    //296
		if ($i == $n || ($i != $n && $y + $pages[$i+1]['size'][1] < 290)) $s2 = '';
		else {
			$page_count++;
			$s2 = ' page-break-after: always;';
			$y = 0;
		}

		$s .= str_replace('%page-break%', $s2, $pages[$i]['data']);
	}

	// заполнение html страницы
	$body = $docs['body']['data'];
	$body = str_replace('%charset%', LANG_CHARSET, $body);
	$body = str_replace('%mode%', isset($docs[$mode[1]]['data']) ? $docs[$mode[1]]['data'] : '', $body);
	$body = str_replace('%browser%', isset($docs[$setting['browser']]['data']) ? $docs[$setting['browser']]['data'] : '', $body);
	$body = str_replace('%order%', $order_count, $body);
	$body = str_replace('%page%', $page_count, $body);
	$body = str_replace('%data%', $s, $body);

    echo $body;

	die();
}




// сохранение настроек
if ($ajax && $mode == 'setting') {
	foreach ($setting as $k => $v) if (isset($_POST[$k])) $setting[$k] = $_POST[$k];
	$setting['duplex_x'] = str_replace(',', '.', $setting['duplex_x']) + 0;
	COption::SetOptionString('edost.delivery', 'document_setting', implode('|', $setting));

	$passport = array();
	for ($i = 0; $i < 6; $i++) $passport[] = (isset($_POST['passport_'.$i]) ? $GLOBALS['APPLICATION']->ConvertCharset($_POST['passport_'.$i], 'utf-8', SITE_CHARSET) : '');
	COption::SetOptionString('edost.delivery', 'document_passport', implode('|', $passport));

	die();
}




// загрузка истории
$history = array();
$history_cache = new CPHPCache();
$history_cache_id = 'sale|11.0.0|edost_delivery|history';
$history_cache_time = 86400*15;
if ($history_cache->InitCache($history_cache_time, $history_cache_id, '/')) $history = $history_cache->GetVars();


// получить список бланков доступных для выбранных заказов + присвоить заказам новый статус + отгрузить
if ($ajax && $mode == 'print') {
	if (count($orders) == 0) die();

	$error = array();
	$print_mode = array();
	$orders_id = array();
	foreach ($orders as $order) if (count($order['DOCS']) > 0) {
		$orders_id[] = $order['ID'];

		foreach ($order['DOCS'] as $v) {
			if ($docs[$v]['mode'] == 'duplex') {
				$print_mode['front'] = 'front';
				$print_mode['back'] = 'back';
			}
			else $print_mode[$docs[$v]['mode']] = $docs[$v]['mode'];
		}

		if ($update_status) {
			// установка статуса заказа
			if ($setting['complete_status'] != 'none' && $setting['complete_status'] != $order['STATUS_ID']) {
				if (!CSaleOrder::StatusOrder($order['ID'], $setting['complete_status'])) $error[] = $order['ID'];
			}

			// отгрузка заказа
			if ($deducted_enabled && $setting['deducted'] == 'Y' && $order['DEDUCTED'] != 'Y') {
				if (CSaleOrder::CanUserChangeOrderFlag($order['ID'], 'PERM_DEDUCTION', $user_groups)) if (!CSaleOrder::DeductOrder($order['ID'], 'Y')) $error[] = $order['ID'];
			}
		}
	}


	// сохранение распечатанных заказов в историю
	$n = count($orders_id);
	if ($n > 0) {
		$first = -1;
		foreach ($history as $k => $v) {
			if ($first == -1) $first = $k;
			if ($v['id'] === $orders_id) {
				unset($history[$k]);
				break;
			}
		}

		$s = '';
		$i = 0;
		foreach ($orders_id as $v) {
			$i++;
			if ($i > 3 && $n > 5) {
	            $s .= ', ... ('.$sign['total'].' '.draw_string('order', $n).')';
				break;
			}
			$s .= ($s != '' ? ', ' : '').$v;
		}

		if (count($history) >= 20 && $first >= 0) unset($history[$first]);

		$history[] = array(
			'date' => ConvertTimeStamp(time(), 'FULL'),
			'name' => $sign['order'] . $s,
			'mode' => 'print',
			'id' => $orders_id,
			'doc' => $docs_active,
		);

		if (!$history_cache->InitCache(1, $history_cache_id, '/')) {
			$history_cache->StartDataCache();
			$history_cache->EndDataCache($history);
		}
	}


	// ответ с результатами в json
	$ar = array(
		'"error": "'.implode('|', $error).'"',
		'"id": "'.implode('|', $orders_id).'"',
		'"mode": "'.implode('|', $print_mode).'"',
	);
	if (is_array($docs_active)) $ar[] = '"doc": "'.implode('|', $docs_active).'"';

	echo '{'.implode(', ', $ar).'}';

	die();
}




// ---------------------------------------------------------------------------




if (!$ajax) {
	$APPLICATION->SetTitle(GetMessage('EDOST_ADMIN_TITLE'));
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
}


if (!$ajax && !$show) { ?>
<div class="adm-info-message" style="display: block"><?=GetMessage('EDOST_ADMIN_DOC_WARNING')?></div>
<? }


if ($show) { ?>

<? if (!$ajax) { ?>
<style>
	div.checkbox label, div.checkbox input { vertical-align: middle; }
	span.low { color: #888; }
	table.standard a { text-decoration: none; }
	table.standard { border-collapse: collapse; border-color: #888; border-style: solid; border-width: 1px; }
	table.standard td { vertical-align: top; }
	tr.slim td { border-width: 1px 0px; }
	span.button { color: #00F; }

	div.menu { float: right; width: 200px; background: #F5F5F5; padding: 3px; text-align: center; font-size: 18px; font-weight: bold; border: 1px solid #A4B9CC; cursor: pointer; color: #5F6674; }
	div.menu:hover { background: #FFF; }

	div.checkbox input[type="checkbox"]:checked + label { color: #000; }
	div.checkbox input[type="checkbox"] + label { color: #888; }

	tr.active { background: #F0FBF0; }
	tr.normal { background: none; }
</style>

<script type="text/javascript">

	function edost_UpdateCookie(name, value) {

		var key = ['filter_days', 'docs_active'];

		var ar = document.cookie.match('(^|;) ?edost_admin=([^;]*)(;|$)');
		ar = (ar ? decodeURIComponent(ar[2]) : '');
		ar = ar.split('|');

		var s = '';
		for (var i = 0; i < key.length; i++) {
			if (i > 0) s += '|';
			if (name == key[i]) s += value;
			else if (ar[i] != undefined) s += ar[i];
		}

		document.cookie = 'edost_admin=' + s + '; path=/; expires=Thu, 01-Jan-2016 00:00:01 GMT';

	}

	function edost_UpdateActive(id, active, checked) {

		if (active == undefined) active = false;
		if (checked == undefined) checked = '';

		if (id == 'all') {
			var ar = BX.findChildren(BX('order_table'), {'tag': 'input', 'type': 'checkbox'}, true);
			for (var i = 0; i < ar.length; i++) edost_UpdateActive(ar[i].id, false, checked);
		}
		else {
			var E = BX(id);
			if (active) E.checked = (E.checked ? false : true);
			if (checked != '') E.checked = (checked == 'Y' ? true : false);
			BX(id + '_tr').className = (E.checked ? 'slim active' : 'slim normal');
		}

	}

	function edost_GetChecked(name, separator) {

		if (separator == undefined) separator = '|';

		var s = '';
		var ar = BX.findChildren(BX(name), {'tag': 'input', 'type': 'checkbox'}, true);
		for (var i = 0; i < ar.length; i++) if (ar[i].checked) {
			var id = ar[i].id;
			id = id.split('_');
			if (id[1] == undefined || id[1] == '') continue;
			id = id[1];

			s += (s != '' ? separator : '') + id;
		}

		return s;

	}

	function edost_SetOrder(mode, id) {

		var id_string = '';
		var param = '';
		var update_order = false;
		var update_docs = false;
		var update_history = false;
		var v = '';

		if (mode == 'menu') {
			var main = (BX('main_div').style.display == 'none' ? false : true);
			BX('main_div').style.display = BX('main_span').style.display = (main ? 'none' : 'block');
			BX('setting_div').style.display = BX('setting_span').style.display = (!main ? 'none' : 'block');
			return;
		}

		if (mode == 'check_Y' || mode == 'check_N') {
			edost_UpdateActive('all', false, mode == 'check_Y' ? 'Y' : 'N');
			return;
		}

		if (mode == 'setting') {
			BX('setting_save').className = 'adm-btn adm-btn-load';
			BX('setting_save_loading').style.display = 'block';

			// ['id', (1 - обновить список заказов, 2 - загрузить с сервера документы)]
			var ar = [['show_order_id', 0], ['insurance_107', 1], ['duplex', 0], ['show_allow_delivery', 1], ['hide_deducted', 1], ['deducted', 1], ['hide_unpaid', 1], ['hide_without_doc', 1], ['complete_status', 1], ['cod', 0], ['info_color', 0], ['browser', 2], ['duplex_x', 0]];
			for (var i = 0; i < ar.length; i++) {
				var E = BX('setting_' + ar[i][0]);
				if (!E) continue;

				if (E.type == 'checkbox') v = (E.checked ? 'Y' : 'N'); else v = E.value;

				param += '&' + ar[i][0] + '=' + v;
				if (ar[i][1] > 0) {
					E2 = BX('setting_' + ar[i][0] + '_start');
					if (!E2) continue;

					if (E2.value != v) {
						E2.value = v;
						if (ar[i][1] == 1) update_order = true;
						if (ar[i][1] == 2) param += '&update_docs=Y';
					}
				}
			}

			param += '&docs_disable=' + edost_GetChecked('setting_docs_disable_div', ',');

			var v = edost_GetChecked('setting_show_status_div', ',');
			param += '&show_status=' + v;
			if (v != BX('setting_show_status_start').value) {
				BX('setting_show_status_start').value = v;
				update_order = true;
			}

			for (var i = 0; i < 6; i++) param += '&passport_' + i + '=' + BX('passport_' + i).value;
		}

		if (mode == 'update_allow_delivery' || mode == 'update_filter_days') {
			if (mode == 'update_filter_days') edost_UpdateCookie('filter_days', BX('filter_days').value);
			mode = 'order';
			param += '&update_allow_delivery=Y';
		}

		if (mode == 'find') {
			mode = 'order';
			var ar = BX('order_find').value;
			ar = ar.replace(/[^0-9,.-]/g, '').replace(/[.]/g, ',');
			ar = ar.split(',');

			var n = 0;
			id_string = '';
			for (var i = 0; i < ar.length; i++) {
				var v = ar[i].split('-');
				if (v[1] == undefined) {
					n++;
					if (n > 200) break;
					id_string += (id_string != '' ? '|' : '') + v[0];
				}
				else if (v[0] != '' || v[1] != '') {
	                if (v[0] == '') v[0] = v[1]*1 - 100*1;
	                if (v[1] == '') v[1] = v[0]*1 + 100*1;
	                if (v[0] < 1) v[0] = 1;
	                if (v[0] > v[1]) {
	                	var x = v[0];
	                	v[0] = v[1];
	                	v[1] = x;
	                }
					for (var i2 = v[0]; i2 <= v[1]; i2++) {
						n++;
						if (n > 200) break;
						id_string += (id_string != '' ? '|' : '') + i2;
					}
				}
			}
		}

		if (mode == 'history_order' || mode == 'history_print') {
			if (mode != 'history_print') mode = 'order';

			var ar = BX('history').value;
			ar = ar.split('-');
			if (ar[1] != undefined) id_string = ar[1];
			if (ar[2] != undefined) param += '&doc=' + ar[2];
		}

		if (mode == 'print' || mode == 'print_manual' || mode == 'history_print') {
			if (mode == 'print') param += '&update_status=Y';
			if (mode == 'print_manual') {
				edost_UpdateCookie('docs_active', edost_GetChecked('doc_div', '-'));
				param += '&doc=' + edost_GetChecked('doc_div');
			}
			if (mode != 'history_print') id_string = edost_GetChecked('order_table');

			if (id_string == '') {
				alert('<?=GetMessage('EDOST_ADMIN_NO_ORDER_ACTIVE')?>');
				return;
			}

			if (id != 'undefined') BX(id).className = 'adm-btn adm-btn-load';
			BX('history_div').innerHTML = '<div style="height: 45px; vertical-align: middle;"><div style="height: 10px;"></div><span class="low" style="font-size: 15px; font-weight: bold;"><?=$sign['loading_history']?></span></div>';

			mode = 'print';
		}


		if (mode == 'order') BX('order_div').innerHTML = '<span class="low" style="font-size: 20px; font-weight: bold;"><?=$sign['loading']?></span>';
		BX.ajax.post('edost.php', 'ajax=Y&mode=' + mode + '&id=' + id_string + param, function(res) {
			if (id != 'undefined') {
				var E = BX(id);
				if (E && E.type == 'button') BX(id).className = (id == 'button_print' ? 'adm-btn-save' : 'adm-btn');
			}

			if (mode == 'history') BX('history_div').innerHTML = res;

			if (mode == 'setting') {
				BX('setting_save').className = 'adm-btn-save';
				BX('setting_save_loading').style.display = 'none';

	            if (update_order) edost_SetOrder('update_allow_delivery');
			}

			if (mode == 'order') BX('order_div').innerHTML = res;

			if (mode == 'print') {
				if (res == '') return;
				res = (window.JSON && window.JSON.parse ? JSON.parse(res) : eval('(' + res + ')'));

				if (res.id != undefined && res.mode != undefined)
					if (res.id == '') alert('<?=GetMessage('EDOST_ADMIN_NO_DOC')?>');
					else {
						var ar = res.mode.split('|');
						for (var i = 0; i < ar.length; i++)
							window.open('edost.php?mode=print|' + ar[i] + '&id=' + res.id + (res.doc != undefined ? '&doc=' + res.doc : ''), '_blank');
					}

				window.setTimeout("edost_SetOrder('history');", 3000);
			}
		});

	}
</script>


<div style="max-width: 950px;">

<?	if ($shop_warning) { ?>
	<div class="adm-info-message" style="display: block"><?=GetMessage('EDOST_ADMIN_SHOP_WARNING')?></div>
<?	} ?>

	<div id="menu" class="menu" onclick="edost_SetOrder('menu');">
		<span id="main_span"><?=GetMessage('EDOST_ADMIN_MENU_SETTING')?></span>
		<span id="setting_span" style="display: none;"><?=GetMessage('EDOST_ADMIN_MENU_MAIN')?></span>
	</div>

<? }

if (!$ajax) { ?>
	<div id="main_div">
		<div style="height: 30px; padding-top: 5px; padding-bottom: 10px;">
			<b><?=GetMessage('EDOST_ADMIN_FIND_HEAD')?></b> <input id="order_find" value="" type="text" style="width: 300px;">
			<input value="<?=GetMessage('EDOST_ADMIN_FIND')?>" type="button" onclick="edost_SetOrder('find')">
			<img id="find_hint" style="position: absolute; margin: 6px 0 0 6px;" src="http://edostimg.ru/img/hint/hint.gif">
			<script type="text/javascript"> new top.BX.CHint({parent: top.BX('find_hint'), show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 350, hint: '<?=GetMessage('EDOST_ADMIN_FIND_HINT')?>'}); </script>
		</div>
<? }

if (!$ajax) { ?>
		<div id="history_div" style="font-size: 13px;">
<? }
if (count($history) > 0 && (!$ajax || ($ajax && $mode == 'history'))) { ?>
			<div style="height: 30px; padding-top: 5px; padding-bottom: 10px;">
				<b><?=GetMessage('EDOST_ADMIN_HISTORY_HEAD')?></b>
				<select id="history">
<?				$ar = array_reverse($history);
				foreach ($ar as $k => $v) { ?>
					<option value="<?=$v['mode']?>-<?=implode('|', $v['id'])?>-<?=(is_array($v['doc']) ? implode('|', $v['doc']) : '')?>"><?=$v['date']?> - <?=$v['name']?></option>
<?				} ?>
				</select>
				<input value="<?=$button['history']['name']?>" type="button" onclick="edost_SetOrder('history_order')">
				<input id="button_history_print" class="adm-btn" value="<?=$button['history']['print']?>" type="button" onclick="edost_SetOrder('history_print', this.id)">
			</div>
<?	}
if ($ajax && $mode == 'history') die();
if (!$ajax) { ?>
		</div>
<? } ?>


<? if (!$ajax) { ?>
	<div id="order_div">
<? } ?>

<? if (!$ajax || ($ajax && $mode == 'order')) { ?>

		<div style="margin: 15px 0px 2px 0px;">
<?		if ($allow_delivery) { ?>
			<b style="color: #08C; font-size: 15px;"><?=GetMessage('EDOST_ADMIN_ORDER_ALLOW_DELIVERY_HEAD')?>
			<select id="filter_days" onchange="edost_SetOrder('update_filter_days')" style="padding: 1px; height: 22px;">
<?			foreach ($setting_data['filter_days'] as $k => $v) { ?>
				<option value="<?=$k?>" <?=($k == $setting_cookie['filter_days'] ? 'selected' : '')?>><?=$v?></option>
<?			} ?>
			</select>
			:</b>
	        <div style="float: right;">
				<input style="height: 18px;" value="<?=$button['update']?>" type="button" onclick="edost_SetOrder('update_allow_delivery')">
			</div>
<?		} else { ?>
			<input value="<?=$button['show_order'].$setting_data['filter_days'][$setting_cookie['filter_days']]?>" type="button" onclick="edost_SetOrder('update_allow_delivery')">
			<div style="margin: 20px"></div>
<?		} ?>
		</div>

		<table id="order_table" class="standard" width="100%" style="max-width: 950px;" border="1" bordercolor="#888" cellpadding="4" cellspacing="0">
<?		foreach ($orders as $v) {?>
			<tr id="order_<?=$v['ID']?>_tr" class="slim checkbox">
				<td width="20" onclick="edost_UpdateActive('order_<?=$v['ID']?>', true)">
					<input class="adm-checkbox adm-designed-checkbox" id="order_<?=$v['ID']?>" type="checkbox" checked="" onclick="edost_UpdateActive(this.id)">
					<label class="adm-designed-checkbox-label adm-checkbox" for="order_<?=$v['ID']?>"></label>
				</td>
				<td width="60" align="right" style="font-size: 13px; cursor: default;" onclick="edost_UpdateActive('order_<?=$v['ID']?>', true)">
					<?=$v['DATE_INSERT']?>
				</td>
				<td width="60" style="font-size: 15px; text-align: center;" align="center">
					<a href="/bitrix/admin/sale_order_detail.php?ID=<?=$v['ID']?>&lang=<?=LANGUAGE_ID?>"><b><?=('<span style="font-size: 10px;">'.$sign['order'] .'</span>'. $v['ID'])?></b></a>
				</td>

				<td width="320" align="left" style="font-size: 13px;">
					<?=$v['FIELD']['user_name']?><br>
					<span class="low"><?=$v['FIELD']['user_address_short']?></span>
					<div><span class="low" style="color: #55F;"><?=$v['DELIVERY_NAME']?></span></div>
					<div style="text-align: right2;"> <span class="low" style="color: #b59422;"><?=$v['PAY_SYSTEM_NAME']?></span></div>
					<?=($allow_delivery && count($v['DOCS']) == 0 ? $setting_data['hide_without_doc']['mark'] : '')?>
				</td>

				<td width="160" style="font-size: 13px; text-align: left;">
					<span id="order_status_<?=$v['ID']?>" style="cursor: default;"><?=$v['STATUS_NAME_SHORT']?></span><br>
<?					foreach ($flag as $k => $f) if (isset($v[$k]) && $v[$k] == $f['value']) echo $f['name'].'<br>'; ?>
					<script type="text/javascript">
						new top.BX.CHint({parent: top.BX('order_status_<?=$v['ID']?>'), show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 250, hint: '<?=('['.$v['STATUS_ID'].'] '.$order_status[$v['STATUS_ID']].'<br>'.$v['DATE_STATUS'])?>'});
					</script>
				</td>

				<td style="font-size: 10px; text-align: left; cursor: default;">
					<span id="order_id_<?=$v['ID']?>">
						<?=$v['ITEMS_STRING']?><br>
						<?=($v['PRICE_DELIVERY'] > 0 ? $sign['delivery'].': '.$v['PRICE_DELIVERY_FORMATED'].'<br>' : '')?>
						<span style="font-size: 13px;"><b><?=$v['TOTAL_FORMATED']?></b></span>
					</span>

<?					if (isset($v['HINT'])) { ?>
					<script type="text/javascript">
						new top.BX.CHint({parent: top.BX('order_id_<?=$v['ID']?>'), show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 350, hint: '<?=$v['HINT']?>'});
					</script>
<?					} ?>
				</td>

				<td width="80" align="center" style="font-size: 16px;">
					<a href="/bitrix/admin/sale_order_new.php?ID=<?=$v['ID']?>&lang=<?=LANGUAGE_ID?>"><?=$sign['change']?></a>
				</td>

			</tr>
<?		} ?>
		</table>

		<div style="float: right; padding: 5px 0 8px 0;">
			<input style="height: 18px;" value="<?=$button['check']['Y']?>" type="button" onclick="edost_SetOrder('check_Y')">
			<input style="height: 18px;" value="<?=$button['check']['N']?>" type="button" onclick="edost_SetOrder('check_N')">
		</div>

<?		if (count($orders) == 0) { ?>
		<div style="margin: 0px 0 30px 0; font-size: 20px; color: #A00;"><?=GetMessage('EDOST_ADMIN_NO_ORDER')?></div>
<?		} else { ?>
		<div class="buttons" style="margin: 10px 0 30px 0;">
			<input id="button_print" class="adm-btn-save" value="<?=$button_print?>" type="button" onclick="edost_SetOrder('print', this.id)">
		</div>
<?		} ?>

<?	} ?>



<?		if (count($orders) != 0) { ?>
		<div style="margin-top: 35px; color: #888; font-size: 16px;">
			<b><?=GetMessage('EDOST_ADMIN_ORDER_MANUAL_PRINT_HEAD')?></b>
			<img id="manual_print_hint" style="position: absolute; margin: 3px 0 0 6px;" src="http://edostimg.ru/img/hint/hint.gif">
			<script type="text/javascript"> new top.BX.CHint({parent: top.BX('manual_print_hint'), show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 250, hint: '<?=GetMessage('EDOST_ADMIN_ORDER_MANUAL_PRINT_HINT')?>'}); </script>
		</div>
		<div id="doc_div" style="margin-top: 5px; padding: 0px; ">
<?			foreach ($docs as $k => $doc) if (isset($doc['mode']) && $doc['mode'] != '') { ?>
			<div class="checkbox" style="padding-top: 5px; font-size: 13px;">
				<input id="doc_<?=$k?>" value="<?=$k?>" style="margin: 0px;" type="checkbox"<?=(in_array($k, $setting_cookie['docs_active']) ? ' checked=""' : '')?>>
				<label for="doc_<?=$k?>"><b><?=$doc['name'].($doc['quantity'] > 1 ? ' ('.$doc['quantity'].$sign['quantity'].')' : '')?></b></label>
			</div>
<?			} ?>

			<div class="buttons" style="margin: 10px 0 30px 0;">
				<input id="button_print_manual" class="adm-btn" value="<?=$button['print']['name']?>" type="button" onclick="edost_SetOrder('print_manual', this.id)">
			</div>
		</div>
<?		} ?>


<? if (!$ajax) { ?>
		</div>
	</div>


	<div id="setting_div" style="display: none; font-size: 13px;">
		<div style="margin-top: 5px; padding: 0px; border: 0px solid #CCC;">
			<b><?=$setting_data['browser_head']?></b>
			<input id="setting_browser_start" type="hidden" value="<?=$setting['browser']?>">
			<select id="setting_browser">
<?				foreach ($setting_data['browser'] as $k => $v) { ?>
				<option value="<?=$k?>" <?=($k == $setting['browser'] ? 'selected' : '')?>><?=$v?></option>
<?				} ?>
			</select>
		</div>

		<div class="checkbox" style="padding-top: 5px;">
			<b><?=$setting_data['duplex_x'][0]?>
			<input class="normal" id="setting_duplex_x" value="<?=$setting['duplex_x']?>" type="text" style="vertical-align: baseline; padding: 0px 4px; height: 19px; width: 30px;" maxlength="4">
			<?=$setting_data['duplex_x'][1]?></b>
			<img id="duplex_x_hint" style="position: absolute; margin: 3px 0 0 6px;" src="http://edostimg.ru/img/hint/hint.gif">
			<script type="text/javascript"> new top.BX.CHint({parent: top.BX('duplex_x_hint'), show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 400, hint: '<?=$setting_data['duplex_x'][2]?>'}); </script>
		</div>


		<div class="checkbox" style="padding-top: 20px;">
			<input id="setting_show_order_id" style="margin: 0px;" type="checkbox"<?=($setting['show_order_id'] == 'Y' ? ' checked=""' : '')?>>
			<label for="setting_show_order_id"><b><?=$setting_data['show_order_id']?></b></label>

	        <span style="vertical-align: middle;">
	        (<?=$setting_data['info_color_head']?>
			<select id="setting_info_color" style="padding: 1px; height: 20px;">
<?				foreach ($setting_data['info_color'] as $v) { ?>
				<option value="<?=$v[1]?>" <?=($v[1] == $setting['info_color'] ? 'selected' : '')?>><?=$v[0]?></option>
<?				} ?>
			</select>)
	        </span>
		</div>

		<div class="checkbox" style="padding-top: 5px;">
			<input id="setting_insurance_107_start" type="hidden" value="<?=$setting['insurance_107']?>">
			<input id="setting_insurance_107" style="margin: 0px;" type="checkbox"<?=($setting['insurance_107'] == 'Y' ? ' checked=""' : '')?>>
			<label for="setting_insurance_107"><b><?=$setting_data['insurance_107']?></b></label>
		</div>

		<div class="checkbox" style="padding-top: 5px;">
			<input id="setting_duplex" style="margin: 0px;" type="checkbox"<?=($setting['duplex'] == 'Y' ? ' checked=""' : '')?>>
			<label for="setting_duplex"><b><?=$setting_data['duplex']?></b></label>
		</div>

		<div style="margin-top: 25px; font-weight: bold;"><?=$setting_data['passport_head']?></div>
		<div style="margin-top: 5px; padding: 0px;">
<?			foreach ($setting_data['passport'] as $k => $v) { ?><?=$v['name']?><input class="normal" id="passport_<?=$k?>" value="<?=$passport[$k]?>" type="text" style="vertical-align: baseline; padding: 0px 4px; height: 19px; width: <?=$v['width']?>px;" maxlength="<?=$v['max']?>"><? } ?>
		</div>


		<div style="padding-top: 25px;">
			<b><?=$setting_data['status']?></b>
			<input id="setting_complete_status_start" type="hidden" value="<?=$setting['complete_status']?>">
			<select id="setting_complete_status">
<?				foreach ($order_status as $k => $v) { ?>
				<option value="<?=$k?>" <?=($k == $setting['complete_status'] ? 'selected' : '')?>><?=($k != 'none' ? '['.$k.'] ' : '')?><?=$v?></option>
<?				} ?>
			</select>
		</div>

		<div style="margin-top: 5px;">
			<b><?=$setting_data['cod']?></b>
			<select id="setting_cod">
<?				foreach ($pay_system as $k => $v) { ?>
				<option value="<?=$k?>" <?=($k == $setting['cod'] ? 'selected' : '')?>>[<?=$k?>] <?=$v?></option>
<?				} ?>
			</select>
		</div>


		<div class="checkbox" style="padding-top: 25px;">
			<input id="setting_show_allow_delivery_start" type="hidden" value="<?=$setting['show_allow_delivery']?>">
			<input id="setting_show_allow_delivery" style="margin: 0px;" type="checkbox"<?=($setting['show_allow_delivery'] == 'Y' ? ' checked=""' : '')?>>
			<label for="setting_show_allow_delivery"><b><?=$setting_data['show_allow_delivery']?></b></label>
		</div>

		<div class="checkbox" style="padding-top: 5px;">
			<input id="setting_hide_unpaid_start" type="hidden" value="<?=$setting['hide_unpaid']?>">
			<input id="setting_hide_unpaid" style="margin: 0px;" type="checkbox"<?=($setting['hide_unpaid'] == 'Y' ? ' checked=""' : '')?>>
			<label for="setting_hide_unpaid"><b><?=$setting_data['hide_unpaid']?></b></label>
		</div>

		<div class="checkbox" style="padding-top: 5px;">
			<input id="setting_hide_without_doc_start" type="hidden" value="<?=$setting['hide_without_doc']?>">
			<input id="setting_hide_without_doc" style="margin: 0px;" type="checkbox"<?=($setting['hide_without_doc'] == 'Y' ? ' checked=""' : '')?>>
			<label for="setting_hide_without_doc"><b><?=$setting_data['hide_without_doc']['name']?></b></label>
		</div>

<?		if ($deducted_enabled) { ?>
		<div class="checkbox" style="padding-top: 5px;">
			<input id="setting_hide_deducted_start" type="hidden" value="<?=$setting['hide_deducted']?>">
			<input id="setting_hide_deducted" style="margin: 0px;" type="checkbox"<?=($setting['hide_deducted'] == 'Y' ? ' checked=""' : '')?>>
			<label for="setting_hide_deducted"><b><?=$setting_data['hide_deducted']?></b></label>
		</div>

		<div class="checkbox" style="padding-top: 5px;">
			<input id="setting_deducted_start" type="hidden" value="<?=$setting['deducted']?>">
			<input id="setting_deducted" style="margin: 0px;" type="checkbox"<?=($setting['deducted'] == 'Y' ? ' checked=""' : '')?>>
			<label for="setting_deducted"><b><?=$setting_data['deducted']?></b></label>
		</div>
<?		} ?>


		<div style="margin-top: 20px; color: #080;"><b><?=$setting_data['show_status']?></b></div>
		<div id="setting_show_status_div" style="margin-top: 0px; padding: 0px; ">
			<input id="setting_show_status_start" type="hidden" value="<?=$setting['show_status']?>">
<?			foreach ($order_status as $k => $v) if ($k != 'none') { ?>
			<div class="checkbox" style="padding-top: 5px; font-size: 13px;">
				<input id="settingshowstatus_<?=$k?>" value="<?=$k?>" style="margin: 0px;" type="checkbox"<?=(in_array($k, $show_status) ? ' checked=""' : '')?>>
				<label for="settingshowstatus_<?=$k?>"><b><?='['.$k.'] '.$v?></b></label>
			</div>
<?			} ?>
		</div>


		<div style="margin-top: 20px; color: #B00;"><b><?=$setting_data['docs_disable']?></b></div>
		<div id="setting_docs_disable_div" style="margin-top: 0px; padding: 0px; ">
<?			foreach ($docs as $k => $doc) if (isset($doc['mode']) && $doc['mode'] != '') { ?>
			<div class="checkbox" style="padding-top: 5px; font-size: 13px;">
				<input id="settingdoc_<?=$k?>" value="<?=$k?>" style="margin: 0px;" type="checkbox"<?=(in_array($doc['id'], $docs_disable) ? ' checked=""' : '')?>>
				<label for="settingdoc_<?=$k?>"><b><?=$doc['name'].($doc['quantity'] > 1 ? ' ('.$doc['quantity'].$sign['quantity'].')' : '')?></b></label>
			</div>
<?			} ?>
		</div>


		<div style="padding-top: 20px;">
			<input id="setting_save" class="adm-btn-save" value="<?=$setting_data['save']?>" type="button" onclick="edost_SetOrder('setting')">
		</div>
		<div id="setting_save_loading" class="adm-btn-load-img" style="margin-top: -24px; margin-left: 80px; display: none;"></div>
	</div>
</div>
<? } ?>

<script type="text/javascript">
	edost_UpdateActive('all');
</script>

<?
}


if (!$ajax) require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');


// вывод значений со склонением
function draw_string($name, $n) {

	$ar = array(
		'order' => array('заказ', 'заказа', 'заказов'),
		'item' => array('предмет', 'предмета', 'предметов'),
		'item2' => array('позиция', 'позиции', 'позиций'),
	);

	$s = '';
	if ($n >= 11 && $n <= 19) $s = $ar[$name][2];
	else {
		$x = $n % 10;
		if ($x == 1) $s = $ar[$name][0];
		else if ($x >= 2 && $x <= 4) $s = $ar[$name][1];
		else $s = $ar[$name][2];
	}

	$s = $GLOBALS['APPLICATION']->ConvertCharset($s, 'windows-1251', SITE_CHARSET);

	return $n.' '.$s;

}

// замена ключей в шаблоне на данные из полей
function draw_field($field_key, $field, $doc, &$page, $block = false) {

	$space = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

	// уменьшение размера шрифта (если текст не входит в поле)
	if (isset($doc['length']) && is_array($doc['length']))
		foreach ($doc['length'] as $k => $v) {
			$size = '';
			$length = strlen($field[$k]);
			if ($length > $v) {
				$size = ($block && $length > $v*1.5 ? ' line-height: 2mm;' : '').' font-size: 2.5mm;';
				if (isset($doc['space'][$k])) $field[$k] = $space . $field[$k];
			}
			if ($block && $length > $v*2.8) $field[$k] = substr($field[$k], 0, ceil($v*2.8)).'...';

			$page = str_replace('%'.$k.'_size%', $size, $page);
		}

	$page = str_replace($field_key, $field, $page);

}

?>