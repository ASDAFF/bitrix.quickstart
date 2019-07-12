<?if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)die();

//echo"<pre>";print_r($arResult['CTEMPLATE']);echo"</pre>";

?><div class="catalogsorter" id="composite_sorter"<?if(isset($arParams['AJAXPAGESID']) && $arParams['AJAXPAGESID']!=''):?> data-ajaxpagesid="<?=$arParams['AJAXPAGESID']?>"<?endif;?>><?
	$frame = $this->createFrame('composite_sorter', false)->begin();
	$frame->setBrowserStorage(true);
	if($arParams['ALFA_CHOSE_TEMPLATES_SHOW']=='Y')
	{
		?><div class="template clearfix"><?
			foreach($arResult['CTEMPLATE'] as $template)
			{
				?><a<?if($template['USING']=='Y'):?> class="selected"<?endif;?> href="<?=$template['URL']?>" data-fvalue="<?=CUtil::JSEscape($template['VALUE'])?>" title="<?=($template['NAME_LANG']!=''?$template['NAME_LANG']:$template['VALUE'])?>"><i class="<?=$template['VALUE']?> icon pngicons"></i><span><?=($template['NAME_LANG']!="" ? $template['NAME_LANG'] : $template['VALUE'])?></span></a><?
			}
		?></div><?
	}
	?><div class="sortaou clearfix"><?
		if($arParams['ALFA_SORT_BY_SHOW']=='Y')
		{
			?><div class="<?if($arParams['ALFA_SHORT_SORTER']=='Y'):?>shortsort<?else:?>sort<?endif;?> clearfix"><?
				?><span class="cool"><?
					?><div class="title"><?=GetMessage('MSG_SORT')?></div><?
					if($arParams['ALFA_SHORT_SORTER']=='Y')
					{
						$arrUsed = array();
						foreach($arResult['CSORTING'] as $sort)
						{
							if('' == $sort['GROUP'] || in_array($sort['GROUP'],$arrUsed) || $sort['VALUE'] == $arResult['USING']['CSORTING']['ARRAY']['VALUE']){
								continue;
							}
							?><span class="drop clearfix"></span><?
							if($arResult['USING']['CSORTING']['ARRAY']['GROUP'] == $sort['GROUP']){
								?><a class="selected" href="<?=$sort['URL']?>"><span class="nowrap"><?=getMessage('CSORTING_'.$arResult['USING']['CSORTING']['ARRAY']['GROUP']); ?><i class="<? echo $arResult['USING']['CSORTING']['ARRAY']['DIRECTION']; ?> icon pngicons"></i></span></a><?
							}
							else{
								?><a href="<?=$sort['URL']?>"><span class="nowrap"><?=getMessage('CSORTING_'.$sort['GROUP']); ?><i class="<? echo $sort['DIRECTION']; ?> icon pngicons"></i></span></a><?	
							}
							$arrUsed[] = $sort['GROUP'];
						}
					}
					else{
						?><div class="dropdown"><?
							?><a class="select" href="#"><span class="nowrap"><?=getMessage('CSORTING_'.$arResult['USING']['CSORTING']['ARRAY']['GROUP']);?><i class="<? echo $arResult['USING']['CSORTING']['ARRAY']['DIRECTION']; ?> icon pngicons"></i></span></a><?
							?><div class="dropdownin"><?
								foreach($arResult['CSORTING'] as $sort){
									?><a<?if($sort['USING']=='Y'):?> class="selected"<?endif;?> href="<?=$sort['URL']?>"><span class="nowrap"><?=getMessage('CSORTING_'.$sort['GROUP'])?><i class="<? echo $sort['DIRECTION']; ?> icon pngicons"></i></span></a><?
								}
							?></div><?
						?></div><?
					}
				?></span><?
			?></div><?
		}
		if($arParams['ALFA_OUTPUT_OF_SHOW']=='Y' && $arParams['ALFA_CHOSE_TEMPLATES_SHOW']!='Y')
		{
			?><div class="output clearfix"><?
				?><span class="cool"><?
					?><div class="title"><?=GetMessage('MSG_OUTPUT')?></div><?
					?><div class="dropdown"><?
						?><a class="select" href="#"><?=$arResult['USING']['COUTPUT']['ARRAY']['VALUE']?><i class="icon pngicons"></i></a><?
						?><div class="dropdownin"><?
							foreach($arResult['COUTPUT'] as $output)
							{
								?><a<?if($output['USING']=='Y'):?> class="selected"<?endif;?> href="<?=$output['URL']?>"><?=($output['NAME_LANG']!=''?$output['NAME_LANG']:$output['VALUE'])?><i class="icon pngicons"></i></a><?
							}
						?></div><?
					?></div><?
				?></span><?
			?></div><?
		}
	?></div><?
	?><div class="clear"></div><?
	$frame->end();
?></div>