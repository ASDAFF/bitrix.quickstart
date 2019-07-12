<?
namespace Citrus\Realty;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

// TODO добавить возможность задавать порядок колонок, отличный от сортировки свойств
// TODO добавить возможность выбора полей для сортировки в разделах

class IblockPropertyList extends \CUserTypeEnum
{
	const	DETAIL_PICTURE = -1,
			NAME = -2,
			DATE_CREATE = -3;

	public function GetUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID" => "citrus.IblockPropertyList",
			"CLASS_NAME" => __CLASS__,
			"DESCRIPTION" => Loc::getMessage("CITRUS_REALTY_IBLOCK_PROPERTY_LIST_DESCRIPTION"),
			"BASE_TYPE" => "enum",
		);
	}

	public function PrepareSettings($arUserField)
	{
		$height = intval($arUserField["SETTINGS"]["LIST_HEIGHT"]);
		$iblockId = intval($arUserField["SETTINGS"]["IBLOCK_ID"]);
		if($iblockId <= 0)
			$iblockId = "";
		return array(
			"DISPLAY" => $arUserField["SETTINGS"]["DISPLAY"] == "CHECKBOX" ? "CHECKBOX" : "LIST",
			"LIST_HEIGHT" => ($height < 1? 5: $height),
			"IBLOCK_ID" => $iblockId,
		);
	}

	public function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
	{
		$result = '';

		if($bVarsFromForm)
			$value = $GLOBALS[$arHtmlControl["NAME"]]["IBLOCK_ID"];
		elseif(is_array($arUserField))
			$value = $arUserField["SETTINGS"]["IBLOCK_ID"];
		else
			$value = "";

		$result .= '
		<tr valign="top">
			<td>' . Loc::getMessage("CITRUS_REALTY_IBLOCK_PROPERTY_LIST_IBLOCK") . ':</td>
			<td>
				'.GetIBlockDropDownList($value, $arHtmlControl["NAME"].'[IBLOCK_TYPE_ID]', $arHtmlControl["NAME"].'[IBLOCK_ID]').'
			</td>
		</tr>
		';

		if($bVarsFromForm)
			$value = $GLOBALS[$arHtmlControl["NAME"]]["DISPLAY"];
		elseif(is_array($arUserField))
			$value = $arUserField["SETTINGS"]["DISPLAY"];
		else
			$value = "LIST";
		$result .= '
		<tr valign="top">
			<td>' . Loc::getMessage("CITRUS_REALTY_IBLOCK_PROPERTY_LIST_TYPE") . ':</td>
			<td>
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="LIST" '.("LIST"==$value? 'checked="checked"': '').'>' . Loc::getMessage("CITRUS_REALTY_IBLOCK_PROPERTY_LIST_LIST") . '</label><br>
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="CHECKBOX" '.("CHECKBOX"==$value? 'checked="checked"': '').'>' . Loc::getMessage("CITRUS_REALTY_IBLOCK_PROPERTY_LIST_CHECKBOX") . '</label><br>
			</td>
		</tr>
		';
		if($bVarsFromForm)
			$value = intval($GLOBALS[$arHtmlControl["NAME"]]["LIST_HEIGHT"]);
		elseif(is_array($arUserField))
			$value = intval($arUserField["SETTINGS"]["LIST_HEIGHT"]);
		else
			$value = 5;
		$result .= '
		<tr valign="top">
			<td>' . Loc::getMessage("CITRUS_REALTY_IBLOCK_PROPERTY_LIST_HEIGHT") . ':</td>
			<td>
				<input type="text" name="'.$arHtmlControl["NAME"].'[LIST_HEIGHT]" size="10" value="'.$value.'">
			</td>
		</tr>
		';
		return $result;
	}

	public function GetList($arUserField)
	{
		$rsElement = false;
		if(\CModule::IncludeModule('iblock'))
		{
			$obElement = new IblockPropertyListEnum();
			$rsElement = $obElement->GetEnumList($arUserField["SETTINGS"]["IBLOCK_ID"]);
		}
		return $rsElement;
	}


	/**
	 * @param int $iblockId ID инфоблока
	 * @return array Массив с полями свойств
	 */
	public static function getPropertiesWithCustomFields($iblockId)
	{
		$result = array();
		static $customFields = array("DETAIL_PICTURE", "NAME", "DATE_CREATE");
		foreach ($customFields as $customField)
		{
			$result[$customField] = array(
					"ID" => constant('self::' . $customField),
					"NAME" => Loc::getMessage("CITRUS_REALTY_IBLOCK_PROPERTY_LIST_F_" . $customField),
			);
		}

		$properties = \CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID" => $iblockId));
		while ($property = $properties->GetNext())
			$result[$property["ID"]] = array(
				"ID" => $property["ID"],
				"NAME" => $property["NAME"],
				"CODE" => $property["CODE"],
			);

		return $result;
	}

}


class IblockPropertyListEnum extends \CDBResult
{
	public static function GetEnumList($IBLOCK_ID)
	{
		if(\CModule::IncludeModule('iblock'))
		{
			$newDbResult = new \CDBResult();
			$newDbResult->InitFromArray(IblockPropertyList::getPropertiesWithCustomFields($IBLOCK_ID));
			return new IblockPropertyListEnum($newDbResult);
		}
		return false;
	}

	public function GetNext($bTextHtmlAuto=true, $use_tilda=true)
	{
		$r = parent::GetNext($bTextHtmlAuto, $use_tilda);
		if ($r)
			$r["VALUE"] = ($r["CODE"] ? '[' . $r["CODE"] . '] ' : '') . $r["NAME"];
		return $r;
	}
}

