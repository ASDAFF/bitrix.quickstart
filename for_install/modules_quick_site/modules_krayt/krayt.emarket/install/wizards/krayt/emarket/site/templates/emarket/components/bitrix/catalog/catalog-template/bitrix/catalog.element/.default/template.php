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
$this->setFrameMode(true);

$templateData = array(
	'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
	'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME']
);

$strMainID = $this->GetEditAreaId($arResult['ID']);
$arItemIDs = array(
	'ID' => $strMainID,
	'PICT' => $strMainID.'_pict',
	'DISCOUNT_PICT_ID' => $strMainID.'_dsc_pict',
	'STICKER_ID' => $strMainID.'_sticker',
	'BIG_SLIDER_ID' => $strMainID.'_big_slider',
	'BIG_IMG_CONT_ID' => $strMainID.'_bigimg_cont',
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
	'BASKET_PROP_DIV' => $strMainID.'_basket_prop',
);
$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);
$templateData['JS_OBJ'] = $strObName;

$strTitle = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && '' != $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
	: $arResult['NAME']
);
$strAlt = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && '' != $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
	: $arResult['NAME']
);
?>

<?if($arParams['DISPLAY_COMPARE']){?>
	<input id="this_list" type="hidden" value="<?=$arParams['COMPARE_NAME']?>">
<?}?>

