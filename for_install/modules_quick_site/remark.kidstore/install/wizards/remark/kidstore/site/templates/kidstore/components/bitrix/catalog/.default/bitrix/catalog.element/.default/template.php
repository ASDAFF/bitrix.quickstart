<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$strMainID = $this->GetEditAreaId($arResult['ID']);
$arItemIDs = array(
	'ID' => $strMainID,
	'PICT' => $strMainID.'_pict',
	'DISCOUNT_PICT_ID' => $strMainID.'_dsc_pict',
	'STICKER_ID' => $strMainID.'_stricker',
	'BIG_SLIDER_ID' => $strMainID.'_big_slider',
	'SLIDER_CONT_ID' => $strMainID.'_slider_cont',
	'SLIDER_LIST' => $strMainID.'_slider_list',
	'SLIDER_LEFT' => $strMainID.'_slider_left',
	'SLIDER_RIGHT' => $strMainID.'_slider_right',
	'OLD_PRICE' => $strMainID.'_old_price',
	'PRICE' => $strMainID.'_price',
	'DISCOUNT_PRICE' => $strMainID.'_price_discount',
	'SLIDER_CONT_OF_ID' => $strMainID.'_slider_cont_',
	'SLIDER_LIST_OF_ID' => $strMainID.'_slider_list_',
	'SLIDER_LEFT_OF_ID' => $strMainID.'_slider_left_',
	'SLIDER_RIGHT_OF_ID' => $strMainID.'_slider_right_',
	'QUANTITY' => $strMainID.'_quantity',
	'QUANTITY_DOWN' => $strMainID.'_quant_down',
	'QUANTITY_UP' => $strMainID.'_quant_up',
	'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
	'QUANTITY_LIMIT' => $strMainID.'_quant_limit',
	'BUY_LINK' => $strMainID.'_buy_link',
	'ADD_BASKET_LINK' => $strMainID.'_add_basket_link',
	'COMPARE_LINK' => $strMainID.'_compare_link',
	'PROP' => $strMainID.'_prop_',
	'PROP_DIV' => $strMainID.'_skudiv',
	'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
	'OFFER_GROUP' => $strMainID.'_set_group_',
	'ZOOM_DIV' => $strMainID.'_zoom_cont',
	'ZOOM_PICT' => $strMainID.'_zoom_pict'
);
$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/i", "x", $strMainID);

?><div class="bx_item_detail" id="<? echo $arItemIDs['ID']; ?>">
	<h1>
