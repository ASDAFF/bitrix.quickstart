<?
    IncludeModuleLangFile(__FILE__);

    use Bitrix\Highloadblock as HLBlock;
    use Bitrix\Main\Entity;

    class CStartShopToolsIBlock
    {
        private static $PrefixQuantity = "STARTSHOP_QUANTITY";
        private static $PrefixQuantityRatio = "STARTSHOP_QUANTITY_RATIO";
        private static $PrefixPrice = "STARTSHOP_PRICE";
        private static $PrefixCurrency = "STARTSHOP_CURRENCY";
        private static $PrefixPictures = "STARTSHOP_PICTURES";
        private static $PrefixArticle = "STARTSHOP_ARTICLE";
        private static $PrefixTraits = "STARTSHOP_TRAITS";
        private static $PrefixOfferLink = "STARTSHOP_OFFER_LINK";
        private static $PrefixUserExternalId = "STARTSHOP_USERID";

        public static function GetQuantityPrefix() {
            return static::$PrefixQuantity;
        }

        public static function GetQuantityRatioPrefix() {
            return static::$PrefixQuantityRatio;
        }

        public static function GetPricePrefix() {
            return static::$PrefixPrice;
        }

        public static function GetCurrencyPrefix() {
            return static::$PrefixCurrency;
        }

        public static function GetPicturesPrefix() {
            return static::$PrefixPictures;
        }

        public static function GetArticlePrefix() {
            return static::$PrefixArticle;
        }

        public static function GetTraitsPrefix() {
            return static::$PrefixTraits;
        }

        public static function GetItemPicture($arItem, $iWidth = false, $iHeight = false, $bPreviewMain = false, $iResizeMode = BX_RESIZE_IMAGE_PROPORTIONAL_ALT)
        {
            $arPicture = array();

            if (!empty($arItem['PREVIEW_PICTURE']) || !empty($arItem['DETAIL_PICTURE'])) {
                if ($bPreviewMain) {
                    if (!empty($arItem['PREVIEW_PICTURE']))
                        $arPicture = $arItem['PREVIEW_PICTURE'];

                    if (empty($arPicture) && !empty($arItem['DETAIL_PICTURE']))
                        $arPicture = $arItem['DETAIL_PICTURE'];
                } else {
                    if (!empty($arItem['DETAIL_PICTURE']))
                        $arPicture = $arItem['DETAIL_PICTURE'];

                    if (empty($arPicture) && !empty($arItem['PREVIEW_PICTURE']))
                        $arPicture = $arItem['PREVIEW_PICTURE'];
                }

                if (is_array($arPicture))
                    if (is_numeric($arPicture['ID'])) {
                        $arPicture = $arPicture['ID'];
                    } else {
                        $arPicture = array();
                    }

                if (!empty($arPicture)) {
                    $arPicture = CFile::GetByID($arPicture)->Fetch();

                    if (!empty($arPicture)) {
                        if (is_numeric($iWidth) && is_numeric($iHeight)) {
                            $arSizedPicture = CFile::ResizeImageGet($arPicture['ID'], array("width" => $iWidth, "height" => $iHeight), $iResizeMode, true);
                            $arPicture['SRC'] = $arSizedPicture['src'];
                            $arPicture['HEIGHT'] = $arSizedPicture['height'];
                            $arPicture['WIDTH'] = $arSizedPicture['width'];
                        } else {
                            $arPicture['SRC'] = CFile::GetPath($arPicture['ID']);
                        }
                    } else {
                        $arPicture = array();
                    }
                } else {
                    $arPicture = array();
                }
            }

            CStartShopEvents::Call('OnToolsIBlockItemPictureGet', array(
                'ITEM' => $arItem,
                'WIDTH' => $iWidth,
                'HEIGHT' => $iHeight,
                'PREVIEW_MAIN' => $bPreviewMain,
                'RESIZE_MODE' => $iResizeMode,
                'PICTURE' => &$arPicture
            ));

            return $arPicture;
        }

        public static function GetPricesValues($arItem, $arPrices, $sCurrencyCodeConvert = null, $sLanguageID = LANGUAGE_ID)
        {
            $arPricesValues = array();

            if (!is_array($arItem) || !is_array($arPrices) || empty($arPrices) || empty($arItem['PROPERTIES']))
                return $arPricesValues;

            foreach ($arPrices as $arPrice) {
                if (!empty($arItem['PROPERTIES'][static::$PrefixPrice.'_'.$arPrice['ID']])) {
                    $fPrice = $arItem['PROPERTIES'][static::$PrefixPrice.'_'.$arPrice['ID']]['VALUE'];
                    $sCurrencyCode = $arItem['PROPERTIES'][static::$PrefixCurrency.'_'.$arPrice['ID']]['VALUE_XML_ID'];

                    if (!is_numeric($fPrice))
                        continue;

                    $fPrice = floatval($fPrice);

                    if (!empty($sCurrencyCodeConvert)) {
                        $arPriceValue = CStartShopCurrency::ConvertAndFormatAsArray($fPrice, $sCurrencyCode, $sCurrencyCodeConvert, $sLanguageID);
                    } else {
                        $arPriceValue = CStartShopCurrency::FormatAsArray($fPrice, $sCurrencyCode, $sLanguageID);
                    }

                    $arPriceValue['TYPE'] = $arPrice['CODE'];
                    $arPricesValues[$arPriceValue['TYPE']] = $arPriceValue;
                }
            }

            CStartShopEvents::Call('OnToolsIBlockPricesValuesGet', array(
                'ITEM' => $arItem,
                'PRICES' => $arPrices,
                'LANGUAGE_ID' => $sLanguageID,
                'VALUES' => &$arPricesValues
            ));

            return $arPricesValues;
        }

        public static function GetQuantityValue($arItem) {
            $fValue = floatval($arItem['PROPERTIES'][static::$PrefixQuantity]['VALUE']);

            CStartShopEvents::Call('OnToolsIBlockQuantityValueGet', array(
                'ITEM' => $arItem,
                'VALUE' => &$fValue
            ));

            return $fValue;
        }

        public static function GetQuantityRatioValue($arItem) {
            $fValue = floatval($arItem['PROPERTIES'][static::$PrefixQuantityRatio]['VALUE']);

            CStartShopEvents::Call('OnToolsIBlockQuantityRatioValueGet', array(
                'ITEM' => $arItem,
                'VALUE' => &$fValue
            ));

            return $fValue;
        }

        public static function UpdateProperties($iIBlockID, $sLanguageID = LANGUAGE_ID)
        {
            if (!CModule::IncludeModule('iblock'))
                return false;

            $iIBlockID = intval($iIBlockID);

            if (CStartShopCatalog::IsValid($iIBlockID)) {
                $iSort = 1;

                $arIBlockProperties = array();
                $dbIBlockProperties = CIBlockProperty::GetList(array(), array('IBLOCK_ID' => $iIBlockID));

                while ($arIBlockProperty = $dbIBlockProperties->Fetch())
                    $arIBlockProperties[$arIBlockProperty['CODE']] = $arIBlockProperty;

                /* †оличество */
                $arQuantityProperty = $arIBlockProperties[static::$PrefixQuantity];
                $arQuantityPropertyFields = array(
                    "NAME" => GetMessage('cstartshoptoolsiblock.properties.quantity'),
                    "SORT" => $iSort++,
                    "ACTIVE" => CStartShopCatalog::IsUseQuantity($iIBlockID) ? 'Y' : 'N',
                    "PROPERTY_TYPE" => "N",
                    "USER_TYPE" => "",
                    "IS_REQUIRED" => "Y",
                    "MULTIPLE" => "N",
                    "DEFAULT_VALUE" => 0,
                    "SMART_FILTER" => "N"
                );

                if (!empty($arQuantityProperty)) {
                    $oIBlockProperty = new CIBlockProperty();
                    $oIBlockProperty->Update($arQuantityProperty['ID'], $arQuantityPropertyFields);
                } else {
                    $oIBlockProperty = new CIBlockProperty();
                    $arQuantityPropertyFields['IBLOCK_ID'] = $iIBlockID;
                    $arQuantityPropertyFields['CODE'] = static::$PrefixQuantity;
                    $oIBlockProperty->Add($arQuantityPropertyFields);
                }

                /* °аг количества */

                $arQuantityRatioProperty = $arIBlockProperties[static::$PrefixQuantityRatio];
                $arQuantityRatioPropertyFields = array(
                    "NAME" => GetMessage('cstartshoptoolsiblock.properties.quantity_ratio'),
                    "SORT" => $iSort++,
                    "ACTIVE" => 'Y',
                    "PROPERTY_TYPE" => "N",
                    "USER_TYPE" => "",
                    "IS_REQUIRED" => "Y",
                    "MULTIPLE" => "N",
                    "DEFAULT_VALUE" => 1,
                    "SMART_FILTER" => "N"
                );

                if (!empty($arQuantityRatioProperty)) {
                    $oIBlockProperty = new CIBlockProperty();
                    $oIBlockProperty->Update($arQuantityRatioProperty['ID'], $arQuantityRatioPropertyFields);
                } else {
                    $oIBlockProperty = new CIBlockProperty();
                    $arQuantityRatioPropertyFields['IBLOCK_ID'] = $iIBlockID;
                    $arQuantityRatioPropertyFields['CODE'] = static::$PrefixQuantityRatio;
                    $oIBlockProperty->Add($arQuantityRatioPropertyFields);
                }

                /* Уипы цен */
                $arPricesTypes = array();
                $dbPricesTypes = CStartShopPrice::GetList(array('SORT' => 'ASC'));

                while ($arPriceType = $dbPricesTypes->Fetch())
                    $arPricesTypes[] = $arPriceType;

                /* ђалюта */
                $arCurrencies = array();
                $dbCurrencies = CStartShopCurrency::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'));

                while ($arCurrency = $dbCurrencies->Fetch())
                    $arCurrencies[] = $arCurrency;

                $arPricesProperties = array();
                $arCurrenciesProperties = array();

                /* Уипы цен и валюта */
                foreach ($arPricesTypes as $arPriceType) {
                    /* ђалюта цены */
                    $sCurrencyPropertyCode = static::$PrefixCurrency.'_'.$arPriceType['ID'];
                    $arCurrencyProperty = $arIBlockProperties[$sCurrencyPropertyCode];

                    $arCurrencyPropertyFields = array(
                        "NAME" => GetMessage('cstartshoptoolsiblock.properties.currency', array('#PRICE_TYPE#' => $arPriceType['LANG'][$sLanguageID]['NAME'])),
                        "SORT" => $iSort++,
                        "ACTIVE" => !empty($arCurrencies) ? 'Y' : 'N',
                        "PROPERTY_TYPE" => "L",
                        "LIST_TYPE" => "L",
                        "USER_TYPE" => "",
                        "IS_REQUIRED" => "N",
                        "MULTIPLE" => "N",
                        "DEFAULT_VALUE" => 0,
                        "SMART_FILTER" => "N"
                    );

                    if (!empty($arCurrencyProperty)) {
                        $oIBlockProperty = new CIBlockProperty();
                        $oIBlockProperty->Update($arCurrencyProperty['ID'], $arCurrencyPropertyFields);
                        $iCurrencyPropertyID = $arCurrencyProperty['ID'];
                    } else {
                        $oIBlockProperty = new CIBlockProperty();
                        $arCurrencyPropertyFields['IBLOCK_ID'] = $iIBlockID;
                        $arCurrencyPropertyFields['CODE'] = $sCurrencyPropertyCode;
                        $iCurrencyPropertyID = $oIBlockProperty->Add($arCurrencyPropertyFields);
                    }

                    $arCurrenciesProperties[] = $sCurrencyPropertyCode;

                    if (!empty($iCurrencyPropertyID)) {
                        $arCurrenciesPropertyEnumsAdded = array();
                        $arCurrenciesPropertyEnums = array();
                        $dbCurrenciesPropertyEnums = CIBlockPropertyEnum::GetList(array(), array('PROPERTY_ID' => $iCurrencyPropertyID));

                        while ($arCurrenciesPropertyEnum = $dbCurrenciesPropertyEnums->Fetch())
                            $arCurrenciesPropertyEnums[$arCurrenciesPropertyEnum['XML_ID']] = $arCurrenciesPropertyEnum;

                        foreach ($arCurrencies as $arCurrency) {
                            $arCurrencyPropertyEnum = $arCurrenciesPropertyEnums[$arCurrency['CODE']];
                            $oPropertyEnum = new CIBlockPropertyEnum();
                            $arCurrencyPropertyEnumFields = array(
                                "VALUE" => '['.$arCurrency['CODE'].'] '.$arCurrency['LANG'][$sLanguageID]['NAME'],
                                "DEF" => $arCurrency['BASE'],
                                "SORT" => $arCurrency['SORT']
                            );

                            if (!empty($arCurrencyPropertyEnum)) {
                                $oPropertyEnum->Update($arCurrencyPropertyEnum['ID'], $arCurrencyPropertyEnumFields);
                            } else {
                                $arCurrencyPropertyEnumFields['PROPERTY_ID'] = $iCurrencyPropertyID;
                                $arCurrencyPropertyEnumFields['XML_ID'] = $arCurrency['CODE'];
                                $oPropertyEnum->Add($arCurrencyPropertyEnumFields);
                            }

                            $arCurrenciesPropertyEnumsAdded[] = $arCurrency['CODE'];
                        }

                        foreach ($arCurrenciesPropertyEnums as $arCurrenciesPropertyEnum)
                            if (!in_array($arCurrenciesPropertyEnum['XML_ID'], $arCurrenciesPropertyEnumsAdded))
                                CIBlockPropertyEnum::Delete($arCurrenciesPropertyEnum['ID']);
                    }

                    /* Уип цены */
                    $sPricePropertyCode = static::$PrefixPrice.'_'.$arPriceType['ID'];
                    $arPriceProperty = $arIBlockProperties[$sPricePropertyCode];
                    $arPricePropertyFields = array(
                        "NAME" => $arPriceType['LANG'][LANGUAGE_ID]['NAME'],
                        "SORT" => $iSort++,
                        "ACTIVE" => $arPriceType['ACTIVE'],
                        "PROPERTY_TYPE" => "N",
                        "USER_TYPE" => "",
                        "IS_REQUIRED" => "N",
                        "MULTIPLE" => "N",
                        "DEFAULT_VALUE" => 0,
                        "SMART_FILTER" => "Y",
                        "DISPLAY_TYPE" => "A"
                    );

                    if (!empty($arPriceProperty)) {
                        $oIBlockProperty = new CIBlockProperty();
                        $oIBlockProperty->Update($arPriceProperty['ID'], $arPricePropertyFields);
                    } else {
                        $oIBlockProperty = new CIBlockProperty();
                        $arPricePropertyFields['IBLOCK_ID'] = $iIBlockID;
                        $arPricePropertyFields['CODE'] = $sPricePropertyCode;
                        $oIBlockProperty->Add($arPricePropertyFields);
                    }

                    $arPricesProperties[] = $sPricePropertyCode;
                }

                foreach ($arIBlockProperties as $arIBlockProperty) {
                    if (preg_match('/^('.preg_quote(static::$PrefixPrice).')/', $arIBlockProperty['CODE']))
                        if (!in_array($arIBlockProperty['CODE'], $arPricesProperties))
                            CIBlockProperty::Delete($arIBlockProperty['ID']);

                    if (preg_match('/^('.preg_quote(static::$PrefixCurrency).')/', $arIBlockProperty['CODE']))
                        if (!in_array($arIBlockProperty['CODE'], $arCurrenciesProperties))
                            CIBlockProperty::Delete($arIBlockProperty['ID']);
                }

                CStartShopEvents::Call('OnToolsIBlockUpdateProperties', array(
                    'IBLOCK' => $iIBlockID,
                    'LANGUAGE_ID' => $sLanguageID
                ));
            }
        }

        public static function UpdatePropertiesAll() {
            $dbCatalogs = CStartShopCatalog::GetList();

            while ($arCatalog = $dbCatalogs->Fetch()) {
                static::UpdateProperties($arCatalog['IBLOCK']);
                static::UpdateProperties($arCatalog['OFFERS_IBLOCK']);
            }

            CStartShopEvents::Call('OnToolsIBlockUpdatePropertiesAll');
        }

        public static function GetOffersMinPrice ($arItem) {
            $arMinPrice = array();
			$arMinPriceTmp = 0;
			
			$arBaseCurrency = CStartShopCurrency::GetBase()->Fetch();
            $codeBaseCurrency = $arBaseCurrency['CODE'];
			
			if (!empty($arItem['STARTSHOP']['OFFERS'])) {
                foreach ($arItem['STARTSHOP']['OFFERS'] as $arOffer) {
					$priceBaseCurrency = CStartShopCurrency::Convert($arOffer['STARTSHOP']['PRICES']['MINIMAL']['VALUE'],
												$arOffer['STARTSHOP']['PRICES']['MINIMAL']['CURRENCY'],
												$codeBaseCurrency
					);
					
                    if (empty($arMinPriceTmp) || $priceBaseCurrency < $arMinPriceTmp) {
                        $arMinPriceTmp = $priceBaseCurrency;
						$arMinPrice = $arOffer['STARTSHOP']['PRICES']['MINIMAL'];
					}
				}
			}
            return $arMinPrice;
        }

        public static function GetOffersProperties($arCatalogs) {
            if (!is_array($arCatalogs) || empty($arCatalogs))
                return CStartShopUtil::ArrayToDBResult(array());

            $arOffersProperties = array();

            foreach ($arCatalogs as $arCatalog)
                if (!empty($arCatalog['OFFERS_IBLOCK']) && !empty($arCatalog['OFFERS_LINK_PROPERTY']) && is_array($arCatalog['OFFERS_PROPERTIES']))
                    foreach ($arCatalog['OFFERS_PROPERTIES'] as $iOffersPropertyID) {
                        $arProperty = CIBlockProperty::GetByID($iOffersPropertyID)->Fetch();

                        if (!empty($arProperty)) {
                            $arOffersProperty = array();

                            if ($arProperty['PROPERTY_TYPE'] == "L" && $arProperty['USER_TYPE'] == null && $arProperty['MULTIPLE'] == "N") {
                                $arOffersProperty = array(
                                    "ID" => $arProperty['ID'],
                                    "CODE" => $arProperty["CODE"],
                                    "NAME" => $arProperty['NAME'],
                                    "TYPE" => "TEXT",
                                    "VALUES" => array()
                                );

                                $arPropertyValues = CStartShopUtil::DBResultToArray(CIBlockPropertyEnum::GetList(array(), array("PROPERTY_ID" => $arProperty['ID'])));

                                foreach ($arPropertyValues as $arPropertyValue)
                                    if (!empty($arPropertyValue["XML_ID"]))
                                        $arOffersProperty['VALUES'][$arPropertyValue["XML_ID"]] = array(
                                            "ID" => $arPropertyValue["ID"],
                                            "CODE" => $arPropertyValue["XML_ID"],
                                            "TEXT" => $arPropertyValue["VALUE"]
                                        );
                            } else if ($arProperty['PROPERTY_TYPE'] == "S" && $arProperty['USER_TYPE'] == "directory" && $arProperty['MULTIPLE'] == "N" && CModule::IncludeModule("highloadblock")) {
                                $arTable = HLBlock\HighloadBlockTable::getList(array(
                                    "filter" => array(
                                        "TABLE_NAME" => $arProperty['USER_TYPE_SETTINGS']['TABLE_NAME']
                                    )
                                ))->Fetch();

                                if (!empty($arTable)) {
                                    $oTableEntity = HLBlock\HighloadBlockTable::compileEntity($arTable);
                                    $oTableEntityDataClass = $oTableEntity->getDataClass();
                                    $oTableFields = $oTableEntity->getFields();
                                    $arTableFields = array();

                                    foreach ($oTableFields as $oTableField)
                                        $arTableFields[] = $oTableField->getName();

                                    $oQuery = new Entity\Query($oTableEntityDataClass);
                                    $oQuery->setSelect(array('*'));
                                    $dbPropertyValues = $oQuery->exec();
                                    $arPropertyValues = array();

                                    while ($arPropertyValue = $dbPropertyValues->Fetch())
                                       $arPropertyValues[] = $arPropertyValue;

                                    if (in_array('UF_XML_ID', $arTableFields) && in_array('UF_NAME', $arTableFields))
                                        if (in_array('UF_FILE', $arTableFields)) {
                                            $arOffersProperty = array(
                                                "ID" => $arProperty['ID'],
                                                "CODE" => $arProperty["CODE"],
                                                "NAME" => $arProperty['NAME'],
                                                "TYPE" => "PICTURE",
                                                "VALUES" => array()
                                            );

                                            foreach ($arPropertyValues as $arPropertyValue) {
                                                $sFilePath = CFile::GetPath($arPropertyValue["UF_FILE"]);

                                                $arOffersProperty["VALUES"][$arPropertyValue["UF_XML_ID"]] = array(
                                                    "ID" => $arPropertyValue["ID"],
                                                    "CODE" => $arPropertyValue["UF_XML_ID"],
                                                    "TEXT" => $arPropertyValue["UF_NAME"],
                                                    "PICTURE" => (!empty($sFilePath) ? $sFilePath : null)
                                                );
                                            }
                                        } else {
                                            $arOffersProperty = array(
                                                "ID" => $arProperty['ID'],
                                                "CODE" => $arProperty["CODE"],
                                                "NAME" => $arProperty['NAME'],
                                                "TYPE" => "TEXT",
                                                "VALUES" => array()
                                            );

                                            foreach ($arPropertyValues as $arPropertyValue) {
                                                $arOffersProperty["VALUES"][$arPropertyValue["UF_XML_ID"]] = array(
                                                    "ID" => $arPropertyValue["ID"],
                                                    "CODE" => $arPropertyValue["UF_XML_ID"],
                                                    "TEXT" => $arPropertyValue["UF_NAME"]
                                                );
                                            }
                                        }
                                }
                            }

                            if (!empty($arOffersProperty))
                                $arOffersProperties[] = $arOffersProperty;
                        }
                    }

            return CStartShopUtil::ArrayToDBResult($arOffersProperties);
        }

        public static function ConvertToOfferProperty($arProperty, $arOffersProperties, $sLinkField = 'ID')
        {
            $arOfferPropertyValue = array();

            if (!empty($sLinkField)) {
                $arOfferProperty = $arOffersProperties[$arProperty[$sLinkField]];

                if (!empty($arOfferProperty))
                    if ($arProperty['PROPERTY_TYPE'] == "L" && $arProperty['USER_TYPE'] == null) {
                        $arOfferPropertyValue = $arOffersProperties[$arProperty[$sLinkField]];
                        $arOfferPropertyValue['VALUE'] = $arOfferPropertyValue['VALUES'][$arProperty['VALUE_XML_ID']];
                        unset($arOfferPropertyValue['VALUES']);
                    } else if ($arProperty['PROPERTY_TYPE'] == "S" && $arProperty['USER_TYPE'] == "directory") {
                        $arOfferPropertyValue = $arOffersProperties[$arProperty[$sLinkField]];
                        $arOfferPropertyValue['VALUE'] = $arOfferPropertyValue['VALUES'][$arProperty['VALUE']];
                        unset($arOfferPropertyValue['VALUES']);
                    }
            }

            if (empty($arOfferPropertyValue['VALUE']))
                $arOfferPropertyValue = array();

            return $arOfferPropertyValue;
        }

        public static function GetOffersJSStructure ($arItem) {
            $arStructure = array();
            $arStructure['PROPERTIES'] = array();
            $arStructure['OFFERS'] = array();

            foreach ($arItem['STARTSHOP']['OFFER']['PROPERTIES'] as $arProperty) {
                $arStructure['PROPERTIES'][] = $arProperty;
            }

            foreach ($arItem['STARTSHOP']['OFFERS'] as $arOffer) {
                $arScriptOffer = array_merge(array("ID" => $arOffer['ID']), $arOffer['STARTSHOP']);
                $arScriptOffer['DATA'] = $arOffer;
                unset($arScriptOffer['DATA']['STARTSHOP']);
                $arStructure['OFFERS'][$arOffer['ID']] = $arScriptOffer;
                unset($arScriptOffer);
            }

            return $arStructure;
        }

        public static function CheckOffersLink($iIBlockID, $bCreateIfNotExists = false) {
            $arCatalog = CStartShopCatalog::GetByIBlock(intval($iIBlockID))->Fetch();

            if (empty($arCatalog))
                $arCatalog = CStartShopCatalog::GetByOffersIBlock(intval($iIBlockID))->Fetch();

            if (!empty($arCatalog)) {
                $bCreate = false;

                if (!empty($arCatalog["OFFERS_IBLOCK"]))
                    if (CIBlock::GetByID($arCatalog["OFFERS_IBLOCK"])->Fetch())
                        if (!empty($arCatalog["OFFERS_LINK_PROPERTY"])) {
                            $arLinkProperty = CIBlockProperty::GetByID($arCatalog["OFFERS_LINK_PROPERTY"])->Fetch();

                            if (!empty($arLinkProperty)) {
                                if ($arLinkProperty["PROPERTY_TYPE"] == "E" && empty($arLinkProperty["USER_TYPE"]) && $arLinkProperty["MULTIPLE"] == "N" && $arLinkProperty["ACTIVE"] == "Y") {
                                    return $arLinkProperty;
                                } else {
                                    $oIBlockProperty = new CIBlockElement();
                                    $oIBlockProperty->Update($arLinkProperty['ID'], array(
                                        "PROPERTY_TYPE" => "E",
                                        "USER_TYPE" => false,
                                        "ACTIVE" => "Y"
                                    ));

                                    if (empty($oIBlockProperty->LAST_ERROR)) {
                                        unset($oIBlockProperty);
                                        return $arLinkProperty;
                                    }

                                    $bCreate = true;
                                    unset($oIBlockProperty);
                                }
                            } else {
                                $bCreate = true;
                            }
                        } else {
                            $bCreate = true;
                        }

                if ($bCreate && $bCreateIfNotExists) {
                    $oIBlockProperty = new CIBlockProperty();
                    $iIBlockPropertyID = $oIBlockProperty->Add(array(
                        "IBLOCK_ID" => $arCatalog["OFFERS_IBLOCK"],
                        "NAME" => GetMessage("cstartshoptoolsiblock.properties.offers_link_property"),
                        "CODE" => static::$PrefixOfferLink,
                        "PROPERTY_TYPE" => "E",
                        "USER_TYPE" => false,
                        "MULTIPLE" => "N",
                        "ACTIVE" => "Y"
                    ));

                    if (!empty($iIBlockPropertyID) && empty($oIBlockProperty->LAST_ERROR)) {
                        CStartShopCatalog::Update($arCatalog['IBLOCK'], array('OFFERS_LINK_PROPERTY' => $iIBlockPropertyID));
                        unset($oIBlockProperty);
                        return CIBlockElement::GetByID($iIBlockPropertyID)->Fetch();
                    }

                    unset($oIBlockProperty);
                }
            }

            return null;
        }

        public static function CheckUserFieldExternalId() {
            $oUserTypeEntity = new CUserTypeEntity();
            $arUserField = $oUserTypeEntity->GetList(array(), array(
                "ENTITY_ID" => "USER",
                "USER_TYPE_ID" => "string",
                "FIELD_NAME" => "UF_".static::$PrefixUserExternalId
            ))->Fetch();

            if (!empty($arUserField)) {
                if ($arUserField["USER_TYPE_ID"] == "string" && $arUserField["MULTIPLE"] == "N") {
                    return $arUserField;
                } else {
                    $oUserTypeEntity->Delete($arUserField["ID"]);
                }
            }

            $iUserFieldId = $oUserTypeEntity->Add(array(
                "ENTITY_ID" =>      "USER",
                "FIELD_NAME" =>     "UF_".static::$PrefixUserExternalId,
                "USER_TYPE_ID" =>   "string",
                "MULTIPLE" =>       "N",
                "MANDATORY" =>      "N",
                "SHOW_FILTER" =>    "N",
                "IS_SEARCHABLE" =>  "N",
                "SHOW_IN_LIST" =>   "",
                "EDIT_IN_LIST" =>   "",
                "XML_ID" =>         static::$PrefixUserExternalId,
                "SETTINGS" =>       array(
                    'DEFAULT_VALUE' => '',
                    'SIZE'          => '20',
                    'ROWS'          => '1',
                    'MIN_LENGTH'    => '0',
                    'MAX_LENGTH'    => '0',
                    'REGEXP'        => '',
                )
            ));

            if (!empty($iUserFieldId))
                return $oUserTypeEntity->GetByID($iUserFieldId);

            return false;
        }

        public static function SetOffersLinkByOffersIBlock($iIBlockID, $iItemID, $iLinkToItemID) {
            $arCatalog = CStartShopCatalog::GetByOffersIBlock(intval($iIBlockID))->Fetch();

            if (!empty($arCatalog) && is_numeric($iItemID) && !empty($iItemID)) {
                if (!empty($arCatalog['OFFERS_IBLOCK']) && !empty($arCatalog['OFFERS_LINK_PROPERTY']))
                    CIBlockElement::SetPropertyValuesEx(
                        $iItemID,
                        $arCatalog['OFFERS_IBLOCK'],
                        array(
                            $arCatalog['OFFERS_LINK_PROPERTY'] => intval($iLinkToItemID)
                        )
                    );
            }
        }

        public static function SetOffersLinkByIBlock($iIBlockID, $iItemID, $iLinkToItemID) {
            $arCatalog = CStartShopCatalog::GetByIBlock(intval($iIBlockID))->Fetch();

            if (!empty($arCatalog) && is_numeric($iItemID) && !empty($iItemID)) {
                if (!empty($arCatalog['OFFERS_IBLOCK']) && !empty($arCatalog['OFFERS_LINK_PROPERTY']))
                    CIBlockElement::SetPropertyValuesEx(
                        $iItemID,
                        $arCatalog['OFFERS_IBLOCK'],
                        array(
                            $arCatalog['OFFERS_LINK_PROPERTY'] => intval($iLinkToItemID)
                        )
                    );
            }
        }

        public static function SetQuantity($iItemID, $fQuantity) {
            $arItem = CIBlockElement::GetByID($iItemID)->Fetch();
            $fQuantity = floatval($fQuantity);

            if (!empty($arItem))
                CIBlockElement::SetPropertyValuesEx(
                    $arItem['ID'],
                    $arItem['IBLOCK_ID'],
                    array(
                        static::$PrefixQuantity => $fQuantity
                    )
                );
        }

        public static function SetQuantityRatio($iItemID, $fQuantityRatio) {
            $arItem = CIBlockElement::GetByID($iItemID)->Fetch();
            $fQuantityRatio = floatval($fQuantityRatio);

            if (!empty($arItem))
                CIBlockElement::SetPropertyValuesEx(
                    $arItem['ID'],
                    $arItem['IBLOCK_ID'],
                    array(
                        static::$PrefixQuantityRatio => $fQuantityRatio
                    )
                );
        }

        public static function SetPriceCurrency($iItemID, $sPriceType, $sCurrency) {
            static::SetPrice($iItemID, array($sPriceType), false, $sCurrency);
        }

        public static function SetPriceValue($iItemID, $sPriceType, $fPrice) {
            static::SetPrice($iItemID, array($sPriceType), $fPrice, false);
        }

        public static function SetPrice($iItemID, $sPriceType, $fPrice, $sCurrency) {
            static::SetPrices($iItemID, array($sPriceType), $fPrice, $sCurrency);
        }

        public static function SetPrices($iItemID, $arPricesTypes = null, $fPrice = false, $sCurrency = false) {
            $arItem = CIBlockElement::GetByID($iItemID)->Fetch();
            $arCurrency = array();

            if (!empty($sCurrency) && $sCurrency !== null) {
                $arCurrency = CStartShopCurrency::GetByCode($sCurrency)->Fetch();
            } else if ($sCurrency !== false && $sCurrency !== null) {
                $arCurrency = CStartShopCurrency::GetBase()->Fetch();
            }

            if (is_array($arPricesTypes) && !empty($arPricesTypes)) {
                $arPricesTypes = CStartShopUtil::DBResultToArray(CStartShopPrice::GetList(array(), array('CODE' => $arPricesTypes)));
            } else {
                $arPricesTypes = CStartShopUtil::DBResultToArray(CStartShopPrice::GetList());
            }

            $arProperties = CStartShopUtil::DBResultToArray(CIBlockProperty::GetList(array(), array(
                'IBLOCK_ID' => $arItem['IBLOCK_ID']
            )), 'CODE');

            $arUpdateFields = array();

            if (!empty($arItem) && !empty($arPricesTypes))
                foreach ($arPricesTypes as $arPriceType) {
                    $sPricePropertyCode = static::$PrefixPrice . '_' . $arPriceType['ID'];
                    $sCurrencyPropertyCode = static::$PrefixCurrency . '_' . $arPriceType['ID'];

                    if (is_numeric($fPrice)) {
                        $arUpdateFields[$sPricePropertyCode] = floatval($fPrice);
                    } else if ($fPrice === null) {
                        $arUpdateFields[$sPricePropertyCode] = "";
                    }

                    if (!empty($arCurrency)) {
                        $arProperty = $arProperties[$sCurrencyPropertyCode];

                        if (!empty($arProperty)) {
                            $arPropertyEnum = CIBlockPropertyEnum::GetList(array(), array(
                                'PROPERTY_ID' => $arProperty['ID'],
                                'XML_ID' => $arCurrency['CODE']
                            ))->Fetch();

                            if (!empty($arPropertyEnum))
                                $arUpdateFields[$sCurrencyPropertyCode] = $arPropertyEnum['ID'];
                        }
                    } else if ($sCurrency === null) {
                        $arUpdateFields[$sCurrencyPropertyCode] = "";
                    }
                }

            if (!empty($arUpdateFields))
                CIBlockElement::SetPropertyValuesEx(
                    $arItem['ID'],
                    $arItem['IBLOCK_ID'],
                    $arUpdateFields
                );
        }
    }
?>