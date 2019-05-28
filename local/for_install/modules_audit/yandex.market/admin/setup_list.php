<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Yandex\Market;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin.php';

Loc::loadMessages(__FILE__);

if (!$USER->IsAdmin())
{
	$APPLICATION->AuthForm(Loc::getMessage('YANDEX_MARKET_ADMIN_SETUP_LIST_ACCESS_DENIED'));
	return;
}
else if (!Main\Loader::includeModule('yandex.market'))
{
	\CAdminMessage::ShowMessage([
        'TYPE' => 'ERROR',
        'MESSAGE' => Loc::getMessage('YANDEX_MARKET_ADMIN_SETUP_LIST_REQUIRE_MODULE')
    ]);
}
else
{
	Market\Metrika::load();

	$APPLICATION->IncludeComponent(
		'yandex.market:admin.grid.list',
		'',
		array(
			'GRID_ID' => 'YANDEX_MARKET_ADMIN_SETUP_LIST',
			'PROVIDER_TYPE' => 'Setup',
			'MODEL_CLASS_NAME' => Market\Export\Setup\Model::getClassName(),
            'EDIT_URL' => '/bitrix/admin/yamarket_setup_edit.php?id=#ID#',
            'ADD_URL' => '/bitrix/admin/yamarket_setup_edit.php',
            'TITLE' => Loc::getMessage('YANDEX_MARKET_ADMIN_SETUP_LIST_PAGE_TITLE'),
            'NAV_TITLE' => Loc::getMessage('YANDEX_MARKET_ADMIN_SETUP_LIST_NAV_TITLE'),
			'LIST_FIELDS' => array(
				'ID',
				'NAME',
				'EXPORT_SERVICE',
				'EXPORT_FORMAT',
				'DOMAIN',
				'HTTPS',
				'IBLOCK',
				'FILE_NAME',
				'ENABLE_AUTO_DISCOUNTS',
				'AUTOUPDATE',
				'REFRESH_PERIOD'
			),
			'DEFAULT_LIST_FIELDS' => [
				'ID',
				'NAME',
				'EXPORT_SERVICE',
				'EXPORT_FORMAT',
				'DOMAIN',
				'HTTPS',
				'IBLOCK',
				'FILE_NAME',
			],
			'CONTEXT_MENU' => [
				[
					'TEXT' => Loc::getMessage('YANDEX_MARKET_ADMIN_SETUP_LIST_BUTTON_ADD'),
					'LINK' => 'yamarket_setup_edit.php?lang=' . LANG,
					'ICON' => 'btn_new'
				]
			],
			'ROW_ACTIONS' => array(
				'RUN' => array(
					'URL' => '/bitrix/admin/yamarket_setup_run.php?id=#ID#',
					'ICON' => 'unpack',
					'TEXT' => Loc::getMessage('YANDEX_MARKET_ADMIN_SETUP_LIST_ROW_ACTION_RUN')
				),
				'EDIT' => array(
					'URL' => '/bitrix/admin/yamarket_setup_edit.php?id=#ID#',
					'ICON' => 'edit',
					'TEXT' => Loc::getMessage('YANDEX_MARKET_ADMIN_SETUP_LIST_ROW_ACTION_EDIT'),
					'DEFAULT' => true
				),
				'COPY' => array(
					'URL' => '/bitrix/admin/yamarket_setup_edit.php?id=#ID#&copy=Y',
					'ICON' => 'copy',
					'TEXT' => Loc::getMessage('YANDEX_MARKET_ADMIN_SETUP_LIST_ROW_ACTION_COPY')
				),
				'DELETE' => array(
					'ICON' => 'delete',
					'TEXT' => Loc::getMessage('YANDEX_MARKET_ADMIN_SETUP_LIST_ROW_ACTION_DELETE'),
					'CONFIRM' => 'Y',
					'CONFIRM_MESSAGE' => Loc::getMessage('YANDEX_MARKET_ADMIN_SETUP_LIST_ROW_ACTION_DELETE_CONFIRM')
				)
			),
			'GROUP_ACTIONS' => [
				'delete' => Loc::getMessage('YANDEX_MARKET_ADMIN_SETUP_LIST_ROW_ACTION_DELETE')
			]
		)
	);
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';