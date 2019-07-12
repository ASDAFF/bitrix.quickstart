<?

if (strlen($arResult["DETAIL_TEXT"]))
	$arResult["DETAIL_TEXT"] = preg_replace('#(<a[^>]*?href=")/#i', '$1' . SITE_DIR, $arResult["DETAIL_TEXT"]);

if (strlen($arResult["PREVIEW_TEXT"]))
	$arResult["PREVIEW_TEXT"] = preg_replace('#(<a[^>]*?href=")/#i', '$1' . SITE_DIR, $arResult["DETAIL_TEXT"]);
