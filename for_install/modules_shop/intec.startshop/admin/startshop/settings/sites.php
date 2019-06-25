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
        'STARTSHOP_SETTINGS_SITES',
        'V'
    ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_SITES',
        'E'
    ) || $USER->IsAdmin();

    if (!$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }
	
	$APPLICATION->SetTitle(GetMessage('title'));

    $bActionApply = !empty($_REQUEST['save']);
    $sError = null;
    $sNotify = null;

    $arUserGroups = array("" => GetMessage("select.empty"));
    $dbUserGroups = CGroup::GetList($by = "c_sort", $order = "asc");

    while ($arUserGroup = $dbUserGroups->Fetch())
        $arUserGroups[$arUserGroup['ID']] = '['.$arUserGroup['ID'].'] '.$arUserGroup['NAME'];

	$arSitesTabs = array();
	$arSites = array();
	$dbSites = CSite::GetList($by = "sort", $order = "asc");
	
	while ($arSite = $dbSites->Fetch())
	{
		$arSites[] = $arSite;
		$arSiteTab = array();
		$arSiteTab["DIV"] = $arSite['ID'];
		$arSiteTab["TAB"] = $arSite["NAME"];
		$arSiteTab["TITLE"] = $arSite["NAME"];
		$arSitesTabs[] = $arSiteTab;
	}
		
	unset($dbSites, $arSite, $arSiteTab);
	
	$oSitesTabs = new CAdminTabControl(
		"Sites",
		$arSitesTabs
	);

    $arSitesSections = array();

    foreach ($arSites as $arSite) {
        $arOrderProperties = CStartShopUtil::DBResultToArray(CStartShopOrderProperty::GetList(array('SORT' => 'ASC'), array('SID' => $arSite['ID'], 'ACTIVE' => 'Y')));
        $arOrderPropertiesValues = array("" => GetMessage("select.empty"));

        foreach ($arOrderProperties as $arOrderProperty)
            $arOrderPropertiesValues[$arOrderProperty['ID']] = $arOrderProperty['LANG'][LANGUAGE_ID]['NAME'];

        $arOrderStatuses = CStartShopUtil::DBResultToArray(CStartShopOrderStatus::GetList(array('SORT' => 'ASC'), array('SID' => $arSite['ID'], 'ACTIVE' => 'Y')));
        $arOrderStatusesValues = array("" => GetMessage("sections.delivery_and_payment.payment.order.status.empty"));

        foreach ($arOrderStatuses as $arOrderStatus)
            $arOrderStatusesValues[$arOrderStatus['ID']] = $arOrderStatus['LANG'][LANGUAGE_ID]['NAME'];

        $arEvents = CStartShopUtil::DBResultToArray(CEventType::GetList(array('LID' => LANGUAGE_ID)));
        $arEventsValues = array("" => GetMessage("select.empty"));

        foreach ($arEvents as $arEvent)
            $arEventsValues[$arEvent['EVENT_NAME']] = $arEvent['EVENT_NAME'];

        $arSitesSections[$arSite['ID']] = array(
            "COMMON" => array(
                "NAME" => GetMessage('sections.common'),
                "PARAMETERS" => array(
                    "ORDER_REGISTER_NEW_USER" => array(
                        "KEY" => "ORDER_REGISTER_NEW_USER_".$arSite['ID'],
                        "TYPE" => "CHECKBOX",
                        "NAME" => GetMessage('sections.common.order_register_new_user'),
                        "DEFAULT" => "N"
                    ),
                    "ORDER_REGISTER_NEW_USER_GROUP" => array(
                        "KEY" => "ORDER_REGISTER_NEW_USER_GROUP_".$arSite['ID'],
                        "TYPE" => "LIST",
                        "NAME" => GetMessage('sections.common.order_register_new_user_group'),
                        "VALUES" => $arUserGroups
                    )
                )
            ),
            "THEME" => array(
                "NAME" => GetMessage('sections.theme'),
                "PARAMETERS" => array(

                )
            ),
            "DELIVERY_AND_PAY" => array(
                "NAME" => GetMessage('sections.delivery_and_payment'),
                "PARAMETERS" => array(
                    "DELIVERY_USE" => array(
                        "KEY" => "DELIVERY_USE_".$arSite['ID'],
                        "TYPE" => "CHECKBOX",
                        "NAME" => GetMessage('sections.delivery_and_payment.delivery.use'),
                        "DEFAULT" => "Y"
                    ),
                    "PAYMENT_USE" => array(
                        "KEY" => "PAYMENT_USE_".$arSite['ID'],
                        "TYPE" => "CHECKBOX",
                        "NAME" => GetMessage('sections.delivery_and_payment.payment.use'),
                        "DEFAULT" => "Y"
                    ),
                    "PAYMENT_ORDER_STATUS" => array(
                        "KEY" => "PAYMENT_ORDER_STATUS_".$arSite['ID'],
                        "TYPE" => "LIST",
                        "NAME" => GetMessage('sections.delivery_and_payment.payment.order.status'),
                        "VALUES" => $arOrderStatusesValues
                    )
                )
            ),
            "MAIL" => array(
                "NAME" => GetMessage('sections.mail'),
                "PARAMETERS" => array(
                    "MAIL_USE" => array(
                        "KEY" => "MAIL_USE_".$arSite['ID'],
                        "TYPE" => "CHECKBOX",
                        "NAME" => GetMessage('sections.mail.use'),
                        "DEFAULT" => "N"
                    ),
                    "MAIL_MAIL" => array(
                        "KEY" => "MAIL_MAIL_".$arSite['ID'],
                        "TYPE" => "STRING",
                        "NAME" => GetMessage('sections.mail.mail')
                    ),
                    "MAIL_ORDER_PROPERTY" => array(
                        "KEY" => "MAIL_ORDER_PROPERTY_".$arSite['ID'],
                        "TYPE" => "LIST",
                        "NAME" => GetMessage('sections.mail.order.property'),
                        "VALUES" => $arOrderPropertiesValues
                    ),
                    "MAIL_ADMIN_ORDER_CREATE" => array(
                        "KEY" => "MAIL_ADMIN_ORDER_CREATE_".$arSite['ID'],
                        "TYPE" => "CHECKBOX",
                        "NAME" => GetMessage('sections.mail.admin.order.create'),
                        "DEFAULT" => "N"
                    ),
                    "MAIL_ADMIN_ORDER_CREATE_EVENT" => array(
                        "KEY" => "MAIL_ADMIN_ORDER_CREATE_EVENT_".$arSite['ID'],
                        "TYPE" => "LIST",
                        "NAME" => GetMessage('sections.mail.admin.order.create.event'),
                        "VALUES" => $arEventsValues
                    ),
                    "MAIL_ADMIN_ORDER_PAY" => array(
                        "KEY" => "MAIL_ADMIN_ORDER_PAY_".$arSite['ID'],
                        "TYPE" => "CHECKBOX",
                        "NAME" => GetMessage('sections.mail.admin.order.pay'),
                        "DEFAULT" => "N"
                    ),
                    "MAIL_ADMIN_ORDER_PAY_EVENT" => array(
                        "KEY" => "MAIL_ADMIN_ORDER_PAY_EVENT_".$arSite['ID'],
                        "TYPE" => "LIST",
                        "NAME" => GetMessage('sections.mail.admin.order.pay.event'),
                        "VALUES" => $arEventsValues
                    ),
                    "MAIL_CLIENT_ORDER_CREATE" => array(
                        "KEY" => "MAIL_CLIENT_ORDER_CREATE_".$arSite['ID'],
                        "TYPE" => "CHECKBOX",
                        "NAME" => GetMessage('sections.mail.client.order.create'),
                        "DEFAULT" => "N"
                    ),
                    "MAIL_CLIENT_ORDER_CREATE_EVENT" => array(
                        "KEY" => "MAIL_CLIENT_ORDER_CREATE_EVENT_".$arSite['ID'],
                        "TYPE" => "LIST",
                        "NAME" => GetMessage('sections.mail.client.order.create.event'),
                        "VALUES" => $arEventsValues
                    )
                )
            )
        );

        foreach (CStartShopVariables::$THEME_COLORS as $sColorKey => $arColor)
            $arSitesSections[$arSite['ID']]["THEME"]["PARAMETERS"][$sColorKey] = array(
                "KEY" => $sColorKey.'_'.$arSite['ID'],
                "TYPE" => "COLOR",
                "NAME" => $arColor['NAME'],
                "DEFAULT" => $arColor['DEFAULT']
            );

        foreach ($arSitesSections[$arSite['ID']] as $sSectionKey => $arSection)
            foreach ($arSection['PARAMETERS'] as $sParameterKey => $arParameter)
                $arSitesSections[$arSite['ID']][$sSectionKey]['PARAMETERS'][$sParameterKey]['VALUE'] = isset($_REQUEST[$arParameter['KEY']]) ? $_REQUEST[$arParameter['KEY']] : CStartShopVariables::Get($sParameterKey, $arParameter['DEFAULT'], $arSite['ID']);


    }

    if (!$bRightsEdit)
        $sError = GetMessage('messages.error.rights');

    if ($bActionApply)
        if ($bRightsEdit) {
            foreach ($arSites as $arSite) {
                foreach ($arSitesSections[$arSite['ID']] as $sSectionKey => $arSection)
                    $arSitesSections[$arSite['ID']][$sSectionKey]['PARAMETERS'] = CStartShopToolsAdmin::SaveParameters($arSection['PARAMETERS'], function ($sKey, $arParameter) {
                        global $arSite;
                        CStartShopVariables::Set($sKey, $arParameter['VALUE'], $arSite['ID']);
                    });

                $arColors = array();

                foreach ($arSitesSections[$arSite['ID']]['THEME']['PARAMETERS'] as $sThemeParameterKey => $arThemeParameter)
                    $arColors[$sThemeParameterKey] = $arThemeParameter['VALUE'];

                CStartShopTheme::SetColors($arColors, $arSite['ID']);
            }

            $sNotify = GetMessage('messages.notify.saved');
        }
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<form method="POST">
	<?
        if (!empty($sError))
            CAdminMessage::ShowMessage($sError);

        if (!empty($sNotify) && empty($sError))
            CAdminMessage::ShowNote($sNotify);

		$oSitesTabs->Begin();
	?>
	<?foreach ($arSites as $arSite):?>
		<?
			$oSitesTabs->BeginNextTab();
		?>
        <?CStartShopToolsAdmin::DrawSections(
            $arSitesSections[$arSite['ID']],
            '<tr class="heading"><td colspan="2">#NAME#</td></tr>#CONTENT#',
            '<tr><td width="50%" class="adm-detail-content-cell-l"><label for="#KEY#">#NAME#:</label></td><td width="50%" class="adm-detail-content-cell-r">#CONTROL#</td></tr>'
        );?>
	<?endforeach?>
	<?if ($bRightsEdit):?>
        <?$oSitesTabs->Buttons();?>
        <input class="adm-btn-save" name="save" type="submit" value="<?=GetMessage('buttons.save')?>" />
    <?endif;?>
	<?
		$oSitesTabs->End();
	?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>