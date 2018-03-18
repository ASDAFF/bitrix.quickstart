<?php
/**
 * —транице настроек модул€.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */

include_once(dirname(__FILE__) . '/include.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/prolog.php'); // пролог модул€

$APPLICATION->SetAdditionalCSS('/bitrix/themes/' . ADMIN_THEME_ID . '/sysupdate.css'); // картинка дл€ логов

//  онтекстное меню.
$arMenu = array(
	array(
		'TEXT' => GetMessage('UNITELLER.AGENT_LOGS'),
		'LINK' => '/bitrix/admin/uniteller_agent_log.php?lang=' . LANGUAGE_ID,
		'ICON' => 'btn_update_log',
	),
);
$context = new CAdminContextMenu($arMenu);
$context->Show();

$aTabs = array(
	array('DIV' => 'fedit1', 'TAB' => GetMessage('UNITELLER.SALE_BTN_HELP'), 'ICON' => '', 'TITLE' => GetMessage('UNITELLER.SALE_BTN_HELP')),
);
$tabControl = new CAdminTabControl('tabControl', $aTabs);
$tabControl->Begin();
$tabControl->BeginNextTab();

?>
<tr>
	<td colspan="2">
		<?= GetMessage('UNITELLER.SALE_HELP_TEXT') ?>
	</td>
</tr>
<?php

$tabControl->EndTab();
$tabControl->End();

?>