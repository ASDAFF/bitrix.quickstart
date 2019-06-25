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
        'STARTSHOP_SETTINGS_CATALOG',
        'V'
    ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_CATALOG',
        'E'
    ) || $USER->IsAdmin();

    if (!$bRightsEdit || !$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

    $arLinks = array(
        'ADD' => "/bitrix/admin/startshop_settings_catalog_edit.php?lang=".LANG."&action=add",
        'EDIT' => "/bitrix/admin/startshop_settings_catalog_edit.php?lang=".LANG."&action=edit&ID=#ID#",
        'BACK' => "/bitrix/admin/startshop_settings_catalog.php?lang=".LANG
    );

    $arItem = array();
    $arOffersProperties = array();

    $bActionSave = !empty($_REQUEST['save']);
    $bActionApply = !empty($_REQUEST['apply']);

    $arActions = array('add', 'edit', 'ajax');
    $sAction = $_REQUEST['action'];

    $sError = null;
    $sNotify = null;

    if (!in_array($sAction, $arActions)) {
        LocalRedirect($arLinks['BACK']);
        die();
    }

    $arValues = array();
    $arValues['IBLOCK'] = intval($_REQUEST['IBLOCK']);
    $arValues['USE_QUANTITY'] = $_REQUEST['USE_QUANTITY'] == "Y" ? 1 : 0;
    $arValues['OFFERS_IBLOCK'] = intval($_REQUEST['OFFERS_IBLOCK']);
    $arValues['OFFERS_LINK_PROPERTY'] = intval($_REQUEST['OFFERS_LINK_PROPERTY']);
    $arValues['OFFERS_PROPERTIES'] = $_REQUEST['OFFERS_PROPERTIES'];

    if (!is_array($arValues['OFFERS_PROPERTIES']))
        $arValues['OFFERS_PROPERTIES'] = array();

    $fOffersPropertiesLinkFilter = function ($arProperty) {
        if ($arProperty['PROPERTY_TYPE'] == 'E' && $arProperty['USER_TYPE'] == null && $arProperty['MULTIPLE'] == "N")
            return true;

        return false;
    };

    $fOffersPropertiesAvailableFilter = function ($arProperty) {
        global $arValues;

        if ($arValues['OFFERS_LINK_PROPERTY'] == $arProperty['ID'])
            return false;

        if ($arProperty['PROPERTY_TYPE'] == 'L' && $arProperty['USER_TYPE'] == null && $arProperty['MULTIPLE'] == "N")
            return true;

        if ($arProperty['PROPERTY_TYPE'] == 'S' && $arProperty['USER_TYPE'] == 'directory' && $arProperty['MULTIPLE'] == "N")
            return true;

        return false;
    };

    $arContextMenu = array(
        array(
            "TEXT" => GetMessage("title.buttons.back"),
            "ICON" => "btn_list",
            "LINK" => $arLinks['BACK']
        ),
        array(
            "TEXT" => GetMessage("title.buttons.add"),
            "ICON" => "btn_new",
            "LINK" => $arLinks['ADD'],
        )
    );

    $arTabs = array(
        array(
            "DIV" => "common",
            "TAB" => GetMessage("tabs.common"),
            "ICON" => "catalog",
            "TITLE" => GetMessage("tabs.common")
        ),
        array(
            "DIV" => "offers",
            "TAB" => GetMessage("tabs.offers"),
            "ICON" => "catalog",
            "TITLE" => GetMessage("tabs.offers")
        )
    );

    $oContextMenu = new CAdminContextMenu($arContextMenu);
    $oTabControl = new CAdminTabControl("tabs", $arTabs);

    if ($sAction == 'ajax') {
        $sAjaxAction = $_POST['ajax_action'];
        $arResponse = array("Error" => true);

        if ($sAjaxAction == 'updateOffersFields') {
            $arResponse['Error'] = false;
            $arResponse['Properties'] = array();
            $iOffersIBlock = intval($_POST['ajax_data_iblock']);
            $arProperties = CStartShopUtil::DBResultToArray(CIBlockProperty::GetList(array("ID" => "ASC"), array("IBLOCK_ID" => $iOffersIBlock)), 'ID');

            $arResponse['Properties'] = CStartShopUtil::ArrayFilter(
                $arProperties,
                $fOffersPropertiesAvailableFilter,
                STARTSHOP_UTIL_ARRAY_FILTER_USE_VALUE
            );

            $arResponse['PropertiesLink'] = CStartShopUtil::ArrayFilter(
                $arProperties,
                $fOffersPropertiesLinkFilter,
                STARTSHOP_UTIL_ARRAY_FILTER_USE_VALUE
            );
        }

        if ($sAjaxAction == "createOffersIBlockLinkProperty") {
            $iOffersIBlock = intval($_POST['ajax_data_iblock']);

            if (!empty($iOffersIBlock)) {
                $arProperty = CIBlockProperty::GetList(array(), array(
                    "IBLOCK_ID" => $iOffersIBlock,
                    "CODE" => "STARTSHOP_LINK")
                )->Fetch();

                $oProperty = new CIBlockProperty();

                if (!empty($arProperty))
                    $oProperty->Delete($arProperty['ID']);

                $iPropertyID = $oProperty->Add(array(
                    "IBLOCK_ID" => $iOffersIBlock,
                    "CODE" => "STARTSHOP_LINK",
                    "NAME" => GetMessage("fields.offers.link.name"),
                    "PROPERTY_TYPE" => "E"
                ));

                if (!empty($iPropertyID)) {
                    $arResponse['Error'] = false;
                    $arResponse['Property'] = $iPropertyID;
                }
            }
        }

        echo \Bitrix\Main\Web\Json::encode($arResponse);
        die();
    }

    if ($sAction == 'add') {
        $APPLICATION->SetTitle(GetMessage('title.add'));

        $arOffersProperties = CStartShopUtil::DBResultToArray(CIBlockProperty::GetList(array("ID" => "ASC"), array("IBLOCK_ID" => $arValues['OFFERS_IBLOCK'])), 'ID');

        if ($bActionSave || $bActionApply)
            if (!empty($arValues['IBLOCK']) && is_numeric($arValues['IBLOCK'])) {
                if ($arValues['IBLOCK'] != $arValues['OFFERS_IBLOCK']) {
                    $arIBlock = CIBlock::GetByID($arValues['IBLOCK'])->Fetch();

                    if (!empty($arIBlock)) {
                        $iItemID = CStartShopCatalog::Add($arValues);

                        if ($iItemID) {
                            if ($bActionSave) LocalRedirect($arLinks['BACK']);
                            if ($bActionApply) LocalRedirect(CStartShopUtil::ReplaceMacros($arLinks['EDIT'], array("ID" => $iItemID)).'&ADDED=Y');
                            die();
                        }

                        $sError = GetMessage('messages.warning.exists');
                    } else {
                        $sError = GetMessage('messages.warning.iblock_not_exists');
                    }
                } else {
                    $sError = GetMessage("messages.error.iblocks_equal");
                }
            } else {
                $arFields = array();

                if (empty($arValues['IBLOCK'])) $arFields[] = GetMessage('fields.iblock');

                $sError = GetMessage('messages.warning.empty_fields', array(
                    '#FIELDS#' => '\''.implode('\', \'', $arFields).'\''
                ));

                unset($arFields);
            }
    }

    if ($sAction == 'edit') {
        $arItem = CStartShopCatalog::GetByIBlock($_REQUEST['ID'])->GetNext();

        if ($_REQUEST['ADDED'] == 'Y')
            $sNotify = GetMessage('messages.notify.added');

        if (empty($arItem)) {
            LocalRedirect($arLinks['BACK']);
            die();
        }

        if (!CStartShopCatalog::IsValid($arItem['IBLOCK']))
            $sError = GetMessage('messages.warning.iblock_not_exists');

        if ($bActionSave || $bActionApply) {
            if ($arValues['OFFERS_IBLOCK'] != $arValues['IBLOCK']) {
                $iUpdated = CStartShopCatalog::Update($arItem['IBLOCK'], $arValues);

                if ($iUpdated !== false) {
                    if ($bActionSave) {
                        LocalRedirect($arLinks['BACK']);
                        die();
                    }

                    if ($bActionApply) {
                        LocalRedirect(CStartShopUtil::ReplaceMacros($arLinks['EDIT'], array('ID' => $iUpdated)).'&UPDATED=Y&'.$oTabControl->ActiveTabParam());
                        die();
                    }
                } else {
                    $sError = GetMessage("messages.error.saving");
                }
            } else {
                $sError = GetMessage("messages.error.iblocks_equal");
            }


            $arItem = CStartShopCatalog::GetByIBlock($_REQUEST['ID'])->GetNext();
        }

        if ($_REQUEST['UPDATED'] == 'Y')
            $sNotify = GetMessage('messages.notify.saved');

        $arValues['IBLOCK'] = intval($arItem['IBLOCK']);
        $arValues['USE_QUANTITY'] = (bool)intval($arItem['USE_QUANTITY']);
        $arValues['OFFERS_IBLOCK'] = intval($arItem['OFFERS_IBLOCK']);
        $arValues['OFFERS_LINK_PROPERTY'] = intval($arItem['OFFERS_LINK_PROPERTY']);
        $arValues['OFFERS_PROPERTIES'] = $arItem['OFFERS_PROPERTIES'];

        $arOffersProperties = CStartShopUtil::DBResultToArray(CIBlockProperty::GetList(array("ID" => "ASC"), array("IBLOCK_ID" => $arValues['OFFERS_IBLOCK'])), 'ID');

        $APPLICATION->SetTitle(GetMessage('title.edit'));
    }

    $arCatalogs = array();
    $arOffersCatalogs = array();

    $dbCatalogs = CStartShopCatalog::GetList(array('IBLOCK' => 'ASC'));

    while ($arCatalog = $dbCatalogs->Fetch()) {
        $arCatalogs[$arCatalog['IBLOCK']] = $arCatalog;

        if (!empty($arCatalog['OFFERS_IBLOCK']))
            $arOffersCatalogs[$arCatalog['OFFERS_IBLOCK']] = $arCatalog;
    }

    $arIBlocks = CStartShopUtil::DBResultToArray(CIBlock::GetList(array("SORT" => "ASC")), 'ID');

    $arAvailableIBlocks = $arIBlocks;
    $arAvailableOffersIBlocks = $arIBlocks;

    foreach ($arCatalogs as $arCatalog) {
        if ($arCatalog['IBLOCK'] != $arItem['IBLOCK'])
            unset($arAvailableIBlocks[$arCatalog['IBLOCK']]);

        if ($arCatalog['OFFERS_IBLOCK'] != $arItem['OFFERS_IBLOCK'])
            unset($arAvailableOffersIBlocks[$arCatalog['OFFERS_IBLOCK']]);

        unset($arAvailableIBlocks[$arCatalog['OFFERS_IBLOCK']]);
        unset($arAvailableOffersIBlocks[$arCatalog['IBLOCK']]);
    }

    $arOffersPropertiesLink = CStartShopUtil::ArrayFilter($arOffersProperties, $fOffersPropertiesLinkFilter, STARTSHOP_UTIL_ARRAY_FILTER_USE_VALUE);
    $arOffersPropertiesAvailable = CStartShopUtil::ArrayFilter($arOffersProperties, $fOffersPropertiesAvailableFilter, STARTSHOP_UTIL_ARRAY_FILTER_USE_VALUE);
?>
<?require_once($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");?>
<?
    $oContextMenu->Show();

    if (!empty($sError))
        CAdminMessage::ShowMessage($sError);

    if (!empty($sNotify) && empty($sError))
        CAdminMessage::ShowNote($sNotify);
?>
<form method="POST" id="StartShopCatalogEdit">
    <?
        $oTabControl->Begin();
        $oTabControl->BeginNextTab();
    ?>
    <?if ($sAction == 'add'):?>
        <input type="hidden" name="ADDED" value="Y" />
    <?endif;?>
    <tr>
        <td width="40%"><b><?=GetMessage("fields.iblock")?>:</b></td>
        <td width="60%">
            <select name="IBLOCK">
                <?foreach ($arAvailableIBlocks as $arIBlock):?>
                    <option value="<?=$arIBlock['ID']?>"<?=$arIBlock['ID'] == $arValues['IBLOCK'] ? ' selected="selected"' : ''?>><?='['.htmlspecialcharsbx($arIBlock['ID']).'] '.htmlspecialcharsbx($arIBlock['NAME'])?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.use_quantity")?>:</td>
        <td width="60%"><input type="checkbox" value="Y" name="USE_QUANTITY"<?=$arValues['USE_QUANTITY'] ? ' checked="checked"' : ''?>/></td>
    </tr>
    <?
        $oTabControl->BeginNextTab();
    ?>
    <tr>
        <td width="40%"><?=GetMessage("fields.offers.iblock")?>:</td>
        <td width="60%">
            <select name="OFFERS_IBLOCK">
                <option value=""><?=GetMessage('select.empty')?></option>
                <?foreach ($arAvailableOffersIBlocks as $arIBlock):?>
                    <option value="<?=$arIBlock['ID']?>"<?=$arIBlock['ID'] == $arValues['OFFERS_IBLOCK'] ? ' selected="selected"' : ''?>><?='['.htmlspecialcharsbx($arIBlock['ID']).'] '.htmlspecialcharsbx($arIBlock['NAME'])?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.offers.link")?>:</td>
        <td width="60%">
            <select name="OFFERS_LINK_PROPERTY">
                <option value=""><?=GetMessage('select.empty')?></option>
                <?foreach ($arOffersPropertiesLink as $arOffersProperty):?>
                    <option value="<?=$arOffersProperty['ID']?>"<?=$arOffersProperty['ID'] == $arValues['OFFERS_LINK_PROPERTY'] ? ' selected="selected"' : ''?>><?='['.htmlspecialcharsbx($arOffersProperty['CODE']).'] '.htmlspecialcharsbx($arOffersProperty['NAME'])?></option>
                <?endforeach;?>
            </select>
            <a class="OFFERS_LINK_PROPERTY_CREATE" style="margin-left: 10px; cursor: pointer;<?=!empty($arOffersPropertiesLink) ? ' display: none;' : ''?>">
                <?=GetMessage('fields.offers.link.create')?>
            </a>
        </td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.offers.properties")?>:</td>
        <td width="60%">
            <select name="OFFERS_PROPERTIES[]" multiple="multiple" size="10">
                <option value=""><?=GetMessage('select.empty')?></option>
                <?foreach ($arOffersPropertiesAvailable as $arOffersProperty):?>
                    <option value="<?=$arOffersProperty['ID']?>"<?=in_array($arOffersProperty['ID'], $arValues['OFFERS_PROPERTIES']) ? ' selected="selected"' : ''?>><?='['.htmlspecialcharsbx($arOffersProperty['CODE']).'] '.htmlspecialcharsbx($arOffersProperty['NAME'])?></option>
                <?endforeach;?>
            </select>
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
</form>
<script type="text/javascript">
    $(document).ready(function() {
        var $oRoot = $('#StartShopCatalogEdit');
        var $oOffers = {};

        $oOffers.Url = <?=CUtil::PhpToJSObject($APPLICATION->GetCurPage())?>;
        $oOffers.Fields = {};
        $oOffers.Buttons = {};
        $oOffers.Buttons.LinkProperty = {};
        $oOffers.Fields.IBlock = $oRoot.find('select[name=OFFERS_IBLOCK]');
        $oOffers.Fields.LinkProperty = $oRoot.find('select[name=OFFERS_LINK_PROPERTY]');
        $oOffers.Fields.Properties = $oRoot.find('select[name=OFFERS_PROPERTIES\\[\\]]');
        $oOffers.Buttons.LinkProperty.Create = $oRoot.find('a.OFFERS_LINK_PROPERTY_CREATE');

        $oOffers.IBlock = <?=CUtil::PhpToJSObject($arValues['OFFERS_IBLOCK'])?>;
        $oOffers.LinkProperty = <?=CUtil::PhpToJSObject($arValues['OFFERS_LINK_PROPERTY'])?>;
        $oOffers.Properties = <?=CUtil::PhpToJSObject($arOffersProperties)?>;
        $oOffers.PropertiesLink = <?=CUtil::PhpToJSObject($arOffersPropertiesLink)?>;

        $oOffers.Fields.IBlock.change(function() {
            $oOffers.IBlock = $(this).val();
            UpdateOffersFields($oOffers.IBlock);
        });

        $oOffers.Fields.LinkProperty.change(function() {
            $oOffers.LinkProperty = $(this).val();
            UpdateOffersPropertiesField();
        });

        $oOffers.Buttons.LinkProperty.Create.click(function () {
            $.post(
                $oOffers.Url,
                {"action":"ajax", "ajax_action":"createOffersIBlockLinkProperty", "ajax_data_iblock": $oOffers.IBlock},
                function ($sResponse) {
                    if ($sResponse === undefined)
                        return;

                    $oJson = $.parseJSON($sResponse);

                    if (!$oJson.Error) {
                        $oOffers.LinkProperty = $oJson.Property;
                        UpdateOffersFields();
                    }
                }
            )
        });

        function UpdateOffersFields() {
            $.post(
                $oOffers.Url,
                {"action":"ajax", "ajax_action":"updateOffersFields", "ajax_data_iblock": $oOffers.IBlock},
                function ($sResponse) {
                    if ($sResponse === undefined)
                        return;

                    $oJson = $.parseJSON($sResponse);

                    if (!$oJson.Error) {
                        $oOffers.Properties = $oJson.Properties;
                        $oOffers.PropertiesLink = $oJson.PropertiesLink;
                        UpdateOffersLinkPropertyField();
                        UpdateOffersPropertiesField();
                    }
                }
            );
        }

        function UpdateOffersLinkPropertyField() {
            $oOffers.Fields.LinkProperty.find('option').remove();
            var $iLinkPropertiesCount = 0;

            $oOffers.Fields.LinkProperty.append(
                $('<option></option>')
                    .attr('value', '')
                    .text(<?=CUtil::PhpToJSObject(GetMessage('select.empty'))?>)
            );

            Startshop.Functions.forEach($oOffers.PropertiesLink, function($iPropertyID, $oProperty) {
                var $oOption = $('<option></option>').attr('value', $iPropertyID).text('[' + $oProperty['CODE'] + '] ' + $oProperty['NAME']);

                if ($oOffers.LinkProperty == $iPropertyID)
                    $oOption.attr('selected', 'selected');

                $oOffers.Fields.LinkProperty.append($oOption);
                $iLinkPropertiesCount++;
            });

            if ($iLinkPropertiesCount > 0 || $oOffers.IBlock == 0)  {
                $oOffers.Buttons.LinkProperty.Create.css('display', 'none');
            } else {
                $oOffers.Buttons.LinkProperty.Create.css('display', '');
            }

            $oOffers.Fields.LinkProperty.focus();
        }

        function UpdateOffersPropertiesField() {
            $oOffers.Fields.Properties.find('option').remove();

            $oOffers.Fields.Properties.append(
                $('<option></option>')
                    .attr('value', '')
                    .text(<?=CUtil::PhpToJSObject(GetMessage('select.empty'))?>)
            );

            Startshop.Functions.forEach($oOffers.Properties, function($iPropertyID, $oProperty) {
                if ($iPropertyID != $oOffers.LinkProperty)
                    $oOffers.Fields.Properties.append(
                        $('<option></option>')
                            .attr('value', $iPropertyID)
                            .text('[' + $oProperty['CODE'] + '] ' + $oProperty['NAME'])
                    );
            });

            $oOffers.Fields.LinkProperty.focus();
        }
    });
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
