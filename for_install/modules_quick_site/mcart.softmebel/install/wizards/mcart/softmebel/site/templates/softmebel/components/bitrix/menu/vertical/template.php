<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/*
 * css *
a.menu1activ, a.menu1activ:hover{	
	color: #4889B9; 
    text-decoration: underline;
	cursor:pointer;
}
.menu1 *, .menu2 * {
	line-height: 1.5;
	white-space: nowrap;
}
.menu1 *{	
	font-size: 12px;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    border-bottom: 1px dashed #336699;
    color: #336699;
}
.menu2 *{font-size: 12px ;} 
  ?>
<div style="margin-top: 20px;">
<? $return=''; $last_item_key=(count($arResult)-1);
if (!empty($arResult)){	
	$return=''; $section='';
	$arResult2=array_reverse($arResult);
	foreach($arResult2 as $arItem){						
		if($arItem["PERMISSION"] > "D"){	
						
			$ar_result=CIBlockSection::GetList(Array("SORT"=>"ASC"), 
						Array("IBLOCK_ID" => $arItem["PARAMS"]["IBLOCK_ID"], "ID" => $arItem["PARAMS"]["SECTION_ID"]), false, Array("UF_*"));
			$this_section=$ar_result->GetNext();	
			if($this_section["UF_NOT_IN_MENU"]){ continue; }	
						
			if($arItem["DEPTH_LEVEL"]==1){						
				if($arItem["SELECTED"] || $selected){ 		
					$selected=$arItem["DEPTH_LEVEL"];												
					$return='<div class="menu1"><a href="'.$arItem["LINK"].'" class="menu1activ">'.$arItem["TEXT"].
								'</a></div>'.$section.$return;
				}else{						
					$return='<div class="menu1"><a href="'.$arItem["LINK"].'">'.$arItem["TEXT"].
							'</a></div>'.$return;									
				}			
				$selected=false;	
				$section='';		
			}else{								
				if($arItem["SELECTED"] || $selected>$arItem["DEPTH_LEVEL"]){
					$selected=$arItem["DEPTH_LEVEL"];				
					$section='<div class="menu2" style="margin-left: '.(($arItem["DEPTH_LEVEL"]-1)*12).';"><a href="'.
											$arItem["LINK"].'" class="menu1activ">'.$arItem["TEXT"].'</a></div>'.$section;						
				}else{					
					$section='<div class="menu2" style="margin-left: '.(($arItem["DEPTH_LEVEL"]-1)*12).';"><a href="'.
																$arItem["LINK"].'">'.$arItem["TEXT"].'</a></div>'.$section;									
				}						
			}		
	    }	    
	}			
} 
print $return; ?>
</div>

<? */
 $return=''; $last_item_key=(count($arResult)-1);
if (!empty($arResult)){	?>
<ul class="side_list"><?php 
	$return=''; $section='';
	$arResult2=array_reverse($arResult);	
	foreach($arResult2 as $k => $arItem){						
		if($arItem["PERMISSION"] > "D"){	
						
			$ar_result=CIBlockSection::GetList(Array("SORT"=>"ASC"), 
						Array("IBLOCK_ID" => $arItem["PARAMS"]["IBLOCK_ID"], "ID" => $arItem["PARAMS"]["SECTION_ID"]), false, Array("UF_*"));
			$this_section=$ar_result->GetNext();	
			if($this_section["UF_NOT_IN_MENU"]){ continue; }	
			
			$style = $arItem['SELECTED'] ? 'class="active"': '' ;
			$style2 = $k==$last_item_key ? 'class="last"': '' ;
			echo '<li '.$style2.'><a '.$style.' href="'.$arItem["LINK"].'">'.$arItem["TEXT"].'</a></li>';
			
	    }	    
	}	?>
</ul> <?php 		
} 
print $return; ?>

 