<div class="emarket-catalog-detail bx_item_detail" id="<? echo $arItemIDs['ID']; ?>">
	<?
	reset($arResult['MORE_PHOTO']);
	$arFirstPhoto = current($arResult['MORE_PHOTO']);
	?>
	<div class="head clear">
	
	
		<!--left block with slider-->
		<div class="block left">
			<?/*slider block*/?>
			<div class="bx_item_slider" id="<? echo $arItemIDs['BIG_SLIDER_ID']; ?>">
				<div class="bx_bigimages" id="<? echo $arItemIDs['BIG_IMG_CONT_ID']; ?>">
					<div class="bx_bigimages_imgcontainer">
						<span class="bx_bigimages_aligner">
                        <img
                            data-zoom-image="<? echo $arFirstPhoto['SRC']; ?>"                        
							id="<? echo $arItemIDs['PICT']; ?>"
							src="<? echo $arFirstPhoto['SRC']; ?>"
							alt="<? echo $strAlt; ?>"
							title="<? echo $strTitle; ?>"
                            class='zoom-img'
						></span>
						<?if ($arResult['LABEL']) {?>
							<div class="bx_stick new" id="<? echo $arItemIDs['STICKER_ID'] ?>"><? echo $arResult['LABEL_VALUE']; ?></div>
						<?}?>
					</div>
				</div>

				<?
				if ($arResult['SHOW_SLIDER'])
				{
					if (!isset($arResult['OFFERS']) || empty($arResult['OFFERS']))
					{
						if (4 < $arResult['MORE_PHOTO_COUNT'])
						{
							$strClass = 'bx_slider_conteiner full';
							$strOneWidth = (100/$arResult['MORE_PHOTO_COUNT']).'%';
							$strWidth = (25*$arResult['MORE_PHOTO_COUNT']).'%';
							$strSlideStyle = '';
						}
						else
						{
							$strClass = 'bx_slider_conteiner';
							$strOneWidth = '25%';
							$strWidth = '100%';
							$strSlideStyle = 'display: none;';
						}
						?>
						<div class="<? echo $strClass; ?>" id="<? echo $arItemIDs['SLIDER_CONT_ID']; ?>">
							<div class="bx_slider_scroller_container">
								<div class="bx_slide" id="gallery">
									<ul style="width: <? echo $strWidth; ?>;" id="<? echo $arItemIDs['SLIDER_LIST']; ?>">
									<?foreach ($arResult['MORE_PHOTO'] as &$arOnePhoto) {?>
										<li data-value="<? echo $arOnePhoto['ID']; ?>" 
											style="width: <? echo $strOneWidth; ?>; padding-top: <? echo $strOneWidth; ?>;">
                                            <a href="#" data-zoom-image="<? echo $arOnePhoto['SRC']; ?>" data-image="<? echo $arOnePhoto['SRC']; ?>" class="elevatezoom-gallery">
    											<span class="cnt">
    												<span class="cnt_item" style="background-image:url('<? echo $arOnePhoto['SRC']; ?>');"></span>
    											</span>
                                            </a>
										</li>
									<?}	unset($arOnePhoto);?>
									</ul>
								</div>
                                <?if(count($arResult['MORE_PHOTO'])> 4):?>
								<div class="bx_slide_left" id="<? echo $arItemIDs['SLIDER_LEFT']; ?>" style="<? echo $strSlideStyle; ?>"></div>
								<div class="bx_slide_right" id="<? echo $arItemIDs['SLIDER_RIGHT']; ?>" style="<? echo $strSlideStyle; ?>"></div>
                                <?endif;?>
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
							if (4 < $arOneOffer['MORE_PHOTO_COUNT'])
							{
								$strClass = 'bx_slider_conteiner full';
								$strOneWidth = (100/$arOneOffer['MORE_PHOTO_COUNT']).'%';
								$strWidth = (25*$arOneOffer['MORE_PHOTO_COUNT']).'%';
								$strSlideStyle = '';
							}
							else
							{
								$strClass = 'bx_slider_conteiner';
								$strOneWidth = '25%';
								$strWidth = '100%';
								$strSlideStyle = 'display: none;';
							}
							?>
							<div class="<? echo $strClass; ?>" id="<? echo $arItemIDs['SLIDER_CONT_OF_ID'].$arOneOffer['ID']; ?>" style="display: <? echo $strVisible; ?>;">
								<div class="bx_slider_scroller_container">
									<div class="bx_slide">
										<ul style="width: <? echo $strWidth; ?>;" id="<? echo $arItemIDs['SLIDER_LIST_OF_ID'].$arOneOffer['ID']; ?>">
											<?foreach ($arOneOffer['MORE_PHOTO'] as &$arOnePhoto) {?>
											<li data-value="<? echo $arOneOffer['ID'].'_'.$arOnePhoto['ID']; ?>" 
												style="width: <? echo $strOneWidth; ?>; padding-top: <? echo $strOneWidth; ?>">
												<span class="cnt">
													<span class="cnt_item" style="background-image:url('<? echo $arOnePhoto['SRC']; ?>');"></span>
												</span>
											</li>
											<?}	unset($arOnePhoto);?>
										</ul>
									</div>
                                    <?if(count($arResult['MORE_PHOTO'])> 0):?>
									<div class="bx_slide_left" id="<? echo $arItemIDs['SLIDER_LEFT_OF_ID'].$arOneOffer['ID'] ?>" style="<? echo $strSlideStyle; ?>" data-value="<? echo $arOneOffer['ID']; ?>"></div>
									<div class="bx_slide_right" id="<? echo $arItemIDs['SLIDER_RIGHT_OF_ID'].$arOneOffer['ID'] ?>" style="<? echo $strSlideStyle; ?>" data-value="<? echo $arOneOffer['ID']; ?>"></div>
                                    <?endif;?>
								</div>
							</div>
							<?
						}
					}
				}
				?>
				
				<?
				if('Y' == $arResult['PROPERTIES']['EMARKET_HIT']['VALUE'])
					echo '<div class="item_hit"></div>';
				elseif('Y' == $arResult['PROPERTIES']['EMARKET_NEW']['VALUE'])
					echo '<div class="item_new"></div>';
				?>
			</div>
			<?/*slider block end*/?>
			
		</div>
		
		
		<!--right block with info-->
		<div class="block right">
					
			<?/*Name block*/?>
			<?if ('Y' == $arParams['DISPLAY_NAME']) {?>
			<div class="bx_item_title">
				
				<?/*property block*/?>
				<?if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']) {?>
				<div class="item_info_props">
					<?if (!empty($arResult['DISPLAY_PROPERTIES'])) {?>
					<dl>
						<?foreach ($arResult['DISPLAY_PROPERTIES'] as &$arOneProp){?>
						
							<?
							if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))
							{
								foreach($arResult['OFFERS'] as $arOffer)
								{
									if( $arOneProp['CODE'] == 'EMARKET_ARTICLE' &&
										$arOffer['PROPERTIES']['EMARKET_ARTICLE']['VALUE'])
										continue 2;	
								}
							}?>
						
						<dt><? echo $arOneProp['NAME']; ?></dt><?
							echo '<dd>', (
								is_array($arOneProp['DISPLAY_VALUE'])
								? implode(' / ', $arOneProp['DISPLAY_VALUE'])
								: $arOneProp['DISPLAY_VALUE']
							), '</dd>';
						}
						unset($arOneProp);?>
					</dl>
					<?}?>
					
					<?if ($arResult['SHOW_OFFERS_PROPS']) {?>
					<dl id="<? echo $arItemIDs['DISPLAY_PROP_DIV'] ?>" style="display: none;"></dl>
					<?}?>
				</div>
				<?}?>
				
				<h1>
					<span><? echo (
						isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && '' != $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
						? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
						: $arResult["NAME"]
					); ?></span>
				</h1>
				
				<?if($arResult['PROPERTIES']['EMARKET_PREVIEW_CH']['VALUE']) {?>
					<div class="item_info_props" style="font-size:14px; font-weight:300;"><?=$arResult['PROPERTIES']['EMARKET_PREVIEW_CH']['VALUE']?></div>
				<?}?>
				
				<?//rating block?>
				<div class="bx_item_rating">
					<?	
					$rating = intval($arResult['PROPERTIES']['EMARKET_RATING']['VALUE']);
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
				
			</div>
			<?}?>
			<?/*Name block end*/?>

			
			<?/*Offers block*/?>
			<?if(isset($arResult['OFFERS']) && !empty($arResult['OFFERS']) && !empty($arResult['OFFERS_PROP'])) {?>				
				<div class="bx_item_title item_info_section"  id="<? echo $arItemIDs['PROP_DIV']; ?>">
				<?
					$arSkuProps = array();
					foreach ($arResult['SKU_PROPS'] as &$arProp)
					{
						if (!isset($arResult['OFFERS_PROP'][$arProp['CODE']]))
							continue;
						$arSkuProps[] = array(
							'ID' => $arProp['ID'],
							'SHOW_MODE' => $arProp['SHOW_MODE'],
							'VALUES_COUNT' => $arProp['VALUES_COUNT']
						);
						if ('TEXT' == $arProp['SHOW_MODE'])
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
								<div class="bx_size_scroller_container">
									<div class="bx_size">
										<ul id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_list" style="width: <? echo $strWidth; ?>;margin-left:0%;">
										<?foreach ($arProp['VALUES'] as $arOneValue){?>
											<li	data-treevalue="<? echo $arProp['ID'].'_'.$arOneValue['ID']; ?>"
												data-onevalue="<? echo $arOneValue['ID']; ?>"
												style="width: <? echo $strOneWidth; ?>;">
												<i></i>
												<span class="cnt"><? echo htmlspecialcharsex($arOneValue['NAME']); ?></span>
											</li>
										<?}?>
										</ul>
									</div>
                                    
									<div class="bx_slide_left" style="<? echo $strSlideStyle; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_left" data-treevalue="<? echo $arProp['ID']; ?>"></div>
									<div class="bx_slide_right" style="<? echo $strSlideStyle; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_right" data-treevalue="<? echo $arProp['ID']; ?>"></div>
                                    
								</div>
							</div>
							<?
						}
						elseif ('PICT' == $arProp['SHOW_MODE'])
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
								<div class="bx_scu_scroller_container">
									<div class="bx_scu">
										<ul id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_list" style="width: <? echo $strWidth; ?>;margin-left:0%;">
										<?foreach ($arProp['VALUES'] as $arOneValue) {?>
											<li data-treevalue="<? echo $arProp['ID'].'_'.$arOneValue['ID'] ?>"
												data-onevalue="<? echo $arOneValue['ID']; ?>"
												style="width: <? echo $strOneWidth; ?>; padding-top: <? echo $strOneWidth; ?>;">
												<i title="<? echo htmlspecialcharsbx($arOneValue['NAME']); ?>"></i>
												<span class="cnt">
													<span class="cnt_item"
														style="background-image:url('<? echo $arOneValue['PICT']['SRC']; ?>');"
														title="<? echo htmlspecialcharsbx($arOneValue['NAME']); ?>"></span>
												</span>
											</li>
										<?}?>
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
			<?}?>
			<?/*Offers block end*/?>
			
			<?
			if ('' != $arResult['PREVIEW_TEXT'])
			{
				if('S' == $arParams['DISPLAY_PREVIEW_TEXT_MODE'] || 
				  ('E' == $arParams['DISPLAY_PREVIEW_TEXT_MODE'] && '' == $arResult['DETAIL_TEXT']))
				{
				?>
				<div class="item_info_section">
					<?echo ('html' == $arResult['PREVIEW_TEXT_TYPE'] ? $arResult['PREVIEW_TEXT'] : '<p>'.$arResult['PREVIEW_TEXT'].'</p>');?>
				</div>
				<?
				}
			}
			?>			
		</div>
		
		<!--control block-->
		<div class="control">
		
			<?/*Price block*/?>
			<div class="item_price">
				<div class="item_current_price" id="<? echo $arItemIDs['PRICE']; ?>">
					<? echo $arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE']; ?>
				</div>
			
				<?$boolDiscountShow = (0 < $arResult['MIN_PRICE']['DISCOUNT_DIFF']);?>			
				<div class="item_old_price" id="<? echo $arItemIDs['OLD_PRICE']; ?>" style="display: <? echo ($boolDiscountShow ? '' : 'none'); ?>">
					<? echo ($boolDiscountShow ? $arResult['MIN_PRICE']['PRINT_VALUE'] : ''); ?>
				</div>

				<?if('Y' == $arParams['SHOW_DISCOUNT_PERCENT']) {?>
					<div class="bx_stick_disc" id="<? echo $arItemIDs['DISCOUNT_PICT_ID'] ?>" 
					style="display:<? echo (0 < $arResult['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'] ? '' : 'none'); ?>;">-<? echo $arResult['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']; ?>%</div>
				<?}?>
			</div>
			<?/*Price block end*/?>

			
			<?/*Control block*/?>
			<div class="item_info_section">
				<?
				if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))
					$canBuy = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['CAN_BUY'];
				else
					$canBuy = $arResult['CAN_BUY'];
				
				if ($canBuy)
				{
					$buyBtnMessage = ('' != $arParams['MESS_BTN_BUY'] ? $arParams['MESS_BTN_BUY'] : GetMessage('CT_BCE_CATALOG_BUY'));
					$buyBtnClass = 'bx_big bx_bt_button bx_cart';
				}
				else
				{
					$buyBtnMessage = ('' != $arParams['MESS_NOT_AVAILABLE'] ? $arParams['MESS_NOT_AVAILABLE'] : GetMessageJS('CT_BCE_CATALOG_NOT_AVAILABLE'));
					$buyBtnClass = 'bx_big bx_bt_button_type_2 bx_cart';
				}
				
				if ('Y' == $arParams['USE_PRODUCT_QUANTITY']) {?>
					<span class="item_section_name_gray"><? echo GetMessage('CATALOG_QUANTITY'); ?></span>
					<div class="item_buttons vam">
						<span class="controls-wrap">
							<a href="javascript:void(0)" class="small_button left" id="<? echo $arItemIDs['QUANTITY_DOWN']; ?>"></a>
							<input id="<? echo $arItemIDs['QUANTITY']; ?>" 
								type="text" 
								class="tac transparent_input" 
								value="<? echo (isset($arResult['OFFERS']) && !empty($arResult['OFFERS'])
									? 1
									: $arResult['CATALOG_MEASURE_RATIO']
								); ?>">
							<a href="javascript:void(0)" class="small_button right" id="<? echo $arItemIDs['QUANTITY_UP']; ?>"></a>
						</span>
						<span class="bx_cnt_desc" id="<? echo $arItemIDs['QUANTITY_MEASURE']; ?>">
							<? echo (isset($arResult['CATALOG_MEASURE_NAME']) ? $arResult['CATALOG_MEASURE_NAME'] : ''); ?>
						</span>
						
						
						<a  href="javascript:void(0);" 
							class="bx_medium_2 ico1" 
							id="<? echo $arItemIDs['BUY_LINK']; ?>">
							<? echo $buyBtnMessage; ?>
						</a>
												
						<a  href="#" onclick="BuyOneClick(this);return false;" 
							data-id="<?=$arResult['ID']?>" 
							class="bx_medium_2 ico2" 
							rel="nofollow">
							<?=GetMessage('CT_BCE_CATALOG_BUY_1')?>
						</a>
					</div>
					<?
					if ('Y' == $arParams['SHOW_MAX_QUANTITY'])
					{
						if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))
						{
							?>
							<p id="<? echo $arItemIDs['QUANTITY_LIMIT']; ?>" style="display: none;">
								<? echo GetMessage('OSTATOK'); ?>: <span></span>
							</p>
							<?
						}
						else
						{
							if ('Y' == $arResult['CATALOG_QUANTITY_TRACE'] && 'N' == $arResult['CATALOG_CAN_BUY_ZERO'])
							{
								?>
								<p id="<? echo $arItemIDs['QUANTITY_LIMIT']; ?>">
									<? echo GetMessage('OSTATOK'); ?>: <span><? echo $arResult['CATALOG_QUANTITY']; ?></span>
								</p>
								<?
							}
						}
					}
				} else {?>
					<div class="item_buttons vam">
						<a  href="javascript:void(0);" 
							class="bx_medium_2 ico1" 
							id="<? echo $arItemIDs['BUY_LINK']; ?>">
							<? echo $buyBtnMessage; ?>
						</a>
						<a  href="#" 
							data-id="<?=$arItem['ID']?>" 
							class="bx_medium_2 ico2" 
							rel="nofollow">
							<?=GetMessage('CT_BCE_CATALOG_BUY_1')?>
						</a>
					</div>
				<?}?>
			</div>
			<?/*Control block end*/?>
			
			
			<?/*Compare block*/?>
			<?if($arParams['DISPLAY_COMPARE']){?>
			<div class="compare-control">	
				<input id="compare_<?=$arResult['ID']?>" 
					class="compare-control-input" 
					type="checkbox" 
					<?if(!empty($_SESSION[$arParams['COMPARE_NAME']][$arResult['IBLOCK_ID']]['ITEMS'][$arResult['ID']])) echo 'checked="checked"';?>
					data-id="<?=$arResult['ID']?>">
				<label for="compare_<?=$arResult['ID']?>"><?=GetMessage('CT_BCE_CATALOG_COMPARE')?></label>
				<div class="load"></div>
			</div>
			<?}?>
			<?/*Compare block end*/?>
			
			
			<?/*Call block*/?>
			<a  href="/error_js.php" 
				id="emarket_call_me"
				data-id="<?=$arResult['ID']?>">
				<?=GetMessage('CT_BCE_CATALOG_CALL')?>
			</a>
			<?/*Call block end*/?>
			
			
			<?/*Brand block*/?>
			<?
			$useBrands = ('Y' == $arParams['BRAND_USE']);
			if ($useBrands || $useVoteRating) {?>
				<div class="item_info_section">
					<?if ($useBrands) {?>
					<?$APPLICATION->IncludeComponent("bitrix:catalog.brandblock", ".default", array(
							"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
							"IBLOCK_ID" => $arParams['IBLOCK_ID'],
							"ELEMENT_ID" => $arResult['ID'],
							"ELEMENT_CODE" => "",
							"PROP_CODE" => $arParams['BRAND_PROP_CODE'],
							"CACHE_TYPE" => $arParams['CACHE_TYPE'],
							"CACHE_TIME" => $arParams['CACHE_TIME'],
							"CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
							"WIDTH" => "",
							"HEIGHT" => ""
							),
							$component,
							array("HIDE_ICONS" => "Y")
						);?>
					<?}?>
				</div>
			<?
			}
			unset($useBrands);
			?>
			<?/*Brand block end*/?>
		</div>
	</div>
	
	<div class="tabs">
		<div class="tabs-menu">
			<a href="#tab_1" class="active"><?=GetMessage('TAB1');?></a>
			<a href="#tab_2"><?=GetMessage('TAB2');?></a>
			<a href="#tab_3"><?=GetMessage('TAB3');?></a>
			<a href="#tab_4"><?=GetMessage('TAB4');?></a>
		</div>
		<div class="tabs-content">		
			<!----tab_1---->
			<div id="tab_1" class="tab clear">
				
				<div class="tab-row first">				
				<h2><?=GetMessage("TEXT_DESC")?></h2>
				<div class="item_info_section">
				<?if ('' != $arResult['PREVIEW_TEXT']) {?>
					<div class="bx_item_description">
						<?
						if ('html' == $arResult['PREVIEW_TEXT'])
							echo $arResult['PREVIEW_TEXT'];
						else
							echo "<p>".$arResult['PREVIEW_TEXT']."</p>";
						?>
					</div>
				<?}
				else 
					echo GetMessage("TEXT_NO_DESC");
				?>
				</div>
				<a class="link" href="#tab_2"><?=GetMessage("TEXT_MORE")?></a>
				
				
				 <?$APPLICATION->ShowViewContent("better_review");?>
				<a class="bx_medium_2 green-link" href="#tab_4"><?=GetMessage("TEXT_REVIEV")?></a>
				<a class="link" href="#tab_4"><?=GetMessage("TEXT_SHOW_ALL")?></a>										
				</div>
				<div class="tab-row second">
					<h2><?=GetMessage("TEXT_PROPERTY")?></h2>
					<div class="property-list">
						<?
						$i=0;
						foreach($arResult['PROPERTY_ITEMS'] as $property)
						{
														
							if($i >= 20) break;	
							
							//wrap items
							$property_val = '';
							$property_code = $property['CODE'];
							$property_table_name = $arResult['PROPERTIES'][$property_code]['USER_TYPE_SETTINGS']['TABLE_NAME'];
							switch($arResult['PROPERTIES'][$property_code]['PROPERTY_TYPE'])
							{
								case 'S':
									if( !isset($property_table_name) || empty($property_table_name))
									{
										$property_val = $arResult['PROPERTIES'][$property_code]['VALUE'];
									}
									else
									{
										$property_val = $arResult['HL_PROP_LIST'][$property_table_name][$arResult['PROPERTIES'][$property_code]['VALUE']]['UF_NAME'];
									}
								break;
								
								case 'L':
									if($arResult['PROPERTIES'][$property_code]['VALUE'] == 'Y')
										$property_val = GetMessage('CATALOG_TC_YES');
									else
										$property_val = $arResult['PROPERTIES'][$property_code]['VALUE'];
								break;
								case 'N':
									if($arResult['PROPERTIES'][$property_code]['VALUE'] > 0)
										$property_val = $arResult['PROPERTIES'][$property_code]['VALUE'].' '.$property['PROPERTIES']['property_measure']['VALUE'];
								break;
							}
							
							if(!$property_val) 
								continue;
							else
								$i++;
							
							if(	$property['PROPERTIES']['property_group']['VALUE'] != $temp &&
								$property['PROPERTIES']['property_group']['VALUE'])
							{
								if($temp) echo '</table></div>';
								?>
								<div class="property-item">
									<h3><?=$arResult['HL_PROP_LIST'][$property['PROPERTIES']['property_group']['USER_TYPE_SETTINGS']['TABLE_NAME']][$property['PROPERTIES']['property_group']['VALUE']]['UF_NAME']?></h3>
									<table>
								<?
								$temp = $property['PROPERTIES']['property_group']['VALUE'];
							}
							?>
							<tr>
								<td><span><?=$property['NAME']?></span></td>
								<td><?=$property_val?></td>
							</tr>
						<?
						}
						
						if($temp)
							echo '</table></div>';
							
						unset($temp);
						?>
					</div>
					<a class="link" href="#tab_3"><?=GetMessage("TEXT_PROPERTY_SHOW")?></a>
				</div>
				<?
				if(!empty($arResult['PROPERTIES']['EMARKET_ACCESSORIES']['VALUE'])) 
				{
					global $arrFilterAcc;
					$arrFilterAcc = array("ID"=>$arResult['PROPERTIES']['EMARKET_ACCESSORIES']['VALUE']);
                }else
                {
                    $SECTION_ID = $arParams["SECTION_ID_VARIABLE"];
                }    
					?>
					<div class="tab-row third">
						<h2 style="margin:0 0 0 -1px; background: #fff;"><?=GetMessage("K_AKSESUAR")?></h2>
							<?$APPLICATION->IncludeComponent(
								"bitrix:catalog.section", 
								"detail-template", 
								array(
									"IBLOCK_TYPE" => "catalog",
									"IBLOCK_ID" =>$arParams['IBLOCK_ID'],
									"SECTION_ID" => '',
									"SECTION_CODE" => "",
									"SECTION_USER_FIELDS" => array(
										0 => "",
										1 => "",
									),
									"ELEMENT_SORT_FIELD" => "sort",
									"ELEMENT_SORT_ORDER" => "asc",
									"ELEMENT_SORT_FIELD2" => "id",
									"ELEMENT_SORT_ORDER2" => "desc",
									"FILTER_NAME" => "arrFilterAcc",
									"INCLUDE_SUBSECTIONS" => "Y",
									"SHOW_ALL_WO_SECTION" => "Y",
									"HIDE_NOT_AVAILABLE" => "N",
									"PAGE_ELEMENT_COUNT" => "30",
									"LINE_ELEMENT_COUNT" => "3",
									"PROPERTY_CODE" => array(
										0 => "EMARKET_TOP_TYPE",
										1 => "",
									),
									"OFFERS_FIELD_CODE" => array(
										0 => "",
										1 => "",
									),
									"OFFERS_PROPERTY_CODE" => array(
										0 => "EMARKET_SKU_MEMORY",
										1 => "EMARKET_SKU_COLOR",
										2 => "",
									),
									"OFFERS_SORT_FIELD" => "sort",
									"OFFERS_SORT_ORDER" => "asc",
									"OFFERS_SORT_FIELD2" => "id",
									"OFFERS_SORT_ORDER2" => "desc",
									"OFFERS_LIMIT" => "5",
									"TEMPLATE_THEME" => "",
									"PRODUCT_DISPLAY_MODE" => "Y",
									"ADD_PICT_PROP" => "-",
									"LABEL_PROP" => "-",
									"PRODUCT_SUBSCRIPTION" => "N",
									"SHOW_DISCOUNT_PERCENT" => "N",
									"SHOW_OLD_PRICE" => "N",
									"MESS_BTN_BUY" => GetMessage("CT_BCE_CATALOG_ADD"),
									"MESS_BTN_ADD_TO_BASKET" => GetMessage("CT_BCE_CATALOG_ADD"),
									"MESS_BTN_SUBSCRIBE" => "Подписаться",
									"MESS_BTN_DETAIL" => "Подробнее",
									"MESS_NOT_AVAILABLE" => GetMessage("K_NO_STORE"),
									"SECTION_URL" => "",
									"DETAIL_URL" => "",
									"SECTION_ID_VARIABLE" => "SECTION_ID",
									"AJAX_MODE" => "N",
									"AJAX_OPTION_JUMP" => "N",
									"AJAX_OPTION_STYLE" => "Y",
									"AJAX_OPTION_HISTORY" => "N",
									"CACHE_TYPE" => "A",
									"CACHE_TIME" => "36000000",
									"CACHE_GROUPS" => "Y",
									"SET_META_KEYWORDS" => "Y",
									"META_KEYWORDS" => "-",
									"SET_META_DESCRIPTION" => "Y",
									"META_DESCRIPTION" => "-",
									"BROWSER_TITLE" => "-",
									"ADD_SECTIONS_CHAIN" => "N",
									"DISPLAY_COMPARE" => "Y",
									"SET_TITLE" => "Y",
									"SET_STATUS_404" => "N",
									"CACHE_FILTER" => "N",
									"PRICE_CODE" => array(
										0 => "BASE",
									),
									"USE_PRICE_COUNT" => "N",
									"SHOW_PRICE_COUNT" => "1",
									"PRICE_VAT_INCLUDE" => "Y",
									"CONVERT_CURRENCY" => "N",
									"BASKET_URL" => SITE_DIR."personal/basket.php",
									"ACTION_VARIABLE" => "action",
									"PRODUCT_ID_VARIABLE" => "id",
									"USE_PRODUCT_QUANTITY" => "Y",
									"ADD_PROPERTIES_TO_BASKET" => "Y",
									"PRODUCT_PROPS_VARIABLE" => "prop",
									"PARTIAL_PRODUCT_PROPERTIES" => "N",
									"PRODUCT_PROPERTIES" => array(
									),
									"OFFERS_CART_PROPERTIES" => array(
									),
									"PAGER_TEMPLATE" => ".default",
									"DISPLAY_TOP_PAGER" => "N",
									"DISPLAY_BOTTOM_PAGER" => "N",
									"PAGER_TITLE" => "Товары",
									"PAGER_SHOW_ALWAYS" => "N",
									"PAGER_DESC_NUMBERING" => "N",
									"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
									"PAGER_SHOW_ALL" => "N",
									"AJAX_OPTION_ADDITIONAL" => "",
									"PRODUCT_QUANTITY_VARIABLE" => "quantity",
									"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
									"OFFER_ADD_PICT_PROP" => "-",
									"OFFER_TREE_PROPS" => array(
										0 => "EMARKET_SKU_COLOR",
										1 => "EMARKET_SKU_MEMORY",
									)
								),
								false
							);?>		
					</div>			
			</div>
			
			<!----tab_2---->
			<div id="tab_2" class="tab clear">
				
				<?if ('Y' == $arParams['DISPLAY_NAME']) {?>
				<div class="bx_item_title">
					<h2>
						<span><?=GetMessage("TEXT_PROPERTY")?>: <? echo (
							isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && '' != $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
							? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
							: $arResult["NAME"]
						); ?></span>
					</h2>
				</div>
				<?}?>
				
				<div class="item_info_section">
					<?if ('' != $arResult['DETAIL_TEXT']) {?>
						<div class="bx_item_description">
							<?
							if ('html' == $arResult['DETAIL_TEXT_TYPE'])
								echo $arResult['DETAIL_TEXT'];
							else
								echo "<p>".$arResult['DETAIL_TEXT']."</p>";
							?>
						</div>
					<?}?>
				</div>
				
			</div>
			
			<!----tab_3---->
			<div id="tab_3" class="tab">
			
				<div class="bx_item_title">
					<h2>
						<span><?=GetMessage("TEXT_DESC_OBZ")?> <? echo (
							isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && '' != $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
							? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
							: $arResult["NAME"]
						); ?></span>
					</h2>
				</div>
				
				<div class="property-list" id="property-list">
					<?
					foreach($arResult['PROPERTY_ITEMS'] as $property)
					{
						//wrap items
						$property_val = '';
						$property_code = $property['CODE'];
						$property_table_name = $arResult['PROPERTIES'][$property_code]['USER_TYPE_SETTINGS']['TABLE_NAME'];
						switch($arResult['PROPERTIES'][$property_code]['PROPERTY_TYPE'])
						{
							case 'S':
								if( !isset($property_table_name) || empty($property_table_name))
								{
									$property_val = $arResult['PROPERTIES'][$property_code]['VALUE'];
								}
								else
								{
									$property_val = $arResult['HL_PROP_LIST'][$property_table_name][$arResult['PROPERTIES'][$property_code]['VALUE']]['UF_NAME'];
								}
							break;
							
							case 'L':
								if($arResult['PROPERTIES'][$property_code]['VALUE'] == 'Y')
									$property_val = GetMessage('CATALOG_TC_YES');
								else
									$property_val = $arResult['PROPERTIES'][$property_code]['VALUE'];
							break;
							case 'N':
								if($arResult['PROPERTIES'][$property_code]['VALUE'] > 0)
									$property_val = $arResult['PROPERTIES'][$property_code]['VALUE'].' '.$property['PROPERTIES']['property_measure']['VALUE'];
							break;
						}
						
						if(!$property_val) continue;
						
						if(	$property['PROPERTIES']['property_group']['VALUE'] != $temp &&
							$property['PROPERTIES']['property_group']['VALUE'])
						{
							if($temp) echo '</table></div>';
							?>
							<div class="property-item">
								<h3><?=$arResult['HL_PROP_LIST'][$property['PROPERTIES']['property_group']['USER_TYPE_SETTINGS']['TABLE_NAME']][$property['PROPERTIES']['property_group']['VALUE']]['UF_NAME']?></h3>
								<table>
							<?
							$temp = $property['PROPERTIES']['property_group']['VALUE'];
						}
						?>
						<tr>
							<td><span><?=$property['NAME']?></span></td>
							<td><?=$property_val?></td>
						</tr>
					<?
					}
					
					if($temp)
						echo '</table></div>';	
					?>
					
				</div>
				<div class="info"><?=GetMessage("TEXT_PRODUCT")?></div>
			</div>
			
			<!----tab_4---->
			<div id="tab_4" class="tab">
				<?if ('Y' == $arParams['USE_COMMENTS']) {?>
					<?$APPLICATION->IncludeComponent(
						"krayt:emarket.comments",
						"",
						Array(
							"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE_ID'],
							"IBLOCK_ID" => $arResult['IBLOCK_ID'],
							"ELEMENT_ID" => $arResult["ID"],
							"ELEMENT_CODE" => '',
							"HLBLOCK_PROP_CODE" => $arParams['BLOG_HLBLOCK_PROP_CODE'],
							"HLBLOCK_CR_PROP_CODE" => $arParams['BLOG_HLBLOCK_CR_PROP_CODE'],
							"EMARKET_COMMENT_PREMODERATION" => "N",
							"EMARKET_CUR_RATING" => $arResult['PROPERTIES']['EMARKET_RATING']['VALUE'],
							"EMARKET_CUR_COMMENTS_COUNT" => $arResult['PROPERTIES']['EMARKET_COMMENTS_COUNT']['VALUE']
						),
					false
					);?>
				<?}?>
			</div>	
		</div>
	</div>
