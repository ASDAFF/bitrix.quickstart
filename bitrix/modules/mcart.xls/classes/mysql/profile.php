<?
class CMcartXlsProfile
{
	const TABLE = 'mcart_xls';
	const TABLE_FIELDS = 'mcart_xls_fields';
	
	static public function Add($arrData) {
		global $DB;
		
		
		
		
		foreach ($arrData as $key=>$data)
		{
		if (empty($data))
		$arrData[$key] = 0;
		}

		$sql = "INSERT INTO ".self::TABLE." (name, iblock_id, section_id, data_row, title_row, diapazone_a, diapazone_z, sheet_id, 
		name_field, identify, sku_iblock_id, cml2_link_code) 
				VALUES ('".$arrData["NAME"]."', "
				.$arrData["IBLOCK_ID"].", "
				.$arrData["SECTION_ID"].", "
				.$arrData["DATA_ROW"].", "
				.$arrData["TITLE_ROW"].", '"
				.$arrData["DIAPAZONE_A"]."', '"
				.$arrData["DIAPAZONE_Z"]."', "
				.$arrData["SHEET_ID"].", "
				.$arrData["NAME_FIELD"].", "
				.$arrData["IDENTIFY"].", "
				.$arrData["SKU_IBLOCK_ID"].", "
				.$arrData["CML2_LINK_CODE"].
				")";
		
		$DB->query($sql);
		$profile_id = $DB->lastID(); 

		foreach ($arrData["FIELDS"] as $key=>$field)
		{
			foreach ($field as $key2=>$val)
			{
				$sql = "INSERT INTO ".self::TABLE_FIELDS." (profile_id, key2, col_id, field_code) VALUES (".$profile_id.", ".$key2.", ".$key.", '".$val."')";
				$DB->query($sql);
			}
		}
	}


}

?>