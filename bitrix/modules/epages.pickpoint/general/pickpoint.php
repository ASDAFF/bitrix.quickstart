<?php

class CPickpoint extends CAllPickpoint
{
    private static $processedOrders = array();

    public function PHPArrayToJS($arDest, $sName)
    {
        return "<script>{$sName} = ".self::PHPArrayToJS_in($arDest).'</script>';
    }

    public function PHPArrayToJS_in($arDest)
    {
        if (is_array($arDest)) {
            foreach ($arDest as $k => $v) {
                $arDest[$k] = '"'.$k.'":'.self::PHPArrayToJS_in($v);
            }
            $arDest = '{'.implode(',', $arDest).'}';
        } else {
            $arDest = '"'.self::js_escape($arDest).'"';
        }

        return $arDest;
    }

    public function js_escape($str)
    {
        $obLocation = CSaleLocation::GetList(
            array(
                'SORT' => 'ASC',
                'CITY_NAME_ORIG' => 'ASC',
            ),
            array(
                'COUNTRY_NAME' => 'Russia',
                '!CITY_ID' => null,
                'CITY_LID' => 'en',
                'CITY_NAME' => $sCode,
            ),
            false,
            false,
            array(
                'ID',
                'CITY_ID',
                'CITY_NAME',
                'CITY_NAME_ORIG',
            )
        );

        return $obLocation->Fetch();
    }

    public function GetCity($arFields)
    {
        $iPPID = intval($arFields['PP_ID']) ? intval($arFields['PP_ID']) : 0;

        $iID = 0;
        $sCode = strlen($arFields['CODE']) ? $arFields['CODE'] : '';
        $iPrice = 0;
        $sActive = 'N';
        if ($iPPID) {
            $obPPCity = self::SelectCityByPPID($iPPID);
            if ($arPPCity = $obPPCity->Fetch()) {
                $iBXID = $arPPCity['BX_ID'];
                if ($arPPCity['ACTIVE'] == 'Y') {
                    $sActive = 'Y';
                }
                $arBXCity = self::SelectCityByID($iBXID);
                $iPrice = floatval($arPPCity['PRICE']);
                $sCode = $arBXCity['CITY_NAME'];
            } else {
                $arCity = self::SelectCityByCode($sCode);
                $iBXID = $arCity['ID'];
            }
        } else {
            $arCity = self::SelectCityByCode($sCode);
            $iBXID = $arCity['ID'];
            $iPPID = 0;
        }

        return array(
            'ID' => $iID,
            'CODE' => $sCode,
            'PP_ID' => $iPPID,
            'BX_ID' => $iBXID,
            'PRICE' => $iPrice,
            'ACTIVE' => $sActive,
        );
    }

    public function SelectCityByCode($sCode)
    {
        $obLocation = CSaleLocation::GetList(
            array(
                'SORT' => 'ASC',
                'CITY_NAME_ORIG' => 'ASC',
            ),
            array(
                'COUNTRY_NAME' => 'Russia',
                '!CITY_ID' => null,
                'CITY_LID' => 'en',
                'CITY_NAME' => $sCode,
            ),
            false,
            false,
            array(
                'ID',
                'CITY_ID',
                'CITY_NAME',
                'CITY_NAME_ORIG',
            )
        );

        return $obLocation->Fetch();
    }

    public function SelectCityByID($iBXID)
    {
        $obLocation = CSaleLocation::GetList(
            array(
                'SORT' => 'ASC',
                'CITY_NAME_ORIG' => 'ASC',
            ),
            array(
                'COUNTRY_NAME' => 'Russia',
                '!CITY_ID' => null,
                'CITY_LID' => 'en',
                'CITY_ID' => $iBXID,
            ),
            false,
            false,
            array(
                'ID',
                'CITY_ID',
                'CITY_NAME',
                'CITY_NAME_ORIG',
            )
        );

        return $obLocation->Fetch();
    }

