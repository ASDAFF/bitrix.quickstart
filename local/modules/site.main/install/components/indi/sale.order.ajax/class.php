<?
/**
 *  module
 * 
 * @category	
 * @package		Sale
 * @link		http://.ru
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

/**
 * Компонент оформления заказа
 *
 * @deprecated
 *
 * @category	
 * @package		Sale
 */
class SaleOrderAjaxComponent extends CBitrixComponent
{
	/**
	 * Конфигурация
	 *
	 * @var array
	 */
	protected $config = array();
	
	/**
	 * Текущий этап
	 *
	 * @var string
	 */
	protected $step = '';
	
	/**
	 * Данные для расчета, вводимые пользователем (тут основные, могут добавляться еще)
	 *
	 * @var array
	 */
	protected $data = array(
		'PERSON_TYPE_ID' => 0,
		'PROFILE_ID' => 0,
		'DELIVERY_ID' => 0,
		'PAY_SYSTEM_ID' => 0,
		'BUYER_STORE' => 0,
		'ORDER_DESCRIPTION' => '',
	);
	
	/**
	 * Предварительные данные заказа
	 *
	 * @var array
	 */
	protected $orderPre = array(
		'DELIVERY_LOCATION' => 0,
		'DELIVERY_PRICE' => 0,
		'TAX_LOCATION' => 0,
	);
	
	/**
	 * Расчитанные данные заказа
	 *
	 * @var array
	 */
	protected $order = array();
	
	/**
	 * Корзина пользователя
	 *
	 * @var array
	 */
	protected $basket = array();
	
	/**
	 * Типы групп элементов корзины
	 *
	 * @var array
	 */
	protected $basketItemsTypes = array(
		'BASKET_ITEMS',
		'DELAY_ITEMS',
	);
	
	/**
	 * Размеры миниатюр в корзине
	 *
	 * @var array
	 */
	protected $basketThumbSize = array(
		'height' => 100,
		'width' => 100,
	);
	
	/**
	 * Типы плательщиков
	 *
	 * @var array
	 */
	protected $personTypes = array();
	
	/**
	 * Профили покупателя
	 *
	 * @var array
	 */
	protected $userProfiles = array();
	
	/**
	 * Службы доставки
	 *
	 * @var array
	 */
	protected $deliveryServices = array();
	
	/**
	 * Платежные системы
	 *
	 * @var array
	 */
	protected $paySystems = array();
	
	/**
	 * Свойства заказа
	 *
	 * @var array
	 */
	protected $orderProps = array();
	
	/**
	 * ID cвойств заказа, которые должны быть удалены из $orderProps
	 *
	 * @var array
	 */
	protected $orderPropsDel = array();
	
	/**
	 * Свойство заказа с типом "Почтовый индекс"
	 *
	 * @var array
	 */
	protected $orderPropZip = array();
	
	/**
	 * Свойство заказа с типом "Местоположение"
	 *
	 * @var array
	 */
	protected $orderPropLocation = array();
	
	/**
	 * Можно оплатить из средств на счету пользователя
	 *
	 * @var boolean
	 */
	protected $payFromAccount = false;
	
	/**
	 * Для оплаты используется предавторизация
	 *
	 * @var boolean
	 */
	protected $usePrepayment = false;
	
	/**
	 * Обработчик предавторизации
	 *
	 * @var CSalePaySystemPrePayment
	 */
	protected $prepaymentHandler;
	
	/**
	 * Ошибки
	 *
	 * @var array
	 */
	protected $errors = array();
	
	/**
	 * Нотификации
	 *
	 * @var array
	 */
	protected $notes = array();
	
	/**
	 * URL для выполнения редиректа
	 *
	 * @var string
	 */
	protected $redirectUrl = '';
	
