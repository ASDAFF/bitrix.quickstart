<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");?>
<?
    global $USER;

    if (!CModule::IncludeModule("iblock"))
        return;

    if (!CModule::IncludeModule('intec.startshop'))
        return;

    $sType = strval($_REQUEST['type']);
    $sMode = strval($_REQUEST['mode']);

    $oData = null;

    $sUploadDirectoryAbsolute = $_SERVER["DOCUMENT_ROOT"]."/upload/startshop";
    $sUploadDirectoryRelative = "/upload/startshop";

    if (!empty($sType) && !empty($sMode) && CStartShopVariables::Get("1C_EXCHANGE_ALLOW") == "Y") {
        if ($sType == "catalog") {
            if ($sMode == "checkauth") {
                DeleteDirFilesEx($sUploadDirectoryRelative);
                CStartShopExchange1CResponse::Authorize(
                    "STARTSHOP",
                    "EXCHANGE"
                );
            } else if ($sMode == "init") {
                CStartShopExchange1CResponse::Initialize(
                    intval(CStartShopVariables::Get("1C_EXCHANGE_FILE_SIZE")),
                    false
                );
            } else if ($sMode == "file") {
                $sFileName = $_GET['filename'];
                $sFileData = file_get_contents('php://input');
                $sFilePath = $sUploadDirectoryAbsolute.'/files/'.$sFileName;
                $sFileDirectory = dirname($sFilePath);

                if (!empty($sFileName) && !empty($sFileData)) {
                    if (!is_dir($sFileDirectory))
                        mkdir($sFileDirectory, 0777, true);

                    if (is_dir($sFileDirectory)) {
                        file_put_contents($sFilePath, $sFileData, FILE_APPEND);

                        if (is_file($sFilePath))
                            CStartShopExchange1CResponse::Success();

                        CStartShopExchange1CResponse::Failure("File: Error saving file!");
                    } else {
                        CStartShopExchange1CResponse::Failure("File: Error create directory!");
                    }
                }

                CStartShopExchange1CResponse::Failure("File: Bad parameters!");
            } else if ($sMode == "import") {
                $sFileName = $_GET['filename'];
                $sFilePath = $sUploadDirectoryAbsolute.'/files/'.$sFileName;
                $sFileDirectory = dirname($sFilePath);
                $sFileData = file_get_contents($sFilePath);

                $oData = new CStartShopUtilsDataFile($sUploadDirectoryAbsolute."/data.txt");
                $oData->Load();

                if (is_file($sFilePath) && !empty($sFileData)) {
                    $oXml = new CDataXML();
                    $oXml->LoadString($sFileData);

                    $eType = CStartShopExchange1CCatalog::GetType($oXml);

                    if ($eType == STARTSHOP_EXCHANGE_1C_CATALOG_TYPE_PRODUCTS) {
                        if (empty($oData->{"STAGE"})) {
                            $oData->{"STAGE"} = "IBLOCKS";
                        }

                        if ($oData->{"STAGE"} == "IBLOCKS") {
                            $arIBlocks = CStartShopExchange1CCatalog::ImportIBlocks(
                                $oXml,
                                CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_TYPE"),
                                CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_SYNCHRONIZE_NAME") == "Y"
                            );

                            if (!empty($arIBlocks)) {
                                $oData->{"STAGE"} = "SECTIONS";
                                $oData->{"IBLOCKS"} = $arIBlocks;
                                CStartShopExchange1CResponse::Progress();
                            } else {
                                CStartShopExchange1CResponse::Failure("Import: Error on step \"IBLOCKS\"");
                            }
                        } else if ($oData->{"STAGE"} == "SECTIONS") {
                            $arIBlocks = $oData->{"IBLOCKS"};

                            if (!empty($arIBlocks)) {
                                $arSections = CStartShopExchange1CCatalog::ImportSections(
                                    $oXml,
                                    $arIBlocks["CATALOG"],
                                    intval(CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_SECTION_ACTION")),
                                    CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_SECTION_SYNCHRONIZE_NAME") == "Y"
                                );

                                $oData->{"STAGE"} = "PROPERTIES";
                                $oData->{"IBLOCK_SECTIONS"} = $arSections;
                                CStartShopExchange1CResponse::Progress();
                            } else {
                                CStartShopExchange1CResponse::Failure("Import: Error on step \"SECTIONS\"");
                            }
                        } else if ($oData->{"STAGE"} == "PROPERTIES") {
                            $arIBlocks = $oData->{"IBLOCKS"};
                            $arIBlockSections = $oData->{"IBLOCK_SECTIONS"};

                            if (!empty($arIBlocks)) {
                                $arProperties = CStartShopExchange1CCatalog::ImportProperties(
                                    $oXml,
                                    $arIBlocks["CATALOG"],
                                    intval(CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_PROPERTY_ACTION")),
                                    CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_PROPERTY_SYNCHRONIZE_NAME") == "Y",
                                    array(
                                        '^'.preg_quote(CStartShopToolsIBlock::GetArticlePrefix()).'$',
                                        '^'.preg_quote(CStartShopToolsIBlock::GetCurrencyPrefix()).'.*',
                                        '^'.preg_quote(CStartShopToolsIBlock::GetPicturesPrefix()).'$',
                                        '^'.preg_quote(CStartShopToolsIBlock::GetPricePrefix()).'.*',
                                        '^'.preg_quote(CStartShopToolsIBlock::GetQuantityPrefix()).'$',
                                        '^'.preg_quote(CStartShopToolsIBlock::GetQuantityRatioPrefix()).'$'
                                    ),
                                    CStartShopToolsIBlock::GetArticlePrefix(),
                                    CStartShopToolsIBlock::GetPicturesPrefix(),
                                    CStartShopToolsIBlock::GetTraitsPrefix()
                                );

                                $oData->{"STAGE"} = "ITEMS";
                                $oData->{"IBLOCK_PROPERTIES"} = $arProperties;
                                CStartShopExchange1CResponse::Progress();
                            } else {
                                CStartShopExchange1CResponse::Failure("Import: Error on step \"PROPERTIES\"");
                            }
                        } else if ($oData->{"STAGE"} == "ITEMS") {
                            if (empty($oData->{"CATALOG_ITEMS_COUNT"})) {
                                $oData->{"CATALOG_ITEMS_COUNT"} = CStartShopExchange1CCatalog::GetItemsCount($oXml);
                                $oData->{"CATALOG_ITEMS_CURRENT"} = 0;
                            }

                            $bLastImport = false;
                            $iImportLength = intval(CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_ELEMENT_IMPORT_LENGTH"));

                            if ($iImportLength >= $oData->{"CATALOG_ITEMS_COUNT"} - $oData->{"CATALOG_ITEMS_CURRENT"}) {
                                $iImportLength = $oData->{"CATALOG_ITEMS_COUNT"} - $oData->{"CATALOG_ITEMS_CURRENT"};
                                $bLastImport = true;
                            }

                            $arIBlocks = $oData->{"IBLOCKS"};

                            if (!empty($arIBlocks)) {
                                CStartShopExchange1CCatalog::ImportItems(
                                    $oXml,
                                    $arIBlocks["CATALOG"],
                                    intval(CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_ELEMENT_ACTION")),
                                    CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_ELEMENT_SYNCHRONIZE_NAME") == "Y",
                                    $oData->{"CATALOG_ITEMS_CURRENT"},
                                    $iImportLength,
                                    $sUploadDirectoryAbsolute."/files",
                                    CStartShopToolsIBlock::GetArticlePrefix(),
                                    CStartShopToolsIBlock::GetPicturesPrefix(),
                                    CStartShopToolsIBlock::GetTraitsPrefix()
                                );

                                if (!$bLastImport) {
                                    $oData->{"CATALOG_ITEMS_CURRENT"} = $oData->{"CATALOG_ITEMS_CURRENT"} + $iImportLength;
                                    CStartShopExchange1CResponse::Progress();
                                } else {
                                    $oData->{"STAGE"} = null;
                                    CStartShopExchange1CResponse::Success();
                                }
                            }

                            CStartShopExchange1CResponse::Failure("Import: Error on step \"ITEMS\"");
                        }
                    } else if ($eType == STARTSHOP_EXCHANGE_1C_CATALOG_TYPE_OFFERS) {
                        if (empty($oData->{"STAGE"})) {
                            $oData->{"STAGE"} = "PRICES_TYPES";
                        }

                        if ($oData->{"STAGE"} == "PRICES_TYPES") {
                            $arPricesTypes = CStartShopExchange1CCatalog::ImportPricesTypes($oXml);
                            $oData->{"STAGE"} = "CURRENCIES";
                            $oData->{"PRICES_TYPES"} = $arPricesTypes;
                            CStartShopExchange1CResponse::Progress();
                        } else if ($oData->{"STAGE"} == "CURRENCIES") {
                            $arCurrencies = CStartShopExchange1CCatalog::ImportCurrencies($oXml);
                            $oData->{"STAGE"} = "OFFERS_PROPERTIES";
                            $oData->{"CURRENCIES"} = $arCurrencies;
                            CStartShopExchange1CResponse::Progress();
                        } else if ($oData->{"STAGE"} == "OFFERS_PROPERTIES") {
                            $arIBlocks = $oData->{"IBLOCKS"};

                            if (!empty($arIBlocks)) {
                                $oData->{"STAGE"} = "OFFERS";
                                CStartShopExchange1CCatalog::ImportOffersProperties($oXml, $arIBlocks['CATALOG']);
                                CStartShopExchange1CResponse::Progress();
                            } else {
                                CStartShopExchange1CResponse::Failure("Import: Error on step \"OFFERS_PROPERTIES\"");
                            }
                        } else if ($oData->{"STAGE"} == "OFFERS") {
                            $arIBlocks = $oData->{"IBLOCKS"};

                            if (!empty($arIBlocks)) {
                                if (empty($oData->{"OFFERS_ITEMS_COUNT"})) {
                                    $oData->{"OFFERS_ITEMS_COUNT"} = CStartShopExchange1CCatalog::GetItemsCount($oXml);
                                    $oData->{"OFFERS_ITEMS_CURRENT"} = 0;
                                }

                                $bLastImport = false;
                                $iImportLength = intval(CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_ELEMENT_IMPORT_LENGTH"));

                                if ($iImportLength >= $oData->{"OFFERS_ITEMS_COUNT"} - $oData->{"OFFERS_ITEMS_CURRENT"}) {
                                    $iImportLength = $oData->{"OFFERS_ITEMS_COUNT"} - $oData->{"OFFERS_ITEMS_CURRENT"};
                                    $bLastImport = true;
                                }

                                CStartShopExchange1CCatalog::ImportOffers(
                                    $oXml,
                                    $arIBlocks['CATALOG'],
                                    intval(CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_ELEMENT_ACTION")),
                                    CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_ELEMENT_SYNCHRONIZE_NAME") == "Y",
                                    $oData->{"OFFERS_ITEMS_CURRENT"},
                                    $iImportLength
                                );

                                if ($bLastImport) {
                                    $oData->{"STAGE"} = null;
                                    CStartShopExchange1CResponse::Success();
                                } else {
                                    $oData->{"OFFERS_ITEMS_CURRENT"} = $oData->{"OFFERS_ITEMS_CURRENT"} + $iImportLength;
                                    CStartShopExchange1CResponse::Progress();
                                }
                            } else {
                                CStartShopExchange1CResponse::Failure("Import: Error on step \"OFFERS\"");
                            }
                        }

                        CStartShopExchange1CResponse::Success();
                    }
                }
            }
        } else {
            if ($sMode == "checkauth") {
                DeleteDirFilesEx($sUploadDirectoryRelative);
                CStartShopExchange1CResponse::Authorize(
                    "STARTSHOP",
                    "EXCHANGE"
                );
            } else if ($sMode == "init") {
                CStartShopExchange1CResponse::Initialize(
                    intval(CStartShopVariables::Get("1C_EXCHANGE_FILE_SIZE")),
                    false
                );
            } else if ($sMode == "query") {
                header("Content-Type: text/xml; charset=windows-1251");

                $oXml = new CStartShopXmlDocument("1.0", "windows-1251", true);

                $oXml->SetRoot(
                    CStartShopXmlNode::Create("КоммерческаяИнформация")
                        ->SetAttribute("ВерсияСхемы", "2.0.3")
                        ->SetAttribute("ДатаФормирования", date("Y-m-d", time()))
                );

                $arIBlocks = CStartShopUtil::DBResultToArray(CIBlock::GetList(), "ID");
                $arOrders = CStartShopUtil::DBResultToArray(CStartShopOrder::GetList(), "ID");
                $arUsers = CStartShopUtil::DBResultToArray(CUser::GetList($by = "timestamp_x", $order = "desc"), "ID");
                $arDeliveries = CStartShopUtil::DBResultToArray(CStartShopDelivery::GetList(), "ID");
                $arPayments = CStartShopUtil::DBResultToArray(CStartShopPayment::GetList(), "ID");
                $arOrderStatuses = array();

                $dbOrderStatuses = CStartShopOrderStatus::GetList();

                while ($arOrderStatus = $dbOrderStatuses->Fetch())
                    $arOrderStatuses[$arOrderStatus["SID"]][$arOrderStatus["ID"]] = $arOrderStatus;

                $arProductsID = array();
                $arProducts = array();

                foreach ($arOrders as $arOrder)
                    foreach ($arOrder['ITEMS'] as $arOrderItem)
                    if (!in_array($arOrderItem['ITEM'], $arProductsID))
                        $arProductsID[] = $arOrderItem['ITEM'];

                if (!empty($arProductsID))
                    $arProducts = CStartShopUtil::DBResultToArray(CStartShopCatalogProduct::GetList(array(), array('ID' => $arProductsID)), "ID");

                foreach ($arOrders as $arOrder) {
                    $oXmlNode = CStartShopXmlNode::Create("Документ");

                    $arCurrency = CStartShopCurrency::GetByCode($arOrder['CURRENCY'])->Fetch();
                    $arUser = $arUsers[$arOrder["USER"]];
                    $arDelivery = $arDeliveries[$arOrder['DELIVERY']];
                    $arPayment = $arPayments[$arOrder['PAYMENT']];
                    $arOrderStatus = $arOrderStatuses[$arOrder['SID']][$arOrder['STATUS']];

                    if (empty($arCurrency))
                        $arCurrency = CStartShopCurrency::GetBase();

                    if (empty($arCurrency))
                        CStartShopExchange1CResponse::Failure("Currency settings invalid!");

                    $oXmlNode
                        ->AddChildrenNode(CStartShopXmlNode::Create("Ид")->SetContent($arOrder['ID']))
                        ->AddChildrenNode(CStartShopXmlNode::Create("Номер")->SetContent($arOrder['ID']))
                        ->AddChildrenNode(CStartShopXmlNode::Create("Дата")->SetContent(date("Y-m-d", strtotime($arOrder['DATE_CREATE']))))
                        ->AddChildrenNode(CStartShopXmlNode::Create("ХозОперация")->SetContent("Заказ товара"))
                        ->AddChildrenNode(CStartShopXmlNode::Create("Роль")->SetContent("Продавец"))
                        ->AddChildrenNode(CStartShopXmlNode::Create("Валюта")->SetContent($arCurrency['CODE']))
                        ->AddChildrenNode(CStartShopXmlNode::Create("Курс")->SetContent($arCurrency['RATE'] * $arCurrency['RATING']))
                        ->AddChildrenNode(CStartShopXmlNode::Create("Сумма")->SetContent($arOrder['AMOUNT']));

                    $oXmlNodeAgents = CStartShopXmlNode::Create("Контрагенты");
                    $oXmlNodeAgent = CStartShopXmlNode::Create("Контрагент");
                    $oXmlNodeAgents->AddChildrenNode($oXmlNodeAgent);

                    if (!empty($arUser)) {
                        $oXmlNodeAgent
                            ->AddChildrenNode(CStartShopXmlNode::Create("Ид")->SetContent("Startshop#".$arUser['ID'].'#'.$arUser['LOGIN']))
                            ->AddChildrenNode(CStartShopXmlNode::Create("Наименование")->SetContent($arUser['LOGIN']))
                            ->AddChildrenNode(CStartShopXmlNode::Create("Роль")->SetContent("Покупатель"));

                        $sName = "";

                        if (!empty($arUser['NAME']) || !empty($arUser['LAST_NAME']) || !empty($arUser['SECOND_NAME'])) {
                            $sName = array();

                            if (!empty($arUser['LAST_NAME']))
                                $sName[] = $arUser['LAST_NAME'];

                            if (!empty($arUser['NAME']))
                                $sName[] = $arUser['NAME'];

                            if (!empty($arUser['SECOND_NAME']))
                                $sName[] = $arUser['SECOND_NAME'];

                            $sName = implode(' ', $sName);
                        } else {
                            $sName = $arUser['LOGIN'];
                        }

                        $oXmlNodeAgent->AddChildrenNode(CStartShopXmlNode::Create("ПолноеНаименование")->SetContent($sName));
                        unset($sName);
                    } else {
                        $oXmlNodeAgent
                            ->AddChildrenNode(CStartShopXmlNode::Create("Ид")->SetContent("Startshop#Anonym"))
                            ->AddChildrenNode(CStartShopXmlNode::Create("Наименование")->SetContent("Аноним"))
                            ->AddChildrenNode(CStartShopXmlNode::Create("Роль")->SetContent("Покупатель"))
                            ->AddChildrenNode(CStartShopXmlNode::Create("ПолноеНаименование")->SetContent("Анонимный пользователь"));
                    }

                    if (!empty($arUser['LAST_NAME']))
                        $oXmlNodeAgent->AddChildrenNode(CStartShopXmlNode::Create("Фамилия")->SetContent($arUser['LAST_NAME']));

                    if (!empty($arUser['NAME']))
                        $oXmlNodeAgent->AddChildrenNode(CStartShopXmlNode::Create("Имя")->SetContent($arUser['NAME']));

                    $oXmlNode->AddChildrenNode($oXmlNodeAgents);
                    $oXmlNode->AddChildrenNode(CStartShopXmlNode::Create("Время")->SetContent(date("H:i:s", strtotime($arOrder['DATE_CREATE']))));
                    $oXmlNodeProducts = CStartShopXmlNode::Create("Товары");

                    if (!empty($arDelivery)) {
                        $sDeliveryPrice = number_format(CStartShopCurrency::Convert($arDelivery['PRICE'], null, $arCurrency['CODE']), 2, '.', '');

                        $oXmlNodeProducts->AddChildrenNode(
                            CStartShopXmlNode::Create("Товар")
                                ->AddChildrenNode(CStartShopXmlNode::Create("Ид")->SetContent("Startshop#Delivery#" . $arDelivery['CODE']))
                                ->AddChildrenNode(CStartShopXmlNode::Create("Наименование")->SetContent($arDelivery['LANG'][LANGUAGE_ID]['NAME']))
                                ->AddChildrenNode(
                                    CStartShopXmlNode::Create("БазоваяЕдиница")
                                        ->SetAttribute('Код', '796')
                                        ->SetAttribute('НаименованиеПолное', 'Штука')
                                        ->SetAttribute('МеждународноеСокращение', 'PCE')
                                        ->SetContent('шт')
                                )
                                ->AddChildrenNode(CStartShopXmlNode::Create("ЦенаЗаЕдиницу")->SetContent($sDeliveryPrice))
                                ->AddChildrenNode(CStartShopXmlNode::Create("Количество")->SetContent("1"))
                                ->AddChildrenNode(CStartShopXmlNode::Create("Сумма")->SetContent($sDeliveryPrice))
                                ->AddChildrenNode(
                                    CStartShopXmlNode::Create("ЗначенияРеквизитов")
                                        ->AddChildrenNode(
                                            CStartShopXmlNode::Create("ЗначениеРеквизита")
                                                ->AddChildrenNode(CStartShopXmlNode::Create("Наименование")->SetContent("ВидНоменклатуры"))
                                                ->AddChildrenNode(CStartShopXmlNode::Create("Значение")->SetContent("Услуга"))
                                        )
                                        ->AddChildrenNode(
                                            CStartShopXmlNode::Create("ЗначениеРеквизита")
                                                ->AddChildrenNode(CStartShopXmlNode::Create("Наименование")->SetContent("ТипНоменклатуры"))
                                                ->AddChildrenNode(CStartShopXmlNode::Create("Значение")->SetContent("Услуга"))
                                        )
                                )
                        );
                    }

                    foreach ($arOrder['ITEMS'] as $arOrderItem) {
                        $arProduct = $arProducts[$arOrderItem['ITEM']];

                        if (!empty($arProduct))
                            if (!empty($arProduct['EXTERNAL_ID'])) {
                                $arCatalog = array();
                                $arTreatsProperty = $arProduct['PROPERTIES'][CStartShopToolsIBlock::GetTraitsPrefix()];

                                if ($arProduct['STARTSHOP']['OFFER']['OFFER']) {
                                    $arCatalog = CStartShopCatalog::GetByOffersIBlock($arProduct['IBLOCK_ID'])->Fetch();
                                } else {
                                    $arCatalog = CStartShopCatalog::GetByIBlock($arProduct['IBLOCK_ID'])->Fetch();
                                }

                                if (empty($arCatalog))
                                    continue;

                                $arCatalogIBlock = $arIBlocks[$arCatalog['IBLOCK']];

                                if (empty($arCatalogIBlock) || empty($arCatalogIBlock['EXTERNAL_ID']))
                                    continue;

                                $oXmlNodeProduct = CStartShopXmlNode::Create("Товар");
                                $oXmlNodeProducts->AddChildrenNode($oXmlNodeProduct);

                                $oXmlNodeProduct->AddChildrenNode(CStartShopXmlNode::Create("Ид")->SetContent($arProduct['EXTERNAL_ID']));
                                $oXmlNodeProduct->AddChildrenNode(CStartShopXmlNode::Create("ИдКаталога")->SetContent($arCatalogIBlock['EXTERNAL_ID']));
                                $oXmlNodeProduct->AddChildrenNode(CStartShopXmlNode::Create("Наименование")->SetContent($arProduct['NAME']));
                                $oXmlNodeProduct->AddChildrenNode(
                                    CStartShopXmlNode::Create("БазоваяЕдиница")
                                        ->SetAttribute('Код', '796')
                                        ->SetAttribute('НаименованиеПолное', 'Штука')
                                        ->SetAttribute('МеждународноеСокращение', 'PCE')
                                        ->SetContent('шт')
                                );

                                $oXmlNodeProduct->AddChildrenNode(
                                    CStartShopXmlNode::Create("ЦенаЗаЕдиницу")
                                        ->SetContent(number_format($arOrder['ITEMS'][$arProduct['ID']]['PRICE'], 2, '.', ''))
                                );
                                $oXmlNodeProduct->AddChildrenNode(
                                    CStartShopXmlNode::Create("Количество")
                                        ->SetContent(number_format($arOrder['ITEMS'][$arProduct['ID']]['QUANTITY'], 2, '.', ''))
                                );
                                $oXmlNodeProduct->AddChildrenNode(
                                    CStartShopXmlNode::Create("Сумма")
                                        ->SetContent(number_format($arOrder['ITEMS'][$arProduct['ID']]['PRICE'] * $arOrder['ITEMS'][$arProduct['ID']]['QUANTITY'], 2, '.', ''))
                                );

                                if (!empty($arTreatsProperty)) {
                                    $oXmlNodeTreats = CStartShopXmlNode::Create("ЗначенияРеквизитов");

                                    if (!empty($arTreatsProperty['VALUE']))
                                        foreach ($arTreatsProperty['VALUE'] as $iTreatID => $sTreatValue) {
                                                $sTreatName = $arTreatsProperty['DESCRIPTION'][$iTreatID];

                                                if (!empty($sTreatName) && !empty($sTreatValue))
                                                    $oXmlNodeTreats->AddChildrenNode(
                                                        CStartShopXmlNode::Create("ЗначениеРеквизита")
                                                            ->AddChildrenNode(CStartShopXmlNode::Create("Наименование")->SetContent($sTreatName))
                                                            ->AddChildrenNode(CStartShopXmlNode::Create("Значение")->SetContent($sTreatValue))
                                                    );
                                            }

                                    if ($oXmlNodeTreats->IsHaveChildrenNodes())
                                        $oXmlNodeProduct->AddChildrenNode($oXmlNodeTreats);

                                    unset($oXmlNodeTreats);
                                }
                            }
                    }

                    if ($oXmlNodeProducts->IsHaveChildrenNodes())
                        $oXmlNode->AddChildrenNode($oXmlNodeProducts);

                    unset($oXmlNodeProducts);

                    $oXmlNodeTreats = CStartShopXmlNode::Create("ЗначенияРеквизитов");

                    if (!empty($arPayment))
                        $oXmlNodeTreats->AddChildrenNode(
                            CStartShopXmlNode::Create("ЗначениеРеквезита")
                                ->AddChildrenNode(CStartShopXmlNode::Create("Наименование")->SetContent("Метод оплаты"))
                                ->AddChildrenNode(CStartShopXmlNode::Create("Значение")->SetContent($arPayment['LANG'][LANGUAGE_ID]['NAME']))
                        );

                    $oXmlNodeTreats->AddChildrenNode(
                        CStartShopXmlNode::Create("ЗначениеРеквезита")
                            ->AddChildrenNode(CStartShopXmlNode::Create("Наименование")->SetContent("Заказ оплачен"))
                            ->AddChildrenNode(CStartShopXmlNode::Create("Значение")->SetContent($arOrder['PAYED'] == "Y" ? "true" : "false"))
                    );

                    if (!empty($arOrderStatus))
                        $oXmlNodeTreats->AddChildrenNode(
                            CStartShopXmlNode::Create("ЗначениеРеквезита")
                                ->AddChildrenNode(CStartShopXmlNode::Create("Наименование")->SetContent("Статус заказа"))
                                ->AddChildrenNode(CStartShopXmlNode::Create("Значение")->SetContent($arOrderStatus['LANG'][LANGUAGE_ID]['NAME']))
                        );

                    if ($oXmlNodeTreats->IsHaveChildrenNodes())
                        $oXmlNode->AddChildrenNode($oXmlNodeTreats);

                    unset($oXmlNodeTreats);

                    $oXml->GetRoot()->AddChildrenNode($oXmlNode);
                }

                echo $oXml->GetDocument();
            } else if ($sMode == "success") {
                CStartShopExchange1CResponse::Success();
            } else if ($sMode == "file") {
                CStartShopExchange1CResponse::Success();
            } else {
                CStartShopExchange1CResponse::Failure("Bad request!");
            }
        }
    } else {
        CStartShopExchange1CResponse::Failure("Bad request!");
    }
?>