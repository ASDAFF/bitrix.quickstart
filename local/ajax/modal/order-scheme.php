<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * Copyright (c) 2018 Created by ASDAFF asdaff.asad@yandex.ru
 */

$entity_data_class = GetHLBlock::GetEntityDataClass(COLOR_HL_BLOCK_ID);
$rsData = $entity_data_class::getList(array(
    'select' => array('*')
));
while ($el = $rsData->fetch()) {
    $el['URL_FILE'] = CFile::GetPath($el['UF_FILE']);
    $arResult[] = $el;
} ?>
<!-- Modal -->
<div class="modal fade" id="order-scheme" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
                <div class="wrp-step">
                    <div class="step-1 selected">1</div>
                    <div class="space"></div>
                    <div class="step-2">2</div>
                    <div class="space"></div>
                    <div class="step-3">3</div>
                </div>

            </div>
            <form name="ORDER_SCHEME" action="<?= PATH_AJAX ?>" method="POST" role="form">
                <input type="hidden" name="ORDER_SCHEME[SITE_ID]" value="<?= SITE_ID ?>"/>
                <input type="hidden" name="ORDER_SCHEME[TITLE]" value="Заказ схемы"/>
                <input type="hidden" name="ORDER_SCHEME[IBLOCK_ID]" value="31"/>
                <input type="hidden" name="ORDER_SCHEME[ID]" value="" id="id_scheme">
                <input type="hidden" name="ORDER_SCHEME[SECT]" value="" id="sect_scheme">
                <input type="hidden" name="ORDER_SCHEME[NAME_SCHEME]" value="" id="name_scheme"/>
                <div class="modal-body">

                    <div id="results-order-scheme">
                        <div class="alert alert-danger" id="beforesend-order-scheme">
                            Пожалуйста заполните все поля.
                        </div>
                        <div class="alert alert-danger" id="error-order-scheme">Ошибка отправки формы.</div>
                        <div class="alert alert-success" id="success-order-scheme">Спасибо за заказ. С вами свяжется наш специалист в ближайшее время. </div>
                    </div>
                    <div class="clearfix">
                        <img src="/local/codenails/ajax/images/loading.gif" alt="Loading" id="form-loading-order-scheme" class="pull-right"/>
                    </div>


                    <section id="step-1">
                        <h4>Что будем красить?</h4>

                        <label for="area">Площадь окраски</label>
                        <input type="number" class="inp" name="ORDER_SCHEME[SPACE]" id="area" placeholder="">
                        <span>M<sup>3</sup></span>

                        <p class="bold-text">Метод окраски:</p>

                        <div class="form-group">
                            <input name="ORDER_SCHEME[METHOD]" id="roller" type="radio" class="radio" value="Окраска кисточкой или валиком" checked>
                            <label for="roller">Окраска кисточкой или валиком</label>
                        </div>


                        <div class="form-group">
                            <input name="ORDER_SCHEME[METHOD]" id="pistol" type="radio" class="radio" value="Пистолетная окраска">
                            <label for="pistol">Пистолетная окраска</label>
                        </div>


                        <div class="form-group">
                            <input name="ORDER_SCHEME[METHOD]" id="airless" type="radio" class="radio" value="Окраска Безвоздушным распылением">
                            <label for="airless">Окраска Безвоздушным распылением</label>
                        </div>


                        <div class="form-group">
                            <input name="ORDER_SCHEME[METHOD]" id="electrostatic" type="radio" class="radio" value="Окраска электростатическим методом">
                            <label for="electrostatic">Окраска электростатическим методом</label>
                        </div>

                        <div class="wrp-bttn">
                            <a href="javascript:void(0);" class="btn btn-next">Далее</a>
                        </div>
                    </section>


                    <section id="step-2">
                        <h4>В какой цвет?</h4>

                        <div class="form-group">
                            <div class="scheme-input">
                                <label class="scheme-label" for="color-ral">Код цвета по системе RAL</label>
                                <input type="text" class="inp" name="ORDER_SCHEME[COLOR]" id="color-ral" placeholder="">
                                <div id="color-ico" class="color-disk"></div>
                            </div>
                            <div class="tbl-scheme-ral">Или воспользуйтесь палитрой</div>
                            <div class="wrap-color clearfix">
                                <? foreach ($arResult as $arItem) { ?>
                                    <a href="javascript:void(0);" title="<?= $arItem['UF_DESCRIPTION'] ?>"
                                       style="background: url('<?= $arItem['URL_FILE'] ?>') repeat 0 0 scroll;"
                                       data-val-color="<?= $arItem['UF_XML_ID'] ?>"
                                       data-bg-color="<?= $arItem['URL_FILE'] ?>"></a>
                                <? } ?>
                            </div>
                        </div>


                        <div class="wrp-bttn">
                            <a href="javascript:void(0);" class="btn btn-next">Далее</a>
                        </div>
                    </section>


                    <section id="step-3">
                        <h4>Ваши личные данные</h4>

                        <div class="form-group">
                            <label class="order-scheme-label" for="name">Ваше имя *</label>
                            <input type="text" class="inp req" name="ORDER_SCHEME[NAME]" id="name" placeholder="">
                        </div>
                        <div class="form-group">
                            <label class="order-scheme-label" for="tel">Телефон *</label>
                            <input type="tel" class="inp req" name="ORDER_SCHEME[PHONE]" id="tel" placeholder="">
                        </div>

                        <div class="wrp-bttn">
                            <button type="submit" class="btn btn-submit">Заказать схему</button>
                        </div>
                    </section>


                </div>
            </form>
        </div>
    </div>
</div>