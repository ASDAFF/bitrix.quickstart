<?
IncludeModuleLangFile(__FILE__);

class CAltasibping
{
	var $siteName;
	var $siteURL;
	var $pageURL;
	var $feedURL;

	function PrepareUrl($url)
	{
		$path_var = explode(",",COption::GetOptionString("altasib.ping", "url_impotant_params"));
		foreach($path_var as $key=>$val)
		{
			$path_var[$key] = trim($val);
		}

		$parse = parse_url($url);
		$path = $parse["path"];
		$query = $parse["query"];
		parse_str($query, $path_vars);

		$result = array();
		ksort($path_vars);
		foreach($path_vars as $key=>$vals)
		{
			if(in_array($key, $path_var))
			{
				$result[$key] = $vals;
			}
		}
		if(!empty($result))
			$query = "?".http_build_query($result);
		else
			$query ="";
		return $parse['scheme']."://".$parse['host'].$path.$query;
	}

	function CheckPingStr($siteName, $pageURL, $SearchURL)
	{
		global $DB;
		$obQuery = $DB->Query("SELECT * FROM `altasib_ping_log` WHERE `NAME` = '".$DB->ForSql($siteName)."' AND `URL`='".$DB->ForSql($pageURL)."' AND `RESULT`='OK' AND `SEACH`='".$DB->ForSql($SearchURL)."' ORDER BY `COUNT` DESC");
		if($arQuery = $obQuery->Fetch()){
			return array('RESULT'=>true, "LASTPING"=>$arQuery);
		} else {
			return array('RESULT'=>false);
		}
	}

	function CheckArray(&$arFields = array(), $arCheck = array(), $bCheckKeys = false, $bCheckEmpty = false)
	{
		if(!is_array($arFields) || !is_array($arCheck))
		{
			$this->last_error = 'Error: param is not array';
			return false;
		}
		if(empty($arFields) && $bCheckEmpty)
		{
			$this->last_error = 'Error: param is empty';
			return false;
		}

		$arTmpFields = $arFields;
		foreach($arFields as $key => $field)
		{
			if(is_array($field))
			{
				if(!is_numeric(trim($key)) && !trim($key))
					continue;
			}
			else
			{
				if((!trim($field)) || (!is_numeric(trim($key)) && !trim($key)))
					continue;
			}

			$arTmpFields[trim($key)] = is_array($field) ? $field : trim($field);
		}
		$arFields = $arTmpFields;

		$arTmpCheck = $arCheck;
		foreach($arCheck as $key => $field)
		{
			if((!trim($field)) || (!is_numeric(trim($key)) && !trim($key)))
				continue;

			$arTmpCheck[trim($key)] = trim($field);
		}
		$arCheck = $arTmpCheck;

		if(!empty($arCheck))
		{
			foreach($arTmpFields as $key => $field)
			{
				if(preg_match("/([a-z_]+)\.(.*)/i", $key, $m))
					continue;
				if(!$bCheckKeys)
				{
					if(!in_array($field, $arCheck))
						unset($arFields[$key]);
				}
				else
				{
					if(!in_array($key, $arCheck))
						unset($arFields[$key]);
				}
			}
		}

		if(empty($arFields) && $bCheckEmpty)
		{
			$this->last_error = 'Error: param is not correct';
			return false;
		}

		return true;
	}


	function GetTableFields($table_name)
	{
		global $DB;
		$arFields = array();
		$db_result = mysql_list_fields($DB->DBName, $table_name, $DB->db_Conn);
		if($db_result <= 0)
		{
			$this->last_error = 'Error: Can\'t read table';
			return $arFields;
		}
		$intNumFields = mysql_num_fields($db_result);

		while (--$intNumFields >= 0)
			$arFields[] = mysql_field_name($db_result, $intNumFields);

		return $arFields;
	}

