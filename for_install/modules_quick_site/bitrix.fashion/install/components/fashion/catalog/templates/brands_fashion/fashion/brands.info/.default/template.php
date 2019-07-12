<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if($arResult['PICTURE']!=false||strlen($arResult['DETAIL_TEXT'])>0){
	echo '<div style="margin-bottom:30px">';
	if($arResult['PICTURE']!=false)
		echo '<div style="float:left"><img src="'.$arResult['PICTURE']['src'].'" width="'.$arResult['PICTURE']['width'].'" height="'.$arResult['PICTURE']['height'].'" /></div>';
	
	if(strlen($arResult['DETAIL_TEXT'])>0)
		echo '<div style="'.(isset($arResult['PICTURE']['width'])?'margin-left:'.($arResult['PICTURE']['width']+25).'px':'').'">'.$arResult['DETAIL_TEXT'].'</div>';
		
	echo '<div style="clear:both"></div>';
	echo '</div>';
}

if(count($arResult['SECTIONS'])>0){
	echo '<h2>'.GetMessage("SECTION_LIST").' '.$arResult['REALNAME'].'</h2>';
	echo '<table class="sections"><tr>';
	foreach($arResult['SECTIONS'] as $key => $section){
		if($section['DEPTH_LEVEL']==1&&$key!=0)
			echo $key%10==0?'</td></tr><tr><td>':'</td><td>';
		elseif($section['DEPTH_LEVEL']==1)
			echo '<td>';
			
		echo '<div class="level'.$section['DEPTH_LEVEL'].'"><a href="'.$section['SECTION_PAGE_URL'].'">'.$section['NAME'].' ('.$section['ELEMENT_CNT'].')</a></div>';
	}
	echo '</td></tr></table>';
}
?>