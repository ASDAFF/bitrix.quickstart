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
        'STARTSHOP_SETTINGS_1C',
        'V'
    ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_1C',
        'E'
    ) || $USER->IsAdmin();

    if (!$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    $APPLICATION->SetTitle(GetMessage('title'));

    $arIBlockTypes = array("" => GetMessage("select.empty"));
    $arIBlockTypes = array_merge($arIBlockTypes, CIBlockParameters::GetIBlockTypes());

    $bActionApply = !empty($_REQUEST['save']);
    $sError = null;
    $sNotify = null;

    $arSections = array(
        "EXCHANGE" => array(
            "NAME" => GetMessage('sections.exchange'),
            "PARAMETERS" => array(
                "1C_EXCHANGE_ALLOW" => array(
                    "TYPE" => "CHECKBOX",
                    "NAME" => GetMessage('sections.exchange.allow'),
                    "DEFAULT" => "Y"
                ),
                "1C_EXCHANGE_FILE_SIZE" => array(
                    "TYPE" => "STRING",
                    "NAME" => GetMessage('sections.exchange.file_size'),
                    "DEFAULT" => "1048576"
                ),
                "1C_EXCHANGE_IBLOCK_TYPE" => array(
                    "TYPE" => "LIST",
                    "NAME" => GetMessage('sections.exchange.iblock_type'),
                    "VALUES" => $arIBlockTypes
                ),
                "1C_EXCHANGE_IBLOCK_SYNCHRONIZE_NAME" => array(
                    "TYPE" => "CHECKBOX",
                    "NAME" => GetMessage('sections.exchange.iblock_synchronize_name'),
                    "DEFAULT" => "Y"
                ),
                "1C_EXCHANGE_IBLOCK_SECTION_SYNCHRONIZE_NAME" => array(
                    "TYPE" => "CHECKBOX",
                    "NAME" => GetMessage('sections.exchange.iblock_section_synchronize_name'),
                    "DEFAULT" => "Y"
                ),
                "1C_EXCHANGE_IBLOCK_SECTION_ACTION" => array(
                    "TYPE" => "LIST",
                    "NAME" => GetMessage('sections.exchange.iblock_section_action'),
                    "DEFAULT" => "NOTHING",
                    "VALUES" => array(
                        "0" => GetMessage('sections.exchange.iblock_section_action.nothing'),
                        "1" => GetMessage('sections.exchange.iblock_section_action.deactivate'),
                        "2" => GetMessage('sections.exchange.iblock_section_action.delete')
                    )
                ),
                "1C_EXCHANGE_IBLOCK_PROPERTY_SYNCHRONIZE_NAME" => array(
                    "TYPE" => "CHECKBOX",
                    "NAME" => GetMessage('sections.exchange.iblock_property_synchronize_name'),
                    "DEFAULT" => "Y"
                ),
                "1C_EXCHANGE_IBLOCK_PROPERTY_ACTION" => array(
                    "TYPE" => "LIST",
                    "NAME" => GetMessage('sections.exchange.iblock_property_action'),
                    "DEFAULT" => "NOTHING",
                    "VALUES" => array(
                        "0" => GetMessage('sections.exchange.iblock_property_action.nothing'),
                        "1" => GetMessage('sections.exchange.iblock_property_action.deactivate'),
                        "2" => GetMessage('sections.exchange.iblock_property_action.delete')
                    )
                ),
                "1C_EXCHANGE_IBLOCK_ELEMENT_SYNCHRONIZE_NAME" => array(
                    "TYPE" => "CHECKBOX",
                    "NAME" => GetMessage('sections.exchange.iblock_element_synchronize_name'),
                    "DEFAULT" => "Y"
                ),
                "1C_EXCHANGE_IBLOCK_ELEMENT_ACTION" => array(
                    "TYPE" => "LIST",
                    "NAME" => GetMessage('sections.exchange.iblock_element_action'),
                    "DEFAULT" => "NOTHING",
                    "VALUES" => array(
                        "0" => GetMessage('sections.exchange.iblock_element_action.nothing'),
                        "1" => GetMessage('sections.exchange.iblock_element_action.deactivate'),
                        "2" => GetMessage('sections.exchange.iblock_element_action.delete')
                    )
                ),
                "1C_EXCHANGE_IBLOCK_ELEMENT_IMPORT_LENGTH" => array(
                    "TYPE" => "STRING",
                    "NAME" => GetMessage('sections.exchange.iblock_element_import_length'),
                    "DEFAULT" => "20"
                )
            )
        )
    );

    foreach ($arSections as $sSectionKey => $arSection)
        foreach ($arSection['PARAMETERS'] as $sParameterKey => $arParameter)
            $arSections[$sSectionKey]['PARAMETERS'][$sParameterKey]['VALUE'] = isset($_REQUEST[$sParameterKey]) ? $_REQUEST[$sParameterKey] : CStartShopVariables::Get($sParameterKey, $arParameter['DEFAULT']);

    if (!$bRightsEdit)
        $sError = GetMessage('messages.error.rights');

    if ($bActionApply)
        if ($bRightsEdit){
            foreach ($arSections as $sSectionKey => $arSection)
                $arSections[$sSectionKey]['PARAMETERS'] = CStartShopToolsAdmin::SaveParameters($arSection['PARAMETERS'], function($sKey, $arParameter) {
                    CStartShopVariables::Set($sKey, $arParameter['VALUE']);
                });

            $sNotify = GetMessage('messages.notify.saved');
        }

    $oTabs = new CAdminTabControl(
        "tabs",
        array(
            array(
                "DIV" => "1C",
                "TAB" => GetMessage('tabs.1c'),
                "TITLE" => GetMessage('tabs.1c')
            )
        )
    );
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
    <form method="POST">
        <?
            if (!empty($sError))
                CAdminMessage::ShowMessage($sError);

            if (!empty($sNotify) && empty($sError))
                CAdminMessage::ShowNote($sNotify);

            $oTabs->Begin();
            $oTabs->BeginNextTab();
        ?>
        <?CStartShopToolsAdmin::DrawSections(
            $arSections,
            '<tr class="heading"><td colspan="2">#NAME#</td></tr>#CONTENT#',
            '<tr><td width="50%" class="adm-detail-content-cell-l"><label for="#KEY#">#NAME#:</label></td><td width="50%" class="adm-detail-content-cell-r">#CONTROL#</td></tr>'
        );?>
        <?if ($bRightsEdit):?>
            <?$oTabs->Buttons();?>
            <input class="adm-btn-save" name="save" type="submit" value="<?=GetMessage('buttons.save')?>" />
        <?endif;?>
        <?
            $oTabs->End();
        ?>
    </form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>