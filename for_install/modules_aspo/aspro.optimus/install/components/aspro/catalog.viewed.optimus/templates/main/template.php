<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
$arParams['TITLE_BLOCK'] = strlen($arParams['TITLE_BLOCK']) ? $arParams['TITLE_BLOCK'] : GetMessage('CATALOG_VIEWED_TITLE');
?>
<!-- noindex -->
<?if(strlen($arResult['ERROR'])):?>
	<?ShowError($arResult['ERROR']);?>
<?else:?>
	<?if($arResult['ITEMS']):?>
		<?//print_r($arResult);?>
		<div class="viewed_block">
			<div class="title_block"><?=$arParams["TITLE_BLOCK"]?></div>
			<div class="outer_wrap">
				<div class="rows_block items">
					<?foreach($arResult['ITEMS'] as $key=>$arItem):?>
						<?
						if($key > 7)
							continue;
						$isItem = (isset($arItem['PRODUCT_ID']) ? true : false);
						?>
						<div class="item_block">
							<?if($isItem):?>
								<div data-id=<?=$arItem['PRODUCT_ID']?> data-picture='<?=str_replace('\'', '"', CUtil::PhpToJSObject($arItem['PICTURE']))?>' class="item_wrap item <?=($isItem ? 'has-item' : '' );?>" id=<?=$this->GetEditAreaId($arItem['PRODUCT_ID'])?>>
									<?
									$this->AddEditAction($arItem['PRODUCT_ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
									$this->AddDeleteAction($arItem['PRODUCT_ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
									?>
								</div>
							<?else:?>
								<div class="item_wrap item"></div>
							<?endif;?>
						</div>
					<?endforeach;?>
				</div>
			</div>
		</div>
		<script type="text/javascript">
		$(document).ready(function() {
			var lastViewedTime = <?=$arResult['LAST_ACTIVE_FROM']?>;
			var bShowMeasure = <?=($arParams['SHOW_MEASURE'] !== 'N' ? 'true' : 'false')?>;
			var $viewedSlider = $('.viewed_block .item_block');

			if($viewedSlider.length){
				// save $.cookie option
				var bCookieJson = $.cookie.json;
				$.cookie.json = true;

				var arViewedLocal = {};
				var arViewedCookie = {};

				try{
					if(typeof BX.localStorage !== 'undefined'){
						var localKey = 'OPTIMUS_VIEWED_ITEMS_<?=SITE_ID?>';
						var cookieParams = {path: '/', expires: 30};
						arViewedLocal = BX.localStorage.get(localKey) ? BX.localStorage.get(localKey) : {};
						arViewedCookie = $.cookie(localKey) ? $.cookie(localKey) : {};
						for(var PRODUCT_ID in arViewedLocal){
							var $item = $viewedSlider.find('div[data-id=' + PRODUCT_ID + ']');
							if($item.length){
								var item = arViewedLocal[PRODUCT_ID];
								var picture = (typeof $item.attr('data-picture') !== 'undefined') ? JSON.parse($item.attr('data-picture')) : {ID: false, SRC: '<?=SITE_TEMPLATE_PATH.'/images/no_photo_medium.png'?>', ALT: item.NAME, TITLE: item.NAME};
								var bIsOffer = (typeof item.IS_OFFER !== 'undefined') ? (item.IS_OFFER === 'Y') : false;
								var bWithOffers = (typeof item.WITH_OFFERS !== 'undefined') ? (item.WITH_OFFERS === 'Y') : false;

								$item.html(
									'<div class="inner_wrap">'+
										'<div class="image_wrapper_block">'+
											'<a href="' + item.DETAIL_PAGE_URL + '" class="thumb">'+
												'<img border="0" src="' + picture.SRC + '" alt="' + (picture.ALT.length ? picture.ALT : item.NAME) + '" title="' + (picture.TITLE.length ? picture.TITLE : item.NAME) + '" />'+
											'</a>'+
										'</div>'+
										'<div class="item_info">'+
											'<div class="item-title">'+
												'<a href="' + item.DETAIL_PAGE_URL + '"><span>' + item.NAME + '</span></a>'+
											'</div>'+
											'<div class="cost prices clearfix">'+
												(item.MIN_PRICE ?
													((((item.MIN_PRICE.VALUE * 1) > (item.MIN_PRICE.DISCOUNT_VALUE * 1))) ?
														'<div class="price only_price">' + (bWithOffers ? '<?=GetMessage('CATALOG_FROM')?> ' : '') + item.MIN_PRICE.PRINT_DISCOUNT_VALUE + (bShowMeasure && item.CATALOG_MEASURE_NAME.length ? '/' + item.CATALOG_MEASURE_NAME : '') + '</div>'
													: '<div class="price only_price">' + (bWithOffers ? '<?=GetMessage('CATALOG_FROM')?> ' : '') + item.MIN_PRICE.PRINT_VALUE + (bShowMeasure && item.CATALOG_MEASURE_NAME.length ? '/' + item.CATALOG_MEASURE_NAME : '') + '</div>')
												: '')+
											'</div>'+
										'</div>'+
									'</div>'
								);
							}
							else{
								// item not finded
								// may be if it`s new item (it`s detail page), than ACTIVE_FROM > last viewed exists item
								// or it`s old died item and quantity limit
								var ACTIVE_FROM = (typeof arViewedLocal[PRODUCT_ID].ACTIVE_FROM !== 'undefined') ? arViewedLocal[PRODUCT_ID].ACTIVE_FROM : ((typeof arViewedCookie[PRODUCT_ID] !== 'undefined') ? arViewedCookie[PRODUCT_ID][0] : false);
								if(!ACTIVE_FROM || ACTIVE_FROM < lastViewedTime){
									// get actual for save
									var _arViewedLocal = BX.localStorage.get(localKey) ? BX.localStorage.get(localKey) : {};
									var _arViewedCookie = $.cookie(localKey) ? $.cookie(localKey) : {};
									delete _arViewedLocal[PRODUCT_ID];
									delete _arViewedCookie[PRODUCT_ID];
									BX.localStorage.set(localKey, _arViewedLocal, 2592000);  // 30 days
									$.cookie(localKey, _arViewedCookie, cookieParams);
								}
							}
						}
					}

					//remove some items
					$viewedSlider.find('>.item').each(function() {
						var PRODUCT_ID = (typeof $(this).attr('data-id') !== 'undefined') ? $(this).attr('data-id') : false;
						if(PRODUCT_ID && (typeof arViewedLocal[PRODUCT_ID] === 'undefined')){
							$(this).removeClass('has-item').find('>.inner_wrap').remove();
						}
					});

					// if no items than remove block
					if(!$viewedSlider.find('>.item.has-item').length){
						$viewedSlider.closest('.viewed_block').remove();
					}
				}
				catch(e){
					console.error(e);
				}
				finally{
					// restore $.cookie option
					$.cookie.json = bCookieJson;
				}
			}

			$('.viewed_block .rows_block .item .item-title').dotdotdot();
			$('.viewed_block .rows_block .item .item-title').sliceHeightNoResize({outer:true, slice:8, autoslicecount:false});
			$('.viewed_block .rows_block .item').sliceHeightNoResize({slice:8, autoslicecount:false});
		});
		</script>
	<?endif;?>
<?endif;?>
<!-- /noindex -->