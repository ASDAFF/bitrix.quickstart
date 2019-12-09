<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if(!empty($arResult))
{
	?><div class="catmenu<?if($arParams['ELLIPSIS_NAMES']=='Y'):?> ellipsisnames<?endif;?> clearfix"><?
		?><div class="title"><?=$arParams['BLOCK_TITLE']?></div><?
		$PREVIOUS_DEPTH_LEVEL = 1;
		$lvl1_count = 0;
		$lvl2_count = 0;
		foreach($arResult as $key => $arMenu)
		{
			$CURRENT_DEPTH_LEVEL = $arMenu['DEPTH_LEVEL'];
			
			if($lvl1_count==$arParams['LVL1_COUNT'] && $CURRENT_DEPTH_LEVEL==1)
				break;
			
			if($CURRENT_DEPTH_LEVEL==1)
				$lvl2_count = 0;
			
			if( ($lvl2_count>=$arParams['LVL2_COUNT'] && $arParams['LVL2_COUNT']>0) || ($CURRENT_DEPTH_LEVEL>1 && $arParams['LVL2_COUNT']<1) ) {
				continue;
			}
			if($CURRENT_DEPTH_LEVEL==1) {
				if($key>0) {
					?></div><?
				}
				?><div class="aroundlevel"><?
				$lvl1_count++;
			} else {
				$lvl2_count++;
			}

			?><div class="item depth_level<?=$CURRENT_DEPTH_LEVEL?>"><?
				?><a href="<?=$arMenu['LINK']?>" title="<?=$arMenu['TEXT']?>"><span><?=$arMenu['TEXT']?></span></a><?
			?></div><?
			
			$PREVIOUS_DEPTH_LEVEL = $CURRENT_DEPTH_LEVEL;
		}
		?></div><?
	?></div><?
}