<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/classes/general/sms4b.php");
//IncludeModuleLangFile(__FILE__);

class Csms4b extends CSms4BitrixWrapper
{

	private $xquery_error = array();

	public function GetListInc($sort,$filter)
	{
		global $DB;
		$id = intval($id);
		$strSql = "SELECT GUID,Moment,TimeOff,Source,Destination,Coding,Total AS Vsego,GROUP_CONCAT(Body ORDER BY Part ASC SEPARATOR '') as Body,count(*) as Total
FROM b_sms4b_incoming ";

		$arrFields = array (
							"GUID",
							"Moment",
							"TimeOff",
							"Source",
							"Destination",
							"Coding",
							"Body",
							"Total",
							);

		$arrFieldsType = array (
							"S",
							"D",
							"D",
							"S",
							"S",
							"N",
							"S",
							"N",
							);

		$strWHERE = '';
		foreach ($filter as $fkey => $fval)
		{
			if(	$fval <> '' &&
				$fkey <> 'Moment_to' &&
				$fkey <> 'TimeOff_to'
				)
			{

				if($strWHERE == '')
					$strWHERE = ' WHERE ';
				else
					$strWHERE .= ' AND ';

				if (in_array($fkey,$arrFields))
				{
					$strWHERE .= $fkey.' = ^'.$arrFieldsType[array_search($fkey,$arrFields)].' ';
					$params[] = $fval;
				}
				elseif($fkey == 'Moment_from')
				{
					$strWHERE .= ' Moment >= ^D';

					$params[] = ConvertDateTime($fval,"YYYY-MM-DD HH:MI:SS");
					if ($filter['Moment_to'] <> '')
					{
						$strWHERE .= ' AND Moment <= ^D ';
						$params[] = ConvertDateTime($filter['Moment_to'],"YYYY-MM-DD HH:MI:SS");
					}
				}
				elseif($fkey == 'TimeOff_from')
				{
					$strWHERE .= ' TimeOff >= ^D';
					$params[] = ConvertDateTime($fval,"YYYY-MM-DD HH:MI:SS");

					if ($filter['TimeOff_to'] <> '')
					{
						$strWHERE .= ' AND TimeOff <= ^D ';
						$params[] = ConvertDateTime($filter['TimeOff_to'],"YYYY-MM-DD HH:MI:SS");
					}
				}
			}
		}
		$strSql .= $strWHERE." GROUP BY GUID HAVING Total = Vsego";


		foreach ($sort as $by => $order)
		if ((strtoupper($order) == 'DESC' || strtoupper($order) == 'ASC') && in_array($by,$arrFields))
			$strSql .= ' ORDER BY '.$by.' '.$order;

		$res = $this->xquery($strSql,$params);

		if($res)
			return($res);
		else
			return false;
	}


