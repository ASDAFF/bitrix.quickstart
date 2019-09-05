<?
/**
 *  module
 *
 * @category       
 * @package        Iblock
 * @link           http://.ru
 * @revision    $Revision$
 * @date        $Date$
 */

namespace Site\Main\Iblock\Property;

/**
 * Тип свойства "Изображение"
 *
 * @category       
 * @package        Iblock
 */

class Image extends Prototype
{
	/**
	 * Возвращает описание типа свойства
	 *
	 * @return array
	 */
	public static function getUserTypeDescription()
	{
		return array(
			'PROPERTY_TYPE' => 'F',
			'USER_TYPE' => 'site-iblock-property-image',
			'DESCRIPTION' => 'Изображение',
			'CheckFields' => array(__CLASS__, 'checkFields'),
			'ConvertToDB' => array(__CLASS__, 'convertToDB'),
			'ConvertFromDB' => array(__CLASS__, 'convertFromDB'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'getPropertyFieldHtml'),
			'GetPropertyFieldHtmlMulty' => array(__CLASS__, 'getPropertyFieldHtmlMulty'),
			'PrepareSettings' => array(__CLASS__, 'prepareSettings'),
			'GetSettingsHTML' => array(__CLASS__, 'getSettingsHTML'),
		);
	}

	/**
	 * Валидирует значение св-ва перед cохранением
	 *
	 * @param array $property Описание типа свойства
	 * @param array $value    Значение свойства
	 *
	 * @return array Сообщения об ошибках
	 */
	public static function checkFields($property, $value)
	{
		return array();
	}

	/**
	 * Преобразует значение св-ва перед в формат, пригодный для записи в БД
	 *
	 * @param array $property Описание типа свойства
	 * @param array $value    Значение свойства
	 *
	 * @return array Преобразованное значение
	 */
	public static function convertToDB($property, $value)
	{
		$result = array();
		if (is_array($value['VALUE']) && array_key_exists('VALUE', $value['VALUE'])) {
			$result['VALUE'] = $value['VALUE']['VALUE'];
			$result['DESCRIPTION'] = $value['DESCRIPTION']['VALUE'];
		} else {
			$result['VALUE'] = $value['VALUE'];
			$result['DESCRIPTION'] = $value['DESCRIPTION'];
		}
		$return = array();

		$return['VALUE'] = (array)$result['VALUE'];

		if ($result['DESCRIPTION']) {
			$return['DESCRIPTION'] = trim($result['DESCRIPTION']);
		}

		if ($return['VALUE']['error'] == 0 && $return['VALUE']['tmp_name']) {
			if (\CFile::IsImage($return['VALUE']['name'], $return['VALUE']['type'])) {
				// Scale
				if ($property['USER_TYPE_SETTINGS']['SCALE'] == 'Y') {
					$resized = \CIBlock::ResizePicture($return['VALUE'], $property['USER_TYPE_SETTINGS']);
					if (is_array($resized)) {
						$return['VALUE'] = $resized;
					} elseif ($property['USER_TYPE_SETTINGS']['IGNORE_ERRORS'] !== 'Y') {
						$return['VALUE'] = array();

						global $APPLICATION;
						$APPLICATION->ThrowException(sprintf("Can't resize picture: %s", $resized));
					}
				}

				// Watermark
				if ($property['USER_TYPE_SETTINGS']['USE_WATERMARK_FILE'] == 'Y') {
					\CIBLock::FilterPicture($return['VALUE']['tmp_name'], array(
						'name' => 'watermark',
						'position' => $property['USER_TYPE_SETTINGS']['WATERMARK_FILE_POSITION'],
						'type' => 'file',
						'size' => 'real',
						'alpha_level' => 100 - min(max($property['USER_TYPE_SETTINGS']['WATERMARK_FILE_ALPHA'], 0), 100),
						'file' => $_SERVER['DOCUMENT_ROOT'] . Rel2Abs('/', $property['USER_TYPE_SETTINGS']['WATERMARK_FILE']),
					));
				}
			}
		}

		// Should delete?
		$del = isset($_POST['PROP_del'][$property['ID']]) ? $_POST['PROP_del'][$property['ID']] : array();
		if ($del) {
			if ($property['MULTIPLE'] == 'Y') {
				if ($value['VALUE_ID'] && isset($del[$value['VALUE_ID']])) {
					$return['VALUE']['del'] = 'Y';
				}
			} else {
				$return['VALUE']['del'] = 'Y';
			}
		}

		return $return;
	}

