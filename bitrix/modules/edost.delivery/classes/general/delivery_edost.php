<?
/*********************************************************************************
Обработчик расчета доставки калькулятора eDost.ru
Версия 1.2.2, 01.05.2014
Автор: ООО "Айсден"

Компании доставки и параметры расчета задаются в личном кабинете eDost.ru (требуется регистрация: http://edost.ru/reg.php)


Пример ручного расчета:
--------------------------------------------------------------------------------
$arOrder = array
(
	// стандартные параметры
	// если переданы только 'WEIGHT' и 'PRICE' (без 'ITEMS'), тогда доставка рассчитывается по текущей корзине магазина (если корзина не пустая)!!!
	// доставка НЕ рассчитывается, если в 'ITEMS' (или корзине) есть хоть один товар с нулевым весом!!!
	'WEIGHT' => 1000, // вес в граммах
	'PRICE' => 2000, // стоимость заказа в рублях (для расчета страховки)
	'LOCATION_FROM' => COption::GetOptionString('sale', 'location', false, SITE_ID), // для модуля eDost указывать не требуется
	'LOCATION_TO' => 234, // ID местоположения, куда необходимо рассчитать доставку
	'LOCATION_ZIP' => 620004, // почтовый индекс (только для расчета почты наземная посылка - включается в админке магазина в настройках модуля eDost)
	'ITEMS' => array( // товары в корзине (добавлено в bitrix 14)
		'0' => array(
			'ID' => 10,
			'PRODUCT_ID' => 20,
			'CAN_BUY' => 'Y',
			'PRICE' => 2000,
			'CURRENCY' => 'RUB',
			'QUANTITY' => 1,
			'WEIGHT' => 1000,
			'CALLBACK_FUNC' => 'CatalogBasketCallback',
			'MODULE' => 'catalog',
			'DIMENSIONS' => Array(
				'WIDTH' => 10,
				'HEIGHT' => 20,
				'LENGTH' => 30
			)
		),
	),

	// дополнительные параметры для модуля edost (указывать не обязательно)
	'NO_LOCAL_CACHE' => 'Y', // 'Y' - не использовать локальный кэш класса CDeliveryEDOST (доставка будет пересчитываться при каждом запросе)
	'CART' => 'Y', // 'Y' - расчет по 'ITEMS' (по умолчанию),  'N' - расчет по параметрам 'WEIGHT', 'PRICE', 'LENGTH', 'WIDTH', 'HEIGHT', 'QUANTITY',  'DOUBLE' - оба варианта
	'QUANTITY' => 1, // количество
	'LENGTH' => 0, 'WIDTH' => 0, 'HEIGHT' => 0, // габариты (размерность габаритов должна совпадать с размерностью в личном кабинете eDost)
	'ORDER_ID' => '1', // ID уже оформленного заказа, для которого необходимо рассчитать доставку
	'SITE_ID' => 's1', // ID сайта (требуется только если передан 'ORDER_ID')
);

// получение списка доступных автоматизированых служб доставки
$rsDeliveryServicesList = CSaleDeliveryHandler::GetList(array("SORT" => "ASC"), array("COMPABILITY" => $arOrder));
while ($arDeliveryService = $rsDeliveryServicesList->Fetch())
	foreach ($arDeliveryService["PROFILES"] as $profile_id => $arDeliveryProfile)
		echo '<b>'.$arDeliveryService['SID'].':'.$profile_id.'</b> - '.$arDeliveryProfile['TITLE'].' ('.$arDeliveryProfile['DESCRIPTION'].')<br>';

// получение стоимости доставки для тарифа "edost" с кодом "5" (EMS Почта России)
$arDeliveryPrice = CSaleDeliveryHandler::CalculateFull("edost", 5, $arOrder, "RUB");
echo "<pre>".print_r($arDeliveryPrice, true)."</pre>";

// загрузка данных из класса модуля для тарифа с кодом "5" (EMS Почта России) - можно использовать вместо функции CalculateFull, но перед этим должен быть получен список доступных тарифов через GetList
if (class_exists(CDeliveryEDOST)) {
	$edost_tariff = CDeliveryEDOST::GetEdostTariff(5);
	echo "<pre>".print_r($edost_tariff, true)."</pre>";
}
--------------------------------------------------------------------------------


Получение идентификатора тарифа и стоимости доставки по коду заказа:
--------------------------------------------------------------------------------
$order = CSaleOrder::GetByID(10);
echo '<br>delivery id: '.$order['DELIVERY_ID'].'<br>delivery price: '.$order['PRICE_DELIVERY'];
--------------------------------------------------------------------------------


Получение названия тарифа доставки по его идентификатору:
--------------------------------------------------------------------------------
$delivery_id = 'edost:5'; // идентификатор тарифа, для которого необходимо получить название
if (intval($delivery_id) > 0) {
	// настраиваемые службы доставки
	$ar = CSaleDelivery::GetByID($delivery_id);
	$name = $ar['NAME'];
}
else {
	// автоматизированные службы доставки
	$id = explode(":", $delivery_id);
	if (isset($id[1])) {
		$db = CSaleDeliveryHandler::GetBySID($id[0]);
		if ($ar = $db->GetNext()) {
			$company = (isset($ar['NAME']) ? $ar['NAME'] : '');
			$name = (isset($ar['PROFILES'][$id[1]]['TITLE']) ? $ar['PROFILES'][$id[1]]['TITLE'] : '');
			$name = $company.($company != '' ? ' (' : '').$name.($company != '' ? ')' : '');
		}
	}
}
echo '<br>tariff name: '.$name;
--------------------------------------------------------------------------------


Коды тарифов:
--------------------------------------------------------------------------------
Код битрикса = код eDost * 2 - 1
и +1, если нужен тариф со страховкой

коды eDost: http://www.edost.ru/kln/help.html#DeliveryCode

Пример:
edost:1 - Почта России (отправление 1-го класса)
edost:2 - Почта России (отправление 1-го класса) со страховкой
edost:3 - Почта России (наземная посылка)
edost:4 - Почта России (наземная посылка) со страховкой
edost:5 - EMS Почта России
--------------------------------------------------------------------------------


*********************************************************************************/


CModule::IncludeModule('sale');

IncludeModuleLangFile(__FILE__);

include_once 'edost_const.php';

define('DELIVERY_EDOST_TARIFF_QTY', 55); // кол-во тарифов доставки доступных в модуле (для контроля версий - не менять!)

define('DELIVERY_EDOST_SERVER', 'edost.ru'); // сервер расчета доставки
define('DELIVERY_EDOST_SERVER2', 'xn--d1ab2amf.xn--p1ai'); // дополнительный сервер (едост.рф)
define('DELIVERY_EDOST_SERVER_PORT', 80); // server port
define('DELIVERY_EDOST_SERVER_PAGE', '/edost_calc_kln.php'); // server page url
define('DELIVERY_EDOST_SERVER_METHOD', 'POST'); // data send method

define('DELIVERY_EDOST_ID_DEFAULT', ''); // идентификатор магазина
define('DELIVERY_EDOST_PS_DEFAULT', ''); // пароль к серверу расчетов
define('DELIVERY_EDOST_HOST_DEFAULT', ''); // временный адрес сервера
define('DELIVERY_EDOST_HIDE_ERR_DEFAULT', 'N'); // скрывать ошибки
define('DELIVERY_EDOST_SHOW_MSG_DEFAULT', 'N'); // если расчет не возможен, то вывести сообщение
define('DELIVERY_EDOST_SHOW_PICKPOINT_MAP_DEFAULT', 'N'); // включить виджет PickPoint по выбору постаматов и пунктов выдачи на карте
define('DELIVERY_EDOST_CODSTATUS_DEFAULT', ''); // при выборе способа оплаты "Наложенный платеж" переводить заказ в статус
define('DELIVERY_EDOST_SEND_ZIP_DEFAULT', 'Y'); // разрешить расчет Почты (наземная посылка) по индексу
define('DELIVERY_EDOST_HIDE_PAYMENT_DEFAULT', 'Y'); // скрывать способы оплаты, если не рассчитана доставка
define('DELIVERY_EDOST_SORT_ASCENDING_DEFAULT', 'N'); // сортировать тарифы по стоимости


class CDeliveryEDOST
{
	public static $rezAll = null;


	function Init() {

		$base_currency = CDeliveryEDOST::GetRUB();

		$profile = array();
		$j = 0;

		// стоимость доставки будет предоставлена позже.
		$profile[0] = array(
			'TITLE' => GetMessage('SALE_DH_EDOST_ERROR_INT'),
			'DESCRIPTION' => '',
			'RESTRICTIONS_WEIGHT' => array(0),
			'RESTRICTIONS_SUM' => array(0),
		);

		for ($i = 1; $i <= DELIVERY_EDOST_TARIFF_QTY; $i++) {
			for ($k = 1; $k <= 2; $k++) {
				if ($k == 1) $insurance = ''; else $insurance = 'S_';
				$j++;
				$profile[$j] = array(
					'TITLE' => GetMessage('SALE_DH_EDOST_TARIFF_'.$insurance.$i),
					'DESCRIPTION' => GetMessage('SALE_DH_EDOST_TARIFFN_'.$insurance.$i),
					'RESTRICTIONS_WEIGHT' => array(0),
					'RESTRICTIONS_SUM' => array(0),
				);
			}
		}

		return array(
			'SID' => 'edost',
			'NAME' => GetMessage('SALE_DH_EDOST_NAME'),
			'DESCRIPTION' => GetMessage('SALE_DH_EDOST_DESCRIPTION'),
			'DESCRIPTION_INNER' => GetMessage('SALE_DH_EDOST_DESCRIPTION_INNER'),
			'BASE_CURRENCY' => $base_currency,

			'HANDLER' => __FILE__,

			'DBGETSETTINGS' => array('CDeliveryEDOST', 'GetSettings'),
			'DBSETSETTINGS' => array('CDeliveryEDOST', 'SetSettings'),
			'GETCONFIG' => array('CDeliveryEDOST', 'GetConfig'),

			'COMPABILITY' => array('CDeliveryEDOST', 'Compability'),
			'CALCULATOR' => array('CDeliveryEDOST', 'Calculate'),

			'PROFILES' => $profile
		);

	}


