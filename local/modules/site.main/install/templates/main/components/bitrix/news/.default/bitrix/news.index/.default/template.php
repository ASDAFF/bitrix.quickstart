<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<section class="news-index news-index-default">
	<?foreach ($arResult["IBLOCKS"] as $arIBlock) {
		if (count($arIBlock["ITEMS"])) {
			?>
			<article>
				<h2><?=$arIBlock["NAME"]?></h2>
				<ul>
					<?foreach ($arIBlock["ITEMS"] as $arItem) {
						?><li>
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
						</li><?
					}?>
				</ul>
			</article>
			<?
		}
	}?>
</section>