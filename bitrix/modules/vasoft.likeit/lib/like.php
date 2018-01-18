<?php
namespace Vasoft\Likeit;

use \Bitrix\Main\Entity;
use Bitrix\Main\Context;
use Bitrix\Main\Application;

/**
 * Class LikeTable Таблица дя хранения лайков проставленных пользователями
 *
 * @package Vasoft\Likeit
 * @author Alexander Vorobyev https://va-soft.ru/
 * @version 1.0.2
 */
class LikeTable extends Entity\DataManager
{
	const LIKE_RESULT_ERROR = 0;
	const LIKE_RESULT_ADDED = 1;
	const LIKE_RESULT_REMOVED = 2;
	const COOKIE_NAME = 'VSLK_HISTORY';

	public static function getTableName()
	{
		return 'vasoft_likeit_like';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\IntegerField('ELEMENTID', array(
				'required' => true,
			)),
			new Entity\StringField('IP', array(
				'required' => true,
			)),
			new Entity\StringField('USERAGENT', array(
				'required' => true,
			)),
			new Entity\StringField('HASH', array(
				'required' => true,
			)),
			new Entity\IntegerField('USERID', array()),
		);
	}

	/**
	 * Создает индексы при установке модуля
	 */
	public static function createIndexes()
	{
		$connection = Application::getInstance()->getConnection(self::getConnectionName());
		if ('mysql' == $connection->getType()) {
			$sql = "CREATE UNIQUE INDEX %s ON " . self::getTableName() . " (%s)";
			$connection->queryExecute(sprintf($sql, 'VASOFT_LIKIT_HASH_EID', 'HASH, ELEMENTID'));
			$sql = "CREATE INDEX %s ON " . self::getTableName() . " (%s)";
			$connection->queryExecute(sprintf($sql, 'VASOFT_LIKIT_EID', 'ELEMENTID'));
			$connection->queryExecute(sprintf($sql, 'VASOFT_LIKIT_HASH', 'HASH'));
			$connection->queryExecute(sprintf($sql, 'VASOFT_LIKIT_USERID', 'USERID'));
		}
	}

	/**
	 * Удаляет индексы при удалении модуля
	 */
	public static function dropIndexes()
	{
		$connection = Application::getInstance()->getConnection(self::getConnectionName());
		if ('mysql' == $connection->getType()) {
			$sql = "DROP INDEX %s ON " . self::getTableName();
			$connection->queryExecute(sprintf($sql, 'VASOFT_LIKIT_HASH'));
			$connection->queryExecute(sprintf($sql, 'VASOFT_LIKIT_USERID'));
			$connection->queryExecute(sprintf($sql, 'VASOFT_LIKIT_EID'));
			$connection->queryExecute(sprintf($sql, 'VASOFT_LIKIT_HASH_EID'));
		}
	}

	/**
	 * Проверяет коичество лайков для списка элементов инфоблока
	 * @param array $arIDs массив ИД элементов ИБ
	 * @param bool $foruser создать массив по текущему пользователю (true) или полный (false)
	 * @return array
	 */
	public static function checkLike(array $arIDs, $foruser = true)
	{
		$cntIds = count($arIDs);
		$arResult = [];
		if ($cntIds > 0) {
			if ($foruser) {
				$arFilterOnce = self::getFields();
				$arFilterOnce['LOGIC'] = 'OR';
			}
			else {
				$arFilterOnce = [];
			}
			if ($cntIds == 1) {
				$arFilter[] = $arFilterOnce;
				$arFilter['ELEMENTID'] = $arIDs[0];
				$arResult[$arIDs[0]] = 0;
			} else {
				$arFilter = ['LOGIC' => 'OR'];
				foreach ($arIDs as $id) {
					$arFilterSub = $arFilterOnce;
					$arFilterSub['ELEMENTID'] = $id;
					$arFilter[] = $arFilterSub;
					$arResult[$id] = 0;
				}
			}
			$likeIterator = self::getList([
				'filter' => $arFilter,
				'select' => ['ELEMENTID', 'CNT'],
				'group' => ['ELEMENTID'],
				'runtime' => [
					'CNT' => [
						'data_type' => 'integer',
						'expression' => ["COUNT(%s)", 'ID']
					]
				]
			]);
			while ($arRecord = $likeIterator->fetch()) {
				$arResult[$arRecord['ELEMENTID']] = $arRecord['CNT'];
			}
		}
		return $arResult;
	}

	/**
	 * Получение полной статистики по лайкам с информацией о выборе текущего пользователя
	 * @param array $arIDS массивИД элементов ИБ
	 * @return array
	 */
	public static function getStatList(array $arIDS)
	{
		$arAll = self::checkLike($arIDS, false);
		$arUser = self::checkLike($arIDS);
		$arResult = [];
		foreach ($arAll as $key => $count) {
			$arResult[] = [
				'ID' => $key,
				'CNT' => $count,
				'CHECKED' => $arUser[$key]
			];
		}
		return $arResult;
	}

	/**
	 * Поучение хэша текущего поьзователя
	 * @return string
	 */
	public static function getHash()
	{
		$server = Context::getCurrent()->getServer();
		return md5($server->get('HTTP_USER_AGENT') . ' ' . $server->get('REMOTE_ADDR'));
	}

	/**
	 * Получение ассива общих полей
	 * @return array
	 */
	private static function getFields()
	{
		global $USER;
		$arResult = [];
		if ($USER->IsAuthorized()) {
			$arResult['USERID'] = $USER->GetId();
		}
		$arResult['HASH'] = self::getCookie();
		return $arResult;
	}

	/**
	 * Получние значения куки текущего пользователя, если куки не существует - создается
	 * @return string
	 */
	public static function getCookie()
	{
		global $APPLICATION;

		$request = Context::getCurrent()->getRequest();
		$verifyCookie = trim($request->getCookie(self::COOKIE_NAME));
		if ($verifyCookie == '') {
			$verifyCookie = self::getHash();
		}
		/**
		 * @todo разобравться как поставить куку D7
		 * Добавление кук на D7 работает иначе. Еси выполнение прерывается,то кука на ставится.
		 * Данный метод вызывается ajax.
		 */
		$APPLICATION->set_cookie(self::COOKIE_NAME, $verifyCookie, time() + 60480000);
		return $verifyCookie;
	}

	/**
	 * Устанваивает/снимает лайк для элемента ИБ с ИД переданным в качестве параметра
	 * @param $ID ИД элемента инфоблока
	 * @return int резуьтат выпоненения:
	 * - 0 - ошибка LikeTable::LIKE_RESULT_ERROR
	 * - 1 - добавлен LikeTable::LIKE_RESULT_ADDED
	 * - 2 - удален LikeTable::LIKE_RESULT_REMOVED
	 */
	public static function setLike($ID)
	{
		$arLikes = self::checkLike([$ID]);
		$arFilter = self::getFields();
		if ($arLikes[$ID] == 0) {
			$arFilter['ELEMENTID'] = $ID;
			$server = Context::getCurrent()->getServer();
			$arFilter['IP'] = $server->get('REMOTE_ADDR');
			$arFilter['USERAGENT'] = $server->get('HTTP_USER_AGENT');
			$res = self::add($arFilter);
			$result = $res->isSuccess() ? self::LIKE_RESULT_ADDED : self::LIKE_RESULT_ERROR;
		} else {
			$arFilter['LOGIC'] = 'OR';
			$arFilter = [$arFilter,'ELEMENTID' => $ID]; 
			$likeIterator = self::getList(['filter' => $arFilter, 'select' => ['ID']]);
			if ($likeIterator->getSelectedRowsCount() == 1) {
				$arRecord = $likeIterator->fetch();
				$res = self::delete($arRecord['ID']);
				$result = $res->isSuccess() ? self::LIKE_RESULT_REMOVED : self::LIKE_RESULT_ERROR;
			} else {
				$result = self::LIKE_RESULT_REMOVED;
			}
		}
		return $result;
	}

	/**
	 * Обработчик события уделения элемента инфоблока
	 * @param $ID
	 */
	public static function onBeforeElementDeleteHandler($ID)
	{
		$ID = intval($ID);
		if ($ID > 0) {
			$connection = Application::getInstance()->getConnection(self::getConnectionName());
			$sql = "DELETE FROM " . self::getTableName() . " WHERE ELEMENTID = %d";
			$connection->queryExecute(sprintf($sql, $ID));
		}
	}
}