	/**
	 * Обрабатывает параметры компонента
	 *
	 * @param array $params Параметры компонента
	 * @return array Обработанные параметры
	 */
	public function onPrepareComponentParams($params)
	{
		$params['SET_TITLE'] = $params['SET_TITLE'] == 'Y';
		$params['DELIVERY_TO_PAYSYSTEM'] = $params['DELIVERY_TO_PAYSYSTEM'] == 'p2d' ? 'p2d' : 'd2p';
		$params['DELIVERY_NO_AJAX'] = $params['DELIVERY_NO_AJAX'] == 'Y';
		$params['PAY_FROM_ACCOUNT'] = $params['PAY_FROM_ACCOUNT'] == 'Y';
		$params['ONLY_FULL_PAY_FROM_ACCOUNT'] = $params['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y';
		$params['USE_PREPAYMENT'] = $params['USE_PREPAYMENT'] == 'Y';
		$params['DISABLE_BASKET_REDIRECT'] = $params['DISABLE_BASKET_REDIRECT'] == 'Y';
		$params['SEND_NEW_USER_NOTIFY'] = $params['SEND_NEW_USER_NOTIFY'] == 'Y';
		$params['PATH_TO_BASKET'] = trim($params['PATH_TO_BASKET']);
		$params['PATH_TO_PAYMENT'] = trim($params['PATH_TO_PAYMENT']);
		$params['PATH_TO_PERSONAL'] = trim($params['PATH_TO_PERSONAL']);
		$params['PATH_TO_ORDERS_LIST'] = $params['PATH_TO_ORDERS_LIST'] ? $params['PATH_TO_ORDERS_LIST'] : $params['PATH_TO_PERSONAL'];
		$params['PATH_TO_FEEDBACK_FORM'] = trim($params['PATH_TO_FEEDBACK_FORM']);
		$params['DELIVERY_GROUPS'] = (array) $params['DELIVERY_GROUPS'];
		$params['PAY_SYSTEMS_ONLINE'] = (array) $params['PAY_SYSTEMS_ONLINE'];
		
		return $params;
	}
	
	/**
	 * Выполняет компонент
	 *
	 * @return void
	 */
	public function executeComponent()
	{
		global $APPLICATION, $USER;
		
		try {
			if ($this->arParams['SET_TITLE']) {
				$APPLICATION->SetTitle(GetMessage('SOA_TITLE'));
			}
			
			if (!\Bitrix\Main\Loader::includeModule('sale')) {
				throw new Exception(GetMessage('SOA_SALE_MODULE_NOT_INSTALL'));
			}
			
			$this->config = array(
				'USE_CATALOG' => true,
				'USE_ACCOUNT_NUMBER' => COption::GetOptionString('sale', 'account_number_template', '') != '',
				'CURRENCY' => CSaleLang::GetLangCurrency(SITE_ID),
				'WEIGHT_UNIT' => htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', false, SITE_ID)),
				'WEIGHT_KOEF' => htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID)),
				'USE_VAT' => false,
				'BASKET_ENABLED' => $this->arParams['PATH_TO_BASKET'] == '',
				'BASKET_USER_ID' => CSaleBasket::GetBasketUserID(),
				'BASKET_URL' => $this->arParams['PATH_TO_BASKET'] == '' ? $APPLICATION->GetCurPageParam('', array('order')) : $this->arParams['PATH_TO_BASKET'],
				'ORDER_URL' => $APPLICATION->GetCurPage() . '?order',
			);
			
			if ($this->config['USE_CATALOG'] && !\Bitrix\Main\Loader::includeModule('catalog')
			) {
				throw new Exception(GetMessage('SOA_CATALOG_MODULE_NOT_INSTALL'));
			}
			
			$this->data = $this->analizeRequest($this->getRequestData());
			
			//Определяемся с текущим шагом
			if ($this->request->getQuery('ORDER_ID') > 0) {
				$this->step = 'success';
			} else {
				if ($this->request->getQuery('order') === null) {
					$this->step = 'basket';
				} else {
					$this->step = $USER->IsAuthorized() ? 'order' : 'identity';
				}
			}
			
			if ($this->arParams['SET_TITLE']) {
				$title = GetMessage('SOA_STEP_' . strtoupper($this->step) . '_FULL');
				if (!$title) {
					$title = GetMessage('SOA_STEP_' . strtoupper($this->step));
				}
				$APPLICATION->SetTitle($title);
			}
			
			switch ($this->step) {
				case 'basket':
					$this->processBasket();
					break;
				case 'identity':
					$this->processIdentity();
					break;
				case 'order':
					$this->processOrder();
					break;
				case 'success':
					$this->processSuccess();
					break;
			}
			
			//Выполняем редирект
			if ($this->redirectUrl) {
				if (defined('\Site\Main\IS_AJAX') && \Site\Main\IS_AJAX) {
					//Если AJAX, то уведомляем Javascript о необходимости выполнить редирект через особый HTTP-заголовок
					CHTTP::SetStatus('200 OK');
					header('X-Redirect-Location: ' . $this->redirectUrl);
					$APPLICATION->RestartBuffer();
					exit();
					
					//$response = \Bitrix\Main\Context::getCurrent()->getResponse();
					//$response->setStatus('200 OK');
					//$response->setHeader('X-Redirect-Location: ' . $this->redirectUrl);
				} else {
					//Обычный редирект
					LocalRedirect($this->redirectUrl);
					//\Bitrix\Main\Context::getCurrent()->getResponse()->redirect($this->redirectUrl);
				}
			}
			
			//Заполняем arResult общими данными
			$this->arResult['CONFIG'] = &$this->config;
			$this->arResult['STEP'] = &$this->step;
			$this->arResult['STEPS'] = array(
				'order',
				'success',
			);
			if (!$USER->IsAuthorized()) {
				array_unshift($this->arResult['STEPS'], 'identity');
			}
			if ($this->config['BASKET_ENABLED']) {
				array_unshift($this->arResult['STEPS'], 'basket');
			}
			$this->arResult['DATA'] = $this->escape($this->data);
			$this->arResult['ERRORS'] = &$this->errors;
			$this->arResult['NOTES'] = &$this->notes;
			
			//\Site\Main\Console::log($this->arResult);
			
			//Подключаем шаблон
			$this->includeComponentTemplate();
		} catch (Exception $e) {
			ShowError($USER->isAdmin()
				? sprintf(
					'%s in %s(%d)',
					$e->getMessage(),
					$e->getFile(),
					$e->getLine()
				)
				: $e->getMessage()
			);
		}
	}
	
	/**
	 * Возвращает данные запроса
	 *
	 * @param Bitrix\Main\Type\ParameterDictionary|null $data Данные текущего контекста
	 * @return array
	 */
	protected function getRequestData($context = null)
	{
		if ($context === null) {
			$context = $this->request->getPostList();
		}
		
		$data = array();
		foreach ($context as $key => $val) {
			$data[$key] = $val;
		}
		
		return $data;
	}
	
	/**
	 * Проверяет данные запроса
	 *
	 * @param array $data Данные
	 * @return array
	 */
	protected function analizeRequest($data)
	{
		$data['PERSON_TYPE_ID'] = intval($data['PERSON_TYPE_ID']);
		$data['PROFILE_ID'] = intval($data['PROFILE_ID']);
		$data['PAY_SYSTEM_ID'] = intval($data['PAY_SYSTEM_ID']);
		$data['DELIVERY_ID'] = trim($data['DELIVERY_ID']);
		
		$this->analizeRequestItems($data);
		
		return $data;
	}
	
	/**
	 * Анализирует каждый элемент запроса
	 *
	 * @param array $data Данные текущего контекста
	 * @return void
	 */
	protected function analizeRequestItems(&$data)
	{
		foreach ($data as $key => &$val) {
			if (is_array($val)) {
				$this->analizeRequestItems($val);
			} else {
				$val = trim($val);
			}
		}
		unset($val);
	}
	
	/**
	 * Формирует корзину
	 *
	 * @param boolean $calcDiscounts Посчитать скидки
	 * @return array
	 */
	protected function getBasket($calcDiscounts = true)
	{
		global $USER;
		
		//Собираем товары
		CSaleBasket::UpdateBasketPrices($this->config['BASKET_USER_ID'], SITE_ID);
		
		$basketItems = array();
		$basketProductsMap = array();//Торговое предложение -> товар
		$basketSets = array();//Наборы
		$itemsRecordset = CSaleBasket::GetList(
			array(
				'ID' => 'ASC',
			),
			array(
				'FUSER_ID' => $this->config['BASKET_USER_ID'],
				'LID' => SITE_ID,
				'ORDER_ID' => 'NULL',
			),
			false,
			false,
			array(
				'ID',
				'CALLBACK_FUNC',
				'MODULE',
				'PRODUCT_ID',
				'QUANTITY',
				'DELAY',
				'CAN_BUY',
				'PRICE',
				'WEIGHT',
				'NAME',
				'CURRENCY',
				'CATALOG_XML_ID',
				'VAT_RATE',
				'NOTES',
				'DISCOUNT_PRICE',
				'PRODUCT_PROVIDER_CLASS',
				'DIMENSIONS',
				'TYPE',
				'SET_PARENT_ID',
				'DETAIL_PAGE_URL',
			)
		);
		while ($item = $itemsRecordset->GetNext()) {
			if ($item['CAN_BUY'] == 'Y' && $item['DELAY'] == 'N') {
				$type = 'ready';
			} elseif ($item['CAN_BUY'] == 'Y' && $item['DELAY'] == 'Y') {
				$type = 'delay';
			} else {
				continue;
			}
			
			$item['PRICE'] = roundEx($item['PRICE'], SALE_VALUE_PRECISION);
			$item['QUANTITY'] = floatval($item['QUANTITY']);
			$item['WEIGHT'] = floatval($item['WEIGHT']);
			$item['VAT_RATE'] = floatval($item['VAT_RATE']);
			
			//Данные о товаре
			$item['PRODUCT_PARENT_ID'] = 0;
			if ($this->config['USE_CATALOG']) {
				$product = CCatalogSku::GetProductInfo($item['PRODUCT_ID']);
				if ($product) {
					$basketProductsMap[$item['PRODUCT_ID']] = $product['ID'];
					
					if ($item['PRODUCT_ID'] != $product['ID']) {
						$item['PRODUCT_PARENT_ID'] = $product['ID'];
					}
				} else {
					$basketProductsMap[$item['PRODUCT_ID']] = $item['PRODUCT_ID'];
				}
			}
			
			//Товары из наборов добавляем в отдельный список
			if (CSaleBasketHelper::isSetItem($item)) {
				$basketSets[$item['SET_PARENT_ID']][] = $item;
			} else {
				$basketItems[$type][] = $item;
			}
		}
		
		//Анализируем наборы
		foreach ($basketItems as &$type) {
			foreach ($type as &$item) {
				if (CSaleBasketHelper::isSetParent($item)) {
					//$item['WEIGHT'] = 0;
					if (is_array($basketSets[$item['SET_PARENT_ID']])) {
						foreach ($basketSets[$item['SET_PARENT_ID']] as $setItem) {
							$item['SET_ITEMS'][] = $setItem;
							//$item['WEIGHT'] += $setItem['WEIGHT'] * $setItem['QUANTITY'];
						}
					}
					//$item['WEIGHT'] = $item['WEIGHT'] / $item['QUANTITY'];
				}
			}
			unset($item);
		}
		unset($type);
		
		//Рассчитываем цены и скидки через API
		if (!isset($basketItems['ready'])) {
			$basketItems['ready'] = array();
		}
		$basket = CSaleOrder::CalculateOrderPrices($basketItems['ready']);
		if ($basket) {
			$basket = array_merge(
				array(
					'SITE_ID' => SITE_ID,
					'USER_ID' => $USER->GetID(),
				),
				$basket
			);
			if ($calcDiscounts) {
				$errors = array();
				CSaleDiscount::DoProcessOrder($basket, array(), $errors);
			}
		} else {
			$basket = array(
				'ORDER_PRICE' => 0,
				'ORDER_WEIGHT' => 0,
				'VAT_RATE' => 0,
				'VAT_SUM' => 0,
				'USE_VAT' => 'N',
				'BASKET_ITEMS' => array(),
			);
		}
		
		$basket['DELAY_ITEMS'] = is_array($basketItems['delay']) ? $basketItems['delay'] : array();
		/*if ($basket['DELAY_ITEMS'] && $calcDiscounts) {
			$delayBasket = array(
				'ORDER_PRICE' => 0,
				'ORDER_WEIGHT' => 0,
				'VAT_RATE' => 0,
				'VAT_SUM' => 0,
				'USE_VAT' => 'N',
				'BASKET_ITEMS' => $basket['DELAY_ITEMS'],
			);
			$errors = array();
			CSaleDiscount::DoProcessOrder($delayBasket, array(), $errors);
			$basket['DELAY_ITEMS'] = $delayBasket['BASKET_ITEMS'];
		}*/
		
		//Единицы измерения
		if ($this->config['USE_CATALOG']) {
			foreach ($this->basketItemsTypes as $itemsType) {
				if ($basket[$itemsType]) {
					$basket[$itemsType] = getMeasures($basket[$itemsType]);
				}
			}
		}
		
		//Данные товаров (включая торговые предложения)
		$basketProducts = array();
		if ($basketProductsMap) {
			$basketProducts = getProductProps(
				array_merge(
					array_keys($basketProductsMap),
					$basketProductsMap
				),
				array(
					'ID',
					'PREVIEW_PICTURE',
					'DETAIL_PICTURE',
					'PREVIEW_TEXT'
				)
			);
		}
		
		//Добавляем оставшиеся данные
		$basket['MAX_DIMENSIONS'] = array();
		foreach ($this->basketItemsTypes as $itemsType) {
			foreach ($basket[$itemsType] as &$item) {
				$this->getBasketItemData($item, $basketProducts);
				
				if (is_array($item['SET_ITEMS'])) {
					foreach ($item['SET_ITEMS'] as &$setItem) {
						$this->getBasketItemData($setItem, $basketProducts);
					}
					unset($setItem);
				}
				
				//Максимальные габариты
				if ($itemsType == 'BASKET_ITEMS' && $item['DIMENSIONS']) {
					$basket['MAX_DIMENSIONS'] = CSaleDeliveryHelper::getMaxDimensions(
						array(
							$item['DIMENSIONS']['WIDTH'],
							$item['DIMENSIONS']['HEIGHT'],
							$item['DIMENSIONS']['LENGTH'],
						),
						$basket['MAX_DIMENSIONS']
					);
				}
			}
			unset($item);
		}
		
		$this->calculateBasket($basket);
		
		return $basket;
	}
	
	/**
	 * Формирует данные элемента корзины
	 *
	 * @param array $item Элемент корзины
	 * @param array $products Данные продуктов
	 * @return void
	 */
	protected function getBasketItemData(&$item, &$products)
	{
		//Торговое предложение
		$item['PRODUCT'] = isset($products[$item['PRODUCT_ID']]) ? $products[$item['PRODUCT_ID']] : array();
		//Товар
		$item['PRODUCT_PARENT'] = $item['PRODUCT_PARENT_ID'] && isset($products[$item['PRODUCT_PARENT_ID']]) ? $products[$item['PRODUCT_PARENT_ID']] : array();
		
		//Габариты
		$dimensions = unserialize($item['~DIMENSIONS']);
		if (is_array($dimensions)) {
			$dimensions = array();
		}
		$item['DIMENSIONS'] = $dimensions;
		unset($item['~DIMENSIONS']);
		
		//Свойства
		$item['PROPS'] = array();
		$propsList = CSaleBasket::GetPropsList(
			array(
				'SORT' => 'ASC',
				'ID' => 'ASC',
			),
			array(
				'BASKET_ID' => $item['ID'],
				'!CODE' => array(
					'CATALOG.XML_ID',
					'PRODUCT.XML_ID',
				)
			)
		);
		while ($prop = $propsList->GetNext()) {
			if (array_key_exists('BASKET_ID', $prop)) {
				unset($prop['BASKET_ID']);
			}
			if (array_key_exists('~BASKET_ID', $prop)) {
				unset($prop['~BASKET_ID']);
			}
			
			$prop = array_filter($prop, array('CSaleBasketHelper', 'filterFields'));
			
			$item['PROPS'][$prop['CODE'] ? $prop['CODE'] : $prop['ID']] = $prop;
		}
		
		//Миниатюра (или из торгового предложения, или из товара)
		$item['THUMB'] = false;
		if ($item['PRODUCT']) {
			$item['THUMB'] = CFile::ResizeImageGet($item['PRODUCT']['PREVIEW_PICTURE'], $this->basketThumbSize);
			if (!$item['THUMB']) {
				$item['THUMB'] = CFile::ResizeImageGet($item['PRODUCT']['DETAIL_PICTURE'], $this->basketThumbSize);
			}
		}
		if (!$item['THUMB'] && $item['PRODUCT_PARENT']) {
			$item['THUMB'] = CFile::ResizeImageGet($item['PRODUCT_PARENT']['PREVIEW_PICTURE'], $this->basketThumbSize);
			if (!$item['THUMB']) {
				$item['THUMB'] = CFile::ResizeImageGet($item['PRODUCT_PARENT']['DETAIL_PICTURE'], $this->basketThumbSize);
			}
		}
	}
	
	/**
	 * Рассчитывает корзину
	 *
	 * @param array $basket Сформированная корзина
	 * @return void
	 */
	protected function calculateBasket(&$basket)
	{
		//Значения с префиксом LOCAL_ - результаты расчетов не через API
		$basket = array_merge($basket, array(
			'LOCAL_PRICE' => 0,
			'LOCAL_DISCOUNT' => 0,
			'LOCAL_WEIGHT' => 0,
			'LOCAL_VAT_RATE' => 0,
			'LOCAL_VAT_SUM' => 0,
			'LOCAL_VAT_SUM_FORMATED' => '',
			'LOCAL_DELAY_PRICE' => 0,
			'LOCAL_DELAY_DISCOUNT' => 0,
			'LOCAL_DELAY_WEIGHT' => 0,
		));
		
		foreach ($this->basketItemsTypes as $itemsType) {
			if (!is_array($basket[$itemsType])) {
				continue;
			}
			
			foreach ($basket[$itemsType] as &$item) {
				//Форматирование уже посчитанного
				$item['PRICE_FORMATED'] = $this->formatCurrency($item['PRICE'], $item['CURRENCY']);
				$item['DISCOUNT_PRICE_FORMATED'] = $this->formatCurrency($item['DISCOUNT_PRICE'], $item['CURRENCY']);
				$item['WEIGHT_FORMATED'] = $this->formatWeight($item['WEIGHT']);
				
				if (isset($item['MEASURE_TEXT']) && strlen($item['MEASURE_TEXT'])) {
					$item['QUANTITY_FORMATED'] = $item['QUANTITY'] . '&nbsp;' . $item['MEASURE_TEXT'];
				} else {
					$item['QUANTITY_FORMATED'] = $item['QUANTITY'];
				}
				
				//Процент скидки
				$item['DISCOUNT_PRICE'] = roundEx($item['DISCOUNT_PRICE'], SALE_VALUE_PRECISION);
				$item['DISCOUNT_PRICE_PERCENT'] = 0;
				$item['DISCOUNT_PRICE_PERCENT_FORMATED'] = '';
				if ($item['DISCOUNT_PRICE'] > 0) {
					$item['DISCOUNT_PRICE_PERCENT'] = $item['DISCOUNT_PRICE'] * 100 / ($item['DISCOUNT_PRICE'] + $item['PRICE']);
					$item['DISCOUNT_PRICE_PERCENT_FORMATED'] = roundEx($item['DISCOUNT_PRICE_PERCENT'], 0) . '%';
				}
				
				//Полная цена без скидки
				$item['FULL_PRICE'] = $item['PRICE'] + $item['DISCOUNT_PRICE'];
				$item['FULL_PRICE_FORMATED'] = $this->formatCurrency($item['FULL_PRICE'], $item['CURRENCY']);
				
				//Стоимость
				$item['SUM'] = $item['PRICE'] * $item['QUANTITY'];
				$item['SUM_FORMATED'] = $this->formatCurrency($item['SUM']);
				
				//Итого
				switch ($itemsType) {
					case 'BASKET_ITEMS':
						$basket['LOCAL_PRICE'] += $item['PRICE'] * $item['QUANTITY'];
						$basket['LOCAL_DISCOUNT'] += $item['DISCOUNT_PRICE'] * $item['QUANTITY'];
						$basket['LOCAL_WEIGHT'] += $item['WEIGHT'] * $item['QUANTITY'];
						
						if ($item['LOCAL_VAT_RATE'] > 0) {
							if ($item['VAT_RATE'] > $basket['LOCAL_VAT_RATE']) {
								$basket['LOCAL_VAT_RATE'] = $item['VAT_RATE'];
							}
							$item['VAT_VALUE'] = ($item['PRICE'] / ($item['VAT_RATE'] + 1)) * $item['VAT_RATE'];
							
							$basket['LOCAL_VAT_SUM'] += roundEx($item['VAT_VALUE'] * $item['QUANTITY'], SALE_VALUE_PRECISION);
						}
						break;
					case 'DELAY_ITEMS':
						$basket['LOCAL_DELAY_PRICE'] += $item['PRICE'] * $item['QUANTITY'];
						$basket['LOCAL_DELAY_DISCOUNT'] += $item['DISCOUNT_PRICE'] * $item['QUANTITY'];
						$basket['LOCAL_DELAY_WEIGHT'] += $item['WEIGHT'] * $item['QUANTITY'];
						break;
				}
			}
			unset($item);
		}
		
		$basket['LOCAL_PRICE_FORMATED'] = $this->formatCurrency($basket['LOCAL_PRICE']);
		$basket['LOCAL_DISCOUNT_FORMATED'] = $this->formatCurrency($basket['LOCAL_DISCOUNT']);
		$basket['LOCAL_VAT_SUM_FORMATED'] = $this->formatCurrency($basket['LOCAL_VAT_SUM']);
		$basket['LOCAL_DELAY_PRICE_FORMATED'] = $this->formatCurrency($basket['LOCAL_DELAY_PRICE']);
		$basket['LOCAL_DELAY_DISCOUNT_FORMATED'] = $this->formatCurrency($basket['LOCAL_DELAY_DISCOUNT']);
		
		$basket['LOCAL_PRICE_WITHOUT_DISCOUNT'] = $basket['LOCAL_PRICE'] + $basket['LOCAL_DISCOUNT'];
		$basket['LOCAL_PRICE_WITHOUT_DISCOUNT_FORMATED'] = $this->formatCurrency($basket['LOCAL_PRICE_WITHOUT_DISCOUNT']);
		$basket['LOCAL_DELAY_PRICE_WITHOUT_DISCOUNT'] = $basket['LOCAL_DELAY_PRICE'] + $basket['LOCAL_DELAY_DISCOUNT'];
		$basket['LOCAL_DELAY_PRICE_WITHOUT_DISCOUNT_FORMATED'] = $this->formatCurrency($basket['LOCAL_DELAY_PRICE_WITHOUT_DISCOUNT']);
		
		$basket['LOCAL_DISCOUNT_PERCENT'] = roundEx($basket['LOCAL_PRICE_WITHOUT_DISCOUNT'] > 0 ? 100 * ($basket['LOCAL_DISCOUNT']) / $basket['LOCAL_PRICE_WITHOUT_DISCOUNT'] : 0);
		$basket['LOCAL_DISCOUNT_PERCENT_FORMATED'] = $basket['LOCAL_DISCOUNT_PERCENT'] . '%';
		$basket['LOCAL_DELAY_DISCOUNT_PERCENT'] = roundEx($basket['LOCAL_DELAY_PRICE_WITHOUT_DISCOUNT'] > 0 ? 100 * ($basket['LOCAL_DELAY_DISCOUNT']) / $basket['LOCAL_DELAY_PRICE_WITHOUT_DISCOUNT'] : 0);
		$basket['LOCAL_DELAY_DISCOUNT_PERCENT_FORMATED'] = $basket['LOCAL_DELAY_DISCOUNT_PERCENT'] . '%';
		
		$basket['LOCAL_WEIGHT_FORMATED'] = $this->formatWeight($basket['LOCAL_WEIGHT']);
		$basket['LOCAL_DELAY_WEIGHT_FORMATED'] = $this->formatWeight($basket['LOCAL_DELAY_WEIGHT']);
		
		if ($basket['LOCAL_VAT_RATE'] > 0) {
			$this->config['USE_VAT'] = true;
		}
	}
	
	/**
	 * Выполняет шаг корзины
	 *
	 * @return void
	 */
	protected function processBasket()
	{
		global $APPLICATION;
		
		//Если корзина на отдельной странице
		if ($this->arParams['PATH_TO_BASKET']) {
			if ($this->arParams['DISABLE_BASKET_REDIRECT']) {
				$this->redirectUrl = $this->config['ORDER_URL'];
			} else {
				$this->redirectUrl = $this->arParams['PATH_TO_BASKET'];
			}
			return;
		}
		
		//Кол-во товаров
		if (is_array($this->data['QTY'])) {
			foreach ($this->data['QTY'] as $itemId => $itemQty) {
				$itemQty = floatval($itemQty);
				if ($itemQty > 0) {
					$this->processBasketAction($itemId, 'update', array(
						'QUANTITY' => $itemQty,
					));
				} else {
					$this->processBasketAction($itemId, 'delete');
				}
			}
		}
		
		//Действие над товаром
		switch ($this->data['ACTION_TYPE']) {
			case 'delay':
				$this->processBasketAction($this->data['ACTION_ITEM'], 'update', array(
					'DELAY' => 'Y',
				));
				break;
			case 'revert':
				$this->processBasketAction($this->data['ACTION_ITEM'], 'update', array(
					'DELAY' => 'N',
				));
				break;
			case 'delete':
				$this->processBasketAction($this->data['ACTION_ITEM'], 'delete');
				break;
		}
		
		//Проверяем купон
		$validCoupon = null;
		if (isset($this->data['COUPON']) && $this->data['COUPON']) {
			$validCoupon = CCatalogDiscountCoupon::SetCoupon($this->data['COUPON']);
		}
		if (!isset($this->data['COUPON']) || $validCoupon === false) {
			CCatalogDiscountCoupon::ClearCoupon();
		}
		
		$this->arResult['COUPON_IS_VALID'] = $validCoupon;
		
		//Собираем корзину
		$this->arResult['BASKET'] = $this->getBasket();
	}
	
	/**
	 * Выполняет дейтвие над элементом корзины
	 *
	 * @param integer $id ID элемента
	 * @param string $action Действие
	 * @param array $params Параметры
	 * @return void
	 */
	protected function processBasketAction($id, $action, $params = array())
	{
		$id = (int) $id;
		
		$item = CSaleBasket::GetList(
			array(
			),
			array(
				'FUSER_ID' => $this->config['BASKET_USER_ID'],
				'LID' => SITE_ID,
				'ORDER_ID' => 'NULL',
				'ID' => $id,
			),
			false,
			false,
			array(
				'ID',
				'CALLBACK_FUNC',
				'MODULE',
				'PRODUCT_ID',
				'QUANTITY',
				'DELAY',
				'CAN_BUY',
				'CURRENCY',
			)
		)->Fetch();
		if ($item) {
			switch ($action) {
				case 'update':
					CSaleBasket::Update($id, $params);
					break;
				
				case 'delete':
					CSaleBasket::Delete($id);
					break;
			}
		}
	}
	
	/**
	 * Выполняет шаг идентификации пользователя
	 *
	 * @return void
	 */
	protected function processIdentity()
	{
		$data = array(
			'ACTION' => $this->request->getPost('action'),
			'SHOW_OLD_FORM' => true,
			'SHOW_NEW_FORM' => true,
			'STORE_PASSWORD' => true,
			'SPAM_REQUEST' => true,
		);
		
		if ($data['ACTION'] && check_bitrix_sessid()) {
			try {
				global $USER;
				
				switch ($data['ACTION']) {
					//Вернувшийся пользователь
					case 'return':
						$login = $this->request->getPost('USER_LOGIN');
						
						if (!strlen($login)) {
							throw new Exception(GetMessage('SOA_ERROR_AUTH_LOGIN'));
						}
						
						$authResult = $USER->Login(
							$login,
							$this->request->getPost('USER_PASSWORD'),
							$this->request->getPost('USER_REMEMBER') == 'Y' ? 'Y' : 'N'
						);
						if ($authResult && $authResult['TYPE'] == 'ERROR') {
							throw new Exception(
								GetMessage('SOA_ERROR_AUTH') . (strlen($authResult['MESSAGE']) ? ': ' . $authResult['MESSAGE'] : '')
							);
						}
						
						global $APPLICATION;
						$this->redirectUrl = $APPLICATION->GetCurPageParam();
						break;
					
					//Новый пользователь
					case 'register':
						$email = $this->request->getPost('NEW_EMAIL');
						$password = $this->request->getPost('NEW_PASSWORD');
						
						if (!strlen($email)) {
							throw new Exception(GetMessage('SOA_ERROR_REG_EMAIL'));
						}
						if (!check_email($email)) {
							throw new Exception(GetMessage('SOA_ERROR_REG_BAD_EMAIL'));
						}
						if (!strlen($password)) {
							throw new Exception(GetMessage('SOA_ERROR_REG_PASS'));
						}
						
						$groupsId = explode(',', COption::GetOptionString('main', 'new_user_registration_def_group', ''));
						
						$user = new CUser();
						$userId = (int) $user->Add(array(
							'LOGIN' => $email,
							'NAME' => $this->request->getPost('NEW_NAME'),
							'LAST_NAME' => $this->request->getPost('NEW_LAST_NAME'),
							'PASSWORD' => $password,
							'CONFIRM_PASSWORD' => $password,
							'EMAIL' => $email,
							'GROUP_ID' => $groupsId,
							'ACTIVE' => 'Y',
							'LID' => SITE_ID,
							'UF_SPAM' => $this->request->getPost('UF_SPAM') == 'Y' ? 'Y' : 'N',
						));
						
						if ($userId <= 0) {
							throw new Exception(
								GetMessage('STOF_ERROR_REG') . ($user->LAST_ERROR ? ': ' . $user->LAST_ERROR : '')
							);
						} else {
							$USER->Authorize($userId);
							if ($USER->IsAuthorized()) {
								if ($this->arParams['SEND_NEW_USER_NOTIFY']) {
									CUser::SendUserInfo(
										$USER->GetID(),
										SITE_ID,
										GetMessage('SOA_NOTE_REG_INFO'),
										true
									);
								}
								
								global $APPLICATION;
								$this->redirectUrl = $APPLICATION->GetCurPageParam();
								return;
							} else {
								$data['SHOW_NEW_FORM'] = false;
								$this->notes[] = GetMessage('SOA_NOTE_REG_CONFIRM');
							}
						}
						break;
					
					default:
						throw new Exception('Unknown action type');
				}
			} catch (Exception $e) {
				$this->errors[] = $e->getMessage();
			}
		}
		
		$this->arResult['IDENTITY'] = $data;
	}
	
	/**
	 * Выполняет шаг оформления заказа
	 *
	 * @return void
	 */
	protected function processOrder()
	{
		global $APPLICATION, $USER;
		
		$this->basket = $this->getBasket(false);
		
		//Если корзина пустая - отправляем заполнять её
		if (!$this->basket['BASKET_ITEMS']) {
			$this->redirectUrl = $this->config['BASKET_URL'];
			return;
		}
		
		try {
			$this->personTypes = $this->getPersonTypes();
			
			$this->userProfiles = $this->getUserProfiles();
			
			$deliveryServicesVsPaySystems = $this->getDeliveryServicesVsPaySystems($this->arParams['DELIVERY_TO_PAYSYSTEM']);
			if ($this->arParams['DELIVERY_TO_PAYSYSTEM'] == 'd2p') {
				$this->deliveryServices = $this->getDeliveryServices();
				$this->paySystems = $this->getPaySystems(
					$deliveryServicesVsPaySystems[$this->data['DELIVERY_ID']]
				);
			} else {
				$this->paySystems = $this->getPaySystems();
				$this->deliveryServices = $this->getDeliveryServices(
					$deliveryServicesVsPaySystems[$this->data['PAY_SYSTEM_ID']]
				);
			}
			$this->calculateOrderPre('deliveryAndPayment');
			
			$this->orderProps = $this->getOrderProps();
			$this->calculateOrderPre('orderProps');
			
			$this->calculateOrder();
			
			$this->proccessUserAccount();
			
			if ($this->isOrderConfirmed()) {
				$this->saveOrder();
			}
		} catch (Exception $e) {
			$this->errors[] = $USER->isAdmin()
				? sprintf(
					'%s in %s (%d)',
					$e->getMessage(),
					$e->getFile(),
					$e->getLine()
				)
				: $e->getMessage();
		}
		
		$this->config['ZIP_AJAX_GATE'] = $this->__path . '/ajax-zip.php';
		
		$this->arResult['PERSON_TYPES'] = &$this->personTypes;
		$this->arResult['USER_PROFILES'] = &$this->userProfiles;
		$this->arResult['DELIVERY_SERVICES'] = &$this->deliveryServices;
		$this->arResult['PAY_SYSTEMS'] = &$this->paySystems;
		$this->arResult['ORDER'] = &$this->order;
		$this->arResult['ORDER_PROPS'] = &$this->orderProps;
	}
	
	/**
	 * Выполняет шаг успешного оформления заказа
	 *
	 * @return void
	 */
	protected function processSuccess()
	{
		global $USER;
		
		$id = (int) $this->request->getQuery('ORDER_ID');
		
		$order = CSaleOrder::GetList(
			array(
				'DATE_UPDATE' => 'DESC',
			),
			array(
				'ID' => $id,
				'LID' => SITE_ID,
				'USER_ID' => $USER->GetID(),
			)
		)->GetNext();
		
		$paySysAction = array();
		
		if ($order) {
			foreach (GetModuleEvents('sale', 'OnSaleComponentOrderOneStepFinal', true) as $event) {
				ExecuteModuleEventEx($event, array($order['ID'], &$order, &$this->arParams));
			}
			
			$order['PRICE_FORMATED'] = $this->formatCurrency($order['PRICE']);
			$order['PRICE_DELIVERY_FORMATED'] = $this->formatCurrency($order['PRICE_DELIVERY']);
			
			if ($order['PAY_SYSTEM_ID'] > 0
				&& $order['PAYED'] != 'Y'
			) {
				$paySysAction = CSalePaySystemAction::GetList(
					array(),
					array(
						'PAY_SYSTEM_ID' => $order['PAY_SYSTEM_ID'],
						'PERSON_TYPE_ID' => $order['PERSON_TYPE_ID']
					),
					false,
					false,
					array(
						'NAME',
						'ACTION_FILE',
						'NEW_WINDOW',
						'PARAMS',
						'ENCODING',
						'LOGOTIP'
					)
				)->Fetch();
				if ($paySysAction) {
					if (strlen($paySysAction['ACTION_FILE'])
						&& $paySysAction['NEW_WINDOW'] != 'Y'
					) {
						CSalePaySystemAction::InitParamArrays($order, $order['ID'], $paySysAction['PARAMS']);
						
						$pathToAction = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] . $paySysAction['ACTION_FILE']);
						while (substr($pathToAction, strlen($pathToAction) - 1, 1) == '/') {
							$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);
						}
						if (file_exists($pathToAction)) {
							if (is_dir($pathToAction) && file_exists($pathToAction . '/payment.php')) {
								$pathToAction .= '/payment.php';
							}
							$paySysAction['PATH_TO_ACTION'] = $pathToAction;
						}
						
						if (strlen($paySysAction['ENCODING'])
							&& $paySysAction['ENCODING'] != SITE_CHARSET
						) {
							AddEventHandler('main', 'OnEndBufferContent', function(&$content) use ($paySysAction) {
								global $APPLICATION;
								
								header('Content-Type: text/html; charset=' . $paySysAction['ENCODING']);
								$content = $APPLICATION->ConvertCharset($content, SITE_CHARSET, $paySysAction['ENCODING']);
								$content = str_replace('charset=' . SITE_CHARSET, 'charset=' . $paySysAction['ENCODING'], $content);
							});
						}
					}
					
					$paySysAction['LOGOTIP'] = $paySysAction['LOGOTIP'] ? CFile::ResizeImageGet($paySysAction['LOGOTIP'], array(
						'height' => 100,
						'width' => 100,
					)) : null;
				}
			}
		}
		
		$this->arResult['ORDER_ID'] = $id;
		$this->arResult['ORDER'] = $order;
		$this->arResult['PAY_SYSTEM'] = $paySysAction;
	}
	
	/**
	 * Возвращает типы плательщиков
	 *
	 * @return array
	 */
	protected function getPersonTypes()
	{
		$itemsRecordset = CSalePersonType::GetList(
			array(
				'SORT' => 'ASC',
				'NAME' => 'ASC',
			),
			array(
				'LID' => SITE_ID,
				'ACTIVE' => 'Y',
			)
		);
		$items = array();
		$selectedId = 0;
		while ($item = $itemsRecordset->GetNext()) {
			$item['SELECTED'] = $this->data['PERSON_TYPE_ID'] == $item['ID'];
			
			if ($item['SELECTED']) {
				$selectedId = $item['ID'];
			}
			
			$items[$item['ID']] = $item;
		}
		
		if (!$selectedId) {
			foreach ($items as &$item) {
				$item['SELECTED'] = true;
				$selectedId = $item['ID'];
				break;
			}
			unset($item);
		}
		
		$this->data['PERSON_TYPE_ID'] = $selectedId;
		
		return $items;
	}
	
	/**
	 * Возвращает профили покупателя
	 *
	 * @return array
	 */
	protected function getUserProfiles()
	{
		global $USER;
		
		$itemsRecordset = CSaleOrderUserProps::GetList(
			array(
				'DATE_UPDATE' => 'DESC',
			),
			array(
				'PERSON_TYPE_ID' => $this->data['PERSON_TYPE_ID'],
				'USER_ID' => intval($USER->GetID()),
			)
		);
		$items = array();
		$selectedId = 0;
		while ($item = $itemsRecordset->GetNext()) {
			$item['SELECTED'] = $this->data['PROFILE_ID'] == $item['ID'];
			
			if ($item['SELECTED']) {
				$selectedId = $item['ID'];
			}
			
			$items[$item['ID']] = $item;
		}
		
		if (!$selectedId) {
			foreach ($items as &$item) {
				$item['SELECTED'] = true;
				$selectedId = $item['ID'];
				break;
			}
			unset($item);
		}
		
		$this->data['PROFILE_ID'] = $selectedId;
		
		return $items;
	}
	
	/**
	 * Возвращает карту совместимости служб доставки и платежных систем
	 *
	 * @param string $dir Направление совместимости d2p|p2d
	 * @return array
	 */
	protected function getDeliveryServicesVsPaySystems($dir = 'd2p')
	{
		$itemsRecordset = CSaleDelivery2PaySystem::GetList();
		$result = array();
		while ($item = $itemsRecordset->Fetch()) {
			$deliveryId = $item['DELIVERY_ID'] . ($item['DELIVERY_PROFILE_ID'] ? ':' . $item['DELIVERY_PROFILE_ID'] : '');
			if ($dir == 'd2p') {
				$result[$deliveryId][$item['PAYSYSTEM_ID']] = true;
			} else {
				$result[$item['PAYSYSTEM_ID']][$deliveryId] = true;
			}
		}
		
		return $result;
	}
	
	/**
	 * Возвращает службы доставки
	 *
	 * @param mixed $allowed Совместимые службы доставки
	 * @return array
	 */
	protected function getDeliveryServices($allowed = null)
	{
		//Формируем список служб доставки
		$items = $this->getDeliveryServicesAutomatic() + $this->getDeliveryServicesManual();
		
		//Оставляем только совместимые, если заданы
		if (is_array($allowed)) {
			$items = array_intersect_key($items, $allowed);
		}
		
		//Сортируем по индексу сортировки
		uasort($items, array('CSaleBasketHelper', 'cmpBySort'));
		
		//Определяем параметры и выбранную службу доставки
		$selectedItem = array();
		foreach ($items as &$item) {
			$item['LOGOTIP'] = CFile::GetFileArray($item['LOGOTIP']);
			
			if ($item['PERIOD_FROM'] > 0 || $item['PERIOD_TO'] > 0) {
				$item['PERIOD_TEXT'] = GetMessage('SOA_DELIV_PERIOD');
				if ($item['PERIOD_FROM'] > 0) {
					$item['PERIOD_TEXT'] .= ' ' . GetMessage('SOA_FROM') . ' ' . intval($item['PERIOD_FROM']);
				}
				if ($item['PERIOD_TO'] > 0) {
					$item['PERIOD_TEXT'] .= ' ' . GetMessage('SOA_TO') . ' ' . intbal($item['PERIOD_TO']);
				}
				if ($item['PERIOD_TYPE'] == 'H') {
					$item['PERIOD_TEXT'] .= ' ' . GetMessage('SOA_HOUR') . ' ';
				} elseif ($item['PERIOD_TYPE'] == 'M') {
					$item['PERIOD_TEXT'] .= ' ' . GetMessage('SOA_MONTH') . ' ';
				} else {
					$item['PERIOD_TEXT'] .= ' ' . GetMessage('SOA_DAY') . ' ';
				}
			}
			
			$item['SELECTED'] = $this->data['DELIVERY_ID'] == $item['ID'];
			
			if ($item['SELECTED']) {
				$selectedItem = $item;
			}
		}
		unset($item);
		
		if (!$selectedItem) {
			foreach ($items as &$item) {
				$item['SELECTED'] = true;
				$selectedItem = $item;
				break;
			}
			unset($item);
		}
		$this->data['DELIVERY_ID'] = $selectedItem ? $selectedItem['ID'] : '';
		
		return $items;
	}
	
	/**
	 * Возвращает автоматизированные службы доставки
	 *
	 * @return array
	 */
	protected function getDeliveryServicesAutomatic()
	{
		$filter = array(
			'COMPABILITY' => array(
				'PRICE' => $this->basket['LOCAL_PRICE'],
				'WEIGHT' => $this->basket['LOCAL_WEIGHT'],
				'MAX_DIMENSIONS' => $this->basket['MAX_DIMENSIONS'],
				'ITEMS' => $this->basket['BASKET_ITEMS'],
				'LOCATION_FROM' => COption::GetOptionString('sale', 'location', false, SITE_ID),
				//'LOCATION_TO' => $this->orderPre['DELIVERY_LOCATION'],
				//'LOCATION_ZIP' => $this->orderPropZip ? $this->orderPropZip['VALUE'] : '',
			)
		);
		
		$itemsRecordset = CSaleDeliveryHandler::GetList(
			array(
				'SORT' => 'ASC',
			),
			$filter
		);
		$items = array();
		while ($item = $itemsRecordset->Fetch()) {
			if (!is_array($item) || !is_array($item['PROFILES'])) {
				continue;
			}
			
			$profiles = $item['PROFILES'];
			unset($item['PROFILES'], $item['CONFIG']);
			foreach ($profiles as $profileId => $profile) {
				if ($profile['ACTIVE'] == 'Y') {
					$profile['TYPE'] = 'automatic';
					$profile['SID'] = $item['SID'];
					$profile['PID'] = $profileId;
					$profile['ID'] = $item['SID'] . ':' . $profileId;
					$profile['NAME'] = $profile['TITLE'];
					$profile['SORT'] = $item['SORT'];
					$profile['SELECTED'] = $this->data['DELIVERY_ID'] == $profile['ID'];
					$profile['HANDLER'] = $item;
					
					$items[$profile['ID']] = $profile;
				}
			}
		}
		
		return $items;
	}
	
	/**
	 * Возвращает настраиваемые службы доставки
	 *
	 * @return array
	 */
	protected function getDeliveryServicesManual()
	{
		$itemsRecordset = CSaleDelivery::GetList(
			array(
				'SORT' => 'ASC',
				'NAME' => 'ASC',
			),
			array(
				'LID' => SITE_ID,
				'+<=WEIGHT_FROM' => $this->basket['LOCAL_WEIGHT'],
				'+>=WEIGHT_TO' => $this->basket['LOCAL_WEIGHT'],
				'+<=ORDER_PRICE_FROM' => $this->basket['LOCAL_PRICE'],
				'+>=ORDER_PRICE_TO' => $this->basket['LOCAL_PRICE'],
				'ACTIVE' => 'Y',
				//'LOCATION' => $this->orderPre['DELIVERY_LOCATION'],
			)
		);
		$items = array();
		while ($item = $itemsRecordset->Fetch()) {
			$deliveryData = CSaleDelivery::GetByID($item['ID']);
			$item['DESCRIPTION'] = $deliveryData['DESCRIPTION'];
			$item['PRICE'] = roundEx(CCurrencyRates::ConvertCurrency(
				$item['PRICE'],
				$item['CURRENCY'],
				$this->config['CURRENCY']
			), SALE_VALUE_PRECISION);
			$item['CURRENCY'] = $this->config['CURRENCY'];
			
			$item['STORES'] = array();
			$store = strlen($item['STORE']) ? unserialize($item['STORE']) : array();
			unset($item['STORE'], $item['~STORE']);
			if ($store) {
				$stores = CCatalogStore::GetList(
					array(
						'SORT' => 'DESC',
						'ID' => 'DESC',
					),
					array(
						'ACTIVE' => 'Y',
						'ID' => $store,
						'ISSUING_CENTER' => 'Y',
						'+SITE_ID' => SITE_ID,
					),
					false,
					false,
					array(
						'ID',
						'TITLE',
						'ADDRESS',
						'DESCRIPTION',
						'IMAGE_ID',
						'PHONE',
						'SCHEDULE',
						'GPS_N',
						'GPS_S',
						'ISSUING_CENTER',
						'SITE_ID',
					)
				);
				$selectedStoreId = 0;
				while ($store = $stores->Fetch()) {
					$store['SELECTED'] = $store['ID'] == $this->data['BUYER_STORE'];
					if ($store['SELECTED']) {
						$selectedStoreId = $store['ID'];
					}
					
					if ($store['IMAGE_ID'] > 0)
						$store['IMAGE_ID'] = CFile::GetFileArray($store['IMAGE_ID']);
					
					$item['STORES'][] = $store;
				}
				if (!$selectedStoreId) {
					$item['STORES'][0]['SELECTED'] = true;
				}
			}
			
			$items[$item['ID']] = $item;
		}
		
		return $items;
	}
	
	/**
	 * Рассчитываеть стоимость доставки для указанной службы доставки
	 *
	 * @param array $deliveryService Служба доставки
	 * @return void
	 */
	protected function calculateDeliveryServicePrice(&$deliveryService)
	{
		$orderParams = array(
			'PRICE' => $this->basket['LOCAL_PRICE'],
			'WEIGHT' => $this->basket['LOCAL_WEIGHT'],
			'DIMENSIONS' => $this->basket['MAX_DIMENSIONS'],
			'ITEMS' => $this->basket['BASKET_ITEMS'],
			'EXTRA_PARAMS' => array(),
			'LOCATION_FROM' => COption::GetOptionString('sale', 'location', false, SITE_ID),
			'LOCATION_TO' => $this->orderPre['DELIVERY_LOCATION'],
			'LOCATION_ZIP' => $this->orderPropZip ? $this->orderPropZip['VALUE'] : '',
		);
		
		if (!isset($deliveryService['EXTRA_PARAMS'])) {
			$deliveryService['EXTRA_PARAMS'] = CSaleDeliveryHandler::GetHandlerExtraParams(
				$deliveryService['SID'],
				$deliveryService['PID'],
				$orderParams,
				SITE_ID
			);
		}
		
		if (!isset($deliveryService['PRICE'])) {
			$deliveryService['PRICE'] = 0;
			if ($deliveryService['SELECTED']
				|| $this->params['DELIVERY_NO_AJAX']
			) {
				$orderParams['EXTRA_PARAMS'] = $deliveryService['EXTRA_PARAMS'];
				
				$priceData = CSaleDeliveryHandler::CalculateFull(
					$deliveryService['SID'],
					$deliveryService['PID'],
					$orderParams,
					$this->config['CURRENCY']
				);
				if ($priceData['RESULT'] == 'ERROR') {
					$deliveryService['ERROR'] = $priceData['TEXT'];
				} else {
					$deliveryService['PRICE'] = roundEx($priceData['VALUE'], SALE_VALUE_PRECISION);
					$deliveryService['PACKS_COUNT'] = $priceData['PACKS_COUNT'];
				}
			}
			
			$deliveryService['PRICE_FORMATED'] = $this->formatCurrency(
				$deliveryService['PRICE'],
				$deliveryService['CURRENCY']
			);
		}
	}
	
	/**
	 * Возвращает платежные системы
	 *
	 * @param mixed $allowed Совместимые службы доставки
	 * @return array
	 */
	protected function getPaySystems($allowed = null)
	{
		$filter = array(
			'ACTIVE' => 'Y',
			'PERSON_TYPE_ID' => $this->data['PERSON_TYPE_ID'],
			'PSA_HAVE_PAYMENT' => 'Y',
		);
		
		//Только совместимые, если заданы
		if (is_array($allowed)) {
			$filter['ID'] = array_keys($allowed);
		}
		
		$itemsRecordset = CSalePaySystem::GetList(
			array(
				'SORT' => 'ASC',
				'PSA_NAME' => 'ASC'
			),
			$filter
		);
		$items = array();
		$selectedItemId = 0;
		while ($item = $itemsRecordset->Fetch()) {
			if (!CSalePaySystemsHelper::checkPSCompability(
				$item['PSA_ACTION_FILE'],
				$this->order,
				$this->basket['LOCAL_PRICE'],
				$this->orderPre['DELIVERY_PRICE'],
				$this->orderPre['DELIVERY_LOCATION']
			)) {
				continue;
			}
			
			$item['PSA_LOGOTIP'] = CFile::GetFileArray($item['PSA_LOGOTIP']);
			$item['PRICE'] = CSalePaySystemsHelper::getPSPrice(
				$item,
				$this->basket['LOCAL_PRICE'],
				$this->orderPre['DELIVERY_PRICE'],
				$this->orderPre['DELIVERY_LOCATION']
			);
			
			$item['SELECTED'] = $this->data['PAY_SYSTEM_ID'] == $item['ID'];
			if ($item['SELECTED']) {
				$selectedItemId = $item['ID'];
			}
			
			$items[$item['ID']] = $item;
		}
		
		if (!$selectedItemId) {
			foreach ($items as &$item) {
				$item['SELECTED'] = true;
				$selectedItemId = $item['ID'];
				break;
			}
			unset($item);
		}
		$this->data['PAY_SYSTEM_ID'] = $selectedItemId;
		
		return $items;
	}
	
	/**
	 * Возвращает свойства заказа
	 *
	 * @return array
	 */
	protected function getOrderProps()
	{
		$propsRecordset = CSaleOrderProps::GetList(
			array(
				'GROUP_SORT' => 'ASC',
				'PROPS_GROUP_ID' => 'ASC',
				'USER_PROPS' => 'ASC',
				'SORT' => 'ASC',
				'NAME' => 'ASC',
			),
			array(
				'PERSON_TYPE_ID' => $this->data['PERSON_TYPE_ID'],
				'ACTIVE' => 'Y',
				'UTIL' => 'N',
				'RELATED' => array(
					'PAYSYSTEM_ID' => $this->data['PAY_SYSTEM_ID'],
					'DELIVERY_ID' => $this->data['DELIVERY_ID'],
					'TYPE' => 'WITH_NOT_RELATED',
				)
			),
			false,
			false,
			array(
				'ID',
				'CODE',
				'TYPE',
				'NAME',
				'REQUIED',
				'SORT',
				'DEFAULT_VALUE',
				'SIZE1',
				'SIZE2',
				'DESCRIPTION',
				'MULTIPLE',
				'USER_PROPS',
				'INPUT_FIELD_LOCATION',
				'PAYSYSTEM_ID',
				'IS_LOCATION',
				'IS_LOCATION4TAX',
				'IS_EMAIL',
				'IS_ZIP',
				'IS_PAYER',
				'IS_PROFILE_NAME',
				'DELIVERY_ID',
				'PROPS_GROUP_ID',
				'GROUP_NAME',
				'GROUP_SORT',
			)
		);
		$props = array();
		while ($prop = $propsRecordset->GetNext()) {
			$prop['PROPS_GROUP_ID'] = intval($prop['PROPS_GROUP_ID']);
			$groupKey = $prop['PROPS_GROUP_ID'] && in_array($prop['PROPS_GROUP_ID'], $this->arParams['DELIVERY_GROUPS']) ? 'DELIVERY' : $prop['PROPS_GROUP_ID'];
			
			if (!isset($props[$groupKey])) {
				$props[$groupKey] = array(
					'ID' => $prop['PROPS_GROUP_ID'],
					'NAME' => $prop['GROUP_NAME'],
					'SORT' => $prop['GROUP_SORT'],
					'ITEMS' => array(),
				);
			}
			$group = &$props[$groupKey];
			
			unset(
				$prop['PROPS_GROUP_ID'],
				$prop['~PROPS_GROUP_ID'],
				$prop['GROUP_NAME'],
				$prop['~GROUP_NAME'],
				$prop['GROUP_SORT'],
				$prop['~GROUP_SORT']
			);
			
			$prop['IS_LOCATION'] = $prop['IS_LOCATION'] == 'Y';
			$prop['IS_LOCATION4TAX'] = $prop['IS_LOCATION4TAX'] == 'Y';
			$prop['IS_EMAIL'] = $prop['IS_EMAIL'] == 'Y';
			$prop['IS_ZIP'] = $prop['IS_ZIP'] == 'Y';
			$prop['IS_PAYER'] = $prop['IS_PAYER'] == 'Y';
			$prop['IS_PROFILE_NAME'] = $prop['IS_PROFILE_NAME'] == 'Y';
			$prop['IS_PHONE'] = stripos($prop['CODE'], 'phone') !== false;
			
			$prop['VALUE'] = $prop['~VALUE'] = $this->getOrderPropValue($prop);
			
			$group['ITEMS'][$prop['ID']] = &$prop;
			
			if ($prop['TYPE'] == 'LOCATION' && $prop['IS_LOCATION']) {
				$this->orderPropLocation = &$prop;
			}
			
			if ($prop['IS_ZIP']) {
				$this->orderPropZip = &$prop;
			}
			
			unset($prop);
		}
		
		foreach ($props as &$group) {
			foreach ($group['ITEMS'] as &$prop) {
				$this->adjustOrderProp($prop);
			}
			unset($prop);
		}
		unset($group);
		
		foreach ($this->orderPropsDel as $propId) {
			foreach ($props as &$group) {
				if (array_key_exists($propId, $group['ITEMS'])) {
					unset($group['ITEMS'][$propId]);
				}
			}
			unset($group);
		}
		
		return $props;
	}
	
	/**
	 * Возвращает значение свойства заказа
	 *
	 * @param array $prop Свойство
	 * @return mixed
	 */
	protected function getOrderPropValue(&$prop)
	{
		global $USER;
		
		$prop['DATA_NAME'] = 'ORDER_PROP_' . ($prop['CODE'] ? $prop['CODE'] : $prop['ID']);
		$prop['FIELD_NAME'] = $prop['DATA_NAME'];
		if ($prop['TYPE'] == 'MULTISELECT') {
			$prop['FIELD_NAME'] .= '[]';
			$prop['DEFAULT_VALUE'] = explode(',', $prop['DEFAULT_VALUE']);
		}
		
		//Если форма не заполнялась или пользователь сменил профиль, то берём из профиля
		if (!isset($this->data[$prop['DATA_NAME']])
			|| ($this->isProfileChanged() && $this->data['PROFILE_ID'])
		) {
			$value = CSaleOrderUserPropsValue::GetList(
				array(
					'SORT' => 'ASC',
				),
				array(
					'USER_PROPS_ID' => $this->data['PROFILE_ID'],
					'ORDER_PROPS_ID' => $prop['ID'],
					'USER_ID' => intval($USER->GetID()),
				),
				false,
				false,
				array(
					'VALUE',
					'PROP_TYPE',
					'VARIANT_NAME',
					'SORT',
					'ORDER_PROPS_ID',
				)
			)->Fetch();
			if ($value) {
				$propValue = $value['VALUE'];
			}
		}
		
		//Если не из профиля, и пользователь не заполнял форму, то местоположение берем из гео-данных
		if (!isset($propValue)
			&& !isset($this->data[$prop['DATA_NAME']])
			&& $prop['TYPE'] == 'LOCATION'
			&& ($prop['IS_LOCATION'] || $prop['IS_LOCATION4TAX'])
		) {
			try {
				$location = \Site\Main\GeoServices::getCurrentLocation();
				if ($location) {
					$propValue = $location['ID'];
				}
			} catch (Exception $e) {
			}
		}
		
		//Из данных формы
		if (!isset($propValue)
			&& isset($this->data[$prop['DATA_NAME']])
		) {
			$propValue = $this->data[$prop['DATA_NAME']];
		}
		
		//Из значения по-умолчанию
		$prop['IS_DEFAULT_VALUE'] = false;
		if (!isset($propValue)) {
			$propValue = $prop['DEFAULT_VALUE'];
			$prop['IS_DEFAULT_VALUE'] = true;
		}
		
		return $prop['TYPE'] == 'MULTISELECT' && !is_array($propValue)
			? explode(',', $propValue)
			: $propValue;
	}
	
	/**
	 * Настраивает значение свойства заказа в соотв-ии с его ограничениями
	 *
	 * @param array $prop Свойство
	 * @return void
	 */
	protected function adjustOrderProp(&$prop)
	{
		global $USER;
		
		$prop['REQUIED'] = $prop['REQUIED'] == 'Y';
		if ($prop['IS_EMAIL']
			|| $prop['IS_PROFILE_NAME']
			|| $prop['IS_LOCATION']
			|| $prop['IS_LOCATION4TAX']
			|| $prop['IS_PAYER']
			|| $prop['IS_ZIP']
		) {
			$prop['REQUIED'] = true;
		}
		
		$applyValToData = false;
		
		switch ($prop['TYPE']) {
			case 'CHECKBOX':
				$prop['SELECTED'] = $prop['VALUE'] == 'Y';
				$prop['VALUE_FORMATED'] = $prop['SELECTED'] ? GetMessage('SOA_Y') : GetMessage('SOA_N');
				break;
			
			case 'RADIO':
			case 'SELECT':
				$variants = CSaleOrderPropsVariant::GetList(
					array(
						'SORT' => 'ASC',
						'NAME' => 'ASC',
					),
					array(
						'ORDER_PROPS_ID' => $prop['ID'],
					),
					false,
					false,
					array('*')
				);
				$prop['VARIANTS'] = array();
				$selectedVariant = array();
				while ($variant = $variants->GetNext()) {
					$variant['SELECTED'] = $variant['VALUE'] == $prop['VALUE'];
					if ($variant['SELECTED']) {
						$selectedVariant = $variant;
					}
					$prop['VARIANTS'][] = $variant;
				}
				
				if (!$selectedVariant) {
					$prop['VARIANTS'][0]['SELECTED']= true;
					$selectedVariant = $prop['VARIANTS'][0];
				}
				
				$prop['VALUE'] = $prop['~VALUE'] = $selectedVariant ? $selectedVariant['VALUE'] : '';
				$prop['VALUE_FORMATED'] = $selectedVariant ? $selectedVariant['NAME'] : '';
				$applyValToData = true;
				break;
			
			case 'MULTISELECT':
				$variants = CSaleOrderPropsVariant::GetList(
					array(
						'SORT' => 'ASC',
					),
					array(
						'ORDER_PROPS_ID' => $prop['ID'],
					),
					false,
					false,
					array('*')
				);
				$prop['VARIANTS'] = array();
				$prop['VALUE_FORMATED'] = array();
				while ($variant = $variants->GetNext()) {
					$variant['SELECTED'] = in_array($variant['VALUE'], $prop['VALUE']);
					
					if ($variant['SELECTED']) {
						$prop['VALUE_FORMATED'][] = $variant['NAME'];
					}
					
					$prop['VARIANTS'][] = $variant;
				}
				$prop['VALUE_FORMATED'] = implode(',', $prop['VALUE_FORMATED']);
				break;
			
			case 'TEXT':
				if (!strlen($prop['VALUE'])) {
					if ($prop['IS_EMAIL']) {
						$prop['VALUE'] = $USER->GetEmail();
					} elseif ($prop['IS_PAYER']) {
						if ($userData = CUser::GetByID($USER->GetID())->Fetch()) {
							$prop['VALUE'] = CUser::FormatName(
								CSite::GetNameFormat(false),
								$userData,
								false,
								false
							);
						}
					}
					
					if ($prop['IS_ZIP']
						&& $this->orderPropLocation
						&& $this->orderPropLocation['VALUE']
					) {
						$zipData = CSaleLocation::GetLocationZIP(
							$this->orderPropLocation['VALUE']
						)->Fetch();
						if ($zipData) {
							$prop['VALUE'] = $zipData['ZIP'];
						}
					}
				}
				
				$prop['VALUE'] = $prop['VALUE_FORMATED'] = htmlspecialcharsEx($prop['VALUE']);
				break;
			
			case 'TEXTAREA':
				$prop['SIZE1'] = max(intval($prop['SIZE1']), 40);
				$prop['SIZE2'] = max(intval($prop['SIZE2']), 4);
				$prop['VALUE_FORMATED'] = htmlspecialcharsEx($prop['VALUE']);
				break;
			
			case 'FILE':
				$prop['VALUE'] = $prop['VALUE'] ? CSaleHelper::getFileInfo($prop['VALUE']) : '';
				break;
			
			case 'LOCATION':
				$prop['VALUE'] = intval($prop['VALUE']);
				$prop['VALUE_FORMATED'] = '';
				
				if (!$prop['VALUE']
					&& $this->orderPropZip
					&& $this->orderPropZip['VALUE']
				) {
					$locationData = CSaleLocation::GetByZIP($this->orderPropZip['VALUE']);
					if ($locationData) {
						$prop['VALUE'] = $locationData['ID'];
					}
				}
				
				$locationData = array();
				if ($prop['VALUE']) {
					$locationData = CSaleLocation::GetList(
						array(),
						array(
							'ID' => $prop['VALUE'],
							'LID' => LANGUAGE_ID,
						),
						false,
						false,
						array(
							'ID',
							'COUNTRY_ID',
							'COUNTRY_NAME',
							'REGION_ID',
							'REGION_NAME',
							'CITY_ID',
							'CITY_NAME',
						)
					)->GetNext();
					
					if (!$locationData) {
						$prop['VALUE'] = 0;
						$regionId = (int) isset($this->data['REGION_' . $prop['FIELD_NAME']]) ? $this->data['REGION_' . $prop['FIELD_NAME']] : 0;
						$countryId = (int) isset($this->data['COUNTRY_' . $prop['FIELD_NAME']]) ? $this->data['COUNTRY_' . $prop['FIELD_NAME']] : 0;
						
						if ($regionId > 0) {
							$locationData = CSaleLocation::GetList(
								array(),
								array(
									'REGION_ID' => $regionId,
									'CITY_ID' => false
								),
								false,
								false,
								array(
									'ID',
									'COUNTRY_ID',
									'COUNTRY_NAME',
									'REGION_ID',
									'REGION_NAME',
									'CITY_ID',
									'CITY_NAME',
								)
							)->Fetch();
						}
						
						if (!$locationData && $countryId > 0)
						{
							$locationData = CSaleLocation::GetList(
								array(),
								array(
									'COUNTRY_ID' => $countryId,
									'REGION_ID' => false,
									'CITY_ID' => false,
								),
								false,
								false,
								array(
									'ID',
									'COUNTRY_ID',
									'COUNTRY_NAME',
									'REGION_ID',
									'REGION_NAME',
									'CITY_ID',
									'CITY_NAME',
								)
							)->Fetch();
						}
						
					}
					
					if ($locationData) {
						$prop['VALUE'] = $locationData['ID'];
						$prop['VALUE_FORMATED'] = array();
						if ($locationData['COUNTRY_NAME']) {
							$prop['VALUE_FORMATED'][] = $locationData['COUNTRY_NAME'];
						}
						if ($locationData['REGION_NAME']) {
							$prop['VALUE_FORMATED'][] = $locationData['REGION_NAME'];
						}
						if ($locationData['CITY_NAME']) {
							$prop['VALUE_FORMATED'][] = $locationData['CITY_NAME'];
						}
						$prop['VALUE_FORMATED'] = implode(', ', $prop['VALUE_FORMATED']);
					}
				}
				$prop['~VALUE'] = $prop['VALUE'];
				
				//Если указано альтернативное поле
				if ($prop['INPUT_FIELD_LOCATION']) {
					if ($locationData && $locationData['CITY_ID'] == 0) {
						$prop['READ_ONLY'] = true;
					} else {
						$this->orderPropsDel[] = $prop['INPUT_FIELD_LOCATION'];
					}
				}
				
				//Список вариантов местоположений
				/*$variants = CSaleLocation::GetList(
					array(
						'SORT' => 'ASC',
						'COUNTRY_NAME_LANG' => 'ASC',
						'CITY_NAME_LANG' => 'ASC',
					),
					array(
						'LID' => LANGUAGE_ID,
					),
					false,
					false,
					array(
						'ID',
						'COUNTRY_NAME',
						'CITY_NAME',
						'SORT',
						'COUNTRY_NAME_LANG',
						'CITY_NAME_LANG',
					)
				);
				$prop['VARIANTS'] = array();
				while ($variant = $variants->GetNext()) {
					$variant['SELECTED'] = $prop['VALUE'] == $variant['ID'];
					
					$variant['NAME'] = array();
					if ($variant['COUNTRY_NAME']) {
						$variant['NAME'][] = $variant['COUNTRY_NAME'];
					}
					if ($variant['CITY_NAME']) {
						$variant['NAME'][] = $variant['CITY_NAME'];
					}
					$variant['NAME'] = implode(', ', $variant['NAME']);
					
					$prop['VARIANTS'][] = $variant;
				}*/
				
				$applyValToData = true;
				break;
		}
		
		if ($applyValToData) {
			$this->data[$prop['DATA_NAME']] = $prop['~VALUE'];
		}
	}
	
	/**
	 * Проверяет корректность заполнения свойств заказа
	 *
	 * @return void
	 */
	protected function checkOrderProps()
	{
		foreach ($this->orderProps as &$group) {
			foreach ($group['ITEMS'] as &$prop) {
				$propVal = $prop['~VALUE'];
				
				if ($prop['IS_EMAIL']
					&& $propVal
					&& !check_email($propVal)
				) {
					$this->errors[$prop['FIELD_NAME']] = GetMessage('SOA_ERROR_EMAIL');
				}
				
				if ($prop['REQUIED']) {
					$error = false;
					switch ($prop['TYPE']) {
						case 'TEXT':
						case 'TEXTAREA':
						case 'RADIO':
						case 'SELECT':
						case 'CHECKBOX':
							$error = strlen($propVal) == 0;
							break;
						case 'LOCATION':
							$error = $propVal <= 0;
							break;
						case 'MULTISELECT':
							$error = count($propVal) == 0;
							break;
						case 'FILE':
							if (is_array($propVal)) {
								foreach ($propVal as $fileData) {
									if (!array_key_exists('name', $fileData)
										|| !strlen($fileData['name'])
									) {
										$error = true;
										break;
									}
								}
							}
							break;
					}
					if ($error) {
						$this->errors[$prop['FIELD_NAME']] = sprintf(GetMessage('SOA_ERROR_REQUIRE'), $prop['NAME']);
					}
				}
			}
			unset($prop);
		}
		unset($group);
	}
	
	/**
	 * Производит расчет предварительных данных заказа на различных этапах
	 *
	 * @param string $stage Этап расчета
	 * @return void
	 */
	protected function calculateOrderPre($stage)
	{
		switch ($stage) {
			case 'deliveryAndPayment':
				break;
			
			case 'orderProps':
				//Определяем местоположение
				foreach ($this->orderProps as &$group) {
					foreach ($group['ITEMS'] as &$prop) {
						if ($prop['TYPE'] == 'LOCATION') {
							if ($prop['IS_LOCATION']) {
								$this->orderPre['DELIVERY_LOCATION'] = $prop['VALUE'];
							}
							if ($prop['IS_LOCATION4TAX']) {
								$this->orderPre['TAX_LOCATION'] = $prop['VALUE'];
							}
						}
					}
					unset($prop);
				}
				unset($group);
				
				//Считаем стоимость
				$this->orderPre['DELIVERY_PRICE'] = 0;
				foreach ($this->deliveryServices as &$deliveryservice) {
					$this->calculateDeliveryServicePrice($deliveryservice);
					if ($deliveryservice['SELECTED']) {
						$this->orderPre['DELIVERY_PRICE'] = $deliveryservice['PRICE'];
					}
				}
				unset($deliveryservice);
				
				break;
		}
	}
	
	/**
	 * Производит полный расчет заказа
	 *
	 * @return void
	 */
	protected function calculateOrder()
	{
		global $USER;
		
		//Набираем св-ва
		$props = array();
		foreach ($this->orderProps as &$group) {
			foreach ($group['ITEMS'] as &$prop) {
				$props[$prop['ID']] = $prop['~VALUE'];
			}
			unset($prop);
		}
		unset($group);
		
		//Модуль sale не станет рассчитывать стоимость доставки, если не найдёт заполненное св-во с типом "Местоположение", исправим этот недостаток
		\Bitrix\Main\EventManager::getInstance()->addEventHandler(
			'sale',
			'OnSaleCalculateOrderProps',
			function($order) {
				if (isset($order['DELIVERY_LOCATION']) && $order['DELIVERY_LOCATION'] > 0) {
					return;
				}
				
				try {
					$locationId = \Bitrix\Main\Config\Option::get('sale', 'location', '1', $order['SITE_ID']);
				} catch (\Exception $e) {
					$locationId = 1;
				}
				$order['DELIVERY_LOCATION'] = $locationId;
			}
		);
		
		//Считаем заказ через API
		$this->order = CSaleOrder::DoCalculateOrder(
			SITE_ID,
			$USER->GetID(),
			$this->basket['BASKET_ITEMS'],
			$this->data['PERSON_TYPE_ID'],
			$props,
			$this->data['DELIVERY_ID'],
			$this->data['PAY_SYSTEM_ID'],
			array(
				'CURRENCY' => $this->config['CURRENCY'],
			),
			$errors,
			$warnings
		);
		
		//Запишем предупреждения
		if (is_array($warnings)) {
			foreach ($warnings as $warning) {
				$this->notes[] = is_array($warning) ? $warning['TEXT'] : $warning;
			}
		}
		
		if ($this->order) {
			//Успешный расчет
			$this->order['SUCCESS'] = true;
		} else {
			//Что-то пошло не так, запишем ошибки
			if (is_array($errors && $errors)) {
				foreach ($errors as $error) {
					$this->errors[] = is_array($error) ? $error['TEXT'] : $error;
				}
			} else {
				$this->errors[] = GetMessage('SOA_ERROR_ORDER_CALCULATE');
			}
			
			//Возьмем результаты из корзины
			$this->order = $this->basket;
			$this->order['SUCCESS'] = false;
		}
		
		//Рассчитываем итоги
		if ($this->order && $this->order['SUCCESS']) {
			$this->calculateBasket($this->order);
			
			$this->order['ORDER_WEIGHT'] = floatval($this->order['ORDER_WEIGHT']);
			$this->order['ORDER_WEIGHT_FORMATED'] = $this->formatWeight($this->order['ORDER_WEIGHT']);
			$this->order['ORDER_PRICE'] = floatval($this->order['ORDER_PRICE']);
			$this->order['ORDER_PRICE_FORMATED'] = $this->formatCurrency($this->order['ORDER_PRICE']);
			$this->order['DELIVERY_PRICE'] = floatval($this->order['DELIVERY_PRICE']);
			$this->order['DELIVERY_PRICE_FORMATED'] = $this->formatCurrency($this->order['DELIVERY_PRICE']);
			$this->order['TAX_PRICE'] = floatval($this->order['TAX_PRICE']);
			$this->order['TAX_PRICE_FORMATED'] = $this->formatCurrency($this->order['TAX_PRICE']);
			$this->order['DISCOUNT_PRICE'] = floatval($this->order['DISCOUNT_PRICE']);
			$this->order['DISCOUNT_PRICE_FORMATED'] = $this->formatCurrency($this->order['DISCOUNT_PRICE']);
			$this->order['USE_VAT'] = $this->order['USE_VAT'] == 'Y';
			$this->order['VAT_SUM'] = floatval($this->order['VAT_SUM']);
			$this->order['VAT_SUM_FORMATED'] = $this->formatCurrency($this->order['VAT_SUM']);
			
			$this->order['TOTAL_PRICE'] = floatval($this->order['ORDER_PRICE'] + $this->order['DELIVERY_PRICE'] + $this->order['TAX_PRICE'] - $this->order['DISCOUNT_PRICE']);
			$this->order['TOTAL_PRICE_FORMATED'] = $this->formatCurrency($this->order['TOTAL_PRICE']);
		}
	}
	
	/**
	 * Проверяет возможность оплатить из средств на счету пользователя
	 *
	 * @return void
	 */
	protected function proccessUserAccount()
	{
		global $USER;
		
		$this->payFromAccount = false;
		if ($this->arParams['PAY_FROM_ACCOUNT']) {
			$userAccount = CSaleUserAccount::GetList(
				array(),
				array(
					'USER_ID' => $USER->GetID(),
					'CURRENCY' => $this->config['CURRENCY'],
				)
			)->GetNext();
			if ($userAccount) {
				$userAccount['CURRENT_BUDGET'] = floatval($userAccount['CURRENT_BUDGET']);
				$userAccount['CURRENT_BUDGET_FORMATED'] = $this->formatCurrency($userAccount['CURRENT_BUDGET']);
				
				if ($userAccount['CURRENT_BUDGET'] > 0) {
					if ($this->arParams['ONLY_FULL_PAY_FROM_ACCOUNT']) {
						if ($userAccount['CURRENT_BUDGET'] >= $this->order['TOTAL_PRICE']) {
							$this->payFromAccount = true;
						}
					} else {
						$this->payFromAccount = true;
					}
				}
			}
		}
		
		if ($this->payFromAccount) {
			$this->order['FROM_ACCOUNT_SUM'] = min($userAccount['CURRENT_BUDGET'], $this->order['TOTAL_SUM']);
			$this->order['FROM_ACCOUNT_SUM_FORMATED'] = $this->formatCurrency($this->order['FROM_ACCOUNT_SUM']);
			
			$this->arResult['USER_ACCOUNT'] = $userAccount;
		} else {
			unset($this->data['PAY_CURRENT_ACCOUNT']);
		}
		
		$this->arResult['PAY_FROM_ACCOUNT'] = $this->payFromAccount;
	}
	
	/**
	 * Оплачивает заказ (частично или полностью) со счета текущего пользователя, если такое возможно
	 *
	 * @return void
	 */
	protected function payOrderFromUserAccount()
	{
		global $USER;
		
		if (!$this->payFromAccount
			|| !$this->data['PAY_CURRENT_ACCOUNT']
		) {
			return;
		}
		
		$withdrawSum = CSaleUserAccount::Withdraw(
			$USER->GetID(),
			$this->order['FROM_ACCOUNT_SUM'],
			$this->config['CURRENCY'],
			$this->order['ID']
		);
		
		if ($withdrawSum > 0) {
			CSaleOrder::Update(
				$this->order['ID'],
				array(
					'SUM_PAID' => $withdrawSum,
					'USER_ID' => $USER->GetID()
				)
			);
			
			if ($withdrawSum == $this->order['TOTAL_SUM']) {
				CSaleOrder::PayOrder($this->order['ID'], 'Y', false, false);
			}
		}
	}
	
	/**
	 * Проверяет использование предавторизации для оплаты
	 *
	 * @return void
	 */
	protected function proccessPrePayment()
	{
		global $APPLICATION;
		
		if (!$this->arParams['USE_PREPAYMENT']) {
			return;
		}
		
		$paySystemAction = CSalePaySystemAction::GetList(
			array(),
			array(
				'PS_ACTIVE' => 'Y',
				'HAVE_PREPAY' => 'Y',
				'PERSON_TYPE_ID' => $this->data['PERSON_TYPE_ID'],
			),
			false,
			false,
			array(
				'ID',
				'PAY_SYSTEM_ID',
				'PERSON_TYPE_ID',
				'NAME',
				'ACTION_FILE',
				'RESULT_FILE',
				'NEW_WINDOW',
				'PARAMS',
				'ENCODING',
				'LOGOTIP'
			)
		)->Fetch();
		if (!$paySystemAction) {
			return;
		}
		if ($this->data['PAY_SYSTEM_ID'] != $paySystemAction['PAY_SYSTEM_ID']) {
			return;
		}
		
		CSalePaySystemAction::InitParamArrays(false, false, $paySystemAction['PARAMS']);
		
		$pathToAction = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] . $paySystemAction['ACTION_FILE']);
		while (substr($pathToAction, strlen($pathToAction) - 1, 1) == '/') {
			$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);
		}
		if (is_dir($pathToAction)) {
			$pathToAction .= '/pre_payment.php';
		}
		if (!file_exists($pathToAction)) {
			return;
		}
		
		include_once $pathToAction;
		$this->prepaymentHandler = new CSalePaySystemPrePayment();
		if (!$this->prepaymentHandler->init()) {
			return;
		}
		
		$this->usePrepayment = true;
		
		$this->prepaymentHandler->encoding = $paySystemAction['ENCODING'];
		if ($this->prepaymentHandler->IsAction()) {
			$this->arResult['PREPAY_ORDER_PROPS'] = $this->prepaymentHandler->getProps();
		} else {
			$this->redirectUrl = $this->prepaymentHandler->BasketButtonAction(array(
				'PATH_TO_ORDER' => $APPLICATION->GetCurPage(),
				'AMOUNT' => $this->order['TOTAL_SUM'],
				'ORDER_REQUEST' => 'Y',
				'BASKET_ITEMS' => $this->order['BASKET_ITEMS'],
			));
		}
		$this->arResult['PREPAY_ADIT_FIELDS'] = $this->prepaymentHandler->getHiddenInputs();
	}
	
	/**
	 * Оплачивает заказ через предавторизацию, если такое возможно
	 *
	 * @return void
	 */
	protected function payOrderFromPrepayment()
	{
		if (!$this->usePrepayment
			|| !$this->prepaymentHandler
			|| !$this->prepaymentHandler->IsAction()
		) {
			return;
		}
		
		$this->prepaymentHandler->orderId = $this->order['ID'];
		$this->prepaymentHandler->orderAmount = $this->order['TOTAL_SUM'];
		$this->prepaymentHandler->deliveryAmount = $this->order['DELIVERY_PRICE'];
		$this->prepaymentHandler->taxAmount = $this->order['TAX_PRICE'];
		
		$basketItems = CSaleBasket::GetList(
			array(
				'ID' => 'ASC'
			),
			array(
				'LID' => SITE_ID,
				'ORDER_ID' => $this->order['ID'],
			),
			false,
			false,
			array(
				'ID',
				'QUANTITY',
				'PRICE',
				'WEIGHT',
				'NAME',
				'CURRENCY',
				'PRODUCT_ID',
				'DETAIL_PAGE_URL'
			)
		);
		$orderData = array(
			'BASKET_ITEMS' => array(),
		);
		while ($basketItem = $basketItems->Fetch()) {
			$orderData['BASKET_ITEMS'][] = $basketItem;
		}
		
		$this->prepaymentHandler->payOrder($orderData);
	}
	
	/**
	 * Сохраняет заказ
	 *
	 * @return void
	 */
	protected function saveOrder()
	{
		global $USER, $APPLICATION;
		
		//Проверяем, все ли правильно заполнено
		$this->checkOrderProps();
		if ($this->errors) {
			return;
		}
		
		if (!$USER->IsAuthorized()) {
			throw new Exception(GetMessage('SOA_ERROR_NEED_AUTH'));
		}
		
		if (!$this->data['PERSON_TYPE_ID']) {
			throw new Exception(GetMessage('SOA_ERROR_SELECT_PERSON_TYPE'));
		}
		if (!$this->personTypes) {
			throw new Exception(GetMessage('SOA_ERROR_PERSON_TYPE'));
		}
		
		if (!$this->data['DELIVERY_ID']) {
			throw new Exception(GetMessage('SOA_ERROR_SELECT_DELIVERY_SERVICE'));
		}
		if (!$this->deliveryServices) {
			throw new Exception(GetMessage('SOA_ERROR_DELIVERY_SERVICE'));
		}
		
		if (!$this->payFromAccount ||
			!$this->data['PAY_CURRENT_ACCOUNT']
		) {
			if (!$this->data['PAY_SYSTEM_ID']) {
				throw new Exception(GetMessage('SOA_ERROR_SELECT_PAY_SYSTEM'));
			}
			if (!$this->paySystems) {
				throw new Exception(GetMessage('SOA_ERROR_PAY_SYSTEM'));
			}
		}
		
		//Формируем дополнительные данные
		$addFields = array(
			//'LID' => SITE_ID,
			'PAYED' => 'N',
			'CANCELED' => 'N',
			'STATUS_ID' => 'N',
			//'USER_ID' => (int) $this->order['USER_ID'],
			'TAX_VALUE' => $this->order['USE_VAT'] ? $this->order['VAT_SUM'] : $this->order['TAX_PRICE'],
			'USER_DESCRIPTION' => $this->data['ORDER_DESCRIPTION']
		);
		
		if ($this->data['BUYER_STORE'] > 0) {
			$addFields['STORE_ID'] = intval($this->data['BUYER_STORE']);
		}
		
		if (\Bitrix\Main\Loader::includeModule('statistic')) {
			$addFields['STAT_GID'] = CStatistic::GetEventParam();
		}
		
		$addFields['AFFILIATE_ID'] = false;
		$affiliateId = (int) CSaleAffiliate::GetAffiliate();
		if ($affiliateId > 0) {
			if (CSaleAffiliate::GetList(
				array(),
				array(
					'SITE_ID' => SITE_ID,
					'ID' => $affiliateId
				)
			)->Fetch()) {
				$addFields['AFFILIATE_ID'] = $affiliateId;
			}
		}
		
		//Сохраняем заказ
		$errors = array();
		$this->order['ID'] = (int) CSaleOrder::DoSaveOrder($this->order, $addFields, 0, $errors);
		if ($errors && is_array($errors)) {
			$this->errors = array_merge($this->errors, $errors);
			return;
		}
		if ($this->order['ID'] == 0) {
			throw new Exception(GetMessage('SOA_ERROR_ORDER'));
		}
		
		//Привязываем корзину к созданному заказу (старый способ для обеспечиния совместимости, т.к. она внутри CSaleOrder::DoSaveOrder привязывется)
		if (CSaleBasket::OrderBasket(
			$this->order['ID'],
			$this->config['BASKET_USER_ID'],
			SITE_ID,
			false
		) === false) {
			throw new Exception(GetMessage('SOA_ERROR_ORDER_BASKET'));
		}
		
		//Добавляем значения, которые были рассчитаны в процессе добавления, например ACCOUNT_NUMBER
		$this->order = array_merge($this->order, (array) CSaleOrder::GetByID($this->order['ID']));
		
		//Оплачиваем со счета текущего пользователя, если нужно
		$this->payOrderFromUserAccount();
		
		//Оплачиваем через предавторизацию, если нужно
		$this->payOrderFromPrepayment();
		
		//Сохраняем профиль покупателя
		CSaleOrderUserProps::DoSaveUserProfile(
			$this->order['USER_ID'],
			$this->data['PROFILE_ID'],
			$this->order['PROFILE_NAME'],
			$this->order['PERSON_TYPE_ID'],
			$this->order['ORDER_PROP'],
			$errors
		);
		
		//Отправляем письмо
		$this->sendOrderEmail();
		
		//Добавляем нотификацию для мобильного приложения
		CSaleMobileOrderPush::send('ORDER_CREATED', array(
			'ORDER_ID' => $this->order['ID'],
		));
		
		//Генерируем статистику
		if (CModule::IncludeModule('statistic')) {
			CStatistic::Set_Event('eStore', 'order_confirm', $this->order['ID']);
		}
		
		//Создаем событие
		foreach (GetModuleEvents('sale', 'OnSaleComponentOrderOneStepComplete', true) as $event) {
			ExecuteModuleEventEx($event, array(
				$this->order['ID'],
				CSaleOrder::GetByID($this->order['ID']),
				$this->arParams
			));
		}
		
		//Отправляем на страницу уведомления о созданном заказе
		$this->redirectUrl = $APPLICATION->GetCurPageParam(
			'ORDER_ID=' . urlencode($this->order['ID']),
			array('ORDER_ID')
		);
	}
	
	/**
	 * Отправляет письмо о новом заказе
	 *
	 * @return void
	 */
	protected function sendOrderEmail()
	{
		global $USER;
		
		$basketItems = array();
		$basketItemsRecordset = CSaleBasket::GetList(
			array(
				'ID' => 'ASC',
			),
			array(
				'ORDER_ID' => $this->order['ID'],
			),
			false,
			false,
			array(
				'ID',
				'PRODUCT_ID',
				'NAME',
				'QUANTITY',
				'PRICE',
				'CURRENCY',
				'TYPE',
				'SET_PARENT_ID',
			)
		);
		while ($basketItem = $basketItemsRecordset->Fetch()) {
			if (CSaleBasketHelper::isSetItem($basketItem)) {
				continue;
			}
			$basketItems[] = $basketItem;
		}
		$basketItems = getMeasures($basketItems);
		$orderList = array();
		foreach ($basketItems as $basketItem) {
			$orderList[] = sprintf(
				'%s - %d %s: %s',
				$basketItem['NAME'],
				$basketItem['QUANTITY'],
				isset($basketItem['MEASURE_TEXT']) && strlen($basketItem['MEASURE_TEXT']) ? $basketItem['MEASURE_TEXT'] : GetMessage('SOA_SHT'),
				$this->formatCurrency($basketItem['PRICE'], $basketItem['CURRENCY'])
			);
		}
		
		$saleEmail = COption::GetOptionString('sale', 'order_email', 'order@' . $_SERVER['SERVER_NAME']);
		
		$eventFields = array(
			'ORDER_ID' => $this->order['ACCOUNT_NUMBER'],
			'ORDER_DATE' => ConvertTimeStamp(),
			'ORDER_USER' => $this->order['PAYER_NAME'] ? $this->order['PAYER_NAME'] : $USER->GetFormattedName(false),
			'PRICE' => $this->order['TOTAL_PRICE_FORMATED'],
			'DELIVERY_PRICE' => $this->order['DELIVERY_PRICE_FORMATED'],
			'BCC' => $saleEmail,
			'EMAIL' => $this->order['USER_EMAIL'] ? $this->order['USER_EMAIL'] : $USER->GetEmail(),
			'ORDER_LIST' => implode('\n', $orderList),
			'SALE_EMAIL' => $saleEmail,
		);
		$eventName = 'SALE_NEW_ORDER';
		
		$eventSend = true;
		foreach (GetModuleEvents('sale', 'OnOrderNewSendEmail', true) as $event) {
			if (ExecuteModuleEventEx($event, array($this->order['ID'], &$eventName, &$eventFields)) === false) {
				$eventSend = false;
			}
		}
		
		if ($eventSend) {
			$event = new CEvent();
			$event->Send($eventName, SITE_ID, $eventFields, 'N');
		}
	}
	
	/**
	 * Возвращает признак, что пользователь выбрал другой профиль из списка
	 *
	 * @return boolean
	 */
	protected function isProfileChanged()
	{
		return $this->request->getPost('PROFILE_CHANGE') == 'Y';
	}
	
	/**
	 * Возвращает признак, что пользователь подверждает заказ
	 *
	 * @return boolean
	 */
	protected function isOrderConfirmed()
	{
		return $this->request->getPost('CONFIRM_ORDER') == 'Y';
	}
	
	/**
	 * Форматирует цену для визуального отображения
	 *
	 * @param float $price Цена
	 * @param string $currency Валюта
	 * @return string
	 */
	protected function formatCurrency($price, $currency = null)
	{
		return SaleFormatCurrency($price, $currency === null ? $this->config['CURRENCY'] : $currency);
	}
	
	/**
	 * Форматирует вес для визуального отображения
	 *
	 * @param float $weight Вес
	 * @return string
	 */
	protected function formatWeight($weight)
	{
		return sprintf(
			'%s %s',
			roundEx(floatval($weight / $this->config['WEIGHT_KOEF']), SALE_WEIGHT_PRECISION),
			$this->config['WEIGHT_UNIT']
		);
	}
	
	/**
	 * Заменят спец. символы HTML на сущности
	 *
	 * @param array $data Данные
	 * @return void
	 */
	protected function escape($data)
	{
		if (is_array($data) || is_object($data)) {
			foreach ($data as $key => $val) {
				if (is_array($val) || is_object($val)) {
					$val = $this->escape($val);
				} else {
					$data['~' . $key] = $val;
					$data[$key]= htmlspecialcharsEx($val);
				}
			}
		} else {
			$data = htmlspecialcharsEx($data);
		}
		
		return $data;
	}
}