<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный счет");
?> 
<h2>Сумма на счете</h2>
<br />
<div>
	<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.account",
	"",
	Array(
		"SET_TITLE" => "Y"
	)
);?>
</div>
<br />

<h2>Пополнение счета</h2>
<br />
<?$APPLICATION->IncludeComponent("bitrix:sale.account.pay", ".default", array(
	"SET_TITLE" => "Y",
	"PATH_TO_BASKET" => "/basket/",
	"REDIRECT_TO_CURRENT_PAGE" => "N",
	"SELL_AMOUNT" => array(
		0 => "1",
		1 => "2",
		2 => "3",
		3 => "4",
		4 => "5",
		5 => "6",
	),
	"SELL_CURRENCY" => "RUB",
	"VAR" => "buyMoney",
	"CALLBACK_NAME" => "PayUserAccountDeliveryOrderCallback"
	),
	false
);?> 
<br /><br />
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>