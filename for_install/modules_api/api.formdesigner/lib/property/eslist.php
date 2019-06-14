<?
namespace Api\FormDesigner\Property;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

//bitrix/modules/iblock/classes/general/prop_
class ESList
{
	public static function GetUserTypeDescription()
	{
		return array(
			'PROPERTY_TYPE'        => 'E',
			'USER_TYPE'            => 'APIFD_ESList',
			'DESCRIPTION'          => Loc::getMessage('AFD_LP_ESLIST_DESCRIPTION'),
			'PrepareSettings'      => array(__CLASS__, 'PrepareSettings'),
			'GetSettingsHTML'      => array(__CLASS__, 'GetSettingsHTML'),
			//'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
			'GetPropertyFieldHtml'   => array(__CLASS__, 'GetPropertyFieldHtml'),
			//'GetPropertyFieldHtmlMulty' => array(__CLASS__, 'GetPropertyFieldHtmlMulty'),
			//'GetPublicViewHTML'    => array(__CLASS__, 'GetPublicViewHTML'),
			//'GetPublicEditHTML'    => array(__CLASS__, 'GetPublicEditHTML'),
			//'ConvertToDB'          => array(__CLASS__, 'ConvertToDB'),
			//'ConvertFromDB'        => array(__CLASS__, 'ConvertFromDB'),
		);
	}

	public static function getElements($iblockId, $arSelect = array(), $arSettings = array())
	{
		static $result = array();

		if($iblockId && !$result)
		{
			$arESList = $arElements = array();

			$parameters = array(
				'order'  => array('SORT' => 'ASC', 'NAME' => 'ASC'),
				'filter' => array("=IBLOCK_ID" => $iblockId, 'ACTIVE' => 'Y'),
				'select' => array('ID', 'NAME', 'IBLOCK_SECTION_ID'), //,'PREVIEW_PICTURE','DETAIL_PICTURE'
			);

			if($arSelect)
				$parameters['select'] = array_merge($parameters['select'],$arSelect);


			$rsElement = \Bitrix\Iblock\ElementTable::getList($parameters);
			while($arElement = $rsElement->fetch())
			{
				//Prepare fields
				$arElement['PICTURE'] = array();

				if($arElement['PREVIEW_PICTURE'])
					$arElement['PICTURE'] = \CFile::GetFileArray($arElement['PREVIEW_PICTURE']);
				elseif($arElement['DETAIL_PICTURE'])
					$arElement['PICTURE'] = \CFile::GetFileArray($arElement['DETAIL_PICTURE']);


				if($arSettings['PICTURE_WIDTH'])
				{
					$arFileTmp = \CFile::ResizeImageGet($arElement['PICTURE'],array(
						'width' => $arSettings['PICTURE_WIDTH'],
						'height' => $arSettings['PICTURE_WIDTH']*2,
					),false, true);

					if($arFileTmp['src'])
						$arFileTmp['src'] = \CUtil::GetAdditionalFileURL($arFileTmp['src'], true);

					$arElement['PICTURE'] = array_change_key_case($arFileTmp, CASE_UPPER);
				}


				//Prepare elements
				if($arElement['IBLOCK_SECTION_ID'])
					$arESList[ $arElement['IBLOCK_SECTION_ID'] ][ $arElement['ID'] ] = $arElement;
				else
					$arElements[ $arElement['ID'] ] = $arElement;
			}

			$rsSection = \Bitrix\Iblock\SectionTable::getList(array(
				'order'  => array('SORT' => 'ASC', 'NAME' => 'ASC'),
				'filter' => array("=IBLOCK_ID" => $iblockId, 'ACTIVE' => 'Y'),
				'select' => array('ID', 'NAME'),
			));
			while($arSection = $rsSection->fetch())
			{
				if($arSectionItems = $arESList[ $arSection['ID'] ])
				{
					$arSection['ITEMS']         = $arSectionItems;
					$result[ $arSection['ID'] ] = $arSection;
				}
			}

			if(!$arESList)
				$result = $arElements;
		}

		return $result;
	}

