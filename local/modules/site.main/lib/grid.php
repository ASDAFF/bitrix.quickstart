<?
/**
 *  module
 *
 * @category
 * @link        http://.ru
 * @revision    $Revision: 2062 $
 * @date        $Date: 2014-10-23 13:18:32 +0300 (Чт, 23 окт 2014) $
 */

namespace Site\Main;

use \Bitrix\Main\Application;
use \Site\Main\Hlblock\Packets;

/**
 * Утилиты для работы с гридами
 *
 */
class Grid
{
	/**
	 * Получение информации для построения грида по юзерам
	 *
	 * @return array
	 */
	public static function getUserGridInfo()
	{
		global $APPLICATION;

		$arResult = array();

		//уникальный идентификатор грида
		$arResult["GRID_ID"] = "grid_user";

		//определяем фильтр, поля фильтра типизированы
		$arResult["FILTER"] = array(
			array(
				"id"    => "FIND",
				"name"  => "Найти",
				"type"  => "quick",
				"items" => array(
					"email" => "Email",
					"name"  => "Имя"
				)
			),
			array(
				"id"   => "PERSONAL_CITY",
				"name" => "Город"
			),
			array(
				"id"   => "EMAIL",
				"name" => "Почта"
			),
			array(
				"id"   => "NAME",
				"name" => "ФИО"
			),
		);

		//инициализируем объект с настройками пользователя для нашего грида
		$grid_options = new \CGridOptions($arResult["GRID_ID"]);

		//какую сортировку сохранил пользователь (передаем то, что по умолчанию)
		$arSort = $grid_options->GetSorting(array(
				"sort" => array("id" => "desc"),
				"vars" => array(
					"by"    => "by",
					"order" => "order"
				)
			)
		);

		//размер страницы в постраничке (передаем умолчания)
		$arNav = $grid_options->GetNavParams(array("nPageSize" => 20));

		//получим текущий фильтр (передаем описанный выше фильтр)
		$arFilter = $grid_options->GetFilter($arResult["FILTER"]);
		//некоторые названия полей фильтра могут не совпадать с API
		if( isset( $arFilter["FIND"] ) ) {
			$arFilter[strtoupper($arFilter["FIND_list"])] = $arFilter["FIND"];
		}

		// выборка данных с учетом сортировки и фильтра, указанных пользователем
		$arSortArg = each($arSort["sort"]);
		$rsUsers = \CUser::GetList($arSortArg["key"], $arSortArg["value"], $arFilter, array(
			'FIELDS' => array('ID', 'NAME', 'ACTIVE', 'PERSONAL_PHONE', 'EMAIL', 'PERSONAL_COUNTRY', 'PERSONAL_CITY'),
			'SELECT'	 => array('UF_RATING', 'UF_USER_TYPE')
		));

		//постраничка с учетом размера страницы
		$rsUsers->NavStart($arNav["nPageSize"]);

		//в этом цикле построчно заполняем данные для грида
		$arRows = array();
		$usersCount = $rsUsers->SelectedRowsCount();
		if( $usersCount > 0 ){
			//это определения для меню действий над строкой
			$arActions = Array(
				array("SEPARATOR" => true),
				array(
					"ICONCLASS" => "delete",
					"TEXT"      => "Удалить",
					"ONCLICK"   => "if(confirm('Вы уверены, что хотите удалить данного пользователя?')) window.location='/bitrix/admin/user_admin.php?action=delete&ID=" . $arUser["ID"] . "&" . bitrix_sessid_get() . "';"
				),
			);

			$arCountries = \GetCountryArray();
			$arUserTypes = array();
			while($arUser = $rsUsers->GetNext()) {
				$arActions = new Mvc\View\Php('grids/user-actions.php',
					array(
						'USER_ID' => $arUser['ID'],
						'STATUS' => ( $arUser['ACTIVE'] == 'Y' ) ? 'N' : 'Y'
					)
				);
				$arUser['ACTIONS'] = $arActions->render();

				$countryRefId = array_search($arUser['PERSONAL_COUNTRY'], $arCountries['reference_id']);
				$arUser['PERSONAL_COUNTRY'] = $arCountries['reference'][$countryRefId];
				$arUserTypes[$arUser['UF_USER_TYPE']] = array();

				//запомнили данные. "data" - вся выборка,  "editable" - можно редактировать строку или нет
				$arRows[] = array(
					"data"     => $arUser,
					"actions"  => $arActions,
					"editable" => true
				);
			}

			$arUserTypes = User::getUTypesInfo($arUserTypes);

			foreach($arRows as &$arRow){
				if( empty($arRow['data']['UF_USER_TYPE']) ){
				    continue;
				}
				$arRow['data']['UF_USER_TYPE'] = $arUserTypes[$arRow['data']['UF_USER_TYPE']];
			}
			unset($arRow);

			//наши накопленные данные
			$arResult["ROWS"] = $arRows;

			//информация для футера списка
			$arResult["ROWS_COUNT"] = $usersCount;

			//сортировка
			$arResult["SORT"] = $arSort["sort"];
			$arResult["SORT_VARS"] = $arSort["vars"];

			//объект постранички - нужен гриду. Убираем ссылку "все".
			$rsUsers->bShowAll = false;
			$arResult["NAV_OBJECT"] = $rsUsers;
		}

		return $arResult;
	}


