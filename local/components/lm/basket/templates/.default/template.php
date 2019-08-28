<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$basket = new \Site\Basket\Basket();
if($basket->hasProducts()):?>
    <div class="basket-page__table">
        <div class="basket-page__table_top">
            <div class="basket-page__table__item">
                <div class="basket-page__table__item__box name"><?=Loc::getMessage('BASKET_PRODUCT_TITLE');?></div>
                <div class="basket-page__table__item__box price"><?=Loc::getMessage('BASKET_RPICE_TITLE');?></div>
                <div class="basket-page__table__item__box header_text"><?=Loc::getMessage('BASKET_HEADER_TEXT_TITLE');?></div>
                <div class="basket-page__table__item__box brand"><?=Loc::getMessage('BASKET_BRAND_TITLE');?></div>
                <div class="basket-page__table__item__box x_fass"><?=Loc::getMessage('BASKET_X_FASS_TITLE');?></div>
                <div class="basket-page__table__item__box count"><?=Loc::getMessage('BASKET_COUNT_TITLE');?></div>
                <div class="basket-page__table__item__box sum"><?=Loc::getMessage('BASKET_SUM_TITLE');?></div>
                <div class="basket-page__table__item__box delete"></div>
            </div>
        </div>
        <div class="basket-page__table_main">
            <?foreach($basket->getProducts() as $item):?>
                <div class="basket-page__table__item" data-item-id="<?=$item['ID']?>">
                    <div class="basket-page__table__item__box name"><?=$item['NAME']?></div>
                    <div class="basket-page__table__item__box header_text"><?=TxtToHTML($item['HEADER_TEXT'])?></div>
                    <div class="basket-page__table__item__box brand"><?=$item['BRAND']?></div>
                    <div class="basket-page__table__item__box x_fass"><?=$item['X_FASS']?></div>
                    <div class="basket-page__table__item__box price"><?=$item['PRICE_FORMATTED']?></div>
                    <div class="basket-page__table__item__box count">
                        <div class="box-short-num">
                            <a class="box-short-num__minus js-quantity js-quantity-minus">-</a>
                            <input class="box-short-num__control js-quantity-input" type="text" name="quantity" data-item-id="<?=$item['ID']?>"
                                   size="1" maxlength="18" min="0" step="1" data-old-value="<?=$item['QUANTITY']?>" value="<?=$item['QUANTITY']?>">
                            <a class="box-short-num__plus js-quantity js-quantity-plus">+</a>
                        </div>
                    </div>
                    <div class="basket-page__table__item__box sum"><?=$item['SUM_FORMATTED']?></div>
                    <div class="basket-page__table__item__box delete">
                        <a class="basket-page__table__item__btn__delete js-product-delete"></a>
                    </div>
                </div>
            <?endforeach;?>
        </div>
        <div class="basket-page__table_footer">
            <div class="basket-page__table__item">
                <div class="basket-page__table__item__box name"></div>
                <div class="basket-page__table__item__box price"></div>
<!--                <div class="basket-page__table__item__box count">--><?//=Loc::getMessage('BASKET_TOTAL_SUM_TITLE');?><!--</div>-->
                <div class="basket-page__table__item__box sum-mega"><?=$basket->getTotalPrice(true);?></div>
            </div>
        </div>
    </div>
    <div class="basket-page__form">
        <form action="/ajax/basket.php?action=sendEmail" class="js-basket-form-send" method="post">
            <div class="it-block">
                <div class="it-error"></div>
                <input type="text" name="name" placeholder="<?=Loc::getMessage('BASKET_FORM_NAME_PLACEHOLDER');?>">
            </div>
            <div class="it-block">
                <div class="it-error"></div>
                <input type="text" name="phone" placeholder="<?=Loc::getMessage('BASKET_FORM_PHONE_PLACEHOLDER');?>">
            </div>
            <div class="it-block">
                <div class="it-error"></div>
                <input type="hidden" name="empty_basket">
            </div>
            <input class="js-personal-data" type="submit" value="<?=Loc::getMessage('BASKET_FORM_BTN_TITLE');?>">

            <div class="basket-page__form__personal-data">
                <label>
                    <input type="checkbox" class="basket-page__form__checkbox" name="checkbox" data-js-core-form-checkbox="js-personal-data">
                </label>
                <label class="basket-page__form__description">
                    Я ознакомлен c положением об обработке и защите персональных данных.
                </label>
            </div>
        </form>
    </div>
    <div class="basket-page__description">
        <div class="basket-page__description__title">
            <?=Loc::getMessage('BASKET_FORM_TITLE');?>
        </div>
        <div class="basket-page__description__text">
            <?=Loc::getMessage('BASKET_FORM_FZ_TITLE');?>
        </div>
    </div>
<?else:?>
    <div style="padding: 15px">
        <?=Loc::getMessage('BASKET_EMPTY_BASKET');?>
    </div>
<?endif;?>