	function GetConfig() {

		$arConfig = array(
			"CONFIG_GROUPS" => array(
				"all" => GetMessage('SALE_DH_EDOST_CONFIG_TITLE'),
			),

			"CONFIG" => array(
				"id" => array(
					"TYPE" => "TEXT",
					"DEFAULT" => DELIVERY_EDOST_ID_DEFAULT,
					"TITLE" => GetMessage('SALE_DH_EDOST_CONFIG_ID'),
					"GROUP" => "all",
				),
				"ps" => array(
					"TYPE" => "TEXT",
					"DEFAULT" => DELIVERY_EDOST_PS_DEFAULT,
					"TITLE" => GetMessage('SALE_DH_EDOST_CONFIG_PS'),
					"GROUP" => "all",
				),
				"host" => array(
					"TYPE" => "TEXT",
					"DEFAULT" => DELIVERY_EDOST_HOST_DEFAULT,
					"TITLE" => GetMessage('SALE_DH_EDOST_CONFIG_HOST'),
					"GROUP" => "all",
				),
				"hide_err" => array(
					"TYPE" => "CHECKBOX",
					"DEFAULT" => DELIVERY_EDOST_HIDE_ERR_DEFAULT,
					"TITLE" => GetMessage('SALE_DH_EDOST_CONFIG_HIDE_ERR'),
					"GROUP" => "all",
				),
				"show_msg" => array(
					"TYPE" => "CHECKBOX",
					"DEFAULT" => DELIVERY_EDOST_SHOW_MSG_DEFAULT,
					"TITLE" => GetMessage('SALE_DH_EDOST_CONFIG_SHOW_MSG'),
					"GROUP" => "all",
				),
				"show_pickpoint_map" => array(
					"TYPE" => "CHECKBOX",
					"DEFAULT" => DELIVERY_EDOST_SHOW_PICKPOINT_MAP_DEFAULT,
					"TITLE" => GetMessage('SALE_DH_EDOST_CONFIG_SHOW_PICKPOINT_MAP'),
					"GROUP" => "all",
				),
				"codstatus" => array(
					"TYPE" => "DROPDOWN",
					"DEFAULT" => DELIVERY_EDOST_CODSTATUS_DEFAULT,
					"TITLE" => GetMessage('SALE_DH_EDOST_CONFIG_CODSTATUS'),
					"GROUP" => "all",
					"VALUES" => array(),
				),
				"send_zip" => array(
					"TYPE" => "CHECKBOX",
					"DEFAULT" => DELIVERY_EDOST_SEND_ZIP_DEFAULT,
					"TITLE" => GetMessage('SALE_DH_EDOST_CONFIG_SEND_ZIP'),
					"GROUP" => "all",
				),
				"hide_payment" => array(
					"TYPE" => "CHECKBOX",
					"DEFAULT" => DELIVERY_EDOST_HIDE_PAYMENT_DEFAULT,
					"TITLE" => GetMessage('SALE_DH_EDOST_CONFIG_HIDE_PAYMENT'),
					"GROUP" => "all",
				),
				"sort_ascending" => array(
					"TYPE" => "CHECKBOX",
					"DEFAULT" => DELIVERY_EDOST_SORT_ASCENDING_DEFAULT,
					"TITLE" => GetMessage('SALE_DH_EDOST_CONFIG_SORT_ASCENDING'),
					"GROUP" => "all",
				),
			),
		);

		// список статусов заказа
		$dbResultList = CSaleStatus::GetList(array("SORT" => "ASC"), array("LID" => LANGUAGE_ID), false, false, array("ID", "SORT", "LID", "NAME"));
		$arConfig["CONFIG"]["codstatus"]["VALUES"][''] = GetMessage('SALE_DH_EDOST_CONFIG_CODSTATUS_NOCHANGE');
		while ($arResult = $dbResultList->Fetch())
			$arConfig["CONFIG"]["codstatus"]["VALUES"][$arResult['ID']] = '['.$arResult['ID'].'] '.$arResult['NAME'];

		return $arConfig;

	}


	// запись параметров в Option
	function SetOption($arSettings) {

		foreach ($arSettings as $key => $v) {
			if ($v == '' && ($key == 'send_zip' || $key == 'hide_payment')) $v = 'Y';
			COption::SetOptionString('edost.delivery', $key, $v);
		}

		if (defined('DELIVERY_EDOST_ORDER_LINK')) COption::SetOptionString('edost.delivery', 'order_link', DELIVERY_EDOST_ORDER_LINK);

	}


	function GetSettings($strSettings) {

		list($id, $ps, $host, $hide_err, $show_msg, $show_pickpoint_map, $codstatus, $send_zip, $hide_payment, $sort_ascending) = explode(";", $strSettings);

		$r = array(
			"id" => $id,
			"ps" => $ps,
			"host" => $host,
			"hide_err" => $hide_err,
			"show_msg" => $show_msg,
			"show_pickpoint_map" => $show_pickpoint_map,
			"codstatus" => $codstatus,
			"send_zip" => $send_zip,
			"hide_payment" => $hide_payment,
			"sort_ascending" => $sort_ascending,
		);

		if (COption::GetOptionString('edost.delivery', 'id', '') == '') CDeliveryEDOST::SetOption($r);

		return $r;

	}

	function SetSettings($arSettings) {
		CDeliveryEDOST::SetOption($arSettings);
		return $arSettings["id"].";".$arSettings["ps"].";".$arSettings["host"].";".$arSettings["hide_err"].";".$arSettings["show_msg"].";".$arSettings["show_pickpoint_map"].";".$arSettings["codstatus"].";".$arSettings["send_zip"].";".$arSettings["hide_payment"].";".$arSettings["sort_ascending"];
	}


	function __GetLocation($location) {

		$arLocation = CSaleLocation::GetByID($location, 'ru'); // название города или региона (для расчета по России)
//		echo "<pre>".print_r($arLocation, true)."</pre>LANGUAGE_ID=".LANGUAGE_ID." location=".$location;

		if (CDeliveryEDOST::__IsRussian($arLocation)) {
			$city = $arLocation["CITY_NAME_LANG"]; // название города (или региона для bitrix 11 и меньше)
			$city = $GLOBALS['APPLICATION']->ConvertCharset($city, LANG_CHARSET, 'windows-1251');

			$region = (isset($arLocation["REGION_NAME_LANG"]) ? $arLocation["REGION_NAME_LANG"] : '');
			if ($region != '') {
				$region = $GLOBALS['APPLICATION']->ConvertCharset($region, LANG_CHARSET, 'windows-1251');

				// перевод регионов bitrix в стандарт edost
				$region_edost = array('Амурская область', 'Архангельская область', 'Астраханская область', 'Белгородская область', 'Брянская область', 'Владимирская область', 'Волгоградская область', 'Вологодская область', 'Воронежская область', 'Еврейская АО', 'Ивановская область', 'Иркутская область', 'Кабардино-Балкарская Республика', 'Калининградская область', 'Калужская область', 'Карачаево-Черкесская Республика', 'Кемеровская область', 'Кировская область', 'Костромская область', 'Курганская область', 'Курская область', 'Ленинградская область', 'Липецкая область', 'Магаданская область', 'Московская область', 'Мурманская область', 'Нижегородская область', 'Новгородская область', 'Новосибирская область', 'Омская область', 'Оренбургская область', 'Орловская область', 'Пензенская область', 'Псковская область', 'Республика Адыгея', 'Республика Алтай', 'Республика Башкортостан', 'Республика Бурятия', 'Республика Дагестан', 'Республика Ингушетия', 'Республика Калмыкия', 'Республика Карелия', 'Республика Коми', 'Республика Марий Эл', 'Республика Мордовия', 'Республика Саха (Якутия)', 'Республика Северная Осетия - Алания', 'Республика Татарстан', 'Республика Тыва', 'Республика Хакасия', 'Ростовская область', 'Рязанская область', 'Самарская область', 'Саратовская область', 'Сахалинская область', 'Свердловская область', 'Смоленская область', 'Тамбовская область', 'Тверская область', 'Томская область', 'Тульская область', 'Тюменская область', 'Удмуртская Республика', 'Ульяновская область', 'Ханты-Мансийский АО', 'Челябинская область', 'Чеченская Республика', 'Чувашская Республика', 'Ярославская область', 'Республика Крым');
				$region_bitrix = array('Амурская обл', 'Архангельская обл', 'Астраханская обл', 'Белгородская обл', 'Брянская обл', 'Владимирская обл', 'Волгоградская обл', 'Вологодская обл', 'Воронежская обл', 'Еврейская Аобл', 'Ивановская обл', 'Иркутская обл', 'Кабардино-Балкарская Респ', 'Калининградская обл', 'Калужская обл', 'Карачаево-Черкесская Респ', 'Кемеровская обл', 'Кировская обл', 'Костромская обл', 'Курганская обл', 'Курская обл', 'Ленинградская обл', 'Липецкая обл', 'Магаданская обл', 'Московская обл', 'Мурманская обл', 'Нижегородская обл', 'Новгородская обл', 'Новосибирская обл', 'Омская обл', 'Оренбургская обл', 'Орловская обл', 'Пензенская обл', 'Псковская обл', 'Адыгея Респ', 'Алтай Респ', 'Башкортостан Респ', 'Бурятия Респ', 'Дагестан Респ', 'Ингушетия Респ', 'Калмыкия Респ', 'Карелия Респ', 'Коми Респ', 'Марий Эл Респ', 'Мордовия Респ', 'Саха /Якутия/ Респ', 'Северная Осетия - Алания Респ', 'Татарстан Респ', 'Тыва Респ', 'Хакасия Респ', 'Ростовская обл', 'Рязанская обл', 'Самарская обл', 'Саратовская обл', 'Сахалинская обл', 'Свердловская обл', 'Смоленская обл', 'Тамбовская обл', 'Тверская обл', 'Томская обл', 'Тульская обл', 'Тюменская обл', 'Удмуртская Респ', 'Ульяновская обл', 'Ханты-Мансийский Автономный округ - Югра АО', 'Челябинская обл', 'Чеченская Респ', 'Чувашская Респ', 'Ярославская обл', 'Крым Респ');
				for ($i = 0; $i < count($region_bitrix); $i++)
					if ($region == $region_bitrix[$i]) {
						$region = $region_edost[$i];
						break;
					}

				if ($city == '') $city = $region;
				else {
					// города, для которых требуется дописывать регион
					$city_with_region = array('Березовский', 'Благовещенск', 'Горняк', 'Железногорск', 'Заводской', 'Заречный', 'Киров', 'Кировск', 'Красноармейск', 'Мирный', 'Павловск', 'Радужный', 'Советск', 'Строитель', 'Троицк', 'Фокино');
					if (in_array($city, $city_with_region)) $city .= ' ('.$region.')';
				}
			}

            $arReturn["CITY"] = $city;
		}
		else {
			$country = $arLocation["COUNTRY_NAME_LANG"]; // название страны для расчета за границу
			$country = $GLOBALS['APPLICATION']->ConvertCharset($country, LANG_CHARSET, 'windows-1251');

			// перевод стран в стандарт eDost
			$country_edost = array('Конго, Демократическая респ.', 'Корея, Северная', 'Корея, Южная');
			$country_bitrix = array('Конго Демократическая респ.', 'Корея Северная', 'Корея Южная');
			for ($i = 0; $i < count($country_bitrix); $i++)
				if ($country == $country_bitrix[$i]) {
					$country = $country_edost[$i];
					break;
				}

			$arReturn["CITY"] = $country;
		}

		return $arReturn;

	}


