<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2010 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");

ClearVars();

if(!$USER->CanDoOperation('edit_ratings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

if (isset($_POST["CLEAR_DATA"]) && $_POST["CLEAR_DATA"] == 'Y' && $USER->IsAdmin() && check_bitrix_sessid())
{
	$_SESSION["SESS_ADMIN"]["RATING_CONFIG_CLEAR_DATA"]=array("MESSAGE"=>GetMessage("RATING_SETTINGS_FRM_RATING_CLEAR_DATA_OK"), "TYPE"=>"OK");
	CRatings::ClearData();
	LocalRedirect("rating_settings.php?lang=".LANG);
}

// set default values
$bTypeChange = isset($_POST["ACTION"]) && $_POST["ACTION"] == 'type_changed' ? true : false;
$ratingId = isset($_POST["RATING_ID"]) ? intval($_POST["RATING_ID"]) : 0;

$sRatingWeightType = isset($_POST["RATING_WEIGHT_TYPE"]) && $_POST["RATING_WEIGHT_TYPE"] == 'auto' ? 'auto' : 'manual';
$sRatingAuthrorityWeight = isset($_POST["RATING_AUTHORITY_WEIGHT"]) && $_POST["RATING_AUTHORITY_WEIGHT"] == 'N' ? 'N' : 'Y';
$ratingNormalization = isset($_POST["RATING_NORMALIZATION"]) ? intval($_POST["RATING_NORMALIZATION"]) : 1000;
$sRatingNormalizationType = isset($_POST["RATING_NORMALIZATION_TYPE"]) && $_POST["RATING_NORMALIZATION_TYPE"] == 'auto' ? 'auto' : 'manual';
$ratingCountVote = isset($_POST["RATING_COUNT_VOTE"]) ? intval($_POST["RATING_COUNT_VOTE"]) : 10;
$ratingStartValue = isset($_POST["RATING_START_AUTHORITY"]) ? intval($_POST["RATING_START_AUTHORITY"]) : 3;
$communityLastVisit = isset($_POST["RATING_COMMUNITY_LAST_VISIT"]) && intval($_POST["RATING_COMMUNITY_LAST_VISIT"]) > 0 ? intval($_POST["RATING_COMMUNITY_LAST_VISIT"]) : 90;
$ratingAuthorityDefault = isset($_POST["RATING_AUTHORITY_DEFAULT"]) ? intval($_POST["RATING_AUTHORITY_DEFAULT"]) : 0;

$sRatingSelfVote = isset($_POST["RATING_SELF_VOTE"]) && $_POST["RATING_SELF_VOTE"] == 'Y' ? 'Y' : 'N';
$sRatingAssignType = isset($_POST["RATING_ASSIGN_TYPE"]) && $_POST["RATING_ASSIGN_TYPE"] == 'auto' ? 'auto' : 'manual';
$ratingAssignRatingGroupAdd = isset($_POST["RATING_ASSIGN_RATING_GROUP_ADD"]) ? intval($_POST["RATING_ASSIGN_RATING_GROUP_ADD"]) : 1;
$ratingAssignRatingGroupDelete = isset($_POST["RATING_ASSIGN_RATING_GROUP_DELETE"]) ? intval($_POST["RATING_ASSIGN_RATING_GROUP_DELETE"]) : 1;
$ratingAssignAuthorityGroupAdd = isset($_POST["RATING_ASSIGN_AUTHORITY_GROUP_ADD"]) ? intval($_POST["RATING_ASSIGN_AUTHORITY_GROUP_ADD"]) : 2;
$ratingAssignAuthorityGroupDelete = isset($_POST["RATING_ASSIGN_AUTHORITY_GROUP_DELETE"]) ? intval($_POST["RATING_ASSIGN_AUTHORITY_GROUP_DELETE"]) : 2;

$sRatingVoteShow = isset($_POST["RATING_VOTE_SHOW"]) && $_POST["RATING_VOTE_SHOW"] == 'Y' ? 'Y' : 'N';
$sRatingVoteType = isset($_POST["RATING_VOTE_TYPE"]) && $_POST["RATING_VOTE_TYPE"] == 'like' ? 'like' : 'standart';
$sRatingVoteTemplate = isset($_POST["RATING_VOTE_TEMPLATE"]) && in_array($_POST["RATING_VOTE_TEMPLATE"], Array('like', 'like_graphic', 'standart', 'standart_text'))? $_POST["RATING_VOTE_TEMPLATE"] : ($sRatingVoteType == 'like'?'like': 'standart');
$sRatingTextLikeY = isset($_POST["RATING_TEXT_LIKE_Y"]) ? htmlspecialcharsbx($_POST["RATING_TEXT_LIKE_Y"]) : GetMessage('RATING_SETTINGS_FRM_BUTTON_LIKE_Y_DEFAULT');
$sRatingTextLikeN = isset($_POST["RATING_TEXT_LIKE_N"]) ? htmlspecialcharsbx($_POST["RATING_TEXT_LIKE_N"]) : GetMessage('RATING_SETTINGS_FRM_BUTTON_LIKE_N_DEFAULT');
$sRatingTextLikeD = isset($_POST["RATING_TEXT_LIKE_D"]) ? htmlspecialcharsbx($_POST["RATING_TEXT_LIKE_D"]) : GetMessage('RATING_SETTINGS_FRM_BUTTON_LIKE_D_DEFAULT');

if (isset($_POST["RATING_ASSIGN_RATING_GROUP"]))
	$ratingAssignRatingGroup = intval($_POST["RATING_ASSIGN_RATING_GROUP"]);
else
{
	$ratingAssignRatingGroup = COption::GetOptionString("main", "rating_assign_rating_group", null);
	if ($ratingAssignRatingGroup == null)
	{
		$rsGroup = $DB->Query("SELECT * FROM b_group WHERE STRING_ID='RATING_VOTE'", true);
		$arGroup = $rsGroup->Fetch();
		$ratingAssignRatingGroup = intval($arGroup['ID']);
		COption::SetOptionString("main", "rating_assign_rating_group", $ratingAssignRatingGroup);
	}
}

if (isset($_POST["RATING_ASSIGN_AUTHORITY_GROUP"]))
	$ratingAssignAuthorityGroup = intval($_POST["RATING_ASSIGN_AUTHORITY_GROUP"]);
else
{
	$ratingAssignAuthorityGroup = COption::GetOptionString("main", "rating_assign_authority_group", null);
	if ($ratingAssignAuthorityGroup == null)
	{
		$rsGroup = $DB->Query("SELECT * FROM b_group WHERE STRING_ID='RATING_VOTE_AUTHORITY'", true);
		$arGroup = $rsGroup->Fetch();
		$ratingAssignAuthorityGroup = intval($arGroup['ID']);
		COption::SetOptionString("main", "rating_assign_authority_group", $ratingAssignAuthorityGroup);
	}
}

if ($ratingAssignRatingGroup == 0 && $ratingAssignAuthorityGroup == 0)
	COption::SetOptionString("main", "rating_assign_type", 'manual');

// save settings
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['save']<>"" && check_bitrix_sessid())
{
	if ($sRatingWeightType == 'auto')
	{
		COption::SetOptionString("main", "rating_normalization_type", $sRatingNormalizationType);
		COption::SetOptionString("main", "rating_normalization", $ratingNormalization);
		COption::SetOptionString("main", "rating_count_vote", $ratingCountVote);
		COption::SetOptionString("main", "rating_authority_weight_formula", $sRatingAuthrorityWeight);
		COption::SetOptionString("main", "rating_community_last_visit", $communityLastVisit);
	}
	if ($sRatingWeightType == 'manual')
	{
		CRatings::SetWeight($_POST['CONFIG']);
	}
	if ($sRatingAssignType == 'auto')
	{
		COption::SetOptionString("main", "rating_assign_rating_group_add", $ratingAssignRatingGroupAdd);
		COption::SetOptionString("main", "rating_assign_rating_group_delete", $ratingAssignRatingGroupDelete);
		COption::SetOptionString("main", "rating_assign_authority_group_add", $ratingAssignAuthorityGroupAdd);
		COption::SetOptionString("main", "rating_assign_authority_group_delete", $ratingAssignAuthorityGroupDelete);
		COption::SetOptionString("main", "rating_assign_rating_group", $ratingAssignRatingGroup);
		COption::SetOptionString("main", "rating_assign_authority_group", $ratingAssignAuthorityGroup);
	}

	COption::SetOptionString("main", "rating_self_vote", $sRatingSelfVote);
	COption::SetOptionString("main", "rating_assign_type", $sRatingAssignType);
	COption::SetOptionString("main", "rating_weight_type", $sRatingWeightType);

	COption::SetOptionString("main", "rating_start_authority", $ratingStartValue);

	COption::SetOptionString("main", "rating_vote_show", $sRatingVoteShow);
	COption::SetOptionString("main", "rating_vote_template", $sRatingVoteTemplate);
	COption::SetOptionString("main", "rating_vote_type", $sRatingVoteType);
	if ($sRatingVoteType == 'like')
	{
		COption::SetOptionString("main", "rating_text_like_y", $sRatingTextLikeY);
		COption::SetOptionString("main", "rating_text_like_n", $sRatingTextLikeN);
		COption::SetOptionString("main", "rating_text_like_d", $sRatingTextLikeD);
	}

	CRatings::SetAuthorityRating($ratingId);
	CRatings::SetVoteGroup($_POST['RATING_VOTE_GROUP_ID'], 'R');
	CRatings::SetVoteGroup($_POST['RATING_VOTE_AUTHORITY_GROUP_ID'], 'A');

	if ($ratingAuthorityDefault > 0)
	{
		$arParams = array();

		if ($ratingAuthorityDefault == 1)
			$arParams['DEFAULT_CONFIG_NEW_USER'] = 'Y';

		if ($ratingAuthorityDefault == 2)
			$arParams['DEFAULT_USER_ACTIVE'] = 'Y';

		if ($ratingAuthorityDefault == 3)
		{
			$arParams['DEFAULT_USER_ACTIVE'] = 'Y';
			$arParams['DEFAULT_CONFIG_NEW_USER'] = 'Y';
		}
		CRatings::SetAuthorityDefaultValue($arParams);
	}
	$_SESSION["SESS_ADMIN"]["RATING_CONFIG_SUCCESS"]=array("MESSAGE"=>GetMessage("RATING_CONFIG_SUCCESS"), "TYPE"=>"OK");
}



// if you changed the type of calculation or choose a different rating for the calculation of the authority, change the default values
if(!$bTypeChange)
{
	$ratingId = CRatings::GetAuthorityRating();
	$sRatingWeightType = COption::GetOptionString("main", "rating_weight_type", "auto");
	$sRatingVoteShow = COption::GetOptionString("main", "rating_vote_show", "N");
	$sRatingVoteType = COption::GetOptionString("main", "rating_vote_type", "standart");
	$sRatingVoteTemplate = COption::GetOptionString("main", "rating_vote_template", $sRatingVoteType == 'like'?'like': 'standart');
	if ($sRatingWeightType == 'auto')
	{
		$sRatingNormalizationType = COption::GetOptionString("main", "rating_normalization_type", "auto");
		$ratingNormalization = COption::GetOptionString("main", "rating_normalization", 1000);
		$ratingCountVote = COption::GetOptionString("main", "rating_count_vote", 10);
		$sRatingAuthrorityWeight = COption::GetOptionString("main", "rating_authority_weight_formula", "Y");
		$communityLastVisit = COption::GetOptionString("main", "rating_community_last_visit", "90");
	}
	$ratingStartValue = COption::GetOptionString("main", "rating_start_authority", 3);

	$ratingAssignRatingGroupAdd = COption::GetOptionString("main", "rating_assign_rating_group_add", 1);
	$ratingAssignRatingGroupDelete = COption::GetOptionString("main", "rating_assign_rating_group_delete", 1);
	$ratingAssignAuthorityGroupAdd = COption::GetOptionString("main", "rating_assign_authority_group_add", 2);
	$ratingAssignAuthorityGroupDelete = COption::GetOptionString("main", "rating_assign_authority_group_delete", 2);
	$ratingAssignRatingGroup = COption::GetOptionString("main", "rating_assign_rating_group", 0);
	$ratingAssignAuthorityGroup = COption::GetOptionString("main", "rating_assign_authority_group", 0);
	$sRatingAssignType = COption::GetOptionString("main", "rating_assign_type", 'manual');
	$sRatingSelfVote = COption::GetOptionString("main", "rating_self_vote", 'N');

	$sRatingTextLikeY = COption::GetOptionString("main", "rating_text_like_y", GetMessage("RATING_SETTINGS_FRM_BUTTON_LIKE_Y_DEFAULT"));
	$sRatingTextLikeN = COption::GetOptionString("main", "rating_text_like_n", GetMessage("RATING_SETTINGS_FRM_BUTTON_LIKE_N_DEFAULT"));
	$sRatingTextLikeD = COption::GetOptionString("main", "rating_text_like_d", GetMessage("RATING_SETTINGS_FRM_BUTTON_LIKE_D_DEFAULT"));
}

$APPLICATION->SetTitle(GetMessage("MAIN_RATING_SETTINGS"));
$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ratings.css");
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

// displaying a message on the action taken
if(is_array($_SESSION["SESS_ADMIN"]["RATING_CONFIG_SUCCESS"]))
{
	CAdminMessage::ShowMessage($_SESSION["SESS_ADMIN"]["RATING_CONFIG_SUCCESS"]);
	$_SESSION["SESS_ADMIN"]["RATING_CONFIG_SUCCESS"]=false;
}
if(is_array($_SESSION["SESS_ADMIN"]["RATING_CONFIG_CLEAR_DATA"]))
{
	CAdminMessage::ShowMessage($_SESSION["SESS_ADMIN"]["RATING_CONFIG_CLEAR_DATA"]);
	$_SESSION["SESS_ADMIN"]["RATING_CONFIG_CLEAR_DATA"]=false;
}
if($message)
	echo $message->Show();

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("RATING_SETTINGS_TAB_WEIGHT"), "TITLE"=>''),
	array("DIV" => "edit2", "TAB" => GetMessage("RATING_SETTINGS_TAB_MAIN"), "TITLE"=>''),
	array("DIV" => "edit3", "TAB" => GetMessage("RATING_SETTINGS_TAB_START_VALUE"), "TITLE"=>''),
);
$editTab = new CAdminTabControl("editTab", $aTabs, true, true);
?>
<form name="form1" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANG?>" method="POST">
<input type="hidden" name="ACTION" value="" id="ACTION">
<?
echo bitrix_sessid_post();

