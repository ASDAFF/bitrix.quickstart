<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CUtil::InitJSCore('core', 'ajax');
$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/components/bitrix/photogallery/templates/.default/script.js');
$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/components/bitrix/photogallery.interface/templates/.default/script.js');
$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/components/bitrix/search.tags.input/templates/.default/script.js');

if (!$this->__component->__parent || strpos($this->__component->__parent->__name, "photogallery") === false)
{
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/photogallery/templates/.default/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/photogallery/templates/.default/themes/gray/style.css');
}

$GLOBALS['APPLICATION']->AddHeadString('<link href="/bitrix/components/bitrix/search.tags.input/templates/.default/style.css" type="text/css" rel="stylesheet" />', true);

/*************************************************************************
	Processing of received parameters
*************************************************************************/
$arParams["WATERMARK"] = ($arParams["WATERMARK"] == "N" ? "N" : "Y");
$arParams["TEMPLATE"] = ($arParams["USE_LIGHT_TEMPLATE"] == "Y" ? "LIGHT-APPLET" : "APPLET");
$arParams["SHOW_WATERMARK"] = ($arParams["SHOW_WATERMARK"] == "N" ? "N" : "Y");
if ($arParams["USE_WATERMARK"] != "Y" || $arParams["WATERMARK"] != "Y")
	$arParams["SHOW_WATERMARK"] = "N";

// Don't show additional settings section if we don't have any additional settings
$arParams["SHOW_ADDITIONAL_SETTINGS"] = ($arParams["SHOW_WATERMARK"] == "Y" || $arParams["SHOW_PUBLIC"] == "Y" || $arParams["SHOW_RESIZER"] == "Y") ? "Y" : "N";

$arParams["JPEG_QUALITY1"] = intVal($arParams["JPEG_QUALITY1"]) > 0 ? intVal($arParams["JPEG_QUALITY1"]) : 80;
$arParams["JPEG_QUALITY2"] = intVal($arParams["JPEG_QUALITY2"]) > 0 ? intVal($arParams["JPEG_QUALITY2"]) : 90;
$arParams["JPEG_QUALITY"] = intVal($arParams["JPEG_QUALITY"]) > 0 ? intVal($arParams["JPEG_QUALITY"]) : 90;

/********************************************************************
	/Processing of received parameters
********************************************************************/

/********************************************************************
				Default values
********************************************************************/
$arWatermark = array();
$arWatermark["additional"] = isset($arParams["USER_SETTINGS"]["additional"]) && $arParams["USER_SETTINGS"]["additional"] == 'Y';

