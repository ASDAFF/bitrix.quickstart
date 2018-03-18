<?php

IncludeModuleLangFile(__FILE__);

abstract class CTriggMine
{
    static $triggmine_is_on;
    static $triggmine_rest_api;
    static $triggmine_token;
    static $triggmine_cart_url;

    static $MODULE_ID = "triggmine.abandonedcartrecovery";

    static $sBuyerId;
    static $sCartId;
    static $sVisitorId;

    static $MIN_VERSION = '3.2.2';
    static $NECESSARY_MODULES = array('sale','main','catalog');
    static $NECESSARY_FUNCTIONS = array(/*'Add2BasketByProductID','LocalRedirect', 'fsd'*/);

    const API_OK = 1;
    const API_NO_ACCESS = 2;
    const API_INVALID_RESPONSE = 3;

    public static function getModuleVersion()
    {
        $info = CModule::CreateModuleObject(self::$MODULE_ID);
        return $info->MODULE_VERSION;
    }

    function versionIs($operator, $toVersion)
    {
        return version_compare(SM_VERSION, $toVersion, $operator);
    }

    public static function setOption($optionName, $optionValue)
    {
        COption::SetOptionString(self::$MODULE_ID, $optionName, $optionValue);
    }

    public static function getOption($optionName)
    {
        return COption::GetOptionString(self::$MODULE_ID, $optionName);
    }

    function deactivate()
    {
        self::setOption("triggmine_is_on", "N");
    }

    function updateOptions($aPostData)
    {
        self::setOption("triggmine_rest_api", (isset($aPostData['triggmine_rest_api']) ? $aPostData['triggmine_rest_api'] : ""));
        self::setOption("triggmine_token", (isset($aPostData['triggmine_token']) ? $aPostData['triggmine_token'] : ""));
        self::setOption("triggmine_cart_url", (isset($aPostData['triggmine_cart_url']) ? $aPostData['triggmine_cart_url'] : ""));
    }

    protected static function getErrorMessage($error_message_id, $param = '')
    {
        global $MESS;
        $message = isset($MESS[$error_message_id]) ? $MESS[$error_message_id] : 'Unknown error';
        $message = str_replace('%1', $param, $message);
        return array('message' => $message);
    }

    function updateStatus($aPostData)
    {
        $errors = array();

        if (isset($aPostData['triggmine_is_on']))
        {
            $aData = array
            (
                "Method"    => "Activate",
                "Token"     => CTriggMine::triggmine_token(),
                "Data"      => array(
                    "field0" => "field0",
               ),
           );

            $aResponse = CTriggMine::sendData($aData);

            if (is_array($aResponse) && isset($aResponse['ErrorCode']) && (int)$aResponse['ErrorCode'] == 0) {
                self::setOption("triggmine_is_on", "Y");
            } else {
                $errors[] = self::getErrorMessage('failed_to_activate', self::getErrorByCode((int)$aResponse['ErrorCode']));
            }
        } else {

            self::setOption("triggmine_is_on", "N");

            /*$aData = array
            (
                "Method"    => "Deactivate",
                "Token"     => CTriggMine::triggmine_token(),
                "Data"      => array(),
           );

            $aResponse = CTriggMine::sendData($aData);



            if (is_array($aResponse) && isset($aResponse['ErrorCode']) && (int)$aResponse['ErrorCode'] == 0) {
                self::setOption("triggmine_is_on", "N");
            } else {
                $errors[] = self::getErrorMessage('failed_to_deactivate', self::getErrorByCode((int)$aResponse['ErrorCode']));
            }*/
        }

        return $errors;
    }