$editTab->Begin();
$editTab->BeginNextTab();
?>
	<tr>
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_RATING_WEIGHT_TYPE')?>:</td>
		<td>
			<?=InputType("radio", 'RATING_WEIGHT_TYPE', 'auto', $sRatingWeightType, false, GetMessage('RATING_SETTINGS_FRM_TYPE_AUTO'), "onclick=\"jsTypeChanged('form1')\"");?>
			<?=InputType("radio", 'RATING_WEIGHT_TYPE', 'manual', $sRatingWeightType, false, GetMessage('RATING_SETTINGS_FRM_TYPE_MANUAL'), "onclick=\"jsTypeChanged('form1')\"");?>
		</td>
	</tr>
<?
$arRatingsList = array();
$db_res = CRatings::GetList(array("ID" => "ASC"), array("ENTITY_ID" => "USER"));
while ($res = $db_res->Fetch())
{
	$arRatingsList['reference'][] = "[ ".$res["ID"]." ] ".$res["NAME"];
	$arRatingsList['reference_id'][] = $res["ID"];
}
?>
	<tr>
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_RATING_ID')?>:</td>
		<td><?=SelectBoxFromArray("RATING_ID", $arRatingsList, $ratingId, "", "onChange=\"jsTypeChanged('form1')\"");?></td>
	</tr>
