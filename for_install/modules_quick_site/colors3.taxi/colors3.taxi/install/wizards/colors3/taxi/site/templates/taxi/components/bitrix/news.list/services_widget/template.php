<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (count($arResult["ITEMS"]) != 0):?>
<h2><?=GetMessage('ELEMENT_SERVICES')?></h2>
  <div class="media">

<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<ul id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"] && $arItem["PREVIEW_TEXT"] && $arItem["DETAIL_TEXT"]):?>
			<li>
				<a title="<?=$arItem["NAME"]?>" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
					<?echo $arItem["NAME"]?>
				</a>
			</li>
		<?elseif (($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"] && $arItem["PREVIEW_TEXT"]) or ($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"] &&$arItem["DETAIL_TEXT"])):?>
			<li>
				<?echo $arItem["NAME"]?>
			</li>
		<?endif;?>
	</ul>
<?endforeach;?>

 <a href="<?=SITE_DIR?>services/" class="see_all"><span><?echo GetMessage('ELEMENT_ALL_SERVICES');?></span></a>
</div>
<?endif;?>