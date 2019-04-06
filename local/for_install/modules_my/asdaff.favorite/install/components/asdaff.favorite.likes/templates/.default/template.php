<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript">
	var sCurPage = '<?= CUtil::JSescape($APPLICATION->GetCurPageParam('a', array('a', 'sessid', 'move', 'moveto', 'del')))?>&'+'<?= bitrix_sessid_get()?>';
	var sMessConfirmDel = '<?= CUtil::JSescape(GetMessage('ASD_TPL_FAV_DEL_CONF'))?>';
</script>

<?if (!empty($arResult['CURRENT_FOLDER'])):?>
<h3><?= $arResult['CURRENT_FOLDER']['NAME']?></h3>
<?endif;?>

<?if (!empty($arResult['FAVS'])):?>
	<?
	$moveOptions = '';
	foreach ($arResult['FOLDERS'] as $ID => $arFolder)
	{
		if ($arFolder['ID'] != $arParams['FOLDER_ID'])
			$moveOptions .= '<option value="'.$ID.'">'.$arFolder['NAME'].'</option>'."\n";
	}

	?>
	<?foreach ($arResult['FAVS'] as $ID => $arItem):?>
	<?if (empty($arItem)) continue;?>
	<div class="asd_fav_item">
		<?if (strlen($arItem['PREVIEW_PICTURE_RESIZED']['src'])){?><a href="<?= $arItem['DETAIL_PAGE_URL']?>"><img src="<?= $arItem['PREVIEW_PICTURE_RESIZED']['src']?>" alt="" /></a><?}?>
		<a href="<?= $arItem['DETAIL_PAGE_URL']?>"><?= $arItem['NAME']?></a><br/>
		<?= $arItem['PREVIEW_TEXT']?>
		<div class="asd_clear"></div>
		<?if ($arResult['CAN_EDIT'] == 'Y'):?>
		<div class="asd_fav_menu">
			<?if ($arParams['ALLOW_MOVED'] == 'Y'):?>
			<a href="#" class="asd_fav_move" id="asd_fm_<?= $ID?>"><?= GetMessage('ASD_TPL_FAV_MOVE')?></a>
			<select id="asd_fs_<?= $ID?>">
				<option val="">...</option>
				<?= $moveOptions?>
			</select> |
			<?endif;?>
			<a href="#" class="asd_fav_delete" id="asd_fd_<?= $ID?>"><?= GetMessage('ASD_TPL_FAV_DEL')?></a>
		</div>
		<?endif;?>
	</div>
	<?endforeach;?>

	<?if (strlen($arResult['NAV_STRING']) > 0):?>
	<div class="asd_fav_pagen">
		<?= $arResult['NAV_STRING']?>
	</div>
	<?endif;?>

<?elseif ($arParams['FOLDER_ID'] > 0):?>
	<?= GetMessage('ASD_TPL_FAV_EMPTY')?>
<?else:?>
	<?= GetMessage('ASD_TPL_FAV_NOTHING')?>
<?endif;?>