if ($arParams['WATERMARK_RULES'] == 'ALL')
{
	$arWatermark["use"] = 'Y';
	$arWatermark["type"] = strtolower($arParams["WATERMARK_TYPE"]);
	$arWatermark["copyright"] = 'N';
	$arWatermark["color"] = $arParams["WATERMARK_COLOR"];

	//if ($arWatermark["type"] == 'text')
	//	$arWatermark["size"] = (isset($arParams["USER_SETTINGS"]["size"]) && in_array($arParams["USER_SETTINGS"]["size"], array("big", "middle", "small"))) ? $arParams["USER_SETTINGS"]["size"] : 'middle';
	//else
	//	$arWatermark["size"] = (isset($arParams["USER_SETTINGS"]["size"]) && in_array($arParams["USER_SETTINGS"]["size"], array("real", "big", "middle", "small"))) ? $arParams["USER_SETTINGS"]["size"] : "real";
	$arWatermark["position"] = (isset($arParams["WATERMARK_POSITION"]) && in_array($arParams["WATERMARK_POSITION"], array("TopLeft", "TopCenter", "TopRight", "CenterLeft", "Center", "CenterRight", "BottomLeft", "BottomCenter", "BottomRight"))) ? $arParams["WATERMARK_POSITION"] : 'BottomRight';
	$arWatermark["opacity"] = isset($arParams["WATERMARK_TRANSPARENCY"]) ? intVal($arParams["WATERMARK_TRANSPARENCY"]) : 50;
	$arWatermark["text"] = $arParams["WATERMARK_TEXT"];

	$arWatermark["file"] = $arParams["WATERMARK_FILE_REL"];
	if ($arWatermark["file"])
	{
		$arWatermark["fileWidth"] = $arParams["WATERMARK_FILE_WIDTH"];
		$arWatermark["fileHeight"] = $arParams["WATERMARK_FILE_HEIGHT"];
	}

	//$arWatermark["original_size"] = isset($arParams["USER_SETTINGS"]["original_size"]) ? intVal($arParams["USER_SETTINGS"]["original_size"]) : 0;
}
else
{
	$arWatermark["use"] = (isset($arParams["USER_SETTINGS"]["use"]) && $arParams["USER_SETTINGS"]["use"] == "Y") ? "Y" : 'N';
	$arWatermark["type"] = (isset($arParams["USER_SETTINGS"]["type"]) && in_array($arParams["USER_SETTINGS"]["type"], array("text", "image"))) ? $arParams["USER_SETTINGS"]["type"] : 'text';
	$arWatermark["copyright"] = (isset($arParams["USER_SETTINGS"]["copyright"]) && $arParams["USER_SETTINGS"]["copyright"] == 'Y') ? 'Y' : 'N';
	$arWatermark["color"] = htmlspecialcharsbx(isset($arParams["USER_SETTINGS"]["color"]) ? $arParams["USER_SETTINGS"]["color"] : '#FF0000');
	if ($arWatermark["type"] == 'text')
		$arWatermark["size"] = (isset($arParams["USER_SETTINGS"]["size"]) && in_array($arParams["USER_SETTINGS"]["size"], array("big", "middle", "small"))) ? $arParams["USER_SETTINGS"]["size"] : 'middle';
	else
		$arWatermark["size"] = (isset($arParams["USER_SETTINGS"]["size"]) && in_array($arParams["USER_SETTINGS"]["size"], array("real", "big", "middle", "small"))) ? $arParams["USER_SETTINGS"]["size"] : "real";
	$arWatermark["position"] = (isset($arParams["USER_SETTINGS"]["position"]) && in_array($arParams["USER_SETTINGS"]["position"], array("TopLeft", "TopCenter", "TopRight", "CenterLeft", "Center", "CenterRight", "BottomLeft", "BottomCenter", "BottomRight"))) ? $arParams["USER_SETTINGS"]["position"] : 'BottomRight';
	$arWatermark["opacity"] = isset($arParams["USER_SETTINGS"]["opacity"]) ? intVal($arParams["USER_SETTINGS"]["opacity"]) : 50;
	$arWatermark["text"] = isset($arParams["USER_SETTINGS"]["text"]) ? $arParams["USER_SETTINGS"]["text"] : "";
	$arWatermark["text"] = htmlspecialcharsbx($arWatermark["text"]);

	$arWatermark["original_size"] = isset($arParams["USER_SETTINGS"]["original_size"]) ? intVal($arParams["USER_SETTINGS"]["original_size"]) : 0;
}

