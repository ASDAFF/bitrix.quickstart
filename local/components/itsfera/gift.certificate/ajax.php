<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/functions.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");

global $USER;
if ($USER->IsAuthorized()) {

    $data = array();

    if (CModule::IncludeModule("gift.certificate")) {
        switch ($_REQUEST['action']) {
            case 'BUY':

                if ($_REQUEST['ID']) {

                    $productId = (int)$_REQUEST['ID'];

                    $basket = Bitrix\Sale\Basket::loadItemsForFUser(Bitrix\Sale\Fuser::getId(),
                        Bitrix\Main\Context::getCurrent()->getSite());
                    $item   = $basket->createItem('catalog', $productId);


                    $item->setFields(array(
                        'QUANTITY'               => 1,
                        'CURRENCY'               => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                        'LID'                    => Bitrix\Main\Context::getCurrent()->getSite(),
                        'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                    ));

                    $basket->save();
                }

                $data['message'] = 'ok';

                break;
        }
    } else {
        $data['error'][] = 'Не найден модуль "Подарочные сертификаты (gift.certificate)"';
    }

} else {
    $data['error'][] = 'Пользователь не авторизован';
}

echo json_encode($data);
