<?
if (!function_exists('AskaronIbvoteIblockVote_CheckVote'))
{
	function AskaronIbvoteIblockVote_CheckVote( $ELEMENT_ID, $arParams )
	{
		global $APPLICATION;
		global $USER;
		
		$bVoted = false;
		
		if ( intval($ELEMENT_ID) > 0 )
		{
			$db_events = GetModuleEvents("askaron.ibvote", "OnStartCheckVoting");
			while($arEvent = $db_events->Fetch())
			{
				$bEventRes = ExecuteModuleEventEx($arEvent, array($ELEMENT_ID, $arParams) );
				if($bEventRes===false)
				{
					$bVoted = true;
					break;
				}
			}	
	
			if (!$bVoted && $arParams["SESSION_CHECK"] == "Y" )
			{
				$bVoted = (is_array($_SESSION["IBLOCK_RATING"]) && array_key_exists($ELEMENT_ID, $_SESSION["IBLOCK_RATING"]));
			}

			if (!$bVoted && $arParams["COOKIE_CHECK"] == "Y" )
			{	
				$arCookie = Array();
				$strCookie = $APPLICATION->get_cookie("ASKARON_IBVOTE_IBLOCK_RATING");

				if ( strlen( $strCookie ) > 0 )
				{
					$arCookie = unserialize( $strCookie );
				}
					
				$bVoted = (is_array($arCookie) && array_key_exists($ELEMENT_ID, $arCookie))? 1: 0;				
			}			
			
			if (!$bVoted && ( $arParams["IP_CHECK_TIME"] > 0) )
			{	
				if(CModule::IncludeModule("askaron.ibvote"))
				{				
					$bVoted = CAskaronIbvoteEvent::CheckVotingIP($ELEMENT_ID, $_SERVER["REMOTE_ADDR"], $arParams["IP_CHECK_TIME"] );
				}
			}
			
			if (!$bVoted && ( $arParams["USER_ID_CHECK_TIME"] > 0) )
			{	
				if ( $USER->IsAuthorized() )
				{
					if(CModule::IncludeModule("askaron.ibvote"))
					{				
						$bVoted = CAskaronIbvoteEvent::CheckVotingUserId($ELEMENT_ID, $USER->GetID(), $arParams["USER_ID_CHECK_TIME"] );
					}
				}
			}			
		}

		if ( $bVoted )
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
}

if(!defined("B_PROLOG_INCLUDED") && isset($_REQUEST["AJAX_CALL"]) && $_REQUEST["AJAX_CALL"]=="Y")
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	if(CModule::IncludeModule("iblock"))
	{
		$arCache = CIBlockRSS::GetCache($_REQUEST["SESSION_PARAMS"]);
		if($arCache && ($arCache["VALID"] == "Y"))
		{
			$arParams = unserialize($arCache["CACHE"]);
			foreach($arParams["PAGE_PARAMS"] as $param_name)
			{
				if(!array_key_exists($param_name, $arParams))
					$arParams[$param_name] = $_REQUEST["PAGE_PARAMS"][$param_name];
			}

			if(array_key_exists("PARENT_NAME", $arParams))
			{
				$component = new CBitrixComponent();
				$component->InitComponent($arParams["PARENT_NAME"], $arParams["PARENT_TEMPLATE_NAME"]);
				$component->InitComponentTemplate($arParams["PARENT_TEMPLATE_PAGE"]);
			}
			else
			{
				$component = null;
			}
			$APPLICATION->IncludeComponent($arParams["COMPONENT_NAME"], $arParams["TEMPLATE_NAME"], $arParams, $component);
		}
	}

	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
	die();
}

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $USER;
global $APPLICATION;

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}
/************************************************
	Processing of received parameters
*************************************************/
$arParamsSave = $arParams;

