<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//echo "<pre>"; print_r($arResult); echo "</pre>";?>
	<div class="b-banner m-sidebar" <?if($arResult["BANNER_PROPERTIES"]["URL"]!=""){?>onclick="location.href = '<?=$arResult["BANNER_PROPERTIES"]["URL"]?>'" style="cursor:pointer" <?}?>>
		<h2 class="b-h2__green"><?=$arResult["BANNER_PROPERTIES"]["NAME"]?></h2>
		<?=$arResult["BANNER"]?>
	</div>