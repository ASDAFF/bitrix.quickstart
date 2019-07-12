<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

if(!empty($arResult['ITEMS'])):
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));
?>
<script>jQuery(function(){$("#comments-quantity").html(<?=($arResult["NAV_RESULT"]->NavRecordCount > $arParams["PAGE_ELEMENT_COUNT"] ? $arParams["PAGE_ELEMENT_COUNT"] : $arResult["NAV_RESULT"]->NavRecordCount)?>)})</script>
<div class="comments margin-clear space-top">	
<?foreach($arResult['ITEMS'] as $key => $arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);			
?>
	<div class="comment clearfix">
		<div class="comment-content clearfix" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
			<h3><?=$arItem["NAME"]?></h3>
			<div class="comment-meta">
			<?for($i=1; $i<6; $i++):?>
				<?if($arItem["PROPERTIES"]["STARS"]["VALUE"] < $i):?>
					<i class="fa fa-star"></i>
				<?else:?>
					<i class="fa fa-star text-default"></i>
				<?endif?>
			<?endfor?> | <?=$arItem["DISPLAY_ACTIVE_FROM"]?></div>
			<div class="comment-body">
				<p><?=$arItem["PREVIEW_TEXT"]?></p>
			</div>
		</div>
	</div>
<?endforeach?>
</div>
<?endif?>