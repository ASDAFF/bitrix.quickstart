<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

foreach ($arResult['IBLOCKS'] as $iblock):
	if (count($iblock['ITEMS']) == 0) {
		continue;
	}
	?>
	<h1><?=$iblock['NAME']?></h1>
	<table cellpadding="0" cellspacing="10" border="0">
		<?foreach ($iblock['ITEMS'] as $item):?>
			<tr style="vertical-align: top;">
				<td>
					<?if(is_array($item['PREVIEW_PICTURE'])):?>
						<a href="<?=$item['DETAIL_PAGE_URL']?>">
							<img src="<?=$item['PREVIEW_PICTURE']['src']?>" border='0' alt="<?=$item['NAME']?>"/>
						</a>
					<?endif;?>
				</td>
				<td style="width: 90%;">
					<a href="<?=$item['DETAIL_PAGE_URL']?>"><?=$item['NAME']?></a>
					<div>
						<?=$item['PREVIEW_TEXT']?>
					</div>
					<?if($item['DATE_ACTIVE_FROM']):?>
						<small><?=$item['DATE_ACTIVE_FROM']?></small>
					<?endif;?>
				</td>
			</tr>
		<?endforeach;?>
	</table>
	<p><a href="http://<?=$_SERVER["HTTP_HOST"]?>/subscribtion/?unsubscribe=yes" target="_blank">Отписаться от рассылки</a></p>
<?endforeach?>