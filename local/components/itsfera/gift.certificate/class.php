<?

if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Sale;
use Bitrix\Sale\Internals;
use Bitrix\Sale\Internals\DiscountCouponTable;

class CGiftCertificate extends CBitrixComponent
{

    const MODULE_ID = 'gift.certificate';

    public function executeComponent ()
    {

        CModule::IncludeModule("iblock");

        global $USER;


        if (Loader::includeModule(self::MODULE_ID)) {

            if ($USER->IsAuthorized()) {

                $iTableID = Option::get(self::MODULE_ID, 'GiftCertificateTableID');

                $hlblock           = \Bitrix\Highloadblock\HighloadBlockTable::getById($iTableID)->fetch();
                $entity            = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();

                $rsData = $entity_data_class::getList(array(
                    "select" => array('*'),
                    "filter" => array(),
                    "order"  => array("ID" => "DESC"),
                ));


                $arIDs = array();
                while ($item = $rsData->fetch()) {

                    $arIDs[] = $item['UF_DISCOUNT_ID'];

                    $filter = array(
                        'filter' => array(
                            '=USER_ID'  => \CUser::GetID(),
                            '=PAYED'    => 'Y',
                            '=CANCELED' => 'N',
                            '>=PRICE'   => $item['UF_PRICE'],
                        ),
                        'select' => array('ID'),
                        'order'  => array('ID' => 'DESC'),
                        'limit'  => 1,
                    );


                    if ($arOrder = Sale\Order::getList($filter)->fetch()) {
                        $this->arResult['ITEMS'][] = CIBlockElement::GetByID($item['UF_PRODUCT_ID'])->GetNext();
                    }


                }

                //все нужные скидки собираем заранее
                $discountList     = array();
                $discountIterator = Internals\DiscountTable::getList(array(
                    'select' => array('ID', 'NAME'),
                    'filter' => array('=ACTIVE' => 'Y', 'ID' => $arIDs),
                    'order'  => array('SORT' => 'ASC', 'NAME' => 'ASC'),
                ));
                while ($discount = $discountIterator->fetch()) {
                    $discount['ID']                = (int)$discount['ID'];
                    $discount['NAME']              = (string)$discount['NAME'];
                    $discountList[$discount['ID']] = $discount['NAME'];
                }
                unset($discount, $discountIterator);

                //получаем список всех купленных сертификатов
                $filter = array(
                    'filter' => array(
                        'DISCOUNT_ID' => $arIDs,
                        'USER_ID'     => $USER->GetID(),
                    ),
                );

                $arCoupons = DiscountCouponTable::GetList($filter)->fetchAll();

                foreach ($arCoupons as $coupon) {

                    $create = new \Bitrix\Main\Type\DateTime($coupon['ACTIVE_FROM']);
                    $active_to = new \Bitrix\Main\Type\DateTime($coupon['ACTIVE_TO']);

                    $this->arResult['COUPONS'][$coupon['ID']] = array(

                        'ID' => $coupon['ID'],
                        'DISCOUNT_NAME' => $discountList[$coupon['DISCOUNT_ID']],
                        'COUPON' => $coupon['COUPON'],
                        'ACTIVE' => $coupon['ACTIVE'],
                        'CREATE' => $create->format("d.m.Y в H:i"),
                        'ACTIVE_TO' => $active_to->format("d.m.Y в H:i"),

                    );

                }
                $this->IncludeComponentTemplate();
            }

        }

    }

}

?>