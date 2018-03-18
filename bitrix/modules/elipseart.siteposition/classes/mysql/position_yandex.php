<?
class CEASitePositionYandex
{
	function GetParam()
	{
		$module_id = "elipseart.siteposition";
		$res["USER"] = COption::GetOptionString($module_id, "YANDEX_LOGIN");
		$res["KEY"] = COption::GetOptionString($module_id, "YANDEX_KEY");
		
		return $res;
	}
	
	function GetPosition($host,$query,$region="")
	{
		global $APPLICATION;
		
		if(!empty($host) && !empty($query))
		{
			$arError = false;
			
			$param = CEASitePositionYandex::GetParam();
			$query = htmlspecialchars($query);
			$host = htmlspecialchars($host);
			$page = 0;
			$result_num = 100;
			
			if(empty($param["USER"]))
			{
				$result["ERROR"] = "user not valid";
				return $result;
			}
			if(empty($param["KEY"]))
			{
				$result["ERROR"] = "key not valid";
				return $result;
			}
			
			$file  = "http://xmlsearch.yandex.ru/xmlsearch";
			$file .= "?user=".$param["USER"];
			$file .= "&key=".$param["KEY"];
			$file .= "&query=".UrlEncode($query);
			$file .= "&page=".$page;
			$file .= "&lr=".$region;
			$file .= "&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D".$result_num.".docs-in-group%3D1"; 
			
			$response = file_get_contents($file);
			
			if($response)
			{
				$xmldoc = new SimpleXMLElement($response);
				
				$error = $xmldoc->response->error;
				$found_all = $xmldoc->response->found;
				$found = $xmldoc->xpath("response/results/grouping/group/doc");
				
				if ($error)
				{
					$result["ERROR"] = $APPLICATION->ConvertCharset($error[0], "UTF-8", "Windows-1251");
					return $result;
				}
				else
				{
					$ALL_RESULT = $found_all->asXML();
					
					$i = 0;
					
					foreach ($found as $item)
					{
						++$i;
						$result["POSITION"] = $i;
						$result["LINK"] = strtolower($item->url->asXML());
						$result["LINK"] = $APPLICATION->ConvertCharset($result["LINK"], "UTF-8", "Windows-1251");
						
						$res_url = explode("/",$result["LINK"]);
						
						$res_page = $res_url;
						unset($res_page[0]);
						unset($res_page[1]);
						$result["PAGE"] = implode("/",$res_page);
						$result["PAGE"] = str_replace($res_url[2],"",$result["PAGE"]);
						$result["PAGE"] = str_replace("</url>","",$result["PAGE"]);
						if(empty($result["PAGE"]))
							$result["PAGE"] = "/";
						
						if(strpos($res_url[2],$host) !== false)
						//if(ereg($host,$res_url[2]))
						{
							return $result;
						}
					}
					
					return array("POSITION"=>0);
				}
			}
			else
			{
				$result = array("ERROR" => "Internal Server Error");
				return $result;
			}
			
			return false;
		}
		
		return false;
	}
	
	function Update($ID=false,$reupdate=false,$timeValid=false)
	{
		$module_id = "elipseart.siteposition";
		
		$arFilter = array(
			"ACTIVE" => "Y",
			"SEARCH_NAME" => array("YANDEX"),
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
				$dateDB = explode(" ",$res["DATE_YANDEX"]);
				$dateDB = $dateDB[0];
				
				if(($dateDB != date("Y-m-d") || $reupdate == "reupdate") && $i < $limit)
				{
					$position = CEASitePositionYandex::GetPosition($res["HOST_NAME"],$res["NAME"],$res["REGION_CODE"]);
					
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
									"SEARCH_ID" => 1,
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
									CEASitePosition::Add("1",$res["ID"],$arParam);
							}
						}
						else
							CEASitePosition::Add("1",$res["ID"],$arParam);
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