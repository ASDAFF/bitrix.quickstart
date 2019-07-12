<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$catName = GetMessage('MENU_HELP');

if (strpos($APPLICATION->GetCurDir(), '#SITE_DIR#news/')!==FALSE) {
	$catName = GetMessage('MENU_DEVELOPMENTS');
}
if (strpos($APPLICATION->GetCurDir(), '#SITE_DIR#about/')!==FALSE) {
	$catName = GetMessage('MENU_COMPANY');
}
if (strpos($APPLICATION->GetCurDir(), '#SITE_DIR#personal/')!==FALSE) {
	$catName = GetMessage('MENU_CABINET');
}
?>
<? if (!empty($arResult)) { ?>
	<div id="navhelp" class="sidebox">
		<h3 class="boxheader"><span class="sidenavswitcher" id="switcher_navhelp"><img width="9" height="9" class="sidenavswitcherimg noprint" title="Hide navigation" alt="-" src="#SITE_DIR#images/buttons/btn_expanded.gif"></span><?=$catName?>&nbsp;&nbsp;&nbsp;</h3>
		<div id="navhelpcontent"><ul class="sidenav">
			<? foreach($arResult as $key=>$arItem) { ?>
				<li><a title="<?=$arItem["TEXT"]?>" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
			<? } ?>
		</ul></div>
		<div class="boxfooter"></div>
	</div>
<? } ?>