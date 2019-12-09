<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><div class='arfiles'><?
	foreach($arResult['ITEMS'] as $arItem)
	{
		?><div class="files clearfix"><?
			?><h4 class="title"><?=$arItem['NAME']?></h4><?
			$index = 1;
			foreach($arItem['PROPERTIES'][$arParams['PROP_CODE_FILE']]['VALUE'] as $arFile)
			{
				?><a class="docs" href="<?=$arFile['FULL_PATH']?>"><?
					?><i class="icon pngicons <?=$arFile['TYPE']?>"></i><?
					?><span class="name"><?=$arFile['ORIGINAL_NAME']?></span><?
					if( isset($arFile['DESCRIPTION']) ) { ?><span class="description"><?=$arFile['DESCRIPTION']?></span><? }
					?><span class="size">(<?=$arFile['TYPE']?>, <?=$arFile['SIZE']?>)</span><?
				?></a><?
				if($index>3) { $index==0; }
				?><span class="separator x<?=$index?>"></span><?
				$index++;
			}
		?></div><?
	}
	
?></div>