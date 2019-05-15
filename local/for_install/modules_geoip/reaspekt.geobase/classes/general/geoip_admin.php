<?
/**
 * Company developer: REASPEKT
 * Developer: adel yusupov
 * Site: http://www.reaspekt.ru
 * E-mail: adel@reaspekt.ru
 * @copyright (c) 2016 REASPEKT
 */
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie; 

Loc::loadMessages(__FILE__);

class ReaspAdminGeoIP {
    const MID = "reaspekt.geobase";
    
    function GetCitySelected() {
        
		if($_SERVER["REQUEST_METHOD"] != "POST")
			return false;

		if(!check_bitrix_sessid('sessid') && !IsIE())
			return false;

		if(
            !isset($_POST['city_name']) 
            && !isset($_POST['add_city'])
			&& !isset($_POST['delete_city']) 
            && !isset($_POST['update'])
        ) {
            
			return false;
            
		} elseif (
            empty($_POST['city_name']) 
            && empty($_POST['add_city'])
			&& empty($_POST['delete_city']) 
            && empty($_POST['update'])
        ) {
            
			die('pusto');
            
        } elseif(isset($_POST['city_name'])) { // search cities
        
			return ReaspAdminGeoIP::CitySearch(true);
            
		} elseif(isset($_POST['add_city']) && $_POST['add_city'] == 'Y') { // add city
			
            if(isset($_POST['city_id'])) {
				global $DB;
				$city_id = $DB->ForSql(htmlspecialchars($_POST['city_id']));

				$sites = CSite::GetList($by="sort", $order="desc", Array("ACTIVE" => "Y"));
				while($Site = $sites->Fetch()) {
					BXClearCache(true, $Site["LID"]."/reaspekt/geobase/");
				}
                
				return(ReaspAdminGeoIP::AddSetCity($city_id));
			}
		} elseif(isset($_POST['update']) && $_POST['update'] == 'Y') { // restart html table
			
            return(ReaspAdminGeoIP::UpdateCityRows());
            
		} elseif(isset($_POST['delete_city']) && $_POST['delete_city'] == 'Y') {
			
            if(isset($_POST['entry_id'])){
				global $DB;
				$city_id = $DB->ForSql(htmlspecialchars($_POST['entry_id']));

				$sites = CSite::GetList($by="sort", $order="desc", Array("ACTIVE" => "Y"));
				while($Site= $sites->Fetch()){
					BXClearCache(true, $Site["LID"]."/reaspekt/geobase/");
				}

				return(ReaspAdminGeoIP::DeleteCity($city_id));
			}
		}
	}
    
    function CitySearch($adminSection = false) {
		$city = trim(urldecode($_POST['city_name']));
        
		if (SITE_CHARSET == 'windows-1251') {
			$city1 = @iconv("UTF-8", "windows-1251//IGNORE", $city); // All AJAX requests come in Unicode
			if($city1)
				$city = $city1;	// if used Windows-machine
		}
        
		$city = addslashes($city);
		$citylen = strlen($city);

		$arCity = array();
		$i = 0;

		if (isset($_POST['lang']) && strtolower($_POST['lang']) == "ru") { // LANGUAGE_ID
		
            if ($citylen > 1) {
                $arCity = ReaspGeoIP::SelectQueryCity($city);
                
                if (SITE_CHARSET == 'windows-1251') {
                    $arCity = ReaspGeoIP::iconvArrUtfToUtf8($arCity);
                }
            }
		}
        
		echo json_encode($arCity);
	}
    
    function AddSetCity($city_id) {
		global $DB;
        
        $arCity = ReaspGeoIP::SelectCityId($city_id);
        
        if ($arCity["ID"]) {
            //Смотрим настройки модуля
            $reaspekt_city_manual_default = Option::get(self::MID, "reaspekt_city_manual_default");
            $ar_reaspekt_city_manual_default = unserialize($reaspekt_city_manual_default);
            $ar_reaspekt_city_manual_default[] = $arCity["ID"];
            //Убираем дубли
            $ar_reaspekt_city_manual_default = array_unique($ar_reaspekt_city_manual_default);
            $reaspekt_city_manual_default = serialize($ar_reaspekt_city_manual_default);
            Option::set(self::MID, "reaspekt_city_manual_default", $reaspekt_city_manual_default);
        } else {
            return false;
        }
        
		return $arCity["ID"];
	}
    
    function UpdateCityRows() {
        $reaspekt_city_manual_default = Option::get(self::MID, "reaspekt_city_manual_default");
        $ar_reaspekt_city_manual_default = unserialize($reaspekt_city_manual_default);
        $arCityData = ReaspGeoIP::SelectCityIdArray($ar_reaspekt_city_manual_default, true);
        
        $strCityDefaultTR = "";
        
        foreach ($ar_reaspekt_city_manual_default as $idCity) {
            $strCityDefaultTR .= '<tr class="reaspekt_geobase_city_line">';
            $strCityDefaultTR .= "<td>" . $arCityData[$idCity]["ID"] . "</td>";
            $strCityDefaultTR .= "<td>" . $arCityData[$idCity]["UF_XML_ID"] . "</td>";
            $strCityDefaultTR .= "<td>" . (($arCityData[$idCity]["UF_ACTIVE"]) ? Loc::getMessage("REASPEKT_GEOBASE_ACTIVE_CITY_TRUE") : Loc::getMessage("REASPEKT_GEOBASE_ACTIVE_CITY_FALSE")) . "</td>";
            $strCityDefaultTR .= "<td>" . $arCityData[$idCity]["CITY"] . "</td>";
            $strCityDefaultTR .= "<td>" . $arCityData[$idCity]["REGION"] . "</td>";
            $strCityDefaultTR .= "<td>" . $arCityData[$idCity]["OKRUG"] . "</td>";
            $strCityDefaultTR .= '<td><input type="submit" name="reaspekt_geobase_del_'.$idCity.'" value="'.GetMessage("REASPEKT_TABLE_CITY_DELETE").'" onclick="reaspekt_geobase_delete_click('.$idCity.');return false;"></td>';
            $strCityDefaultTR .= "</tr>";
        }
        
        echo $strCityDefaultTR;
    }
    
    function DeleteCity($ID) {
		global $DB;
		$ID = IntVal($ID);

		if($ID <= 0)
			return false;

		$reaspekt_city_manual_default = Option::get(self::MID, "reaspekt_city_manual_default");
        $ar_reaspekt_city_manual_default = unserialize($reaspekt_city_manual_default);
        
        foreach ($ar_reaspekt_city_manual_default as $keyCity => &$idCity) {
            if (intval($idCity) == $ID) {
                unset($ar_reaspekt_city_manual_default[$keyCity]);
            }
        }
        
        $reaspekt_city_manual_default = serialize($ar_reaspekt_city_manual_default);
        
        Option::set(self::MID, "reaspekt_city_manual_default", $reaspekt_city_manual_default);
        
        return true;
	}
}