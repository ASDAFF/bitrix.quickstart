<?
define("NO_KEEP_STATISTIC", true);
define("BX_STATISTIC_BUFFER_USED", false);
define("NO_LANG_FILES", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_PUBLIC_TOOLS", true);

$site_id = isset($_POST["site"]) ? trim($_POST["site"]) : "";
$site_id = substr(preg_replace("/[^a-z0-9_]/i", "", $site_id), 0, 2);
$user_id = intval($_POST["USER_ID"]);
$arFriendID = $_POST["FRIEND_ID"];

define("SITE_ID", $site_id);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/bx_root.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);

$rsSite = CSite::GetByID($site_id);
if ($arSite = $rsSite->Fetch())
	define("LANGUAGE_ID", $arSite["LANGUAGE_ID"]);
else
	define("LANGUAGE_ID", "en");

__IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID."/ajax.php");

if(CModule::IncludeModule("compression"))
	CCompress::Disable2048Spaces();

if (!CModule::IncludeModule("socialnetwork"))
{
	echo CUtil::PhpToJsObject(Array('ERROR' => 'SONET_MODULE_NOT_INSTALLED'));
	die();
}

if (!$GLOBALS["USER"]->IsAuthorized())
{
	echo CUtil::PhpToJsObject(Array('ERROR' => 'CURRENT_USER_NOT_AUTH'));
	die();
}

if ($user_id <= 0)
{
	echo CUtil::PhpToJsObject(Array('ERROR' => 'USER_ID_NOT_DEFINED'));
	die();
}
else
{
	$rsUser = CUser::GetByID($user_id);
	$arUser = $rsUser->Fetch();
	if (!$arUser)
	{
		echo CUtil::PhpToJsObject(Array('ERROR' => 'USER_ID_NOT_DEFINED'));
		die();
	}
}

if (!is_array($arFriendID) || count($arFriendID) <= 0)
{
	echo CUtil::PhpToJsObject(Array('ERROR' => 'FRIEND_ID_NOT_DEFINED'));
	die();
}

if (check_bitrix_sessid())
{
	$arCurrentUserPerms = CSocNetUserPerms::InitUserPerms($GLOBALS["USER"]->GetID(), $arUser["ID"], CSocNetUser::IsCurrentUserModuleAdmin());	
	if (!$arCurrentUserPerms || !$arCurrentUserPerms["IsCurrentUser"])
	{
		echo CUtil::PhpToJsObject(Array('ERROR' => 'USER_NO_PERMS'));
		die();
	}

	if (in_array($_POST['ACTION'], array('BAN', 'UNBAN', 'EX')))
	{
		if ($_POST['ACTION'] == 'BAN')
			$relation_type = SONET_RELATIONS_FRIEND;	
		elseif ($_POST['ACTION'] == 'UNBAN')
			$relation_type = SONET_RELATIONS_BAN;
		elseif ($_POST['ACTION'] == 'EX')
			$relation_type = SONET_RELATIONS_FRIEND;

		$arRelationID = array();
		$arRelationUserID = array();
		$rsRelation = CSocNetUserRelations::GetRelatedUsers($user_id, $relation_type);
		while($arRelation = $rsRelation->Fetch())
		{
			$arRelationID[] = $arRelation["ID"];
			$arRelationUserID[] = $arRelation[(($user_id == $arRelation["FIRST_USER_ID"]) ? "SECOND" : "FIRST")."_USER_ID"];
		}

		if (count(array_diff($arFriendID, $arRelationUserID)) > 0)
		{
			echo CUtil::PhpToJsObject(Array('ERROR' => 'FRIEND_ID_INCORRECT_2'));
			die();
		}

		if ($_POST['ACTION'] == "BAN")
		{
			foreach($arRelationUserID as $relation_user_id)
			{
				if (!CSocNetUserRelations::BanUser($user_id, $relation_user_id))
				{
					echo CUtil::PhpToJsObject(Array('ERROR' => 'USER_ACTION_FAILED: '.(($e = $APPLICATION->GetException()) ? $e->GetString() : "")));
					die();
				}
			}
		}
		else
		{
			foreach($arRelationID as $relation_id)
			{
				if (
					($_POST['ACTION'] == "EX" && !CSocNetUserRelations::Delete($relation_id))
					|| ($_POST['ACTION'] == "UNBAN" && !CSocNetUserRelations::UnBanMember($user_id, $relation_id))
				)
				{
					echo CUtil::PhpToJsObject(Array('ERROR' => 'USER_ACTION_FAILED: '.(($e = $APPLICATION->GetException()) ? $e->GetString() : "")));
					die();
				}
			}
		}
	}

	echo CUtil::PhpToJsObject(Array('SUCCESS' => 'Y'));
}
else
	echo CUtil::PhpToJsObject(Array('ERROR' => 'SESSION_ERROR'));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>