<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");?>
<?
    global $USER, $APPLICATION;
    IncludeModuleLangFile(__FILE__);

    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

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

    $arLinks = array(
        'ADD' => "startshop_orders_edit.php?lang=".LANG."&action=add",
        'VIEW' => "startshop_orders_edit.php?lang=".LANG."&action=view&ID=#ID#",
        'EDIT' => "startshop_orders_edit.php?lang=".LANG."&action=edit&ID=#ID#",
        'DELETE' => "startshop_orders.php?lang=".LANG."&action=delete&ID=#ID#",
        'PAYED' => "startshop_orders.php?lang=".LANG."&action=payed&ID=#ID#",
        'UNPAYED' => "startshop_orders.php?lang=".LANG."&action=unpayed&ID=#ID#",
        'STATUS' => "startshop_orders.php?lang=".LANG."&action=status&ID=#ID#&STATUS=#STATUS#",
    );

    $APPLICATION->SetTitle(GetMessage('title'));

    $sAction = $_REQUEST['action'];

    if ($bRightsEdit) {
        if ($sAction == 'delete' && is_numeric($_REQUEST['ID']))
            CStartShopOrder::Delete($_REQUEST['ID']);

        if ($sAction == 'payed' && is_numeric($_REQUEST['ID']))
            CStartShopOrder::Update($_REQUEST['ID'], array('PAYED' => 'Y'));

        if ($sAction == 'unpayed' && is_numeric($_REQUEST['ID']))
            CStartShopOrder::Update($_REQUEST['ID'], array('PAYED' => 'N'));

        if ($sAction == 'status' && is_numeric($_REQUEST['ID']) && is_numeric($_REQUEST['STATUS']))
            CStartShopOrder::Update($_REQUEST['ID'], array('STATUS' => $_REQUEST['STATUS']));

        if (is_array($_REQUEST['ID'])) {
            if ($sAction == 'delete.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopOrder::Delete($iItemID);

            if ($sAction == 'payed.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopOrder::Update($iItemID, array('PAYED' => 'Y'));

            if ($sAction == 'unpayed.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopOrder::Update($iItemID, array('PAYED' => 'N'));
        }
    }

    $arUsers = CStartShopUtil::DBResultToArray(CUser::GetList($by = "timestamp_x", $order = "asc"), 'ID');
    $arSites = CStartShopUtil::DBResultToArray(CSite::GetList($by = "sort", $order = "asc"), 'ID');

    $arOrderStatuses = array();
    $dbOrderStatuses = CStartShopOrderStatus::GetList(array('SORT' => 'ASC'));

    while ($arOrderStatus = $dbOrderStatuses->Fetch())
        $arOrderStatuses[$arOrderStatus['ID']] = $arOrderStatus;

    $arOrderProperties = CStartShopUtil::DBResultToArray(CStartShopOrderProperty::GetList(array('SORT' => 'ASC')), 'ID');

    $arDeliveries = array();
    $dbDeliveries = CStartShopDelivery::GetList(array('SORT' => 'ASC'));

    while ($arDelivery = $dbDeliveries->Fetch())
        $arDeliveries[$arDelivery['ID']] = $arDelivery;

    $arPayments = array();
    $dbPayments = CStartShopPayment::GetList(array('SORT' => 'ASC'));

    while ($arPayment = $dbPayments->Fetch())
        $arPayments[$arPayment['ID']] = $arPayment;

    $arFilterFields = array(
        'find_id' => 'ID',
        'find_user' => 'USER',
        'find_sid' => 'SID',
        'find_status' => 'STATUS',
        'find_delivery' => 'DELIVERY',
        'find_payment' => 'PAYMENT',
        'find_payed' => 'PAYED',
        'find_date_create_from' => '>=DATE_CREATE',
        'find_date_create_to' => '<=DATE_CREATE'
    );

    $arFilter = array();

    foreach ($arFilterFields as $sFilterFieldKey => $sFilterField)
        if (!empty($_REQUEST[$sFilterFieldKey]) || is_numeric($_REQUEST[$sFilterFieldKey]))
            $arFilter[$sFilterField] = $_REQUEST[$sFilterFieldKey];

    if (!empty($arFilter['>=DATE_CREATE']))
        $arFilter['>=DATE_CREATE'] = date('Y-m-d H:i:s', strtotime($arFilter['>=DATE_CREATE']));

    if (!empty($arFilter['<=DATE_CREATE']))
        $arFilter['<=DATE_CREATE'] = date('Y-m-d H:i:s', strtotime($arFilter['<=DATE_CREATE']));

    $sListID = "startshop_orders_list";

    $sSortBy = "ID";
    $sOrderBy = "asc";
    $oSort = new CAdminSorting(
        $sListID,
        $sSortBy,
        $sOrderBy,
        $sListID.'_by',
        $sListID.'_orders'
    );

    $sSortBy = $_REQUEST[$sListID.'_by'];
    $sOrderBy = $_REQUEST[$sListID.'_orders'];

    $oFilter = new CAdminFilter($sListID.'_filter', array(
        GetMessage('table.header.id'),
        GetMessage('table.header.user'),
        GetMessage('table.header.sid'),
        GetMessage('table.header.status'),
        GetMessage('table.header.delivery'),
        GetMessage('table.header.payment'),
        GetMessage('table.header.payed'),
        GetMessage('table.header.date_create')
    ));

    $oList = new CAdminList($sListID, $oSort);
    $oList->InitFilter(array_keys($arFilterFields));
    $dbResult = CStartShopOrder::GetList(array($sSortBy => $sOrderBy), $arFilter);
    $dbResult = new CAdminResult($dbResult, $sListID);
    $dbResult->NavStart(20);
    $oList->NavText($dbResult->GetNavPrint(GetMessage('nav.title'), true));


    $arHeaders = array(
        array(
            "id" => "ID",
            "content" => GetMessage('table.header.id'),
            "sort" => "ID",
            "default" => true
        ),
        array(
            "id" => "SID",
            "content" => GetMessage('table.header.sid'),
            "sort" => "SID",
            "default" => true
        ),
        array(
            "id" => "USER",
            "content" => GetMessage('table.header.user'),
            "sort" => "USER",
            "default" => true
        ),
        array(
            "id" => "STATUS",
            "content" => GetMessage('table.header.status'),
            "sort" => "STATUS",
            "default" => true
        ),
        array(
            "id" => "DELIVERY",
            "content" => GetMessage('table.header.delivery'),
            "sort" => "DELIVERY",
            "default" => true
        ),
        array(
            "id" => "PAYMENT",
            "content" => GetMessage('table.header.payment'),
            "sort" => "PAYMENT",
            "default" => true
        ),
        array(
            "id" => "PAYED",
            "content" => GetMessage('table.header.payed'),
            "sort" => "PAYED",
            "default" => true
        ),
        array(
            "id" => "AMOUNT",
            "content" => GetMessage('table.header.amount'),
            "default" => true
        ),
        array(
            "id" => "DATE_CREATE",
            "content" => GetMessage('table.header.date_create'),
            "sort" => "DATE_CREATE",
            "default" => true
        )
    );

    foreach ($arOrderProperties as $arOrderProperty)
        $arHeaders[] = array(
            'id' => $arOrderProperty['CODE'].'_'.$arOrderProperty['SID'],
            'content' => '['.$arOrderProperty['SID'].'] '.$arOrderProperty['LANG'][LANGUAGE_ID]['NAME'],
            'default' => false
        );

    $oList->AddHeaders($arHeaders);

    if ($bRightsEdit)
        $oList->AddGroupActionTable(array(
            "delete.selected" => GetMessage('actions.group.delete'),
            "payed.selected" => GetMessage('actions.group.payed'),
            "unpayed.selected" => GetMessage('actions.group.unpayed')
        ));

    while ($arResultItem = $dbResult->GetNext()) {
        $arActions = array();

        $arActions[] = array(
            "ICON" => "",
            "TEXT" => GetMessage('actions.view'),
            "ACTION" => $oList->ActionRedirect(CStartShopUtil::ReplaceMacros($arLinks['VIEW'], $arResultItem))
        );

        if ($bRightsEdit) {
            $arActions[] = array(
                "ICON" => "edit",
                "TEXT" => GetMessage('actions.edit'),
                "ACTION" => $oList->ActionRedirect(CStartShopUtil::ReplaceMacros($arLinks['EDIT'], $arResultItem))
            );

            $arActions[] = array(
                "ICON" => "delete",
                "TEXT" => GetMessage('actions.delete'),
                "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['DELETE'], $arResultItem))
            );

            $arActions[] = array(
                "SEPARATOR" => "Y"
            );

            $arResultItemStatuses = CStartShopUtil::ArrayFilter($arOrderStatuses, function ($arStatus) {
                global $arResultItem;
                return $arStatus['SID'] == $arResultItem['SID'] && $arStatus['ID'] != $arResultItem['STATUS'];
            });

            if (!empty($arResultItemStatuses)) {
                $arAction = array(
                    "TEXT" => GetMessage('actions.status'),
                );

                foreach ($arResultItemStatuses as $arItemStatus)
                    $arAction['MENU'][] = array(
                        "TEXT" => $arItemStatus['LANG'][LANGUAGE_ID]['NAME'],
                        "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['STATUS'], array('ID' => $arResultItem['ID'], 'STATUS' => $arItemStatus['ID'])))
                    );

                $arActions[]  = $arAction;
                unset($arAction);
            }

            if ($arResultItem['PAYED'] == 'Y') {
                $arActions[] = array(
                    "TEXT" => GetMessage('actions.unpayed'),
                    "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['UNPAYED'], $arResultItem))
                );
            } else {
                $arActions[] = array(
                    "TEXT" => GetMessage('actions.payed'),
                    "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['PAYED'], $arResultItem))
                );
            }
        }

        if (!empty($arUsers[$arResultItem['USER']])) {
            if (!empty($arUsers[$arResultItem['USER']]['NAME']) && !empty($arUsers[$arResultItem['USER']]['LAST_NAME']) && !empty($arUsers[$arResultItem['USER']]['SECOND_NAME'])) {
                $sUser = '['.$arUsers[$arResultItem['USER']]['ID'].'] '.$arUsers[$arResultItem['USER']]['LAST_NAME'].' '.$arUsers[$arResultItem['USER']]['NAME'].' '. $arUsers[$arResultItem['USER']]['SECOND_NAME'];
            } else {
                $sUser = '['.$arUsers[$arResultItem['USER']]['ID'].'] '.$arUsers[$arResultItem['USER']]['LOGIN'];
            }
        } else {
            $sUser = GetMessage('select.empty');
        }

        $sStatus = $arOrderStatuses[$arResultItem["STATUS"]]["SID"] == $arResultItem["SID"] ?
            $arOrderStatuses[$arResultItem["STATUS"]]['LANG'][LANGUAGE_ID]['NAME'] :
            GetMessage('select.empty');

        $sDelivery = $arDeliveries[$arResultItem["DELIVERY"]]["SID"] == $arResultItem["SID"] ?
            $arDeliveries[$arResultItem["DELIVERY"]]['LANG'][LANGUAGE_ID]['NAME'] :
            GetMessage('select.empty');

        $sPayment = !empty($arPayments[$arResultItem["PAYMENT"]]) ?
            $arPayments[$arResultItem["PAYMENT"]]['LANG'][LANGUAGE_ID]['NAME'] :
            GetMessage('select.empty');

        $sPayed = $arResultItem["PAYED"] == 'Y' ?
            GetMessage('active.yes') :
            GetMessage('active.no');

        $sDateCreate = date("d.m.Y H:i:s", strtotime($arResultItem['DATE_CREATE']));
        $sAmount = CStartShopCurrency::ConvertAndFormatAsString(floatval($arResultItem["AMOUNT"]), $arResultItem['CURRENCY']);

        $arRowFields = array(
            "ID" => $arResultItem["ID"],
            "USER" => $sUser,
            "SID" => '['.$arResultItem["SID"].'] '.$arSites[$arResultItem["SID"]]['NAME'],
            "STATUS" => $sStatus,
            "DELIVERY" => $sDelivery,
            "PAYMENT" => $sPayment,
            "PAYED" => $sPayed,
            "AMOUNT" => $sAmount,
            "DATE_CREATE" => $sDateCreate
        );

        foreach ($arResultItem['PROPERTIES'] as $iResultItemPropertyID => $cResultItemPropertyValue) {
            $arOrderProperty = $arOrderProperties[$iResultItemPropertyID];
            if ($arOrderProperty['TYPE'] == 'S' && ($arOrderProperty['SUBTYPE'] == 'TEXT' || empty($arOrderProperty['SUBTYPE']))) {
                $arRowFields[$arOrderProperty['CODE'].'_'.$arOrderProperty['SID']] = $cResultItemPropertyValue;
            } else if ($arOrderProperty['TYPE'] == 'B' && empty($arOrderProperty['SUBTYPE'])) {
                $arRowFields[$arOrderProperty['CODE'].'_'.$arOrderProperty['SID']] = $cResultItemPropertyValue == "Y" ? GetMessage("active.yes") : GetMessage("active.no");
            } else if ($arOrderProperty['TYPE'] == 'L' && $arOrderProperty['SUBTYPE'] == 'IBLOCK_ELEMENT') {
                $arIBlockElement = CIBlockElement::GetByID($cResultItemPropertyValue)->Fetch();
                $arRowFields[$arOrderProperty['CODE'].'_'.$arOrderProperty['SID']] = $arIBlockElement['NAME'];
                unset($arIBlockElement);
            }
        }

        $oRow = &$oList->AddRow($arResultItem["ID"], $arRowFields);

        $oRow->AddActions($arActions);
    }

    $arContextMenu = array();

    if ($bRightsEdit)
        $arContextMenu[] = array(
            "TEXT" => GetMessage("actions.add"),
            "ICON" => "btn_new",
            "LINK" => $arLinks['ADD'],
            "TITLE" => GetMessage("actions.add")
        );

    $oList->AddAdminContextMenu($arContextMenu, true, true);
    $oList->CheckListMode();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<form name="find_form" method="POST">
    <?$oFilter->Begin();?>
    <tr>
        <td><?=GetMessage("table.header.id")?>:</td>
        <td><input type="text" name="find_id" value="<?=htmlspecialcharsbx($_REQUEST['find_id'])?>" /></td>
    </tr>
    <tr>
        <td><?=GetMessage("table.header.user")?>:</td>
        <td>
            <select name="find_user[]" multiple="multiple">
                <option value=""><?=GetMessage('select.empty')?></option>
                <?if (!is_array($_REQUEST['find_user'])) $_REQUEST['find_user'] = array();?>
                <?foreach ($arUsers as $arUser):?>
                    <option value="<?=$arUser['ID']?>"<?=in_array($arUser['ID'], $_REQUEST['find_user']) ? ' selected="selected"' : ''?>>
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
    <tr>
        <td><?=GetMessage("table.header.sid")?>:</td>
        <td>
            <select name="find_sid">
                <option value=""><?=GetMessage('select.empty')?></option>
                <?foreach ($arSites as $arSite):?>
                    <option value="<?=$arSite['ID']?>"<?=$arSite['ID'] == $_REQUEST['find_sid'] ? ' selected="selected"' : ''?>>[<?=htmlspecialcharsbx($arSite['ID'])?>] <?=htmlspecialcharsbx($arSite['NAME'])?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("table.header.status")?>:</td>
        <td>
            <select name="find_status[]" multiple="multiple">
                <option value=""><?=GetMessage('select.empty')?></option>
                <?if (!is_array($_REQUEST['find_status'])) $_REQUEST['find_status'] = array();?>
                <?foreach ($arOrderStatuses as $arOrderStatus):?>
                    <option value="<?=$arOrderStatus['ID']?>"<?=in_array($arOrderStatus['ID'], $_REQUEST['find_status']) ? ' selected="selected"' : ''?>>[<?=htmlspecialcharsbx($arOrderStatus['SID'])?>] <?=htmlspecialcharsbx($arOrderStatus['LANG'][LANGUAGE_ID]['NAME'])?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("table.header.delivery")?>:</td>
        <td>
            <select name="find_delivery">
                <option value=""><?=GetMessage('select.empty')?></option>
                <?foreach ($arDeliveries as $arDelivery):?>
                    <option value="<?=$arDelivery['ID']?>"<?=$arDelivery['ID'] == $_REQUEST['find_delivery'] ? ' selected="selected"' : ''?>>[<?=htmlspecialcharsbx($arDelivery['SID'])?>] <?=htmlspecialcharsbx($arDelivery['LANG'][LANGUAGE_ID]['NAME'])?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("table.header.payment")?>:</td>
        <td>
            <select name="find_payment">
                <option value=""><?=GetMessage('select.empty')?></option>
                <?foreach ($arPayments as $arPayment):?>
                    <option value="<?=$arPayment['ID']?>"<?=$arPayment['ID'] == $_REQUEST['find_payment'] ? ' selected="selected"' : ''?>><?=htmlspecialcharsbx($arPayment['LANG'][LANGUAGE_ID]['NAME'])?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("table.header.payed")?>:</td>
        <td><input type="checkbox" value="Y" name="find_payed"<?=$_REQUEST['find_payed'] == 'Y' ? ' checked="checked"' : ''?> /></td>
    </tr>
    <tr>
        <td><?=GetMessage("table.header.date_create")?></td>
        <td><?=CAdminCalendar::CalendarPeriod("find_date_create_from", "find_date_create_to", $_REQUEST['find_date_create_from'], $_REQUEST['find_date_create_to'])?></td>
    </tr>
    <?
        $oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
        $oFilter->End();
    ?>
</form>
<?$oList->DisplayList();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
