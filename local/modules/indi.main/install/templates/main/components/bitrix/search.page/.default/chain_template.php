<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

ob_start();
?>
<ul class="list-inline">
	<?
	$itemNum = 0;
	foreach ($arCHAIN as $item) {
		if (strlen($item['LINK']) < strlen(SITE_DIR)) {
			continue;
		}
		if ($itemNum > 0) {
			?><li class="separator">/</li><?
		}
		if ($item['LINK']) {
			?><li><a href="<?=$item['LINK']?>"><?=htmlspecialcharsex($item['TITLE'])?></a></li><?
		} else {
			?><li><a><?=htmlspecialcharsex($item['TITLE'])?></a></li><?
		}
		$itemNum++;
	}
	?>
</ul>
<?
$html = ob_get_contents();
ob_end_clean();

return $html;