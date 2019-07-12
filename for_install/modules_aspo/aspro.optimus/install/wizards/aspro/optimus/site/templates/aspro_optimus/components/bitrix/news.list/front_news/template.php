<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if($arResult["ITEMS"]){?>
	<div class="news_blocks front">
		<div class="top_block">
			<?
			$title_block=($arParams["TITLE_BLOCK"] ? $arParams["TITLE_BLOCK"] : GetMessage('NEWS_TITLE'));
			$title_block_all=($arParams["TITLE_BLOCK_ALL"] ? $arParams["TITLE_BLOCK_ALL"] : GetMessage('ALL_NEWS'));
			$url=($arParams["ALL_URL"] ? $arParams["ALL_URL"] : SITE_DIR."company/news/");
			?>
			<div class="title_block"><?=$title_block;?></div>
			<a href="<?=SITE_DIR.$url;?>"><?=$title_block_all;?></a>
			<div class="clearfix"></div>
		</div>
		<div class="info_block">
			<div class="news_items">
				<?foreach($arResult["ITEMS"] as $arItem){
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					?>
					<div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="item box-sizing dl">
						<?if($arItem['PREVIEW_PICTURE'] && $arParams["SHOW_IMAGE"]=="Y"){?>
							<div class="image">
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
									<?$img=CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array( 'width' => 60, 'height' => 60 ), BX_RESIZE_IMAGE_EXACT, true, false, false, 60)?>
									<img class="img-responsive" src="<?=$img["src"];?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>">
								</a>
							</div>
						<?}?>
						<div class="info">
							<?if($arItem["DISPLAY_ACTIVE_FROM"] && $arParams["SHOW_DATE"]=="Y"){?>
								<div class="date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></div>
							<?}?>
							<a class="name dark_link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
						</div>
						<div class="clearfix"></div>
					</div>
				<?}?>
			</div>
		</div>
	</div>
<?}?>