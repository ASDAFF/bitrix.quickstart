<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<style type="text/css">
#slider {
    width: <?=$arParams["INSECO_SLIDERWI"]?>px;
}
.scroll {
    height: <?=$arParams["INSECO_SCROLLHE"]?>px;
    width: <?=$arParams["INSECO_SCROLLWI"]?>px;
}
</style>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
<?endforeach;?>
<div id="slider">
 <ul class="navigation">
<?foreach($arResult["ITEMS"] as $arItem):?>
                <li><a href="#<?echo $arItem["ID"]?>"><?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
				<?echo $arItem["NAME"]?>
		<?endif;?></a></li>

            
<?endforeach;?>
</ul>
<div class="scroll">
                <div class="scrollContainer">
		<?foreach($arResult["ITEMS"] as $arItem):?>

                <div class="panel" id="<?echo $arItem["ID"];?>"><h2><?echo $arItem["NAME"]?></h2>
		<p><?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<?echo $arItem["PREVIEW_TEXT"];?>
		<?endif;?></p></div>
                    
<?endforeach;?>
</div>
            </div>
<div id="shade"></div></div>