    public function CheckRequest()
    {
        $parameters = array(
            'PP_ID',
            'PP_ADDRESS',
            'PP_NAME',
            'PP_SMS_PHONE',
            'PP_ZONE',
            'PP_COEFF',
        );

        foreach ($parameters as $parameter) {
            if (isset($_REQUEST[$parameter])) {
                $_SESSION['PICKPOINT'][$parameter] = $_REQUEST[$parameter];
            }
            if (isset($_REQUEST['order'][$parameter])) {
                $_SESSION['PICKPOINT'][$parameter] = $_REQUEST['order'][$parameter];
            }
        }
    }

    private static function processOrderAdd($orderId, $deliveryId)
    {
        if (in_array($orderId, static::$processedOrders)) {
            return;
        }

        $MODULE_ID = static::$moduleId;

        $deliveryCode = self::getProfileCodeById($deliveryId);

        if ('pickpoint:postamat' == $deliveryCode) {
            $arToAdd = array(
                'ORDER_ID' => $orderId,
                'POSTAMAT_ID' => $_SESSION['PICKPOINT']['PP_ID'],
                'ADDRESS' => $_SESSION['PICKPOINT']['PP_ADDRESS'],
                'SMS_PHONE' => $_SESSION['PICKPOINT']['PP_SMS_PHONE'],
            );
            self::AddOrderPostamat($arToAdd);
            if (COption::GetOptionString($MODULE_ID, 'pp_add_info', '')) {
                $sDescription = "{$_SESSION['PICKPOINT']['PP_ID']}\n"
                    ."{$_SESSION['PICKPOINT']['PP_ADDRESS']}\n"
                    ."{$_SESSION['PICKPOINT']['PP_SMS_PHONE']}";
                $saleOrder = new CSaleOrder();
                $saleOrder->Update($orderId, array('USER_DESCRIPTION' => $sDescription, 'COMMENTS' => $sDescription));
            }
        }

        unset($_SESSION['PICKPOINT']);
        static::$processedOrders[] = $orderId;
    }

    public function OnOrderAdd($orderId, $arFields)
    {
        static::processOrderAdd($orderId, $arFields['DELIVERY_ID']);
    }

    public static function OnSaleOrderSaved(\Bitrix\Main\Event $event)
    {
        if (!$event->getParameter('IS_NEW')) {
            return;
        }
        /** @var \Bitrix\Sale\Order $order */
        $order = $event->getParameter('ENTITY');
        static::processOrderAdd($order->getId(), reset($order->getDeliverySystemId()));
    }

    public function OnOrderAddV15($orderId, $arFields)
    {
        static::processOrderAdd($orderId, $arFields['DELIVERY_ID']);
    }

    protected static function getProfileCodeById($profileId)
    {
        //bitrix v16+ check profile ID
        if ($profileId !== 'pickpoint:postamat' && class_exists('Bitrix\Sale\Delivery\Services\Table')) {
            $arDelivery = Bitrix\Sale\Delivery\Services\Table::getList(
                array(
                    'filter' => array(
                        'ID' => $profileId,
                    ),
                    'select' => array('CODE'),
                )
            )->fetch()
            ;

            if ($arDelivery['CODE'] == 'pickpoint:postamat') {
                return $arDelivery['CODE'];
            }
        } else {
            return $profileId;
        }
    }

    public function Calculate($arOrder)
    {
        $MODULE_ID = static::$moduleId;

        $ppzoneID = intval($_SESSION['PICKPOINT']['PP_ZONE']) + 2;

        $obZone = self::SelectZoneByID($ppzoneID);
        $price = 0;
        if ($arZone = $obZone->Fetch()) {
            $price = $arZone['PRICE'];
        }

        if (COption::GetOptionString($MODULE_ID, 'pp_use_coeff', '')) {
            if (doubleval($_SESSION['PICKPOINT']['PP_COEFF']) > 1) {
                if (!$coeff = COption::GetOptionString($MODULE_ID, 'pp_custom_coeff', '')) {
                    $coeff = doubleval($_SESSION['PICKPOINT']['PP_COEFF']);
                }

                $price *= $coeff;
            }
        }

        if (intval($price) > 0) {
            $minOrderFreePrice = COption::GetOptionString($MODULE_ID, 'pp_free_delivery_price', '');
            if (intval($minOrderFreePrice) > 0 && $arOrder['PRICE'] >= $minOrderFreePrice) {
                $price = 0;
            }
        }

        return $price;
    }

