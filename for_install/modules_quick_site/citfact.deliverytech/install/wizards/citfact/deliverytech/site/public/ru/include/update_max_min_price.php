<?
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("UpdateEl", "MaxMinFunc"));
AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("UpdateEl", "MaxMinFunc"));

class UpdateEl {
	function MaxMinFunc(&$arFields) {
		if (intVal($arFields["ID"]) > 0) {
			$iblock_id = "#CATALOG_IBLOCK_ID#";
			$catalog_group_id = 1;
			$min_code = "MINIMUM_PRICE";
			$max_code = "MAXIMUM_PRICE";
			CModule::IncludeModule("catalog");
			$sku = CCatalogSKU::GetInfoByProductIBlock($iblock_id);
			$max_price = 0;
			$min_price = 1000000;

			if ($arFields["IBLOCK_ID"] == $iblock_id || $arFields["IBLOCK_ID"] == $sku["IBLOCK_ID"]) {
				if ($arFields["IBLOCK_ID"] == $iblock_id) {
					$element_id = $arFields["ID"];
				} else if ($arFields["IBLOCK_ID"] == $sku["IBLOCK_ID"]) {
					$ar_get = CCatalogSku::GetProductInfo($arFields["ID"], $sku["IBLOCK_ID"]);
					$element_id = $ar_get["ID"];
				}
				$db_get = CPrice::GetList(Array(), Array("PRODUCT_ID" => $element_id, "CATALOG_GROUP_ID" => $catalog_group_id), false, false, Array());
				if ($ar_get = $db_get->Fetch()) {
					$min_price = $max_price = floatVal($ar_get["PRICE"]);
				}
				if (intVal($sku["IBLOCK_ID"]) > 0) {
					$db_get = CIBlockElement::GetList(Array(), Array("PROPERTY_".$sku["SKU_PROPERTY_ID"] => $element_id, "IBLOCK_ID" => $sku["IBLOCK_ID"], "ACTIVE" => "Y", "ACTIVE_DATE" => "Y"), false, false, Array("ID"));
					while ($ar_get = $db_get->GetNext()) {
						$db_get2 = CPrice::GetList(Array(), Array("PRODUCT_ID" => $ar_get["ID"], "CATALOG_GROUP_ID" => $catalog_group_id), false, false, Array());
						if ($ar_get2 = $db_get2->Fetch()) {
							if (floatVal($ar_get2["PRICE"]) > $max_price) {
								$max_price = floatVal($ar_get2["PRICE"]);
							}
							if (floatVal($ar_get2["PRICE"]) < $min_price) {
								$min_price = floatVal($ar_get2["PRICE"]);
							}
						}
					}
				}
				CIBlockElement::SetPropertyValuesEx($element_id, $iblock_id, array($min_code => $min_price));
				CIBlockElement::SetPropertyValuesEx($element_id, $iblock_id, array($max_code => $max_price));
			}
		}
	}
}
?>