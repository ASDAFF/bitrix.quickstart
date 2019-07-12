<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-top">

<?
foreach($arResult["ROWS"] as $arItems):
?>
	<div class="catalog-item-cards">
	<table class="catalog-item-card" cellspacing="0">
		<tr class="top">
<?
	foreach($arItems as $key => $arElement):
		if ($key > 0):
?>
			<td class="delimeter"></td>
<?
		endif;
?>
			<td width="<?=$arResult["TD_WIDTH"]?>"><?if(is_array($arElement)):?><div class="corner left-top"></div><div class="corner right-top"></div><div class="border-top"></div><?endif;?></td>
<?
	endforeach;
?>
		</tr>
		<tr>
<?
	foreach($arItems as $key => $arElement):
		if ($key > 0):
?>
			<td class="delimeter"></td>
<?
		endif;
		if(is_array($arElement)):
			$bPicture = is_array($arElement["PREVIEW_IMG"]);
?>
			<td>
				<div class="catalog-item-card<?=$bPicture ? '' : ' no-picture-mode'?>">
<?
			if ($bPicture):
?>
					<div class="item-image">
						<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" id="catalog_list_image_<?=$arElement['ID']?>" /></a>
					</div>
<?
			endif;
?>
					<div class="item-info">
						<p class="item-title">
							<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a>
						</p>
						<p class="item-desc">
<?
			foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):
?>
							<small><?=$arProperty["NAME"]?>:&nbsp;<?
				if(is_array($arProperty["DISPLAY_VALUE"]))
					echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
				else
					echo $arProperty["DISPLAY_VALUE"];
?></small><br />
<?
			endforeach;
?>
							<?=strip_tags($arElement["PREVIEW_TEXT"]);?>
							<span class="item-desc-overlay"></span>
						</p>
<?
			if(count($arElement["PRICES"])>0):
				foreach($arElement["PRICES"] as $code=>$arPrice):
					if($arPrice["CAN_ACCESS"]):
?>
						<p class="item-price">
<?
						if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):
?>
							<span><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span> <s><?=$arPrice["PRINT_VALUE"]?></s> 
<?
						else:
?>
							<span><?=$arPrice["PRINT_VALUE"]?></span>
<?
						endif;
?>
						</p>
<?
					endif;
				endforeach;
			else:
				$price_from = '';
				if($arElement['DISPLAY_PROPERTIES']['MAXIMUM_PRICE']['VALUE'] > $arElement['DISPLAY_PROPERTIES']['MINIMUM_PRICE']['VALUE'])
				{
					$price_from = GetMessage("CR_PRICE_OT");	
				}
				CModule::IncludeModule("sale")
?>
				<p class="item-price"><span><?=$price_from?><?=FormatCurrency($arElement['DISPLAY_PROPERTIES']['MINIMUM_PRICE']['VALUE'], CSaleLang::GetLangCurrency(SITE_ID))?></span></p>
<?
			endif;
?>
					</div>
				</div>
			</td>
<?
		else:
?>
			<td class="delimeter"></td>
<?
		endif;
?>
<?
	endforeach;
?>
		</tr>
		<tr class="bottom">
<?
	foreach($arItems as $key => $arElement):
		if ($key > 0):
?>
			<td class="delimeter"></td>
<?
		endif;
?>
			<td><?if(is_array($arElement)):?><div class="corner left-bottom"></div><div class="corner right-bottom"></div><div class="border-bottom"></div><?endif;?></td>
<?
	endforeach;
?>
		</tr>
	</table>
	</div>
<?
endforeach;
?>
</div>

