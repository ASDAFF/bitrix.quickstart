<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/*
 * $arItem['SECOND_PICT']
 */
if (!empty($arResult['ITEMS']))
{

	$arSkuTemplate = array();
	if (!empty($arResult['SKU_PROPS']))
	{
		foreach ($arResult['SKU_PROPS'] as &$arProp)
		{
			ob_start();
			if ('L' == $arProp['PROPERTY_TYPE'])
			{
				if (5 < $arProp['VALUES_COUNT'])
				{
					$strClass = 'bx_item_detail_size full';
					$strWidth = ($arProp['VALUES_COUNT']*20).'%';
					$strOneWidth = (100/$arProp['VALUES_COUNT']).'%';
					$strSlideStyle = '';
				}
				else
				{
					$strClass = 'bx_item_detail_size';
					$strWidth = '100%';
					$strOneWidth = '20%';
					$strSlideStyle = 'display: none;';
				}
				?>
				


<?
			}
			elseif ('E' == $arProp['PROPERTY_TYPE'])
			{
				if (5 < $arProp['VALUES_COUNT'])
				{
					$strClass = 'bx_item_detail_scu full';
					$strWidth = ($arProp['VALUES_COUNT']*20).'%';
					$strOneWidth = (100/$arProp['VALUES_COUNT']).'%';
					$strSlideStyle = '';
				}
				else
				{
					$strClass = 'bx_item_detail_scu';
					$strWidth = '100%';
					$strOneWidth = '20%';
					$strSlideStyle = 'display: none;';
				}
				?>
				
				



<?
			}
			$arSkuTemplate[$arProp['CODE']] = ob_get_contents();
			ob_end_clean();
		}
		unset($arProp);
	}
	

	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));
?>

<div class="products group">
 <h2><?=GetMessage("CR_TITLE_SPECIALOFFER")?></h2>
<ul class="catalog-list group">



<?
foreach ($arResult['ITEMS'] as $key => $arItem)
{
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
	$strMainID = $this->GetEditAreaId($arItem['ID']);

	$arItemIDs = array(
		'ID' => $strMainID,
		'PICT' => $strMainID.'_pict',
		'SECOND_PICT' => $strMainID.'_secondpict',
		'MAIN_PROPS' => $strMainID.'_main_props',

		'QUANTITY' => $strMainID.'_quantity',
		'QUANTITY_DOWN' => $strMainID.'_quant_down',
		'QUANTITY_UP' => $strMainID.'_quant_up',
		'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
		'BUY_LINK' => $strMainID.'_buy_link',
		'SUBSCRIBE_LINK' => $strMainID.'_subscribe',

		'PRICE' => $strMainID.'_price',
		'DSC_PERC' => $strMainID.'_dsc_perc',
		'SECOND_DSC_PERC' => $strMainID.'_second_dsc_perc',

		'PROP_DIV' => $strMainID.'_sku_tree',
		'PROP' => $strMainID.'_prop_',
		'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop'

	);

	$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/i", "x", $strMainID);

	?>
	
	

	
	
	
	<li class="itembg R2D2" itemscope itemtype = "http://schema.org/Product">
	<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
	<div class="picture"> 


		 <?$resize = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array("width"=>264, "height"=>264), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
          <img src="<?echo $resize['src']?>" width="<?echo $resize['width']?>" height="<?echo $resize['height']?>" alt="<?=$arItem["NAME"]?>" />

	</div>

	<h3><?=$arItem["NAME"]?></h3>
                    
     <div class="price"><span>

                                            <?
                            if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]))   //if product has offers
                            {
                                    if (count($arItem["OFFERS"]) > 1)
                                    {
                                            echo GetMessage("CR_PRICE_OT")."&nbsp;";
                                            echo $arItem["PRINT_MIN_OFFER_PRICE"];
                                    }
                                    else
                                    {
                                            foreach($arItem["OFFERS"] as $arOffer):?>
                                                    <?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
                                                            <?if($arPrice["CAN_ACCESS"]):?>
                                                                            <?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
                                   <?=$arPrice["PRINT_DISCOUNT_VALUE"]?>
                                               <?=$arPrice["PRINT_VALUE"]?>
                             <?else:?>
                                      <?=$arPrice["PRINT_VALUE"]?>
                                                                            <?endif?>
                                                            <?endif;?>
                                                    <?endforeach;?>
                                            <?endforeach;
                                    }
                            }
                            else // if product doesn't have offers
                            {
                                    if(count($arItem["PRICES"])>0 && $arItem['PROPERTIES']['MAXIMUM_PRICE']['VALUE'] == $arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE']):
                                            foreach($arItem["PRICES"] as $code=>$arPrice):
                                                    if($arPrice["CAN_ACCESS"]):
                                                            ?>
                                                                    <?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
                                                                            <?=$arPrice["PRINT_DISCOUNT_VALUE"]?>
               <?=$arPrice["PRINT_VALUE"]?>
                                                                    <?else:?>
              <?=$arPrice["PRINT_VALUE"]?>
                                                                    <?endif;?>
                                                            <?
                                                    endif;
                                            endforeach;
                                    else:
                                            $price_from = '';
                                            if($arItem['PROPERTIES']['MAXIMUM_PRICE']['VALUE'] > $arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE'])
                                            {
                                                    $price_from = GetMessage("CR_PRICE_OT")."&nbsp;";
                                            }
                                            CModule::IncludeModule("sale")
                                            ?>
                                            <?=$price_from?><?=FormatCurrency($arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE'], CSaleLang::GetLangCurrency(SITE_ID))?>
                                            <?
                                    endif;
                            }
                            ?><? if ($arItem["PROPERTIES"]["ED_IZM"]){?>/<?=$arItem["PROPERTIES"]["ED_IZM"]["VALUE"];?><?}?></span>
     </div>
	
	

	
		
		

</a>
</li>



<?
}
?><?
}
?>
</ul>
</div>