	/**
	 * Преобразует значение св-ва из формата БД в оперативный формат
	 *
	 * @param array $property Описание типа свойства
	 * @param array $value    Значение свойства
	 *
	 * @return array Преобразованное значение
	 */
	public static function convertFromDB($property, $value)
	{
		$return = array();

		if (strlen(trim($value['VALUE'])) > 0) {
			$return['VALUE'] = $value['VALUE'];
		}

		if (strlen(trim($value['DESCRIPTION'])) > 0) {
			$return['DESCRIPTION'] = $value['DESCRIPTION'];
		}

		return $return;
	}

	/**
	 * Возвращает HTML код для вывода поля ввода свойства
	 *
	 * @param array $property    Описание типа свойства
	 * @param array $value       Значение свойства
	 * @param array $htmlControl UI элемент
	 *
	 * @return string
	 */
	public static function getPropertyFieldHtml($property, $value, $htmlControl)
	{
		if (is_array($value['VALUE']) && array_key_exists('VALUE', $value['VALUE'])) {
			$value['VALUE'] = $value['VALUE']['VALUE'];
			$value['DESCRIPTION'] = $value['DESCRIPTION']['VALUE'];
		}

		$return = '';

		if ($htmlControl['MODE'] == 'FORM_FILL' && \Bitrix\Main\Loader::includeModule('fileman')) {
			$return .= \CFileInput::Show(
				$htmlControl['VALUE'],
				$value['VALUE'],
				array(
					'PATH' => 'Y',
					'IMAGE' => 'Y',
					'MAX_SIZE' => array(
						'W' => \COption::GetOptionString('iblock', 'detail_image_size'),
						'H' => \COption::GetOptionString('iblock', 'detail_image_size'),
					),
				),
				array(
					'upload' => true,
					'medialib' => true,
					'file_dialog' => true,
					'cloud' => true,
					'del' => true,
					'description' => $property['WITH_DESCRIPTION'] == 'Y' ? array(
						'VALUE' => $value['DESCRIPTION'],
						'NAME' => $htmlControl['DESCRIPTION'],
					) : false,
				)
			);
		} else {
			$id = preg_replace("/[^a-zA-Z0-9_]/i", 'x', htmlspecialcharsbx($htmlControl['VALUE']));

			$return .= '<input type="text" name="' . htmlspecialcharsbx($htmlControl['VALUE']) . '" id="' . $id . '" value="' . htmlspecialcharsEx($value['VALUE']) . '">';

			if ($property['WITH_DESCRIPTION'] == 'Y' && trim($htmlControl['DESCRIPTION']) != '') {
				$return .= ' <span>Описание:<input type="text" name="' . htmlspecialcharsEx($htmlControl['DESCRIPTION']) . '" value="' . htmlspecialcharsEx($value['DESCRIPTION']) . '"></span>';
			}
		}

		return $return;
	}

