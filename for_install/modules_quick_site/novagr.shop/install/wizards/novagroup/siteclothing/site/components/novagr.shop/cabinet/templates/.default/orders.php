<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
if (CUser::IsAuthorized()):
    ?>
    <?php
    $APPLICATION->IncludeComponent("bitrix:sale.personal.order.list", "demoshop_list", array(
	"PATH_TO_DETAIL" => "",
	"PATH_TO_COPY" => "",
	"PATH_TO_CANCEL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["cancel"],
	"PATH_TO_BASKET" => $arResult["FOLDER"],
	"ORDERS_PER_PAGE" => "10",
	"SET_TITLE" => "N",
	"SAVE_IN_SESSION" => "Y",
	"NAV_TEMPLATE" => "arrows",
	"ID" => $_REQUEST["ID"],
	"PATH_TO_PAYMENT" => SITE_DIR."cabinet/order/payment/"
	),
        $component
);
    ?>
<?php else:
    ?>
    <div class="alert alert-error">
        <?= GetMessage('NEED_AUTH_MESS') ?>
    </div>
<?php
endif;
?>