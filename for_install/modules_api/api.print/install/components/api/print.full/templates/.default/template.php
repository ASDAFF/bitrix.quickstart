<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=$arResult['SITE_NAME'];?></title>
	<? if(!empty($arResult['CSS_FILE_URL'])): ?>
		<link rel="stylesheet" href="<?=$arResult['CSS_FILE_URL'];?>" type="text/css" media="print, all"/>
	<? endif; ?>
</head>
<body>
<div id="print_wrapper">
	<?
	if(!empty($arResult['ERROR_MESSAGE']))
	{
		foreach($arResult['ERROR_MESSAGE'] as $v)
			ShowError($v);
	}
	else
	{
		?>
		<div id="print_panel">
			<button id="print_button" value="<?=GetMessage('GET_PRINT');?>" onclick="var wrap = document.getElementById('print_wrapper'); wrap.removeChild(document.getElementById('print_panel'));  window.print(); window.close();"><?=GetMessage('GET_PRINT');?></button>
		</div>
		<? if(!empty($arResult['TEXT_TEMPLATE'])):
		echo $arResult['TEXT_TEMPLATE'];
	endif; ?>
		<? if(!empty($arResult['ITEMS'])):?>
		<? foreach($arResult['ITEMS'] as $arItem):?>
			<?
			$borderWidth = 0;
			$borderClass = '';
			if($arItem['PREVIEW_PICTURE']['BORDER'] || $arItem['DETAIL_PICTURE']['BORDER'])
			{
				$borderWidth = 12;
				$borderClass = ' class="border"';
			}
			?>
			<div class="post-item">
				<h1><?=$arItem['NAME'];?></h1>
				<? if(!empty($arItem['DATE_ACTIVE_FROM'])):?>
					<h6><?=$arItem['DATE_ACTIVE_FROM']?></h6>
				<? endif; ?>
				<? if(!empty($arItem['PREVIEW_PICTURE']['SRC'])):?>
					<div class="preview-align-<?=$arItem['PREVIEW_PICTURE']['ALIGN'];?>">
						<img src="<?=$arItem['PREVIEW_PICTURE']['SRC'];?>"
							<?=$borderClass;?>
							  alt="<?=$arItem['NAME'];?>"
							  title="<?=$arItem['NAME'];?>"
							  width="<?=$arItem['PREVIEW_PICTURE']['WIDTH'];?>"
							  height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT'];?>"/>
						<? if(!empty($arItem['PREVIEW_PICTURE']['DESCRIPTION'])):?>
							<p style="width:<?=intval($arItem['PREVIEW_PICTURE']['WIDTH']) + $borderWidth;?>px;"><?=$arItem['PREVIEW_PICTURE']['DESCRIPTION'];?></p>
						<? endif; ?>
					</div>
				<? elseif(!empty($arItem['DETAIL_PICTURE']['SRC'])):?>
					<div class="preview-align-<?=$arItem['DETAIL_PICTURE']['ALIGN'];?>">
						<img src="<?=$arItem['DETAIL_PICTURE']['SRC'];?>"
							<?=$borderClass;?>
							  alt="<?=$arItem['NAME'];?>"
							  title="<?=$arItem['NAME'];?>"
							  width="<?=$arItem['DETAIL_PICTURE']['WIDTH'];?>"
							  height="<?=$arItem['DETAIL_PICTURE']['HEIGHT'];?>"/>
						<? if(!empty($arItem['DETAIL_PICTURE']['DESCRIPTION'])):?>
							<p style="width:<?=intval($arItem['DETAIL_PICTURE']['WIDTH']) + $borderWidth;?>px;"><?=$arItem['DETAIL_PICTURE']['DESCRIPTION'];?></p>
						<? endif; ?>
					</div>
				<? endif; ?>
				<? if(!empty($arItem['DISPLAY_PROPERTIES'])):?>
					<table class="table">
						<tbody>
						<? foreach($arItem['DISPLAY_PROPERTIES'] as $pid => $arProperty):?>
							<tr>
								<th><?=$arProperty['NAME'];?>:</th>
								<td>
									<? if(is_array($arProperty['DISPLAY_VALUE'])):
										echo implode("&nbsp;/&nbsp;", $arProperty['DISPLAY_VALUE']);
									else:
										echo $arProperty['DISPLAY_VALUE']; ?>
									<? endif ?> <?=$arProperty['DESCRIPTION'];?>
								</td>
							</tr>
						<? endforeach ?>
						</tbody>
					</table>
				<? endif; ?>
				<? if(!empty($arItem['PREVIEW_TEXT'])):?>
					<?=$arItem['PREVIEW_TEXT'];?>
				<? else:?>
					<?=$arItem['DETAIL_TEXT'];?>
				<? endif; ?>
			</div>
		<? endforeach; ?>
	<? endif; ?>
	<? } ?>
</div>
</body>
</html>