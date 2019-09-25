<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
/** @unique code EMARKET_BRAND */
/** @unique code EMARKET_SKU_COLOR */
/** @default CATALOG_COMPARE_LIST */

$this->setFrameMode(true);

if (!empty($arResult['ITEMS']))
{

$arSkuTemplate = array();
if (!empty($arResult['SKU_PROPS']))
{
	foreach ($arResult['SKU_PROPS'] as &$arProp)
	{
		ob_start();
		if ('TEXT' == $arProp['SHOW_MODE'])
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
			<div class="<? echo $strClass; ?>" id="#ITEM#_prop_<? echo $arProp['ID']; ?>_cont">
				<span class="bx_item_section_name_gray"><? echo htmlspecialcharsex($arProp['NAME']); ?></span>
				<div class="bx_size_scroller_container">
					<div class="bx_size">
						<ul id="#ITEM#_prop_<? echo $arProp['ID']; ?>_list" style="width: <? echo $strWidth; ?>;">
							<?foreach ($arProp['VALUES'] as $arOneValue) {?>
								<li
									data-treevalue="<? echo $arProp['ID'].'_'.$arOneValue['ID']; ?>"
									data-onevalue="<? echo $arOneValue['ID']; ?>"
									style="width: <? echo $strOneWidth; ?>;"
								>
									<i></i>
									<span class="cnt"><? echo htmlspecialcharsex($arOneValue['NAME']); ?></span>
								</li>
							<?}?>
						</ul>
					</div>
					<div class="bx_slide_left" id="#ITEM#_prop_<? echo $arProp['ID']; ?>_left" data-treevalue="<? echo $arProp['ID']; ?>" style="<? echo $strSlideStyle; ?>"></div>
					<div class="bx_slide_right" id="#ITEM#_prop_<? echo $arProp['ID']; ?>_right" data-treevalue="<? echo $arProp['ID']; ?>" style="<? echo $strSlideStyle; ?>"></div>
				</div>
			</div>
		<?
		}
		elseif ('PICT' == $arProp['SHOW_MODE'])
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
			<div class="<? echo $strClass; ?>" id="#ITEM#_prop_<? echo $arProp['ID']; ?>_cont">
				<?if('EMARKET_SKU_COLOR' != $arProp['CODE']){?>
				<span class="bx_item_section_name_gray"><? echo htmlspecialcharsex($arProp['NAME']); ?></span>
				<?}?>
				<div class="bx_scu_scroller_container">
					<div class="bx_scu">
						<ul id="#ITEM#_prop_<? echo $arProp['ID']; ?>_list" style="width: <? echo $strWidth; ?>;">
						<?foreach ($arProp['VALUES'] as $arOneValue) {?>
							<li
								data-treevalue="<? echo $arProp['ID'].'_'.$arOneValue['ID'] ?>"
								data-onevalue="<? echo $arOneValue['ID']; ?>"
								style="width: <? echo $strOneWidth; ?>; padding-top: <? echo $strOneWidth; ?>;"
								>
								
								<i title="<? echo htmlspecialcharsbx($arOneValue['NAME']); ?>"></i>
								<span class="cnt">
									<span class="cnt_item"
										style="background-image:url('<? echo $arOneValue['PICT']['SRC']; ?>');"
										title="<? echo htmlspecialcharsbx($arOneValue['NAME']); ?>"
									></span>
								</span>
							</li>
						<?}?>
						</ul>
					</div>
					<div class="bx_slide_left" id="#ITEM#_prop_<? echo $arProp['ID']; ?>_left" data-treevalue="<? echo $arProp['ID']; ?>" style="<? echo $strSlideStyle; ?>"></div>
					<div class="bx_slide_right" id="#ITEM#_prop_<? echo $arProp['ID']; ?>_right" data-treevalue="<? echo $arProp['ID']; ?>" style="<? echo $strSlideStyle; ?>"></div>
				</div>
			</div>
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

<?if($arParams['DISPLAY_COMPARE']){?>
	<input id="this_list" type="hidden" value="<?=$arParams['COMPARE_NAME']?>">
<?}?>

<div class="catalog-top <?if($GLOBALS['KRAYT_is_sb'])echo"is_sb"?> bx_catalog_list_home">
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
			'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
			'BASKET_PROP_DIV' => $strMainID.'_basket_prop',
		);		
		
		$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

		$strTitle = (
			isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && '' != isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"])
			? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]
			: $arItem['NAME']
		);
	?>
		<div class="item <?=$arItem['PROPERTIES']['EMARKET_TOP_TYPE']['VALUE_XML_ID']?> <?echo ($arItem['SECOND_PICT'] ? 'bx_catalog_item double' : 'bx_catalog_item');?>" id="<?=$strMainID?>">
            
            <?if('Y' == $arItem['PROPERTIES']['EMARKET_HIT']['VALUE']) {?>
				<a class="item_hit" href="<? echo $arItem['DETAIL_PAGE_URL']; ?>"></a>
			<?} elseif('Y' == $arItem['PROPERTIES']['EMARKET_NEW']['VALUE']) {?>
				<a class="item_new" href="<? echo $arItem['DETAIL_PAGE_URL']; ?>"></a>
			<?}?>
            
			<?//PICTURE?>
			<a id="<? echo $arItemIDs['PICT']; ?>"
				href="<? echo $arItem['DETAIL_PAGE_URL']; ?>"
				class="bx_catalog_item_images"
				style="background-image: url(<? echo $arItem['PREVIEW_PICTURE']['SRC']; ?>)"
				title="<? echo $strTitle; ?>">		
				<?if($arItem['LABEL']) {?>
					<div class="bx_stick average left top" title="<? echo $arItem['LABEL_VALUE']; ?>"><? echo $arItem['LABEL_VALUE']; ?></div>
				<?}?>
			</a>
			
			<?//NAME?>
			<div class="bx_catalog_item_title">
				<a href="<? echo $arItem['DETAIL_PAGE_URL']; ?>" title="<? echo $arItem['NAME']; ?>"><? echo $arItem['NAME']; ?></a>
			</div>
            
            <?//RATING?>
				<div class="bx_catalog_item_rating">
					<?	
					$rating = intval($arItem['PROPERTIES']['EMARKET_RATING']['VALUE']);
					for($i=1; $i<=10; $i++)
					{
						if(($i == $rating) && ($i%2)) 
						{
							echo '<div class="star half"></div>';
							$i++;
							continue;
						}
						if(!($i%2))
						{
							if($i < $rating)
							{
								echo '<div class="star"></div>';
							}
							elseif($i == $rating)
							{
								echo '<div class="star"></div>';
							}
							elseif($i > $rating)
							{
								echo '<div class="star empty"></div>';
							}
						}
					}
					?>
				</div>
			
			<?//PRICE?>
			<div class="bx_catalog_item_price">
				<div id="<? echo $arItemIDs['PRICE']; ?>" class="bx_price"><?
				if (!empty($arItem['MIN_PRICE']))
				{
					if ('N' == $arParams['PRODUCT_DISPLAY_MODE'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
					{
						echo GetMessage(
							'CT_BCS_TPL_MESS_PRICE_SIMPLE_MODE',
							array(
								'#PRICE#' => $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'],
								'#MEASURE#' => GetMessage(
									'CT_BCS_TPL_MESS_MEASURE_SIMPLE_MODE',
									array(
										'#VALUE#' => $arItem['MIN_PRICE']['CATALOG_MEASURE_RATIO'],
										'#UNIT#' => $arItem['MIN_PRICE']['CATALOG_MEASURE_NAME']
									)
								)
							)
						);
					}
					else
					{
						echo $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];
					}
					if ('Y' == $arParams['SHOW_OLD_PRICE'] && $arItem['MIN_PRICE']['DISCOUNT_VALUE'] < $arItem['MIN_PRICE']['VALUE'])
					{
						?> <span><? echo $arItem['MIN_PRICE']['PRINT_VALUE']; ?></span><?
					}
				}
				?>
				</div>
				<?if ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']) {?>
					<div id="<? echo $arItemIDs['DSC_PERC']; ?>"
						class="bx_stick_disc right bottom"
						style="display:<? echo (0 < $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'] ? '' : 'none'); ?>;">-<? echo $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']; ?>%</div>

					<div id="<? echo $arItemIDs['SECOND_DSC_PERC']; ?>"
						class="bx_stick_disc right bottom"
						style="display:<? echo (0 < $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'] ? '' : 'none'); ?>;">-<? echo $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']; ?>%</div>
				<?}?>
			</div>
			
		</div>
	<?}?>
	
	<div style="clear:both;"></div>	
</div>
<?
}

//echo '<pre>'; print_r($arResult); echo '</pre>';
//echo '<pre>'; print_r($_SESSION); echo '</pre>';
?>