    public function CheckPPPaySystem($iPSID, $iPTID)
    {
        $arPS = (CSalePaySystem::GetByID($iPSID, $iPTID));
        if (substr_count($arPS['PSA_ACTION_FILE'], 'epages.pickpoint')) {
            return 1;
        }

        return 0;
    }

    public function GetOrdersArray()
    {
        $obOrdersPostamat = self::SelectOrderPostamat();
        $arItems = array();
        while ($arOrderPostamat = $obOrdersPostamat->Fetch()) {
            $obOrder = CSaleOrder::GetList(
                array(),
                array(
                    'ID' => $arOrderPostamat['ORDER_ID'],
                    '!STATUS_ID' => 'F',
                    'CANCELED' => 'N',
                ),
                false,
                false,
                array(
                    'ID',
                    'PAY_SYSTEM_ID',
                    'PERSON_TYPE_ID',
                    'DATE_INSERT',
                    'PRICE',
                    'DELIVERY_ID',
                )
            );
            if ($arOrder = $obOrder->Fetch()) {
                if (strpos($arOrder['DELIVERY_ID'], 'pickpoint') !== false) {
                    $arSettings = unserialize($arOrderPostamat['SETTINGS']);
                    $arItem = array(
                        'ORDER_ID' => $arOrder['ID'],
                        'ORDER_DATE' => $arOrder['DATE_INSERT'],
                        'PAYED_BY_PP' => self::CheckPPPaySystem(
                            $arOrder['PAY_SYSTEM_ID'],
                            $arOrder['PERSON_TYPE_ID']
                        ),
                        'PRICE' => $arOrder['PRICE'],
                        'PP_ADDRESS' => $arOrderPostamat['ADDRESS'],
                        'INVOICE_ID' => $arOrderPostamat['PP_INVOICE_ID'],
                        'SETTINGS' => $arSettings,
                    );
                    $arItems[] = $arItem;
                }
            }
        }

        return $arItems;
    }

    public function GetParam($iOrderID, $iPersonType, $sPPField)
    {
        require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/epages.pickpoint/constants.php';
        $arTableOptions = (unserialize(COption::GetOptionString('epages.pickpoint', 'OPTIONS')));
        if (!isset($arTableOptions[$iPersonType][$sPPField])) {
            $arData = array($arOptionDefaults[$sPPField]);
        } else {
            $arData = $arTableOptions[$iPersonType][$sPPField];
        }
        $arReturn = array();
        foreach ($arData as $arOption) {
            switch ($arOption['TYPE']) {
                case 'ANOTHER':
                    $arReturn[] = $arOption['VALUE'];
                    break;
                case 'USER':
                    $obOrder = CSaleOrder::GetList(
                        array(),
                        array('ID' => $iOrderID),
                        false,
                        false,
                        array(
                            'ID',
                            'USER_ID',
                        )
                    );
                    $arOrder = $obOrder->Fetch();
                    $obUser = CUser::GetByID($arOrder['USER_ID']);
                    if ($arUser = $obUser->Fetch()) {
                        if ($arOption['VALUE'] == 'USER_FIO') {
                            $arReturn[] = $arUser['LAST_NAME'].($arUser['NAME']
                                    ? ' '.$arUser['NAME']
                                    :
                                    '').($arUser['SECOND_NAME'] ? ' '.$arUser['SECOND_NAME'] : '');
                        } elseif (strlen($arUser[$arOption['VALUE']])) {
                            $arReturn[] = $arUser[$arOption['VALUE']];
                        }
                    }
                    break;
                case 'ORDER':
                    $arOrder = CSaleOrder::GetByID($iOrderID);
                    $arReturn[] = $arOrder[$arOption['VALUE']];
                    break;
                case 'PROPERTY':
                    $obProperty = CSaleOrderPropsValue::GetList(
                        array(),
                        array(
                            'ORDER_ID' => $iOrderID,
                            'ORDER_PROPS_ID' => $arOption['VALUE'],
                        ),
                        false,
                        false,
                        array('VALUE')
                    );
                    if ($arProperty = $obProperty->Fetch()) {
                        if (strlen($arProperty['VALUE']) > 0) {
                            $arReturn[] = $arProperty['VALUE'];
                        }
                    }
                    break;
            }
        }

        return $arReturn;
    }

