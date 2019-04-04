<?
	
	function dwGetCity($UTF8){

		$bd = file(dirname(__FILE__)."/files/cidr_optim.txt");
		if($UTF8 === true){
			$city = file(dirname(__FILE__)."/files/cities_utf8.txt");
		}else{
			$city = file(dirname(__FILE__)."/files/cities.txt");
		}

		$ip = ip2long($_SERVER["REMOTE_ADDR"]);

		if(!empty($bd) && !empty($ip)){
			
			foreach ($bd as $i => $str) {
				
				$exStr = explode("	", $str);
				$arRange = explode("-", $exStr[0]);

				if(ip2long(trim($arRange[0])) <= $ip && ip2long(trim($arRange[1])) >= $ip){
					if(!empty($exStr[2])){
						if(!empty($city)){
							foreach ($city as $x => $cStr) {
								$exCstr = explode("	", $cStr);
								$exStr[2] = trim($exStr[2]);
								if($exCstr[0] == $exStr[2]){
									unset($bd, $city);
									return array("CITY" =>$exCstr, "IP" => $exStr);
								}
							}
						}
					}
				}

			}
		
		}

		unset($bd);
		unset($city);

		return false;

	}

?>