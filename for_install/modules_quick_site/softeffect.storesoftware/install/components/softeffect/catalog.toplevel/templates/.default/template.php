<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="navbrands" class="noprint"> 
	<ul>
		<? $i=1; foreach($arResult['SECTIONS'] as $value) { ?>
			<li<? if ($arResult['SECTION_SELECTED']==$value[2]) { ?> class="activetab"<? } ?>><a href="<?=$value['1']?>"><?=$value['0']?></a></li>
			<? if($i>=$arResult['MAX_COUNT']) { ?>
				<div class="allb"><a href="<?=$arResult['BRANDS_URL']?>"><?=GetMessage('SE_TOPLEVEL_ALLBRANDS')?> &raquo;</a></div>
				<?
				break;
			}
			$i++;
		} ?>
	</ul>
</div>