<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if (!empty($arResult)) {
	?><ul class="nav navbar-nav list-unstyled main-menu-nav" style="overflow: hidden;"><?
		?><li class="dropdown other invisible"><?
			?><a href="#">...</a><?
			?><ul class="dropdown-menu list-unstyled dropdown-menu-right"></ul><?
		?></li><?
		$previousLevel = 0;
		foreach ($arResult as $key => $arItem) {
			if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel) {
				echo str_repeat("</li></ul>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
			}
			if($arItem['DEPTH_LEVEL']==1) {
				?><li class="dropdown lvl1 <?if ($arItem['SELECTED']=='Y'):?>active<?endif;?>" id="element<?=$key?>"><?
					?><a href="<?=$arItem['LINK']?>" class="dropdown-toggle" data-toggle="dropdown"><?
						?><?=$arItem['TEXT']?><?
						if($arItem['IS_PARENT']==1) { ?><span class="hidden-md hidden-lg"><i></i></span><? }
					?></a><?
			} else {
				if($previousLevel==1) { ?><ul class="dropdown-menu list-unstyled"><? }
				if($arItem['IS_PARENT']==1) {
					?><li class="dropdown-submenu <?if ($arItem['SELECTED']=='Y'):?>active<?endif;?>"><?
						?><a href="<?=$arItem['LINK']?>"><?
							?><?=$arItem['TEXT']?><?
							if($arItem['IS_PARENT']==1) { ?><span class="hidden-md hidden-lg"><i></i></span><? }
						?></a><?
						?><ul class="dropdown-menu list-unstyled"><?
				} else {
					?><li class="<?if ($arItem['SELECTED']=='Y') {?>active<?}?>"><a href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a><?
				}
			}
			$previousLevel = $arItem["DEPTH_LEVEL"];
		}
		if ($previousLevel > 1) {
			echo str_repeat("</li></ul>", ($previousLevel-1) );
		}
	?></ul><?
}