    public function ExportXML($arIDs)
    {
        require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/epages.pickpoint/constants.php';
        global $APPLICATION;

        $sReturn = '<'."?xml version='1.0' encoding='UTF-8'?".">\n<documents>\n";
        foreach ($arIDs as $iOrderID) {
            $obOrder = CSaleOrder::GetList(
                array(),
                array('ID' => $iOrderID),
                false,
                false,
                array(
                    'ID',
                    'PERSON_TYPE_ID',
                    'PAY_SYSTEM_ID',
                )
            );
            if ($arOrder = $obOrder->Fetch()) {
                $obData = self::SelectOrderPostamat($arOrder['ID']);
                $arData = $obData->Fetch();
                $sReturn .= "\t<document>\n";
                $arFIO = self::GetParam($arOrder['ID'], $arOrder['PERSON_TYPE_ID'], 'FIO');
                $sFIO = current($arFIO);
                $sReturn .= "\t\t<fio>{$sFIO}</fio>\n";
                $sReturn .= "\t\t<sms_phone>{$arData['SMS_PHONE']}</sms_phone>\n";
                $arEmail = self::GetParam($arOrder['ID'], $arOrder['PERSON_TYPE_ID'], 'EMAIL');
                $sEmail = current($arEmail);
                $sReturn .= "\t\t<email>{$sEmail}</email>\n";
                $arAdditionalPhones = self::GetParam(
                    $arOrder['ID'],
                    $arOrder['PERSON_TYPE_ID'],
                    'ADDITIONAL_PHONES'
                );
                $sReturn .= "\t\t<additional_phones>\n";
                foreach ($arAdditionalPhones as $sPhone) {
                    $sReturn .= "\t\t\t<phone>{$sPhone}</phone>\n";
                }
                $sReturn .= "\t\t</additional_phones>\n";
                $sReturn .= "\t\t<order_id>{$arOrder['ID']}</order_id>\n";
                if (self::CheckPPPaySystem($arOrder['PAY_SYSTEM_ID'], $arOrder['PERSON_TYPE_ID'])) {
                    $iPrice = number_format($_REQUEST['EXPORT'][$arOrder['ID']]['PAYED'], 2, '.', '');
                } else {
                    $iPrice = 0;
                }
                $sReturn .= "\t\t<summ_rub>{$iPrice}</summ_rub>\n";
                $sReturn .= "\t\t<terminal_id>{$arData['POSTAMAT_ID']}</terminal_id>\n";
                $sReturn .= "\t\t<type_service>{$arServiceTypes[$_REQUEST['EXPORT'][$arOrder['ID']]['SERVICE_TYPE']]}</type_service>\n";
                $sReturn .= "\t\t<type_reception>{$arEnclosingTypes[$_REQUEST['EXPORT'][$arOrder['ID']]['ENCLOSING_TYPE']]}</type_reception>\n";
                $sEmbed = COption::GetOptionString('epages.pickpoint', 'pp_enclosure', '');
                $sReturn .= "\t\t<embed>{$sEmbed}</embed>\n";
                $sReturn .= "\t\t<size_x>{$arSizes[$_REQUEST['EXPORT'][$arOrder['ID']]['SIZE']]['SIZE_X']}</size_x>\n";
                $sReturn .= "\t\t<size_y>{$arSizes[$_REQUEST['EXPORT'][$arOrder['ID']]['SIZE']]['SIZE_Y']}</size_y>\n";
                $sReturn .= "\t\t<size_z>{$arSizes[$_REQUEST['EXPORT'][$arOrder['ID']]['SIZE']]['SIZE_Z']}</size_z>\n";
                $sReturn .= "\t</document>\n";
            }
        }
        $sReturn .= '</documents>';
        $APPLICATION->RestartBuffer();
        ob_start();
        echo $sReturn;
        $contents = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="pickpoint_export.xml"');
        header('Content-Length: '.strlen($contents));
        echo $contents;
        die();
    }