	public function GetList($sort,$filter)
	{
		global $DB;
		$id=intval($id);
		$strSql = "SELECT * FROM b_sms4b";

		$arrFields = array (
							"id",
							"GUID",
							"SenderName",
							"Destination",
							"StartSend",
							"LastModified",
							"CountPart",
							"SendPart",
							"CodeType",
							"TextMessage",
							"Sale_Order",
							"Posting",
							"Events",
							);

		$arrFieldsType = array (
							"N",
							"S",
							"S",
							"S",
							"D",
							"D",
							"N",
							"N",
							"N",
							"S",
							"S",
							"N",
							"S",
							);

		$strWHERE = '';
		foreach ($filter as $fkey => $fval)
		{
			if(	$fval <> '' &&
				$fkey <> 'StartSend_to' &&
				$fkey <> 'LastModified_to'
				)
			{

				if($strWHERE == '')
					$strWHERE = ' WHERE ';
				else
					$strWHERE .= ' AND ';

				if (in_array($fkey,$arrFields))
				{
					$strWHERE .= $fkey.' = ^'.$arrFieldsType[array_search($fkey,$arrFields)].' ';
					$params[] = $fval;
				}
				elseif($fkey == 'StartSend_from')
				{
					$strWHERE .= ' StartSend >= ^D';
					$params[] = $fval;

					if ($filter['StartSend_to'] <> '')
					{
						$strWHERE .= ' AND StartSend <= ^D ';
						$params[] = ConvertDateTime($filter['StartSend_to'],"YYYY-MM-DD HH:MI:SS");
					}
				}
				elseif($fkey == 'LastModified_from')
				{
					$strWHERE .= ' LastModified >= ^D';
					$params[] = $fval;

					if ($filter['LastModified_to'] <> '')
					{
						$strWHERE .= ' AND LastModified <= ^D ';
						$params[] = ConvertDateTime($filter['LastModified_to'],"YYYY-MM-DD HH:MI:SS");
					}
				}
				elseif($fkey == 'OnlyUpdate')
				{
					$strWHERE .= ' (CountPart > SendPart OR CountPart = 0) ';

				}
			}
		}
		$strSql .= $strWHERE;

		foreach ($sort as $by => $order)
		if ((strtoupper($order) == 'DESC' || strtoupper($order) == 'ASC') && in_array($by,$arrFields))
			$strSql .= ' ORDER BY '.$by.' '.$order;

		$res = $this->xquery($strSql,$params);

		if($res)
			return($res);
		else
			return false;
	}

	public function GetByID($id)
	{
		global $DB;
		$id = intval($id);
		$strSql = "SELECT * FROM b_sms4b WHERE ".
							" id = ".$id;
		$res = $DB->Query($strSql, true, $err_mess.__LINE__);
		$arRes = $res->Fetch();
		if(is_array($arRes) && count($arRes) > 0)
			return($arRes);
		else
			return false;
	}