	function __IsRussian($arLocation)
	{

        if (function_exists('mb_strtoupper')) {
			$countryNameOrig	= mb_strtoupper($arLocation["COUNTRY_NAME_ORIG"], LANG_CHARSET);
			$countryNameShort	= mb_strtoupper($arLocation["COUNTRY_SHORT_NAME"], LANG_CHARSET);
			$countryNameLang	= mb_strtoupper($arLocation["COUNTRY_NAME_LANG"], LANG_CHARSET);
		}
		else {
			$countryNameOrig	= ToUpper($arLocation["COUNTRY_NAME_ORIG"]);
			$countryNameShort	= ToUpper($arLocation["COUNTRY_SHORT_NAME"]);
			$countryNameLang	= ToUpper($arLocation["COUNTRY_NAME_LANG"]);
		}

		return
			($countryNameOrig == GetMessage("SALE_DH_EDOST_RUSSIA")
			|| $countryNameShort == GetMessage("SALE_DH_EDOST_RUSSIA")
			|| $countryNameLang == GetMessage("SALE_DH_EDOST_RUSSIA")
			|| $countryNameOrig == "RUSSIA"
			|| $countryNameShort == "RUSSIA"
			|| $countryNameLang == "RUSSIA"
			|| $countryNameOrig == GetMessage("SALE_DH_EDOST_RUSSIAN_FEDERATION")
			|| $countryNameShort == GetMessage("SALE_DH_EDOST_RUSSIAN_FEDERATION")
			|| $countryNameLang == GetMessage("SALE_DH_EDOST_RUSSIAN_FEDERATION")
			|| $countryNameOrig == "RUSSIAN FEDERATION"
			|| $countryNameShort == "RUSSIAN FEDERATION"
			|| $countryNameLang == "RUSSIAN FEDERATION");

	}


	function Calculate($profile, $arConfig, $arOrder, $STEP) {
//		echo "<br>Calculate ============<br><pre>"; print_r($arOrder); echo "</pre>============<br>";

		if ($STEP >= 3)
			return array(
				'RESULT' => 'ERROR',
				'TEXT' => GetMessage('SALE_DH_EDOST_ERROR_CONNECT'),
			);

		$r = CDeliveryEDOST::calc($arOrder, $arConfig);

		if ($r['qty_company'] > 0)
			for ($i = 1; $i <= $r['qty_company']; $i++)
				if ($r['id'.$i] <= DELIVERY_EDOST_TARIFF_QTY) {
					if ($r['strah'.$i] == 1) $id_prof = $r['id'.$i]*2; else $id_prof = $r['id'.$i]*2 - 1;

					if ($profile == $id_prof)
						return array(
							'RESULT' => 'OK',
							'VALUE' => $r['price'.$i],
							'TRANSIT' => $r['day'.$i],
						);
				}

		return array(
			'RESULT' => 'OK',
			'VALUE' => 0,
			'TRANSIT' => ''
		);

	}

	function Compability($arOrder, $arConfig) {
//		echo "<br>Compability ============<br><pre>"; print_r($arOrder); echo "</pre>============<br>";

		$r = CDeliveryEDOST::calc($arOrder, $arConfig);
		$rz = array();

		for ($i = 1; $i <= $r['qty_company']; $i++)
			if ($r['id'.$i] <= DELIVERY_EDOST_TARIFF_QTY) {
				if ($r['strah'.$i] == 1) $id_prof = $r['id'.$i]*2; else $id_prof = $r['id'.$i]*2 - 1;
				$rz[] = $id_prof;
			}

		// нулевой тариф "Стоимость доставки будет предоставлена позже" (используется также для отображения ошибки)
		if ($r['qty_company'] == 0)
            if (!(isset($r['hide']) && $r['hide'] == 'Y') && ($arConfig['hide_err']['VALUE'] == 'N' || $arConfig['show_msg']['VALUE'] == 'Y')) $rz = array(0);

		return $rz;

	}


	// получение тарифа по коду
	function GetEdostTariff($id) {

		$r = self::$rezAll;

		if ($r['qty_company'] > 0)
			for ($i = 1; $i <= $r['qty_company']; $i++)
				if ($r['id'.$i] <= DELIVERY_EDOST_TARIFF_QTY) {
					if ($r['strah'.$i] == 1) $id_prof = $r['id'.$i]*2; else $id_prof = $r['id'.$i]*2 - 1;

					if ($id == $id_prof)
						return array(
							"result" => "OK",
							"price" => $r['price'.$i],
							"priceinfo" => (isset($r['priceinfo'.$i]) ? $r['priceinfo'.$i] : 0),
							"day" => ((isset($r['day'.$i]) && $r['day'.$i] != '&nbsp;') ? $r['day'.$i] : ''),
							"pricecash" => $r['pricecash'.$i],
							"transfer" => $r['transfer'.$i],
							"pickpointmap" => (isset($r['pickpointmap'.$i]) ? $r['pickpointmap'.$i] : ''),
							"office" => (isset($r['office'.$i]) ? $r['office'.$i] : array()),
						);
				}

        // тариф не найден - вывод ошибки
		$st = GetMessage("SALE_DH_EDOST_ERROR_TXT");
		if ($r['stat'] >= 2 && $r['stat'] <= 14) $st .= GetMessage("SALE_DH_EDOST_ERROR".$r['stat']);
		else $st .= GetMessage("SALE_DH_EDOST_ERROR_NOSHIPPING");

		return array('result' => 'ERR', 'error' => $st, 'new_name' => $r['package_err']);

	}


	// получение кода рублевой валюты
	function GetRUB()
	{

        $currency = 'RUB';
		if (CCurrency::GetByID('RUR')) $currency = 'RUR';
		if (CCurrency::GetByID('RUB')) $currency = 'RUB';

		return $currency;

	}

	// получение упаковки (для модуля edost.package)
	function GetEdostPackage() {
		$r = self::$rezAll;
		return (isset($r['package']) ? $r['package'] : '');
	}

	// получение предупреждения (warning)
	function GetEdostWarning() {

        $s = '';
		$r = self::$rezAll;

		if (isset($r['warning']) && count($r['warning']) > 0) {
			foreach ($r['warning'] as $w)
				if ($w >= 1 && $w <= 2) $s .= GetMessage('SALE_DH_EDOST_WARNING'.$w).'<br>';

			$head = GetMessage('SALE_DH_EDOST_WARNING');
			if ($head != '' && $s != '') $s = $head.'<br>'.$s;
		}

		return $s;

	}

	function CommaToPoint(&$n) {
		if (isset($n)) $n = str_replace(',', '.', $n);
	}

	function SetRez($data) {
		self::$rezAll = $data;
		return $data;
	}