    public function ExportOrders($arIDs)
    {
        global $APPLICATION;
        $MODULE_ID = static::$moduleId;
        $api_login = COption::GetOptionString($MODULE_ID, 'pp_api_login', '');
        $api_password = COption::GetOptionString($MODULE_ID, 'pp_api_password', '');
        $authResult = self::Login($api_login, $api_password);
        if (!is_array($authResult['ERROR'])) {
            $sessionId = $authResult;
            if (strlen($sessionId) > 0) {
                require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/epages.pickpoint/constants.php';
                $ikn_number = COption::GetOptionString($MODULE_ID, 'pp_ikn_number', '');
                $store_city = COption::GetOptionString($MODULE_ID, 'pp_from_city', '');
                $store_address = COption::GetOptionString($MODULE_ID, 'pp_store_address', '');
                $store_phone = COption::GetOptionString($MODULE_ID, 'pp_store_phone', '');
                $sEmbed = COption::GetOptionString($MODULE_ID, 'pp_enclosure', '');
                $ClientReturnAddress = array(
                    'PhoneNumber' => $store_phone,
                    'CityName' => $store_city,
                    'Address' => $store_address,
                );
                $arQuery = array('SessionId' => $sessionId);
                foreach ($arIDs as $iOrderID) {
                    $arSending = array('IKN' => $ikn_number);
                    $arInvoice = array();
                    $obOrder = CSaleOrder::GetList(
                        array(),
                        array('ID' => $iOrderID),
                        false,
                        false,
                        array(
                            'ID',
                            'PRICE',
                            'PERSON_TYPE_ID',
                            'PAY_SYSTEM_ID',
                        )
                    );
                    if ($arOrder = $obOrder->Fetch()) {
                        $obData = self::SelectOrderPostamat($arOrder['ID']);
                        $arData = $obData->Fetch();

                        $arFIO = self::GetParam($arOrder['ID'], $arOrder['PERSON_TYPE_ID'], 'FIO');
                        $sFIO = current($arFIO);

                        $arSending['EDTN'] = $arOrder['ID'];
                        $arInvoice['SenderCode'] = $arOrder['ID'];
                        $arInvoice['Description'] = $sEmbed;
                        $arInvoice['RecipientName'] = $sFIO;
                        $arInvoice['PostamatNumber'] = $arData['POSTAMAT_ID'];
                        $arInvoice['MobilePhone'] = $arData['SMS_PHONE'];

                        $arEmail = self::GetParam($arOrder['ID'], $arOrder['PERSON_TYPE_ID'], 'EMAIL');
                        $sEmail = current($arEmail);

                        $arInvoice['Email'] = $sEmail;
                        if (self::CheckPPPaySystem($arOrder['PAY_SYSTEM_ID'], $arOrder['PERSON_TYPE_ID'])) {
                            $arInvoice['PostageType'] = $arServiceTypesCodes[1];
                        } else {
                            $arInvoice['PostageType'] = $arServiceTypesCodes[0];
                        }
                        $arInvoice['GettingType']
                            = $arEnclosingTypesCodes[$_REQUEST['EXPORT'][$arOrder['ID']]['ENCLOSING_TYPE']];
                        $arInvoice['PayType'] = 1;

                        if (self::CheckPPPaySystem($arOrder['PAY_SYSTEM_ID'], $arOrder['PERSON_TYPE_ID'])
                            || ($_REQUEST['EXPORT'][$arOrder['ID']]['PAYED']) > 0
                        ) {
                            //$iPrice = number_format($_REQUEST["EXPORT"][$arOrder["ID"]]["PAYED"],2,".","");
                            $iPrice = number_format($arOrder['PRICE'], 2, '.', '');
                        } else {
                            $iPrice = 0;
                        }
                        $arInvoice['Sum'] = $iPrice;
                        $arInvoice['ClientReturnAddress'] = $ClientReturnAddress;

                        $arInvoice['UnclaimedReturnAddress'] = $ClientReturnAddress;

                        $arSending['Invoice'] = $arInvoice;
                        $arQuery['Sendings'][] = $arSending;
                    }
                }
                if (count($arQuery['Sendings']) > 0) {
                    $response = self::Query('createsending', $arQuery);

                    foreach ($response->CreatedSendings as $key => $createdSendings) {
                        if ($createdSendings->ErrorMessage) {
                            self::checkErrors($createdSendings);
                        } elseif (intval($createdSendings->InvoiceNumber) > 0) {
                            self::SetOrderInvoice(
                                $arQuery['Sendings'][$key]['Invoice']['SenderCode'],
                                $createdSendings->InvoiceNumber
                            );
                        }
                    }
                    foreach ($response->RejectedSendings as $rejectedSending) {
                        self::checkErrors($rejectedSending);
                    }
                }

                self::Logout($sessionId);
            }
        } else {
            return $authResult;
        }
    }

