<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<table class="catalogSections" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;border-bottom:none;">
<?
$i=0;
$count=0;
$NUM_COLS = 4;
$NUM_IN_COLS=ceil(count($arResult["SECTIONS"])/$NUM_COLS);
$CURRENT_DEPTH=$arResult["SECTION"]["DEPTH_LEVEL"]+1;
foreach($arResult["SECTIONS"] as $arSection):
	$i++;
	$count++;
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT"));
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CATALOG_SECTION_DELETE_CONFIRM')));	
	
	$bHasPicture = is_array($arSection['PICTURE_PREVIEW']);
	$bHasChildren = is_array($arSection['CHILDREN']) && count($arSection['CHILDREN']) > 0;
	?>
	
	<?if ($i==1) {?>
	<tr>	
	<?}?>
		<td align="center" style="width: <?=100/$NUM_COLS?>%;border: 1px solid #CCCCCC;">
			<div class="catalog-section<?=$bHasPicture ? '' : ' no-picture-mode'?>" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
	
				<?if ($bHasPicture):?>
				<div style="height: 150px; margin: 5px 25px 0 25px;" class="catalog-section-image"><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><img src="<?=$arSection['PICTURE_PREVIEW']['SRC']?>" width="<?=$arSection['PICTURE_PREVIEW']['WIDTH']?>" height="<?=$arSection['PICTURE_PREVIEW']['HEIGHT']?>" /></a></div>
				<?endif;?>
	
				<div class="catalog-section-info" style="margin: 0 25px 0 25px;">
				<?if ($arSection['NAME'] && $arResult['SECTION']['ID'] != $arSection['ID']):?>
					<a href="<?=$arSection["SECTION_PAGE_URL"]?>"><h2><?=$arSection["NAME"]?></h2></a>
				<?endif;?>
				<?if ($arSection['DESCRIPTION']):?>
					<div class="catalog-section-desc"><?=$arSection['DESCRIPTION_TYPE'] == 'text' ? $arSection['DESCRIPTION'] : $arSection['~DESCRIPTION']?></div>
				<?endif;?>
	
				</div>
	
			</div>
		</td>		
	<?
	if ($NUM_COLS==$i) {
		$i=0;
		?>
		</tr>
	<?}?>	
<?endforeach;?>
<?if($i<$NUM_COLS && $i!=0):?>
	<?while($i<$NUM_COLS):?>
		<td style="border:none;">
		</td>
	<?$i++;
	endwhile?>
	</tr>
<?endif?>
</table>