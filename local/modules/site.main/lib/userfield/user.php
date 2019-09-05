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
 * Пользовательское поле "Привязка к пользователю"
 * 
 * @category	
 * @package		UserField
 */
class User extends \CUserTypeEnum
{
	/**
	 * Возвращает описание поля
	 *
	 * @return array
	 */
	function GetUserTypeDescription()
	{
		return array_merge(parent::GetUserTypeDescription(), array(
			'USER_TYPE_ID' => 'site_main_user',
			'CLASS_NAME' => __CLASS__,
			'DESCRIPTION' => 'Привязка к пользователю',
			'BASE_TYPE' => 'int',
		));
	}
	
	/**
	 * Вызывается перед сохранением метаданных свойства в БД
	 *
	 * @param array $userField Массив описывающий поле
	 * @return array Массив который в дальнейшем будет сериализован и сохранен в БД
	 */
	function PrepareSettings($userField)
	{
		return array(
			'GROUP_ID' => intval($userField['SETTINGS']['GROUP_ID']),
		);
	}
	
	/**
	 * Выводит форму настройки свойства
	 *
	 * @param array $userField Массив описывающий поле
	 * @param array $htmlControl Массив управления из формы. Пока содержит только один элемент NAME (html безопасный)
	 * @param boolean $varsFromForm Взять введенное в форму значение
	 * @return string HTML для вывода
	 */
	function GetSettingsHTML($userField, $htmlControl, $varsFromForm)
	{
		$result = '';
		
		$groupId = (int) $varsFromForm ? $GLOBALS[$htmlControl['NAME']]['GROUP_ID'] : $userField['SETTINGS']['GROUP_ID'];
		
		$result .= '<tr>
			<td>Группа:</td>
			<td>
				<select name="' . $htmlControl['NAME'] . '[GROUP_ID]">
					<option value="0">Все пользователи</option>';
		
		$groups = \CGroup::GetDropDownList();
		while ($group = $groups->Fetch()) {
			$result .= '<option value="' . $group['REFERENCE_ID'] . '"' . ($group['REFERENCE_ID'] == $groupId ? ' selected="selected"' : '') . '>' . $group['REFERENCE'] . '</option>';
		}
		
		$result .= '</select>
			</td>
		</tr>';
		
		return $result;
	}
	
	/**
	 * Возвращает список пользователей для вывода в интерфейсе
	 *
	 * @param array $userField Массив описывающий поле
	 * @return \CDBResult
	 */
	function GetList($userField)
	{
		$filter = array();
		if ($userField['SETTINGS']['GROUP_ID']) {
			$filter['GROUPS_ID'] = array($userField['SETTINGS']['GROUP_ID']);
		}
		
		$enum = new UserEnum();
		return $enum->GetTreeList($filter);
	}
}