<?
if ($sRatingWeightType == 'auto')
{
	$communitySize = COption::GetOptionString("main", "rating_community_size", 3);
	$voteWeight = COption::GetOptionString("main", "rating_vote_weight", 1);
	?>

		<tr>
			<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_COMMUNITY_SIZE_USER')?>:</td>
			<td><?=($communitySize>0? $communitySize: GetMessage('RATING_SETTINGS_FRM_COMMUNITY_SIZE_ZERO'))?></td>
		</tr>
		<tr>
			<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_RATING_NORMALIZATION_TYPE')?>:</td>
			<td>
				<?=InputType("radio", 'RATING_NORMALIZATION_TYPE', 'auto', $sRatingNormalizationType, false, GetMessage('MAIN_YES'), "onclick=\"jsNormType('hide')\"");?>
				<?=InputType("radio", 'RATING_NORMALIZATION_TYPE', 'manual', $sRatingNormalizationType, false, GetMessage('MAIN_NO'), "onclick=\"jsNormType('show')\"");?>
			</td>
		</tr>
		<tr>
			<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_RATING_NORMALIZATION')?>:</td>
			<td><input type="text" size="2" value="<?=$ratingNormalization?>" name="RATING_NORMALIZATION" id="rating_settings_rating_normalization"> / <?=GetMessage('RATING_SETTINGS_FRM_COMMUNITY_SIZE')?></td>
		</tr>
		<tr>
			<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_RATING_COUNT_VOTE')?>:</td>
			<td><input type="text" size="2" value="<?=$ratingCountVote?>" name="RATING_COUNT_VOTE"> + <?=GetMessage('RATING_SETTINGS_FRM_AUTHORITY')?></td>
		</tr>
		<tr>
			<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_AUTHORITY_WEIGHT')?>:</td>
			<td>
				<?=InputType("radio", 'RATING_AUTHORITY_WEIGHT', 'Y', $sRatingAuthrorityWeight, false, GetMessage('RATING_SETTINGS_FRM_AUTHORITY_WEIGHT_Y'));?>
				<?=InputType("radio", 'RATING_AUTHORITY_WEIGHT', 'N', $sRatingAuthrorityWeight, false, GetMessage('RATING_SETTINGS_FRM_AUTHORITY_WEIGHT_N'));?>
			</td>
		</tr>
	<?
}
if ($sRatingWeightType == 'manual')
{
	$db_res = CRatings::GetRatingValueInfo($ratingId);
	$arValueInfo = $db_res->Fetch();
	?>
		<tr class="heading">
			<td colspan="2"><?=GetMessage('RATING_SETTINGS_CAT_RATING_INFO')?></td>
		</tr>
		<tr>
			<td><?=GetMessage('RATING_SETTINGS_FRM_RATING_INFO_MAX')?>:</td>
			<td><?=round($arValueInfo['MAX'],2)?></td>
		</tr>
		<tr>
			<td><?=GetMessage('RATING_SETTINGS_FRM_RATING_INFO_MIN')?>:</td>
			<td><?=round($arValueInfo['MIN'],2)?></td>
		</tr>
		<tr>
			<td><?=GetMessage('RATING_SETTINGS_FRM_RATING_INFO_AVG')?>:</td>
			<td><?=round($arValueInfo['AVG'],2)?></td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage('RATING_SETTINGS_CAT_CONFIG')?></td>
		</tr>
	<?
	$db_res = CRatings::GetWeightList(array("RATING_TO" => "ASC"), array());
	$conditionCount = 0;
	$conditionMaxCount = 0;
	?>
		<tr>
			<td colspan="2" align="left" class="rating_settings" id="rating_settings_weight">
				<?
				$arCondition = array();
				while ($res = $db_res->Fetch())
				{
					$arCondition[] = $res;
					$conditionMaxCount++;
				}
				foreach($arCondition as $key => $res)
				{
					$conditionCount++;
				?>
					<div id="rating_settings_weight_<?=$conditionCount?>">
						<?if($conditionCount == $conditionMaxCount):?>
							<span><?=GetMessage('RATING_SETTINGS_FRM_FROM')?> <input type="text" size="6" value="<?=($res['RATING_FROM'] == -1000000? 0 : floatVal($res['RATING_FROM']-0.0001))?>" id="rating_settings_weight_<?=$conditionCount?>_from" name="CONFIG[<?=$conditionCount?>][RATING_FROM]" class="rating_settings_from" readonly></span>
						<?else:?>
							<span><?=GetMessage('RATING_SETTINGS_FRM_TO')?> <input type="text" size="7" value="<?=$res['RATING_TO']?>" id="rating_settings_weight_<?=$conditionCount?>_to" name="CONFIG[<?=$conditionCount?>][RATING_TO]" onchange="jsChangeRatingWeight()"></span>
						<?endif;?>
						<span><?=GetMessage('RATING_SETTINGS_FRM_WEIGHT')?> <input type="text" size="6" value="<?=$res['WEIGHT']?>" id="rating_settings_weight_<?=$conditionCount?>_weight" name="CONFIG[<?=$conditionCount?>][WEIGHT]"></span>
						<span><?=GetMessage('RATING_SETTINGS_FRM_COUNT')?> <input type="text" size="3" value="<?=$res['COUNT']?>" id="rating_settings_weight_<?=$conditionCount?>_count" name="CONFIG[<?=$conditionCount?>][COUNT]"></span>
						<?if($conditionCount != $conditionMaxCount):?>
							<a href="#delete" onclick="jsDeleteRatingWeight(<?=$conditionCount?>);return false;"><img src="/bitrix/themes/.default/images/cross.gif" title="<?=GetMessage('RATING_SETTINGS_FRM_DELETE')?>" border="0" align="absmiddle"></a>
						<?endif;?>
					</div>
				<?}?>
				<div id="rating_settings_weight_add" rel="<?=$conditionMaxCount?>"><span class="settings_add"><a href="#add" onclick="jsAddRatingWeight();return false;"><?=GetMessage('RATING_SETTINGS_FRM_ADD')?></a></span></div>
			</td>
		</tr>
	<?
}
?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage('RATING_SETTINGS_FRM_BUTTON_TYPE')?></td>
	</tr>
	<tr>
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_RATING_VOTE_SHOW')?>:</td>
		<td>
			<?=InputType("radio", 'RATING_VOTE_SHOW', 'Y', $sRatingVoteShow, false, GetMessage('MAIN_YES'));?>
			<?=InputType("radio", 'RATING_VOTE_SHOW', 'N', $sRatingVoteShow, false, GetMessage('MAIN_NO'));?>
		</td>
	</tr>
	<tr>
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_RATING_VOTE_TYPE')?>:</td>
		<td>
			<?=InputType("radio", 'RATING_VOTE_TYPE', 'like', $sRatingVoteType, false, GetMessage('RATING_SETTINGS_FRM_V_TYPE_LIKE'), "onclick=\"jsVoteTypeChanged('like')\"");?>
			<?=InputType("radio", 'RATING_VOTE_TYPE', 'standart', $sRatingVoteType, false, GetMessage('RATING_SETTINGS_FRM_V_TYPE_STANDART'), "onclick=\"jsVoteTypeChanged('standart')\"");?>
		</td>
	</tr>
	<tr>
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_RATING_VOTE_TEMPLATE')?>:</td>
		<td>
			<div id="rating_vote_type_like">
			<?=InputType("radio", 'RATING_VOTE_TEMPLATE', 'like', $sRatingVoteTemplate, false, '<span style="display:inline-block;width: 120px;height: 16px"><img src="/bitrix/images/main/rating/'.LANGUAGE_ID.'/like.png" title="'.GetMessage('RATING_SETTINGS_FRM_V_TPL_TEXT').'" style="position: absolute;"/></span>', "", "rating_vote_template_like");?>
			<?=InputType("radio", 'RATING_VOTE_TEMPLATE', 'like_graphic', $sRatingVoteTemplate, false, '<span style="display:inline-block;width: 118px;height: 16px"><img src="/bitrix/images/main/rating/'.LANGUAGE_ID.'/like_graphic.png" title="'.GetMessage('RATING_SETTINGS_FRM_V_TPL_GRAPHIC').'" style="position: absolute;"/></span>', "", "rating_vote_template_like_graphic");?>
			</div>
			<div id="rating_vote_type_standart">
			<?=InputType("radio", 'RATING_VOTE_TEMPLATE', 'standart_text', $sRatingVoteTemplate, false, '<span style="display:inline-block;width: 172px;height: 16px"><img src="/bitrix/images/main/rating/'.LANGUAGE_ID.'/standart_text.png" title="'.GetMessage('RATING_SETTINGS_FRM_V_TPL_TEXT').'" style="position: absolute;"/></span>', "", "rating_vote_template_standart_text");?>
			<?=InputType("radio", 'RATING_VOTE_TEMPLATE', 'standart', $sRatingVoteTemplate, false, '<span style="display:inline-block;width: 80px;height: 16px"><img src="/bitrix/images/main/rating/'.LANGUAGE_ID.'/standart.png" title="'.GetMessage('RATING_SETTINGS_FRM_V_TPL_GRAPHIC').'" style="position: absolute;"/></span>', "", "rating_vote_template_standart");?>
			</div>
		</td>
	</tr>
	<tr id="rating_vote_type_like_text_1">
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_BUTTON_LIKE_Y')?>:</td>
		<td><input type="text" value="<?=$sRatingTextLikeY?>" name="RATING_TEXT_LIKE_Y"></td>
	</tr>
	<tr id="rating_vote_type_like_text_2">
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_BUTTON_LIKE_N')?>:</td>
		<td><input type="text" value="<?=$sRatingTextLikeN?>" name="RATING_TEXT_LIKE_N"></td>
	</tr>
	<tr id="rating_vote_type_like_text_3">
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_BUTTON_LIKE_D')?>:</td>
		<td><input type="text" value="<?=$sRatingTextLikeD?>" name="RATING_TEXT_LIKE_D"></td>
	</tr>
	<tr id="rating_vote_type_like_text_3">
		<td colspan="2">
		<?=BeginNote()?>
			<?=GetMessage('RATING_SETTINGS_FRM_CACHE')?> <a href="/bitrix/admin/cache.php?lang=<?=LANGUAGE_ID?>&tabControl_active_tab=fedit2"><?=GetMessage('RATING_SETTINGS_FRM_CACHE_LINK')?></a>.
		<?=EndNote()?>
		</td>
	</tr>