/********************************************************************
				/Default values
********************************************************************/
?>
<script>
	BXIU_MESS = {
		DefaultColor: '<?= GetMessage("P_DEF_COLOR")?>',
		TopLeft: '<?= GetMessage("P_WATERMARK_POSITION_TL")?>',
		TopCenter: '<?= GetMessage("P_WATERMARK_POSITION_TC")?>',
		TopRight: '<?= GetMessage("P_WATERMARK_POSITION_TR")?>',
		CenterLeft: '<?= GetMessage("P_WATERMARK_POSITION_ML")?>',
		Center: '<?= GetMessage("P_WATERMARK_POSITION_MC")?>',
		CenterRight: '<?= GetMessage("P_WATERMARK_POSITION_MR")?>',
		BottomLeft: '<?= GetMessage("P_WATERMARK_POSITION_BL")?>',
		BottomCenter: '<?= GetMessage("P_WATERMARK_POSITION_BC")?>',
		BottomRight: '<?= GetMessage("P_WATERMARK_POSITION_BR")?>',
		SizeReal: '<?= GetMessage("P_WATERMARK_SIZE_REAL")?>',
		SizeBig: '<?= GetMessage("P_WATERMARK_SIZE_BIG")?>',
		SizeMiddle: '<?= GetMessage("P_WATERMARK_SIZE_MIDDLE")?>',
		SizeSmall: '<?= GetMessage("P_WATERMARK_SIZE_SMALL")?>',
		Opacity: '<?= GetMessage("P_OPACITY")?>',
		PositionTitle: '<?=GetMessage("P_WATERMARK_POSITION_TITLE")?>',
		SizeTitle: '<?=GetMessage("P_WATERMARK_SIZE_TITLE")?>',
		CopyrightTitleOn: '<?= (GetMessage("P_WATERMARK_COPYRIGHT").": ".GetMessage("P_WATERMARK_COPYRIGHT_SHOW"))?>',
		CopyrightTitleOff: '<?= (GetMessage("P_WATERMARK_COPYRIGHT").": ".GetMessage("P_WATERMARK_COPYRIGHT_HIDE"))?>',
		DelEntry: "<?= GetMessage("P_DEL_PREVIEW")?>",
		DelEntryConfirm: "<?= GetMessage("P_DEL_PREVIEW_CONFIRM")?>",
		SourceFile: "<?= GetMessage("SourceFile")?>",
		Title: "<?=CUtil::JSEscape(GetMessage("Title"))?>",
		Tags: "<?=CUtil::JSEscape(GetMessage("Tags"))?>",
		Description: "<?=CUtil::JSEscape(GetMessage("Description"))?>",
		NoPhoto: "<?=CUtil::JSEscape(GetMessage("NoPhoto"))?>",
		Public: "<?=CUtil::JSEscape(GetMessage("Public"))?>",
		ErrorNoData: "<?=CUtil::JSEscape(GetMessage("ErrorNoData", array('#POST_MAX_SIZE#' => $arResult["UPLOAD_MAX_FILE_SIZE_MB"])))?>",
		LargeSizeWarn: "<?=CUtil::JSEscape(GetMessage("P_LARGE_SIZE_WARN"))?>",
		WrongTypeWarn: "<?=CUtil::JSEscape(GetMessage("P_NOT_IMAGE_TYPE_WARN"))?>",
		WrongServerResponse: "<?=CUtil::JSEscape(GetMessage("P_WRONG_SERVER_RESPONSE"))?>"
	};
</script>

<?if (!empty($arResult["ERROR_MESSAGE"])):?>
<div id="photo_error_<?=$arParams["UPLOADER_ID"]?>" class="photo-error">
	<?ShowError($arResult["ERROR_MESSAGE"]);?>
</div>
<?endif;?>

<div class="image-uploader-objects">

<?
if($arParams['SHOW_MAGIC_QUOTES_NOTICE_ADMIN'])
	echo GetMessage('MAGIC_QUOTES_NOTICE_ADMIN', array("#URL#" => '/bitrix/admin/site_checker.php')).'<br/><br/>';
elseif($arParams['SHOW_MAGIC_QUOTES_NOTICE'])
	echo GetMessage('MAGIC_QUOTES_NOTICE').'<br/><br/>';
?>

<?/* CONTROLS IN THE TOP OF UPLOADER*/?>
<form id="<?= $arParams["UPLOADER_ID"]?>_form" name="<?= $arParams["UPLOADER_ID"]?>_form" action="<?=  htmlspecialcharsbx($arParams["ACTION_URL"])?>" method="POST" enctype="multipart/form-data" class="bxiu-photo-form">
	<input type="hidden" name="save_upload" id="save_upload" value="Y" />
	<input type="hidden" name="sessid" id="sessid" value="<?= bitrix_sessid()?>" />
	<input type="hidden" name="FileCount" value="<?=$arParams["UPLOAD_MAX_FILE"]?>" />
	<input type="hidden" name="SECTION_ID" value="<?=$arParams["SECTION_ID"]?>" />

	<input type="hidden" name="photo_album_id" value="" />
	<input type="hidden" name="new_album_name" value="" />
	<input type="hidden" name="photo_resize_size" value="" />
	<input type="hidden" name="photo_watermark_use" value="" />
	<input type="hidden" name="photo_watermark_type" value="" />
	<input type="hidden" name="photo_watermark_text" value="" />
	<input type="hidden" name="photo_watermark_copyright" value="" />
	<input type="hidden" name="photo_watermark_color" value="" />
	<input type="hidden" name="photo_watermark_size" value="" />
	<input type="hidden" name="photo_watermark_opacity" value="" />
	<input type="hidden" name="photo_watermark_position" value="" />
	<input type="hidden" name="photo_watermark_path" value="" id="<?= $arParams["UPLOADER_ID"]?>_wmark_path"/>
	<input type="hidden" name="photo_public" value="" />
