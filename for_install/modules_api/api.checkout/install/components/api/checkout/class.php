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
	 Bitrix\Sale\Services\Company;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponent $this
 * @var array            $arParams
 * @var array            $arResult
 * @var string           $componentPath
 * @var string           $componentName
 * @var string           $componentTemplate
 *
 * @var string           $parentComponentPath
 * @var string           $parentComponentName
 * @var string           $parentComponentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 */

//ID компонента
//$cpId = $this->getEditAreaId($this->__currentCounter);

//Объект родительского компонента
//$parent = $this->getParent();
//$parentPath = $parent->getPath();

Loc::loadMessages(__FILE__);

if(!Loader::includeModule("sale")) {
	ShowError(Loc::getMessage("SOA_MODULE_NOT_INSTALL"));
	return;
}

class ApiCheckout extends \CBitrixComponent
{
	const AUTH_BLOCK       = 'AUTH';
	const REGION_BLOCK     = 'REGION';
	const PAY_SYSTEM_BLOCK = 'PAY_SYSTEM';
	const DELIVERY_BLOCK   = 'DELIVERY';
	const PROPERTY_BLOCK   = 'PROPERTY';

	/** @var Order $order */
	protected $order;
	protected $action;
	protected $arUserResult;
	protected $isOrderConfirmed;
	protected $arCustomSelectFields = array();
	protected $arElementId          = array();
	protected $arSku2Parent         = array();

	/** @var Delivery\Services\Base[] $arDeliveryServiceAll */
	protected $arDeliveryServiceAll  = array();
	protected $arPaySystemServiceAll = array();
	protected $arActivePaySystems    = array();
	protected $arIblockProps         = array();

	/** @var  PaySystem\Service $prePaymentService */
	protected $prePaymentService;
	protected $useCatalog;

	/** @var Main\Context $context */
	protected $context;
	protected $checkSession = true;
	protected $isRequestViaAjax;

	public static function compareProperties($a, $b)
	{
		$sortA = intval($a['SORT']);
		$sortB = intval($b['SORT']);
		if($sortA == $sortB)
			return 0;

		return ($sortA < $sortB) ? -1 : 1;
	}

	public function onPrepareComponentParams($arParams)
	{
		global $APPLICATION;

		$this->useCatalog = Loader::includeModule("catalog");

		$arParams['USE_PRELOAD'] = $arParams['USE_PRELOAD'] == 'N' ? 'N' : 'Y';

		if($arParams["SET_TITLE"] == "Y")
			$APPLICATION->SetTitle(Loc::getMessage("SOA_TITLE"));

		$arParams["PATH_TO_BASKET"] = trim($arParams["PATH_TO_BASKET"]);
		if(strlen($arParams["PATH_TO_BASKET"]) <= 0)
			$arParams["PATH_TO_BASKET"] = "/";

		$arParams["PATH_TO_PERSONAL"] = trim($arParams["PATH_TO_PERSONAL"]);
		if(strlen($arParams["PATH_TO_PERSONAL"]) <= 0)
			$arParams["PATH_TO_PERSONAL"] = "index.php";

		$arParams["PATH_TO_PAYMENT"] = trim($arParams["PATH_TO_PAYMENT"]);
		if(strlen($arParams["PATH_TO_PAYMENT"]) <= 0)
			$arParams["PATH_TO_PAYMENT"] = "payment.php";

		$arParams["PATH_TO_AUTH"] = trim($arParams["PATH_TO_AUTH"]);
		if(strlen($arParams["PATH_TO_AUTH"]) <= 0)
			$arParams["PATH_TO_AUTH"] = "/auth/";

		$arParams["PAY_FROM_ACCOUNT"]           = $arParams["PAY_FROM_ACCOUNT"] == "Y" ? "Y" : "N";
		$arParams["COUNT_DELIVERY_TAX"]         = $arParams["COUNT_DELIVERY_TAX"] == "Y" ? "Y" : "N";
		$arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] = $arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" ? "Y" : "N";
		$arParams["DELIVERY_NO_AJAX"]           = $arParams["DELIVERY_NO_AJAX"] == "Y" ? "Y" : "N";
		$arParams["USE_PREPAYMENT"]             = $arParams["USE_PREPAYMENT"] == 'Y' ? 'Y' : 'N';
		$arParams["DISPLAY_IMG_HEIGHT"]         = intval($arParams["DISPLAY_IMG_HEIGHT"]) <= 0 ? 90 : intval($arParams["DISPLAY_IMG_HEIGHT"]);

		$arParams["DELIVERY_TO_PAYSYSTEM"] = $arParams["DELIVERY_TO_PAYSYSTEM"] == 'p2d' ? 'p2d' : 'd2p';

		if(!isset($arParams["DISABLE_BASKET_REDIRECT"]) || $arParams["DISABLE_BASKET_REDIRECT"] !== 'Y')
			$arParams["DISABLE_BASKET_REDIRECT"] = "N";

		$arParams["ALLOW_AUTO_REGISTER"] = $arParams["ALLOW_AUTO_REGISTER"] == "Y" ? "Y" : "N";
		$arParams["CURRENT_PAGE"]        = $APPLICATION->GetCurPage();

		$arParams['TEMPLATE_LOCATION'] = $arParams['TEMPLATE_LOCATION'] == 'steps' ? 'steps' : 'search';

		$this->arResult = array(
			 "PERSON_TYPE"                 => array(),
			 "PAY_SYSTEM"                  => array(),
			 "ORDER_PROP"                  => array(),
			 "DELIVERY"                    => array(),
			 "TAX"                         => array(),
			 "ERROR"                       => array(),
			 "ERROR_SORTED"                => array(),
			 "WARNING"                     => array(),
			 "JS_DATA"                     => array(),
			 "SHOW_EMPTY_BASKET"           => false,
			 "ORDER_PRICE"                 => 0,
			 "ORDER_WEIGHT"                => 0,
			 "VATE_RATE"                   => 0,
			 "VAT_SUM"                     => 0,
			 "bUsingVat"                   => false,
			 "BASKET_ITEMS"                => array(),
			 "COUNT_BASKET_ITEMS"          => 0,
			 "BASE_LANG_CURRENCY"          => Bitrix\Sale\Internals\SiteCurrencyTable::getSiteCurrency(SITE_ID),
			 "WEIGHT_UNIT"                 => htmlspecialcharsbx(Option::get('sale', 'weight_unit', false, SITE_ID)),
			 "WEIGHT_KOEF"                 => htmlspecialcharsbx(Option::get('sale', 'weight_koef', 1, SITE_ID)),
			 "TaxExempt"                   => array(),
			 "DISCOUNT_PRICE"              => 0,
			 "DISCOUNT_PERCENT"            => 0,
			 "DELIVERY_PRICE"              => 0,
			 "TAX_PRICE"                   => 0,
			 "PAYED_FROM_ACCOUNT_FORMATED" => false,
			 "ORDER_TOTAL_PRICE_FORMATED"  => false,
			 "ORDER_WEIGHT_FORMATED"       => false,
			 "ORDER_PRICE_FORMATED"        => false,
			 "VAT_SUM_FORMATED"            => false,
			 "DELIVERY_SUM"                => false,
			 "DELIVERY_PROFILE_SUM"        => false,
			 "DELIVERY_PRICE_FORMATED"     => false,
			 "DISCOUNT_PERCENT_FORMATED"   => false,
			 "PAY_FROM_ACCOUNT"            => false,
			 "CURRENT_BUDGET_FORMATED"     => false,
			 "DISCOUNTS"                   => array(),
			 "AUTH"                        => array(),
			 "HAVE_PREPAYMENT"             => false,
			 "PREPAY_PS"                   => array(),
			 "PREPAY_ADIT_FIELDS"          => "",
			 "PREPAY_ORDER_PROPS"          => array(),
		);

		$this->arResult["AUTH"]["new_user_registration_email_confirmation"] = Option::get("main", "new_user_registration_email_confirmation", "N", SITE_ID) == "Y" ? "Y" : "N";
		$this->arResult["AUTH"]["new_user_registration"]                    = Option::get("main", "new_user_registration", "Y") == "Y" ? "Y" : "N";
		$this->arResult["AUTH"]["new_user_email_required"]                  = Option::get("main", "new_user_email_required", "") == "Y" ? "Y" : "N";

		if($arParams["ALLOW_AUTO_REGISTER"] == "Y" && ($this->arResult["AUTH"]["new_user_registration_email_confirmation"] == "Y" || $this->arResult["AUTH"]["new_user_registration"] == "N"))
			$arParams["ALLOW_AUTO_REGISTER"] = "N";
		$arParams["SEND_NEW_USER_NOTIFY"] = $arParams["SEND_NEW_USER_NOTIFY"] == "N" ? "N" : "Y";

		$arParams["ALLOW_NEW_PROFILE"]   = $arParams["ALLOW_NEW_PROFILE"] == "N" ? "N" : "Y";
		$arParams["DELIVERY_NO_SESSION"] = $arParams["DELIVERY_NO_SESSION"] == "N" ? "N" : "Y";

		if(
			 !isset($arParams['SHOW_NOT_CALCULATED_DELIVERIES'])
			 || !in_array($arParams['SHOW_NOT_CALCULATED_DELIVERIES'], array('N', 'L', 'Y'))
		) {
			$arParams['SHOW_NOT_CALCULATED_DELIVERIES'] = 'L';
		}


		//compatibility to old default columns in basket
		if(!empty($arParams["PRODUCT_COLUMNS_VISIBLE"]))
			$arParams["PRODUCT_COLUMNS"] = $arParams["PRODUCT_COLUMNS_VISIBLE"];

		$arDefaults = array('PROPS', 'DISCOUNT_PRICE_PERCENT_FORMATED', 'PRICE_FORMATED');
		$arDiff     = array();
		if(!empty($arParams["PRODUCT_COLUMNS"]) && is_array($arParams["PRODUCT_COLUMNS"]))
			$arDiff = array_diff($arParams["PRODUCT_COLUMNS"], $arDefaults);

		$this->arResult["GRID"]["DEFAULT_COLUMNS"] = count($arParams["PRODUCT_COLUMNS"]) > 2 && empty($arDiff);


		if(empty($arParams["PRODUCT_COLUMNS"])) {
			$arParams["PRODUCT_COLUMNS"] = array(
				 "PICTURE"  => Loc::getMessage("SOA_COLUMN_PICTURE"),
				 "NAME"     => Loc::getMessage("SOA_COLUMN_NAME"),
				 "PROPS"    => Loc::getMessage("SOA_COLUMN_PROPS"),
				 "QUANTITY" => Loc::getMessage("SOA_COLUMN_QUANTITY"),
				 "SUM"      => Loc::getMessage("SOA_COLUMN_SUM"),
			);
		}
		else{
			foreach($arParams["PRODUCT_COLUMNS"] as $key => $code){
				unset($arParams["PRODUCT_COLUMNS"][ $key ]);
				$arParams["PRODUCT_COLUMNS"][$code] = Loc::getMessage("SOA_COLUMN_".$code);
			}
			unset($key,$code);
		}

		if(!empty($arParams["PRODUCT_COLUMNS_HIDDEN"])) {
			foreach($arParams["PRODUCT_COLUMNS_HIDDEN"] as $key => $code){
				unset($arParams["PRODUCT_COLUMNS_HIDDEN"][ $key ]);
				$arParams["PRODUCT_COLUMNS_HIDDEN"][$code] = Loc::getMessage("SOA_COLUMN_".$code);
			}
			unset($key,$code);
		}

		foreach($arParams as $k => $v) {
			if(strpos($k, "ADDITIONAL_PICT_PROP_") !== false) {
				$iblockId = intval(substr($k, strlen("ADDITIONAL_PICT_PROP_")));
				if($v !== '-')
					$arParams["ADDITIONAL_PICT_PROP"][ $iblockId ] = $v;

				unset($arParams[ $k ]);
			}
		}