	public	function ArrayAdd($param = array())
	{
		global $USER,$DB;

		if(is_array($param) && count($param) > 0)
		{
			$sql_ins = "insert into ".$this->dbpref."b_sms4b (GUID,SenderName,Destination,StartSend,LastModified,Status,CountPart,SendPart,CodeType,TextMessage,Sale_Order,Posting,Events) values ";
			$sql_arIns = '';
			$sql_arUpd = '';
			$sql_arUpd = 'UPDATE '.$this->dbpref.'b_sms4b SET ';
			$sql_arUpd_SendPart = '';
			$sql_arUpd_StartSend = '';
			$sql_arUpd_LastModified = '';
			$sql_arUpd_Status = '';
			$sql_arUpd_Where ='';
			$paramIns = array();
			$LastModified = '';

			foreach ($param as $record)
			{
				if(is_array($record) && count($record) > 0)
				{
					$strSql = "SELECT GUID FROM ".$this->dbpref."b_sms4b WHERE GUID = ^S ";

					$res = $this->xquery($strSql, array($record["GUID"]));

					$arRes = $res->Fetch();

					$record["StartSend"] = explode('.',$record["StartSend"]);
					$record["StartSend"] = $record["StartSend"][0];
					$record["LastModified"] = explode('.',$record["LastModified"]);
					$record["LastModified"] = $record["LastModified"][0];

					if ($record["LastModified"] > $LastModified)
						$LastModified = $record["LastModified"];

					if($arRes['GUID'] <> '')
					{
						$sql_arUpd_SendPart .= '
when GUID = ^S then ^N';
						$paramUpdateSendPart[] = $record["GUID"];
						$paramUpdateSendPart[] = $record["SendPart"];

						$sql_arUpd_StartSend .= '
when GUID = ^S then ^D';
						$paramUpdateStartSend[] = $record["GUID"];
						$paramUpdateStartSend[] = $record["StartSend"];

						$sql_arUpd_LastModified .= '
when GUID = ^S then ^D';
						$paramUpdateLastModified[] = $record["GUID"];
						$paramUpdateLastModified[] = $record["LastModified"];

						$sql_arUpd_Where .= ' GUID = ^S OR';
						$param_Where[] = $record["GUID"];
					}
					else
					{
						$sql_arIns .= "(^S,^S,^S,^D,^D,^N,^N,^N,^N,^S,^S,^N,^S),";
						$paramIns = array_merge(
												$paramIns,array(
												$record["GUID"],
												$record["SenderName"],
												$record["Destination"],
												$record["StartSend"],
												$record["LastModified"],
												$record["Status"],
												$record["CountPart"],
												$record["SendPart"],
												$record["CodeType"],
												$record["TextMessage"],
												$record["Sale_Order"],
												$record["Posting"],
												$record["Events"]
						));
					}
				}
			}

			$sql_arUpd_Where = ' WHERE '.trim($sql_arUpd_Where,'OR');

			if ($sql_arUpd_SendPart <> '')
			{
				$sql_Upd = $sql_arUpd.'
SendPart = case'.$sql_arUpd_SendPart.'
end'.$sql_arUpd_Where;
				foreach ($param_Where as $wpar)
					$paramUpdateSendPart[] = $wpar;
				$res = $this->xquery($sql_Upd, $paramUpdateSendPart);
			}

			if ($sql_arUpd_StartSend <> '')
			{
				$sql_Upd = $sql_arUpd.'
StartSend = case'.$sql_arUpd_StartSend.'
end'.$sql_arUpd_Where;
				$res = $this->xquery($sql_Upd, $paramUpdateStartSend);
				foreach ($param_Where as $wpar)
					$paramUpdateStartSend[] = $wpar;
				$res = $this->xquery($sql_Upd, $paramUpdateStartSend);
			}

			if ($sql_arUpd_LastModified <> '')
			{
				$sql_Upd = $sql_arUpd.'
LastModified = case'.$sql_arUpd_LastModified.'
end'.$sql_arUpd_Where;
				$res = $this->xquery($sql_Upd, $paramUpdateLastModified);
				foreach ($param_Where as $wpar)
					$paramUpdateLastModified[] = $wpar;
				$res = $this->xquery($sql_Upd, $paramUpdateLastModified);
			}

			$sql_ins .= trim($sql_arIns,",");

			if(trim($sql_arIns,",") <> '')
				$res = $this->xquery($sql_ins, $paramIns);
		}

		return $LastModified;
	}


