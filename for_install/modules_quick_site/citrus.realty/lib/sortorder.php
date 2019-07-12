<?
namespace Citrus\Realty;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Класс для работы с сортировкой предложений
 * @package Citrus\Realty
 */
class SortOrder
{
	protected static $fields = array("~DATE_CREATE" => "Create date");
	protected static $orderSet = array(
        "desc" => "desc",
		"asc" => "asc",
	);
	private static $current = null;

	public static function getFieldName($code)
	{
		return $code[0] == '~' ? substr($code, 1) : 'PROPERTY_' . $code;
	}

	/**
	 * Возвращает массив с текущими параметрами сортировки предложений
	 * @return array
	 */
	public static function getCurrent()
	{
		// TODO !
		$field = array_key_exists('sort', $_REQUEST) ? $_REQUEST["sort"] : array_shift(array_keys(self::$fields));
        if ($field == 'rooms')
            self::$orderSet = array(
                "asc" => "asc",
                "desc" => "desc",
            );
		$order = array_key_exists('order', $_REQUEST) && array_key_exists($_REQUEST['order'], self::$orderSet) ? $_REQUEST["order"] : array_shift(array_keys(self::$orderSet));
		return array(self::getFieldName($field), $order);
	}

	/**
	 * Возвращает массив с параметрами сортировки, который можно передать в вызов \CIBlockElement::GetList()
	 *
	 * @return array|null
	 */
	public static function getArray()
	{
		if (!isset(self::$current))
		{
			$current = self::getCurrent();
			self::$current = array(
				$current[0] => $current[1],
				"ID" => "desc",
			);
		}
		return self::$current;
	}

	/**
	 * Возвращает текущее поле, по которому производится сортировка
	 * @param int $i Порядковый номер поля (при использовании множественной сортировки). Если не указан == 0
	 * @return string Код поля
	 */
	public static function getField($i = 0)
	{
		$order = self::getArray();
		$i = $i >= 0 && $i < count($order) ? $i : 0;
		$fields = array_keys($order);
		return $fields[$i];
	}

	/**
	 * Возвращает текущее направление сортировки
	 * @param int $i Порядковый номер поля (при использовании множественной сортировки). Если не указан == 0
	 * @return string Строка asc или desc
	 */
	public static function getOrder($i = 0)
	{
		$order = self::getArray();
		$i = $i >= 0 && $i < count($order) ? $i : 0;
		$orders = array_values($order);
		return $orders[$i];
	}

    /**
     * Задаёт текущий контекст (инфоблок и его раздел) для вывода сортировки
     *
     * @param int $iblockId ID инфоблока
     * @param int|array $section Массив с ID раздела и символьными кодом раздела
     */
    public static function setContext($iblockId, $section)
    {
        global $USER_FIELD_MANAGER;
        if (!\CModule::IncludeModule("iblock"))
            return;

        if (!is_array($section))
            $section = array($section);

        $sectionId = \CIBlockFindTools::GetSectionID(
            $section[0],
            $section[1] ? $section[1] : '',
            array(
                "GLOBAL_ACTIVE" => "Y",
                "IBLOCK_ID" => $iblockId,
            )
        );

        $sortFields = $USER_FIELD_MANAGER->GetUserFieldValue("IBLOCK_{$iblockId}_SECTION", "UF_SORT_FIELDS", $sectionId);
        self::setFields($iblockId, $sortFields);
    }

	/**
	 * Задаёт настройки полей сортировки для раздела
	 *
	 * @param int $iblockId ID инфоблока
	 * @param array $sortFields Поля сортировки — значение пользовательского поля для раздела
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 * @throws \Bitrix\Main\Config\ConfigurationException
	 * @return array
	 */
	public static function setFields($iblockId, $sortFields = array())
	{
		$result = array();
		$iblockFields = null;

		if (!is_array($sortFields) || empty($sortFields))
		{
			// поля по умолчанию
			$iblockFields = IblockPropertyList::getPropertiesWithCustomFields($iblockId);
			$defaultFields = array("~DATE_CREATE", "cost", "common_area");
			foreach ($defaultFields as $field)
			{
				if (substr($field,0,1) == '~')
					$result[$field] = $iblockFields[substr($field,1)]["NAME"];
				else
					$result[$field] = $iblockFields[Helper::getPropertyIdByCode($iblockId, $field)]["NAME"];
			}
		}
		else
		{
			// поля, указанные для раздела
			foreach ($sortFields as $propertyId)
			{
				if ($propertyId < 0 && !isset($iblockFields))
					$iblockFields = IblockPropertyList::getPropertiesWithCustomFields($iblockId);
				switch ($propertyId)
				{
					case IblockPropertyList::NAME:
						$result["~NAME"] = $iblockFields["NAME"]["NAME"];
						break;
					case IblockPropertyList::DETAIL_PICTURE:
						$result["~DETAIL_PICTURE"] = $iblockFields["DETAIL_PICTURE"]["NAME"];
						break;
					case IblockPropertyList::DATE_CREATE:
						$result["~DATE_CREATE"] = $iblockFields["DATE_CREATE"]["NAME"];
						break;
					default:
						$propertyFields = \CIBlockProperty::GetByID($propertyId)->GetNext();
						$result[$propertyFields["CODE"]] = $propertyFields["NAME"];
				}
			}
		}

		return self::$fields = $result;
	}

	/**
	 * Выводит html-код элемента, который используется пользователями для выбора направления сортировки
	 * @param array|bool $fields Поля сортировки. Массив полей можно получить методом setFields();
	 * @return string
	 */
	public static function renderControl($fields = false)
	{
		global $APPLICATION;

        $APPLICATION->SetAdditionalCSS('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');

		$current = self::getCurrent();
		ob_start();
		?>
		<div class="sort">
			<span class="sort-case"><?=GetMessage("CITRUS_REALTY_ORDER_TITLE")?>:</span>
			<ul>
				<?
				$fields = is_array($fields) ? $fields : self::$fields;
                $currentField = self::getField(0);
                $defaultField = array_shift(array_keys($fields));
                $currentOrder = self::getOrder(0);
                $defaultOrder = array_shift(array_keys(self::$orderSet));
				foreach ($fields as $field => $params)
				{
					$isDefaultField = $field == $defaultField;
                    $isDefaultOrder = $currentOrder == $defaultOrder;
                    $isCurrentField = self::getFieldName($field) == $currentField;

                    $args = array();
                    if ($isDefaultOrder && $isCurrentField)
                        $args['order'] = 'order=' . array_pop(self::$orderSet);

                    if (!$isDefaultField)
                        $args['sort'] = "sort=" . $field;

					$url = $APPLICATION->GetCurPageParam(implode('&', $args), array("sort", "order"));
                    if ($isCurrentField)
                        $sortIcon = $isDefaultOrder ? ' <i class="fa fa-sort-desc"></i>' : ' <i class="fa fa-sort-asc"></i>';
                    else
                        $sortIcon = '';

					if (is_array($params))
					{
						?><li><a href="<?=$url?>"<?=(self::getFieldName($field) == $current[0] ? ' class="selected"' : '')?>><?=strtolower($params['title'])?><?=$sortIcon?></a></li><?
					}
					else
					{
						?><li><a href="<?=$url?>"<?=(self::getFieldName($field) == $current[0] ? ' class="selected"' : '')?>><?=strtolower($params)?><?=$sortIcon?></a></li><?
					}
				}
				?>
			</ul>
		</div>
		<?
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}