<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$frame = $this->createFrame()->begin();?>
<?
if(strlen($arResult["ERROR_MESSAGE"]) > 0){
	ShowError($arResult["ERROR_MESSAGE"]);
}
?>
<?if(count($arResult["STORES"]) > 0):?>
	<?
	// get shops
	$arShops = array();
	CModule::IncludeModule('iblock');
	$dbRes = CIBlock::GetList(array(), array('CODE' => 'aspro_optimus_shops', 'ACTIVE' => 'Y', 'SITE_ID' => SITE_ID));
	if ($arShospIblock = $dbRes->Fetch()){
		$dbRes = CIBlockElement::GetList(array(), array('ACTIVE' => 'Y', 'IBLOCK_ID' => $arShospIblock['ID']), false, false, array('ID', 'DETAIL_PAGE_URL', 'PROPERTY_STORE_ID'));
		while($arShop = $dbRes->GetNext()){
			$arShops[$arShop['PROPERTY_STORE_ID_VALUE']] = $arShop;
		}
	}
	?>
	<div class="stores_block_wrap">
		<?$empty_count=0;
		$count_stores=count($arResult["STORES"]);?>
		<?foreach($arResult["STORES"] as $pid => $arProperty):
			if($arParams['SHOW_EMPTY_STORE'] == 'N' && $arProperty['AMOUNT'] <= 0)
				$empty_count++;?>
			<div class="stores_block <?=(isset($arProperty["IMAGE_ID"]) && !empty($arProperty["IMAGE_ID"]) ? 'w_image' : 'wo_image')?>" style="display: <? echo ($arParams['SHOW_EMPTY_STORE'] == 'N' && $arProperty['AMOUNT'] <= 0 ? 'none' : ''); ?>;">
				<div class="stores_text_wrapp <?=(isset($arProperty["IMAGE_ID"]) && !empty($arProperty["IMAGE_ID"]) ? 'image_block' : '')?>">
					<?if (isset($arProperty["IMAGE_ID"]) && !empty($arProperty["IMAGE_ID"])):?>
						<div class="imgs"><?=GetMessage('S_IMAGE')?> <?=CFile::ShowImage($arProperty["IMAGE_ID"], 100, 100, "border=0", "", true);?></div>
					<?endif;?>
					<div class="main_info">
						<?if (isset($arProperty["TITLE"])):?>
							<span>
								<a class="title_stores" href="<?=$arProperty["URL"]?>" data-storehref="<?=$arProperty["URL"]?>" data-iblockhref="<?=$arShops[$arProperty['ID']]['DETAIL_PAGE_URL']?>"> <?=$arProperty["TITLE"].(strlen($arProperty["ADDRESS"]) && strlen($arProperty["TITLE"]) ? ', ' : '').$arProperty["ADDRESS"]?></a>
							</span>
						<?endif;?>
						<?if(isset($arProperty["PHONE"])):?><span class="store_phone p10"><?=GetMessage('S_PHONE')?> <?=$arProperty["PHONE"]?></span><?endif;?>
						<?if(isset($arProperty["SCHEDULE"])):?><div class="schedule p10"><?=GetMessage('S_SCHEDULE')?>&nbsp;<?=str_replace("&lt;br/&gt;", "<br/>", $arProperty["SCHEDULE"]);?></div><?endif;?>
						<?if (!empty($arProperty['USER_FIELDS']) && is_array($arProperty['USER_FIELDS'])){
							foreach ($arProperty['USER_FIELDS'] as $userField){
								if (isset($userField['CONTENT'])){
									?><span><?=$userField['TITLE']?>: <?=$userField['CONTENT']?></span><br /><?
								}
							}
						}?>
						<?if ($arParams['SHOW_GENERAL_STORE_INFORMATION'] == "Y"){?>
							<?=GetMessage('BALANCE')?>
						<?}?>
					</div>
				</div>
				<?
				$totalCount = COptimus::CheckTypeCount($arProperty["NUM_AMOUNT"]);
				$arQuantityData = COptimus::GetQuantityArray($totalCount);
				?>
				<?if(strlen($arQuantityData["TEXT"])):?>
					<?=$arQuantityData["HTML"]?>
				<?endif;?>
			</div>
		<?endforeach;?>
		<?if($empty_count==$count_stores){?>
			<div class="stores_block">
				<div class="stores_text_wrapp"><?=GetMessage('NO_STORES')?></div>
			</div>
		<?}?>
	</div>
<?endif;?>