<?
IncludeModuleLangFile(__FILE__);

class CAllCatalogSKU
{
	public function GetProductInfo($intOfferID, $intIBlockID = 0)
	{
		$intOfferID = intval($intOfferID);
		if (0 >= $intOfferID)
			return false;

		$intIBlockID = intval($intIBlockID);
		if (0 >= $intIBlockID)
		{
			$rsItems = CIBlockElement::GetList(array(), array("ID" => $intOfferID, "SHOW_HISTORY" => "Y"), false, false, array('ID','IBLOCK_ID'));
			if ($arItem = $rsItems->Fetch())
			{
				$intIBlockID = intval($arItem['IBLOCK_ID']);
			}
		}
		if (0 >= $intIBlockID)
			return false;

		$arSkuInfo = CCatalogSKU::GetInfoByOfferIBlock($intIBlockID);
		if (empty($arSkuInfo) || empty($arSkuInfo['SKU_PROPERTY_ID']))
			return false;

		$rsItems = CIBlockElement::GetProperty($intIBlockID,$intOfferID,array(),array('ID' => $arSkuInfo['SKU_PROPERTY_ID']));
		if ($arItem = $rsItems->Fetch())
		{
			$arItem['VALUE'] = intval($arItem['VALUE']);
			if (0 < $arItem['VALUE'])
			{
				return array(
					'ID' => $arItem['VALUE'],
					'IBLOCK_ID' => $arSkuInfo['PRODUCT_IBLOCK_ID'],
				);
			}
		}
		return false;
	}

	public function GetInfoByOfferIBlock($intIBlockID)
	{
		$intIBlockID = intval($intIBlockID);
		if (0 >= $intIBlockID)
			return false;
		$rsOffers = CCatalog::GetList(array(),array('IBLOCK_ID' => $intIBlockID),false,false,array('IBLOCK_ID','PRODUCT_IBLOCK_ID','SKU_PROPERTY_ID'));
		return $rsOffers->Fetch();
	}

	public function GetInfoByProductIBlock($intIBlockID)
	{
		$intIBlockID = intval($intIBlockID);
		if (0 >= $intIBlockID)
			return false;
		$rsProducts = CCatalog::GetList(array(),array('PRODUCT_IBLOCK_ID' => $intIBlockID),false,false,array('IBLOCK_ID','PRODUCT_IBLOCK_ID','SKU_PROPERTY_ID'));
		return $rsProducts->Fetch();
	}

	public function GetInfoByLinkProperty($intPropertyID)
	{
		$ID = intval($intPropertyID);
		if (0 >= $intPropertyID)
			return false;
		$rsProducts = CCatalog::GetList(array(),array('SKU_PROPERTY_ID' => $intPropertyID),false,false,array('IBLOCK_ID','PRODUCT_IBLOCK_ID','SKU_PROPERTY_ID'));
		return $rsProducts->Fetch();
	}
}
?>