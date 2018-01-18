<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $DB, $USER, $APPLICATION;

CUtil::DecodeUriComponent($_POST);

function getMessageChat($auctionId, $productId, $state)
{
	global $DB;
	
	$lastId = $state;
	$data = '';
	
	$strSql = "SELECT * FROM b_auction WHERE auction_id=".$DB->ForSQL($auctionId)." AND product_id=".$DB->ForSQL($productId)." AND id > ".$DB->ForSQL($state)." ORDER BY id asc";
	$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	while ($arRes = $dbRes->Fetch())
	{
		$lastId = $arRes["id"];

		$arTime = ParseDateTime($arRes["date"], CLang::GetDateFormat("FULL"));
		$date = $arTime["HH"].":".$arTime["MI"].":".$arTime["SS"];
		
		$data .= "<div class='auction-chat-item'><span class='auction-chat-items-date'>[".$date."]</span><span class='auction-chat-items-name'>".htmlspecialcharsbx($arRes["user_name"]).":</span><span class='auction-chat-items-event'>".htmlspecialcharsbx($arRes["message"])."</span></div>";
	}
	
	return array('state' => $lastId, 'data' => $data);
}


/*
200 - ok update
300 - ok add new message
400 - error other
404 - error add
405 - error auction id, product_id
*/

$auctionId = 0;
$productId = 0;
$state = 0;
$message = '';
$data = '';
$status = 400;

if (is_set($_POST["auctionId"]))
	$auctionId = intval($_POST["auctionId"]);

if (is_set($_POST["productId"]))
	$productId = intval($_POST["productId"]);

if (is_set($_POST["state"]))
	$state = intval($_POST["state"]);

if (is_set($_POST["message"]))
	$message = trim($_POST["message"]);

if ($auctionId > 0 && $productId > 0)
{
	CModule::IncludeModule("iblock");
	$res = CIBlockElement::GetList(array(), array("ID"=>$auctionId), false, false, array("IBLOCK_ID"));
	$arRes = $res->Fetch();
	$auctionAccess = CIBlock::GetPermission($arRes["IBLOCK_ID"]);
	
	//send message
	if (strlen($message) > 0 && $USER->IsAuthorized() && $auctionAccess > "R")
	{
		$message = strip_tags($message);
		
		$userName = $USER->GetFormattedName(false, false);
		if (strlen($userName) <= 0)
			$userName = $USER->GetLogin();
	
		$strSql = "INSERT INTO b_auction(user_id, auction_id, product_id, date, user_name, message) ".
				"VALUES(".$USER->GetID().", ".$auctionId.", ".$productId.", ".$DB->GetNowFunction().", '".$userName."', '".$message."')";
		if (!$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__))
		{
			$status = 404;
			$data = '';
		}
	}
	
	//select message
	if ($status != 404)
	{
		$arResult = getMessageChat($auctionId, $productId, $state);
		if ($arResult["state"] != $state)
		{
			$status = 200;
			$state = $arResult["state"];
			$data = $arResult["data"];
		}
		else
		{
			$status = 400;
			$data = '';
		}
	}
}
else
{
	$status = 405;
}

header('Content-type: application/json');

$data = iconv('windows-1251', 'UTF-8', $data);
echo '{"status":"'.$status.'","state":"'.$state.'","data":"'.$data.'"}';;

die();
?>