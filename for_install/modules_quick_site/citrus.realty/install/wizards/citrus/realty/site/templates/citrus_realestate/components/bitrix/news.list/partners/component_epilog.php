<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->SetAdditionalCSS($templateFolder . '/colorbox/colorbox.css');?>

<?if (CheckVersion(SM_VERSION, '11.0.10')):
	$res = CJSCore::Init('jquery', true);
	if (strlen($res) > 0)
	{
		$APPLICATION->AddHeadString($res);
	}
	$APPLICATION->AddHeadScript("{$templateFolder}/colorbox/jquery.colorbox-min.js");
	$APPLICATION->AddHeadScript("{$templateFolder}/script2.js");?>
<?else:?>
	<script type="text/javascript">
		if (!window.jQuery) { 
			document.write(unescape('%3Cscript src="<?=$templateFolder?>/colorbox/jquery-1.7.1.min.js" type="text/javascript"%3E%3C/script%3E')); 
		}
		document.write(unescape('%3Cscript src="<?=$templateFolder?>/colorbox/jquery.colorbox-min.js" type="text/javascript"%3E%3C/script%3E')); 
	</script>
<?endif?>

<script type="text/javascript">
$(document).ready(function() {
	$(".b-news-list .b-news-list-item .colorbox").colorbox({
		maxWidth: <?=(intval($arParams['COLORBOX_MAXWIDTH']) <= 0 ? 800 : intval($arParams['COLORBOX_MAXWIDTH']))?>,
		maxHeight: <?=(intval($arParams['COLORBOX_MAXHEIGHT']) <= 0 ? 600 : intval($arParams['COLORBOX_MAXHEIGHT']))?>
	});
});
</script>
