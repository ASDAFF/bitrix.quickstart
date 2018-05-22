<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div class="persontypes clearfix"><?
	if(count($arResult['PERSON_TYPE'])>1)
	{
		?><span class="subtitle"><?=GetMessage('SOA_TEMPL_PERSON_TYPE')?></span><?
		foreach($arResult['PERSON_TYPE'] as $v)
		{
			?><div class="label left"><?
				?><input type="radio" id="PERSON_TYPE_<?=$v['ID']?>" name="PERSON_TYPE" value="<?=$v['ID']?>"<?if($v['CHECKED']=='Y') echo ' checked="checked"';?> <?
					?>onClick="submitForm()"> <?
					?><label for="PERSON_TYPE_<?=$v['ID']?>"><?=$v['NAME']?></label><?
			?></div><?
		}
		?><input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult['USER_VALS']['PERSON_TYPE_ID']?>" /><?
	} else {
		if(IntVal($arResult['USER_VALS']['PERSON_TYPE_ID'])>0)
		{
			//for IE 8, problems with input hidden after ajax
			?><span style="display:none;"><?
			?><input type="text" name="PERSON_TYPE" value="<?=IntVal($arResult['USER_VALS']['PERSON_TYPE_ID'])?>" /><?
			?><input type="text" name="PERSON_TYPE_OLD" value="<?=IntVal($arResult['USER_VALS']['PERSON_TYPE_ID'])?>" /><?
			?></span><?
		} else {
			foreach($arResult['PERSON_TYPE'] as $v)
			{
				?><input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?=$v["ID"]?>" /><?
				?><input type="hidden" name="PERSON_TYPE_OLD" value="<?=$v["ID"]?>" /><?
			}
		}
	}
?></div>