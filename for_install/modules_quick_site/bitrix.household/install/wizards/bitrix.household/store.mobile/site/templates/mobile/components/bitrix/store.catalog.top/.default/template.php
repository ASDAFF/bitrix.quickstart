<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-top">

<?
foreach($arResult["ROWS"] as $arItems):
?>
	<div class="catalog-item-cards">
	<table class="catalog-item-card" cellspacing="0">
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
						<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" id="catalog_list_image_<?=$arElement['ID']?>" /></a><br />
					</div>
<?
			endif;
?>
					<div class="item-info">
						<p class="item-title">
							<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a>
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
	</table>
	</div>
<?
endforeach;
?>
</div>