</form>

	<div id="bxiu_controls_cont<?=$arParams["UPLOADER_ID"]?>" class="bxiu-top-controls<?= ($arWatermark['additional'] == 'Y' ? ' bxiu-top-controls-add' : '')?>">
		<div class="bxiu-top-bar">
			<div class="bxiu-album-cont">
				<label for="photo_album_id<?=$arParams["UPLOADER_ID"]?>"><?=GetMessage("P_TO_ALBUM")?>:</label>
				<select id="photo_album_id<?=$arParams["UPLOADER_ID"]?>">
					<option value="new" <?=(intVal($arParams["SECTION_ID"]) == 0 ? "selected" : "")?>> - <?=GetMessage("P_IN_NEW_ALBUM")?> -</option>
				<?if (is_array($arResult["SECTION_LIST"])):?>
					<?foreach ($arResult["SECTION_LIST"] as $key => $val):?>
						<option value="<?=$key?>" <?=($arParams["SECTION_ID"] == $key ? "selected" : "")?>><?=$val?></option>
					<?endforeach;?>
				<?endif;?>
				</select>
				<input id="new_album_name<?=$arParams["UPLOADER_ID"]?>" type="text" value="<?= $arParams["NEW_ALBUM_NAME"]?>" style="display: none;"/>
			</div>
			<? if ($arParams['SHOW_ADDITIONAL_SETTINGS'] == "Y"): /* Additional section link*/?>
			<a class="bxiu-add-set-link" href="javascript:void(0);" id="show_add_params_link<?=$arParams["UPLOADER_ID"]?>"><?= GetMessage('P_ADDITIONAL_SETTINGS')?></a>
			<a class="bxiu-hide-add-set-link" href="javascript:void(0);" id="hide_add_params_link<?=$arParams["UPLOADER_ID"]?>"><?= GetMessage('P_ADDITIONAL_SETTINGS_HIDE')?></a>
			<?endif; /* END Additional section link*/?>

			<?// Show mode selector only if we have ImageUploader or Flash uploader in settings
			if ($arParams['UPLOADER_TYPE'] != 'form'):?>
				<?if($arParams["VIEW_MODE"] == "applet"):?>
					<a class="bxiu-mode-link" href="<?= $arParams["SIMPLE_FORM_URL"]?>"><?= GetMessage('P_SHOW_FORM')?></a>
				<?else:?>
					<a class="bxiu-mode-link" href="<?= $arParams["MULTIPLE_FORM_URL"]?>"><?= GetMessage('P_SHOW_APPLET')?></a>
				<?endif;?>
			<?endif;?>
		</div>
		<? if ($arParams['SHOW_ADDITIONAL_SETTINGS'] == "Y"): /* Additional section*/?>
		<div class="bxiu-add-params" id="add_params_cont<?=$arParams["UPLOADER_ID"]?>">
				<? // Left column controls block
				if($arParams["SHOW_RESIZER"] == 'Y' || $arParams["SHOW_PUBLIC"] == "Y"):?>
				<div id="bxiu_left_col_<?=$arParams["UPLOADER_ID"]?>" class="bxiu-left-column-controls">

				<? // Resize image on uploading block
				if($arParams["SHOW_RESIZER"] == 'Y'):?>
					<div class="bxiu-resize-cont">
						<label for="photo_resize_size"><?=GetMessage("P_RESIZE")?>:</label>
						<select id="bxiu_resize_<?=$arParams["UPLOADER_ID"]?>">
							<?if ($arParams["ORIGINAL_SIZE"] == 0):?>
								<option value="0" <?if(!$arWatermark["original_size"]):?> selected<?endif;?>><?=GetMessage("P_ORIGINAL")?></option>
							<?endif;?>
							<?foreach ($arParams['SIZES_SHOWN'] as $size):?>
								<option value="<?= $size[0]?>" <?if($arWatermark["original_size"] == $size[0]):?> selected<?endif;?>><?= $size[1]?></option>
							<?endforeach;?>
						</select>
					</div>
				<?endif; /* END Resize image on uploading block*/?>

				<? if ($arParams["SHOW_PUBLIC"] == "Y"):?>
					<div class="bxiu-user-public-cont">
						<input name="Public" id="bxiu_public_<?=$arParams["UPLOADER_ID"]?>" type="checkbox" value="Y" <?= ($arParams["PUBLIC_BY_DEFAULT"] == "Y" ? " checked='checked' " : "")?>/>
						<label for=""bxiu_public_<?=$arParams["UPLOADER_ID"]?>""><?=GetMessage("Public")?></label>
					</div>
				<?else:?>
						<input name="Public" type="hidden" value="Y" />
				<?endif;?>
				</div>
				<?endif; /* END left column block*/?>

				<?
				// Show separator
				if($arParams["SHOW_RESIZER"] == 'Y' && $arParams["SHOW_WATERMARK"] == "Y"):?>
					<div class="bxiu-vertical-separator" id="bxiu_separator_<?=$arParams["UPLOADER_ID"]?>"></div>
				<?endif;?>

				<? if ($arParams["SHOW_WATERMARK"] == "Y"):?>
					<div id="<?=$arParams["UPLOADER_ID"]?>_watermark_cont" class="bxiu-watermark-cont">
						<div class="bxiu-watermark-use-cont">
							<input type="checkbox" id="<?=$arParams["UPLOADER_ID"]?>_use_watermark" value="Y" <?= ($arWatermark["use"] == 'Y' ? 'checked' : '')?>/>
							<label for="<?=$arParams["UPLOADER_ID"]?>_use_watermark"><?=GetMessage("P_WATERMARK")?></label>
						</div>

						<div class="bxiu-watermark-type-cont">
							<input type="radio" id="<?=$arParams["UPLOADER_ID"]?>_wmark_type_text" name="wmark_type_radio"/> <label for="<?=$arParams["UPLOADER_ID"]?>_wmark_type_text"><?= GetMessage("P_WATERMARK_TEXT")?></label>
							<input type="radio" id="<?=$arParams["UPLOADER_ID"]?>_wmark_type_img" name="wmark_type_radio"/> <label for="<?=$arParams["UPLOADER_ID"]?>_wmark_type_img"><?= GetMessage("P_WATERMARK_IMG")?></label>
						</div>

						<div class="bxiu-watermark-types">
							<div class="bxiu-watermark-image">
								<div  id="<?=$arParams["UPLOADER_ID"]?>_wmark_preview_cont" class="bxiu-watermark-preview">
									<img class="bxiu-watermark-image-preview" id="watermark_img_preview<?=$arParams["UPLOADER_ID"]?>" src="/bitrix/images/1.gif"/>
									<div id="<?=$arParams["UPLOADER_ID"]?>_wmark_preview_del" class="bxiu-file-del" title="<?= GetMessage('P_DEL_PREVIEW')?>"></div>
								</div>
								<div id="bxiu_wm_img_iframe_cont<?=$arParams["UPLOADER_ID"]?>">
									<div class="bxiu-loading"></div>
									<form name="wm_form" id="bxiu_wm_form<?=$arParams["UPLOADER_ID"]?>" action="<?= htmlspecialcharsbx($arParams["ACTION_URL"])?>" method="POST" enctype="multipart/form-data" class="bxiu-photo-form">
										<input type="hidden" name="watermark_iframe" value="Y" />
										<input type="hidden" name="sessid" id="sessid" value="<?= bitrix_sessid()?>" />
										<input name="watermark_img" type="file" size="30" id="bxiu_wm_img<?=$arParams["UPLOADER_ID"]?>"/>
									</form>
									<div class="bxiu-watermark-image-but-cont" id="<?=$arParams["UPLOADER_ID"]?>_img_but_cont"></div>
								</div>
							</div>
							<div class="bxiu-watermark-text">
								<input type="text" id="<?=$arParams["UPLOADER_ID"]?>_wmark_text" value="<?=$arWatermark["text"]?>" size="25" class="bxiu-watermark-text-inp"/>
								<div class="bxiu-watermark-text-but-cont"  id="<?=$arParams["UPLOADER_ID"]?>_text_but_cont"></div>
							</div>
						</div>

					</div>
				<?endif; /* END Resize image on uploading block*/?>
		</div>
		<?endif; /* END Additional section*/?>
	</div>
