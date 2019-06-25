<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
	global $USER;
	
	if (!CModule::IncludeModule("iblock"))
		return;

    if (!CModule::IncludeModule("intec.startshop"))
        return;

	IncludeModuleLangFile(__FILE__);

	$arMenu = array(
		"parent_menu" => "global_menu_services",
		"text" => GetMessage('menu.name'),
		"icon" => "startshop-menu-icon",
		"page_icon" => "startshop-menu-icon",
		"url" => "",
		"items_id" => "intec_startshop",
		"items" => array()
	);
	$arMenuSettings = array(
		"text" => GetMessage('menu.settings.name'),
		"items_id" => "intec_startshop_settings",
		"icon" => "startshop-menu-icon-settings",
		"page_icon" => "startshop-menu-icon-settings",
		"items" => array()
	);
	$arMenuSettingsOrder = array(
		"text" => GetMessage('menu.settings.order.name'),
		"icon" => "startshop-menu-icon-settings-order",
		"page_icon" => "startshop-menu-icon-settings-order",
		"items_id" => "intec_startshop_settings_order",
		"items" => array()
	);

    if (CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_CATALOG',
        'V'
    ) || $USER->IsAdmin())
        $arMenuSettings['items'][] = array(
            "text" => GetMessage('menu.settings.catalog.name'),
            "url" => "/bitrix/admin/startshop_settings_catalog.php?lang=".LANG,
            "items_id" => "intec_startshop_settings_catalogs",
            "icon" => "startshop-menu-icon-settings-catalogs",
            "page_icon" => "startshop-menu-icon-settings-catalogs",
            "more_url" => array(
                "startshop_settings_catalog.php",
                "startshop_settings_catalog_edit.php"
            )
        );

    if (CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_SITES',
        'V'
    ) || $USER->IsAdmin())
        $arMenuSettings['items'][] = array(
            "text" => GetMessage('menu.settings.sites.name'),
            "url" => "/bitrix/admin/startshop_settings_sites.php?lang=".LANG,
            "items_id" => "intec_startshop_settings_sites",
            "icon" => "startshop-menu-icon-settings-sites",
            "page_icon" => "startshop-menu-icon-settings-sites",
            "more_url" => array(
                "startshop_settings_sites.php"
            )
        );

    if (CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_ORDER_PROPERTY',
        'V'
    ) || $USER->IsAdmin())
        $arMenuSettingsOrder['items'][] = array(
            "text" => GetMessage('menu.settings.order.fields.name'),
            "url" => "/bitrix/admin/startshop_settings_order_property.php?lang=".LANG,
            "items_id" => "intec_startshop_settings_order_properties",
            "icon" => "startshop-menu-icon-settings-order-properties",
            "page_icon" => "startshop-menu-icon-settings-order-properties",
            "more_url" => array(
                "startshop_settings_order_property.php",
                "startshop_settings_order_property_edit.php"
            )
        );

    if (CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_ORDER_STATUS',
        'V'
    ) || $USER->IsAdmin())
        $arMenuSettingsOrder['items'][] = array(
            "text" => GetMessage('menu.settings.order.statuses.name'),
            "url" => "/bitrix/admin/startshop_settings_order_status.php?lang=".LANG,
            "items_id" => "intec_startshop_settings_order_statuses",
            "icon" => "startshop-menu-icon-settings-order-statuses",
            "page_icon" => "startshop-menu-icon-settings-order-statuses",
            "more_url" => array(
                "startshop_settings_order_status.php",
                "startshop_settings_order_status_edit.php"
            )
        );

    if (!empty($arMenuSettingsOrder['items']))
        $arMenuSettings['items'][] = $arMenuSettingsOrder;

    if (CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_PRICE',
        'V'
    ) || $USER->IsAdmin())
        $arMenuSettings['items'][] = array(
            "text" => GetMessage('menu.settings.price.name'),
            "url" => "/bitrix/admin/startshop_settings_price.php?lang=".LANG,
            "items_id" => "intec_startshop_settings_prices",
            "icon" => "startshop-menu-icon-settings-prices",
            "page_icon" => "startshop-menu-icon-settings-prices",
            "more_url" => array(
                "startshop_settings_price.php",
                "startshop_settings_price_edit.php"
            )
        );

    if (CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_DELIVERY',
        'V'
    ) || $USER->IsAdmin())
        $arMenuSettings['items'][] = array(
            "text" => GetMessage('menu.settings.delivery.name'),
            "url" => "/bitrix/admin/startshop_settings_delivery.php?lang=".LANG,
            "items_id" => "intec_startshop_settings_deliveries",
            "icon" => "startshop-menu-icon-settings-deliveries",
            "page_icon" => "startshop-menu-icon-settings-deliveries",
            "more_url" => array(
                "startshop_settings_delivery.php",
                "startshop_settings_delivery_edit.php"
            )
        );

    if (CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_CURRENCY',
        'V'
    ) || $USER->IsAdmin())
        $arMenuSettings['items'][] = array(
            "text" => GetMessage('menu.settings.currency.name'),
            "url" => "/bitrix/admin/startshop_settings_currency.php?lang=".LANG,
            "items_id" => "intec_startshop_settings_currencies",
            "icon" => "startshop-menu-icon-settings-currencies",
            "page_icon" => "startshop-menu-icon-settings-currencies",
            "more_url" => array(
                "startshop_settings_currency.php",
                "startshop_settings_currency_edit.php"
            ),
        );

    if (CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_PAYMENT',
        'V'
    ) || $USER->IsAdmin())
        $arMenuSettings['items'][] = array(
            "text" => GetMessage('menu.settings.payment.name'),
            "url" => "/bitrix/admin/startshop_settings_payment.php?lang=".LANG,
            "items_id" => "intec_startshop_settings_payments",
            "icon" => "startshop-menu-icon-settings-payments",
            "page_icon" => "startshop-menu-icon-settings-payments",
            "more_url" => array(
                "startshop_settings_payment.php",
                "startshop_settings_payment_edit.php"
            )
        );

    if ($USER->IsAdmin())
        $arMenuSettings['items'][] = array(
            "text" => GetMessage('menu.settings.rights.name'),
            "url" => "/bitrix/admin/startshop_settings_rights.php?lang=".LANG,
            "items_id" => "intec_startshop_settings_rights",
            "icon" => "startshop-menu-icon-settings-rights",
            "page_icon" => "startshop-menu-icon-settings-rights",
            "more_url" => array(
                "startshop_settings_rights.php",
            )
        );

    if (CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_1C',
        'V'
    ) || $USER->IsAdmin())
        $arMenuSettings['items'][] = array(
            "text" => GetMessage('menu.settings.1c.name'),
            "url" => "/bitrix/admin/startshop_settings_1c.php?lang=".LANG,
            "items_id" => "intec_startshop_settings_1c",
            "icon" => "startshop-menu-icon-settings-1c",
            "page_icon" => "startshop-menu-icon-settings-1c",
            "more_url" => array(
                "startshop_settings_1c.php",
            )
        );

    if (!empty($arMenuSettings['items']))
        $arMenu['items'][] = $arMenuSettings;

    if (CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_ORDERS',
        'V'
    ) || $USER->IsAdmin())
        $arMenu['items'][] = array(
            "text" => GetMessage('menu.orders.name'),
            "icon" => "startshop-menu-icon-orders",
            "items_id" => "intec_startshop_orders",
            "page_icon" => "startshop-menu-icon-orders",
            "url" => "/bitrix/admin/startshop_orders.php?lang=".LANG,
            "more_url" => array(
                "startshop_orders.php",
                "startshop_orders_edit.php",
                "startshop_orders_add_item",
                "startshop_orders_edit_item"
            )
        );

    if (CStartShopUtilsRights::AllowedForGroups(
            $USER->GetUserGroupArray(),
            'STARTSHOP_FORMS',
            'V'
        ) || $USER->IsAdmin())
        $arMenu['items'][] = array(
            "text" => GetMessage('menu.forms.name'),
            "icon" => "startshop-menu-icon-forms",
            "items_id" => "intec_startshop_forms",
            "page_icon" => "startshop-menu-icon-forms",
            "url" => "/bitrix/admin/startshop_forms.php?lang=".LANG,
            "more_url" => array(
                'startshop_forms.php',
                'startshop_forms_edit.php',
                'startshop_forms_results.php',
                'startshop_forms_results_edit.php',
                'startshop_forms_fields.php',
                'startshop_forms_fields_edit.php'
            )
        );

	if (!empty($arMenu['items']))
		return $arMenu;

	return array();
?>