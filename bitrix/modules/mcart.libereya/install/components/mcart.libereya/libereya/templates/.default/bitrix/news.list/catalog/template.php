<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->SetViewTarget('list_pager');?>
	<?=$arResult["NAV_STRING"]?>
<?$this->EndViewTarget('list_pager');?>	
<div class="library-products">

<?$cnt = 1; // line reset counter?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?//dump($arItem["PROPERTIES"]);?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
<?if($cnt == 5): $cnt = 1;?>
	<div class="cb"></div>
</div>

<div class="library-products">
<?endif;?>
	<div class="item-col item-<?=$cnt;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="item">
			<div class="rating-content">
				<?$APPLICATION->IncludeComponent(
					"mcart.libereya:libereya.vote",
					"ajax",
					Array(
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"ELEMENT_ID" => $arItem["ID"],
						"MAX_VOTE" => $arParams["MAX_VOTE"],
						"VOTE_NAMES" => $arParams["VOTE_NAMES"],
						"CACHE_TYPE" => $parent->arParams["CACHE_TYPE"],
						"CACHE_TIME" => $parent->arParams["CACHE_TIME"],
						"DISPLAY_AS_RATING" => $parent->arParams["DISPLAY_AS_RATING"],
					),
					$component->GetParent()
				);?>
			</div>
			<a class="image" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
				<img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" style="float:left" />
			</a>
			<div class="about">
		
				<span class="author">
				
				<?
				$authors = array();
				if(is_array($arItem["PROPERTIES"]["AUTHORS"]["VALUE"]))
				{
					foreach($arItem["PROPERTIES"]["AUTHORS"]["VALUE"] as $item){						$authors[] = $arParams['LINKED_ELEMENTS']["AUTHORS"][$item]; 
					}
					echo implode(', ', $authors);
				}
				
				?>
				
				</span>
				<span class="name"><?=$arItem["NAME"]?></span>
				<span class="pages"></span>	
			</div>
			<div class="group">
				<?//print_r($arItem["PROPERTIES"]);?>
				<div id="booking_result_<?=$arItem['ID']?>">
					<?if(!empty($arItem["PROPERTIES"]['BOOK_FILE']['VALUE']) || $arItem["PROPERTIES"]["BOOK_TYPE"]["VALUE"] == GetMessage("MCART_LIBEREYA_ELEKTRONNAA")):?>
						<span class="btn btn-greys"><?=GetMessage("MCART_LIBEREYA_ELEKTRONNAA1")?></span>			
					<?elseif( empty($arItem["PROPERTIES"]["BOOKING"]["VALUE"]) 	
							):?>
						<a class="btn btn-green" onclick="app.loadAsync('/bitrix/components/mcart.libereya/libereya/async.php', 'async=y&element_id=<?=$arItem['ID']?>&action=booking', 'booking_result_<?=$arItem['ID']?>');" href="javascript:;"><?=GetMessage("MCART_LIBEREYA_ZABRONIROVATQ")?></a>
					<?else:?>
						<span class="btn btn-greys"><?=GetMessage("MCART_LIBEREYA_NET_V_NALICII")?></span>
					<?endif;?>
				</div>
				<?if(!empty($arItem["PREVIEW_TEXT"])):?>
				<a class="list" onclick="return app.annotation(this)" href="#"></a>
				<div class="dispnone annotation_content"><h5><?=GetMessage('ANNOTATION_HEADER');?></h5><p><?=$arItem["PREVIEW_TEXT"]?></p></div>
				<?endif;?>
			</div>
			

			<?$cnt++;?>
		</div>	
	</div>		
<?endforeach;?>
	<div class="cb"></div>
</div>
