<?php

use \Bitrix\Main\Localization\Loc;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();


$formId = $this->getEditAreaId('input');
?>
<div class="search clearfix">
	<form action="" method="get" class="search__form">
        <div class="form-group form-inline">
            <input type="hidden" name="tags" value="<?=$arResult['REQUEST']['TAGS']?>">
            
            <div class="input-group">
                <button class="search__btn" for="#<?=$menuId?>" type="submit" value="">
                    <svg class="icon-glass icon-svg"><use xlink:href="#svg-glass"></use></svg>
                </button>
                
                <?if($arParams["USE_SUGGEST"] === "Y"):
                    if(strlen($arResult["REQUEST"]["~QUERY"]) && is_object($arResult["NAV_RESULT"]))
                    {
                        $arResult["FILTER_MD5"] = $arResult["NAV_RESULT"]->GetFilterMD5();
                        $obSearchSuggest = new CSearchSuggest($arResult["FILTER_MD5"], $arResult["REQUEST"]["~QUERY"]);
                        $obSearchSuggest->SetResultCount($arResult["NAV_RESULT"]->NavRecordCount);
                    }
                    ?>
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:search.suggest.input",
                        "al",
                        array(
                            "NAME" => "q",
                            "VALUE" => $arResult["REQUEST"]["~QUERY"],
                            "INPUT_SIZE" => 40,
                            "DROPDOWN_SIZE" => 10,
                            "FILTER_MD5" => $arResult["FILTER_MD5"],
                        ),
                        $component, array("HIDE_ICONS" => "Y")
                    );?>
                <?else:?>
                    <input class="search__input form-control" type="text" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" size="40" />
                <?endif;?>
            </div>
            
            <?if($arParams["SHOW_WHERE"]):?>
                <select class="form-control" name="where">
                <option value=""><?=GetMessage("SEARCH_ALL")?></option>
                <?foreach($arResult["DROPDOWN"] as $key=>$value):?>
                <option value="<?=$key?>"<?if($arResult["REQUEST"]["WHERE"]==$key) echo " selected"?>><?=$value?></option>
                <?endforeach?>
                </select>
            <?endif;?>
            <input type="hidden" name="how" value="<?=$arResult['REQUEST']['HOW']=='d'? 'd': 'r'?>">
        </div>
            
        <?if($arParams["SHOW_WHEN"]):?>
            <div class="form-group">
                <script>
                var switch_search_params = function()
                {
                    var sp = document.getElementById('search_params');
                    var flag;
                    var i;

                    if(sp.style.display == 'none')
                    {
                        flag = false;
                        sp.style.display = 'block'
                    }
                    else
                    {
                        flag = true;
                        sp.style.display = 'none';
                    }

                    var from = document.getElementsByName('from');
                    for(i = 0; i < from.length; i++)
                        if(from[i].type.toLowerCase() == 'text')
                            from[i].disabled = flag;

                    var to = document.getElementsByName('to');
                    for(i = 0; i < to.length; i++)
                        if(to[i].type.toLowerCase() == 'text')
                            to[i].disabled = flag;

                    return false;
                }
                </script>
                <label class="search-page-params anchor form-label" href="#" onclick="return switch_search_params()"><?echo GetMessage('CT_BSP_ADDITIONAL_PARAMS')?></label>
                <div id="search_params" class="search-page-params" style="display:<?echo $arResult["REQUEST"]["FROM"] || $arResult["REQUEST"]["TO"]? 'block': 'none'?>">
                    <?$APPLICATION->IncludeComponent(
                        'bitrix:main.calendar',
                        'al',
                        array(
                            'SHOW_INPUT' => 'Y',
                            'INPUT_NAME' => 'from',
                            'INPUT_VALUE' => $arResult["REQUEST"]["~FROM"],
                            'INPUT_NAME_FINISH' => 'to',
                            'INPUT_VALUE_FINISH' =>$arResult["REQUEST"]["~TO"],
                            'INPUT_ADDITIONAL_ATTR' => 'size="10"',
                        ),
                        null,
                        array('HIDE_ICONS' => 'Y')
                    );?>
                </div>
            </div>
        <?endif?>
	</form>
    
    <?if(isset($arResult["REQUEST"]["ORIGINAL_QUERY"])):
	?>
	<div class="search-language-guess">
		<?echo GetMessage("CT_BSP_KEYBOARD_WARNING", array("#query#"=>'<a href="'.$arResult["ORIGINAL_QUERY_URL"].'">'.$arResult["REQUEST"]["ORIGINAL_QUERY"].'</a>'))?>
	</div><br /><?
    endif;?>
   
