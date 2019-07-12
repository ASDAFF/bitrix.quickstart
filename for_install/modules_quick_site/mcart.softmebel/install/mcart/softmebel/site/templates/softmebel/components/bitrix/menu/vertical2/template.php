<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div>
<? $return=''; $last_item_key=(count($arResult)-1);
if (!empty($arResult)){	
	$return=''; $section='';
	$arResult2=array_reverse($arResult);
	foreach($arResult2 as $arItem){						
		if($arItem["PERMISSION"] > "D"){				
//			if($arItem["DEPTH_LEVEL"]==1){						
				if($arItem["SELECTED"] || $selected){ 		
					$selected=$arItem["DEPTH_LEVEL"];												
					$return='<div class="menu3"><a href="'.$arItem["LINK"].'" class="menu1activ">&sim;&nbsp;'.$arItem["TEXT"].
								'&nbsp;&sim;</a></div>'.$section.$return;
				}else{						
					$return='<div class="menu3"><a href="'.$arItem["LINK"].'">&nbsp;'.$arItem["TEXT"].
							'&nbsp;</a></div>'.$return;									
				}			
				$selected=false;	
				$section='';		
//			}else{				
//				//style="padding-left: '.(($arItem["DEPTH_LEVEL"]-1)*12).';"	
//				if($arItem["SELECTED"] || $selected>$arItem["DEPTH_LEVEL"]){
//					$selected=$arItem["DEPTH_LEVEL"];				
//					$section='<div style="margin-top: 17px;" class="menu3"><a href="'.$arItem["LINK"].'" class="menu1activ">&sim;&nbsp;'.
//																								$arItem["TEXT"].'</a></div>'.$section;						
//				}else{					
//					$section='<div style="margin-top: 17px;" class="menu3"><a href="'.$arItem["LINK"].'">&sim;&nbsp;'.
//																								$arItem["TEXT"].'</a></div>'.$section;									
//				}						
//			}		
	    }	    
	}			
} 
print $return; ?>
</div>