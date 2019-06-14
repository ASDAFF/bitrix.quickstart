<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Andrew N. Popov                  #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2010 ALTASIB             #
#################################################
?>
<?
Class ALX_GeoIP
{
                function GetAddr(){

                	if (!function_exists('curl_init'))
			{
			    ShowError("Error! cURL not installed!");
			    return;
			}
                        global $APPLICATION;

                        if(!is_array($_SESSION["GEOIP"])){

                                $ip = $_SERVER["REMOTE_ADDR"];
                                if (!empty($_SERVER["HTTP_X_REAL_IP"])) {
                                    $ip = $_SERVER["HTTP_X_REAL_IP"];
                                }

                                if(COption::GetOptionString("altasib_geoip", "set_cookie", "Y") == "Y"){
                                        $last_ip = $APPLICATION->get_cookie("LAST_IP");
                                        $strData = $APPLICATION->get_cookie("GEOIP");
                                }

                                if(($ip == $last_ip) && $strData){

                                        $arData = unserialize($strData);

                                }else{

                                        $arData = ALX_GeoIP::GetGeoData($ip);
                                        if (!$arData) return false;


                                        $strData = serialize($arData);

                                        if(COption::GetOptionString("altasib_geoip", "set_cookie", "N") == "Y"){
                                                $APPLICATION->set_cookie("GEOIP", $strData, time()+30000000);
					        $APPLICATION->set_cookie("LAST_IP", $ip, time()+30000000);
                                        }
                                }

                                $_SESSION["GEOIP"] = $arData;
                        }

                        return $_SESSION["GEOIP"];
                }


        function ParseXML($text){

                if (strlen($text) > 0)
                {
                        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");
                        $objXML = new CDataXML();
                        $res = $objXML->LoadString($text);
                        if($res !== false)
                        {
                                $arRes = $objXML->GetArray();
                        }
                }

                $arRes = current($arRes);
                $arRes = $arRes["#"];
                $arRes = current($arRes);

                $ar = Array();

                foreach($arRes as $key => $arVal){

                        foreach($arVal["#"] as $title => $Tval){

                                $ar[$key][$title] = $Tval["0"]["#"];

                        }

                }

                return ($ar[0]);

        }
        function GetGeoData($ip){

                if(!$arData = ALX_GeoIP::GetGeoDataIpgeobase_ru($ip))
                    if(!$arData = ALX_GeoIP::GetGeoDataGeoip_Elib_ru($ip))
                         return false;
                return $arData;

        }
        function GetGeoDataIpgeobase_ru($ip){

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, "http://ipgeobase.ru:7020/geo/?ip=".$ip);
                curl_setopt($ch, CURLOPT_HEADER, TRUE);
                curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_TIMEOUT, 1);

                $text = curl_exec($ch);

                $errno = curl_errno($ch);
                $errstr = curl_error($ch);
                curl_close($ch);

                if ($errno)
                   return false;

                $text = iconv("windows-1251", SITE_CHARSET, $text);

                $arData = ALX_GeoIP::ParseXML($text);
                return ($arData);

        }
        function GetGeoDataGeoip_Elib_ru($ip){
                if(!$text = file_get_contents('http://geoip.elib.ru/cgi-bin/getdata.pl?ip='.$ip.'&hex=3ffd'))
                  return false;
                $text = iconv("UTF-8", SITE_CHARSET, $text);

                $arData_ = ALX_GeoIP::ParseXML($text);
                if(isset($arData_["Error"]))
                  return false;

                $arData = Array(
                    "inetnum" => $ip,
                    "country" => $arData_["Country"],
                    "city" => $arData_["Town"],
                    "region" => $arData_["Region"],
                    "district" => "",
                    "lat" => $arData_["Lat"],
                    "lng" => $arData_["Lon"]
                );

                return ($arData);
        }



}

?>
