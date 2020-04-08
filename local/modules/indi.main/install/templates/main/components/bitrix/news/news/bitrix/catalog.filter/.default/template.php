<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="news-period">

	<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">
		<?foreach($arResult["ITEMS"] as $arItem):
			if(array_key_exists("HIDDEN", $arItem)):
				echo $arItem["INPUT"];
			endif;
		endforeach;?>




        <?//php if(SITE_ID === "s1"){?>
            <a class=" <?if($_REQUEST["set_filter"]!="Y"){?>active<?}?>" href="/kaluga.kuzov-auto.ru/newsga.kuzov-auto.ru/news"><?=GetMessage("IBLOCK_DEL_FILTER_ALL");?></a>
        <?/*php } else {?>
            <a class=" <?if($_REQUEST["set_filter"]!="Y"){?>active<?}?>" href="/en/news/"><?=GetMessage("IBLOCK_DEL_FILTER_ALL");?></a>
        <?php }*/?>

		<?foreach($arResult["ITEMS"] as $arItemK=>$arItem):?>

			<?if(!array_key_exists("HIDDEN", $arItem)):?>
				<?
				if($arItemK=='DATE_ACTIVE_FROM'){
					for ($i = $arResult["YEAR_TO"]; $i >= $arResult["YEAR_FROM"]; $i--) {
						$active = "";
						if(isset($arItem["INPUT_VALUES"][0]) && !empty($arItem["INPUT_VALUES"][0]) && isset($arItem["INPUT_VALUES"][1]) && !empty($arItem["INPUT_VALUES"][1])) {
							if (ConvertDateTime($arItem["INPUT_VALUES"][0], "YYYY", "ru") <= $i && ConvertDateTime($arItem["INPUT_VALUES"][1], "YYYY", "ru") >= $i) {
								$active = "active";
							}
						}
						else{
//								if($i == $arResult["YEAR_TO"]){
//									$active = "active";
//								}
						}
						?>

						<a class=" js-news-period <?=$active?>" href="#"><?=$i;?></a>

						<?
					}

					?>
					<div style="display: none;">
						<?=$arItem["INPUT"]?>
					</div>
				<?}?>

			<?endif?>
		<?endforeach;?>
		<input type="hidden" name="set_filter" value="Y" />

	</form>
</div>



