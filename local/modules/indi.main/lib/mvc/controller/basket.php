<?php
/**
 * Individ module
 *
 * @category	Individ
 * @package		MVC
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */
namespace Indi\Main\Mvc\Controller;
use Indi\Main as Main;
use Indi\Main\Mvc as Mvc;
use Indi\Main\Util as Util;
use Indi\Main\Mvc\View;
use \Bitrix\Main\Loader;
use Indi\Main\Basket as SaleBasket;
use Indi\Main\Iblock\Prototype as IblockPrototype;

Loader::includeModule('sale');
/**
 * Контроллер для Корзины
 *
 * @category	Individ
 * @package		MVC
 */
class Basket extends Prototype
{
    /**
     * Добавляет товар в корзину
     *
     * @return number
     */
    public function addAction()
    {
        $this->view = new Mvc\View\Json();
        //$this->returnAsIs = true;
        $id = $this->getParam("id");
        $quantity = $this->getParam("quantity");

        if ($quantity < 1) {
            $quantity = 1;
        }
        $obBasket = new SaleBasket();
        $curCount = $obBasket->add($id, $quantity);
        return $curCount;
    }

    /**
     * Изменяем товар в корзине
     *
     * @return number
     */
    public function editAction()
    {
        $this->view = new Mvc\View\Json();
        //$this->returnAsIs = true;
        $id = $this->getParam("id");
        $quantity = $this->getParam("quantity");

        $obBasket = new SaleBasket();
        $curCount = $obBasket->add($id, $quantity);
        return $curCount;
    }

    /**
     * Возращает кол-во товаров
     *
     * @return mixed
     */
    public function countAction(){

        $this->view = new Mvc\View\Json();

        $obBasket = new SaleBasket();
        $curCount = $obBasket->getCount();
        return $curCount;

    }

    /**
     * Возращает общую стоимость
     *
     * @return mixed
     */
    public function priceAction(){

        $this->view = new Mvc\View\Json();

        $obBasket = new SaleBasket();
        $curCount = $obBasket->getPrice();
        return $curCount;

    }

