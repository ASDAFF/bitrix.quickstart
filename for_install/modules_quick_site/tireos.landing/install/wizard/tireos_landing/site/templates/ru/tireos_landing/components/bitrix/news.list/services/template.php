<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?foreach($arResult["ITEMS"] as $i=>$arItem):?>

<?
	$price1 = floor($arItem["PROPERTIES"]["PRICE"]["VALUE"]);
	$price2 = number_format($arItem["PROPERTIES"]["PRICE"]["VALUE"] - $price1, 2, ".", " ");
	$price2 = substr($price2, 2);
?>

<?if($i==0):?>
<div class="col-sm-4 noR-border table-offers" id='animIt11'>
	<table class='table package-services not-favorable basic'>
		<caption class='noR-border'>
<?elseif($i==1):?>
<div class="col-sm-4 profitable table-offers" id='animIt13'>
	<table class='table package-services business'>
		<caption>
<?else:?>
<div class='col-sm-4 noL-border table-offers' id='animIt12'>
	<table class='table package-services not-favorable premium'>
		<caption class='noL-border'>
<?endif;?>
    
		<?=$arItem["NAME"]?></caption>
		 <thead>
			<tr>
				<td><b><sup>от</sup><?=$price1?></b><sup>руб.</sup></td>
			</tr>
		 </thead>
		<tbody>
        <?foreach($arItem["PROPERTIES"]["ADVANTAGES"]["VALUE"] as $item):?>
		<tr>
			<td><?=$item?></td>
		</tr>
        <?endforeach;?>
		<tr class='lastTr'>
			<td><a class='btn buy-now<?if($i==1):?> offsetY-5<?elseif($i==2):?> offsetY-6<?endif;?>' href='<?=$arItem["PROPERTIES"]["LINK"]["VALUE"]?>'>Заказать</a></td>
		</tr>
	</tbody>
</table>
				</div>
<?endforeach;?>