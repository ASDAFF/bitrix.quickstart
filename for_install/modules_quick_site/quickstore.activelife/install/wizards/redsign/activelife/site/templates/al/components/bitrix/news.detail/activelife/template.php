<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

?>
<div class="news-detail clearfix">
    <?php if ($arParams['DISPLAY_PICTURE']!='N' && is_array($arResult['DETAIL_PICTURE']) && $arResult['DETAIL_PICTURE']['SRC']!=''): ?>
		<img class="detail_picture" src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" alt="<?=$arResult['DETAIL_PICTURE']['ALT']?>"  title="<?=$arResult['DETAIL_PICTURE']['TITLE']?>">
	<?php elseif ($arParams['DISPLAY_PICTURE']!='N' && is_array($arResult['PREVIEW_PICTURE']) && $arResult['PREVIEW_PICTURE']['SRC'] != ''): ?>
		<img class="detail_picture" src="<?=$arResult['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arResult['PREVIEW_PICTURE']['ALT']?>"  title="<?=$arResult['PREVIEW_PICTURE']['TITLE']?>">
	<?php endif; ?>
    
    <?php
	echo $arResult['DETAIL_TEXT'];
	foreach($arResult['DISPLAY_PROPERTIES'] as $pid=>$arProperty){
		echo $arProperty['NAME'];?>:&nbsp;<?
		if(is_array($arProperty['DISPLAY_VALUE'])){
			echo implode('&nbsp;/&nbsp;', $arProperty['DISPLAY_VALUE']);
		}
		else{
			echo $arProperty['DISPLAY_VALUE'];
		}
		echo '<br>';
	}
    ?>
</div>