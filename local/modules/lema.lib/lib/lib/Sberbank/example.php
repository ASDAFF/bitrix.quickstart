<?php
/**
 *
confirm.php:

<? if (!empty($arResult["ORDER"])): ?>
    <?php

    //ONLINE PAY (SBERBANK)
    $sberBank = new Lema\Sberbank\PayHandler(array(
        'username' => 'USERNAME-api', //username of api
        'password' => 'PASSWORD', //password of api
        'orderId' => $arResult["ORDER"]['ID'], //created order id
        'returnUrl' => \Lema\Common\Helper::getFullUrl($APPLICATION->GetCurPageParam()), //return url (this page by default)
        'onlinePaymentId' => 3, //ID of created online pay system
        'debug' => false, //for debug, this change order sum to 0.01 rub
        'testMerchant' => true, //use test merchant ?
    ));

    //check order
    //return false, if it's not online pay
    //or return array [payed => true/false, failed => true/false, declined => true/false]
    $answer = $sberBank->checkOrder(isset($_GET['orderId']) ? $_GET['orderId'] : null);

    ?>
	<table class="sale_order_full_table">
		<tr>
			<td>
				<?=Loc::getMessage("SOA_ORDER_SUC", array(
					"#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"],
					"#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]
				))?>

                <?if(false !== $answer)://check, is it online payment?>

                    <br>
                    <br>
                    <?php
                    $payment = $sberBank->getPaymentSystem();
                    ?>
                    <table class="sale_order_full_table">
                        <tr>
                            <td class="ps_logo">
                                <div class="pay_name"><?=Loc::getMessage("SOA_PAY") ?></div>
                                <?=CFile::ShowImage($payment->getField('LOGOTIP'), 100, 100, "border=0\" style=\"width:100px\"", "", false) ?>
                                <div class="paysystem_name"><?=$payment->getField('NAME') ?></div>
                                <br/>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <?if($answer['declined']):?>
                        <?=Loc::getMessage("SOA_PAYMENT_DECLINED")?>
                    <? elseif ($answer['failed']): ?>
                        <?=Loc::getMessage("SOA_PAYMENT_FAIL")?>
                    <? elseif ($answer['payed']): ?>
                        <?=Loc::getMessage("SOA_PAYMENT_SUC", array(
                            "#PAYMENT_ID#" => $arResult['ORDER']['ID']
                        ))?>
                    <? endif ?>
                    <br /><br />
                    <?=Loc::getMessage("SOA_ORDER_SUC1", array("#LINK#" => $arParams["PATH_TO_PERSONAL"]))?>

                <?endif;?>

			</td>
		</tr>
	</table>

	<?

    //uncomment if Ecommerce needed
    //\Lema\Seo\ECommerce::get()->setIblockId(2)->load($arResult['ORDER']['ID'])->setViewContent();

    //if it's online pay - return, next code is not need yet
    if(false !== $answer)
        return ;

	if ($arResult["ORDER"]["IS_ALLOW_PAY"] === 'Y')
	{
		....

message file:

$MESS["SOA_PAYMENT_SUC"] = "Ваш заказ успешно оплачен. Номер вашей оплаты: <b>№#PAYMENT_ID#</b>";
$MESS["SOA_PAYMENT_FAIL"] = "К сожалению, оплата заказа завершилась с ошибкой.";
$MESS["SOA_PAYMENT_DECLINED"] = "К сожалению, оплата заказа отклонена. Истекло время оплаты или недействительная карта.";

* 
**/
