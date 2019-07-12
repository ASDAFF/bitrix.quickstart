<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?

global $USER;
if ($USER->IsAuthorized())
{
	//LocalRedirect(SITE_DIR);
	return;
}

// use template order
$arResult['SHOW_FIELDS'] = array_unique($arResult['SHOW_FIELDS']);
if ($arParams['USE_CUSTOM_ORDER'] === 'Y')
{
	$arResult['SHOW_FIELDS'] = $arParams['SHOW_FIELDS'];
}


// time zone settings adaptation
$prev = 0;
foreach ($arResult['SHOW_FIELDS'] as $k => $field)
{
	if ($field === 'AUTO_TIME_ZONE' && $k % 2 == 1)
	{
		$arResult['SHOW_FIELDS'][$k] = $arResult['SHOW_FIELDS'][$prev];
		$arResult['SHOW_FIELDS'][$prev] = $field;
	}
	$prev = $k;
}
?>