    public function SaveOrderOptions($orderID)
    {
        if (is_array($_REQUEST['EXPORT'][$orderID])) {
            $settings = serialize($_REQUEST['EXPORT'][$orderID]);
            self::SetOrderSettings($orderID, $settings);
        }
    }

    private function checkErrors($response)
    {
        global $APPLICATION;

        if ($response->ErrorMessage) {
            if (defined('BX_UTF') && BX_UTF == true) {
                $APPLICATION->ThrowException($response->ErrorMessage);
            } else {
                $APPLICATION->ThrowException(
                    $APPLICATION->ConvertCharset($response->ErrorMessage, 'utf-8', 'windows-1251')
                );
            }
        }
    }

    private static function Query($method, $arQuery)
    {
        global $APPLICATION;
        $MODULE_ID = static::$moduleId;
        $bpp_test_mode = COption::GetOptionString($MODULE_ID, 'pp_test_mode', '');
        if ($bpp_test_mode) {
            $apiUrl = '/apitest/';
        } else {
            $apiUrl = '/api/';
        }
        if (!(defined('BX_UTF') && BX_UTF == true)) {
            $arQuery = $APPLICATION->ConvertCharsetArray($arQuery, 'windows-1251', 'utf-8');
        }
        $response = QueryGetData(
            'e-solution.pickpoint.ru',
            '80',
            $apiUrl.$method,
            json_encode($arQuery),
            $error_number = 0,
            $error_text = '',
            'POST',
            '',
            'application/json'
        );

        $response = json_decode($response);

        self::checkErrors($response);

        return $response;
    }

    private function Login($login, $password)
    {
        $arQuery = array(
            'Login' => $login,
            'Password' => $password,
        );

        $response = self::Query('login', $arQuery);

        if (!$response->ErrorMessage && $response->SessionId) {
            return $response->SessionId;
        } else {
            return array('ERROR' => $response->ErrorMessage);
        }
    }

    private function Logout($sessionId)
    {
        $arQuery = array('SessionId' => $sessionId);
        $response = self::Query('logout', $arQuery);
    }

