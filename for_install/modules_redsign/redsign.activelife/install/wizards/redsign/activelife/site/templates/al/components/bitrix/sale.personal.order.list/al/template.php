<?

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/script.js");
Asset::getInstance()->addCss("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/style.css");
CJSCore::Init(array('clipboard'));

Loc::loadMessages(__FILE__);

if (!empty($arResult['ERRORS']['FATAL']))
{
	foreach($arResult['ERRORS']['FATAL'] as $error)
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
	?>
	<ul class="nav-tabs text-center">
		<?
		$nothing = !isset($_REQUEST["filter_history"]) && !isset($_REQUEST["show_all"]);
		$clearFromLink = array("filter_history","filter_status","show_all", "show_canceled");

        ?>
        <li <?php if ($_REQUEST["filter_history"] != 'Y'): ?>class="active"<?php endif; ?>>
            <a href="<?=$APPLICATION->GetCurPageParam("", $clearFromLink, false)?>">
                <?echo Loc::getMessage("SPOL_TPL_CUR_ORDERS")?>
            </a>
        </li>
        <li <?php if (!$nothing && $_REQUEST["filter_history"] == 'Y' && $_REQUEST["show_canceled"] != 'Y'): ?>class="active"<?php endif; ?>>
            <a href="<?=$APPLICATION->GetCurPageParam("filter_history=Y", $clearFromLink, false)?>">
                <?echo Loc::getMessage("SPOL_TPL_VIEW_ORDERS_HISTORY")?>
            </a>
        </li>
        <li <?php if (!$nothing && $_REQUEST["filter_history"] == 'Y' && $_REQUEST["show_canceled"] == 'Y'): ?>class="active"<?php endif; ?>>
            <a href="<?=$APPLICATION->GetCurPageParam("filter_history=Y&show_canceled=Y", $clearFromLink, false)?>">
                <?echo Loc::getMessage("SPOL_TPL_VIEW_ORDERS_CANCELED")?>
            </a>
        </li>
	</ul>
	<?
	if (!empty($arResult['ERRORS']['NONFATAL']))
	{
		foreach($arResult['ERRORS']['NONFATAL'] as $error)
		{
			ShowError($error);
		}
	}
	if (!count($arResult['ORDERS']))
	{
		if ($_REQUEST["filter_history"] == 'Y')
		{
			if ($_REQUEST["show_canceled"] == 'Y')
			{
				?>
                <p class="alert alert-danger"><?=Loc::getMessage('SPOL_TPL_EMPTY_CANCELED_ORDER')?></p>
				<?
			}
			else
			{
				?>
                <p class="alert alert-danger"><?=Loc::getMessage('SPOL_TPL_EMPTY_HISTORY_ORDER_LIST')?></p>
				<?
			}
		}
		else
		{
			?>
            <p class="alert alert-danger"><?=Loc::getMessage('SPOL_TPL_EMPTY_ORDER_LIST')?></p>
			<?
		}
	}
    /*
	if (!count($arResult['ORDERS']))
	{
		?>
		<div class="row col-md-12 col-sm-12">
			<a href="<?=htmlspecialcharsbx($arParams['PATH_TO_CATALOG'])?>" class="sale-order-history-link">
				<?=Loc::getMessage('SPOL_TPL_LINK_TO_CATALOG')?>
			</a>
		</div>
		<?
	}
    */

	if ($_REQUEST["filter_history"] !== 'Y')
	{
		$paymentChangeData = array();
		$orderHeaderStatus = null;

		foreach ($arResult['ORDERS'] as $key => $order)
		{
/*
			if ($orderHeaderStatus !== $order['ORDER']['STATUS_ID'])
			{
				$orderHeaderStatus = $order['ORDER']['STATUS_ID'];

				?>
				<h1 class="sale-order-title">
					<?= Loc::getMessage('SPOL_TPL_ORDER_IN_STATUSES') ?> &laquo;<?=htmlspecialcharsbx($arResult['INFO']['STATUS'][$orderHeaderStatus]['NAME'])?>&raquo;
				</h1>
				<?
			}
*/
			?>
			<div class="order_item panel">
                <div class="panel__head">
                    <h2 class="panel__name">
                        <?=Loc::getMessage('SPOL_TPL_ORDER')?>
                        <?=Loc::getMessage('SPOL_TPL_NUMBER_SIGN').$order['ORDER']['ACCOUNT_NUMBER']?>
                        <?=Loc::getMessage('SPOL_TPL_FROM_DATE')?>
                        <?=$order['ORDER']['DATE_INSERT']->format($arParams['ACTIVE_DATE_FORMAT'])?>,
                        <?=count($order['BASKET_ITEMS']);?>
                        <?
                        $count = count($order['BASKET_ITEMS']) % 10;
                        if ($count == '1')
                        {
                            echo Loc::getMessage('SPOL_TPL_GOOD');
                        }
                        elseif ($count >= '2' && $count <= '4')
                        {
                            echo Loc::getMessage('SPOL_TPL_TWO_GOODS');
                        }
                        else
                        {
                            echo Loc::getMessage('SPOL_TPL_GOODS');
                        }
                        ?>
                        <?=Loc::getMessage('SPOL_TPL_SUMOF')?>
                        <?=$order['ORDER']['FORMATED_PRICE']?>
                    </h2>
                </div>
				<div class="panel__body">
                    <span class="panel__sub">
                        <span class="panel__subtitle"><?=Loc::getMessage('SPOL_TPL_PAYMENT')?></span>
                        <span class="panel__subborder"></span>
                    </span>
                    <?
                    $showDelimeter = false;
                    foreach ($order['PAYMENT'] as $payment)
                    {
                        $paymentChangeData[$payment['ACCOUNT_NUMBER']] = array(
                            "order" => htmlspecialcharsbx($order['ORDER']['ACCOUNT_NUMBER']),
                            "payment" => htmlspecialcharsbx($payment['ACCOUNT_NUMBER'])
                        );
                        ?>
                        <div class="row sale-order-list-inner-row">
                            <?
                            if ($showDelimeter)
                            {
                                ?>
                                <div class="sale-order-list-top-border"></div>
                                <?
                            }
                            else
                            {
                                $showDelimeter = true;
                            }
                            ?>

                            <div class="sale-order-list-inner-row-body">
                                <div class="col-md-9 col-sm-8 col-xs-12 sale-order-list-payment">
                                    <div class="sale-order-list-payment-title">
                                        <?
                                        $paymentSubTitle = Loc::getMessage('SPOL_TPL_BILL')." ".Loc::getMessage('SPOL_TPL_NUMBER_SIGN').htmlspecialcharsbx($payment['ACCOUNT_NUMBER']);
                                        if(isset($payment['DATE_BILL']))
                                        {
                                            $paymentSubTitle .= " ".Loc::getMessage('SPOL_TPL_FROM_DATE')." ".$payment['DATE_BILL']->format($arParams['ACTIVE_DATE_FORMAT']);
                                        }
                                        $paymentSubTitle .=",";
                                        echo $paymentSubTitle;
                                        ?>
                                        <span class="sale-order-list-payment-title-element"><?=$payment['PAY_SYSTEM_NAME']?></span>
                                        <?
                                        if ($payment['PAID'] === 'Y')
                                        {
                                            ?>
                                            <span class="sale-order-list-status-success"><?=Loc::getMessage('SPOL_TPL_PAID')?></span>
                                            <?
                                        }
                                        else
                                        {
                                            ?>
                                            <span class="sale-order-list-status-alert"><?=Loc::getMessage('SPOL_TPL_NOTPAID')?></span>
                                            <?
                                        }
                                        ?>
                                    </div>
                                    <div class="sale-order-list-payment-price">
                                        <span class="sale-order-list-payment-element"><?=Loc::getMessage('SPOL_TPL_SUM_TO_PAID')?>:</span>

                                        <span class="sale-order-list-payment-number"><?=$payment['FORMATED_SUM']?></span>
                                    </div>
                                    <?
                                    if ($payment['PAID'] !== 'Y')
                                    {
                                        ?>
                                        <a href="#" class="sale-order-list-change-payment anchor collapsed" id="<?= htmlspecialcharsbx($payment['ACCOUNT_NUMBER']) ?>">
                                            <?=Loc::getMessage('SPOL_TPL_CHANGE_PAY_TYPE')?><i class="collapsed__icon"></i>
                                        </a>
                                        <?
                                    }
                                    ?>
                                </div>
                                <?
                                if ($payment['PAID'] === 'N' && $payment['IS_CASH'] !== 'Y')
                                {
                                    if ($payment['NEW_WINDOW'] === 'Y')
                                    {
                                        ?>
                                        <div class="col-md-3 col-sm-4 col-xs-12 sale-order-list-button-container">
                                            <a class="sale-order-list-button" target="_blank" href="<?=htmlspecialcharsbx($payment['PSA_ACTION_FILE'])?>">
                                                <?=Loc::getMessage('SPOL_TPL_PAY')?>
                                            </a>
                                        </div>
                                        <?
                                    }
                                    else
                                    {
                                        ?>
                                        <div class="col-md-3 col-sm-4 col-xs-12 sale-order-list-button-container">
                                            <a class="sale-order-list-button ajax_reload" href="<?=htmlspecialcharsbx($payment['PSA_ACTION_FILE'])?>">
                                                <?=Loc::getMessage('SPOL_TPL_PAY')?>
                                            </a>
                                        </div>
                                        <?
                                    }
                                }
                                ?>

                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-10 col-xs-12 sale-order-list-inner-row-template">
                                <a class="sale-order-list-cancel-payment">
                                    <i class="fa fa-long-arrow-left"></i> <?=Loc::getMessage('SPOL_CANCEL_PAYMENT')?>
                                </a>
                            </div>
                        </div>
                        <?
                    }
                    if (!empty($order['SHIPMENT']))
                    {
                        ?>
                        <div class="panel__sub">
                            <span class="panel__subtitle"><?=Loc::getMessage('SPOL_TPL_DELIVERY')?></span>
                            <span class="panel__subborder"></span>
                        </div>
                        <?
                    }
                    $showDelimeter = false;
                    foreach ($order['SHIPMENT'] as $shipment)
                    {
                        if (empty($shipment))
                        {
                            continue;
                        }
                        ?>
                        <div class="row sale-order-list-inner-row">
                            <?
                                if ($showDelimeter)
                                {
                                    ?>
                                    <div class="sale-order-list-top-border"></div>
                                    <?
                                }
                                else
                                {
                                    $showDelimeter = true;
                                }
                            ?>
                            <div class="col-md-9 col-sm-8 col-xs-12 sale-order-list-shipment">
                                <div class="sale-order-list-shipment-title">
                                <span class="sale-order-list-shipment-element">
                                    <?=Loc::getMessage('SPOL_TPL_LOAD')?>
                                    <?
                                    $shipmentSubTitle = Loc::getMessage('SPOL_TPL_NUMBER_SIGN').htmlspecialcharsbx($shipment['ACCOUNT_NUMBER']);
                                    if ($shipment['DATE_DEDUCTED'])
                                    {
                                        $shipmentSubTitle .= " ".Loc::getMessage('SPOL_TPL_FROM_DATE')." ".$shipment['DATE_DEDUCTED']->format($arParams['ACTIVE_DATE_FORMAT']);
                                    }

                                    if ($shipment['FORMATED_DELIVERY_PRICE'])
                                    {
                                        $shipmentSubTitle .= ", ".Loc::getMessage('SPOL_TPL_DELIVERY_COST')." ".$shipment['FORMATED_DELIVERY_PRICE'];
                                    }
                                    echo $shipmentSubTitle;
                                    ?>
                                </span>
                                    <?
                                    if ($shipment['DEDUCTED'] == 'Y')
                                    {
                                        ?>
                                        <span class="sale-order-list-status-success"><?=Loc::getMessage('SPOL_TPL_LOADED');?></span>
                                        <?
                                    }
                                    else
                                    {
                                        ?>
                                        <span class="sale-order-list-status-alert"><?=Loc::getMessage('SPOL_TPL_NOTLOADED');?></span>
                                        <?
                                    }
                                    ?>
                                </div>

                                <div class="sale-order-list-shipment-status">
                                    <span class="sale-order-list-shipment-status-item"><?=Loc::getMessage('SPOL_ORDER_SHIPMENT_STATUS');?>:</span>
                                    <span class="sale-order-list-shipment-status-block"><?=$shipment['DELIVERY_STATUS_NAME']?></span>
                                </div>

                                <?
                                if (!empty($shipment['DELIVERY_ID']))
                                {
                                    ?>
                                    <div class="sale-order-list-shipment-item">
                                        <?=Loc::getMessage('SPOL_TPL_DELIVERY_SERVICE')?>:
                                        <?=$arResult['INFO']['DELIVERY'][$shipment['DELIVERY_ID']]['NAME']?>
                                    </div>
                                    <?
                                }

                                if (!empty($shipment['TRACKING_NUMBER']))
                                {
                                    ?>
                                    <div class="sale-order-list-shipment-item">
                                        <span class="sale-order-list-shipment-id-name"><?=Loc::getMessage('SPOL_TPL_POSTID')?>:</span>
                                        <span class="sale-order-list-shipment-id"><?=htmlspecialcharsbx($shipment['TRACKING_NUMBER'])?></span>
                                        <span class="sale-order-list-shipment-id-icon"></span>
                                    </div>
                                    <?
                                }
                                ?>
                            </div>
                            <?
                            if (strlen($shipment['TRACKING_URL']) > 0)
                            {
                                ?>
                                <div class="col-md-2 col-md-offset-1 col-sm-12 sale-order-list-shipment-button-container">
                                    <a class="sale-order-list-shipment-button" target="_blank" href="<?=$shipment['TRACKING_URL']?>">
                                        <?=Loc::getMessage('SPOL_TPL_CHECK_POSTID')?>
                                    </a>
                                </div>
                                <?
                            }
                            ?>
                        </div>
                        <?
                    }
                    ?>
				</div>
                <ul class="panel__footer list-inline">
                    <li>
                        <a href="<?=htmlspecialcharsbx($order['ORDER']['URL_TO_DETAIL'])?>">
                            <svg class="btn__icon icon-list icon-svg"><use xlink:href="#svg-list"></use></svg><?=getMessage('SPOL_TPL_MORE_ON_ORDER')?>
                        </a>
                    </li>
                    <li>
                        <a class="order_item" href="<?=htmlspecialcharsbx($order['ORDER']['URL_TO_COPY'])?>">
                            <svg class="btn__icon icon-refresh2 icon-svg"><use xlink:href="#svg-refresh2"></use></svg><?=getMessage('SPOL_TPL_REPEAT_ORDER')?>
                        </a>
                    </li>
                    <li>
                        <a href="<?=htmlspecialcharsbx($order['ORDER']['URL_TO_CANCEL'])?>" class="">
                            <svg class="btn__icon icon-close icon-svg"><use xlink:href="#svg-close"></use></svg><?=getMessage('SPOL_TPL_CANCEL_ORDER')?>
                        </a>
                    </li>
                </ul>
			</div>
			<?
		}
	}
	else
	{
		$orderHeaderStatus = null;
/*
		if ($_REQUEST["show_canceled"] === 'Y' && count($arResult['ORDERS']))
		{
			?>
			<h1 class="sale-order-title">
				<?= Loc::getMessage('SPOL_TPL_ORDERS_CANCELED_HEADER') ?>
			</h1>
			<?
		}
*/
		foreach ($arResult['ORDERS'] as $key => $order)
		{
/*
			if ($orderHeaderStatus !== $order['ORDER']['STATUS_ID'] && $_REQUEST["show_canceled"] !== 'Y')
			{
				$orderHeaderStatus = $order['ORDER']['STATUS_ID'];
				?>
				<h1 class="sale-order-title">
					<?= Loc::getMessage('SPOL_TPL_ORDER_IN_STATUSES') ?> &laquo;<?=htmlspecialcharsbx($arResult['INFO']['STATUS'][$orderHeaderStatus]['NAME'])?>&raquo;
				</h1>
				<?
			}
*/
			?>
			<div class="order_item panel">
				<div class="panel__head">
                    <h2 class="panel__name">
                        <?= Loc::getMessage('SPOL_TPL_ORDER') ?>
                        <?= Loc::getMessage('SPOL_TPL_NUMBER_SIGN') ?>
                        <?= htmlspecialcharsbx($order['ORDER']['ACCOUNT_NUMBER'])?>
                        <?= Loc::getMessage('SPOL_TPL_FROM_DATE') ?>
                        <?= $order['ORDER']['DATE_INSERT'] ?>,
                        <?= count($order['BASKET_ITEMS']); ?>
                        <?
                        $count = substr(count($order['BASKET_ITEMS']), -1);
                        if ($count == '1')
                        {
                            echo Loc::getMessage('SPOL_TPL_GOOD');
                        }
                        elseif ($count >= '2' || $count <= '4')
                        {
                            echo Loc::getMessage('SPOL_TPL_TWO_GOODS');
                        }
                        else
                        {
                            echo Loc::getMessage('SPOL_TPL_GOODS');
                        }
                        ?>
                        <?= Loc::getMessage('SPOL_TPL_SUMOF') ?>
                        <?= $order['ORDER']['FORMATED_PRICE'] ?>
                    </h2>
                    <div class="panel__more">
                        <?
                        if ($_REQUEST["show_canceled"] !== 'Y')
                        {
                            ?>
                            <span class="sale-order-list-accomplished-date">
                                <?= Loc::getMessage('SPOL_TPL_ORDER_FINISHED')?>
                            </span>
                            <?
                        }
                        else
                        {
                            ?>
                            <span class="sale-order-list-accomplished-date canceled-order">
                                <?= Loc::getMessage('SPOL_TPL_ORDER_CANCELED')?>
                            </span>
                            <?
                        }
                        ?>
                        <span class="sale-order-list-accomplished-date-number"><?= $order['ORDER']['DATE_STATUS_FORMATED'] ?></span>
                    </div>
				</div>
				<div class="panel__body">
				</div>
                <ul class="panel__footer list-inline">
                    <li>
                        <a href="<?=htmlspecialcharsbx($order['ORDER']['URL_TO_DETAIL'])?>">
                            <svg class="btn__icon icon-list icon-svg"><use xlink:href="#svg-list"></use></svg><?=getMessage('SPOL_TPL_MORE_ON_ORDER')?>
                        </a>
                    </li>
                    <li>
                        <a class="order_item" href="<?=htmlspecialcharsbx($order['ORDER']['URL_TO_COPY'])?>">
                            <svg class="btn__icon icon-refresh2 icon-svg"><use xlink:href="#svg-refresh2"></use></svg><?=getMessage('SPOL_TPL_REPEAT_ORDER')?>
                        </a>
                    </li>
                </ul>
			</div>
			<?
		}
	}
	?>
	<div class="clearfix"></div>
	<?
	echo $arResult["NAV_STRING"];

	if ($_REQUEST["filter_history"] !== 'Y')
	{
		$javascriptParams = array(
			"url" => CUtil::JSEscape($this->__component->GetPath().'/ajax.php'),
			"templateFolder" => CUtil::JSEscape($templateFolder),
			"paymentList" => $paymentChangeData
		);
		$javascriptParams = CUtil::PhpToJSObject($javascriptParams);
		?>
		<script>
			BX.Sale.PersonalOrderComponent.PersonalOrderList.init(<?=$javascriptParams?>);
		</script>
		<?
	}
}
?>
