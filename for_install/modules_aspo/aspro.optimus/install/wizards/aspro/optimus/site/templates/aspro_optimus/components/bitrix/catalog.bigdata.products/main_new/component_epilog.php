<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
global $APPLICATION;
if (isset($templateData['TEMPLATE_THEME']))
{
	$APPLICATION->SetAdditionalCSS($templateData['TEMPLATE_THEME']);
}
?>
<?$arItems=COptimus::getBasketItems();?>
<script>
	touchItemBlock('.catalog_item a');
	<?if(is_array($arItems["DELAY"]) && !empty($arItems["DELAY"])):?>
		<?foreach( $arItems["DELAY"] as $key=>$item ){?>
			$('.wish_item.to[data-item=<?=$key?>]').hide();
			$('.wish_item.in[data-item=<?=$key?>]').show();
			if ($('.wish_item[data-item=<?=$key?>]').find(".value.added").length) {
				$('.wish_item[data-item=<?=$key?>]').addClass("added");
				$('.wish_item[data-item=<?=$key?>]').find(".value").hide();
				$('.wish_item[data-item=<?=$key?>]').find(".value.added").css('display','block');
			}
		<?}?>
	<?endif;?>
	<?if(is_array($arItems["COMPARE"]) && !empty($arItems["COMPARE"])):?>
		<?foreach( $arItems["COMPARE"] as $key=>$item ){?>
			$('.compare_item.to[data-item=<?=$key?>]').hide();
			$('.compare_item.in[data-item=<?=$key?>]').show();
			if ($('.compare_item[data-item=<?=$key?>]').find(".value.added").length){
				$('.compare_item[data-item=<?=$key?>]').addClass("added");
				$('.compare_item[data-item=<?=$key?>]').find(".value").hide();
				$('.compare_item[data-item=<?=$key?>]').find(".value.added").css('display','block');
			}
		<?}?>
	<?endif;?>
</script>
