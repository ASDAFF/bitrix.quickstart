<!--        template   start  -->

<? if (!isset($_GET['ORDER_ID'])): ?>
    <div style="background-color: #f3f3f3; padding:20px 0;">
        <section class="container-plus" style="">
            <div class="cart-container">
                <h2 class="title-section">Оформление заказа</h2>
                <h4 class="padding-left-right-15">Контактные данные</h4>
                <div class="flex flex-wrap flex-item-center flex-justify-center flex-content-center order-prop-proxy">
                    <? foreach ($arResult['ORDER_PROP']['USER_PROPS_Y'] as $key => $value): ?>
                        <div class="flex-col-xl-4 flex-col-lg-4 flex-col-md-4 flex-col-sm-12 flex-col-xs-12">
                            <div class="input-container margin-bottom-10 padding-left-right-15">
                                <input type="<?= $value['IS_EMAIL'] == "Y" ? "email" : $value['TYPE']; ?>"
                                value="<?= $value['VALUE']; ?>"
                                data-order-value="<?= $value['FIELD_NAME']; ?>"
                                data-order-requied="<?= $value['REQUIED'] == "Y" ? $value['REQUIED'] : "N"; ?>"
                                 placeholder="<?= $value['NAME']?><?=$value['REQUIED'] == 'Y' ? '*' :'';?>"

                                >
                            </div>
                        </div>
                    <? endforeach; ?>


                    <div class="flex-col-xl-6 flex-col-lg-6 flex-col-md-6 flex-col-sm-12 flex-col-xs-12 delivery-proxy">
                        <h4 class="padding-left-right-15">Доставка</h4>
                        <? foreach ($arResult['ORDER_PROP']['USER_PROPS_N'] as $key => $value): ?>
                            <div class="input-container margin-bottom-10 padding-left-right-15 order-prop-n-proxy">
                                <input type="<?= $value['TYPE']; ?>"
                                value="<?= $value['VALUE']; ?>"
                                data-order-value="<?= $value['FIELD_NAME']; ?>"
                                data-order-requied="<?= $value['REQUIED'] == "Y" ? $value['REQUIED'] : "N"; ?>"
                                 placeholder="<?= $value['NAME']?><?=$value['REQUIED'] == 'Y' ? '*' :'';?>"
                                >
                            </div>
                        <? endforeach; ?>
                        <? foreach ($arResult['DELIVERY'] as $key => $value): ?>
                            <label class="radio-btn-container "><?= $value['NAME']; ?> <span
                                class="radio-desc-text"><?= $value['PRICE_FORMATED']; ?></span>
                                <input type="radio" <?= $value['CHECKED'] == "Y" ? 'checked="checked"' : '' ?>
                                data-order-value="<?= "ID_" . $value['FIELD_NAME'] . "_" . $value['ID']; ?>"
                                name="rab_<?= $value['FIELD_NAME']; ?>">
                                <span class="radio-checkmark"></span>
                            </label>

                        <? endforeach; ?>

                    </div>

                    <div class="flex-col-xl-6 flex-col-lg-6 flex-col-md-6 flex-col-sm-12 flex-col-xs-12 pay-system-proxy">
                        <h4 class="padding-left-right-15">Оплата</h4>
                        <div class="input-container margin-bottom-10 padding-left-right-15">
                            <? foreach ($arResult['PAY_SYSTEM'] as $key => $value): ?>
                                <div class="flex-col-xl-4 flex-col-lg-4 flex-col-md-4 flex-col-sm-12 flex-col-xs-12">
                                    <div class="input-container margin-bottom-10 padding-left-right-15">

                                    </div>
                                </div>
                                <label class="radio-btn-container"><?= $value['NAME']; ?>
                                <input type="radio" <?= $value['CHECKED'] == "Y" ? 'checked="checked"' : '' ?>
                                data-order-value="<?= "ID_PAY_SYSTEM_ID_" . $value['ID']; ?>"
                                name="rab_PAY_SYSTEM_ID">
                                <span class="radio-checkmark"></span>
                            </label>
                        <? endforeach; ?>
                    </div>
                    <div class="flex-col-xl-6 flex-col-lg-6 flex-col-md-6 flex-col-sm-12 flex-col-xs-12 inline-block">
                        <div class="make-order">
                            <h4 class="padding-left-right-15">итого:</h4>
                            <h3 class="padding-left-right-15 total-proxy">39 000 руб.</h3>

                        </div>
                    </div>
                    <div class="flex-col-xl-12 flex-col-lg-12 flex-col-md-12 flex-col-sm-12 flex-col-xs-12 order-save-proxy">

                        <div id="bx-soa-orderSave">
                            <a href="javascript:void(0)"
                            class="listing-item-button" data-save-button="true">
                            <?= $arParams['MESS_ORDER'] ?>
                        </a>
                        <div class="checkbox">
                            <?
                            if ($arParams['USER_CONSENT'] === 'Y') {
                                $APPLICATION->IncludeComponent(
                                    'bitrix:main.userconsent.request',
                                    'schock',
                                    array(
                                        'ID' => $arParams['USER_CONSENT_ID'],
                                        'IS_CHECKED' => $arParams['USER_CONSENT_IS_CHECKED'],
                                        'IS_LOADED' => $arParams['USER_CONSENT_IS_LOADED'],
                                        'AUTO_SAVE' => 'N',
                                        'SUBMIT_EVENT_NAME' => 'bx-soa-order-save',
                                        'REPLACE' => array(
                                            'button_caption' => isset($arParams['~MESS_ORDER']) ? $arParams['~MESS_ORDER'] : $arParams['MESS_ORDER'],
                                            'fields' => $arResult['USER_CONSENT_PROPERTY_DATA']
                                        )
                                    )
                                );
                            }
                            ?>
                        </div>
                    </div>

                </div>
            </div>


        </div>
        <style>
        #bx-soa-main-notifications,
        #bx-soa-auth,
        #bx-soa-total-mobile,
        #bx-soa-region,
        #bx-soa-delivery,
        #bx-soa-pickup,
        #bx-soa-paysystem,
        #bx-soa-properties,
        #bx-soa-basket,
        #bx-soa-total {
            display: none !important;
        }

        .bx-soa {
            width: 100% !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .radio-btn-container span {
            font-size: 16px;
            color: # #9a9a9b;
            display: block;
        }

        .cart-container h2.title-section {
            font-weight: 500;
            font-size: 4rem;
            color: #404345;
        }

        .cart-container h4 {
            font-family: GothaProMed;
            font-weight: bold;
            font-size: 18px;
            text-transform: uppercase;
        }

        .delivery-proxy,
        .pay-system-proxy {
            padding-top: 20px;
        }

        .order-save-proxy {
            position: relative;
            top: -20px;
            display: inline-block;
            margin: 20px auto;
        }

        #bx-soa-orderSave {
            display: block !important;
            font-family: GothaProMed;
            font-weight: 500;
            font-size: 18px;
            text-transform: uppercase;
        }

        .listing-item-button {
            padding: 2%;
        }

        input:checked ~ .checkmark {
            background-color: #2196F3;
        }
    </style>

    <script>

        $(".delivery-proxy label").on('click', function () {

            $('#bx-soa-delivery .bx-soa-section-title-container').click();
            var id = $(this).find('input').attr('data-order-value');
            $('#' + id).click();


        });

        $(".pay-system-proxy label").on('click', function () {
            $('#bx-soa-paysystem .bx-soa-section-title-container').click();
            var id = $(this).find('input').attr('data-order-value');
            $('#' + id).click();

        });
        $(".order-prop-proxy input").change(function () {
            $('#bx-soa-properties .bx-soa-section-title-container').click();
            var id = $(this).attr('data-order-value');
            var val = $(this).val();
            var requied = $(this).attr('data-order-requied');
            if (val == "" && requied == "Y") {
                $(this).addClass('form-error');
            }
            else {
                $(this).removeClass('form-error');
            }

            $('input[name="' + id + '"]').val(val);
        });

        $('#bx-soa-orderSave a').on('click', function () {
            $(".order-prop-proxy input").each(function () {
                var id = $(this).attr('data-order-value');
                var val = $(this).val();
                var requied = $(this).attr('data-order-requied');
                if (val == "" && requied == "Y") {
                    $(this).addClass('form-error');
                }
                else {
                    $(this).removeClass('form-error');
                }
                $('input[name="' + id + '"]').val(val);
            });
            $(".order-prop-n-proxy input").each(function () {
                var id = $(this).attr('data-order-value');
                var val = $(this).val();
                var requied = $(this).attr('data-order-requied');
                if (val == "" && requied == "Y") {
                    $(this).addClass('form-error');
                }
                else {
                    $(this).removeClass('form-error');
                }
                $('input[name="' + id + '"]').val(val);
            });
        });

        $(".order-prop-n-proxy input").change(function () {
            $('#bx-soa-properties .bx-soa-section-title-container').click();
            var id = $(this).attr('data-order-value');
            var val = $(this).val();
            var requied = $(this).attr('data-order-requied');
            if (val == "" && requied == "Y") {
                $(this).addClass('form-error');
            }
            else {
                $(this).removeClass('form-error');
            }
            $('input[name="' + id + '"]').val(val);
        });

        setInterval(function () {
            var total = $('#bx-soa-total .bx-soa-cart-total-line-total .bx-soa-cart-d').html();
            $('.total-proxy').html(total);
        }, 1000);

        document.BasketVal = $('.total-price-value').html();
        setInterval(function () {
            if ($('.total-price-value').html() != document.BasketVal) {
                document.BasketVal = $('.total-price-value').html();
                BX.Sale.OrderAjaxComponent.sendRequest();

            }
        },
        1000
        );
    </script>

</div>
</section>
</div>
<? endif; ?>
<!--        template end  -->



