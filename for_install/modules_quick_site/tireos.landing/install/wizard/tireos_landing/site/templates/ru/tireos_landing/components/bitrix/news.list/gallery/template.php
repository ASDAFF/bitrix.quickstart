<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<? $item_num = 0;
$animIt = 14; 
foreach($arResult["ITEMS"] as $i=>$arItem):?>

<? $img_small = getResizedImgById($arItem["PROPERTIES"]["PICTURE_G"]["VALUE"],370,183); ?>

<? $id = 14+floor($i/3); if($id>16) $id = 16; ?>

			<div class="col-md-4 col-sm-6 animIt<?=$id?><?if($id==16):?> gItemHidden<?endif;?>">
 				<figure class='media-news'>
  					<? $img_id = $arItem["PROPERTIES"]["PICTURE_G"]["VALUE"];
					$img_src = CFile::GetPath($img_id);?>
                    
					<a href="<?php echo $img_src?>" class="group2 bwWrapper">
						<img src="<?php echo $img_small["src"]?>" alt="<?=$arItem["NAME"]?>" />
						<i class="zoom-icoBw"></i>
					</a>
				</figure>
			</div>                

           
          <?php /*?> <? if($item_num!==0):?>  <?php */?>
            
                                               

			<?php /*?><? endif?><?php */?>
<? $item_num+=3;
$animIt ++;
endforeach?>
<div class="spacer5"></div>   