</div>

<?
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
					$strProps .= '<dt>'.$arOneProp['NAME'].'</dt><dd>'.(
						is_array($arOneProp['VALUE'])
						? implode(' / ', $arOneProp['VALUE'])
						: $arOneProp['VALUE']
					).'</dd>';
				}
			}
		}
		$arOneJS['DISPLAY_PROPERTIES'] = $strProps;
	}
	if (isset($arOneJS))
		unset($arOneJS);
	$arJSParams = array(
		'CONFIG' => array(
			'USE_CATALOG' => $arResult['CATALOG'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE' => true,
			'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
			'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
			'DISPLAY_COMPARE' => ('Y' == $arParams['DISPLAY_COMPARE']),
			'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
			'OFFER_GROUP' => $arResult['OFFER_GROUP'],
			'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE']
		),
		'PRODUCT_TYPE' => $arResult['CATALOG_TYPE'],
		'VISUAL' => array(
			'ID' => $arItemIDs['ID'],
		),
		'DEFAULT_PICTURE' => array(
			'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
			'DETAIL_PICTURE' => $arResult['DEFAULT_PICTURE']
		),
		'PRODUCT' => array(
			'ID' => $arResult['ID'],
			'NAME' => $arResult['~NAME']
		),
		'BASKET' => array(
			'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'BASKET_URL' => $arParams['BASKET_URL'],
			'SKU_PROPS' => $arResult['OFFERS_PROP_CODES']
		),
		'OFFERS' => $arResult['JS_OFFERS'],
		'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'],
		'TREE_PROPS' => $arSkuProps
	);
}
else
{
	$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
	if ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET'] && !$emptyProductProperties)
	{
		?><div id="<? echo $arItemIDs['BASKET_PROP_DIV']; ?>" style="display: none;"><?
		
		if (!empty($arResult['PRODUCT_PROPERTIES_FILL']))
		{
			foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo)
			{
				?>
					<input
						type="hidden"
						name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"
						value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>"
					>
				<?
				if (isset($arResult['PRODUCT_PROPERTIES'][$propID]))
					unset($arResult['PRODUCT_PROPERTIES'][$propID]);
			}
		}
		$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
		if (!$emptyProductProperties)
		{
			?><table><?
			foreach ($arResult['PRODUCT_PROPERTIES'] as $propID => $propInfo)
			{
				?>
				<tr>
					<td><? echo $arResult['PROPERTIES'][$propID]['NAME']; ?></td>
					<td>
					<?
						if('L' == $arResult['PROPERTIES'][$propID]['PROPERTY_TYPE'] && 
							'C' == $arResult['PROPERTIES'][$propID]['LIST_TYPE']
						)
						{
							foreach($propInfo['VALUES'] as $valueID => $value)
							{
								?><label><input
									type="radio"
									name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"
									value="<? echo $valueID; ?>"
									<? echo ($valueID == $propInfo['SELECTED'] ? '"checked"' : ''); ?>
								><? echo $value; ?></label><br><?
							}
						}
						else
						{
							?><select name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"><?
							foreach($propInfo['VALUES'] as $valueID => $value)
							{
								?>
								<option
									value="<? echo $valueID; ?>"
									<? echo ($valueID == $propInfo['SELECTED'] ? '"selected"' : ''); ?>
								><? echo $value; ?></option><?
							}
							?></select><?
						}
					?>
					</td>
				</tr>
				<?
			}
			?></table><?
		}
		?></div><?
	}
	$arJSParams = array(
		'CONFIG' => array(
			'USE_CATALOG' => $arResult['CATALOG'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE' => (isset($arResult['MIN_PRICE']) && !empty($arResult['MIN_PRICE']) && is_array($arResult['MIN_PRICE'])),
			'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
			'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
			'DISPLAY_COMPARE' => ('Y' == $arParams['DISPLAY_COMPARE']),
			'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE']
		),
		'VISUAL' => array(
			'ID' => $arItemIDs['ID'],
		),
		'PRODUCT_TYPE' => $arResult['CATALOG_TYPE'],
		'PRODUCT' => array(
			'ID' => $arResult['ID'],
			'PICT' => $arFirstPhoto,
			'NAME' => $arResult['~NAME'],
			'SUBSCRIPTION' => true,
			'PRICE' => $arResult['MIN_PRICE'],
			'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'],
			'SLIDER' => $arResult['MORE_PHOTO'],
			'CAN_BUY' => $arResult['CAN_BUY'],
			'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
			'QUANTITY_FLOAT' => is_double($arResult['CATALOG_MEASURE_RATIO']),
			'MAX_QUANTITY' => $arResult['CATALOG_QUANTITY'],
			'STEP_QUANTITY' => $arResult['CATALOG_MEASURE_RATIO'],
			'BUY_URL' => $arResult['~BUY_URL'],
		),
		'BASKET' => array(
			'ADD_PROPS' => ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET']),
			'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
			'EMPTY_PROPS' => $emptyProductProperties,
			'BASKET_URL' => $arParams['BASKET_URL']
		)
	);
	unset($emptyProductProperties);
}
?>