<?
$editTab->BeginNextTab();
?>
	<tr>
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_SELF_VOTE')?>:</td>
		<td>
			<?=InputType("radio", 'RATING_SELF_VOTE', 'Y', $sRatingSelfVote, false, GetMessage('MAIN_YES'));?>
			<?=InputType("radio", 'RATING_SELF_VOTE', 'N', $sRatingSelfVote, false, GetMessage('MAIN_NO'));?>
		</td>
	</tr>
	<tr>
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_COMMUNITY_LAST_VISIT')?>:</td>
		<td><input type="text" size="2" value="<?=$communityLastVisit?>" name="RATING_COMMUNITY_LAST_VISIT"></td>
	</tr>
	<tr>
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_AUTO_ASSIGN')?>:</td>
		<td>
			<?=InputType("radio", 'RATING_ASSIGN_TYPE', 'auto', $sRatingAssignType, false, GetMessage('MAIN_YES'), "onclick=\"jsAutoAssign('show')\"");?>
			<?=InputType("radio", 'RATING_ASSIGN_TYPE', 'manual', $sRatingAssignType, false, GetMessage('MAIN_NO'), "onclick=\"jsAutoAssign('hide')\"");?>
		</td>
	</tr>
<?
	$arRatingVoteGroupIdList = Array();
	$arRatingVoteGroupIdList2 = Array();
	$arRatingVoteGroupIdList2["REFERENCE"][] = "";
	$arRatingVoteGroupIdList2["REFERENCE_ID"][] = 0;
	$rsGroups = CGroup::GetList($by="c_sort", $order="asc", $filter=array());
	while($arGroup = $rsGroups->Fetch())
	{
		if ($arGroup['ID'] == 2)
			continue;

		$arRatingVoteGroupIdList["REFERENCE"][] = $arGroup["NAME"];
		$arRatingVoteGroupIdList["REFERENCE_ID"][] = $arGroup["ID"];

		if ($arGroup['ID'] == 1)
			continue;

		$arRatingVoteGroupIdList2["REFERENCE"][] = $arGroup["NAME"];
		$arRatingVoteGroupIdList2["REFERENCE_ID"][] = $arGroup["ID"];
	}
