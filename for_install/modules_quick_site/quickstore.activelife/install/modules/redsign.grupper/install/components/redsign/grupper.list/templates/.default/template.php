<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<div id="rs_grupper">
<?foreach($arResult["GROUPED_ITEMS"] as $arrValue):?>
	<?if(is_array($arrValue["PROPERTIES"]) && count($arrValue["PROPERTIES"])>0):?>
		<strong><?=$arrValue["GROUP"]["NAME"]?></strong>
		<ul class="options">
		<?foreach($arrValue["PROPERTIES"] as $property):?>
			<li><span><?=$property["NAME"]?></span><b><?=$property["DISPLAY_VALUE"]?></b></li>
		<?endforeach;?>
		</ul>
		<hr />
	<?endif;?>
<?endforeach;?>

<?if(is_array($arResult["NOT_GROUPED_ITEMS"]) && count($arResult["NOT_GROUPED_ITEMS"])>0):?>
	<ul class="options">
		<?foreach($arResult["NOT_GROUPED_ITEMS"] as $property):?>
			<li><span><?=$property["NAME"]?></span><b><?=$property["DISPLAY_VALUE"]?></b></li>
		<?endforeach;?>
	</ul>
<?endif;?>
</div>