    function getErrors()
    {
        $errors = array();

        if (self::checkVersion() !== true) {
            $errors[] = self::getErrorMessage('low_version', self::$MIN_VERSION);
            return $errors;
        }

        if (self::checkCartUrl() !== true) {
            $errors[] = self::getErrorMessage('wrong_cart_url');
            return $errors;
        }

        if (($missingModule = self::checkModules()) !== true) {
            $errors[] = self::getErrorMessage('missing_module', $missingModule);
            return $errors;
        }

        if (($missingFunction = self::checkFunctions()) !== true) {
            $errors[] = self::getErrorMessage('missing_function', $missingFunction);
            return $errors;
        }

        if ((self::checkUrlFopenEnabled() !== true) && (self::checkCurlEnabled() !== true)) {
            $errors[] = self::getErrorMessage('no_transport');
            return $errors;
        }

        $triggmine_rest_api = self::triggmine_rest_api();

        if (empty($triggmine_rest_api)) {
            $errors[] = self::getErrorMessage('empty_api_url');
            return $errors;
        }

        if (!self::isValidUrl($triggmine_rest_api)) {
            $errors[] = self::getErrorMessage('invalid_api_url', $triggmine_rest_api);
            return $errors;
        }

        $triggmine_token = self::triggmine_token();

        if (empty($triggmine_token)) {
            $errors[] = self::getErrorMessage('empty_api_key');
            return $errors;
        }

        $apiResponse = self::checkApi();

        if ($apiResponse === self::API_NO_ACCESS) {
            $errors[] = self::getErrorMessage('no_access_to_api', self::$triggmine_rest_api);
            return $errors;
        }

        if ($apiResponse === self::API_INVALID_RESPONSE) {
            $errors[] = self::getErrorMessage('invalid_response_from_api', self::$triggmine_rest_api);
        }

        if ((int)$apiResponse >= 99) {
            if ((int)$apiResponse == 110 || (int)$apiResponse == 111 || (int)$apiResponse == 112) {
                $errors[] = self::getErrorMessage('invalid_token');
            } else {
                $errors[] = self::getErrorMessage('api_returns_error', self::getErrorByCode($apiResponse));
            }
        }

        if (empty($errors)) {
            self::setOption("last_check_time", time());
        }

        return $errors;
    }

    public static function isValidUrl($url)
    {
        $passedCheck = filter_var($url, FILTER_VALIDATE_URL);

        if ($passedCheck === false) {
            return false;
        } else {
            return true;
        }
    }

    public static function setLastCheckTime(){
        self::setOption("last_check_time", time());
    }

    public static function getLastCheckTime($format){
        $time = self::getOption("last_check_time");
        return date($format, $time);
    }

    function checkVersion()
    {
        if (self::versionIs('>=', self::$MIN_VERSION)) {
            return true;
        } else {
            return false;
        }
    }

