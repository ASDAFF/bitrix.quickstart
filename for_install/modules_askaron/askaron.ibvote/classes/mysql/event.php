<?
##############################################
# Askaron.Ibvote module                      #
# Copyright (c) 2011 Askaron Systems         #
# http://askaron.ru                          #
# mailto:mail@askaron.ru                     #
##############################################

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/classes/general/event.php");

class CAskaronIbvoteEvent extends CAllAskaronIbvoteEvent
{
	function err_mess()
	{
		$module_id = "askaron.ibvote";
		return "<br>Module: ".$module_id."<br>Class: CAskaronIbvoteEvent<br>File: ".__FILE__;
	}

    function GetList( $arOrder = array('id' => 'desc'), $arFilter=array(), $arGroupBy = false )
	{
		//echo '<pre>'; print_r($arFilter); echo '</pre>'; 
		
		global $DB;
		$err_mess = (CAskaronIbvoteEvent::err_mess() )."<br>Function: GetList<br>Line: ";

		// filter
		$strSqlSearch = "";
		$arSqlSearch = array();
		$arFilter = (is_array($arFilter) ? $arFilter : array());

		foreach ($arFilter as $key => $val)
		{
			if(is_array($val))
			{
				if(count($val) <= 0)
					continue;
			}
			else
			{
				// "NOT_REF" for filter with function SelectBoxFromArray
				if( (strlen($val) <= 0) || ($val === "NOT_REF") )
					continue;
			}

			$key = strtoupper($key);			
			
			$action = '=';

			$actionStr2 = substr($key, 0, 2);
			if (				
				$actionStr2 == '>='
			||
				$actionStr2 == '<='
			)
			{
				$action = $actionStr2;
				$key = substr($key, 2);
			}
			else
			{
				$actionStr1 = substr($key, 0, 1);
				if (
						$actionStr1 == '='
					||
						$actionStr1 == '>'
					||
						$actionStr1 == '<'
				)
				{
					$action = $actionStr1;
					$key = substr($key, 1);
				}
			}					
			
			switch($key)
			{
				
				// equal
				case "ID":
				case "ELEMENT_ID":
				case "ANSWER":
				case "IP":
				case "USER_ID":
				case "STAT_SESSION_ID":
					
					$arSqlSearch[] = "E.".$key." ".$action." '".$DB->ForSql( $val )."' and E.".$key." is not null";

					//E.SUCCESS_EXEC='Y' and E.SUCCESS_EXEC is not null
					//$match = ($arFilter[$key."_EXACT_MATCH"] == "Y" ? "Y" : "N");
					//$arSqlSearch[] = GetFilterQuery("E.".$key, $val, $match);
					break;

				case "DATE_VOTE":
					$arSqlSearch[] = "E.DATE_VOTE ".$action." ".$DB->CharToDateFunction($val, "FULL");
					break;
				
				// only day, no time
				//case "DATE_VOTE_1":
				//	$arSqlSearch[] = "E.DATE_VOTE ".$action." ".$DB->CharToDateFunction($val, "SHORT");
				//	break;
				//case "DATE_VOTE_2":
				//	$arSqlSearch[] = "E.DATE_VOTE ".$action." ".$DB->CharToDateFunction($val, "SHORT")." + INTERVAL 1 DAY";
				//	break;
			}			
		}

		//$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$strSqlSearch = '
(
	1=1';

		foreach ($arSqlSearch as $value)
		{
			$strSqlSearch .= '
	AND
	(
		'.$value.'
	)';
		}

		$strSqlSearch .= '
)';

		// order
		$strSqlOrder = "";
		$arSqlOrder = Array();		
		
		if ( is_array( $arOrder ) && count( $arOrder ) > 0 )
		{
			foreach($arOrder as $by=>$order)
			{
				$by = strtolower($by);
				$order = strtolower($order);
				if ($order!="asc")
					$order = "desc";

				if ($by == "id")						$arSqlOrder[] = " E.ID ".$order." ";
				elseif ($by == "element_id")			$arSqlOrder[] = " E.ELEMENT_ID ".$order." ";
				elseif ($by == "answer")				$arSqlOrder[] = " E.ANSWER ".$order." ";
				elseif ($by == "date_vote")				$arSqlOrder[] = " E.DATE_VOTE ".$order." ";				
				elseif ($by == "ip")					$arSqlOrder[] = " E.IP ".$order." ";
				elseif ($by == "user_id")				$arSqlOrder[] = " E.USER_ID ".$order." ";
				elseif ($by == "stat_session_id")		$arSqlOrder[] = " E.STAT_SESSION_ID ".$order." ";
				else
				{
					$arSqlOrder[] = " E.ID ".$order." ";
					$by = "id";
				}
			}
		}
		else
		{
			$arSqlOrder[] = " E.ID desc ";
			$by = "id";
		}

		//DelDuplicateSort($arSqlOrder);

		for ($i=0; $i<count($arSqlOrder); $i++)
		{
			if($i==0)
				$strSqlOrder = " ORDER BY ";
			else
				$strSqlOrder .= ",";

			$strSqlOrder .= $arSqlOrder[$i];
		}

		// group
		$strSqlGroup = "";
		$arSqlGroup = array();

		if (is_array($arGroupBy) && count($arGroupBy)>0)
		{
			$arGroupByUnique = array_unique($arGroupBy);
			foreach ($arGroupByUnique as $fieldName)
			{
				switch ($fieldName)
				{
					case 'ID': $arSqlGroup[] = 'E.ID'; break;
					case 'ELEMENT_ID': $arSqlGroup[] = 'E.ELEMENT_ID'; break;
					case 'ANSWER': $arSqlGroup[] = 'E.ANSWER'; break;
					case 'DATE_VOTE': $arSqlGroup[] = 'E.DATE_VOTE'; break;
					case 'IP': $arSqlGroup[]	= 'E.IP'; break;
					case 'USER_ID': $arSqlGroup[] = 'E.USER_ID'; break;
					case 'STAT_SESSION_ID': $arSqlGroup[]	= 'E.STAT_SESSION_ID'; break;

					default: break;
				}
			}
		}

		for ($i=0; $i<count($arSqlGroup); $i++)
		{
			if($i==0)
				$strSqlGroup = " GROUP BY ";
			else
				$strSqlGroup .= ",";

			$strSqlGroup .= $arSqlGroup[$i];			
		}

		// select
		$strSqlSelect = "";

		if (is_array($arGroupBy))
		{
			$arSqlSelect = array("COUNT(*) as CNT");
			$arSqlSelect = array_merge( $arSqlSelect, $arSqlGroup);
		}
		else
		{
			$arSqlSelect = array(
				'E.ID',
				'E.ELEMENT_ID',
				'E.ANSWER',
				'E.DATE_VOTE',
				'E.IP',
				'E.USER_ID',
				'E.STAT_SESSION_ID',
			);
		}

		$first = true;
		foreach ($arSqlSelect as $value)
		{
			if (!$first)
			{
				$strSqlSelect .= ", ";
			}

			$strSqlSelect .= $value;
			$first = false;
		}
		

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT COUNT(*) as CNT
				FROM b_askaron_ibvote_event E
				WHERE
				".$strSqlSearch.";";

			$res = $DB->Query($strSql, false, $err_mess.__LINE__);
			if ($arRes = $res->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}
		else
		{
			$strSql = "
			SELECT
				".$strSqlSelect."
			FROM b_askaron_ibvote_event E
			WHERE
				".$strSqlSearch."
				".$strSqlGroup."
				".$strSqlOrder.";";

			//echo '<pre>'; print_r($strSql); echo '</pre>'; 
			
			$res = $DB->Query($strSql, false, $err_mess.__LINE__);
			return $res;
		}
	}


