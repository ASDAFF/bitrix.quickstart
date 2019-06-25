<?
    CModule::IncludeModule($this->MODULE_ID);
    CModule::IncludeModule('iblock');

    $arSites = CStartShopUtil::DBResultToArray(CSite::GetList($by = "sort", $order = "asc"), 'ID');
    $arLanguages = CStartShopUtil::DBResultToArray(CLanguage::GetList($by = "sort", $order = "asc"), "ID");

    $arEvents = array(
        array(
            'EVENT_NAME' => 'STARTSHOP_NEW_ORDER',
            'NAME' => GetMessage('events.types.new_order.name'),
            'DESCRIPTION' => GetMessage('events.types.new_order.description'),
            'OPTION' => 'MAIL_CLIENT_ORDER_CREATE_EVENT',
            'TEMPLATES' => array(
                array(
                    'ACTIVE' => 'Y',
                    'EMAIL_FROM' => '#STARTSHOP_SHOP_EMAIL#',
                    'EMAIL_TO' => '#STARTSHOP_CLIENT_EMAIL#',
                    'SUBJECT' => GetMessage('events.types.new_order.template.subject'),
                    'BODY_TYPE' => 'html',
                    'MESSAGE' => GetMessage('events.types.new_order.template.message')
                )
            )
        ),
        array(
            'EVENT_NAME' => 'STARTSHOP_NEW_ORDER_ADMIN',
            'NAME' => GetMessage('events.types.new_order_admin.name'),
            'DESCRIPTION' => GetMessage('events.types.new_order_admin.description'),
            'OPTION' => 'MAIL_ADMIN_ORDER_CREATE_EVENT',
            'TEMPLATES' => array(
                array(
                    'ACTIVE' => 'Y',
                    'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
                    'EMAIL_TO' => '#STARTSHOP_SHOP_EMAIL#',
                    'SUBJECT' => GetMessage('events.types.new_order_admin.template.subject'),
                    'BODY_TYPE' => 'html',
                    'MESSAGE' => GetMessage('events.types.new_order_admin.template.message')
                )
            )
        ),
        array(
            'EVENT_NAME' => 'STARTSHOP_PAY_ORDER_ADMIN',
            'NAME' => GetMessage('events.types.pay_order_admin.name'),
            'DESCRIPTION' => GetMessage('events.types.pay_order_admin.description'),
            'OPTION' => 'MAIL_ADMIN_ORDER_PAY_EVENT',
            'TEMPLATES' => array(
                array(
                    'ACTIVE' => 'Y',
                    'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
                    'EMAIL_TO' => '#STARTSHOP_SHOP_EMAIL#',
                    'SUBJECT' => GetMessage('events.types.pay_order_admin.template.subject'),
                    'BODY_TYPE' => 'html',
                    'MESSAGE' => GetMessage('events.types.pay_order_admin.template.message')
                )
            )
        )
    );

    $obEventType = new CEventType();
	$obEventTemp = new CEventMessage();


    foreach ($arEvents as $arEvent) {
        $dbEventType = $obEventType->GetList(array(
                'EVENT_NAME' => $arEvent['EVENT_NAME'])
        );

        if ($dbEventType->SelectedRowsCount() == 0) {
            $obEventType->Add(array(
                'LID' => 'ru',
                'EVENT_NAME' => $arEvent['EVENT_NAME'],
                'NAME' => $arEvent['NAME'],
                'DESCRIPTION' => $arEvent['DESCRIPTION']
            ));

            if (!empty($arEvent['OPTION']))
                foreach ($arSites as $arSite)
                    CStartShopVariables::Set($arEvent['OPTION'], $arEvent['EVENT_NAME'], $arSite['ID']);

            if (is_array($arEvent['TEMPLATES']))
                foreach ($arEvent['TEMPLATES'] as $arTemplate) {
                    $arTemplate['LID'] = array_keys($arSites);
                    $arTemplate['EVENT_NAME'] = $arEvent['EVENT_NAME'];
					$obEventTemp->Add($arTemplate);
                }
        }
    }

    /* Добавление стандартных каталогов */
    CModule::IncludeModule($this->MODULE_ID);

    /* Применение пользовательских параметров */

    foreach ($arSites as $arSite)
    {
        $arSections = array();
        $arSectionsInstall = $_REQUEST['startshopInstallSections_'.$arSite['ID']];
        $arSectionsIBlocks = $_REQUEST['startshopInstallSectionsIBlocks_'.$arSite['ID']];
        $arSectionsPaths = $_REQUEST['startshopInstallSectionsPaths_'.$arSite['ID']];

        $arMacros = array(
            "CATALOG_IBLOCK_ID" => "",
            "CATALOG_IBLOCK_TYPE" => "",
            "CATALOG_PATH" => "",
            "CART_PATH" => "",
            "PERSONAL_PATH" => "",
            "SITE_DIR" => $arSite['DIR']
        );

        if (!is_array($arSections))
            $arSections = array();

        if ($arSectionsInstall['CATALOG'] == 'Y' && !empty($arSectionsPaths['CATALOG']) && $arIBlock = CIBlock::GetByID(intval($arSectionsIBlocks['CATALOG']))->Fetch()) {
            $sDirectory = $_SERVER['DOCUMENT_ROOT'].'/'.$arSite['DIR'].'/'.$arSectionsPaths['CATALOG'];
            $sDirectory = preg_replace('/\/{2,}/', '/', $sDirectory);

            $arSections['CATALOG'] = array();
            $arSections['CATALOG']['DIRECTORY'] = array(
                "FROM" => "#DOCUMENT_ROOT#/bitrix/modules/intec.startshop/install/public/#ENCODING#/catalog/",
                "TO" => $sDirectory
            );

            $arMacros["CATALOG_IBLOCK_ID"] = $arIBlock['ID'];
            $arMacros["CATALOG_IBLOCK_TYPE"] = $arIBlock['TYPE'];
            $arMacros["CATALOG_PATH"] = preg_replace('/\/{2,}/', '/', '/'.$arSite['DIR'].'/'.$arSectionsPaths['CATALOG'].'/');

            CStartShopCatalog::Add(array(
                "IBLOCK" => $arIBlock['ID'],
                "USE_QUANTITY" => 1
            ));
        }

        /* Создание раздела корзины */

        if ($arSectionsInstall['CART'] == 'Y' && !empty($arSectionsPaths['CART'])) {
            $sDirectory = $_SERVER['DOCUMENT_ROOT'].'/'.$arSite['DIR'].'/'.$arSectionsPaths['CART'];
            $sDirectory = preg_replace('/\/{2,}/', '/', $sDirectory);

            $arSections['CART'] = array();
            $arSections['CART']['DIRECTORY'] = array(
                "FROM" => "#DOCUMENT_ROOT#/bitrix/modules/intec.startshop/install/public/#ENCODING#/cart/",
                "TO" => $sDirectory
            );

            $arMacros["CART_PATH"] = preg_replace('/\/{2,}/', '/', '/'.$arSite['DIR'].'/'.$arSectionsPaths['CART'].'/');
        }

        /* Создание раздела заказа */

        if ($arSectionsInstall['PERSONAL'] == 'Y' && !empty($arSectionsPaths['PERSONAL'])) {
            $sDirectory = $_SERVER['DOCUMENT_ROOT'].'/'.$arSite['DIR'].'/'.$arSectionsPaths['PERSONAL'];
            $sDirectory = preg_replace('/\/{2,}/', '/', $sDirectory);

            $arSections['PERSONAL'] = array();
            $arSections['PERSONAL']['DIRECTORY'] = array(
                "FROM" => "#DOCUMENT_ROOT#/bitrix/modules/intec.startshop/install/public/#ENCODING#/personal/",
                "TO" => $sDirectory
            );

            $arMacros["PERSONAL_PATH"] = preg_replace('/\/{2,}/', '/', '/'.$arSite['DIR'].'/'.$arSectionsPaths['PERSONAL'].'/');
        }

        foreach ($arSections as $arSection) {
            if (!is_dir($arSection['DIRECTORY']['TO']))
                mkdir($arSection['DIRECTORY']['TO']);

            CopyDirFiles(
                CStartShopUtil::ReplaceMacros(
                    $arSection['DIRECTORY']['FROM'],
                    array(
                        "DOCUMENT_ROOT" => $_SERVER['DOCUMENT_ROOT'],
                        "ENCODING" => SITE_CHARSET == "windows-1251" ? "windows-1251" : "utf-8"
                    )
                ),
                $arSection['DIRECTORY']['TO'],
                true,
                true,
                false
            );

            CStartShopUtil::ReplaceMacrosInDir($arSection['DIRECTORY']['TO'], $arMacros);
        }
    }
?>