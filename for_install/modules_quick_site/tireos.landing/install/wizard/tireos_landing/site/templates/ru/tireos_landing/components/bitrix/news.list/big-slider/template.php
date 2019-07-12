<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

	<ul class="form-bxslider xSlider list-unstyled">
<? $idIm = 1;
 foreach($arResult["ITEMS"] as $arItem):?>
	<li class="firstslide" style="background:url('<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>') no-repeat;" >
    	<div class="container relative body-slide">
			<div class="list-forstart fin_1">
             <? if ($arItem["PROPERTIES"]["HEADER"]["VALUE"]){?>
            <h2 class="h-Bold"><? echo $arItem["PROPERTIES"]["HEADER"]["VALUE"]?></h2>
            <?}?> 
            <div class="desc"><?=$arItem["PREVIEW_TEXT"];?></div>
           
   			 </div>
			 
             </div>
             	<? $img_id = $arItem["PROPERTIES"]["PICTURE_D"]["VALUE"];
	                $img_src = CFile::GetPath($img_id);?>
             <div class="img-slider slide-man<?=$idIm?> fin_2" style="background:url('<?php echo $img_src?>') no-repeat;"></div>
				</li>
    
 

<? $idIm++;
endforeach?>
	</ul>
    