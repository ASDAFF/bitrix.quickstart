<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$f = 0;

foreach($arResult["SECTIONS"] as $arSection)
{
	if( $arSection["ELEMENT_CNT"] > 0 )
	{
		$f = 1;
		break;
	}
}

?>



<? if ($f): ?>
<div class="brands-goods">
	<p>
	<?=GetMessage('CT_ITEMS')?>:
	<?foreach($arResult["SECTIONS"] as $arSection){?>
		<?if( $arSection["ELEMENT_CNT"] > 0 ){?>
			<a href="<?=$arSection["SECTION_PAGE_URL"]?>?<?=htmlspecialcharsbx("arrFilter_".$arParams["FILTER_ID"]."_".abs(crc32($arParams["FILTER_ELEMENT"])));?>=Y&set_filter=y"><span><?=$arSection["NAME"]?></span></a> (<?=$arSection["ELEMENT_CNT"]?>)
		<?}?>
	<?}?>
	</p>
</div>
<? endif; ?>