	function Delete($ID)
	{
		global $DB;
		//global $APPLICATION;
		
		$result = false;
		
		$err_mess = (CAskaronIbvoteEvent::err_mess() )."<br>Function: Delete<br>Line: ";
		$ID = IntVal($ID);

		$res = self::GetByID($ID);
		if ( $arFields = $res->Fetch() )
		{
			//$APPLICATION->ResetException();
			//$db_events = GetModuleEvents("askaron.ibvote", "OnBeforeEventDelete");
			//while($arEvent = $db_events->Fetch())
			//{
			//	if(ExecuteModuleEventEx($arEvent, array($ID))===false)
			//	{
			//		$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
			//		if($ex = $APPLICATION->GetException())
			//			$err .= ': '.$ex->GetString();
			//		$APPLICATION->throwException($err);
			//		return false;
			//	}
			//}

			$result = $DB->Query("DELETE FROM b_askaron_ibvote_event WHERE ID=".$ID, false, $err_mess.__LINE__);

			$events = GetModuleEvents("askaron.ibvote", "OnAfterIbvoteEventDelete");
			while ($arEvent = $events->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array( $ID, &$arFields ) );
			}
		}
		
		return $result;
	}

	function Add($arFields)
	{
		$err_mess = (CAskaronIbvoteEvent::err_mess() )."<br>Function: Add<br>Line: ";
		global $DB;

		if( isset( $arFields['ID']) )
			unset( $arFields['ID'] );
		
		if (empty($arFields))
			return false;		

	
		$arInsertFields['ELEMENT_ID'] = "'".intval( $arFields['ELEMENT_ID'] )."'";
		
		if( strlen( $arFields["ANSWER"] ) > 0)
		{
			$arInsertFields['ANSWER']  = "'".intval( $arFields["ANSWER"] )."'";		
		}
			
		if(strlen( $arFields["IP"] ) > 0)
		{
			$arInsertFields['IP'] = "'".$DB->ForSql($arFields['IP'],15)."'";			
		}
		else
		{
			$arInsertFields['IP'] = "'".$DB->ForSql($_SERVER["REMOTE_ADDR"],15)."'";
		}
		
		$arInsertFields["DATE_VOTE"] = $DB->GetNowFunction();
		
		if ( strlen( $arFields['USER_ID'] ) > 0 )
		{
			$arInsertFields['USER_ID'] = "'".intval( $arFields['USER_ID'] )."'";
		}	
		
		// statistic module		
		$arInsertFields["STAT_SESSION_ID"] = "'".intval($_SESSION["SESS_SESSION_ID"])."'";
		
		$ID = $DB->Insert("b_askaron_ibvote_event", $arInsertFields, $err_mess.__LINE__);
		
		if (intval( $ID ) > 0 )
		{
			$arFields['ID'] = $ID;
		}
		
		$events = GetModuleEvents("askaron.ibvote", "OnAfterIbvoteEventAdd");
		while ($arEvent = $events->Fetch())
		{
			ExecuteModuleEventEx($arEvent, array( &$arFields ) );
		}

		
		return $ID;
		
		//$arInsert = $DB->PrepareInsert("b_askaron_ibvote_event", $arFields);
		//$strSql = "INSERT INTO b_askaron_ibvote_event (".$arInsert[0].") VALUES (".$arInsert[1].")";
		//$DB->Query($strSql, false, $err_mess.__LINE__);
		//return intval($DB->LastID());
	}	
	
	function Update($ID, $arFields)
	{
		$err_mess = (CAskaronIbvoteEvent::err_mess() )."<br>Function: Update<br>Line: ";
		global $DB;
		$ID = intval($ID);

		if( !$this->CheckFields($arFields) )
			return false;

		$strUpdate = $DB->PrepareUpdate("b_askaron_ibvote_event", $arFields);

		if($strUpdate!="")
		{
			$strSql = "UPDATE b_askaron_ibvote_event SET ".$strUpdate." WHERE ID=".$ID;
			$DB->Query($strSql, false, $err_mess.__LINE__);
			
			$events = GetModuleEvents("askaron.ibvote", "OnAfterIbvoteEventUpdate");
			while ($arEvent = $events->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array( $ID, &$arFields ) );
			}
		}
		
		return true;
	}
	
	// returns true if voted
	function CheckVotingIP($ELEMENT_ID, $IP='', $KEEP_IP_SEC=0)
	{
		global $DB;
		$err_mess = (CAskaronIbvoteEvent::err_mess() )."<br>Function: CheckVotingIP<br>Line: ";

		$ELEMENT_ID = intval($ELEMENT_ID);
		$KEEP_IP_SEC = intval($KEEP_IP_SEC);

		if ( $KEEP_IP_SEC > 0)
		{
			// we can use GetList and getmicrotime, but sometimes 'PHP server time' and 'DB server time' are different.
			
			$strSqlSearch = "
				E.ELEMENT_ID='".$ELEMENT_ID."'
					AND
				E.IP='".$DB->ForSql($IP, 15)."'
					AND
				E.DATE_VOTE >= FROM_UNIXTIME(UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - ".$KEEP_IP_SEC.")
			";
			
			$strSql =
				"SELECT E.ID, E.ELEMENT_ID, E.DATE_VOTE, E.IP
				FROM b_askaron_ibvote_event E
				WHERE
				".$strSqlSearch."
				ORDER BY E.DATE_VOTE desc
				LIMIT 1;";

			$res = $DB->Query($strSql, false, $err_mess.__LINE__);
			if ($arRes = $res->Fetch())
			{
				//AddMessage2Log( print_r($arRes, true) );
				return true;
			}			
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	// returns true if voted
	function CheckVotingUserId($ELEMENT_ID, $USER_ID, $KEEP_IP_SEC=0)
	{
		global $DB;
		$err_mess = (CAskaronIbvoteEvent::err_mess() )."<br>Function: CheckVotingIP<br>Line: ";

		$ELEMENT_ID = intval($ELEMENT_ID);
		$USER_ID = intval($USER_ID);
		$KEEP_IP_SEC = intval($KEEP_IP_SEC);

		if ( $ELEMENT_ID > 0 && $USER_ID > 0 && $KEEP_IP_SEC > 0)
		{
			// we can use GetList and getmicrotime, but sometimes 'PHP server time' and 'DB server time' are different.

			$strSqlSearch = "
				E.ELEMENT_ID='".$ELEMENT_ID."'
					AND
				E.USER_ID='".$USER_ID."'
					AND
				E.DATE_VOTE >= FROM_UNIXTIME(UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - ".$KEEP_IP_SEC.")
			";

			$strSql =
				"SELECT E.ID, E.ELEMENT_ID, E.DATE_VOTE, E.USER_ID
				FROM b_askaron_ibvote_event E
				WHERE
				".$strSqlSearch."
				ORDER BY E.DATE_VOTE desc
				LIMIT 1;";

			$res = $DB->Query($strSql, false, $err_mess.__LINE__);
			if ($arRes = $res->Fetch())
			{
				//AddMessage2Log( print_r($arRes, true) );
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}	
}
?>