	/**
	 * Получение информации для построения грида по пакетам
	 *
	 * @return array
	 */
	public static function getPacketGridInfo()
	{
		global $APPLICATION;

		$arResult = array();

		//уникальный идентификатор грида
		$arResult["GRID_ID"] = "grid_pack";
		$arReq = Application::getInstance()->getContext()->getRequest()->toArray();
		$bUseUserFilter = false;

		//определяем фильтр, поля фильтра типизированы
		$arResult["FILTER"] = array(
			array(
				"id"    => "FIND_PACK",
				"name"  => "Найти",
				"type"  => "quick",
				"items" => array(
					"uf_name"  => "Название пакета",
				)
			),
			array(
				"id"   => "UF_NAME",
				"name" => "Название пакета",
			),
		);

		//инициализируем объект с настройками пользователя для нашего грида
		$grid_options = new \CGridOptions($arResult["GRID_ID"]);

		//какую сортировку сохранил пользователь (передаем то, что по умолчанию)
		$arSort = $grid_options->GetSorting(array(
				"vars" => array(
					"by"    => "UF_NAME",
					"order" => "asc"
				)
			)
		);
		if( !empty($arReq['by']) && !empty($arReq['order']) && strpos($arReq['by'], 'ADDITIONAL') === false ){
			$arSort['sort'] = array($arReq['by'] => $arReq['order']);
		}
		else{
			$arSort['sort'] = array('ID' => 'asc');
		}
		
		$arNav = $grid_options->GetNavParams(array('nPageSize' => 10));

		//получим текущий фильтр (передаем описанный выше фильтр)
		if( !empty($arReq['FIND_PACK']) && !empty($arReq['FIND_PACK_list']) ){
			$arFilter = $grid_options->GetFilter($arResult["FILTER"]);
		}

		//некоторые названия полей фильтра могут не совпадать с API
		if( isset($arFilter["FIND_PACK"]) && !empty($arFilter["FIND_PACK"]) ) {
			$arFilter = array(strtoupper($arFilter["FIND_PACK_list"]) => $arFilter["FIND_PACK"]);
		}
		
		// выборка данных с учетом сортировки и фильтра, указанных пользователем
		$arSortArg = each($arSort["sort"]);

		$arStatuses = Packets::getPStatusesValId();
		$arFilter['UF_STATUS'] = $arStatuses['На проверке'];

		$arPackets = Packets::getInstance()->getData(
			$arFilter,
			array(
				'ID',
				'UF_DATE_CHANGE',
				'UF_NAME',
				'USER_NAME' => 'USER.NAME',
				'USER_EMAIL' => 'USER.EMAIL',
				'USER_RATING' => 'USER.UF_RATING',
				'UF_PACKET_FILE' 
			),
			array($arSortArg['key'] => $arSortArg['value']),
			( !empty($arReq['PAGEN_1']) ? $arReq['PAGEN_1'] - 1 : null ) * $arNav['nPageSize'],
			$arNav['nPageSize'],
			array(
				'USER' => array(
					'data_type' => '\Bitrix\Main\UserTable',
					'reference' => array(
						'=this.UF_USER' => 'ref.ID'
					),
					'join_type' => 'inner'
				)
			),
			true
		);

		$packetsCount = count($arPackets['ITEMS']);
		if( $packetsCount > 0 ) {
			//это определения для меню действий над строкой
			$arActions = Array(
				array("SEPARATOR" => true),
				array(
					"ICONCLASS" => "delete",
					"TEXT"      => "Удалить",
					"ONCLICK"   => "if(confirm('Вы уверены, что хотите удалить данного пользователя?')) window.location='/bitrix/admin/user_admin.php?action=delete&ID=" . $arUser["ID"] . "&" . bitrix_sessid_get() . "';"
				),
			);


			$arRows = $arUsers = array();

			//в этом цикле построчно заполняем данные для грида
			foreach($arPackets['ITEMS'] as &$arPacket) {
				$bSatisfying = true;
				$arActions = new Mvc\View\Php('grids/packet-actions.php',
					array(
						'UF_PACKET_FILE' => str_replace('available', 'check', $arPacket['UF_PACKET_FILE']),
						'PACKET_ID' => $arPacket['ID'],
					)
				);
				$arPacket['ACTIONS'] = $arActions->render();


				// Если применялся фильтр по пользователям, то еще раз фильтруем данные
				if( $bUseUserFilter ){
					foreach($arUserFilterFields as $filterField => $userField){
						if( !empty($arReq[$filterField]) ){
							$bUseUserFilter = true;
						}
						if( !empty($arReq[$filterField]) && $arPacket[$userField] != $arReq[$filterField] ){
							$bSatisfying = false;
						}
					}
				}

				if( $bSatisfying ){
					//запомнили данные. "data" - вся выборка,  "editable" - можно редактировать строку или нет
					$arRows[] = array(
						"data"     => $arPacket,
						"actions"  => $arActions,
						"editable" => true
					);
				}

			}
			unset( $arPacket );

			if( !empty($arRows) && strpos($arReq['by'], 'ADDITIONAL') !== false && !empty($arReq) ){
				$compField = str_replace('ADDITIONAL_', '', $arReq['by']);
				$compSign = ( $arReq['order'] == 'asc' ) ? 1 : -1;

				uasort($arRows, function($arPacketA, $arPacketB) use($compField, $compSign){
					return strnatcasecmp($arPacketA['data'][$compField], $arPacketB['data'][$compField]) * $compSign;
				});
			}
			
			//постраничка с учетом размера страницы и фильтра по юзерам (Задаетяс обязательно после применения фильтра по подьзователям)
			$pagen = ( $arReq['PAGEN_1'] ) ? $arReq['PAGEN_1'] : 1;
			$arPackets['NAV_OBJECT']->NavStart($arNav['nPageSize'], false, $arPackets['COUNT_INFO']['TOTAL_PAGES']);
			$arPackets['NAV_OBJECT']->NavRecordCount = $arPackets['COUNT_INFO']['TOTAL_COUNT'];
			$arPackets['NAV_OBJECT']->NavPageCount = $arPackets['COUNT_INFO']['TOTAL_PAGES'];
			$arPackets['NAV_OBJECT']->NavPageNomer = $pagen;

			$arResult['PACKETS_COUNT'] = $arPackets['COUNT_INFO']['TOTAL_COUNT'];
			$arResult['NAV_OBJECT'] = $arPackets['NAV_OBJECT'];

			//наши накопленные данные
			$arResult["ROWS"] = $arRows;

			//информация для футера списка
			$arResult["ROWS_COUNT"] = count($arRows);

			//сортировка
			if( !empty($arReq['by']) && !empty($arReq['order']) ){
			    $arResult["SORT"] = array($arReq['by'] => $arReq['order']);
			}
			else{
				$arResult["SORT"] = $arSort["sort"];
			}

			$arResult["SORT_VARS"] = $arSort["vars"];
		}

		return $arResult;
	}
}