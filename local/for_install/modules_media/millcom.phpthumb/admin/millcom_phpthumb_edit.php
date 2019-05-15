<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

CModule::IncludeModule("millcom.phpthumb");

$EDIT_ID = false;
if (isset($_REQUEST['ID']) && is_numeric($_REQUEST['ID']))
	$EDIT_ID = $DB->ForSql($_REQUEST['ID']);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$menu = array(
	array(
		"TEXT" => GetMessage("MILLCOM_PHPTHUMB_TEMPLATES_LIST"),
		"ICON" => "btn_list",
		"LINK" => "/bitrix/admin/millcom_phpthumb_list.php?" . bitrix_sessid_get() . "&lang=" . LANG,
	)
);

$top_menu = new CAdminContextMenu($menu);
$top_menu->Show();


if (isset($_REQUEST['MESS'])) {
	switch($_REQUEST['MESS']) {
		case 'ADD':
			CAdminMessage::ShowNote(GetMessage("MILLCOM_PHPTHUMB_NOTE_ADD"));
			break;
		case 'EDIT':
			CAdminMessage::ShowNote(GetMessage("MILLCOM_PHPTHUMB_NOTE_EDIT"));
			break;
	}
}

if (isset($_REQUEST["apply"]) || isset($_REQUEST["save"])) {
	if ($_REQUEST['NAME']) {
		$arOptions = unserialize($_REQUEST['OPTIONS_SERIALIZE']); 
		if (!empty($_REQUEST['OPTIONS'])) {
			foreach ($_REQUEST['OPTIONS'] as $key => $value) {
				if ($value && $value != '-')
					$arOptions[$key] = $value;
				else
					unset($arOptions[$key]);
			}
		}
		if ($EDIT_ID) {
			CMillcomPhpThumbTemplates::Update($EDIT_ID, array(
				'NAME' => $_REQUEST['NAME'],
				'OPTIONS' => serialize($arOptions)
			));
			$MESS = 'EDIT';
		} else {
			$EDIT_ID = CMillcomPhpThumbTemplates::Add(array(
				'NAME' => $_REQUEST['NAME'],
				'OPTIONS' => serialize($arOptions)
			));
			$MESS = 'ADD';
		}
		if(isset($_REQUEST["save"])) {
			LocalRedirect("/bitrix/admin/millcom_phpthumb_list.php?" . bitrix_sessid_get() . "&lang=" . LANG . '&MESS=' . $MESS);
		} else {
			LocalRedirect("/bitrix/admin/millcom_phpthumb_edit.php?" . bitrix_sessid_get() . "&lang=" . LANG . "&ID=" . $EDIT_ID . '&MESS=' . $MESS);
		}

	} else {
		CAdminMessage::ShowMessage(array(
			"MESSAGE" => GetMessage("MILLCOM_PHPTHUMB_ERROR"),
			"DETAILS" => GetMessage("MILLCOM_PHPTHUMB_ERROR_FIELD", array('#FIELD#' => GetMessage("MILLCOM_PHPTHUMB_FIELD_NAME"))),
			"HTML" => false,
			"TYPE" => "ERROR"
		)); 
	}
}



if ($EDIT_ID) {
	$res = CMillcomPhpThumbTemplates::GetByID($EDIT_ID);
	$editRow = $res->Fetch();
	$APPLICATION->SetTitle(GetMessage("MILLCOM_PHPTHUMB_TITLE_EDIT"));
} else {
	$APPLICATION->SetTitle(GetMessage("MILLCOM_PHPTHUMB_TITLE_ADD"));
	$editRow = array(
		'ID' => '',
		'NAME' => '',
		'OPTIONS' => ''
	);
}


if (isset($_REQUEST['CLEAR_CACHE']) && $EDIT_ID) {
	$filePath = $_SERVER['DOCUMENT_ROOT'].'/upload/phpthumb/'.$EDIT_ID.'/';
	$dir = opendir($filePath);
	while($file = readdir($dir)){
		if($file == '.' || $file == '..'){
			continue;
		}
		unlink($filePath.$file);
	}
}



$tabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MILLCOM_PHPTHUMB_TAB1"), "TITLE" => GetMessage("MILLCOM_PHPTHUMB_TAB1_TITLE")),
	array("DIV" => "edit2", "TAB" => GetMessage("MILLCOM_PHPTHUMB_TAB2"), "TITLE" => GetMessage("MILLCOM_PHPTHUMB_TAB2_TITLE")),
	array("DIV" => "edit3", "TAB" => GetMessage("MILLCOM_PHPTHUMB_TAB3"), "TITLE" => GetMessage("MILLCOM_PHPTHUMB_TAB3_TITLE")),
);
$tabs_control = new CAdminTabControl("tabs_control", $tabs);


$settingRows = unserialize($editRow['OPTIONS']);

?>
	<style>
		.img-demo {
			background-color: #fff;
			background-image: -webkit-linear-gradient(45deg,#efefef 25%,transparent 25%,transparent 75%,#efefef 75%,#efefef),-webkit-linear-gradient(45deg,#efefef 25%,transparent 25%,transparent 75%,#efefef 75%,#efefef);
			background-position: 0 0,10px 10px;
			-webkit-background-size: 21px 21px;
			background-size: 21px 21px;
		}
	</style>
	<form method="post" action="<?=$APPLICATION->GetCurPage()?>?ID=<?=$EDIT_ID?>&lang=<?=LANGUAGE_ID?>" enctype="multipart/form-data">
		<?php
		print bitrix_sessid_post();
		$tabs_control->Begin();
		$tabs_control->BeginNextTab();
		?>
		<tr>
			<td width="40%"><span class="adm-required-field"><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_NAME");?>:</span> </td>
			<td>
				<input type="text" name="NAME" value="<?=$editRow['NAME']?>">
			</td>
		</tr>
		<tr>
			<td><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_OPTIONS");?>: </td>
			<td>
				<textarea name="OPTIONS_SERIALIZE" style="width: 90%" rows="1"><?=$editRow['OPTIONS']?></textarea>
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_SIZE");?></td>
		</tr>
		<tr>
			<td><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_QUALITY");?>: </td>
			<td>
				<input type="text" name="OPTIONS[q]" value="<?=$settingRows['q']?>">
			</td>
		</tr>
		<tr>
			<td>
				<span id="hint_BG"></span><script type="text/javascript">BX.hint_replace(BX('hint_BG'), '<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_BACKGROUND_DESCR");?>');</script>
				<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_BACKGROUND");?>:
			</td>
			<td>
				<input type="text" name="OPTIONS[bg]" value="<?=$settingRows['bg']?>">
			</td>
		</tr>
		<tr>
			<td><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_FORMAT");?>: </td>
			<td>
				<select name="OPTIONS[f]">
					<option value="-"><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_SELECT");?></option>
					<option value="jpg" <?=($settingRows['f'] == 'jpg' ? 'selected="selected"': '');?>>JPG</option>
					<option value="png" <?=($settingRows['f'] == 'png' ? 'selected="selected"': '');?>>PNG</option>
					<option value="gif" <?=($settingRows['f'] == 'gif' ? 'selected="selected"': '');?>>GIF</option>
				</select>
			</td>
		</tr>
		
		<tr class="heading">
			<td colspan="2"><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_SIZE");?></td>
		</tr>
		<tr>
			<td><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_WIDHT");?>: </td>
			<td>
				<input type="text" name="OPTIONS[w]" value="<?=$settingRows['w']?>">
			</td>
		</tr>
		<tr>
			<td><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_HEIGHT");?>: </td>
			<td>
				<input type="text" name="OPTIONS[h]" value="<?=$settingRows['h']?>">
			</td>
		</tr>
		<tr>
			<td><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_ZC");?>: </td>
			<td>
				<select name="OPTIONS[zc]">
					<option value="-"><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_SELECT");?></option>
					<option value="C" <?=($settingRows['zc'] == 'C' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_C");?></option>
					<option value="T" <?=($settingRows['zc'] == 'T' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_T");?></option>
					<option value="R" <?=($settingRows['zc'] == 'R' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_R");?></option>
					<option value="B" <?=($settingRows['zc'] == 'B' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_B");?></option>
					<option value="L" <?=($settingRows['zc'] == 'L' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_L");?></option>
					<option value="TR" <?=($settingRows['zc'] == 'TR' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_T");?>/<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_R");?></option>
					<option value="TL" <?=($settingRows['zc'] == 'TL' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_T");?>/<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_L");?></option>
					<option value="BL" <?=($settingRows['zc'] == 'BL' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_B");?>/<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_L");?></option>
					<option value="BR" <?=($settingRows['zc'] == 'BR' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_B");?>/<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_R");?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_FAR");?>: </td>
			<td>
				<select name="OPTIONS[far]">
					<option value="-"><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_SELECT");?></option>
					<option value="C" <?=($settingRows['far'] == 'C' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_C");?></option>
					<option value="T" <?=($settingRows['far'] == 'T' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_T");?></option>
					<option value="R" <?=($settingRows['far'] == 'R' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_R");?></option>
					<option value="B" <?=($settingRows['far'] == 'B' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_B");?></option>
					<option value="L" <?=($settingRows['far'] == 'L' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_L");?></option>
					<option value="TR" <?=($settingRows['far'] == 'TR' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_T");?>/<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_R");?></option>
					<option value="TL" <?=($settingRows['far'] == 'TL' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_T");?>/<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_L");?></option>
					<option value="BL" <?=($settingRows['far'] == 'BL' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_B");?>/<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_L");?></option>
					<option value="BR" <?=($settingRows['far'] == 'BR' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_B");?>/<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_R");?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_IAR");?>: </td>
			<td>
				<select name="OPTIONS[iar]">
					<option value="-"><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_SELECT");?></option>
					<option value="C" <?=($settingRows['iar'] == 'C' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_C");?></option>
					<option value="T" <?=($settingRows['iar'] == 'T' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_T");?></option>
					<option value="R" <?=($settingRows['iar'] == 'R' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_R");?></option>
					<option value="B" <?=($settingRows['iar'] == 'B' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_B");?></option>
					<option value="L" <?=($settingRows['iar'] == 'L' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_L");?></option>
					<option value="TR" <?=($settingRows['iar'] == 'TR' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_T");?>/<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_R");?></option>
					<option value="TL" <?=($settingRows['iar'] == 'TL' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_T");?>/<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_L");?></option>
					<option value="BL" <?=($settingRows['iar'] == 'BL' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_B");?>/<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_L");?></option>
					<option value="BR" <?=($settingRows['iar'] == 'BR' ? 'selected="selected"': '');?>><?=GetMessage("MILLCOM_PHPTHUMB_FIELD_B");?>/<?=GetMessage("MILLCOM_PHPTHUMB_FIELD_R");?></option>
				</select>
			</td>
		</tr>	
		<?$tabs_control->BeginNextTab();?>
		<?if($EDIT_ID):
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/upload/phpthumb/'.$EDIT_ID.'/';

		$dir = opendir($filePath);
		$count = 0;
		while($file = readdir($dir)){
			if($file == '.' || $file == '..'){
				continue;
			}
			$count++;
		}
		?>

		<p><?=GetMessage("MILLCOM_PHPTHUMB_FILE_COUNT", array("#COUNT#" => $count));?></p>
		<a href="<?=$APPLICATION->GetCurPage()?>?ID=<?=$EDIT_ID?>&lang=<?=LANGUAGE_ID?>&CLEAR_CACHE=1" class="adm-btn adm-btn-green"><?=GetMessage("MILLCOM_PHPTHUMB_CLEAR_CACHE");?></a>
		<?else:?>
			<div class="adm-info-message-wrap">
				<div class="adm-info-message" style="display: block;">
					<?=GetMessage("MILLCOM_PHPTHUMB_SAVE_TEMPLATE");?>
				</div>
			</div>
		<?endif;?>
		
		<?$tabs_control->BeginNextTab();?>
		<?if($EDIT_ID):?>
		<?
		$DEMO = CMillcomPhpThumb::generateImg('bitrix/images/millcom.phpthumb/demo.jpg', $EDIT_ID, true);
		?>
		<img src="<?=$DEMO;?>" alt="" class="img-demo">
		<?else:?>
			<div class="adm-info-message-wrap">
				<div class="adm-info-message" style="display: block;">
					<?=GetMessage("MILLCOM_PHPTHUMB_SAVE_TEMPLATE");?>
				</div>
			</div>
		<?endif;?>
		<?php
		$tabs_control->Buttons(
			array(
				"back_url" => '/bitrix/admin/millcom_phpthumb_list.php?' . bitrix_sessid_get() . '&lang=' . LANG
			)
		);
		$tabs_control->End();
		?>
	</form>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>