	// расчет доставки
	function calc($arOrder, $arConfig) {
//		echo "arOrder:<br><pre>"; print_r($arOrder); echo "</pre>";

		if (!(self::$rezAll == null || (isset($arOrder['NO_LOCAL_CACHE']) && $arOrder['NO_LOCAL_CACHE'] == 'Y'))) return self::$rezAll;

		$edost_CALC = new edost_class();

		$weight_zero = false;
		$total_weight = 0;
		$total_price = 0;
		$length = 0; $width = 0; $height = 0;
		$size_in = array();


		if (!isset($arOrder['ITEMS']) && isset($arOrder['BASKET_ITEMS'])) $arOrder['ITEMS'] = $arOrder['BASKET_ITEMS']; // поддержка старых параметров (до битрикс 14)

		$cart = (!isset($arOrder['CART']) ? 'Y' : $arOrder['CART']);
		if (isset($arOrder['NO_CART']) && $arOrder['NO_CART'] == 'Y') $cart = (isset($arOrder['ADD_CART']) && $arOrder['ADD_CART'] == 'Y' ? 'DOUBLE' : 'N'); // поддержка старых параметров (до версии 1.2.0)

		$currency = CSaleLang::GetLangCurrency(isset($arOrder['SITE_ID']) ? $arOrder['SITE_ID'] : SITE_ID);
		$base_currency = CDeliveryEDOST::GetRUB();

		$weight_default = (defined('DELIVERY_EDOST_WEIGHT_DEFAULT') ? DELIVERY_EDOST_WEIGHT_DEFAULT : 0);

		$prop_weight = (defined('DELIVERY_EDOST_WEIGHT_PROPERTY_NAME') ? 'PROPERTY_'.DELIVERY_EDOST_WEIGHT_PROPERTY_NAME.'_VALUE' : '');
		$prop_measure = (defined('DELIVERY_EDOST_WEIGHT_PROPERTY_MEASURE') ? DELIVERY_EDOST_WEIGHT_PROPERTY_MEASURE : 'G');

		$prop_volume = (defined('DELIVERY_EDOST_VOLUME_PROPERTY_NAME') ? 'PROPERTY_'.DELIVERY_EDOST_VOLUME_PROPERTY_NAME.'_VALUE' : '');
		$prop_ratio = (defined('DELIVERY_EDOST_VOLUME_PROPERTY_RATIO') ? DELIVERY_EDOST_VOLUME_PROPERTY_RATIO : 1);

		$arSelect = array("ID", "NAME", "PROPERTY_".DELIVERY_EDOST_LENGTH_PROPERTY_NAME, "PROPERTY_".DELIVERY_EDOST_HEIGHT_PROPERTY_NAME, "PROPERTY_".DELIVERY_EDOST_WIDTH_PROPERTY_NAME);
		if ($prop_weight != '') $arSelect[] = "PROPERTY_".DELIVERY_EDOST_WEIGHT_PROPERTY_NAME;
		if ($prop_volume != '') $arSelect[] = "PROPERTY_".DELIVERY_EDOST_VOLUME_PROPERTY_NAME;

		$prop_size = array('PROPERTY_'.DELIVERY_EDOST_LENGTH_PROPERTY_NAME.'_VALUE', 'PROPERTY_'.DELIVERY_EDOST_WIDTH_PROPERTY_NAME.'_VALUE', 'PROPERTY_'.DELIVERY_EDOST_HEIGHT_PROPERTY_NAME.'_VALUE');

		$weight_from_main_product = (defined('DELIVERY_EDOST_WEIGHT_FROM_MAIN_PRODUCT') && DELIVERY_EDOST_WEIGHT_FROM_MAIN_PRODUCT == 'Y' ? true : false);
		$property_from_main_product = (defined('DELIVERY_EDOST_PROPERTY_FROM_MAIN_PRODUCT') && DELIVERY_EDOST_PROPERTY_FROM_MAIN_PRODUCT == 'Y' ? true : false);

		$write_log = (defined('DELIVERY_EDOST_WRITE_LOG') && DELIVERY_EDOST_WRITE_LOG == 1 ? true : false);


		// модуль упаковки
		if (class_exists(CEdostPackage) && COption::GetOptionString('edost.package', 'package_activate', 'Y') == 'Y') {
            $items = array();  // товары для модуля упаковки
			$run_package = true;
		}
		else $run_package = false;


		// получение данных по товарам в $arOrder['ITEMS'] ИЛИ в корзине ИЛИ по коду заказа
		if ($cart != 'N') if (CModule::IncludeModule("iblock")) {
			$arBasketItems = array();

			if (isset($arOrder['ITEMS'])) {
				// товары из списка
				if (is_array($arOrder['ITEMS']) && count($arOrder['ITEMS']) > 0)
					foreach ($arOrder['ITEMS'] as $key => $arItems)
						if ((!isset($arItems['CAN_BUY']) || $arItems['CAN_BUY'] == 'Y') && (!isset($arItems['DELAY']) || $arItems['DELAY'] == 'N') && isset($arItems['QUANTITY']) && $arItems['QUANTITY'] > 0) $arBasketItems[] = $arItems;
			}
			else {
				// товары из корзины ИЛИ заказа
				if (isset($arOrder['ORDER_ID']) && $arOrder['ORDER_ID'] > 0 && isset($arOrder['SITE_ID'])) $arFilter = array('ORDER_ID' => $arOrder['ORDER_ID'], 'LID' => $arOrder['SITE_ID']);
				else $arFilter = array('FUSER_ID' => CSaleBasket::GetBasketUserID(), 'LID' => SITE_ID, 'ORDER_ID' => 'NULL');

				$dbBasketItems = CSaleBasket::GetList(
					array('NAME' => 'ASC', 'ID' => 'ASC'), $arFilter, false, false,
			        array('ID', 'CALLBACK_FUNC', 'MODULE', 'PRODUCT_ID', 'QUANTITY', 'DELAY', 'CAN_BUY', 'PRICE', 'WEIGHT')
				);

				while ($arItems = $dbBasketItems->Fetch())
					if ($arItems['CAN_BUY'] == 'Y' && $arItems['DELAY'] == 'N' && isset($arItems['QUANTITY']) && $arItems['QUANTITY'] > 0) $arBasketItems[] = $arItems;
			}

			foreach ($arBasketItems as $key => $arItems) {
//				echo '<br>arItems edost module ('.$key.'): <pre style="font-size: 12px">'; print_r($arItems); echo "</pre>";

				$weight = (isset($arItems['WEIGHT']) && $arItems['WEIGHT'] > 0 ? $arItems['WEIGHT'] : 0);
				$v = (isset($arItems['DIMENSIONS']) ? $arItems['DIMENSIONS'] : '');

				// использовать новый интерфейс IBXSaleProductProvider !!!!!
				if (isset($arItems['MODULE']) && isset($arItems['CALLBACK_FUNC']) && strlen($arItems['CALLBACK_FUNC']) > 0) {
					CSaleBasket::UpdatePrice($arItems['ID'], $arItems['CALLBACK_FUNC'], $arItems['MODULE'], $arItems['PRODUCT_ID'], $arItems['QUANTITY']);
					$arItems = CSaleBasket::GetByID($arItems['ID']);
				}

				// получение данных из главного товара по id торгового предложения (включается в константах)
				if (isset($arItems['PRODUCT_ID']) && ($weight_from_main_product || $property_from_main_product)) {
					$main_product = CCatalogSku::GetProductInfo($arItems['PRODUCT_ID']);
                    if (isset($main_product['ID']) && $main_product['ID'] > 0) {
	                    if ($weight_from_main_product && $weight == 0) {
							$ar = CCatalogProduct::GetByID($main_product['ID']);
//							echo '<br>main_product '.$main_product['ID'].':<pre>'; print_r($ar); echo '</pre>';
							if (isset($ar['WEIGHT']) && $ar['WEIGHT'] > 0) $weight = $ar['WEIGHT'];
						}

						if ($property_from_main_product) $arItems['PRODUCT_ID'] = $main_product['ID'];
					}
				}

				$s = array((isset($v['LENGTH']) ? $v['LENGTH'] : 0), (isset($v['WIDTH']) ? $v['WIDTH'] : 0), (isset($v['HEIGHT']) ? $v['HEIGHT'] : 0));

				// загрузка свойств товара (если не задан вес или габариты)
				if ($weight == 0 || $s[0] == 0 || $s[1] == 0 || $s[2] == 0) {
					$tmpElementRes = CIBlockElement::GetById($arItems['PRODUCT_ID']);
					$tmpElement = $tmpElementRes->Fetch();
					$res = CIBlockElement::GetList(array(), array('ID' => $arItems['PRODUCT_ID'], 'IBLOCK_ID' => $tmpElement['IBLOCK_ID']), false, Array('nPageSize' => 5), $arSelect);
					if ($ar = $res->GetNext()) {
//						echo '<br>res_ar: <pre style="font-size: 12px">'; print_r($ar); echo '</pre>';

						if ($weight == 0 && $prop_weight != '' && isset($ar[$prop_weight])) {
						    CDeliveryEDOST::CommaToPoint($ar[$prop_weight]);
						    if ($ar[$prop_weight] > 0) {
							    $weight = $ar[$prop_weight];
							    if ($prop_measure == 'KG') $weight = CSaleMeasure::Convert($weight, 'KG', 'G');
							}
						}

						if ($s[0] == 0 || $s[1] == 0 || $s[2] == 0) {
							$s = array(0, 0, 0);
							foreach ($prop_size as $k => $v) if (isset($ar[$v])) {
								CDeliveryEDOST::CommaToPoint($ar[$v]);
								if ($ar[$v] > 0) $s[$k] = $ar[$v];
							}

							// если габаритов нет, но задан объем, тогда габариты вычисляются по объему
							if ($s[0] == 0 && $s[1] == 0 && $s[2] == 0) {
								$volume = 0;
								if ($prop_volume != '' && isset($ar[$prop_volume])) {
									CDeliveryEDOST::CommaToPoint($ar[$prop_volume]);
									if ($ar[$prop_volume] > 0) $volume = $ar[$prop_volume];
								}
								$s[0] = $s[1] = $s[2] = pow($volume, 1/3) * $prop_ratio;
							}
						}
					}
				}

				// если задано только два размера, тогда считается, что это труба (длина и диаметр)
				if ($s[0] > 0 && $s[1] > 0 && $s[2] == 0) $s[2] = $s[1];
				if ($s[0] > 0 && $s[2] > 0 && $s[1] == 0) $s[1] = $s[2];

				if ($s[0] > 0 && $s[1] > 0 && $s[2] > 0) {
					if ($run_package) $items[] = array('X' => $s[0], 'Y' => $s[1], 'Z' => $s[2], 'NUMBER' => $arItems['QUANTITY']);
					else $size_in[] = $edost_CALC->SumSizeOneGoods($s[0], $s[1], $s[2], $arItems['QUANTITY']);
				}

				if ($weight == 0) $weight = $weight_default;
				if ($weight == 0) $weight_zero = true;
				$weight = $weight * $arItems['QUANTITY'];

				$total_weight += $weight;
				$total_price += CCurrencyRates::ConvertCurrency($arItems['PRICE'], isset($arItems['CURRENCY']) ? $arItems['CURRENCY'] : $currency, $base_currency) * $arItems['QUANTITY'];
//				echo '<br>weight: <b>'.$weight. '</b>, total_weight: <b>'.$total_weight.'</b> - price: <b>'.$arItems['PRICE'].'</b>, total_price: <b>'.$total_price.'</b> - quantity: <b>'.$arItems['QUANTITY'].'</b><pre>'; print_r($s); echo '</pre>';
			}
		}


		if (defined('DELIVERY_EDOST_IGNORE_ZERO_WEIGHT') && DELIVERY_EDOST_IGNORE_ZERO_WEIGHT == 'Y') $weight_zero = false;


		if ($cart == 'Y') {
			if ($weight_zero) $arOrder['WEIGHT'] = 0;
			else if ($total_weight > 0) $arOrder['WEIGHT'] = $total_weight;

			if ($total_price > 0) $arOrder['PRICE'] = $total_price;
		}
		else {
			$x = (isset($arOrder['LENGTH']) && $arOrder['LENGTH'] > 0 ? $arOrder['LENGTH'] : 0);
			$y = (isset($arOrder['WIDTH']) && $arOrder['WIDTH'] > 0 ? $arOrder['WIDTH'] : 0);
			$z = (isset($arOrder['HEIGHT']) && $arOrder['HEIGHT'] > 0 ? $arOrder['HEIGHT'] : 0);
			$qty = (isset($arOrder['QUANTITY']) && intval($arOrder['QUANTITY']) > 0 ? intval($arOrder['QUANTITY']) : 1);

			$arOrder['WEIGHT'] = $arOrder['WEIGHT'] * $qty;
			$arOrder['PRICE'] = $arOrder['PRICE'] * $qty;

			if ($cart == 'DOUBLE') {
				if ($weight_zero) $arOrder['WEIGHT'] = 0;
				else {
					$arOrder['WEIGHT'] += $total_weight;
					$arOrder['PRICE'] += $total_price;
				}
			}
			else {
				$items = array();
				$size_in = array();
			}

			if ($x > 0 && $y > 0 && $z > 0) {
				if ($run_package) $items[] = array('X' => $x, 'Y' => $y, 'Z' => $z, 'NUMBER' => $qty);
				else $size_in[] = $edost_CALC->SumSizeOneGoods($x, $y, $z, $qty);
			}
		}


		$package = array();
		if ($arOrder['WEIGHT'] > 0) {
			if ($run_package) {
				$package = CEdostPackage::CalcPackage($items); // подбор оптимального ящика для упаковки всех товаров
				$length	= $package['X'];
				$width	= $package['Y'];
				$height	= $package['Z'];
				$arOrder['WEIGHT'] += $package['WEIGHT'];
				$arOrder['PRICE'] += $package['COST'];
			}
			else {
				$size = $edost_CALC->SumSize($size_in); // суммируем габариты всех товаров
				$length	= $size['length'];
				$width	= $size['width'];
				$height	= $size['height'];
			}
		}


		$to_zip = (!(isset($arConfig['send_zip']['VALUE']) && $arConfig['send_zip']['VALUE'] == 'N') && isset($arOrder['LOCATION_ZIP']) ? substr($arOrder['LOCATION_ZIP'], 0, 8) : '');


		// загрузка старого расчета из кэша
		$cache_id = 'sale|11.0.0|edost|'.$arOrder['LOCATION_FROM'].'|'.$arOrder['LOCATION_TO'].'|'.$arOrder['WEIGHT'].'|'.ceil($arOrder['PRICE']).'|'.$length.'|'.$width.'|'.$height.'|'.$to_zip;
//		echo "<br><b>cache_id:</b> ".$cache_id;

		$obCache = new CPHPCache();
		if ($obCache->InitCache(DELIVERY_EDOST_CACHE_LIFETIME, $cache_id, "/")) {
//			echo "<br>OLD data from cache";
			$r = $obCache->GetVars();
			return self::SetRez($r);
		}


		$arOrder['WEIGHT'] = CSaleMeasure::Convert($arOrder['WEIGHT'], 'G', 'KG');

		$arLocationTo = CDeliveryEDOST::__GetLocation($arOrder['LOCATION_TO']);
		$to_city = $arLocationTo['CITY']; // местоположение в кодировке 'windows-1251'
//		echo "<br><br>LOCATION_ZIP: ".$arOrder['LOCATION_ZIP']."<br>LOCATION_TO: ".$arOrder["LOCATION_TO"]."<br><br>arLocationTo: <pre>"; print_r($arLocationTo); echo "</pre>";

		// отключение модуля для заданных местоположений
		if (defined('DELIVERY_EDOST_LOCATION_DISABLE') && DELIVERY_EDOST_LOCATION_DISABLE != '') {
			$ar = explode('|', DELIVERY_EDOST_LOCATION_DISABLE);
			$to_city_utf = $GLOBALS['APPLICATION']->ConvertCharset($to_city, 'windows-1251', 'utf-8');
			foreach ($ar as $v) if ($v != '' && ($v == $arOrder['LOCATION_TO'] || $v == $to_city || $v == $to_city_utf))
				return self::SetRez(array('qty_company' => 0, 'stat' => 5, 'hide' => 'Y'));
		}

		// ошибка "Не задан вес"
		if (!($arOrder['WEIGHT'] > 0)) return self::SetRez(array('qty_company' => 0, 'stat' => 11));

		// ошибка "В выбранное местоположение расчет доставки не производится" (неверная контрольная сумма)
		if ($to_city == '' || $edost_CALC->bad_city($to_city)) return self::SetRez(array('qty_company' => 0, 'stat' => 5));


		// параметры запроса на сервер расчетов
		$arQuery = array();
		$arQuery[] = "to_city=".urlencode($to_city);
		$arQuery[] = "strah=".urlencode($arOrder['PRICE']); // стоимость заказа для страховки отправления
		$arQuery[] = "id=".$arConfig["id"]["VALUE"]; // id магазина
		$arQuery[] = "p=".$arConfig["ps"]["VALUE"]; // пароль к серверу расчетов
		$arQuery[] = "weight=".urlencode($arOrder["WEIGHT"]);
		$arQuery[] = "ln=".$length;
		$arQuery[] = "wd=".$width;
		$arQuery[] = "hg=".$height;
		if ($to_zip != '') $arQuery[] = "zip=".$to_zip;

		// сервер расчетов (задан в настройках ИЛИ стандартный)
		$host = trim(strtolower($arConfig['host']['VALUE']));
		if (substr($host, 0, 7) == 'http://') $host = substr($host, 7, 100);
		if (substr($host, 0, 4) == 'www.') $host = substr($host, 4, 100);

		if ($host == '') {
			$standard_server = true;
			$host = COption::GetOptionString('edost.delivery', 'edost_cur_host', DELIVERY_EDOST_SERVER);
		}
		else $standard_server = false;
//		echo '<br>host: '.$host.' - '.$standard_server;


		// запрос на сервер расчетов
		$data = $edost_CALC->ManualPOST('http://'.$host.DELIVERY_EDOST_SERVER_PAGE, implode('&', $arQuery));
		if ($write_log) CDeliveryEDOST::__Write2Log($GLOBALS['APPLICATION']->ConvertCharset($to_city, 'windows-1251', 'utf-8').', '.$arOrder["WEIGHT"].' kg, '.$arOrder['PRICE'].' rub, '.$length.'x'.$width.'x'.$height.' - '.date("Y.m.d H:i:s")."\r\n".$data);
/*
		// запрос на сервер расчетов через bitrix api
		$error_number = 0;
		$error_text = "";
		$data = QueryGetData($host, DELIVERY_EDOST_SERVER_PORT, DELIVERY_EDOST_SERVER_PAGE, implode("&", $arQuery), $error_number, $error_text, DELIVERY_EDOST_SERVER_METHOD);
*/

		// обработка ответа
		$r = $edost_CALC->edost_calc($data);

		// переключение на второй стандартный сервер, если первый не отвечает (код ошибки 8)
		if ($r['stat'] == 8 && $standard_server) {
			if ($host == DELIVERY_EDOST_SERVER) COption::SetOptionString('edost.delivery', 'edost_cur_host', DELIVERY_EDOST_SERVER2);
			else COption::SetOptionString('edost.delivery', 'edost_cur_host', DELIVERY_EDOST_SERVER);
		}

		// перевод текстовых значений в кодировку сайта
		for ($i = 1; $i <= $r['qty_company']; $i++) {
			$r['day'.$i] = $GLOBALS['APPLICATION']->ConvertCharset($r['day'.$i], 'utf-8', LANG_CHARSET);
			if (LANGUAGE_ID <> 'ru') $r['day'.$i] = preg_replace("/[^0-9-]/i", "", $r['day'.$i]); // стираем слово "дней", если язык не русский
			$r['company'.$i] = $GLOBALS['APPLICATION']->ConvertCharset($r['company'.$i], 'utf-8', LANG_CHARSET);
			$r['name'.$i] = $GLOBALS['APPLICATION']->ConvertCharset($r['name'.$i], 'utf-8', LANG_CHARSET);

			if (isset($r['office'.$i])) {
				for ($h = 0; $h < count($r['office'.$i]); $h++) {
					$ar_fields = array('code', 'name', 'address', 'tel', 'schedule');
					foreach ($ar_fields as $field) if (isset($r['office'.$i][$h][$field]))
						$r['office'.$i][$h][$field] = $GLOBALS['APPLICATION']->ConvertCharset($r['office'.$i][$h][$field], 'utf-8', LANG_CHARSET);
				}
			}
		}
//		echo '<br><b>edost result:</b><pre style="font-size: 12px">'; print_r($r); echo "</pre>";


		// модуль упаковки
		if ($run_package) {
			if (count($package) > 0 && $package['PACKAGES'] <> '') {
				$r['package'] = $package['PACKAGES'];
				if ($package['COST'] > 0)
					for ($i = 1; $i <= $r['qty_company']; $i++) {
						$r['price'.$i] += $package['COST'];
						if (isset($r['pricecash'.$i]) && $r['pricecash'.$i] >= 0) $r['pricecash'.$i] += $package['COST'];
					}
			}
			else $r = array('qty_company' => 0, 'stat' => 0, 'package_err' => COption::GetOptionString('edost.package', 'no_package', ''));
		}


		if ($r['qty_company'] == 0 && $r['stat'] != 1) {
			$st = ($r['stat'] >= 2 && $r['stat'] <= 14 ? GetMessage('SALE_DH_EDOST_ERROR'.$r['stat']) : '');
			if ($write_log) CDeliveryEDOST::__Write2Log($GLOBALS['APPLICATION']->ConvertCharset(GetMessage('SALE_DH_EDOST_ERROR_TXT').$r['stat'].' ('.$st.')', LANG_CHARSET, 'utf-8'));
		}
		else {
			$obCache->StartDataCache();
			$obCache->EndDataCache($r);
		}

		return self::SetRez($r);

	}