    function checkCartUrl()
    {
        $url = self::triggmine_cart_url_full(true);
        if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        } else {
            return false;
        }
    }

    function checkUrlFopenEnabled()
    {
        if (ini_get('allow_url_fopen') && function_exists('file_get_contents')) {
            return true;
        } else {
            return false;
        }
    }

    function checkCurlEnabled()
    {
        if (function_exists('curl_version')) {
            return true;
        } else {
            return false;
        }
    }

    function checkApi()
    {
        $aData = array(
            "Method"    => "Test",
            "Token"    => self::triggmine_token()
       );

        $aData = self::sendData($aData);

        if (!empty($aData) && is_array($aData) && isset($aData['ErrorCode']) && (int)$aData['ErrorCode'] == 0) {
            return self::API_OK;
        } elseif (!empty($aData) && is_array($aData) && isset($aData['ErrorCode'])) {
            return $aData['ErrorCode'];
        } elseif (empty($aData)) {
            return self::API_NO_ACCESS;
        } else {
            return self::API_INVALID_RESPONSE;
        }
    }

    function checkModules()
    {
        foreach (self::$NECESSARY_MODULES as $moduleId) {
            if (!IsModuleInstalled($moduleId)) {
                return $moduleId;
            }
        }
        return true;
    }

    function checkFunctions()
    {
        foreach (self::$NECESSARY_FUNCTIONS as $functionName) {
            if (!function_exists($functionName)) {
                return $functionName;
            }
        }
        return true;
    }

    function getProductData($iProductId)
    {
        if (self::versionIs('>=', '3.0.8')) {
            if (CModule::IncludeModule("catalog") && CModule::IncludeModule("iblock")){
                $res = CIBlockElement::GetByID($iProductId);
                $arRes = $res->GetNext();
                if (self::versionIs('>=', '11.0.2')) {
                    $mxResult = CCatalogSku::GetProductInfo($arRes['ID']);
                    if (is_array($mxResult)) {
                        $db_res = CIBlockElement::GetByID($mxResult['ID']);
                        $arRes['BASE_PRODUCT'] = $db_res->GetNext();
                    }
                }
                return $arRes;
            }
        } elseif (self::versionIs('>=', '3.0.5')){
            $res = GetIBlockElement($iProductId);
            return $res;
        } else {

        }
    }

    function getDomain()
    {
        if (defined('SITE_SERVER_NAME') && strlen(SITE_SERVER_NAME) != 0) {
            $sDomain = SITE_SERVER_NAME;
        } else {
            $sDomain = $_SERVER['HTTP_HOST'];
        }
        return "http://" . $sDomain;
    }

    /**
     * ���������� ������ ��� ���������� ������ �� ��������.
     */
    function addProduct($iProductId, $aProduct, $isUpdate = false)
    {
        CTriggMineDebug::debugInfo("add product $iProductId");
        CTriggMineDebug::debugInfo(print_r($aProduct, true));

        // cannot be bought - do nothing (don't add to basket, don't update in the basket)
        if ($aProduct['CAN_BUY'] === 'N') {
            CTriggMineDebug::debugInfo("product $iProductId cannot be bought");
            return;
        }

        // if this is subscription - do nothing (don't add to basket, don't update in the basket)
        if (isset($aProduct['SUBSCRIBE']) && $aProduct['SUBSCRIBE'] === 'Y') {
            CTriggMineDebug::debugInfo("this is a subscribe for $iProductId");
            return;
        }

        if (self::isEnabled())
        {
            try
            {
                if (!$aProductFull = self::getProductData($aProduct['PRODUCT_ID'])) throw new Exception('NoProductObject');

                $CartItemId = self::createProductID($aProductFull);

                $aData = array
                (
                    "Method"    => "CreateReplaceCartItem",
                    "Token"     => self::triggmine_token(),
                    "Data"      => array
                    (
                        "CartItemId"        => $CartItemId,
                        "Title"             => $aProduct['NAME'],
                        "ShortDescription"  => $aProductFull['PREVIEW_TEXT'],
                        "FullDescription"   => $aProductFull['DETAIL_TEXT'],

                        "Price"             => $aProduct['PRICE'],
                        "Currency"          => $aProduct['CURRENCY'],
                        "Count"             => $aProduct['QUANTITY'],
                   ),
                );

                if ($isUpdate) {
                    $aData['Data']['ReplaceOnly'] = 1;
                }

                // preview picture

                $previewPictureId = $aProductFull['PREVIEW_PICTURE'];
                if (!$previewPictureId) {
                    $previewPictureId = $aProductFull['BASE_PRODUCT']['PREVIEW_PICTURE'];
                }

                if ($previewPictureId && CFile::GetPath($previewPictureId) != '') {
                    $aData['Data']["ThumbnailUrl"] = self::getDomain() . CFile::GetPath($previewPictureId);
                }

                // detail picture

                $detailPictureId = $aProductFull['DETAIL_PICTURE'];
                if (!$detailPictureId) {
                    $detailPictureId = $aProductFull['BASE_PRODUCT']['DETAIL_PICTURE'];
                }

                if ($detailPictureId && CFile::GetPath($detailPictureId) != '') {
                    $aData['Data']["ImageUrl"] = self::getDomain() . CFile::GetPath($detailPictureId);
                }

                $aData = self::checkOutgoingData($aData);

                $aData = self::sendData($aData);
                if ($aData == false) return false;
                self::checkIncomingData($aData);
            }
            catch (Exception $e)
            {
                self::reportLog($e);
            }
        }

    }

    /**
     * ����������, ���� ����� ������� �� �������� �������.
     */
    function updateProduct($iProductId, $aProduct)
    {
        if (self::isEnabled())
        {
            try
            {
                if (isset($aProduct['IGNORE_CALLBACK_FUNC'])) {
                    CTriggMineDebug::debugInfo("ignored update of product - " . json_encode($aProduct));
                    return;
                }

                CTriggMineDebug::debugInfo("update product $iProductId - " . json_encode($aProduct));

                if (!$aProduct = CSaleBasket::GetById($iProductId)) throw new Exception('NoProductObject');

                self::addProduct($iProductId, $aProduct, true);
            }
            catch (Exception $e)
            {
                self::reportLog($e);
            }
        }
    }

    /**
     * ����������, ���� ����� ������� �� �������� �������.
     */
    function onOrderPropsChange($arResult, $arUserResult, $arParams)
    {

    }

    /**
     * ���������� ������ �� �������� ������ �� �������.
     */
    function deleteProduct($iProductId)
    {
        if (self::isEnabled() == true && self::getBuyerId() !== null && self::getCartId() !== null)
        {
            try
            {
                if (!$aProduct = CSaleBasket::GetById($iProductId)) throw new Exception('NoProductObject');
                if (!$aProductFull = self::getProductData($aProduct['PRODUCT_ID'])) throw new Exception('NoProductObject');

                $CartItemId = self::createProductID($aProduct);
                $aData = array
                (
                    "Method"    => "DeleteCartItem",
                    "Token"     => self::triggmine_token(),
                    "Data"      => array
                    (
                        "CartItemId"    => (string) $aProduct['PRODUCT_ID'],
                   ),
               );
                $aData = self::checkOutgoingData($aData);
                $aData = self::sendData($aData);
                if ($aData == false) return false;
                self::checkIncomingData($aData);
            }
            catch (Exception $e)
            {
                self::reportLog($e);
            }
        }
    }

    /**
     * ����� ����� ��� ������. ����� �������� ������ �� ����, ���� ������� �� �������
     */
    function purchaseOrder($id, $arFields)
    {
        CTriggMineDebug::debugInfo("Order ID: $id");
        CTriggMineDebug::debugInfo(print_r($arFields, true));

        $aData = array
        (
            "Method"    => "PurchaseCart",
            "Token"     => self::triggmine_token(),
            "Data"      => array(),
       );
        $aData = self::checkOutgoingData($aData);
        if (!empty($_SESSION['triggmine']['RedirectId']))
            $aData['Data']['RedirectId'] = $_SESSION['triggmine']['RedirectId'];

        self::sendData($aData);
        if ($aData == false) return false;
    }

    /**
     * ����������� ���������� � ����
     * ������������ ��� ���������/���������� sBuyerId
     */
    public function buyerLogin($arParams)
    {
        $aData = array(
            "Method"    => "GetBuyerId",
            "Token"     => self::triggmine_token(),
            "Data"      => array(
                "BuyerEmail" => $arParams['user_fields']['EMAIL'],
           ),
       );

        CTriggMineDebug::debugInfo($arParams['user_fields']);

        $aData = self::checkOutgoingData($aData);
        $aData = self::sendData($aData);
        if ($aData == false) return false;
        self::checkIncomingData($aData);

        self::updateBuyerInfo($arParams);
    }

    public static function updateBuyerInfo($arParams)
    {
        $aData = array(
            "Method"    => "CreateReplaceBuyerInfo",
            "Token"     => self::triggmine_token(),
            "Data"      => array(
                "BuyerId" => self::getBuyerId(),
                "BuyerEmail" => $arParams['user_fields']['EMAIL'],
                "FirstName" => $arParams['user_fields']['NAME'],
                "LastName" => $arParams['user_fields']['LAST_NAME']
            ),
        );

        $aData = self::sendData($aData);
        if ($aData == false) return false;
        self::checkIncomingData($aData);
    }


    public static function updateCartPingTime()
    {
        $_SESSION['triggmine']['LastCartPingTime'] = time();
    }

    public static function timeToPingCart()
    {
        if (empty($_SESSION['triggmine']['LastCartPingTime'])) return true;
        $timePassed = time() - $_SESSION['triggmine']['LastCartPingTime'];
        if ($timePassed >= 60) {
            return true;
        } else {
            return false;
        }
    }

    public static function isAdminArea()
    {
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, '/bitrix/admin') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public static function isNormalVisitor()
    {
        // if this is a robot - no request for visitor
        if (IsModuleInstalled('statistic') && empty($_SESSION['SESS_GUEST_ID'])) {
            // this is a robot, no actions
            CTriggMineDebug::debugLine('GUEST: ROBOT');
            return false;
        }

        if (strpos($_SERVER['SCRIPT_FILENAME'], 'ajax.php') !== false) {
            CTriggMineDebug::debugLine('GUEST: AJAX');
            return false;
        }

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'facebookexternalhit') !== false) {
            CTriggMineDebug::debugLine('GUEST: FACEBOOK');
            return false;
        }

        return true;
    }

    /**
     * @param $arParams
     */
    public function getVisitorId($arParams)
    {
        CTriggMineDebug::onPageLoad();

        if (self::isNormalVisitor()) {

            if (!self::getSessionVisitorId() || empty($_SESSION['triggmine']['VisitorId'])) {
                self::requestVisitorId();
            }

            if (self::getCartId() && self::timeToPingCart() && !self::isAdminArea()) {
                self::pingCart();
                self::updateCartPingTime();
            }

            // debug visitor data
            CTriggMineDebug::debugLine('GUEST ' . $_SESSION['SESS_GUEST_ID'] . ' = ' . $_SESSION['triggmine']['VisitorId']);
        }
    }

    public static function pingCart()
    {
        $aData = array(
            "Method"    => "PingCart",
            "Token"     => self::triggmine_token(),
            "Data"      => array(
                "BuyerId" => self::getBuyerId(),
                "CartId" => self::getCartId()
            )
        );

        $aData = self::sendData($aData);
        if ($aData == false) return false;
    }

    public static function requestVisitorId()
    {
        $aData['Agent'] = 'Bitrix';

        $aData = array(
            "Method"    => "GetVisitorId",
            "Token"     => self::triggmine_token(),
            "Data"      => $aData
        );

        if (self::getSessionVisitorId() !== null) {
            $aData['Data']['VisitorId'] = (string)self::getSessionVisitorId();
        }

        $aData = self::checkOutgoingData($aData);
        $aData = self::sendData($aData);
        if ($aData == false) return false;
        self::checkIncomingData($aData);
    }

    public static function setBuyerId($sBuyerId)
    {
        self::$sBuyerId = $sBuyerId;
        setcookie('triggmine[BuyerId]', $sBuyerId, time() + 86400 * 364, "/");
    }

    public static function setCartId($sCartId)
    {
        self::$sCartId = $_SESSION['triggmine']['CartId'] = $sCartId;
    }

    public static function setRedirectId($sRedirectId){
        $_SESSION['triggmine']['RedirectId'] = $sRedirectId;
    }

    /**
     * 
     */
    public function retrieveLostCart()
    {
        if (empty($_GET['BuyerId']) || empty($_GET['CartId']) || empty($_GET['RedirectId'])) return true;

        try
        {
            self::setBuyerId($_GET['BuyerId']);
            self::setCartId($_GET['CartId']);
            self::setRedirectId($_GET['RedirectId']);

            $aData = array
            (
                "Method"    => "GetCartContent",
                "Token"     => self::triggmine_token(),
                "Data"      => array(),
           );
            unset($_GET['BuyerId'], $_GET['CartId'], $_GET['RedirectId']);

            $aData = self::checkOutgoingData($aData);

            $aData = self::sendData($aData);
            if ($aData == false) return false;
            self::checkIncomingData($aData);

            if (empty($aData['Data']['Items'])) return true;

            if (CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID()) == false) throw new Exception("Can't cleanup cart");

            foreach ($aData['Data']['Items'] as $aProduct)
            {
                $aProductIDs = explode("|", $aProduct['CartItemId']);
                $CartItemId = array_shift($aProductIDs);

                if (count($aProductIDs) > 0)
                {
                    foreach ($aProductIDs as $s)
                    {
                        $s = explode('_', $s);

                        $aProductParams[] = array(
                            "NAME"  => $s[0],
                            "CODE"  => $s[1],
                            "VALUE" => $s[2],
                            "SORT"  => $s[3]
                       );
                    }
                }

                if (!Add2BasketByProductID($CartItemId, $aProduct['Count'], $aProductParams)) throw new Exception("Can't add product");
            }

            self::checkIncomingData($aData);
            LocalRedirect(self::triggmine_cart_url_full());
        }
        catch (Exception $e)
        {
            self::reportLog($e);
        }
    }

    public static function getBuyerId()
    {
        if (empty(self::$sBuyerId) && !empty($_COOKIE['triggmine']['BuyerId'])){
            self::$sBuyerId = $_COOKIE['triggmine']['BuyerId'];
        }

        if (!empty(self::$sBuyerId)){
            return self::$sBuyerId;
        } else {
            return null;
        }
    }

    public static function getCartId()
    {
        if (empty(self::$sCartId) && !empty($_SESSION['triggmine']['CartId'])){
            self::$sCartId = $_SESSION['triggmine']['CartId'];
        }

        if (!empty(self::$sCartId)){
            return self::$sCartId;
        } else {
            return null;
        }
    }

    public static function getSessionVisitorId()
    {
        if (empty(self::$sVisitorId) && !empty($_COOKIE['triggmine']['VisitorId'])){
            self::$sVisitorId = $_COOKIE['triggmine']['VisitorId'];
        }

        if (!empty(self::$sVisitorId)){
            return self::$sVisitorId;
        } else {
            return null;
        }
    }

    public static function getRedirectId()
    {
        if (!empty($_SESSION['triggmine']['RedirectId'])) {
            return $_SESSION['triggmine']['RedirectId'];
        } else {
            return null;
        }
    }


    /**
     * �������� ������� sBuyerId && sCartId && $sVisitorId  � ������������ ������.
     * ���� ��� ����, ����������� � ������ ������������ ������.
     */
    function checkOutgoingData($aData)
    {
        if (self::getBuyerId() !== null) {
            $aData['Data']['BuyerId'] = (string)self::getBuyerId();
        }

        if (self::getCartId() !== null) {
            $aData['Data']['CartId'] = (string)self::getCartId();
        }

        $aData['Data']['CartUrl'] = self::triggmine_cart_url_full();

        if (self::getRedirectId() !== null) {
            $aData['Data']['RedirectId'] = (string)self::getRedirectId();
        }

        return $aData;
    }

    public static function setVisitorId($sVisitorId)
    {
        self::$sVisitorId = $sVisitorId;
        setcookie('triggmine[VisitorId]', $sVisitorId, time() + 86400 * 364 * 20, "/");
        $_SESSION['triggmine']['VisitorId'] = $sVisitorId;
    }

    /**
     * �������� ������� sBuyerId && sCartId && $sVisitorId � ���������� ������.
     * ���� ��� ����, ��������� �� � ������ � �����.
     */
    function checkIncomingData($aData)
    {
        if (!empty($aData['Data']['VisitorId']))
        {
            self::setVisitorId($aData['Data']['VisitorId']);
        }

        if (!empty($aData['Data']['BuyerId']))
        {
            self::setBuyerId($aData['Data']['BuyerId']);
        }

        if (!empty($aData['Data']['CartId']))
        {
            self::setCartId($aData['Data']['CartId']);
        }

        if (!empty($aData['Data']['RedirectId']))
        {
            self::setRedirectId($aData['Data']['RedirectId']);
        }
    }

    /**
     * Sending data
     */
    function sendData($aData)
    {
        if (!self::isEnabled() && $aData['Method'] != 'Test' && $aData['Method'] != 'Activate') return false;

        if (strtoupper(LANG_CHARSET) != 'UTF-8')
        {
            $aData = self::convertArray($aData);
        }

        $sUrl = strstr(self::triggmine_rest_api(), "http://") ? self::triggmine_rest_api() : "http://" . self::triggmine_rest_api();


        if (self::checkUrlFopenEnabled()) {
            $sResult = self::_sendDataWithFopen($sUrl, $aData);
        } elseif (self::checkCurlEnabled()) {
            $sResult = self::_sendDataWithCurl($sUrl, $aData);
        } else {
            $sResult = false;
        }

        $aResponse = json_decode($sResult, true);

        CTriggMineDebug::debugRequest($aData, $aResponse);

        if (is_array($aResponse)) {
            if (strtoupper(LANG_CHARSET) != 'UTF-8') {
                $aResponse = self::convertArray($aResponse, 1);
            }
            return $aResponse;
        } else {
            return false;
        }
    }

    function _sendDataWithFopen($sUrl, $aData)
    {
        $aOptions = array(
            'http'      => array(
                'method'    => 'POST',
                'content'   => json_encode($aData),
                'header'    =>  "Content-Type: application/json\r\n" . "Accept: application/json\r\n"
           )
       );

        $oContext  = stream_context_create($aOptions);
        $sResult = file_get_contents($sUrl, false, $oContext);
        return $sResult;
    }

    function _sendDataWithCurl($sUrl, $aData)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $sUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($aData));
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    function isOn($ignoreSession = false)
    {
        if (!$ignoreSession && isset($_SESSION['triggmine']) && isset($_SESSION['triggmine']['session_triggmine_is_on']) && $_SESSION['triggmine']['session_triggmine_is_on']== 'Y') {
            return 'Y';
        }

        if (!isset(self::$triggmine_is_on)) {
            self::$triggmine_is_on = COption::GetOptionString(self::$MODULE_ID, "triggmine_is_on");
        }

        if (empty(self::$triggmine_is_on)) {
            return false;
        }

        return self::$triggmine_is_on;
    }

    function triggmine_rest_api()
    {
        if (!isset(self::$triggmine_rest_api))
            self::$triggmine_rest_api = COption::GetOptionString(self::$MODULE_ID, "triggmine_rest_api");
        if (empty(self::$triggmine_rest_api)) return false;

        return self::$triggmine_rest_api;
    }

    function triggmine_token()
    {
        if (!isset(self::$triggmine_token))
            self::$triggmine_token = COption::GetOptionString(self::$MODULE_ID, "triggmine_token");
        if (empty(self::$triggmine_token)) return false;

        return self::$triggmine_token;
    }

    function triggmine_cart_url_full($getFresh = false)
    {
        return self::triggmine_cart_host() . "/" . self::triggmine_cart_url($getFresh);
    }

    function triggmine_cart_host()
    {
        if (defined('SITE_SERVER_NAME') && strlen(SITE_SERVER_NAME) != 0){
            $path =  'http://' . SITE_SERVER_NAME;
        } else {
            $path = 'http://' . $_SERVER['HTTP_HOST'];
        }

        return $path;
    }

    function triggmine_cart_url($getFresh = false)
    {
        if (!isset(self::$triggmine_cart_url) || $getFresh) {
            self::$triggmine_cart_url = COption::GetOptionString(self::$MODULE_ID, "triggmine_cart_url");
        }

        if (empty(self::$triggmine_cart_url)) {
            return "personal/cart/";
        }

        return self::$triggmine_cart_url;
    }

    function isEnabled()
    {
        if (self::isOn() == "N" || self::triggmine_rest_api() == "" || self::triggmine_token() == "") return false;
        return true;
    }

    function getErrorByCode($iCodeId)
    {
        $aErrors = array(
            'UnhandledException'            =>  99,         // �������������� ������ � ������ ������
            'InvalidRequestFormat'          => 100,         // ������������ ������ �������: �� JSON

            'TokenNotFound'                 => 110,         // ������� �������� �� ������: Token
            'TokenInvalidType'              => 111,         // ������������ ��� ���������: Token
            'TokenUnsupportedValue'         => 112,         // �� �������������� �������� ���� ������

            'MethodNotFound'                => 120,         // ������� �������� �� ������: Method
            'MethodInvalidType'             => 121,         // ������������ ��� ���������: Method
            'MethodUnsupportedValue'        => 122,         // �� �������������� �������� ���� ������

            'DataNotFound'                  => 130,         // ������� �������� �� ������: Data
            'DataInvalidType'               => 131,         // ������������ ��� ���������: Data

            'BuyerIdNotFound'               => 140,         // ������� �������� �� ������: BuyerId
            'BuyerIdInvalidType'            => 141,         // ������������ ��� ���������: BuyerId
            'BuyerIdUnsupportedValue'       => 142,         // �� �������������� �������� ���� ������
            'BuyerIdUnregistered'           => 143,         // ����������� � ����

            'CartIdNotFound'                => 150,         // ������� �������� �� ������: CartId
            'CartIdInvalidType'             => 151,         // ������������ ��� ���������: CartId
            'CartIdUnsupportedValue'        => 152,         // �� �������������� �������� ���� ������
            'CartIdUnregistered'            => 153,         // ����������� � ����

            'BuyerEmailNotFound'            => 160,         // ������� �������� �� ������: BuyerEmail
            'BuyerEmailInvalidType'         => 161,         // ������������ ��� ���������: BuyerEmail
            'BuyerEmailUnsupportedValue'    => 162,         // �� �������������� �������� ���� ������
            'BuyerEmailInvalidSyntax'       => 163,         // ���� �� �������� �������� ���������� ����������

            'CartItemIdNotFound'            => 170,         // ������� �������� �� ������: CartItemId
            'CartItemIdInvalidType'         => 171,         // ������������ ��� ���������: CartItemId
            'CartItemIdUnsupportedValue'    => 172,         // �� �������������� �������� ���� ������
            'CartItemIdIsNull'              => 173,         //

            'CartStateNotFound'             => 180,         // ������� �������� �� ������: CartState
            'CartStateInvalidType'          => 181,         // ������������ ��� ���������: CartState
            'CartStateUnsupportedValue'     => 182,         // �� �������������� �������� ���� ������

            'RedirectIdNotFound'            => 190,
            'RedirectIdInvalidType'         => 191,
            'RedirectIdUnsupportedValue'    => 192,
            'RedirectIdInvalidFormat'       => 193,
            'RedirectIdUnregistered'        => 194,

            'LogIdNotFound'                 => 210,
            'LogIdInvalidType'              => 211,
            'LogIdUnsupportedValue'         => 212,
            'LogIdInvalidFormat'            => 213,
            'LogIdUnregistered'             => 214,
       );

        foreach ($aErrors as $sName => $iErrorId)
        {
            if ($iErrorId == $iCodeId) return $sName;
        }

        return false;
    }

    public function reportLog(Exception $e)
    {
        $aData = array
            (
                "Method"    => "Log",
                "Token"     => self::triggmine_token(),
                "Data"      => array
                (
                    "Description" => $e->getMessage() . "\n" . $e->getTraceAsString()
               )
           );

        $aData = self::checkOutgoingData($aData);
        $aData = self::sendData($aData);
        if ($aData == false) return false;
    }

    public function convertArray($aArray, $isInverse = 0)
    {
        foreach ($aArray as $id => $value)
        {
            if (is_numeric($value)) continue;
            if (is_array($value)) {
                $aArray[$id] = self::convertArray($value, ($isInverse == 0) ? 0 : 1);
            } else {
                if ($isInverse == 0)
                    $aArray[$id] = mb_convert_encoding($value, 'UTF-8', 'windows-1251');
                else
                    $aArray[$id] = mb_convert_encoding($value, 'windows-1251', 'UTF-8');
            }
        }
        return $aArray;
    }

    public function createProductID($aProduct)
    {
        $sProductID = (string) $aProduct['ID'];
        if (!empty($aProduct['PROPERTIES']['CML2_LINK']))
        {
            $sProductID .= '|';
            unset($aProduct['PROPERTIES']['CML2_LINK']);
            foreach ($aProduct['PROPERTIES'] as $aProps)
            {
                $aParams = array(
                    "NAME"  => $aProps["NAME"],
					"CODE"  => $aProps["CODE"],
					"VALUE" => $aProps["VALUE"],
					"SORT"  => $aProps["SORT"]
               );
                $sProductID .= implode('_', $aParams) . '|';
            }
            $sProductID =  substr($sProductID, 0, -1);
        }

        return $sProductID;
    }

}

?>