<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<ul class="clearing-thumbs" data-clearing>
<?foreach($arResult["ROWS"] as $arItems):?>
	<?foreach($arItems as $arItem):?>
		<?if(is_array($arItem)):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BPS_ELEMENT_DELETE_CONFIRM')));
			?>
			<li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<a class="th radius" href="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>">
					<?if(is_array($arItem["PICTURE"])):?>
						<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" data-caption="<?=$arItem["NAME"]?>" />
					<?endif?>
				</a>
			</li>
		<?endif;?>
	<?endforeach?>
<?endforeach?>
</ul>

