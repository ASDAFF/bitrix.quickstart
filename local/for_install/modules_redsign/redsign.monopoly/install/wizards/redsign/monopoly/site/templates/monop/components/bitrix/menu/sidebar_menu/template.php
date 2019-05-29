<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$this->SetViewTarget('sidebar_menu');
if (!empty($arResult)) {
	?><ul class="nav-sidebar nav nav-list"><?
		$previousLevel = 0;
		$index = 0;
		foreach ($arResult as $key => $arItem) {
			if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel) {
				echo str_repeat("</li></ul>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
			}
			if ($arItem['IS_PARENT']==1) {
				?><li class="dropdown-submenu<?if($arItem['SELECTED']=='Y'):?> active showed<?endif;?>"><?
					?><a href="<?=$arItem['LINK']?>"><i<?if($arItem['SELECTED']!='Y'):?> class="collapsed"<?endif;?> href="#collapse<?=$index?>" data-toggle="collapse"></i><?=$arItem['TEXT']?></a><?
					?><ul class="lvl2 collapse<?if($arItem['SELECTED']=='Y'):?> in<?endif;?>" id="collapse<?=$index?>"><?
			} else {
				?><li class="<?if ($arItem['SELECTED']=='Y'):?>active<?endif;?>"><a href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a><?
			}
			$previousLevel = $arItem["DEPTH_LEVEL"];
			$index++;
		}
		if ($previousLevel > 1) {
			echo str_repeat("</li></ul>", ($previousLevel-1) );
		}
	?></ul><?
}

$this->EndViewTarget();

if (!empty($arResult)) {
	?><ul class="nav-sidebar nav nav-list"><?
		$previousLevel = 0;
		$index = 0;
		foreach ($arResult as $key => $arItem) {
			if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel) {
				echo str_repeat("</li></ul>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
			}
			if ($arItem['IS_PARENT']==1) {
				?><li class="dropdown-submenu<?if($arItem['SELECTED']=='Y'):?> active showed<?endif;?>"><?
					?><a href="<?=$arItem['LINK']?>"><i<?if($arItem['SELECTED']!='Y'):?> class="collapsed"<?endif;?> href="#collapse<?=$index?>_pc" data-toggle="collapse"></i><?=$arItem['TEXT']?></a><?
					?><ul class="lvl2 collapse<?if($arItem['SELECTED']=='Y'):?> in<?endif;?>" id="collapse<?=$index?>_pc"><?
			} else {
				?><li class="<?if ($arItem['SELECTED']=='Y'):?>active<?endif;?>"><a href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a><?
			}
			$previousLevel = $arItem["DEPTH_LEVEL"];
			$index++;
		}
		if ($previousLevel > 1) {
			echo str_repeat("</li></ul>", ($previousLevel-1) );
		}
	?></ul><?
}
