<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var $APPLICATION CMain
 */

if ($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(Loc::getMessage("SOA_ORDER_COMPLETE"));
?>

<? if (!empty($arResult["ORDER"])): ?>
    <div class="panel">
        <div class="panel__head">
            <?=Loc::getMessage('SOA_ORDER_COMPLETE');?>
        </div>
        <div class="panel__body clearfix">
            <div class="row">
                <div class="panel__col col-md-12">
                    <?=Loc::getMessage("SOA_ORDER_SUC", array(
    					"#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"],
    					"#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]
    				))?>
                    <? if (!empty($arResult['ORDER']["PAYMENT_ID"])): ?>
    					<?=Loc::getMessage("SOA_PAYMENT_SUC", array(
    						"#PAYMENT_ID#" => $arResult['PAYMENT'][$arResult['ORDER']["PAYMENT_ID"]]['ACCOUNT_NUMBER']
    					))?>
		            <? endif ?>
                    <br><br>
                    <?=Loc::getMessage("SOA_ORDER_SUC1", array("#LINK#" => $arParams["PATH_TO_PERSONAL"]))?>
                </div>

	<?
	if (!empty($arResult["PAYMENT"]))
	{
		foreach ($arResult["PAYMENT"] as $payment)
		{
			if ($payment["PAID"] != 'Y')
			{
    ?>
                <div class="panel__col col-md-12">
                <?
				if (!empty($arResult['PAY_SYSTEM_LIST'])
					&& array_key_exists($payment["PAY_SYSTEM_ID"], $arResult['PAY_SYSTEM_LIST'])
				)
				{
					$arPaySystem = $arResult['PAY_SYSTEM_LIST'][$payment["PAY_SYSTEM_ID"]];


					if (empty($arPaySystem["ERROR"]))
					{
					?>

                                    <div><?=Loc::getMessage("SOA_PAY")?>: <?= $arPaySystem["NAME"] ?></div><br>
                                    <div><?=CFile::ShowImage($arPaySystem["LOGOTIP"], 60, 60, "border=0\" style=\"width:60px\"", "", false)?></div>
									<? if (strlen($arPaySystem["ACTION_FILE"]) > 0 && $arPaySystem["NEW_WINDOW"] == "Y" && $arPaySystem["IS_CASH"] != "Y"): ?>
										<?
										$orderAccountNumber = urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]));
										$paymentAccountNumber = $payment["ACCOUNT_NUMBER"];
										?>
										<script>
											window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=$orderAccountNumber?>&PAYMENT_ID=<?=$paymentAccountNumber?>');
										</script>
                                        <br><div>
										<?=Loc::getMessage("SOA_PAY_LINK", array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$orderAccountNumber."&PAYMENT_ID=".$paymentAccountNumber))?>
										<? if (CSalePdf::isPdfAvailable() && $arPaySystem['IS_AFFORD_PDF']): ?>
											<br/>
											<?=Loc::getMessage("SOA_PAY_PDF", array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$orderAccountNumber."&pdf=1&DOWNLOAD=Y"))?>
										<? endif ?>
                                        </div>
									<? else: ?>
										<?=$arPaySystem["BUFFERED_OUTPUT"]?>
									<? endif ?>
					<?
					}
					else
					{
					?>
						<span style="color:red;"><?= Loc::getMessage("SOA_ORDER_PS_ERROR")?></span>
					<?
					}
				}
				else
				{
				?>
					<div class="errortext"><?= Loc::getMessage("SOA_ORDER_PS_ERROR")?></div>
				<?
				}
                ?>
                            </div>
    <?
			}
		}
	}
	?>
            </div>
        </div>
    </div>

<? else: ?>
    <div class="panel">
        <div class="panel__head">
            <?=Loc::getMessage("SOA_ERROR_ORDER")?>
        </div>
        <div class="panel__body clearfix">
            <div class="row">
                <div class="panel__col col-xs-12 col-md-12">
                    <?=Loc::getMessage("SOA_ERROR_ORDER_LOST", array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))?>
                    <?=Loc::getMessage("SOA_ERROR_ORDER_LOST1")?>
                </div>
            </div>
        </div>
    </div>
<? endif ?>