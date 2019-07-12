<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(count($arResult["ITEMS"]) > 0):  ?>


<div class="left-block">
						<div class="lb-wrap">
							<div class="lb-wrap">
								<h2><?= GetMessage("EST_BRANDS")?></h2>

<ul class="links">

<?foreach($arResult["ITEMS"] as $key => $arItem):

	if(is_array($arItem))
	{
        ?><li><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></li>
<?
	}
endforeach;
?>

</ul>


</div>
						</div>
					</div>

<? endif; ?>