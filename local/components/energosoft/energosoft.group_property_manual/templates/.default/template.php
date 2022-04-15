<?
######################################################
# Name: energosoft.grouping                          #
# File: template.php                                 #
# (c) 2005-2012 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<table cellspacing="0" cellpadding="0" border="0">
<?foreach($arResult as $arElement):?>
	<?if(count($arElement["PROPERTIES"]) > 0 || $arParams["ES_SHOW_EMPTY"] == "Y"):?>
	<tr>
		<td><b><?=$arElement["NAME"]?></b></td>
		<td></td>
	</tr>
	<?foreach($arElement["PROPERTIES"] as $arProp):?>
		<?if($arProp["DISPLAY_VALUE"] != "" || $arParams["ES_SHOW_EMPTY_PROPERTY"] == "Y"):?>
		<tr>
			<td style="padding-left:10px;"><?=$arProp["NAME"]?></td>
			<td><?=$arProp["VALUE_ENUM"]==""?$arProp["DISPLAY_VALUE"]:$arProp["VALUE_ENUM"]?></td>
		</tr>
		<?endif;?>
	<?endforeach;?>
	<?endif;?>
<?endforeach;?>
</table>