// required params
$arParams = array(
	"IBLOCK_ID" => intval($arParams["IBLOCK_ID"]),
	"ELEMENT_ID" => intval($arParams["ELEMENT_ID"]),
	"MAX_VOTE" => intval($arParams["MAX_VOTE"])<=0? 5: intval($arParams["MAX_VOTE"]),
	"VOTE_NAMES" => is_array($arParams["VOTE_NAMES"])? $arParams["VOTE_NAMES"]: array(),
	"CACHE_TYPE" => $arParams["CACHE_TYPE"],
	"CACHE_TIME" => $arParams["CACHE_TIME"],	
	"DISPLAY_AS_RATING" => $arParams["DISPLAY_AS_RATING"]=="vote_avg"? "vote_avg": "rating",
	"READ_ONLY" => $arParams["READ_ONLY"],
	"IP_CHECK_TIME" => intval($arParams["IP_CHECK_TIME"]),
	"USER_ID_CHECK_TIME" => intval($arParams["USER_ID_CHECK_TIME"]),
	"SESSION_CHECK" =>  $arParams["SESSION_CHECK"] != "N" ? "Y": "N",
	"COOKIE_CHECK" => $arParams["COOKIE_CHECK"] != "Y" ? "N": "Y",
);

// additional params
foreach ( $arParamsSave as $key=>$value)
{
	if ( !isset( $arParams[$key] ) && strpos( $key, "~" ) !== 0 )
	{
		$arParams[$key] = $value;
	}
}

//echo "<pre>"; print_r($arParams); echo "</pre>";

$bVoted = 0;


