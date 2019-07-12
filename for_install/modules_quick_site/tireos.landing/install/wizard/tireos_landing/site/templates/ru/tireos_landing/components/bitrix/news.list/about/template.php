<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

			
<? $idIT = 5;
   $idITi = 6;
  $answers = null;
 $item_num = 0; 
 foreach($arResult["ITEMS"] as $arItem):?>
		<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
 <div class="container make-row"<? if($item_num==0):?> data-anchor="information"<? endif?> >
 <div class="row">
 <? if($item_num%2==0):?>
 <div class="col-sm-6 media-wr" id="animIt<?=$idIT?>">
 
      	<? $img_id = $arItem["PROPERTIES"]["IMAGE_VIDEO"]["VALUE"];
	       //$img_src = CFile::GetPath($img_id);
		   $img_data = getResizedImgById($img_id, 517, 345);
		   $img_src = $img_data["src"]; ?>

	<figure class='media-news'>

     <? if($arItem["PROPERTIES"]["LINKS_VIDEO"]["VALUE"]): ?>

      		<a class="group1" title="This is title text" href="<? echo $arItem["PROPERTIES"]["LINKS_VIDEO"]["VALUE"]?>"><img src="<?php echo $img_src?>" alt="<?=$arItem["NAME"]?>" />
      		<i class="zoom-ico"></i></a>

     <? elseif($arItem["PROPERTIES"]["IMAGE_VIDEO"]["VALUE"]): ?>
     	<a href="<?php echo $img_src?>" class="group3" title='This is title text'><img src="<?php echo $img_src?>" alt="<?=$arItem["NAME"]?>" >
		<i class="zoom-icoBw"></i></a>
   <? endif; ?>

     
      </figure>
      </div>
      <? endif?>
      
    <div class="col-sm-6" id="animIt<?=$idITi?>" >
    	<? if ($arItem["PROPERTIES"]["HEADER_BLOCK"]["VALUE"]){?>
    	<h2 class='xh-Bold'><? echo $arItem["PROPERTIES"]["HEADER_BLOCK"]["VALUE"]?></h2>
        <? } ?>
        <div class="excerpt"><?=$arItem["PREVIEW_TEXT"]?></div>
				<? if ($arItem["PROPERTIES"]["PRICE"]["VALUE"]){?>
             	<? $price_id = $arItem["PROPERTIES"]["PRICE"]["VALUE"];
	       $price_src = CFile::GetPath($price_id);?>
        					
        					<a href="<?=$price_src?>" class='more'>
						<div class="inside">
							<div class="backside"> <? echo $arItem["PROPERTIES"]["PRICE"]["NAME"]?> </div>
							<div class="frontside"> <? echo $arItem["PROPERTIES"]["PRICE"]["NAME"]?> </div>
						</div>
                       
					</a> 
					<? } ?>
                    </div>
                    <? if($item_num%2==1):?>
 <div class="col-sm-6 media-wr" id="animIt<?=$idIT?>"> <? /* <?=$idIT-=1?> */ ?>
 
      	<? $img_id = $arItem["PROPERTIES"]["IMAGE_VIDEO"]["VALUE"];
	       $img_src = CFile::GetPath($img_id);?>

	<figure class='media-news'>

     <? if($arItem["PROPERTIES"]["LINKS_VIDEO"]["VALUE"]): ?>

      		<a class="group1" title="This is title text" href="<? echo $arItem["PROPERTIES"]["LINKS_VIDEO"]["VALUE"]?>"><img src="<?php echo $img_src?>" alt="<?=$arItem["NAME"]?>" />
      		<i class="zoom-ico"></i></a>

     <? elseif($arItem["PROPERTIES"]["IMAGE_VIDEO"]["VALUE"]): ?>
     	<a href="<?php echo $img_src?>" class="group3" title='This is title text'><img src="<?php echo $img_src?>" alt="<?=$arItem["NAME"]?>" >
		<i class="zoom-icoBw"></i></a>
   <? endif; ?>

     
      </figure>
      </div>
      <? endif?></div>
        <div class="spacer2"></div>
     
</div>
     
<? $idIT+=2;
$idITi +=2;
$item_num++;
endforeach?>