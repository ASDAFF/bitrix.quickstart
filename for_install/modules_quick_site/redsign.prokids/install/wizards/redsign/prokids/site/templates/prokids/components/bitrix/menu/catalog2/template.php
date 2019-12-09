<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if(is_array($arResult) && count($arResult)>0){
	?><div class="catalogmenu2_column"><?
		?><ul class="catalogmenu2 clearfix"><?
			?><li class="parent"><a href="<?=$arParams['CATALOG_PATH']?>" class="parent"><?=GetMessage('RSGOPRO_CATALOG')?><i class="menu icon<?if($arParams['IS_MAIN']=='Y'):?> show<?endif;?>"></i></a><?
			?><ul class="first clearfix lvl1<?if($arParams['IS_MAIN']=='Y'):?> show<?endif;?>"><?
			$previousLevel = 0;
			$index = 1;
			$max = $arParams['RSGOPRO_MAX_ITEM'];
			$last_lvl1 = false;
			foreach($arResult as $arItem){
				if($previousLevel>1 && $arItem['DEPTH_LEVEL']==1){
					echo '</div></ul></li><!-- the end -->';
				} 
				if ( $previousLevel>1 && $arItem['DEPTH_LEVEL']==2 ){
					echo '</div>';
				}
				if($arItem['DEPTH_LEVEL']>3) {
					continue;
				}
				if($arItem['DEPTH_LEVEL'] == 1){
					$last_lvl1 = $arItem['ITEM_INDEX'];
				}
				if($arItem['IS_PARENT']){
					if($arItem['DEPTH_LEVEL'] == 1){
						?><li class="first<?if($index>$max):?> more<?endif;?><?if($arItem['IS_LAST_LVL1']=='Y'):?> lastchild<?endif;?>"><a href="<?=$arItem['LINK']?>" class="first<?if($arItem['SELECTED']):?> selected<?endif?>" title="<?=$arItem['TEXT']?>"><?=$arItem['TEXT']?><i class="menu icon pngicons"></i></a><?
							?><ul <?
								?>class="lvl<?if($arItem['DEPTH_LEVEL']>3):?>4<?else:?><?=($arItem['DEPTH_LEVEL']+1)?><?endif;?> <?
								?>lvl2-count-<?if($arItem['LVL2_COUNT']>3):?>4<?else:?><?=($arItem['LVL2_COUNT'])?><?endif;?>"><?
						$index++;
					} else {
						if($arItem['DEPTH_LEVEL']==2){
							?><div class="mrow"><?
						}
							?><a href="<?=$arItem['LINK']?>" class="sub<?if($arItem['DEPTH_LEVEL']>2):?>-sub<?endif;?>" title="<?=$arItem['TEXT']?>"><span><?=$arItem['TEXT']?></span></a><?
					}
				} else {
					if($arItem['DEPTH_LEVEL']==1){
						?><li class="first<?if($index>$max):?> more<?endif;?><?if($arItem['IS_LAST_LVL1']=='Y'):?> lastchild<?endif;?>"><a href="<?=$arItem['LINK']?>" class="first<?if($arItem['SELECTED']):?> selected<?endif?>" title="<?=$arItem['TEXT']?>"><?=$arItem['TEXT']?></a></li><?
						$index++;
					} else {
						if($arItem['DEPTH_LEVEL']==2){
							?><div class="mrow"><?
						}
						?><a href="<?=$arItem['LINK']?>" class="sub<?if($arItem['DEPTH_LEVEL']>2):?>-sub<?endif;?>" title="<?=$arItem['TEXT']?>"><span><?=$arItem['TEXT']?></span></a><?
					}
				}
				$previousLevel = $arItem['DEPTH_LEVEL'];
			}
			if($previousLevel>1){
				echo '</div></ul></li>';
			}
			if($index>($max+1)){
				?><li class="first morelink lastchild"><a href="<?=$arParams['CATALOG_PATH']?>" class="first morelink"><?=GetMessage('RSGOPRO_MORE')?><i class="icon pngicons"></i></a></li><?
			}
			?></ul></li><?
		?></ul><?
		
		?><ul class="catalogmenusmall clearfix"><?
			?><li class="parent"><a href="<?=$arParams['CATALOG_PATH']?>" class="parent"><?=GetMessage('RSGOPRO_CATALOG')?><i class="menu icon"></i></a><?
			?><ul class="first clearfix lvl1 noned"><?
				foreach($arResult as $arItem){
					if($arItem['DEPTH_LEVEL'] == 1){
						?><li class="first<?if($arItem['IS_LAST_LVL1']=='Y'):?> lastchild<?endif;?>"><a href="<?=$arItem['LINK']?>" class="first<?if($arItem['SELECTED']):?> selected<?endif?>"><?=$arItem['TEXT']?></a></li><?
					}
				}
			?></ul><?
		?></ul><?
	?></div><?
}