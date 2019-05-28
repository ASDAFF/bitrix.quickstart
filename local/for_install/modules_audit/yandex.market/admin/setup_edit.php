<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Yandex\Market;

define('BX_SESSION_ID_CHANGE', false);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin.php';

Loc::loadMessages(__FILE__);

if (!$USER->IsAdmin())
{
	$APPLICATION->AuthForm(Loc::getMessage('YANDEX_MARKET_SETUP_EDIT_ACCESS_DENIED'));
	return;
}
else if (!Main\Loader::includeModule('yandex.market'))
{
	\CAdminMessage::ShowMessage([
        'TYPE' => 'ERROR',
        'MESSAGE' => Loc::getMessage('YANDEX_MARKET_SETUP_EDIT_REQUIRE_MODULE')
    ]);
}
else
{
	Market\Metrika::load();

	$APPLICATION->IncludeComponent('yandex.market:admin.form.edit', '', [
		'TITLE' => Market\Config::getLang('SETUP_EDIT_TITLE_EDIT'),
		'TITLE_ADD' => Market\Config::getLang('SETUP_EDIT_TITLE_ADD'),
		'BTN_SAVE' => Market\Config::getLang('SETUP_EDIT_BTN_SAVE'),
		'FORM_ID'   => 'YANDEX_MARKET_ADMIN_SETUP_EDIT',
		'FORM_BEHAVIOR' => 'steps',
		'PRIMARY'   => !empty($_GET['id']) ? $_GET['id'] : null,
		'COPY' => isset($_GET['copy']) ? $_GET['copy'] === 'Y' : false,
		'LIST_URL'  => '/bitrix/admin/yamarket_setup_list.php?lang=' . LANGUAGE_ID,
		'SAVE_URL'  => '/bitrix/admin/yamarket_setup_run.php?lang=' . LANGUAGE_ID . '&id=#ID#',
		'PROVIDER_TYPE' => 'Setup',
		'MODEL_CLASS_NAME' => Market\Export\Setup\Model::getClassName(),
		'CONTEXT_MENU' => [
			[
                'ICON' => 'btn_list',
	            'LINK' => '/bitrix/admin/yamarket_setup_list.php',
	            'TEXT' => Market\Config::getLang('SETUP_EDIT_CONTEXT_MENU_LIST')
            ]
        ],
		'TABS' => [
			[
				'name' => Market\Config::getLang('SETUP_EDIT_TAB_COMMON'),
				'data' => [
					'METRIKA_GOAL' => 'select_infoblocks'
				],
				'fields' => [
					'NAME',
					'DOMAIN',
					'HTTPS',
					'FILE_NAME',
					'EXPORT_SERVICE',
					'EXPORT_FORMAT',
					'SHOP_DATA',
					'IBLOCK',
					'ENABLE_AUTO_DISCOUNTS',
					'AUTOUPDATE',
					'REFRESH_PERIOD',
				]
			],
			[
				'name' => Market\Config::getLang('SETUP_EDIT_TAB_PARAM'),
				'layout' => 'setup-param',
				'data' => [
					'METRIKA_GOAL' => 'infoblock_matching'
				],
				'fields' => [
					'IBLOCK_LINK.PARAM'
				]
			],
			[
				'name' => Market\Config::getLang('SETUP_EDIT_TAB_FILTER'),
				'layout' => 'setup-filter',
				'final' => true,
				'data' => [
					'METRIKA_GOAL' => 'delivery_options'
				],
				'fields' => [
					'DELIVERY',
					'SALES_NOTES',
					'IBLOCK_LINK.DELIVERY',
					'IBLOCK_LINK.SALES_NOTES',
					'IBLOCK_LINK.FILTER',
					'IBLOCK_LINK.EXPORT_ALL',
				]
			]
		]
	]);
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
