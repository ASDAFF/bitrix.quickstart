<?
$ModuleID = 'webdebug.reviews';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/install/demo.php');
if (!CModule::IncludeModule($ModuleID)) {
	die('Module is not found!');
}
IncludeModuleLangFile(__FILE__);
$WD_Reviews2_InterfaceID = IntVal($_GET['interface']);
$WD_Reviews2_ReviewID = IntVal($_GET['ID']);
CAjax::Init();
if ($_GET['bxpublic']!='Y') {
	CWD_Reviews2::InitJQuery();
}
$Lang = LANGUAGE_ID;

$Mode = $WD_Reviews2_ReviewID>0 ? 'edit' : 'add';
if ($Mode=='edit') {
	$APPLICATION->SetTitle(GetMessage('WD_REVIEWS2_PAGE_TITLE_EDIT'));
} else {
	$APPLICATION->SetTitle(GetMessage('WD_REVIEWS2_PAGE_TITLE_ADD'));
}

$arTabs = array(
	array("DIV"=>"wd_reviews2_tab_review", "TAB"=>GetMessage('WD_REVIEWS2_TAB1_NAME'), "TITLE"=>GetMessage('WD_REVIEWS2_TAB1_DESC')),
);

$tabControl = new CAdminTabControl("WD_Reviews2_Review", $arTabs);

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
	$arErrors = array();
	$arSaveFields = $_POST["fields"];
	$arSaveFields['INTERFACE_ID'] = $WD_Reviews2_InterfaceID;
	$arSaveFields['MODERATED'] = $arSaveFields['MODERATED']=='Y' ? 'Y' : 'N';
	$obReviews = new CWD_Reviews2_Reviews;
	if (strlen($arSaveFields['ANSWER'])) {
		$arSaveFields['ANSWER'] = CWD_Reviews2::ProtectText($arSaveFields['ANSWER']);
	}
	if ($Mode=="edit") {
		$ID = $WD_Reviews2_ReviewID;
		$bResult = $obReviews->Update($ID, $arSaveFields);
	} else {
		$ID = $obReviews->Add($arSaveFields);
		$bResult = $ID>0;
	}
	if (!$bResult) {
		$Delimiter = '<br/>';
		if (!defined('BX_PUBLIC_MODE') || BX_PUBLIC_MODE != 1) {
			$Delimiter = "\n";
		}
		CWD_Reviews2::ShowError(implode($Delimiter,$obReviews->arLastErrors));
	}
	if ($bResult) {
		if (isset($_POST["save"]) && trim($_POST["save"])!="") {
			LocalRedirect("/bitrix/admin/wd_reviews2_list.php?interface={$WD_Reviews2_InterfaceID}&lang=".LANGUAGE_ID);
		} else {
			LocalRedirect("/bitrix/admin/wd_reviews2_edit.php?interface={$WD_Reviews2_InterfaceID}&ID={$ID}&lang=".LANGUAGE_ID."&".$tabControl->ActiveTabParam());
		}
	}
}

// Get current interface data
$arReview = array();
if ($WD_Reviews2_ReviewID>0) {
	$resReview = CWD_Reviews2_Reviews::GetByID($WD_Reviews2_ReviewID);
	$arFields = $resReview->GetNext();
}

// Prepare fields
if (!is_array($arFields)) {
	$arFields = array();
}
if ($arFields['USER_ID']==='0') {
	$arFields['USER_ID']='';
}
if ($arFields['ANSWER_USER_ID']==='0') {
	$arFields['ANSWER_USER_ID']='';
}
if ($arFields['DATE_ANSWER']===CWD_Reviews2::GetZeroDate()) {
	$arFields['DATE_ANSWER']='';
}
if (isset($arFields['~DATA_FIELDS'])) {
	$arFields['DATA_FIELDS'] = unserialize($arFields['~DATA_FIELDS']);
	if (!is_array($arFields['DATA_FIELDS'])) {
		$arFields['DATA_FIELDS'] = array();
	}
}
if (isset($arFields['~DATA_RATINGS'])) {
	$arFields['DATA_RATINGS'] = unserialize($arFields['~DATA_RATINGS']);
	if (!is_array($arFields['DATA_RATINGS'])) {
		$arFields['DATA_RATINGS'] = array();
	}
}

if ($Mode=='add' && !isset($arFields['DATE_CREATED'])) {
	$arFields['DATE_CREATED'] = date(CDatabase::DateFormatToPHP(FORMAT_DATETIME));
}

