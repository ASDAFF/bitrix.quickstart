<?
if(IsModuleInstalled('{MODULE_ID}'))
{
	if (is_dir(dirname(__FILE__).'/install/components'))
		$updater->CopyFiles("install/components", "components/{NAMESPACE}");

	if (is_dir(dirname(__FILE__).'/install/js'))
		$updater->CopyFiles("install/js", "js/{MODULE_ID}/");
}


/* 

// 
// Sample database update
//


if($updater->CanUpdateDatabase())
{
	if($updater->TableExists("b_iblock_element_property"))
	{
		if(!$DB->IndexExists("b_iblock_element_property", array("VALUE_NUM", "IBLOCK_PROPERTY_ID")))
		{
			$updater->Query(array(
				"MySQL" => "CREATE INDEX ix_iblock_element_prop_num ON b_iblock_element_property(VALUE_NUM, IBLOCK_PROPERTY_ID)",
				"MSSQL" => "CREATE INDEX IX_B_IBLOCK_ELEMENT_PROPERTY_4 ON B_IBLOCK_ELEMENT_PROPERTY(VALUE_NUM, IBLOCK_PROPERTY_ID)",
				"Oracle" => "CREATE INDEX IX_IBLOCK_ELEMENT_PROP_NUM ON B_IBLOCK_ELEMENT_PROPERTY(VALUE_NUM, IBLOCK_PROPERTY_ID)",
			));
		}
        }
	if($updater->TableExists("b_iblock_property"))
	{
		if(!$DB->IndexExists("b_iblock_property", array("UPPER(\"CODE\")")))
		{
			$updater->Query(array(
				"Oracle" => "CREATE INDEX ix_iblock_property_2 ON B_IBLOCK_PROPERTY(UPPER(CODE))",
			));
		}
        }
}

*/
?>