?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage('RATING_SETTINGS_FRM_RATING')?></td>
	</tr>
<?
$arRatingVoteGroupID = array();
$rsGroups = CRatings::GetVoteGroup('R');
while($arGroup = $rsGroups->Fetch())
	$arRatingVoteGroupID[] = $arGroup["GROUP_ID"];


?>
	<tr>
		<td width="50%" valign="top"><?=GetMessage('RATING_SETTINGS_FRM_RATING_VOTE_GROUP_ID')?>:</td>
		<td><?=SelectBoxMFromArray("RATING_VOTE_GROUP_ID[]", $arRatingVoteGroupIdList, $arRatingVoteGroupID, "", true, 5);?></td>
	</tr>
		<tr id="rating_settings_auto_assign_1_1">
			<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_AUTO_ASSIGN')?>:</td>
			<td><?=SelectBoxFromArray("RATING_ASSIGN_RATING_GROUP", $arRatingVoteGroupIdList2, $ratingAssignRatingGroup);?></td>
		</tr>
		<tr id="rating_settings_auto_assign_1_2">
			<td width="50%"></td>
			<td>
				<?=(COption::GetOptionString("main", "rating_weight_type", "auto") == "auto"? GetMessage('RATING_SETTINGS_FRM_ASSIGN_VOTE_1') : GetMessage('RATING_SETTINGS_FRM_ASSIGN_AUTHORITY'))?>: <input name="RATING_ASSIGN_RATING_GROUP_ADD" value="<?=$ratingAssignRatingGroupAdd?>" style="width:45px;" type="text"><br> <?=GetMessage('RATING_SETTINGS_FRM_ASSIGN_VOTE_2')?>: <input name="RATING_ASSIGN_RATING_GROUP_DELETE" value="<?=$ratingAssignRatingGroupDelete?>" style="width:45px;" type="text">
			</td>
		</tr>

	<?