	function __Write2Log($data)
	{

		if (!(defined('DELIVERY_EDOST_WRITE_LOG') && DELIVERY_EDOST_WRITE_LOG === 1)) return;

		$fp = fopen(dirname(__FILE__)."/edost.log", "a");
		fwrite($fp, "\r\n==========================================\r\n");
		fwrite($fp, $data);
		fclose($fp);

	}
}


AddEventHandler('sale', 'onSaleDeliveryHandlersBuildList', array('CDeliveryEDOST', 'Init'));


class edost_class {
	var $index, $curElem;
	var $status = 0;
	var $parser;
	var $rz;
	var $rz_office;
	var $warning;

	public static $NowErr = false;
	public static $crcmas=array(0,18462,45420,2744,54398,20162,14445,27629,59073,60222,948,9395,52515,56904,46371,58818,63979,46532,8753,8669,41103,54767,26215,391,56900,16214,12740,52508,23756,38183,23101,31036,61691,10024,1435,47230,59397,32974,46645,14929,3792,35982,44771,9852,28609,17150,16613,17034,59958,2921,10325,721,19263,4631,53269,20632,24147,14623,8906,65407,5339,26767,35388,44706,26159,43362,24606,18431,42918,47660,56931,20959,3425,60623,22452,2588,20041,16017,54969,48859,56178,47736,51011,24944,46863,63399,44317,28029,3628,3198,26674,33703,12233,15340,33387,35925,17103,6428,43154,28394,14317,34448,3885,64287,5304,26743,53220,65021,20133,32531,7338,9253,39165,8041,26997,58589,60356,42452,43737,39154,51658,63848,46582,57219,45403,1486,57775,35608,55295,27338,44515,29407,46562,35424,42348,7885,5087,65105,7830,2523,41102,44713,27865,56387,65130,28479,39572,52439,7203,57634,55542,19999,23263,32071,23026,49608,41247,24098,2389,39269,42938,42005,347,32788,43570,24086,33792,43044,60255,45333,23919,34826,13287,29963,8496,15679,23834,15044,26783,7838,1874,1766,25558,64610,32746,13944,18233,14005,3318,6100,3364,47597,1466,65306,41904,22494,11367,61884,37419,62027,32762,37709,2014,10943,47599,52375,24865,20951,45559,35469,920,9020,15882,8343,20902,56922,28423,9524,33761,2560,11889,48408,12742,42566,47956,58708,9869,23455,46135,2600,15403,58127,12162,18018,30970,15788,51416,899,33572,35260,18528,15119,21393,52586,43626,52707,25163,32073,43049,12569,37950,58593,22467,8047,56883,62460,58302,52211,1798,13328,34911,38627,7526,41384,13078,49639,23923,35986,32541,39121,5046,26583,28602,26867,26975,28026,51949,55374,55297,62035,38361,11233,33983,47336,31970,38161,20771,40664,59394,23326,37890,33862,21701,27169,17540,10447,52874,23697,23175,9861,21139,59991,20246,57658,44355,25813,28035,46932,21910,167,54713,8581,24595,38117,25216,6679,49649,44604,23002,22719,1506,64360,29753,36038,12884,36707,24139,19471,49002,5708,58982,50787,4807,59108,27008,50769,34614,51610,52768,55490,5042,24824,9711,58547,57659,59120,54338,24726,15686,33482,54743,40939,49818,14762,4985,1252,17271,60062,63608,35221,65411,28580,15917,29488,40623,41702,39734,37252,2163,29758,8504,8636,25613,40972,2505,63628,44187,27274,36338,37773,44582,22288,55897,20709,61848,29464,24474,61806,64815,45110,61456,33246,40854,48595,38224,61256,44411,14982,13319,44272,13823,60586,50527,63309,38395,3625,36885,595,24561,50039,56577,65085,29797,60302,18340,15884,3446,54002,22308,17065,40903,33073,65057,53110,8458,911,63533,26128,4893,32312,38837,24730,50513,9785,58423,64003,34150,52163,134,17062,16633,51693,64400,13280,64573,15155,29438,11006,46995,8423,43705,42664,1067,28221,57672,19028,13110,21126,27965,8260,24019,63901,31753,30827,30735,7371,2640,35179,366,5247,367,54647,52570,20438,4065,37098,51115,59946,61346,45807,49001,19025,29153,49694,64531,52923,20005,26697,55137,7289,39629,11613,24490,10046,54020,54447,21859,21506,32915,43996,32129,5504,3632,35472,59027,14681,52365,36191,48237,55752,41284,4119,10200,24653,61305,49001,26524,22150,38929,8369,57730,6783,53583,43450,51799,8977,48979,44804,39742,44070,7173,51851,2367,39104,8091,8265,9734,49735,61560,38508,8013,43039,24888,41733,16308,58716,7103,63910,61459,23234,56632,31514,40997,10854,35825,7869,14054,7408,3583,58766,48161,40740,31624,2367,5762,9692,16155,28052,15472,33263,32779,1476,3990,34168,10114,64877,12051,23510,21448,28188,53399,35873,6130,57749,63469,15367,24439,64067,54346,46265,2978,24093,26202,53738,12026,62881,17474,22697,25895,36143,30493,39807,10478,1069,39386,14434,39982,50095,8407,13227,6906,21893,41416,55878,7763,52278,49437,1011,46667,29622,55346,31389,39088,52241,64177,61835,56693,53592,2407,25244,34557,13513,27162,65041,56076,46646,60106,14113,34177,36290,58463,787,2038,30951,21257,55650,53906,11529,12142,58623,18406,31748,43654,42027,22313,20921,46925,8907,8301,54227,44489,2552,47950,64219,2393,14263,4395,20106,61172,7574,923,6145,37339,30740,14062,6712,29,35926,25239,45102,61607,59658,7669,573,21557,39278,45588,44489,30885,47826,40813,65452,63131,8427,50026,13683,45133,52822,5055,40539,15164,21854,44022,43529,27322,55865,41452,12390,21575,25194,27249,27944,56525,7569,61594,18762,25515,30793,9694,38297,55792,62380,10120,64739,3495,49730,24984,23470,2449,32866,45717,16052,47874,5319,35765,64946,15027,48221,31734,40104,923,27379,59816,57291,36757,27841,9942,13174,33711,17095,41988,15612,61620,65350,13847,24327,36319,43374,56623,684,33955,4957,21220,52456,23819,42710,44648,22088,43123,63233,26013,40945,43372,49649,52425,59918,61137,27887,5880,9738,27036,53543,18606,37956,11949,13065,55674,12692,27328,29467,41233,12275,51376,31176,30475,14306,34757,21431,34498,5820,48773,16136,64836,29747,59222,43578,54987,55040,36382,36391,49327,9260,10535,8966,4311,28484,16112,63352,65349,59841,11309,45827,28844,55776,26704,13060,48687,35367,9423,13576,52421,49243,56739,34654,55474,42148,452,35936,25176,4778,584,38080,15984,31877,5797,3628,2822,32335,4011,8949,29217,16985,8754,17543,15720,29101,44156,22796,45558,56101,55773,9777,48454,56084,52245,38082,6618,48112,42941,1991,1715,53330,38687,19547,42785,52176,17088,41913,52149,27320,9074,22109,43584,36569,20844,41906,57326,16481,61186,51428,40265,37011,3207,40414,53647,30543,44538,55016,15285,38667,8398,53729,41112,5553,54994,1346,47607,41776,63391,46133,18698,30525,41053,39447,5,56834,16531,23787,12099,62993,9682,33780,53261,10685,23132,55428,54719,5665,62993,37581,37196,26151,7022,37760,48220,1906,6315,50707,55261,33829,47007,37131,57517,665,34325,5488,24829,9746,2546,5592,52158,60691,12191,52066,7656,56939,61046,44542,35129,36004,35909,35909,1045,20818,24383,11422,26320,2341,56701,30383,1158,31382,12021,33849,28972,57919,44171,38366,43560,2572,10524,1383,18486,6475,59508,22899,59729,37432,27610,7221,18148,45138,47843,43135,56735,42292,1624,15940,8779,40256,20557,18291,25533,61316,35762,35700,29054,13377,26388,41369,1452,34702,63341,615,46325,22916,14344,51700,52439,57574,35310,55007,24349,31534,44497,15720,28392,18816,13683,1713,20949,60561,47223,41049,57174,56506,24040,44083,8123,36378,57372,18058,4146,20622,58877,55729,19948,56592,11114,2521,56533,33506,4322,40070,36188,32972,41633,42480,3503,25756,6189,55942,22486,3116,10061,41976,37956,27443,2623,1268,2479,30829,12615,57480,40380,9893,60968,16518,48501,42281,50435,12843,776,63620,19969,33589,23042,5936,49906,30843,53049,33367,29661,20476,45446,15924,38286,18767,19540,57402,41217,6278,57337,57599,9035,57932,48180,64264,62559,51906,8099,4260,24074,53641,13117,18473,11152,46178,5234,63643,40499,38492,55372,54893,58438,46462,34032,59577,52419,13331,28737,63104,12949,37304,22161,17154,20112,48474,35096,12985,33329,51563,5517,16097,56378,1218,38827,41435,33502,17227,59148,64493,12375,28876,42350,31013,9835,19610,51228,20518,38437,28671,61991,46586,55842,55693,13921,35599,15324,9464,15454,39417,56421,34351,52353,48944,18697,2195,23720,18385,27168,12767,5163,5509,31718,12764,62621,32982,21406,322,28675,37975,42080,17430,14952,8529,56305,38881,63179,2188,32346,46784,60564,20068,3730,26398,31039,3143,48136,5031,20261,22064,33132,40138,58687,62454,50517,50713,51895,45663,8502,50716,20892,19598,35169,50924,48126,61903,20432,16961,35821,59180,56469,10466,27831,42988,54952,11731,53774,30716,8034,18991,12292,27020,35200,21141,35778,9251,24122,35410,20084,28206,47686,19805,47369,46787,32370,64165,25365,19899,46535,46307,64594,55748,45351,1426,42340,38657,58208,21113,29766,27317,722,38885,61258,44883,48372,34894,24084,46788,36898,43932,33760,8311,27130,38618,38482,30186,56254,20661,20403,39366,46879,65379,27687,10002,17758,24999,21208,45082,5523,34972,13489,5767,39105,17163,15260,52693,4479,1279,1434,2502,21938,39410,10080,4468,8003,17664,46378,39486,49251,9838,61355,35182,54394,20401,7163,44248,37521,5918,42188,37033,50472,37142,32932,65043,16799,63556,20460,506,53270,21722,40903,21557,64332,25435,4790,25760,49076,42254,38771,24581,33590,47846,28462,63193,33199,36114,50687,5572,35570,44671,53874,56658,6535,27150,57472,18906,45863,15982,38657,5207,25143,4657,57409,38964,53027,30050,41125,33504,62930,21928,25379,2171,53910,62489,64423,8946,38938,6635,15125,14486,28672,12636,39297,12421,18911,28872,52198,61510,13235,22400,54879,48134,43771,35982,62366,30868,5174,46189,20431,35732,65177,9422,16182,14956,62741,37339,21461,21120,11749,50710,43013,62788,19756,24942,5652,16819,51457,37808,7355,8130,60773,38661,44564,3627,2452,55775,58853,20217,34004,15555,21680,10535,13198,469,3231,3323,55000,30261,62647,52093,19913,55970,3948,31308,63680,31963,23289,45919,12775,37502,53043,30301,16037,47317,46826,33231,48513,15647,45956,41877,17201,35593,56966,33130,44241,3996,20669,268,61731,31038,20351,2703,4192,48236,20497,19730,14544,17385,56695,8411,31071,42567,11437,28390,25505,19406,63217,23264,18646,9064,55445,28984,37021,2214,43455,46941,42762,22996,13028,3851,16421,6705,61607,37011,43751,44200,7823,11440,7362,48954,27374,60310,52066,64837,31646,31227,20462,32526,60554,37127,14117,56642,28431,43253,32942,20687,16832,27283,65419,31706,52672,29784,62076,37780,17020,4339,28751,64648,30719,62518,17792,61056,62111,43761,39169,57940,51335,32139,4398,61939,41632,31450,21139,42824,53045,15354,4446,50388,37101,19850,7048,44240,15725,28398,34299,10436,56883,10321,20679,36526,3892,35496,2638,11699,26984,14869,31420,3858,27888,10699,40305,49023,17917,14676,27798,19970,9621,31838,10495,55689,1184,7651,13750,10847,12598,43772,53287,43660,53776,16354,39479,53614,54223,54827,26924,36545,23958,60098,5576,21680,35058,30569,49188,48799,56409,30282,14499,10745,51940,22471,21857,22391,64258,36188,49553,57795,51463,63245,61531,56853,58150,64811,61595,18056,50172,57789,8562,48844,27144,62850,22024,5761,17170,52951,24831,51081,60975,19763,55516,59113,50006,41879,29726,10181,25363,62678,15037,22308,3355,42628,24349,61074,11809,40610,51915,29949,42909,57663,342,38885,38998,46731,53946,23209,34764,16299,44148,54540,36282,6215,3097,23009,38106,55124,56513,25111,511,44602,12616,58486,21486,39039,21235,52672,42152,53713,34730,13672,38736,51557,60851,19956,47446,24469,62344,16320,1239,3694,45236,17284,15962,26578,60703,48346,64750,3655,44906,16009,30739,52714,41475,57884,54590,3757,26356,58864,19796,62933,62434,3650,25991,63121,20010,61428,41302,45472,60017,18039,6317,59019,65360,15900,13559,53258,41221,55332,39117,10474,65418,22630,52849,4522,59983,9446,23853,18343,23068,20484,2330,19628,31444,41768,41986,25305,35371,202,13811,15822,11082,640,17494,13437,55763,54891,13048,56050,41931,18182,3662,12808,11677,40559,54089,51465,60050,45994,25131,6380,41172,13916,55610,25060,56936,40727,39962,60171,35540,46569,47852,60097,46164,12178,33605,58601,25777,25864,17979,62351,15575,26008,29441,38028,49435,10427,44912,14225,28892,43928,50987,14368,42243,2682,11382,9329,50353,64573,899,49168,41682,56974,12352,54040,41430,60776,57522,21264,12175,47293,4418,2649,42697,15061,12456,27307,47084,1851,5329,58473,50514,5602,24548,45337,61402,10409,59058,43134,56110,17480,22983,21478,51467,27184,24136,44200,62465,5566,42017,39517,19722,51467,38881,14647,19317,4807,4537,18055,32922,27638,38440,1378,6778,53502,25066,32374,16572,47253,7186,31294,26132,50300,12331,18529,25408,23912,33605,64425,57198,18600,50508,18505,61849,1797,962,2036,28508,47876,11816,25343,62032,22684,33618,34087,54682,45923,19016,34541,55746,17650,12519,60920,58773,38836,46975,4337,59624,56094,7249,5312,979,24290,14785,1304,47635,20481,59517,57013,40128,30614,26124,9011,42913,6225,54377,28809,50715,9777,5279,47919,23922,62533,39668,15323,53752,43914,32722,41610,57153,53334,53073,57879,12273,44056,2285,42052,58595,51364,54001,64738,54025,54671,3478,56629,57880,25228,53536,33352,20218,18404,10101,45075,26915,20807,54823,12065,20537,51155,62318,2685,59222,856,22701,16963,41321,20649,39241,20506,63422,23298,34930,38171,21933,8644,50670,62313,55651,35444,11997,9114,24100,42111,26603,6604,33060,48909,18907,35965,33444,48060,6594,47167,8582,60460,17777,59590,794,59237,42633,2456,46332,26567,49353,25019,23095,65379,60635,57628,15612,33331,53571,5910,6066,11860,44497,2943,61364,46316,25803,53690,25072,37743,44903,6035,31446,49809,3249,44483,9282,39234,45976,50123,37277,9606,17753,56719,7753,30004,24763,8285,4943,30288,12164,49379,3175,51337,19102,3882,62706,31,56334,34289,19029,55792,39693,6834,39224,8073,51775,59,21049,45670,11846,3869,31410,14666,65021,4362,24313,58202,25651,59059,27335,49857,1938,57623,28735,59361,58123,1532,36968,60319,23920,65048,40817,53408,36350,49680,55327,1567,21958,10209,29356,864,16217,38996,18752,5909,13107,18130,13626,56744,63072,5802,55511,25324,20345,27579,19535,2767,64759,15798,61628,24325,20740,49677,60658,41229,30110,43198,55433,11268,50437,6596,14745,48879,38574,58353,26653,52928,28767,23307,65323,61028,39112,1888,26908,3261,36059,45506,12768,12591,11939,42132,31609,60600,32888,25456,20704,15318,58881,38102,59769,61722,37319,5488,11910,32641,51511,2022,27805,53207,39858,25791,45131,55074,23228,37934,54587,16732,8196,51880,38296,39195,33119,20811,829,36059,19138,25478,46587,20375,4124,20595,36666,53121,26254,31242,65166,30076,7167,3238,62237,30569,50944,12347,2161,47682,3115,52243,12892,43336,34376,6899,31127,57388,23177,624,62311,14845,14397,3602,29591,37730,55613,31501,44238,13254,16126,3636,18723,40865,45197,20682,26502,17621,28410,24647,39592,52190,4687,18124,25685,37385,17729,36066,32561,38620,4025,38977,55940,15341,7986,45489,11592,51166,35088,33287,5495,46749,38377,24352,39579,15647,39472,21434,43595,38367,24728,6793,29096,24400,50016,65524,46296,61389,36464,54105,61185,6822);

