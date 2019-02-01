<?
$ModuleID = 'webdebug.reviews';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/install/demo.php');
if (!CModule::IncludeModule($ModuleID)) {
	die('Module is not found!');
}
IncludeModuleLangFile(__FILE__);
CAjax::Init();

$WD_Reviews2_InterfaceID = IntVal($_GET["ID"]);
$ReviewsCount = CWD_Reviews2_Reviews::GetInterfaceReviewsCount($WD_Reviews2_InterfaceID);

$Mode = $WD_Reviews2_InterfaceID>0 ? 'edit' : 'add';
if ($Mode=='edit') {
	$APPLICATION->SetTitle(GetMessage('WD_REVIEWS2_PAGE_TITLE_EDIT'));
} else {
	$APPLICATION->SetTitle(GetMessage('WD_REVIEWS2_PAGE_TITLE_ADD'));
}

$arTabs = array(
	array("DIV"=>"wd_reviews2_tab_general", "TAB"=>GetMessage('WD_REVIEWS2_TAB_GENERAL_NAME'), "TITLE"=>GetMessage('WD_REVIEWS2_TAB_GENERAL_DESC'), 'ICON'=>'wd_reviews2_tab_icon_general'),
	array("DIV"=>"wd_reviews2_tab_fields", "TAB"=>GetMessage('WD_REVIEWS2_TAB_FIELDS_NAME'), "TITLE"=>GetMessage('WD_REVIEWS2_TAB_FIELDS_DESC'), 'ICON'=>'wd_reviews2_tab_icon_fields'),
	array("DIV"=>"wd_reviews2_tab_ratings", "TAB"=>GetMessage('WD_REVIEWS2_TAB_RATINGS_NAME'), "TITLE"=>GetMessage('WD_REVIEWS2_TAB_RATINGS_DESC'), 'ICON'=>'wd_reviews2_tab_icon_ratings'),
);

