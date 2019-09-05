<?php
/**
 *  module
 *
 * @category
 * @link		http://.ru
 * @revision	$Revision: 2062 $
 * @date		$Date: 2014-10-23 13:18:32 +0300 (Чт, 23 окт 2014) $
 */

namespace Site\Main;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

/**
 * Утилиты для работы с пользователями
 */
class Packets
{
	public static function getPacketsCount(){
		$arPackets = array();
	}

	/**
	 * Получение информации о типах пользователей пользователей
	 *
	 * @param     $arUTypesId - ID типов пользователей
	 * @param int $cacheTime - время кеширования
	 *
	 * @return array|mixed
	 */
	public function getPacketsStatusInfo($arStatusValId, $cacheTime = 86400){
		$arStatuses = array();
		$cache = new Cache(array(__METHOD__, $arStatusValId), __CLASS__, $cacheTime);
		if ($cache->start()) {
			/*Получаем типы пользователей*/
			$rsStatuses = \CUserFieldEnum::GetList(
				array(),
				array('ID' => array_keys($arStatusValId))
			);

			if( $rsStatuses->SelectedRowsCount() > 0 ){
				while($arStatus = $rsStatuses->GetNext()){
					$arStatuses[$arUType['ID']] = $arStatus['VALUE'];
				}

				$cache->end($arUserTypes);
			}
			else{
				$cache->abort();
			}
		}
		else{
			$arStatuses = $cache->getVars();
		}

		return	$arStatuses;
	}
}