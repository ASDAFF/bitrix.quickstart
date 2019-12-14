<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(strlen($arResult['ERROR_MESSAGE'])>0)
{
	ShowError($arResult['ERROR_MESSAGE']);
}
if(strlen($arResult['NAV_STRING'])>0)
{
	?><p><?=$arResult['NAV_STRING']?></p><?
}

?><div class="profillist"><?
	if( is_array($arResult['PROFILES']) && count($arResult['PROFILES'])>0 )
	{
		foreach($arResult['PROFILES'] as $val)
		{
			?><a class="item" href="<?=$val['URL_TO_DETAIL']?>"><?
				?><div class="clearfix"><?
					?><div class="namedate"><?
						?><span class="name"><?=$val['NAME']?></span><?
					?></div><?
				?></div><?
				?><div class="data"><?
					?><?=GetMessage('P_DATE_UPDATE')?>: <?=$val['DATE_UPDATE']?><br /><?
					?><?=GetMessage('P_PERSON_TYPE')?>: <?=$val['PERSON_TYPE']["NAME"]?><?
				?></div><?
				?><div class="action"><?
					?><span class="edit" title="<?=GetMessage('SALE_DETAIL_DESCR')?>" onclick="javascript:window.location='<?=$val['URL_TO_DETAIL']?>';"><i class="icon pngicons"></i><?=GetMessage('SALE_DETAIL')?></span> &nbsp;&nbsp;&nbsp; <?
					?><span class="delete" title="<?=GetMessage('SALE_DELETE_DESCR')?>" onclick="javascript:if(confirm('<?=GetMessage('STPPL_DELETE_CONFIRM')?>')) window.location='<?=$val['URL_TO_DETELE']?>';return false;"><i class="icon pngicons"></i><?=GetMessage('SALE_DELETE')?></span><?
				?></div><?
			?></a><?
		}
	} else {
		ShowError( GetMessage('NO_PROFILES') );
	}
	
?></div><?

if(strlen($arResult['NAV_STRING'])>0)
{
	?><p><?=$arResult['NAV_STRING']?></p><?
}