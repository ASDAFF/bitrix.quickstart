<?
/**
 *  module
 * 
 * @category	
 * @package		UserField
 * @link		http://.ru
 * @revision	$Revision: 2062 $
 * @date		$Date: 2014-10-23 14:18:32 +0400 (Чт, 23 окт 2014) $
 */

namespace Site\Main\UserField;

/**
 * Перечисляемый тип для списка пользователей
 * 
 * @category	
 * @package		UserField
 */
class UserEnum extends \CDBResult
{
	/**
	 * Возвращает список пользователей
	 *
	 * @param array $filter Фильтр
	 * @param string $sortBy Поле сортировки
	 * @param string $sortDir Направление сортировки
	 * @return \CDBResult
	 */
	function GetTreeList($filter = array(), $sortBy = 'LAST_NAME', $sortDir = 'asc')
	{
		$users = \CUser::GetList($sortBy, $sortDir, $filter, array(
			'FIELDS' => array(
				'ID',
				'LOGIN',
				'NAME',
				'LAST_NAME',
				'EMAIL',
			)
		)); 
		
		if ($users) {
			$users = new UserEnum($users);
		}
		
		return $users;
	}
	
	/**
	 * Возвращает очередную запись из списка
	 *
	 * @param boolean $textHtmlAuto Экранировать значения
	 * @param boolean $useTilda Сохранять оригинал
	 * @return array
	 */
	function GetNext($textHtmlAuto = true, $useTilda = true)
	{
		$row = parent::GetNext($textHtmlAuto, $useTilda);
		if ($row) {
			$row['VALUE'] = $row['LAST_NAME'] . ' ' . $row['NAME'] . ' [' . $row['LOGIN'] . ']';
		}
		
		return $row;
	}
}