<?
if ('Y' == $arParams['USE_VOTE_RATING'])
{
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:iblock.vote",
	"stars",
	array(
		"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		"ELEMENT_ID" => $arResult['ID'],
		"ELEMENT_CODE" => "",
		"MAX_VOTE" => "5",
		"VOTE_NAMES" => array("1", "2", "3", "4", "5"),
		"SET_STATUS_404" => "N",
		"DISPLAY_AS_RATING" => $arParams['VOTE_DISPLAY_AS_RATING'],
		"CACHE_TYPE" => $arParams['CACHE_TYPE'],
		"CACHE_TIME" => $arParams['CACHE_TIME']
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?>
<?
}
?>
		<span><? echo $arResult['NAME']; ?></span>
	</h1>
	<div class="bx_item_container">
		<div class="bx_lt">
<div class="bx_item_slider" id="<? echo $arItemIDs['BIG_SLIDER_ID']; ?>">
	<div class="bx_bigimages">
		<div class="bx_bigimages_imgcontainer">
			<span class="bx_bigimages_aligner"></span><img
				id="<? echo $arItemIDs['PICT']; ?>"
				src="<? echo $arResult['DETAIL_PICTURE']['SRC']; ?>"
				alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
				title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
				id="image_<?=$arResult["DETAIL_PICTURE"]["ID"]?>"
			>
<?
if ('Y' == $arParams['SHOW_DISCOUNT_PERCENT'])
{
?>
			<div class="bx_stick_disc" id="<? echo $arItemIDs['DISCOUNT_PICT_ID'] ?>" style="display: none;"></div>
<?
}
if ($arResult['LABEL'])
{
?>
			<div class="bx_stick new" id="<? echo $arItemIDs['STICKER_ID'] ?>"><? echo $arResult['LABEL_VALUE']; ?></div>
<?
}
?>
		</div>
	</div>
<?
if ($arResult['SHOW_SLIDER'])
{
	if (!isset($arResult['OFFERS']) || empty($arResult['OFFERS']))
	{
		if (5 < $arResult['MORE_PHOTO_COUNT'])
		{
			$strClass = 'bx_slider_conteiner full';
			$strOneWidth = (100/$arResult['MORE_PHOTO_COUNT']).'%';
			$strWidth = (20*$arResult['MORE_PHOTO_COUNT']).'%';
			$strSlideStyle = '';
		}
		else
		{
			$strClass = 'bx_slider_conteiner';
			$strOneWidth = '20%';
			$strWidth = '100%';
			$strSlideStyle = 'display: none;';
		}
?>
	<div class="<? echo $strClass; ?>" id="<? echo $arItemIDs['SLIDER_CONT_ID']; ?>">
		<div class="bx_slider_scroller_container">
			<div class="bx_slide">
				<ul style="width: <? echo $strWidth; ?>;" id="<? echo $arItemIDs['SLIDER_LIST']; ?>">
<?
		foreach ($arResult['MORE_PHOTO'] as &$arOnePhoto)
		{
?>
					<li style="width: <? echo $strOneWidth; ?>; padding-top: <? echo $strOneWidth; ?>;"><a href="javascript:void(0)"><span style="background-image:url('<? echo $arOnePhoto['SRC']; ?>');"></span></a></li>
<?
		}
		unset($arOnePhoto);
?>
				</ul>
			</div>
			<div class="bx_slide_left" id="<? echo $arItemIDs['SLIDER_LEFT']; ?>" style="<? echo $strSlideStyle; ?>"></div>
			<div class="bx_slide_right" id="<? echo $arItemIDs['SLIDER_RIGHT']; ?>" style="<? echo $strSlideStyle; ?>"></div>
		</div>
	</div>
<?
	}
	else
	{
		foreach ($arResult['OFFERS'] as $key => $arOneOffer)
		{
			if (!isset($arOneOffer['MORE_PHOTO_COUNT']) || 0 >= $arOneOffer['MORE_PHOTO_COUNT'])
				continue;
			$strVisible = ($key == $arResult['OFFERS_SELECTED'] ? '' : 'none');
			if (5 < $arOneOffer['MORE_PHOTO_COUNT'])
			{
				$strClass = 'bx_slider_conteiner full';
				$strOneWidth = (100/$arOneOffer['MORE_PHOTO_COUNT']).'%';
				$strWidth = (20*$arOneOffer['MORE_PHOTO_COUNT']).'%';
				$strSlideStyle = '';
			}
			else
			{
				$strClass = 'bx_slider_conteiner';
				$strOneWidth = '20%';
				$strWidth = '100%';
				$strSlideStyle = 'display: none;';
			}
?>
	<div class="<? echo $strClass; ?>" id="<? echo $arItemIDs['SLIDER_CONT_OF_ID'].$arOneOffer['ID']; ?>" style="display: <? echo $strVisible; ?>;">
		<div class="bx_slider_scroller_container">
			<div class="bx_slide">
				<ul style="width: <? echo $strWidth; ?>;" id="<? echo $arItemIDs['SLIDER_LIST_OF_ID'].$arOneOffer['ID']; ?>">
<?
			foreach ($arOneOffer['MORE_PHOTO'] as &$arOnePhoto)
			{
?>
					<li data-value="<? echo $arOneOffer['ID'].'_'.$arOnePhoto['ID']; ?>" style="width: <? echo $strOneWidth; ?>; padding-top: <? echo $strOneWidth; ?>"><a href="javascript:void(0)"><span style="background-image:url('<? echo $arOnePhoto['SRC']; ?>');"></span></a></li>
<?
			}
			unset($arOnePhoto);
?>
				</ul>
			</div>
			<div class="bx_slide_left" id="<? echo $arItemIDs['SLIDER_LEFT_OF_ID'].$arOneOffer['ID'] ?>" style="<? echo $strSlideStyle; ?>" data-value="<? echo $arOneOffer['ID']; ?>"></div>
			<div class="bx_slide_right" id="<? echo $arItemIDs['SLIDER_RIGHT_OF_ID'].$arOneOffer['ID'] ?>" style="<? echo $strSlideStyle; ?>" data-value="<? echo $arOneOffer['ID']; ?>"></div>
		</div>
	</div>
<?
		}
	}
}
?>
</div>
		</div>

		<div class="bx_rt">
<?if ('Y' == $arParams['BRAND_USE']):?>
	<div class="bx_optionblock">
		<?$APPLICATION->IncludeComponent("bitrix:catalog.brandblock", ".default", array(
			"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
			"IBLOCK_ID" => $arParams['IBLOCK_ID'],
			"ELEMENT_ID" => $arResult['ID'],
			"ELEMENT_CODE" => "",
			"PROP_CODE" => $arParams['BRAND_PROP_CODE'],
			"CACHE_TYPE" => $arParams['CACHE_TYPE'],
			"CACHE_TIME" => $arParams['CACHE_TIME'],
			"WIDTH" => "",
			"HEIGHT" => ""
			),
			$component
		);?>
	</div>
<?endif;?>
<div class="item_price">
<?
$boolDiscountShow = (0 < $arResult['MIN_PRICE']['DISCOUNT_DIFF']);
?>
	<div class="item_old_price" id="<? echo $arItemIDs['OLD_PRICE']; ?>" style="display: <? echo ($boolDiscountShow ? '' : 'none'); ?>"><? echo ($boolDiscountShow ? $arResult['MIN_PRICE']['PRINT_VALUE'] : ''); ?></div>
	<div class="item_current_price" id="<? echo $arItemIDs['PRICE']; ?>"><? echo $arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE']; ?></div>
	<div class="item_economy_price" id="<? echo $arItemIDs['DISCOUNT_PRICE']; ?>" style="display: <? echo ($boolDiscountShow ? '' : 'none'); ?>"><? echo ($boolDiscountShow ? GetMessage('ECONOMY_INFO', array('#ECONOMY#' => $arResult['MIN_PRICE']['PRINT_DISCOUNT_DIFF'])) : ''); ?></div>
</div>
<?
if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
{
?>
<div class="item_info_section">
<?
	if (!empty($arResult['DISPLAY_PROPERTIES']))
	{
?>
	<dl>
<?
		foreach ($arResult['DISPLAY_PROPERTIES'] as &$arOneProp)
		{
?>
		<dt><strong><? echo $arOneProp['NAME']; ?></strong> <?
			echo (
				is_array($arOneProp['DISPLAY_VALUE'])
				? implode(' / ', $arOneProp['DISPLAY_VALUE'])
				: $arOneProp['DISPLAY_VALUE']
			);
?>
		</dt>
<?
		}
		unset($arOneProp);
?>
	</dl>
<?
	}
	if ($arResult['SHOW_OFFERS_PROPS'])
	{
?>
	<dl id="<? echo $arItemIDs['DISPLAY_PROP_DIV'] ?>" style="display: none;"></dl>
<?
	}
?>
</div>
<?
}
if ('' != $arResult['PREVIEW_TEXT'])
{
?>
<div class="item_info_section">
<?
	echo ('html' == $arResult['PREVIEW_TEXT_TYPE'] ? $arResult['PREVIEW_TEXT'] : '<p>'.$arResult['PREVIEW_TEXT'].'</p>');
?>
</div>
<?
}
if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS']) && !empty($arResult['OFFERS_PROP']))
{
	$arSkuProps = array();
?>
<div class="item_info_section" style="padding-right:150px;" id="<? echo $arItemIDs['PROP_DIV']; ?>">
<?
	foreach ($arResult['SKU_PROPS'] as &$arProp)
	{
		if (!isset($arResult['OFFERS_PROP'][$arProp['CODE']]))
			continue;
		$arSkuProps[] = array(
			'ID' => $arProp['ID'],
			'TYPE' => $arProp['PROPERTY_TYPE'],
			'VALUES_COUNT' => $arProp['VALUES_COUNT']
		);
		if ('L' == $arProp['PROPERTY_TYPE'])
		{
			if (5 < $arProp['VALUES_COUNT'])
			{
				$strClass = 'bx_item_detail_size full';
				$strOneWidth = (100/$arProp['VALUES_COUNT']).'%';
				$strWidth = (20*$arProp['VALUES_COUNT']).'%';
				$strSlideStyle = '';
			}
			else
			{
				$strClass = 'bx_item_detail_size';
				$strOneWidth = '20%';
				$strWidth = '100%';
				$strSlideStyle = 'display: none;';
			}
?>
	<div class="<? echo $strClass; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_cont">
		<span class="bx_item_section_name_gray"><? echo htmlspecialcharsex($arProp['NAME']); ?></span>
		<div class="bx_size_scroller_container"><div class="bx_size">
			<ul id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_list" style="width: <? echo $strWidth; ?>;margin-left:0%;">
<?
			foreach ($arProp['VALUES'] as $arOneValue)
			{
?>
				<li
					data-treevalue="<? echo $arProp['ID'].'_'.$arOneValue['ID']; ?>"
					data-onevalue="<? echo $arOneValue['ID']; ?>"
					style="width: <? echo $strOneWidth; ?>;"
				><span></span>
				<a href="javascript:void(0)"><? echo htmlspecialcharsex($arOneValue['NAME']); ?></a></li>
<?
			}
?>
			</ul>
			</div>
			<div class="bx_slide_left" style="<? echo $strSlideStyle; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_left" data-treevalue="<? echo $arProp['ID']; ?>"></div>
			<div class="bx_slide_right" style="<? echo $strSlideStyle; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_right" data-treevalue="<? echo $arProp['ID']; ?>"></div>
		</div>
	</div>
<?
		}
		elseif ('E' == $arProp['PROPERTY_TYPE'])
		{
			if (5 < $arProp['VALUES_COUNT'])
			{
				$strClass = 'bx_item_detail_scu full';
				$strOneWidth = (100/$arProp['VALUES_COUNT']).'%';
				$strWidth = (20*$arProp['VALUES_COUNT']).'%';
				$strSlideStyle = '';
			}
			else
			{
				$strClass = 'bx_item_detail_scu';
				$strOneWidth = '20%';
				$strWidth = '100%';
				$strSlideStyle = 'display: none;';
			}
?>
	<div class="<? echo $strClass; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_cont">
		<span class="bx_item_section_name_gray"><? echo htmlspecialcharsex($arProp['NAME']); ?></span>
		<div class="bx_scu_scroller_container"><div class="bx_scu">
			<ul id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_list" style="width: <? echo $strWidth; ?>;margin-left:0%;">
<?
			foreach ($arProp['VALUES'] as $arOneValue)
			{
?>
				<li
					data-treevalue="<? echo $arProp['ID'].'_'.$arOneValue['ID'] ?>"
					data-onevalue="<? echo $arOneValue['ID']; ?>"
					style="width: <? echo $strOneWidth; ?>; padding-top: <? echo $strOneWidth; ?>;"
				><span></span>
				<a href="javascript:void(0)"><span style="background-image:url('<? echo $arOneValue['PICT']['SRC']; ?>');"></span></a></li>
<?
			}
?>
			</ul>
			</div>
			<div class="bx_slide_left" style="<? echo $strSlideStyle; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_left" data-treevalue="<? echo $arProp['ID']; ?>"></div>
			<div class="bx_slide_right" style="<? echo $strSlideStyle; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_right" data-treevalue="<? echo $arProp['ID']; ?>"></div>
		</div>
	</div>
<?
		}
	}
	unset($arProp);
?>
</div>
<?
}
?>
<div class="item_info_section">
<?
if ((isset($arResult['OFFERS']) && !empty($arResult['OFFERS'])) || $arResult["CAN_BUY"])
{
	if ('Y' == $arParams['USE_PRODUCT_QUANTITY'])
	{
?>
	<span class="item_section_name_gray"><? echo GetMessage('CATALOG_QUANTITY'); ?></span>
	<div class="item_buttons vam">
		<span class="item_buttons_counter_block">
			<a href="javascript:void(0)" class="bx_bt_white bx_small bx_fwb" id="<? echo $arItemIDs['QUANTITY_DOWN']; ?>">-</a>
			<input id="<? echo $arItemIDs['QUANTITY']; ?>" type="text" class="tac transparent_input" value="<? echo (isset($arResult['OFFERS']) && !empty($arResult['OFFERS'])
					? 1
					: $arResult['CATALOG_MEASURE_RATIO']
				); ?>">
			<a href="javascript:void(0)" class="bx_bt_white bx_small bx_fwb" id="<? echo $arItemIDs['QUANTITY_UP']; ?>">+</a>
			<span id="<? echo $arItemIDs['QUANTITY_MEASURE']; ?>"><? echo (isset($arResult['CATALOG_MEASURE_NAME']) ? $arResult['CATALOG_MEASURE_NAME'] : ''); ?></span>
		</span>
		<span class="item_buttons_counter_block">
			<a href="javascript:void(0);" class="bx_big bx_bt_blue bx_cart" id="<? echo $arItemIDs['BUY_LINK']; ?>"><span></span><? echo ('' != $arParams['MESS_BTN_ADD_TO_BASKET']
					? $arParams['MESS_BTN_ADD_TO_BASKET']
					: GetMessage('CT_BCE_CATALOG_ADD')
				); ?></a>
<?
		if ('Y' == $arParams['DISPLAY_COMPARE'])
		{
?>
			<a href="javascript:void(0)" class="bx_big bx_bt_white bx_cart" style="margin-left: 10px"><? echo ('' != $arParams['MESS_BTN_COMPARE']
					? $arParams['MESS_BTN_COMPARE']
					: GetMessage('CT_BCE_CATALOG_COMPARE')
				); ?></a>
<?
		}
?>
		</span>
	</div>
<?
		if ('Y' == $arParams['SHOW_MAX_QUANTITY'])
		{
			if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))
			{
?>
	<p id="<? echo $arItemIDs['QUANTITY_LIMIT']; ?>" style="display: none;"><? echo GetMessage('OSTATOK'); ?>: <span></span></p>
<?
			}
			else
			{
				if ('Y' == $arResult['CATALOG_QUANTITY_TRACE'] && 'N' == $arResult['CATALOG_CAN_BUY_ZERO'])
				{
?>
	<p id="<? echo $arItemIDs['QUANTITY_LIMIT']; ?>"><? echo GetMessage('OSTATOK'); ?>: <span><? $arResult['CATALOG_QUANTITY']; ?></span></p>
<?
				}
			}
		}
	}
	else
	{
?>
	<div class="item_buttons vam">
		<span class="item_buttons_counter_block">
			<a href="javascript:void(0);" class="bx_big bx_bt_blue bx_cart" id="<? echo $arItemIDs['BUY_LINK']; ?>"><span></span><? echo ('' != $arParams['MESS_BTN_ADD_TO_BASKET']
					? $arParams['MESS_BTN_ADD_TO_BASKET']
					: GetMessage('CT_BCE_CATALOG_ADD')
				); ?></a>
<?
		if ('Y' == $arParams['DISPLAY_COMPARE'])
		{
?>
			<a id="<? echo $arItemIDs['COMPARE_LINK']; ?>" href="javascript:void(0)" class="bx_big bx_bt_white bx_cart" style="margin-left: 10px"><? echo ('' != $arParams['MESS_BTN_COMPARE']
					? $arParams['MESS_BTN_COMPARE']
					: GetMessage('CT_BCE_CATALOG_COMPARE')
				); ?></a>
<?
		}
?>
		</span>
	</div>
<?
	}
}
else
{


}
?>

