<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/*
if (!empty($arResult)){				
	print '<table border=0 id="menu"><tr>';	
	foreach($arResult as $k=>$arItem){
		if($k==0){
			print '<td class="first-child"><ul><li><a href="'.$arItem["LINK"].'" >'.$arItem["TEXT"].'</a></li>';
			continue;
		}
		if( $k & 1 ){ //нечетное
			print '<li><a href="'.$arItem["LINK"].'" >'.$arItem["TEXT"].'</a></li></ul></td>';
		}else{
			print '<td><ul><li><a href="'.$arItem["LINK"].'" >'.$arItem["TEXT"].'</a></li>';
		}	
	}
	if(!($k & 1)){ //четное
		print '<li style="background: none;">&nbsp;</li></ul></td>';
	}
	print '</tr></table>';		
} */ 

if (is_array($arResult) && count($arResult)>0){
	
	$arMenu=array();
	$key=-1;
	foreach($arResult as $k => $arItem){
		if($arItem['DEPTH_LEVEL']==1){
			$key++;
			$arMenu[$key]=$arItem;
		}else{
			$arMenu[$key]['CHILDREN'][]=$arItem;
		}
	}	?>
	<table cellspacing="0" cellpadding="0" width="100%" height="82" border="0">
	<tr><td><div class="top_menu" ><div class="top_menu_wrap" ><ul><?php 
	foreach($arMenu as $k => $arItem){
		$style = $arItem['SELECTED'] ? 'class="active"': '' ;		
		echo '<li><a '.$style.' href="'.$arItem["LINK"].'">'.$arItem["TEXT"].'</a>';				
		if (is_array($arItem['CHILDREN']) && count($arItem['CHILDREN'])>0){
			echo '<ul>';
			foreach($arItem['CHILDREN'] as $k2 => $arItem2){
				$style = $arItem2['SELECTED'] ? 'class="active"': '' ;		
				echo '<li><a '.$style.' href="'.$arItem2["LINK"].'">'.$arItem2["TEXT"].'</a></li>';			
			}
			echo '</ul>';
		}		
		echo '</li>';
	}	?>                  
	</ul></div></div></td></tr></table><?php 		
} ?>