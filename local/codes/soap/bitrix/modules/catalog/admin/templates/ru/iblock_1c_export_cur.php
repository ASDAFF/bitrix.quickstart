<?
$base_currency = (CCurrency::GetByID('RUR') ? 'RUR' : 'RUB');

$arCurrencies = array(
		"USD" => "USD",
		"EUR" => "EUR",
		"RUR" => $base_currency,
		"RUB" => $base_currency,
		"руб." => $base_currency,
		"руб" => $base_currency
	);
?>