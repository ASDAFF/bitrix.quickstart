<?php
use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\Config\Option,
	Bitrix\Main\Web\Json,
	Bitrix\Main\Localization\Loc,
	Bitrix\Sale,
	Bitrix\Sale\Order,
	Bitrix\Sale\PersonType,
	Bitrix\Sale\Shipment,
	Bitrix\Sale\PaySystem,
	Bitrix\Sale\Payment,
	Bitrix\Sale\Delivery,
	Bitrix\Sale\Location\LocationTable,
	Bitrix\Sale\Result,
	Bitrix\Sale\DiscountCouponsManager,
	Bitrix\Sale\Services\Company,
	Bitrix\Sale\Location\GeoIp;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var $APPLICATION CMain
 * @var $USER CUser
 */

Loc::loadMessages(__FILE__);

if (!Loader::includeModule("sale"))
{
	ShowError(Loc::getMessage("SOA_MODULE_NOT_INSTALL"));
	return;
}

class SaleOrderAjax extends \CBitrixComponent
{
	const AUTH_BLOCK = 'AUTH';
	const REGION_BLOCK = 'REGION';
	const PAY_SYSTEM_BLOCK = 'PAY_SYSTEM';
	const DELIVERY_BLOCK = 'DELIVERY';
	const PROPERTY_BLOCK = 'PROPERTY';

	/** @var Order $order */
	protected $order;
	/** @var Sale\Basket\Storage $basketStorage */
	protected $basketStorage;
	/** @var Sale\Basket */
	private $calculateBasket;

	protected $action;
	protected $arUserResult;
	protected $isOrderConfirmed;
	protected $arCustomSelectFields = array();
	protected $arElementId = array();
	protected $arSku2Parent = array();
	/** @var Delivery\Services\Base[] $arDeliveryServiceAll */
	protected $arDeliveryServiceAll = array();
	protected $arPaySystemServiceAll = array();
	protected $arActivePaySystems = array();
	protected $arIblockProps = array();
	/** @var  PaySystem\Service $prePaymentService */
	protected $prePaymentService;
	protected $useCatalog;
	/** @var Main\Context $context*/
	protected $context;
	protected $checkSession = true;
	protected $isRequestViaAjax;

