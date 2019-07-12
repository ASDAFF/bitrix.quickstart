<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<a class="mlfSmallcart" href="<?=SITE_DIR?>personal/basket/">
<?if($arResult["ORDER"]["CNT"]>0){?>
<div class="cart"><?=GetMessage("MLIFE_ASZ_BASKET_SMALL_T_TOVAR")?>: <?=$arResult["ORDER"]["CNT"]?><br/><?=GetMessage("MLIFE_ASZ_BASKET_SMALL_T_SUMM")?>: <?=$arResult["ORDER"]["ITEMSUMFIN_DISPLAY"]?></div>
<?}else{?>
<div class="cartempty"><?=GetMessage("MLIFE_ASZ_BASKET_SMALL_T_EMPTY")?></div>
<?}?>
</a>

<?if($_REQUEST['ajaxsmallbasket']!=1){?>
<script>
$(document).ready(function(){
	$(document).on("refreshBasket",".mlfSmallcart",function(){
		$.ajax({
			url: '<?=$APPLICATION->GetCurPage(false)?>',
			data: {ajaxsmallbasket:'1'},
			dataType : "html",
			success: function (data, textStatus) {
				$('.mlfKorz').html(data);
			}
		});
	});
});
</script>
<?}?>