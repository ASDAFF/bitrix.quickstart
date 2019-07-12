<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<div class="news_detail_wrapp big item">
	<?if( !empty($arResult["DETAIL_PICTURE"])):?>
		<div class="detail_picture_block clearfix">
			<?$img = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array( "width" => 270, "height" => 185 ), BX_RESIZE_IMAGE_PROPORTIONAL, true, array() );?>
			<a href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" class="fancy">
				<img src="<?=$img["src"]?>" alt="<?=($arResult["DETAIL_PICTURE"]["ALT"] ? $arResult["DETAIL_PICTURE"]["ALT"] : $arResult["NAME"])?>" title="<?=($arResult["DETAIL_PICTURE"]["TITLE"] ? $arResult["DETAIL_PICTURE"]["TITLE"] : $arResult["NAME"])?>"/>
			</a>
		</div>
	<?endif;?>
	<?if($arParams["DISPLAY_DATE"]!="N"):?>
			<?if($arResult["PROPERTIES"][$arParams["PERIOD_PROPERTY"]]["VALUE"]):?>
				<div class="news_date_time_detail date_small"><?=$arResult["PROPERTIES"][$arParams["PERIOD_PROPERTY"]]["VALUE"];?></div>	
			<?elseif($arResult["DISPLAY_ACTIVE_FROM"]):?>
				<div class="news_date_time_detail date_small"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></div>	
			<?endif;?>
	<?endif;?>

	<?if ($arResult["PREVIEW_TEXT"]):?>
		<div class="<?if ($left_side):?>right_side<?endif;?> margin preview_text"><?=$arResult["PREVIEW_TEXT"];?></div>
	<?endif;?>
	
		
	<?if ($arResult["DETAIL_TEXT"]):?>
		<div class="detail_text <?=($arResult["DETAIL_PICTURE"] ? "wimg" : "");?>"><?=$arResult["DETAIL_TEXT"]?></div>
	<?endif;?>
	
	
	<?/*if($arParams["SHOW_PRICE"]=="Y" && $arResult["PROPERTIES"][$arParams["PRICE_PROPERTY"]]["VALUE"]):?>
		<div class="price_block">
			<div class="price"><?=GetMessage("SERVICE_PRICE")?> <?=$arResult["PROPERTIES"][$arParams["PRICE_PROPERTY"]]["VALUE"];?></div>
		</div>
	<?endif;*/?>

	<div class="clear"></div>
	<?if($arParams["SHOW_GALLERY"]=="Y" && $arResult["PROPERTIES"][$arParams["GALLERY_PROPERTY"]]["VALUE"]){?>
		<div class="row galley">
			<ul class="module-gallery-list">
				<?
					$files = $arResult["PROPERTIES"][$arParams["GALLERY_PROPERTY"]]["VALUE"];		
					if(array_key_exists('SRC', $files)) 
					{
					   $files['SRC'] = '/' . substr($files['SRC'], 1);
					   $files['ID'] = $arResult["PROPERTIES"][$arParams["GALLERY_PROPERTY"]]["VALUE"][0];
					   $files = array($files);
					}
				?>
				<?	foreach($files as $arFile):?>
					<?
						$img = CFile::ResizeImageGet($arFile, array('width'=>230, 'height'=>155), BX_RESIZE_IMAGE_EXACT, true);
						
						$img_big = CFile::ResizeImageGet($arFile, array('width'=>800, 'height'=>600), BX_RESIZE_IMAGE_PROPORTIONAL, true);
						$img_big = $img_big['src'];
					?>
					<li class="item_block">
						<a href="<?=$img_big;?>" class="fancy" data-fancybox-group="gallery">
							<img src="<?=$img["src"];?>" alt="<?=$arResult["NAME"];?>" title="<?=$arResult["NAME"];?>" />
						</a>				  
					</li>
				<?endforeach;?>
			</ul>
			<div class="clear"></div>
		</div>
	<?}?>


	<?/*if ($arParams["SHOW_FAQ_BLOCK"]=="Y"):?>
		<div class="ask_big_block">
			<div class="ask_btn_block">
				<a class="button vbig_btn wides ask_btn"><span><?=GetMessage("ASK_QUESTION")?></span></a>
			</div>
			<div class="description">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/ask_block_detail_description.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("ASK_QUESTION_DETAIL_TEXT"), ));?>
			</div>
		</div>
	<?endif;*/?>
</div>