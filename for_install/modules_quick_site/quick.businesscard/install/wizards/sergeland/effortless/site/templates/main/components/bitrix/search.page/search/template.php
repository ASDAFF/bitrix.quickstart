<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>
<form action="" method="get" class="mb-40">
	<input type="hidden" name="how" value="<?=$arResult["REQUEST"]["HOW"]=="d"? "d": "r"?>" />
	<div class="form-group nospace">
		<div class="input-group">
			<input type="text" name="q" value="<?=$arResult["REQUEST"]["~QUERY"]?>" placeholder="<?=GetMessage("SEARCH_PlACEHOLDER")?>" class="form-control">
			<span class="input-group-btn"><button class="btn btn-default" type="submit"><?=GetMessage("SEARCH_GO")?></button></span>
		</div>
	</div>
</form>
<?if($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false):?>
<?elseif($arResult["ERROR_CODE"]!=0):?>
	<p><?=GetMessage("SEARCH_ERROR")?></p>
	<?ShowError($arResult["ERROR_TEXT"]);?>
	<p><?=GetMessage("SEARCH_CORRECT_AND_CONTINUE")?></p><br>
	<p><?=GetMessage("SEARCH_SINTAX")?><br /><b><?=GetMessage("SEARCH_LOGIC")?></b></p>
	<table border="0" cellpadding="5">
		<tr>
			<td align="center" valign="top"><?=GetMessage("SEARCH_OPERATOR")?></td><td valign="top"><?=GetMessage("SEARCH_SYNONIM")?></td>
			<td><?=GetMessage("SEARCH_DESCRIPTION")?></td>
		</tr>
		<tr>
			<td align="center" valign="top"><?=GetMessage("SEARCH_AND")?></td><td valign="top">and, &amp;, +</td>
			<td><?=GetMessage("SEARCH_AND_ALT")?></td>
		</tr>
		<tr>
			<td align="center" valign="top"><?=GetMessage("SEARCH_OR")?></td><td valign="top">or, |</td>
			<td><?=GetMessage("SEARCH_OR_ALT")?></td>
		</tr>
		<tr>
			<td align="center" valign="top"><?=GetMessage("SEARCH_NOT")?></td><td valign="top">not, ~</td>
			<td><?=GetMessage("SEARCH_NOT_ALT")?></td>
		</tr>
		<tr>
			<td align="center" valign="top">( )</td>
			<td valign="top">&nbsp;</td>
			<td><?=GetMessage("SEARCH_BRACKETS_ALT")?></td>
		</tr>
	</table>
<?elseif(count($arResult["SEARCH"])>0):?>
	<?foreach($arResult["SEARCH"] as $arItem):?>
	<div class="search-result">
		<h3><a href="<?=$arItem["URL"]?>"><?=$arItem["TITLE_FORMATED"]?></a></h3>
		<p><?=$arItem["BODY_FORMATED"]?></p>
		<div class="search-info"><span><?=GetMessage("SEARCH_MODIFIED")?></span> - <span><?=$arItem["DATE_CHANGE"]?></span><?if($arItem["CHAIN_PATH"]):?> - <span><?=$arItem["CHAIN_PATH"]?></span><?endif;?></div>
		<hr>
	</div>		
	<?endforeach;?>	
	<?if($arParams["DISPLAY_BOTTOM_PAGER"] != "N"):?>
		<?=$arResult["NAV_STRING"]?>
	<?endif;?>
<?else:?>
	<?ShowNote(GetMessage("SEARCH_NOTHING_TO_FOUND"));?>
<?endif;?>