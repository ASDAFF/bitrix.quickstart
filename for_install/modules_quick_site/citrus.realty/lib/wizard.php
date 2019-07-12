<?
namespace Citrus\Realty;

use \Bitrix\Main\Localization\Loc;

class Wizard
{
	static $initialized = false;

	public static function init()
	{
		if (!self::$initialized)
		{
			if (!\Bitrix\Main\Loader::includeModule("iblock"))
				throw new \Exception(Loc::getMessage("CITRUS_REALTY_IBLOCK_MODULE_NOT_FOUND"));
		}
	}

	/**
	 * Замена символьных кодов свойств на их ID
	 * @param int $iblockId
	 * @param string $string
	 */
	private static function properties2Id($iblockId, &$string)
	{
		static $propertyList = array();
		if (!array_key_exists($iblockId, $propertyList))
		{
			$dbProperties = \CIBlockProperty::GetList(Array(), Array("IBLOCK_ID" => $iblockId));
			while ($property = $dbProperties->Fetch())
				if (strlen($property["CODE"]) > 0)
					$propertyList[$iblockId]["-PROPERTY_" . $property["CODE"] . "-"] = "-PROPERTY_" . $property["ID"] . "-";
		}
		if (is_set($propertyList, $iblockId))
			return ($string = str_ireplace(array_keys($propertyList[$iblockId]), array_values($propertyList[$iblockId]), $string));
		else
			return $string;
	}

	public static function importFromXml($xmlFile, $iblockCode, $iblockType, $siteId = SITE_ID, $permissions = Array())
	{
		self::init();

		$dbIblock = \CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType, "SITE_ID" => $siteId));
		if ($iblock = $dbIblock->Fetch())
			return $iblock["ID"];

		if (!is_array($siteId))
			$siteId = Array($siteId);

		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/".strtolower($GLOBALS["DB"]->type)."/cml2.php");

		$result = ImportXMLFile(
			$xmlFile,
			$iblockType,
			$siteId,
			$section_action = "N",
			$element_action = "N",
			$use_crc = false,
			$preview = false,
			$sync = false,
			$return_last_error = true,
			$return_iblock_id = true
		);
		if (!is_integer($result) || $result <= 0)
			throw new \Exception(strlen($result) ? $result : "Error importing iblock $iblockCode");

		if (empty($permissions))
		{
			$permissions = Array(1 => "X", 2 => "R");
			\CIBlock::SetPermission($result, $permissions);
		}

		return $result;
	}

	public static function importIblock($params, $reinstall = false)
	{
		static $requiredParams = array("xmlId", "site", "type", "file", "code");
		foreach ($requiredParams as $requiredParam)
			if (!is_set($params, $requiredParam) || !strlen($params[$requiredParam]))
				throw new \Bitrix\Main\ArgumentNullException($requiredParam);

		self::init();

		$iblockVersion = array_key_exists("version", $params) && $params["version"] > 1 ? $params["version"] : false;
		$xmlIdWithSite = $params["xmlId"] . "_" . $params["site"];

		$dbIblock = \CIBlock::GetList(array(), array("XML_ID" => $xmlIdWithSite, "TYPE" => $params["type"], "SITE_ID" => $params["site"]));
		$iblockId = false;
		if ($arIBlock = $dbIblock->Fetch())
		{
			$iblockId = $arIBlock["ID"];
			if ($reinstall)
			{
				\CIBlock::Delete($arIBlock["ID"]);
				$iblockId = false;
			}
		}

		if ($iblockId == false)
		{
			// если в параметрах указана версия инфоблока большая 1, подставим номер версии в событии на добавление инфоблока
			if ($iblockVersion)
			{
				$eventManager = \Bitrix\Main\EventManager::getInstance();
				$eventKey = $eventManager->addEventHandler("iblock", "OnBeforeIBlockAdd", function (&$arFields) use ($params, $iblockVersion) {
					if ($arFields["XML_ID"] == $params["xmlId"])
						$arFields["VERSION"] = $iblockVersion;
				});
			}

			$iblockId = self::importFromXml(
				$params["file"],
				$params["code"],
				$params["type"],
				$params["site"],
				$permissions = Array(
					"1" => "X",
					"2" => "R"
				)
			);

			if ($iblockVersion && $eventKey)
				$eventManager->removeEventHandler("iblock", "OnBeforeIBlockAdd", $eventKey);

			if ($iblockId < 1)
				throw new \Exception("Error importing iblock " . $params["code"]);

			// настройка полей формы редактирования
			if (array_key_exists('formFields', $params) && is_array($params['formFields']))
			{
				// замена кодов свойств на их ID для установки настроек
				self::properties2Id($iblockId, $params['formFields']['tabs']);
				\CUserOptions::SetOption("form", "form_element_" . $iblockId, $params['formFields'], $bCommon = true);
			}

			// настройка полей списков
			$listSettingsFields = array(
				'sectionListFields' => "tbl_iblock_section_".md5($params["type"].".".$iblockId),
				'elementListFields' => "tbl_iblock_element_".md5($params["type"].".".$iblockId),
				'combinedListFields' => "tbl_iblock_list_".md5($params["type"].".".$iblockId),
			);
			foreach ($listSettingsFields as $param => $option)
			{
				if (array_key_exists($param, $params) && is_array($params[$param]))
				{
					// замена кодов свойств на их ID для установки настроек
					self::properties2Id($iblockId, $params[$param]['columns']);
					\CUserOptions::SetOption("list", $option, $params[$param], $bCommon = true);
				}
			}

			$arFields = Array("ACTIVE" => "Y", "XML_ID" => $xmlIdWithSite);
			if ($params["iblockFields"])
				$arFields["FIELDS"] = $params["iblockFields"];
			$obIblock = new \CIBlock();
			$obIblock->Update($iblockId, $arFields);
		}
		return $iblockId;
	}
}