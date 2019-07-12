<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form action="<?=$arResult["FORM_ACTION"]?>">
<table cellspacing="0" cellpadding="0" class="search-form">
<tr> 
<td><input type="text" name="q" value="" class="inputsearch" maxlength="50" /></td>
<td><input name="s" type="image" src="<?=SITE_TEMPLATE_PATH?>/img/search.gif" alt="<?=GetMessage("BSF_T_SEARCH_BUTTON");?>" /></td>
</tr>
</table>
</form>
