<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="main-site-selector main-site-selector-default pull-right">
	<div class="btn-group btn-group-sm">
		<?foreach ($arResult['SITES'] as $site) {
			?>
			<a class="btn btn-default site-<?=$site['LID']?><?=$site['CURRENT'] == 'Y' ? ' active' : ''?>" <?=$site['CURRENT'] == 'Y' ? '' : ' href="' . $site['URL'] . '"'?>>
				<?=$site['NAME']?>
			</a>
			<?
		}?>
	</div>
</div>