/****************************************
	Any actions without cache
*****************************************/
if(
	$_SERVER["REQUEST_METHOD"] == "POST"
	&& !empty($_REQUEST["vote"])
	&& ($_REQUEST["AJAX_CALL"]=="Y" || check_bitrix_sessid())
	&& $arParams["READ_ONLY"]!=="Y"
)
{
	$ELEMENT_ID = intval($_REQUEST["vote_id"]);
	if ( $ELEMENT_ID > 0 && $ELEMENT_ID == $arParams["ELEMENT_ID"] )
	{
		$RATING = intval($_REQUEST["rating"])+1;
		if($RATING>0 && $RATING<=$arParams["MAX_VOTE"])
		{
			// not voted
			if( !AskaronIbvoteIblockVote_CheckVote( $ELEMENT_ID, $arParams ) )		 
			{
				// set flag "voted" (1.1.0)
				$bVoted = 1;

				if ($arParams["SESSION_CHECK"]=="Y")
				{
					if(!is_array($_SESSION["IBLOCK_RATING"]))
						$_SESSION["IBLOCK_RATING"] = Array();				
						
					$_SESSION["IBLOCK_RATING"][$ELEMENT_ID]=true;
				}
				
				if ($arParams["COOKIE_CHECK"]=="Y")
				{
					$strCookie = $APPLICATION->get_cookie("ASKARON_IBVOTE_IBLOCK_RATING");
					
					if ( strlen( $strCookie ) > 0 )
					{
						$arCookie = unserialize( $strCookie );
					}
					
					if ( !is_array($arCookie) )
						$arCookie = Array();
					
					$arCookie[$ELEMENT_ID] = true;
					$strCookie = serialize($arCookie);
					$APPLICATION->set_cookie("ASKARON_IBVOTE_IBLOCK_RATING", $strCookie);
				}
				
				$rsProperties = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $ELEMENT_ID, "value_id", "asc", array("ACTIVE"=>"Y"));
				$arProperties = array();
				while($arProperty = $rsProperties->Fetch())
				{
					if($arProperty["CODE"]=="vote_count")
						$arProperties["vote_count"] = $arProperty;
					elseif($arProperty["CODE"]=="vote_sum")
						$arProperties["vote_sum"] = $arProperty;
					elseif($arProperty["CODE"]=="rating")
						$arProperties["rating"] = $arProperty;
				}

				$obProperty = new CIBlockProperty;
				$res = true;
				if(!array_key_exists("vote_count", $arProperties))
				{
					$res = $obProperty->Add(array(
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"ACTIVE" => "Y",
						"PROPERTY_TYPE" => "N",
						"MULTIPLE" => "N",
						"NAME" => "vote_count",
						"CODE" => "vote_count",
					));
					if($res)
						$arProperties["vote_count"] = array("VALUE"=>0);
				}
				if($res && !array_key_exists("vote_sum", $arProperties))
				{
					$res = $obProperty->Add(array(
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"ACTIVE" => "Y",
						"PROPERTY_TYPE" => "N",
						"MULTIPLE" => "N",
						"NAME" => "vote_sum",
						"CODE" => "vote_sum",
					));
					if($res)
						$arProperties["vote_sum"] = array("VALUE"=>0);
				}
				if($res && !array_key_exists("rating", $arProperties))
				{
					$res = $obProperty->Add(array(
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"ACTIVE" => "Y",
						"PROPERTY_TYPE" => "N",
						"MULTIPLE" => "N",
						"NAME" => "rating",
						"CODE" => "rating",
					));
					if($res)
						$arProperties["rating"] = array("VALUE"=>0);
				}
				if($res)
				{
					$arProperties["vote_count"]["VALUE"] = intval($arProperties["vote_count"]["VALUE"])+1;
					$arProperties["vote_sum"]["VALUE"] = intval($arProperties["vote_sum"]["VALUE"])+$RATING;
					//rating = (SUM(vote)+31.25) / (COUNT(*)+10)
					$arProperties["rating"]["VALUE"] = round(($arProperties["vote_sum"]["VALUE"]+31.25/5*$arParams["MAX_VOTE"])/($arProperties["vote_count"]["VALUE"]+10),2);
					
					//$db_events = GetModuleEvents("askaron.ibvote", "OnBeforeRatingWrite");
					//while($arEvent = $db_events->Fetch())
					//{
					//	$bEventRes = ExecuteModuleEventEx(
					//		$arEvent, 
					//		array(
					//			$ELEMENT_ID,
					//			$arProperties["vote_count"]["VALUE"],
					//			$arProperties["vote_sum"]["VALUE"],
					//			&$arProperties["rating"]["VALUE"],
					//		) 
					//	);
					//}
					
					$DB->StartTransaction();
					CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, $arParams["IBLOCK_ID"], array(
						"vote_count" => array(
							"VALUE" => $arProperties["vote_count"]["VALUE"],
							"DESCRIPTION" => $arProperties["vote_count"]["DESCRIPTION"],
						),
						"vote_sum" => array(
							"VALUE" => $arProperties["vote_sum"]["VALUE"],
							"DESCRIPTION" => $arProperties["vote_sum"]["DESCRIPTION"],
						),
						"rating" => array(
							"VALUE" => $arProperties["rating"]["VALUE"],
							"DESCRIPTION" => $arProperties["rating"]["DESCRIPTION"],
						),
					));
					$DB->Commit();
					
					if(CModule::IncludeModule("askaron.ibvote"))
					{
						$event = new CAskaronIbvoteEvent;
						$arEventFields = array(
							'ELEMENT_ID' =>  $ELEMENT_ID,
							'ANSWER' => $RATING,
							'USER_ID' => $USER->GetID(),
						);
						$event->add($arEventFields);
					}
					
					$this->ClearResultCache(array($USER->GetGroups(), 1));
					$this->ClearResultCache(array($USER->GetGroups(), 0));
					
					$clear_cache=COption::GetOptionString("askaron.ibvote", "clear_cache");
					if ( $clear_cache !== "N" )
					{
						if(defined("BX_COMP_MANAGED_CACHE"))
						{
							$GLOBALS["CACHE_MANAGER"]->ClearByTag("iblock_id_".$arParams["IBLOCK_ID"]);
						}						
					}
				}
			}
		}
		if($_REQUEST["AJAX_CALL"]!="Y")
			LocalRedirect(!empty($_REQUEST["back_page"])?$_REQUEST["back_page"]:$APPLICATION->GetCurPageParam());
	}
}
if ( !$bVoted )
{
	$bVoted = AskaronIbvoteIblockVote_CheckVote( $arParams["ELEMENT_ID"], $arParams );
}
//$bVoted = (is_array($_SESSION["IBLOCK_RATING"]) && array_key_exists($arParams["ELEMENT_ID"], $_SESSION["IBLOCK_RATING"]))? 1: 0;

