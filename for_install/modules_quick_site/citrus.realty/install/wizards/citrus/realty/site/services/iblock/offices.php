<?

$wizard =& $this->GetWizard();

// заполним данные первого офиса значениями, указанными в мастере
$obElement = new CIBlockElement();
\Citrus\Realty\Helper::resetCache();
$office = \Citrus\Realty\Helper::getOfficeInfo();
if ($office)
{
	$obElement->SetPropertyValuesEx($office["ID"], $iblockId, array(
		"phones" => $wizard->GetVar("siteTelephone"),
		"address" => $wizard->GetVar("siteAddress"),
	));
}
else
{
	$wizard =& $this->GetWizard();
	$arFields = array(
		"IBLOCK_ID" => $iblockId,
		"NAME" => $wizard->GetVar("siteName"),
		"PROPERTIES" => array(
			"phones" => $wizard->GetVar("siteTelephone"),
			"address" => $wizard->GetVar("siteAddress"),
		),
	);
	if (!$obElement->Add($arFields))
		die("Office add failed: " . $obElement->LAST_ERROR);
}
