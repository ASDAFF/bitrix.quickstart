<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

?><div class="search-page"><?
?><form action="" method="get"><?
	?><div class="field-wrap"><?
		?><div class="row"><?
			?><div class="col col-md-5"><?
				if($arParams["USE_SUGGEST"] === "Y") {
					if(strlen($arResult["REQUEST"]["~QUERY"]) && is_object($arResult["NAV_RESULT"])) {
						$arResult["FILTER_MD5"] = $arResult["NAV_RESULT"]->GetFilterMD5();
						$obSearchSuggest = new CSearchSuggest($arResult["FILTER_MD5"], $arResult["REQUEST"]["~QUERY"]);
						$obSearchSuggest->SetResultCount($arResult["NAV_RESULT"]->NavRecordCount);
					}
					?><?$APPLICATION->IncludeComponent(
						"bitrix:search.suggest.input",
						"",
						array(
							"NAME" => "q",
							"VALUE" => $arResult["REQUEST"]["~QUERY"],
							"INPUT_SIZE" => 40,
							"DROPDOWN_SIZE" => 10,
							"FILTER_MD5" => $arResult["FILTER_MD5"],
						),
						$component, array("HIDE_ICONS" => "Y")
					);?><?
				} else {
					?><input class="form-control" type="text" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" size="40" /><?
				}
			?></div><?
			?><div class="col col-md-3"><?
				if($arParams["SHOW_WHERE"]) {
					?><select class="form-control" name="where"><?
						?><option value=""><?=GetMessage("SEARCH_ALL")?></option><?
						foreach($arResult["DROPDOWN"] as $key=>$value) {
							?><option value="<?=$key?>"<?if($arResult["REQUEST"]["WHERE"]==$key) echo " selected"?>><?=$value?></option><?
						}
					?></select><?
				}
			?></div>
			<div class="col col-md-2">
				&nbsp;<input class="btn btn-primary" type="submit" value="<?=GetMessage("SEARCH_GO")?>" />
				<input type="hidden" name="how" value="<?echo $arResult["REQUEST"]["HOW"]=="d"? "d": "r"?>" />
			</div>
		</div>
	</div>

</form><br />
<?if($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false):?>
<?elseif($arResult["ERROR_CODE"]!=0):?>
	<p><?=GetMessage("SEARCH_ERROR")?></p>
	<?ShowError($arResult["ERROR_TEXT"]);?>
	<p><?=GetMessage("SEARCH_CORRECT_AND_CONTINUE")?></p>
	<br /><br />
	<p><?=GetMessage("SEARCH_SINTAX")?><br /><b><?=GetMessage("SEARCH_LOGIC")?></b></p>
	<div class="table-responsive">
		<table class="table table-striped table-hover" border="0" cellpadding="5">
			<tbody>
				<tr>
					<td align="center" valign="top"><?=GetMessage("SEARCH_OPERATOR")?></td><td valign="top"><?=GetMessage("SEARCH_SYNONIM")?></td>
					<td><?=GetMessage("SEARCH_DESCRIPTION")?></td>
				</tr>
				<tr>
					<td align="center" valign="top"><?=GetMessage("SEARCH_AND")?></td><td valign="top">and, &amp;, +</td>
					<td><?=GetMessage("SEARCH_AND_ALT")?></td>
				</tr>
				<tr>
					<td align="center" valign="top"><?=GetMessage("SEARCH_OR")?></td><td valign="top">or, |</td>
					<td><?=GetMessage("SEARCH_OR_ALT")?></td>
				</tr>
				<tr>
					<td align="center" valign="top"><?=GetMessage("SEARCH_NOT")?></td><td valign="top">not, ~</td>
					<td><?=GetMessage("SEARCH_NOT_ALT")?></td>
				</tr>
				<tr>
					<td align="center" valign="top">( )</td>
					<td valign="top">&nbsp;</td>
					<td><?=GetMessage("SEARCH_BRACKETS_ALT")?></td>
				</tr>
			</tbody>
		</table>
	</div>
