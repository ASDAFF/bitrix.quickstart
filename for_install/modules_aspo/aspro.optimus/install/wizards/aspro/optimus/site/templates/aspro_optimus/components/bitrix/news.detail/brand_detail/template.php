<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<div class="news_detail_wrapp ">
	<?if( !empty($arResult["DETAIL_PICTURE"])):?>
		<div class="detail_picture_block clearfix">
			<?$img = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array( "width" => 270, "height" => 175 ), BX_RESIZE_IMAGE_PROPORTIONAL, true, array() );?>
			<a href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" class="fancy">
				<img src="<?=$img["src"]?>" alt="<?=($arResult["DETAIL_PICTURE"]["ALT"] ? $arResult["DETAIL_PICTURE"]["ALT"] : $arResult["NAME"])?>" title="<?=($arResult["DETAIL_PICTURE"]["TITLE"] ? $arResult["DETAIL_PICTURE"]["TITLE"] : $arResult["NAME"])?>" height="<?=$img["height"]?>" width="<?=$img["width"]?>" />
			</a>
		</div>
	<?endif;?>
	<?if ($arResult["PREVIEW_TEXT"]):?>
		<div class="<?if ($left_side):?>right_side<?endif;?> preview_text"><?=$arResult["PREVIEW_TEXT"];?></div>
	<?endif;?>
	<?if ($arResult["DETAIL_TEXT"]):?>
		<div class="detail_text <?=($arResult["DETAIL_PICTURE"] ? "wimg" : "");?>"><?=$arResult["DETAIL_TEXT"]?></div>
	<?endif;?>
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
		
	
</div>