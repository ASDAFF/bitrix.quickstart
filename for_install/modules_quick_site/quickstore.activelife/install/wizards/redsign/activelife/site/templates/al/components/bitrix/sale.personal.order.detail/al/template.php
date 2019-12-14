<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/script.js");
Asset::getInstance()->addCss("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/style.css");
CJSCore::Init(array('clipboard'));

$APPLICATION->SetTitle(
    Loc::getMessage('SPOD_LIST_MY_ORDER', array(
        '#ACCOUNT_NUMBER#' => htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]),
        '#DATE_ORDER_CREATE#' => $arResult["DATE_INSERT_FORMATED"]
    ))
);

if (!empty($arResult['ERRORS']['FATAL']))
{
	foreach ($arResult['ERRORS']['FATAL'] as $error)
	{
		ShowError($error);
	}

	$component = $this->__component;

	if ($arParams['AUTH_FORM_IN_TEMPLATE'] && isset($arResult['ERRORS']['FATAL'][$component::E_NOT_AUTHORIZED]))
	{
		$APPLICATION->AuthForm('', false, false, 'N', false);
	}
}
else
{
	if (!empty($arResult['ERRORS']['NONFATAL']))
	{
		foreach ($arResult['ERRORS']['NONFATAL'] as $error)
		{
			ShowError($error);
		}
	}
	?>
	<div class="container-fluid sale-order-detail">
		<a class="sale-order-detail-back-to-list-link-up" href="<?= htmlspecialcharsbx($arResult["URL_TO_LIST"]) ?>">
			&larr; <?= Loc::getMessage('SPOD_RETURN_LIST_ORDERS') ?>
		</a>
        <div class="panel">
            <div class="panel__head">
                <h3 class="sale-order-detail-about-order-title-element">
                    <?= Loc::getMessage('SPOD_LIST_ORDER_INFO') ?>
                    <?/*= Loc::getMessage('SPOD_SUB_ORDER_TITLE', array(
                        "#ACCOUNT_NUMBER#"=> htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]),
                        "#DATE_ORDER_CREATE#"=> $arResult["DATE_INSERT_FORMATED"]
                    ))?>
                    <?= count($arResult['BASKET']);?>
                    <?
                    $count = count($arResult['BASKET']) % 10;
                    if ($count == '1')
                    {
                        echo Loc::getMessage('SPOD_TPL_GOOD');
                    }
                    elseif ($count >= '2' && $count <= '4')
                    {
                        echo Loc::getMessage('SPOD_TPL_TWO_GOODS');
                    }
                    else
                    {
                        echo Loc::getMessage('SPOD_TPL_GOODS');
                    }
                    ?>
                    <?=Loc::getMessage('SPOD_TPL_SUMOF')?>
                    <?=$arResult["PRICE_FORMATED"]*/?>
                </h3>
            </div>
            <div class="panel__body">

                <div class="row">
                    <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-name">
                        <div class="sale-order-detail-about-order-inner-container-name-title">
                            <?
                            $userName = $arResult["USER"]["NAME"] ." ". $arResult["USER"]["SECOND_NAME"] ." ". $arResult["USER"]["LAST_NAME"];
                            if (strlen($userName) || strlen($arResult['FIO']))
                            {
                                echo Loc::getMessage('SPOD_LIST_FIO').':';
                            }
                            else
                            {
                                echo Loc::getMessage('SPOD_LOGIN').':';
                            }
                            ?>
                        </div>
                        <div class="sale-order-detail-about-order-inner-container-name-detail">
                            <?
                            if (strlen($userName))
                            {
                                echo htmlspecialcharsbx($userName);
                            }
                            elseif (strlen($arResult['FIO']))
                            {
                                echo htmlspecialcharsbx($arResult['FIO']);
                            }
                            else
                            {
                                echo htmlspecialcharsbx($arResult["USER"]['LOGIN']);
                            }
                            ?>
                        </div>
                        <a class="sale-order-detail-about-order-inner-container-name-read-less">
                            <span class="anchor"><?=Loc::getMessage('SPOD_LIST_LESS')?></span><i class="collapsed__icon"></i>
                        </a>
                        <a class="sale-order-detail-about-order-inner-container-name-read-more collapsed">
                            <span class="anchor"><?=Loc::getMessage('SPOD_LIST_MORE')?></span><i class="collapsed__icon"></i>
                        </a>
                    </div>

                    <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-status">
                        <div class="sale-order-detail-about-order-inner-container-status-title">
                            <?= Loc::getMessage('SPOD_LIST_CURRENT_STATUS', array(
                                '#DATE_ORDER_CREATE#' => $arResult["DATE_INSERT_FORMATED"]
                            )) ?>
                        </div>
                        <div class="sale-order-detail-about-order-inner-container-status-detail">
                            <?
                            if ($arResult['CANCELED'] !== 'Y')
                            {
                                echo $arResult["STATUS"]["NAME"];
                            }
                            else
                            {
                                echo Loc::getMessage('SPOD_ORDER_CANCELED');
                            }
                            ?>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 sale-order-detail-about-order-inner-container-price">
                        <div class="sale-order-detail-about-order-inner-container-price-title">
                            <?= Loc::getMessage('SPOD_ORDER_PRICE')?>:
                        </div>
                        <div class="sale-order-detail-about-order-inner-container-price-detail">
                            <?= $arResult["PRICE_FORMATED"]?>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 sale-order-detail-about-order-inner-container-repeat">
                        <a href="<?=$arResult["URL_TO_COPY"]?>" class="sale-order-detail-about-order-inner-container-repeat-button">
                            <?= Loc::getMessage('SPOD_ORDER_REPEAT') ?>
                        </a>
                        <?
                        if ($arResult["CAN_CANCEL"] === "Y")
                        {
                            ?>
                            <a href="<?=$arResult["URL_TO_CANCEL"]?>" class="sale-order-detail-about-order-inner-container-repeat-cancel">
                                <?= Loc::getMessage('SPOD_ORDER_CANCEL') ?>
                            </a>
                            <?
                        }
                        ?>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-about-order-inner-container-details">
                    <div class="panel__sub">
                        <h4 class="panel__subtitle"><?=Loc::getMessage('SPOD_USER_INFORMATION')?></h4>
                        <span class="panel__subborder"></span>
                    </div>
                    <ul class="sale-order-detail-about-order-inner-container-details-list">
                        <?
                        if (strlen($arResult["USER"]["EMAIL"]))
                        {
                            ?>
                            <li class="sale-order-detail-about-order-inner-container-list-item">
                                <?= Loc::getMessage('SPOD_LOGIN')?>:
                                <div class="sale-order-detail-about-order-inner-container-list-item-element">
                                    <?= htmlspecialcharsbx($arResult["USER"]["LOGIN"]) ?>
                                </div>
                            </li>
                            <?
                        }
                        if (strlen($arResult["USER"]["EMAIL"]))
                        {
                            ?>
                            <li class="sale-order-detail-about-order-inner-container-list-item">
                                <?= Loc::getMessage('SPOD_EMAIL')?>:
                                <a class="sale-order-detail-about-order-inner-container-list-item-link"
                                   href="mailto:<?= htmlspecialcharsbx($arResult["USER"]["EMAIL"]) ?>"><?= htmlspecialcharsbx($arResult["USER"]["EMAIL"]) ?></a>
                            </li>
                            <?
                        }
                        if (strlen($arResult["USER"]["EMAIL"]))
                        {
                            ?>
                            <li class="sale-order-detail-about-order-inner-container-list-item">
                                <?= Loc::getMessage('SPOD_ORDER_PERS_TYPE') ?>:
                                <div class="sale-order-detail-about-order-inner-container-list-item-element">
                                    <?= htmlspecialcharsbx($arResult["PERSON_TYPE"]["NAME"]) ?>
                                </div>
                            </li>
                            <?
                        }
                        if (isset($arResult["ORDER_PROPS"]))
                        {
                            foreach ($arResult["ORDER_PROPS"] as $property)
                            {
                                ?>
                                <li class="sale-order-detail-about-order-inner-container-list-item">
                                    <?= htmlspecialcharsbx($property['NAME']) ?>:
                                    <div class="sale-order-detail-about-order-inner-container-list-item-element">
                                        <?
                                        if ($property["TYPE"] == "Y/N")
                                        {
                                            echo Loc::getMessage('SPOD_' . ($property["VALUE"] == "Y" ? 'YES' : 'NO'));
                                        }
                                        else
                                        {
                                            if ($property['MULTIPLE'] == 'Y' && $property['TYPE'] !== 'FILE')
                                            {
                                                $propertyList = unserialize($property["VALUE"]);
                                                foreach ($propertyList as $propertyElement)
                                                {
                                                    echo $propertyElement . '</br>';
                                                }
                                            }
                                            else
                                            {
                                                echo $property["VALUE"];
                                            }
                                        }
                                        ?>
                                    </div>
                                </li>
                                <?
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head">
                <h3 class="sale-order-detail-payment-options-title-element">
                    <?= Loc::getMessage('SPOD_ORDER_PAYMENT') ?>
                </h3>
            </div>
            <div class="panel__body">

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-info">
                        <div class="row">
                            <div class="col-md-1 col-sm-2 col-xs-2 sale-order-detail-payment-options-info-image"></div>
                            <div class="col-md-11 col-sm-10 col-xs-10 sale-order-detail-payment-options-info-container">
                                <div class="sale-order-detail-payment-options-info-order-number">
                                    <?= Loc::getMessage('SPOD_SUB_ORDER_TITLE', array(
                                        "#ACCOUNT_NUMBER#"=> htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]),
                                        "#DATE_ORDER_CREATE#"=> $arResult["DATE_INSERT_FORMATED"]
                                    ))?>
                                    <?
                                    if ($arResult['CANCELED'] !== 'Y')
                                    {
                                        echo $arResult["STATUS"]["NAME"];
                                    }
                                    else
                                    {
                                        echo Loc::getMessage('SPOD_ORDER_CANCELED');
                                    }
                                    ?>
                                </div>
                                <div class="sale-order-detail-payment-options-info-total-price">
                                    <?=Loc::getMessage('SPOD_ORDER_PRICE_FULL')?>:
                                    <span><?=$arResult["PRICE_FORMATED"]?></span>
                                </div>
                            </div>
                        </div>
                    </div><!--sale-order-detail-payment-options-info-->
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-methods-container">
                        <?
                        foreach ($arResult['PAYMENT'] as $payment)
                        {
                            ?>
                            <div class="row payment-options-methods-row">
                                <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-methods">
                                    <div class="row sale-order-detail-payment-options-methods-information-block">
                                        <div class="col-md-2 col-sm-5 col-xs-12 sale-order-detail-payment-options-methods-image-container">
                                            <span class="sale-order-detail-payment-options-methods-image-element"
                                              style="background-image: url('<?=htmlspecialcharsbx($payment['PAY_SYSTEM']["SRC_LOGOTIP"])?>');"></span>
                                        </div>
                                        <div class="col-md-8 col-sm-7 col-xs-12 sale-order-detail-payment-options-methods-info">
                                            <div class="sale-order-detail-payment-options-methods-info-title">
                                                <div class="sale-order-detail-methods-title">
                                                    <?
                                                    $paymentData[$payment['ACCOUNT_NUMBER']] = array(
                                                        "payment" => $payment['ACCOUNT_NUMBER'],
                                                        "order" => $arResult['ACCOUNT_NUMBER']
                                                    );
                                                    $paymentSubTitle = Loc::getMessage('SPOD_TPL_BILL')." ".Loc::getMessage('SPOD_NUM_SIGN').$payment['ACCOUNT_NUMBER'];
                                                    if(isset($payment['DATE_BILL']))
                                                    {
                                                        $paymentSubTitle .= " ".Loc::getMessage('SPOD_FROM')." ".$payment['DATE_BILL']->format($arParams['ACTIVE_DATE_FORMAT']);
                                                    }
                                                    $paymentSubTitle .=",";
                                                    echo htmlspecialcharsbx($paymentSubTitle);
                                                    ?>
                                                    <span class="sale-order-list-payment-title-element"><?=$payment['PAY_SYSTEM_NAME']?></span>
                                                    <?
                                                    if ($payment['PAID'] === 'Y')
                                                    {
                                                        ?>
                                                        <span class="sale-order-detail-payment-options-methods-info-title-status-success">
                                                        <?=Loc::getMessage('SPOD_PAYMENT_PAID')?></span>
                                                        <?
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <span class="sale-order-detail-payment-options-methods-info-title-status-alert">
                                                        <?=Loc::getMessage('SPOD_PAYMENT_UNPAID')?></span>
                                                        <?
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="sale-order-detail-payment-options-methods-info-total-price">
                                                <span class="sale-order-detail-sum-name"><?= Loc::getMessage('SPOD_ORDER_PRICE_BILL')?>:</span>
                                                <span class="sale-order-detail-sum-number"><?=$payment['PRICE_FORMATED']?></span>
                                            </div>
                                            <?
                                            if ($payment['PAID'] !== 'Y' && $arResult['CANCELED'] !== 'Y')
                                            {
                                                ?>
                                                <a href="#" id="<?=$payment['ACCOUNT_NUMBER']?>" class="sale-order-detail-payment-options-methods-info-change-link anchor collapsed">
                                                    <?=Loc::getMessage('SPOD_CHANGE_PAYMENT_TYPE')?><i class="collapsed__icon"></i>
                                                </a>
                                                <?
                                            }
                                            ?>
                                        </div>
                                        <?
                                        if ($payment['PAY_SYSTEM']["IS_CASH"] !== "Y")
                                        {
                                            ?>
                                            <div class="col-md-2 col-sm-12 col-xs-12 sale-order-detail-payment-options-methods-button-container">
                                                <?
                                                if ($payment['PAY_SYSTEM']['PSA_NEW_WINDOW'] === 'Y')
                                                {
                                                    ?>
                                                    <a class="sale-order-detail-payment-options-methods-button-element-new-window"
                                                       target="_blank"
                                                       href="<?=htmlspecialcharsbx($payment['PAY_SYSTEM']['PSA_ACTION_FILE'])?>">
                                                        <?= Loc::getMessage('SPOD_ORDER_PAY') ?>
                                                    </a>
                                                    <?
                                                }
                                                else
                                                {
                                                    if ($payment["PAID"] === "Y" || $arResult["CANCELED"] === "Y")
                                                    {
                                                        ?>
                                                        <a class="sale-order-detail-payment-options-methods-button-element inactive-button"><?= Loc::getMessage('SPOD_ORDER_PAY') ?></a>
                                                        <?
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <a class="sale-order-detail-payment-options-methods-button-element"><?= Loc::getMessage('SPOD_ORDER_PAY') ?></a>
                                                        <?
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <?
                                        }
                                        ?>
                                        <div class="sale-order-detail-payment-inner-row-template col-md-offset-2 col-sm-offset-5 col-md-5 col-sm-10 col-xs-12">
                                            <a class="sale-order-list-cancel-payment">
                                                <i class="fa fa-long-arrow-left"></i> <?=Loc::getMessage('SPOD_CANCEL_PAYMENT')?>
                                            </a>
                                        </div>
                                    </div>
                                    <?
                                    if ($payment["PAID"] !== "Y"
                                        && $payment['PAY_SYSTEM']["IS_CASH"] !== "Y"
                                        && $payment['PAY_SYSTEM']['PSA_NEW_WINDOW'] !== 'Y'
                                        && $arResult['CANCELED'] !== 'Y')
                                    {
                                        ?>
                                        <div class="row sale-order-detail-payment-options-methods-template col-md-12 col-sm-12 col-xs-12">
                                            <span class="sale-paysystem-close active-button">
                                                <span class="sale-paysystem-close-item sale-order-payment-cancel"></span><!--sale-paysystem-close-item-->
                                            </span><!--sale-paysystem-close-->
                                            <?=$payment['BUFFERED_OUTPUT']?>
                                                <!--<a class="sale-order-payment-cancel">--><?//= Loc::getMessage('SPOD_CANCEL_PAY') ?><!--</a>-->
                                        </div>
                                        <?
                                    }
                                    ?>
                                </div>
                            </div>
                            <?
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <?
        if (count($arResult['SHIPMENT']))
        {
            ?>
            <div class="panel">
                <div class="panel__head">
                    <h3 class="sale-order-detail-payment-options-title-element">
                        <?= Loc::getMessage('SPOD_ORDER_SHIPMENT') ?>
                    </h3>
                </div>
                <div class="panel__body">
                    <?
                        foreach ($arResult['SHIPMENT'] as $shipment)
                        {
                            ?>
                            <div class="row">
                                <div class="col-md-12 col-md-12 col-sm-12 sale-order-detail-payment-options-shipment-container">
                                    <div class="row">
                                        <div class="col-md-12 col-md-12 col-sm-12 sale-order-detail-payment-options-shipment">
                                            <div>
                                                <div class="col-md-3 col-sm-5 sale-order-detail-payment-options-shipment-image-container">
                                                    <?
                                                        if (strlen($shipment['DELIVERY']["SRC_LOGOTIP"]))
                                                        {
                                                            ?>
                                                            <span class="sale-order-detail-payment-options-shipment-image-element"
                                                                  style="background-image: url('<?=htmlspecialcharsbx($shipment['DELIVERY']["SRC_LOGOTIP"])?>')"></span>
                                                            <?
                                                        }
                                                    ?>
                                                </div>
                                                <div class="col-md-7 col-sm-7 sale-order-detail-payment-options-methods-shipment-list">
                                                    <div class="sale-order-detail-payment-options-methods-shipment-list-item-title">
                                                        <?
                                                            //change date
                                                            if (!strlen($shipment['PRICE_DELIVERY_FORMATED']))
                                                            {
                                                                $shipment['PRICE_DELIVERY_FORMATED'] = 0;
                                                            }
                                                            $shipmentRow = Loc::getMessage('SPOD_SUB_ORDER_SHIPMENT')." ".Loc::getMessage('SPOD_NUM_SIGN').$shipment["ACCOUNT_NUMBER"];
                                                            if ($shipment["DATE_DEDUCTED"])
                                                            {
                                                                $shipmentRow .= " ".Loc::getMessage('SPOD_FROM')." ".$shipment["DATE_DEDUCTED"]->format($arParams['ACTIVE_DATE_FORMAT']);
                                                            }
                                                            $shipmentRow .= ", ".Loc::getMessage('SPOD_SUB_PRICE_DELIVERY', array(
                                                                    '#PRICE_DELIVERY#' => $shipment['PRICE_DELIVERY_FORMATED']
                                                                ));
                                                            echo htmlspecialcharsbx($shipmentRow);
                                                        ?>
                                                    </div>
                                                    <?
                                                        if (strlen($shipment["DELIVERY_NAME"]))
                                                        {
                                                            ?>
                                                            <div class="sale-order-detail-payment-options-methods-shipment-list-item">
                                                                <?= Loc::getMessage('SPOD_ORDER_DELIVERY')?>: <?= htmlspecialcharsbx($shipment["DELIVERY_NAME"])?>
                                                            </div>
                                                            <?
                                                        }
                                                    ?>
                                                    <div class="sale-order-detail-payment-options-methods-shipment-list-item">
                                                        <?= Loc::getMessage('SPOD_ORDER_SHIPMENT_STATUS')?>:
                                                        <?= htmlspecialcharsbx($shipment['STATUS_NAME'])?>
                                                    </div>
                                                    <?
                                                        if (strlen($shipment['TRACKING_NUMBER']))
                                                        {
                                                            ?>
                                                            <div class="sale-order-detail-payment-options-methods-shipment-list-item">
                                                                <span class="sale-order-list-shipment-id-name"><?= Loc::getMessage('SPOD_ORDER_TRACKING_NUMBER')?>:</span>
                                                                <span class="sale-order-detail-shipment-id"><?= htmlspecialcharsbx($shipment['TRACKING_NUMBER'])?></span>
                                                                <span class="sale-order-detail-shipment-id-icon"></span>
                                                            </div>
                                                            <?
                                                        }
                                                    ?>
                                                    <div class="sale-order-detail-payment-options-methods-shipment-list-item-link">
                                                        <a class="sale-order-detail-show-link collapsed">
                                                            <span class="anchor"><?=Loc::getMessage('SPOD_LIST_SHOW_ALL')?></span><i class="collapsed__icon"></i>
                                                        </a>
                                                        <a class="sale-order-detail-hide-link">
                                                            <span class="anchor"><?=Loc::getMessage('SPOD_LIST_LESS')?></span><i class="collapsed__icon"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?
                                                    if (strlen($shipment['TRACKING_URL']))
                                                    {
                                                        ?>
                                                        <div class="col-md-2 col-sm-12 sale-order-detail-payment-options-shipment-button-container">
                                                            <a class="sale-order-detail-payment-options-shipment-button-element" href="<?=$shipment['TRACKING_URL']?>">
                                                                <?= Loc::getMessage('SPOD_ORDER_CHECK_TRACKING')?>
                                                            </a>
                                                        </div>
                                                        <?
                                                    }
                                                ?>
                                            </div><!--row-->
                                            <div class="col-sm-12 sale-order-detail-payment-options-shipment-composition-map">
                                                <?
                                                    if (isset($arResult['DELIVERY']['STORE_LIST']))
                                                    {
                                                        foreach ($arResult['DELIVERY']['STORE_LIST'] as $store)
                                                        {
                                                            ?>
                                                            <div class="row">
                                                                <div class="col-md-12 col-sm-12 sale-order-detail-map-container">
                                                                    <div class="row">
                                                                        <h4 class="sale-order-detail-payment-options-shipment-composition-map-title">
                                                                            <?= Loc::getMessage('SPOD_SHIPMENT_STORE')?>
                                                                        </h4>
                                                                        <?
                                                                            $APPLICATION->IncludeComponent(
                                                                                "bitrix:map.yandex.view",
                                                                                "",
                                                                                Array(
                                                                                    "INIT_MAP_TYPE" => "COORDINATES",
                                                                                    "MAP_DATA" =>   serialize(
                                                                                        array(
                                                                                            'yandex_lon' => $store['GPS_S'],
                                                                                            'yandex_lat' => $store['GPS_N'],
                                                                                            'PLACEMARKS' => array(
                                                                                                array(
                                                                                                    "LON" => $store['GPS_S'],
                                                                                                    "LAT" => $store['GPS_N'],
                                                                                                    "TEXT" => $store['TITLE']
                                                                                                )
                                                                                            )
                                                                                        )
                                                                                    ),
                                                                                    "MAP_WIDTH" => "100%",
                                                                                    "MAP_HEIGHT" => "300",
                                                                                    "CONTROLS" => array("ZOOM", "SMALLZOOM", "SCALELINE"),
                                                                                    "OPTIONS" => array(
                                                                                        "ENABLE_DRAGGING",
                                                                                        "ENABLE_SCROLL_ZOOM",
                                                                                        "ENABLE_DBLCLICK_ZOOM"
                                                                                    ),
                                                                                    "MAP_ID" => ""
                                                                                )
                                                                            );
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?
                                                            if (strlen($store['ADDRESS']))
                                                            {
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-md-12 col-sm-12 sale-order-detail-payment-options-shipment-map-address">
                                                                        <div class="row">
                                                            <span class="col-md-2 sale-order-detail-payment-options-shipment-map-address-title">
                                                                <?= Loc::getMessage('SPOD_STORE_ADDRESS')?>:</span>
                                                                <span class="col-md-10 sale-order-detail-payment-options-shipment-map-address-element">
                                                                <?= htmlspecialcharsbx($store['ADDRESS'])?></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?
                                                            }
                                                        }
                                                    }
                                                ?>
                                                <div class="row">
                                                    <div class="col-md-12 col-sm-12 sale-order-detail-payment-options-shipment-composition-container">
                                                        <div class="row">
                                                            <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-shipment-composition-title">
                                                                <h3 class="sale-order-detail-payment-options-shipment-composition-title-element"><?= Loc::getMessage('SPOD_ORDER_SHIPMENT_BASKET')?></h3>
                                                            </div>
                                                        </div>

                                                        <div class="table_items">
                                                            <?/*?>
                                                            <div class="sale-order-detail-order-item-tr hidden-sm hidden-xs">
                                                                <div class="sale-order-detail-order-item-td"
                                                                     style="padding-bottom: 5px;">
                                                                    <div class="sale-order-detail-order-item-td-title">
                                                                        <?= Loc::getMessage('SPOD_NAME')?>
                                                                    </div>
                                                                </div>
                                                                <div class="sale-order-detail-order-item-nth-4p1"></div>
                                                                <div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right"
                                                                     style="padding-bottom: 5px;">
                                                                    <div class="sale-order-detail-order-item-td-title">
                                                                        <?= Loc::getMessage('SPOD_QUANTITY')?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?
                                                            */
                                                                foreach ($shipment['ITEMS'] as $item)
                                                                {
                                                                    $basketItem = $arResult['BASKET'][$item['BASKET_ID']];
                                                                    ?>
                                                                    <div class="table_item product">
                                                                        <div class="table_item__td table_item__item">
                                                                            <div class="table_item__pic">
                                                                                <a href="<?=htmlspecialcharsbx($basketItem['DETAIL_PAGE_URL'])?>">
                                                                                    <?
                                                                                        if (strlen($basketItem['PICTURE']['SRC']))
                                                                                        {
                                                                                            $imageSrc = htmlspecialcharsbx($basketItem['PICTURE']['SRC']);
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            $imageSrc = SITE_TEMPLATE_PATH.'/assets/img/noimg.png';
                                                                                        }
                                                                                    ?>
                                                                                    <img class="table_item__img" src="<?=$imageSrc?>" alt="<?=$basketItem['NAME']?>" title="<?=$basketItem['NAME']?>">
                                                                                </a>
                                                                            </div>
                                                                            <div class="table_item__content">
                                                                                <div class="table_item__head">
                                                                                    <h4 class="table_item__name">
                                                                                        <a href="<?=htmlspecialcharsbx($basketItem['DETAIL_PAGE_URL'])?>">
                                                                                            <?=htmlspecialcharsbx($basketItem['NAME'])?>
                                                                                        </a>
                                                                                    </h4>
                                                                                </div>
                                                                                <?
                                                                                if (isset($basketItem['PROPS']) && is_array($basketItem['PROPS']))
                                                                                {
                                                                                    ?><dl class="table_item__props dl-list"><?
                                                                                    foreach ($basketItem['PROPS'] as $itemProps)
                                                                                    {
                                                                                        ?>
                                                                                        <dt><?=htmlspecialcharsbx($itemProps['NAME'])?>:</dt>
                                                                                        <dd><?=htmlspecialcharsbx($itemProps['VALUE'])?></dd>
                                                                                        <?
                                                                                    }
                                                                                    ?></dl><?
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="table_item__td table_item__quantity">
                                                                            <span class="table_item__title"><?=Loc::getMessage('SPOD_QUANTITY')?></span>
                                                                            <span><?=$item['QUANTITY']?>&nbsp;<?=htmlspecialcharsbx($item['MEASURE_NAME'])?></span>
                                                                        </div>
                                                                    </div>
                                                                    <?
                                                                }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?
                        }
                    ?>
                </div>
            </div>
            <?
        }
        ?>


        <div class="panel">
            <div class="panel__head">
                <h3 class="sale-order-detail-payment-options-order-content-title-element">
                    <?= Loc::getMessage('SPOD_ORDER_BASKET')?>
                </h3>
            </div>
            <div class="panel__body">
                <div class="table_items">
                    <?/*
                    <div class="sale-order-detail-order-item-tr hidden-sm hidden-xs">
                        <div class="sale-order-detail-order-item-td" style="padding-bottom: 5px;">
                            <div class="sale-order-detail-order-item-td-title">
                                <?= Loc::getMessage('SPOD_NAME')?>
                            </div>
                        </div>
                        <div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right" style="padding-bottom: 5px;">
                            <div class="sale-order-detail-order-item-td-title">
                                <?= Loc::getMessage('SPOD_PRICE')?>
                            </div>
                        </div>
                        <?
                        if (strlen($arResult["SHOW_DISCOUNT_TAB"]))
                        {
                            ?>
                            <div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right"
                                 style="padding-bottom: 5px;">
                                <div class="sale-order-detail-order-item-td-title">
                                    <?= Loc::getMessage('SPOD_DISCOUNT') ?>
                                </div>
                            </div>
                            <?
                        }
                        ?>
                        <div class="sale-order-detail-order-item-nth-4p1"></div>
                        <div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right" style="padding-bottom: 5px;">
                            <div class="sale-order-detail-order-item-td-title">
                                <?= Loc::getMessage('SPOD_QUANTITY')?>
                            </div>
                        </div>
                        <div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right" style="padding-bottom: 5px;">
                            <div class="sale-order-detail-order-item-td-title">
                                <?= Loc::getMessage('SPOD_ORDER_PRICE')?>
                            </div>
                        </div>
                    </div>
                    <?
                    */
                    foreach ($arResult['BASKET'] as $basketItem)
                    {
                        ?>
                        <div class="table_item product">
                            <div class="table_item__td table_item__item">
                                <div class="table_item__pic">
                                    <a href="<?=htmlspecialcharsbx($basketItem['DETAIL_PAGE_URL'])?>">
                                        <?
                                        if (strlen($basketItem['PICTURE']['SRC']))
                                        {
                                            $imageSrc = $basketItem['PICTURE']['SRC'];
                                        }
                                        else
                                        {
                                            $imageSrc = SITE_TEMPLATE_PATH.'/assets/img/noimg.png';
                                        }
                                        ?>
                                        <img class="table_item__img" src="<?=$imageSrc?>" alt="<?=$basketItem['NAME']?>" title="<?=$basketItem['NAME']?>">
                                    </a>
                                </div>
                                <div class="table_item__content">
                                    <div class="table_item__head">
                                        <h4 class="table_item__name">
                                            <a href="<?=htmlspecialcharsbx($basketItem['DETAIL_PAGE_URL'])?>">
                                                <?=htmlspecialcharsbx($basketItem['NAME'])?>
                                            </a>
                                        </h4>
                                    </div>
                                    <?
                                    if (isset($basketItem['PROPS']) && is_array($basketItem['PROPS']))
                                    {
                                        ?><dl class="table_item__props dl-list"><?
                                        foreach ($basketItem['PROPS'] as $itemProps)
                                        {
                                            ?>
                                            <dt><?=htmlspecialcharsbx($itemProps['NAME'])?>:</dt>
                                            <dd><?=htmlspecialcharsbx($itemProps['VALUE'])?></dd>
                                            <?
                                        }
                                        ?></dl><?
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="table_item__td table_item__price price">
                                <div class="price__pdv"><?=$basketItem['BASE_PRICE_FORMATED']?></div>
                                <div class="price__pdd"><?=$basketItem['DISCOUNT_PRICE_PERCENT_FORMATED']?></div>
                            </div>
                            <?
                            /*
                            if (strlen($basketItem["DISCOUNT_PRICE_PERCENT_FORMATED"]))
                            {
                                ?>
                                <div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
                                    <div class="sale-order-detail-order-item-td-title col-xs-7 col-sm-5 visible-xs visible-sm">
                                        <?= Loc::getMessage('SPOD_DISCOUNT') ?>
                                    </div>
                                    <div class="sale-order-detail-order-item-td-text">
                                        <strong class="bx-price"><?= $basketItem['DISCOUNT_PRICE_PERCENT_FORMATED'] ?></strong>
                                    </div>
                                </div>
                                <?
                            }
                            elseif (strlen($arResult["SHOW_DISCOUNT_TAB"]))
                            {
                                ?>
                                <div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
                                    <div class="sale-order-detail-order-item-td-title col-xs-7 col-sm-5 visible-xs visible-sm">
                                        <?= Loc::getMessage('SPOD_DISCOUNT') ?>
                                    </div>
                                    <div class="sale-order-detail-order-item-td-text">
                                        <strong class="bx-price"></strong>
                                    </div>
                                </div>
                                <?
                            }
                            */
                            ?>
                            <div class="table_item__td table_item__quantity">
                                <span><?=$basketItem['QUANTITY']?>&nbsp;
                                <?
                                if (strlen($basketItem['MEASURE_NAME']))
                                {
                                    echo htmlspecialcharsbx($basketItem['MEASURE_NAME']);
                                }
                                else
                                {
                                    echo Loc::getMessage('SPOD_DEFAULT_MEASURE');
                                }
                                ?></span>
                            </div>
                            <div class="table_item__td table_item__sum price">
                                <span class="table_item__title"><?=Loc::getMessage('SPOD_ORDER_PRICE')?>:</span>
                                <span class="price__pdv"><?=$basketItem['FORMATED_SUM']?></span>
                            </div>
                        </div>
                        <?
                    }
                    ?>
                </div>
            </div>
        </div>

        <table class="cart__total table">
            <?php if (floatval($arResult["ORDER_WEIGHT"])): ?>
                <tr>
                    <td><?=Loc::getMessage('SPOD_TOTAL_WEIGHT')?>:</td>
                    <td><?=$arResult['ORDER_WEIGHT_FORMATED'] ?></td>
                </tr>
            <?php endif; ?>
            <?php if ($arResult['PRODUCT_SUM_FORMATED'] != $arResult['PRICE_FORMATED'] && !empty($arResult['PRODUCT_SUM_FORMATED'])): ?>
                <tr>
                    <td><?=Loc::getMessage('SPOD_COMMON_SUM')?>:</td>
                    <td><?=$arResult['PRODUCT_SUM_FORMATED']?></td>
                </tr>
            <?php endif; ?>
            <?php if (strlen($arResult["PRICE_DELIVERY_FORMATED"])): ?>
                <tr>
                    <td><?=Loc::getMessage('SPOD_DELIVERY')?>:</td>
                    <td><?=$arResult['PRICE_DELIVERY_FORMATED']?></td>
                </tr>
            <?php endif; ?>
            <?php foreach ($arResult["TAX_LIST"] as $tax): ?>
                <tr>
                    <td><?=Loc::getMessage('SPOD_TAX')?>:</td>
                    <td><?=$tax["VALUE_MONEY_FORMATED"]?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td><b><?=Loc::getMessage('SPOD_SUMMARY')?>:</b></td>
                <td class="price__pdv"><?=$arResult['PRICE_FORMATED']?></td>
            </tr>
        </table>

		<a class="sale-order-detail-back-to-list-link-down" href="<?= $arResult["URL_TO_LIST"] ?>">&larr; <?= Loc::getMessage('SPOD_RETURN_LIST_ORDERS')?></a>
	</div>
	<?
	$javascriptParams = array(
	"url" => CUtil::JSEscape($this->__component->GetPath().'/ajax.php'),
	"templateFolder" => CUtil::JSEscape($templateFolder),
	"paymentList" => $paymentData
	);
	$javascriptParams = CUtil::PhpToJSObject($javascriptParams);
	?>
	<script>
		BX.Sale.PersonalOrderComponent.PersonalOrderDetail.init(<?=$javascriptParams?>);
	</script>
<?
}
?>