	/**
	 * Возвращает HTML код для вывода множественного поля ввода свойства
	 *
	 * @param array $property    Описание типа свойства
	 * @param array $values      Значения свойства
	 * @param array $htmlControl UI элемент
	 *
	 * @return string
	 */
	public static function getPropertyFieldHtmlMulty($property, $values, $htmlControl)
	{
		$return = '';
		if ($htmlControl['MODE'] == 'FORM_FILL'
			&& \Bitrix\Main\Loader::includeModule('fileman')
		) {
			$inputName = array();
			$description = array();
			foreach ($values as $valueID => $value) {
				$key = $htmlControl['VALUE'] . '[' . $valueID . ']';
				$inputName[$key . '[VALUE]'] = $value['VALUE'];
				$description[$key . '[DESCRIPTION]'] = $value['DESCRIPTION'];
				if ($value['VALUE']) {
					$return .= '<input type="hidden" name="' . htmlspecialcharsbx(self::getFieldName($key . '[VALUE]', 'VALUE_ID')) . '" value="' . htmlspecialcharsbx($valueID) . '"/>';
				}
			}

			$return .= \CFileInput::ShowMultiple(
				$inputName,
				$htmlControl['VALUE'] . '[n#IND#][VALUE]',
				array(
					'PATH' => 'Y',
					'IMAGE' => 'Y',
					'MAX_SIZE' => array(
						'W' => \COption::GetOptionString('iblock', 'detail_image_size'),
						'H' => \COption::GetOptionString('iblock', 'detail_image_size'),
					),
				),
				false,
				array(
					'upload' => true,
					'medialib' => true,
					'file_dialog' => true,
					'cloud' => true,
					'del' => true,
					'description' => $property['WITH_DESCRIPTION'] == 'Y' ? array(
						'VALUES' => $description,
						'NAME_TEMPLATE' => $htmlControl['VALUE'] . '[n#IND#][DESCRIPTION]',
					) : false,
				)
			);
		} else {
			$tableId = md5($htmlControl['VALUE']);
			$return = '<table id="tb' . $tableId . '">';
			foreach ($values as $valueId => $value) {
				$return .= '<tr><td>';

				$return .= '<input type="text" name="' . htmlspecialcharsbx($htmlControl['VALUE'] . "[$valueId][VALUE]") . '" value="' . htmlspecialcharsEx($value['VALUE']) . '">';

				if ($property['WITH_DESCRIPTION'] == 'Y' && trim($htmlControl['DESCRIPTION']) != '') {
					$return .= ' <span>Описание:<input type="text" name="' . htmlspecialcharsEx($htmlControl['DESCRIPTION'] . "[$valueId][DESCRIPTION]") . '" value="' . htmlspecialcharsEx($value['DESCRIPTION']) . '"></span>';
				}

				$return .= '</td></tr>';
			}

			$return .= '<tr><td>';
			$return .= '<input type="text" name="' . htmlspecialcharsbx($htmlControl['VALUE'] . '[n0][VALUE]') . '" value="">';
			if ($property['WITH_DESCRIPTION'] == 'Y' && trim($htmlControl['DESCRIPTION']) != '') {
				$return .= ' <span>Описание:<input type="text" name="' . htmlspecialcharsEx($htmlControl['DESCRIPTION'] . '[n0][DESCRIPTION]') . '" value=""></span>';
			}
			$return .= '</td></tr>';

			$return .= '<tr><td><input type="button" value="Добавить" onClick="addNewRow(\'tb' . $tableId . '\')"></td></tr>';
			$return .= '</table>';
		}

		return $return;
	}

	/**
	 * Подготавливает список настроек
	 *
	 * @param array $fields Список полей
	 *
	 * @return array
	 */
	public static function prepareSettings($fields)
	{
		return array(
			'SCALE' => isset($fields['USER_TYPE_SETTINGS']['SCALE']) ? $fields['USER_TYPE_SETTINGS']['SCALE'] : 'N',
			'WIDTH' => intval(isset($fields['USER_TYPE_SETTINGS']['WIDTH']) ? $fields['USER_TYPE_SETTINGS']['WIDTH'] : 0),
			'HEIGHT' => intval(isset($fields['USER_TYPE_SETTINGS']['HEIGHT']) ? $fields['USER_TYPE_SETTINGS']['HEIGHT'] : 0),
			'IGNORE_ERRORS' => isset($fields['USER_TYPE_SETTINGS']['IGNORE_ERRORS']) ? $fields['USER_TYPE_SETTINGS']['IGNORE_ERRORS'] : 'Y',
			'METHOD' => isset($fields['USER_TYPE_SETTINGS']['METHOD']) ? $fields['USER_TYPE_SETTINGS']['METHOD'] : 'Y',
			'COMPRESSION' => intval(isset($fields['USER_TYPE_SETTINGS']['COMPRESSION']) ? $fields['USER_TYPE_SETTINGS']['COMPRESSION'] : 80),
			'USE_WATERMARK_FILE' => isset($fields['USER_TYPE_SETTINGS']['USE_WATERMARK_FILE']) ? $fields['USER_TYPE_SETTINGS']['USE_WATERMARK_FILE'] : 'N',
			'WATERMARK_FILE' => isset($fields['USER_TYPE_SETTINGS']['WATERMARK_FILE']) ? $fields['USER_TYPE_SETTINGS']['WATERMARK_FILE'] : '',
			'WATERMARK_FILE_ALPHA' => intval(isset($fields['USER_TYPE_SETTINGS']['WATERMARK_FILE_ALPHA']) ? $fields['USER_TYPE_SETTINGS']['WATERMARK_FILE_ALPHA'] : 0),
			'WATERMARK_FILE_POSITION' => isset($fields['USER_TYPE_SETTINGS']['WATERMARK_FILE_POSITION']) ? $fields['USER_TYPE_SETTINGS']['WATERMARK_FILE_POSITION'] : 'mc',
		);
	}