$tabControl = new CAdminTabControl("WD_Reviews2_Interface", $arTabs);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (webdebug_reviews_show_demo()) {
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	webdebug_reviews_show_demo();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

// Get site list
$arSites = CWD_Reviews2::GetSitesList();

if (isset($_POST["save"]) && trim($_POST["save"])!="" || isset($_POST["apply"]) && trim($_POST["apply"])!="") {
	$arSaveFields = $_POST["fields"];
	if ($arSaveFields['DELETE_IMAGES']=='Y') {
		$resInterfaceTemp = CWD_Reviews2_Interface::GetByID($WD_Reviews2_InterfaceID);
		if ($arInterfaceTemp = $resInterfaceTemp->GetNext(false,false)) {
			DeleteDirFilesEx('/upload/webdebug.reviews/'.$WD_Reviews2_InterfaceID.'/');
		}
		$arSaveFields['RATING_IMG_ACTIVE'] = '';
		$arSaveFields['RATING_IMG_HALF'] = '';
		$arSaveFields['RATING_IMG_INACTIVE'] = '';
		$arSaveFields['RATING_IMG_RANGE'] = '';
	}
	$arSaveFields['PRE_MODERATION'] = $arSaveFields['PRE_MODERATION']=='Y' ? 'Y' : 'N';
	$arSaveFields['ALLOW_UNREGISTERED'] = $arSaveFields['ALLOW_UNREGISTERED']=='Y' ? 'Y' : 'N';
	$arSaveFields['EMAIL_ON_ADD'] = $arSaveFields['EMAIL_ON_ADD']=='Y' ? 'Y' : 'N';
	$arSaveFields['EMAIL_ON_ANSWER'] = $arSaveFields['EMAIL_ON_ANSWER']=='Y' ? 'Y' : 'N';
	$arSaveFields['EMAIL_ON_MODERATED'] = $arSaveFields['EMAIL_ON_MODERATED']=='Y' ? 'Y' : 'N';
	$arSaveFields['RATING_IS_REQUIRED'] = $arSaveFields['RATING_IS_REQUIRED']=='Y' ? 'Y' : 'N';
	$arSaveFields['RATING_HALF_SHOW'] = $arSaveFields['RATING_HALF_SHOW']=='Y' ? 'Y' : 'N';
	$arSaveFields['RATING_SHOW_SPACES'] = $arSaveFields['RATING_SHOW_SPACES']=='Y' ? 'Y' : 'N';
	$arSaveFields['RATING_USE_RANGE'] = $arSaveFields['RATING_USE_RANGE']=='Y' ? 'Y' : 'N';
	if ($arSaveFields['RATING_STARS_COUNT_DEF']>$arSaveFields['RATING_STARS_COUNT']) {
		$arSaveFields['RATING_STARS_COUNT_DEF']=$arSaveFields['RATING_STARS_COUNT'];
	}
	$WD_Reviews2_Interface = new CWD_Reviews2_Interface;
	if ($Mode=="edit") {
		$ID = $WD_Reviews2_InterfaceID;
		$bResult = $WD_Reviews2_Interface->Update($ID, $arSaveFields);
	} else {
		$ID = $WD_Reviews2_Interface->Add($arSaveFields);
		$bResult = $ID>0;
	}
	$arImagesFields = array();
	if ($bResult) {
		// Start of 'Save files'
		$Dir = '/upload/webdebug.reviews';
		foreach(array('ACTIVE','HALF','INACTIVE') as $Key) {
			if ($_FILES['RATING_IMG_'.$Key]['error']===0) {
				$arPath = pathinfo($_FILES['RATING_IMG_'.$Key]['name']);
				$Ext = ToUpper($arPath['extension']);
				if ($Ext=='PNG') {
					$Dir2 = $Dir.'/'.$WD_Reviews2_InterfaceID.'/star';
					mkdir($_SERVER['DOCUMENT_ROOT'].$Dir2, BX_DIR_PERMISSIONS, true);
					$FileName = $Dir2.'/'.ToLower($Key).'.png';
					if (move_uploaded_file($_FILES['RATING_IMG_'.$Key]['tmp_name'],$_SERVER['DOCUMENT_ROOT'].$FileName)) {
						$arImagesFields['RATING_IMG_'.$Key] = $FileName;
					}
				}
			}
		}
		if ($_FILES['RATING_IMG_RANGE']['error']===0) {
			$arPath = pathinfo($_FILES['RATING_IMG_RANGE']['name']);
			$Ext = $arPath['extension'];
			if (ToUpper($Ext)=='ZIP' && class_exists('ZipArchive')) {
				$Dir2 = $Dir.'/'.$WD_Reviews2_InterfaceID.'/pack';
				$Zip = new ZipArchive;
				if ($Zip->open($_FILES['RATING_IMG_RANGE']['tmp_name']) === TRUE) {
					mkdir($_SERVER['DOCUMENT_ROOT'].$Dir2, BX_DIR_PERMISSIONS, true);
					$Zip->extractTo($_SERVER['DOCUMENT_ROOT'].$Dir2);
					$Zip->close();
					$arRatingRange = array();
					// search images
					$Handle = opendir($_SERVER['DOCUMENT_ROOT'].$Dir2);
					while (($File = readdir($Handle)) !== false)  {
						if($File != '.' && $File != '..') {
							if(is_file($_SERVER['DOCUMENT_ROOT'].$Dir2.'/'.$File)) {
								$arPath = pathinfo($File);
								$Ext = $arPath['extension'];
								if (ToUpper($Ext)=='PNG') {
									$FileName = $arPath['filename'];
									if (is_numeric($FileName) && $FileName>0) {
										$arItem = array();
										$arItem[] = $Dir2.'/'.$File;
										if(is_file($_SERVER['DOCUMENT_ROOT'].$Dir2.'/'.$FileName.'_.'.$Ext)) {
											$arItem[] = $Dir2.'/'.$FileName.'_.'.$Ext;
										} elseif (is_file($_SERVER['DOCUMENT_ROOT'].$Dir2.'/0.'.$Ext)) {
											$arItem[] = $Dir2.'/0.'.$Ext;
										} else {
											$arItem[] = '';
										}
										$arRatingRange[$FileName] = $arItem;
									}
								}
							}
						}
					}
					closedir($Handle);
					$arImagesFields['RATING_IMG_RANGE'] = serialize($arRatingRange);
				}
			}
		}
		if (!empty($arImagesFields)) {
			$arImagesFields['INTERFACE_ID'] = $WD_Reviews2_InterfaceID;
			$WD_Reviews2_Interface->Update($ID, $arImagesFields);
		}
		// End of 'Save files'
		if (isset($_POST["save"]) && trim($_POST["save"])!="") {
			LocalRedirect("/bitrix/admin/wd_reviews2_interfaces.php?lang=".LANGUAGE_ID);
		} else {
			LocalRedirect("/bitrix/admin/wd_reviews2_interface.php?ID={$ID}&lang=".LANGUAGE_ID."&".$tabControl->ActiveTabParam());
		}
	} else {
		CWD_Reviews2::ShowError(implode("\n",$WD_Reviews2_Interface->arLastErrors));
	}
}

// Get current interface data
$arInterface = array();
if ($WD_Reviews2_InterfaceID>0) {
	$resInterface = CWD_Reviews2_Interface::GetByID($WD_Reviews2_InterfaceID);
	if ($arInterface = $resInterface->GetNext()) {
		if (isset($arInterface['~RATING_IMG_RANGE'])) {
			$arInterface['RATING_IMG_RANGE'] = unserialize($arInterface['~RATING_IMG_RANGE']);
		}
	} else {
		CWD_Reviews2::ShowError(GetMessage('WD_REVIEWS2_ERROR_INTERFACE_NOT_FOUND'));
		die();
	}
}
if (empty($arInterface)) {
	$arInterface = array(
		'SORT' => '100',
	);
}

// Handling error saving
if ($WD_Reviews2_InterfaceID==0 && is_array($_POST['fields'])) {
	$arInterface = $_POST['fields'];
}

// If not exists
if ($Mode=='edit' && (!is_array($arInterface) || empty($arInterface))) {
	CWD_Reviews2::ShowError(GetMessage('WD_REVIEWS2_ERROR_INTERFACE_NOT_FOUND'));
	die();
}

// Deleting
if ($ReviewsCount===0 && $_GET["action"]=="delete" && IntVal($_GET["ID"])>0 && check_bitrix_sessid()) {
	$ID = IntVal($_GET["ID"]);
	$WD_Reviews2_Interface = new CWD_Reviews2_Interface;
	if ($WD_Reviews2_Interface->Delete($ID)) {
		LocalRedirect('wd_reviews2_interfaces.php.php?lang='.LANGUAGE_ID);
	} else {
		CWD_Reviews2::ShowError(implode("\n",$WD_Reviews2_Interface->arLastErrors));
		die();
	}
}

// MenuItem: Profiles
$aMenu[] = array(
	"TEXT"	=> GetMessage('WD_REVIEWS2_MENU_INTERFACE_LIST'),
	"LINK"	=> "/bitrix/admin/wd_reviews2_interfaces.php?lang=".LANGUAGE_ID,
	"ICON"	=> "btn_list",
);
if ($Mode == "edit") {
	// MenuItem: Add
	$aMenu[] = array(
		"TEXT"	=> GetMessage('WD_REVIEWS2_MENU_INTERFACE_ADD'),
		"LINK"	=> "/bitrix/admin/wd_reviews2_interface.php?lang=".LANGUAGE_ID,
		"ICON"	=> "btn_new",
	);
	if ($ReviewsCount===0) {
		// MenuItem: Delete
		$aMenu[] = array(
			"TEXT"	=> GetMessage('WD_REVIEWS2_MENU_INTERFACE_DELETE'),
			"LINK"	=> "javascript:if(confirm('".GetMessage('WD_REVIEWS2_MENU_INTERFACE_DELETE_CONFIRM')."')) window.location='/bitrix/admin/wd_reviews2_interfaces.php?action=delete&ID=".$ID."&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."';",
			"ICON"	=> "btn_delete",
		);
	}
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?CWD_Reviews2::InitJQuery();?>

<style>
.bx-core-waitwindow {left:auto!important; position:fixed!important; right:5px!important; top:5px!important;}
</style>
<form method="post" action="" enctype="multipart/form-data" name="post_form" id="wd_reviews2_interface_form">
	<?$tabControl->Begin();?>
	<?$tabControl->BeginNextTab();?>
		<tr id="tr_name">
			<td class="field-name adm-required-field" width="40%"><?=GetMessage('WD_REVIEWS2_INTERFACE_NAME');?>:</td>
			<td class="field-data">
				<input type="text" name="fields[NAME]" value="<?=$arInterface["NAME"]?>" size="60" maxlength="255" />
			</td>
		</tr>
		<tr id="tr_descr">
			<td class="field-name" width="40%" valign="top"><br/><?=GetMessage('WD_REVIEWS2_INTERFACE_DESCRIPTION');?>:</td>
			<td class="field-data">
				<textarea name="fields[DESCRIPTION]" cols="60" rows="3"><?=$arInterface["DESCRIPTION"]?></textarea>
			</td>
		</tr>
		<tr id="tr_sort">
			<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_INTERFACE_SORT');?>:</td>
			<td class="field-data">
				<input type="text" name="fields[SORT]" value="<?=$arInterface["SORT"]?>" size="10" maxlength="10" />
			</td>
		</tr>
		<tr id="tr_pre_moderation">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_INTERFACE_PREMODERATION_HINT'));?> <?=GetMessage('WD_REVIEWS2_INTERFACE_PREMODERATION');?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[PRE_MODERATION]" value="Y"<?if($arInterface['PRE_MODERATION']=='Y'):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr id="tr_allow_unregistered">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_INTERFACE_ALLOW_UNREG_HINT'));?> <?=GetMessage('WD_REVIEWS2_INTERFACE_ALLOW_UNREG');?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[ALLOW_UNREGISTERED]" value="Y"<?if($arInterface['ALLOW_UNREGISTERED']=='Y'):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr id="tr_captcha_mode">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_INTERFACE_USE_CAPTCHA_HINT'));?> <?=GetMessage('WD_REVIEWS2_INTERFACE_USE_CAPTCHA');?>:</td>
			<td class="field-data">
				<select name="fields[CAPTCHA_MODE]">
					<option value=""><?=GetMessage('WD_REVIEWS2_INTERFACE_USE_CAPTCHA_N');?></option>
					<option value="U"<?if($arInterface['CAPTCHA_MODE']=='U'):?> selected="selected"<?endif?>><?=GetMessage('WD_REVIEWS2_INTERFACE_USE_CAPTCHA_U');?></option>
					<option value="Y"<?if($arInterface['CAPTCHA_MODE']=='Y'):?> selected="selected"<?endif?>><?=GetMessage('WD_REVIEWS2_INTERFACE_USE_CAPTCHA_Y');?></option>
				</select>
			</td>
		</tr>
		<tr id="tr_success_message">
			<td class="field-name" width="40%" valign="top"><br/><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_INTERFACE_SUCCESS_MESSAGE_HINT'));?> <?=GetMessage('WD_REVIEWS2_INTERFACE_SUCCESS_MESSAGE');?>:</td>
			<td class="field-data">
				<textarea name="fields[SUCCESS_MESSAGE]" cols="60" rows="3"><?=$arInterface["SUCCESS_MESSAGE"]?></textarea>
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage('WD_REVIEWS2_HEADER_EMAIL_NOTICE');?></td>
		</tr>
		<tr id="tr_url">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_INTERFACE_URL_HINT'));?> <?=GetMessage('WD_REVIEWS2_INTERFACE_URL');?>:</td>
			<td class="field-data">
				<input type="text" name="fields[URL]" value="<?=$arInterface["URL"]?>" size="52" maxlength="255" id="wd_reviews2_url" />
				<input type="button" id="wd_reviews2_btn_show_url_macroses" value='...'>
				<script>
					jQuery.fn.extend({insertAtCaret:function(a){return this.each(function(b){if(document.selection)this.focus(),sel=document.selection.createRange(),sel.text=a,this.focus();else if(this.selectionStart||"0"==this.selectionStart){b=this.selectionStart;var c=this.selectionEnd,d=this.scrollTop;this.value=this.value.substring(0,b)+a+this.value.substring(c,this.value.length);this.focus();this.selectionStart=b+a.length;this.selectionEnd=b+a.length;this.scrollTop=d}else this.value+=a,this.focus()})}});
					function __SetUrlVar(id, mnu_id, el_id) {
						$('#'+el_id).insertAtCaret(id).focus();
					}
				</script>
				<?
				$u = new CAdminPopupEx(
					"wd_reviews2_btn_show_url_macroses",
					CIBlockParameters::GetPathTemplateMenuItems("DETAIL", "__SetUrlVar", "wd_reviews2_btn_show_url_macroses", "wd_reviews2_url")
				);
				$u->Show();
				?>
			</td>
		</tr>
		<tr id="tr_email_on_add">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_EMAIL_ON_ADD_HINT'));?> <?=GetMessage('WD_REVIEWS2_EMAIL_ON_ADD');?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[EMAIL_ON_ADD]" value="Y"<?if($arInterface['EMAIL_ON_ADD']=='Y'):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr id="tr_email_on_answer">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_EMAIL_ON_ANSWER_HINT'));?> <?=GetMessage('WD_REVIEWS2_EMAIL_ON_ANSWER');?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[EMAIL_ON_ANSWER]" value="Y"<?if($arInterface['EMAIL_ON_ANSWER']=='Y'):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr id="tr_email_on_moderated">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_EMAIL_ON_MODERATED_HINT'));?> <?=GetMessage('WD_REVIEWS2_EMAIL_ON_MODERATED');?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[EMAIL_ON_MODERATED]" value="Y"<?if($arInterface['EMAIL_ON_MODERATED']=='Y'):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage('WD_REVIEWS2_HEADER_ADDITIONAL');?></td>
		</tr>
		<tr id="tr_jquery_init_url">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_INTERFACE_INCLUDE_JQUERY_HINT'));?> <?=GetMessage('WD_REVIEWS2_INTERFACE_INCLUDE_JQUERY');?></td>
			<td class="field-data">
				<textarea name="fields[JQUERY_INIT_URL]" cols="60" rows="3"><?=$arInterface["JQUERY_INIT_URL"]?></textarea>
			</td>
		</tr>
	<?$tabControl->BeginNextTab();?>
		<tr>
			<td class="field-data" colspan="2">
				<?if($WD_Reviews2_InterfaceID>0):?>
					<?require_once($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/'.$ModuleID.'/include/fields_list.php');?>
					<br/>
				<?else:?>
					<?=GetMessage('WD_REVIEWS2_NEED_SAVE_BEFORE_FIELDS');?>
				<?endif?>
			</td>
		</tr>
	<?$tabControl->BeginNextTab();?>
		<tr>
			<td class="field-data" colspan="2">
				<?if($WD_Reviews2_InterfaceID>0):?>
					<?require_once($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/'.$ModuleID.'/include/ratings_list.php');?>
					<br/>
				<?else:?>
					<?=GetMessage('WD_REVIEWS2_NEED_SAVE_BEFORE_RATINGS');?>
				<?endif?>
			</td>
		</tr>
		<?if($WD_Reviews2_InterfaceID>0):?>
		<tr class="heading">
			<td colspan="2"><?=GetMessage('WD_REVIEWS2_HEADER_RATINGS');?></td>
		</tr>
		<tr id="tr_rating_is_required">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_RATINGS_REQUIRED_HINT'));?> <?=GetMessage('WD_REVIEWS2_RATINGS_REQUIRED');?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[RATING_IS_REQUIRED]" value="Y"<?if($arInterface['RATING_IS_REQUIRED']=='Y'):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr id="tr_rating_star_count">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_RATINGS_STARSCOUNT_HINT'));?> <?=GetMessage('WD_REVIEWS2_RATINGS_STARSCOUNT');?>:</td>
			<td class="field-data">
				<input type="text" name="fields[RATING_STARS_COUNT]" value="<?=$arInterface['RATING_STARS_COUNT']?>" size="10" maxlength="10" />
			</td>
		</tr>
		<tr id="tr_rating_star_count">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_RATINGS_STARSCOUNTDEF_HINT'));?> <?=GetMessage('WD_REVIEWS2_RATINGS_STARSCOUNTDEF');?>:</td>
			<td class="field-data">
				<input type="text" name="fields[RATING_STARS_COUNT_DEF]" value="<?=$arInterface['RATING_STARS_COUNT_DEF']?>" size="10" maxlength="10" />
			</td>
		</tr>
		<tr id="tr_rating_star_hints">
			<td class="field-name" width="40%" valign="top"><br/><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_RATINGS_HINTS_HINT'));?> <?=GetMessage('WD_REVIEWS2_RATINGS_HINTS');?>:</td>
			<td class="field-data">
				<textarea name="fields[RATING_STARS_HINTS]" cols="30" rows="5" style="overflow:auto; resize:vertical"><?=$arInterface['RATING_STARS_HINTS']?></textarea>
			</td>
		</tr>
		<tr id="tr_rating_halh_show">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_RATINGS_SHOW_HALFS_HINT'));?> <?=GetMessage('WD_REVIEWS2_RATINGS_SHOW_HALFS');?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[RATING_HALF_SHOW]" value="Y"<?if($arInterface['RATING_HALF_SHOW']=='Y'):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr id="tr_rating_show_spaces">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_RATINGS_SHOW_INTERVALS_HINT'));?> <?=GetMessage('WD_REVIEWS2_RATINGS_SHOW_INTERVALS');?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[RATING_SHOW_SPACES]" value="Y"<?if($arInterface['RATING_SHOW_SPACES']=='Y'):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr id="tr_rating_use_range">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_RATINGS_USE_IMG_SET_HINT'));?> <?=GetMessage('WD_REVIEWS2_RATINGS_USE_IMG_SET');?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[RATING_USE_RANGE]" value="Y"<?if($arInterface['RATING_USE_RANGE']=='Y'):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr id="tr_rating_img_active">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_RATINGS_IMG_ACTIVE_HINT'));?> <?=GetMessage('WD_REVIEWS2_RATINGS_IMG_ACTIVE');?>:</td>
			<td class="field-data">
				<table>
					<tbody>
						<tr>
							<td style="width:1px">
								<input type="file" name="RATING_IMG_ACTIVE" />
							</td>
							<td style="font-size:0; padding:0 20px;">
								<?if(strlen($arInterface['RATING_IMG_ACTIVE'])):?>
									<img src="<?=$arInterface['RATING_IMG_ACTIVE'];?>" alt="" style="float:left; margin-right:4px;" />
								<?endif?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr id="tr_rating_img_active">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_RATINGS_IMG_HALF_HINT'));?> <?=GetMessage('WD_REVIEWS2_RATINGS_IMG_HALF');?>:</td>
			<td class="field-data">
				<table>
					<tbody>
						<tr>
							<td style="width:1px">
								<input type="file" name="RATING_IMG_HALF" />
							</td>
							<td style="font-size:0; padding:0 20px;">
								<?if(strlen($arInterface['RATING_IMG_HALF'])):?>
									<img src="<?=$arInterface['RATING_IMG_HALF'];?>" alt="" style="float:left; margin-right:4px;" />
								<?endif?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr id="tr_rating_img_inactive">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_RATINGS_IMG_INACTIVE_HINT'));?> <?=GetMessage('WD_REVIEWS2_RATINGS_IMG_INACTIVE');?>:</td>
			<td class="field-data">
				<table>
					<tbody>
						<tr>
							<td style="width:1px">
								<input type="file" name="RATING_IMG_INACTIVE" />
							</td>
							<td style="font-size:0; padding:0 20px;">
								<?if(strlen($arInterface['RATING_IMG_INACTIVE'])):?>
									<img src="<?=$arInterface['RATING_IMG_INACTIVE'];?>" alt="" style="float:left; margin-right:4px;" />
								<?endif?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr id="tr_rating_img_range">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_RATINGS_IMG_SET_ZIP_HINT'));?> <?=GetMessage('WD_REVIEWS2_RATINGS_IMG_SET_ZIP');?>:</td>
			<td class="field-data">
				<table>
					<tbody>
						<tr>
							<td style="width:1px">
								<input type="file" name="RATING_IMG_RANGE" />
							</td>
							<?if(is_array($arInterface['RATING_IMG_RANGE']) && !empty($arInterface['RATING_IMG_RANGE'])):?>
								<td style="font-size:0; padding:0 20px;">
									<div class="wd_reviews2_start_test_top" style="display:inline-block">
										<?foreach($arInterface['RATING_IMG_RANGE'] as $Key => $arValue):?>
											<img src="<?=$arValue[0];?>" alt="" style="float:left; margin-right:4px;" />
										<?endforeach?>
										<div style="clear:both"></div>
									</div>
									<br/>
									<div class="wd_reviews2_start_test_bottom" style="display:inline-block">
										<?foreach($arInterface['RATING_IMG_RANGE'] as $Key => $arValue):?>
											<img src="<?=$arValue[1];?>" alt="" style="float:left; margin-right:4px;" />
										<?endforeach?>
										<div style="clear:both"></div>
									</div>
								</td>
							<?endif?>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr id="tr_rating_use_range">
			<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_RATINGS_DELETE_IMAGES_HINT'));?> <?=GetMessage('WD_REVIEWS2_RATINGS_DELETE_IMAGES');?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[DELETE_IMAGES]" value="Y" />
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage('WD_REVIEWS2_HEADER_APPEARANCE');?></td>
		</tr>
		<tr id="tr_example_random">
			<td class="field-name" width="40%" valign="top"><?=GetMessage('WD_REVIEWS2_EXAMPLE_RANDOM');?>:</td>
			<td class="field-data">
				<?=CWD_Reviews2::ShowRating(rand(1,$arInterface['RATING_STARS_COUNT']), array('INTERFACE_ID'=>$WD_Reviews2_InterfaceID));?>
			</td>
		</tr>
		<tr id="tr_example_empty">
			<td class="field-name" width="40%" valign="top"><?=GetMessage('WD_REVIEWS2_EXAMPLE_EMPTY');?>:</td>
			<td class="field-data">
				<?=CWD_Reviews2::ShowRating(0, array('INTERFACE_ID'=>$WD_Reviews2_InterfaceID));?>
			</td>
		</tr>
		<tr id="tr_example_full">
			<td class="field-name" width="40%" valign="top"><?=GetMessage('WD_REVIEWS2_EXAMPLE_FULL');?>:</td>
			<td class="field-data">
				<?=CWD_Reviews2::ShowRating($arInterface['RATING_STARS_COUNT'], array('INTERFACE_ID'=>$WD_Reviews2_InterfaceID));?>
			</td>
		</tr>
		<?endif?>
	<?$tabControl->Buttons(array("disabled"=>false,"back_url"=>"wd_reviews2_interfaces.php?lang=".LANGUAGE_ID));?>
	<?$tabControl->End();?>
</form>

<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>