<?if($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false):?>
<?elseif($arResult["ERROR_CODE"]!=0):?>
	<?php ShowError($arResult['ERROR_TEXT']) ?>
	<p><?=getMessage('SEARCH_CORRECT_AND_CONTINUE')?></p><br /><br />
<?php elseif (count($arResult['SEARCH']) > 0): ?>
	<?php if ($arParams['DISPLAY_TOP_PAGER'] != 'N'): ?>
		<div class="search__pagenav"><?=$arResult['NAV_STRING']?></div>
	<?php endif; ?>

		<?php if (!empty($arResult['EXT_SEARCH']['IBLOCK']['IBLOCKS'])): ?>
            <?php
			foreach ($arResult['EXT_SEARCH']['IBLOCK']['IBLOCKS'] as $iblock_id => $arIblock) {
				// catalog
				if (in_array($iblock_id, $arParams['IBLOCK_ID'])) {
					global $arrSearchFilter;
					$arIds = array();
					foreach ($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$iblock_id] as $arItem) {
						if ($arItem['ITEM_ID'] != $arItem['ID'] && intval($arItem['ITEM_ID']) > 0) {
							$arIds[] = $arItem['ITEM_ID'];
						}
					}
                    
					if (is_array($arIds) && count($arIds) > 0) {
						$arrSearchFilter = array('ID' => $arIds);
						$arComponentParams = array(
							"IBLOCK_TYPE" => "catalog",
							"IBLOCK_ID" => $iblock_id,
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
							"PAGE_ELEMENT_COUNT" => "20",
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
							"PARENT_TITLE" => $arIblock['NAME'],
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
							'BY_LINK' => 'Y',
						);
						
						foreach($arParams as $key => $val) {
							if (strpos($key, 'ADDITIONAL_PICT_PROP_') !== false && strpos($key, '~') !== 0) {
								$arComponentParams['ADDITIONAL_PICT_PROP'] = $val;
							} else if (strpos($key, 'ICON_NOVELTY_PROP_') !== false && strpos($key, '~') !== 0) {
								$arComponentParams['ICON_NOVELTY_PROP'] = $val;
							} else if (strpos($key, 'ICON_DEALS_PROP_') !== false && strpos($key, '~') !== 0) {
								$arComponentParams['ICON_DEALS_PROP'] = $val;
							} else if (strpos($key, 'ICON_DISCOUNT_PROP_') !== false && strpos($key, '~') !== 0) {
								$arComponentParams['ICON_DISCOUNT_PROP'] = $val;
							} else if (strpos($key, 'ICON_HITS_PROP_') !== false && strpos($key, '~') !== 0) {
								$arComponentParams['ICON_HITS_PROP'] = $val;
							} else if (strpos($key, 'OFFER_ADDITIONAL_PICT_PROP_') !== false && strpos($key, '~') !== 0) {
								$arComponentParams['OFFER_ADDITIONAL_PICT_PROP'] = $val;
							} else if (strpos($key, 'OFFER_TREE_PROPS_') !== false && strpos($key, '~') !== 0) {
								$arComponentParams['OFFER_TREE_PROPS'] = $val;
							} else if (strpos($key, 'OFFER_TREE_COLOR_PROPS_') !== false && strpos($key, '~') !== 0) {
								$arComponentParams['OFFER_TREE_COLOR_PROPS'] = $val;
							} else if (strpos($key, 'OFFER_TREE_BTN_PROPS_') !== false && strpos($key, '~') !== 0) {
								$arComponentParams['OFFER_TREE_BTN_PROPS'] = $val;
							}
						}
						$arComponentParams['OFFERS_PROPERTY_CODE'] = $arComponentParams['OFFER_TREE_PROPS'];
						$APPLICATION->IncludeComponent(
							"bitrix:catalog.section",
							"search_page",
							$arComponentParams,
							$component//,
							//array('HIDE_ICONS'=>'Y')
						);
					}
				}
			}
            ?>
			<?php foreach ($arResult['EXT_SEARCH']['IBLOCK']['IBLOCKS'] as $iblock_id => $arIblock): ?>
				<?php // catalog
				if (in_array($iblock_id,$arParams['IBLOCK_ID'])): 
					$c = false;
					foreach ($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$iblock_id] as $arItem) {
						if (intval($arItem['ITEM_ID']) < 1) {
							$c = true;
							break;
						}
					}
                ?>
					<?php if ($c): ?>
						<section class="search__items">
							<h2 class="search__title"><?=$arIblock['NAME']?></h2>
							<?php foreach($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$iblock_id] as $arItem): ?>
								<?php if (intval($arItem['ITEM_ID']) < 1): ?>
									<article class="search_item clearfix">
                                        <header class="search_item__head">
                                            <a class="search_item__name" href="<?=$arItem['URL']?>"><?=$arItem['TITLE_FORMATED']?></a>
                                            <ol class="search_item__breadcrumb breadcrumb"><?=$arItem['CHAIN_PATH']?></ol>
                                        </header>
                                        <div class="search_item__descr"><?=$arItem['BODY_FORMATED']?></div>
									</article>
								<?php endif; ?>
							<?php endforeach; ?>
						</section>
					<?php endif; ?>
				<?php endif; ?>
			<?php endforeach; ?>
			
            <?php
            // other
			foreach ($arResult['EXT_SEARCH']['IBLOCK']['IBLOCKS'] as $iblock_id => $arIblock): ?>
				<?php if (!in_array($iblock_id,$arParams['IBLOCK_ID'])): ?>
					<section class="search__items">
						<h2 class="search__title"><?=$arIblock['NAME']?></h2>
						<?php foreach ($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$iblock_id] as $arItem): ?>
							<article class="search_item clearfix">
                               <header class="search_item__head">
                                    <a class="search_item__name" href="<?=$arItem['URL']?>"><?=$arItem['TITLE_FORMATED']?></a>
                                    <ol class="search_item__breadcrumb breadcrumb"><?=$arItem['CHAIN_PATH']?></ol>
                                </header>
                                <div class="search_item__descr"><?=$arItem['BODY_FORMATED']?></div>
							</article>
						<?php endforeach; ?>
					</section>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
        
        <?php
		////////////////// OTHER
		if (!empty($arResult['EXT_SEARCH']['OTHER']['ITEMS'])):
        ?>
            <section class="search__items">
				<h2 class="search__title"><?=getMessage('RS_SLINE.BSP_SEARCH.OTHER')?></h2>
				<?php foreach ($arResult['EXT_SEARCH']['OTHER']['ITEMS'] as $arItem): ?>
                    <article class="search_item clearfix">
                       <header class="search_item__head">
                            <a class="search_item__name" href="<?=$arItem['URL']?>"><?=$arItem['TITLE_FORMATED']?></a>
                            <ol class="search_item__breadcrumb breadcrumb"><?=$arItem['CHAIN_PATH']?></ol>
                        </header>
                        <div class="search_item__descr"><?=$arItem['BODY_FORMATED']?></div>
                    </article>
				<?php endforeach; ?>
			</section>
		<?php endif; ?>
	
    <?php if($arParams['DISPLAY_BOTTOM_PAGER'] != 'N'): ?>
		<div class="search__pagenav"><?=$arResult['NAV_STRING']?></div>
	<?php endif; ?>
    
    <p>
	<?if($arResult["REQUEST"]["HOW"]=="d"):?>
		<a class="anchor" href="<?=$arResult["URL"]?>&amp;how=r<?echo $arResult["REQUEST"]["FROM"]? '&amp;from='.$arResult["REQUEST"]["FROM"]: ''?><?echo $arResult["REQUEST"]["TO"]? '&amp;to='.$arResult["REQUEST"]["TO"]: ''?>"><?=GetMessage("SEARCH_SORT_BY_RANK")?></a>&nbsp;|&nbsp;<b><?=GetMessage("SEARCH_SORTED_BY_DATE")?></b>
	<?else:?>
		<b><?=GetMessage("SEARCH_SORTED_BY_RANK")?></b>&nbsp;|&nbsp;<a class="anchor" href="<?=$arResult["URL"]?>&amp;how=d<?echo $arResult["REQUEST"]["FROM"]? '&amp;from='.$arResult["REQUEST"]["FROM"]: ''?><?echo $arResult["REQUEST"]["TO"]? '&amp;to='.$arResult["REQUEST"]["TO"]: ''?>"><?=GetMessage("SEARCH_SORT_BY_DATE")?></a>
	<?endif;?>
	</p>
<?php else: ?>
	<?php ShowNote(getMessage('SEARCH_NOTHING_TO_FOUND')) ?>
<?php endif; ?>