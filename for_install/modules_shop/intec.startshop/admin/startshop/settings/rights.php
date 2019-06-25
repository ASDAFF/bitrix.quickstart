<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");?>
<?
    global $USER, $APPLICATION;
    IncludeModuleLangFile(__FILE__);

    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

    if (!CModule::IncludeModule("iblock"))
        return;

    if (!CModule::IncludeModule("intec.startshop"))
        return;

    if (!$USER->IsAdmin()) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    $APPLICATION->SetTitle(GetMessage('title'));

    $bActionApply = !empty($_REQUEST['save']);
    $iGroup = intval($_REQUEST['group']);
    $arGroupCurrent = array();

    $oGroup = new CGroup;
    $arGroups = CStartShopUtil::DBResultToArray($oGroup->GetList($by = "c_sort", $order = "asc"), 'ID');

    unset($arGroups[1]);

    if (array_key_exists($iGroup, $arGroups)) {
        $arGroupCurrent = $arGroups[$iGroup];
    } else {
        reset($arGroups);
        $arGroupCurrent = current($arGroups);
    }

    $arParameters = array(
        'STARTSHOP_SETTINGS_CATALOG' => array(
            "TYPE" => "LIST",
            "NAME" => GetMessage('sections.catalog'),
            "VALUES" => array(
                "" => GetMessage('rights.NONE'),
                "V" => GetMessage('rights.V'),
                "E.V" => GetMessage('rights.E.V')
            )
        ),
        'STARTSHOP_SETTINGS_SITES' => array(
            "TYPE" => "LIST",
            "NAME" => GetMessage('sections.sites'),
            "VALUES" => array(
                "" => GetMessage('rights.NONE'),
                "V" => GetMessage('rights.V'),
                "E.V" => GetMessage('rights.E.V')
            )
        ),
        'STARTSHOP_SETTINGS_ORDER_PROPERTY' => array(
            "TYPE" => "LIST",
            "NAME" => GetMessage('sections.order.properties'),
            "VALUES" => array(
                "" => GetMessage('rights.NONE'),
                "V" => GetMessage('rights.V'),
                "E.V" => GetMessage('rights.E.V')
            )
        ),
        'STARTSHOP_SETTINGS_ORDER_STATUS' => array(
            "TYPE" => "LIST",
            "NAME" => GetMessage('sections.order.statuses'),
            "VALUES" => array(
                "" => GetMessage('rights.NONE'),
                "V" => GetMessage('rights.V'),
                "E.V" => GetMessage('rights.E.V')
            )
        ),
        'STARTSHOP_SETTINGS_PRICE' => array(
            "TYPE" => "LIST",
            "NAME" => GetMessage('sections.price'),
            "VALUES" => array(
                "" => GetMessage('rights.NONE'),
                "V" => GetMessage('rights.V'),
                "E.V" => GetMessage('rights.E.V')
            )
        ),
        'STARTSHOP_SETTINGS_DELIVERY' => array(
            "TYPE" => "LIST",
            "NAME" => GetMessage('sections.delivery'),
            "VALUES" => array(
                "" => GetMessage('rights.NONE'),
                "V" => GetMessage('rights.V'),
                "E.V" => GetMessage('rights.E.V')
            )
        ),
        'STARTSHOP_SETTINGS_CURRENCY' => array(
            "TYPE" => "LIST",
            "NAME" => GetMessage('sections.currency'),
            "VALUES" => array(
                "" => GetMessage('rights.NONE'),
                "V" => GetMessage('rights.V'),
                "E.V" => GetMessage('rights.E.V')
            )
        ),
        'STARTSHOP_SETTINGS_PAYMENT' => array(
            "TYPE" => "LIST",
            "NAME" => GetMessage('sections.payment'),
            "VALUES" => array(
                "" => GetMessage('rights.NONE'),
                "V" => GetMessage('rights.V'),
                "E.V" => GetMessage('rights.E.V')
            )
        ),
        'STARTSHOP_SETTINGS_1C' => array(
            "TYPE" => "LIST",
            "NAME" => GetMessage('sections.1c'),
            "VALUES" => array(
                "" => GetMessage('rights.NONE'),
                "V" => GetMessage('rights.V'),
                "E.V" => GetMessage('rights.E.V')
            )
        ),
        'STARTSHOP_ORDERS' => array(
            "TYPE" => "LIST",
            "NAME" => GetMessage('sections.orders'),
            "VALUES" => array(
                "" => GetMessage('rights.NONE'),
                "V" => GetMessage('rights.V'),
                "E.V" => GetMessage('rights.E.V')
            )
        ),
        'STARTSHOP_FORMS' => array(
            "TYPE" => "LIST",
            "NAME" => GetMessage('sections.forms'),
            "VALUES" => array(
                "" => GetMessage('rights.NONE'),
                "V" => GetMessage('rights.V'),
                "E.V" => GetMessage('rights.E.V')
            )
        )
    );

    if ($bActionApply) {
        $arParameters = CStartShopToolsAdmin::SaveParameters($arParameters, function ($sKey, $arParameter) {
            global $arGroupCurrent;
            CStartShopUtilsRights::SetRights($arGroupCurrent['ID'], $sKey, explode('.', $_REQUEST[$sKey]));
        });

        $sNotify = GetMessage('messages.notify.saved');
    }

    foreach ($arParameters as $sKey => $arParameter)
        $arParameters[$sKey]['VALUE'] = implode(
            '.',
            CStartShopUtilsRights::GetRights(
                $arGroupCurrent['ID'],
                $sKey
            )
        );

    $oTabs = new CAdminTabControl("Rights", array(
        array(
            "DIV" => "Rights",
            "TAB" => GetMessage('title'),
            "TITLE" => GetMessage('tabs.common', array('#GROUP#' => $arGroupCurrent['NAME']))
        )
    ));
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<form>
    <select onchange="window.location='<?=$APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>&group='+this[this.selectedIndex].value;">
        <?foreach ($arGroups as $arGroup):?>
            <option value="<?=$arGroup['ID']?>"<?=$arGroupCurrent['ID'] == $arGroup['ID'] ? ' selected="selected"' : ''?>><?=htmlspecialcharsbx('['.$arGroup['ID'].'] '.$arGroup['NAME'])?></option>
        <?endforeach;?>
    </select>
</form>
<br />
<form method="POST">
    <?$oTabs->Begin();?>
    <?$oTabs->BeginNextTab();?>
        <?CStartShopToolsAdmin::DrawParameters($arParameters, '<tr><td width="50%" class="adm-detail-content-cell-l"><label for="#KEY#">#NAME#:</label></td><td width="50%" class="adm-detail-content-cell-r">#CONTROL#</td></tr>') ?>
    <?$oTabs->Buttons();?>
        <input class="adm-btn-save" name="save" type="submit" value="<?=GetMessage('buttons.save')?>" />
    <?$oTabs->End();?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