    public function GetCitiesCSV()
    {
        $MODULE_ID = static::$moduleId;
        $iTimeDelta = 86400; //Next Day

        if (@fopen(PP_CSV_URL, 'r')) {
            $sFileData = file_get_contents(PP_CSV_URL);
        }
        if (defined('BX_UTF') && BX_UTF == true) {
            $sFileData = iconv('windows-1251', 'utf-8', $sFileData);
        }
        if (strlen($sFileData) > 0) {
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/epages.pickpoint/cities.csv', $sFileData);
            $hFile = fopen($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/epages.pickpoint/cities.csv', 'r');
            $arCities = array();
            while ($sStr = fgets($hFile)) {
                $arStr = explode(';', $sStr);
                $arCities[] = trim($arStr[0]);
            }
            if (!empty($arCities)) {
                self::DeleteCities($arCities);
            } else {
                $iTimeDelta = 3600;
            } // 1 hour
        } else {
            $iTimeDelta = 3600;
        } // 1 hour
        COption::SetOptionInt($MODULE_ID, 'pp_city_download_timestamp', time() + $iTimeDelta);
    }

    public function GetZonesArray()
    {
        $arZones = array();
        $obZone = self::SelectZones();

        while ($arZone = $obZone->Fetch()) {
            $arZones[$arZone['ZONE_ID']] = $arZone;
        }

        return $arZones;
    }

    public function OnSCOrderOneStepDeliveryHandler(&$arResult, &$arUserResult)
    {
        global $DB;

        if (empty($arResult['DELIVERY'])) {
            return;
        }

        $content = '';

        $str_from_city = COption::GetOptionString('epages.pickpoint', 'pp_from_city');
        ob_start();
        require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/epages.pickpoint/block.php';
        $content = ob_get_contents();
        ob_end_clean();

        $_REQUEST['PP_ID'] = strlen($_REQUEST['PP_ID']) > 0 ? $_REQUEST['PP_ID'] : $_SESSION['PICKPOINT']['PP_ID'];
        $_REQUEST['PP_ADDRESS'] = strlen($_REQUEST['PP_ADDRESS']) > 0 ? $_REQUEST['PP_ADDRESS']
            : $_SESSION['PICKPOINT']['PP_ADDRESS'];
        $_REQUEST['PP_ZONE'] = strlen($_REQUEST['PP_ZONE']) > 0 ? $_REQUEST['PP_ZONE']
            : $_SESSION['PICKPOINT']['PP_ZONE'];
        $_REQUEST['PP_NAME'] = strlen($_REQUEST['PP_NAME']) > 0 ? $_REQUEST['PP_NAME']
            : $_SESSION['PICKPOINT']['PP_NAME'];
        $_REQUEST['PP_COEFF'] = strlen($_REQUEST['PP_COEFF']) > 0 ? $_REQUEST['PP_COEFF']
            : $_SESSION['PICKPOINT']['PP_COEFF'];

        $content .= '<input id = "pp_address" type = "hidden" name = "PP_ADDRESS" value = "'
            .$_REQUEST['PP_ADDRESS']
            .'"/><input id = "pp_zone" type = "hidden" name = "PP_ZONE" value = "'
            .$_REQUEST['PP_ZONE']
            .'"/><input id = "pp_name" type = "hidden" name = "PP_NAME" value = "'
            .$_REQUEST['PP_NAME']
            .'"/><input id = "pp_coeff" type = "hidden" name = "PP_COEFF" value = "'
            .$_REQUEST['PP_COEFF']
            .'"/>';

        //check if sale module was converted to new structure, introduced in v15.5.10
        if (!$DB->TableExists('b_sale_delivery_srv')) { //old version
            if (isset($arResult['DELIVERY']['pickpoint'])) {
                $arResult['DELIVERY']['pickpoint']['PROFILES']['postamat']['DESCRIPTION'] .= $content;
                $arResult['DELIVERY']['pickpoint']['PROFILES']['postamat']['DESCRIPTION']
                    .= '<input id = "pp_id" type = "hidden" name = "PP_ID" value = "'
                    .$_REQUEST['PP_ID']
                    .'" data-delivery-id="pickpoint:postamat"/>';
            }
        } else { //new version
            foreach ($arResult['DELIVERY'] as &$arDelivery) {
                $serviceRes = Bitrix\Sale\Delivery\Services\Table::getList(
                    array(
                        'filter' => array(
                            'ID' => $arDelivery['ID'],
                        ),
                        'select' => array('CODE'),
                    )
                );
                $arDeliveryCode = $serviceRes->fetch();

                if (strpos($arDeliveryCode['CODE'], 'pickpoint') !== false) {
                    $arDelivery['DESCRIPTION'] .= $content;
                    $arDelivery['DESCRIPTION'] .= '<input id = "pp_id" type = "hidden" name = "PP_ID" value = "'
                        .$_REQUEST['PP_ID']
                        .'" data-delivery-id="'.$arDelivery['ID'].'"/>';
                }
            }
        }
    }

    public function addPickpointJs()
    {
        global $APPLICATION;
        $MODULE_ID = static::$moduleId;

        //include widget js files
        $APPLICATION->AddHeadString(
            '<script type="text/javascript" src="//pickpoint.ru/select/postamat.js" charset="utf-8"></script>'
        );
        if (defined('BX_UTF') && BX_UTF == true) {
            $APPLICATION->AddHeadScript("/bitrix/js/{$MODULE_ID}/script_utf.js");
        } else {
            $APPLICATION->AddHeadScript("/bitrix/js/{$MODULE_ID}/script.js");
        }
        //$APPLICATION->AddHeadString('<script type="text/javascript">if(typeof CheckData === "function") CheckData();</script>');
    }
}
