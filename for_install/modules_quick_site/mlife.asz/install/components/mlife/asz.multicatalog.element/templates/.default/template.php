<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?//echo'<pre>';print_r($arResult);echo'</pre>';?>
<script type="text/javascript">
$("a.fancyDetail, a.galerytovar").fancybox({
//"padding": 0,
"margin": 50,
"titleShow": true,
helpers: {
	title : {
			type : 'float'
		},
	thumbs : {
            width: 50,
            height: 50
        }
}
});
</script>

<div class="catalogElement prod<?=$arResult["ID"]?>">
	<h1><?=$arResult["NAME"]?></h1>
	<div class="leftblock">
		<div class="image">
			<?if(isset($arResult["IMAGE"]["SRC"])){?>
				<a href="<?=$arResult["IMAGE"]["BIG_SRC"]?>" class="galerytovar" rel="galery"><img alt="<?=$arResult["NAME"]?>" src="<?=$arResult["IMAGE"]["SRC"]?>"/></a>
			<?}else{?>
				<img src="<?=$templateFolder?>/images/no_photo.jpg"/>
			<?}?>
		</div>
				<?
		if (is_array($arResult['MORE_PHOTO']) && count($arResult['MORE_PHOTO']) > 0) {
		?>
		<div class="addImage"><div id="slider_<?=$arResult['ID']?>" class="addImageslider"><div class="slider-wrap">
			<?
			$i=2;
			foreach($arResult['MORE_PHOTO'] as $foto) {
			if(fmod($i,2)==0 and $i==2){
			echo '<div class="slide">';
			}
			elseif (fmod($i,2)==0) {
			echo '</div><div class="slide">';
			}
			?>
			<a class="fancyDetail" rel="galery" href="<?=$foto['SRC']?>"><img src="<?=$foto['SRC_PREW']?>" alt="<?=$arResult["NAME"]?>" width="<?=$foto["PREVIEW_WIDTH"]?>" height="<?=$foto["PREVIEW_HEIGHT"]?>"/></a>
			<?
			$i++;
			}?></div>
		</div></div>
		</div>
		<?if(count($arResult['MORE_PHOTO']) > 0){?>
		<script type="text/javascript">
			<?if(count($arResult['MORE_PHOTO']) > 2){?>
			$("#slider_<?=$arResult['ID']?>").mlfslide({
			'id': <?=$arResult['ID']?>,
			'mlfSlideSpeed' : 700,
			'mlfTimeOut' : 0,
			'mlfNeedCount' : false,
			});
			<?}else{?>
			$("#slider_<?=$arResult['ID']?>").mlfslide({
			'id': <?=$arResult['ID']?>,
			'mlfSlideSpeed' : 700,
			'mlfTimeOut' : 0,
			'mlfNeedLinks' : false,
			'mlfNeedCount' : false,
			});
			<?}?>
		</script>
		<?}?>
		<?
		}
		?>
	</div>
	<div class="descrBlock">
		<?if(!empty($arParams["PROPERTY_CODE_LABEL"])){?>
			<div class="labels">
				<?
				$i=0;
				foreach($arParams["PROPERTY_CODE_LABEL"] as $labelCode){
				$i++;
				if($i==4) $i = 1;
				?>
				<?if($arResult["PROPERTIES"][$labelCode]["VALUE"]){?>
				<div class="label color<?=$i?>"><?=$arResult["PROPERTIES"][$labelCode]["NAME"]?>
				<?if($arResult["PROPERTIES"][$labelCode]["VALUE"]!="Y"){?>: <?=$arResult["PROPERTIES"][$labelCode]["VALUE"]?><?}?></div>
				<?}?>
				<?}?>
				<?if(isset($arResult['DISCOUNT'][$arResult["ID"]]['DISCOUNT']) && $arResult['DISCOUNT'][$arResult["ID"]]['DISCOUNT']>0){
				$i++;
				if($i==4) $i = 1;
				?>
				<div class="label color<?=$i?>">
				<?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_T_SKIDKA");?>: 
				<?$discount = round(100*($arResult['DISCOUNT'][$arResult["ID"]]['DISCOUNT']/$arResult['DISCOUNT'][$arResult["ID"]]['PRICE']))?>
				<?if($discount>1){?>
				<?=$discount?>%
				<?}else{?>
				<?=\Mlife\Asz\CurencyFunc::priceFormat($arResult['DISCOUNT'][$arResult["ID"]]['DISCOUNT'])?>
				<?}?>
				</div>
				<?}?>
			</div>
		<?}?>
		<div class="avalible<?if($arResult["QUANT"]<=0){?> zakaz<?}?>"><?if($arResult["QUANT"]>0){?><?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_E_1")?><?}else{?><?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_E_2")?><?}?></div>
		<div class="price">
		<?=GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_T_1")?>: 
		<?if($arResult["PRICE"][$arResult["ID"]]["DISPLAY"]){?>
		<?if(isset($arResult['DISCOUNT'][$arResult["ID"]]['DISCOUNT']) && $arResult['DISCOUNT'][$arResult["ID"]]['DISCOUNT']>0){?>
			<div class="oldPrice"><?=$arResult["PRICE"][$arResult["ID"]]["DISPLAY"]?></div>
			<div class="newPrice"><?=\Mlife\Asz\CurencyFunc::priceFormat($arResult['DISCOUNT'][$arResult["ID"]]['DISCOUNT_PRICE'])?></div>
		<?}else{?>
			<?=$arResult["PRICE"][$arResult["ID"]]["DISPLAY"]?>
		<?}?>
		<?}else{?>
		<?=GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_T_2")?>
		<?}?></div>
		<div class="text"><?=$arResult["PREVIEW_TEXT"]?></div>
		<?if($arResult["PRICE"][$arResult["ID"]]["VALUE"]>0){?>
		<div class="addToCart">
			<a href="#" data-id="<?=$arResult["ID"]?>"><?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_T_2")?></a>
		</div>
		<?}?>
	</div>
	<div class="mlf_descr_tab">
		<?$showtabindex=true;
		$showtab = false;
		?>
		<ul class="tabNavigation">
			<li><a href="#all"><?=GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_T_3")?></a></li>
			<?if($arResult["DETAIL_TEXT"]){?>
			<li><a href="#tab2"<?if($showtabindex) { echo ' class="selected"';$showtabindex=false;}?>><?=GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_T_4")?></a></li>
			<?
			$showtab = true;
			}?>
			<?if(is_array($arResult["DISPLAY_PROPERTIES"]) && count($arResult["DISPLAY_PROPERTIES"])>0){?>
			<li><a href="#tab3"<?if($showtabindex) { echo ' class="selected"';$showtabindex=false;}?>><?=GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_T_5")?></a></li>
			<?
			$showtab = true;
			}?>
		</ul>
		<?if($arResult["DETAIL_TEXT"]){?>
		<div class="mlfTab" id="tab2">
			<div class="allDesc">
				<?=$arResult["DETAIL_TEXT"]?>
			</div>
		</div>
		<?}?>
		<?if(is_array($arResult["DISPLAY_PROPERTIES"]) && count($arResult["DISPLAY_PROPERTIES"])>0){?>
		<div class="mlfTab" id="tab3">
			<div class="harakt">
				<?if($arResult["DISPLAY_PROPERTIES"]["YANDEX_DESC"]["VALUE"]["TEXT"]){?>
				<?=htmlspecialcharsBack($arResult["DISPLAY_PROPERTIES"]["YANDEX_DESC"]["VALUE"]["TEXT"])?>
				<?}else{?>
				<table class="haraktAll">
					<?foreach($arResult["DISPLAY_PROPERTIES"] as $prop){?>
						<tr>
						<td class="name"><?=$prop['NAME']?></td>
						<td>
						<?if(is_array($prop['DISPLAY_VALUE']) && count($prop['DISPLAY_VALUE']>0)){?>
							<?
							echo implode(',',$prop['DISPLAY_VALUE']);
							?>
						<?}else{?>
						<?=trim($prop['DISPLAY_VALUE'])?>
						<?}?>
						</td>
						</tr>
					<?}?>
				</table>
				<?}?>
			</div>
		</div>
		<?}?>
	</div>
	
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(function () {
			var tabContainers = $('div.mlf_descr_tab > div');
			tabContainers.hide().filter($('div.mlf_descr_tab ul.tabNavigation a.selected').attr('href')).slideToggle();
			$('div.mlf_descr_tab ul.tabNavigation a').click(function () {
			$('div.mlf_descr_tab > div .tabtitle').remove();
			if(this.hash=='#all') {
				for (var i = 0; i < tabContainers.length; i++) {
				$('#'+tabContainers[i].id).prepend('<h3 class="tabtitle">'+$('ul.tabNavigation li a[href="#'+tabContainers[i].id+'"]').html()+'</h3>');
				}
				tabContainers.slideDown();
			} else {
				tabContainers.hide();
				tabContainers.filter(this.hash).slideDown();
			}
			$('div.mlf_descr_tab ul.tabNavigation a').removeClass('selected');
			$(this).addClass('selected');
			return false;
			}).filter('#tab1').click();
		});
		<?if(!$showtab){?>$(".mlf_descr_tab").hide();<?}?>
	});
</script>