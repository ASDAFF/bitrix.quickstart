<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arResult['SLIDER_ID'] = md5(serialize($arParams));

$arParams['WIDTH'] = IntVal($arParams['WIDTH']) > 0 ? $arParams['WIDTH'] : 1024;
$arParams['HEIGHT'] = IntVal($arParams['HEIGHT']) > 0 ? $arParams['HEIGHT'] : 460;

$arParams['DELAY'] = IntVal($arParams['DELAY']) > 0 ? $arParams['DELAY'] : 5000;
$arParams['SPEED'] = IntVal($arParams['SPEED']) > 0 ? $arParams['SPEED'] : 350;

$arParams['HOVER_PAUSE'] = $arParams['HOVER_PAUSE'] != "N";
$arParams['SHOW_NEXT_PREV'] = $arParams['SHOW_NEXT_PREV'] == "Y";
$arParams['SHOW_PAGINATION'] = $arParams['SHOW_PAGINATION'] != "N";

$arSliderParams = Array(
	'play' => $arParams['DELAY'],
	'pause'=> $arParams['DELAY']/2,
	'hoverPause' => $arParams['HOVER_PAUSE'],
	'fadeSpeed' => $arParams['SPEED'],
	'generateNextPrev' => $arParams['SHOW_NEXT_PREV'],
	'generatePagination' => $arParams['SHOW_PAGINATION'],
	
	'container' => 'slides-container',
	'next' => 'slides-next',
	'prev' => 'slides-prev',
	'paginationClass' => 'slides-pagination' ,
);

if (count($arResult['ITEMS']) > 1)
{
	?>
	<script type="text/javascript">
	$(function() {
		$('#<?=CUtil::JSEscape($arResult['SLIDER_ID'])?>').slides(<?=CUtil::PhpToJSObject($arSliderParams)?>);
	}); 
	</script>
	<?
}

?>
<div class="slides" id="<?=$arResult['SLIDER_ID']?>"><div class="slides-container" style="height: <?=$arParams['HEIGHT']?>px;">
<?
	foreach ($arResult['ITEMS'] as $idx=>$arElement)
	{
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<div class="slide" id="<?=$this->GetEditAreaId($arElement['ID'])?>">
			<?
			if ($bHasLink = is_array($arElement['DISPLAY_PROPERTIES']['SLIDER_LINK']))
			{
				?><a href="<?$arElement['DISPLAY_PROPERTIES']['SLIDER_LINK']['VALUE']?>"><?
			}
					
			$arImg = CAllFile::ResizeImageGet($arElement['PREVIEW_PICTURE'], Array('width' => $arParams['WIDTH'], 'height' => $arParams['HEIGHT']), BX_RESIZE_IMAGE_EXACT, $bInitSizes = true);
			echo CFile::ShowImage($arImg['src'], $arImg['width'], $arImg['height'], 'alt="' . $arElement['NAME'] . '"');
			
			if ($bHasLink)
			{
				?></a><?
			}
			
			?>
		</div>
		<?
	}
?>
</div></div>