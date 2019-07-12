<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$searchArray = array(
		"products" => "Товары",
		/*"brands" => "Производители"*/
);
//deb($arResult);

if (isset($_GET['q'])) {
	$searchValue = $_GET['q'];

} else {
	$searchValue = "";

}

?>
<form class="bs-docs-example form-inline" action="<?=$arResult["FORM_ACTION"]?>" method="get">
<?php /*?><input type="hidden" name="tags" value="<?=$arResult["REQUEST"]["TAGS"];?>" /> */ ?>
<input type="text" name="q" value="<?=$searchValue?>" size="15" class="searchb" />

<select class="searchspan" name="SEARCH_WHERE">
<?
foreach ($searchArray as $key => $value) {
	if (isset($_GET['SEARCH_WHERE']) && $_GET['SEARCH_WHERE'] == $key) $selected = 'selected = "selected"';
	else $selected = '';
	?><option <?=$selected?> value="<?=$key?>"><?=$value?></option><?
}
?>
</select>
<button class="btn" type="submit">Найти</button>
</form>

<div class="clear"></div>
<div class="sample">
<?
/* echo"<pre>";
print_r($arCloudParams);
echo"</pre>"; */
?>
<!--noindex-->Часто ищут:
	<a href="/search/?q=Diesel&SEARCH_WHERE=brands" rel="nofollow">Diesel</a> 	<a href="/search/?q=Versace&SEARCH_WHERE=brands" rel="nofollow">Versace</a> 	<a href="/search/?q=валенки&SEARCH_WHERE=products" rel="nofollow">валенки</a> 	<a href="/search/?q=ADAM JONES&SEARCH_WHERE=brands" rel="nofollow">ADAM JONES</a> <!--/noindex-->
<?
// $APPLICATION->IncludeComponent("bitrix:search.tags.cloud", "trendlist.ru.default", $arCloudParams, $component);?>
</div>