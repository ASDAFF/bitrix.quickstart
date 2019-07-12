<?
// TODO ��������� � �������� ������ ���� ������� (�������� � ��������� � �.�.)

namespace Citrus\Realty;

/** @var array $arParams ���������, ������, ���������. �� ����������� ����������� ���� ����������, �� ��������� ��� ������ ��  � ����� template.php. */
/** @var array $arResult ���������, ������/���������. ����������� ����������� ���� ������ ����������. */
/** @var \CBitrixComponentTemplate $this ������� ������ (������, ����������� ������) */

$obEnum = new \CUserFieldEnum();
if ($arResult["UF_TYPE"] && ($enum = $obEnum->GetList(array(), array("ID" => $arResult["UF_TYPE"]))->Fetch()))
	$arResult["UF_TYPE_XML_ID"] = $enum["XML_ID"];
else
	$arResult["UF_TYPE_XML_ID"] = false;

// �������� � ���� ����������
$arResult["SORT_FIELDS"] = SortOrder::setFields($arResult["IBLOCK_ID"], $arResult["UF_SORT_FIELDS"]);

// ������� ������� ��� �����������
// TODO �����������!
if (!$arResult["UF_TYPE_XML_ID"] || $arResult["UF_TYPE_XML_ID"] == 'list' || $arResult["UF_TYPE_XML_ID"] == 'cards')
{
	$iblockFields = null;
	if (empty($arResult["UF_PROP_LINK"]))
	{
		// ���� �� ���������
		$iblockFields = IblockPropertyList::getPropertiesWithCustomFields($arResult["IBLOCK_ID"]);
		$arResult["DISPLAY_COLUMNS"] = array();
        $arResult["DISPLAY_COLUMNS_DEFAULT"] = true;
		$defaultFields = array("~DETAIL_PICTURE", "cost", "address", "rooms", "common_area", "floor");
		foreach ($defaultFields as $field)
		{
			if (substr($field,0,1) == '~')
				$arResult["DISPLAY_COLUMNS"][$field] = $iblockFields[substr($field,1)]["NAME"];
			else
				$arResult["DISPLAY_COLUMNS"][$field] = $iblockFields[Helper::getPropertyIdByCode($arResult["IBLOCK_ID"], $field)]["NAME"];
		}
	}
	else
	{
		// ����, ��������� ��� �������
		$arResult["DISPLAY_COLUMNS"] = array();
        $arResult["DISPLAY_COLUMNS_DEFAULT"] = false;
		foreach ($arResult["UF_PROP_LINK"] as $propertyId)
		{
			if ($propertyId < 0 && !isset($iblockFields))
				$iblockFields = IblockPropertyList::getPropertiesWithCustomFields($arResult["IBLOCK_ID"]);
			switch ($propertyId)
			{
				case IblockPropertyList::NAME:
					$arResult["DISPLAY_COLUMNS"]["~NAME"] = $iblockFields["NAME"]["NAME"];
					break;
				case IblockPropertyList::DETAIL_PICTURE:
					$arResult["DISPLAY_COLUMNS"]["~DETAIL_PICTURE"] = $iblockFields["DETAIL_PICTURE"]["NAME"];
					break;
				case IblockPropertyList::DATE_CREATE:
					$arResult["DISPLAY_COLUMNS"]["~DATE_CREATE"] = $iblockFields["DATE_CREATE"]["NAME"];
					break;
				default:
					$propertyFields = \CIBlockProperty::GetByID($propertyId)->GetNext();
					$arResult["DISPLAY_COLUMNS"][$propertyFields["CODE"]] = $propertyFields["NAME"];
			}
		}
	}

	if (array_key_exists("address", $arResult["DISPLAY_COLUMNS"]) && array_key_exists("district", $arResult["DISPLAY_COLUMNS"]))
		unset($arResult["DISPLAY_COLUMNS"]["district"]);
}