	public static function PrepareSettings($arFields)
	{
		$arSettings = $arFields['USER_TYPE_SETTINGS'];

		if(!$arSettings['SHOW_PICTURE'][0])
			$arSettings['SHOW_PICTURE'] = array();

		if(!isset($arSettings['PICTURE_WIDTH']))
			$arSettings['PICTURE_WIDTH'] = 240;
		else
			$arSettings['PICTURE_WIDTH'] = intval($arSettings['PICTURE_WIDTH']);

		return $arSettings;
	}

	/**
	 * Настройки свойства для формы редактирования инфоблока
	 *
	 * @param $arProperty
	 * @param $strHTMLControlName
	 * @param $arPropertyFields
	 *
	 * @return string
	 */
	public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
	{
		$arSettings   = self::PrepareSettings($arProperty);
		$arPropertyFields = array(
			'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'SEARCHABLE', 'MULTIPLE_CNT', 'SMART_FILTER')
		);

		ob_start();
		?>
		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AFD_LP_ESLIST_SHOW_PICTURE')?>:</td>
			<td class="adm-detail-content-cell-r">
				<?echo SelectBoxMFromArray(
					$strHTMLControlName['NAME'].'[SHOW_PICTURE][]',
					array(
						'REFERENCE' => array(
							Loc::getMessage('AFD_LP_ESLIST_OPTION_NO_VALUE'),
							Loc::getMessage('AFD_LP_ESLIST_PREVIEW_PICTURE'),
							Loc::getMessage('AFD_LP_ESLIST_DETAIL_PICTURE'),
						),
						'REFERENCE_ID' => array(
							'',
							'PREVIEW_PICTURE',
							'DETAIL_PICTURE'
						)
					),
					$arSettings['SHOW_PICTURE'],
					'',
					false,
					3
				);?>
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AFD_LP_ESLIST_PICTURE_WIDTH')?>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="text"
				       size="28"
				       name="<?=$strHTMLControlName['NAME'] . '[PICTURE_WIDTH]';?>"
				       value="<?=$arSettings['PICTURE_WIDTH']?>">
			</td>
		</tr>
		<?
		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	 * Выводит значения свойства в административной части в форме редактирования элемента
	 *
	 * @param $arProperty
	 * @param $value
	 * @param $strHTMLControlName
	 *
	 * @return string
	 */
	public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		$arESList = self::getElements($arProperty['LINK_IBLOCK_ID']);
		$count = count($arESList);
		$size = ($arProperty['MULTIPLE'] == 'Y' ? ($count>20?20:$count) : 1);

		ob_start();
		?>
		<select name="<?=$strHTMLControlName['VALUE']?>" size="<?=$size?>">
			<?if($arProperty['IS_REQUIRED'] != 'Y'):?>
				<option value=""><?=Loc::getMessage('AFD_LP_ESLIST_OPTION_NO_VALUE')?></option>
			<?endif?>
			<? foreach($arESList as $k => $section): ?>
				<?if($section['ITEMS']):?>
					<optgroup label="<?=$section['NAME']?>">
						<?foreach($section['ITEMS'] as $item):?>
							<option value="<?=$item['ID'];?>"<? if(($item['ID'] == $value['VALUE'])): ?> selected=""<? endif; ?>><?=$item['NAME'];?></option>
						<? endforeach; ?>
					</optgroup>
				<?else:?>
					<option value="<?=$section['ID'];?>"<? if(($section['ID'] == $value['VALUE'])): ?> selected=""<? endif; ?>><?=$section['NAME'];?></option>
				<?endif?>
			<? endforeach; ?>
		</select>
		<?if($arProperty['WITH_DESCRIPTION'] == 'Y'):?>
			&nbsp;<input type="text" size="60" name="<?=$strHTMLControlName['DESCRIPTION']?>" value="<?=htmlspecialcharsbx($value["DESCRIPTION"])?>">
		<?endif?>
		<?
		$return = ob_get_contents();
		ob_end_clean();

		return  $return;
	}
}