<?php
elseif(count($arResult["SEARCH"])>0):
	if($arParams["DISPLAY_TOP_PAGER"] != "N"):
		echo $arResult["NAV_STRING"];
	endif;
	?>
	<br /><hr />
	<?php 
	if (!empty($arResult['EXT_SEARCH']['IBLOCK']['IBLOCKS'])):
		foreach($arResult["EXT_SEARCH"]['IBLOCK']['IBLOCKS'] as $key=>$arSearch):
			if($key == $arParams["IBLOCK_ID"]):
				global $arrSearchFilter;
				$arIds = array();
				foreach ($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$key] as $arItem)
				{
					if( $arItem['ITEM_ID']!=$arItem['ID'] && intval($arItem['ITEM_ID'])>0 )
					{
						$arIds[] = $arItem['ITEM_ID'];
					}
				}				
				if (is_array($arIds) && count($arIds) > 0):
					$arrSearchFilter = array('ID' => $arIds);
					$arComponentParams = array(
						"IBLOCK_TYPE" => "catalog",
							"IBLOCK_ID" => $key,
							"SECTION_ID" => "",
							"SECTION_CODE" => "",
							"SECTION_USER_FIELDS" => array(
								0 => "",
								1 => "",
							),
							"ELEMENT_SORT_FIELD" => "shows",
							"ELEMENT_SORT_ORDER" => "desc",
							"ELEMENT_SORT_FIELD2" => "sort",
							"ELEMENT_SORT_ORDER2" => "asc",
							"FILTER_NAME" => "arrSearchFilter",
							"INCLUDE_SUBSECTIONS" => "Y",
							"SHOW_ALL_WO_SECTION" => "Y",
							"HIDE_NOT_AVAILABLE" => "N",
							"PAGE_ELEMENT_COUNT" => "40",
							"LINE_ELEMENT_COUNT" => "",
							"PROPERTY_CODE" => array(
								0 => "",
								1 => "",
							),
							"OFFERS_FIELD_CODE" => $arParams['OFFERS_FIELD_CODE'],
							"OFFERS_SORT_FIELD" => "catalog_PRICE_".$arParams['SKU_PRICE_SORT_ID'],
							"OFFERS_SORT_ORDER" => "asc",
							"OFFERS_SORT_FIELD2" => "sort",
							"OFFERS_SORT_ORDER2" => "asc",
							"OFFERS_LIMIT" => "0",
							"TEMPLATE_VIEW" => $arParams['TEMPLATE_VIEW'],
							"SHOW_ERROR_EMPTY_ITEMS" => "Y",
							"PARENT_TITLE" => $arSearch['NAME'],
							"SHOW_PARENT" => $arParams['SHOW_PARENT'],
							"USE_HOVER_POPUP" => $arParams['USE_HOVER_POPUP'],
							"USE_SLIDER_MODE" => $arParams['USE_SLIDER_MODE'],
							"SECTION_URL" => "",
							"DETAIL_URL" => "",
							"SECTION_ID_VARIABLE" => "SECTION_ID",
							"AJAX_MODE" => "N",
							"AJAX_OPTION_JUMP" => "N",
							"AJAX_OPTION_STYLE" => "N",
							"AJAX_OPTION_HISTORY" => "N",
							"AJAX_OPTION_ADDITIONAL" => "",
							"CACHE_TYPE" => "A",
							"CACHE_TIME" => "36000000",
							"CACHE_GROUPS" => "Y",
							"SET_TITLE" => "N",
							"SET_BROWSER_TITLE" => "N",
							"BROWSER_TITLE" => "-",
							"SET_META_KEYWORDS" => "N",
							"META_KEYWORDS" => "-",
							"SET_META_DESCRIPTION" => "N",
							"META_DESCRIPTION" => "-",
							"ADD_SECTIONS_CHAIN" => "N",
							"SET_STATUS_404" => "N",
							"CACHE_FILTER" => "N",
							"NOVELTY_TIME" => $arParams['TEMPLATE_VIEW'],
							"USE_FAVORITE" => $arParams['USE_FAVORITE'],
							"USE_SHARE" => $arParams['USE_SHARE'],
							"SHOW_SECTION_URL" => $arParams['SHOW_SECTION_URL'],
							"SECTION_PAGE_MORE_URL" => "",
							"SOCIAL_SERVICES" => $arParams['SOCIAL_SERVICES'],
							"SOCIAL_SKIN" => $arParams['SOCIAL_SKIN'],
							"SOCIAL_POPUP_TYPE" => $arParams['SOCIAL_POPUP_TYPE'],
							"ACTION_VARIABLE" => "action",
							"PRODUCT_ID_VARIABLE" => "id",
							"PRICE_CODE" => $arParams['PRICE_CODE'],
							"USE_PRICE_COUNT" => "N",
							"SHOW_PRICE_COUNT" => "1",
							"PRICE_VAT_INCLUDE" => $arParams['PRICE_VAT_INCLUDE'],
							"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
							"CURRENCY_ID" => $arParams['CURRENCY_ID'],
							"SHOW_OLD_PRICE" => $arParams['SHOW_OLD_PRICE'],
							"SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
							
							"BASKET_URL" => "/personal/cart/",
							"USE_PRODUCT_QUANTITY" => "N",
							"PRODUCT_QUANTITY_VARIABLE" => "",
							"ADD_PROPERTIES_TO_BASKET" => "N",
							"PRODUCT_PROPS_VARIABLE" => "prop",
							"PARTIAL_PRODUCT_PROPERTIES" => "N",
							"PRODUCT_PROPERTIES" => array(
							),
							"OFFERS_CART_PROPERTIES" => $arParams['OFFERS_CART_PROPERTIES'],
							"DISPLAY_COMPARE" => $arParams['DISPLAY_COMPARE'],
							"COMPARE_PATH" => $arParams['COMPARE_PATH'],
							"PAGER_TEMPLATE" => "",
							"DISPLAY_TOP_PAGER" => "N",
							"DISPLAY_BOTTOM_PAGER" => "N",
							"PAGER_TITLE" => "",
							"PAGER_SHOW_ALWAYS" => "N",
							"PAGER_DESC_NUMBERING" => "N",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
							"PAGER_SHOW_ALL" => "N",
							"USE_AJAXPAGES" => "N",
							"RSFLYAWAY_TEMPLATE" => $arParams["TEMPLATE_VIEW"],
							"RSFLYAWAY_PROP_SKU_MORE_PHOTO" => $arParams["RSFLYAWAY_PROP_SKU_MORE_PHOTO"],
							"OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
							"OFFER_TREE_COLOR_PROPS" => $arParams["OFFER_TREE_COLOR_PROPS"],
							"RSFLYAWAY_PROP_OFF_POPUP" => $arParams["USE_HOVER_POPUP"],
					);
					?><div class="rs_section-box"><?
						$APPLICATION->IncludeComponent(
							"bitrix:catalog.section",
							"flyaway",
							$arComponentParams,
							$component//,
							//array('HIDE_ICONS'=>'Y')
						);
					?></div><?
				endif;
			endif;
		endforeach;
	endif;
	foreach ($arResult['EXT_SEARCH']['IBLOCK']['IBLOCKS'] as $iblock_id => $arIblock):
		if ($iblock_id == $arParams['IBLOCK_ID']):
			$c = false;
			foreach ($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$iblock_id] as $arItem):
				if ( intval($arItem['ITEM_ID'])<1 ):
					$c = true;
					break;
				endif;
			endforeach;
			if ($c):
				?><div class="rs_section-box"><?
					?><h2 class="head2"><?=$arIblock['NAME']?></h2><?
					foreach($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$iblock_id] as $arItem):
						if( intval($arItem['ITEM_ID'])<1 ):
							?><div class="rs_strip"><?
								?><a href="<?=$arItem['URL']?>"><?=$arItem['TITLE_FORMATED']?></a><?
								?><div><?=$arItem['BODY_FORMATED']?></div><?
								?><div><?=$arItem['CHAIN_PATH']?></div><?
							?></div><?
						endif;
					endforeach;
				?></div><?
			endif;
		endif;
	endforeach;
	// other
	foreach ($arResult['EXT_SEARCH']['IBLOCK']['IBLOCKS'] as $iblock_id => $arIblock):
		if ($iblock_id != $arParams['IBLOCK_ID']):
			?><div class="rs_section-box"><?
				?><h2 class="head2"><?=$arIblock['NAME']?></h2><?
				foreach ($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$iblock_id] as $arItem):
					?><div class="rs_strip"><?
						?><a href="<?=$arItem['URL']?>"><?=$arItem['TITLE_FORMATED']?></a><?
						?><div><?=$arItem['BODY_FORMATED']?></div><?
						?><div><?=$arItem['CHAIN_PATH']?></div><?
					?></div><?
				endforeach;
			?></div><?
		endif;
	endforeach;
	if (!empty($arResult['EXT_SEARCH']['OTHER']['ITEMS'])):
		?><div class="rs_section-box"><?
			?><h2 class="head2"><?=getMessage('FLW_SEARCH_OTHER')?></h2><?
			foreach ($arResult['EXT_SEARCH']['OTHER']['ITEMS'] as $arOther):
				?><div class="rs_strip"><?
					?><a href="<?=$arOther['URL']?>"><?=$arOther['TITLE_FORMATED']?></a><?
					?><div><?=$arOther['BODY_FORMATED']?></div><?
					?><div><?=$arOther['CHAIN_PATH']?></div><?
				?></div><?
			endforeach;
		?></div><?
	endif;

	if($arParams["DISPLAY_BOTTOM_PAGER"] != "N"):
		echo $arResult["NAV_STRING"];
	endif;
else:
	ShowNote(GetMessage("SEARCH_NOTHING_TO_FOUND"));
endif; ?>
</div>