</div>
<?
/* ************** Select uploader type ************** */
if ($arParams['UPLOADER_TYPE'] == 'applet' && $arParams["VIEW_MODE"] == "applet"): /* Show Image Uploader */?>
<?
CImageUploader::ShowScript(array(
	'id' => $arParams['UPLOADER_ID'],
	'layout' => $arParams['APPLET_LAYOUT'] == 'simple' ? 'OnePane' : 'ThreePanes',
	'folderViewMode' => 'Thumbnails',
	'uploadViewMode' => 'Tiles',
	'height' => $arParams["UPLOADER_HEIGHT"].'px',
	'folderPaneHeight' => round($arParams["UPLOADER_HEIGHT"] / 2),
	'thumbnailJpegQuality' => $arParams["JPEG_QUALITY"],
	'enableCrop' => true,
	'cropRatio' => '0.75',
	'cropMinSize' => '150',
	'fileMask' => '*.jpg;*.jpeg;*.png;*.gif;*.bmp',
	'actionUrl' => $arParams["ACTION_URL"],
	'redirectUrl' => $arParams["REDIRECT_URL"],
	'appendFormName' => $arParams["UPLOADER_ID"].'_form',
	'showAddFileButton' => $arParams['APPLET_LAYOUT'] == 'simple',
	'showAddFolderButton' => $arParams['APPLET_LAYOUT'] == 'simple',
	'filesPerPackage' => 2, //
	'converters' => $arParams['converters'],
	'maxFileSize' => $arResult["UPLOAD_MAX_FILE_SIZE"],
	'pathToTmp' => $arParams["PATH_TO_TMP"],
	'useWatermark' => true,
	'watermarkConfig' => array(
		'values' => array(
			'use' => $arWatermark['use'],
			'type' => $arWatermark['type'],
			'text' => $arWatermark['text'],
			'color' => $arWatermark['color'],
			'position' => $arWatermark['position'],
			'size' => $arWatermark['size'],
			'opacity' => $arWatermark['opacity'],
			'file' => $arWatermark['file']
		),

		'rules' => $arParams["WATERMARK_RULES"], // USER | ALL
		'type' => $arParams['WATERMARK_TYPE'], // BOTH | TEXT | PICTURE
		'text' => $arParams['WATERMARK_TEXT'],
		'color' => $arParams['WATERMARK_COLOR'],
		'position' => $arParams['WATERMARK_POSITION'],
		'size' => $arParams['WATERMARK_SIZE'],
		'opacity' => $arParams['WATERMARK_TRANSPARENCY'],
		'file' => $arParams['WATERMARK_FILE_REL'],
		'fileWidth' => $arWatermark["fileWidth"],
		'fileHeight' => $arWatermark["fileHeight"]
	)
));
?>

