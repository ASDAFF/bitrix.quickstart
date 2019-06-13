<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript">
	var sFolderPath = '<?= CUtil::JSescape($arParams['FOLDER_URL'])?>';
	var sMessDelConfirm = '<?= CUtil::JSescape(GetMessage('ASD_TPL_DEL_CONFIRM'))?>';
	var sTitleEditFolder = '<?= CUtil::JSescape(GetMessage('ASD_TPL_EDIT'))?>';
	var sTitleDeleteFolder = '<?= CUtil::JSescape(GetMessage('ASD_TPL_DEL'))?>';
	var sTitleStar = '<?= CUtil::JSescape(GetMessage('ASD_TPL_STAR'))?>';
	var sTitleStarAlready = '<?= CUtil::JSescape(GetMessage('ASD_TPL_STAR_ALREADY'))?>';
	var sSessId = '<?= bitrix_sessid()?>';
	var iMaxChars = <?= $s1=$arParams['MAX_CHARS']?>;
	var sType = '<?= $s2=$arParams['FAV_TYPE']?>';
	var sCharset = '<?= SITE_CHARSET?>';
	var sKey = '<?= md5($s1.$s2.Coption::GetOptionString('asd.favorite', 'js_key'))?>';
</script>

<div class="asd_fav_folders">
	<?foreach ($arResult['ITEMS'] as $arItem):?>
	<div class="asd_fav_folder" id="asd_f<?= $arItem['ID']?>">
		<?if ($arParams['ALLOW_EDIT'] == 'Y'):?>
		<div class="asd_fav_star<?if ($arItem['DEFAULT'] == 'Y'){?> asd_fav_star_act<?}?>" id="asd_s<?= $arItem['ID']?>" title="<?= GetMessage('ASD_TPL_STAR'.($arItem['DEFAULT']=='Y'?'_ALREADY':''))?>"></div>
		<div class="asd_fav_edit" id="asd_e<?= $arItem['ID']?>" title="<?= GetMessage('ASD_TPL_EDIT')?>"></div>
		<?endif;?>
		<a href="<?= str_replace('#ID#', $arItem['ID'], $arParams['FOLDER_URL'])?>"<?if ($arParams['FOLDER_ID']==$arItem['ID']){?> class="asd_fav_sel"<?}?> id="asd_a<?= $arItem['ID']?>"><?= $arItem['NAME']?></a> <b><?= intval($arResult['COUNTS'][$arItem['ID']])?></b>
		<div class="asd_fav_input" id="asd_i<?= $arItem['ID']?>">
			<input type="text" value="" maxlength="<?= $arParams['MAX_CHARS']?>" />
			<div class="asd_fav_del" id="asd_d<?= $arItem['ID']?>" title="<?= GetMessage('ASD_TPL_DEL')?>"></div>
		</div>
	</div>
	<?endforeach;?>
	<?if ($arParams['ALLOW_EDIT'] == 'Y'):?>
	<div class="asd_fav_folder" id="asd_f0">
		<div class="asd_fav_star" id="asd_s0" title="<?= GetMessage('ASD_TPL_STAR')?>"></div>
		<div class="asd_fav_edit" id="asd_e0" title="<?= GetMessage('ASD_TPL_NEW')?>"></div>
		<a href="#" id="asd_a0" class="asd_fav_new_link"><?= GetMessage('ASD_TPL_NEW_NAME')?></a> <b class="asd_count"></b>
		<div class="asd_fav_input" id="asd_i0">
			<input type="text" value="" maxlength="<?= $arParams['MAX_CHARS']?>" />
			<div class="asd_fav_del" id="asd_d0" title=""></div>
		</div>
	</div>
	<?endif;?>
</div>
<?if ($arParams['ALLOW_EDIT'] == 'Y'):?>
<div class="asd_fav_buttons">
	<div id="asd_save"><span><?= GetMessage('ASD_TPL_SAVE')?></span></div>
	<div id="asd_cancel"><span><?= GetMessage('ASD_TPL_CANCEL')?></span></div>
</div>
<?endif;?>