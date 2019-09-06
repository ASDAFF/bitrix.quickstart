<?
namespace WS\SaleUserProfilesPlus;

class Tools{
    static function SelectBoxPersonTypes($personID = 0, $name, $htmlattrs = ""){
        if (empty($name)) {
            return false;
        }

        $html = '<select name="'.$name.'" '.$htmlattrs.'>';
        $res = \CSalePersonType::GetList(Array(), Array());
        while ($arRes = $res->Fetch()) {
            $html .= '<option '.(($arRes['ID']==$personID)?'selected':'').' value="'.$arRes['ID'].'">'.$arRes["NAME"].'</option>';
        }
        $html .= '</select>';
        return $html;
    }

    static function SelectBoxLocations($lid, $name, $locationID, $onChange="") {
        $html = '<select name="'.$name.'" onChange="'.$onChange.'">';
        $res = \CSaleLocation::GetList(array(), array("LID" => $lid), false, false, array());
        while ($arRes = $res->Fetch()) {
            $html .= '<option '.(($arRes['ID']==$locationID)?'selected':'').' value="'.$arRes['ID'].'">'.htmlspecialcharsbx($arRes["COUNTRY_NAME"] . ((!empty($arRes["REGION_NAME"]))?' - '.$arRes["REGION_NAME"]:'') . ((!empty($arRes["CITY_NAME"]))?' - '.$arRes["CITY_NAME"]:'')) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
}
?>