    /**
     * Возвращает попап товара при добавлении в корзину
     *
     * @return string
     */
    public function getpopupAction()
    {
        $this->view = new Mvc\View\Html();
        $this->returnAsIs = true;
        $id = $this->getParam("id");

        $obiblock = IblockPrototype::getInstance('shop');
        $element = $obiblock->getElementById($id, 0);

        $obBasket = new SaleBasket();
        $count = $obBasket->getCountProducts();

        if($count == 0){
            $count = 1;
        }

        $countItem = (int)$obBasket->getItemQuantity($id);

        $price = \CPrice::GetBasePrice($id);
        if($price["CURRENCY"] != "RUB"){
            $rubVal = ceil(\CCurrencyRates::ConvertCurrency($price["PRICE"], $price["CURRENCY"], "RUB"));
        }
        $currencyFormat = \CCurrencyLang::GetFormatDescription("RUB");
        $priceFormat = str_replace('#', $rubVal, $currencyFormat['FORMAT_STRING']);

        $countItem = $countItem > 0 ? $countItem : 1;

        if (is_array($element) && !empty($element)) {

            $name = $element['NAME'];
            $img = "";

            if ($element["DETAIL_PICTURE"]["ID"] > 0) {
                $ImgId = $element["DETAIL_PICTURE"]["ID"];
            } elseif ($element["PREVIEW_PICTURE"]["ID"] > 0) {
                $ImgId = $element["PREVIEW_PICTURE"]["ID"];
            } else {
                $ImgId = false;
            }

            if ($ImgId) {
                $tmp = \CFile::ResizeImageGet($ImgId, array('width' => 100, 'height' => 90), BX_RESIZE_IMAGE_PROPORTIONAL);
                $img = $tmp["src"];
            } else {
                $img = Util::getNoPhoto(array('width'=>100, 'height'=>90));
            }

            $title = "Товар добавлен в корзину";
            $str1 =
                '<div class="white-popup ">
                <div class="white-popup-title">' . $title . '</div>
                <span class="products__link products__link_full_all">
                    <img class="products__picture" src="' . $img . '">
                    <span class="products__name">' . $name . '</span>
                </span>
                <div class="row mt-3">
                    <div class="col-sm-6"><a id="modal_close" class="js-popup-close products__btn products__btn-continue" href="javascript:void(0);" onClick="closeModal();">Продолжить</a></div>
                    <div class="col-sm-6"><a class="products__btn products__btn-offer" href="/personal/cart/">Оформить заказ</a></div>
                </div>
            </div>';

            $strCount = $count > 0 ? 'Всего в вашей корзине ' . \Indi\Main\Util::getNumEnding($count, array('товар', 'товара', 'товаров')) . '.' : "";

            if(strlen($element['PROPERTIES']['ARTNUMBER']['VALUE']) > 0){
                $artNum = '<div class="col-3 product-row__desc">
                                    <div class="row">
                                        <div class="col-12"><span>Артикул: ' . $element['PROPERTIES']['ARTNUMBER']['VALUE'] . '</span></div>
                                        <div class="col-12 product-row__desc__name"><span>' . $name . '</span></div>
                                    </div>
                                </div>';
            }

            $view = new View\Php('add-basket/popup.php',
                array(
                    'strCount' => $strCount,
                    'artNum' => $artNum,
                    'img' => $img,
                    'id' => $element['ID'],
                    'countItem' => $countItem,
                    'priceFormat' => $priceFormat,
                    'name' => $name,
                )
            );
            $str = $view->render();

            if(false){
                $str2 = '<div class="modal-header">
                           <div class="row">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Закрыть</span></button>
                                <div class="col-12">
                                    <h2 class="modal-title" id="form-result-new-label-2">Товар добавлен в корзину</h2>
                                </div>
                                <div class="col-12">
                                    <h4>' . $strCount . ' <a href="/personal/cart/">Посмотреть</a></h4>
                                </div>
                           </div>
                        </div>
                        <div class="modal-body">
                            <div class="row product-row">
                                <div class="col-3 product-row__img justify-content-center"><img src="' . $img . '" alt="" class="img-fluid"></div>
                                ' . $artNum . '
                                <div class="col-3 product-row__count d-flex justify-content-center">
                                    <div class="quantity js-popup-basket-quantity" data-id="' . $element['ID'] . '">
                                        <input type="button" value="-" class="minus js-popup-basket-dec">
                                        <input type="number" id="quantity_5cffcb1fbb8e1" class="col-6 input-text qty text js-popup-basket-input" data-step="1" min="1" max=""
                                               name="quantity" value="' . $countItem . '" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric"
                                               aria-labelledby="iPhone Dock quantity">
                                        <input type="button" value="+" class="plus js-popup-basket-inc">
                                    </div>
                                </div>
                                <div class="col-3 product-row__price text-center"><span>' . $priceFormat . '</span></div> 
                            </div>
                            <div class="white-popup ">
                                <span class="products__link products__link_full_all">
                                    <img class="products__picture" src="' . $img . '">
                                    <span class="products__name">' . $name . '</span>
                                </span>
                                <div class="row mt-3">
                                    <div class="col-sm-6"><a id="modal_close" class="js-popup-close products__btn products__btn-continue" href="javascript:void(0);" onClick="closeModal();">Продолжить</a></div>
                                    <div class="col-sm-6"><a class="products__btn products__btn-offer" href="/personal/cart/">Оформить заказ</a></div>
                                </div>
                            </div>
                        </div>';
            }
        } else {
            $title = "Товар не найден";
            $str =
                '<div class="white-popup ">
                <div class="white-popup-title">' . $title . '</div>
                <div class="row mt-6">
                    <div class="col-sm-12"><a class="js-popup-close btn btn-default btn-block " href="javascript:void(0);">Закрыть</a></div>
                </div>
            </div>';
        }


        return $str;
    }


    /**
     * Удаляет товар из корзины
     *
     * @return void
     */
    public function removeAction()
    {
        $this->view = new Mvc\View\Json();
        $this->returnAsIs = true;
        $id = $this->getParam("id");
        \CSaleBasket::Delete($id);
    }

    /**
     * Изменение количества покупаемого товара
     *
     */
    public function updateCountAction()
    {
        $this->view = new Mvc\View\Json();
        $this->returnAsIs = true;
        $id = $this->getParam("ID");
        $quantity = $this->getParam("QUANTITY");
        \CSaleBasket::Update($id, array("QUANTITY" => $quantity));
    }
}
?>