<? elseif($arParams['UPLOADER_TYPE'] == 'flash' && $arParams["VIEW_MODE"] == "applet"): /*Show Flash uploader*/?>

<?
CFlashUploader::ShowScript(array(
	'id' => $arParams['UPLOADER_ID'],
	'height' => $arParams["UPLOADER_HEIGHT"].'px',
	'fileMask' => "[['*.jpg; *.jpeg; *.png; *.gif; *.bmp', '*.jpg;*.jpeg;*.png;*.gif;*.bmp'], ['*.*','*.*']]",
	'actionUrl' => $arParams["ACTION_URL"],
	'redirectUrl' => $arParams["REDIRECT_URL"],
	'appendFormName' => $arParams["UPLOADER_ID"].'_form',
	'converters' => $arParams['converters'],
	'maxFileSize' => $arResult["UPLOAD_MAX_FILE_SIZE"],
	'pathToTmp' => $arParams["PATH_TO_TMP"],
	'thumbnailJpegQuality' => $arParams["JPEG_QUALITY"]
));
?>

<? else: /* Simple uploader in form*/?>
<div id="bxiu_simple_cont<?= $arParams['UPLOADER_ID']?>" class="bxiu-simple-cont">
	<div class="bxiu-field-upload">
		<input type="button" value="<?= GetMessage("AddFiles")?>" class="bxiu-add-files-but"/>
		<input type="file" name="photos[]" size="1"<?= (strpos($_SERVER["HTTP_USER_AGENT"], "Opera") === false ? " multiple=\"multiple\"" : "")?> id="bxiu_upload_inp<?= $arParams['UPLOADER_ID']?>" class="bxiu-fake-input" />
	</div>
	<div class="empty-clear"></div>
	<div class="bxiu-files-list" id="bxiu_files_list<?= $arParams['UPLOADER_ID']?>"></div>
	<div class="empty-clear"></div>
	<input type="button" id="bxiu_simple_go<?= $arParams['UPLOADER_ID']?>" value="<?= GetMessage("P_GO_TO_ALBUM")?>" style="display: none;">
