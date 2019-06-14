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
Class CAltasibGeoBaseImport
{
	function InitImportKladr()
	{
		global $DB, $DBType;

		if (!$DB->TableExists('altasib_geobase_kladr_cities'))
		{
			if(!$DB->Query("SELECT '1' FROM altasib_geobase_kladr_region", true)){
				$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/altasib.geobase/install/db/".$DBType."/install.sql");
			}

			CAltasibGeoBaseImport::LoadBaseFromFiles();
		}
		else
			return;
	}
	function LoadBaseFromFiles()
	{
		$TableFiles = array('Kladr_districts.csv', 'Kladr_region.csv', 'Kladr_selected.csv', 'Kladr_cities.csv');
		$Tables = array('altasib_geobase_kladr_districts', 'altasib_geobase_kladr_region', 'altasib_geobase_selected', 'altasib_geobase_kladr_cities');
		$UPath = '/upload/altasib/geobase/';

		$aFields = array(
			array( // districts
				'ID', 'ACTIVE', 'SORT', 'NAME', 'CODE',
				'ID_REGION', 'SOCR'
			),
			array( // regions
				'ID', 'ACTIVE', 'SORT', 'NAME', 'FULL_NAME', 'CODE',
				'SOCR', 'POSTINDEX'
			),
			array( // selected
				'ID', 'ACTIVE', 'SORT', 'NAME', 'CODE', 'ID_DISTRICT',
				'ID_REGION', 'SOCR'
			), 
			array( // cities
				'ID', 'ACTIVE', 'SORT', 'NAME', 'CODE', 'ID_DISTRICT',
				'SOCR', 'STATUS', 'POSTINDEX', 'SORTINDEX'
			)
		);
		for($i=0; $i<count($Tables); $i++){
			if(SITE_CHARSET == "UTF-8"){
				$strFName = $_SERVER['DOCUMENT_ROOT'].$UPath.$TableFiles[$i];
				file_put_contents($strFName, iconv("windows-1251", "UTF-8", file_get_contents($strFName)));
			}
			CAltasibGeoBaseImport::InsertDataTable($UPath.$TableFiles[$i], $Tables[$i], $aFields[$i]);
		}

		$arKR = array("Kladr_cities.csv", "Kladr_districts.csv", "Kladr_region.csv", "Kladr_selected.csv");
		foreach($arKR as $elem){
			if (file_exists($UPath.$elem))
				@unlink($path.$elem);
		}
		return true;
	}


	function InitImportMM()
	{
		global $DB, $DBType;

		if (!$DB->TableExists('altasib_geobase_mm_city') || !$DB->TableExists('altasib_geobase_mm_country')
				|| !$DB->TableExists('altasib_geobase_mm_region'))
		{
			$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/altasib.geobase/install/db/".$DBType."/install_mm.sql");

			CAltasibGeoBaseImport::LoadMMFromFiles();
		}
		else
			return;
	}
	function LoadMMFromFiles()
	{
		global $DB;
		$TableFiles = array('geo_city.csv', 'region-code-localized.csv', 'geo_country.csv');
		$Tables = array('altasib_geobase_mm_city', 'altasib_geobase_mm_region', 'altasib_geobase_mm_country');
		$UPath = '/upload/altasib/geobase/mm/';

		$aFields = array(
			array( // city
				'id', 'country_id', 'name_ru', 'name_en', 'region', 'postal_code', 'latitude', 'longitude'
			),
			array( // region
				'country_code', 'region_code', 'lang', 'region_name', 'GeoNames_ID'
			),
			array( // country
				'id', 'name_ru', 'name_en', 'code'
			)
		);
		for($i=0; $i<count($Tables); $i++){
			if(SITE_CHARSET == "UTF-8" && $Tables[$i] != "altasib_geobase_mm_region"){
				$strFName = $_SERVER['DOCUMENT_ROOT'].$UPath.$TableFiles[$i];
				file_put_contents($strFName, iconv("windows-1251", "UTF-8", file_get_contents($strFName)));
			}
			CAltasibGeoBaseImport::InsertDataTable($UPath.$TableFiles[$i], $Tables[$i], $aFields[$i]);
		}

		DeleteDirFilesEx($UPath);
		return true;
	}

	function InsertDataTable($fileName, $table, $afields)
	{
		global $DB;
		$table = $DB->ForSQL($table);
		$strFName = $_SERVER['DOCUMENT_ROOT'].$fileName;
		$cols = "(".implode(',', $afields).")";
		$handle = fopen($strFName, "r");

		while ($data = fgetcsv($handle, 255, ';'))
		{
			$insVal = array();
			foreach($data as $v)
			{
				$insVal[] = "'".$DB->ForSQL($v)."'";
			}
			$vals = implode(',',$insVal);
			$DB->Query('INSERT INTO `'.$table.'` '.$cols.' VALUES ( '.$vals.' )', true);
		}
		fclose($handle);
		return true;
	}
}
?>