$TargetForAdd = false;
if($Mode=='add' && $_GET['public_add']=='Y' && strlen($_GET['target'])) {
	$arFields['TARGET'] = htmlspecialchars($_GET['target']);
	$TargetForAdd = true;
}

// Handling error saving
if (is_array($_POST['fields'])) {
	$arFields = $_POST['fields'];
	$arFields['DATA_FIELDS'] = $arFields['FIELDS'];
	$arFields['DATA_RATINGS'] = $arFields['RATINGS'];
	unset($arFields['FIELDS'],$arFields['RATINGS']);
}

// If not exists
if ($Mode=='edit' && (!is_array($arFields) || empty($arFields))) {
	CWD_Reviews2::ShowError(GetMessage('WD_REVIEWS2_ERROR_REVIEW_NOT_FOUND'));
	die();
}

// Deleting
if ($_GET["action"]=="delete" && $WD_Reviews2_ReviewID>0 && check_bitrix_sessid()) {
	$obReviews = new CWD_Reviews2_Reviews;
	if ($obReviews->Delete($WD_Reviews2_ReviewID)) {
		LocalRedirect('/bitrix/admin/wd_reviews2_list.php?interface={$WD_Reviews2_InterfaceID}&lang='.LANGUAGE_ID);
	}
}

// MenuItem: Profiles
$aMenu[] = array(
	"TEXT"	=> GetMessage('WD_REVIEWS2_MENU_LIST'),
	"LINK"	=> "/bitrix/admin/wd_reviews2_list.php?interface={$WD_Reviews2_InterfaceID}&lang=".LANGUAGE_ID,
	"ICON"	=> "btn_list",
);
if ($Mode == "edit") {
	// MenuItem: Add
	$aMenu[] = array(
		"TEXT"	=> GetMessage('WD_REVIEWS2_MENU_ADD'),
		"LINK"	=> "/bitrix/admin/wd_reviews2_edit.php?interface={$WD_Reviews2_InterfaceID}&lang=".LANGUAGE_ID,
		"ICON"	=> "btn_new",
	);
	// MenuItem: Delete
	$Confirm = GetMessage('WD_REVIEWS2_MENU_DELETE_CONFIRM');
	$aMenu[] = array(
		"TEXT"	=> GetMessage('WD_REVIEWS2_MENU_DELETE'),
		"LINK"	=> "javascript:if (confirm('{$Confirm}')) window.location='/bitrix/admin/wd_reviews2_list.php?interface={$WD_Reviews2_InterfaceID}&action=delete&ID=".$ID."&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."';",
		"ICON"	=> "btn_delete",
	);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?
$arInterface = CWD_Reviews2_Reviews::ReviewGetInterface($WD_Reviews2_InterfaceID);
$arReviewFields = CWD_Reviews2_Reviews::ReviewGetFields($WD_Reviews2_ReviewID, $WD_Reviews2_InterfaceID);
$arReviewRatings = CWD_Reviews2_Reviews::ReviewGetRatings($WD_Reviews2_ReviewID, $WD_Reviews2_InterfaceID);
?>

<style>
.bx-core-waitwindow {left:auto!important; position:fixed!important; right:5px!important; top:5px!important;}
</style>
<form method="post" action="<?=POST_FORM_ACTION_URI;?>" enctype="multipart/form-data" name="post_form" id="wd_reviews2_review_form">
	<?$tabControl->Begin();?>
	<?$tabControl->BeginNextTab();?>
		<tr id="tr_moderated">
			<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_FIELD_MODERATED')?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[MODERATED]" value="Y"<?if($arFields["MODERATED"]=='Y'):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr id="tr_date_created">
			<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_FIELD_DATE_CREATED');?>:</td>
			<td class="field-data">
				<?=CalendarDate('fields[DATE_CREATED]', $arFields['DATE_CREATED'], 'post_form', '15', 'autocomplete="off"');?>
			</td>
		</tr>
		<tr id="tr_target">
			<td class="field-name adm-required-field" width="40%"><?=GetMessage('WD_REVIEWS2_FIELD_TARGET');?>:</td>
			<td class="field-data">
				<input type="text" name="fields[TARGET]" value="<?=$arFields["TARGET"]?>" size="30" maxlength="255"<?if($TargetForAdd):?> disabled="disabled"<?endif?> />
				<?if(!$TargetForAdd):?>
					<?
						$LinkAdmin = false;
						$LinkPublic = false;
						if(preg_match('#^E_(\d+)$#is', $arFields["TARGET"], $M)) {
							$TargetElementID = $M[1];
							if($TargetElementID>0 && CModule::IncludeModule('iblock')) {
								$resItem = CIBlockElement::GetList(false,array('ID'=>$TargetElementID),false,false,array('ID','IBLOCK_ID','NAME','SECTION_ID','DETAIL_PAGE_URL'));
								if ($arItem = $resItem->GetNext(false,false)) {
									$LinkPublic = $arItem['DETAIL_PAGE_URL'];
									$LinkAdmin = "/bitrix/admin/iblock_element_edit.php?IBLOCK_ID={$arItem['IBLOCK_ID']}&type={$arItem['IBLOCK_TYPE_ID']}&ID={$arItem['ID']}&lang={$Lang}&find_section_section={$arItem['IBLOCK_SECTION_ID']}&WF=Y";
								}
							}
						}
					?>
					<?if($LinkPublic!==false):?>
						&nbsp;&nbsp;
						<a href="<?=$LinkPublic;?>" target="_blank" title="<?=GetMessage('WD_REVIEWS2_FIELD_TARGET_BLANK');?>"><?=GetMessage('WD_REVIEWS2_FIELD_TARGET_OPEN_SITE');?></a>
						&nbsp;
						<a href="<?=$LinkAdmin;?>" target="_blank" title="<?=GetMessage('WD_REVIEWS2_FIELD_TARGET_BLANK');?>"><?=GetMessage('WD_REVIEWS2_FIELD_TARGET_OPEN_ADMIN');?></a>
					<?endif?>
				<?endif?>
			</td>
		</tr>
		<tr id="tr_user_id">
			<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_FIELD_USER_ID');?>:</td>
			<td class="field-data">
				<input type="text" name="fields[USER_ID]" value="<?=$arFields["USER_ID"]?>" size="10" maxlength="255" />
				<input class="tablebodybutton" type="button" name="FindUserID" id="FindUserID" onclick="window.open('/bitrix/admin/user_search.php?lang=ru&FN=post_form&FC=fields[USER_ID]', '', 'scrollbars=yes,resizable=yes,width=900,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 900)/2-5));" value="...">
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage('WD_REVIEWS2_HEADER_FIELD');?></td>
		</tr>
		<?if(!empty($arReviewFields)):?>
			<?foreach($arReviewFields as $arReviewField):?>
				<tr id="tr_field_<?=ToLower($arReviewField['CODE']);?>">
					<td class="field-name<?if($arReviewField['REQUIRED']=='Y'):?> adm-required-field<?endif?>" width="40%"><?=$arReviewField['NAME'];?>:</td>
					<td class="field-data">
						<?=CWD_Reviews2::ShowField($arFields['DATA_FIELDS'][$arReviewField['CODE']], $arReviewField, 'fields[FIELDS]');?>
					</td>
				</tr>
			<?endforeach?>
		<?else:?>
			<tr id="tr_no_fields">
				<td class="field-data" colspan="2">
					<div><?=GetMessage('WD_REVIEWS2_NO_FIELDS',array('#LINK#'=>"/bitrix/admin/wd_reviews2_interface.php?ID={$WD_Reviews2_InterfaceID}&lang={$Lang}&WD_Reviews2_Interface_active_tab=wd_reviews2_tab_fields"));?></div><br/>
				</td>
			</tr>
		<?endif?>
		<tr class="heading">
			<td colspan="2"><?=GetMessage('WD_REVIEWS2_HEADER_RATINGS');?></td>
		</tr>
		<?if(!empty($arReviewRatings)):?>
			<?foreach($arReviewRatings as $arReviewRating):?>
				<tr id="tr_rating_<?=ToLower($arReviewRating['CODE']);?>">
					<td class="field-name" width="40%"><?=$arReviewRating['NAME'];?>:</td>
					<td class="field-data">
						<?=CWD_Reviews2::ShowRating($arFields['DATA_RATINGS'][$arReviewRating['ID']], array('INTERFACE_ID'=>$arReviewRating['INTERFACE_ID'],'INPUT_NAME'=>'fields[RATINGS]['.$arReviewRating['ID'].']'));?>
					</td>
				</tr>
			<?endforeach?>
		<?else:?>
			<tr id="tr_no_ratings">
				<td class="field-data" colspan="2">
					<div><?=GetMessage('WD_REVIEWS2_NO_RATINGS',array('#LINK#'=>"/bitrix/admin/wd_reviews2_interface.php?ID={$WD_Reviews2_InterfaceID}&lang={$Lang}&WD_Reviews2_Interface_active_tab=wd_reviews2_tab_ratings"));?></div><br/>
				</td>
			</tr>
		<?endif?>
		<?if($WD_Reviews2_ReviewID>0):?>
			<tr class="heading">
				<td colspan="2"><?=GetMessage('WD_REVIEWS2_HEADER_RATINGS_VOTING');?></td>
			</tr>
			<tr id="tr_vote_y">
				<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_VOTES_Y');?>:</td>
				<td class="field-data">
					<span<?if($arFields['VOTES_Y']>0):?> style="color:green;"<?endif?>><?=$arFields['VOTES_Y'];?></span>
				</td>
			</tr>
			<tr id="tr_vote_n">
				<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_VOTES_N');?>:</td>
				<td class="field-data">
					<span<?if($arFields['VOTES_N']>0):?> style="color:red;"<?endif?>><?=$arFields['VOTES_N'];?></span>
				</td>
			</tr>
			<tr id="tr_vote_result">
				<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_VOTE_RESULT');?>:</td>
				<td class="field-data">
					<span style="<?if($arFields['VOTE_RESULT']>0):?>color:green;<?elseif($arFields['VOTE_RESULT']<0):?>color:red;<?endif?>font-weight:bold;"><?=$arFields['VOTE_RESULT'];?></span>
				</td>
			</tr>
			<tr class="heading">
				<td colspan="2"><?=GetMessage('WD_REVIEWS2_HEADER_ASWER');?></td>
			</tr>
			<tr id="tr_answer">
				<td class="field-name" width="40%" valign="top"><br/><?=GetMessage('WD_REVIEWS2_ANSWER');?>:</td>
				<td class="field-data">
					<?$arTypes = WDR2_GetFieldTypes();?>
					<?if(array($arTypes['TEXTAREA'])):?>
						<?
						$ClassName = $arTypes['TEXTAREA']['CLASS'];
						$arVisualEditorFields = array(
							'CODE' => 'ANSWER',
							'PARAMS' => array(
								'use_visual_editor' => 'Y',
								'visual_editor_height' => '150',
							),
						);
						print $ClassName::Show($arFields["~ANSWER"], $arVisualEditorFields, 'fields');
						?>
					<?else:?>
						<textarea name="fields[ANSWER]" cols="60" rows="5"><?=$arFields["ANSWER"]?></textarea>
					<?endif?>
				</td>
			</tr>
			<tr id="tr_date_created">
				<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_DATE_ANSWER');?>:</td>
				<td class="field-data">
					<?=CalendarDate('fields[DATE_ANSWER]', $arFields['DATE_ANSWER'], 'post_form', '15', 'autocomplete="off"');?>
				</td>
			</tr>
			<tr id="tr_answer_user_id">
				<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_ANSWER_USER_ID');?>:</td>
				<td class="field-data">
					<input type="text" name="fields[ANSWER_USER_ID]" value="<?=$arFields["ANSWER_USER_ID"]?>" size="10" maxlength="255" />
					<input class="tablebodybutton" type="button" name="FindAnswerUserID" id="FindAnswerUserID" onclick="window.open('/bitrix/admin/user_search.php?lang=ru&FN=post_form&FC=fields[ANSWER_USER_ID]', '', 'scrollbars=yes,resizable=yes,width=900,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 900)/2-5));" value="...">
				</td>
			</tr>
		<?endif?>
		<?if (!defined('BX_PUBLIC_MODE') || BX_PUBLIC_MODE != 1):?>
			<?$tabControl->Buttons(array("disabled"=>false,"back_url"=>"wd_reviews2_list.php?lang=".LANGUAGE_ID.'&interface='.$WD_Reviews2_InterfaceID));?>
		<?elseif(!$bPropertyAjax && $nobuttons !== "Y"):?>
			<?
			$strCancel = GetMessage('WD_REVIEWS2_CANCEL');
			$btnCancel = "{
				title: '{$strCancel}',
				name: 'cancel',
				id: 'wdr2_review_public_edit_cancel',
				action: function () {BX.WindowManager.Get().Close();if(window.reloadAfterClose)top.BX.reload(true);}
			}";
			$tabControl->ButtonsPublic(array('.btnSave',$btnCancel));
			?>
		<?endif;?>
	<?$tabControl->End();?>
</form>

<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>