		return $arParams;
	}

	public function executeComponent()
	{
		global $APPLICATION;
		$this->setFrameMode(false);
		$this->context          = Main\Application::getInstance()->getContext();
		$this->checkSession     = $this->arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid();
		$this->isRequestViaAjax = $this->request->isPost() && $this->request->get('via_ajax') == 'Y';
		$isAjaxRequest          = $this->request["is_ajax_post"] == "Y";

		if($isAjaxRequest)
			$APPLICATION->RestartBuffer();

		$this->action = $this->prepareAction();
		Sale\Compatible\DiscountCompatibility::stopUsageCompatible();
		$this->doAction($this->action);
		Sale\Compatible\DiscountCompatibility::revertUsageCompatible();

		if(!$isAjaxRequest) {
			CJSCore::Init(array('fx', 'popup', 'window', 'ajax', 'date'));
		}

		//is included in all cases for old template
		$this->includeComponentTemplate();

		if($isAjaxRequest) {
			$APPLICATION->FinalActions();
			die();
		}
	}

	/**
	 * Prepares action string to execute in doAction
	 *
	 * refreshOrderAjax/saveOrderAjax - process/save order via JSON (new template)
	 * enterCoupon/removeCoupon - add/delete coupons via JSON (new template)
	 * showAuthForm - show authorization form (old/new templates)         [including component template]
	 * processOrder - process order (old(all hits)/new(first hit) templates) [including component template]
	 * showOrder - show created order (old/new templates)               [including component template]
	 *
	 * @return null|string
	 */
	//HACK: !$this->arParams["ORDER_USER_ID"]
	protected function prepareAction()
	{
		global $USER;

		$action = $this->request->get('action');

		if(!$USER->IsAuthorized() && !$this->arParams["ORDER_USER_ID"] &&  $this->arParams["ALLOW_AUTO_REGISTER"] == "N")
			$action = 'showAuthForm';

		if(empty($action)) {
			if(strlen($this->request->get("ORDER_ID")) <= 0)
				$action = 'processOrder';
			else
				$action = 'showOrder';
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
		if(is_callable(array($this, $action . "Action"))) {
			call_user_func(
				 array($this, $action . "Action")
			);
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
		$arResult = &$this->arResult;

		$request = $this->isRequestViaAjax && $this->request->get('save') != 'Y' ? $this->request->get('order') : $this->request;

		$this->checkSocServicesAuthForm();

		$arResult["AUTH"]["USER_LOGIN"]           = strlen($request["USER_LOGIN"]) > 0 ? htmlspecialcharsbx($request["USER_LOGIN"]) : htmlspecialcharsbx(${Option::get("main", "cookie_name", "BITRIX_SM") . "_LOGIN"});
		$arResult["AUTH"]["captcha_registration"] = (Option::get("main", "captcha_registration", "N") == "Y") ? "Y" : "N";
		if($arResult["AUTH"]["captcha_registration"] == "Y")
			$arResult["AUTH"]["capCode"] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());

		$arResult["POST"] = array();

		if($this->request->isPost() && $this->checkSession) {
			foreach($request as $vname => $vvalue) {
				if(in_array($vname, array("USER_LOGIN", "USER_PASSWORD", "do_authorize", "NEW_NAME", "NEW_LAST_NAME", "NEW_EMAIL", "NEW_GENERATE", "NEW_LOGIN", "NEW_PASSWORD", "NEW_PASSWORD_CONFIRM", "captcha_sid", "captcha_word", "do_register", "is_ajax_post")))
					continue;
				if(is_array($vvalue)) {
					foreach($vvalue as $k => $v)
						$arResult["POST"][ htmlspecialcharsbx($vname . "[" . $k . "]") ] = htmlspecialcharsbx($v);
				}
				else
					$arResult["POST"][ htmlspecialcharsbx($vname) ] = htmlspecialcharsbx($vvalue);
			}

			if($request["do_authorize"] == "Y") {
				$this->doAuthorize();
			}
			elseif($request["do_register"] == "Y" && $arResult["AUTH"]["new_user_registration"] == "Y") {
				$this->doRegister();
			}
			elseif($this->isRequestViaAjax) {
				$this->showAjaxAnswer(array(
					 'order' => array(
							'SHOW_AUTH' => true,
							'AUTH'      => $arResult["AUTH"],
					 ),
				));
			}
		}

		if($this->isRequestViaAjax) {
			if(empty($arResult['ERROR']))
				$this->refreshOrderAjaxAction();
			else
				$this->showAjaxAnswer(array(
					 'order' => array(
							'SHOW_AUTH' => true,
							'AUTH'      => $arResult["AUTH"],
							'ERROR'     => $arResult["ERROR_SORTED"],
					 ),
				));
		}
		else
			$this->processOrderAction();
	}

	protected function checkSocServicesAuthForm()
	{
		global $APPLICATION;
		$arResult = &$this->arResult;

		$arResult["ALLOW_SOCSERV_AUTHORIZATION"] = Option::get("main", "allow_socserv_authorization", "Y") == "Y" ? "Y" : "N";
		$arResult["AUTH_SERVICES"]               = false;
		$arResult["CURRENT_SERVICE"]             = false;
		$arResult["FOR_INTRANET"]                = false;

		if(Bitrix\Main\ModuleManager::isModuleInstalled("intranet") || Bitrix\Main\ModuleManager::isModuleInstalled("rest"))
			$arResult["FOR_INTRANET"] = true;

		if(Loader::includeModule("socialservices") && $arResult["ALLOW_SOCSERV_AUTHORIZATION"] == 'Y') {
			$oAuthManager = new CSocServAuthManager();
			$arServices   = $oAuthManager->GetActiveAuthServices(array(
				 'BACKURL'      => $this->arParams['~CURRENT_PAGE'],
				 'FOR_INTRANET' => $arResult['FOR_INTRANET'],
			));

			if(!empty($arServices)) {
				$arResult["AUTH_SERVICES"] = $arServices;
				if(isset($this->request["auth_service_id"])
					 && $this->request["auth_service_id"] != ''
					 && isset($arResult["AUTH_SERVICES"][ $this->request["auth_service_id"] ])
				) {
					$arResult["CURRENT_SERVICE"] = $this->request["auth_service_id"];
					if(isset($this->request["auth_service_error"]) && $this->request["auth_service_error"] <> '') {
						$this->addError($oAuthManager->GetError($arResult["CURRENT_SERVICE"], $this->request["auth_service_error"]), self::AUTH_BLOCK);
					}
					elseif(!$oAuthManager->Authorize($this->request["auth_service_id"])) {
						$ex = $APPLICATION->GetException();
						if($ex)
							$this->addError($ex->GetString(), self::AUTH_BLOCK);
					}
				}
			}
		}
	}

	protected function addError($res, $type = 'MAIN')
	{
		if($res instanceof Result) {
			foreach($res->getErrorMessages() as $error) {
				$this->arResult["ERROR"][]                 = $error;
				$this->arResult["ERROR_SORTED"][ $type ][] = $error;
			}
		}
		else {
			$this->arResult["ERROR"][]                 = $res;
			$this->arResult["ERROR_SORTED"][ $type ][] = $res;
		}
	}

	protected function doAuthorize()
	{
		global $USER;
		$request = $this->isRequestViaAjax && $this->request->get('save') != 'Y' ? $this->request->get('order') : $this->request;

		if(strlen($request["USER_LOGIN"]) <= 0)
			$this->addError(Loc::getMessage("STOF_ERROR_AUTH_LOGIN"), self::AUTH_BLOCK);

		if(empty($this->arResult["ERROR"])) {
			$rememberMe   = $request["USER_REMEMBER"] == 'Y' ? 'Y' : 'N';
			$arAuthResult = $USER->Login($request["USER_LOGIN"], $request["USER_PASSWORD"], $rememberMe);
			if($arAuthResult != false && $arAuthResult["TYPE"] == "ERROR")
				$this->addError(Loc::getMessage("STOF_ERROR_AUTH") . (strlen($arAuthResult["MESSAGE"]) > 0 ? ": " . $arAuthResult["MESSAGE"] : ""), self::AUTH_BLOCK);
		}
	}

	protected function doRegister()
	{
		global $APPLICATION, $USER;
		$arResult = &$this->arResult;
		$request  = $this->isRequestViaAjax && $this->request->get('save') != 'Y' ? $this->request->get('order') : $this->request;

		if(strlen($request["NEW_NAME"]) <= 0)
			$this->addError(Loc::getMessage("STOF_ERROR_REG_NAME"), self::AUTH_BLOCK);

		if(strlen($request["NEW_LAST_NAME"]) <= 0)
			$this->addError(Loc::getMessage("STOF_ERROR_REG_LASTNAME"), self::AUTH_BLOCK);

		if(Option::get("main", "new_user_email_required", "") == "Y") {
			if(strlen($request["NEW_EMAIL"]) <= 0)
				$this->addError(Loc::getMessage("STOF_ERROR_REG_EMAIL"), self::AUTH_BLOCK);
			elseif(!check_email($request["NEW_EMAIL"]))
				$this->addError(Loc::getMessage("STOF_ERROR_REG_BAD_EMAIL"), self::AUTH_BLOCK);
		}

		$arResult["AUTH"]["NEW_EMAIL"] = $request["NEW_EMAIL"];

		if(empty($arResult["ERROR"])) {
			if($request["NEW_GENERATE"] == "Y") {
				$generatedData    = $this->generateUserData(array('EMAIL' => $request["NEW_EMAIL"]));
				$arResult["AUTH"] = array_merge($arResult["AUTH"], $generatedData);
			}
			else {
				if(strlen($request["NEW_LOGIN"]) <= 0)
					$this->addError(Loc::getMessage("STOF_ERROR_REG_FLAG"), self::AUTH_BLOCK);

				if(strlen($request["NEW_PASSWORD"]) <= 0)
					$this->addError(Loc::getMessage("STOF_ERROR_REG_FLAG1"), self::AUTH_BLOCK);

				if(strlen($request["NEW_PASSWORD"]) > 0 && strlen($request["NEW_PASSWORD_CONFIRM"]) <= 0)
					$this->addError(Loc::getMessage("STOF_ERROR_REG_FLAG1"), self::AUTH_BLOCK);

				if(strlen($request["NEW_PASSWORD"]) > 0
					 && strlen($request["NEW_PASSWORD_CONFIRM"]) > 0
					 && $request["NEW_PASSWORD"] != $request["NEW_PASSWORD_CONFIRM"]
				)
					$this->addError(Loc::getMessage("STOF_ERROR_REG_PASS"), self::AUTH_BLOCK);

				$arResult["AUTH"]["NEW_LOGIN"]            = $request["NEW_LOGIN"];
				$arResult["AUTH"]["NEW_NAME"]             = $request["NEW_NAME"];
				$arResult["AUTH"]["NEW_PASSWORD"]         = $request["NEW_PASSWORD"];
				$arResult["AUTH"]["NEW_PASSWORD_CONFIRM"] = $request["NEW_PASSWORD_CONFIRM"];
			}
		}

		if(empty($arResult["ERROR"])) {
			$arAuthResult = $USER->Register($arResult["AUTH"]["NEW_LOGIN"], $request["NEW_NAME"], $request["NEW_LAST_NAME"], $arResult["AUTH"]["NEW_PASSWORD"], $arResult["AUTH"]["NEW_PASSWORD_CONFIRM"], $arResult["AUTH"]["NEW_EMAIL"], LANG, $request["captcha_word"], $request["captcha_sid"]);
			if($arAuthResult != false && $arAuthResult["TYPE"] == "ERROR")
				$this->addError(Loc::getMessage("STOF_ERROR_REG") . (strlen($arAuthResult["MESSAGE"]) > 0 ? ": " . $arAuthResult["MESSAGE"] : ""), self::AUTH_BLOCK);
			else {
				if($USER->IsAuthorized()) {
					if($this->arParams["SEND_NEW_USER_NOTIFY"] == "Y")
						CUser::SendUserInfo($USER->GetID(), SITE_ID, Loc::getMessage("INFO_REQ"), true);

					if($this->isRequestViaAjax)
						$this->refreshOrderAjaxAction();
					else
						LocalRedirect($APPLICATION->GetCurPageParam());
				}
				else {
					$arResult["OK_MESSAGE"][] = Loc::getMessage("STOF_ERROR_REG_CONFIRM");
				}
			}
		}

		$arResult["AUTH"]["~NEW_LOGIN"]     = $arResult["AUTH"]["NEW_LOGIN"];
		$arResult["AUTH"]["NEW_LOGIN"]      = htmlspecialcharsEx($arResult["AUTH"]["NEW_LOGIN"]);
		$arResult["AUTH"]["~NEW_NAME"]      = $request["NEW_NAME"];
		$arResult["AUTH"]["NEW_NAME"]       = htmlspecialcharsEx($request["NEW_NAME"]);
		$arResult["AUTH"]["~NEW_LAST_NAME"] = $request["NEW_LAST_NAME"];
		$arResult["AUTH"]["NEW_LAST_NAME"]  = htmlspecialcharsEx($request["NEW_LAST_NAME"]);
		$arResult["AUTH"]["~NEW_EMAIL"]     = $arResult["AUTH"]["NEW_EMAIL"];
		$arResult["AUTH"]["NEW_EMAIL"]      = htmlspecialcharsEx($arResult["AUTH"]["NEW_EMAIL"]);
	}

	/**
	 * Generation of user registration fields (login, password, etc)
	 *
	 * @param array $userProps
	 *
	 * @return array
	 * @throws Main\ArgumentNullException
	 */
	public function generateUserData($userProps = array())
	{
		global $USER;

		$userEmail = (is_array($userProps) && strlen($userProps['EMAIL']) > 0) ? $userProps['EMAIL'] : '';
		$newLogin  = $userEmail;
		$newEmail  = $userEmail;

		$payerName = (is_array($userProps) && strlen($userProps['PAYER']) > 0) ? $userProps['PAYER'] : '';

		if($userEmail == '') {
			$newEmail = false;
			if(is_array($userProps) && strlen($userProps['PHONE']) > 0)
				$newLogin = trim($userProps['PHONE']);
			else
				$newLogin = randString(5);
		}

		$newName     = "";
		$newLastName = "";

		if(strlen($payerName) > 0) {
			$arNames     = explode(" ", $payerName);
			$newName     = $arNames[1];
			$newLastName = $arNames[0];
		}

		$pos = strpos($newLogin, "@");
		if($pos !== false)
			$newLogin = substr($newLogin, 0, $pos);

		if(strlen($newLogin) > 47)
			$newLogin = substr($newLogin, 0, 47);

		if(strlen($newLogin) < 3)
			$newLogin .= "_";

		if(strlen($newLogin) < 3)
			$newLogin .= "_";

		$dbUserLogin = CUser::GetByLogin($newLogin);
		if($arUserLogin = $dbUserLogin->Fetch()) {
			$newLoginTmp = $newLogin;
			$uind        = 0;
			do {
				$uind++;
				if($uind == 10) {
					$newLogin    = $userEmail;
					$newLoginTmp = $newLogin;
				}
				elseif($uind > 10) {
					$newLogin    = "buyer" . time() . GetRandomCode(2);
					$newLoginTmp = $newLogin;
					break;
				}
				else {
					$newLoginTmp = $newLogin . $uind;
				}
				$dbUserLogin = CUser::GetByLogin($newLoginTmp);
			}
			while($arUserLogin = $dbUserLogin->Fetch());
			$newLogin = $newLoginTmp;
		}

		$def_group = Option::get("main", "new_user_registration_def_group", "");
		if($def_group != "") {
			$groupID  = explode(",", $def_group);
			$arPolicy = $USER->GetGroupPolicy($groupID);
		}
		else {
			$arPolicy = $USER->GetGroupPolicy(array());
		}

		$password_min_length = intval($arPolicy["PASSWORD_LENGTH"]);
		if($password_min_length <= 0)
			$password_min_length = 6;
		$password_chars = array(
			 "abcdefghijklnmopqrstuvwxyz",
			 "ABCDEFGHIJKLNMOPQRSTUVWXYZ",
			 "0123456789",
		);
		if($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
			$password_chars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";
		$newPassword = $newPasswordConfirm = randString($password_min_length + 2, $password_chars);

		return array(
			 'NEW_EMAIL'            => $newEmail,
			 'NEW_LOGIN'            => $newLogin,
			 'NEW_NAME'             => $newName,
			 'NEW_LAST_NAME'        => $newLastName,
			 'NEW_PASSWORD'         => $newPassword,
			 'NEW_PASSWORD_CONFIRM' => $newPasswordConfirm,
			 'GROUP_ID'             => $groupID,
		);
	}

	/**
	 * Ajax action - recalculate order and send JSON answer with data/errors
	 */
	protected function refreshOrderAjaxAction()
	{
		global $USER;

		$error = false;
		$this->request->set($this->request->get('order'));
		if($this->checkSession) {
			$this->order = $this->createOrder($USER->GetID() ? $USER->GetID() : CSaleUser::GetAnonymousUserID());
			$this->prepareResultArray();
			self::scaleImages($this->arResult['JS_DATA'], $this->arParams['SERVICES_IMAGES_SCALING']);
		}
		else
			$error = Loc::getMessage('SESSID_ERROR');

		$this->showAjaxAnswer(array(
			 'order'     => $this->arResult['JS_DATA'],
			 'locations' => $this->arResult['LOCATIONS'],
			 'error'     => $error,
		));
	}

	/**
	 * Initializes user data and creates order.
	 * Checks for event flags for possible order/payments recalculations.
	 * Execution of 'OnSaleComponentOrderOneStepDiscountBefore' event.
	 *
	 * @param $userId
	 *
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
		if($this->arUserResult['RECREATE_ORDER'])
			$order = $this->getOrder($userId);

		// $this->arUserResult['CALCULATE_PAYMENT'] - flag for order payments recalculation after events manipulations
		if($this->arUserResult['CALCULATE_PAYMENT'])
			$this->recalculatePayment($order);

		return $order;
	}

	/**
	 * Create $this->arUserResult array and fill with data from request
	 * Execution of 'OnSaleComponentOrderUserResult' event
	 */
	protected function makeUserResultArray()
	{
		$request = &$this->request;

		$arUserResult = array(
			 "PERSON_TYPE_ID"        => $this->arParams['PERSON_TYPE_ID'],
			 "PERSON_TYPE_OLD"       => false,
			 "PAY_SYSTEM_ID"         => $this->arParams['PAY_SYSTEM_ID'],
			 "DELIVERY_ID"           => $this->arParams['DELIVERY_ID'],
			 "ORDER_PROP"            => array(),
			 "DELIVERY_LOCATION"     => false,
			 "TAX_LOCATION"          => false,
			 "PAYER_NAME"            => false,
			 "USER_EMAIL"            => false,
			 "PROFILE_NAME"          => false,
			 "PAY_CURRENT_ACCOUNT"   => false,
			 "CONFIRM_ORDER"         => false,
			 "FINAL_STEP"            => false,
			 "ORDER_DESCRIPTION"     => false,
			 "PROFILE_ID"            => false,
			 "PROFILE_CHANGE"        => false,
			 "DELIVERY_LOCATION_ZIP" => false,
			 "USE_PRELOAD"           => $this->arParams['USE_PRELOAD'] === 'Y',
		);

		if($request->isPost()) {
			if(intval($request->get('PERSON_TYPE')) > 0)
				$arUserResult["PERSON_TYPE_ID"] = intval($request->get('PERSON_TYPE'));

			if(intval($request->get('PERSON_TYPE_OLD')) > 0)
				$arUserResult["PERSON_TYPE_OLD"] = intval($request->get('PERSON_TYPE_OLD'));

			if(empty($arUserResult["PERSON_TYPE_OLD"]) || $arUserResult["PERSON_TYPE_OLD"] == $arUserResult["PERSON_TYPE_ID"]) {
				$profileId = $request->get('PROFILE_ID');
				if(!empty($profileId))
					$arUserResult["PROFILE_ID"] = intval($profileId);

				$paySystemId = $request->get('PAY_SYSTEM_ID');
				if(!empty($paySystemId))
					$arUserResult["PAY_SYSTEM_ID"] = intval($paySystemId);

				$deliveryId = $request->get('DELIVERY_ID');
				if(!empty($deliveryId))
					$arUserResult["DELIVERY_ID"] = $deliveryId;

				$buyerStore = $request->get('BUYER_STORE');
				if(!empty($buyerStore))
					$arUserResult["BUYER_STORE"] = intval($buyerStore);

				$deliveryExtraServices = $request->get('DELIVERY_EXTRA_SERVICES');
				if(!empty($deliveryExtraServices))
					$arUserResult["DELIVERY_EXTRA_SERVICES"] = $deliveryExtraServices;

				if(strlen($request->get('ORDER_DESCRIPTION')) > 0) {
					$arUserResult["~ORDER_DESCRIPTION"] = $request->get('ORDER_DESCRIPTION');
					$arUserResult["ORDER_DESCRIPTION"]  = htmlspecialcharsbx($request->get('ORDER_DESCRIPTION'));
				}

				if($request->get('PAY_CURRENT_ACCOUNT') == "Y")
					$arUserResult["PAY_CURRENT_ACCOUNT"] = "Y";

				if($request->get('confirmorder') == "Y") {
					$arUserResult["CONFIRM_ORDER"] = "Y";
					$arUserResult["FINAL_STEP"]    = "Y";
				}

				$arUserResult["PROFILE_CHANGE"] = $request->get('profile_change') == "Y" ? "Y" : "N";
			}
		}

		foreach(GetModuleEvents("sale", 'OnSaleComponentOrderUserResult', true) as $arEvent)
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

		foreach(GetModuleEvents("sale", $eventName, true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$this->arResult, &$arModifiedResult, &$this->arParams, true));

		if(!empty($order))
			$this->synchronize($arModifiedResult, $order);
	}

	protected function synchronize($arModifiedResult, Order $order)
	{
		$modifiedFields = self::arrayDiffRecursive($arModifiedResult, $this->arUserResult);

		if(!empty($modifiedFields))
			$this->synchronizeOrder($modifiedFields, $order);
	}

	public static function arrayDiffRecursive($arr1, $arr2)
	{
		$modified = array();

		foreach($arr1 as $key => $value) {
			if(array_key_exists($key, $arr2)) {
				if(is_array($value) && is_array($arr2[ $key ])) {
					$arDiff = self::arrayDiffRecursive($value, $arr2[ $key ]);
					if(count($arDiff))
						$modified[ $key ] = $arDiff;
				}
				else if($value != $arr2[ $key ])
					$modified[ $key ] = $value;
			}
			else
				$modified[ $key ] = $value;
		}

		return $modified;
	}

	/**
	 * Synchronization of modified fields with current order object.
	 *
	 * @param       $modifiedFields
	 * @param Order $order
	 *
	 * @throws Main\NotSupportedException
	 * @throws Main\ObjectNotFoundException
	 */
	protected function synchronizeOrder($modifiedFields, Order $order)
	{
		if(!empty($modifiedFields) && is_array($modifiedFields)) {
			$recalculatePayment = $modifiedFields['CALCULATE_PAYMENT'] === true;
			unset($modifiedFields['CALCULATE_PAYMENT']);
			$recalculateDelivery = false;
			$propertyCollection  = $order->getPropertyCollection();

			foreach($modifiedFields as $field => $value) {
				switch($field) {
					case 'PERSON_TYPE_ID':
						$order->setPersonTypeId($value);
						break;
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
						if(is_array($value)) {

							/** @var Sale\PropertyValue $property */
							foreach($propertyCollection as $property) {
								if(array_key_exists($property->getPropertyId(), $value)) {
									$property->setValue($value[ $property->getPropertyId() ]);
									$arProperty = $property->getProperty();
									if($arProperty['IS_LOCATION'] == 'Y' || $arProperty['IS_ZIP'] == 'Y')
										$recalculateDelivery = true;
								}
							}
						}
						break;
					case 'ORDER_DESCRIPTION':
						$order->setField("USER_DESCRIPTION", $value);
						break;
					case 'DELIVERY_LOCATION':
						$codeValue = CSaleLocation::getLocationCODEbyID($value);
						if($property = $propertyCollection->getDeliveryLocation()) {
							$property->setValue($codeValue);
							$this->arUserResult['ORDER_PROP'][ $property->getPropertyId() ] = $codeValue;
						}

						$recalculateDelivery = true;
						break;
					case 'DELIVERY_LOCATION_BCODE':
						if($property = $propertyCollection->getDeliveryLocation()) {
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][ $property->getPropertyId() ] = $value;
						}

						$recalculateDelivery = true;
						break;
					case 'DELIVERY_LOCATION_ZIP':
						if($property = $propertyCollection->getDeliveryLocationZip()) {
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][ $property->getPropertyId() ] = $value;
						}

						$recalculateDelivery = true;
						break;
					case 'TAX_LOCATION':
						$codeValue = CSaleLocation::getLocationCODEbyID($value);
						if($property = $propertyCollection->getTaxLocation()) {
							$property->setValue($codeValue);
							$this->arUserResult['ORDER_PROP'][ $property->getPropertyId() ] = $codeValue;
						}
						break;
					case 'TAX_LOCATION_BCODE':
						if($property = $propertyCollection->getTaxLocation()) {
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][ $property->getPropertyId() ] = $value;
						}
						break;
					case 'PAYER_NAME':
						if($property = $propertyCollection->getPayerName()) {
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][ $property->getPropertyId() ] = $value;
						}
						break;
					case 'USER_EMAIL':
						if($property = $propertyCollection->getUserEmail()) {
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][ $property->getPropertyId() ] = $value;
						}
						break;
					case 'PROFILE_NAME':
						if($property = $propertyCollection->getProfileName()) {
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][ $property->getPropertyId() ] = $value;
						}
						break;
				}

				$this->arUserResult[ $field ] = $value;
			}

			if($recalculateDelivery) {
				if($shipment = $this->getCurrentShipment($order)) {
					$this->initDelivery($shipment);
					$recalculatePayment = true;
				}
			}

			if($recalculatePayment)
				$this->recalculatePayment($order);
		}
	}

	public function getCurrentShipment(Order $order)
	{
		/** @var Shipment $shipment */
		foreach($order->getShipmentCollection() as $shipment) {
			if(!$shipment->isSystem())
				return $shipment;
		}

		return null;
	}

	/**
	 * Initialization of shipment object with first/selected delivery service.
	 *
	 * @param Shipment $shipment
	 *
	 * @throws Main\NotSupportedException
	 */
	protected function initDelivery(Shipment $shipment)
	{
		$deliveryId                 = intval($this->arUserResult['DELIVERY_ID']);
		$this->arDeliveryServiceAll = Delivery\Services\Manager::getRestrictedObjectsList($shipment);

		/** @var Sale\ShipmentCollection $shipmentCollection */
		$shipmentCollection = $shipment->getCollection();
		$order              = $shipmentCollection->getOrder();
		if(!empty($this->arDeliveryServiceAll)) {
			if(array_key_exists($deliveryId, $this->arDeliveryServiceAll)) {
				$deliveryObj = $this->arDeliveryServiceAll[ $deliveryId ];
			}
			else {
				reset($this->arDeliveryServiceAll);
				$deliveryObj = current($this->arDeliveryServiceAll);
				if($deliveryId != 0)
					$this->addWarning(Loc::getMessage("DELIVERY_CHANGE_WARNING"), self::DELIVERY_BLOCK);
			}

			if($deliveryObj->isProfile())
				$name = $deliveryObj->getNameWithParent();
			else
				$name = $deliveryObj->getName();

			$shipment->setFields(array(
				 'DELIVERY_ID'   => $deliveryObj->getId(),
				 'DELIVERY_NAME' => $name,
				 'CURRENCY'      => $order->getCurrency(),
			));
			$this->arUserResult['DELIVERY_ID'] = $deliveryObj->getId();

			$deliveryStoreList = Delivery\ExtraServices\Manager::getStoresList($deliveryObj->getId());
			if(count($deliveryStoreList) > 0) {
				if($this->arUserResult['BUYER_STORE'] <= 0 || !in_array($this->arUserResult['BUYER_STORE'], $deliveryStoreList))
					$this->arUserResult['BUYER_STORE'] = current($deliveryStoreList);

				$shipment->setStoreId($this->arUserResult['BUYER_STORE']);
			}

			$deliveryExtraServices = $this->arUserResult['DELIVERY_EXTRA_SERVICES'];
			if(is_array($deliveryExtraServices) && !empty($deliveryExtraServices[ $deliveryObj->getId() ])) {
				$shipment->setExtraServices($deliveryExtraServices[ $deliveryObj->getId() ]);
				$deliveryObj->getExtraServices()->setValues($deliveryExtraServices[ $deliveryObj->getId() ]);
			}

			$res = $shipmentCollection->calculateDelivery();
			if(!$res->isSuccess()) {
				$errMessages = '';

				if(count($res->getErrorMessages()) > 0)
					foreach($res->getErrorMessages() as $message)
						$errMessages .= $message . '<br />';
				else
					$errMessages = Loc::getMessage("SOA_DELIVERY_CALCULATE_ERROR");

				$shipment->setFields(array(
					 'MARKED'        => 'Y',
					 'REASON_MARKED' => $errMessages,
				));
			}
		}
		else {
			$service = Delivery\Services\Manager::getById(Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId());
			$shipment->setFields(array(
				 'DELIVERY_ID'   => $service['ID'],
				 'DELIVERY_NAME' => $service['NAME'],
				 'CURRENCY'      => $order->getCurrency(),
			));
		}

		$shipment->setField('COMPANY_ID', Company\Manager::getAvailableCompanyIdByEntity($shipment));
	}

	protected function addWarning($res, $type)
	{
		if(!empty($type))
			$this->arResult["WARNING"][ $type ][] = $res;
	}

	/**
	 * Recalculates payment prices which could change due to shipment/discounts.
	 *
	 * @param Order $order
	 *
	 * @throws Main\ObjectNotFoundException
	 */
	protected function recalculatePayment(Order $order)
	{
		// one more delivery calculation for some cases when payment affect delivery price
		$res = $order->getShipmentCollection()->calculateDelivery();
		if(!$res->isSuccess()) {
			$shipment = $this->getCurrentShipment($order);
			if(!empty($shipment)) {
				$errMessages = '';

				if(count($res->getErrorMessages()) > 0) {
					foreach($res->getErrorMessages() as $message) {
						$errMessages .= $message . '<br />';
					}
				}
				else {
					$errMessages = Loc::getMessage("SOA_DELIVERY_CALCULATE_ERROR");
				}

				$shipment->setFields(array(
					 'MARKED'        => 'Y',
					 'REASON_MARKED' => $errMessages,
				));
			}
		}

		$paySystemId       = intval($this->arUserResult['PAY_SYSTEM_ID']);
		$paymentCollection = $order->getPaymentCollection();

		list($sumToSpend, $arPsFromInner) = $this->getInnerPaySystemInfo($order, true);

		if($this->arUserResult['PAY_CURRENT_ACCOUNT'] == "Y" && $sumToSpend > 0) {
			$this->arActivePaySystems = array_intersect_key($this->arActivePaySystems, $arPsFromInner);
			if($innerPayment = $this->getInnerPayment($order)) {
				$sumToPay = $sumToSpend >= $order->getPrice() ? $order->getPrice() : $sumToSpend;
				$innerPayment->setField('SUM', $sumToPay);
			}
		}
		else {
			$paymentCollection->getInnerPayment()->delete();
		}

		/** @var Payment $extPayment */
		$extPayment   = $this->getExternalPayment($order);
		$remainingSum = empty($innerPayment) ? $order->getPrice() : $order->getPrice() - $innerPayment->getSum();
		if($remainingSum > 0 || $order->getPrice() == 0) {
			if(empty($extPayment)) {
				$extPayment = $paymentCollection->createItem();
			}
			$extPayment->setField('SUM', $remainingSum);

			$this->arActivePaySystems = array_intersect_key($this->arActivePaySystems, PaySystem\Manager::getListWithRestrictions($extPayment));
			if(array_key_exists($paySystemId, $this->arActivePaySystems)) {
				$arPaySystem = $this->arActivePaySystems[ $paySystemId ];
			}
			else if(array_key_exists($paySystemId, $this->arPaySystemServiceAll)) {
				$arPaySystem = $this->arPaySystemServiceAll[ $paySystemId ];
			}
			else {
				if(key($this->arActivePaySystems) == PaySystem\Manager::getInnerPaySystemId()) {
					if($sumToSpend > 0) {
						if(count($this->arActivePaySystems) > 1) {
							next($this->arActivePaySystems);
						}
						else if(empty($innerPayment)) {
							$remainingSum = $remainingSum > $sumToSpend ? $sumToSpend : $remainingSum;
							$extPayment->setField('SUM', $remainingSum);
						}
						else
							$extPayment->delete();

						$remainingSum = $order->getPrice() - $paymentCollection->getSum();
						if($remainingSum > 0) {
							$this->addWarning(Loc::getMessage("INNER_PAYMENT_BALANCE_ERROR"), self::PAY_SYSTEM_BLOCK);
							$order->setFields(array(
								 'MARKED'        => 'Y',
								 'REASON_MARKED' => Loc::getMessage("INNER_PAYMENT_BALANCE_ERROR"),
							));
						}
					}
					else {
						unset($this->arActivePaySystems[ PaySystem\Manager::getInnerPaySystemId() ]);
						unset($this->arPaySystemServiceAll[ PaySystem\Manager::getInnerPaySystemId() ]);
					}
				}

				$arPaySystem = current($this->arActivePaySystems);
			}

			if(!array_key_exists(intval($arPaySystem['ID']), $this->arActivePaySystems)) {
				$this->addError(Loc::getMessage("P2D_CALCULATE_ERROR"), self::PAY_SYSTEM_BLOCK);
				$this->addError(Loc::getMessage("P2D_CALCULATE_ERROR"), self::DELIVERY_BLOCK);
			}

			if(!empty($arPaySystem)) {
				$needSum = !empty($innerPayment) ? $order->getPrice() - $innerPayment->getSum() : $order->getPrice();
				$extPayment->setFields(array(
					 'PAY_SYSTEM_ID'   => $arPaySystem["ID"],
					 'PAY_SYSTEM_NAME' => $arPaySystem["NAME"],
					 'SUM'             => $needSum,
				));
				$this->arUserResult['PAY_SYSTEM_ID'] = $arPaySystem["ID"];
			}

			if(!empty($this->arUserResult["PREPAYMENT_MODE"]))
				$this->showOnlyPrepaymentPs($paySystemId);
		}

		if(!empty($innerPayment) && !empty($extPayment) && $remainingSum == 0) {
			$extPayment->delete();
		}
	}

	/**
	 * Set user budget data to $this->arResult. Returns sum to spend(including restrictions).
	 *
	 * @param Order $order
	 * @param bool  $recalculate
	 *
	 * @return array
	 * @throws Main\ObjectNotFoundException
	 */
	protected function getInnerPaySystemInfo(Order $order, $recalculate = false)
	{
		$arResult            = &$this->arResult;
		$innerPsId           = PaySystem\Manager::getInnerPaySystemId();
		$arPaySystemServices = array();
		$sumToSpend          = 0;

		if($this->arParams["PAY_FROM_ACCOUNT"] == "Y") {
			$this->loadUserAccount($order);

			if(!empty($arResult["USER_ACCOUNT"]) && $arResult["USER_ACCOUNT"]["CURRENT_BUDGET"] > 0) {
				$innerPayment        = $order->getPaymentCollection()->getInnerPayment();
				$arPaySystemServices = $recalculate ? $this->arPaySystemServiceAll : PaySystem\Manager::getListWithRestrictions($innerPayment);

				if(array_key_exists($innerPsId, $arPaySystemServices)) {
					$userBudget = floatval($arResult["USER_ACCOUNT"]["CURRENT_BUDGET"]);
					$sumRange   = Sale\Services\PaySystem\Restrictions\Manager::getPriceRange($innerPayment, $innerPsId);

					if($this->arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y')
						$sumRange['MIN'] = $order->getPrice();

					if(!empty($sumRange)) {
						if((empty($sumRange['MIN']) || $sumRange['MIN'] <= $userBudget)
							 && (empty($sumRange['MAX']) || $sumRange['MAX'] >= $userBudget)
						)
							$sumToSpend = $userBudget;

						if(!empty($sumRange['MAX']) && $sumRange['MAX'] <= $userBudget)
							$sumToSpend = $sumRange['MAX'];
					}
					else
						$sumToSpend = $userBudget;

					if($sumToSpend > 0) {
						$arResult["PAY_FROM_ACCOUNT"]        = "Y";
						$arResult["CURRENT_BUDGET_FORMATED"] = SaleFormatCurrency($arResult["USER_ACCOUNT"]["CURRENT_BUDGET"], $order->getCurrency());
					}
					else {
						$arResult["PAY_FROM_ACCOUNT"] = "N";
						unset($arResult["CURRENT_BUDGET_FORMATED"]);
					}
				}
				else
					$arResult["PAY_FROM_ACCOUNT"] = "N";
			}
			else
				$arResult["PAY_FROM_ACCOUNT"] = "N";
		}

		return array($sumToSpend, $arPaySystemServices);
	}

	protected function loadUserAccount(Order $order)
	{
		if(!isset($this->arResult["USER_ACCOUNT"])) {
			$dbUserAccount                  = CSaleUserAccount::GetList(
				 array(),
				 array(
						"USER_ID"  => $order->getUserId(),
						"CURRENCY" => $order->getCurrency(),
				 )
			);
			$this->arResult["USER_ACCOUNT"] = $dbUserAccount->Fetch();
		}
	}

	public function getInnerPayment(Order $order)
	{
		/** @var Payment $payment */
		foreach($order->getPaymentCollection() as $payment) {
			if($payment->getPaymentSystemId() == PaySystem\Manager::getInnerPaySystemId())
				return $payment;
		}

		return null;
	}

	public function getExternalPayment(Order $order)
	{
		/** @var Payment $payment */
		foreach($order->getPaymentCollection() as $payment) {
			if($payment->getPaymentSystemId() != PaySystem\Manager::getInnerPaySystemId())
				return $payment;
		}

		return null;
	}

	protected function showOnlyPrepaymentPs($paySystemId)
	{
		if(empty($this->arPaySystemServiceAll) || intval($paySystemId) == 0)
			return;

		foreach($this->arPaySystemServiceAll as $key => $psService) {
			if($paySystemId != $psService['ID'])
				unset($this->arPaySystemServiceAll[ $key ]);
		}
	}

	/**
	 * Returns created order object based on user and request data.
	 * Execution of 'OnSaleComponentOrderCreated' event.
	 *
	 * @param $userId
	 *
	 * @return Order
	 */
	protected function getOrder($userId)
	{
		global $USER;

		/** @var Order $order */
		$order = Order::create($this->context->getSite(), $userId);
		$order->isStartField();

		$this->initLastOrderData($order);

		if($this->arParams["USE_PREPAYMENT"] == "Y") {
			$this->usePrepayment($order);
		}

		$isPersonTypeChanged = $this->initPersonType($order);
		$this->initProperties($order, $isPersonTypeChanged);
		$this->initBasket($order);

		$taxes = $order->getTax();
		$taxes->setDeliveryCalculate($this->arParams['COUNT_DELIVERY_TAX'] == "Y");

		$shipment = $this->initShipment($order);

		$order->doFinalAction(true);

		if($this->arParams["DELIVERY_TO_PAYSYSTEM"] == "d2p") {
			$this->initDelivery($shipment);
			$this->initPayment($order);
		}
		else {
			$this->initPayment($order);
			$this->initDelivery($shipment);
		}

		$this->initOrderFields($order);
		$this->recalculatePayment($order);

		$eventParameters = array(
			 $order, &$this->arUserResult, $this->request,
			 &$this->arParams, &$this->arResult, &$this->arDeliveryServiceAll, &$this->arPaySystemServiceAll,
		);
		foreach(GetModuleEvents("sale", 'OnSaleComponentOrderCreated', true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, $eventParameters);
		}

		// no need to calculate deliveries on order saving
		if($this->action !== 'saveOrderAjax') {
			$this->calculateDeliveries($order);
		}

		return $order;
	}

	protected function initLastOrderData(Order $order)
	{
		global $USER;

		if(
			 ($this->request->getRequestMethod() === 'GET' || $this->request->get('do_authorize') === 'Y' || $this->request->get('do_register') === 'Y')
			 && $this->arUserResult['USE_PRELOAD']
			 && $USER->IsAuthorized()
		  )
		{
			$showData      = array();
			$lastOrderData = $this->getLastOrderData($order);

			if(!empty($lastOrderData)) {
				if(!empty($lastOrderData['PERSON_TYPE_ID']))
					$this->arUserResult['PERSON_TYPE_ID'] = $showData['PERSON_TYPE_ID'] = $lastOrderData['PERSON_TYPE_ID'];

				if(!empty($lastOrderData['PAY_CURRENT_ACCOUNT']))
					$this->arUserResult['PAY_CURRENT_ACCOUNT'] = $showData['PAY_CURRENT_ACCOUNT'] = $lastOrderData['PAY_CURRENT_ACCOUNT'];

				if(!empty($lastOrderData['PAY_SYSTEM_ID']))
					$this->arUserResult['PAY_SYSTEM_ID'] = $showData['PAY_SYSTEM_ID'] = $lastOrderData['PAY_SYSTEM_ID'];

				if(!empty($lastOrderData['DELIVERY_ID']))
					$this->arUserResult['DELIVERY_ID'] = $showData['DELIVERY_ID'] = $lastOrderData['DELIVERY_ID'];

				if(!empty($lastOrderData['DELIVERY_EXTRA_SERVICES']))
					$this->arUserResult['DELIVERY_EXTRA_SERVICES'] = $showData['DELIVERY_EXTRA_SERVICES'] = $lastOrderData['DELIVERY_EXTRA_SERVICES'];

				if(!empty($lastOrderData['BUYER_STORE']))
					$this->arUserResult['BUYER_STORE'] = $showData['BUYER_STORE'] = $lastOrderData['BUYER_STORE'];

				$this->arUserResult['LAST_ORDER_DATA'] = $showData;
			}
		}
	}

	protected function getLastOrderData(Order $order)
	{
		$lastOrderData = array();

		$filter = array(
			 'filter' => array(
					'USER_ID' => $order->getUserId(),
					'LID'     => $order->getSiteId(),
			 ),
			 'select' => array('ID'),
			 'order'  => array('ID' => 'DESC'),
			 'limit'  => 1,
		);

		if($arOrder = Order::getList($filter)->fetch()) {
			/** @var Order $lastOrder */
			$lastOrder                       = Order::load($arOrder['ID']);
			$lastOrderData['PERSON_TYPE_ID'] = $lastOrder->getPersonTypeId();

			if($payment = $this->getInnerPayment($lastOrder))
				$lastOrderData['PAY_CURRENT_ACCOUNT'] = 'Y';

			if($payment = $this->getExternalPayment($lastOrder))
				$lastOrderData['PAY_SYSTEM_ID'] = $payment->getPaymentSystemId();

			if($shipment = $this->getCurrentShipment($lastOrder)) {
				$lastOrderData['DELIVERY_ID']                                           = $shipment->getDeliveryId();
				$lastOrderData['BUYER_STORE']                                           = $shipment->getStoreId();
				$lastOrderData['DELIVERY_EXTRA_SERVICES'][ $shipment->getDeliveryId() ] = $shipment->getExtraServices();
				if($storeFields = Delivery\ExtraServices\Manager::getStoresFields($lastOrderData['DELIVERY_ID'], false))
					unset($lastOrderData['DELIVERY_EXTRA_SERVICES'][ $shipment->getDeliveryId() ][ $storeFields['ID'] ]);
			}
		}

		return $lastOrderData;
	}

	/**
	 * Check if PayPal prepayment is available
	 *
	 * @param Order $order
	 *
	 * @throws Main\ArgumentException
	 * @throws Main\NotSupportedException
	 */
	protected function usePrepayment(Order $order)
	{
		global $APPLICATION;
		$arResult = &$this->arResult;

		$arPersonTypes = PersonType::load($this->context->getSite());
		$arPersonTypes = array_keys($arPersonTypes);
		if(!empty($arPersonTypes)) {
			$paySysAction = PaySystem\Manager::getList(array(
				 'select' => array(
						"ID", "PAY_SYSTEM_ID", "PERSON_TYPE_ID", "NAME", "ACTION_FILE", "RESULT_FILE",
						"NEW_WINDOW", "PARAMS", "ENCODING", "LOGOTIP",
				 ),
				 'filter' => array(
						"ACTIVE"         => "Y",
						"HAVE_PREPAY"    => "Y",
						"PERSON_TYPE_ID" => $arPersonTypes,
				 ),
			));
			if($arPaySysAction = $paySysAction->fetch()) {
				$arResult["PREPAY_PS"]       = $arPaySysAction;
				$arResult["HAVE_PREPAYMENT"] = true;

				$this->prePaymentService = new PaySystem\Service($arPaySysAction);
				if($this->prePaymentService->isPrePayable()) {
					$this->prePaymentService->initPrePayment(null, $this->request);
					if($this->request->get('paypal') == 'Y' && $this->request->get('token')) {
						$arResult["PREPAY_ORDER_PROPS"] = $this->prePaymentService->getPrePaymentProps();
						if(intval($this->arUserResult['PAY_SYSTEM_ID']) <= 0) {
							$this->arUserResult["PERSON_TYPE_ID"] = $arResult["PREPAY_PS"]["PERSON_TYPE_ID"];
						}
						$this->arUserResult["PREPAYMENT_MODE"] = true;
						$this->arUserResult["PAY_SYSTEM_ID"]   = $arResult["PREPAY_PS"]["ID"];
					}
					else {
						if($this->arUserResult["PAY_SYSTEM_ID"] == $arResult["PREPAY_PS"]["ID"]) {
							$basketItems = array();
							$basket      = Sale\Basket::loadItemsForFUser(CSaleBasket::GetBasketUserID(), $this->context->getSite())->getOrderableItems();
							/** @var Sale\BasketItem $item */
							foreach($basket as $key => $item) {
								$basketItems[ $key ]["NAME"]     = $item->getField('NAME');
								$basketItems[ $key ]["PRICE"]    = $item->getPrice();
								$basketItems[ $key ]["QUANTITY"] = $item->getQuantity();
							}
							$orderData                = array(
								 "PATH_TO_ORDER" => $APPLICATION->GetCurPage(),
								 "AMOUNT"        => $order->getPrice(),
								 "ORDER_REQUEST" => "Y",
								 "BASKET_ITEMS"  => $basketItems,
							);
							$arResult["REDIRECT_URL"] = $this->prePaymentService->basketButtonAction($orderData);
							if($arResult["REDIRECT_URL"] != '') {
								$arResult["NEED_REDIRECT"] = "Y";
							}
						}
					}

					ob_start();
					$this->prePaymentService->setTemplateParams(array(
						 'TOKEN'    => $this->request->get('token'),
						 'PAYER_ID' => $this->request->get('PayerID'),
					));
					$this->prePaymentService->showTemplate(null, 'prepay_hidden_fields');
					$arResult["PREPAY_ADIT_FIELDS"] = ob_get_contents();
					ob_end_clean();
				}
			}
		}
	}

	/**
	 * Initialization of person types. Set person type data to $this->arResult.
	 * Return true if person type changed.
	 * Execution of 'OnSaleComponentOrderOneStepPersonType' event
	 *
	 * @param Order $order
	 *
	 * @return bool
	 * @throws Main\ArgumentException
	 */
	protected function initPersonType(Order $order)
	{
		$arResult        = &$this->arResult;
		$personTypeId    = intval($this->arUserResult['PERSON_TYPE_ID']);
		$personTypeIdOld = intval($this->arUserResult['PERSON_TYPE_OLD']);

		$personTypes = PersonType::load($this->context->getSite());
		foreach($personTypes as $personType) {
			if($personTypeId === intval($personType["ID"]) || !array_key_exists($personTypeId, $personTypes)) {
				$personTypeId = intval($personType["ID"]);
				$order->setPersonTypeId($personTypeId);
				$this->arUserResult['PERSON_TYPE_ID'] = $personTypeId;
				$personType["CHECKED"]                = "Y";
			}
			$arResult["PERSON_TYPE"][ $personType["ID"] ] = $personType;
		}

		if($personTypeId == 0)
			$this->addError(Loc::getMessage("SOA_ERROR_PERSON_TYPE"), self::REGION_BLOCK);

		$this->executeEvent('OnSaleComponentOrderOneStepPersonType', $order);

		return count($arResult["PERSON_TYPE"]) > 1 && ($personTypeId !== $personTypeIdOld);
	}

	/**
	 * Initialization of order properties from request, user profile, default values.
	 * Checking properties (if saving order) and setting to order.
	 * Execution of 'OnSaleComponentOrderProperties' event.
	 *
	 * @param Order $order
	 * @param       $isPersonTypeChanged
	 *
	 * @throws Main\ArgumentException
	 */
	//HACK: $useProfileProperties
	protected function initProperties(Order $order, $isPersonTypeChanged)
	{
		global $USER;

		$arResult          = &$this->arResult;
		$isProfileChanged  = $this->arUserResult['PROFILE_CHANGE'] == 'Y';
		$justAuthorized    = $this->request->get('do_authorize') == 'Y' || $this->request->get('do_register') == 'Y';
		$profileProperties = array();
		$orderProperties   = $this->getPropertyValuesFromRequest();
		$firstLoad         = $this->request->getRequestMethod() === 'GET';

		$this->initUserProfiles($order, $isPersonTypeChanged);

		$loadFromProfile      = $firstLoad || $isProfileChanged || $isPersonTypeChanged;
		$haveProfileId        = intval($this->arUserResult['PROFILE_ID']) > 0;
		$useProfileProperties = ($loadFromProfile || $justAuthorized) && $haveProfileId;

		if($useProfileProperties) {
			$dbUserPropsValues = CSaleOrderUserPropsValue::GetList(
				 array("SORT" => "ASC"),
				 array(
						"USER_PROPS_ID" => intval($this->arUserResult['PROFILE_ID']),
						"USER_ID"       => intval($order->getUserId()),
				 ),
				 false,
				 false,
				 array("VALUE", "PROP_TYPE", "VARIANT_NAME", "SORT", "ORDER_PROPS_ID")
			);
			while($propValue = $dbUserPropsValues->Fetch()) {
				if($propValue["PROP_TYPE"] == "ENUM")
					$propValue["VALUE"] = explode(",", $propValue["VALUE"]);

				if($propValue["PROP_TYPE"] == "LOCATION" && !empty($propValue["VALUE"])) {

					$arLoc = LocationTable::getById($propValue["VALUE"])->fetch();
					if(!empty($arLoc))
						$propValue["VALUE"] = $arLoc['CODE'];
				}

				if($propValue["PROP_TYPE"] == "FILE" && !empty($propValue["VALUE"])) {
					if(CheckSerializedData($propValue['VALUE'])
						 && ($values = @unserialize($propValue['VALUE'])) !== false
					) {
						$propValue['VALUE'] = array();
						foreach($values as $value)
							$propValue['VALUE'][] = CFile::GetFileArray($value);
					}
				}

				$profileProperties[ $propValue["ORDER_PROPS_ID"] ] = $propValue['VALUE'];
			}
		}

		$propertyCollection = $order->getPropertyCollection();

		/** @var Sale\PropertyValue $property */
		foreach($propertyCollection as $property) {
			if($property->isUtil())
				continue;

			$arProperty = $property->getProperty();

			if($arProperty['USER_PROPS'] == 'Y') {
				if($isProfileChanged && !$haveProfileId) {
					$curVal = '';
				}
				elseif($useProfileProperties) { //Заполнит поля из профиля
					$curVal = $profileProperties[ $arProperty['ID'] ];
				}
				elseif(isset($orderProperties[ $arProperty['ID'] ])) {
					$curVal = $orderProperties[ $arProperty['ID'] ];
				}
				else {
					$curVal = '';
				}
			}
			else {
				$curVal = $orderProperties[ $arProperty['ID'] ];
			}

			if($arResult["HAVE_PREPAYMENT"] && !empty($arResult["PREPAY_ORDER_PROPS"][ $arProperty["CODE"] ])) {
				if($arProperty["TYPE"] == 'LOCATION') {
					$cityName   = ToUpper($arResult["PREPAY_ORDER_PROPS"][ $arProperty["CODE"] ]);
					$arLocation = LocationTable::getList(array(
						 'select' => array('CODE'),
						 'filter' => array('NAME.NAME_UPPER' => $cityName),
					))->fetch();
					if(!empty($arLocation)) {
						$curVal = $arLocation['CODE'];
					}
				}
				else
					$curVal = $arResult["PREPAY_ORDER_PROPS"][ $arProperty["CODE"] ];
			}

			if(empty($curVal)) {
				if(!empty($arProperty["DEFAULT_VALUE"])) {
					$curVal = $arProperty["DEFAULT_VALUE"];
				}
				else if($isPersonTypeChanged || $justAuthorized) {
					$curVal = $this->getValueFromCUser($arProperty);
				}
			}

			if($arProperty["TYPE"] == 'LOCATION') {
				if((!$loadFromProfile || $this->request->get('PROFILE_ID') === '0')
					 && $this->request->get('location_type') != 'code'
				) {
					$curVal = CSaleLocation::getLocationCODEbyID($curVal);
				}
			}

			$this->arUserResult['ORDER_PROP'][ $arProperty["ID"] ] = $curVal;
		}

		$this->checkZipProperty($order, $loadFromProfile);
		$this->checkAltLocationProperty($order, $useProfileProperties, $profileProperties);

		foreach(GetModuleEvents("sale", 'OnSaleComponentOrderProperties', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$this->arUserResult, $this->request, &$this->arParams, &$this->arResult));


		if($this->isOrderConfirmed) {
			$res = $propertyCollection->checkErrors(array('PROPERTIES' => $this->arUserResult['ORDER_PROP']), array(), true);
			if(!$res->isSuccess())
				$this->addError($res, self::PROPERTY_BLOCK);
		}

		$res = $propertyCollection->setValuesFromPost(array('PROPERTIES' => $this->arUserResult['ORDER_PROP']), array());
		if($this->isOrderConfirmed && !$res->isSuccess())
			$this->addError($res, self::PROPERTY_BLOCK);
	}

	/**
	 * Returns array of order properties from request
	 *
	 * @return array
	 */
	protected function getPropertyValuesFromRequest()
	{
		$orderProperties = array();

		foreach($this->request as $k => $v) {
			if(strpos($k, "ORDER_PROP_") !== false) {
				if(strpos($k, "[]") !== false)
					$orderPropId = intval(substr($k, strlen("ORDER_PROP_"), strlen($k) - 2));
				else
					$orderPropId = intval(substr($k, strlen("ORDER_PROP_")));

				if($orderPropId > 0)
					$orderProperties[ $orderPropId ] = $v;
			}
		}

		foreach($this->request->getFileList() as $k => $arFileData) {
			if(strpos($k, "ORDER_PROP_") !== false) {
				$orderPropId = intval(substr($k, strlen("ORDER_PROP_")));

				if(is_array($arFileData)) {
					foreach($arFileData as $param_name => $value) {
						if(is_array($value)) {
							foreach($value as $nIndex => $val) {
								if(strlen($arFileData["name"][ $nIndex ]) > 0)
									$orderProperties[ $orderPropId ][ $nIndex ][ $param_name ] = $val;
							}
						}
						else
							$orderProperties[ $orderPropId ][ $param_name ] = $value;
					}
				}
			}
		}

		return $orderProperties;
	}

	/**
	 * Initialization of user profiles. Set user profiles data to $this->arResult.
	 *
	 * @param Order $order
	 * @param       $isPersonTypeChanged
	 */
	//HACK: if($USER->IsAuthorized())
	protected function initUserProfiles(Order $order, $isPersonTypeChanged)
	{
		global $USER;

		$arResult = &$this->arResult;

		$justAuthorized = $this->request->get('do_authorize') == 'Y' || $this->request->get('do_register') == 'Y';
		$bFirst         = false;
		$dbUserProfiles = CSaleOrderUserProps::GetList(
			 array("DATE_UPDATE" => "DESC"),
			 array(
					"PERSON_TYPE_ID" => $order->getPersonTypeId(),
					"USER_ID"        => $order->getUserId(),
			 )
		);
		while($arUserProfiles = $dbUserProfiles->GetNext()) {
			if(!$bFirst && (empty($this->arUserResult['PROFILE_CHANGE']) || $isPersonTypeChanged || $justAuthorized)) {

				$bFirst = true;

				if($USER->IsAuthorized())
					$this->arUserResult['PROFILE_ID'] = intval($arUserProfiles["ID"]);
			}

			if(intval($this->arUserResult['PROFILE_ID']) == intval($arUserProfiles["ID"]))
				$arUserProfiles["CHECKED"] = "Y";

			$arResult["ORDER_PROP"]["USER_PROFILES"][ $arUserProfiles["ID"] ] = $arUserProfiles;
		}
	}

	/**
	 * Returns user property value from CUser
	 *
	 * @param  $property
	 *
	 * @return  string
	 */
	protected function getValueFromCUser($property)
	{
		global $USER;

		$value = '';

		if($property['IS_EMAIL'] === 'Y') {
			$value = $USER->GetEmail();
		}
		elseif($property['IS_PAYER'] === 'Y') {
			$rsUser = CUser::GetByID($USER->GetID());
			if($arUser = $rsUser->Fetch()) {
				$value = CUser::FormatName(
					 CSite::GetNameFormat(false),
					 array(
							'NAME'        => $arUser['NAME'],
							'LAST_NAME'   => $arUser['LAST_NAME'],
							'SECOND_NAME' => $arUser['SECOND_NAME'],
					 ),
					 false,
					 false
				);
			}
		}
		elseif($property['IS_PHONE'] === 'Y') {
			$rsUser = CUser::GetByID($USER->GetID());
			if($arUser = $rsUser->Fetch()) {
				if(!empty($arUser['PERSONAL_PHONE'])) {
					$value = $arUser['PERSONAL_PHONE'];
				}
				elseif(!empty($arUser['PERSONAL_MOBILE'])) {
					$value = $arUser['PERSONAL_MOBILE'];
				}
			}
		}
		elseif($property['IS_ADDRESS'] === 'Y') {
			$rsUser = CUser::GetByID($USER->GetID());
			if($arUser = $rsUser->Fetch()) {
				if(!empty($arUser['PERSONAL_STREET'])) {
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
		$zip                = $propertyCollection->getDeliveryLocationZip();
		$location           = $order->getPropertyCollection()->getDeliveryLocation();
		if(!empty($zip) && !empty($location)) {
			$locId           = $location->getField('ORDER_PROPS_ID');
			$locValue        = $this->arUserResult['ORDER_PROP'][ $locId ];
			$locationChanged = $locValue != $this->request->get('RECENT_DELIVERY_VALUE');

			if(!$loadFromProfile && $locationChanged) {
				$zipList = CSaleLocation::GetLocationZIP(CSaleLocation::getLocationIDbyCODE($locValue));
				if($arZip = $zipList->Fetch()) {
					if(strlen($arZip['ZIP']) > 0)
						$this->arUserResult['ORDER_PROP'][ $zip->getField('ORDER_PROPS_ID') ] = $arZip['ZIP'];
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
		$propertyCollection           = $order->getPropertyCollection();
		/** @var Sale\PropertyValue $property */
		foreach($propertyCollection as $property) {
			if($property->isUtil())
				continue;

			if($property->getType() == 'LOCATION') {
				$propertyFields = $property->getProperty();
				if((int)$propertyFields['INPUT_FIELD_LOCATION'] > 0) {
					if($useProfileProperties) {
						$deleteAltProp = empty($profileProperties[ $propertyFields['INPUT_FIELD_LOCATION'] ]);
					}
					else {
						$deleteAltProp = !isset($locationAltPropDisplayManual[ $propertyFields['ID'] ])
							 || !(bool)$locationAltPropDisplayManual[ $propertyFields['ID'] ];
					}

					if($deleteAltProp && isset($this->arUserResult['ORDER_PROP'][ $propertyFields['INPUT_FIELD_LOCATION'] ]))
						unset($this->arUserResult['ORDER_PROP'][ $propertyFields['INPUT_FIELD_LOCATION'] ]);
				}
			}
		}
	}

	/**
	 * Append basket(for current FUser) to order object
	 *
	 * @param Order $order
	 *
	 * @throws Main\ObjectNotFoundException
	 */
	protected function initBasket(Order $order)
	{
		$fUserId = CSaleBasket::GetBasketUserID();
		$siteId  = $this->context->getSite();
		CSaleBasket::UpdateBasketPrices($fUserId, $siteId);
		Sale\Compatible\DiscountCompatibility::stopUsageCompatible();
		$basket = Sale\Basket::loadItemsForFUser($fUserId, $siteId)->getOrderableItems();
		$order->setBasket($basket);

		if(count($basket) == 0) {
			global $APPLICATION;

			if($this->arParams["DISABLE_BASKET_REDIRECT"] == 'Y') {
				$this->arResult['SHOW_EMPTY_BASKET'] = true;
				if($this->request->get('json') == "Y" || $this->isRequestViaAjax) {
					$APPLICATION->RestartBuffer();
					echo json_encode(array("success" => "N", "redirect" => $this->arParams['~CURRENT_PAGE']));
					die();
				}
			}
			else {
				if($this->request->get('json') == "Y" || $this->isRequestViaAjax) {
					$APPLICATION->RestartBuffer();
					echo json_encode(array("success" => "N", "redirect" => $this->arParams["PATH_TO_BASKET"]));
					die();
				}
				LocalRedirect($this->arParams["PATH_TO_BASKET"]);
				die();
			}
		}
	}

	/**
	 * Initialization of shipment object. Filling with basket items.
	 *
	 * @param Order $order
	 *
	 * @return Shipment
	 * @throws Main\ArgumentTypeException
	 * @throws Main\NotSupportedException
	 */
	public function initShipment(Order $order)
	{
		$shipmentCollection     = $order->getShipmentCollection();
		$shipment               = $shipmentCollection->createItem();
		$shipmentItemCollection = $shipment->getShipmentItemCollection();
		$shipment->setField('CURRENCY', $order->getCurrency());

		/** @var Sale\BasketItem $item */
		foreach($order->getBasket() as $item) {
			/** @var Sale\ShipmentItem $shipmentItem */
			$shipmentItem = $shipmentItemCollection->createItem($item);
			$shipmentItem->setQuantity($item->getQuantity());
		}

		return $shipment;
	}

	/**
	 * Initialization of inner/external payment objects with first/selected pay system services.
	 *
	 * @param Order $order
	 *
	 * @throws Main\ObjectNotFoundException
	 */
	protected function initPayment(Order $order)
	{
		$this->arPaySystemServiceAll = array();
		$paySystemId                 = intval($this->arUserResult['PAY_SYSTEM_ID']);
		$paymentCollection           = $order->getPaymentCollection();
		$innerPayment                = null;

		list($sumToSpend, $arPsFromInner) = $this->getInnerPaySystemInfo($order);

		if($sumToSpend > 0) {
			$this->arPaySystemServiceAll = $arPsFromInner;
			$this->arActivePaySystems    = $arPsFromInner;

			if($this->arUserResult['PAY_CURRENT_ACCOUNT'] == "Y") {
				$innerPayment = $this->getInnerPayment($order);
				$sumToPay     = $sumToSpend >= $order->getPrice() ? $order->getPrice() : $sumToSpend;
				$innerPayment->setField('SUM', $sumToPay);
			}
			else {
				$paymentCollection->getInnerPayment()->delete();
			}
		}

		$remainingSum = $order->getPrice() - $paymentCollection->getSum();
		if($remainingSum > 0 || $order->getPrice() == 0) {
			/** @var Payment $extPayment */
			$extPayment = $paymentCollection->createItem();
			$extPayment->setField('SUM', $remainingSum);
			$arPaySystemServices = PaySystem\Manager::getListWithRestrictions($extPayment);

			if($sumToSpend > 0)
				$this->arActivePaySystems = array_intersect_key($this->arActivePaySystems, $arPaySystemServices);
			else
				$this->arActivePaySystems = $arPaySystemServices;

			$this->arPaySystemServiceAll += $arPaySystemServices;

			if(array_key_exists($paySystemId, $this->arActivePaySystems)) {
				$arPaySystem = $this->arActivePaySystems[ $paySystemId ];
			}
			else {
				reset($this->arActivePaySystems);

				if(key($this->arActivePaySystems) == PaySystem\Manager::getInnerPaySystemId()) {
					if($sumToSpend > 0) {
						if(count($this->arActivePaySystems) > 1) {
							next($this->arActivePaySystems);
						}
						else if(empty($innerPayment)) {
							$remainingSum = $remainingSum > $sumToSpend ? $sumToSpend : $remainingSum;
							$extPayment->setField('SUM', $remainingSum);
						}
						else
							$extPayment->delete();

						$remainingSum = $order->getPrice() - $paymentCollection->getSum();
						if($remainingSum > 0) {
							$this->addWarning(Loc::getMessage("INNER_PAYMENT_BALANCE_ERROR"), self::PAY_SYSTEM_BLOCK);
							$order->setFields(array(
								 'MARKED'        => 'Y',
								 'REASON_MARKED' => Loc::getMessage("INNER_PAYMENT_BALANCE_ERROR"),
							));
						}
					}
					else {
						unset($this->arActivePaySystems[ PaySystem\Manager::getInnerPaySystemId() ]);
						unset($this->arPaySystemServiceAll[ PaySystem\Manager::getInnerPaySystemId() ]);
					}
				}

				$arPaySystem = current($this->arActivePaySystems);

				if(!empty($arPaySystem) && $paySystemId != 0)
					$this->addWarning(Loc::getMessage("PAY_SYSTEM_CHANGE_WARNING"), self::PAY_SYSTEM_BLOCK);
			}

			if(!empty($arPaySystem)) {
				$extPayment->setFields(array(
					 'PAY_SYSTEM_ID'   => $arPaySystem["ID"],
					 'PAY_SYSTEM_NAME' => $arPaySystem["NAME"],
				));
				$extPayment->setField('COMPANY_ID', Company\Manager::getAvailableCompanyIdByEntity($extPayment));
				$this->arUserResult['PAY_SYSTEM_ID'] = $arPaySystem["ID"];
			}
			else
				$extPayment->delete();
		}

		if(empty($this->arPaySystemServiceAll))
			$this->addError(Loc::getMessage("SOA_ERROR_PAY_SYSTEM"), self::PAY_SYSTEM_BLOCK);

		if(!empty($this->arUserResult["PREPAYMENT_MODE"]))
			$this->showOnlyPrepaymentPs($paySystemId);
	}

	/**
	 * Check required fields for actual properties(with/without relations). Set user description.
	 *
	 * @param Order $order
	 *
	 * @throws Main\ObjectNotFoundException
	 */
	protected function initOrderFields(Order $order)
	{
		if($this->isOrderConfirmed) {
			$actualProperties   = array();
			$paymentSystemIds   = $order->getPaymentSystemId();
			$deliverySystemIds  = $order->getDeliverySystemId();
			$propertyCollection = $order->getPropertyCollection();

			/** @var Sale\PropertyValue $property */
			foreach($propertyCollection as $property) {
				if($property->isUtil())
					continue;

				$arProperty = $property->getProperty();
				if(isset($arProperty['RELATION'])
					 && !$this->checkRelatedProperty($arProperty, $paymentSystemIds, $deliverySystemIds)
				) {
					unset($this->arUserResult['ORDER_PROP'][ $property->getPropertyId() ]);
					continue;
				}

				$actualProperties[ $property->getPropertyId() ] = $this->arUserResult['ORDER_PROP'][ $property->getPropertyId() ];
			}

			$res = $propertyCollection->checkRequired(array_keys($actualProperties), array('PROPERTIES' => $actualProperties));
			if(!$res->isSuccess())
				$this->addError($res, self::PROPERTY_BLOCK);
		}

		$order->setField("USER_DESCRIPTION", $this->arUserResult['ORDER_DESCRIPTION']);
	}

	/**
	 * Returns true if current property is valid for selected payment & delivery
	 *
	 * @param $property
	 * @param $arPaymentId
	 * @param $arDeliveryId
	 *
	 * @return bool
	 */
	protected function checkRelatedProperty($property, $arPaymentId, $arDeliveryId)
	{
		$okByPs       = null;
		$okByDelivery = null;

		if(is_array($property['RELATION']) && !empty($property['RELATION'])) {
			foreach($property['RELATION'] as $relation) {
				if(empty($okByPs) && $relation['ENTITY_TYPE'] == 'P')
					$okByPs = in_array($relation['ENTITY_ID'], $arPaymentId);

				if(empty($okByDelivery) && $relation['ENTITY_TYPE'] == 'D')
					$okByDelivery = in_array($relation['ENTITY_ID'], $arDeliveryId);
			}
		}

		return ((is_null($okByPs) || $okByPs) && (is_null($okByDelivery) || $okByDelivery));
	}

	/**
	 * Calculates all available deliveries for order object.
	 * Uses cloned order not to harm real order.
	 * Execution of 'OnSaleComponentOrderDeliveriesCalculated' event
	 *
	 * @param Order $order
	 *
	 * @throws Main\NotSupportedException
	 */
	protected function calculateDeliveries(Order $order)
	{
		$this->arResult['DELIVERY'] = array();
		/** @var Shipment $shipment */
		$shipment = $this->getCurrentShipment($order);

		if(!empty($this->arDeliveryServiceAll)) {
			$problemDeliveries = array();
			/** @var Order $clonedOrder */
			$clonedOrder = $order->createClone();
			/** @var Shipment $clonedShipment */
			$clonedShipment = $this->getCurrentShipment($clonedOrder);
			$clonedShipment->setField('CUSTOM_PRICE_DELIVERY', 'N');

			foreach($this->arDeliveryServiceAll as $key => $deliveryObj) {
				$calcResult = false;
				$calcOrder  = false;
				$arDelivery = array();

				if(intval($shipment->getDeliveryId()) == intval($deliveryObj->getId())) {
					$arDelivery['CHECKED'] = 'Y';
					$mustBeCalculated      = true;
					$calcResult            = $deliveryObj->calculate($shipment);
					$calcOrder             = $order;
				}
				else {
					$mustBeCalculated = $this->arParams['DELIVERY_NO_AJAX'] === 'Y' || $deliveryObj->isCalculatePriceImmediately();
					if($mustBeCalculated) {
						$clonedShipment->setField('DELIVERY_ID', $deliveryObj->getId());
						$clonedOrder->getShipmentCollection()->calculateDelivery();
						$calcResult = $deliveryObj->calculate($clonedShipment);
						$calcOrder  = $clonedOrder;
					}
				}

				if($mustBeCalculated) {
					if($calcResult->isSuccess()) {
						$arDelivery['PRICE']          = $calcResult->getPrice();
						$arDelivery['PRICE_FORMATED'] = SaleFormatCurrency($calcResult->getPrice(), $calcOrder->getCurrency());

						$currentCalcDeliveryPrice = $calcOrder->getDeliveryPrice();
						if($currentCalcDeliveryPrice >= 0 && $calcResult->getPrice() != $currentCalcDeliveryPrice) {
							$arDelivery['DELIVERY_DISCOUNT_PRICE']          = $currentCalcDeliveryPrice;
							$arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED'] = SaleFormatCurrency($currentCalcDeliveryPrice, $calcOrder->getCurrency());
						}

						if(strlen($calcResult->getPeriodDescription()) > 0) {
							$arDelivery['PERIOD_TEXT'] = $calcResult->getPeriodDescription();
						}
					}
					else {
						if(count($calcResult->getErrorMessages()) > 0) {
							foreach($calcResult->getErrorMessages() as $message) {
								$arDelivery['CALCULATE_ERRORS'] .= $message . '<br>';
							}
						}
						else {
							$arDelivery['CALCULATE_ERRORS'] = Loc::getMessage('SOA_DELIVERY_CALCULATE_ERROR');
						}


						if($arDelivery['CHECKED'] !== 'Y') {
							if($this->arParams['SHOW_NOT_CALCULATED_DELIVERIES'] === 'N') {
								unset($this->arDeliveryServiceAll[ $key ]);
								continue;
							}
							elseif($this->arParams['SHOW_NOT_CALCULATED_DELIVERIES'] === 'L') {
								$problemDeliveries[ $deliveryObj->getId() ] = $arDelivery;
								continue;
							}
						}
					}
				}

				$this->arResult['DELIVERY'][ $deliveryObj->getId() ] = $arDelivery;
			}
		}

		if(!empty($problemDeliveries)) {
			$this->arResult['DELIVERY'] += $problemDeliveries;
		}

		$eventParameters = array(
			 $order, &$this->arUserResult, $this->request,
			 &$this->arParams, &$this->arResult, &$this->arDeliveryServiceAll, &$this->arPaySystemServiceAll,
		);
		foreach(GetModuleEvents('sale', 'OnSaleComponentOrderDeliveriesCalculated', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, $eventParameters);
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

		$this->obtainDelivery();
		$this->obtainPaySystem();
		$this->obtainTaxes();
		$this->obtainTotal();

		$this->getJsDataResult();

		$this->arResult['USER_VALS'] = $this->arUserResult;

		$this->executeEvent('OnSaleComponentOrderOneStepProcess', $this->order);

		//try to avoid use "executeEvent" methods and use new events like this
		foreach(GetModuleEvents("sale", 'OnSaleComponentOrderResultPrepared', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($this->order, &$this->arUserResult, $this->request, &$this->arParams, &$this->arResult));
	}

	public function initGrid()
	{
		$this->arResult["GRID"]["HEADERS"]        = $this->getGridHeaders($this->arParams["PRODUCT_COLUMNS"]);
		$this->arResult["GRID"]["HEADERS_HIDDEN"] = $this->getGridHeaders($this->arParams["PRODUCT_COLUMNS_HIDDEN"]);
	}

	public function getGridHeaders($productColumns)
	{
		$arr = array();

		if(is_array($productColumns) && !empty($productColumns)) {
			$arCodes     = array();
			$iBlockProps = array();
			foreach($productColumns as $key => $value) // making grid headers array
			{
				if(strncmp($value, "PROPERTY_", 9) == 0) {
					$propCode = substr($value, 9);

					if($propCode == '')
						continue;

					$arCodes[] = $propCode;
				}
			}

			if($this->useCatalog && !empty($arCodes)) {
				$iBlockList      = array();
				$catalogIterator = Bitrix\Catalog\CatalogIblockTable::getList(array(
					 'select'  => array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID', 'SITE_ID' => 'IBLOCK_SITE.SITE_ID'),
					 'filter'  => array('SITE_ID' => SITE_ID),
					 'runtime' => array(
							'IBLOCK_SITE' => array(
								 'data_type' => 'Bitrix\Iblock\IblockSiteTable',
								 'reference' => array(
										'ref.IBLOCK_ID' => 'this.IBLOCK_ID',
								 ),
								 'join_type' => 'inner',
							),
					 ),
				));
				while($catalog = $catalogIterator->fetch()) {
					$iBlockList[ $catalog['IBLOCK_ID'] ] = $catalog['IBLOCK_ID'];

					if(intval($catalog['PRODUCT_IBLOCK_ID']) > 0)
						$iBlockList[ $catalog['PRODUCT_IBLOCK_ID'] ] = $catalog['PRODUCT_IBLOCK_ID'];
				}

				if(!empty($iBlockList)) {
					$propertyIterator = Bitrix\Iblock\PropertyTable::getList(array(
						 'select' => array('ID', 'IBLOCK_ID', 'NAME', 'ACTIVE', 'SORT', 'CODE', 'TIMESTAMP_X',
						                   'DEFAULT_VALUE', 'PROPERTY_TYPE', 'ROW_COUNT', 'COL_COUNT', 'LIST_TYPE',
						                   'MULTIPLE', 'XML_ID', 'FILE_TYPE', 'MULTIPLE_CNT', 'LINK_IBLOCK_ID', 'WITH_DESCRIPTION',
						                   'SEARCHABLE', 'FILTRABLE', 'IS_REQUIRED', 'VERSION', 'USER_TYPE', 'USER_TYPE_SETTINGS', 'HINT'),
						 'filter' => array(
								'@IBLOCK_ID' => array_keys($iBlockList),
								'=ACTIVE'    => 'Y',
								'@CODE'      => $arCodes,
						 ),
						 'order'  => array('SORT' => 'ASC', 'ID' => 'ASC'),
					));
					while($property = $propertyIterator->fetch()) {
						$this->arIblockProps[ $property['IBLOCK_ID'] ][ $property['CODE'] ] = $property;

						if(!isset($iBlockProps[ $property['CODE'] ]))
							$iBlockProps[ $property['CODE'] ] = $property;
					}
				}
			}

			foreach($productColumns as $key => $value) // making grid headers array
			{
				// processing iblock properties
				if(strncmp($value, "PROPERTY_", 9) == 0) {
					$propCode = substr($value, 9);

					if($propCode == '')
						continue;

					$this->arCustomSelectFields[] = $value; // array of iblock properties to select
					$id                           = $value . "_VALUE";
					$name                         = $value;

					if(array_key_exists($propCode, $iBlockProps)) {
						$name = $iBlockProps[ $propCode ]["NAME"];
					}
				}
				else {
					$id   = $key;
					$name = $value;
				}

				$arColumn = array(
					 "id"   => $id,
					 "name" => $name,
				);

				if($key == "PRICE_FORMATED")
					$arColumn["align"] = "right";

				$arr[] = $arColumn;
			}
		}

		return $arr;
	}

	/**
	 * Set basket items data from order object to $this->arResult
	 */
	protected function obtainBasket()
	{
		$arResult = &$this->arResult;

		$arResult["MAX_DIMENSIONS"] = $arResult["ITEMS_DIMENSIONS"] = array();
		$arResult["BASKET_ITEMS"]   = array();

		/** @var Sale\BasketItem $basketItem */
		foreach($this->order->getBasket() as $basketItem) {
			$arBasketItem = $basketItem->getFieldValues();
			if($basketItem->getVatRate() > 0) {
				$arResult["bUsingVat"]     = "Y";
				$arBasketItem["VAT_VALUE"] = $basketItem->getVat();
			}
			$arBasketItem["QUANTITY"]        = $basketItem->getQuantity();
			$arBasketItem["PRICE_FORMATED"]  = SaleFormatCurrency($basketItem->getPrice(), $this->order->getCurrency());
			$arBasketItem["WEIGHT_FORMATED"] = roundEx(doubleval($basketItem->getWeight() / $arResult["WEIGHT_KOEF"]), SALE_WEIGHT_PRECISION) . " " . $arResult["WEIGHT_UNIT"];
			$arBasketItem["DISCOUNT_PRICE"]  = $basketItem->getDiscountPrice();

			if(($basketItem->getDiscountPrice() + $basketItem->getPrice()) > 0)
				$arBasketItem["DISCOUNT_PRICE_PERCENT"] = $basketItem->getDiscountPrice() * 100 / ($basketItem->getDiscountPrice() + $basketItem->getPrice());
			else
				$arBasketItem["DISCOUNT_PRICE_PERCENT"] = 0;

			$arBasketItem["DISCOUNT_PRICE_PERCENT_FORMATED"] = Bitrix\Sale\PriceMaths::roundPrecision($arBasketItem["DISCOUNT_PRICE_PERCENT"]) . "%";
			$arBasketItem["BASE_PRICE_FORMATED"]             = SaleFormatCurrency($basketItem->getBasePrice(), $this->order->getCurrency());

			$arDim = unserialize($basketItem->getField('DIMENSIONS'));
			if(is_array($arDim)) {
				$arResult["MAX_DIMENSIONS"] = CSaleDeliveryHelper::getMaxDimensions(
					 array(
							$arDim["WIDTH"],
							$arDim["HEIGHT"],
							$arDim["LENGTH"],
					 ),
					 $arResult["MAX_DIMENSIONS"]);

				$arResult["ITEMS_DIMENSIONS"][] = $arDim;
			}

			$arBasketItem["PROPS"] = array();

			/** @var Sale\BasketPropertiesCollection $propertyCollection */
			$propertyCollection = $basketItem->getPropertyCollection();
			$propList           = $propertyCollection->getPropertyValues();
			foreach($propList as $key => &$prop) {
				if($prop['CODE'] == 'CATALOG.XML_ID' || $prop['CODE'] == 'PRODUCT.XML_ID' || $prop['CODE'] == 'SUM_OF_CHARGE')
					continue;

				$prop                    = array_filter($prop, array("CSaleBasketHelper", "filterFields"));
				$arBasketItem["PROPS"][] = $prop;
			}

			$this->arElementId[]               = $arBasketItem["PRODUCT_ID"];
			$arBasketItem["SUM_NUM"]           = $basketItem->getPrice() * $basketItem->getQuantity();
			$arBasketItem["SUM"]               = SaleFormatCurrency($basketItem->getPrice() * $basketItem->getQuantity(), $this->order->getCurrency());
			$arBasketItem["SUM_BASE"]          = $basketItem->getBasePrice() * $basketItem->getQuantity();
			$arBasketItem["SUM_BASE_FORMATED"] = SaleFormatCurrency($basketItem->getBasePrice() * $basketItem->getQuantity(), $this->order->getCurrency());

			$arResult["BASKET_ITEMS"][] = $arBasketItem;
		}
	}

	/**
	 * Set basket items data from iblocks (basket column properties, sku, preview pictures, etc) to $this->arResult
	 */
	protected function obtainPropertiesForIbElements()
	{
		$arResult                 = &$this->arResult;
		$arResult["GRID"]["ROWS"] = array();
		$arParents                = array();

		if($this->useCatalog) {
			$arParents = CCatalogSku::getProductList($this->arElementId);
			if(!empty($arParents)) {
				foreach($arParents as $productId => $arParent) {
					$this->arElementId[]              = $arParent["ID"];
					$this->arSku2Parent[ $productId ] = $arParent["ID"];
				}
			}
		}

		$arElementData = array();
		$arProductData = array();
		$elementIndex  = array();
		$res           = CIBlockElement::GetList(
			 array(),
			 array("=ID" => array_unique($this->arElementId)),
			 false,
			 false,
			 array("ID", "IBLOCK_ID", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PREVIEW_TEXT")
		);
		while($arElement = $res->Fetch()) {
			$arElementData[ $arElement["IBLOCK_ID"] ][] = $arElement["ID"];
			$arProductData[ $arElement["ID"] ]          = $arElement;
			$elementIndex[ $arElement["ID"] ]           = array();
		}

		foreach($arElementData as $iBlockId => $arElemId) {
			$arCodes = array();
			if(!empty($this->arIblockProps[ $iBlockId ]))
				$arCodes = array_keys($this->arIblockProps[ $iBlockId ]);

			$imageCode = $this->arParams['ADDITIONAL_PICT_PROP'][ $iBlockId ];

			if(!empty($imageCode) && !in_array($imageCode, $arCodes))
				$arCodes[] = $imageCode;

			if(!empty($arCodes)) {
				CIBlockElement::GetPropertyValuesArray($elementIndex, $iBlockId,
					 array("ID" => $arElemId),
					 array("CODE" => $arCodes)
				);
			}
		}
		unset($arElementData);

		$arAdditionalImages = array();
		foreach($elementIndex as $productId => $productProperties) {
			if(!empty($productProperties) && is_array($productProperties)) {
				foreach($productProperties as $code => $property) {
					if(!empty($this->arParams['ADDITIONAL_PICT_PROP'])
						 && array_key_exists($arProductData[ $productId ]['IBLOCK_ID'], $this->arParams['ADDITIONAL_PICT_PROP'])
					) {
						if($this->arParams['ADDITIONAL_PICT_PROP'][ $arProductData[ $productId ]['IBLOCK_ID'] ] == $code)
							$arAdditionalImages[ $productId ] = is_array($property['VALUE']) ? current($property['VALUE']) : $property['VALUE'];
					}

					if(!empty($this->arIblockProps[ $arProductData[ $productId ]['IBLOCK_ID'] ])
						 && array_key_exists($code, $this->arIblockProps[ $arProductData[ $productId ]['IBLOCK_ID'] ])
					) {
						if(is_array($property['VALUE']))
							$arProductData[ $productId ][ 'PROPERTY_' . $code . '_VALUE' ] = implode(', ', $property['VALUE']);
						else
							$arProductData[ $productId ][ 'PROPERTY_' . $code . '_VALUE' ] = $property['VALUE'];

						if(is_array($property['PROPERTY_VALUE_ID']))
							$arProductData[ $productId ][ 'PROPERTY_' . $code . '_VALUE_ID' ] = implode(', ', $property['PROPERTY_VALUE_ID']);
						else
							$arProductData[ $productId ][ 'PROPERTY_' . $code . '_VALUE_ID' ] = $property['PROPERTY_VALUE_ID'];

						if($property['PROPERTY_TYPE'] == 'L')
							$arProductData[ $productId ][ 'PROPERTY_' . $code . '_ENUM_ID' ] = $property['VALUE_ENUM_ID'];
					}
				}
			}
		}
		unset($elementIndex);

		$currentProductProperties = array();

		foreach($arResult["BASKET_ITEMS"] as &$arResultItem) {
			$productId                              = $arResultItem["PRODUCT_ID"];
			$arParent                               = $arParents[ $productId ];
			$itemIblockId                           = intval($arProductData[ $productId ]['IBLOCK_ID']);
			$currentProductProperties[ $productId ] = isset($this->arIblockProps[ $itemIblockId ])
				 ? $this->arIblockProps[ $itemIblockId ]
				 : array();

			if((int)$arProductData[ $productId ]["PREVIEW_PICTURE"] <= 0
				 && (int)$arProductData[ $productId ]["DETAIL_PICTURE"] <= 0
				 && $arParent
			) {
				$productId = $arParent["ID"];
			}

			if((int)$arProductData[ $productId ]["PREVIEW_PICTURE"] > 0)
				$arResultItem["PREVIEW_PICTURE"] = $arProductData[ $productId ]["PREVIEW_PICTURE"];
			if((int)$arProductData[ $productId ]["DETAIL_PICTURE"] > 0)
				$arResultItem["DETAIL_PICTURE"] = $arProductData[ $productId ]["DETAIL_PICTURE"];
			if($arProductData[ $productId ]["PREVIEW_TEXT"] != '')
				$arResultItem["PREVIEW_TEXT"] = $arProductData[ $productId ]["PREVIEW_TEXT"];

			if(!empty($arProductData[ $arResultItem["PRODUCT_ID"] ]) && is_array($arProductData[ $arResultItem["PRODUCT_ID"] ])) {
				foreach($arProductData[ $arResultItem["PRODUCT_ID"] ] as $key => $value) {
					if(strpos($key, "PROPERTY_") !== false)
						$arResultItem[ $key ] = $value;
				}
			}
			// if sku element doesn't have some property value - we'll show parent element value instead
			if(array_key_exists($arResultItem["PRODUCT_ID"], $this->arSku2Parent)) {
				$parentIblockId = $arProductData[ $this->arSku2Parent[ $arResultItem["PRODUCT_ID"] ] ]['IBLOCK_ID'];

				if(!empty($this->arIblockProps[ $parentIblockId ]))
					$currentProductProperties[ $arResultItem["PRODUCT_ID"] ] = array_merge($this->arIblockProps[ $parentIblockId ], $currentProductProperties[ $arResultItem["PRODUCT_ID"] ]);

				foreach($this->arCustomSelectFields as $field) {
					$fieldVal = $field . "_VALUE";
					$parentId = $this->arSku2Parent[ $arResultItem["PRODUCT_ID"] ];

					if((!isset($arResultItem[ $fieldVal ]) || (isset($arResultItem[ $fieldVal ]) && strlen($arResultItem[ $fieldVal ]) == 0))
						 && (isset($arProductData[ $parentId ][ $fieldVal ]) && !empty($arProductData[ $parentId ][ $fieldVal ]))
					) // can be array or string
					{
						$arResultItem[ $fieldVal ] = $arProductData[ $parentId ][ $fieldVal ];
					}
				}
			}

			// replace PREVIEW_PICTURE with selected ADDITIONAL_PICT_PROP
			if(empty($arProductData[ $arResultItem["PRODUCT_ID"] ]["PREVIEW_PICTURE"])
				 && empty($arProductData[ $arResultItem["PRODUCT_ID"] ]["DETAIL_PICTURE"])
				 && $arAdditionalImages[ $arResultItem["PRODUCT_ID"] ]
			) {
				$arResultItem["PREVIEW_PICTURE"] = $arAdditionalImages[ $arResultItem["PRODUCT_ID"] ];
			}
			else if(empty($arResultItem["PREVIEW_PICTURE"])
				 && empty($arResultItem["DETAIL_PICTURE"])
				 && $arAdditionalImages[ $productId ]
			) {
				$arResultItem["PREVIEW_PICTURE"] = $arAdditionalImages[ $productId ];
			}


			$arResultItem["PREVIEW_PICTURE_SRC"] = "";
			if(isset($arResultItem["PREVIEW_PICTURE"]) && intval($arResultItem["PREVIEW_PICTURE"]) > 0) {
				$arImage = CFile::GetFileArray($arResultItem["PREVIEW_PICTURE"]);
				if(!empty($arImage)) {
					self::resizeImage($arResultItem, 'PREVIEW_PICTURE', $arImage,
						 array("width" => 320, "height" => 320),
						 array("width" => 110, "height" => 110),
						 $this->arParams['BASKET_IMAGES_SCALING']
					);
				}
			}

			$arResultItem["DETAIL_PICTURE_SRC"] = "";
			if(isset($arResultItem["DETAIL_PICTURE"]) && intval($arResultItem["DETAIL_PICTURE"]) > 0) {
				$arImage = CFile::GetFileArray($arResultItem["DETAIL_PICTURE"]);
				if(!empty($arImage)) {
					self::resizeImage($arResultItem, 'DETAIL_PICTURE', $arImage,
						 array("width" => 320, "height" => 320),
						 array("width" => 110, "height" => 110),
						 $this->arParams['BASKET_IMAGES_SCALING']
					);
				}
			}
		}

		if(!empty($arResult["BASKET_ITEMS"]) && $this->useCatalog)
			$arResult["BASKET_ITEMS"] = getMeasures($arResult["BASKET_ITEMS"]); // get measures

		foreach($arResult["BASKET_ITEMS"] as $key => $arBasketItem) {
			// prepare values for custom-looking columns
			$arCols = array("PROPS" => $this->getPropsInfo($arBasketItem));

			if(isset($arBasketItem["PREVIEW_PICTURE"]) && intval($arBasketItem["PREVIEW_PICTURE"]) > 0)
				$arCols["PREVIEW_PICTURE"] = CSaleHelper::getFileInfo($arBasketItem["PREVIEW_PICTURE"], array("WIDTH" => 110, "HEIGHT" => 110));

			if(isset($arBasketItem["DETAIL_PICTURE"]) && intval($arBasketItem["DETAIL_PICTURE"]) > 0)
				$arCols["DETAIL_PICTURE"] = CSaleHelper::getFileInfo($arBasketItem["DETAIL_PICTURE"], array("WIDTH" => 110, "HEIGHT" => 110));

			if(isset($arBasketItem["MEASURE_TEXT"]) && strlen($arBasketItem["MEASURE_TEXT"]) > 0)
				$arCols["QUANTITY"] = $arBasketItem["QUANTITY"] . "&nbsp;" . $arBasketItem["MEASURE_TEXT"];

			foreach($arBasketItem as $tmpKey => $value) {
				if((strpos($tmpKey, "PROPERTY_", 0) === 0) && (strrpos($tmpKey, "_VALUE") == strlen($tmpKey) - 6)) {
					$code     = str_replace(array("PROPERTY_", "_VALUE"), "", $tmpKey);
					$propData = $currentProductProperties[ $arBasketItem['PRODUCT_ID'] ][ $code ];

					// display linked property type
					if($propData['PROPERTY_TYPE'] === 'E') {
						$propData['VALUE'] = $value;
						$arCols[ $tmpKey ] = $this->getLinkedPropValue($arBasketItem, $propData);
					}
					else {
						$arCols[ $tmpKey ] = $this->getIblockProps($value, $propData, array('WIDTH' => 110, 'HEIGHT' => 110));
					}
				}
			}

			$this->arResult['COUNT_BASKET_ITEMS'] += $arBasketItem["QUANTITY"];

			$arResult["GRID"]["ROWS"][ $arBasketItem["ID"] ] = array(
				 "id"       => $arBasketItem["ID"],
				 "data"     => $arBasketItem,
				 "actions"  => array(),
				 "columns"  => $arCols,
				 "editable" => true,
			);
		}
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
		if($scale == 'no_scale') {
			$item[ $imageKey . "_SRC" ]          = $arImage['SRC'];
			$item[ $imageKey . "_SRC_ORIGINAL" ] = $arImage['SRC'];
		}
		else if($scale == 'adaptive') {
			$arFileTmp                  = CFile::ResizeImageGet(
				 $arImage,
				 array("width" => $sizeAdaptive["width"] / 2, "height" => $sizeAdaptive["height"] / 2),
				 BX_RESIZE_IMAGE_PROPORTIONAL,
				 true
			);
			$item[ $imageKey . "_SRC" ] = $arFileTmp["src"];

			$arFileTmp                     = CFile::ResizeImageGet(
				 $arImage,
				 $sizeAdaptive,
				 BX_RESIZE_IMAGE_PROPORTIONAL,
				 true
			);
			$item[ $imageKey . "_SRC_2X" ] = $arFileTmp["src"];

			$item[ $imageKey . "_SRC_ORIGINAL" ] = $arImage['SRC'];
		}
		else {
			$arFileTmp                  = CFile::ResizeImageGet($arImage, $sizeStandard, BX_RESIZE_IMAGE_PROPORTIONAL, true);
			$item[ $imageKey . "_SRC" ] = $arFileTmp["src"];

			$item[ $imageKey . "_SRC_ORIGINAL" ] = $arImage['SRC'];
		}
	}

	public function getPropsInfo($source)
	{
		$resultHTML = "";
		foreach($source["PROPS"] as $val)
			$resultHTML .= str_replace(" ", "&nbsp;", $val["NAME"] . ": " . $val["VALUE"]) . "<br />";

		return $resultHTML;
	}

	public function getLinkedPropValue($basketItem, $property)
	{
		$result = array();

		if($property['MULTIPLE'] === 'Y')
			$property['VALUE'] = explode(',', $property['VALUE']);

		$formattedProperty = CIBlockFormatProperties::GetDisplayValue($basketItem, $property, 'sale_out');
		if(!empty($formattedProperty['DISPLAY_VALUE'])) {
			if(is_array($formattedProperty['DISPLAY_VALUE'])) {
				foreach($formattedProperty['DISPLAY_VALUE'] as $key => $formatValue) {
					$result[] = array(
						 'type'         => 'linked',
						 'value'        => $property['VALUE'][ $key ],
						 'value_format' => $formatValue,
					);
				}
			}
			else {
				$result[] = array(
					 'type'         => 'linked',
					 'value'        => is_array($property['VALUE']) ? reset($property['VALUE']) : $property['VALUE'],
					 'value_format' => $formattedProperty['DISPLAY_VALUE'],
				);
			}
		}

		return $result;
	}

	public function getIblockProps($value, $propData, $arSize = array("WIDTH" => 90, "HEIGHT" => 90), $orderId = 0)
	{
		$res = array();

		if($propData["MULTIPLE"] == "Y") {
			$arVal = array();
			if(!is_array($value)) {
				if(strpos($value, ",") !== false)
					$arVal = explode(",", $value);
				else
					$arVal[] = $value;
			}
			else
				$arVal = $value;

			if(!empty($arVal)) {
				foreach($arVal as $key => $val) {
					if($propData["PROPERTY_TYPE"] == "F")
						$res[] = $this->getFileData(trim($val), $orderId, $arSize);
					else
						$res[] = array("type" => "value", "value" => $val);
				}
			}
		}
		else {
			if($propData["PROPERTY_TYPE"] == "F")
				$res[] = $this->getFileData($value, $orderId, $arSize);
			else
				$res[] = array("type" => "value", "value" => $value);
		}

		return $res;
	}

	public function getFileData($fileId, $orderId = 0, $arSize = array("WIDTH" => 90, "HEIGHT" => 90))
	{
		$res    = "";
		$arFile = CFile::GetFileArray($fileId);

		if($arFile) {
			$is_image = CFile::IsImage($arFile["FILE_NAME"], $arFile["CONTENT_TYPE"]);
			if($is_image) {
				$arImgProduct = CFile::ResizeImageGet($arFile, array("width" => $arSize["WIDTH"], "height" => $arSize["HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);

				if(is_array($arImgProduct))
					$res = array("type" => "image", "value" => $arImgProduct["src"], "source" => $arFile["SRC"]);
			}
			else
				$res = array("type" => "file", "value" => "<a href=" . $arFile["SRC"] . ">" . $arFile["ORIGINAL_NAME"] . "</a>");
		}

		return $res;
	}

	protected function getOrderPropFormatted($arProperty, &$arDeleteFieldLocation = array())
	{
		static $propertyGroupID = 0;
		static $propertyUSER_PROPS = "";

		$arProperty["FIELD_NAME"] = "ORDER_PROP_" . $arProperty["ID"];

		if(strlen($arProperty["CODE"]) > 0)
			$arProperty["FIELD_ID"] = "ORDER_PROP_" . $arProperty["CODE"];
		else
			$arProperty["FIELD_ID"] = "ORDER_PROP_" . $arProperty["ID"];

		if(intval($arProperty["PROPS_GROUP_ID"]) != $propertyGroupID || $propertyUSER_PROPS != $arProperty["USER_PROPS"])
			$arProperty["SHOW_GROUP_NAME"] = "Y";

		$propertyGroupID    = $arProperty["PROPS_GROUP_ID"];
		$propertyUSER_PROPS = $arProperty["USER_PROPS"];

		if($arProperty["REQUIRED"] == "Y" || $arProperty["IS_PROFILE_NAME"] == "Y"
			 || $arProperty["IS_LOCATION"] == "Y" || $arProperty["IS_LOCATION4TAX"] == "Y"
			 || $arProperty["IS_PAYER"] == "Y" || $arProperty["IS_ZIP"] == "Y"
		) {
			$arProperty["REQUIED"]          = "Y";
			$arProperty["REQUIED_FORMATED"] = "Y";
		}

		if($arProperty["IS_LOCATION"] == "Y") {
			$deliveryId                                    = CSaleLocation::getLocationIDbyCODE(current($arProperty['VALUE']));
			$this->arUserResult['DELIVERY_LOCATION']       = $deliveryId;
			$this->arUserResult['DELIVERY_LOCATION_BCODE'] = current($arProperty['VALUE']);
		}

		if($arProperty["IS_ZIP"] == "Y")
			$this->arUserResult['DELIVERY_LOCATION_ZIP'] = current($arProperty['VALUE']);

		if($arProperty["IS_LOCATION4TAX"] == "Y") {
			$taxId                                    = CSaleLocation::getLocationIDbyCODE(current($arProperty['VALUE']));
			$this->arUserResult['TAX_LOCATION']       = $taxId;
			$this->arUserResult['TAX_LOCATION_BCODE'] = current($arProperty['VALUE']);
		}

		if($arProperty["IS_PAYER"] == "Y")
			$this->arUserResult['PAYER_NAME'] = current($arProperty['VALUE']);

		if($arProperty["IS_EMAIL"] == "Y")
			$this->arUserResult['USER_EMAIL'] = current($arProperty['VALUE']);

		if($arProperty["IS_PROFILE_NAME"] == "Y")
			$this->arUserResult['PROFILE_NAME'] = current($arProperty['VALUE']);

		switch($arProperty["TYPE"]) {
			case 'Y/N':
				self::formatYN($arProperty);
				break;
			case 'STRING':
				self::formatString($arProperty);
				break;
			case 'NUMBER':
				self::formatNumber($arProperty);
				break;
			case 'ENUM':
				self::formatEnum($arProperty);
				break;
			case 'LOCATION':
				self::formatLocation($arProperty, $arDeleteFieldLocation, $this->arResult['LOCATION_ALT_PROP_DISPLAY_MANUAL']);
				break;
			case 'FILE':
				self::formatFile($arProperty);
				break;
			case 'DATE':
				self::formatDate($arProperty);
				break;
		}

		return $arProperty;
	}

	public static function formatYN(array &$arProperty)
	{
		$curVal = $arProperty['VALUE'];

		if(current($curVal) == "Y") {
			$arProperty["CHECKED"]        = "Y";
			$arProperty["VALUE_FORMATED"] = Loc::getMessage("SOA_Y");
		}
		else
			$arProperty["VALUE_FORMATED"] = Loc::getMessage("SOA_N");

		$arProperty["SIZE1"] = (intval($arProperty["SIZE1"]) > 0) ? $arProperty["SIZE1"] : 30;

		$arProperty["VALUE"] = current($curVal);
		$arProperty["TYPE"]  = 'CHECKBOX';
	}

	public static function formatString(array &$arProperty)
	{
		$curVal = $arProperty['VALUE'];

		if(!empty($arProperty["MULTILINE"]) && $arProperty["MULTILINE"] == 'Y') {
			$arProperty["TYPE"]  = 'TEXTAREA';
			$arProperty["SIZE2"] = (intval($arProperty["ROWS"]) > 0) ? $arProperty["ROWS"] : 4;
			$arProperty["SIZE1"] = (intval($arProperty["COLS"]) > 0) ? $arProperty["COLS"] : 40;
		}
		else
			$arProperty["TYPE"] = 'TEXT';

		$arProperty["SOURCE"]         = current($curVal) == $arProperty['DEFAULT_VALUE'] ? 'DEFAULT' : 'FORM';
		$arProperty["VALUE"]          = current($curVal);
		$arProperty["VALUE_FORMATED"] = $arProperty["VALUE"];
	}

	public static function formatNumber(array &$arProperty)
	{
		$curVal                       = $arProperty['VALUE'];
		$arProperty["TYPE"]           = 'TEXT';
		$arProperty["VALUE"]          = current($curVal);
		$arProperty["VALUE_FORMATED"] = $arProperty["VALUE"];
	}

	public static function formatEnum(array &$arProperty)
	{
		$curVal = $arProperty['VALUE'];

		if($arProperty["MULTIELEMENT"] == 'Y') {
			if($arProperty["MULTIPLE"] == 'Y') {
				$setValue                 = array();
				$arProperty["FIELD_NAME"] = "ORDER_PROP_" . $arProperty["ID"] . '[]';
				$arProperty["SIZE1"]      = (intval($arProperty["SIZE1"]) > 0) ? $arProperty["SIZE1"] : 5;

				$i = 0;
				foreach($arProperty["OPTIONS"] as $val => $name) {
					$arVariants = array(
						 'VALUE' => $val,
						 'NAME'  => $name,
					);
					if((is_array($curVal) && in_array($arVariants["VALUE"], $curVal))) {
						$arVariants["SELECTED"] = "Y";
						if($i > 0)
							$arProperty["VALUE_FORMATED"] .= ", ";
						$arProperty["VALUE_FORMATED"] .= $arVariants["NAME"];
						$setValue[] = $arVariants["VALUE"];
						$i++;
					}
					$arProperty["VARIANTS"][] = $arVariants;
				}

				$arProperty["TYPE"] = 'MULTISELECT';
			}
			else {
				foreach($arProperty['OPTIONS'] as $val => $name) {
					$arVariants = array(
						 'VALUE' => $val,
						 'NAME'  => $name,
					);
					if($arVariants["VALUE"] == current($curVal)) {
						$arVariants["CHECKED"]        = "Y";
						$arProperty["VALUE_FORMATED"] = $arVariants["NAME"];
					}

					$arProperty["VARIANTS"][] = $arVariants;
				}
				$arProperty["TYPE"] = 'RADIO';
			}
		}
		else {
			if($arProperty["MULTIPLE"] == 'Y') {
				$setValue                 = array();
				$arProperty["FIELD_NAME"] = "ORDER_PROP_" . $arProperty["ID"] . '[]';
				$arProperty["SIZE1"]      = ((intval($arProperty["SIZE1"]) > 0) ? $arProperty["SIZE1"] : 5);

				$i = 0;
				foreach($arProperty["OPTIONS"] as $val => $name) {
					$arVariants = array(
						 'VALUE' => $val,
						 'NAME'  => $name,
					);
					if(is_array($curVal) && in_array($arVariants["VALUE"], $curVal)) {
						$arVariants["SELECTED"] = "Y";
						if($i > 0)
							$arProperty["VALUE_FORMATED"] .= ", ";
						$arProperty["VALUE_FORMATED"] .= $arVariants["NAME"];
						$setValue[] = $arVariants["VALUE"];
						$i++;
					}
					$arProperty["VARIANTS"][] = $arVariants;
				}

				$arProperty["TYPE"] = 'MULTISELECT';
			}
			else {
				$arProperty["SIZE1"] = ((intval($arProperty["SIZE1"]) > 0) ? $arProperty["SIZE1"] : 1);
				$flagDefault         = "N";
				$nameProperty        = "";
				foreach($arProperty["OPTIONS"] as $val => $name) {
					$arVariants = array(
						 'VALUE' => $val,
						 'NAME'  => $name,
					);
					if($flagDefault == "N" && $nameProperty == "") {
						$nameProperty = $arVariants["NAME"];
					}
					if($arVariants["VALUE"] == current($curVal)) {
						$arVariants["SELECTED"]       = "Y";
						$arProperty["VALUE_FORMATED"] = $arVariants["NAME"];
						$flagDefault                  = "Y";
					}
					$arProperty["VARIANTS"][] = $arVariants;
				}
				if($flagDefault == "N") {
					$arProperty["VARIANTS"][0]["SELECTED"]       = "Y";
					$arProperty["VARIANTS"][0]["VALUE_FORMATED"] = $nameProperty;
				}
				$arProperty["TYPE"] = 'SELECT';
			}
		}
	}

	public static function formatLocation(array &$arProperty, array &$arDeleteFieldLocation, $locationAltPropDisplayManual = null)
	{
		$curVal              = CSaleLocation::getLocationIDbyCODE(current($arProperty['VALUE']));
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
		while($arVariants = $dbVariants->GetNext()) {
			$city = !empty($arVariants['CITY_NAME']) ? ' - ' . $arVariants['CITY_NAME'] : '';

			if($arVariants['ID'] === $curVal) {
				// set formatted value
				$locationFound                = $arVariants;
				$arVariants['SELECTED']       = 'Y';
				$arProperty['VALUE_FORMATED'] = $arVariants['COUNTRY_NAME'] . $city;
			}

			$arVariants['NAME'] = $arVariants['COUNTRY_NAME'] . $city;
			// save to variants
			$arProperty['VARIANTS'][] = $arVariants;
		}

		if(!$locationFound && intval($curVal)) {
			$item = CSaleLocation::GetById($curVal);
			if($item) {
				// set formatted value
				$locationFound                = $item;
				$arProperty["VALUE_FORMATED"] = $item["COUNTRY_NAME"] . ((strlen($item["CITY_NAME"]) > 0) ? " - " : "") . $item["CITY_NAME"];
				$item['SELECTED']             = 'Y';
				$item['NAME']                 = $item["COUNTRY_NAME"] . ((strlen($item["CITY_NAME"]) > 0) ? " - " : "") . $item["CITY_NAME"];

				// save to variants
				$arProperty["VARIANTS"][] = $item;
			}
		}

		if($locationFound) {
			// enable location town text
			if(isset($locationAltPropDisplayManual)) // its an ajax-hit and sale.location.selector.steps is used
			{
				if(intval($locationAltPropDisplayManual[ $arProperty["ID"] ])) // user MANUALLY selected "Other location" in the selector
					unset($arDeleteFieldLocation[ $arProperty["ID"] ]);
				else
					$arDeleteFieldLocation[ $arProperty["ID"] ] = $arProperty["INPUT_FIELD_LOCATION"];
			}
			else {
				if($arProperty["IS_LOCATION"] == "Y" && intval($arProperty["INPUT_FIELD_LOCATION"]) > 0) {
					if(intval($locationFound["CITY_ID"]) <= 0)
						unset($arDeleteFieldLocation[ $arProperty["ID"] ]);
					else
						$arDeleteFieldLocation[ $arProperty["ID"] ] = $arProperty["INPUT_FIELD_LOCATION"];
				}
			}
		}
		else {
			// nothing found, may be it is the first load - hide
			$arDeleteFieldLocation[ $arProperty["ID"] ] = $arProperty["INPUT_FIELD_LOCATION"];
		}
	}

	public static function formatFile(array &$arProperty)
	{
		$curVal = $arProperty['VALUE'];

		$arProperty["SIZE1"] = intval($arProperty["SIZE1"]);
		if($arProperty['MULTIPLE'] == 'Y') {
			$arr    = array();
			$curVal = isset($curVal) ? $curVal : $arProperty["DEFAULT_VALUE"];
			foreach($curVal as $file) {
				$arr[] = $file['ID'];
			}
			$arProperty["VALUE"] = serialize($arr);
		}
		else {
			$arFile = isset($curVal) && is_array($curVal) ? current($curVal) : $arProperty["DEFAULT_VALUE"];
			if(is_array($arFile))
				$arProperty["VALUE"] = $arFile['ID'];
		}
	}

	public static function formatDate(array &$arProperty)
	{
		$arProperty["VALUE"]          = current($arProperty['VALUE']);
		$arProperty["VALUE_FORMATED"] = $arProperty["VALUE"];
	}

	/**
	 * Set delivery data from shipment object and delivery services object to $this->arResult
	 * Execution of 'OnSaleComponentOrderOneStepDelivery' event
	 *
	 * @throws Main\NotSupportedException
	 */
	protected function obtainDelivery()
	{
		$arResult = &$this->arResult;

		$arStoreId = array();
		/** @var Shipment $shipment */
		$shipment = $this->getCurrentShipment($this->order);

		if(!empty($this->arDeliveryServiceAll)) {
			foreach($this->arDeliveryServiceAll as $deliveryObj) {
				$arDelivery = &$this->arResult["DELIVERY"][ $deliveryObj->getId() ];

				$arDelivery['ID']             = $deliveryObj->getId();
				$arDelivery['NAME']           = $deliveryObj->isProfile() ? $deliveryObj->getNameWithParent() : $deliveryObj->getName();
				$arDelivery['OWN_NAME']       = $deliveryObj->getName();
				$arDelivery['DESCRIPTION']    = $deliveryObj->getDescription();
				$arDelivery['FIELD_NAME']     = 'DELIVERY_ID';
				$arDelivery["CURRENCY"]       = $this->order->getCurrency();
				$arDelivery['SORT']           = $deliveryObj->getSort();
				$arDelivery['EXTRA_SERVICES'] = $deliveryObj->getExtraServices()->getItems();
				$arDelivery['STORE']          = Delivery\ExtraServices\Manager::getStoresList($deliveryObj->getId());

				if(intval($deliveryObj->getLogotip()) > 0)
					$arDelivery["LOGOTIP"] = CFile::GetFileArray($deliveryObj->getLogotip());

				if(!empty($arDelivery['STORE']) && is_array($arDelivery['STORE'])) {
					foreach($arDelivery['STORE'] as $val)
						$arStoreId[ $val ] = $val;
				}

				$buyerStore = $this->request->get('BUYER_STORE');
				if(!empty($buyerStore) && !empty($arDelivery['STORE']) && is_array($arDelivery['STORE']) && in_array($buyerStore, $arDelivery['STORE'])) {
					$this->arUserResult['DELIVERY_STORE'] = $arDelivery["ID"];
				}
			}
		}

		$arResult["BUYER_STORE"] = $shipment->getStoreId();

		$arStore = array();
		$dbList  = CCatalogStore::GetList(
			 array("SORT" => "DESC", "ID" => "DESC"),
			 array("ACTIVE" => "Y", "ID" => $arStoreId, "ISSUING_CENTER" => "Y", "+SITE_ID" => SITE_ID),
			 false,
			 false,
			 array("ID", "TITLE", "ADDRESS", "DESCRIPTION", "IMAGE_ID", "PHONE", "SCHEDULE", "GPS_N", "GPS_S", "ISSUING_CENTER", "SITE_ID")
		);
		while($arStoreTmp = $dbList->Fetch()) {
			if($arStoreTmp["IMAGE_ID"] > 0)
				$arStoreTmp["IMAGE_ID"] = CFile::GetFileArray($arStoreTmp["IMAGE_ID"]);
			else
				$arStoreTmp["IMAGE_ID"] = null;

			$arStore[ $arStoreTmp["ID"] ] = $arStoreTmp;
		}

		$arResult["STORE_LIST"] = $arStore;

		$arResult["DELIVERY_EXTRA"] = array();
		$deliveryExtra              = $this->request->get('DELIVERY_EXTRA');
		if(is_array($deliveryExtra) && !empty($deliveryExtra[ $this->arUserResult["DELIVERY_ID"] ]))
			$arResult["DELIVERY_EXTRA"] = $deliveryExtra[ $this->arUserResult["DELIVERY_ID"] ];

		$this->executeEvent('OnSaleComponentOrderOneStepDelivery', $this->order);
	}

	/**
	 * Set pay system data from inner/external payment object and pay system services object to $this->arResult
	 * Execution of 'OnSaleComponentOrderOneStepPaySystem' event
	 */
	protected function obtainPaySystem()
	{
		$arResult     = &$this->arResult;
		$innerPsId    = PaySystem\Manager::getInnerPaySystemId();
		$innerPayment = $this->getInnerPayment($this->order);
		$extPayment   = $this->getExternalPayment($this->order);

		if(!empty($innerPayment) && $innerPayment->getSum() > 0) {
			$arResult["PAYED_FROM_ACCOUNT_FORMATED"]      = SaleFormatCurrency($innerPayment->getSum(), $this->order->getCurrency());
			$arResult["ORDER_TOTAL_LEFT_TO_PAY"]          = $this->order->getPrice() - $innerPayment->getSum();
			$arResult["ORDER_TOTAL_LEFT_TO_PAY_FORMATED"] = SaleFormatCurrency(($this->order->getPrice() - $innerPayment->getSum()), $this->order->getCurrency());
		}

		$paymentId = !empty($extPayment) ? $extPayment->getPaymentSystemId() : null;
		if(!empty($this->arPaySystemServiceAll)) {
			if(!empty($this->arPaySystemServiceAll[ $innerPsId ])) {
				$innerPaySystem = $this->arPaySystemServiceAll[ $innerPsId ];

				if($innerPaySystem["LOGOTIP"] > 0)
					$innerPaySystem["LOGOTIP"] = CFile::GetFileArray($innerPaySystem["LOGOTIP"]);

				$arResult["INNER_PAY_SYSTEM"] = $innerPaySystem;
				unset($this->arPaySystemServiceAll[ $innerPsId ]);
			}

			foreach($this->arPaySystemServiceAll as $arPaySystem) {
				$arPaySystem["PSA_ID"]          = $arPaySystem["ID"];
				$arPaySystem["PSA_NAME"]        = htmlspecialcharsEx($arPaySystem["NAME"]);
				$arPaySystem["PSA_ACTION_FILE"] = $arPaySystem["ACTION_FILE"];
				unset($arPaySystem["ACTION_FILE"]);
				$arPaySystem["PSA_RESULT_FILE"] = $arPaySystem["RESULT_FILE"];
				unset($arPaySystem["RESULT_FILE"]);
				$arPaySystem["PSA_NEW_WINDOW"] = $arPaySystem["NEW_WINDOW"];
				unset($arPaySystem["NEW_WINDOW"]);
				$arPaySystem["PSA_PERSON_TYPE_ID"] = $arPaySystem["PERSON_TYPE_ID"];
				unset($arPaySystem["PERSON_TYPE_ID"]);
				$arPaySystem["PSA_PARAMS"] = $arPaySystem["PARAMS"];
				unset($arPaySystem["PARAMS"]);
				$arPaySystem["PSA_TARIF"] = $arPaySystem["TARIF"];
				unset($arPaySystem["TARIF"]);
				$arPaySystem["PSA_HAVE_PAYMENT"] = $arPaySystem["HAVE_PAYMENT"];
				unset($arPaySystem["HAVE_PAYMENT"]);
				$arPaySystem["PSA_HAVE_ACTION"] = $arPaySystem["HAVE_ACTION"];
				unset($arPaySystem["HAVE_ACTION"]);
				$arPaySystem["PSA_HAVE_RESULT"] = $arPaySystem["HAVE_RESULT"];
				unset($arPaySystem["HAVE_RESULT"]);
				$arPaySystem["PSA_HAVE_PREPAY"] = $arPaySystem["HAVE_PREPAY"];
				unset($arPaySystem["HAVE_PREPAY"]);
				$arPaySystem["PSA_HAVE_RESULT_RECEIVE"] = $arPaySystem["HAVE_RESULT_RECEIVE"];
				unset($arPaySystem["HAVE_RESULT_RECEIVE"]);
				$arPaySystem["PSA_ENCODING"] = $arPaySystem["ENCODING"];
				unset($arPaySystem["ENCODING"]);

				if($arPaySystem["LOGOTIP"] > 0)
					$arPaySystem["PSA_LOGOTIP"] = CFile::GetFileArray($arPaySystem["LOGOTIP"]);

				unset($arPaySystem["LOGOTIP"]);

				if($paymentId == $arPaySystem['ID'])
					$arPaySystem["CHECKED"] = 'Y';

				$arPaySystem['PRICE'] = 0;
				if($arPaySystem['HAVE_PRICE'] == 'Y' && !empty($extPayment)) {
					$service = PaySystem\Manager::getObjectById($arPaySystem['ID']);
					if($service !== null) {
						$arPaySystem['PRICE']           = $service->getPaymentPrice($extPayment);
						$arPaySystem['PRICE_FORMATTED'] = SaleFormatCurrency($arPaySystem['PRICE'], $this->order->getCurrency());
						if($paymentId == $arPaySystem['ID']) {
							$arResult['PAY_SYSTEM_PRICE']           = $arPaySystem['PRICE'];
							$arResult['PAY_SYSTEM_PRICE_FORMATTED'] = $arPaySystem['PRICE_FORMATTED'];
						}
					}
				}

				$arResult["PAY_SYSTEM"][] = $arPaySystem;
			}
		}

		$this->executeEvent('OnSaleComponentOrderOneStepPaySystem', $this->order);
	}

	/**
	 * Set taxes data from order object to $this->arResult
	 */
	protected function obtainTaxes()
	{
		$arResult = &$this->arResult;

		$arResult["USE_VAT"]  = $this->order->isUsedVat();
		$arResult["VAT_RATE"] = $this->order->getVatRate();
		$arResult["VAT_SUM"]  = $this->order->getVatSum();

		if($arResult["VAT_SUM"] === null)
			$arResult["VAT_SUM"] = 0;

		$arResult["VAT_SUM_FORMATED"] = SaleFormatCurrency($arResult["VAT_SUM"], $this->order->getCurrency());

		$taxes = $this->order->getTax();

		if($this->order->isUsedVat())
			$arResult['TAX_LIST'] = $taxes->getAvailableList();
		else {
			$arResult['TAX_LIST'] = $taxes->getTaxList();
			if(is_array($arResult['TAX_LIST']) && !empty($arResult['TAX_LIST'])) {
				foreach($arResult['TAX_LIST'] as $key => &$tax) {
					if($tax['VALUE_MONEY'])
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
		$arResult = &$this->arResult;

		$locationAltPropDisplayManual = $this->request->get('LOCATION_ALT_PROP_DISPLAY_MANUAL');
		if(!empty($locationAltPropDisplayManual) && is_array($locationAltPropDisplayManual)) {
			foreach($locationAltPropDisplayManual as $propId => $switch) {
				if(intval($propId))
					$arResult['LOCATION_ALT_PROP_DISPLAY_MANUAL'][ intval($propId) ] = !!$switch;
			}
		}

		$basket = $this->order->getBasket();

		$arResult["ORDER_PRICE"]          = $basket->getPrice();
		$arResult["ORDER_PRICE_FORMATED"] = SaleFormatCurrency($basket->getPrice(), $this->order->getCurrency());

		$arResult["ORDER_WEIGHT"]          = $basket->getWeight();
		$arResult["ORDER_WEIGHT_FORMATED"] = roundEx(doubleval($basket->getWeight() / $arResult["WEIGHT_KOEF"]), SALE_WEIGHT_PRECISION) . " " . $arResult["WEIGHT_UNIT"];

		$arResult["PRICE_WITHOUT_DISCOUNT_VALUE"] = $basket->getBasePrice();
		$arResult["PRICE_WITHOUT_DISCOUNT"]       = SaleFormatCurrency($basket->getBasePrice(), $this->order->getCurrency());

		$arResult['DISCOUNT_PRICE']          = $this->order->getDiscountPrice();
		$arResult['DISCOUNT_PRICE_FORMATED'] = SaleFormatCurrency($this->order->getDiscountPrice(), $this->order->getCurrency());

		$arResult['DELIVERY_PRICE']          = $this->order->getDeliveryPrice();
		$arResult['DELIVERY_PRICE_FORMATED'] = SaleFormatCurrency($this->order->getDeliveryPrice(), $this->order->getCurrency());

		$arResult["ORDER_TOTAL_PRICE"]          = $this->order->getPrice();
		$arResult["ORDER_TOTAL_PRICE_FORMATED"] = SaleFormatCurrency($this->order->getPrice(), $this->order->getCurrency());
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
		$arResult = &$this->arResult;
		$result   = &$this->arResult['JS_DATA'];

		$result['IS_AUTHORIZED']   = $USER->IsAuthorized();
		$result['LAST_ORDER_DATA'] = array();

		if(
			 ($this->request->getRequestMethod() === 'GET' || $this->request->get('do_authorize') === 'Y' || $this->request->get('do_register') === 'Y')
			 && $this->arUserResult['USE_PRELOAD']
			 && $result['IS_AUTHORIZED']
		) {
			$lastOrder = &$this->arUserResult['LAST_ORDER_DATA'];

			if(!empty($lastOrder)) {
				$status = false;
				if(!empty($lastOrder['PERSON_TYPE_ID']))
					$status = $this->order->getPersonTypeId() == $lastOrder['PERSON_TYPE_ID'];

				$result['LAST_ORDER_DATA']['PERSON_TYPE'] = $status;

				$status = false;
				if(!empty($lastOrder['DELIVERY_ID']) && $shipment = $this->getCurrentShipment($this->order))
					if(empty($lastOrder['DELIVERY_EXTRA_SERVICES'][ $lastOrder['DELIVERY_ID'] ]))
						$status = $shipment->getDeliveryId() == $lastOrder['DELIVERY_ID'];

				$result['LAST_ORDER_DATA']['DELIVERY'] = $status;

				$status = false;
				if(empty($lastOrder['PAY_CURRENT_ACCOUNT']) && !empty($lastOrder['PAY_SYSTEM_ID']) && $payment = $this->getExternalPayment($this->order))
					$status = $payment->getPaymentSystemId() == $lastOrder['PAY_SYSTEM_ID'];

				$result['LAST_ORDER_DATA']['PAY_SYSTEM'] = $status;

				$status = false;
				if(!empty($lastOrder['BUYER_STORE']) && $shipment = $this->getCurrentShipment($this->order))
					$status = $shipment->getStoreId() == $lastOrder['BUYER_STORE'];

				$result['LAST_ORDER_DATA']['PICK_UP'] = $status;
			}
			else {
				// last order data cannot initialize
				$result['LAST_ORDER_DATA']['FAIL'] = true;
			}
		}
		else {
			// last order data not initialized
			$result['LAST_ORDER_DATA']['FAIL'] = false;
		}

		$result['SHOW_AUTH']         = !$USER->IsAuthorized() && $this->arParams["ALLOW_AUTO_REGISTER"] == "N";
		$result['SHOW_EMPTY_BASKET'] = $arResult['SHOW_EMPTY_BASKET'];
		$result['AUTH']              = $arResult['AUTH'];
		$result['OK_MESSAGE']        = $arResult['OK_MESSAGE'];
		$result['GRID']              = $arResult['GRID'];
		$result['PERSON_TYPE']       = $arResult["PERSON_TYPE"];
		$result['PAY_SYSTEM']        = $arResult["PAY_SYSTEM"];
		$result['INNER_PAY_SYSTEM']  = $arResult["INNER_PAY_SYSTEM"];
		$result['DELIVERY']          = $arResult["DELIVERY"];
		foreach($result['DELIVERY'] as &$delivery) {
			if(!empty($delivery['EXTRA_SERVICES'])) {
				$arExtraService = array();
				/** @var Delivery\ExtraServices\Base $extraService */
				foreach($delivery['EXTRA_SERVICES'] as $extraServiceId => $extraService) {
					if($extraService->canUserEditValue()) {
						$arr                     = array();
						$arr['id']               = $extraServiceId;
						$arr['name']             = $extraService->getName();
						$arr['value']            = $extraService->getValue();
						$arr['price']            = $extraService->getPriceShipment($this->getCurrentShipment($this->order));
						$arr['priceFormatted']   = SaleFormatCurrency($extraService->getPriceShipment($this->getCurrentShipment($this->order)), $this->order->getCurrency());
						$arr['description']      = $extraService->getDescription();
						$arr['canUserEditValue'] = $extraService->canUserEditValue();
						$arr['editControl']      = $extraService->getEditControl('DELIVERY_EXTRA_SERVICES[' . $delivery['ID'] . '][' . $extraServiceId . ']');
						$arr['viewControl']      = $extraService->getViewControl();
						$arExtraService[]        = $arr;
					}
				}
				$delivery['EXTRA_SERVICES'] = $arExtraService;
			}
		}

		$result["USER_PROFILES"] = $arResult["ORDER_PROP"]['USER_PROFILES'];

		$arr               = $this->order->getPropertyCollection()->getArray();
		$paymentSystemIds  = $this->order->getPaymentSystemId();
		$deliverySystemIds = $this->order->getDeliverySystemId();
		foreach($arr['properties'] as $key => $property) {
			if($property['UTIL'] == 'Y'
				 || isset($property['RELATION']) && !$this->checkRelatedProperty($property, $paymentSystemIds, $deliverySystemIds)
			)
				unset($arr['properties'][ $key ]);
		}
		usort($arr['properties'], array('self', 'compareProperties'));

		if(!empty($arr['groups']) && !empty($arr['properties'])) {
			$groupIndexList = array();
			foreach($arr['groups'] as $groupdData) {
				$groupIndexList[] = intval($groupdData['ID']);
			}

			if(!empty($groupIndexList)) {
				foreach($arr['properties'] as $index => $propertyData) {
					if(array_key_exists('PROPS_GROUP_ID', $propertyData)) {
						if(!in_array($propertyData['PROPS_GROUP_ID'], $groupIndexList)) {
							$arr['properties'][ $index ]['PROPS_GROUP_ID'] = 0;
						}
					}
				}
			}
		}
		$result["ORDER_PROP"] = $arr;

		$result['STORE_LIST']  = $arResult['STORE_LIST'];
		$result['BUYER_STORE'] = $arResult['BUYER_STORE'];

		$result['COUPON_LIST'] = array();
		$arCoupons             = DiscountCouponsManager::get(true, array(), true, true);
		if(!empty($arCoupons)) {
			foreach($arCoupons as &$oneCoupon) {
				if($oneCoupon['STATUS'] == DiscountCouponsManager::STATUS_NOT_FOUND || $oneCoupon['STATUS'] == DiscountCouponsManager::STATUS_FREEZE)
					$oneCoupon['JS_STATUS'] = 'BAD';
				elseif($oneCoupon['STATUS'] == DiscountCouponsManager::STATUS_NOT_APPLYED || $oneCoupon['STATUS'] == DiscountCouponsManager::STATUS_ENTERED)
					$oneCoupon['JS_STATUS'] = 'ENTERED';
				else
					$oneCoupon['JS_STATUS'] = 'APPLIED';

				$oneCoupon['JS_CHECK_CODE'] = '';
				if(isset($oneCoupon['CHECK_CODE_TEXT']))
					$oneCoupon['JS_CHECK_CODE'] = (is_array($oneCoupon['CHECK_CODE_TEXT']) ? implode('<br>', $oneCoupon['CHECK_CODE_TEXT']) : $oneCoupon['CHECK_CODE_TEXT']);

				$result['COUPON_LIST'][] = $oneCoupon;
			}
			unset($oneCoupon);
			$result['COUPON_LIST'] = array_values($arCoupons);
		}
		unset($arCoupons);

		$result['PAY_CURRENT_ACCOUNT'] = 'N';
		if($innerPaySystem = $this->order->getPaymentCollection()->getInnerPayment())
			if($innerPaySystem->getSum() > 0)
				$result['PAY_CURRENT_ACCOUNT'] = 'Y';

		$result['PAY_FROM_ACCOUNT']        = $arResult["PAY_FROM_ACCOUNT"];
		$result['CURRENT_BUDGET_FORMATED'] = $arResult["CURRENT_BUDGET_FORMATED"];
		$result['COUNT_BASKET_ITEMS']      = $arResult["COUNT_BASKET_ITEMS"];

		$result['TOTAL'] = array(
			 'PRICE_WITHOUT_DISCOUNT_VALUE'     => $arResult["PRICE_WITHOUT_DISCOUNT_VALUE"],
			 'PRICE_WITHOUT_DISCOUNT'           => $arResult["PRICE_WITHOUT_DISCOUNT"],
			 'PAYED_FROM_ACCOUNT_FORMATED'      => $arResult["PAYED_FROM_ACCOUNT_FORMATED"],
			 'ORDER_TOTAL_PRICE'                => $arResult["ORDER_TOTAL_PRICE"],
			 'ORDER_TOTAL_PRICE_FORMATED'       => $arResult["ORDER_TOTAL_PRICE_FORMATED"],
			 'ORDER_TOTAL_LEFT_TO_PAY'          => $arResult["ORDER_TOTAL_LEFT_TO_PAY"],
			 'ORDER_TOTAL_LEFT_TO_PAY_FORMATED' => $arResult["ORDER_TOTAL_LEFT_TO_PAY_FORMATED"],
			 'ORDER_WEIGHT'                     => $arResult["ORDER_WEIGHT"],
			 'ORDER_WEIGHT_FORMATED'            => $arResult["ORDER_WEIGHT_FORMATED"],
			 'ORDER_PRICE'                      => $arResult["ORDER_PRICE"],
			 'ORDER_PRICE_FORMATED'             => $arResult["ORDER_PRICE_FORMATED"],
			 'USE_VAT'                          => $arResult["USE_VAT"],
			 'VAT_RATE'                         => $arResult["VAT_RATE"],
			 'VAT_SUM'                          => $arResult["VAT_SUM"],
			 'VAT_SUM_FORMATED'                 => $arResult["VAT_SUM_FORMATED"],
			 'TAX_PRICE'                        => $arResult["TAX_PRICE"],
			 'TAX_LIST'                         => $arResult["TAX_LIST"],
			 'DISCOUNT_PRICE'                   => $arResult["DISCOUNT_PRICE"],
			 'DISCOUNT_PRICE_FORMATED'          => $arResult["DISCOUNT_PRICE_FORMATED"],
			 'DELIVERY_PRICE'                   => $arResult["DELIVERY_PRICE"],
			 'DELIVERY_PRICE_FORMATED'          => $arResult["DELIVERY_PRICE_FORMATED"],
			 'PAY_SYSTEM_PRICE'                 => $arResult["PAY_SYSTEM_PRICE"],
			 'PAY_SYSTEM_PRICE_FORMATTED'       => $arResult["PAY_SYSTEM_PRICE_FORMATTED"],
		);

		$result['ERROR']   = $arResult["ERROR_SORTED"];
		$result['WARNING'] = $arResult["WARNING"];

		$arResult['LOCATIONS'] = $this->getLocationsResult();

		foreach(GetModuleEvents("sale", 'OnSaleComponentOrderJsData', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$this->arResult, &$this->arParams));
	}

	/**
	 * Returns array with locations data output
	 *
	 * @return array
	 */
	protected function getLocationsResult()
	{
		$locations          = array();
		$propertyCollection = $this->order->getPropertyCollection();
		$properties         = $propertyCollection->getArray();

		foreach($properties['properties'] as $property) {
			if($property['UTIL'] == 'Y')
				continue;

			if($property['TYPE'] == 'LOCATION') {
				$locationTemplateP = $this->arParams['TEMPLATE_LOCATION'];
				$locationTemplate  = $this->request->get('PERMANENT_MODE_STEPS') == 1 ? 'steps' : $locationTemplateP;

				$locations[ $property['ID'] ]['template']  = $locationTemplate;
				$locations[ $property['ID'] ]['output']    = $this->getLocationHtml($property, $locationTemplate);
				$locations[ $property['ID'] ]['showAlt']   = isset($this->arUserResult['ORDER_PROP'][ $property['INPUT_FIELD_LOCATION'] ]);
				$locations[ $property['ID'] ]['lastValue'] = reset($property['VALUE']);
			}
		}

		return $locations;
	}

	protected function getLocationHtml($property, $locationTemplate)
	{
		global $APPLICATION;

		$propertyId          = intval($property["ID"]);
		$locationOutput      = array();
		$showDefault         = true;
		$showAltPropLocation = isset($this->arUserResult['ORDER_PROP'][ $property['INPUT_FIELD_LOCATION'] ]);

		$isMultiple      = $property['MULTIPLE'] == 'Y' && $property['IS_LOCATION'] != 'Y';
		$requestLocation = $this->request->get('ORDER_PROP_' . $propertyId);
		$actualValues    = !empty($requestLocation) && is_array($requestLocation) ? $requestLocation : $property['VALUE'];

		if(!empty($actualValues) && is_array($actualValues)) {
			foreach($actualValues as $key => $value) {
				$parameters = array(
					 "CODE"                     => $value,
					 "INPUT_NAME"               => 'ORDER_PROP_' . $propertyId . ($isMultiple ? '[' . $key . ']' : ''),
					 "CACHE_TYPE"               => "A",
					 "CACHE_TIME"               => "36000000",
					 "SEARCH_BY_PRIMARY"        => "N",
					 "SHOW_DEFAULT_LOCATIONS"   => $showDefault ? 'Y' : 'N',
					 "PROVIDE_LINK_BY"          => 'code',
					 "JS_CALLBACK"              => "submitFormProxy",
					 "JS_CONTROL_DEFERRED_INIT" => $propertyId . ($isMultiple ? '_' . $key : ''),
					 "JS_CONTROL_GLOBAL_ID"     => $propertyId . ($isMultiple ? '_' . $key : ''),
					 "DISABLE_KEYBOARD_INPUT"   => "Y",
					 "PRECACHE_LAST_LEVEL"      => "N",
					 "PRESELECT_TREE_TRUNK"     => "Y",
					 "SUPPRESS_ERRORS"          => "Y",
					 "FILTER_BY_SITE"           => "Y",
					 "FILTER_SITE_ID"           => SITE_ID,
				);

				ob_start();

				if($locationTemplate == 'steps') {
					echo '<input type="hidden" id="LOCATION_ALT_PROP_DISPLAY_MANUAL[' . $propertyId . ']" name="LOCATION_ALT_PROP_DISPLAY_MANUAL[' . $propertyId . ']" value="' . ($showAltPropLocation ? '1' : '0') . '" />';
				}

				$APPLICATION->IncludeComponent(
					 "bitrix:sale.location.selector." . $locationTemplate,
					 "",
					 $parameters,
					 null,
					 array('HIDE_ICONS' => 'Y')
				);

				$locationOutput[] = ob_get_contents();
				ob_end_clean();

				$showDefault = false;
			}
		}

		if($isMultiple) {
			$parameters = array(
				 "CODE"                     => '',
				 "INPUT_NAME"               => 'ORDER_PROP_' . $propertyId . '[#key#]',
				 "CACHE_TYPE"               => "A",
				 "CACHE_TIME"               => "36000000",
				 "SEARCH_BY_PRIMARY"        => "N",
				 "SHOW_DEFAULT_LOCATIONS"   => 'N',
				 "PROVIDE_LINK_BY"          => 'code',
				 "JS_CALLBACK"              => "submitFormProxy",
				 "JS_CONTROL_DEFERRED_INIT" => $propertyId . '_key__',
				 "JS_CONTROL_GLOBAL_ID"     => $propertyId . '_key__',
				 "DISABLE_KEYBOARD_INPUT"   => "Y",
				 "PRECACHE_LAST_LEVEL"      => "N",
				 "PRESELECT_TREE_TRUNK"     => "Y",
				 "SUPPRESS_ERRORS"          => "Y",
				 "FILTER_BY_SITE"           => "Y",
				 "FILTER_SITE_ID"           => SITE_ID,
			);

			ob_start();

			$APPLICATION->IncludeComponent(
				 "bitrix:sale.location.selector." . $locationTemplate,
				 "",
				 $parameters,
				 null,
				 array('HIDE_ICONS' => 'Y')
			);

			$locationOutput['clean'] = ob_get_contents();
			ob_end_clean();
		}

		return $locationOutput;
	}


	public static function makeCompatibleArray(&$array)
	{
		if(!is_array($array) || empty($array))
			return;

		$arr = array();
		foreach($array as $key => $value) {
			if(is_array($value) || preg_match("/[;&<>\"]/", $value))
				$arr[ $key ] = htmlspecialcharsEx($value);
			else
				$arr[ $key ] = $value;

			$arr[ "~" . $key ] = $value;
		}
		$array = $arr;
	}


	/**
	 * Scales images of all entities depending on scale parameters
	 *
	 * @param        $result
	 * @param string $scale
	 */
	public static function scaleImages(&$result, $scale = '')
	{
		if(!empty($result) && is_array($result)) {
			if(!empty($result['DELIVERY']) && is_array($result['DELIVERY'])) {
				foreach($result['DELIVERY'] as $key => $delivery) {
					if(!empty($delivery["LOGOTIP"])) {
						self::resizeImage($delivery, 'LOGOTIP', $delivery["LOGOTIP"],
							 array("width" => 600, "height" => 600),
							 array("width" => 95, "height" => 55),
							 $scale
						);
						$result["DELIVERY"][ $key ] = $delivery;
					}
				}
				unset($logotype, $delivery);
			}

			if(!empty($result['PAY_SYSTEM']) && is_array($result['PAY_SYSTEM'])) {
				foreach($result['PAY_SYSTEM'] as $key => $paySystem) {
					if(!empty($paySystem["PSA_LOGOTIP"])) {
						self::resizeImage($paySystem, 'PSA_LOGOTIP', $paySystem["PSA_LOGOTIP"],
							 array("width" => 600, "height" => 600),
							 array("width" => 95, "height" => 55),
							 $scale
						);
						$result["PAY_SYSTEM"][ $key ] = $paySystem;
					}
				}
				unset($logotype, $paySystem);
			}

			if(!empty($result['INNER_PAY_SYSTEM']) && is_array($result['INNER_PAY_SYSTEM']) && !empty($result['INNER_PAY_SYSTEM']["LOGOTIP"])) {
				self::resizeImage($result['INNER_PAY_SYSTEM'], 'LOGOTIP', $result['INNER_PAY_SYSTEM']["LOGOTIP"],
					 array("width" => 600, "height" => 600),
					 array("width" => 95, "height" => 55),
					 $scale
				);
			}

			if(!empty($result['STORE_LIST']) && is_array($result['STORE_LIST'])) {
				foreach($result['STORE_LIST'] as $key => $store) {
					if(!empty($store["IMAGE_ID"])) {
						self::resizeImage($store, 'IMAGE_ID', $store["IMAGE_ID"],
							 array("width" => 320, "height" => 320),
							 array("width" => 115, "height" => 115),
							 $scale
						);
						$result["STORE_LIST"][ $key ] = $store;
					}
				}
				unset($logotype, $store);
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

		foreach(GetModuleEvents("sale", 'OnSaleComponentOrderShowAjaxAnswer', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$result));

		$APPLICATION->RestartBuffer();

		if($this->request->get('save') != 'Y')
			header('Content-Type: application/json');

		echo Json::encode($result);

		CMain::FinalActions();
		die();
	}


	//HACK: Default router
	protected function processOrderAction()
	{
		global $APPLICATION, $USER;

		$arResult = &$this->arResult;
		$this->isOrderConfirmed = $this->request->isPost() && $this->request->get("confirmorder") == 'Y' && $this->checkSession;
		$needToRegister = !$USER->IsAuthorized() && $this->arParams["ALLOW_AUTO_REGISTER"] == "Y";
		$saveToSession = false;

		if ($this->isOrderConfirmed && $needToRegister)
			list($userId, $saveToSession) = $this->autoRegisterUser();
		else
			$userId = $USER->GetID();

		if (!$userId && $this->arParams['ORDER_USER_ID'])
			$userId = intval($this->arParams['ORDER_USER_ID']);

		if (!$userId)
			$userId = CSaleUser::GetAnonymousUserID();

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
				$arResult["USER_VALS"]["CONFIRM_ORDER"] = "N";
		}
		else
			$arResult["USER_VALS"]["CONFIRM_ORDER"] = "N";
	}

	//HACK
	protected function autoRegisterUser()
	{
		$personType    = $this->request->get('PERSON_TYPE');
		$userProps     = Sale\PropertyValue::getMeaningfulValues($personType, $this->getPropertyValuesFromRequest());
		$userId        = false;
		$saveToSession = false;

		//TODO: Search user in DB required
		if($this->arParams['ORDER_USER_ID']){
			$userId        = intval($this->arParams['ORDER_USER_ID']);
			$saveToSession = false;
		}
		elseif(is_array($userProps) && strlen($userProps['EMAIL']) > 0 && Option::get("main", "new_user_email_uniq_check", "") == "Y") {
			$res = Bitrix\Main\UserTable::getRow(array(
				 'filter' => array(
						"=EMAIL"           => $userProps['EMAIL'],
						"EXTERNAL_AUTH_ID" => '',
				 ),
				 'select' => array('ID'),
			));

			if(intval($res["ID"]) > 0) {
				$userId        = intval($res["ID"]);
				$saveToSession = true;
			}
			else
				$userId = $this->registerAndLogIn($userProps);
		}
		else if((is_array($userProps) && strlen($userProps['EMAIL']) > 0) || Option::get("main", "new_user_email_required", "") == "N")
			$userId = $this->registerAndLogIn($userProps);
		else
			$this->addError(Loc::getMessage("STOF_ERROR_EMAIL"), self::AUTH_BLOCK);

		return array($userId, $saveToSession);
	}

	/**
	 * Creating new user and logging in
	 *
	 * @param $userProps
	 *
	 * @return bool|int
	 */
	protected function registerAndLogIn($userProps)
	{
		$userId   = false;
		$userData = $this->generateUserData($userProps);

		$user         = new CUser;
		$arAuthResult = $user->Add(array(
			 "LOGIN"            => $userData['NEW_LOGIN'],
			 "NAME"             => $userData['NEW_NAME'],
			 "LAST_NAME"        => $userData['NEW_LAST_NAME'],
			 "PASSWORD"         => $userData['NEW_PASSWORD'],
			 "CONFIRM_PASSWORD" => $userData['NEW_PASSWORD_CONFIRM'],
			 "EMAIL"            => $userData['NEW_EMAIL'],
			 "GROUP_ID"         => $userData['GROUP_ID'],
			 "ACTIVE"           => "Y",
			 "LID"              => $this->context->getSite(),
		));

		if(intval($arAuthResult) <= 0) {
			$this->addError(Loc::getMessage("STOF_ERROR_REG") . ((strlen($user->LAST_ERROR) > 0) ? ": " . $user->LAST_ERROR : ""), self::AUTH_BLOCK);
		}
		else {
			global $USER;
			$userId = intval($arAuthResult);
			$USER->Authorize($arAuthResult);
			if($USER->IsAuthorized()) {
				if($this->arParams["SEND_NEW_USER_NOTIFY"] == "Y")
					CUser::SendUserInfo($USER->GetID(), $this->context->getSite(), Loc::getMessage("INFO_REQ"), true);
			}
			else
				$this->addError(Loc::getMessage("STOF_ERROR_REG_CONFIRM"), self::AUTH_BLOCK);
		}

		return $userId;
	}

	/**
	 * Action - saves order if there are no errors
	 * Execution of 'OnSaleComponentOrderOneStepComplete' event
	 *
	 * @param bool $saveToSession
	 */
	protected function saveOrder($saveToSession = false)
	{
		$arResult = &$this->arResult;

		$this->initStatGid();
		$this->initAffiliate();

		$res = $this->order->save();
		if($res->isSuccess()) {
			$arResult["ORDER_ID"]       = $res->getId();
			$arResult["ACCOUNT_NUMBER"] = $this->order->getField('ACCOUNT_NUMBER');
		}
		else{
			$this->addError($res, 'MAIN');
		}

		if($arResult["HAVE_PREPAYMENT"] && empty($arResult["ERROR"]))
			$this->prepayOrder();

		if(empty($arResult["ERROR"]))
			$this->saveProfileData();

		if(empty($arResult["ERROR"])) {
			$this->addStatistic();

			if($saveToSession) {
				if(!is_array($_SESSION['SALE_ORDER_ID']))
					$_SESSION['SALE_ORDER_ID'] = array();

				$_SESSION['SALE_ORDER_ID'][] = $res->getId();
			}
		}

		foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepComplete", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($arResult["ORDER_ID"], $this->order->getFieldValues(), $this->arParams));
	}

	protected function initStatGid()
	{
		if(Loader::includeModule("statistic"))
			$this->order->setField('STAT_GID', CStatistic::GetEventParam());
	}

	protected function initAffiliate()
	{
		$affiliateID = CSaleAffiliate::GetAffiliate();
		if($affiliateID > 0) {
			$dbAffiliate  = CSaleAffiliate::GetList(array(), array("SITE_ID" => $this->context->getSite(), "ID" => $affiliateID));
			$arAffiliates = $dbAffiliate->Fetch();
			if(count($arAffiliates) > 1)
				$this->order->setField('AFFILIATE_ID', $affiliateID);
		}
	}

	protected function prepayOrder()
	{
		if($this->prePaymentService && $this->prePaymentService->isPrePayable() && $this->request->get('paypal') == 'Y') {
			/** @var Payment $payment */
			$payment = $this->getExternalPayment($this->order);
			if($payment) {
				$this->prePaymentService->setOrderDataForPrePayment(
					 array(
							'ORDER_ID'       => $this->order->getId(),
							'PAYMENT_ID'     => $payment->getId(),
							'ORDER_PRICE'    => $payment->getSum(),
							'DELIVERY_PRICE' => $this->order->getDeliveryPrice(),
							'TAX_PRICE'      => $this->order->getTaxPrice(),
					 )
				);

				$orderData = array();
				/** @var Sale\BasketItem $item */
				foreach($this->order->getBasket() as $item)
					$orderData['BASKET_ITEMS'][] = $item->getFieldValues();

				$this->prePaymentService->payOrderByPrePayment($orderData);
			}
		}
	}

	//HACK: if($this->arParams['ALLOW_NEW_PROFILE'] == 'Y'){
	protected function saveProfileData()
	{
		global $USER;

		$arResult    = &$this->arResult;
		$profileId   = null;
		$profileName = '';
		$properties  = array();

		if(isset($arResult['ORDER_PROP']) && is_array($arResult['ORDER_PROP']['USER_PROFILES'])) {
			foreach($arResult['ORDER_PROP']['USER_PROFILES'] as $profile) {
				if($profile['CHECKED'] == 'Y') {
					$profileId = $profile['ID'];
					break;
				}
			}
		}

		$propertyCollection = $this->order->getPropertyCollection();
		if(!empty($propertyCollection)) {
			if($profileProp = $propertyCollection->getProfileName())
				$profileName = $profileProp->getValue();

			/** @var Sale\PropertyValue $property */
			foreach($propertyCollection as $property) {
				$properties[ $property->getField('ORDER_PROPS_ID') ] = $property->getValue();
			}
		}

		if($this->arParams['ALLOW_NEW_PROFILE'] == 'Y' || $USER->IsAuthorized()){
			CSaleOrderUserProps::DoSaveUserProfile(
				 $this->order->getUserId(),
				 $profileId,
				 $profileName,
				 $this->order->getPersonTypeId(),
				 $properties,
				 $arResult["ERROR"]
			);
		}
	}

	protected function addStatistic()
	{
		if(Loader::includeModule("statistic")) {
			$event1 = "eStore";
			$event2 = "order_confirm";
			$event3 = $this->order->getId();

			$e = $event1 . "/" . $event2 . "/" . $event3;

			if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) {
				CStatistic::Set_Event($event1, $event2, $event3);
				$_SESSION["ORDER_EVENTS"][] = $e;
			}
		}
	}

	/**
	 * Ajax action - attempt to save order and send JSON answer with data/errors
	 */
	protected function saveOrderAjaxAction()
	{
		global $USER;

		$arOrderRes = array();
		if($this->checkSession) {
			$this->isOrderConfirmed = true;
			$needToRegister         = !$USER->IsAuthorized() && $this->arParams["ALLOW_AUTO_REGISTER"] == "Y";
			$saveToSession          = false;

			if($needToRegister)
				list($userId, $saveToSession) = $this->autoRegisterUser();
			else
				$userId = $USER->GetID();

			if (!$userId && $this->arParams['ORDER_USER_ID'])
				$userId = intval($this->arParams['ORDER_USER_ID']);

			if (!$userId)
				$userId = CSaleUser::GetAnonymousUserID();

			$this->order = $this->createOrder($userId);

			$isActiveUser = intval($userId) > 0 && $userId != CSaleUser::GetAnonymousUserID();

			if($isActiveUser && empty($this->arResult['ERROR']))
				$this->saveOrder($saveToSession);


			if(empty($this->arResult["ERROR"]))
				$arOrderRes["REDIRECT_URL"] = $this->arParams["~CURRENT_PAGE"] . "?ORDER_ID=" . urlencode($this->arResult["ACCOUNT_NUMBER"]);
			else
				$arOrderRes['ERROR'] = $this->arResult['ERROR_SORTED'];
		}
		else
			$arOrderRes["ERROR"]['MAIN'] = Loc::getMessage('SESSID_ERROR');

		$this->showAjaxAnswer(array('order' => $arOrderRes));
	}

	/**
	 * Ajax action - add coupon and if needed recalculate order with JSON answer
	 */
	protected function enterCouponAction()
	{
		$coupon = trim($this->request->get('coupon'));

		if(!empty($coupon)) {
			if(DiscountCouponsManager::add($coupon))
				$this->refreshOrderAjaxAction();
			else
				$this->showAjaxAnswer($coupon);
		}
	}

	/**
	 * Ajax action - remove coupon and if needed recalculate order with JSON answer
	 */
	protected function removeCouponAction()
	{
		$coupon = trim($this->request->get('coupon'));

		if(!empty($coupon)) {
			$active = $this->isActiveCoupon($coupon);
			DiscountCouponsManager::delete($coupon);
			if($active)
				$this->refreshOrderAjaxAction();
			else
				$this->showAjaxAnswer($coupon);
		}
	}

	protected function isActiveCoupon($coupon)
	{
		$arCoupons = DiscountCouponsManager::get(true, array('COUPON' => $coupon), true, true);
		if(!empty($arCoupons)) {
			$arCoupon = array_shift($arCoupons);
			if($arCoupon['STATUS'] == DiscountCouponsManager::STATUS_NOT_APPLYED)
				return true;
		}

		return false;
	}

	/**
	 * Action - show created order and payment info
	 */
	protected function showOrderAction()
	{
		global $USER;

		$arResult = &$this->arResult;
		$arOrder  = false;
		$orderId  = urldecode($this->request->get('ORDER_ID'));

		$arResult["USER_VALS"]["CONFIRM_ORDER"] = "Y";

		/** @var Order $order */
		if($order = Order::loadByAccountNumber($orderId)) {
			$arOrder                    = $order->getFieldValues();
			$arResult["ORDER_ID"]       = $arOrder["ID"];
			$arResult["ACCOUNT_NUMBER"] = $arOrder["ACCOUNT_NUMBER"];
		}

		$checkedBySession = is_array($_SESSION['SALE_ORDER_ID']) && in_array(intval($order->getId()), $_SESSION['SALE_ORDER_ID']);
		if(!empty($arOrder) && ($order->getUserId() == $USER->GetID() || $checkedBySession)) {
			foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepFinal", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array($arResult["ORDER_ID"], &$arOrder, &$this->arParams));

			$arResult["PAYMENT"] = array();
			$paymentCollection   = $order->getPaymentCollection();
			/** @var Payment $payment */
			foreach($paymentCollection as $payment) {
				$arResult["PAYMENT"][ $payment->getId() ] = $payment->getFieldValues();

				if(intval($payment->getPaymentSystemId()) > 0 && !$payment->isPaid()) {
					$paySystemService = PaySystem\Manager::getObjectById($payment->getPaymentSystemId());
					if(!empty($paySystemService)) {
						$arPaySysAction = $paySystemService->getFieldsValues();

						/** @var PaySystem\ServiceResult $initResult */
						$initResult = $paySystemService->initiatePay($payment, null, PaySystem\BaseServiceHandler::STRING);
						if($initResult->isSuccess())
							$arPaySysAction['BUFFERED_OUTPUT'] = $initResult->getTemplate();
						else
							$arPaySysAction["ERROR"] = $initResult->getErrorMessages();

						$arResult["PAYMENT"][ $payment->getId() ]['PAID'] = $payment->getField('PAID');

						$arOrder['PAYMENT_ID']           = $payment->getId();
						$arOrder['PAY_SYSTEM_ID']        = $payment->getPaymentSystemId();
						$arPaySysAction["NAME"]          = htmlspecialcharsEx($arPaySysAction["NAME"]);
						$arPaySysAction["IS_AFFORD_PDF"] = $paySystemService->isAffordPdf();

						if($arPaySysAction > 0)
							$arPaySysAction["LOGOTIP"] = CFile::GetFileArray($arPaySysAction["LOGOTIP"]);

						$arResult["PAY_SYSTEM_LIST"][ $payment->getPaymentSystemId() ] = $arPaySysAction;
					}
					else
						$arResult["PAY_SYSTEM_LIST"][ $payment->getPaymentSystemId() ] = array('ERROR' => true);
				}
			}

			$arResult["ORDER"] = $arOrder;
		}
		else
		{
			$arResult["ACCOUNT_NUMBER"] = $orderId;
			$arResult["ORDER"] = array(
				 'ACCOUNT_NUMBER' => $arOrder['ACCOUNT_NUMBER'],
				 'DATE_INSERT'    => $arOrder['DATE_INSERT'],
			);
		}
	}
}

