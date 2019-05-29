<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Yandex\Market;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

Loc::loadMessages(__FILE__);

if (!$USER->IsAdmin())
{
	$APPLICATION->AuthForm(Loc::getMessage('YANDEX_MARKET_ADMIN_ELEMENT_EXPORT_RESULT_REQUIRE_MODULE'));
	return;
}
else if (!Main\Loader::includeModule('yandex.market'))
{
	\CAdminMessage::ShowMessage([
        'TYPE' => 'ERROR',
        'MESSAGE' => Loc::getMessage('YANDEX_MARKET_ADMIN_ELEMENT_EXPORT_RESULT_REQUIRE_MODULE')
    ]);
}
else
{
	$request = Main\Context::getCurrent()->getRequest();
	$id = (int)$request->get('id');

	Market\Ui\Iblock\AdminElementEdit::showTabExportResult($id);
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin_after.php';