	public function AddIncoming($record)
	{
			$strSql = "SELECT GUID FROM ".$this->dbpref."b_sms4b_incoming WHERE GUID = ^S AND Part = ^N";
			$res = $this->xquery($strSql, array($record["GUID"],$record["Part"]));
			if($res)
				$arRes = $res->Fetch();

			if(!isset($arRes['GUID']) && $arRes['GUID'] == '')
			{
				if($record["GUID"] == '')
					return false;
				$record["Moment"] = explode('.',$record["Moment"]);
				$record["Moment"] = $record["Moment"][0];
				$record["TimeOff"] = explode('.',$record["TimeOff"]);
				$record["TimeOff"] = $record["TimeOff"][0];

				if ($record["LastModified"] > $LastModified)
					$LastIncoming = $record["Moment"];

				$sql_ins = "insert into ".$this->dbpref."b_sms4b_incoming (GUID,Moment,TimeOff,Source,Destination,Coding,Body,Total,Part) values ";

				$sql_ins .= " (^S,^D,^D,^S,^S,^N,^S,^N,^N)";
				$paramIns = array(
									$record["GUID"],
									$record["Moment"],
									$record["TimeOff"],
									$record["Source"],
									$record["Destination"],
									$record["Coding"],
									$record["Body"],
									$record["Total"],
									$record["Part"]
									);
				$res = $this->xquery($sql_ins, $paramIns);
			}
	}


/*
	function for typification
*/
	public function xquery($qtext,$params)
	{
		global $DB;
		// getting the list of function's arguments  //$this->xquery_error[] =
		if (is_array(func_get_arg(0)))
		{
			$args = func_get_arg(0);
		}
		else
		{
			$args = func_get_args();
		}

		$args = $params;

		if (empty($qtext))
		{
			return false; // Hmm, nothing to do!
		}

		$qtext=str_replace('^@', BX_DBPREF.'_', $qtext); // replacing with table prefixes

		$i = 0; $curArg = 0;

		while ($i < strlen($qtext))
		{
			if ($find = strpos($qtext,'^'))
			{
				if ($curArg>=count($args))
				{
					return false; // too many parameters in the query template!
				}

				$curr_delimeter = substr($qtext,$find+1,1);
				switch ($curr_delimeter)
				{
					case 'N':
					{
						if (is_null($args[$curArg]))
						{
							$qtext=$this->repl($qtext, $find, 'NULL');
							continue;
						}

						if (!is_numeric($args[$curArg]))
						{
							$this->xquery_error[] = ('incorrect parameter, numbers only');
							return false; // incorrect parameter, numbers only!
						}

						$qtext=$this->repl($qtext, $find, $args[$curArg]);

						break;
					}
					case 'S':
					{
						if (is_null($args[$curArg]))
						{
							$qtext=$this->repl($qtext, $find, 'NULL');
							continue;
						}

						$args[$curArg] = mysql_real_escape_string($args[$curArg]);

						$qtext=$this->repl($qtext, $find,"'".$args[$curArg]."'");

						break;
					}
					case 'D':
					{
						if (is_null($args[$curArg]))
						{
							$qtext = $this->repl($qtext, $find, 'NULL');
							continue;
						}

						$time = trim($args[$curArg]);
						$time = str_replace('.','-',$time);

						if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',$time))
							$full_format = 1;
						elseif(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$time))
							$full_format = 1;
						else
						{
							$this->xquery_error[] = ('incorrect date format');
							return false;
						}

						$args[$curArg] = mysql_real_escape_string($args[$curArg]);

						$qtext=$this->repl($qtext, $find, "'".$args[$curArg] ."'");

						break;
					}
					case 'B':
					{
						if (is_null($params[$curArg]))
						{
							$qtext=$this->repl($qtext, $find, 'NULL');
							continue;
						}

						if ($params[$curArg]!=='Y' && $params[$curArg]!=='N')
						{
							$this->xquery_error[] = ('incorrect parameter, bools only');
							return false; // incorrect parameter, bools only!
						}

						$qtext=$this->repl($qtext, $find, $params[$curArg]);

						break;
					}

					case '0':
					{
						if (is_null($args[$curArg]))
						{
							$this->xquery_error[] = ('<br />incorrect parameter, nulls are not allowed');
							return false; // incorrect parameter, nulls are not allowed!
						}

						$args[$curArg]=strtoupper($args[$curArg]);

						if ( ($args[$curArg]!='NULL') && ($args[$curArg]!='NOT NULL') )
						{
							$this->xquery_error[] = ('<br />incorrect parameter, "NULL" or "NOT NULL" only');
							return false; // incorrect parameter, "NULL" or "NOT NULL" only!
						}

						$qtext=$this->repl($qtext, $find, $args[$curArg]);

						break;
					}
					default:
					{
						$qtext=$this->repl($qtext, $find, '  ');
					}
				}

				$curArg++;
			}
			else
			{
				$i++;
			}
		}

//		global $USER;
//		$USER->IsAdmin() ? $show = false : $show = true;

		$res = $DB->Query($qtext, $show, $err_mess.__LINE__);

		if (!$res) return false;
		return $res;
	}

	//function for xquery
	private function repl($qtext, $pos, $with)
	{
		return substr($qtext,0,$pos).$with.substr($qtext, $pos+2);
	}

}

global $SMS4B;
if (!is_object($SMS4B))
	$SMS4B = new Csms4b();
?>