	function bad_city($to_city) {
		//Проверяем контрольную сумму города для расчета, если не совпадает, то даже не посылаем запрос на расчет
		$crc = $this->crc16($this->strtoupper_ru($to_city));
		if (!in_array($crc, self::$crcmas)) return(true); //Не показываем ошибку, но не считаем
		return (false);
	}

	function crc16($data) {
		$data = $data.'..................................................';
		$crc = 0xFFFF;
		for ($i = 0; $i < 50; $i++) {
			$x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
			$x ^= $x >> 4;
			$crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
		}
		return $crc;
	}

	function strtoupper_ru($content) {
        if (function_exists('mb_strtoupper')) $s = mb_strtoupper($content, 'windows-1251'); else $s = ToUpper($content);
		return $s;
	}

	public static function edost_error_handler($code, $msg, $file, $line) {
//		echo "**** Произошла ошибка $msg ($code) ****<br>";
		self::$NowErr = true;
		return true;
	}

	// отправка POST запроса
	function ManualPOST($url, $post, $xml = true) {
		$parse_url = parse_url($url);
		$path = $parse_url['path'];

		$host= $parse_url['host'];
		$rez = '';

		self::$NowErr = false;
		set_error_handler(array('edost_class', 'edost_error_handler'));

		$fp = fsockopen($host, 80, $errno, $errstr, 4); // 4 - макс. время запроса

		restore_error_handler();

		if ($errno == 13) return('Err14'); // ошибка "Настройки сервера не позволяют отправить запрос на расчет"

		if (self::$NowErr) return(''); // ошибка

		if ($fp) {
			$out =	"POST ".$path." HTTP/1.0\n".
					"Host: ".$host."\n".
					"Referer: ".$url."\n".
					"Content-Type: application/x-www-form-urlencoded\n".
					"Content-Length: ".strlen($post)."\n\n".
					$post."\n\n";

			fputs($fp, $out);

			$q = 0;
			while ($gets = fgets($fp, 512)) {
				$rez.= $gets;
				$q++;
			}
			fclose($fp);

			if ($xml) $rez = stristr($rez, '<?xml version='); // удаление заголовка
		}

		return($rez);
	}


