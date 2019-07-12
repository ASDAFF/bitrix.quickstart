<?
namespace Citrus\Realty;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * @package Citrus\Realty
 */
class Helper
{
	private static $iblocks = null;
	private static $lastSection = null;

	public static function resetCache()
	{
		self::$iblocks = null;
		self::$lastSection = null;
	}

	/**
	 * ShowPanel()
	 * ��������� ������ ������� ������� �� ���������������� ������
	 *
	 * @return void
	 */
	public static function showPanel()
	{
		if ($GLOBALS["USER"]->IsAdmin() && \COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "citrus_realty")
		{
			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => BX_ROOT . "/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=citrus:realty&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "citrus_realty_wizard",
				"ICON" => "bx-panel-site-wizard-icon",
				"MAIN_SORT" => 2500,
				"TYPE" => "BIG",
				"SORT" => 10,
				"ALT" => Loc::getMessage("CITRUS_REALTY_BUTTON_DESCRIPTION"),
				"TEXT" => Loc::getMessage("CITRUS_REALTY_BUTTON_NAME"),
				"MENU" => array(),
			));
		}
	}

	/**
	 * ���������� ������ � �������������� ���������� ����� ���������� �� ID (��� �������� �����)
	 *
	 * @return array ������������� ������, ��� ����� � ���������� ���� ����������, � �������� � �� ID
	 * @throws \Bitrix\Main\LoaderException
	 * @throws \Exception
	 */
	public static function getIBlockIds($siteId = false)
	{
		if (false === $siteId && !defined("ADMIN_SECTION"))
			$siteId = SITE_ID;
		if (!isset(self::$iblocks) || !isset(self::$iblocks[$siteId]))
		{
			if (!\Bitrix\Main\Loader::includeModule("iblock"))
				throw new \Exception(Loc::getMessage("CITRUS_REALTY_IBLOCK_MODULE_NOT_FOUND"));

			self::$iblocks = array();
			$filter = Array("!CODE" => false);
			if ($siteId)
				$filter["SITE_ID"] = $siteId;
			$dbIblock = \CIBlock::GetList(
				Array(),
				$filter,
				$bIncCnt = false
			);
			while ($iblock = $dbIblock->GetNext())
			{
				if (array_key_exists($iblock["CODE"], self::$iblocks) && !is_array(self::$iblocks[$iblock["CODE"]]))
				{
					self::$iblocks[$siteId][$iblock["CODE"]] = array(self::$iblocks[$iblock["CODE"]]);
					self::$iblocks[$siteId][$iblock["CODE"]][] = $iblock["ID"];
				}
				else
					self::$iblocks[$siteId][$iblock["CODE"]] = $iblock["ID"];
			}
		}
		return self::$iblocks[$siteId];
	}

	/**
	 * ���������� ID ��������� �� ������� ����� �� ��� ����
	 *
	 * @param string $code ���������� ��� ���������
	 * @return int ID ���������
	 * @throws \Exception
	 */
	public static function getIblock($code, $siteId = false)
	{
		$iblocks = self::getIBlockIds($siteId);

		if (!strlen($code))
			throw new \Exception("Empty \$code parameter");
		if (array_key_exists($code, $iblocks))
			return $iblocks[$code];
		else
			throw new \Exception("IBlock �{$code}� not found");
	}

	/**
	 * ���������� ���������� ��� �������� ����� �������� �������
	 *
	 * @return string
	 */
	public static function getTheme()
	{
		return \COption::GetOptionString("main", "wizard_citrus_realestate_theme_id", "red", SITE_ID);
	}

	/**
	 * ���������� ���������� � ���������� � �����
	 *
	 * @param int|bool $contactId ID ���������, ���� �� ������, ����� ������ ������ �� ������ (� ������� �������� ����������)
	 * @return array|bool
	 * @throws \Exception
	 */
	public static function getContactInfo($contactId = false)
	{
		$filter = Array("ACTIVE" => "Y", "IBLOCK_ID" => self::getIblock('staff'));
		if ($contactId)
			$filter["ID"] = $contactId;
		$dbContacts = \CIBlockElement::GetList(
			Array("SORT" => "ASC"),
			$filter,
			$arGroupBy = false,
			$arNavStartParams = array("nTopCount" => 1),
			$arSelectFields = Array("ID", "NAME","DETAIL_PAGE_URL","PROPERTY_office", "PROPERTY_contacts")
		);
		$dbContacts->SetUrlTemplates();
		if ($contact = $dbContacts->GetNext(true, false))
		{
			$dbOffice = \CIBlockElement::GetList(
				Array("SORT" => "ASC"),
				Array("ACTIVE" => "Y", "ID" => $contact["PROPERTY_OFFICE_VALUE"], "IBLOCK_ID" => self::getIblock('offices')),
				$arGroupBy = false,
				$arNavStartParams = array("nTopCount" => 1),
				$arSelectFields = array("ID", "NAME", "PROPERTY_address", "PROPERTY_phones", "PROPERTY_schedule")
			);
			if ($office = $dbOffice->GetNext(true, false))
				$contact['office'] = $office;
			else
				$contact['office'] = false;
		}
		else
			return false;
		return $contact;
	}

	/**
	 * ���������� ������ �� ������� ������
	 *
	 * @return array
	 * @throws \Bitrix\Main\LoaderException
	 * @throws \Exception
	 */
	public static function getOfficesDropdownList()
	{
		$list = array();
		if (\Bitrix\Main\Loader::includeModule("iblock"))
		{
			$dbOffice = \CIBlockElement::GetList(
				Array("SORT" => "ASC"),
				Array("ACTIVE" => "Y", "IBLOCK_ID" => self::getIblock('offices')),
				$arGroupBy = false,
				$arNavStartParams = false,
				$arSelectFields = array("ID", "NAME")
			);
			while ($office = $dbOffice->GetNext(true, false))
				$list[$office["ID"]] = $office["NAME"];
		}
		return $list;
	}

	/**
	 * ���������� ���� ���������� �����
	 * @param bool $officeId ID ������������ �����. ���� �������� �� ������, ����� ���������� ���� ������� � ������ ����� (�� ������� ����������)
	 * @return array|bool ���� ����� ��� false � ������ ���� ����(�) �� ������
	 * @throws \Exception
	 */
	public static function getOfficeInfo($officeId = false)
	{
		$filter = Array("ACTIVE" => "Y", "IBLOCK_ID" => self::getIblock('offices'));
		if (intval($officeId) > 0)
			$filter["ID"] = intval($officeId);
		$dbOffice = \CIBlockElement::GetList(
			Array("SORT" => "ASC"),
			$filter,
			$arGroupBy = false,
			$arNavStartParams = array("nTopCount" => 1),
			$arSelectFields = array("ID", "NAME", "PROPERTY_address", "PROPERTY_phones", "PROPERTY_schedule", "IBLOCK_ID")
		);
		if ($office = $dbOffice->GetNext(true, false))
			return $office;
		else
			return false;
	}

	/**
	 * ���������� ������ ����������� �������� ��������� (����������� � ��������)
	 *
	 * @param array $item ������� ��������� � ������ PREVIEW_PICTURE �/��� DETAIL_PICTURE
	 * @param int $width ������ ��������
	 * @param int $hight ������ ��������
	 * @return array ������, ������������ ������� \CFile::ResizeImageGet()
	 */
	public static function resizeOfferImage($item, $width, $hight)
	{
		if (is_array($item["PREVIEW_PICTURE"]))
			$preview = \CFile::ResizeImageGet($item["PREVIEW_PICTURE"]["ID"], Array('width' => $width, 'height' => $hight), BX_RESIZE_IMAGE_EXACT, $bInitSizes = true);
		elseif (is_array($item["DETAIL_PICTURE"]))
			$preview = \CFile::ResizeImageGet($item["DETAIL_PICTURE"]["ID"], Array('width' => $width, 'height' => $hight), BX_RESIZE_IMAGE_EXACT, $bInitSizes = true);
		else
			// nook return image placeholder
			array();
		return $preview;
	}

	/**
	 * ��������� ID �������, � ������� ����� ������� (������������ ��� ������������ ������ ������ ����������� �� ���� �� �������)
	 * @param int $sectionId ID �������
	 */
	public static function setLastSection($sectionId)
	{
		self::$lastSection = $sectionId;
	}

	/**
	 * ���������� ID �������, ������������ ������� setLastSection()
	 * @return int ID �������
	 * @throws \Exception
	 */
	public static function getLastSection()
	{
		if (!isset(self::$lastSection))
			throw new \Exception('$lastSection isn\'t set');
		return self::$lastSection;
	}

	/**
	 * ���������� �� �������� � ��������� ���������� ����� � ��������� ���������
	 * @param int $iblockId ID ��������������� �����
	 * @param string $code ���������� ��� ��������
	 * @return bool|int ID ��������, ���� false, ���� �������� � ����� ����� �� �������
	 * @throws \Bitrix\Main\ArgumentTypeException
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 * @throws \Bitrix\Main\Config\ConfigurationException
	 */
	public static function getPropertyIdByCode($iblockId, $code)
	{
		static $cache = array();

		if (!\CModule::IncludeModule("iblock"))
			throw new \Bitrix\Main\Config\ConfigurationException("iblock module is required");
		if (intval($iblockId) <= 0)
			throw new \Bitrix\Main\ArgumentOutOfRangeException("iblockId", 1);

		if (!array_key_exists($iblockId, $cache))
		{
			$dbProperties = \CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $iblockId, "!CODE" => false));
			while ($property = $dbProperties->Fetch())
				$cache[$iblockId][$property["CODE"]] = $property["ID"];
		}
		return array_key_exists($code, $cache[$iblockId]) ? $cache[$iblockId][$code] : false;
	}

}