	public function onPrepareComponentParams($arParams)
	{
		global $APPLICATION;

		if (isset($arParams['CUSTOM_SITE_ID']))
		{
			$this->setSiteId($arParams['CUSTOM_SITE_ID']);
		}

		$this->useCatalog = Loader::includeModule('catalog');

		if (!isset($arParams['COMPATIBLE_MODE']) && $this->initComponentTemplate())
		{
			$template = $this->getTemplate();

			if (
				$template instanceof CBitrixComponentTemplate
				&& $template->GetSiteTemplate() == ''
				&& $template->GetName() === '.default'
			)
			{
				$arParams['COMPATIBLE_MODE'] = 'N';
			}
			else
			{
				$arParams['COMPATIBLE_MODE'] = 'Y';
			}
		}
		else
		{
			$arParams['COMPATIBLE_MODE'] = $arParams['COMPATIBLE_MODE'] === 'N' ? 'N' : 'Y';
		}

		$arParams['USE_PRELOAD'] = $arParams['USE_PRELOAD'] === 'N' ? 'N' : 'Y';

		if ($arParams['SET_TITLE'] === 'Y')
		{
			$APPLICATION->SetTitle(Loc::getMessage('SOA_TITLE'));
		}

		$arParams['ACTION_VARIABLE'] = isset($arParams['ACTION_VARIABLE']) ? trim($arParams['ACTION_VARIABLE']) : '';
		if ($arParams['ACTION_VARIABLE'] == '')
		{
			$arParams['ACTION_VARIABLE'] = 'soa-action';
		}

		$arParams['PATH_TO_BASKET'] = isset($arParams['PATH_TO_BASKET']) ? trim($arParams['PATH_TO_BASKET']) : '';
		if ($arParams['PATH_TO_BASKET'] == '')
		{
			$arParams['PATH_TO_BASKET'] = '/personal/cart/';
		}

		$arParams['PATH_TO_PERSONAL'] = isset($arParams['PATH_TO_PERSONAL']) ? trim($arParams['PATH_TO_PERSONAL']) : '';
		if ($arParams['PATH_TO_PERSONAL'] == '')
		{
			$arParams['PATH_TO_PERSONAL'] = 'index.php';
		}

		$arParams['PATH_TO_PAYMENT'] = isset($arParams['PATH_TO_PAYMENT']) ? trim($arParams['PATH_TO_PAYMENT']) : '';
		if ($arParams['PATH_TO_PAYMENT'] == '')
		{
			$arParams['PATH_TO_PAYMENT'] = 'payment.php';
		}

		$arParams['PATH_TO_AUTH'] = isset($arParams['PATH_TO_AUTH']) ? trim($arParams['PATH_TO_AUTH']) : '';
		if ($arParams['PATH_TO_AUTH'] == '')
		{
			$arParams['PATH_TO_AUTH'] = '/auth/';
		}

		$arParams['PAY_FROM_ACCOUNT'] = $arParams['PAY_FROM_ACCOUNT'] === 'Y' ? 'Y' : 'N';
		$arParams['COUNT_DELIVERY_TAX'] = $arParams['COUNT_DELIVERY_TAX'] === 'Y' ? 'Y' : 'N';
		$arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] = $arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] === 'Y' ? 'Y' : 'N';
		$arParams['USE_PREPAYMENT'] = $arParams['USE_PREPAYMENT'] === 'Y' ? 'Y' : 'N';
		$arParams['DISPLAY_IMG_HEIGHT'] = intval($arParams['DISPLAY_IMG_HEIGHT']) <= 0 ? 90 : intval($arParams['DISPLAY_IMG_HEIGHT']);
		$arParams['SHOW_VAT_PRICE'] = $arParams['SHOW_VAT_PRICE'] === 'N' ? 'N' : 'Y';
		$arParams['DELIVERY_TO_PAYSYSTEM'] = $arParams['DELIVERY_TO_PAYSYSTEM'] === 'p2d' ? 'p2d' : 'd2p';

		if (!isset($arParams['DISABLE_BASKET_REDIRECT']) || $arParams['DISABLE_BASKET_REDIRECT'] !== 'Y')
		{
			$arParams['DISABLE_BASKET_REDIRECT'] = 'N';
		}

		$arParams['ALLOW_AUTO_REGISTER'] = $arParams['ALLOW_AUTO_REGISTER'] === 'Y' ? 'Y' : 'N';
		$arParams['CURRENT_PAGE'] = $APPLICATION->GetCurPage();

		$siteId = $this->getSiteId();

		$this->arResult = array(
			'PERSON_TYPE' => array(),
			'PAY_SYSTEM' => array(),
			'ORDER_PROP' => array(),
			'DELIVERY' => array(),
			'TAX' => array(),
			'ERROR' => array(),
			'ERROR_SORTED' => array(),
			'WARNING' => array(),
			'JS_DATA' => array(),
			'SHOW_EMPTY_BASKET' => false,
			'ORDER_PRICE' => 0,
			'ORDER_WEIGHT' => 0,
			'VATE_RATE' => 0,
			'VAT_SUM' => 0,
			'bUsingVat' => false,
			'BASKET_ITEMS' => array(),
			'BASE_LANG_CURRENCY' => Bitrix\Sale\Internals\SiteCurrencyTable::getSiteCurrency($siteId),
			'WEIGHT_UNIT' => htmlspecialcharsbx(Option::get('sale', 'weight_unit', false, $siteId)),
			'WEIGHT_KOEF' => htmlspecialcharsbx(Option::get('sale', 'weight_koef', 1, $siteId)),
			'TaxExempt' => array(),
			'DISCOUNT_PRICE' => 0,
			'DISCOUNT_PERCENT' => 0,
			'DELIVERY_PRICE' => 0,
			'TAX_PRICE' => 0,
			'PAYED_FROM_ACCOUNT_FORMATED' => false,
			'ORDER_TOTAL_PRICE_FORMATED' => false,
			'ORDER_WEIGHT_FORMATED' => false,
			'ORDER_PRICE_FORMATED' => false,
			'VAT_SUM_FORMATED' => false,
			'DELIVERY_SUM' => false,
			'DELIVERY_PROFILE_SUM' => false,
			'DELIVERY_PRICE_FORMATED' => false,
			'DISCOUNT_PERCENT_FORMATED' => false,
			'PAY_FROM_ACCOUNT' => false,
			'CURRENT_BUDGET_FORMATED' => false,
			'DISCOUNTS' => array(),
			'AUTH' => array(),
			'HAVE_PREPAYMENT' => false,
			'PREPAY_PS' => array(),
			'PREPAY_ADIT_FIELDS' => '',
			'PREPAY_ORDER_PROPS' => array(),
		);

		$this->arResult['AUTH']['new_user_registration_email_confirmation'] = Option::get('main', 'new_user_registration_email_confirmation', 'N', $siteId) === 'Y' ? 'Y' : 'N';
		$this->arResult['AUTH']['new_user_registration'] = Option::get('main', 'new_user_registration', 'Y') === 'Y' ? 'Y' : 'N';
		$this->arResult['AUTH']['new_user_email_required'] = Option::get('main', 'new_user_email_required', '') === 'Y' ? 'Y' : 'N';

		if (
			$arParams['ALLOW_AUTO_REGISTER'] === 'Y'
			&& (
				$this->arResult['AUTH']['new_user_registration_email_confirmation'] === 'Y'
				|| $this->arResult['AUTH']['new_user_registration'] === 'N'
			)
		)
		{
			$arParams['ALLOW_AUTO_REGISTER'] = 'N';
		}

		$arParams['ALLOW_APPEND_ORDER'] = $arParams['ALLOW_APPEND_ORDER'] === 'N' ? 'N' : 'Y';
		$arParams['SEND_NEW_USER_NOTIFY'] = $arParams['SEND_NEW_USER_NOTIFY'] === 'N' ? 'N' : 'Y';
		$arParams['ALLOW_NEW_PROFILE'] = $arParams['ALLOW_NEW_PROFILE'] === 'N' ? 'N' : 'Y';
		$arParams['DELIVERY_NO_SESSION'] = $arParams['DELIVERY_NO_SESSION'] === 'N' ? 'N' : 'Y';

		if (!isset($arParams['DELIVERY_NO_AJAX']) || !in_array($arParams['DELIVERY_NO_AJAX'], array('Y', 'N', 'H')))
		{
			$arParams['DELIVERY_NO_AJAX'] = 'N';
		}

		if (
			!isset($arParams['SHOW_NOT_CALCULATED_DELIVERIES'])
			|| !in_array($arParams['SHOW_NOT_CALCULATED_DELIVERIES'], array('N', 'L', 'Y'))
		)
		{
			$arParams['SHOW_NOT_CALCULATED_DELIVERIES'] = 'L';
		}

		if ($arParams['DELIVERY_NO_AJAX'] !== 'Y')
		{
			$arParams['SHOW_NOT_CALCULATED_DELIVERIES'] = 'Y';
		}

		//compatibility to old default columns in basket
		if (!empty($arParams['PRODUCT_COLUMNS_VISIBLE']))
		{
			$arParams['PRODUCT_COLUMNS'] = $arParams['PRODUCT_COLUMNS_VISIBLE'];
		}
		else
		{
			if (!isset($arParams['PRODUCT_COLUMNS_VISIBLE']) && !isset($arParams['PRODUCT_COLUMNS']))
			{
				$arParams['PRODUCT_COLUMNS'] = array('PREVIEW_PICTURE', 'PROPS');
			}
			elseif (!isset($arParams['PRODUCT_COLUMNS_VISIBLE']) && is_array($arParams['PRODUCT_COLUMNS']))
			{
				if (!empty($arParams['PRODUCT_COLUMNS']))
				{
					$arParams['PRODUCT_COLUMNS'] = array_merge($arParams['PRODUCT_COLUMNS'], array('PRICE_FORMATED'));
				}
				else
				{
					$arParams['PRODUCT_COLUMNS'] = array('PROPS', 'DISCOUNT_PRICE_PERCENT_FORMATED', 'PRICE_FORMATED');
				}
			}

			$arParams['PRODUCT_COLUMNS_VISIBLE'] = $arParams['PRODUCT_COLUMNS'];
		}

		$arDefaults = array('PROPS', 'DISCOUNT_PRICE_PERCENT_FORMATED', 'PRICE_FORMATED');
		$arDiff = array();
		if (!empty($arParams['PRODUCT_COLUMNS']) && is_array($arParams['PRODUCT_COLUMNS']))
		{
			$arDiff = array_diff($arParams['PRODUCT_COLUMNS'], $arDefaults);
		}

		$this->arResult['GRID']['DEFAULT_COLUMNS'] = count($arParams['PRODUCT_COLUMNS']) > 2 && empty($arDiff);

		if (empty($arParams['PRODUCT_COLUMNS']))
		{
			$arParams['PRODUCT_COLUMNS'] = array(
				'NAME' => Loc::getMessage('SOA_NAME_DEFAULT_COLUMN'),
				'QUANTITY' => Loc::getMessage('SOA_QUANTITY_DEFAULT_COLUMN'),
				'SUM' => Loc::getMessage('SOA_SUM_DEFAULT_COLUMN')
			);
		}
		else
		{
			// processing default or certain iblock fields if they are selected
			if (($key = array_search('PREVIEW_TEXT', $arParams['PRODUCT_COLUMNS'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS'][$key]);
				$arParams['PRODUCT_COLUMNS']['PREVIEW_TEXT'] = Loc::getMessage('SOA_NAME_COLUMN_PREVIEW_TEXT');
			}

			if (($key = array_search('PREVIEW_PICTURE', $arParams['PRODUCT_COLUMNS'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS'][$key]);
				$arParams['PRODUCT_COLUMNS']['PREVIEW_PICTURE'] = Loc::getMessage('SOA_NAME_COLUMN_PREVIEW_PICTURE');
			}

			if (($key = array_search('DETAIL_PICTURE', $arParams['PRODUCT_COLUMNS'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS'][$key]);
				$arParams['PRODUCT_COLUMNS']['DETAIL_PICTURE'] = Loc::getMessage('SOA_NAME_COLUMN_DETAIL_PICTURE');
			}

			if (($key = array_search('PROPS', $arParams['PRODUCT_COLUMNS'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS'][$key]);
				$arParams['PRODUCT_COLUMNS']['PROPS'] = Loc::getMessage('SOA_PROPS_DEFAULT_COLUMN');
			}

			if (($key = array_search('NOTES', $arParams['PRODUCT_COLUMNS'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS'][$key]);
				$arParams['PRODUCT_COLUMNS']['NOTES'] = Loc::getMessage('SOA_PRICE_TYPE_DEFAULT_COLUMN');
			}

			if (($key = array_search('DISCOUNT_PRICE_PERCENT_FORMATED', $arParams['PRODUCT_COLUMNS'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS'][$key]);
				$arParams['PRODUCT_COLUMNS']['DISCOUNT_PRICE_PERCENT_FORMATED'] = Loc::getMessage('SOA_DISCOUNT_DEFAULT_COLUMN');
			}

			if (($key = array_search('PRICE_FORMATED', $arParams['PRODUCT_COLUMNS'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS'][$key]);
				$arParams['PRODUCT_COLUMNS']['PRICE_FORMATED'] = Loc::getMessage('SOA_PRICE_DEFAULT_COLUMN');
			}

			if (($key = array_search('WEIGHT_FORMATED', $arParams['PRODUCT_COLUMNS'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS'][$key]);
				$arParams['PRODUCT_COLUMNS']['WEIGHT_FORMATED'] = Loc::getMessage('SOA_WEIGHT_DEFAULT_COLUMN');
			}
		}

		if (!empty($arParams['PRODUCT_COLUMNS_HIDDEN']))
		{
			// processing default or certain iblock fields if they are selected
			if (($key = array_search('PREVIEW_TEXT', $arParams['PRODUCT_COLUMNS_HIDDEN'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS_HIDDEN'][$key]);
				$arParams['PRODUCT_COLUMNS_HIDDEN']['PREVIEW_TEXT'] = Loc::getMessage('SOA_NAME_COLUMN_PREVIEW_TEXT');
			}

			if (($key = array_search('PREVIEW_PICTURE', $arParams['PRODUCT_COLUMNS_HIDDEN'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS_HIDDEN'][$key]);
				$arParams['PRODUCT_COLUMNS_HIDDEN']['PREVIEW_PICTURE'] = Loc::getMessage('SOA_NAME_COLUMN_PREVIEW_PICTURE');
			}

			if (($key = array_search('DETAIL_PICTURE', $arParams['PRODUCT_COLUMNS_HIDDEN'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS_HIDDEN'][$key]);
				$arParams['PRODUCT_COLUMNS_HIDDEN']['DETAIL_PICTURE'] = Loc::getMessage('SOA_NAME_COLUMN_DETAIL_PICTURE');
			}

			if (($key = array_search('PROPS', $arParams['PRODUCT_COLUMNS_HIDDEN'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS_HIDDEN'][$key]);
				$arParams['PRODUCT_COLUMNS_HIDDEN']['PROPS'] = Loc::getMessage('SOA_PROPS_DEFAULT_COLUMN');
			}

			if (($key = array_search('NOTES', $arParams['PRODUCT_COLUMNS_HIDDEN'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS_HIDDEN'][$key]);
				$arParams['PRODUCT_COLUMNS_HIDDEN']['NOTES'] = Loc::getMessage('SOA_PRICE_TYPE_DEFAULT_COLUMN');
			}

			if (($key = array_search('DISCOUNT_PRICE_PERCENT_FORMATED', $arParams['PRODUCT_COLUMNS_HIDDEN'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS_HIDDEN'][$key]);
				$arParams['PRODUCT_COLUMNS_HIDDEN']['DISCOUNT_PRICE_PERCENT_FORMATED'] = Loc::getMessage('SOA_DISCOUNT_DEFAULT_COLUMN');
			}

			if (($key = array_search('PRICE_FORMATED', $arParams['PRODUCT_COLUMNS_HIDDEN'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS_HIDDEN'][$key]);
				$arParams['PRODUCT_COLUMNS_HIDDEN']['PRICE_FORMATED'] = Loc::getMessage('SOA_PRICE_DEFAULT_COLUMN');
			}

			if (($key = array_search('WEIGHT_FORMATED', $arParams['PRODUCT_COLUMNS_HIDDEN'])) !== false)
			{
				unset($arParams['PRODUCT_COLUMNS_HIDDEN'][$key]);
				$arParams['PRODUCT_COLUMNS_HIDDEN']['WEIGHT_FORMATED'] = Loc::getMessage('SOA_WEIGHT_DEFAULT_COLUMN');
			}
		}

		// required grid columns
		if (empty($arParams['PRODUCT_COLUMNS']['NAME']))
		{
			$arParams['PRODUCT_COLUMNS'] = array('NAME' => Loc::getMessage('SOA_NAME_DEFAULT_COLUMN')) + $arParams['PRODUCT_COLUMNS'];
		}

		if (empty($arParams['PRODUCT_COLUMNS']['QUANTITY']))
		{
			$arParams['PRODUCT_COLUMNS']['QUANTITY'] = Loc::getMessage('SOA_QUANTITY_DEFAULT_COLUMN');
		}

		if (empty($arParams['PRODUCT_COLUMNS']['SUM']))
		{
			$arParams['PRODUCT_COLUMNS']['SUM'] = Loc::getMessage('SOA_SUM_DEFAULT_COLUMN');
		}

		foreach ($arParams as $k => $v)
		{
			if (strpos($k, 'ADDITIONAL_PICT_PROP_') !== false)
			{
				$iblockId = intval(substr($k, strlen('ADDITIONAL_PICT_PROP_')));

				if ($v !== '-')
				{
					$arParams['ADDITIONAL_PICT_PROP'][$iblockId] = $v;
				}

				unset($arParams[$k]);
			}
		}

		if (!isset($arParams['BASKET_IMAGES_SCALING']) || !in_array($arParams['BASKET_IMAGES_SCALING'], array('standard', 'adaptive', 'no_scale')))
		{
			$arParams['BASKET_IMAGES_SCALING'] = 'adaptive';
		}

		$arParams['USE_PHONE_NORMALIZATION'] = isset($arParams['USE_PHONE_NORMALIZATION']) && $arParams['USE_PHONE_NORMALIZATION'] === 'N' ? 'N' : 'Y';

		return $arParams;
	}

	/**
	 * Returns array of order properties from request
	 *
	 * @return array
	 */
	protected function getPropertyValuesFromRequest()
	{
		$orderProperties = array();

		foreach ($this->request as $k => $v)
		{
			if (strpos($k, "ORDER_PROP_") !== false)
			{
				if (strpos($k, "[]") !== false)
					$orderPropId = intval(substr($k, strlen("ORDER_PROP_"), strlen($k) - 2));
				else
					$orderPropId = intval(substr($k, strlen("ORDER_PROP_")));

				if ($orderPropId > 0)
					$orderProperties[$orderPropId] = $v;
			}
		}

		foreach ($this->request->getFileList() as $k => $arFileData)
		{
			if (strpos($k, "ORDER_PROP_") !== false)
			{
				$orderPropId = intval(substr($k, strlen("ORDER_PROP_")));

				if (is_array($arFileData))
				{
					foreach ($arFileData as $param_name => $value)
					{
						if (is_array($value))
						{
							foreach ($value as $nIndex => $val)
							{
								if (strlen($arFileData["name"][$nIndex]) > 0)
									$orderProperties[$orderPropId][$nIndex][$param_name] = $val;
							}
						}
						else
							$orderProperties[$orderPropId][$param_name] = $value;
					}
				}
			}
		}

		return $orderProperties;
	}

	protected function getBasketStorage()
	{
		if (!isset($this->basketStorage))
		{
			$this->basketStorage = Sale\Basket\Storage::getInstance(Sale\Fuser::getId(), Main\Context::getCurrent()->getSite());
		}

		return $this->basketStorage;
	}

	protected function getLastOrderData(Order $order)
	{
		$lastOrderData = array();

		$registry = Sale\Registry::getInstance(Sale\Registry::REGISTRY_TYPE_ORDER);
		/** @var Order $orderClassName */
		$orderClassName = $registry->getOrderClassName();

		$filter = array(
			'filter' => array(
				'USER_ID' => $order->getUserId(),
				'LID' => $order->getSiteId()
			),
			'select' => array('ID'),
			'order' => array('ID' => 'DESC'),
			'limit' => 1
		);

		if ($arOrder = $orderClassName::getList($filter)->fetch())
		{
			/** @var Order $lastOrder */
			$lastOrder = $orderClassName::load($arOrder['ID']);
			$lastOrderData['PERSON_TYPE_ID'] = $lastOrder->getPersonTypeId();

			if ($payment = $this->getInnerPayment($lastOrder))
				$lastOrderData['PAY_CURRENT_ACCOUNT'] = 'Y';

			if ($payment = $this->getExternalPayment($lastOrder))
				$lastOrderData['PAY_SYSTEM_ID'] = $payment->getPaymentSystemId();

			if ($shipment = $this->getCurrentShipment($lastOrder))
			{
				$lastOrderData['DELIVERY_ID'] = $shipment->getDeliveryId();
				$lastOrderData['BUYER_STORE'] = $shipment->getStoreId();
				$lastOrderData['DELIVERY_EXTRA_SERVICES'][$shipment->getDeliveryId()] = $shipment->getExtraServices();
				if ($storeFields = Delivery\ExtraServices\Manager::getStoresFields($lastOrderData['DELIVERY_ID'], false))
					unset($lastOrderData['DELIVERY_EXTRA_SERVICES'][$shipment->getDeliveryId()][$storeFields['ID']]);
			}
		}

		return $lastOrderData;
	}

	protected function initLastOrderData(Order $order)
	{
		global $USER;

		if (
			($this->request->getRequestMethod() === 'GET' || $this->request->get('do_authorize') === 'Y' || $this->request->get('do_register') === 'Y')
			&& $this->arUserResult['USE_PRELOAD']
			&& $USER->IsAuthorized()
		)
		{
			$showData = array();
			$lastOrderData = $this->getLastOrderData($order);

			if (!empty($lastOrderData))
			{
				if (!empty($lastOrderData['PERSON_TYPE_ID']))
					$this->arUserResult['PERSON_TYPE_ID'] = $showData['PERSON_TYPE_ID'] = $lastOrderData['PERSON_TYPE_ID'];

				if (!empty($lastOrderData['PAY_CURRENT_ACCOUNT']))
					$this->arUserResult['PAY_CURRENT_ACCOUNT'] = $showData['PAY_CURRENT_ACCOUNT'] = $lastOrderData['PAY_CURRENT_ACCOUNT'];

				if (!empty($lastOrderData['PAY_SYSTEM_ID']))
					$this->arUserResult['PAY_SYSTEM_ID'] = $showData['PAY_SYSTEM_ID'] = $lastOrderData['PAY_SYSTEM_ID'];

				if (!empty($lastOrderData['DELIVERY_ID']))
					$this->arUserResult['DELIVERY_ID'] = $showData['DELIVERY_ID'] = $lastOrderData['DELIVERY_ID'];

				if (!empty($lastOrderData['DELIVERY_EXTRA_SERVICES']))
					$this->arUserResult['DELIVERY_EXTRA_SERVICES'] = $showData['DELIVERY_EXTRA_SERVICES'] = $lastOrderData['DELIVERY_EXTRA_SERVICES'];

				if (!empty($lastOrderData['BUYER_STORE']))
					$this->arUserResult['BUYER_STORE'] = $showData['BUYER_STORE'] = $lastOrderData['BUYER_STORE'];

				$this->arUserResult['LAST_ORDER_DATA'] = $showData;
			}
		}
	}

	/**
	 * Initialization of order properties from request, user profile, default values.
	 * Checking properties (if saving order) and setting to order.
	 * Execution of 'OnSaleComponentOrderProperties' event.
	 *
	 * @param Order $order
	 * @param       $isPersonTypeChanged
	 * @throws Main\ArgumentException
	 */
	protected function initProperties(Order $order, $isPersonTypeChanged)
	{
		$arResult =& $this->arResult;
		$orderProperties = $this->getPropertyValuesFromRequest();

		$this->initUserProfiles($order, $isPersonTypeChanged);

		$firstLoad = $this->request->getRequestMethod() === 'GET';
		$isProfileChanged = $this->arUserResult['PROFILE_CHANGE'] === 'Y';

		$loadFromProfile = $firstLoad || $isProfileChanged || $isPersonTypeChanged;
		$justAuthorized = $this->request->get('do_authorize') === 'Y' || $this->request->get('do_register') === 'Y';
		$haveProfileId = intval($this->arUserResult['PROFILE_ID']) > 0;
		$useProfileProperties = ($loadFromProfile || $justAuthorized) && $haveProfileId;

		$profileProperties = array();

		if ($haveProfileId)
		{
			$profileProperties = Sale\OrderUserProperties::getProfileValues((int)$this->arUserResult['PROFILE_ID']);
		}

		$ipAddress = '';

		if($this->arParams['SPOT_LOCATION_BY_GEOIP'] === 'Y')
			$ipAddress = \Bitrix\Main\Service\GeoIp\Manager::getRealIp();

		$propertyCollection = $order->getPropertyCollection();
		/** @var Sale\PropertyValue $property */
		foreach ($propertyCollection as $property)
		{
			if ($property->isUtil())
				continue;

			$arProperty = $property->getProperty();

			if ($arProperty['USER_PROPS'] === 'Y')
			{
				if ($isProfileChanged && !$haveProfileId)
				{
					$curVal = '';
				}
				elseif (
					$useProfileProperties
					|| (!isset($orderProperties[$arProperty['ID']]) && isset($profileProperties[$arProperty['ID']]))
				)
				{
					$curVal = $profileProperties[$arProperty['ID']];
				}
				elseif (isset($orderProperties[$arProperty['ID']]))
				{
					$curVal = $orderProperties[$arProperty['ID']];
				}
				else
				{
					$curVal = '';
				}
			}
			else
			{
				$curVal = $orderProperties[$arProperty['ID']];
			}

			if ($arResult['HAVE_PREPAYMENT'] && !empty($arResult['PREPAY_ORDER_PROPS'][$arProperty['CODE']]))
			{
				if ($arProperty['TYPE'] === 'LOCATION')
				{
					$cityName = ToUpper($arResult['PREPAY_ORDER_PROPS'][$arProperty['CODE']]);
					$arLocation = LocationTable::getList(array(
						'select' => array('CODE'),
						'filter' => array('NAME.NAME_UPPER' => $cityName),
					))->fetch();
					if (!empty($arLocation))
					{
						$curVal = $arLocation['CODE'];
					}
				}
				else
				{
					$curVal = $arResult['PREPAY_ORDER_PROPS'][$arProperty['CODE']];
				}
			}

			if ($arProperty['TYPE'] === 'LOCATION' && empty($curVal) && !empty($ipAddress))
			{
				$locCode = GeoIp::getLocationCode($ipAddress, LANGUAGE_ID);

				if (!empty($locCode))
				{
					$curVal = $locCode;
				}
			}
			elseif ($arProperty['IS_ZIP'] === 'Y' && empty($curVal) && !empty($ipAddress))
			{
				$zip = GeoIp::getZipCode($ipAddress);

				if (!empty($zip))
				{
					$curVal = $zip;
				}
			}
			elseif ($arProperty['IS_PHONE'] === 'Y' && !empty($curVal))
			{
				$curVal = $this->getNormalizedPhone($curVal);
			}

			if (empty($curVal))
			{
				// getting default value for all properties except LOCATION (LOCATION - only for first load or person type change)
				if ($arProperty['TYPE'] !== 'LOCATION' || $firstLoad || $isPersonTypeChanged)
				{
					if ($loadFromProfile || $justAuthorized)
					{
						$curVal = $this->getValueFromCUser($arProperty);
					}

					if (empty($curVal) && !empty($arProperty['DEFAULT_VALUE']))
					{
						$curVal = $arProperty['DEFAULT_VALUE'];
					}
				}
			}

			if ($arProperty['TYPE'] === 'LOCATION')
			{
				if ((!$loadFromProfile || $this->request->get('PROFILE_ID') === '0')
					&& $this->request->get('location_type') != 'code'
				)
				{
					$curVal = CSaleLocation::getLocationCODEbyID($curVal);
				}
			}

			$this->arUserResult['ORDER_PROP'][$arProperty['ID']] = $curVal;
		}

		$this->checkZipProperty($order, $useProfileProperties);
		$this->checkAltLocationProperty($order, $useProfileProperties, $profileProperties);

		foreach (GetModuleEvents('sale', 'OnSaleComponentOrderProperties', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$this->arUserResult, $this->request, &$this->arParams, &$this->arResult));

		if ($this->isOrderConfirmed)
		{
			$res = $propertyCollection->checkErrors(array('PROPERTIES' => $this->arUserResult['ORDER_PROP']), array(), true);
			if (!$res->isSuccess())
			{
				$this->addError($res, self::PROPERTY_BLOCK);
			}
		}

		$res = $propertyCollection->setValuesFromPost(array('PROPERTIES' => $this->arUserResult['ORDER_PROP']), array());
		if ($this->isOrderConfirmed && !$res->isSuccess())
		{
			$this->addError($res, self::PROPERTY_BLOCK);
		}
	}

	/**
	 * Returns user property value from CUser
	 *
	 * @param	$property
	 * @return	string
	 */
	protected function getValueFromCUser($property)
	{
		global $USER;

		$value = '';

		if ($property['IS_EMAIL'] === 'Y')
		{
			$value = $USER->GetEmail();
		}
		elseif ($property['IS_PAYER'] === 'Y')
		{
			$rsUser = CUser::GetByID($USER->GetID());
			if ($arUser = $rsUser->Fetch())
			{
				$value = CUser::FormatName(
					CSite::GetNameFormat(false),
					array(
						'NAME' => $arUser['NAME'],
						'LAST_NAME' => $arUser['LAST_NAME'],
						'SECOND_NAME' => $arUser['SECOND_NAME']
					),
					false,
					false
				);
			}
		}
		elseif ($property['IS_PHONE'] === 'Y')
		{
			$rsUser = CUser::GetByID($USER->GetID());
			if ($arUser = $rsUser->Fetch())
			{
				if (!empty($arUser['PERSONAL_PHONE']))
				{
					$value = $arUser['PERSONAL_PHONE'];
				}
				elseif (!empty($arUser['PERSONAL_MOBILE']))
				{
					$value = $arUser['PERSONAL_MOBILE'];
				}
			}
		}
		elseif ($property['IS_ADDRESS'] === 'Y')
		{
			$rsUser = CUser::GetByID($USER->GetID());
			if ($arUser = $rsUser->Fetch())
			{
				if (!empty($arUser['PERSONAL_STREET']))
				{
					$value = $arUser['PERSONAL_STREET'];
				}
			}
		}

		return $value;
	}

	/**
	 * Defines zip value if location was changed.
	 *
	 * @param Order $order
	 * @param       $loadFromProfile
	 */
	protected function checkZipProperty(Order $order, $loadFromProfile)
	{
		$propertyCollection = $order->getPropertyCollection();
		$zip = $propertyCollection->getDeliveryLocationZip();
		$location = $propertyCollection->getDeliveryLocation();
		if (!empty($zip) && !empty($location))
		{
			$locId = $location->getField('ORDER_PROPS_ID');
			$locValue = $this->arUserResult['ORDER_PROP'][$locId];
			// $locationChanged = $locValue != $this->request->get('RECENT_DELIVERY_VALUE');

			// need to override flag for zip data from profile
			if ($loadFromProfile)
			{
				$this->arUserResult['ZIP_PROPERTY_CHANGED'] = 'Y';
			}

			// don't autoload zip property if user manually changed it
			if ($this->arUserResult['ZIP_PROPERTY_CHANGED'] !== 'Y')
			{
				$res = Sale\Location\Admin\LocationHelper::getZipByLocation($locValue);

				if ($arZip = $res->fetch())
				{
					if (!empty($arZip['XML_ID']))
					{
						$this->arUserResult['ORDER_PROP'][$zip->getField('ORDER_PROPS_ID')] = $arZip['XML_ID'];
					}
				}
			}
		}
	}

	/**
	 * Checks order properties for proper alternate location property display.
	 *
	 * @param Order $order
	 * @param       $useProfileProperties
	 * @param array $profileProperties
	 */
	protected function checkAltLocationProperty(Order $order, $useProfileProperties, array $profileProperties)
	{
		$locationAltPropDisplayManual = $this->request->get('LOCATION_ALT_PROP_DISPLAY_MANUAL');
		$propertyCollection = $order->getPropertyCollection();
		/** @var Sale\PropertyValue $property */
		foreach ($propertyCollection as $property)
		{
			if ($property->isUtil())
				continue;

			if ($property->getType() == 'LOCATION')
			{
				$propertyFields = $property->getProperty();
				if ((int)$propertyFields['INPUT_FIELD_LOCATION'] > 0)
				{
					if ($useProfileProperties)
					{
						$deleteAltProp = empty($profileProperties[$propertyFields['INPUT_FIELD_LOCATION']]);
					}
					else
					{
						$deleteAltProp = !isset($locationAltPropDisplayManual[$propertyFields['ID']])
							|| !(bool)$locationAltPropDisplayManual[$propertyFields['ID']];

						// check if have no city at all then show alternate property
						if (
							isset($locationAltPropDisplayManual[$propertyFields['ID']])
							&& !$this->haveCitiesInTree($this->arUserResult['ORDER_PROP'][$property->getPropertyId()])
						)
						{
							$deleteAltProp = false;
						}
					}

					if ($deleteAltProp)
					{
						unset($this->arUserResult['ORDER_PROP'][$propertyFields['INPUT_FIELD_LOCATION']]);
					}
				}
			}
		}
	}

	protected function haveCitiesInTree($locationCode)
	{
		if (empty($locationCode))
			return false;

		$haveCities = false;
		$location = LocationTable::getRow(array('filter' => array('=CODE' => $locationCode)));

		if (!empty($location))
		{
			if ($location['TYPE_ID'] >= 5)
			{
				$haveCities = true;
			}
			else
			{
				$parameters = array(
					'filter' => array(
						'>=LEFT_MARGIN' => (int)$location['LEFT_MARGIN'],
						'<=RIGHT_MARGIN' => (int)$location['RIGHT_MARGIN'],
						'>=DEPTH_LEVEL' => (int)$location['DEPTH_LEVEL'],
						'!CITY_ID' => null,
					),
					'count_total' => true
				);
				$haveCities = LocationTable::getList($parameters)->getCount() > 0;
			}
		}

		return $haveCities;
	}

	/**
	 * Returns basket quantity list for orderable items
	 *
	 * @param Sale\BasketBase $basket
	 * @return array
	 */
	protected function getActualQuantityList(Sale\BasketBase $basket)
	{
		$quantityList = array();

		if (!$basket->isEmpty())
		{
			/** @var Sale\BasketItemBase $basketItem */
			foreach ($basket as $basketItem)
			{
				if ($basketItem->canBuy() && !$basketItem->isDelay())
				{
					$quantityList[$basketItem->getBasketCode()] = $basketItem->getQuantity();
				}
			}
		}

		return $quantityList;
	}

	/**
	 * Append basket(for current FUser) to order object
	 *
	 * @param Order $order
	 * @throws Main\ObjectNotFoundException
	 */
	protected function initBasket(Order $order)
	{
		$basketStorage = $this->getBasketStorage();
		$basket = $basketStorage->getBasket();

		$this->arUserResult['QUANTITY_LIST'] = $this->getActualQuantityList($basket);

		$result = $basket->refresh();
		if ($result->isSuccess())
		{
			$basket->save();
		}

		// right NOW we decide to work only with available basket
		// full basket won't update anymore
		$availableBasket = $basketStorage->getOrderableBasket();
		if ($availableBasket->isEmpty())
		{
			$this->showEmptyBasket();
		}

		$order->appendBasket($availableBasket);
	}

	protected function showEmptyBasket()
	{
		global $APPLICATION;

		if ($this->action === 'saveOrderAjax')
		{
			$APPLICATION->RestartBuffer();
			echo json_encode(array(
				'order' => array(
					'REDIRECT_URL' => $this->arParams['~CURRENT_PAGE']
				)
			));
			die();
		}

		if ($this->arParams['DISABLE_BASKET_REDIRECT'] === 'Y')
		{
			$this->arResult['SHOW_EMPTY_BASKET'] = true;

			if ($this->request->get('json') === 'Y' || $this->isRequestViaAjax)
			{
				$APPLICATION->RestartBuffer();
				echo json_encode(array(
					'success' => 'N',
					'redirect' => $this->arParams['~CURRENT_PAGE']
				));
				die();
			}
		}
		else
		{
			if ($this->request->get('json') === 'Y' || $this->isRequestViaAjax)
			{
				$APPLICATION->RestartBuffer();
				echo json_encode(array(
					'success' => 'N',
					'redirect' => $this->arParams['PATH_TO_BASKET']
				));
				die();
			}

			LocalRedirect($this->arParams['PATH_TO_BASKET']);
			die();
		}
	}

	protected function addError($res, $type = 'MAIN')
	{
		if ($res instanceof Result)
		{
			foreach ($res->getErrorMessages() as $error)
			{
				$this->arResult['ERROR'][] = $error;

				if (
					empty($this->arResult['ERROR_SORTED'][$type])
					|| (!empty($this->arResult['ERROR_SORTED'][$type]) && !in_array($error, $this->arResult['ERROR_SORTED'][$type]))
				)
				{
					$this->arResult['ERROR_SORTED'][$type][] = $error;
				}
			}
		}
		else
		{
			$this->arResult['ERROR'][] = $res;

			if (
				empty($this->arResult['ERROR_SORTED'][$type])
				|| (!empty($this->arResult['ERROR_SORTED'][$type]) && !in_array($res, $this->arResult['ERROR_SORTED'][$type]))
			)
			{
				$this->arResult['ERROR_SORTED'][$type][] = $res;
			}
		}
	}

	protected function addWarning($res, $type)
	{
		if (
			!empty($type)
			&& (
				empty($this->arResult['WARNING'][$type])
				|| (!empty($this->arResult['WARNING'][$type]) && !in_array($res, $this->arResult['WARNING'][$type]))
			)
		)
		{
			$this->arResult['WARNING'][$type][] = $res;
		}
	}

	/**
	 * Action - show and process authorization form
	 *
	 * @throws Main\ArgumentNullException
	 */
	protected function showAuthFormAction()
	{
		global $APPLICATION;
		$arResult =& $this->arResult;

		$request = $this->isRequestViaAjax && $this->request->get('save') != 'Y' ? $this->request->get('order') : $this->request;

		$this->checkSocServicesAuthForm();

		$arResult['AUTH']['USER_LOGIN'] = strlen($request['USER_LOGIN']) > 0
			? htmlspecialcharsbx($request['USER_LOGIN'])
			: htmlspecialcharsbx(${Option::get('main', 'cookie_name', 'BITRIX_SM').'_LOGIN'});
		$arResult['AUTH']['captcha_registration'] = Option::get('main', 'captcha_registration', 'N') === 'Y' ? 'Y' : 'N';

		if ($arResult['AUTH']['captcha_registration'] === 'Y')
		{
			$arResult['AUTH']['capCode'] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
		}

		$arResult['POST'] = array();

		if ($this->request->isPost() && $this->checkSession)
		{
			foreach ($request as $name => $value)
			{
				if (in_array(
					$name,
					array(
						'USER_LOGIN', 'USER_PASSWORD', 'do_authorize', 'NEW_NAME', 'NEW_LAST_NAME', 'NEW_EMAIL',
						'NEW_GENERATE', 'NEW_LOGIN', 'NEW_PASSWORD', 'NEW_PASSWORD_CONFIRM', 'captcha_sid',
						'captcha_word', 'do_register', 'is_ajax_post'
					)
				))
					continue;

				if (is_array($value))
				{
					foreach ($value as $k => $v)
					{
						$arResult['POST'][htmlspecialcharsbx($name.'['.$k.']')] = htmlspecialcharsbx($v);
					}
				}
				else
				{
					$arResult['POST'][htmlspecialcharsbx($name)] = htmlspecialcharsbx($value);
				}
			}

			if ($request['do_authorize'] === 'Y')
			{
				$this->doAuthorize();
			}
			elseif ($request['do_register'] === 'Y' && $arResult['AUTH']['new_user_registration'] === 'Y')
			{
				$this->doRegister();
			}
			elseif ($this->isRequestViaAjax)
			{
				$this->showAjaxAnswer(array(
					'order' => array(
						'SHOW_AUTH' => true,
						'AUTH' => $arResult['AUTH']
					)
				));
			}
		}

		if ($this->isRequestViaAjax)
		{
			if (empty($arResult['ERROR']))
			{
				$this->refreshOrderAjaxAction();
			}
			else
			{
				$this->showAjaxAnswer(array(
					'order' => array(
						'SHOW_AUTH' => true,
						'AUTH' => $arResult['AUTH'],
						'ERROR' => $arResult['ERROR_SORTED']
					)
				));
			}
		}
		else
		{
			$this->processOrderAction();
		}
	}

	protected function checkSocServicesAuthForm()
	{
		global $APPLICATION;
		$arResult =& $this->arResult;

		$arResult["ALLOW_SOCSERV_AUTHORIZATION"] = Option::get("main", "allow_socserv_authorization", "Y") == "Y" ? "Y" : "N";
		$arResult["AUTH_SERVICES"] = false;
		$arResult["CURRENT_SERVICE"] = false;
		$arResult["FOR_INTRANET"] = false;

		if (Bitrix\Main\ModuleManager::isModuleInstalled("intranet") || Bitrix\Main\ModuleManager::isModuleInstalled("rest"))
			$arResult["FOR_INTRANET"] = true;

		if (Loader::includeModule("socialservices") && $arResult["ALLOW_SOCSERV_AUTHORIZATION"] == 'Y')
		{
			$oAuthManager = new CSocServAuthManager();
			$arServices = $oAuthManager->GetActiveAuthServices(array(
				'BACKURL' => $this->arParams['~CURRENT_PAGE'],
				'FOR_INTRANET' => $arResult['FOR_INTRANET'],
			));

			if (!empty($arServices))
			{
				$arResult["AUTH_SERVICES"] = $arServices;
				if (isset($this->request["auth_service_id"])
					&& $this->request["auth_service_id"] != ''
					&& isset($arResult["AUTH_SERVICES"][$this->request["auth_service_id"]])
				)
				{
					$arResult["CURRENT_SERVICE"] = $this->request["auth_service_id"];
					if (isset($this->request["auth_service_error"]) && $this->request["auth_service_error"] <> '')
					{
						$this->addError($oAuthManager->GetError($arResult["CURRENT_SERVICE"], $this->request["auth_service_error"]), self::AUTH_BLOCK);
					}
					elseif (!$oAuthManager->Authorize($this->request["auth_service_id"]))
					{
						$ex = $APPLICATION->GetException();
						if ($ex)
							$this->addError($ex->GetString(), self::AUTH_BLOCK);
					}
				}
			}
		}
	}

	protected function doAuthorize()
	{
		global $USER;
		$request = $this->isRequestViaAjax && $this->request->get('save') != 'Y' ? $this->request->get('order') : $this->request;

		if (strlen($request["USER_LOGIN"]) <= 0)
			$this->addError(Loc::getMessage("STOF_ERROR_AUTH_LOGIN"), self::AUTH_BLOCK);

		if (empty($this->arResult["ERROR"]))
		{
			$rememberMe = $request["USER_REMEMBER"] == 'Y' ? 'Y' : 'N';
			$arAuthResult = $USER->Login($request["USER_LOGIN"], $request["USER_PASSWORD"], $rememberMe);
			if ($arAuthResult != false && $arAuthResult["TYPE"] == "ERROR")
				$this->addError(Loc::getMessage("STOF_ERROR_AUTH").(strlen($arAuthResult["MESSAGE"]) > 0 ? ": ".$arAuthResult["MESSAGE"] : "" ), self::AUTH_BLOCK);
		}
	}

	protected function doRegister()
	{
		global $APPLICATION, $USER;
		$arResult =& $this->arResult;
		$request = $this->isRequestViaAjax && $this->request->get('save') != 'Y' ? $this->request->get('order') : $this->request;

		if (strlen($request['NEW_NAME']) <= 0)
		{
			$this->addError(Loc::getMessage('STOF_ERROR_REG_NAME'), self::AUTH_BLOCK);
		}

		if (strlen($request['NEW_LAST_NAME']) <= 0)
		{
			$this->addError(Loc::getMessage('STOF_ERROR_REG_LASTNAME'), self::AUTH_BLOCK);
		}

		if (Option::get('main', 'new_user_email_required', '') === 'Y')
		{
			if (strlen($request['NEW_EMAIL']) <= 0)
			{
				$this->addError(Loc::getMessage('STOF_ERROR_REG_EMAIL'), self::AUTH_BLOCK);
			}
			elseif (!check_email($request['NEW_EMAIL']))
			{
				$this->addError(Loc::getMessage('STOF_ERROR_REG_BAD_EMAIL'), self::AUTH_BLOCK);
			}
		}

		$arResult['AUTH']['NEW_EMAIL'] = $request['NEW_EMAIL'];

		if (empty($arResult['ERROR']))
		{
			if ($request['NEW_GENERATE'] === 'Y')
			{
				$generatedData = $this->generateUserData(array('EMAIL' => $request['NEW_EMAIL']));
				$arResult['AUTH'] = array_merge($arResult['AUTH'], $generatedData);
			}
			else
			{
				if (strlen($request['NEW_LOGIN']) <= 0)
				{
					$this->addError(Loc::getMessage('STOF_ERROR_REG_FLAG'), self::AUTH_BLOCK);
				}

				if (strlen($request['NEW_PASSWORD']) <= 0)
				{
					$this->addError(Loc::getMessage('STOF_ERROR_REG_FLAG1'), self::AUTH_BLOCK);
				}

				if (strlen($request['NEW_PASSWORD']) > 0 && strlen($request['NEW_PASSWORD_CONFIRM']) <= 0)
				{
					$this->addError(Loc::getMessage('STOF_ERROR_REG_FLAG1'), self::AUTH_BLOCK);
				}

				if (
					strlen($request['NEW_PASSWORD']) > 0
					&& strlen($request['NEW_PASSWORD_CONFIRM']) > 0
					&& $request['NEW_PASSWORD'] != $request['NEW_PASSWORD_CONFIRM']
				)
				{
					$this->addError(Loc::getMessage('STOF_ERROR_REG_PASS'), self::AUTH_BLOCK);
				}

				$arResult['AUTH']['NEW_LOGIN'] = $request['NEW_LOGIN'];
				$arResult['AUTH']['NEW_NAME'] = $request['NEW_NAME'];
				$arResult['AUTH']['NEW_PASSWORD'] = $request['NEW_PASSWORD'];
				$arResult['AUTH']['NEW_PASSWORD_CONFIRM'] = $request['NEW_PASSWORD_CONFIRM'];
			}
		}

		if (empty($arResult['ERROR']))
		{
			$arAuthResult = $USER->Register(
				$arResult['AUTH']['NEW_LOGIN'],
				$request['NEW_NAME'],
				$request['NEW_LAST_NAME'],
				$arResult['AUTH']['NEW_PASSWORD'],
				$arResult['AUTH']['NEW_PASSWORD_CONFIRM'],
				$arResult['AUTH']['NEW_EMAIL'],
				LANG,
				$request['captcha_word'],
				$request['captcha_sid']
			);

			if ($arAuthResult != false && $arAuthResult['TYPE'] === 'ERROR')
			{
				$this->addError(Loc::getMessage('STOF_ERROR_REG').(strlen($arAuthResult['MESSAGE']) > 0 ? ': '.$arAuthResult['MESSAGE'] : '' ), self::AUTH_BLOCK);
			}
			else
			{
				if ($USER->IsAuthorized())
				{
					if ($this->arParams['SEND_NEW_USER_NOTIFY'] === 'Y')
					{
						CUser::SendUserInfo($USER->GetID(), $this->getSiteId(), Loc::getMessage('INFO_REQ'), true);
					}

					if ($this->isRequestViaAjax)
					{
						$this->refreshOrderAjaxAction();
					}
					else
					{
						LocalRedirect($APPLICATION->GetCurPageParam());
					}
				}
				else
				{
					$arResult['OK_MESSAGE'][] = Loc::getMessage('STOF_ERROR_REG_CONFIRM');
				}
			}
		}

		$arResult['AUTH']['~NEW_LOGIN'] = $arResult['AUTH']['NEW_LOGIN'];
		$arResult['AUTH']['NEW_LOGIN'] = htmlspecialcharsEx($arResult['AUTH']['NEW_LOGIN']);
		$arResult['AUTH']['~NEW_NAME'] = $request['NEW_NAME'];
		$arResult['AUTH']['NEW_NAME'] = htmlspecialcharsEx($request['NEW_NAME']);
		$arResult['AUTH']['~NEW_LAST_NAME'] = $request['NEW_LAST_NAME'];
		$arResult['AUTH']['NEW_LAST_NAME'] = htmlspecialcharsEx($request['NEW_LAST_NAME']);
		$arResult['AUTH']['~NEW_EMAIL'] = $arResult['AUTH']['NEW_EMAIL'];
		$arResult['AUTH']['NEW_EMAIL'] = htmlspecialcharsEx($arResult['AUTH']['NEW_EMAIL']);
	}

	protected function initStatGid()
	{
		if (Loader::includeModule("statistic"))
			$this->order->setField('STAT_GID', CStatistic::GetEventParam());
	}

	protected function initAffiliate()
	{
		$affiliateID = CSaleAffiliate::GetAffiliate();
		if ($affiliateID > 0)
		{
			$dbAffiliate = CSaleAffiliate::GetList(array(), array("SITE_ID" => $this->getSiteId(), "ID" => $affiliateID));
			$arAffiliates = $dbAffiliate->Fetch();
			if (count($arAffiliates) > 1)
				$this->order->setField('AFFILIATE_ID', $affiliateID);
		}
	}

	/**
	 * Generation of user registration fields (login, password, etc)
	 *
	 * @param array $userProps
	 * @return array
	 * @throws Main\ArgumentNullException
	 */
	public function generateUserData($userProps = array())
	{
		global $USER;

		$userEmail = isset($userProps['EMAIL']) ? trim((string)$userProps['EMAIL']) : '';
		$newLogin = $userEmail;

		if (empty($userEmail))
		{
			$newEmail = false;
			$normalizedPhone = $this->getNormalizedPhone($userProps['PHONE']);

			if (!empty($normalizedPhone))
			{
				$newLogin = $normalizedPhone;
			}
		}
		else
		{
			$newEmail = $userEmail;
		}

		if (empty($newLogin))
		{
			$newLogin = randString(5).mt_rand(0, 99999);
		}

		$pos = strpos($newLogin, '@');
		if ($pos !== false)
		{
			$newLogin = substr($newLogin, 0, $pos);
		}

		if (strlen($newLogin) > 47)
		{
			$newLogin = substr($newLogin, 0, 47);
		}

		$newLogin = str_pad($newLogin, 3, '_');

		$dbUserLogin = CUser::GetByLogin($newLogin);
		if ($userLoginResult = $dbUserLogin->Fetch())
		{
			do
			{
				$newLoginTmp = $newLogin.mt_rand(0, 99999);
				$dbUserLogin = CUser::GetByLogin($newLoginTmp);
			}
			while ($userLoginResult = $dbUserLogin->Fetch());

			$newLogin = $newLoginTmp;
		}

		$newName = '';
		$newLastName = '';
		$payerName = isset($userProps['PAYER']) ? trim((string)$userProps['PAYER']) : '';

		if (!empty($payerName))
		{
			$arNames = explode(' ', $payerName);
			$newName = $arNames[1];
			$newLastName = $arNames[0];
		}

		$defGroup = Option::get('main', 'new_user_registration_def_group', '');
		if (!empty($defGroup))
		{
			$groupId = explode(',', $defGroup);
			$arPolicy = $USER->GetGroupPolicy($groupId);
		}
		else
		{
			$groupId = false;
			$arPolicy = $USER->GetGroupPolicy(array());
		}

		$passwordMinLength = (int)$arPolicy['PASSWORD_LENGTH'];
		if ($passwordMinLength <= 0)
		{
			$passwordMinLength = 6;
		}

		$passwordChars = array(
			'abcdefghijklnmopqrstuvwxyz',
			'ABCDEFGHIJKLNMOPQRSTUVWXYZ',
			'0123456789',
		);
		if ($arPolicy['PASSWORD_PUNCTUATION'] === 'Y')
		{
			$passwordChars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";
		}

		$newPassword = $newPasswordConfirm = randString($passwordMinLength + 2, $passwordChars);

		return array(
			'NEW_EMAIL' => $newEmail,
			'NEW_LOGIN' => $newLogin,
			'NEW_NAME' => $newName,
			'NEW_LAST_NAME' => $newLastName,
			'NEW_PASSWORD' => $newPassword,
			'NEW_PASSWORD_CONFIRM' => $newPasswordConfirm,
			'GROUP_ID' => $groupId
		);
	}

	protected function getNormalizedPhone($phone)
	{
		if ($this->arParams['USE_PHONE_NORMALIZATION'] === 'Y')
		{
			$phone = NormalizePhone((string)$phone, 3);
		}

		return $phone;
	}

	/**
	 * Creating new user and logging in
	 *
	 * @param $userProps
	 * @return bool|int
	 */
	protected function registerAndLogIn($userProps)
	{
		$userId = false;
		$userData = $this->generateUserData($userProps);

		$user = new CUser;
		$arAuthResult = $user->Add(array(
			'LOGIN' => $userData['NEW_LOGIN'],
			'NAME' => $userData['NEW_NAME'],
			'LAST_NAME' => $userData['NEW_LAST_NAME'],
			'PASSWORD' => $userData['NEW_PASSWORD'],
			'CONFIRM_PASSWORD' => $userData['NEW_PASSWORD_CONFIRM'],
			'EMAIL' => $userData['NEW_EMAIL'],
			'GROUP_ID' => $userData['GROUP_ID'],
			'ACTIVE' => 'Y',
			'LID' => $this->context->getSite(),
			'PERSONAL_PHONE' => isset($userProps['PHONE']) ? $this->getNormalizedPhone($userProps['PHONE']) : '',
			'PERSONAL_ZIP' => isset($userProps['ZIP']) ? $userProps['ZIP'] : '',
			'PERSONAL_STREET' => isset($userProps['ADDRESS']) ? $userProps['ADDRESS'] : ''
		));

		if (intval($arAuthResult) <= 0)
		{
			$this->addError(Loc::getMessage('STOF_ERROR_REG').((strlen($user->LAST_ERROR) > 0) ? ': '.$user->LAST_ERROR : '' ), self::AUTH_BLOCK);
		}
		else
		{
			global $USER;
			$userId = intval($arAuthResult);
			$USER->Authorize($arAuthResult);
			if ($USER->IsAuthorized())
			{
				if ($this->arParams['SEND_NEW_USER_NOTIFY'] == 'Y')
				{
					CUser::SendUserInfo($USER->GetID(), $this->context->getSite(), Loc::getMessage('INFO_REQ'), true);
				}
			}
			else
			{
				$this->addError(Loc::getMessage('STOF_ERROR_REG_CONFIRM'), self::AUTH_BLOCK);
			}
		}

		return $userId;
	}

	/**
	 * Returns array of user id and 'save to session' flag (true if 'unique user e-mails' option
	 * active and we already have this e-mail)
	 *
	 * @return array
	 * @throws Main\ArgumentNullException
	 */
	protected function autoRegisterUser()
	{
		$personType = $this->request->get('PERSON_TYPE');
		if ($personType <= 0)
		{
			$personTypes = PersonType::load($this->getSiteId());
			foreach ($personTypes as $type)
			{
				$personType = $type['ID'];
				break;
			}

			unset($personTypes, $type);
		}

		$userProps = Sale\PropertyValue::getMeaningfulValues($personType, $this->getPropertyValuesFromRequest());
		$userId = false;
		$saveToSession = false;

		if (
			$this->arParams['ALLOW_APPEND_ORDER'] === 'Y'
			&& Option::get('main', 'new_user_email_uniq_check', '') === 'Y'
			&& ($userProps['EMAIL'] != '' || $userProps['PHONE'] != '')
		)
		{
			$existingUserId = 0;

			if ($userProps['EMAIL'] != '')
			{
				$res = Bitrix\Main\UserTable::getRow(array(
					'filter' => array(
						'=ACTIVE' => 'Y',
						'=EMAIL' => $userProps['EMAIL']
					),
					'select' => array('ID')
				));
				if (isset($res['ID']))
				{
					$existingUserId = (int)$res['ID'];
				}
			}

			if ($existingUserId === 0 && !empty($userProps['PHONE']))
			{
				$normalizedPhone = $this->getNormalizedPhone($userProps['PHONE']);

				if (!empty($normalizedPhone))
				{
					$res = Bitrix\Main\UserTable::getRow(array(
						'filter' => array(
							'ACTIVE' => 'Y',
							array(
								'LOGIC' => 'OR',
								'=PERSONAL_PHONE' => $normalizedPhone,
								'=PERSONAL_MOBILE' => $normalizedPhone
							)
						),
						'select' => array('ID')
					));
					if (isset($res['ID']))
					{
						$existingUserId = (int)$res['ID'];
					}
				}
			}

			if ($existingUserId > 0)
			{
				$userId = $existingUserId;
				$saveToSession = true;
			}
			else
			{
				$userId = $this->registerAndLogIn($userProps);
			}
		}
		elseif ($userProps['EMAIL'] != '' || Option::get('main', 'new_user_email_required', '') === 'N')
		{
			$userId = $this->registerAndLogIn($userProps);
		}
		else
		{
			$this->addError(Loc::getMessage('STOF_ERROR_EMAIL'), self::AUTH_BLOCK);
		}

		return array($userId, $saveToSession);
	}

	public function initGrid()
	{
		$this->arResult["GRID"]["HEADERS"] = $this->getGridHeaders($this->arParams["PRODUCT_COLUMNS"]);
		$this->arResult["GRID"]["HEADERS_HIDDEN"] = $this->getGridHeaders($this->arParams["PRODUCT_COLUMNS_HIDDEN"]);
	}

	public function getGridHeaders($productColumns)
	{
		$arr = array();

		if (is_array($productColumns) && !empty($productColumns))
		{
			$arCodes = array();
			$iBlockProps = array();
			foreach ($productColumns as $key => $value) // making grid headers array
			{
				if (strncmp($value, "PROPERTY_", 9) == 0)
				{
					$propCode = substr($value, 9);

					if ($propCode == '')
						continue;

					$arCodes[] = $propCode;
				}
			}

			if ($this->useCatalog && !empty($arCodes))
			{
				$iBlockList = array();
				$catalogIterator = Bitrix\Catalog\CatalogIblockTable::getList(array(
					'select' => array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID', 'SITE_ID' => 'IBLOCK_SITE.SITE_ID'),
					'filter' => array('SITE_ID' => $this->getSiteId()),
					'runtime' => array(
						'IBLOCK_SITE' => array(
							'data_type' => 'Bitrix\Iblock\IblockSiteTable',
							'reference' => array(
								'ref.IBLOCK_ID' => 'this.IBLOCK_ID',
							),
							'join_type' => 'inner'
						)
					)
				));
				while ($catalog = $catalogIterator->fetch())
				{
					$iBlockList[$catalog['IBLOCK_ID']] = $catalog['IBLOCK_ID'];

					if (intval($catalog['PRODUCT_IBLOCK_ID']) > 0)
						$iBlockList[$catalog['PRODUCT_IBLOCK_ID']] = $catalog['PRODUCT_IBLOCK_ID'];
				}

				if (!empty($iBlockList))
				{
					$propertyIterator = Bitrix\Iblock\PropertyTable::getList(array(
						'select' => array('ID', 'IBLOCK_ID', 'NAME', 'ACTIVE', 'SORT', 'CODE', 'TIMESTAMP_X',
							'DEFAULT_VALUE', 'PROPERTY_TYPE', 'ROW_COUNT', 'COL_COUNT', 'LIST_TYPE',
							'MULTIPLE', 'XML_ID', 'FILE_TYPE', 'MULTIPLE_CNT', 'LINK_IBLOCK_ID', 'WITH_DESCRIPTION',
							'SEARCHABLE', 'FILTRABLE', 'IS_REQUIRED', 'VERSION', 'USER_TYPE', 'USER_TYPE_SETTINGS', 'HINT'),
						'filter' => array(
							'@IBLOCK_ID' => array_keys($iBlockList),
							'=ACTIVE' => 'Y',
							'@CODE' => $arCodes
						),
						'order' => array('SORT' => 'ASC', 'ID' => 'ASC')
					));
					while ($property = $propertyIterator->fetch())
					{
						$this->arIblockProps[$property['IBLOCK_ID']][$property['CODE']] = $property;

						if (!isset($iBlockProps[$property['CODE']]))
							$iBlockProps[$property['CODE']] = $property;
					}
				}
			}

			// making grid headers array
			foreach ($productColumns as $key => $value)
			{
				// processing iblock properties
				if (strncmp($value, "PROPERTY_", 9) == 0)
				{
					$propCode = substr($value, 9);

					if ($propCode == '')
						continue;

					// array of iblock properties to select
					$this->arCustomSelectFields[] = $value;
					$id = $value."_VALUE";
					$name = $value;

					if (array_key_exists($propCode, $iBlockProps))
					{
						$name = $iBlockProps[$propCode]["NAME"];
					}
				}
				else
				{
					$id = $key;
					$name = $value;
				}

				$arColumn = array(
					"id" => $id,
					"name" => $name
				);

				if ($key == "PRICE_FORMATED")
				{
					$arColumn["align"] = "right";
				}

				$arr[] = $arColumn;
			}
		}

		return $arr;
	}

	public function getPropsInfo($source)
	{
		$resultHTML = "";

		foreach ($source["PROPS"] as $val)
		{
			$resultHTML .= str_replace(" ", "&nbsp;", $val["NAME"].": ".$val["VALUE"])."<br />";
		}

		return $resultHTML;
	}

	public function getIblockProps($value, $propData, $arSize = array("WIDTH" => 90, "HEIGHT" => 90), $orderId = 0)
	{
		$res = array();

		if ($propData["MULTIPLE"] == "Y")
		{
			$arVal = array();
			if (!is_array($value))
			{
				if (strpos($value, ",") !== false)
					$arVal = explode(",", $value);
				else
					$arVal[] = $value;
			}
			else
				$arVal = $value;

			if (!empty($arVal))
			{
				foreach ($arVal as $key => $val)
				{
					if ($propData["PROPERTY_TYPE"] == "F")
						$res[] = $this->getFileData(trim($val), $orderId, $arSize);
					else
						$res[] = array("type" => "value", "value" => $val);
				}
			}
		}
		else
		{
			if ($propData["PROPERTY_TYPE"] == "F")
				$res[] = $this->getFileData($value, $orderId, $arSize);
			else
				$res[] = array("type" => "value", "value" => $value);
		}

		return $res;
	}

	public function getLinkedPropValue($basketItem, $property)
	{
		$result = array();

		if ($property['MULTIPLE'] === 'Y')
			$property['VALUE'] = explode(',', $property['VALUE']);

		$formattedProperty = CIBlockFormatProperties::GetDisplayValue($basketItem, $property, 'sale_out');
		if (!empty($formattedProperty['DISPLAY_VALUE']))
		{
			if (is_array($formattedProperty['DISPLAY_VALUE']))
			{
				foreach ($formattedProperty['DISPLAY_VALUE'] as $key => $formatValue)
				{
					$result[] = array(
						'type' => 'linked',
						'value' => $property['VALUE'][$key],
						'value_format' => $formatValue
					);
				}
			}
			else
			{
				$result[] = array(
					'type' => 'linked',
					'value' => is_array($property['VALUE']) ? reset($property['VALUE']) : $property['VALUE'],
					'value_format' => $formattedProperty['DISPLAY_VALUE']
				);
			}
		}

		return $result;
	}

	public function getDirectoryProperty($basketItem, $property)
	{
		$result = array();

		if ($property['MULTIPLE'] === 'Y')
		{
			$property['VALUE'] = explode(', ', $basketItem['PROPERTY_'.$property['CODE'].'_VALUE']);
		}
		else
		{
			$property['VALUE'] = $basketItem['PROPERTY_'.$property['CODE'].'_VALUE'];
		}

		$property['~VALUE'] = $property['VALUE'];

		if (CheckSerializedData($property['USER_TYPE_SETTINGS']))
		{
			$property['USER_TYPE_SETTINGS'] = unserialize($property['USER_TYPE_SETTINGS']);
		}

		$formattedProperty = CIBlockFormatProperties::GetDisplayValue($basketItem, $property, 'sale_out');
		if (!empty($formattedProperty['DISPLAY_VALUE']))
		{
			if (is_array($formattedProperty['DISPLAY_VALUE']))
			{
				foreach ($formattedProperty['DISPLAY_VALUE'] as $key => $formatValue)
				{
					$result[] = array(
						'type' => 'value',
						'value' => $formatValue,
						'value_raw' => $property['VALUE'][$key]
					);
				}
			}
			else
			{
				$result[] = array(
					'type' => 'value',
					'value' => $formattedProperty['DISPLAY_VALUE'],
					'value_raw' => is_array($property['VALUE']) ? reset($property['VALUE']) : $property['VALUE']
				);
			}
		}

		return $result;
	}

	public function getFileData($fileId, $orderId = 0, $arSize = array("WIDTH" => 90, "HEIGHT" => 90))
	{
		$res = "";
		$arFile = CFile::GetFileArray($fileId);

		if ($arFile)
		{
			$is_image = CFile::IsImage($arFile["FILE_NAME"], $arFile["CONTENT_TYPE"]);
			if ($is_image)
			{
				$arImgProduct = CFile::ResizeImageGet($arFile, array("width" => $arSize["WIDTH"], "height" => $arSize["HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);

				if (is_array($arImgProduct))
					$res = array("type" => "image", "value" => $arImgProduct["src"], "source" => $arFile["SRC"]);
			}
			else
				$res = array("type" => "file", "value" => "<a href=".$arFile["SRC"].">".$arFile["ORIGINAL_NAME"]."</a>");
		}

		return $res;
	}

	/**
	 * Set formatted order properties to $this->arResult (heavy load due to compatibility)
	 * Execution of 'OnSaleComponentOrderOneStepOrderProps' event
	 */
	protected function obtainFormattedProperties()
	{
		$arResult =& $this->arResult;
		$arDeleteFieldLocation = array();
		$propIndex = array();
		$arOrderProps = $this->order->getPropertyCollection()->getArray();

        usort($arOrderProps["properties"], "cmpSortOrderProperties");

		foreach ($arOrderProps["properties"] as $arProperty)
		{
			$arProperties = $this->getOrderPropFormatted($arProperty, $arDeleteFieldLocation);

			$flag = $arProperties["USER_PROPS"] == "Y" ? 'Y' : 'N';

			$arResult["ORDER_PROP"]["USER_PROPS_".$flag][$arProperties["ID"]] = $arProperties;
			$propIndex[$arProperties["ID"]] =& $arResult["ORDER_PROP"]["USER_PROPS_".$flag][$arProperties["ID"]];

			$arResult["ORDER_PROP"]["PRINT"][$arProperties["ID"]] = array(
				"ID" => $arProperties["ID"],
				"CODE" => $arProperties["CODE"],
				"NAME" => $arProperties["NAME"],
				"VALUE" => $arProperties["VALUE_FORMATED"],
				"SHOW_GROUP_NAME" => $arProperties["SHOW_GROUP_NAME"]
			);
		}

		// additional city property process
		foreach ($propIndex as $propId => $propDesc)
		{
			if (intval($propDesc['INPUT_FIELD_LOCATION']) && isset($propIndex[$propDesc['INPUT_FIELD_LOCATION']]))
			{
				$propIndex[$propDesc['INPUT_FIELD_LOCATION']]['IS_ALTERNATE_LOCATION_FOR'] = $propId;
				$propIndex[$propId]['CAN_HAVE_ALTERNATE_LOCATION'] = $propDesc['INPUT_FIELD_LOCATION']; // more strict condition rather INPUT_FIELD_LOCATION, check if the property really exists
			}
		}

		//delete prop for text location (town)
		if (count($arDeleteFieldLocation) > 0)
		{
			foreach ($arDeleteFieldLocation as $fieldId)
				unset($arResult["ORDER_PROP"]["USER_PROPS_Y"][$fieldId]);
		}

		$this->executeEvent('OnSaleComponentOrderOneStepOrderProps', $this->order);
	}

	private function cmpSortOrderProperties($a, $b) {
        if ($a["SORT"] == $b["SORT"]) {
            return 0;
        }
        return ($a["SORT"] < $b["SORT"]) ? -1 : 1;
    }

	protected function getOrderPropFormatted($arProperty, &$arDeleteFieldLocation = array())
	{
		static $propertyGroupID = 0;
		static $propertyUSER_PROPS = '';

		$arProperty['FIELD_NAME'] = 'ORDER_PROP_'.$arProperty['ID'];

		if ($arProperty['CODE'] != '')
		{
			$arProperty['FIELD_ID'] = 'ORDER_PROP_'.$arProperty['CODE'];
		}
		else
		{
			$arProperty['FIELD_ID'] = 'ORDER_PROP_'.$arProperty['ID'];
		}

		if (intval($arProperty['PROPS_GROUP_ID']) != $propertyGroupID || $propertyUSER_PROPS != $arProperty['USER_PROPS'])
		{
			$arProperty['SHOW_GROUP_NAME'] = 'Y';
		}

		$propertyGroupID = $arProperty['PROPS_GROUP_ID'];
		$propertyUSER_PROPS = $arProperty['USER_PROPS'];

		if ($arProperty['REQUIRED'] === 'Y' || $arProperty['IS_PROFILE_NAME'] === 'Y'
			|| $arProperty['IS_LOCATION'] === 'Y' || $arProperty['IS_LOCATION4TAX'] === 'Y'
			|| $arProperty['IS_PAYER'] === 'Y' || $arProperty['IS_ZIP'] === 'Y')
		{
			$arProperty['REQUIED'] = 'Y';
			$arProperty['REQUIED_FORMATED'] = 'Y';
		}

		if ($arProperty['IS_LOCATION'] === 'Y')
		{
			$deliveryId = CSaleLocation::getLocationIDbyCODE(current($arProperty['VALUE']));
			$this->arUserResult['DELIVERY_LOCATION'] = $deliveryId;
			$this->arUserResult['DELIVERY_LOCATION_BCODE'] = current($arProperty['VALUE']);
		}

		if ($arProperty['IS_ZIP'] === 'Y')
		{
			$this->arUserResult['DELIVERY_LOCATION_ZIP'] = current($arProperty['VALUE']);
		}

		if ($arProperty['IS_LOCATION4TAX'] === 'Y')
		{
			$taxId = CSaleLocation::getLocationIDbyCODE(current($arProperty['VALUE']));
			$this->arUserResult['TAX_LOCATION'] = $taxId;
			$this->arUserResult['TAX_LOCATION_BCODE'] = current($arProperty['VALUE']);
		}

		if ($arProperty['IS_PAYER'] === 'Y')
		{
			$this->arUserResult['PAYER_NAME'] = current($arProperty['VALUE']);
		}

		if ($arProperty['IS_EMAIL'] === 'Y')
		{
			$this->arUserResult['USER_EMAIL'] = current($arProperty['VALUE']);
		}

		if ($arProperty['IS_PROFILE_NAME'] === 'Y')
		{
			$this->arUserResult['PROFILE_NAME'] = current($arProperty['VALUE']);
		}

		switch ($arProperty['TYPE'])
		{
			case 'Y/N': self::formatYN($arProperty); break;
			case 'STRING': self::formatString($arProperty); break;
			case 'NUMBER': self::formatNumber($arProperty); break;
			case 'ENUM': self::formatEnum($arProperty); break;
			case 'LOCATION':
				self::formatLocation($arProperty, $arDeleteFieldLocation, $this->arResult['LOCATION_ALT_PROP_DISPLAY_MANUAL']);
				break;
			case 'FILE': self::formatFile($arProperty); break;
			case 'DATE': self::formatDate($arProperty); break;
		}

		return $arProperty;
	}

	public static function formatYN(array &$arProperty)
	{
		$curVal = $arProperty['VALUE'];

		if (current($curVal) == "Y")
		{
			$arProperty["CHECKED"] = "Y";
			$arProperty["VALUE_FORMATED"] = Loc::getMessage("SOA_Y");
		}
		else
			$arProperty["VALUE_FORMATED"] = Loc::getMessage("SOA_N");

		$arProperty["SIZE1"] = (intval($arProperty["SIZE1"]) > 0) ? $arProperty["SIZE1"] : 30;

		$arProperty["VALUE"] = current($curVal);
		$arProperty["TYPE"] = 'CHECKBOX';
	}

	public static function formatString(array &$arProperty)
	{
		$curVal = $arProperty['VALUE'];

		if (!empty($arProperty["MULTILINE"]) && $arProperty["MULTILINE"] == 'Y')
		{
			$arProperty["TYPE"] = 'TEXTAREA';
			$arProperty["SIZE2"] = (intval($arProperty["ROWS"]) > 0) ? $arProperty["ROWS"] : 4;
			$arProperty["SIZE1"] = (intval($arProperty["COLS"]) > 0) ? $arProperty["COLS"] : 40;
		}
		else
			$arProperty["TYPE"] = 'TEXT';

		$arProperty["SOURCE"] = current($curVal) == $arProperty['DEFAULT_VALUE'] ? 'DEFAULT' : 'FORM';
		$arProperty["VALUE"] = current($curVal);
		$arProperty["VALUE_FORMATED"] = $arProperty["VALUE"];
	}

	public static function formatNumber(array &$arProperty)
	{
		$curVal = $arProperty['VALUE'];
		$arProperty["TYPE"] = 'TEXT';
		$arProperty["VALUE"] = current($curVal);
		$arProperty["VALUE_FORMATED"] = $arProperty["VALUE"];
	}

	public static function formatEnum(array &$arProperty)
	{
		$curVal = $arProperty['VALUE'];

		if ($arProperty["MULTIELEMENT"] == 'Y')
		{
			if ($arProperty["MULTIPLE"] == 'Y')
			{
				$setValue = array();
				$arProperty["FIELD_NAME"] = "ORDER_PROP_".$arProperty["ID"].'[]';
				$arProperty["SIZE1"] = (intval($arProperty["SIZE1"]) > 0) ? $arProperty["SIZE1"] : 5;

				$i = 0;
				foreach ($arProperty["OPTIONS"] as $val => $name)
				{
					$arVariants = array(
						'VALUE' => $val,
						'NAME' => $name
					);
					if ((is_array($curVal) && in_array($arVariants["VALUE"], $curVal)))
					{
						$arVariants["SELECTED"] = "Y";
						if ($i > 0)
							$arProperty["VALUE_FORMATED"] .= ", ";
						$arProperty["VALUE_FORMATED"] .= $arVariants["NAME"];
						$setValue[] = $arVariants["VALUE"];
						$i++;
					}
					$arProperty["VARIANTS"][] = $arVariants;
				}

				$arProperty["TYPE"] = 'MULTISELECT';
			}
			else
			{
				foreach ($arProperty['OPTIONS'] as $val => $name)
				{
					$arVariants = array(
						'VALUE' => $val,
						'NAME' => $name
					);
					if ($arVariants["VALUE"] == current($curVal))
					{
						$arVariants["CHECKED"] = "Y";
						$arProperty["VALUE_FORMATED"] = $arVariants["NAME"];
					}

					$arProperty["VARIANTS"][] = $arVariants;
				}
				$arProperty["TYPE"] = 'RADIO';
			}
		}
		else
		{
			if ($arProperty["MULTIPLE"] == 'Y')
			{
				$setValue = array();
				$arProperty["FIELD_NAME"] = "ORDER_PROP_".$arProperty["ID"].'[]';
				$arProperty["SIZE1"] = ((intval($arProperty["SIZE1"]) > 0) ? $arProperty["SIZE1"] : 5);

				$i = 0;
				foreach ($arProperty["OPTIONS"] as $val => $name)
				{
					$arVariants = array(
						'VALUE' => $val,
						'NAME' => $name
					);
					if (is_array($curVal) && in_array($arVariants["VALUE"], $curVal))
					{
						$arVariants["SELECTED"] = "Y";
						if ($i > 0)
							$arProperty["VALUE_FORMATED"] .= ", ";
						$arProperty["VALUE_FORMATED"] .= $arVariants["NAME"];
						$setValue[] = $arVariants["VALUE"];
						$i++;
					}
					$arProperty["VARIANTS"][] = $arVariants;
				}

				$arProperty["TYPE"] = 'MULTISELECT';
			}
			else
			{
				$arProperty["SIZE1"] = ((intval($arProperty["SIZE1"]) > 0) ? $arProperty["SIZE1"] : 1);
				$flagDefault = "N";
				$nameProperty = "";
				foreach ($arProperty["OPTIONS"] as $val => $name)
				{
					$arVariants = array(
						'VALUE' => $val,
						'NAME' => $name
					);
					if ($flagDefault == "N" && $nameProperty == "")
					{
						$nameProperty = $arVariants["NAME"];
					}
					if ($arVariants["VALUE"] == current($curVal))
					{
						$arVariants["SELECTED"] = "Y";
						$arProperty["VALUE_FORMATED"] = $arVariants["NAME"];
						$flagDefault = "Y";
					}
					$arProperty["VARIANTS"][] = $arVariants;
				}
				if ($flagDefault == "N")
				{
					$arProperty["VARIANTS"][0]["SELECTED"]= "Y";
					$arProperty["VARIANTS"][0]["VALUE_FORMATED"] = $nameProperty;
				}
				$arProperty["TYPE"] = 'SELECT';
			}
		}
	}

	public static function formatLocation(array &$arProperty, array &$arDeleteFieldLocation, $locationAltPropDisplayManual = null)
	{
		$curVal = CSaleLocation::getLocationIDbyCODE(current($arProperty['VALUE']));
		$arProperty["VALUE"] = $curVal;

		$locationFound = false;
		//todo select via D7
		$dbVariants = CSaleLocation::GetList(
			array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"),
			array("LID" => LANGUAGE_ID),
			false,
			false,
			array("ID", "COUNTRY_NAME", "CITY_NAME", "SORT", "COUNTRY_NAME_LANG", "CITY_NAME_LANG", "CITY_ID", "CODE")
		);
		while ($arVariants = $dbVariants->GetNext())
		{
			$city = !empty($arVariants['CITY_NAME']) ? ' - '.$arVariants['CITY_NAME'] : '';

			if ($arVariants['ID'] === $curVal)
			{
				// set formatted value
				$locationFound = $arVariants;
				$arVariants['SELECTED'] = 'Y';
				$arProperty['VALUE_FORMATED'] = $arVariants['COUNTRY_NAME'].$city;
			}

			$arVariants['NAME'] = $arVariants['COUNTRY_NAME'].$city;
			// save to variants
			$arProperty['VARIANTS'][] = $arVariants;
		}

		if(!$locationFound && intval($curVal))
		{
			$item = CSaleLocation::GetById($curVal);
			if ($item)
			{
				// set formatted value
				$locationFound = $item;
				$arProperty["VALUE_FORMATED"] = $item["COUNTRY_NAME"].((strlen($item["CITY_NAME"]) > 0) ? " - " : "").$item["CITY_NAME"];
				$item['SELECTED'] = 'Y';
				$item['NAME'] = $item["COUNTRY_NAME"].((strlen($item["CITY_NAME"]) > 0) ? " - " : "").$item["CITY_NAME"];

				// save to variants
				$arProperty["VARIANTS"][] = $item;
			}
		}

		if ($locationFound)
		{
			// enable location town text
			if (isset($locationAltPropDisplayManual)) // its an ajax-hit and sale.location.selector.steps is used
			{
				if (intval($locationAltPropDisplayManual[$arProperty["ID"]])) // user MANUALLY selected "Other location" in the selector
					unset($arDeleteFieldLocation[$arProperty["ID"]]);
				else
					$arDeleteFieldLocation[$arProperty["ID"]] = $arProperty["INPUT_FIELD_LOCATION"];
			}
			else
			{
				if ($arProperty["IS_LOCATION"] == "Y" && intval($arProperty["INPUT_FIELD_LOCATION"]) > 0)
				{
					if (intval($locationFound["CITY_ID"]) <= 0)
						unset($arDeleteFieldLocation[$arProperty["ID"]]);
					else
						$arDeleteFieldLocation[$arProperty["ID"]] = $arProperty["INPUT_FIELD_LOCATION"];
				}
			}
		}
		else
		{
			// nothing found, may be it is the first load - hide
			$arDeleteFieldLocation[$arProperty["ID"]] = $arProperty["INPUT_FIELD_LOCATION"];
		}
	}

	public static function formatFile(array &$arProperty)
	{
		$curVal = $arProperty['VALUE'];

		$arProperty["SIZE1"] = intval($arProperty["SIZE1"]);
		if ($arProperty['MULTIPLE'] == 'Y')
		{
			$arr = array();
			$curVal = isset($curVal) ? $curVal : $arProperty["DEFAULT_VALUE"];
			foreach ($curVal as $file)
			{
				$arr[] = $file['ID'];
			}
			$arProperty["VALUE"] = serialize($arr);
		}
		else
		{
			$arFile = isset($curVal) && is_array($curVal) ? current($curVal) : $arProperty["DEFAULT_VALUE"];
			if (is_array($arFile))
				$arProperty["VALUE"] = $arFile['ID'];
		}
	}

	public static function formatDate(array &$arProperty)
	{
		$arProperty["VALUE"] = current($arProperty['VALUE']);
		$arProperty["VALUE_FORMATED"] = $arProperty["VALUE"];
	}

	/**
	 * Set basket items data from order object to $this->arResult
	 */
	protected function obtainBasket()
	{
		$arResult =& $this->arResult;

		$arResult["MAX_DIMENSIONS"] = $arResult["ITEMS_DIMENSIONS"] = array();
		$arResult["BASKET_ITEMS"] = array();

		$this->calculateBasket = $this->order->getBasket()->createClone();

		$discounts = $this->order->getDiscount();
		$showPrices = $discounts->getShowPrices();
		if (!empty($showPrices['BASKET']))
		{
			foreach ($showPrices['BASKET'] as $basketCode => $data)
			{
				$basketItem = $this->calculateBasket->getItemByBasketCode($basketCode);
				if ($basketItem instanceof Sale\BasketItemBase)
				{
					$basketItem->setFieldNoDemand('BASE_PRICE', $data['SHOW_BASE_PRICE']);
					$basketItem->setFieldNoDemand('PRICE', $data['SHOW_PRICE']);
					$basketItem->setFieldNoDemand('DISCOUNT_PRICE', $data['SHOW_DISCOUNT']);
				}
			}
		}
		unset($showPrices);

		/** @var Sale\BasketItem $basketItem */
		foreach ($this->calculateBasket as $basketItem)
		{
			$arBasketItem = $basketItem->getFieldValues();
			if ($basketItem->getVatRate() > 0)
			{
				$arResult["bUsingVat"] = "Y";
				$arBasketItem["VAT_VALUE"] = $basketItem->getVat();
			}
			$arBasketItem["QUANTITY"] = $basketItem->getQuantity();
			$arBasketItem["PRICE_FORMATED"] = SaleFormatCurrency($basketItem->getPrice(), $this->order->getCurrency());
			$arBasketItem["WEIGHT_FORMATED"] = roundEx(doubleval($basketItem->getWeight()/$arResult["WEIGHT_KOEF"]), SALE_WEIGHT_PRECISION)." ".$arResult["WEIGHT_UNIT"];
			$arBasketItem["DISCOUNT_PRICE"] = $basketItem->getDiscountPrice();

			$arBasketItem["DISCOUNT_PRICE_PERCENT"] = 0;
			if ($arBasketItem['CUSTOM_PRICE'] != 'Y')
			{
				$arBasketItem['DISCOUNT_PRICE_PERCENT'] = Sale\Discount::calculateDiscountPercent(
					$arBasketItem['BASE_PRICE'],
					$arBasketItem['DISCOUNT_PRICE']
				);
				if ($arBasketItem['DISCOUNT_PRICE_PERCENT'] === null)
					$arBasketItem['DISCOUNT_PRICE_PERCENT'] = 0;
			}
			$arBasketItem["DISCOUNT_PRICE_PERCENT_FORMATED"] = $arBasketItem['DISCOUNT_PRICE_PERCENT'].'%';

			$arBasketItem["BASE_PRICE_FORMATED"] = SaleFormatCurrency($basketItem->getBasePrice(), $this->order->getCurrency());

			$arDim = unserialize($basketItem->getField('DIMENSIONS'));
			if (is_array($arDim))
			{
				$arResult["MAX_DIMENSIONS"] = CSaleDeliveryHelper::getMaxDimensions(
					array(
						$arDim["WIDTH"],
						$arDim["HEIGHT"],
						$arDim["LENGTH"]
					),
					$arResult["MAX_DIMENSIONS"]);

				$arResult["ITEMS_DIMENSIONS"][] = $arDim;
			}

			$arBasketItem["PROPS"] = array();
			/** @var Sale\BasketPropertiesCollection $propertyCollection */
			$propertyCollection = $basketItem->getPropertyCollection();
			$propList = $propertyCollection->getPropertyValues();
			foreach ($propList as $key => &$prop)
			{
				if ($prop['CODE'] == 'CATALOG.XML_ID' || $prop['CODE'] == 'PRODUCT.XML_ID' || $prop['CODE'] == 'SUM_OF_CHARGE')
					continue;

				$prop = array_filter($prop, array("CSaleBasketHelper", "filterFields"));
				$arBasketItem["PROPS"][] = $prop;
			}

			$this->arElementId[] = $arBasketItem["PRODUCT_ID"];
			$arBasketItem["SUM_NUM"] = $basketItem->getPrice() * $basketItem->getQuantity();
			$arBasketItem["SUM"] = SaleFormatCurrency($basketItem->getPrice() * $basketItem->getQuantity(), $this->order->getCurrency());
			$arBasketItem["SUM_BASE"] = $basketItem->getBasePrice() * $basketItem->getQuantity();
			$arBasketItem["SUM_BASE_FORMATED"] = SaleFormatCurrency($basketItem->getBasePrice() * $basketItem->getQuantity(), $this->order->getCurrency());

			$arResult["BASKET_ITEMS"][] = $arBasketItem;
		}
	}

	/**
	 * Set basket items data from iblocks (basket column properties, sku, preview pictures, etc) to $this->arResult
	 */
	protected function obtainPropertiesForIbElements()
	{
		if (empty($this->arElementId))
		{
			return;
		}

		$arResult =& $this->arResult;
		$arResult["GRID"]["ROWS"] = array();
		$arParents = array();

		if ($this->useCatalog)
		{
			$arParents = CCatalogSku::getProductList($this->arElementId);
			if (!empty($arParents))
			{
				foreach ($arParents as $productId => $arParent)
				{
					$this->arElementId[] = $arParent["ID"];
					$this->arSku2Parent[$productId] = $arParent["ID"];
				}
			}
		}

		$arElementData = array();
		$arProductData = array();
		$elementIndex = array();
		$res = CIBlockElement::GetList(
			array(),
			array("=ID" => array_unique($this->arElementId)),
			false,
			false,
			array("ID", "IBLOCK_ID", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PREVIEW_TEXT")
		);
		while ($arElement = $res->Fetch())
		{
			$arElementData[$arElement["IBLOCK_ID"]][] = $arElement["ID"];
			$arProductData[$arElement["ID"]] = $arElement;
			$elementIndex[$arElement["ID"]] = array();
		}

		foreach ($arElementData as $iBlockId => $arElemId)
		{
			$arCodes = array();
			if (!empty($this->arIblockProps[$iBlockId]))
				$arCodes = array_keys($this->arIblockProps[$iBlockId]);

			$imageCode = $this->arParams['ADDITIONAL_PICT_PROP'][$iBlockId];

			if (!empty($imageCode) && !in_array($imageCode, $arCodes))
				$arCodes[] = $imageCode;

			if (!empty($arCodes))
			{
				CIBlockElement::GetPropertyValuesArray($elementIndex, $iBlockId,
					array("ID" => $arElemId),
					array("CODE" => $arCodes)
				);
			}
		}
		unset($arElementData);

		$arAdditionalImages = array();
		foreach ($elementIndex as $productId => $productProperties)
		{
			if (!empty($productProperties) && is_array($productProperties))
			{
				foreach ($productProperties as $code => $property)
				{
					if (
						!empty($this->arParams['ADDITIONAL_PICT_PROP'])
						&& array_key_exists($arProductData[$productId]['IBLOCK_ID'], $this->arParams['ADDITIONAL_PICT_PROP'])
					)
					{
						if ($this->arParams['ADDITIONAL_PICT_PROP'][$arProductData[$productId]['IBLOCK_ID']] == $code)
						{
							$arAdditionalImages[$productId] = is_array($property['VALUE']) ? current($property['VALUE']) : $property['VALUE'];
						}
					}

					if (
						!empty($this->arIblockProps[$arProductData[$productId]['IBLOCK_ID']])
						&& array_key_exists($code, $this->arIblockProps[$arProductData[$productId]['IBLOCK_ID']])
					)
					{
						if (is_array($property['VALUE']))
						{
							$arProductData[$productId]['PROPERTY_'.$code.'_VALUE'] = implode(', ', $property['VALUE']);
						}
						else
						{
							$arProductData[$productId]['PROPERTY_'.$code.'_VALUE'] = $property['VALUE'];
						}

						if (is_array($property['PROPERTY_VALUE_ID']))
						{
							$arProductData[$productId]['PROPERTY_'.$code.'_VALUE_ID'] = implode(', ', $property['PROPERTY_VALUE_ID']);
						}
						else
						{
							$arProductData[$productId]['PROPERTY_'.$code.'_VALUE_ID'] = $property['PROPERTY_VALUE_ID'];
						}

						if ($property['PROPERTY_TYPE'] == 'L')
						{
							$arProductData[$productId]['PROPERTY_'.$code.'_ENUM_ID'] = $property['VALUE_ENUM_ID'];
						}
					}
				}
			}
		}
		unset($elementIndex);

		$currentProductProperties = array();

		$needToResizeProductImages = $this->arParams['COMPATIBLE_MODE'] === 'Y'
			|| isset($this->arParams['PRODUCT_COLUMNS']['PREVIEW_PICTURE'])
			|| isset($this->arParams['PRODUCT_COLUMNS']['DETAIL_PICTURE'])
			|| isset($this->arParams['PRODUCT_COLUMNS_HIDDEN']['PREVIEW_PICTURE'])
			|| isset($this->arParams['PRODUCT_COLUMNS_HIDDEN']['DETAIL_PICTURE']);


		foreach ($arResult["BASKET_ITEMS"] as &$arResultItem)
		{
			$productId = $arResultItem["PRODUCT_ID"];
			$arParent = $arParents[$productId];
			$itemIblockId = intval($arProductData[$productId]['IBLOCK_ID']);
			$currentProductProperties[$productId] = isset($this->arIblockProps[$itemIblockId])
				? $this->arIblockProps[$itemIblockId]
				: array();

			if (
				(int)$arProductData[$productId]["PREVIEW_PICTURE"] <= 0
				&& (int)$arProductData[$productId]["DETAIL_PICTURE"] <= 0
				&& $arParent
			)
			{
				$productId = $arParent["ID"];
			}

			if ((int)$arProductData[$productId]["PREVIEW_PICTURE"] > 0)
			{
				$arResultItem["PREVIEW_PICTURE"] = $arProductData[$productId]["PREVIEW_PICTURE"];
			}

			if ((int)$arProductData[$productId]["DETAIL_PICTURE"] > 0)
			{
				$arResultItem["DETAIL_PICTURE"] = $arProductData[$productId]["DETAIL_PICTURE"];
			}

			if ($arProductData[$productId]["PREVIEW_TEXT"] != '')
			{
				$arResultItem["PREVIEW_TEXT"] = $arProductData[$productId]["PREVIEW_TEXT"];
				$arResultItem["PREVIEW_TEXT_TYPE"] = $arProductData[$productId]["PREVIEW_TEXT_TYPE"];
			}

			if (!empty($arProductData[$arResultItem["PRODUCT_ID"]]) && is_array($arProductData[$arResultItem["PRODUCT_ID"]]))
			{
				foreach ($arProductData[$arResultItem["PRODUCT_ID"]] as $key => $value)
				{
					if (strpos($key, "PROPERTY_") !== false)
						$arResultItem[$key] = $value;
				}
			}

			// if sku element doesn't have some property value - we'll show parent element value instead
			if (isset($this->arSku2Parent[$arResultItem["PRODUCT_ID"]]))
			{
				$parentIblockId = $arProductData[$this->arSku2Parent[$arResultItem["PRODUCT_ID"]]]['IBLOCK_ID'];

				if (!empty($this->arIblockProps[$parentIblockId]))
				{
					$currentProductProperties[$arResultItem["PRODUCT_ID"]] = array_merge(
						$this->arIblockProps[$parentIblockId],
						$currentProductProperties[$arResultItem["PRODUCT_ID"]]
					);
				}

				foreach ($this->arCustomSelectFields as $field)
				{
					$fieldVal = $field."_VALUE";
					$parentId = $this->arSku2Parent[$arResultItem["PRODUCT_ID"]];

					// can be array or string
					if (
						(!isset($arResultItem[$fieldVal]) || (isset($arResultItem[$fieldVal]) && strlen($arResultItem[$fieldVal]) == 0))
						&& (isset($arProductData[$parentId][$fieldVal]) && !empty($arProductData[$parentId][$fieldVal]))
					)
					{
						$arResultItem[$fieldVal] = $arProductData[$parentId][$fieldVal];
					}
				}
			}

			// replace PREVIEW_PICTURE with selected ADDITIONAL_PICT_PROP
			if (
				empty($arProductData[$arResultItem["PRODUCT_ID"]]["PREVIEW_PICTURE"])
				&& empty($arProductData[$arResultItem["PRODUCT_ID"]]["DETAIL_PICTURE"])
				&& $arAdditionalImages[$arResultItem["PRODUCT_ID"]]
			)
			{
				$arResultItem["PREVIEW_PICTURE"] = $arAdditionalImages[$arResultItem["PRODUCT_ID"]];
			}
			elseif (
				empty($arResultItem["PREVIEW_PICTURE"])
				&& empty($arResultItem["DETAIL_PICTURE"])
				&& $arAdditionalImages[$productId]
			)
			{
				$arResultItem["PREVIEW_PICTURE"] = $arAdditionalImages[$productId];
			}

			$arResultItem["PREVIEW_PICTURE_SRC"] = "";

			if (
				$needToResizeProductImages
				&& isset($arResultItem["PREVIEW_PICTURE"])
				&& (int)$arResultItem["PREVIEW_PICTURE"] > 0
			)
			{
				$arImage = CFile::GetFileArray($arResultItem["PREVIEW_PICTURE"]);
				if (!empty($arImage))
				{
					self::resizeImage($arResultItem, 'PREVIEW_PICTURE', $arImage,
						array("width" => 320, "height" => 320),
						array("width" => 110, "height" => 110),
						$this->arParams['BASKET_IMAGES_SCALING']
					);
				}
			}

			$arResultItem["DETAIL_PICTURE_SRC"] = "";

			if (
				$needToResizeProductImages
				&& isset($arResultItem["DETAIL_PICTURE"])
				&& (int)$arResultItem["DETAIL_PICTURE"] > 0
			)
			{
				$arImage = CFile::GetFileArray($arResultItem["DETAIL_PICTURE"]);
				if (!empty($arImage))
				{
					self::resizeImage($arResultItem, 'DETAIL_PICTURE', $arImage,
						array("width" => 320, "height" => 320),
						array("width" => 110, "height" => 110),
						$this->arParams['BASKET_IMAGES_SCALING']
					);
				}
			}
		}

		if (!empty($arResult["BASKET_ITEMS"]) && $this->useCatalog)
		{
			$arResult["BASKET_ITEMS"] = getMeasures($arResult["BASKET_ITEMS"]);
		}

		foreach ($arResult["BASKET_ITEMS"] as $arBasketItem)
		{
			// prepare values for custom-looking columns
			$arCols = array(
				"PROPS" => $this->getPropsInfo($arBasketItem)
			);

			if ($this->arParams['COMPATIBLE_MODE'] === 'Y')
			{
				if (isset($arBasketItem["PREVIEW_PICTURE"]) && (int)$arBasketItem["PREVIEW_PICTURE"] > 0)
				{
					$arCols["PREVIEW_PICTURE"] = CSaleHelper::getFileInfo(
						$arBasketItem["PREVIEW_PICTURE"],
						array("WIDTH" => 110, "HEIGHT" => 110)
					);
				}

				if (isset($arBasketItem["DETAIL_PICTURE"]) && (int)$arBasketItem["DETAIL_PICTURE"] > 0)
				{
					$arCols["DETAIL_PICTURE"] = CSaleHelper::getFileInfo(
						$arBasketItem["DETAIL_PICTURE"],
						array("WIDTH"=> 110, "HEIGHT" => 110)
					);
				}
			}

			if (!empty($arBasketItem["MEASURE_TEXT"]))
			{
				$arCols["QUANTITY"] = $arBasketItem["QUANTITY"]."&nbsp;".$arBasketItem["MEASURE_TEXT"];
			}

			foreach ($arBasketItem as $tmpKey => $value)
			{
				if ((strpos($tmpKey, "PROPERTY_", 0) === 0) && (strrpos($tmpKey, "_VALUE") == strlen($tmpKey) - 6))
				{
					$code = str_replace(array("PROPERTY_", "_VALUE"), "", $tmpKey);
					$propData = $currentProductProperties[$arBasketItem['PRODUCT_ID']][$code];

					// display linked property type
					if ($propData['PROPERTY_TYPE'] === 'E')
					{
						$propData['VALUE'] = $value;
						$arCols[$tmpKey] = $this->getLinkedPropValue($arBasketItem, $propData);
					}
					elseif ($propData['PROPERTY_TYPE'] === 'S' && $propData['USER_TYPE'] === 'directory')
					{
						$arCols[$tmpKey] = $this->getDirectoryProperty($arBasketItem, $propData);
					}
					else
					{
						$arCols[$tmpKey] = $this->getIblockProps($value, $propData, array('WIDTH' => 110, 'HEIGHT' => 110));
					}
				}
			}

			$arResult["GRID"]["ROWS"][$arBasketItem["ID"]] = array(
				"id" => $arBasketItem["ID"],
				"data" => $arBasketItem,
				"actions" => array(),
				"columns" => $arCols,
				"editable" => true
			);
		}
	}

	/**
	 * Set delivery data from shipment object and delivery services object to $this->arResult
	 * Execution of 'OnSaleComponentOrderOneStepDelivery' event
	 *
	 * @throws Main\NotSupportedException
	 */
	protected function obtainDelivery()
	{
		$arResult =& $this->arResult;

		$arStoreId = array();
		/** @var Shipment $shipment */
		$shipment = $this->getCurrentShipment($this->order);

		if (!empty($this->arDeliveryServiceAll))
		{
			foreach ($this->arDeliveryServiceAll as $deliveryObj)
			{
				$arDelivery =& $this->arResult["DELIVERY"][$deliveryObj->getId()];

				$arDelivery['ID'] = $deliveryObj->getId();
				$arDelivery['NAME'] = $deliveryObj->isProfile() ? $deliveryObj->getNameWithParent() : $deliveryObj->getName();
				$arDelivery['OWN_NAME'] = $deliveryObj->getName();
				$arDelivery['DESCRIPTION'] = $deliveryObj->getDescription();
				$arDelivery['FIELD_NAME'] = 'DELIVERY_ID';
				$arDelivery["CURRENCY"] = $this->order->getCurrency();
				$arDelivery['SORT'] = $deliveryObj->getSort();
				$arDelivery['EXTRA_SERVICES'] = $deliveryObj->getExtraServices()->getItems();
				$arDelivery['STORE'] = Delivery\ExtraServices\Manager::getStoresList($deliveryObj->getId());

				if (intval($deliveryObj->getLogotip()) > 0)
					$arDelivery["LOGOTIP"] = CFile::GetFileArray($deliveryObj->getLogotip());

				if (!empty($arDelivery['STORE']) && is_array($arDelivery['STORE']))
				{
					foreach ($arDelivery['STORE'] as $val)
						$arStoreId[$val] = $val;
				}

				$buyerStore = $this->request->get('BUYER_STORE');
				if (!empty($buyerStore) && !empty($arDelivery['STORE']) && is_array($arDelivery['STORE']) && in_array($buyerStore, $arDelivery['STORE']))
				{
					$this->arUserResult['DELIVERY_STORE'] = $arDelivery["ID"];
				}
			}
		}

		$arResult["BUYER_STORE"] = $shipment->getStoreId();

		$arStore = array();
		$dbList = CCatalogStore::GetList(
			array("SORT" => "DESC", "ID" => "DESC"),
			array("ACTIVE" => "Y", "ID" => $arStoreId, "ISSUING_CENTER" => "Y", "+SITE_ID" => $this->getSiteId()),
			false,
			false,
			array("ID", "TITLE", "ADDRESS", "DESCRIPTION", "IMAGE_ID", "PHONE", "SCHEDULE", "GPS_N", "GPS_S", "ISSUING_CENTER", "SITE_ID")
		);
		while ($arStoreTmp = $dbList->Fetch())
		{
			if ($arStoreTmp["IMAGE_ID"] > 0)
				$arStoreTmp["IMAGE_ID"] = CFile::GetFileArray($arStoreTmp["IMAGE_ID"]);
			else
				$arStoreTmp["IMAGE_ID"] = null;

			$arStore[$arStoreTmp["ID"]] = $arStoreTmp;
		}

		$arResult["STORE_LIST"] = $arStore;

		$arResult["DELIVERY_EXTRA"] = array();
		$deliveryExtra = $this->request->get('DELIVERY_EXTRA');
		if (is_array($deliveryExtra) && !empty($deliveryExtra[$this->arUserResult["DELIVERY_ID"]]))
			$arResult["DELIVERY_EXTRA"] = $deliveryExtra[$this->arUserResult["DELIVERY_ID"]];

		$this->executeEvent('OnSaleComponentOrderOneStepDelivery', $this->order);
	}

	/**
	 * Set pay system data from inner/external payment object and pay system services object to $this->arResult
	 * Execution of 'OnSaleComponentOrderOneStepPaySystem' event
	 */
	protected function obtainPaySystem()
	{
		$arResult =& $this->arResult;

		$innerPayment = $this->getInnerPayment($this->order);
		if (!empty($innerPayment) && $innerPayment->getSum() > 0)
		{
			$arResult['PAYED_FROM_ACCOUNT_FORMATED'] = SaleFormatCurrency($innerPayment->getSum(), $this->order->getCurrency());
			$arResult['ORDER_TOTAL_LEFT_TO_PAY'] = $this->order->getPrice() - $innerPayment->getSum();
			$arResult['ORDER_TOTAL_LEFT_TO_PAY_FORMATED'] = SaleFormatCurrency($this->order->getPrice() - $innerPayment->getSum(), $this->order->getCurrency());
		}

		$paySystemList = $this->arParams['DELIVERY_TO_PAYSYSTEM'] === 'p2d' ? $this->arActivePaySystems : $this->arPaySystemServiceAll;
		if (!empty($paySystemList))
		{
			$innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();

			if (!empty($paySystemList[$innerPaySystemId]))
			{
				$innerPaySystem = $paySystemList[$innerPaySystemId];

				if ($innerPaySystem['LOGOTIP'] > 0)
				{
					$innerPaySystem['LOGOTIP'] = CFile::GetFileArray($innerPaySystem['LOGOTIP']);
				}

				$arResult['INNER_PAY_SYSTEM'] = $innerPaySystem;
				unset($paySystemList[$innerPaySystemId]);
			}

			$extPayment = $this->getExternalPayment($this->order);
			$paymentId = !empty($extPayment) ? $extPayment->getPaymentSystemId() : null;

			foreach ($paySystemList as $paySystem)
			{
				$paySystem['PSA_ID'] = $paySystem['ID'];

				if ((string)$paySystem['PSA_NAME'] === '')
				{
					$paySystem['PSA_NAME'] = $paySystem['NAME'];
				}

				$paySystem['PSA_NAME'] = htmlspecialcharsEx($paySystem['PSA_NAME']);

				$keyMap = array(
					'ACTION_FILE', 'RESULT_FILE', 'NEW_WINDOW', 'PERSON_TYPE_ID', 'PARAMS', 'TARIF', 'HAVE_PAYMENT',
					'HAVE_ACTION', 'HAVE_RESULT', 'HAVE_PREPAY', 'HAVE_RESULT_RECEIVE', 'ENCODING'
				);
				foreach ($keyMap as $key)
				{
					$paySystem["PSA_{$key}"] = $paySystem[$key];
					unset($paySystem[$key]);
				}

				if ($paySystem['LOGOTIP'] > 0)
				{
					$paySystem['PSA_LOGOTIP'] = CFile::GetFileArray($paySystem['LOGOTIP']);
				}
				unset($paySystem['LOGOTIP']);

				if ($paymentId == $paySystem['ID'])
				{
					$paySystem['CHECKED'] = 'Y';
				}

				$paySystem['PRICE'] = 0;
				if ($paySystem['HAVE_PRICE'] === 'Y' && !empty($extPayment))
				{
					$service = PaySystem\Manager::getObjectById($paySystem['ID']);
					if ($service !== null)
					{
						$paySystem['PRICE'] = $service->getPaymentPrice($extPayment);
						$paySystem['PRICE_FORMATTED'] = SaleFormatCurrency($paySystem['PRICE'], $this->order->getCurrency());

						if ($paymentId == $paySystem['ID'])
						{
							$arResult['PAY_SYSTEM_PRICE'] = $extPayment->getField('PRICE_COD');
							$arResult['PAY_SYSTEM_PRICE_FORMATTED'] = SaleFormatCurrency($arResult['PAY_SYSTEM_PRICE'], $this->order->getCurrency());
						}
					}
				}

				$arResult['PAY_SYSTEM'][] = $paySystem;
			}
		}

		$this->executeEvent('OnSaleComponentOrderOneStepPaySystem', $this->order);
	}

	/**
	 * Set related to payment/delivery order properties data from order object to $this->arResult
	 *
	 * @throws Main\ObjectNotFoundException
	 */
	protected function obtainRelatedProperties()
	{
		$arRes = array();
		$arProps = $this->order->getPropertyCollection()->getArray();
		$arPaymentSystemId = $this->order->getPaymentSystemId();
		$arDeliverySystemId = $this->order->getDeliverySystemId();

		foreach ($arProps['properties'] as $key => $property)
		{
			if ($property['UTIL'] == 'Y')
				continue;

			if (isset($property['RELATION']))
			{
				if ($this->checkRelatedProperty($property, $arPaymentSystemId, $arDeliverySystemId))
				{
					if (!empty($this->arResult['PERSON_TYPE'][$property['PERSON_TYPE_ID']]))
					{
						$personType = $this->arResult['PERSON_TYPE'][$property['PERSON_TYPE_ID']];
						$property['PERSON_TYPE_LID'] = $personType['ID'];
						$property['PERSON_TYPE_NAME'] = $personType['NAME'];
						$property['PERSON_TYPE_SORT'] = $personType['SORT'];
						$property['PERSON_TYPE_ACTIVE'] = $personType['ACTIVE'];
					}

					foreach ($arProps['groups'] as $group)
					{
						if ($group['ID'] == $property['PROPS_GROUP_ID'])
						{
							$property['GROUP_NAME'] = $group['NAME'];
							$property['GROUP_ID'] = $group['ID'];
							$property['GROUP_PERSON_TYPE_ID'] = $group['PERSON_TYPE_ID'];
							break;
						}
					}

					$property['SETTINGS'] = array(
						'MINLENGTH' => $property['MINLENGTH'],
						'MAXLENGTH' => $property['MAXLENGTH'],
						'PATTERN' => $property['PATTERN'],
						'MULTILINE' => $property['MULTILINE'],
						'SIZE' => $property['SIZE']
					);
					$property['PAYSYSTEM_ID'] = $property['ID'];
					$property['DELIVERY_ID'] = $property['ID'];

					$arRes[] = $this->getOrderPropFormatted($property);
				}
			}
		}

		$this->arResult["ORDER_PROP"]["RELATED"] = $arRes;
	}

	/**
	 * Set taxes data from order object to $this->arResult
	 */
	protected function obtainTaxes()
	{
		$arResult =& $this->arResult;

		$arResult["USE_VAT"] = $this->order->isUsedVat();
		$arResult["VAT_RATE"] = $this->order->getVatRate();
		$arResult["VAT_SUM"] = $this->order->getVatSum();

		if ($arResult["VAT_SUM"] === null)
			$arResult["VAT_SUM"] = 0;

		$arResult["VAT_SUM_FORMATED"] = SaleFormatCurrency($arResult["VAT_SUM"], $this->order->getCurrency());

		$taxes = $this->order->getTax();
		$taxes->refreshData();

		if ($this->order->isUsedVat())
		{
			if ($this->arParams['SHOW_VAT_PRICE'] === 'Y')
			{
				$arResult['TAX_LIST'] = $taxes->getAvailableList();
			}
		}
		else
		{
			$arResult['TAX_LIST'] = $taxes->getTaxList();
			if (is_array($arResult['TAX_LIST']) && !empty($arResult['TAX_LIST']))
			{
				foreach ($arResult['TAX_LIST'] as $key => &$tax)
				{
					if ($tax['VALUE_MONEY'])
						$tax['VALUE_MONEY_FORMATED'] = SaleFormatCurrency($tax['VALUE_MONEY'], $this->order->getCurrency());
				}
			}
		}

		$arResult['TAX_PRICE'] = $this->order->getTaxPrice();
	}

	/**
	 * Set order total prices data from order object to $this->arResult
	 */
	protected function obtainTotal()
	{
		$arResult =& $this->arResult;

		$locationAltPropDisplayManual = $this->request->get('LOCATION_ALT_PROP_DISPLAY_MANUAL');
		if (!empty($locationAltPropDisplayManual) && is_array($locationAltPropDisplayManual))
		{
			foreach ($locationAltPropDisplayManual as $propId => $switch)
			{
				if (intval($propId))
				{
					$arResult['LOCATION_ALT_PROP_DISPLAY_MANUAL'][intval($propId)] = !!$switch;
				}
			}
		}

		$basket = $this->calculateBasket;

		$arResult['ORDER_PRICE'] = $basket->getPrice();
		$arResult['ORDER_PRICE_FORMATED'] = SaleFormatCurrency($arResult['ORDER_PRICE'], $this->order->getCurrency());

		$arResult['ORDER_WEIGHT'] = $basket->getWeight();
		$arResult['ORDER_WEIGHT_FORMATED'] = roundEx(floatval($arResult['ORDER_WEIGHT'] / $arResult['WEIGHT_KOEF']), SALE_WEIGHT_PRECISION).' '.$arResult['WEIGHT_UNIT'];

		$arResult['PRICE_WITHOUT_DISCOUNT_VALUE'] = $basket->getBasePrice();
		$arResult['PRICE_WITHOUT_DISCOUNT'] = SaleFormatCurrency($arResult['PRICE_WITHOUT_DISCOUNT_VALUE'], $this->order->getCurrency());

		$arResult['DISCOUNT_PRICE'] = Sale\PriceMaths::roundPrecision(
			$this->order->getDiscountPrice() + ($arResult['PRICE_WITHOUT_DISCOUNT_VALUE'] - $arResult['ORDER_PRICE'])
		);
		$arResult['DISCOUNT_PRICE_FORMATED'] = SaleFormatCurrency($arResult['DISCOUNT_PRICE'], $this->order->getCurrency());

		$arResult['DELIVERY_PRICE'] = Sale\PriceMaths::roundPrecision($this->order->getDeliveryPrice());
		$arResult['DELIVERY_PRICE_FORMATED'] = SaleFormatCurrency($arResult['DELIVERY_PRICE'], $this->order->getCurrency());

		$arResult['ORDER_TOTAL_PRICE'] = Sale\PriceMaths::roundPrecision($this->order->getPrice());
		$arResult['ORDER_TOTAL_PRICE_FORMATED'] = SaleFormatCurrency($arResult['ORDER_TOTAL_PRICE'], $this->order->getCurrency());
	}

	/**
	 * Obtains all order fields filled by user.
	 */
	protected function obtainUserConsentInfo()
	{
		$propertyNames = array();

		$propertyIterator = Sale\Internals\OrderPropsTable::getList(array(
			'select' => array('NAME'),
			'filter' => array(
				'ACTIVE' => 'Y',
				'UTIL' => 'N',
				'PERSON_TYPE_SITE.SITE_ID' => $this->getSiteId()
			),
			'order' => array(
				'SORT' => 'ASC',
				'ID' => 'ASC'
			),
			'runtime' => array(
				new \Bitrix\Main\Entity\ReferenceField(
					'PERSON_TYPE_SITE',
					'Bitrix\Sale\Internals\PersonTypeSiteTable',
					array('=this.PERSON_TYPE_ID' => 'ref.PERSON_TYPE_ID')
				),
			)
		));
		while ($property = $propertyIterator->fetch())
		{
			$propertyNames[] = $property['NAME'];
		}

		$this->arResult['USER_CONSENT_PROPERTY_DATA'] = $propertyNames;
	}

	/**
	 * Returns true if current property is valid for selected payment & delivery
	 *
	 * @param $property
	 * @param $arPaymentId
	 * @param $arDeliveryId
	 * @return bool
	 */
	protected function checkRelatedProperty($property, $arPaymentId, $arDeliveryId)
	{
		$okByPs = null;
		$okByDelivery = null;

		if (is_array($property['RELATION']) && !empty($property['RELATION']))
		{
			foreach ($property['RELATION'] as $relation)
			{
				if (empty($okByPs) && $relation['ENTITY_TYPE'] == 'P')
					$okByPs = in_array($relation['ENTITY_ID'], $arPaymentId);

				if (empty($okByDelivery) && $relation['ENTITY_TYPE'] == 'D')
					$okByDelivery = in_array($relation['ENTITY_ID'], $arDeliveryId);
			}
		}

		return ((is_null($okByPs) || $okByPs) && (is_null($okByDelivery) || $okByDelivery));
	}

	/**
	 * Make $arResult compatible ('~' prefixes and htmlspecialcharsEx)
	 */
	protected function makeResultCompatible()
	{
		$arResult =& $this->arResult;

		if (is_array($arResult['PERSON_TYPE']) && !empty($arResult['PERSON_TYPE']))
			foreach ($arResult['PERSON_TYPE'] as &$item)
				self::makeCompatibleArray($item);

		if (is_array($arResult['ORDER_PROP']['RELATED']) && !empty($arResult['ORDER_PROP']['RELATED']))
			foreach ($arResult['ORDER_PROP']['RELATED'] as &$item)
				self::makeCompatibleArray($item);

		if (is_array($arResult['ORDER_PROP']['USER_PROPS_Y']) && !empty($arResult['ORDER_PROP']['USER_PROPS_Y']))
			foreach ($arResult['ORDER_PROP']['USER_PROPS_Y'] as &$item)
				self::makeCompatibleArray($item);

		if (is_array($arResult['ORDER_PROP']['USER_PROPS_N']) && !empty($arResult['ORDER_PROP']['USER_PROPS_N']))
			foreach ($arResult['ORDER_PROP']['USER_PROPS_N'] as &$item)
				self::makeCompatibleArray($item);

		if (is_array($arResult['BASKET_ITEMS']) && !empty($arResult['BASKET_ITEMS']))
			foreach ($arResult['BASKET_ITEMS'] as &$item)
				self::makeCompatibleArray($item);

		if (is_array($arResult['GRID']['ROWS']) && !empty($arResult['GRID']['ROWS']))
			foreach ($arResult['GRID']['ROWS'] as &$item)
				self::makeCompatibleArray($item['data']);

		if (is_array($arResult['USER_ACCOUNT']) && !empty($arResult['USER_ACCOUNT']))
			self::makeCompatibleArray($arResult['USER_ACCOUNT']);
	}

	public static function makeCompatibleArray(&$array)
	{
		if (empty($array) || !is_array($array))
			return;

		$arr = array();
		foreach ($array as $key => $value)
		{
			if (is_array($value) || preg_match("/[;&<>\"]/", $value))
			{
				$arr[$key] = htmlspecialcharsEx($value);
			}
			else
			{
				$arr[$key] = $value;
			}

			$arr["~{$key}"] = $value;
		}

		$array = $arr;
	}

	/**
	 * Check if PayPal prepayment is available
	 *
	 * @param Order $order
	 * @throws Main\ArgumentException
	 * @throws Main\NotSupportedException
	 */
	protected function usePrepayment(Order $order)
	{
		global $APPLICATION;
		$arResult =& $this->arResult;

		$prePayablePs = array();
		$personTypes = array_keys(PersonType::load($this->context->getSite()));

		if (!empty($personTypes))
		{
			$paySysActionIterator = PaySystem\Manager::getList(array(
				'select' => array(
					'ID', 'PAY_SYSTEM_ID', 'PERSON_TYPE_ID', 'NAME', 'ACTION_FILE', 'RESULT_FILE',
					'NEW_WINDOW', 'PARAMS', 'ENCODING', 'LOGOTIP'
				),
				'filter'  => array(
					'ACTIVE' => 'Y',
					'HAVE_PREPAY' => 'Y'
				)
			));
			$helper = Main\Application::getConnection()->getSqlHelper();

			while ($paySysAction = $paySysActionIterator->fetch())
			{
				$dbRestriction = Sale\Internals\ServiceRestrictionTable::getList(array(
					'select' => array('PARAMS'),
					'filter' => array(
						'SERVICE_ID' => $paySysAction['ID'],
						'CLASS_NAME' => $helper->forSql('\Bitrix\Sale\Services\PaySystem\Restrictions\PersonType'),
						'SERVICE_TYPE' => Sale\Services\PaySystem\Restrictions\Manager::SERVICE_TYPE_PAYMENT
					)
				));

				if ($restriction = $dbRestriction->fetch())
				{
					if (array_intersect($personTypes, $restriction['PARAMS']['PERSON_TYPE_ID']))
					{
						$prePayablePs = $paySysAction;
						break;
					}
				}
				else
				{
					$prePayablePs = $paySysAction;
					break;
				}
			}

			if ($prePayablePs)
			{
				$arResult['PREPAY_PS'] = $prePayablePs;
				$arResult['HAVE_PREPAYMENT'] = true;

				$this->prePaymentService = new PaySystem\Service($prePayablePs);
				if ($this->prePaymentService->isPrePayable())
				{
					$this->prePaymentService->initPrePayment(null, $this->request);

					if ($this->request->get('paypal') == 'Y' && $this->request->get('token'))
					{
						$arResult['PREPAY_ORDER_PROPS'] = $this->prePaymentService->getPrePaymentProps();

						if (intval($this->arUserResult['PAY_SYSTEM_ID']) <= 0)
						{
							$this->arUserResult['PERSON_TYPE_ID'] = $arResult['PREPAY_PS']['PERSON_TYPE_ID'];
						}

						$this->arUserResult['PREPAYMENT_MODE'] = true;
						$this->arUserResult['PAY_SYSTEM_ID'] = $arResult['PREPAY_PS']['ID'];
					}
					else
					{
						if ($this->arUserResult['PAY_SYSTEM_ID'] == $arResult['PREPAY_PS']['ID'])
						{
							$basketItems = array();
							/** @var Sale\BasketItem $item */
							foreach ($this->getBasketStorage()->getBasket() as $key => $item)
							{
								if ($item->canBuy() && !$item->isDelay())
								{
									$basketItems[$key]['NAME'] = $item->getField('NAME');
									$basketItems[$key]['PRICE'] = $item->getPrice();
									$basketItems[$key]['QUANTITY'] = $item->getQuantity();
								}
							}

							$orderData = array(
								'PATH_TO_ORDER' => $APPLICATION->GetCurPage(),
								'AMOUNT' => $order->getPrice(),
								'ORDER_REQUEST' => 'Y',
								'BASKET_ITEMS' => $basketItems
							);
							$arResult['REDIRECT_URL'] = $this->prePaymentService->basketButtonAction($orderData);

							if ($arResult['REDIRECT_URL'] != '')
							{
								$arResult['NEED_REDIRECT'] = 'Y';
							}
						}
					}

					ob_start();
					$this->prePaymentService->setTemplateParams(array(
						'TOKEN' => $this->request->get('token'),
						'PAYER_ID' => $this->request->get('PayerID')
					));
					$this->prePaymentService->showTemplate(null, 'prepay_hidden_fields');
					$arResult['PREPAY_ADIT_FIELDS'] = ob_get_contents();
					ob_end_clean();
				}
			}
		}
	}

	protected function prepayOrder()
	{
		if ($this->prePaymentService && $this->prePaymentService->isPrePayable() && $this->request->get('paypal') == 'Y')
		{
			/** @var Payment $payment */
			$payment = $this->getExternalPayment($this->order);
			if($payment)
			{
				$this->prePaymentService->setOrderDataForPrePayment(
					array(
						'ORDER_ID' => $this->order->getId(),
						'PAYMENT_ID' => $payment->getId(),
						'ORDER_PRICE' => $payment->getSum(),
						'DELIVERY_PRICE' => $this->order->getDeliveryPrice(),
						'TAX_PRICE' => $this->order->getTaxPrice()
					)
				);

				$orderData = array();
				/** @var Sale\BasketItem $item */
				foreach ($this->order->getBasket() as $item)
					$orderData['BASKET_ITEMS'][] = $item->getFieldValues();

				$this->prePaymentService->payOrderByPrePayment($orderData);
			}
		}
	}

	/**
	 * Initialization of person types. Set person type data to $this->arResult.
	 * Return true if person type changed.
	 * Execution of 'OnSaleComponentOrderOneStepPersonType' event
	 *
	 * @param Order $order
	 * @return bool
	 * @throws Main\ArgumentException
	 */
	protected function initPersonType(Order $order)
	{
		$arResult =& $this->arResult;
		$personTypeId = intval($this->arUserResult['PERSON_TYPE_ID']);
		$personTypeIdOld = intval($this->arUserResult['PERSON_TYPE_OLD']);

		$personTypes = PersonType::load($this->context->getSite());
		foreach ($personTypes as $personType)
		{
			if ($personTypeId === intval($personType["ID"]) || !array_key_exists($personTypeId, $personTypes))
			{
				$personTypeId = intval($personType["ID"]);
				$order->setPersonTypeId($personTypeId);
				$this->arUserResult['PERSON_TYPE_ID'] = $personTypeId;
				$personType["CHECKED"] = "Y";
			}
			$arResult["PERSON_TYPE"][$personType["ID"]] = $personType;
		}

		if ($personTypeId == 0)
			$this->addError(Loc::getMessage("SOA_ERROR_PERSON_TYPE"), self::REGION_BLOCK);

		$this->executeEvent('OnSaleComponentOrderOneStepPersonType', $order);

		return count($arResult["PERSON_TYPE"]) > 1 && ($personTypeId !== $personTypeIdOld);
	}

	/**
	 * Initialization of user profiles. Set user profiles data to $this->arResult.
	 *
	 * @param Order $order
	 * @param       $isPersonTypeChanged
	 */
	protected function initUserProfiles(Order $order, $isPersonTypeChanged)
	{
		$arResult =& $this->arResult;

		$justAuthorized = $this->request->get('do_authorize') == 'Y' || $this->request->get('do_register') == 'Y';
		$bFirst = false;
		$dbUserProfiles = CSaleOrderUserProps::GetList(
			array('DATE_UPDATE' => 'DESC'),
			array(
				'PERSON_TYPE_ID' => $order->getPersonTypeId(),
				'USER_ID' => $order->getUserId()
			)
		);
		while ($arUserProfiles = $dbUserProfiles->GetNext())
		{
			if (!$bFirst && (empty($this->arUserResult['PROFILE_CHANGE']) || $isPersonTypeChanged || $justAuthorized))
			{
				$bFirst = true;
				$this->arUserResult['PROFILE_ID'] = (int)$arUserProfiles['ID'];
			}

			if ((int)$this->arUserResult['PROFILE_ID'] === (int)$arUserProfiles['ID'])
			{
				$arUserProfiles['CHECKED'] = 'Y';
			}

			$arResult['ORDER_PROP']['USER_PROFILES'][$arUserProfiles['ID']] = $arUserProfiles;
		}
	}

	public function getCurrentShipment(Order $order)
	{
		/** @var Shipment $shipment */
		foreach ($order->getShipmentCollection() as $shipment)
		{
			if (!$shipment->isSystem())
				return $shipment;
		}

		return null;
	}

	/**
	 * Initialization of shipment object with first/selected delivery service.
	 *
	 * @param Shipment $shipment
	 * @throws Main\NotSupportedException
	 */
	protected function initDelivery(Shipment $shipment)
	{
		$deliveryId = intval($this->arUserResult['DELIVERY_ID']);
		$this->arDeliveryServiceAll = Delivery\Services\Manager::getRestrictedObjectsList($shipment);
		/** @var Sale\ShipmentCollection $shipmentCollection */
		$shipmentCollection = $shipment->getCollection();
		$order = $shipmentCollection->getOrder();

		if (!empty($this->arDeliveryServiceAll))
		{
			if (isset($this->arDeliveryServiceAll[$deliveryId]))
			{
				$deliveryObj = $this->arDeliveryServiceAll[$deliveryId];
			}
			else
			{
				$deliveryObj = reset($this->arDeliveryServiceAll);

				if (!empty($deliveryId))
				{
					$this->addWarning(Loc::getMessage("DELIVERY_CHANGE_WARNING"), self::DELIVERY_BLOCK);
				}

				$deliveryId = $deliveryObj->getId();
			}

			if ($deliveryObj->isProfile())
			{
				$name = $deliveryObj->getNameWithParent();
			}
			else
			{
				$name = $deliveryObj->getName();
			}

			$order->isStartField();

			$shipment->setFields(array(
				'DELIVERY_ID' => $deliveryId,
				'DELIVERY_NAME' => $name,
				'CURRENCY' => $order->getCurrency()
			));
			$this->arUserResult['DELIVERY_ID'] = $deliveryId;

			$deliveryStoreList = Delivery\ExtraServices\Manager::getStoresList($deliveryId);
			if (!empty($deliveryStoreList))
			{
				if ($this->arUserResult['BUYER_STORE'] <= 0 || !in_array($this->arUserResult['BUYER_STORE'], $deliveryStoreList))
				{
					$this->arUserResult['BUYER_STORE'] = current($deliveryStoreList);
				}

				$shipment->setStoreId($this->arUserResult['BUYER_STORE']);
			}

			$deliveryExtraServices = $this->arUserResult['DELIVERY_EXTRA_SERVICES'];
			if (is_array($deliveryExtraServices) && !empty($deliveryExtraServices[$deliveryId]))
			{
				$shipment->setExtraServices($deliveryExtraServices[$deliveryId]);
				$deliveryObj->getExtraServices()->setValues($deliveryExtraServices[$deliveryId]);
			}

			$shipmentCollection->calculateDelivery();

			$order->doFinalAction(true);
		}
		else
		{
			$service = Delivery\Services\Manager::getById(Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId());
			$shipment->setFields(array(
				'DELIVERY_ID' => $service['ID'],
				'DELIVERY_NAME' => $service['NAME'],
				'CURRENCY' => $order->getCurrency()
			));
		}
	}

	protected function loadUserAccount(Order $order)
	{
		if (!isset($this->arResult["USER_ACCOUNT"]))
		{
			$dbUserAccount = CSaleUserAccount::GetList(
				array(),
				array(
					"USER_ID" => $order->getUserId(),
					"CURRENCY" => $order->getCurrency(),
				)
			);
			$this->arResult["USER_ACCOUNT"] = $dbUserAccount->Fetch();
		}
	}

	/**
	 * Set user budget data to $this->arResult. Returns sum to spend(including restrictions).
	 *
	 * @param Order $order
	 * @param bool  $recalculate
	 * @return array
	 * @throws Main\ObjectNotFoundException
	 */
	protected function getInnerPaySystemInfo(Order $order, $recalculate = false)
	{
		$arResult =& $this->arResult;

		$sumToSpend = 0;
		$arPaySystemServices = array();

		if ($this->arParams['PAY_FROM_ACCOUNT'] === 'Y' && $order->isAllowPay())
		{
			$innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();
			$innerPayment = $order->getPaymentCollection()->getInnerPayment();

			if (!$innerPayment)
			{
				$innerPayment = $order->getPaymentCollection()->createInnerPayment();
			}

			$this->loadUserAccount($order);
			$userBudget = (float)$arResult['USER_ACCOUNT']['CURRENT_BUDGET'];

			// finding correct inner pay system price ranges to setField()
			$sumRange = Sale\Services\PaySystem\Restrictions\Manager::getPriceRange($innerPayment, $innerPaySystemId);
			if (!empty($sumRange))
			{
				if (
					(empty($sumRange['MIN']) || $sumRange['MIN'] <= $userBudget)
					&& (empty($sumRange['MAX']) || $sumRange['MAX'] >= $userBudget)
				)
				{
					$sumToSpend = $userBudget;
				}

				if (!empty($sumRange['MAX']) && $sumRange['MAX'] <= $userBudget)
				{
					$sumToSpend = $sumRange['MAX'];
				}
			}
			else
			{
				$sumToSpend = $userBudget;
			}

			$sumToSpend = $sumToSpend >= $order->getPrice() ? $order->getPrice() : $sumToSpend;

			if ($this->arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] === 'Y' && $sumToSpend < $order->getPrice())
			{
				$sumToSpend = 0;
			}

			if (!empty($arResult['USER_ACCOUNT']) && $sumToSpend > 0)
			{
				// setting inner payment price
				$innerPayment->setField('SUM', $sumToSpend);
				// getting allowed pay systems by restrictions
				$arPaySystemServices = PaySystem\Manager::getListWithRestrictions($innerPayment);
				// delete inner pay system if restrictions has not passed
				if (!isset($arPaySystemServices[$innerPaySystemId]))
				{
					$innerPayment->delete();
					$sumToSpend = 0;
				}
			}
			else
			{
				$innerPayment->delete();
			}
		}

		if ($sumToSpend > 0)
		{
			$arResult['PAY_FROM_ACCOUNT'] = 'Y';
			$arResult['CURRENT_BUDGET_FORMATED'] = SaleFormatCurrency($arResult['USER_ACCOUNT']['CURRENT_BUDGET'], $order->getCurrency());
		}
		else
		{
			$arResult['PAY_FROM_ACCOUNT'] = 'N';
			unset($arResult['CURRENT_BUDGET_FORMATED']);
		}

		return array($sumToSpend, $arPaySystemServices);
	}

	public function getInnerPayment(Order $order)
	{
		/** @var Payment $payment */
		foreach ($order->getPaymentCollection() as $payment)
		{
			if ($payment->getPaymentSystemId() == PaySystem\Manager::getInnerPaySystemId())
				return $payment;
		}

		return null;
	}

	public function getExternalPayment(Order $order)
	{
		/** @var Payment $payment */
		foreach ($order->getPaymentCollection() as $payment)
		{
			if ($payment->getPaymentSystemId() != PaySystem\Manager::getInnerPaySystemId())
				return $payment;
		}

		return null;
	}

	protected function showOnlyPrepaymentPs($paySystemId)
	{
		if (empty($this->arPaySystemServiceAll) || intval($paySystemId) == 0)
			return;

		foreach ($this->arPaySystemServiceAll as $key => $psService)
		{
			if ($paySystemId != $psService['ID'])
			{
				unset($this->arPaySystemServiceAll[$key]);
				unset($this->arActivePaySystems[$key]);
			}
		}
	}

	/**
	 * Initialization of inner/external payment objects with first/selected pay system services.
	 *
	 * @param Order $order
	 * @throws Main\ObjectNotFoundException
	 */
	protected function initPayment(Order $order)
	{
		list($sumToSpend, $innerPaySystemList) = $this->getInnerPaySystemInfo($order);

		if ($sumToSpend > 0)
		{
			$innerPayment = $this->getInnerPayment($order);
			if (!empty($innerPayment))
			{
				if ($this->arUserResult['PAY_CURRENT_ACCOUNT'] === 'Y')
				{
					$innerPayment->setField('SUM', $sumToSpend);
				}
				else
				{
					$innerPayment->delete();
					$innerPayment = null;
				}

				$this->arPaySystemServiceAll = $this->arActivePaySystems = $innerPaySystemList;
			}
		}

		$innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();
		$extPaySystemId = (int)$this->arUserResult['PAY_SYSTEM_ID'];

		$paymentCollection = $order->getPaymentCollection();
		$remainingSum = $order->getPrice() - $paymentCollection->getSum();
		if ($remainingSum > 0 || $order->getPrice() == 0)
		{
			/** @var Payment $extPayment */
			$extPayment = $paymentCollection->createItem();
			$extPayment->setField('SUM', $remainingSum);

			$extPaySystemList = PaySystem\Manager::getListWithRestrictions($extPayment);

			// we already checked restrictions for inner pay system (could be different by price restrictions)
			if (empty($innerPaySystemList[$innerPaySystemId]))
			{
				unset($extPaySystemList[$innerPaySystemId]);
			}
			elseif (empty($extPaySystemList[$innerPaySystemId]))
			{
				$extPaySystemList[$innerPaySystemId] = $innerPaySystemList[$innerPaySystemId];
			}

			$this->arPaySystemServiceAll = $this->arActivePaySystems = $extPaySystemList;

			if ($extPaySystemId !== 0 && array_key_exists($extPaySystemId, $this->arPaySystemServiceAll))
			{
				$selectedPaySystem = $this->arPaySystemServiceAll[$extPaySystemId];
			}
			else
			{
				reset($this->arPaySystemServiceAll);

				if (key($this->arPaySystemServiceAll) == $innerPaySystemId)
				{
					if (count($this->arPaySystemServiceAll) > 1)
					{
						next($this->arPaySystemServiceAll);
					}
					elseif ($sumToSpend > 0)
					{
						$extPayment->delete();
						$extPayment = null;

						/** @var Payment $innerPayment */
						$innerPayment = $this->getInnerPayment($order);
						if (empty($innerPayment))
						{
							$innerPayment = $paymentCollection->getInnerPayment();
							if (!$innerPayment)
							{
								$innerPayment = $paymentCollection->createInnerPayment();
							}
						}

						$sumToPay = $remainingSum > $sumToSpend ? $sumToSpend : $remainingSum;
						$innerPayment->setField('SUM', $sumToPay);
					}
					else
					{
						unset($this->arActivePaySystems[$innerPaySystemId]);
						unset($this->arPaySystemServiceAll[$innerPaySystemId]);
					}
				}

				$selectedPaySystem = current($this->arPaySystemServiceAll);

				if (!empty($selectedPaySystem) && $extPaySystemId != 0)
				{
					$this->addWarning(Loc::getMessage('PAY_SYSTEM_CHANGE_WARNING'), self::PAY_SYSTEM_BLOCK);
				}
			}

			if (!empty($selectedPaySystem))
			{
				if ($selectedPaySystem['ID'] != $innerPaySystemId)
				{
					$extPayment->setFields(array(
						'PAY_SYSTEM_ID' => $selectedPaySystem['ID'],
						'PAY_SYSTEM_NAME' => $selectedPaySystem['NAME']
					));

					$this->arUserResult['PAY_SYSTEM_ID'] = $selectedPaySystem['ID'];
				}
			}
			elseif (!empty($extPayment))
			{
				$extPayment->delete();
				$extPayment = null;
			}
		}

		if (empty($this->arPaySystemServiceAll))
		{
			$this->addError(Loc::getMessage('SOA_ERROR_PAY_SYSTEM'), self::PAY_SYSTEM_BLOCK);
		}

		if (!empty($this->arUserResult['PREPAYMENT_MODE']))
		{
			$this->showOnlyPrepaymentPs($this->arUserResult['PAY_SYSTEM_ID']);
		}
	}

	/**
	 * Recalculates payment prices which could change due to shipment/discounts.
	 *
	 * @param Order $order
	 * @throws Main\ObjectNotFoundException
	 */
	protected function recalculatePayment(Order $order)
	{
		// one more delivery calculation for some cases when payment affects delivery price
		if ($this->arParams['DELIVERY_TO_PAYSYSTEM'] === 'd2p')
		{
			$res = $order->getShipmentCollection()->calculateDelivery();
			if (!$res->isSuccess())
			{
				$shipment = $this->getCurrentShipment($order);
				if (!empty($shipment))
				{
					$errMessages = '';
					$errors = $res->getErrorMessages();

					if (!empty($errors))
					{
						foreach ($errors as $message)
						{
							$errMessages .= $message.'<br />';
						}
					}
					else
					{
						$errMessages = Loc::getMessage('SOA_DELIVERY_CALCULATE_ERROR');
					}

					$r = new Result();
					$r->addError(new Sale\ResultWarning(
						$errMessages,
						'SALE_DELIVERY_CALCULATE_ERROR'
					));

					Sale\EntityMarker::addMarker($order, $shipment, $r);
					$shipment->setField('MARKED', 'Y');
				}
			}
		}

		list($sumToSpend, $innerPaySystemList) = $this->getInnerPaySystemInfo($order, true);

		$innerPayment = $this->getInnerPayment($order);
		if (!empty($innerPayment))
		{
			if ($this->arUserResult['PAY_CURRENT_ACCOUNT'] === 'Y' && $sumToSpend > 0)
			{
				$innerPayment->setField('SUM', $sumToSpend);
			}
			else
			{
				$innerPayment->delete();
				$innerPayment = null;
			}

			if ($sumToSpend > 0)
			{
				$this->arPaySystemServiceAll = $innerPaySystemList;
				$this->arActivePaySystems += $innerPaySystemList;
			}
		}

		/** @var Payment $innerPayment */
		$innerPayment = $this->getInnerPayment($order);
		/** @var Payment $extPayment */
		$extPayment = $this->getExternalPayment($order);

		$remainingSum = empty($innerPayment) ? $order->getPrice() : $order->getPrice() - $innerPayment->getSum();
		if ($remainingSum > 0 || $order->getPrice() == 0)
		{
			$paymentCollection = $order->getPaymentCollection();
			$innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();
			$extPaySystemId = (int)$this->arUserResult['PAY_SYSTEM_ID'];

			if (empty($extPayment))
			{
				$extPayment = $paymentCollection->createItem();
			}

			$extPayment->setField('SUM', $remainingSum);

			$extPaySystemList = PaySystem\Manager::getListWithRestrictions($extPayment);
			// we already checked restrictions for inner pay system (could be different by price restrictions)
			if (empty($innerPaySystemList[$innerPaySystemId]))
			{
				unset($extPaySystemList[$innerPaySystemId]);
			}
			elseif (empty($extPaySystemList[$innerPaySystemId]))
			{
				$extPaySystemList[$innerPaySystemId] = $innerPaySystemList[$innerPaySystemId];
			}

			$this->arPaySystemServiceAll = $extPaySystemList;
			$this->arActivePaySystems += $extPaySystemList;

			if ($extPaySystemId !== 0 && array_key_exists($extPaySystemId, $this->arPaySystemServiceAll))
			{
				$selectedPaySystem = $this->arPaySystemServiceAll[$extPaySystemId];
			}
			else
			{
				reset($this->arPaySystemServiceAll);

				if (key($this->arPaySystemServiceAll) == $innerPaySystemId)
				{
					if (count($this->arPaySystemServiceAll) > 1)
					{
						next($this->arPaySystemServiceAll);
					}
					elseif ($sumToSpend > 0)
					{
						$extPayment->delete();
						$extPayment = null;

						/** @var Payment $innerPayment */
						$innerPayment = $this->getInnerPayment($order);
						if (empty($innerPayment))
						{
							$innerPayment = $paymentCollection->getInnerPayment();
							if (!$innerPayment)
							{
								$innerPayment = $paymentCollection->createInnerPayment();
							}
						}

						$sumToPay = $remainingSum > $sumToSpend ? $sumToSpend : $remainingSum;
						$innerPayment->setField('SUM', $sumToPay);

						if ($order->getPrice() - $paymentCollection->getSum() > 0)
						{
							$this->addWarning(Loc::getMessage('INNER_PAYMENT_BALANCE_ERROR'), self::PAY_SYSTEM_BLOCK);

							$r = new Result();
							$r->addError(new Sale\ResultWarning(
								Loc::getMessage('INNER_PAYMENT_BALANCE_ERROR'),
								'SALE_INNER_PAYMENT_BALANCE_ERROR'
							));

							Sale\EntityMarker::addMarker($order, $innerPayment, $r);
							$innerPayment->setField('MARKED', 'Y');
						}
					}
					else
					{
						unset($this->arActivePaySystems[$innerPaySystemId]);
						unset($this->arPaySystemServiceAll[$innerPaySystemId]);
					}
				}

				$selectedPaySystem = current($this->arPaySystemServiceAll);

				if (!empty($selectedPaySystem) && $extPaySystemId != 0)
				{
					$this->addWarning(Loc::getMessage('PAY_SYSTEM_CHANGE_WARNING'), self::PAY_SYSTEM_BLOCK);
				}
			}

			if (!array_key_exists((int)$selectedPaySystem['ID'], $this->arPaySystemServiceAll))
			{
				$this->addError(Loc::getMessage('P2D_CALCULATE_ERROR'), self::PAY_SYSTEM_BLOCK);
				$this->addError(Loc::getMessage('P2D_CALCULATE_ERROR'), self::DELIVERY_BLOCK);
			}

			if (!empty($selectedPaySystem))
			{
				if ($selectedPaySystem['ID'] != $innerPaySystemId)
				{
					$codSum = 0;
					$service = PaySystem\Manager::getObjectById($selectedPaySystem['ID']);
					if ($service !== null)
					{
						$codSum = $service->getPaymentPrice($extPayment);
					}

					$extPayment->setFields(array(
						'PAY_SYSTEM_ID' => $selectedPaySystem['ID'],
						'PAY_SYSTEM_NAME' => $selectedPaySystem['NAME'],
						'PRICE_COD' => $codSum
					));

					$this->arUserResult['PAY_SYSTEM_ID'] = $selectedPaySystem['ID'];
				}
			}
			elseif (!empty($extPayment))
			{
				$extPayment->delete();
				$extPayment = null;
			}

			if (!empty($this->arUserResult['PREPAYMENT_MODE']))
			{
				$this->showOnlyPrepaymentPs($this->arUserResult['PAY_SYSTEM_ID']);
			}
		}

		if (!empty($innerPayment) && !empty($extPayment) && $remainingSum == 0)
		{
			$extPayment->delete();
			$extPayment = null;
		}
	}

	/**
	 * Calculates all available deliveries for order object.
	 * Uses cloned order not to harm real order.
	 * Execution of 'OnSaleComponentOrderDeliveriesCalculated' event
	 *
	 * @param Order $order
	 * @throws Main\NotSupportedException
	 */
	protected function calculateDeliveries(Order $order)
	{
		$this->arResult['DELIVERY'] = array();
		$problemDeliveries = array();

		if (!empty($this->arDeliveryServiceAll))
		{
			/** @var Order $orderClone */
			$orderClone = null;
			$anotherDeliveryCalculated = false;
			/** @var Shipment $shipment */
			$shipment = $this->getCurrentShipment($order);

			foreach ($this->arDeliveryServiceAll as $deliveryId => $deliveryObj)
			{
				$calcResult = false;
				$calcOrder = false;
				$arDelivery = array();

				if ((int)$shipment->getDeliveryId() === $deliveryId)
				{
					$arDelivery['CHECKED'] = 'Y';
					$mustBeCalculated = true;
					$calcResult = $deliveryObj->calculate($shipment);
					$calcOrder = $order;
				}
				else
				{
					$mustBeCalculated = $this->arParams['DELIVERY_NO_AJAX'] === 'Y'
						|| ($this->arParams['DELIVERY_NO_AJAX'] === 'H' && $deliveryObj->isCalculatePriceImmediately());

					if ($mustBeCalculated)
					{
						$anotherDeliveryCalculated = true;

						if (empty($orderClone))
						{
							$orderClone = $this->getOrderClone($order);
						}

						$orderClone->isStartField();

						$clonedShipment = $this->getCurrentShipment($orderClone);
						$clonedShipment->setField('DELIVERY_ID', $deliveryId);

						$calculationResult = $orderClone->getShipmentCollection()->calculateDelivery();
						if ($calculationResult->isSuccess())
						{
							$calcDeliveries = $calculationResult->get('CALCULATED_DELIVERIES');
							$calcResult = reset($calcDeliveries);
						}

						if (empty($calcResult))
						{
							$calcResult = new Delivery\CalculationResult();
						}

						$orderClone->doFinalAction(true);

						$calcOrder = $orderClone;
					}
				}

				if ($mustBeCalculated)
				{
					if ($calcResult->isSuccess())
					{
						$arDelivery['PRICE'] = Sale\PriceMaths::roundPrecision($calcResult->getPrice());
						$arDelivery['PRICE_FORMATED'] = SaleFormatCurrency($arDelivery['PRICE'], $calcOrder->getCurrency());

						$currentCalcDeliveryPrice = Sale\PriceMaths::roundPrecision($calcOrder->getDeliveryPrice());
						if ($currentCalcDeliveryPrice >= 0 && $arDelivery['PRICE'] != $currentCalcDeliveryPrice)
						{
							$arDelivery['DELIVERY_DISCOUNT_PRICE'] = $currentCalcDeliveryPrice;
							$arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED'] = SaleFormatCurrency($arDelivery['DELIVERY_DISCOUNT_PRICE'], $calcOrder->getCurrency());
						}

						if (strlen($calcResult->getPeriodDescription()) > 0)
						{
							$arDelivery['PERIOD_TEXT'] = $calcResult->getPeriodDescription();
						}
					}
					else
					{
						if (count($calcResult->getErrorMessages()) > 0)
						{
							foreach ($calcResult->getErrorMessages() as $message)
							{
								$arDelivery['CALCULATE_ERRORS'] .= $message.'<br>';
							}
						}
						else
						{
							$arDelivery['CALCULATE_ERRORS'] = Loc::getMessage('SOA_DELIVERY_CALCULATE_ERROR');
						}


						if ($arDelivery['CHECKED'] !== 'Y')
						{
							if ($this->arParams['SHOW_NOT_CALCULATED_DELIVERIES'] === 'N')
							{
								unset($this->arDeliveryServiceAll[$deliveryId]);
								continue;
							}
							elseif ($this->arParams['SHOW_NOT_CALCULATED_DELIVERIES'] === 'L')
							{
								$problemDeliveries[$deliveryId] = $arDelivery;
								continue;
							}
						}
					}

					$arDelivery['CALCULATE_DESCRIPTION'] = $calcResult->getDescription();
				}

				$this->arResult['DELIVERY'][$deliveryId] = $arDelivery;
			}

			// for discounts: last delivery calculation need to be on real order with selected delivery
			if ($anotherDeliveryCalculated)
			{
				$order->doFinalAction(true);
			}
		}

		if (!empty($problemDeliveries))
		{
			$this->arResult['DELIVERY'] += $problemDeliveries;
		}

		$eventParameters = array(
			$order, &$this->arUserResult, $this->request,
			&$this->arParams, &$this->arResult, &$this->arDeliveryServiceAll, &$this->arPaySystemServiceAll
		);
		foreach (GetModuleEvents('sale', 'OnSaleComponentOrderDeliveriesCalculated', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, $eventParameters);
	}

	/**
	 * @param Order $order
	 *
	 * @return Order
	 */
	protected function getOrderClone(Order $order)
	{
		/** @var Order $orderClone */
		$orderClone = $order->createClone();

		$clonedShipment = $this->getCurrentShipment($orderClone);
		if (!empty($clonedShipment))
		{
			$clonedShipment->setField('CUSTOM_PRICE_DELIVERY', 'N');
		}

		return $orderClone;
	}

	/**
	 * Check required fields for actual properties(with/without relations). Set user description.
	 *
	 * @param Order $order
	 * @throws Main\ObjectNotFoundException
	 */
	protected function initOrderFields(Order $order)
	{
		if ($this->isOrderConfirmed)
		{
			$actualProperties = array();
			$paymentSystemIds = $order->getPaymentSystemId();
			$deliverySystemIds = $order->getDeliverySystemId();
			$propertyCollection = $order->getPropertyCollection();
			/** @var Sale\PropertyValue $property */
			foreach ($propertyCollection as $property)
			{
				if ($property->isUtil())
					continue;

				$arProperty = $property->getProperty();
				if (isset($arProperty['RELATION'])
					&& !$this->checkRelatedProperty($arProperty, $paymentSystemIds, $deliverySystemIds)
				)
				{
					unset($this->arUserResult['ORDER_PROP'][$property->getPropertyId()]);
					continue;
				}

				$actualProperties[$property->getPropertyId()] = $this->arUserResult['ORDER_PROP'][$property->getPropertyId()];
			}

			$res = $propertyCollection->checkRequired(array_keys($actualProperties), array('PROPERTIES' => $actualProperties));
			if (!$res->isSuccess())
				$this->addError($res, self::PROPERTY_BLOCK);
		}

		$order->setField("USER_DESCRIPTION", $this->arUserResult['ORDER_DESCRIPTION']);
		$order->setField('COMPANY_ID', Company\Manager::getAvailableCompanyIdByEntity($order));

		if ($order->getField('COMPANY_ID') > 0)
		{
			$responsibleGroups = Sale\Internals\CompanyResponsibleGroupTable::getCompanyGroups($order->getField('COMPANY_ID'));
			if (!empty($responsibleGroups) && is_array($responsibleGroups))
			{
				$usersList = array();
				foreach ($responsibleGroups as $groupId)
				{
					$usersList = array_merge($usersList, CGroup::GetGroupUser($groupId));
				}

				if (!empty($usersList) && is_array($usersList))
				{
					$usersList = array_unique($usersList);
					$responsibleUserId = $usersList[array_rand($usersList)];

					/** @var Main\Entity\Event $event */
					$event = new Main\Event('sale', 'OnSaleComponentBeforeOrderSetResponsibleUserId', array(
						'ENTITY' => $order,
						'VALUE' => $responsibleUserId,
					));
					$event->send();

					if ($event->getResults())
					{
						$result = new Result();
						/** @var Main\EventResult $eventResult */
						foreach($event->getResults() as $eventResult)
						{
							if($eventResult->getType() == Main\EventResult::SUCCESS)
							{
								if ($eventResultData = $eventResult->getParameters())
								{
									if (isset($eventResultData['VALUE']) && $eventResultData['VALUE'] != $responsibleUserId)
									{
										$responsibleUserId = $eventResultData['VALUE'];
									}
								}
							}
						}
					}

					$order->setField('RESPONSIBLE_ID', $responsibleUserId);
				}

			}
		}

	}

	/**
	 * Ajax action - recalculate order and send JSON answer with data/errors
	 */
	protected function refreshOrderAjaxAction()
	{
		global $USER;

		$error = false;
		$this->request->set($this->request->get('order'));
		if ($this->checkSession)
		{
			$this->order = $this->createOrder($USER->GetID() ? $USER->GetID() : CSaleUser::GetAnonymousUserID());
			$this->prepareResultArray();
			self::scaleImages($this->arResult['JS_DATA'], $this->arParams['SERVICES_IMAGES_SCALING']);
		}
		else
			$error = Loc::getMessage('SESSID_ERROR');

		$this->showAjaxAnswer(array(
			'order' => $this->arResult['JS_DATA'],
			'locations' => $this->arResult['LOCATIONS'],
			'error' => $error
		));
	}

	/**
	 * Returns true if basket quantity list is equal to basket "before refresh" state
	 *
	 * @param Order $order
	 * @return bool
	 */
	protected function checkOrderConsistency(Order $order)
	{
		return $this->getActualQuantityList($order->getBasket()) === $this->arUserResult['QUANTITY_LIST'];
	}

	/**
	 * Ajax action - attempt to save order and send JSON answer with data/errors
	 */
	protected function saveOrderAjaxAction()
	{
		global $USER;

		$arOrderRes = array();
		if ($this->checkSession)
		{
			$this->isOrderConfirmed = true;
			$needToRegister = !$USER->IsAuthorized() && $this->arParams["ALLOW_AUTO_REGISTER"] == "Y";
			$saveToSession = false;

			if ($needToRegister)
			{
				list($userId, $saveToSession) = $this->autoRegisterUser();
			}
			else
			{
				$userId = $USER->GetID() ? $USER->GetID() : CSaleUser::GetAnonymousUserID();
			}

			$this->order = $this->createOrder($userId);

			$isActiveUser = intval($userId) > 0 && $userId != CSaleUser::GetAnonymousUserID();

			if ($isActiveUser && empty($this->arResult['ERROR']))
			{
				if (!$this->checkOrderConsistency($this->order))
				{
					$r = new Result();
					$r->addError(new Sale\ResultWarning(
						Loc::getMessage('ORDER_CONSISTENCY_CHANGED'),
						'SALE_ORDER_CONSISTENCY_CHANGED_ERROR'
					));

					Sale\EntityMarker::addMarker($this->order, $this->order, $r);
					$this->order->setField('MARKED', 'Y');
				}

				$this->saveOrder($saveToSession);
			}

			if (empty($this->arResult["ERROR"]))
			{
				$arOrderRes["REDIRECT_URL"] = $this->arParams["~CURRENT_PAGE"]."?ORDER_ID=".urlencode($this->arResult["ACCOUNT_NUMBER"]);
				$arOrderRes["ID"] = $this->arResult["ACCOUNT_NUMBER"];
			}
			else
			{
				$arOrderRes['ERROR'] = $this->arResult['ERROR_SORTED'];
			}
		}
		else
		{
			$arOrderRes["ERROR"]['MAIN'] = Loc::getMessage('SESSID_ERROR');
		}

		$this->showAjaxAnswer(array('order' => $arOrderRes));
	}

	/**
	 * Ajax action - add coupon and if needed recalculate order with JSON answer
	 */
	protected function enterCouponAction()
	{
		$coupon = trim($this->request->get('coupon'));

		if (!empty($coupon))
		{
			if (DiscountCouponsManager::add($coupon))
			{
				$this->refreshOrderAjaxAction();
			}
			else
			{
				$this->showAjaxAnswer($coupon);
			}
		}
	}

	/**
	 * Ajax action - remove coupon and if needed recalculate order with JSON answer
	 */
	protected function removeCouponAction()
	{
		$coupon = htmlspecialchars_decode(trim($this->request->get('coupon')));

		if (!empty($coupon))
		{
			$active = $this->isActiveCoupon($coupon);
			DiscountCouponsManager::delete($coupon);

			if ($active)
			{
				$this->refreshOrderAjaxAction();
			}
			else
			{
				$this->showAjaxAnswer($coupon);
			}
		}
	}

	/**
	 * Execution of 'OnSaleComponentOrderShowAjaxAnswer' event
	 *
	 * @param $result
	 */
	protected function showAjaxAnswer($result)
	{
		global $APPLICATION;

		foreach (GetModuleEvents("sale", 'OnSaleComponentOrderShowAjaxAnswer', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$result));

		$APPLICATION->RestartBuffer();

		if ($this->request->get('save') != 'Y')
			header('Content-Type: application/json');

		echo Json::encode($result);

		CMain::FinalActions();
		die();
	}

	public static function compareProperties($a, $b)
	{
		$sortA = intval($a['SORT']);
		$sortB = intval($b['SORT']);
		if ($sortA == $sortB)
			return 0;

		return ($sortA < $sortB) ? -1 : 1;
	}

	/**
	 * Resize image depending on scale type
	 *
	 * @param array  $item
	 * @param        $imageKey
	 * @param array  $arImage
	 * @param array  $sizeAdaptive
	 * @param array  $sizeStandard
	 * @param string $scale
	 */
	public static function resizeImage(array &$item, $imageKey, array $arImage, array $sizeAdaptive, array $sizeStandard, $scale = '')
	{
		if ($scale == '')
		{
			$scale = 'adaptive';
		}

		if ($scale === 'no_scale')
		{
			$item[$imageKey.'_SRC'] = $arImage['SRC'];
			$item[$imageKey.'_SRC_ORIGINAL'] = $arImage['SRC'];
		}
		elseif ($scale === 'adaptive')
		{
			$arFileTmp = CFile::ResizeImageGet(
				$arImage,
				array('width' => $sizeAdaptive['width'] / 2 , 'height' => $sizeAdaptive['height'] / 2),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$item[$imageKey.'_SRC'] = $arFileTmp['src'];

			$arFileTmp = CFile::ResizeImageGet(
				$arImage,
				$sizeAdaptive,
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$item[$imageKey.'_SRC_2X'] = $arFileTmp['src'];

			$item[$imageKey.'_SRC_ORIGINAL'] = $arImage['SRC'];
		}
		else
		{
			$arFileTmp = CFile::ResizeImageGet($arImage, $sizeStandard, BX_RESIZE_IMAGE_PROPORTIONAL, true);
			$item[$imageKey.'_SRC'] = $arFileTmp['src'];

			$item[$imageKey.'_SRC_ORIGINAL'] = $arImage['SRC'];
		}
	}

	/**
	 * Obtain all order data to $this->arResult['JS_DATA'] for template js initialization
	 * Execution of 'OnSaleComponentOrderJsData' event
	 *
	 * @throws Main\ObjectNotFoundException
	 */
	protected function getJsDataResult()
	{
		global $USER;
		$arResult =& $this->arResult;
		$result =& $this->arResult['JS_DATA'];

		$result['IS_AUTHORIZED'] = $USER->IsAuthorized();
		$result['LAST_ORDER_DATA'] = array();

		if (
			($this->request->getRequestMethod() === 'GET' || $this->request->get('do_authorize') === 'Y' || $this->request->get('do_register') === 'Y')
			&& $this->arUserResult['USE_PRELOAD']
			&& $result['IS_AUTHORIZED']
		)
		{
			$lastOrder =& $this->arUserResult['LAST_ORDER_DATA'];

			if (!empty($lastOrder))
			{
				$status = false;
				if (!empty($lastOrder['PERSON_TYPE_ID']))
				{
					$status = $this->order->getPersonTypeId() == $lastOrder['PERSON_TYPE_ID'];
				}

				$result['LAST_ORDER_DATA']['PERSON_TYPE'] = $status;

				$status = false;
				if (!empty($lastOrder['DELIVERY_ID']) && $shipment = $this->getCurrentShipment($this->order))
				{
					if (empty($lastOrder['DELIVERY_EXTRA_SERVICES'][$lastOrder['DELIVERY_ID']]))
					{
						$status = $shipment->getDeliveryId() == $lastOrder['DELIVERY_ID'];
					}
				}

				$result['LAST_ORDER_DATA']['DELIVERY'] = $status;

				$status = false;
				if (empty($lastOrder['PAY_CURRENT_ACCOUNT']) && !empty($lastOrder['PAY_SYSTEM_ID']) && $payment = $this->getExternalPayment($this->order))
				{
					$status = $payment->getPaymentSystemId() == $lastOrder['PAY_SYSTEM_ID'];
				}

				$result['LAST_ORDER_DATA']['PAY_SYSTEM'] = $status;

				$status = false;
				if (!empty($lastOrder['BUYER_STORE']) && $shipment = $this->getCurrentShipment($this->order))
				{
					$status = $shipment->getStoreId() == $lastOrder['BUYER_STORE'];
				}

				$result['LAST_ORDER_DATA']['PICK_UP'] = $status;
			}
			else
			{
				// last order data cannot initialize
				$result['LAST_ORDER_DATA']['FAIL'] = true;
			}
		}
		else
		{
			// last order data not initialized
			$result['LAST_ORDER_DATA']['FAIL'] = false;
		}

		$result['ZIP_PROPERTY_CHANGED'] = $this->arUserResult['ZIP_PROPERTY_CHANGED'];
		$result['ORDER_DESCRIPTION'] = $this->arUserResult['ORDER_DESCRIPTION'];
		$result['SHOW_AUTH'] = !$USER->IsAuthorized() && $this->arParams["ALLOW_AUTO_REGISTER"] == "N";
		$result['SHOW_EMPTY_BASKET'] = $arResult['SHOW_EMPTY_BASKET'];
		$result['AUTH'] = $arResult['AUTH'];
		$result['OK_MESSAGE'] = $arResult['OK_MESSAGE'];
		$result['GRID'] = $arResult['GRID'];
		$result['PERSON_TYPE'] = $arResult["PERSON_TYPE"];
		$result['PAY_SYSTEM'] = $arResult["PAY_SYSTEM"];
		$result['INNER_PAY_SYSTEM'] = $arResult["INNER_PAY_SYSTEM"];
		$result['DELIVERY'] = $arResult["DELIVERY"];

		foreach ($result['DELIVERY'] as &$delivery)
		{
			if (!empty($delivery['EXTRA_SERVICES']))
			{
				$arExtraService =  array();
				/** @var Delivery\ExtraServices\Base $extraService*/
				foreach ($delivery['EXTRA_SERVICES'] as $extraServiceId => $extraService)
				{
					if ($extraService->canUserEditValue())
					{
						$arr = array();
						$arr['id'] = $extraServiceId;
						$arr['name'] = $extraService->getName();
						$arr['value'] = $extraService->getValue();
						$arr['price'] = $extraService->getPriceShipment($this->getCurrentShipment($this->order));
						$arr['priceFormatted'] = SaleFormatCurrency($extraService->getPriceShipment($this->getCurrentShipment($this->order)), $this->order->getCurrency());
						$arr['description'] = $extraService->getDescription();
						$arr['canUserEditValue'] = $extraService->canUserEditValue();
						$arr['editControl'] = $extraService->getEditControl('DELIVERY_EXTRA_SERVICES['.$delivery['ID'].']['.$extraServiceId.']');
						$arr['viewControl'] = $extraService->getViewControl();
						$arExtraService[] = $arr;
					}
				}

				$delivery['EXTRA_SERVICES'] = $arExtraService;
			}
		}

		$result["USER_PROFILES"] = $arResult["ORDER_PROP"]['USER_PROFILES'];

		$arr = $this->order->getPropertyCollection()->getArray();
		$paymentSystemIds = $this->order->getPaymentSystemId();
		$deliverySystemIds = $this->order->getDeliverySystemId();
		foreach ($arr['properties'] as $key => $property)
		{
			if ($property['UTIL'] === 'Y'
				||
				isset($property['RELATION']) && !$this->checkRelatedProperty($property, $paymentSystemIds, $deliverySystemIds)
			)
			{
				unset($arr['properties'][$key]);
			}
		}
		usort($arr['properties'], array('self', 'compareProperties'));

		if (!empty($arr['groups']) && !empty($arr['properties']))
		{
			$groupIndexList = array();
			foreach ($arr['groups'] as $groupdData)
			{
				$groupIndexList[] = intval($groupdData['ID']);
			}

			if (!empty($groupIndexList))
			{
				foreach ($arr['properties'] as $index => $propertyData)
				{
					if (array_key_exists('PROPS_GROUP_ID', $propertyData))
					{
						if (!in_array($propertyData['PROPS_GROUP_ID'], $groupIndexList))
						{
							$arr['properties'][$index]['PROPS_GROUP_ID'] = 0;
						}
					}

					if ($propertyData['TYPE'] === 'ENUM' && is_array($propertyData['OPTIONS']))
					{
						$arr['properties'][$index]['OPTIONS_SORT'] = array_keys($propertyData['OPTIONS']);
					}
				}
			}

		}

		$result["ORDER_PROP"] = $arr;
		$result['STORE_LIST'] = $arResult['STORE_LIST'];
		$result['BUYER_STORE'] = $arResult['BUYER_STORE'];

		$result['COUPON_LIST'] = array();
		$arCoupons = DiscountCouponsManager::get(true, array(), true, true);
		if (!empty($arCoupons))
		{
			foreach ($arCoupons as &$oneCoupon)
			{
				if ($oneCoupon['STATUS'] == DiscountCouponsManager::STATUS_NOT_FOUND || $oneCoupon['STATUS'] == DiscountCouponsManager::STATUS_FREEZE)
				{
					$oneCoupon['JS_STATUS'] = 'BAD';
				}
				elseif ($oneCoupon['STATUS'] == DiscountCouponsManager::STATUS_NOT_APPLYED || $oneCoupon['STATUS'] == DiscountCouponsManager::STATUS_ENTERED)
				{
					$oneCoupon['JS_STATUS'] = 'ENTERED';
				}
				else
				{
					$oneCoupon['JS_STATUS'] = 'APPLIED';
				}

				$oneCoupon['JS_CHECK_CODE'] = '';
				if (isset($oneCoupon['CHECK_CODE_TEXT']))
				{
					$oneCoupon['JS_CHECK_CODE'] = is_array($oneCoupon['CHECK_CODE_TEXT'])
						? implode('<br>', $oneCoupon['CHECK_CODE_TEXT'])
						: $oneCoupon['CHECK_CODE_TEXT'];
				}

				$result['COUPON_LIST'][] = $oneCoupon;
			}

			unset($oneCoupon);
			$result['COUPON_LIST'] = array_values($arCoupons);
		}
		unset($arCoupons);

		$result['PAY_CURRENT_ACCOUNT'] = 'N';
		if ($innerPaySystem = $this->order->getPaymentCollection()->getInnerPayment())
		{
			if ($innerPaySystem->getSum() > 0)
			{
				$result['PAY_CURRENT_ACCOUNT'] = 'Y';
			}
		}

		$result['PAY_FROM_ACCOUNT'] = $arResult["PAY_FROM_ACCOUNT"];
		$result['CURRENT_BUDGET_FORMATED'] = $arResult["CURRENT_BUDGET_FORMATED"];

		$result['TOTAL'] = array(
			'PRICE_WITHOUT_DISCOUNT_VALUE' => $arResult["PRICE_WITHOUT_DISCOUNT_VALUE"],
			'PRICE_WITHOUT_DISCOUNT' => $arResult["PRICE_WITHOUT_DISCOUNT"],
			'PAYED_FROM_ACCOUNT_FORMATED' => $arResult["PAYED_FROM_ACCOUNT_FORMATED"],
			'ORDER_TOTAL_PRICE' => $arResult["ORDER_TOTAL_PRICE"],
			'ORDER_TOTAL_PRICE_FORMATED' => $arResult["ORDER_TOTAL_PRICE_FORMATED"],
			'ORDER_TOTAL_LEFT_TO_PAY' => $arResult["ORDER_TOTAL_LEFT_TO_PAY"],
			'ORDER_TOTAL_LEFT_TO_PAY_FORMATED' => $arResult["ORDER_TOTAL_LEFT_TO_PAY_FORMATED"],
			'ORDER_WEIGHT' => $arResult["ORDER_WEIGHT"],
			'ORDER_WEIGHT_FORMATED' => $arResult["ORDER_WEIGHT_FORMATED"],
			'ORDER_PRICE' => $arResult["ORDER_PRICE"],
			'ORDER_PRICE_FORMATED' => $arResult["ORDER_PRICE_FORMATED"],
			'USE_VAT' => $arResult["USE_VAT"],
			'VAT_RATE' => $arResult["VAT_RATE"],
			'VAT_SUM' => $arResult["VAT_SUM"],
			'VAT_SUM_FORMATED' => $arResult["VAT_SUM_FORMATED"],
			'TAX_PRICE' => $arResult["TAX_PRICE"],
			'TAX_LIST' => $arResult["TAX_LIST"],
			'DISCOUNT_PRICE' => $arResult["DISCOUNT_PRICE"],
			'DISCOUNT_PRICE_FORMATED' => $arResult["DISCOUNT_PRICE_FORMATED"],
			'DELIVERY_PRICE' => $arResult["DELIVERY_PRICE"],
			'DELIVERY_PRICE_FORMATED' => $arResult["DELIVERY_PRICE_FORMATED"],
			'PAY_SYSTEM_PRICE' => $arResult["PAY_SYSTEM_PRICE"],
			'PAY_SYSTEM_PRICE_FORMATTED' => $arResult["PAY_SYSTEM_PRICE_FORMATTED"]
		);

		$result['ERROR'] = $arResult["ERROR_SORTED"];
		$result['WARNING'] = $arResult["WARNING"];

		$arResult['LOCATIONS'] = $this->getLocationsResult();

		foreach (GetModuleEvents("sale", 'OnSaleComponentOrderJsData', true) as $arEvent)
		{
			ExecuteModuleEventEx($arEvent, array(&$this->arResult, &$this->arParams));
		}
	}

	/**
	 * Scales images of all entities depending on scale parameters
	 *
	 * @param        $result
	 * @param string $scale
	 */
	public static function scaleImages(&$result, $scale = '')
	{
		if (!empty($result) && is_array($result))
		{
			if (!empty($result['DELIVERY']) && is_array($result['DELIVERY']))
			{
				foreach ($result['DELIVERY'] as $key => $delivery)
				{
					if (!empty($delivery["LOGOTIP"]))
					{
						self::resizeImage($delivery, 'LOGOTIP', $delivery["LOGOTIP"],
							array("width" => 600, "height" => 600),
							array("width" => 95, "height" => 55),
							$scale
						);
						$result["DELIVERY"][$key] = $delivery;
					}

				}
				unset($logotype, $delivery);
			}

			if (!empty($result['PAY_SYSTEM']) && is_array($result['PAY_SYSTEM']))
			{
				foreach ($result['PAY_SYSTEM'] as $key => $paySystem)
				{
					if (!empty($paySystem["PSA_LOGOTIP"]))
					{
						self::resizeImage($paySystem, 'PSA_LOGOTIP', $paySystem["PSA_LOGOTIP"],
							array("width" => 600, "height" => 600),
							array("width" => 95, "height" => 55),
							$scale
						);
						$result["PAY_SYSTEM"][$key] = $paySystem;
					}
				}
				unset($logotype, $paySystem);
			}

			if (!empty($result['INNER_PAY_SYSTEM']) && is_array($result['INNER_PAY_SYSTEM']) && !empty($result['INNER_PAY_SYSTEM']["LOGOTIP"]))
			{
				self::resizeImage($result['INNER_PAY_SYSTEM'], 'LOGOTIP', $result['INNER_PAY_SYSTEM']["LOGOTIP"],
					array("width" => 600, "height" => 600),
					array("width" => 95, "height" => 55),
					$scale
				);
			}

			if (!empty($result['STORE_LIST']) && is_array($result['STORE_LIST']))
			{
				foreach ($result['STORE_LIST'] as $key => $store)
				{
					if (!empty($store["IMAGE_ID"]))
					{
						self::resizeImage($store, 'IMAGE_ID', $store["IMAGE_ID"],
							array("width" => 320, "height" => 320),
							array("width" => 115, "height" => 115),
							$scale
						);
						$result["STORE_LIST"][$key] = $store;
					}
				}
				unset($logotype, $store);
			}
		}
	}

	/**
	 * Returns array with locations data output
	 *
	 * @return array
	 */
	protected function getLocationsResult()
	{
		$locations = array();
		$propertyCollection = $this->order->getPropertyCollection();
		$properties = $propertyCollection->getArray();

		foreach ($properties['properties'] as $property)
		{
			if ($property['UTIL'] == 'Y')
				continue;

			if ($property['TYPE'] == 'LOCATION')
			{
				$locationTemplateP = $this->arParams['TEMPLATE_LOCATION'] == '.default' ? 'steps' : 'search';
				$locationTemplate = $this->request->get('PERMANENT_MODE_STEPS') == 1 ? 'steps' : $locationTemplateP;

				$locations[$property['ID']]['template'] = $locationTemplate;
				$locations[$property['ID']]['output'] = $this->getLocationHtml($property, $locationTemplate);
				$locations[$property['ID']]['showAlt'] = isset($this->arUserResult['ORDER_PROP'][$property['INPUT_FIELD_LOCATION']]);
				$locations[$property['ID']]['lastValue'] = reset($property['VALUE']);

				if ($property['IS_LOCATION'] === 'Y')
				{
					$locations[$property['ID']]['coordinates'] = LocationTable::getRow(array(
						'select' => array('LONGITUDE', 'LATITUDE'),
						'filter' => array('=CODE' => reset($property['VALUE']))
					));
				}
			}
		}

		return $locations;
	}

	protected function getLocationHtml($property, $locationTemplate)
	{
		global $APPLICATION;

		$locationOutput = array();
		$showDefault = true;

		$propertyId = (int)$property['ID'];
		$isMultiple = $property['MULTIPLE'] == 'Y' && $property['IS_LOCATION'] != 'Y';

		$locationAltPropDisplayManual = $this->request->get('LOCATION_ALT_PROP_DISPLAY_MANUAL');
		$altPropManual = isset($locationAltPropDisplayManual[$propertyId]) && (bool)$locationAltPropDisplayManual[$propertyId];

		$location = $this->order->getPropertyCollection()->getItemByOrderPropertyId($propertyId);
		$actualValues = $location->getValue();

		if (!is_array($actualValues))
		{
			$actualValues = array($actualValues);
		}

		if (!empty($actualValues) && is_array($actualValues))
		{
			foreach ($actualValues as $key => $value)
			{
				$parameters = array(
					'CODE' => $value,
					'INPUT_NAME' => 'ORDER_PROP_'.$propertyId.($isMultiple ? '['.$key.']' : ''),
					'CACHE_TYPE' => 'A',
					'CACHE_TIME' => '36000000',
					'SEARCH_BY_PRIMARY' => 'N',
					'SHOW_DEFAULT_LOCATIONS' => $showDefault ? 'Y' : 'N',
					'PROVIDE_LINK_BY' => 'code',
					'JS_CALLBACK' => 'submitFormProxy',
					'JS_CONTROL_DEFERRED_INIT' => $propertyId.($isMultiple ? '_'.$key : ''),
					'JS_CONTROL_GLOBAL_ID' => $propertyId.($isMultiple ? '_'.$key : ''),
					'DISABLE_KEYBOARD_INPUT' => 'Y',
					'PRECACHE_LAST_LEVEL' => 'N',
					'PRESELECT_TREE_TRUNK' => 'Y',
					'SUPPRESS_ERRORS' => 'Y',
					'FILTER_BY_SITE' => 'Y',
					'FILTER_SITE_ID' => $this->getSiteId()
				);

				ob_start();

				if ($locationTemplate == 'steps')
				{
					echo '<input type="hidden" id="LOCATION_ALT_PROP_DISPLAY_MANUAL['.$propertyId
						.']" name="LOCATION_ALT_PROP_DISPLAY_MANUAL['.$propertyId.']" value="'
						.($altPropManual ? '1' : '0').'" />';
				}

				$APPLICATION->IncludeComponent(
					'bitrix:sale.location.selector.'.$locationTemplate,
					'',
					$parameters,
					null,
					array('HIDE_ICONS' => 'Y')
				);

				$locationOutput[] = ob_get_contents();
				ob_end_clean();

				$showDefault = false;
			}
		}

		if ($isMultiple)
		{
			$parameters = array(
				'CODE' => '',
				'INPUT_NAME' => 'ORDER_PROP_'.$propertyId.'[#key#]',
				'CACHE_TYPE' => 'A',
				'CACHE_TIME' => '36000000',
				'SEARCH_BY_PRIMARY' => 'N',
				'SHOW_DEFAULT_LOCATIONS' => 'N',
				'PROVIDE_LINK_BY' => 'code',
				'JS_CALLBACK' => 'submitFormProxy',
				'JS_CONTROL_DEFERRED_INIT' => $propertyId.'_key__',
				'JS_CONTROL_GLOBAL_ID' => $propertyId.'_key__',
				'DISABLE_KEYBOARD_INPUT' => 'Y',
				'PRECACHE_LAST_LEVEL' => 'N',
				'PRESELECT_TREE_TRUNK' => 'Y',
				'SUPPRESS_ERRORS' => 'Y',
				'FILTER_BY_SITE' => 'Y',
				'FILTER_SITE_ID' => $this->getSiteId()
			);

			ob_start();

			$APPLICATION->IncludeComponent(
				'bitrix:sale.location.selector.'.$locationTemplate,
				'',
				$parameters,
				null,
				array('HIDE_ICONS' => 'Y')
			);

			$locationOutput['clean'] = ob_get_contents();
			ob_end_clean();
		}

		return $locationOutput;
	}

	protected function isActiveCoupon($coupon)
	{
		$arCoupons = DiscountCouponsManager::get(true, array('COUPON' => $coupon), true, true);
		if (!empty($arCoupons))
		{
			$arCoupon = array_shift($arCoupons);
			if ($arCoupon['STATUS'] == DiscountCouponsManager::STATUS_NOT_APPLYED)
				return true;
		}

		return false;
	}

	/**
	 * Prepares $this->arResult
	 * Execution of 'OnSaleComponentOrderOneStepProcess' event
	 */
	protected function prepareResultArray()
	{
		$this->initGrid();
		$this->obtainBasket();
		$this->obtainPropertiesForIbElements();

		if ($this->arParams['COMPATIBLE_MODE'] == 'Y')
		{
			$this->obtainFormattedProperties();
		}

		$this->obtainDelivery();
		$this->obtainPaySystem();
		$this->obtainTaxes();
		$this->obtainTotal();

		if ($this->arParams['USER_CONSENT'] === 'Y')
		{
			$this->obtainUserConsentInfo();
		}

//		$this->getJsDataResult();

		if ($this->arParams['COMPATIBLE_MODE'] == 'Y')
		{
			$this->obtainRelatedProperties();
			$this->makeResultCompatible();
			$this->makeOrderDataArray();
		}

		$this->arResult['USER_VALS'] = $this->arUserResult;
		$this->executeEvent('OnSaleComponentOrderOneStepProcess', $this->order);
		$this->arResult['USER_VALS'] = $this->arUserResult;

		//try to avoid use "executeEvent" methods and use new events like this
		foreach (GetModuleEvents("sale", 'OnSaleComponentOrderResultPrepared', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($this->order, &$this->arUserResult, $this->request, &$this->arParams, &$this->arResult));
	}

	/**
	 * Create $this->arUserResult array and fill with data from request
	 * Execution of 'OnSaleComponentOrderUserResult' event
	 */
	protected function makeUserResultArray()
	{
		$request =& $this->request;

		$arUserResult = array(
			"PERSON_TYPE_ID" => false,
			"PERSON_TYPE_OLD" => false,
			"PAY_SYSTEM_ID" => false,
			"DELIVERY_ID" => false,
			"ORDER_PROP" => array(),
			"DELIVERY_LOCATION" => false,
			"TAX_LOCATION" => false,
			"PAYER_NAME" => false,
			"USER_EMAIL" => false,
			"PROFILE_NAME" => false,
			"PAY_CURRENT_ACCOUNT" => false,
			"CONFIRM_ORDER" => false,
			"FINAL_STEP" => false,
			"ORDER_DESCRIPTION" => false,
			"PROFILE_ID" => false,
			"PROFILE_CHANGE" => false,
			"DELIVERY_LOCATION_ZIP" => false,
			"ZIP_PROPERTY_CHANGED" => 'N',
			"QUANTITY_LIST" => array(),
			"USE_PRELOAD" => $this->arParams['USE_PRELOAD'] === 'Y'
		);

		if ($request->isPost())
		{
			if (intval($request->get('PERSON_TYPE')) > 0)
				$arUserResult["PERSON_TYPE_ID"] = intval($request->get('PERSON_TYPE'));

			if (intval($request->get('PERSON_TYPE_OLD')) > 0)
				$arUserResult["PERSON_TYPE_OLD"] = intval($request->get('PERSON_TYPE_OLD'));

			if (empty($arUserResult["PERSON_TYPE_OLD"]) || $arUserResult["PERSON_TYPE_OLD"] == $arUserResult["PERSON_TYPE_ID"])
			{
				$profileId = $request->get('PROFILE_ID');
				if (!empty($profileId))
					$arUserResult["PROFILE_ID"] = intval($profileId);

				$paySystemId = $request->get('PAY_SYSTEM_ID');
				if (!empty($paySystemId))
					$arUserResult["PAY_SYSTEM_ID"] = intval($paySystemId);

				$deliveryId = $request->get('DELIVERY_ID');
				if (!empty($deliveryId))
					$arUserResult["DELIVERY_ID"] = $deliveryId;

				$buyerStore = $request->get('BUYER_STORE');
				if (!empty($buyerStore))
					$arUserResult["BUYER_STORE"] = intval($buyerStore);

				$deliveryExtraServices = $request->get('DELIVERY_EXTRA_SERVICES');
				if (!empty($deliveryExtraServices))
					$arUserResult["DELIVERY_EXTRA_SERVICES"] = $deliveryExtraServices;

				if (strlen($request->get('ORDER_DESCRIPTION')) > 0)
				{
					$arUserResult["~ORDER_DESCRIPTION"] = $request->get('ORDER_DESCRIPTION');
					$arUserResult["ORDER_DESCRIPTION"] = htmlspecialcharsbx($request->get('ORDER_DESCRIPTION'));
				}

				if ($request->get('PAY_CURRENT_ACCOUNT') == "Y")
					$arUserResult["PAY_CURRENT_ACCOUNT"] = "Y";

				if ($request->get('confirmorder') == "Y")
				{
					$arUserResult["CONFIRM_ORDER"] = "Y";
					$arUserResult["FINAL_STEP"] = "Y";
				}

				$arUserResult["PROFILE_CHANGE"] = $request->get('profile_change') == "Y" ? "Y" : "N";
			}

			$arUserResult['ZIP_PROPERTY_CHANGED'] = $this->request->get('ZIP_PROPERTY_CHANGED') === 'Y' ? 'Y' : 'N';
		}

		foreach (GetModuleEvents("sale", 'OnSaleComponentOrderUserResult', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arUserResult, $this->request, &$this->arParams));

		$this->arUserResult = $arUserResult;
	}

	/**
	 * Wrapper for event execution method.
	 * Synchronizes modified data from event if needed.
	 *
	 * @deprecated
	 * Compatibility method for old events.
	 * Use new events like "OnSaleComponentOrderCreated" and "OnSaleComponentOrderResultPrepared" instead.
	 *
	 * @param string $eventName
	 * @param null   $order
	 */
	protected function executeEvent($eventName = '', $order = null)
	{
		$arModifiedResult = $this->arUserResult;

		foreach (GetModuleEvents("sale", $eventName, true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$this->arResult, &$arModifiedResult, &$this->arParams, true));

		if (!empty($order))
			$this->synchronize($arModifiedResult, $order);
	}

	protected function synchronize($arModifiedResult, Order $order)
	{
		$modifiedFields = self::arrayDiffRecursive($arModifiedResult, $this->arUserResult);

		if (!empty($modifiedFields))
			$this->synchronizeOrder($modifiedFields, $order);
	}

	/**
	 * Synchronization of modified fields with current order object.
	 *
	 * @param       $modifiedFields
	 * @param Order $order
	 * @throws Main\NotSupportedException
	 * @throws Main\ObjectNotFoundException
	 */
	protected function synchronizeOrder($modifiedFields, Order $order)
	{
		if (!empty($modifiedFields) && is_array($modifiedFields))
		{
			$recalculatePayment = $modifiedFields['CALCULATE_PAYMENT'] === true;
			unset($modifiedFields['CALCULATE_PAYMENT']);
			$recalculateDelivery = false;

			if (!empty($modifiedFields['PERSON_TYPE_ID']))
			{
				$order->setPersonTypeId($modifiedFields['PERSON_TYPE_ID']);
			}

			$propertyCollection = $order->getPropertyCollection();

			foreach ($modifiedFields as $field => $value)
			{
				switch ($field)
				{
					case 'PAY_SYSTEM_ID':
						$recalculatePayment = true;
						break;
					case 'PAY_CURRENT_ACCOUNT':
						$recalculatePayment = true;
						break;
					case 'DELIVERY_ID':
						$recalculateDelivery = true;
						break;
					case 'ORDER_PROP':
						if (is_array($value))
						{
							/** @var Sale\PropertyValue $property */
							foreach ($propertyCollection as $property)
							{
								if (array_key_exists($property->getPropertyId(), $value))
								{
									$property->setValue($value[$property->getPropertyId()]);
									$arProperty = $property->getProperty();
									if ($arProperty['IS_LOCATION'] === 'Y' || $arProperty['IS_ZIP'] === 'Y')
									{
										$recalculateDelivery = true;
									}
								}
							}
						}

						break;
					case 'ORDER_DESCRIPTION':
						$order->setField('USER_DESCRIPTION', $value);
						break;
					case 'DELIVERY_LOCATION':
						$codeValue = CSaleLocation::getLocationCODEbyID($value);
						if ($property = $propertyCollection->getDeliveryLocation())
						{
							$property->setValue($codeValue);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $codeValue;
						}

						$recalculateDelivery = true;
						break;
					case 'DELIVERY_LOCATION_BCODE':
						if ($property = $propertyCollection->getDeliveryLocation())
						{
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $value;
						}

						$recalculateDelivery = true;
						break;
					case 'DELIVERY_LOCATION_ZIP':
						if ($property = $propertyCollection->getDeliveryLocationZip())
						{
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $value;
						}

						$recalculateDelivery = true;
						break;
					case 'TAX_LOCATION':
						$codeValue = CSaleLocation::getLocationCODEbyID($value);
						if ($property = $propertyCollection->getTaxLocation())
						{
							$property->setValue($codeValue);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $codeValue;
						}

						break;
					case 'TAX_LOCATION_BCODE':
						if ($property = $propertyCollection->getTaxLocation())
						{
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $value;
						}

						break;
					case 'PAYER_NAME':
						if ($property = $propertyCollection->getPayerName())
						{
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $value;
						}

						break;
					case 'USER_EMAIL':
						if ($property = $propertyCollection->getUserEmail())
						{
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $value;
						}

						break;
					case 'PROFILE_NAME':
						if ($property = $propertyCollection->getProfileName())
						{
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $value;
						}

						break;
				}

				$this->arUserResult[$field] = $value;
			}

			if ($recalculateDelivery)
			{
				if ($shipment = $this->getCurrentShipment($order))
				{
					$this->initDelivery($shipment);
					$recalculatePayment = true;
				}
			}

			if ($recalculatePayment)
			{
				$this->recalculatePayment($order);
			}
		}
	}

	public static function arrayDiffRecursive($arr1, $arr2)
	{
		$modified = array();

		foreach ($arr1 as $key => $value)
		{
			if (array_key_exists($key, $arr2))
			{
				if (is_array($value) && is_array($arr2[$key]))
				{
					$arDiff = self::arrayDiffRecursive($value, $arr2[$key]);
					if (!empty($arDiff))
					{
						$modified[$key] = $arDiff;
					}
				}
				elseif ($value != $arr2[$key])
				{
					$modified[$key] = $value;
				}
			}
			else
			{
				$modified[$key] = $value;
			}
		}

		return $modified;
	}

	protected function makeOrderDataArray()
	{
		$orderData = $this->order->getFieldValues();
		$orderData['ORDER_PRICE'] = $this->arResult['ORDER_PRICE'];
		$orderData['ORDER_WEIGHT'] = $this->arResult['ORDER_WEIGHT'];
		$orderData['WEIGHT_UNIT'] = $this->arResult['WEIGHT_UNIT'];
		$orderData['WEIGHT_KOEF'] = $this->arResult['WEIGHT_KOEF'];
		$orderData['SITE_ID'] = $this->getSiteId();
		$orderData['USE_VAT'] = $this->arResult["USE_VAT"];
		$orderData['VAT_RATE'] = $this->arResult["VAT_RATE"];
		$orderData['VAT_SUM'] = $this->arResult["VAT_SUM"];

		$this->arResult['ORDER_DATA'] = array_merge($orderData, $this->arUserResult);
	}

	protected function saveProfileData()
	{
		$arResult =& $this->arResult;
		$profileId = 0;
		$profileName = '';
		$properties = array();

		if (isset($arResult['ORDER_PROP']) && is_array($arResult['ORDER_PROP']['USER_PROFILES']))
		{
			foreach ($arResult['ORDER_PROP']['USER_PROFILES'] as $profile)
			{
				if ($profile['CHECKED'] === 'Y')
				{
					$profileId = (int)$profile['ID'];
					break;
				}
			}
		}

		$propertyCollection = $this->order->getPropertyCollection();
		if (!empty($propertyCollection))
		{
			if ($profileProp = $propertyCollection->getProfileName())
				$profileName = $profileProp->getValue();

			/** @var Sale\PropertyValue $property */
			foreach ($propertyCollection as $property)
			{
				$properties[$property->getField('ORDER_PROPS_ID')] = $property->getValue();
			}
		}

		CSaleOrderUserProps::DoSaveUserProfile(
			$this->order->getUserId(),
			$profileId,
			$profileName,
			$this->order->getPersonTypeId(),
			$properties,
			$arResult["ERROR"]
		);
	}

	protected function addStatistic()
	{
		if (Loader::includeModule("statistic"))
		{
			$event1 = "eStore";
			$event2 = "order_confirm";
			$event3 = $this->order->getId();
			$money = $this->order->getPrice();
			$currency = $this->order->getCurrency();

			$e = $event1."/".$event2."/".$event3;

			if (!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"])))
			{
				CStatistic::Set_Event($event1, $event2, $event3, $goto = "", $money, $currency);
				$_SESSION["ORDER_EVENTS"][] = $e;
			}
		}
	}

	/**
	 * Initialization of shipment object. Filling with basket items.
	 *
	 * @param Order $order
	 * @return Shipment
	 * @throws Main\ArgumentTypeException
	 * @throws Main\NotSupportedException
	 */
	public function initShipment(Order $order)
	{
		$shipmentCollection = $order->getShipmentCollection();
		$shipment = $shipmentCollection->createItem();
		$shipmentItemCollection = $shipment->getShipmentItemCollection();
		$shipment->setField('CURRENCY', $order->getCurrency());

		/** @var Sale\BasketItem $item */
		foreach ($order->getBasket() as $item)
		{
			/** @var Sale\ShipmentItem $shipmentItem */
			$shipmentItem = $shipmentItemCollection->createItem($item);
			$shipmentItem->setQuantity($item->getQuantity());
		}

		return $shipment;
	}

	/**
	 * Initializes user data and creates order.
	 * Checks for event flags for possible order/payments recalculations.
	 * Execution of 'OnSaleComponentOrderOneStepDiscountBefore' event.
	 *
	 * @param $userId
	 * @return Order
	 */
	protected function createOrder($userId)
	{
		$this->makeUserResultArray();

		DiscountCouponsManager::init();
		$this->executeEvent('OnSaleComponentOrderOneStepDiscountBefore');

		/** @var Order $order */
		$order = $this->getOrder($userId);

		// $this->arUserResult['RECREATE_ORDER'] - flag for full order recalculation after events manipulations
		if ($this->arUserResult['RECREATE_ORDER'])
			$order = $this->getOrder($userId);

		// $this->arUserResult['CALCULATE_PAYMENT'] - flag for order payments recalculation after events manipulations
		if ($this->arUserResult['CALCULATE_PAYMENT'])
			$this->recalculatePayment($order);

		return $order;
	}


	/**
	 * Returns created order object based on user and request data.
	 * Execution of 'OnSaleComponentOrderCreated' event.
	 *
	 * @param $userId
	 * @return Order
	 */
	protected function getOrder($userId)
	{
		$registry = Sale\Registry::getInstance(Sale\Registry::REGISTRY_TYPE_ORDER);
		/** @var Order $orderClassName */
		$orderClassName = $registry->getOrderClassName();

		$order = $orderClassName::create($this->context->getSite(), $userId);
		$order->isStartField();

		$this->initLastOrderData($order);

		$order->setField('STATUS_ID', Sale\OrderStatus::getInitialStatus());

		if ($this->arParams['USE_PREPAYMENT'] === 'Y')
		{
			$this->usePrepayment($order);
		}

		$isPersonTypeChanged = $this->initPersonType($order);
		$this->initProperties($order, $isPersonTypeChanged);
		$this->initBasket($order);

		$taxes = $order->getTax();
		$taxes->setDeliveryCalculate($this->arParams['COUNT_DELIVERY_TAX'] === 'Y');

		$shipment = $this->initShipment($order);

		$order->doFinalAction(true);

		if ($this->arParams['DELIVERY_TO_PAYSYSTEM'] === 'd2p')
		{
			$this->initDelivery($shipment);
			$this->initPayment($order);
		}
		else
		{
			$this->initPayment($order);
			$this->initDelivery($shipment);
		}

		$this->initEntityCompanyIds($order);
		$this->initOrderFields($order);
		$this->recalculatePayment($order);

		$eventParameters = array(
			$order, &$this->arUserResult, $this->request,
			&$this->arParams, &$this->arResult, &$this->arDeliveryServiceAll, &$this->arPaySystemServiceAll
		);
		foreach (GetModuleEvents('sale', 'OnSaleComponentOrderCreated', true) as $arEvent)
		{
			ExecuteModuleEventEx($arEvent, $eventParameters);
		}

		// no need to calculate deliveries when order is saving
		if ($this->action !== 'saveOrderAjax')
		{
			$this->calculateDeliveries($order);
		}

		return $order;
	}

	/**
	 * Initializes companies for payment and shipment
	 *
	 * @param Order $order
	 */
	protected function initEntityCompanyIds(Order $order)
	{
		$paymentCollection = $order->getPaymentCollection();
		if ($paymentCollection)
		{
			/** @var Payment $payment */
			foreach ($paymentCollection as $payment)
			{
				if ($payment->isInner())
					continue;

				$payment->setField('COMPANY_ID', Company\Manager::getAvailableCompanyIdByEntity($payment));
				if ($payment->getField('COMPANY_ID') > 0)
				{
					$responsibleGroups = Sale\Internals\CompanyResponsibleGroupTable::getCompanyGroups($payment->getField('COMPANY_ID'));
					if (!empty($responsibleGroups) && is_array($responsibleGroups))
					{
						$usersList = array();
						foreach ($responsibleGroups as $groupId)
						{
							$usersList = array_merge($usersList, CGroup::GetGroupUser($groupId));
						}

						if (!empty($usersList) && is_array($usersList))
						{
							$usersList = array_unique($usersList);
							$responsibleUserId = $usersList[array_rand($usersList)];

							/** @var Main\Entity\Event $event */
							$event = new Main\Event('sale', 'OnSaleComponentBeforePaymentSetResponsibleUserId', array(
								'ENTITY' => $payment,
								'VALUE' => $responsibleUserId,
							));
							$event->send();

							if ($event->getResults())
							{
								$result = new Result();
								/** @var Main\EventResult $eventResult */
								foreach($event->getResults() as $eventResult)
								{
									if($eventResult->getType() == Main\EventResult::SUCCESS)
									{
										if ($eventResultData = $eventResult->getParameters())
										{
											if (isset($eventResultData['VALUE']) && $eventResultData['VALUE'] != $responsibleUserId)
											{
												$responsibleUserId = $eventResultData['VALUE'];
											}
										}
									}
								}
							}

							$payment->setField('RESPONSIBLE_ID', $responsibleUserId);
						}
					}
				}
			}
		}

		$shipmentCollection = $order->getShipmentCollection();
		if ($shipmentCollection)
		{
			/** @var Shipment $shipment */
			foreach ($shipmentCollection as $shipment)
			{
				if ($shipment->isSystem())
					continue;

				$shipment->setField('COMPANY_ID', Company\Manager::getAvailableCompanyIdByEntity($shipment));

				if ($shipment->getField('COMPANY_ID') > 0)
				{
					$responsibleGroups = Sale\Internals\CompanyResponsibleGroupTable::getCompanyGroups($shipment->getField('COMPANY_ID'));
					if (!empty($responsibleGroups) && is_array($responsibleGroups))
					{
						$usersList = array();
						foreach ($responsibleGroups as $groupId)
						{
							$usersList = array_merge($usersList, CGroup::GetGroupUser($groupId));
						}

						if (!empty($usersList) && is_array($usersList))
						{
							$usersList = array_unique($usersList);
							$responsibleUserId = $usersList[array_rand($usersList)];

							/** @var Main\Entity\Event $event */
							$event = new Main\Event('sale', 'OnSaleComponentBeforeShipmentSetResponsibleUserId', array(
								'ENTITY' => $shipment,
								'VALUE' => $responsibleUserId,
							));
							$event->send();

							if ($event->getResults())
							{
								$result = new Result();
								/** @var Main\EventResult $eventResult */
								foreach($event->getResults() as $eventResult)
								{
									if($eventResult->getType() == Main\EventResult::SUCCESS)
									{
										if ($eventResultData = $eventResult->getParameters())
										{
											if (isset($eventResultData['VALUE']) && $eventResultData['VALUE'] != $responsibleUserId)
											{
												$responsibleUserId = $eventResultData['VALUE'];
											}
										}
									}
								}
							}

							$shipment->setField('RESPONSIBLE_ID', $responsibleUserId);
						}
					}
				}
			}
		}
	}

	/**
	 * Prepares action string to execute in doAction
	 *
	 * refreshOrderAjax/saveOrderAjax - process/save order via JSON (new template)
	 * enterCoupon/removeCoupon - add/delete coupons via JSON (new template)
	 * showAuthForm - show authorization form (old/new templates) 				 [including component template]
	 * processOrder - process order (old(all hits)/new(first hit) templates) [including component template]
	 * showOrder - show created order (old/new templates)							 [including component template]
	 *
	 * @return null|string
	 */
	protected function prepareAction()
	{
		global $USER;

		$action = $this->request->offsetExists($this->arParams['ACTION_VARIABLE'])
			? $this->request->get($this->arParams['ACTION_VARIABLE'])
			: $this->request->get('action');

		if (!$USER->IsAuthorized() && $this->arParams['ALLOW_AUTO_REGISTER'] === 'N')
		{
			$action = 'showAuthForm';
		}

		if (empty($action))
		{
			if ($this->request->get('ORDER_ID') == '')
			{
				$action = 'processOrder';
			}
			else
			{
				$action = 'showOrder';
			}
		}

		return $action;
	}

	/**
	 * Executes prepared action with postfix 'Action'
	 *
	 * @param $action
	 */
	protected function doAction($action)
	{
		if (is_callable(array($this, $action."Action")))
		{
			call_user_func(
				array($this, $action."Action")
			);
		}
	}

	protected function processOrderAction()
	{
		global $APPLICATION, $USER;

		$arResult =& $this->arResult;
		$this->isOrderConfirmed = $this->request->isPost()
			&& $this->request->get("confirmorder") == 'Y'
			&& $this->checkSession;
		$needToRegister = !$USER->IsAuthorized() && $this->arParams["ALLOW_AUTO_REGISTER"] == "Y";
		$saveToSession = false;

		if ($this->isOrderConfirmed && $needToRegister)
		{
			list($userId, $saveToSession) = $this->autoRegisterUser();
		}
		else
		{
			$userId = $USER->GetID();
		}

		if (!$userId)
		{
			$userId = CSaleUser::GetAnonymousUserID();
		}

		$this->order = $this->createOrder($userId);
		$this->prepareResultArray();

		$isActiveUser = intval($userId) > 0 && $userId != CSaleUser::GetAnonymousUserID();
		if ($this->isOrderConfirmed && $isActiveUser && empty($arResult["ERROR"]))
		{
			$this->saveOrder($saveToSession);

			if (empty($arResult["ERROR"]))
			{
				$arResult["REDIRECT_URL"] = $APPLICATION->GetCurPageParam("ORDER_ID=".urlencode(urlencode($arResult["ACCOUNT_NUMBER"])), array("ORDER_ID"));

				if ($this->request['json'] == "Y" && ($this->isOrderConfirmed || $arResult["NEED_REDIRECT"] == "Y"))
				{
					$APPLICATION->RestartBuffer();
					echo json_encode(array("success" => "Y", "redirect" => $arResult["REDIRECT_URL"]));
					die();
				}
			}
			else
			{
				$arResult["USER_VALS"]["CONFIRM_ORDER"] = "N";
			}
		}
		else
		{
			$arResult["USER_VALS"]["CONFIRM_ORDER"] = "N";
		}
	}

	/**
	 * Action - show created order and payment info
	 */
	protected function showOrderAction()
	{
		global $USER;
		$arResult =& $this->arResult;
		$arOrder = false;
		$arResult["USER_VALS"]["CONFIRM_ORDER"] = "Y";
		$orderId = urldecode($this->request->get('ORDER_ID'));
		$checkedBySession = false;

		$registry = Sale\Registry::getInstance(Sale\Registry::REGISTRY_TYPE_ORDER);
		/** @var Order $orderClassName */
		$orderClassName = $registry->getOrderClassName();

		/** @var Order $order */
		if ($order = $orderClassName::loadByAccountNumber($orderId))
		{
			$arOrder = $order->getFieldValues();
			$arResult["ORDER_ID"] = $arOrder["ID"];
			$arResult["ACCOUNT_NUMBER"] = $arOrder["ACCOUNT_NUMBER"];
			$arOrder["IS_ALLOW_PAY"] = $order->isAllowPay()? 'Y' : 'N';
			$checkedBySession = !empty($_SESSION['SALE_ORDER_ID']) && is_array($_SESSION['SALE_ORDER_ID'])
				&& in_array(intval($order->getId()), $_SESSION['SALE_ORDER_ID']);
		}

		if (!empty($arOrder) && ($order->getUserId() == $USER->GetID() || $checkedBySession))
		{
			foreach (GetModuleEvents("sale", "OnSaleComponentOrderOneStepFinal", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array($arResult["ORDER_ID"], &$arOrder, &$this->arParams));

			$arResult["PAYMENT"] = array();
			if ($order->isAllowPay())
			{
				$paymentCollection = $order->getPaymentCollection();
				/** @var Payment $payment */
				foreach ($paymentCollection as $payment)
				{
					$arResult["PAYMENT"][$payment->getId()] = $payment->getFieldValues();

					if (intval($payment->getPaymentSystemId()) > 0 && !$payment->isPaid())
					{
						$paySystemService = PaySystem\Manager::getObjectById($payment->getPaymentSystemId());
						if (!empty($paySystemService))
						{
							$arPaySysAction = $paySystemService->getFieldsValues();

							if ($paySystemService->getField('NEW_WINDOW') === 'N' || $paySystemService->getField('ID') == PaySystem\Manager::getInnerPaySystemId())
							{
								/** @var PaySystem\ServiceResult $initResult */
								$initResult = $paySystemService->initiatePay($payment, null, PaySystem\BaseServiceHandler::STRING);
								if ($initResult->isSuccess())
									$arPaySysAction['BUFFERED_OUTPUT'] = $initResult->getTemplate();
								else
									$arPaySysAction["ERROR"] = $initResult->getErrorMessages();
							}

							$arResult["PAYMENT"][$payment->getId()]['PAID'] = $payment->getField('PAID');

							$arOrder['PAYMENT_ID'] = $payment->getId();
							$arOrder['PAY_SYSTEM_ID'] = $payment->getPaymentSystemId();
							$arPaySysAction["NAME"] = htmlspecialcharsEx($arPaySysAction["NAME"]);
							$arPaySysAction["IS_AFFORD_PDF"] = $paySystemService->isAffordPdf();

							if ($arPaySysAction > 0)
								$arPaySysAction["LOGOTIP"] = CFile::GetFileArray($arPaySysAction["LOGOTIP"]);

							if ($this->arParams['COMPATIBLE_MODE'] == 'Y' && !$payment->isInner())
							{
								// compatibility
								\CSalePaySystemAction::InitParamArrays($order->getFieldValues(), $order->getId(), '', array(), $payment->getFieldValues());
								$map = CSalePaySystemAction::getOldToNewHandlersMap();
								$oldHandler = array_search($arPaySysAction["ACTION_FILE"], $map);
								if ($oldHandler !== false && !$paySystemService->isCustom())
									$arPaySysAction["ACTION_FILE"] = $oldHandler;

								if (strlen($arPaySysAction["ACTION_FILE"]) > 0 && $arPaySysAction["NEW_WINDOW"] != "Y")
								{
									$pathToAction = Main\Application::getDocumentRoot().$arPaySysAction["ACTION_FILE"];

									$pathToAction = str_replace("\\", "/", $pathToAction);
									while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
										$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);

									if (file_exists($pathToAction))
									{
										if (is_dir($pathToAction) && file_exists($pathToAction."/payment.php"))
											$pathToAction .= "/payment.php";

										$arPaySysAction["PATH_TO_ACTION"] = $pathToAction;
									}
								}

								$arResult["PAY_SYSTEM"] = $arPaySysAction;
							}

							$arResult["PAY_SYSTEM_LIST"][$payment->getPaymentSystemId()] = $arPaySysAction;
							$arResult["PAY_SYSTEM_LIST_BY_PAYMENT_ID"][$payment->getId()] = $arPaySysAction;
						}
						else
							$arResult["PAY_SYSTEM_LIST"][$payment->getPaymentSystemId()] = array('ERROR' => true);
					}
				}
			}

			$arResult["ORDER"] = $arOrder;
		}
		else
			$arResult["ACCOUNT_NUMBER"] = $orderId;
	}

	/**
	 * Action - saves order if there are no errors
	 * Execution of 'OnSaleComponentOrderOneStepComplete' event
	 *
	 * @param bool $saveToSession
	 */
	protected function saveOrder($saveToSession = false)
	{
		$arResult =& $this->arResult;

		$this->initStatGid();
		$this->initAffiliate();

		$res = $this->order->save();
		if ($res->isSuccess())
		{
			$arResult["ORDER_ID"] = $res->getId();
			$arResult["ACCOUNT_NUMBER"] = $this->order->getField('ACCOUNT_NUMBER');

			if ($this->arParams['USER_CONSENT'] === 'Y')
			{
				Main\UserConsent\Consent::addByContext(
					$this->arParams['USER_CONSENT_ID'], 'sale/order', $arResult['ORDER_ID']
				);
			}

			$fUserId = Sale\Fuser::getId();
			$siteId = $this->getSiteId();
			Sale\BasketComponentHelper::clearFUserBasketPrice($fUserId, $siteId);
			Sale\BasketComponentHelper::clearFUserBasketQuantity($fUserId, $siteId);
		}
		else
		{
			$this->addError($res, 'MAIN');
		}

		if ($arResult['HAVE_PREPAYMENT'] && empty($arResult['ERROR']))
		{
			$this->prepayOrder();
		}

		if (empty($arResult['ERROR']))
		{
			$this->saveProfileData();
		}

		if (empty($arResult['ERROR']))
		{
			$this->addStatistic();

			if ($saveToSession)
			{
				if (!is_array($_SESSION['SALE_ORDER_ID']))
				{
					$_SESSION['SALE_ORDER_ID'] = array();
				}

				$_SESSION['SALE_ORDER_ID'][] = $res->getId();
			}
		}

		foreach (GetModuleEvents('sale', 'OnSaleComponentOrderOneStepComplete', true) as $arEvent)
		{
			ExecuteModuleEventEx($arEvent, array($arResult['ORDER_ID'], $this->order->getFieldValues(), $this->arParams));
		}
	}

	public function executeComponent()
	{
		global $APPLICATION;

		$this->setFrameMode(false);
		$this->context = Main\Application::getInstance()->getContext();
		$this->checkSession = $this->arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid();
		$this->isRequestViaAjax = $this->request->isPost() && $this->request->get('via_ajax') == 'Y';
		$isAjaxRequest = $this->request["is_ajax_post"] == "Y";

		if ($isAjaxRequest)
			$APPLICATION->RestartBuffer();

		$this->action = $this->prepareAction();
		Sale\Compatible\DiscountCompatibility::stopUsageCompatible();
		$this->doAction($this->action);
		Sale\Compatible\DiscountCompatibility::revertUsageCompatible();

		if (!$isAjaxRequest)
		{
			CJSCore::Init(array('fx', 'popup', 'window', 'ajax', 'date'));
		}

		//is included in all cases for old template
		$this->includeComponentTemplate();

		if ($isAjaxRequest)
		{
			$APPLICATION->FinalActions();
			die();
		}
	}
}