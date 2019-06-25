<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");?>
<?
    global $USER, $APPLICATION;
    IncludeModuleLangFile(__FILE__);

    if (!CModule::IncludeModule("iblock"))
        return;

    if (!CModule::IncludeModule("intec.startshop"))
        return;

    $bRightsView = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_ORDERS',
        'V'
    ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_ORDERS',
        'E'
    ) || $USER->IsAdmin();

    if (!$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

    $arLinks = array(
        'ADD' => "/bitrix/admin/startshop_orders_edit.php?lang=".LANG."&action=add",
        'ADD_ITEM' => "/bitrix/admin/startshop_orders_add_item.php?lang=".LANG."&order=#ID#",
        'EDIT_ITEM' => "/bitrix/admin/startshop_orders_edit_item.php?lang=".LANG."&order=#ID#&item=#ITEM#",
        'DELETE_ITEM' => "/bitrix/admin/startshop_orders_edit.php?lang=".LANG."&action=edit&ID=#ID#&listaction=delete&listitem=#ITEM#",
        'EDIT' => "/bitrix/admin/startshop_orders_edit.php?lang=".LANG."&action=edit&ID=#ID#",
        'VIEW' => "/bitrix/admin/startshop_orders_edit.php?lang=".LANG."&action=view&ID=#ID#",
        'BACK' => "/bitrix/admin/startshop_orders.php?lang=".LANG,
        'IBLOCK_EDIT_ITEM' => "/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=#IBLOCK_ID#&type=#IBLOCK_TYPE_ID#&ID=#ID#&lang=".LANG
    );

    $arItem = array();

    $bActionSave = !empty($_REQUEST['save']);
    $bActionApply = !empty($_REQUEST['apply']);

    $arActions = array('add', 'edit', 'view');
    $sAction = $_REQUEST['action'];

    if (empty($sAction) && !empty($_REQUEST['ID']))
        $sAction = 'view';

    if (!$bRightsEdit && ($sAction == 'add' || $sAction == 'edit')) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    $sError = null;
    $sNotify = null;

    if (!in_array($sAction, $arActions)) {
        LocalRedirect($arLinks['BACK']);
        die();
    }

    $arValues = array();
    $arValues['USER'] = intval($_REQUEST['USER']);
    $arValues['DELIVERY'] = intval($_REQUEST['DELIVERY']);
    $arValues['PAYMENT'] = intval($_REQUEST['PAYMENT']);
    $arValues['STATUS'] = intval($_REQUEST['STATUS']);
    $arValues['PAYED'] = strval($_REQUEST['PAYED']);

    if ($sAction == 'add') {
        $arValues['SID'] = strval($_REQUEST['SID']);
        $arValues['CURRENCY'] = strval($_REQUEST['CURRENCY']);
        $arValues = array(
            'USER' => $arValues['USER'],
            'SID' => $arValues['SID'],
            'CURRENCY' => $arValues['CURRENCY']
        );

        if ($bActionSave) {
            if (!empty($arValues['SID']) && !empty($arValues['CURRENCY'])) {
                $iItemID = CStartShopOrder::Add($arValues);

                if ($iItemID) {
                    LocalRedirect(CStartShopUtil::ReplaceMacros($arLinks['EDIT'], array('ID' => $iItemID)).'&ADDED=Y');
                    die();
                }

                $sError = GetMessage('messages.warning.create');
            } else {
                $arEmptyFields = array();

                if (empty($arValues['SID'])) $arEmptyFields[] = GetMessage('fields.site');
                if (empty($arValues['CURRENCY'])) $arEmptyFields[] = GetMessage('fields.currency');

                $sError = GetMessage('messages.warning.empty_fields', array(
                    '#FIELDS#' => '\''.implode('\', \'', $arEmptyFields).'\''
                ));
            }
        }

        $APPLICATION->SetTitle(GetMessage('title.add'));
    }

    if ($sAction == 'edit' || $sAction == 'view') {
        $arItem = CStartShopOrder::GetByID($_REQUEST['ID'])->GetNext();

        if ($_REQUEST['ADDED'] == 'Y')
            $sNotify = GetMessage('messages.notify.added');

        if (empty($arItem)) {
            LocalRedirect($arLinks['BACK']);
            die();
        }

        $arDeliveries = CStartShopUtil::DBResultToArray(CStartShopDelivery::GetList(array('SORT' => 'ASC'), array('SID' => $arItem['SID'])), 'ID');
        $arPayments = CStartShopUtil::DBResultToArray(CStartShopPayment::GetList(array('SORT' => 'ASC')), 'ID');
        $arStatuses = CStartShopUtil::DBResultToArray(CStartShopOrderStatus::GetList(array('SORT' => 'ASC'), array('SID' => $arItem['SID'])), 'ID');
        $arOrderProperties = CStartShopUtil::DBResultToArray(CStartShopOrderProperty::GetList(array('SORT' => 'ASC'), array('SID' => $arItem['SID'])), 'ID');

        $arOrderPropertiesDisplayed = array();
        $arOrderPropertiesDelivery = array();
        $arOrderPropertiesEditable = array();

        $arOrderPropertiesDisplayed = $arOrderProperties;


        foreach ($arDeliveries as $arDelivery)
            foreach ($arDelivery['PROPERTIES'] as $iDeliveryPropertyID) {
                unset($arOrderPropertiesDisplayed[$iDeliveryPropertyID]);
                $arOrderPropertiesDelivery[$iDeliveryPropertyID] = $arOrderProperties[$iDeliveryPropertyID];
            }

        $arOrderPropertiesEditable = $arOrderPropertiesDisplayed;
        $arOrderPropertiesEmpty = array();

        if (!empty($arDeliveries[$arValues['DELIVERY']]))
            foreach ($arDeliveries[$arValues['DELIVERY']]['PROPERTIES'] as $iDeliveryProperty) {
                $arOrderPropertiesEditable[$iDeliveryProperty] = $arOrderProperties[$iDeliveryProperty];
            }

        $arValues['PROPERTIES'] = array();

        foreach ($arOrderPropertiesEditable as $arOrderProperty) {
            $cOrderPropertyValue = $_REQUEST['PROPERTY_'.$arOrderProperty['ID']];
            $arValues['PROPERTIES'][$arOrderProperty['ID']] = $cOrderPropertyValue;

            if ($arOrderProperty['REQUIRED'] == 'Y' && $arOrderProperty['ACTIVE'] == 'Y' && empty($cOrderPropertyValue))
                $arOrderPropertiesEmpty[] = $arOrderProperty['LANG'][LANGUAGE_ID]['NAME'];
        }

        if (($bActionSave || $bActionApply) && $sAction == 'edit') {
            if (empty($arOrderPropertiesEmpty)) {
                $bUpdated = CStartShopOrder::Update($arItem['ID'], $arValues);

                if ($bUpdated) {
                    if ($bActionSave) {
                        LocalRedirect($arLinks['BACK']);
                        die();
                    }

                    $sNotify = GetMessage('messages.notify.saved');
                }

                $arItem = CStartShopOrder::GetByID($_REQUEST['ID'])->GetNext();
            } else {
                $sError = GetMessage('messages.warning.empty_fields', array(
                    '#FIELDS#' => '\''.implode('\', \'', $arOrderPropertiesEmpty).'\''
                ));
            }
        }

        $arOrderItemsElementsID = array_keys($arItem['ITEMS']);

        if (!empty($arOrderItemsElementsID))
            $arOrderItemsElements = CStartShopUtil::DBResultToArray(CStartShopCatalogProduct::GetList(array(), array(
                "ID" => $arOrderItemsElementsID
            )), 'ID');

        foreach ($arItem['ITEMS'] as $iOrderItemKey => $arOrderItem)
            $arItem['ITEMS'][$iOrderItemKey]['ELEMENT'] = $arOrderItemsElements[$arOrderItem['ITEM']];

        unset($arOrderItemsElementID, $arOrderItemsElements);

        $arValues['USER'] = intval($arItem['USER']);
        $arValues['DELIVERY'] = intval($arItem['DELIVERY']);
        $arValues['PAYMENT'] = intval($arItem['PAYMENT']);
        $arValues['STATUS'] = intval($arItem['STATUS']);
        $arValues['PROPERTIES'] = $arItem['PROPERTIES'];
        $arValues['PAYED'] = strval($arItem['PAYED']);

        $APPLICATION->SetTitle(GetMessage('title.edit'));

        if ($sAction == 'view')
            $APPLICATION->SetTitle(GetMessage('title.view', $arItem));
    }

    $arUsers = CStartShopUtil::DBResultToArray(CUser::GetList($by = "timestamp_x", $order = "asc"), 'ID');
    $arSites = CStartShopUtil::DBResultToArray(CSite::GetList($by = "sort", $order = "asc"), 'ID');
    $arCurrencies = CStartShopUtil::DBResultToArray(CStartShopCurrency::GetList(array('SORT' => 'ASC')), 'CODE');

    if ($sAction == 'view') {
        $arPayment = array();
        $arDelivery = array();

        if (!empty($arItem['PAYMENT']))
            $arPayment = CStartShopPayment::GetList(array(), array('ID' => $arItem['PAYMENT']))->Fetch();

        if (!empty($arItem['DELIVERY']))
            $arDelivery = CStartShopDelivery::GetList(array(), array('ID' => $arItem['DELIVERY'], 'SID' => $arItem['SID']))->Fetch();

        $arOrderPropertiesDisplayedDelivery = array();

        if (!empty($arDelivery['PROPERTIES']))
            foreach ($arDelivery['PROPERTIES'] as $iDeliveryProperty)
                $arOrderPropertiesDisplayedDelivery[] = $arOrderProperties[$iDeliveryProperty];

        $arViewOrdersInformation = array(
            array(
                "NAME" => GetMessage('fields.id'),
                "VALUE" => $arItem['ID']
            ),
            array(
                "NAME" => GetMessage('fields.date_create'),
                "VALUE" => date("d.m.Y H:i:s", strtotime($arItem['DATE_CREATE']))
            )
        );

        if (!empty($arStatuses[$arItem['STATUS']]))
            $arViewOrdersInformation[] = array(
                "NAME" => GetMessage("fields.status"),
                "VALUE" => $arStatuses[$arItem['STATUS']]['LANG'][LANGUAGE_ID]['NAME']
            );

        $arViewOrdersInformation[] = array(
            "NAME" => GetMessage("fields.site"),
            "VALUE" => '['.$arItem['SID'].'] '.$arSites[$arItem['SID']]['NAME']
        );

        if (!empty($arUsers[$arItem['USER']]))
            if (!empty($arUsers[$arItem['USER']]['NAME']) && !empty($arUsers[$arItem['USER']]['LAST_NAME']) && !empty($arUsers[$arItem['USER']]['SECOND_NAME'])) {
                $arViewOrdersInformation[] = array(
                    "NAME" => GetMessage("fields.user"),
                    "VALUE" => '['.$arUsers[$arItem['USER']]['ID'].'] '.$arUsers[$arItem['USER']]['LAST_NAME'].' '.$arUsers[$arItem['USER']]['NAME'].' '. $arUsers[$arItem['USER']]['SECOND_NAME']
                );
            } else {
                $arViewOrdersInformation[] = array(
                    "NAME" => GetMessage("fields.user"),
                    "VALUE" => '['.$arUsers[$arItem['USER']]['ID'].'] '.$arUsers[$arItem['USER']]['LOGIN']
                );
            }


        $arViewOrdersDeliveryInformation = array(
            array(
                "NAME" => GetMessage('fields.delivery'),
                "VALUE" => $arDelivery['LANG'][LANGUAGE_ID]['NAME']
            )
        );

        $arViewOrdersPaymentInformation = array(
            array(
                "NAME" => GetMessage('fields.payment'),
                "VALUE" => $arPayment['LANG'][LANGUAGE_ID]['NAME']
            ),
            array(
                "NAME" => GetMessage('fields.payed'),
                "VALUE" => $arItem['PAYED'] == 'Y' ? GetMessage('select.yes') : GetMessage('select.no')
            ),
            array(
                "NAME" => GetMessage('fields.total'),
                "VALUE" => CStartShopCurrency::ConvertAndFormatAsString($arItem['AMOUNT'], $arItem['CURRENCY'])
            )
        );

        if ($arDelivery['PRICE'] > 0)
            $arViewOrdersDeliveryInformation[] = array(
                "NAME" => GetMessage('fields.delivery.price'),
                "VALUE" => CStartShopCurrency::FormatAsString($arDelivery['PRICE'])
            );

        foreach ($arOrderPropertiesDisplayed as $arOrderProperty)
            if (!empty($arItem['PROPERTIES'][$arOrderProperty['ID']]))
                if ($arOrderProperty['TYPE'] == 'S' && (empty($arOrderProperty['SUBTYPE']) || $arOrderProperty['SUBTYPE'] == "TEXT")) {
                    $arViewOrdersInformation[] = array(
                        "NAME" => $arOrderProperty['LANG'][LANGUAGE_ID]['NAME'],
                        "VALUE" => $arItem['PROPERTIES'][$arOrderProperty['ID']]
                    );
                } else if ($arOrderProperty['TYPE'] == 'B' && empty($arOrderProperty['SUBTYPE'])) {
                    $arViewOrdersInformation[] = array(
                        "NAME" => $arOrderProperty['LANG'][LANGUAGE_ID]['NAME'],
                        "VALUE" => $arItem['PROPERTIES'][$arOrderProperty['ID']] == 'Y' ? GetMessage('select.yes') : GetMessage('select.no')
                    );
                } else if ($arOrderProperty['TYPE'] == 'L' && $arOrderProperty['SUBTYPE'] == "IBLOCK_ELEMENT") {
                    $arIBlockElement = CIBlockElement::GetByID($arItem['PROPERTIES'][$arOrderProperty['ID']])->Fetch();
                    if (!empty($arIBlockElement))
                        $arViewOrdersInformation[] = array(
                            "NAME" => $arOrderProperty['LANG'][LANGUAGE_ID]['NAME'],
                            "VALUE" => $arIBlockElement['NAME']
                        );
                }

        foreach ($arOrderPropertiesDisplayedDelivery as $arOrderProperty)
            if (!empty($arItem['PROPERTIES'][$arOrderProperty['ID']]))
                if ($arOrderProperty['TYPE'] == 'S' && (empty($arOrderProperty['SUBTYPE']) || $arOrderProperty['SUBTYPE'] == "TEXT")) {
                    $arViewOrdersDeliveryInformation[] = array(
                        "NAME" => $arOrderProperty['LANG'][LANGUAGE_ID]['NAME'],
                        "VALUE" => $arItem['PROPERTIES'][$arOrderProperty['ID']]
                    );
                } else if ($arOrderProperty['TYPE'] == 'B' && empty($arOrderProperty['SUBTYPE'])) {
                    $arViewOrdersDeliveryInformation[] = array(
                        "NAME" => $arOrderProperty['LANG'][LANGUAGE_ID]['NAME'],
                        "VALUE" => $arItem['PROPERTIES'][$arOrderProperty['ID']] == 'Y' ? GetMessage('select.yes') : GetMessage('select.no')
                    );
                } else if ($arOrderProperty['TYPE'] == 'L' && $arOrderProperty['SUBTYPE'] == "IBLOCK_ELEMENT") {
                    $arIBlockElement = CIBlockElement::GetByID($arItem['PROPERTIES'][$arOrderProperty['ID']])->Fetch();
                    if (!empty($arIBlockElement))
                        $arViewOrdersDeliveryInformation[] = array(
                            "NAME" => $arOrderProperty['LANG'][LANGUAGE_ID]['NAME'],
                            "VALUE" => $arIBlockElement['NAME']
                        );
                }

        $arViewOrdersInformationFirst = CStartShopUtil::ArrayFilter($arViewOrdersInformation, function ($sKey) { return(!($sKey & 1)); }, STARTSHOP_UTIL_ARRAY_FILTER_USE_KEY);
        $arViewOrdersInformationSecond = CStartShopUtil::ArrayFilter($arViewOrdersInformation, function ($sKey) { return(($sKey & 1)); }, STARTSHOP_UTIL_ARRAY_FILTER_USE_KEY);
        $arViewOrdersDeliveryInformationFirst = CStartShopUtil::ArrayFilter($arViewOrdersDeliveryInformation, function ($sKey) { return(!($sKey & 1)); }, STARTSHOP_UTIL_ARRAY_FILTER_USE_KEY);
        $arViewOrdersDeliveryInformationSecond = CStartShopUtil::ArrayFilter($arViewOrdersDeliveryInformation, function ($sKey) { return(($sKey & 1)); }, STARTSHOP_UTIL_ARRAY_FILTER_USE_KEY);
        $arViewOrdersPaymentInformationFirst = CStartShopUtil::ArrayFilter($arViewOrdersPaymentInformation, function ($sKey) { return(!($sKey & 1)); }, STARTSHOP_UTIL_ARRAY_FILTER_USE_KEY);
        $arViewOrdersPaymentInformationSecond = CStartShopUtil::ArrayFilter($arViewOrdersPaymentInformation, function ($sKey) { return(($sKey & 1)); }, STARTSHOP_UTIL_ARRAY_FILTER_USE_KEY);
    }

    $arContextMenu = array(
        array(
            "TEXT" => GetMessage("title.buttons.back"),
            "ICON" => "btn_list",
            "LINK" => $arLinks['BACK']
        )
    );

    if ($bRightsEdit)
        $arContextMenu[] = array(
            "TEXT" => GetMessage("title.buttons.add"),
            "ICON" => "btn_new",
            "LINK" => $arLinks['ADD'],
        );

    if ($sAction == 'view') {
        if ($bRightsEdit)
            $arContextMenu[] = array(
                "TEXT" => GetMessage("title.buttons.edit"),
                "LINK" => CStartShopUtil::ReplaceMacros($arLinks['EDIT'], $arItem),
            );
    } else if ($sAction == 'edit') {
        $arContextMenu[] = array(
            "TEXT" => GetMessage("title.buttons.view"),
            "LINK" => CStartShopUtil::ReplaceMacros($arLinks['VIEW'], $arItem),
        );
    }

    $arTabs = array(
        array(
            "DIV" => "common",
            "TAB" => GetMessage("tabs.common"),
            "ICON" => "catalog",
            "TITLE" => GetMessage("tabs.common")
        )
    );

    if ($sAction == 'edit') {
        if (!empty($arOrderPropertiesDisplayed))
            $arTabs[] = array(
                "DIV" => "properties.order",
                "TAB" => GetMessage("tabs.properties.order"),
                "TITLE" => GetMessage("tabs.properties.order")
            );

        if (!empty($arOrderPropertiesDelivery))
            $arTabs[] = array(
                "DIV" => "properties.delivery",
                "TAB" => GetMessage("tabs.properties.delivery"),
                "TITLE" => GetMessage("tabs.properties.delivery")
            );

        $arTabs[] = array(
            "DIV" => "items",
            "TAB" => GetMessage("tabs.items"),
            "TITLE" => GetMessage("tabs.items")
        );

        if ($_REQUEST['listaction'] == 'delete' && !empty($_REQUEST['listitem']))
            CStartShopOrder::DeleteItem($arItem['ID'], $_REQUEST['listitem']);

        $sListID = "startshop_orders_items_list";

        $oList = new CAdminList($sListID);

        $dbResult = CStartShopOrder::GetItemsList(array(), array('ORDER' => $arItem['ID']));
        $dbResult = new CAdminResult($dbResult, $sListID);
        $dbResult->NavStart(20);
        $oList->NavText($dbResult->GetNavPrint(GetMessage('nav.title_items'), true));

        $oList->AddHeaders(array(
            array(
                "id" => "ITEM",
                "content" => GetMessage('fields.items.item'),
                "sort" => "ITEM",
                "default" => true
            ),
            array(
                "id" => "NAME",
                "content" => GetMessage('fields.items.name'),
                'sort' => 'NAME',
                "default" => true
            ),
            array(
                "id" => "PRICE",
                "content" => GetMessage('fields.items.price'),
                "sort" => "PRICE",
                "default" => true
            ),
            array(
                "id" => "QUANTITY",
                "content" => GetMessage('fields.items.quantity'),
                "sort" => "QUANTITY",
                "default" => true
            ),
            array(
                "id" => "AMOUNT",
                "content" => GetMessage('fields.items.amount'),
                "default" => true
            )
        ));

        while ($arResultItem = $dbResult->GetNext()) {
            $arActions = array(
                array(
                    "ICON" => "edit",
                    "TEXT" => GetMessage('actions.edit_item'),
                    "ACTION" => $oList->ActionRedirect(CStartShopUtil::ReplaceMacros($arLinks['EDIT_ITEM'], array('ID' => $arItem['ID'], 'ITEM' => $arResultItem['ITEM'])))
                ),
                array(
                    "ICON" => "delete",
                    "TEXT" => GetMessage('actions.delete_item'),
                    "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['DELETE_ITEM'], array('ID' => $arItem['ID'], 'ITEM' => $arResultItem['ITEM'])))
                )
            );

            $oRow = &$oList->AddRow($arResultItem["ITEM"], array(
                "ITEM" => $arResultItem["ITEM"],
                "NAME" => $arResultItem["NAME"],
                "PRICE" => CStartShopCurrency::ConvertAndFormatAsString($arResultItem["PRICE"], $arItem['CURRENCY']),
                "QUANTITY" => $arResultItem["QUANTITY"],
                "AMOUNT" => CStartShopCurrency::ConvertAndFormatAsString($arResultItem['PRICE'] * $arResultItem['QUANTITY'], $arItem['CURRENCY'])
            ));

            $oRow->AddActions($arActions);
        }

        $arItemsContextMenu = array(
            array(
                "TEXT" => GetMessage("actions.add_item"),
                "ICON" => "btn_new",
                "LINK" => CStartShopUtil::ReplaceMacros($arLinks['ADD_ITEM'], $arItem),
                "TITLE" => GetMessage("actions.add_item")
            )
        );

        $oList->AddAdminContextMenu($arItemsContextMenu);
        $oList->CheckListMode();
    }

    $oContextMenu = new CAdminContextMenu($arContextMenu);
    $oTabControl = new CAdminTabControl("tabs", $arTabs);
?>
<?require_once($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");?>
<?
    $oContextMenu->Show();

    if (!empty($sError))
        CAdminMessage::ShowMessage($sError);

    if (!empty($sNotify) && empty($sError))
        CAdminMessage::ShowNote($sNotify);
?>
<form method="POST" id="StartshopOrdersEdit">
    <?if ($sAction == 'add'):?>
        <?
            $oTabControl->Begin();
            $oTabControl->BeginNextTab();
        ?>
        <tr>
            <td width="40%"><b><?=GetMessage("fields.site")?>:</b></td>
            <td width="60%">
                <?foreach ($arSites as $arSite):?>
                    <label><input type="radio" value="<?=htmlspecialcharsbx($arSite['ID'])?>" name="SID"<?=$arValues['SID'] == $arSite['ID'] ? ' checked="checked"' : ''?>/><?=htmlspecialcharsbx($arSite['NAME'])?> (<?=htmlspecialcharsbx($arSite['ID'])?>)</label><br />
                <?endforeach?>
            </td>
        </tr>
        <tr>
            <td width="40%"><b><?=GetMessage("fields.currency")?>:</b></td>
            <td width="60%">
                <select name="CURRENCY">
                    <option value=""><?=GetMessage('select.empty')?></option>
                    <?foreach ($arCurrencies as $arCurrency):?>
                        <?if ($arCurrency['ACTIVE'] != 'Y') continue;?>
                        <option value="<?=htmlspecialcharsbx($arCurrency['CODE'])?>"<?=$arValues['CURRENCY'] == $arCurrency['CODE'] ? ' selected="selected"' : ''?>>[<?=htmlspecialcharsbx($arCurrency['CODE'])?>] <?=htmlspecialcharsbx($arCurrency['LANG'][LANGUAGE_ID]['NAME'])?></option>
                    <?endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("fields.user")?>:</td>
            <td width="60%">
                <select name="USER">
                    <option value=""><?=GetMessage('fields.user.not_selected')?></option>
                    <?foreach ($arUsers as $arUser):?>
                        <option value="<?=$arUser['ID']?>"<?=$arValues['USER'] == $arUser['ID'] ? ' selected="selected"' : ''?>>
                            <?if (!empty($arUser['NAME']) && !empty($arUser['LAST_NAME']) && !empty($arUser['SECOND_NAME'])):?>
                                <?='['.htmlspecialcharsbx($arUser['ID']).'] '.htmlspecialcharsbx($arUser['LAST_NAME']).' '.htmlspecialcharsbx($arUser['NAME']).' '.htmlspecialcharsbx($arUser['SECOND_NAME'])?>
                            <?else:?>
                                <?='['.htmlspecialcharsbx($arUser['ID']).'] '.htmlspecialcharsbx($arUser['LOGIN'])?>
                            <?endif;?>
                        </option>
                    <?endforeach;?>
                </select>
            </td>
        </tr>
        <?
            $oTabControl->Buttons();
        ?>
            <input type="submit" class="adm-btn-save" value="<?=GetMessage('actions.create')?>" name="save" />
            <a href="<?=$arLinks['BACK']?>" class="adm-btn"><?=GetMessage('actions.cancel')?></a>
        <?
            $oTabControl->End();
        ?>
    <?elseif ($sAction == 'view'):?>
        <div class="startshop-adm-orders-header"><?=GetMessage('section.information')?></div>
        <div class="startshop-adm-orders-information">
            <div class="startshop-adm-orders-information-wrapper">
                <?if (!empty($arViewOrdersInformationFirst)):?>
                    <div class="startshop-adm-orders-information-wrapper-wrapper">
                        <table class="startshop-adm-orders-table">
                            <?foreach ($arViewOrdersInformationFirst as $arViewOrderInformationElement):?>
                                <tr>
                                    <td class="startshop-adm-orders-name"><div class="startshop-adm-orders-name"><?=htmlspecialcharsbx($arViewOrderInformationElement['NAME'])?>:</div></td>
                                    <td class="startshop-adm-orders-value"><div class="startshop-adm-orders-value"><?=htmlspecialcharsbx($arViewOrderInformationElement['VALUE'])?></div></td>
                                </tr>
                            <?endforeach;?>
                        </table>
                    </div>
                <?endif;?>
                <?if (!empty($arViewOrdersInformationSecond)):?>
                    <div class="startshop-adm-orders-information-wrapper-wrapper">
                        <table class="startshop-adm-orders-table">
                            <?foreach ($arViewOrdersInformationSecond as $arViewOrderInformationElement):?>
                                <tr>
                                    <td class="startshop-adm-orders-name"><div class="startshop-adm-orders-name"><?=htmlspecialcharsbx($arViewOrderInformationElement['NAME'])?>:</div></td>
                                    <td class="startshop-adm-orders-value"><div class="startshop-adm-orders-value"><?=htmlspecialcharsbx($arViewOrderInformationElement['VALUE'])?></div></td>
                                </tr>
                            <?endforeach;?>
                        </table>
                    </div>
                <?endif;?>
            </div>
        </div>
        <?if (!empty($arItem['ITEMS'])):?>
            <div class="startshop-adm-orders-header"><?=GetMessage('section.items')?></div>
            <div class="startshop-adm-orders-information">
                <table class="startshop-adm-orders-items-table">
                    <tr class="startshop-adm-orders-items-row-header">
                        <td><div class="startshop-adm-orders-cell"><?=GetMessage('fields.items.item')?></div></td>
                        <td><div class="startshop-adm-orders-cell"><?=GetMessage('fields.items.name')?></div></td>
                        <td><div class="startshop-adm-orders-cell"><?=GetMessage('fields.items.properties')?></div></td>
                        <td><div class="startshop-adm-orders-cell"><?=GetMessage('fields.items.price')?></div></td>
                        <td><div class="startshop-adm-orders-cell"><?=GetMessage('fields.items.quantity')?></div></td>
                        <td><div class="startshop-adm-orders-cell"><?=GetMessage('fields.items.amount')?></div></td>
                    </tr>
                    <?$fOrderItemsAmount = 0;?>
                    <?foreach ($arItem['ITEMS'] as $arOrderItem):?>
                        <tr>
                            <td><div class="startshop-adm-orders-cell">
                                <?if (!empty($arOrderItem['ELEMENT'])):?>
                                    <a target="_blank" href="<?=CStartShopUtil::ReplaceMacros($arLinks['IBLOCK_EDIT_ITEM'], $arOrderItem['ELEMENT'])?>">
                                        <?=htmlspecialcharsbx($arOrderItem['ITEM'])?>
                                    </a>
                                <?else:?>
                                    <?=htmlspecialcharsbx($arOrderItem['ITEM'])?>
                                <?endif;?>
                            </div></td>
                            <td><div class="startshop-adm-orders-cell"><?=htmlspecialcharsbx($arOrderItem['NAME'])?></div></td>
                            <td><div class="startshop-adm-orders-cell">
                                <?if ($arOrderItem['ELEMENT']['STARTSHOP']['OFFER']['OFFER']):?>
                                    <?foreach ($arOrderItem['ELEMENT']['STARTSHOP']['OFFER']['PROPERTIES'] as $arProperty):?>
                                        <?if ($arProperty['TYPE'] == 'TEXT'):?>
                                            <div class="startshop-adm-offer-property startshop-adm-offer-property-text">
                                                <div class="startshop-adm-offer-property-name">
                                                    <?=$arProperty['NAME']?>:
                                                </div>
                                                <div class="startshop-adm-offer-property-value">
                                                    <?=$arProperty['VALUE']['TEXT']?>
                                                </div>
                                            </div>
                                        <?else:?>
                                            <div class="startshop-adm-offer-property startshop-adm-offer-property-picture">
                                                <div class="startshop-adm-offer-property-name">
                                                    <?=$arProperty['NAME']?>:
                                                </div>
                                                <div class="startshop-adm-offer-property-value">
                                                    <div class="startshop-adm-offer-property-value-wrapper">
                                                        <img src="<?=$arProperty['VALUE']['PICTURE']?>" alt="<?=$arProperty['VALUE']['TEXT']?>" title="<?=$arProperty['VALUE']['TEXT']?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        <?endif;?>
                                    <?endforeach?>
                                <?else:?>
                                    <?=GetMessage('fields.items.properties.empty')?>
                                <?endif;?>
                            </div></td>
                            <td><div class="startshop-adm-orders-cell"><?=htmlspecialcharsbx(CStartShopCurrency::ConvertAndFormatAsString($arOrderItem['PRICE'], $arItem['CURRENCY']))?></div></td>
                            <td><div class="startshop-adm-orders-cell"><?=htmlspecialcharsbx($arOrderItem['QUANTITY'])?></div></td>
                            <td><div class="startshop-adm-orders-cell"><?=htmlspecialcharsbx(CStartShopCurrency::ConvertAndFormatAsString($arOrderItem['PRICE'] * $arOrderItem['QUANTITY'], $arItem['CURRENCY']))?></div></td>
                        </tr>
                        <?$fOrderItemsAmount += $arOrderItem['PRICE'] * $arOrderItem['QUANTITY'];?>
                    <?endforeach;?>
                    <tr class="startshop-adm-orders-items-row-footer">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><div class="startshop-adm-orders-cell"><?=GetMessage('fields.items.total')?>:</div></td>
                        <td><div class="startshop-adm-orders-cell"><?=htmlspecialcharsbx(CStartShopCurrency::FormatAsString($fOrderItemsAmount, $arItem['CURRENCY']))?></div></td>
                    </tr>
                </table>
            </div>
        <?endif;?>
        <?if (!empty($arDelivery)):?>
            <div class="startshop-adm-orders-header"><?=GetMessage('section.delivery')?></div>
            <div class="startshop-adm-orders-information">
                <div class="startshop-adm-orders-information-wrapper">
                    <?if (!empty($arViewOrdersDeliveryInformationFirst)):?>
                        <div class="startshop-adm-orders-information-wrapper-wrapper">
                            <table class="startshop-adm-orders-table">
                                <?foreach ($arViewOrdersDeliveryInformationFirst as $arViewOrderInformationElement):?>
                                    <tr>
                                        <td class="startshop-adm-orders-name"><div class="startshop-adm-orders-name"><?=htmlspecialcharsbx($arViewOrderInformationElement['NAME'])?>:</div></td>
                                        <td class="startshop-adm-orders-value"><div class="startshop-adm-orders-value"><?=htmlspecialcharsbx($arViewOrderInformationElement['VALUE'])?></div></td>
                                    </tr>
                                <?endforeach;?>
                            </table>
                        </div>
                    <?endif;?>
                    <?if (!empty($arViewOrdersDeliveryInformationSecond)):?>
                        <div class="startshop-adm-orders-information-wrapper-wrapper">
                            <table class="startshop-adm-orders-table">
                                <?foreach ($arViewOrdersDeliveryInformationSecond as $arViewOrderInformationElement):?>
                                    <tr>
                                        <td class="startshop-adm-orders-name"><div class="startshop-adm-orders-name"><?=htmlspecialcharsbx($arViewOrderInformationElement['NAME'])?>:</div></td>
                                        <td class="startshop-adm-orders-value"><div class="startshop-adm-orders-value"><?=htmlspecialcharsbx($arViewOrderInformationElement['VALUE'])?></div></td>
                                    </tr>
                                <?endforeach;?>
                            </table>
                        </div>
                    <?endif;?>
                </div>
            </div>
        <?endif;?>
        <?if (!empty($arPayment)):?>
            <div class="startshop-adm-orders-header"><?=GetMessage('section.payment')?></div>
            <div class="startshop-adm-orders-information">
                <div class="startshop-adm-orders-information-wrapper">
                    <?if (!empty($arViewOrdersPaymentInformationFirst)):?>
                        <div class="startshop-adm-orders-information-wrapper-wrapper">
                            <table class="startshop-adm-orders-table">
                                <?foreach ($arViewOrdersPaymentInformationFirst as $arViewOrderInformationElement):?>
                                    <tr>
                                        <td class="startshop-adm-orders-name"><div class="startshop-adm-orders-name"><?=htmlspecialcharsbx($arViewOrderInformationElement['NAME'])?>:</div></td>
                                        <td class="startshop-adm-orders-value"><div class="startshop-adm-orders-value"><?=htmlspecialcharsbx($arViewOrderInformationElement['VALUE'])?></div></td>
                                    </tr>
                                <?endforeach;?>
                            </table>
                        </div>
                    <?endif;?>
                    <?if (!empty($arViewOrdersPaymentInformationSecond)):?>
                        <div class="startshop-adm-orders-information-wrapper-wrapper">
                            <table class="startshop-adm-orders-table">
                                <?foreach ($arViewOrdersPaymentInformationSecond as $arViewOrderInformationElement):?>
                                    <tr>
                                        <td class="startshop-adm-orders-name"><div class="startshop-adm-orders-name"><?=htmlspecialcharsbx($arViewOrderInformationElement['NAME'])?>:</div></td>
                                        <td class="startshop-adm-orders-value"><div class="startshop-adm-orders-value"><?=htmlspecialcharsbx($arViewOrderInformationElement['VALUE'])?></div></td>
                                    </tr>
                                <?endforeach;?>
                            </table>
                        </div>
                    <?endif;?>
                </div>
            </div>
        <?endif;?>
    <?elseif ($sAction == 'edit'):?>
        <?
            $oTabControl->Begin();
            $oTabControl->BeginNextTab();
        ?>
        <tr>
            <td width="40%"><b><?=GetMessage("fields.id")?>:</b></td>
            <td width="60%"><?=htmlspecialcharsbx($arItem['ID'])?></td>
        </tr>
        <tr>
            <td width="40%"><b><?=GetMessage('fields.date_create')?>:</b></td>
            <td width="60%"><?=date("d.m.Y H:i:s", strtotime($arItem['DATE_CREATE']))?></td>
        </tr>
        <tr>
            <td width="40%"><b><?=GetMessage("fields.site")?>:</b></td>
            <td width="60%"><?=htmlspecialcharsbx('['.$arItem['SID'].'] '.$arSites[$arItem['SID']]['NAME'])?></td>
        </tr>
        <tr>
            <td width="40%"><b><?=GetMessage("fields.currency")?>:</b></td>
            <td width="60%"><?=htmlspecialcharsbx('['.$arItem['CURRENCY'].'] '.$arCurrencies[$arItem['CURRENCY']]['LANG'][LANGUAGE_ID]['NAME'])?></td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("fields.payed")?>:</td>
            <td width="60%"><input type="checkbox" value="Y" name="PAYED"<?=$arValues['PAYED'] == 'Y' ? ' checked="checked"' : ''?>/></td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("fields.user")?>:</td>
            <td width="60%">
                <select name="USER">
                    <option value=""><?=GetMessage('fields.user.not_selected')?></option>
                    <?foreach ($arUsers as $arUser):?>
                        <option value="<?=$arUser['ID']?>"<?=$arValues['USER'] == $arUser['ID'] ? ' selected="selected"' : ''?>>
                            <?if (!empty($arUser['NAME']) && !empty($arUser['LAST_NAME']) && !empty($arUser['SECOND_NAME'])):?>
                                <?=htmlspecialcharsbx('['.$arUser['ID'].'] '.$arUser['LAST_NAME'].' '.$arUser['NAME'].' '.$arUser['SECOND_NAME'])?>
                            <?else:?>
                                <?=htmlspecialcharsbx('['.$arUser['ID'].'] '.$arUser['LOGIN'])?>
                            <?endif;?>
                        </option>
                    <?endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("fields.delivery")?>:</td>
            <td width="60%">
                <select name="DELIVERY">
                    <option value=""><?=GetMessage('fields.delivery.not_selected')?></option>
                    <?foreach ($arDeliveries as $arDelivery):?>
                        <option value="<?=$arDelivery['ID']?>"<?=$arValues['DELIVERY'] == $arDelivery['ID'] ? ' selected="selected"' : ''?>>[<?=htmlspecialcharsbx($arDelivery['CODE'])?>] <?=htmlspecialcharsbx($arDelivery['LANG'][LANGUAGE_ID]['NAME'])?></option>
                    <?endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("fields.payment")?>:</td>
            <td width="60%">
                <select name="PAYMENT">
                    <option value=""><?=GetMessage('fields.payment.not_selected')?></option>
                    <?foreach ($arPayments as $arPayment):?>
                        <option value="<?=$arPayment['ID']?>"<?=$arValues['PAYMENT'] == $arPayment['ID'] ? ' selected="selected"' : ''?>>[<?=htmlspecialcharsbx($arPayment['CODE'])?>] <?=htmlspecialcharsbx($arPayment['LANG'][LANGUAGE_ID]['NAME'])?></option>
                    <?endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("fields.status")?>:</td>
            <td width="60%">
                <select name="STATUS">
                    <option value=""><?=GetMessage('fields.status.not_selected')?></option>
                    <?foreach ($arStatuses as $arStatus):?>
                        <option value="<?=$arStatus['ID']?>"<?=$arValues['STATUS'] == $arStatus['ID'] ? ' selected="selected"' : ''?>><?=htmlspecialcharsbx($arStatus['LANG'][LANGUAGE_ID]['NAME'])?></option>
                    <?endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%"><b><?=GetMessage("fields.total")?>:</b></td>
            <td width="60%"><?=htmlspecialcharsbx(CStartShopCurrency::FormatAsString(CStartShopCurrency::Convert($arItem['AMOUNT'], $arItem['CURRENCY'])))?></td>
        </tr>
        <?if (!empty($arOrderPropertiesDisplayed)):?>
            <?$oTabControl->BeginNextTab();?>
            <?foreach ($arOrderPropertiesDisplayed as $arOrderProperty):?>
                <?if ($arOrderProperty['TYPE'] == 'S' && empty($arOrderProperty['SUBTYPE'])):?>
                    <tr>
                        <td width="40%">
                            <?=$arOrderProperty['REQUIRED'] == 'Y' ? '<b>' : ''?>
                            <?=$arOrderProperty['LANG'][LANGUAGE_ID]['NAME']?>
                            <?=$arOrderProperty['REQUIRED'] == 'Y' ? ' <span class="required" style="vertical-align: super; font-size: smaller;">*</span>:</b>' : ':'?>
                        </td>
                        <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['PROPERTIES'][$arOrderProperty['ID']])?>" name="PROPERTY_<?=$arOrderProperty['ID']?>"/></td>
                    </tr>
                <?endif;?>
                <?if ($arOrderProperty['TYPE'] == 'S' && $arOrderProperty['SUBTYPE'] == 'TEXT'):?>
                    <tr>
                        <td width="40%">
                            <?=$arOrderProperty['REQUIRED'] == 'Y' ? '<b>' : ''?>
                            <?=$arOrderProperty['LANG'][LANGUAGE_ID]['NAME']?>
                            <?=$arOrderProperty['REQUIRED'] == 'Y' ? ' <span class="required" style="vertical-align: super; font-size: smaller;">*</span>:</b>' : ':'?>
                        </td>
                        <td width="60%"><textarea name="PROPERTY_<?=$arOrderProperty['ID']?>" style="min-width: 200px; min-height: 60px;"><?=htmlspecialcharsbx($arValues['PROPERTIES'][$arOrderProperty['ID']])?></textarea></td>
                    </tr>
                <?endif;?>
                <?if ($arOrderProperty['TYPE'] == 'B' && empty($arOrderProperty['SUBTYPE'])):?>
                    <tr>
                        <td width="40%">
                            <?=$arOrderProperty['REQUIRED'] == 'Y' ? '<b>' : ''?>
                            <?=$arOrderProperty['LANG'][LANGUAGE_ID]['NAME']?>
                            <?=$arOrderProperty['REQUIRED'] == 'Y' ? ' <span class="required" style="vertical-align: super; font-size: smaller;">*</span>:</b>' : ':'?>
                        </td>
                        <td width="60%">
                            <input type="hidden" name="PROPERTY_<?=$arOrderProperty['ID']?>" value="N" />
                            <input type="checkbox" name="PROPERTY_<?=$arOrderProperty['ID']?>" value="Y"<?=$arValues['PROPERTIES'][$arOrderProperty['ID']] == 'Y' ? ' checked="checked"' : ''?> />
                        </td>
                    </tr>
                <?endif;?>
                <?if ($arOrderProperty['TYPE'] == 'L' && $arOrderProperty['SUBTYPE'] == 'IBLOCK_ELEMENT'):?>
                    <?if (!empty($arOrderProperty['DATA']['IBLOCK_ID'])):?>
                        <?
                            $dbElements = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $arOrderProperty['DATA']['IBLOCK_ID']))
                        ?>
                        <tr>
                            <td width="40%">
                                <?=$arOrderProperty['REQUIRED'] == 'Y' ? '<b>' : ''?>
                                <?=$arOrderProperty['LANG'][LANGUAGE_ID]['NAME']?>
                                <?=$arOrderProperty['REQUIRED'] == 'Y' ? ' <span class="required" style="vertical-align: super; font-size: smaller;">*</span>:</b>' : ':'?>
                            </td>
                            <td width="60%">
                                <select name="PROPERTY_<?=$arOrderProperty['ID']?>">
                                    <option value=""><?=GetMessage('select.empty')?></option>
                                    <?while ($arElement = $dbElements->Fetch()):?>
                                        <option value="<?=$arElement['ID']?>"<?=$arValues['PROPERTIES'][$arOrderProperty['ID']] == $arElement['ID'] ? ' selected="selected"' : ''?>><?=htmlspecialcharsbx($arElement['NAME'])?></option>
                                    <?endwhile;?>
                                </select>
                            </td>
                        </tr>
                    <?endif;?>
                <?endif;?>
            <?endforeach;?>
        <?endif;?>
        <?if (!empty($arOrderPropertiesDelivery)):?>
            <?$oTabControl->BeginNextTab();?>
            <?foreach ($arOrderPropertiesDelivery as $arOrderProperty):?>
                <?if ($arOrderProperty['TYPE'] == 'S' && empty($arOrderProperty['SUBTYPE'])):?>
                    <tr class="delivery-property">
                        <td width="40%">
                            <?=$arOrderProperty['REQUIRED'] == 'Y' ? '<b>' : ''?>
                            <?=$arOrderProperty['LANG'][LANGUAGE_ID]['NAME']?>
                            <?=$arOrderProperty['REQUIRED'] == 'Y' ? ' <span class="required" style="vertical-align: super; font-size: smaller;">*</span>:</b>' : ':'?>
                        </td>
                        <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['PROPERTIES'][$arOrderProperty['ID']])?>" name="PROPERTY_<?=$arOrderProperty['ID']?>"/></td>
                    </tr>
                <?endif;?>
                <?if ($arOrderProperty['TYPE'] == 'S' && $arOrderProperty['SUBTYPE'] == 'TEXT'):?>
                    <tr class="delivery-property">
                        <td width="40%">
                            <?=$arOrderProperty['REQUIRED'] == 'Y' ? '<b>' : ''?>
                            <?=$arOrderProperty['LANG'][LANGUAGE_ID]['NAME']?>
                            <?=$arOrderProperty['REQUIRED'] == 'Y' ? ' <span class="required" style="vertical-align: super; font-size: smaller;">*</span>:</b>' : ':'?>
                        </td>
                        <td width="60%"><textarea name="PROPERTY_<?=$arOrderProperty['ID']?>" style="min-width: 200px; min-height: 60px;"><?=htmlspecialcharsbx($arValues['PROPERTIES'][$arOrderProperty['ID']])?></textarea></td>
                    </tr>
                <?endif;?>
                <?if ($arOrderProperty['TYPE'] == 'B' && empty($arOrderProperty['SUBTYPE'])):?>
                    <tr class="delivery-property">
                        <td width="40%">
                            <?=$arOrderProperty['REQUIRED'] == 'Y' ? '<b>' : ''?>
                            <?=$arOrderProperty['LANG'][LANGUAGE_ID]['NAME']?>
                            <?=$arOrderProperty['REQUIRED'] == 'Y' ? ' <span class="required" style="vertical-align: super; font-size: smaller;">*</span>:</b>' : ':'?>
                        </td>
                        <td width="60%">
                            <input type="hidden" name="PROPERTY_<?=$arOrderProperty['ID']?>" value="N" />
                            <input type="checkbox" name="PROPERTY_<?=$arOrderProperty['ID']?>" value="Y"<?=$arValues['PROPERTIES'][$arOrderProperty['ID']] == 'Y' ? ' checked="checked"' : ''?> />
                        </td>
                    </tr>
                <?endif;?>
                <?if ($arOrderProperty['TYPE'] == 'L' && $arOrderProperty['SUBTYPE'] == 'IBLOCK_ELEMENT'):?>
                    <?if (!empty($arOrderProperty['DATA']['IBLOCK_ID'])):?>
                        <?
                            $dbElements = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $arOrderProperty['DATA']['IBLOCK_ID']))
                        ?>
                        <tr class="delivery-property">
                            <td width="40%">
                                <?=$arOrderProperty['REQUIRED'] == 'Y' ? '<b>' : ''?>
                                <?=$arOrderProperty['LANG'][LANGUAGE_ID]['NAME']?>
                                <?=$arOrderProperty['REQUIRED'] == 'Y' ? ' <span class="required" style="vertical-align: super; font-size: smaller;">*</span>:</b>' : ':'?>
                            </td>
                            <td width="60%">
                                <select name="PROPERTY_<?=$arOrderProperty['ID']?>">
                                    <option value=""><?=GetMessage('select.empty')?></option>
                                    <?while ($arElement = $dbElements->Fetch()):?>
                                        <option value="<?=$arElement['ID']?>"<?=$arValues['PROPERTIES'][$arOrderProperty['ID']] == $arElement['ID'] ? ' selected="selected"' : ''?>><?=htmlspecialcharsbx($arElement['NAME'])?></option>
                                    <?endwhile;?>
                                </select>
                            </td>
                        </tr>
                    <?endif;?>
                <?endif;?>
            <?endforeach;?>
            <script type="text/javascript">
                var StartshopOrdersEdit = {};

                StartshopOrdersEdit.Form = {};
                StartshopOrdersEdit.Form.Selector = 'form#StartshopOrdersEdit';
                StartshopOrdersEdit.Fields = {};
                StartshopOrdersEdit.Fields.Delivery = {};
                StartshopOrdersEdit.Fields.Delivery.Selector = StartshopOrdersEdit.Form.Selector + ' select[name=DELIVERY]';
                StartshopOrdersEdit.Deliveries = <?=CUtil::PhpToJSObject($arDeliveries);?>;

                StartshopOrdersEdit.Fields.Delivery.Handle = function ($oObject) {
                    var $oDelivery = StartshopOrdersEdit.Deliveries[$oObject.val()];

                    if ($oDelivery !== undefined) {
                        if ($oDelivery['PROPERTIES'].length > 0) {
                            $(StartshopOrdersEdit.Form.Selector + ' .delivery-property').hide();

                            Startshop.Functions.forEach($oDelivery.PROPERTIES, function ($iArrayIndex, $iPropertyIndex) {
                                $(StartshopOrdersEdit.Form.Selector + ' [name="PROPERTY_' + $iPropertyIndex + '"]').parents('.delivery-property').first().show();
                            });

                            tabs.EnableTab('properties.delivery');
                        } else {
                            tabs.DisableTab('properties.delivery');
                        }
                    } else {
                        tabs.DisableTab('properties.delivery');
                    }
                };

                $(document).ready(function() {
                    StartshopOrdersEdit.Fields.Delivery.Handle($(StartshopOrdersEdit.Fields.Delivery.Selector));
                });

                $(StartshopOrdersEdit.Fields.Delivery.Selector).on('change', function () {
                    StartshopOrdersEdit.Fields.Delivery.Handle($(this));
                });
            </script>
        <?endif;?>
        <?$oTabControl->BeginNextTab();?>
        <tr>
            <td>
                <?$oList->DisplayList();?>
            </td>
        </tr>
        <?
            $oTabControl->Buttons(
                array(
                    "back_url" => $arLinks['BACK']
                )
            );
        ?>
        <?
            $oTabControl->End();
        ?>
    <?endif;?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>