if($this->StartResultCache(false, array($USER->GetGroups(), $bVoted)))
{
	if($arParams["ELEMENT_ID"]>0)
	{
		//SELECT
		$arSelect = array(
			"ID",
			"IBLOCK_ID",
			"PROPERTY_*",
		);
		//WHERE
		$arFilter = array(
			"ID" => $arParams["ELEMENT_ID"],
			"IBLOCK_ACTIVE" => "Y",
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
		);
		//ORDER BY
		$arSort = array(
		);
		//EXECUTE
		$rsElement = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
		if($obElement = $rsElement->GetNextElement())
		{
			$arResult = $obElement->GetFields();
			$arResult["PROPERTIES"] = $obElement->GetProperties();
			
			$arResult["BACK_PAGE_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam());
			$arResult["VOTE_NAMES"] = array();
			foreach($arParams["VOTE_NAMES"] as $k=>$v)
			{
				if(strlen($v)>0)
					$arResult["VOTE_NAMES"][]=htmlspecialchars($v);
				if(count($arResult["VOTE_NAMES"])>=$arParams["MAX_VOTE"])
					break;
			}
			for($i=0;$i<$arParams["MAX_VOTE"];$i++)
				if(!array_key_exists($i, $arResult["VOTE_NAMES"]))
					$arResult["VOTE_NAMES"][$i]=$i+1;

			$arResult["VOTED"] = $bVoted;
			
			//echo "<pre>",htmlspecialchars(print_r($arResult,true)),"</pre>";
			//echo "<pre>",htmlspecialchars(print_r($arResult,true)),"</pre>";
			
			$this->SetResultCacheKeys(array(
				"AJAX",
			));
			$this->IncludeComponentTemplate();			
		}
		else
		{
			$this->AbortResultCache();
			ShowError(GetMessage("ASKARON_IBVOTE_ELEMENT_NOT_FOUND"));

			if ( $USER->IsAdmin() )
			{
				echo GetMessage("ASKARON_IBVOTE_ELEMENT_NOT_FOUND_ADMIN_NOTES", array( "#IBLOCK_ID#" => $arParams["IBLOCK_ID"], "#ELEMENT_ID#" => $arParams[ "ELEMENT_ID" ] ) );
			}
		
			@define("ERROR_404", "Y");
			if($arParams["SET_STATUS_404"]==="Y")
				CHTTP::SetStatus("404 Not Found");
		}
	}
	else
	{
		$this->AbortResultCache();
		ShowError(GetMessage("ASKARON_IBVOTE_EMPTY_ELEMENT_ID"));
		
		if ( $USER->IsAdmin() )
		{
			echo GetMessage("ASKARON_IBVOTE_EMPTY_ELEMENT_ID_ADMIN_NOTES");
		}
		
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
	}
}

if(array_key_exists("AJAX", $arResult) && ($_REQUEST["AJAX_CALL"] != "Y"))
{
	if(!is_array($_SESSION["iblock.vote"]))
		$_SESSION["iblock.vote"] = array();
	if(!array_key_exists($arResult["AJAX"]["SESSION_KEY"], $_SESSION["iblock.vote"]))
	{
		$arCache = CIBlockRSS::GetCache($arResult["AJAX"]["SESSION_KEY"]);
		if(!$arCache || ($arCache["VALID"] != "Y"))
		{
			CIBlockRSS::UpdateCache($arResult["AJAX"]["SESSION_KEY"], serialize($arResult["AJAX"]["SESSION_PARAMS"]), 24*30, is_array($arCache));
		}
		$_SESSION["iblock.vote"][$arResult["AJAX"]["SESSION_KEY"]] = true;
	}

	if(!defined("ADMIN_SECTION") || (ADMIN_SECTION !== true))
	{
		IncludeAJAX();
	}
}
?>
