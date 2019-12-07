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

if(0<$arResult["SECTIONS_COUNT"]):
	$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
	$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
	$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));	
?>
	<?$count = count($arResult['SECTIONS']);
	foreach($arResult['SECTIONS'] as &$arSection):
		$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
		$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
	?>
		<?if($cell%2 == 0):?>
		<div class="row">
		<?endif?>
			<div class="col-sm-6 col-md-6" id="<?=$this->GetEditAreaId($arSection['ID'])?>">
				<div class="list-with-image clearfix">
					<?if(!empty($arSection['PICTURE']['SRC'])):?>
					<div class="col-md-4">
						<div class="overlay-container">
							<a href="<?=$arSection['SECTION_PAGE_URL']?>"><img src="<?=$arSection['PICTURE']['SRC']?>" alt="<?=$arSection['PICTURE']['TITLE']?>"></a>
						</div>
						<div class="mb-15 hidden-md hidden-lg"></div>
					</div>
					<?endif?>
					<div class="<?if(!empty($arSection['PICTURE']['SRC'])):?>col-md-8<?else:?>col-md-12<?endif?>">
						<h2><a href="<?=$arSection['SECTION_PAGE_URL']?>"><?=$arSection['NAME']?></a></h2>
						<?if(!empty($arSection['DESCRIPTION'])):?><p class="small"><?=$arSection['DESCRIPTION']?></p><?endif?>
					</div>
				</div>
			</div>
		<?$cell++;
		if($cell%2 == 0 || $count == $cell):?>
		</div>
		<?endif?>
	<?endforeach?>
<?endif?>