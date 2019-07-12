<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section-list">
<?foreach($arResult["SECTIONS"] as $arSection):?>
	<?
		$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
		$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="col col_1_2 box box_border box_white" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
		<div class="inner">			
			<div class="head">
				<h2><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?=$arSection["NAME"]?></a><?if($arParams["COUNT_ELEMENTS"]):?><span>(<?=$arSection["ELEMENT_CNT"]?>)</span><?endif;?></h2>
				<?=$arSection["DESCRIPTION"]?>
			</div>
			<div class="img"><?if(is_array($arSection["PICTURE"])):?><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><img src="<?=$arSection["PICTURE"]["SRC"]?>"></a><?endif?></div>			
		</div>
	</div>			
<?endforeach?>
</div>