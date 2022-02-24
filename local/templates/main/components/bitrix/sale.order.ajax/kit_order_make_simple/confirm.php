<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var $APPLICATION CMain
 */

if ($arParams["SET_TITLE"] == "Y")
{
	$APPLICATION->SetTitle(Loc::getMessage("SOA_ORDER_COMPLETE"));
}
?>

<? if (!empty($arResult["ORDER"])): ?>

    <div class="soa_confirm_info">
        <div>
            <img src="<?=$templateFolder?>/images/confirm_success.png" alt="success">
        </div>
        <div class="soa_confirm_info__number">
            <div>
                <?=Loc::getMessage("SOA_ORDER_SUC", array(
                    "#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"]->toUserTime()->format('d.m.Y H:i'),
                    "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]
                ))?>
            </div>
            <? if (!empty($arResult['ORDER']["PAYMENT_ID"])): ?>
                <div>
                    <?=Loc::getMessage("SOA_PAYMENT_SUC", array(
                        "#PAYMENT_ID#" => $arResult['PAYMENT'][$arResult['ORDER']["PAYMENT_ID"]]['ACCOUNT_NUMBER']
                    ))?>
                </div>
            <? endif ?>
        </div>
        <div>
            <?=Loc::getMessage("SOA_ORDER_SUC1", array("#LINK#" => $arParams["PATH_TO_PERSONAL"]))?>
        </div>
    </div>

	<?
	if ($arResult["ORDER"]["IS_ALLOW_PAY"] === 'Y')
	{
		if (!empty($arResult["PAYMENT"]))
		{
			foreach ($arResult["PAYMENT"] as $payment)
			{
				if ($payment["PAID"] != 'Y')
				{
					if (!empty($arResult['PAY_SYSTEM_LIST'])
						&& array_key_exists($payment["PAY_SYSTEM_ID"], $arResult['PAY_SYSTEM_LIST'])
					)
					{
						$arPaySystem = $arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'][$payment["ID"]];

						if (empty($arPaySystem["ERROR"]))
						{
							?>

							<div class="soa_confirm_payment">
                                <div class="soa_confirm_payment__logo">
                                    <div class="soa_confirm_payment__logo_title"><?=Loc::getMessage("SOA_PAYMENT_METHOD")?></div>
									<div class="soa_confirm_payment__image"><?=CFile::ShowImage($arPaySystem["LOGOTIP"], 100, 100, "border=0 style=\"width: 100px\"", "", false)?></div>
									<div class="soa_confirm_payment__paysystem_name"><?=$arPaySystem["NAME"]?></div>
                                </div>

                                <div class="soa_confirm_payment_paysystem">
                                    <?if(strlen($arPaySystem["ACTION_FILE"]) > 0 && $arPaySystem["NEW_WINDOW"] == "Y" && $arPaySystem["IS_CASH"] != "Y"):?>
                                        <?
                                        $orderAccountNumber = urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]));
                                        $paymentAccountNumber = $payment["ACCOUNT_NUMBER"];
                                        ?>
                                        <script>
                                            window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=$orderAccountNumber?>&PAYMENT_ID=<?=$paymentAccountNumber?>');
                                        </script>
                                    <?=Loc::getMessage("SOA_PAY_LINK", array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$orderAccountNumber."&PAYMENT_ID=".$paymentAccountNumber))?>

                                    <?if(CSalePdf::isPdfAvailable() && $arPaySystem['IS_AFFORD_PDF']):?>
                                        <br/>
                                        <?=Loc::getMessage("SOA_PAY_PDF", array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$orderAccountNumber."&pdf=1&DOWNLOAD=Y"))?>
                                    <?endif?>

                                    <?else:?>
                                        <?=$arPaySystem["BUFFERED_OUTPUT"]?>
                                    <?endif?>
                                </div>
                            </div>

							<?
						}
						else
						{
							?>
							<span style="color:red;"><?=Loc::getMessage("SOA_ORDER_PS_ERROR")?></span>
							<?
						}
					}
					else
					{
						?>
						<span style="color:red;"><?=Loc::getMessage("SOA_ORDER_PS_ERROR")?></span>
						<?
					}
				}
			}
		}
	}
	else
	{
		?>
		<br /><strong><?=$arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR']?></strong>
		<?
	}
	?>

<? else: ?>

	<b><?=Loc::getMessage("SOA_ERROR_ORDER")?></b>
	<br /><br />

	<table class="sale_order_full_table">
		<tr>
			<td>
				<?=Loc::getMessage("SOA_ERROR_ORDER_LOST", ["#ORDER_ID#" => htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"])])?>
				<?=Loc::getMessage("SOA_ERROR_ORDER_LOST1")?>
			</td>
		</tr>
	</table>

<? endif ?>