$arRatingVoteAuthorityGroupID = array();
$rsGroups = CRatings::GetVoteGroup('A');
while($arGroup = $rsGroups -> Fetch())
	$arRatingVoteAuthorityGroupID[] = $arGroup["GROUP_ID"];
?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage('RATING_SETTINGS_FRM_AUTHORITY')?></td>
	</tr>
	<tr>
		<td width="50%" valign="top"><?=GetMessage('RATING_SETTINGS_FRM_RATING_VOTE_AUTHORITY_GROUP_ID')?></td>
		<td><?=SelectBoxMFromArray("RATING_VOTE_AUTHORITY_GROUP_ID[]", $arRatingVoteGroupIdList, $arRatingVoteAuthorityGroupID, "", true, 5);?></td>
	</tr>
	<tr id="rating_settings_auto_assign_2_1">
			<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_AUTO_ASSIGN')?>:</td>
			<td><?=SelectBoxFromArray("RATING_ASSIGN_AUTHORITY_GROUP", $arRatingVoteGroupIdList2, $ratingAssignAuthorityGroup);?></td>
		</tr>
	<tr id="rating_settings_auto_assign_2_2">
			<td width="50%"></td>
			<td> <?=(COption::GetOptionString("main", "rating_weight_type", "auto") == "auto"? GetMessage('RATING_SETTINGS_FRM_ASSIGN_VOTE_1') : GetMessage('RATING_SETTINGS_FRM_ASSIGN_AUTHORITY'))?><input name="RATING_ASSIGN_AUTHORITY_GROUP_ADD" value="<?=$ratingAssignAuthorityGroupAdd?>" style="width:45px;" type="text"><br> <?=GetMessage('RATING_SETTINGS_FRM_ASSIGN_VOTE_2')?>: <input name="RATING_ASSIGN_AUTHORITY_GROUP_DELETE" value="<?=$ratingAssignAuthorityGroupDelete?>" style="width:45px;" type="text"></td>
		</tr>
