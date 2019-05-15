<?
/**
 * Company developer: ALTASIB
 * Developer: adumnov
 * Site: http://www.altasib.ru
 * E-mail: dev@altasib.ru
 * @package bitrix
 * @subpackage altasib.geobase
 * @copyright (c) 2006-2015 ALTASIB
 */
IncludeModuleLangFile(__FILE__);
Class CAltasibGeoBaseSelected extends CAltasibGeoBaseAllSelected
{
	function UpdateCityRows()
	{
		$resCities = CAltasibGeoBaseAllSelected::GetMoreCities(true);
		while($arCities = $resCities->Fetch()):
			if(empty($arCities["R_FNAME"]))
			{
				$arRG = CAltasibGeoBase::GetRegionLang($arCities["CTR_CODE"], $arCities['R_ID']);
				if(!empty($arRG['region_name'])){
					if (LANG_CHARSET == 'windows-1251')
						$arRG['region_name'] = iconv("UTF-8", "windows-1251", $arRG['region_name']);

					$arCities["R_FNAME"] = $arRG['region_name'];
				}
			}

			echo '<tr class="altasib_geobase_city_line">
				<td>'.htmlspecialcharsEx($arCities["C_SOCR"]).'</td>
				<td width="16%">'.htmlspecialcharsEx($arCities["C_NAME"]).'
				<td width="16%">'.(!empty($arCities["C_NAME_EN"]) ? htmlspecialcharsEx($arCities["C_NAME_EN"]) : '').'</td>
				<td>'.$arCities["C_CODE"].'</td>
				<td>'.(htmlspecialcharsEx(!empty($arCities['D_NAME']) ? $arCities['D_NAME'].' '.$arCities['D_SOCR'] : $arCities['ID_DISTRICT'])).'</td>
				<td>'.htmlspecialcharsEx($arCities["R_FNAME"]).'</td>
				<td>'.htmlspecialcharsEx($arCities["CTR_CODE"]).'</td>
				<td>'.htmlspecialcharsEx($arCities["CTR_NAME_RU"]).'</td>
				<td><input type="submit" name="altasib_geobase_del_'.$arCities['ID'].'" value="'.GetMessage("ALTASIB_TABLE_CITY_DELETE").'" onclick="altasib_geobase_delete_click('.$arCities['ID'].'); return false;"></td>
			</tr>';
		endwhile;
	}

	function BeforeAddCity($cityId){
		$arData = CAltasibGeoBase::GetInfoKladrByCode($cityId);
		if (!$arData)
			return false;
		$arField = array(
			'ACTIVE' => 'Y',
			'SORT' => 500,
			'NAME' => $arData['CITY']['NAME'],
			'NAME_EN' => "", // new
			'CODE' => $arData['CODE'],
			'ID_DISTRICT' => $arData['CITY']['ID_DISTRICT'],
			'ID_REGION' => $arData['REGION']['CODE'],
			'COUNTRY_CODE' => "RU",
			'SOCR' => $arData['CITY']['SOCR']
		);

		return(CAltasibGeoBaseAllSelected::AddCity($arField));
	}

	function BeforeAddMMCity($cityId){
		// $arData = CAltasibGeoBase::GetInfoMMByCode($cityId);
		$arData = CAltasibGeoBase::GetDataMMByID($cityId)->Fetch();
		if (!$arData)
			return false;
		$arField = array(
			'ACTIVE' => 'Y',
			'SORT' => 500,
			'NAME' => $arData['CITY_RU'],
			'NAME_EN' => $arData['CITY_EN'], // new
			'CODE' => $arData['CITY_ID'],
			'ID_DISTRICT' => "",
			'ID_REGION' => $arData['REGION_ID'],
			'COUNTRY_CODE' => $arData['COUNTRY_CODE'],
			'SOCR' => ""
		);

		return(CAltasibGeoBaseAllSelected::AddCity($arField));
	}

	function CheckFields(&$arFields)
	{
		if (is_set($arFields, "NAME") && strlen($arFields["NAME"]) <= 0) return false;
		if (is_set($arFields, "CODE") && strlen($arFields["CODE"]) <= 0) return false;
		if (is_set($arFields, "ID_REGION") && strlen($arFields["ID_REGION"]) <= 0) return false;

		return true;
	}

	function GetCityByID($ID, $afields, $active = false)
	{
		global $DB;
		$ID = IntVal($ID);
		if ($ID<=0) return false;

		$strSql =
		"SELECT ".implode(',', $afields)." FROM altasib_geobase_selected ".
		"WHERE ID = ".$ID
		.($active != false ? 'AND ACTIVE = "Y"' : '')
		." ORDER BY ID";

		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br />Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return false;
	}

	function GetAllCities($afields, $active = false)
	{
		global $DB;
		$strSql =
		"SELECT ".implode(',', $afields)." FROM altasib_geobase_selected "
		.($active != false ? 'WHERE ACTIVE = "Y"' : '')
		." ORDER BY `ID`, `SORT`";

		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br />Line: ".__LINE__);

		return $db_res;
	}
}
?>