</div>
<div class="item_info_section">
    <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/catalog_info.php"), false);?>
</div>
<div class="item_info_section">

    <?if($arParams["USE_STORE"] == "Y" && IsModuleInstalled("catalog"))
    {
    ?>
    <?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount", ".default", array(
        "PER_PAGE" => "10",
        "USE_STORE_PHONE" => $arParams['STORE_PARAMS']["USE_STORE_PHONE"],
        "SCHEDULE" => $arParams['STORE_PARAMS']["USE_STORE_SCHEDULE"],
        "USE_MIN_AMOUNT" => $arParams['STORE_PARAMS']["USE_MIN_AMOUNT"],
        "MIN_AMOUNT" => $arParams['STORE_PARAMS']["MIN_AMOUNT"],
        "ELEMENT_ID" => $arResult['ID'],
        "STORE_PATH"  =>  $arParams['STORE_PARAMS']["STORE_PATH"],
        "MAIN_TITLE"  =>  $arParams['STORE_PARAMS']["MAIN_TITLE"],
    ),
    $component
);?>
    <?
    }?>

</div>
			<div class="clb"></div>
		</div>

		<div class="bx_md">
            <div class="item_info_separator"></div>

            <div class="item_info_section">
<?
if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))
{
	if ($arResult['OFFER_GROUP'])
	{
		foreach ($arResult['OFFERS'] as $arOffer)
		{
			if (!$arOffer['OFFER_GROUP'])
				continue;
?>
	<span id="<? echo $arItemIDs['OFFER_GROUP'].$arOffer['ID']; ?>" style="display: none;">
<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor",
	".default",
	array(
		"IBLOCK_ID" => $arResult["OFFERS_IBLOCK"],
		"ELEMENT_ID" => $arOffer['ID'],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?><?
?>
	</span>
<?
		}
	}
}
else
{
?><?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor",
	".default",
	array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_ID" => $arResult["ID"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?><?
}
?>
</div>
		</div>
		<div class="bx_rb">
<div class="item_info_section">
<?
if ('' != $arResult['DETAIL_TEXT'])
{
?>
	<div class="bx_item_description">
		<div class="bx_item_section_name_gray" style="border-bottom: 1px solid #f2f2f2;"><? echo GetMessage('FULL_DESCRIPTION'); ?></div>
<?
	if ('html' == $arResult['DETAIL_TEXT_TYPE'])
	{
		echo $arResult['DETAIL_TEXT'];
	}
	else
	{
		?><p><? echo $arResult['DETAIL_TEXT']; ?></p><?
	}
?>
	</div>
<?
}
?>
</div>
		</div>
		<div class="bx_lb">
<div class="tac ovh">
<?/*$APPLICATION->IncludeComponent(
	"bitrix:catalog.socnets.buttons",
	"",
	array(
		"URL_TO_LIKE" => $APPLICATION->GetCurPageParam(),
		"TITLE" => "",
		"DESCRIPTION" => "",
		"IMAGE" => "",
		"FB_USE" => "Y",
		"TW_USE" => "Y",
		"GP_USE" => "Y",
		"VK_USE" => "Y",
		"TW_VIA" => "",
		"TW_HASHTAGS" => "",
		"TW_RELATED" => ""
	),
	$component,
	array("HIDE_ICONS" => "Y")
);*/?>
</div>
<div class="tab-section-container">
<?
if ('Y' == $arParams['USE_COMMENTS'])
{
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.comments",
	"",
	array(
		"ELEMENT_ID" => $arResult['ID'],
		"ELEMENT_CODE" => "",
		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		"URL_TO_COMMENT" => "",
		"WIDTH" => "",
		"COMMENTS_COUNT" => "5",
		"BLOG_USE" => $arParams['BLOG_USE'],
		"FB_USE" => $arParams['FB_USE'],
		"FB_APP_ID" => $arParams['FB_APP_ID'],
		"VK_USE" => $arParams['VK_USE'],
		"VK_API_ID" => $arParams['VK_API_ID'],
		"CACHE_TYPE" => $arParams['CACHE_TYPE'],
		"CACHE_TIME" => $arParams['CACHE_TIME'],
		"BLOG_TITLE" => "",
		"BLOG_URL" => "",
		"PATH_TO_SMILE" => "/bitrix/images/blog/smile/",
		"EMAIL_NOTIFY" => "N",
		"AJAX_POST" => "Y",
		"SHOW_SPAM" => "Y",
		"SHOW_RATING" => "N",
		"FB_TITLE" => "",
		"FB_USER_ADMIN_ID" => "",
		"FB_APP_ID" => $arParams['FB_APP_ID'],
		"FB_COLORSCHEME" => "light",
		"FB_ORDER_BY" => "reverse_time",
		"VK_TITLE" => "",
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?>
<?
}
?>
</div>
		</div>
			<div style="clear: both;"></div>
	</div>
	<div class="clb"></div>
</div><?
if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))
	{
		foreach ($arResult['JS_OFFERS'] as &$arOneJS)
		{
			if ($arOneJS['PRICE']['DISCOUNT_VALUE'] != $arOneJS['PRICE']['VALUE'])
			{
				$arOneJS['PRICE']['PRINT_DISCOUNT_DIFF'] = GetMessage('ECONOMY_INFO', array('#ECONOMY#' => $arOneJS['PRICE']['PRINT_DISCOUNT_DIFF']));
				$arOneJS['PRICE']['DISCOUNT_DIFF_PERCENT'] = -$arOneJS['PRICE']['DISCOUNT_DIFF_PERCENT'];
			}
			$strProps = '';
			if ($arResult['SHOW_OFFERS_PROPS'])
			{
				if (!empty($arOneJS['DISPLAY_PROPERTIES']))
				{
					foreach ($arOneJS['DISPLAY_PROPERTIES'] as $arOneProp)
					{
						$strProps .= '<dt><strong>'.$arOneProp['NAME'].'</strong> '.(
							is_array($arOneProp['VALUE'])
							? implode(' / ', $arOneProp['VALUE'])
							: $arOneProp['VALUE']
						).'</dt>';
					}
				}
			}
			$arOneJS['DISPLAY_PROPERTIES'] = $strProps;
		}
		if (isset($arOneJS))
			unset($arOneJS);
		$arJSParams = array(
			'PRODUCT_TYPE' => $arResult['CATALOG_TYPE'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_ADD_BASKET_BTN' => true,
			'SHOW_BUY_BTN' => false,
			'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
			'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
			'DISPLAY_COMPARE' => ('Y' == $arParams['DISPLAY_COMPARE']),
			'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
			'OFFER_GROUP' => $arResult['OFFER_GROUP'],
			'VISUAL' => array(
				'BIG_SLIDER_ID' => $arItemIDs['ID'],
				'ID' => $arItemIDs['ID'],
				'PICT_ID' => $arItemIDs['PICT'],
				'QUANTITY_ID' => $arItemIDs['QUANTITY'],
				'QUANTITY_UP_ID' => $arItemIDs['QUANTITY_UP'],
				'QUANTITY_DOWN_ID' => $arItemIDs['QUANTITY_DOWN'],
				'QUANTITY_MEASURE' => $arItemIDs['QUANTITY_MEASURE'],
				'QUANTITY_LIMIT' => $arItemIDs['QUANTITY_LIMIT'],
				'PRICE_ID' => $arItemIDs['PRICE'],
				'OLD_PRICE_ID' => $arItemIDs['OLD_PRICE'],
				'DISCOUNT_VALUE_ID' => $arItemIDs['DISCOUNT_PRICE'],
				'DISCOUNT_PERC_ID' => $arItemIDs['DISCOUNT_PICT_ID'],
				'NAME_ID' => $arItemIDs['NAME'],
				'TREE_ID' => $arItemIDs['PROP_DIV'],
				'TREE_ITEM_ID' => $arItemIDs['PROP'],
				'SLIDER_CONT_OF_ID' => $arItemIDs['SLIDER_CONT_OF_ID'],
				'SLIDER_LIST_OF_ID' => $arItemIDs['SLIDER_LIST_OF_ID'],
				'SLIDER_LEFT_OF_ID' => $arItemIDs['SLIDER_LEFT_OF_ID'],
				'SLIDER_RIGHT_OF_ID' => $arItemIDs['SLIDER_RIGHT_OF_ID'],
				'BUY_ID' => $arItemIDs['BUY_LINK'],
				'ADD_BASKET_ID' => $arItemIDs['ADD_BASKET_LINK'],
				'COMPARE_LINK_ID' => $arItemIDs['COMPARE_LINK'],
				'DISPLAY_PROP_DIV' => $arItemIDs['DISPLAY_PROP_DIV'],
				'OFFER_GROUP' => $arItemIDs['OFFER_GROUP'],
				'ZOOM_DIV' => $arItemIDs['ZOOM_DIV'],
				'ZOOM_PICT' => $arItemIDs['ZOOM_PICT']
			),
			'DEFAULT_PICTURE' => array(
				'PREVIEW_PICTURE' => $arResult['PREVIEW_PICTURE'],
				'DETAIL_PICTURE' => $arResult['DETAIL_PICTURE']
			),
			'OFFERS' => $arResult['JS_OFFERS'],
			'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'],
			'TREE_PROPS' => $arSkuProps,
			'AJAX_PATH' => POST_FORM_ACTION_URI,
			'MESS' => array(
				'ECONOMY_INFO' => GetMessage('ECONOMY_INFO')
			)
		);
	}
	else
	{
		$arJSParams = array(
			'PRODUCT_TYPE' => $arResult['CATALOG_TYPE'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_ADD_BASKET_BTN' => true,
			'SHOW_BUY_BTN' => false,
			'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
			'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
			'DISPLAY_COMPARE' => ('Y' == $arParams['DISPLAY_COMPARE']),
			'VISUAL' => array(
				'BIG_SLIDER_ID' => $arItemIDs['ID'],
				'ID' => $arItemIDs['ID'],
				'PICT_ID' => $arItemIDs['PICT'],
				'QUANTITY_ID' => $arItemIDs['QUANTITY'],
				'QUANTITY_UP_ID' => $arItemIDs['QUANTITY_UP'],
				'QUANTITY_DOWN_ID' => $arItemIDs['QUANTITY_DOWN'],
				'PRICE_ID' => $arItemIDs['PRICE'],
				'OLD_PRICE_ID' => $arItemIDs['OLD_PRICE'],
				'DISCOUNT_VALUE_ID' => $arItemIDs['DISCOUNT_PRICE'],
				'DISCOUNT_PERC_ID' => $arItemIDs['DISCOUNT_PICT_ID'],
				'NAME_ID' => $arItemIDs['NAME'],
				'TREE_ID' => $arItemIDs['PROP_DIV'],
				'TREE_ITEM_ID' => $arItemIDs['PROP'],
				'SLIDER_CONT_OF_ID' => $arItemIDs['SLIDER_CONT_OF_ID'],
				'SLIDER_LIST_OF_ID' => $arItemIDs['SLIDER_LIST_OF_ID'],
				'SLIDER_LEFT_OF_ID' => $arItemIDs['SLIDER_LEFT_OF_ID'],
				'SLIDER_RIGHT_OF_ID' => $arItemIDs['SLIDER_RIGHT_OF_ID'],
				'BUY_ID' => $arItemIDs['BUY_LINK'],
				'ADD_BASKET_ID' => $arItemIDs['ADD_BASKET_LINK'],
				'COMPARE_LINK_ID' => $arItemIDs['COMPARE_LINK'],
			),
			'PRODUCT' => array(
				'ID' => $arResult['ID'],
				'PICT' => $arResult['DETAIL_PICTURE'],
				'NAME' => $arResult['~NAME'],
				'SUBSCRIPTION' => true,
				'PRICE' => $arResult['MIN_PRICE'],
				'CAN_BUY' => $arResult['CAN_BUY'],
				'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
				'QUANTITY_FLOAT' => is_double($arResult['CATALOG_MEASURE_RATIO']),
				'MAX_QUANTITY' => $arResult['CATALOG_QUANTITY'],
				'STEP_QUANTITY' => $arResult['CATALOG_MEASURE_RATIO'],
				'BUY_URL' => $arResult['~BUY_URL'],
			),
			'AJAX_PATH' => POST_FORM_ACTION_URI,
			'MESS' => array()
		);
	}
?>
<script type="text/javascript">
var <? echo $strObName; ?> = new JCCatalogElement(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
</script>