<?
$editTab->BeginNextTab();
?>
	<tr>
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_RATING_START_AUTHORITY')?>:</td>
		<td><input type="text" size="2" value="<?=$ratingStartValue?>" name="RATING_START_AUTHORITY"> <?=($sRatingWeightType == 'auto'? 'x '.GetMessage('RATING_SETTINGS_FRM_RATING_NORMALIZATION'):'')?></td>
	</tr>
	<tr>
		<td width="50%" valign="top" style="padding-top: 9px;"><?=GetMessage('RATING_SETTINGS_FRM_DEF_VALUE')?>:</td>
		<td>
			<?=InputType("radio", 'RATING_AUTHORITY_DEFAULT', '1', '', false, GetMessage('RATING_SETTINGS_FRM_DEF_VALUE_1'));?>
			<?
			if (IsModuleInstalled("forum"))
			{
				echo '<br>'.InputType("radio", 'RATING_AUTHORITY_DEFAULT', '2', '', false, GetMessage('RATING_SETTINGS_FRM_DEF_VALUE_2'));
				echo '<br>'.InputType("radio", 'RATING_AUTHORITY_DEFAULT', '3', '', false, GetMessage('RATING_SETTINGS_FRM_DEF_VALUE_3'));
			}
			?>
			<br><?=InputType("radio", 'RATING_AUTHORITY_DEFAULT', '0', '0', false, GetMessage('RATING_SETTINGS_FRM_DEF_VALUE_4'));?>
		</td>
	</tr>
<?if($USER->IsAdmin()):?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage('RATING_SETTINGS_FRM_CLEAR')?></td>
	</tr>
	<tr>
		<td width="50%"><?=GetMessage('RATING_SETTINGS_FRM_RATING_CLEAR_DATA')?>:</td>
		<td><input type="checkbox" name="CLEAR_DATA" value="Y" onclick="return confirm('<?=GetMessage("RATING_SETTINGS_FRM_RATING_CLEAR_DATA_CONFIRM")?>')? true: false"></td>
	</tr>
<?
endif;
$editTab->Buttons();
?>
	<input type="submit" accesskey="x" name="save" value="<?=GetMessage("RATING_SETTINGS_BUTTON_SAVE")?>" class="adm-btn-save">
	<input type="button" name="cancel" value="<?=GetMessage("RATING_SETTINGS_BUTTON_RESET")?>" title="<?=GetMessage("RATING_SETTINGS_BUTTON_RESET_TITLE")?>" onclick="window.location='<?=(strpos($_REQUEST["addurl"], '/') === 0? htmlspecialcharsbx(CUtil::addslashes($_REQUEST["addurl"])):"rating_index.php?lang=".LANG)?>'">
<?
$editTab->End();
?>
</form>
<script type="text/javascript">
jsVoteTypeChanged('<?=$sRatingVoteType?>');
<?if($sRatingAssignType=="manual"):?>
	jsAutoAssign('hide');
<?endif;?>
<?if($sRatingNormalizationType=="auto"):?>
	jsNormType('hide');