	/**
	 * Возвращает HTML код для вывода настроек
	 *
	 * @param array $property    Описание типа свойства
	 * @param array $htmlControl UI элемент
	 * @param array $fields      Поля
	 *
	 * @return string
	 */
	public static function getSettingsHTML($property, $htmlControl, &$fields)
	{
		$fields = array(
			'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'MULTIPLE_CNT', 'DEFAULT_VALUE', 'FILE_TYPE'),
		);

		$settings = self::prepareSettings($property);

		return '<tr>
			<td>Уменьшать, если большое</td>
			<td>' . InputType(
			'checkbox',
			$htmlControl['NAME'] . '[SCALE]',
			'Y',
			$settings['SCALE']
		) . '</td>
			</tr>
			<tr>
			<td>Максимальная ширина</td>
			<td><input type="text" name="' . $htmlControl['NAME'] . '[WIDTH]" value="' . $settings['WIDTH'] . '"></td>
			</tr>
			<tr>
			<td>Максимальная высота</td>
			<td><input type="text" name="' . $htmlControl['NAME'] . '[HEIGHT]" value="' . $settings['HEIGHT'] . '"></td>
			</tr>
			<tr>
			<td>Игнорировать ошибки масштабирования</td>
			<td>' . InputType(
			'checkbox',
			$htmlControl['NAME'] . '[IGNORE_ERRORS]',
			'Y',
			$settings['IGNORE_ERRORS']
		) . '</td>
			</tr>
			<tr>
			<td>Сохранять качество при масштабировании</td>
			<td>' . InputType(
			'checkbox',
			$htmlControl['NAME'] . '[METHOD]',
			'Y',
			$settings['METHOD']
		) . '</td>
			</tr>
			<tr>
			<td>Качество (только для JPEG, 1-100)</td>
			<td><input type="text" name="' . $htmlControl['NAME'] . '[COMPRESSION]" value="' . $settings['COMPRESSION'] . '"></td>
			</tr>
			<tr>
			<td>Наносить авторский знак в виде изображения</td>
			<td>' . InputType(
			'checkbox',
			$htmlControl['NAME'] . '[USE_WATERMARK_FILE]',
			'Y',
			$settings['USE_WATERMARK_FILE']
		) . '</td>
			</tr>
			<tr>
			<td>Путь к изображению с авторским знаком</td>
			<td><input type="text" name="' . $htmlControl['NAME'] . '[WATERMARK_FILE]" value="' . htmlspecialcharsbx($settings['WATERMARK_FILE']) . '"></td>
			</tr>
			<tr>
			<td>Прозрачность авторского знака (%)</td>
			<td><input type="text" name="' . $htmlControl['NAME'] . '[WATERMARK_FILE_ALPHA]" value="' . $settings['WATERMARK_FILE_ALPHA'] . '"></td>
			</tr>
			<tr>
			<td>Размещение авторского знака</td>
			<td>' . SelectBoxFromArray(
			$htmlControl['NAME'] . '[WATERMARK_FILE_POSITION]',
			array(
				'reference_id' => array(
					'tl',
					'tc',
					'tr',
					'ml',
					'mc',
					'mr',
					'bl',
					'bc',
					'br',
				),
				'reference' => array(
					'Сверху слева',
					'Сверху по центру',
					'Сверху справа',
					'Слева',
					'По центру',
					'Справа',
					'Снизу слева',
					'Снизу по центру',
					'Снизу справа',
				),
			),
			$settings['WATERMARK_FILE_POSITION']
		) . '</td>
			</tr>';
	}

	/**
	 * Возвращает имя поля для хранения текущего значения
	 *
	 * @param string $valueFieldName Имя поля
	 * @param string $type           Тип поля
	 *
	 * @return string
	 */
	protected static function getFieldName($valueFieldName, $type)
	{
		return str_replace('[VALUE]', '[' . $type . ']', $valueFieldName);
	}
}