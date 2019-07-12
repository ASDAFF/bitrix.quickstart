<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<? if (!empty($arResult["ITEMS"])): ?>


<div id="lists">
	<div>
		<h2 class="red"><a href="<?= SITE_DIR; ?>about/news/"><?= GetMessage('CT_BNL_NEWS_AND_ACTIONS'); ?></a><sup></sup></h2>


<ul>
    <?foreach($arResult["ITEMS"] as $arItem):?>
    <?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<span class="date"><?= $arItem["DISPLAY_ACTIVE_FROM"]?></span>
		<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a>
	</li>
	<?endforeach;?>
</ul>

	</div>

</div>

<? endif; ?>