<?endif;?>
<?if ($sRatingWeightType == 'manual'):?>
	var new_weight_config_to = 50;
	var new_weight_config_weight = 1;
	var new_weight_config_count = 10;
	var div_settings_next = <?=$conditionMaxCount?>;

	function jsAddRatingWeight()
	{
		// Variable definition references to DOM objects
		var div_add_button = BX('rating_settings_weight_add');
		var div_settings_end = BX('rating_settings_weight_<?=$conditionMaxCount?>');
		var div_settings_last = parseInt(div_add_button.getAttribute('rel'));
		div_settings_next = div_settings_next+1;

		if (div_settings_last != 0)
		{
			var div_settings_to = parseFloat(BX('rating_settings_weight_'+div_settings_last+(div_settings_last == <?=$conditionMaxCount?> ? '_from' : '_to')).value);
			var div_settings_weight = parseFloat(BX('rating_settings_weight_'+div_settings_last+'_weight').value);
			var div_settings_count = parseFloat(BX('rating_settings_weight_'+div_settings_last+'_count').value);
		}
		else
		{
			var div_settings_to = 0;
			var div_settings_weight = new_weight_config_weight;
			var div_settings_count = new_weight_config_count;
			div_settings_end = div_add_button;
		}

		// iterate value if it is not first condition in list
		if (div_settings_last != <?=$conditionMaxCount?>)
		{
			div_settings_to = div_settings_to + new_weight_config_to;
			div_settings_weight = div_settings_weight + new_weight_config_weight;
			div_settings_count = div_settings_count + new_weight_config_count;
		}
		else
		{
			div_settings_to = 0;
			div_settings_weight = 0;
			div_settings_count = 0;
		}

		div_settings_to = isNaN(div_settings_to)? 0: div_settings_to;
		div_settings_weight = isNaN(div_settings_weight)? 0: div_settings_weight;
		div_settings_count = isNaN(div_settings_count)? 0: div_settings_count;

		// Create new DOM object
		var el=document.createElement('div');
		el.id='rating_settings_weight_'+div_settings_next;
		el.innerHTML = '<span><?=GetMessage('RATING_SETTINGS_FRM_TO')?> <input type="text" size="7" value="'+div_settings_to+'" id="rating_settings_weight_'+div_settings_next+'_to" name="CONFIG['+div_settings_next+'][RATING_TO]" onchange="jsChangeRatingWeight()"></span>\
						<span><?=GetMessage('RATING_SETTINGS_FRM_WEIGHT')?> <input type="text" size="7" value="'+div_settings_weight+'" id="rating_settings_weight_'+div_settings_next+'_weight" name="CONFIG['+div_settings_next+'][WEIGHT]"></span>\
						<span><?=GetMessage('RATING_SETTINGS_FRM_COUNT')?> <input type="text" size="6" value="'+div_settings_count+'" id="rating_settings_weight_'+div_settings_next+'_count" name="CONFIG['+div_settings_next+'][COUNT]"></span>\
						<a href="#delete" onclick="jsDeleteRatingWeight('+div_settings_next+');return false;"><img src="/bitrix/themes/.default/images/cross.gif" title="<?=GetMessage('RATING_SETTINGS_FRM_DELETE')?>" border="0" align="absmiddle"></a>';
		BX('rating_settings_weight').insertBefore(el, div_settings_end);

		div_add_button.setAttribute('rel', div_settings_next);

		// define "from" config variable
		div_settings_end_from 	= BX('rating_settings_weight_<?=$conditionMaxCount?>_from');
		div_settings_end_weight = BX('rating_settings_weight_<?=$conditionMaxCount?>_weight');
		div_settings_end_count	= BX('rating_settings_weight_<?=$conditionMaxCount?>_count');

		div_settings_end_from.value = div_settings_to;

		// replace values of variables only if previous value is more
		if (div_settings_end_weight.value < div_settings_weight + new_weight_config_weight)
			div_settings_end_weight.value = div_settings_weight + new_weight_config_weight;
		if (div_settings_end_count.value < div_settings_count + new_weight_config_count)
			div_settings_end_count.value = div_settings_count + new_weight_config_count;
	}

	function jsDeleteRatingWeight(num)
	{
		var last_item = parseInt(BX('rating_settings_weight_add').getAttribute('rel'));

		BX.remove(BX('rating_settings_weight_'+num));

		// iterate through available configs, that would get last config
		while( last_item > 0 )
		{
			if (BX('rating_settings_weight_'+last_item) !== null && last_item != <?=$conditionMaxCount?>)
				break;
			last_item--;
		}
		if (last_item == 0)
			last_item = <?=$conditionMaxCount?>;

		BX('rating_settings_weight_add').setAttribute('rel', last_item);
		// finding maximum weight
		jsChangeRatingWeight();
	}

	function jsChangeRatingWeight()
	{
		var max_weight = 0;
		var input_end = BX('rating_settings_weight_<?=$conditionMaxCount?>_from');
		var last_item = parseInt(BX('rating_settings_weight_add').getAttribute('rel'));
		// iterate through available configs, that would get max weight
		while( last_item > 1 )
		{
			if (BX('rating_settings_weight_'+last_item+'_to') !== null)
			{
				current_item = parseFloat(BX('rating_settings_weight_'+last_item+'_to').value);
				if (max_weight < current_item )
					max_weight = current_item;
			}
			last_item--;
		}
		input_end.value = max_weight;
	}
<?endif;?>
	function jsTypeChanged(form_id)
	{
		var _form = document.forms[form_id];
		var _flag = document.getElementById('ACTION');
		if(_form)
		{
			_flag.value = 'type_changed';
			_form.submit();
		}
	}
	function jsAutoAssign(flag)
	{
		if (flag == 'show')
		{
			BX('rating_settings_auto_assign_1_1').style.display="table-row";
			BX('rating_settings_auto_assign_1_2').style.display="table-row";
			BX('rating_settings_auto_assign_2_1').style.display="table-row";
			BX('rating_settings_auto_assign_2_2').style.display="table-row";
		}
		else
		{
			BX('rating_settings_auto_assign_1_1').style.display="none";
			BX('rating_settings_auto_assign_1_2').style.display="none";
			BX('rating_settings_auto_assign_2_1').style.display="none";
			BX('rating_settings_auto_assign_2_2').style.display="none";
		}
	}
	function jsVoteTypeChanged(type)
	{
		if (type == 'like')
		{
			BX('rating_vote_type_like').style.display="block";
			BX('rating_vote_type_standart').style.display="none";
			BX('rating_vote_type_like_text_1').style.display="table-row";
			BX('rating_vote_type_like_text_2').style.display="table-row";
			BX('rating_vote_type_like_text_3').style.display="table-row";
			if ('<?=$sRatingVoteType?>' == 'like')
				BX('rating_vote_template_<?=$sRatingVoteTemplate?>').checked = true;
			else
				BX('rating_vote_template_like').checked = true;
		}
		else
		{
			BX('rating_vote_type_like').style.display="none";
			BX('rating_vote_type_standart').style.display="block";
			BX('rating_vote_type_like_text_1').style.display="none";
			BX('rating_vote_type_like_text_2').style.display="none";
			BX('rating_vote_type_like_text_3').style.display="none";
			if ('<?=$sRatingVoteType?>' == 'standart')
				BX('rating_vote_template_<?=$sRatingVoteTemplate?>').checked = true;
			else
				BX('rating_vote_template_standart_text').checked = true;
		}
	}
	function jsNormType(flag)
	{
		bxNormalize = BX('rating_settings_rating_normalization');
		if (bxNormalize == null)
			return false;
		if (flag == 'hide')
		{
			bxNormalize.readOnly=true;
			bxNormalize.style.backgroundColor = "#e8e8e8";
		}
		else
		{
			bxNormalize.readOnly=false;
			bxNormalize.style.backgroundColor = "";
		}
	}
</script>
<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>