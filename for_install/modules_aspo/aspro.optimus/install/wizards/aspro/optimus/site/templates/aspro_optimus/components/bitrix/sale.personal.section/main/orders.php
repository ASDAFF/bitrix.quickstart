<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;

//$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_MAIN"), $arResult['SEF_FOLDER']);
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_ORDERS"), $arResult['PATH_TO_ORDERS']);?>
<div class="personal_wrapper">
	<div class="orders_wrapper">
		<?$APPLICATION->IncludeComponent(
			"bitrix:sale.personal.order.list",
			"",
			array(
				"PATH_TO_DETAIL" => $arResult["PATH_TO_ORDER_DETAIL"],
				"PATH_TO_CANCEL" => $arResult["PATH_TO_ORDER_CANCEL"],
				"PATH_TO_CATALOG" => $arParams["PATH_TO_CATALOG"],
				"PATH_TO_COPY" => $arResult["PATH_TO_ORDER_COPY"],
				"PATH_TO_BASKET" => $arParams["PATH_TO_BASKET"],
				"PATH_TO_PAYMENT" => $arParams["PATH_TO_PAYMENT"],
				"SAVE_IN_SESSION" => $arParams["SAVE_IN_SESSION"],
				"ORDERS_PER_PAGE" => $arParams["ORDERS_PER_PAGE"],
				"SET_TITLE" =>$arParams["SET_TITLE"],
				"ID" => $arResult["VARIABLES"]["ID"],
				"NAV_TEMPLATE" => $arParams["NAV_TEMPLATE"],
				"ACTIVE_DATE_FORMAT" => $arParams["ACTIVE_DATE_FORMAT"],
				"HISTORIC_STATUSES" => $arParams["ORDER_HISTORIC_STATUSES"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			),
			$component
		);
		?>
	</div>
</div>

