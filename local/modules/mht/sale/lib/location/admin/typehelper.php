<?
/**
 * This class is for internal use only, not a part of public API.
 * It can be changed at any time without notification.
 *
 * @access private
 */

namespace Bitrix\Sale\Location\Admin;

class TypeHelper extends NameHelper
{
	const LIST_PAGE_URL = 'sale_location_type_list.php';
	const EDIT_PAGE_URL = 'sale_location_type_edit.php';

	#####################################
	#### Entity settings
	#####################################

	/**
	* Function returns class name for an attached entity
	* @return string Entity class name
	*/
	public function getEntityRoadMap()
	{
		return array(
			'main' => array(
				'name' => 'Bitrix\Sale\Location\Type',
			),
			'name' => array(
				'name' => 'Bitrix\Sale\Location\Name\Type',
				'pages' => array(
					'list' => array(
						'includedColumns' => array('NAME')
					),
					'detail' => array(
						'includedColumns' => array('NAME')
					)
				)
			),
		);
	}

	public static function getTypeCodeIdMapCached()
	{
		static $types;

		if($types == null)
		{
			$res = \Bitrix\Sale\Location\TypeTable::getList(array(
				'select' => array(
					'ID', 'CODE'
				)
			));

			$types = array();
			while($item = $res->Fetch())
			{
				$types['ID2CODE'][intval($item['ID'])] = $item['CODE'];
				$types['CODE2ID'][$item['CODE']] = intval($item['ID']);
			}
		}

		return $types;
	}
}