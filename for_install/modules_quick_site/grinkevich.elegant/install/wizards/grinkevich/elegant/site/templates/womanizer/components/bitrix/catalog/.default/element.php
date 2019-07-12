<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>



<?$ElementID=$APPLICATION->IncludeComponent(
				"bitrix:catalog.element",
				".default",
				Array(
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
					"META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
					"META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
					"BROWSER_TITLE" => 'TITLE',
					'DETAIL_BROWSER_TITLE' => 'TITLE',
					'LIST_BROWSER_TITLE' => 'TITLE',
					"BASKET_URL" => $arParams["BASKET_URL"],
					"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
					"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
					"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
					"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
					"CACHE_TYPE" => 'N',
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"SET_TITLE" => 'Y',
					"SET_STATUS_404" => $arParams["SET_STATUS_404"],
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
					"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
					"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
					"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
					"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
					"LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
					"LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
					"LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
					"LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"],

					"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
					"OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
					"OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
					"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
					"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],

					"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
					"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
					"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
					"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
					"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
					"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
					'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
					'CURRENCY_ID' => $arParams['CURRENCY_ID'],
					'USE_ELEMENT_COUNTER' => $arParams['USE_ELEMENT_COUNTER'],
					"USE_COMPARE" => $arParams["USE_COMPARE"],
					"COMPARE_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
					"COMPARE_NAME" => $arParams["COMPARE_NAME"],

					"DISPLAY_DETAIL_IMG_WIDTH"	 =>	$arParams["DISPLAY_DETAIL_IMG_WIDTH"],
					"DISPLAY_DETAIL_IMG_HEIGHT" =>	$arParams["DISPLAY_DETAIL_IMG_HEIGHT"],
					"DISPLAY_MORE_PHOTO_WIDTH"	 =>	$arParams["DISPLAY_MORE_PHOTO_WIDTH"],
					"DISPLAY_MORE_PHOTO_HEIGHT" =>	$arParams["DISPLAY_MORE_PHOTO_HEIGHT"],
					"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
					"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
					"SHARPEN" => $arParams["SHARPEN"],
					//forum reviews
					"USE_REVIEW" => $arParams["USE_REVIEW"],
					"USE_CAPTCHA" => $arParams["USE_CAPTCHA"],
					"FORUM_ID" => $arParams["FORUM_ID"],
					"AJAX_POST" => $arParams["REVIEW_AJAX_POST"],
					"SHOW_RATING" => "Y",
					"SHOW_MINIMIZED" => "Y",

					"PRODUCT_PROPERTIES" => array(
						0 => "SIZE",
					),
					"USE_STORE" => $arParams["USE_STORE"],
					"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
					"USE_STORE_SCHEDULE" => $arParams["USE_STORE_SCHEDULE"],
				),
				$component
			);?>


