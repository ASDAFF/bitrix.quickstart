<?
define("PUBLIC_AJAX_MODE", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("NO_AGENT_CHECK", true);
define("DisableEventsCheck", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($_POST['RATING_VOTE_LIST'] == 'Y'
	&& strlen($_POST['RATING_VOTE_TYPE_ID']) > 0
	&& intval($_POST['RATING_VOTE_ENTITY_ID']) > 0 && check_bitrix_sessid())
{
	$APPLICATION->RestartBuffer();

	$ar = Array(
		"ENTITY_TYPE_ID" => $_POST['RATING_VOTE_TYPE_ID'],
		"ENTITY_ID" 	 => intval($_POST['RATING_VOTE_ENTITY_ID']),
		"LIST_PAGE" 	 => intval($_POST['RATING_VOTE_LIST_PAGE']),
		"LIST_LIMIT" 	 => 20,
		"LIST_TYPE" 	 => isset($_POST['RATING_VOTE_LIST_TYPE']) && $_POST['RATING_VOTE_LIST_TYPE'] == 'minus'? 'minus': 'plus',
	);
	$arResult = CRatings::GetRatingVoteList($ar);
	if (!isset($_POST["PATH_TO_USER_PROFILE"]) || strlen($_POST["PATH_TO_USER_PROFILE"])==0)
		$_POST["PATH_TO_USER_PROFILE"] = '/people/user/#USER_ID#/';

	$arVoteList = Array();
	$arVoteList['items_all'] = $arResult['items_all'];
	$arVoteList['items_page'] = $arResult['items_page'];
	foreach($arResult['items'] as $key => $value)
	{
		$arVoteList['items'][] = Array(
			'USER_ID' => $value['ID'],
			'VOTE_VALUE' => $value['VOTE_VALUE'],
			'PHOTO' => $value['PHOTO'],
			'FULL_NAME' => $value['FULL_NAME'],
			'URL' => CUtil::JSEscape(CComponentEngine::MakePathFromTemplate($_POST["PATH_TO_USER_PROFILE"], array("UID" => $value["USER_ID"], "user_id" => $value["USER_ID"], "USER_ID" => $value["USER_ID"])))
		);
	}

	Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
	echo CUtil::PhpToJsObject($arVoteList);
	die();
}
else if ($_POST['RATING_VOTE'] == 'Y'
	&& strlen($_POST['RATING_VOTE_TYPE_ID']) > 0
	&& intval($_POST['RATING_VOTE_ENTITY_ID']) > 0 && check_bitrix_sessid())
{
	$arParams['ENTITY_TYPE_ID'] = $_POST['RATING_VOTE_TYPE_ID'];
	$arParams['ENTITY_ID'] = intval($_POST['RATING_VOTE_ENTITY_ID']);
	$arComponentVoteResult  = CRatings::GetRatingVoteResult($_POST['RATING_VOTE_TYPE_ID'], intval($_POST['RATING_VOTE_ENTITY_ID']));
	if (!empty($arComponentVoteResult))
	{
		$arParams['TOTAL_VALUE'] = $arComponentVoteResult['TOTAL_VALUE'];
		$arParams['TOTAL_VOTES'] = $arComponentVoteResult['TOTAL_VOTES'];
		$arParams['TOTAL_POSITIVE_VOTES'] = $arComponentVoteResult['TOTAL_POSITIVE_VOTES'];
		$arParams['TOTAL_NEGATIVE_VOTES'] = $arComponentVoteResult['TOTAL_NEGATIVE_VOTES'];
		$arParams['USER_HAS_VOTED'] = $arComponentVoteResult['USER_HAS_VOTED'];
		$arParams['USER_VOTE'] = $arComponentVoteResult['USER_VOTE'];
	}
	else
	{
		$arParams['TOTAL_VALUE'] = 0;
		$arParams['TOTAL_VOTES'] = 0;
		$arParams['TOTAL_POSITIVE_VOTES'] = 0;
		$arParams['TOTAL_NEGATIVE_VOTES'] = 0;
		$arParams['USER_HAS_VOTED'] = 'N';
		$arParams['USER_VOTE'] = '0';
	}
	$arAllowVote = CRatings::CheckAllowVote($arParams);
	if ($arAllowVote['RESULT'])
	{
		$APPLICATION->RestartBuffer();
		$action = 'list';
		if ($_POST['RATING_VOTE_ACTION'] == 'plus'
		|| $_POST['RATING_VOTE_ACTION'] == 'minus')
		{
			$arAdd = array(
				"ENTITY_TYPE_ID" => $_POST['RATING_VOTE_TYPE_ID'],
				"ENTITY_ID" 	 => intval($_POST['RATING_VOTE_ENTITY_ID']),
				"VALUE" 		 	=> $_POST['RATING_VOTE_ACTION'] == 'plus' ? 1 : -1,
				"USER_IP" 		 => $_SERVER['REMOTE_ADDR'],
				"USER_ID" 		 => $USER->GetId()
			);
			CRatings::AddRatingVote($arAdd);
			$action = $_POST['RATING_VOTE_ACTION'];
		}
		else if ($_POST['RATING_VOTE_ACTION'] == 'cancel')
		{
			$arCancel = array(
				"ENTITY_TYPE_ID" => $_POST['RATING_VOTE_TYPE_ID'],
				"ENTITY_ID" 	 => intval($_POST['RATING_VOTE_ENTITY_ID']),
				"USER_ID" 		 => $USER->GetId(),
			);
			CRatings::CancelRatingVote($arCancel);
			$action = $_POST['RATING_VOTE_ACTION'];
		}
		$ar = Array(
			"ENTITY_TYPE_ID" => $_POST['RATING_VOTE_TYPE_ID'],
			"ENTITY_ID" 	 => intval($_POST['RATING_VOTE_ENTITY_ID']),
			"LIST_LIMIT" 	 => 0,
			"LIST_TYPE" 	 => isset($_POST['RATING_VOTE_ACTION']) && $_POST['RATING_VOTE_ACTION'] == 'minus'? 'minus': 'plus',
		);
		$arVoteList = CRatings::GetRatingVoteList($ar);
		if ($_POST['RATING_RESULT'] == 'Y') 
		{
			$arVoteResult = GetVoteResult($_POST['RATING_VOTE_TYPE_ID'], $_POST['RATING_VOTE_ENTITY_ID']);
			$arVoteList = array_merge($arVoteList, $arVoteResult);
		}
		$arVoteList['action'] = $action;
		Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
		echo CUtil::PhpToJsObject($arVoteList);
	}
	die();
} 
else if ($_POST['RATING_RESULT'] == 'Y'
	&& strlen($_POST['RATING_VOTE_TYPE_ID']) > 0
	&& intval($_POST['RATING_VOTE_ENTITY_ID']) > 0 && check_bitrix_sessid())
{
	$arJSON = GetVoteResult($_POST['RATING_VOTE_TYPE_ID'], $_POST['RATING_VOTE_ENTITY_ID']);
	Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
	echo CUtil::PhpToJsObject($arJSON);
	die();
}

function GetVoteResult($entityTypeId, $entityId)
{
	global $USER;
	$entityId 		= intval($entityId);
	$userId 			= intval($USER->GetId());

	$arRatingResult = CRatings::GetRatingVoteResult($entityTypeId, $entityId, $userId);
	if (empty($arRatingResult)) 
	{
		$arRatingResult['USER_HAS_VOTED'] = $USER->IsAuthorized() ? "N" : "Y";
		$arRatingResult['USER_VOTE'] = 0;
		$arRatingResult['TOTAL_VALUE'] = 0;
		$arRatingResult['TOTAL_VOTES'] = 0;
		$arRatingResult['TOTAL_POSITIVE_VOTES'] = 0;
		$arRatingResult['TOTAL_NEGATIVE_VOTES'] = 0;
	}

	$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/lang/".LANGUAGE_ID."/vote.ajax.php");
	include_once($path);
	$resultStatus = $arRatingResult['TOTAL_VALUE'] < 0 ? 'minus' : 'plus';
	$resultTitle  = sprintf($MESS["RATING_COMPONENT_DESC"], $arRatingResult['TOTAL_VOTES'], $arRatingResult['TOTAL_POSITIVE_VOTES'], $arRatingResult['TOTAL_NEGATIVE_VOTES']);



	return Array(
		'resultValue' => intval($arRatingResult["TOTAL_POSITIVE_VOTES"]) - intval($arRatingResult["TOTAL_NEGATIVE_VOTES"]),//$arRatingResult['TOTAL_VALUE'],
		'resultVotes' => $arRatingResult['TOTAL_VOTES'],
		'resultPositiveVotes' => $arRatingResult['TOTAL_POSITIVE_VOTES'],
		'resultNegativeVotes' => $arRatingResult['TOTAL_NEGATIVE_VOTES'],		
		'resultStatus' => $resultStatus,
		'resultTitle' => $resultTitle,
	);
}


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>