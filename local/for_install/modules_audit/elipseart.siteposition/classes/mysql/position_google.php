<?
class CEASitePositionGoogle
{
	function GetParam($key_num=0)
	{
		$module_id = "elipseart.siteposition";
		$res["KEY"] = COption::GetOptionString($module_id, "GOOGLE_KEY_".intval($key_num));
		
		return $res;
	}
	
	function GetPosition($host,$query,$region="",$key_num=0)
	{
		global $APPLICATION;
		
		if(!empty($host) && !empty($query))
		{
			$arError = false;
			
			$param = CEASitePositionGoogle::GetParam(intval($key_num));
			$query = htmlspecialchars($query);
			$host = htmlspecialchars($host);
			$result_num = 10;
			
			if(empty($param["KEY"]))
			{
				$result["ERROR"] = "key not valid";
				return $result;
			}
			
			for($x=0;$x<10;++$x)
			{
				$file  = "https://www.googleapis.com/customsearch/v1";
				$file .= "?key=".$param["KEY"];
				$file .= "&cx=013036536707430787589:_pqjad5hr1a";
				$file .= "&q=".UrlEncode($APPLICATION->ConvertCharset($query, "Windows-1251", "UTF-8"));
				$file .= "&hl=ru";
				$file .= "&num=".$result_num;
				$file .= "&start=".($x*10+1);
				
				$response = file_get_contents($file);
				$response = $APPLICATION->ConvertCharset($response, "UTF-8", "Windows-1251");
				
				if($response)
				{
					//$response = json_decode($response);
					$response = CUtil::JsObjectToPhp($response);
					
					if ($error)
					{
						//$result["ERROR"] = $error;
						//return $result;
					}
					elseif(is_array($response["items"]))
					{
						$i = 0;
						foreach ($response["items"] as $item)
						{
							++$i;
							$result["POSITION"] = $x*$result_num+$i;
							$result["LINK"] = strtolower($item["link"]);
							
							$res_url = explode("/",$result["LINK"]);
							
							$res_page = $res_url;
							unset($res_page[0]);
							unset($res_page[1]);
							$result["PAGE"] = implode("/",$res_page);
							$result["PAGE"] = str_replace($res_url[2],"",$result["PAGE"]);
							if(empty($result["PAGE"]))
								$result["PAGE"] = "/";
							
							if(strpos($res_url[2],$host) !== false)
							//if(ereg($host,$res_url[2]))
							{
								return $result;
							}
			            }
			            
			            
			        }
			
			    }
				else
				{
					$result = array("ERROR" => "Internal Server Error");
					return $result;
			    }
				
			}
			
			return array("POSITION"=>0);
		}
		
		return false;
	}
	
	function Update($ID=false,$reupdate=false,$timeValid=false)
	{
		$module_id = "elipseart.siteposition";
		
		$arFilter = array(
			"ACTIVE" => "Y",
			"SEARCH_NAME" => array("GOOGLE"),
		);
		
		if($ID > 0)
			$arFilter["ID"] = intval($ID);
		
		$limit = COption::GetOptionString($module_id, "LIMIT");
		if($limit < 0)
			$limit = 10;
		
		$timeFrom = COption::GetOptionString($module_id, "TIME_FROM");
		if(empty($timeFrom))
			$timeFrom = "00:00";
		
		$timeTo = COption::GetOptionString($module_id, "TIME_TO");
		if(empty($timeTo))
			$timeTo = "00:00";
		
		if(
			(($timeFrom != "00:00" || $timeTo != "00:00") && date("H:i") > $timeFrom && date("H:i") < $timeTo)
			|| $timeValid == "N" 
		)
		{
			$i = 0;
			$rsData = CEASitePositionKeyword::GetList(
				array(
					"SORT" => "ASC"
				),
				$arFilter,
				true,
				false,
				false
			);
			while($res = $rsData->Fetch())
			{
				$position = false;
				
				$dateDB = explode(" ",$res["DATE_GOOGLE"]);
				$dateDB = $dateDB[0];
				
				if(($dateDB != date("Y-m-d") || $reupdate == "reupdate") && $i < $limit)
				{
					$key_num = 0;
					while(!is_array($position) || $position["ERROR"] == "Internal Server Error")
					{
						$newParam = CEASitePositionGoogle::GetParam($key_num);
						
						if(!empty($newParam["KEY"]))
						{
							$position = CEASitePositionGoogle::GetPosition($res["HOST_NAME"],$res["NAME"],$res["REGION_CODE"],$key_num);
						}	
						else
							break;
	
						++$key_num;
					}
					
					if(is_array($position) && empty($position["ERROR"]))
					{
						$arParam = array(
							"ACTIVE" => "Y",
							"DATE" => date("Y-m-d H:i:s"),
							"POSITION" => $position["POSITION"],
							"PAGE" => $position["PAGE"],
						);
						
						if($reupdate == "reupdate")
						{
							$ruData = CEASitePosition::GetList(
								array(
									"DATE" => "DESC"
								),
								array(
									"SEARCH_ID" => 2,
									"KEYWORD_ID" => $res["ID"]
								),
								1
							);
							if($resPos = $ruData->Fetch())
							{
								$datePosDB = explode(" ",$resPos["DATE"]);
								$datePosDB = $datePosDB[0];
								if($datePosDB == date("Y-m-d"))
									CEASitePosition::Update($resPos["ID"],$arParam);
								else
									CEASitePosition::Add("2",$res["ID"],$arParam);
							}
						}
						else
							CEASitePosition::Add("2",$res["ID"],$arParam);
					}
					elseif(!empty($position["ERROR"]))
					{
						
					}
					
					++$i;
				}
			}
		}
	}
	
}
?>