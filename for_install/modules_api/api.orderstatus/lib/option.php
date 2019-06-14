<?php
namespace Api\OrderStatus;

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class HistoryTable
 *
 * Fields:
 * <ul>
 * <li> IBLOCK_ID int mandatory
 * <li> YANDEX_EXPORT bool optional default 'N'
 * <li> SUBSCRIPTION bool optional default 'N'
 * <li> VAT_ID int optional
 * <li> PRODUCT_IBLOCK_ID int mandatory
 * <li> SKU_PROPERTY_ID int mandatory
 * </ul>
 *
 * @package Api\OrderStatus
 **/
class OptionTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'api_orderstatus_option';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'ID'          => new Main\Entity\IntegerField('ID', array(
				'primary'      => true,
				'autocomplete' => true,
				'title'        => Loc::getMessage('AOS_OPTION_ID'),
			)),
			'NAME'    => new Main\Entity\StringField('NAME', array(
				'title'   => Loc::getMessage('AOS_OPTION_NAME'),
			)),
			'VALUE'   => new Main\Entity\TextField('VALUE', array(
				'title' => Loc::getMessage('AOS_OPTION_'),
			)),
			'SITE_ID' => new Main\Entity\StringField('SITE_ID', array(
				'title' => Loc::getMessage('AOS_OPTION_SITE_ID'),
			)),
		);
	}

	public static function addEx($option)
	{
		$option = (array)$option;
		if(!$option)
			return false;

		$filter = array('=NAME' => $option['NAME']);
		if($option['SITE_ID']){
			$filter['=SITE_ID'] = $option['SITE_ID'];
		}

		$arExistOption = self::getList(array(
			'select' => array('ID'),
			'filter' => $filter,
		))->fetch();

		if($arExistOption['ID'])
			self::update($arExistOption['ID'], $option);
		else
			$arExistOption['ID'] = self::add($option);

		return $arExistOption['ID'];
	}
	
	public static function getOtions($siteId)
	{
		if(!$siteId)
			return false;

		$connection = Application::getConnection();
		$sqlHelper = $connection->getSqlHelper();

		static $map = array();
		if(!$map)
		{
			$strSql = "SELECT NAME, VALUE, SITE_ID
							FROM ". self::getTableName() ."
							WHERE SITE_ID = '". $sqlHelper->forSql($siteId) ."'";

			$result = $connection->query($strSql);

			while ($option = $result->fetch())
			{
				$map[ $option['NAME'] ] = $option['VALUE'];
			}

			if(isset($map['SALE_LOGO']))
				$map['SALE_LOGO'] = '<img src="'. $map['SALE_URL'] . $map['SALE_LOGO'] .'">';

		}

		/*if(!$map)
		{
			$where = array();
			foreach($arFields as $field)
				$where[] = "NAME='". $sqlHelper->forSql($field) ."'";

			$strSql = "SELECT NAME, VALUE, SITE_ID
							FROM ". self::getTableName() ."
							WHERE (". implode(' OR ', $where) .")
								AND SITE_ID = '". $sqlHelper->forSql($siteId) ."'
							";

			$result = $connection->query($strSql);

			while ($arOption = $result->fetch())
			{
				$map[$arOption['NAME']] = $arOption['VALUE'];
			}
		}
		*/

		return isset($map) ? $map : false;
	}
}