	function GetList($table_name, $arFilter = array(), $arSelect = array(), $arSort = array(), $arNavigation = array(), $Logic = "AND")
	{
		global $DB;

		$result = new CDBResult;

		$arAvailFields = CAltasibping::GetTableFields($table_name);
		if(empty($arAvailFields))
		{
			$this->last_error = 'Error: can not get table fields';
			return $result;
		}

		if(!CAltasibping::CheckArray($arFilter))
			return $result;

		$bOtherTable = false;
		$arFilterKeys = array_keys($arFilter);

		foreach($arFilterKeys as $key)
		{
			$bFound = false;
			foreach($arAvailFields as $field)
				if(preg_match('/^[\<\>\=\!\?]{0,2}'.$field.'$/i', $key))
					$bFound = true;

			if(preg_match("/([a-z_]+)\.(.*)/i", $key, $m))
			{
				$bOtherTable = true;
					continue;
			}
			if(!$bFound && !$arFilter[$key]["LOGIC"])
				unset($arFilter[$key]);
		}

		if(!CAltasibping::CheckArray($arSelect, $arAvailFields))
			return $result;
		if(!CAltasibping::CheckArray($arSort, $arAvailFields, true))
			return $result;

		$arTmpSort = array();
		foreach($arSort as $field => $sort)
			if(in_array($sort, array("ASC", "DESC")))
				$arTmpSort[] = $field.' '.$sort;

		if(!CAltasibping::CheckArray($arNavigation))
			return $result;

		$arTmpNavigation = array();
		$arNavAvailKeys = array('nPageSize', 'nPageSizeMax', 'nPageNum', 'getCount');
		foreach($arNavigation as $key => $value)
		{
			if($key == 'getCount')
				$arTmpNavigation[$key] = trim($value);
			elseif(in_array($key, $arNavAvailKeys) && intval($value))
				$arTmpNavigation[$key] = intval($value);
		}
		$arNavigation = $arTmpNavigation;

		$strSql = "SELECT DISTINCT ";
		if($bOtherTable && !empty($arSelect))
		{
			foreach($arSelect as $k=>$v)
				$arSelect[$k] = $table_name.".".$v;
		}

		if($arNavigation["getCount"]) /* in page navigation - get count all records */
		{
			if(in_array($arNavigation["getCount"], $arAvailFields))
				$strSql .= "SQL_CALC_FOUND_ROWS ".$arNavigation["getCount"]." ";
			else
				$strSql .= "SQL_CALC_FOUND_ROWS ID ";
		}
		elseif(!empty($arSelect))
			$strSql .= implode(', ', $arSelect)." ";
		else
			$strSql .= ($bOtherTable ? $table_name."." : "")."* ";
		$strSql .= "FROM ".$table_name." ";

		$Logic = ($Logic == 'OR') ? 'OR' : 'AND';

		if(!empty($arFilter))
		{
			$strSql .= "WHERE ";
			$arTmpFilter = array();
			foreach($arFilter as $key => $value)
			{
				if(preg_match("/([a-z_]+)\.(.*)/i", $key, $m))
				{
					if(!$tmp_table_name)
						$tmp_table_name = $table_name;

					if(strpos($tmp_table_name,$m[1]) === false)
					{
						$strSql = str_replace("FROM ".$tmp_table_name." ", "FROM ".$tmp_table_name.",".$m[1]." ",$strSql);
						$tmp_table_name = $tmp_table_name.",".$m[1];
					}
				}
				if(preg_match("/([a-z_]+)\.(.*)/i", $value, $m))
				{
					$arTmpFilter[] = $key." = '".$DB->ForSql($value)."' ";
				}
				elseif(is_array($value))
				{
					if ($value["LOGIC"] == "OR")
					{
						foreach($value as $i => $v)
						{
							if ($i != "LOGIC")
							{
								if(preg_match('/^([\<\>\=\!\?]{0,2})(.*)$/i', $i, $mFilter))
								{
									switch($mFilter[1])
									{
										case '!=':
											if ($v == "")
												$arTmpFilterLOGIC[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." IS NOT NULL ";
											else
												$arTmpFilterLOGIC[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." <> '".$DB->ForSql($v)."' ";
											break;
										case '!':
											$arTmpFilterLOGIC[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." <> '".$DB->ForSql($v)."' ";
											break;
										case '>':
										case '>=':
										case '<':
										case '<=':
											$arTmpFilterLOGIC[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." ".$mFilter[1]."  '".$DB->ForSql($v)."' ";
											break;
										case '?':
											$arTmpFilterLOGIC[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." LIKE '%".$DB->ForSql($v)."%' ";
											break;
										case '??':
											$arTmpFilterLOGIC[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." LIKE '%".$DB->ForSql($v)."' ";
											break;
										default:
											if ($v == "")
												$arTmpFilterLOGIC[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." IS NULL ";
											else
												$arTmpFilterLOGIC[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." = '".$DB->ForSql($v)."' ";
											break;
									}
								}
							}

						}
						$arTmpFilter[] = "(".implode(' '.$value["LOGIC"].' ', $arTmpFilterLOGIC).") ";
					}
					else
					{
						$not = '';
						$kkey = $key;

						if(preg_match('/^\!\?(.*)$/i', $key, $mFilter))
						{
							$kkey = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[1]) ? $table_name."." : "").$mFilter[1];
							foreach($value as $i => $v)
								$value[$i] = $kkey." NOT LIKE '%".$DB->ForSql($v)."%'";

							$arTmpFilter[] = "(".implode(" AND ", $value).") ";

							continue;
						}
						elseif(preg_match('/^\?\?(.*)$/i', $key, $mFilter))
						{
							$kkey = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[1]) ? $table_name."." : "").$mFilter[1];
							foreach($value as $i => $v)
								$value[$i] = $kkey." LIKE '%".$DB->ForSql($v)."'";

							$arTmpFilter[] = "(".implode(" OR ", $value).") ";

							continue;
						}
						elseif(preg_match('/^\?(.*)$/i', $key, $mFilter))
						{
							$kkey = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[1]) ? $table_name."." : "").$mFilter[1];
							foreach($value as $i => $v)
								$value[$i] = $kkey." LIKE '%".$DB->ForSql($v)."%'";

							$arTmpFilter[] = "(".implode(" OR ", $value).") ";

							continue;
						}
						elseif(preg_match('/^\!(.*)$/i', $key, $mFilter))
						{
							$not = ' NOT ';
							$kkey = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[1]) ? $table_name."." : "").$mFilter[1];
						}

						$not = $not ? '<>' : '';
						$operation = $not ? '<>' : '=';
						$value = array_unique($value);


						if($operation == '=')
						{
							$newvalue = array();
							$IssetVal = false;
							foreach($value as $i => $v)
							{
								if(strlen(trim($v))>0)
									$newvalue[$i] = $DB->ForSql($v);
							}
							if(count($newvalue)>0) {
								$arTmpFilter[] = $kkey." in (".implode(",", $newvalue).") ";
							}
							else {
								foreach($value as $i => $v)
								$value[$i] = $kkey." ".$operation." '".$DB->ForSql($v)."'";

								$arTmpFilter[] = "(".implode(" OR ", $value).") ";
							}
						}
						else
						{
							foreach($value as $i => $v)
								$value[$i] = $kkey." ".$operation." '".$DB->ForSql($v)."'";

							$arTmpFilter[] = "(".implode(" AND ", $value).") ";
						}
					}
				}
				elseif(preg_match('/^([\<\>\=\!\?]{0,2})(.*)$/i', $key, $mFilter))
				{
					switch($mFilter[1])
					{
						case '!=':
							if ($value == "")
								$arTmpFilter[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." IS NOT NULL ";
							else
								$arTmpFilter[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." <> '".$DB->ForSql($v)."' ";
						break;
						case '!':
							$arTmpFilter[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." <> '".$DB->ForSql($value)."' ";
						break;
						case '>':
						case '>=':
						case '<':
						case '<=':
							$arTmpFilter[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." ".$mFilter[1]."  '".$DB->ForSql($value)."' ";
						break;
						case '?':
							$arTmpFilter[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." LIKE '%".$DB->ForSql($value)."%' ";
						break;
						case '??':
							$arTmpFilter[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." LIKE '%".$DB->ForSql($value)."' ";
						break;
						default:
							if ($value == "")
								$arTmpFilter[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." IS NULL ";
							else
								$arTmpFilter[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $mFilter[2]) ? $table_name."." : "").$mFilter[2]." = '".$DB->ForSql($value)."' ";
						break;
					}
				}
				else
					$arTmpFilter[] = ($bOtherTable && !preg_match("/([a-z_]+)\.(.*)/i", $key) ? $table_name."." : "").$key." = '".$DB->ForSql($value)."' ";

			}
			$strSql .= implode(' '.$Logic.' ', $arTmpFilter);
		}
		else
			$strSql .= "WHERE 1 = 1";

		if(!empty($arTmpSort))
			$strSql .= " ORDER BY ".implode(', ', $arTmpSort);

		if(defined("DEBUG_QUERY_ALX") && DEBUG_QUERY_ALX)


		if($arNavigation["nPageNum"] || $arNavigation["nPageSize"])
		{
			$arNavigation["nPageSizeMax"] = $arNavigation["nPageSizeMax"] ? $arNavigation["nPageSizeMax"] : $arNavigation["nPageSize"];
			$arNavigation["nPageNum"] = $arNavigation["nPageNum"] ? $arNavigation["nPageNum"] : 1;
			$arNavigation["nPageSize"] = $arNavigation["nPageSize"] ? $arNavigation["nPageSize"] : 20;

			$strSql .= " LIMIT ".($arNavigation["nPageNum"] * $arNavigation["nPageSize"] - $arNavigation["nPageSize"]).", ".$arNavigation["nPageSizeMax"];
		}
		if($arNavigation["getCount"])
		{
			if(strpos($strSql, "LIMIT") === false)
				$strSql .= " LIMIT 1";

			$DB->Query($strSql);
			$rs = $DB->Query("SELECT FOUND_ROWS() AS COUNT");
			$ar = $rs->Fetch();
			return $ar["COUNT"];
		}
		else
		{
			return $DB->Query($strSql);
		}
	}

	function CAltasibping($siteName,$siteURL,$pageURL,$feedURL){
		$this -> siteName = $siteName;
		$this -> siteURL = $siteURL;
		$this -> pageURL = $pageURL;
		$this -> feedURL = $feedURL;
	}

	function CAltasibwrite_table_ping($blogID, $blogName, $active, $blogUrl,$key)
	{
		if ($active=="Y"){ $active=true; }else{ $active=0;}
		global $DB;

		$res = $DB->Query("INSERT INTO `altasib_table_ping` (
				ID,
				SITE_ID,
				DATE,
				TIME,
				NAME,
				URL,
				A
			) VALUES
			('".intval($blogID)."','".$DB->ForSql($key)."','".$DB->ForSql(date('Y-m-d'))."', '".$DB->ForSql(date("H:i:s"))."','".$DB->ForSql($blogName)."', '".$DB->ForSql($blogUrl)."', ".intval($active).")
		");
	}


	function SendPing($blogName, $blogUrl,/* $key, */ $arURLping/* , $blogXml = "" */)
	{
		if (defined("SITE_CHARSET") && strlen(SITE_CHARSET) > 0)
			$serverCharset = SITE_CHARSET;
		else
			$serverCharset = "windows-1251";
		if(strlen($blogName) <= 0)
			return false;
		if(strlen($blogUrl) <= 0)
			return false;
		$blogName = $GLOBALS["APPLICATION"]->ConvertCharset(trim($blogName), $serverCharset, "UTF-8");
		$blogUrl = $GLOBALS["APPLICATION"]->ConvertCharset(trim($blogUrl), $serverCharset, "UTF-8");
//		$blogXml = $GLOBALS["APPLICATION"]->ConvertCharset(trim($blogXml), $serverCharset, "UTF-8");

		$query .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
		$query .= "<methodCall>\r\n";
		$query .= "<methodName>weblogUpdates.ping</methodName>\r\n";
		$query .= "<params>\r\n";
		$query .= "<param>\r\n";
		$query .= "<value>".htmlspecialcharsEx($blogName)."</value>\r\n";
		$query .= "</param>\r\n";
		$query .= "<param>\r\n";
		$query .= "<value>".htmlspecialcharsEx($blogUrl)."</value>\r\n";
		$query .= "</param>\r\n";
/*		if(strlen($blogXml) > 0)
		$query .= "<param>
				<value>".htmlspecialcharsEx($blogXml)."</value>
			</param>"; */
		$query .= "</params>\r\n";
		$query .= "</methodCall>";

		$len = count(str_split($query));

		if(!is_array($arURLping) && strlen($arURLping)>0){
			$arURLping = unserialize($arURLping);
		}else{
			$arURLping= array();
		}
		$urls = COption::GetOptionString("altasib.ping", "send_blog_ping_address");

		if($urls = COption::GetOptionString("altasib.ping", "send_blog_ping_address"))
		{
			if(count($arURLping)>0){
				$arUrls = $arURLping;
			} else {
				$arUrls = explode("\r\n", $urls);
			}

			$count=0;
			$arData=array();
			if(!empty($arUrls))
			{
				foreach($arUrls as $v)
				{
					if(strlen($v) > 0)
					{
						$arData["URL"][$count] = $v;
						// CAltasibping::writelog($v.";",$key);

						$v = str_replace("http://", "", $v);
						$v = str_replace("https://", "", $v);
						$arPingUrl = explode("/", $v);

						$host = trim($arPingUrl[0]);
						unset($arPingUrl[0]);
						$path = "/".trim(implode("/", $arPingUrl));

						$arHost = explode(":", $host);
						$port = ((count($arHost) > 1) ? $arHost[1] : 80);
						$host = $arHost[0];

						if(strlen($host) > 0)
						{
							$fp = @fsockopen($host, $port, $errno, $errstr, 30);

							if ($fp)
							{
								$out = "";
								$out .= "POST ".$path." HTTP/1.1\r\n";
								$out .= "Host: ".$host." \r\n";
								$out .= "Content-type: text/xml\r\n";
								$out .= "User-Agent: bitrixBlog\r\n";
								$out .= "Content-length: ".$len."\r\n\r\n";
								$out = $GLOBALS["APPLICATION"]->ConvertCharset($out, $serverCharset, "UTF-8");
								$out .= $query;

								fputs($fp, $out);
								$response = fgets($fp, 256);

								fclose($fp);

								// if(eregi("HTTP/1.[01] 200 OK", $response))
								if(preg_match("/HTTP\/1.[01] 200 OK/i", $response)
									|| preg_match("/Thanks for the ping/i", $response))
								{
									$arData[$count] = "OK";
								}
								else
								{
									$arData[$count] = "Server error ping: ".htmlspecialcharsEx(str_replace("HTTP/1.1", "", $response));
								}
							}
							else
							{
								$arData[$count] = date("d.m.Y G:i:s").": Cannot open connection: ".$errstr." (".$errno.")";
							}
						}
						$count++;
					}
				}
			}
		}
		return $arData;
	}

	function OnUpdateElement($arFields){
		$bActive = true;
		$curTime = ConvertTimeStamp(time(),"FULL");
		global $DB;
		if($arFields['ACTIVE'] != "Y"
			|| (!empty($arFields['ACTIVE_FROM']) && $DB->CompareDates($arFields['ACTIVE_FROM'], $curTime) == 1)
			|| (!empty($arFields['ACTIVE_TO']) && $DB->CompareDates($arFields['ACTIVE_TO'], $curTime) == -1)
		)
			$bActive = false;
		$obQuery = $DB->Query("SELECT * FROM `altasib_table_ping` WHERE `ID` = ".intval($arFields["ID"]));

		if($bActive){
			if($arQuery = $obQuery->Fetch())
				$res_ = $DB->Query("UPDATE `altasib_table_ping` SET `A`=1 WHERE `COUNT`=".intval($arQuery["COUNT"]));
			else
				CAltasibping::OnUpdatesEvent($arFields);
		} else {
			if($arQuery = $obQuery->Fetch()) // must be removed
				$res_ = $DB->Query("UPDATE `altasib_table_ping` SET `A`=0 WHERE `COUNT`=".intval($arQuery["COUNT"]));
		}
	}

	function OnUpdatesEvent($arFields){
		if ($arFields['ACTIVE'] != "Y")
			return;

		global $DB;
		$curTime = ConvertTimeStamp(time(), "FULL");
		if((!empty($arFields['ACTIVE_FROM']) && $DB->CompareDates($arFields['ACTIVE_FROM'], $curTime) == 1)
			|| (!empty($arFields['ACTIVE_TO']) && $DB->CompareDates($arFields['ACTIVE_TO'], $curTime) == -1))
				return;

		if (!CModule::IncludeModule("altasib.ping") || !CModule::IncludeModule("iblock"))
			return;

		if (!isset($arFields['RESULT_MESSAGE']) && !empty($arFields['NAME']))
		{
			if (!isset($arFields['WF_PARENT_ELEMENT_ID']) || $arFields['WF_PARENT_ELEMENT_ID'] == $arFields['RESULT'])
			{
				$SITES = array();
				$rsSites = CIBlock::GetSite($arFields["IBLOCK_ID"]);
				while($arSite = $rsSites->Fetch()){
					$SITES[$arSite['SITE_ID']] = $arSite;
				}

				$res = CIBlock::GetByID(intval($arFields["IBLOCK_ID"]));
				if($ar_res = $res->GetNext())
				{
					$arSearch = array(
						"#SITE_DIR#",
						"#SERVER_NAME#",
						"#IBLOCK_TYPE_ID#",
						"#IBLOCK_ID#",
						"#IBLOCK_CODE#",
						"#IBLOCK_EXTERNAL_ID#",
						"#SECTION_ID#",
						"#SECTION_CODE#",
						"#CODE#",
						"#ELEMENT_CODE#",
						"#ID#",
						"#ELEMENT_ID#",
						"#EXTERNAL_ID#"
					);

					foreach($SITES as $siteid => $site){
						$IDs_Ibock = COption::GetOptionString("altasib.ping", 'IBLOCK_ID', '', $siteid);
						$IDs_Ibock = explode(",", $IDs_Ibock);

						if (in_array($arFields["IBLOCK_ID"], $IDs_Ibock))
						{
							$obIBElem = CIBlockElement::GetList(Array(), Array("ID" => $arFields["ID"]), false, false, Array("DETAIL_PAGE_URL", "ID", "IBLOCK_ID", "NAME","ACTIVE","IBLOCK_TYPE_ID","IBLOCK_CODE","IBLOCK_EXTERNAL_ID","IBLOCK_SECTION_ID","SECTION_CODE","CODE","EXTERNAL_ID"));

							if($arIBElem = $obIBElem->GetNext()){ // Fetch()

								if(!empty($arIBElem['IBLOCK_SECTION_ID'])){
									$ressec = CIBlockSection::GetByID($arIBElem['IBLOCK_SECTION_ID']);

									if($ar_sec1 = $ressec->GetNext()){
										$arSection = $ar_sec1;
									}
								}
							}

							$arPage[$siteid] = array(
								"SITE_DIR" => substr($site["DIR"] , 0, -1),
								"SERVER_NAME" => "",//$site["SERVER_NAME"],
								"IBLOCK_TYPE_ID" => $arIBElem["IBLOCK_TYPE_ID"],
								"IBLOCK_ID" => $arIBElem["IBLOCK_ID"],
								"IBLOCK_CODE" => $arIBElem["IBLOCK_CODE"],
								"IBLOCK_EXTERNAL_ID" => $arIBElem["IBLOCK_EXTERNAL_ID"],
								"SECTION_ID" => $arIBElem["IBLOCK_SECTION_ID"],
								"SECTION_CODE" => $arSection["CODE"],
								"CODE" => $arIBElem["CODE"],
								"ELEMENT_CODE" => $arIBElem["CODE"],
								"ID" => $arIBElem["ID"],
								"ELEMENT_ID" => $arIBElem["ID"],
								"EXTERNAL_ID" => $arIBElem["EXTERNAL_ID"],
							);

							if(preg_match_all('/#([A-Za-z0-9_]{1,})#/',$arIBElem["DETAIL_PAGE_URL"], $arZamena)!=false){
								$detail_page = str_replace($arSearch, array_values($arPage[$siteid]), $arIBElem["DETAIL_PAGE_URL"]);
								$detail_page = preg_replace("'(?<!:)/+'s", "/", $detail_page);
							} else {
								if(!empty($arIBElem["DETAIL_PAGE_URL"]))
									$detail_page = $arIBElem["DETAIL_PAGE_URL"];
							}
							$scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ||
								(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ? "https" : "http");

							if ($site["SERVER_NAME"]){
								$siteURL = $scheme."://".str_replace("http://", "", $site["SERVER_NAME"]);
								$pageURL = $scheme."://".str_replace("http://", "", $site["SERVER_NAME"]).$detail_page;
							}else{
								$siteURL = $scheme."://".$_SERVER["SERVER_NAME"];
								$pageURL = $scheme."://".$_SERVER["SERVER_NAME"].$detail_page;
							}

							CAltasibping::CAltasibwrite_table_ping($arIBElem["ID"],$arIBElem["NAME"],$arIBElem["ACTIVE"], $pageURL, $site["SITE_ID"]);
						}
						unset($IDs_Ibock);
					}
				}
			}
		}
	}
}
?>