</div>

<?endif; /* ************** END Select uploader type ************** */?>

<script>
BX.ready(function(){
	oBXIU<?= $arParams['UPLOADER_ID']?> = new window.BXImageUploader(
		{
			id: '<?= $arParams['UPLOADER_ID']?>',
			type: '<?= (($arParams["UPLOADER_TYPE"] == 'form' || $arParams["VIEW_MODE"] != 'applet') ? 'form' : 'applet')?>',
			typeEx: '<?= $arParams["UPLOADER_TYPE"]?>',
			showWatermark: <?= ($arParams["SHOW_WATERMARK"] == "Y" ? 'true' : 'false')?>,
			thumbnailSize: '<?= $arParams["THUMBNAIL_SIZE"]?>',
			showAdditionalSettings: <?= ($arParams["SHOW_ADDITIONAL_SETTINGS"] == "Y" ? 'true' : 'false')?>,
			opacityForText: <?= (($arParams["UPLOADER_TYPE"] == "applet" && $arParams["VIEW_MODE"] == "applet") ? 'true' : 'false')?>,
			uploadMaxFileSize: '<?= $arResult["UPLOAD_MAX_FILE_SIZE"]?>',
			initConfig: {
				add: <?= ($arWatermark['additional'] == 'Y' ? 'true' : 'false')?>,
				watermark: {
					use: <?= ($arWatermark['use'] == 'Y' ? 'true' : 'false')?>,
					type: '<?= $arWatermark['type']?>',
					text: '<?= $arWatermark['text']?>',
					copyright: <?= ($arWatermark['copyright'] == 'Y' ? 'true' : 'false')?>,
					color: '<?= $arWatermark['color']?>',
					position: '<?= $arWatermark['position']?>',
					size: '<?= $arWatermark['size']?>',
					opacity: '<?= $arWatermark['opacity']?>'
				}
			},
			actionUrl: '<?= CUtil::JSEscape($arParams["ACTION_URL"])?>',
			redirectUrl: '<?= CUtil::JSEscape($arParams["SUCCESS_URL"])?>',
			dropUrl: '<?= CUtil::JSEscape($arParams["DETAIL_DROP_URL"])?>'
		}
	);
	oBXIU<?= $arParams['UPLOADER_ID']?>.Init();
});
</script>

<? if ($arParams["ORIGINAL_SIZE"] && $arParams["SHOW_RESIZER"] == "N" || $arResult["UPLOAD_MAX_FILE_SIZE_MB"] && $arParams["ALLOW_UPLOAD_BIG_FILES"] != "Y" || $arParams["MODERATION"] == "Y"):?>
<div class="bxiu-notice">
<? if ($arParams["MODERATION"] == "Y"):?>
	<p><?= GetMessage("P_MODERATION_NITICE");?></p>
<?endif;?>
<? if ($arParams["ORIGINAL_SIZE"] && $arParams["SHOW_RESIZER"] == "N"):?>
	<p><?= GetMessage("P_MAX_FILE_DIMENTIONS_NOTICE", Array('#MAX_FILE_DIMENTIONS#' => intVal($arParams["ORIGINAL_SIZE"])));?></p>
<?endif;?>
<? if ($arResult["UPLOAD_MAX_FILE_SIZE_MB"] && $arParams["ALLOW_UPLOAD_BIG_FILES"] != "Y"):?>
	<p><?= GetMessage("P_MAX_FILE_SIZE_NOTICE", Array('#POST_MAX_SIZE_STR#' => $arResult["UPLOAD_MAX_FILE_SIZE_MB"]));?></p>
<?endif;?>
</div>
<?endif;?>

<noscript>
<style>
div.image-uploader-objects, div.photo-uploader-button, #ControlsAppletForm {display:none;}
</style>
</noscript>