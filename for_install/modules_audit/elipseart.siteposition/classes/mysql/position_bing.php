<?
class CEASitePositionBing
{
	function GetParam()
	{
		$module_id = "elipseart.siteposition";
		$res["KEY"] = COption::GetOptionString($module_id, "BING_KEY");
		
		return $res;
	}
	
	function GetPosition($host,$query,$region="")
	{
		global $APPLICATION;
		
		if(!empty($host) && !empty($query))
		{
			$arError = false;
			
			$param = CEASitePositionBing::GetParam();
			$query = htmlspecialchars($query);
			$host = htmlspecialchars($host);
			$result_num = 50;
			
			if(empty($param["KEY"]))
			{
				$result["ERROR"] = "key not valid";
				return $result;
			}
			
			for($x=0;$x<2;++$x)
			{
				$ServiceRootURL =  'https://api.datamarket.azure.com/Bing/Search/';
				$WebSearchURL = $ServiceRootURL . 'Web?$format=json&$top=50&$skip='.($x*$result_num).'&Market=%27ru-RU%27&Query=';
				$context = stream_context_create(array(
					'http' => array(
						'request_fulluri' => true,
						'header'  => "Authorization: Basic " . base64_encode($param["KEY"] . ":" . $param["KEY"])
					)
				));
				$request = $WebSearchURL . urlencode( '\'' . $APPLICATION->ConvertCharset($query, "Windows-1251", "UTF-8") . '\'');
				$response = file_get_contents($request, 0, $context);
				
				if($response)
				{
					$response = $APPLICATION->ConvertCharset($response, "UTF-8", "Windows-1251");
					//$response = json_decode($response);
					$response = CUtil::JsObjectToPhp($response);
					
					if ($response["d"]["errors"])
					{
						foreach($response["d"]["errors"] as $error)
						{
							$result["ERROR"] .= "[".$error["Code"]."] ".$error["Message"];
						}
						
						return $result;
					}
					elseif(is_array($response["d"]["results"]))
					{
						$i = 0;
						foreach ($response["d"]["results"] as $item)
						{
							++$i;
							$result["POSITION"] = $x*$result_num+$i;
							$result["LINK"] = strtolower($item["Url"]);
							
							$res_url = explode("/",$result["LINK"]);
							
							$res_page = $res_url;
							unset($res_page[0]);
							unset($res_page[1]);
							$result["PAGE"] = implode("/",$res_page);
							$result["PAGE"] = str_replace($res_url[2],"",$result["PAGE"]);
							if(empty($result["PAGE"]))
								$result["PAGE"] = "/";
							
							//if(ereg($host,$res_url[2]))
							if(strpos($res_url[2],$host) !== false)
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
			"SEARCH_NAME" => array("BING"),
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
				
				$dateDB = explode(" ",$res["DATE_BING"]);
				$dateDB = $dateDB[0];
				
				if(($dateDB != date("Y-m-d") || $reupdate == "reupdate") && $i < $limit)
				{
					$position = CEASitePositionBing::GetPosition($res["HOST_NAME"],$res["NAME"],$res["REGION_CODE"]);
					
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
									"SEARCH_ID" => 3,
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
									CEASitePosition::Add("3",$res["ID"],$arParam);
							}
						}
						else
							CEASitePosition::Add("3",$res["ID"],$arParam);
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