<script type="text/javascript">
	var <? echo $strObName; ?> = new JCCatalogElement(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
	BX.message({
		MESS_BTN_BUY: '<? echo ('' != $arParams['MESS_BTN_BUY'] ? CUtil::JSEscape($arParams['MESS_BTN_BUY']) : GetMessageJS('CT_BCE_CATALOG_BUY')); ?>',
		MESS_BTN_ADD_TO_BASKET: '<? echo ('' != $arParams['MESS_BTN_ADD_TO_BASKET'] ? CUtil::JSEscape($arParams['MESS_BTN_ADD_TO_BASKET']) : GetMessageJS('CT_BCE_CATALOG_ADD')); ?>',
		MESS_NOT_AVAILABLE: '<? echo ('' != $arParams['MESS_NOT_AVAILABLE'] ? CUtil::JSEscape($arParams['MESS_NOT_AVAILABLE']) : GetMessageJS('CT_BCE_CATALOG_NOT_AVAILABLE')); ?>',
		TITLE_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR') ?>',
		TITLE_BASKET_PROPS: '<? echo GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS') ?>',
		BASKET_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
		BTN_SEND_PROPS: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS'); ?>',
		BTN_MESSAGE_CLOSE: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE') ?>',
		SITE_ID: '<? echo SITE_ID; ?>'
	});
