<?

if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Context;
use Bitrix\Catalog;

class CDiscountTimer extends CBitrixComponent
{

    public function executeComponent ()
    {

        global $DB;

        $request                  = Context::getCurrent()->getRequest();
        $this->arResult['isPost'] = $request->isPost();

        $this->arParams["PER_PAGE"] = (isset($this->arParams["PER_PAGE"]) ? intval($this->arParams["PER_PAGE"]) : 4);
        $this->arParams["PAGEN"]    = 1;


        if ($this->arResult['isPost']) {
            foreach ($request->getQueryList() as $name => $value) {
                if (strpos($name, 'PAGEN_') !== false) {
                    $this->arParams["PAGEN"] = (int)$request->get($name);
                }
            }
        }


        $this->arParams['DISCOUNT_ID'] = array_filter($this->arParams['DISCOUNT_ID']);

        foreach ($this->arParams['DISCOUNT_ID'] as $key => $discountID) {

            if ( ! $discountID) {
                throw new Exception('Неверное значение ID скидки.');
            }

            $discount = Catalog\DiscountTable::getList(array(
                'select' => array(
                    'ID',
                    'NAME',
                    'ACTIVE_FROM',
                    'ACTIVE_TO',
                    'CONDITIONS_LIST',
                    'VALUE_TYPE',
                    'VALUE',
                    'CURRENCY',
                ),
                'filter' => array(
                    'ACTIVE'        => 'Y',
                    'ID'            => intval($discountID),
                    '<=ACTIVE_FROM' => new \Bitrix\Main\Type\DateTime(),
                    '>=ACTIVE_TO'   => new \Bitrix\Main\Type\DateTime(),
                ),
                'limit'  => 1,
            ))->fetch();

            if (is_array($discount)) {
                $discount['ACTIVE_TO_FORMAT'] = $discount['ACTIVE_TO']->format('c');


                $dNow                           = new DateTime("now");
                $expired                        = new DateTime($discount['ACTIVE_TO']);
                $interval                       = $dNow->diff($expired);
                $discount['ACTIVE_TO_FORMAT_U'] = intval(
                    ($interval->m * 30 * 24 * 60 * 60) +
                    ($interval->d * 24 * 60 * 60) +
                    ($interval->h * 60 * 60) +
                    ($interval->i * 60) +
                    $interval->s);


                $this->arResult['DISCOUNT'] = $discount;

                $productIterator = \Bitrix\Iblock\ElementTable::getList(array(
                        'select' => array('*'),
                        'filter' => array(
                            '=ACTIVE' => 'Y',
                            'ID'      => $discount['CONDITIONS_LIST']['CHILDREN'][0]['DATA']['value'],
                        ),
                    )
                );

                while ($product = $productIterator->fetch()) {
                    $product['DISCOUNT']       = $discount;
                    $this->arResult['ITEMS'][] = $product;

                }
            }
        }

        $rsList = new CDBResult;
        $rsList->InitFromArray($this->arResult['ITEMS']);
        $rsList->NavStart(($this->arParams["PER_PAGE"]), false, $this->arParams["PAGEN"]);

        $this->arResult['COUNT'] = count($this->arResult['ITEMS']);

        unset($this->arResult['ITEMS']);

        while ($ar_Field = $rsList->Fetch()) {
            $this->arResult['ITEMS'][] = $ar_Field;
        }

        if ( count($this->arResult['ITEMS']) > 0)
            $this->IncludeComponentTemplate();
    }

}

?>