<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
IncludeTemplateLangFile(__FILE__);
?>
<?php
    if(CUser::IsAuthorized()):
    ?>
        <?
        $APPLICATION->IncludeComponent(
            "bitrix:sale.personal.order.list",
            "demoshop_list",
            array(
                "PATH_TO_DETAIL" => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['detail'],
                "PATH_TO_CANCEL" => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['cancel'], //http://jw.local/cabinet/orders/?CANCEL=Y&ID=29
                "PATH_TO_COPY" => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['copy'], //http://jw.local/cabinet/orders/?COPY_ORDER=Y&ID=13
                "PATH_TO_BASKET" => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['cart'],
                "PATH_TO_PAYMENT" =>  SITE_DIR."cabinet/order/payment/",
                "SAVE_IN_SESSION" => "N",
                "ORDERS_PER_PAGE" => 0,
                "SET_TITLE" => "N",
                "ID" => $arResult["VARIABLES"]["ID"],
                "NAV_TEMPLATE" => 'bootstrap',
            ),
            $component
        );
        ?>
    <?php
        else:
    ?>
    <div class="alert alert-error">
        <?=GetMessage("NOVAGR_JWSHOP_DLA_PROSMOTRA_SPISKA")?>.
    </div>
    <?php
        endif;
?>