<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

?><table class="info"><?
	
	?><tr><?
		?><td class="first"><?=GetMessage('USER_ID')?></td><?
		?><td class="first"><?=$arResult["ID"]?></td><?
	?></tr><?
	
	?><tr><?
		?><td><?=GetMessage('LOGIN')?></td><?
		?><td><?=$arResult["arUser"]["LOGIN"]?></td><?
	?></tr><?
	
	?><tr><?
		?><td><?=GetMessage('NAME')?></td><?
		?><td><?=$arResult["arUser"]["NAME"]?></td><?
	?></tr><?
	
	?><tr><?
		?><td><?=GetMessage('LAST_NAME')?></td><?
		?><td><?=$arResult["arUser"]["LAST_NAME"]?></td><?
	?></tr><?
	
	?><tr><?
		?><td><?=GetMessage('SECOND_NAME')?></td><?
		?><td><?=$arResult["arUser"]["SECOND_NAME"]?></td><?
	?></tr><?
	
	?><tr><?
		?><td><?=GetMessage('EMAIL')?></td><?
		?><td class="info_email"><?=$arResult["arUser"]["EMAIL"]?></td><?
	?></tr><?
	
?></table>