</script>
<?
//echo '<pre>'; print_r($arResult['JS_OFFERS']); echo '</pre>';
//echo '<pre>'; print_r($arResult['PROPERTIES']['EMARKET_ACCESSORIES']); echo '<pre>';
?>
<div id="BasketEmodal" style="height: 220px;width: 580px;" class="emodal_form">
    <div class="emodal-title">
        <span><?=GetMessage("EMODAL_BASKET_TITLE")?></span>
        <a href="#" onclick="$('#BasketEmodal').eModalClose();return false;" class="emodal-close"></a>
    </div>
    <div class="emodal-data">        
    </div>
    <div class="emodal-bnts">
        <a class="btn_emodal" href="<?=$arParams["BASKET_URL"]; ?>"><?=GetMessage("EMODAL_BTN_ORDER")?></a>
        <a class="btn-close" href="#" onclick="$('#BasketEmodal').eModalClose();return false;"><?=GetMessage("EMODAL_BTN_MORE")?></a>
    </div>
</div>
<div id="OneClickEmodal" style="height: 240px;width: 312px;" class="emodal_form">
    <div class="emodal-title">
        <span><?=GetMessage("EMODAL_BYUONE_TITLE")?></span>
        <a href="#" onclick="$('#OneClickEmodal').eModalClose();return false;" class="emodal-close"></a>
    </div>
    <div  id='FromBuyOneClick' class="emodal-data">
        
    </div>    
</div>