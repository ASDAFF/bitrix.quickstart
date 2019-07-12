<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//echo '<pre>';print_r($arResult); echo '</pre>';

if(is_array($arResult['ITEMS']) && ($cnt = count($arResult['ITEMS']))>0) {?>
<div class="catalogList">
	<?$i=0; $count = 3;
	foreach($arResult['ITEMS'] as $item){
		?>
		<div class="item prod<?=$item['ID']?>">
			<div class="image">
			<a href="<?=$item['DETAIL_PAGE_URL']?>">
			<?if(isset($item['IMAGE']) && is_array($item['IMAGE']) && isset($item['IMAGE']['SRC']) && strlen($item['IMAGE']['SRC'])>0){?>
				<img src="<?=$item['IMAGE']['SRC']?>" alt="<?=$item['NAME']?>"/>
			<?}else{?>
				<img src="<?=$templateFolder?>/images/no_photo.jpg" alt="<?=$item['NAME']?>"/>
			<?}?>
			</a>
			</div>
			<div class="desc">
				<div class="rightDesc">
					<?if(isset($arResult["PRICE"][$item["ID"]]["DISPLAY"])){?>
						<div class="price">
						<?if($arResult["PRICE"][$item["ID"]]["VALUE"]>0){?>
						<?if(isset($arResult['DISCOUNT'][$item["ID"]]['DISCOUNT']) && $arResult['DISCOUNT'][$item["ID"]]['DISCOUNT']>0){?>
						<div class="oldPrice"><?=$arResult["PRICE"][$item["ID"]]["DISPLAY"]?></div>
						<div class="newPrice"><?=\Mlife\Asz\CurencyFunc::priceFormat($arResult['DISCOUNT'][$item["ID"]]['DISCOUNT_PRICE'])?></div>
						<?}else{?>
						<?=$arResult["PRICE"][$item["ID"]]["DISPLAY"]?>
						<?}?>
						<?}else{?>
						<?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_T_1");?>
						<?}?>
						</div>
						<?if($arResult["PRICE"][$item["ID"]]["VALUE"]>0){?>
						<div class="addToCart">
							<a href="#" data-id="<?=$item["ID"]?>"><?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_T_2");?></a>
						</div>
						<?}?>
					<?}?>
					<div class="avalible"></div>
				</div>
				<div class="leftDesc">
					<div class="name"><a href="<?=$item['DETAIL_PAGE_URL']?>"><?=$item['NAME']?></a></div>
					<?if(!empty($arParams["PROPERTY_CODE_LABEL"])){?>
						<div class="labels">
							<?
							$i=0;
							foreach($arParams["PROPERTY_CODE_LABEL"] as $labelCode){
							$i++;
							if($i==4) $i = 1;
							?>
							<?if($item["PROP"][$labelCode]["VALUE"]){?>
							<div class="label color<?=$i?>"><?=$item["PROP"][$labelCode]["NAME"]?>
							<?if($item["PROP"][$labelCode]["VALUE"]!="Y"){?>: <?=$item["PROP"][$labelCode]["VALUE"]?><?}?></div>
							<?}?>
							<?}?>
							<?if(isset($arResult['DISCOUNT'][$item["ID"]]['DISCOUNT']) && $arResult['DISCOUNT'][$item["ID"]]['DISCOUNT']>0){
							$i++;
							if($i==4) $i = 1;
							?>
							<div class="label color<?=$i?>">
							<?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_T_SKIDKA");?>: 
							<?$discount = round(100*($arResult['DISCOUNT'][$item["ID"]]['DISCOUNT']/$arResult['DISCOUNT'][$item["ID"]]['PRICE']))?>
							<?if($discount>1){?>
							<?=$discount?>%
							<?}else{?>
							<?=\Mlife\Asz\CurencyFunc::priceFormat($arResult['DISCOUNT'][$item["ID"]]['DISCOUNT'])?>
							<?}?>
							</div>
							<?}?>
						</div>
					<?}?>
					<?if(isset($item["DAY"])){
					$dateOb = new \Bitrix\Main\Type\DateTime($arResult["DISCOUNT_INFO"][$item["DAY"]]["DATE_END"]);
					?>
						<script>
						$(document).ready(function(){
							var unix_timestamp = <?=$dateOb->getTimestamp()?>;
							$('#share_<?=$item["ID"]?>').countdown({
							timestamp: new Date(unix_timestamp*1000),
							showtext: false,
							text_d: "<?=GetMessage("MLIFE_ASZ_CATALOG_DAY_TITLE")?>",
							});
						});
						</script>
						<div class="shareWrapper">
						<div class="titleDay"><?=GetMessage("MLIFE_ASZ_CATALOG_DAY_TITLE_MAIN")?>:</div><div class="share" id="share_<?=$item["ID"]?>"></div>
						</div>
					<?}?>
				</div>
			</div>
		</div>
		<?
	}
	?>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]=="Y"){?>
<div class="nav">
<?echo $arResult["NAV_STRING"];?>
</div>
<?}?>
	<?
}

?>