<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if ($arParam['SHOW_ACCOUNT_PAGE'] === 'N')
{
	LocalRedirect($arParams['SEF_FOLDER']);
}

use Bitrix\Main\Localization\Loc;

$APPLICATION->SetTitle(Loc::getMessage("SPS_TITLE_ACCOUNT"));
// $APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_MAIN"), $arResult['SEF_FOLDER']);
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_ACCOUNT"));
?>
<div class="personal_wrapper">
	<div class="inner_border">
		<?if ($arParam['SHOW_ACCOUNT_COMPONENT'] !== 'N')
		{
			$APPLICATION->IncludeComponent(
				"bitrix:sale.personal.account",
				"",
				Array(
					"SET_TITLE" => "N"
				),
				$component
			);
		}
		?>
		<h3 class="sale-personal-section-account-sub-header">
			<?=Loc::getMessage("SPS_BUY_MONEY")?>
		</h3>

		<?
		if ($arParam['SHOW_ACCOUNT_PAY_COMPONENT'] !== 'N')
		{
			$APPLICATION->IncludeComponent(
				"bitrix:sale.account.pay",
				"",
				Array(
					"COMPONENT_TEMPLATE" => ".default",
					"REFRESHED_COMPONENT_MODE" => "Y",
					"ELIMINATED_PAY_SYSTEMS" => $arParams['ACCOUNT_PAYMENT_ELIMINATED_PAY_SYSTEMS'],
					"PATH_TO_BASKET" => $arParams['PATH_TO_BASKET'],
					"PATH_TO_PAYMENT" => $arParams['PATH_TO_PAYMENT'],
					"PERSON_TYPE" => $arParams['ACCOUNT_PAYMENT_PERSON_TYPE'],
					"REDIRECT_TO_CURRENT_PAGE" => "N",
					"SELL_AMOUNT" => $arParams['ACCOUNT_PAYMENT_SELL_TOTAL'],
					"SELL_CURRENCY" => $arParams['ACCOUNT_PAYMENT_SELL_CURRENCY'],
					"SELL_SHOW_FIXED_VALUES" => $arParams['ACCOUNT_PAYMENT_SELL_SHOW_FIXED_VALUES'],
					"SELL_SHOW_RESULT_SUM" =>  $arParams['ACCOUNT_PAYMENT_SELL_SHOW_RESULT_SUM'],
					"SELL_TOTAL" => $arParams['ACCOUNT_PAYMENT_SELL_TOTAL'],
					"SELL_USER_INPUT" => $arParams['ACCOUNT_PAYMENT_SELL_USER_INPUT'],
					"SELL_VALUES_FROM_VAR" => "N",
					"SELL_VAR_PRICE_VALUE" => "",
					"SET_TITLE" => "N",
				),
				$component
			);
		}
		?>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.btn').addClass('button')
		})
	</script>
</div>