	// функции разбора XML
	function start_tag($parser, $name, $attrs) {
//		echo "<b>Element: $name</b><br>";
		switch($name) {
			case 'tarif':	$this->curElem = array(); break;
			case 'office':	$this->curElem = array(); break;
			default:		$this->index = $name; break;
		};
	}

	function end_tag($parser, $name) {
//		echo "<b>element:: $name</b><br>";
		if ((is_array($this->curElem)) && ($name == 'tarif')) {
			$this->rz[] = $this->curElem;
			$this->curElem = null;
		};
		if ((is_array($this->curElem)) && ($name == 'office')) {
			$this->rz_office[] = $this->curElem; //$this->curElem['to_tarif'];
			$this->curElem = null;
		};
		$this->index = null;
	}

	function char_data($parser, $data){
		if ((is_array($this->curElem)) && ($this->index)) {
			if (array_key_exists($this->index,$this->curElem)) $this->curElem[$this->index]=$this->curElem[$this->index].$data; else $this->curElem[$this->index] = $data;
		}
		if ( (strlen(trim($data)) > 0) and ($this->index=='stat') ) $this->status = $data; // статус результата
		if ( (strlen(trim($data)) > 0) and ($this->index=='warning') ) $this->warning[] = $data; // предупреждение
//		if (strlen(trim($data)) > 0) { echo 'String: '.$data.' - '.$this ->index.'<br>'; } // вывод строки
	}


