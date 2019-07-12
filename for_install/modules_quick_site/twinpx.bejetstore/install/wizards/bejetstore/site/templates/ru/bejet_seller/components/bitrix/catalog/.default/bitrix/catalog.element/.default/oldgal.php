	<div class="bx_item_slider" id="<? echo $arItemIDs['BIG_SLIDER_ID']; ?>">
		<div class="bx_bigimages" id="<? echo $arItemIDs['BIG_IMG_CONT_ID']; ?>">
			<div class="bx_bigimages_imgcontainer">
				<span class="bx_bigimages_aligner"><img id="<? echo $arItemIDs['PICT']; ?>" src="<? echo $arFirstPhoto['SRC']; ?>" alt="<? echo $strAlt; ?>" title="<? echo $strTitle; ?>" ></span>
				<?
				if (('Y' == $arParams['SHOW_DISCOUNT_PERCENT'] && 0 < $arResult['MIN_PRICE']['DISCOUNT_DIFF']) || $arResult['LABEL'])
				{
				?>
					<div class="bj-gallery-info">
					<?
					if ('Y' == $arParams['SHOW_DISCOUNT_PERCENT'] && 0 < $arResult['MIN_PRICE']['DISCOUNT_DIFF'])
					{
					?>
						<span class="bj-gallery-info__i i-discount" id="<? echo $arItemIDs['DISCOUNT_PICT_ID'] ?>" style="display: none;"></span>
						<?if ($arResult['LABEL'])
						{
						?>
										<span class="i-sep"></span>
						<?
						}
						?>
					<?
					}
					?>
					<?				
					if ($arResult['LABEL'])
					{
					?>
						<span class="bj-gallery-info__i i-new" id="<? echo $arItemIDs['STICKER_ID'] ?>"><? echo $arResult['LABEL_VALUE']; ?></span>
					<?
					}
					?>
					</div>
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
									<li data-value="<? echo $arOnePhoto['ID']; ?>" style="width: <? echo $strOneWidth; ?>; padding-top: <? echo $strOneWidth; ?>;"><span class="cnt"><span class="cnt_item" style="background-image:url('<? echo $arOnePhoto['SRC']; ?>');"></span></span></li>
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
										<li data-value="<? echo $arOneOffer['ID'].'_'.$arOnePhoto['ID']; ?>" style="width: <? echo $strOneWidth; ?>; padding-top: <? echo $strOneWidth; ?>"><span class="cnt"><span class="cnt_item" style="background-image:url('<? echo $arOnePhoto['SRC']; ?>');"></span></span></li>
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