	// разбор ответа сервера расчетов
	function edost_calc($xml) {

		$this->status = 0;
		$this->index = null;
		$this->rz = array();
		$this->rz_office = array();
		$this->curElem = null;
		$this->warning = array();

		$xml = stristr($xml, "<?xml version=");
//		echo '<br>XML: '.$xml;

		if ($xml == 'Err14') return array('qty_company' => 0, 'stat' => 14);
		if ($xml == '') return array('qty_company' => 0, 'stat' => 8);

		$code = "UTF-8"; // кодировка xml
		$this->parser = xml_parser_create($code);
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, 'start_tag', 'end_tag');
		xml_set_character_data_handler($this->parser, 'char_data');
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); //если folding включен, то все имена тегов будут переведены в верхний регистр

		if (!xml_parse($this->parser, $xml, true)) {
			//die(sprintf('Ошибка XML: %s в строке %d', xml_error_string(xml_get_error_code($this->parser)),xml_get_current_line_number($this->parser)));
			return (array('qty_company' => 0, 'stat' => 10));
		}
		xml_parser_free($this->parser);

		if ($this->status <> 1) return array('qty_company' => 0, 'stat' => $this->status);

		$rez = array();
		$i = 0;
		$rez['qty_company'] = count($this->rz);
		$rez['stat'] = $this->status;

		// предупреждения
		$rez['warning'] = $this->warning;

//		echo "<br>edost server respond (warning):<pre>".print_r($this->warning, true)."</pre>";
//		echo "<br>edost server respond (office):<pre>".print_r($this->rz_office, true)."</pre>------";
//		echo "<br>edost server respond (rz):<pre>".print_r($this->rz, true)."</pre>";

		foreach ($this->rz as $n) {
			$i++;
			$rez['price'.$i] = preg_replace("/[^0-9.]/i", "", substr(trim($n['price']), 0, 11)); // цена доставки (включается в заказ)
			if (isset($n['priceinfo'])) $rez['priceinfo'.$i] = preg_replace("/[^0-9.]/i", "", substr(trim($n['priceinfo']), 0, 11)); // цена доставки для информации (НЕ включается в заказ)

			$rez['day'.$i] = (isset($n['day']) ? substr(trim($n['day']), 0, 60) : '&nbsp;'); // срок доставки
			$rez['company'.$i] = substr($n['company'], 0, 80); // название компании (в 2 раза больше из-за UTF)
			$rez['name'.$i] = (isset($n['name']) ? substr(trim($n['name']), 0, 80) : ''); // название тарифа (в 2 раза больше из-за UTF)
			$rez['strah'.$i] = substr(trim($n['strah']), 0, 1); // 1 - со страховкой, 0 - без
			$rez['id'.$i] = substr(trim($n['id']), 0, 5); // код тарифа в системе eDost: http://edost.ru/kln/help.html#DeliveryCode
			$rez['pricecash'.$i] = (isset($n['pricecash']) ? preg_replace("/[^0-9.-]/i", "", substr(trim($n['pricecash']), 0, 11)) : -1); // цена доставки при наложенном платеже
			$rez['transfer'.$i] = (isset($n['transfer']) ? preg_replace("/[^0-9.]/i", "", substr(trim($n['transfer']), 0, 11)) : 0); // сумма денежного перевода при наложенном платеже (оплачивается при получении, отдельно от стоимости заказа)

			if (isset($n['pickpointmap'])) $rez['pickpointmap'.$i] = preg_replace("/[^a-z_-]/i", "", substr(trim($n['pickpointmap']), 0, 25)); // код для отображения на карте PickPoint

			for ($h = 0; $h < count($this->rz_office); $h++) {
				$to_tarif = explode(',', $this->rz_office[$h]['to_tarif']); //16,18
				if (in_array($rez['id'.$i], $to_tarif)) {
					$rez['office'.$i][] = $this->rz_office[$h];
				}
			}

//			echo '<br>price='.$n['price'].' , id='.$n['id'].' , strah='.$n['strah'].' , day='.$n['day'].' , company='.$n['company'];
		};

		return $rez;

	}


	// расчет габаритов всего заказа
	function SumSize($a) {

		$n = count($a);
		if (!($n > 0)) return( array('length' => '0', 'width' => '0', 'height' => '0') );

		for ($i3=1; $i3<$n; $i3++) {
			// отсортировать размеры по убыванию
			for ($i2=$i3-1; $i2<$n; $i2++) {
				for ($i=0; $i<=1; $i++) {
					if ($a[$i2]['X'] < $a[$i2]['Y']) {
						$a1 = $a[$i2]['X'];
						$a[$i2]['X'] = $a[$i2]['Y'];
						$a[$i2]['Y'] = $a1;
					};
					if ( ($i==0) and ($a[$i2]['Y'] < $a[$i2]['Z']) ) {
						$a1 = $a[$i2]['Y'];
						$a[$i2]['Y'] = $a[$i2]['Z'];
						$a[$i2]['Z'] = $a1;
					}
				}
				$a[$i2]['Sum'] = $a[$i2]['X'] + $a[$i2]['Y'] + $a[$i2]['Z']; // сумма сторон
			}

			// отсортировать грузы по возрастанию
			for ($i2=$i3; $i2<$n; $i2++)
				for ($i=$i3; $i<$n; $i++)
					if ($a[$i-1]['Sum'] > $a[$i]['Sum']) {
						$a2 = $a[$i];
						$a[$i] = $a[$i-1];
						$a[$i-1] = $a2;
					}

			// расчитать сумму габаритов двух самых маленьких грузов
			if ($a[$i3-1]['X'] > $a[$i3]['X']) $a[$i3]['X'] = $a[$i3-1]['X'];
			if ($a[$i3-1]['Y'] > $a[$i3]['Y']) $a[$i3]['Y'] = $a[$i3-1]['Y'];
			$a[$i3]['Z'] = $a[$i3]['Z'] + $a[$i3-1]['Z'];
			$a[$i3]['Sum'] = $a[$i3]['X'] + $a[$i3]['Y'] + $a[$i3]['Z']; // сумма сторон
		}

		return ( array('length' => Round($a[$n-1]['X'], 2), 'width' => Round($a[$n-1]['Y'], 2), 'height' => Round($a[$n-1]['Z'], 2)) );

	}


	// расчет габаритов одного товара
	function SumSizeOneGoods($xi, $yi, $zi, $qty) {

		// сортировка габаритов по возрастанию
		$ar = array($xi, $yi, $zi);
		sort($ar);

		if ($qty <= 1) return array('X' => $ar[0], 'Y' => $ar[1], 'Z' => $ar[2]);

		$x1 = 0;
		$y1 = 0;
		$z1 = 0;
		$l = 0;

		$max1 = floor(Sqrt($qty));

		for ($y = 1; $y <= $max1; $y++) {
			$i = ceil($qty/$y);
			$max2 = floor(Sqrt($i));

			for ( $z=1; $z <= $max2; $z++ ) {
				$x = ceil($i/$z);

				$l2 = $x*$ar[0] + $y*$ar[1] + $z*$ar[2];
				if ($l == 0 || $l2 < $l) {
					$l = $l2;
					$x1 = $x;
					$y1 = $y;
					$z1 = $z;
				}
			}
		}

//		echo '<br>количество товаров по сторонам: x='.$x1.', y='.$y1.', z='.$z1;

		return array('X' => $x1*$ar[0], 'Y' => $y1*$ar[1], 'Z' => $z1*$ar[2]);

	}

}

?>