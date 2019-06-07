<?
namespace Api\FormDesigner\Property;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

//bitrix/modules/iblock/classes/general/prop_
class PSList
{
	public static function GetUserTypeDescription()
	{
		return array(
			'PROPERTY_TYPE'        => 'S',
			'USER_TYPE'            => 'APIFD_PSList',
			'DESCRIPTION'          => Loc::getMessage('AFD_LP_PSLIST_DESCRIPTION'),
			'PrepareSettings'      => array(__CLASS__, 'PrepareSettings'),
			'GetSettingsHTML'      => array(__CLASS__, 'GetSettingsHTML'),
			'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			//'GetPropertyFieldHtmlMulty' => array(__CLASS__, 'GetPropertyFieldHtmlMulty'),
			//'GetPublicViewHTML'    => array(__CLASS__, 'GetPublicViewHTML'),
			//'GetPublicEditHTML'    => array(__CLASS__, 'GetPublicEditHTML'),
			//'ConvertToDB'          => array(__CLASS__, 'ConvertToDB'),
			//'ConvertFromDB'        => array(__CLASS__, 'ConvertFromDB'),
		);
	}


	public static function getPaySystems($parameters = array())
	{
		static $result = array();

		if(!$parameters['order'])
			$parameters['order'] = array('SORT' => 'ASC','NAME' => 'ASC');

		if(!$parameters['select'])
			$parameters['select'] = array('ID','NAME', 'LOGOTIP', 'ACTION_FILE','DESCRIPTION','CODE');

		if(!$parameters['filter'])
			$parameters['filter'] = array('ACTIVE' => 'Y');

		if(!$result)
		{
			if(Loader::includeModule('sale'))
			{
				$rsPaySystem = \Bitrix\Sale\Internals\PaySystemActionTable::getList($parameters);
				while($arPaySystem = $rsPaySystem->fetch())
				{
					if($arPaySystem['LOGOTIP'])
						$arPaySystem['LOGOTIP'] = \CFile::GetFileArray($arPaySystem['LOGOTIP']);

					$result[$arPaySystem['ID']] = $arPaySystem;
				}
			}
		}

		return $result;
	}


	public static function PrepareSettings($arFields)
	{
		$arSettings = $arFields['USER_TYPE_SETTINGS'];

		if(!$arSettings['PAYSYSTEM'][0])
			$arSettings['PAYSYSTEM'] = array();

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
		$arPaySystems = self::getPaySystems();

		$count = count($arPaySystems);
		$size = ($count>20?20:$count + 1);

		$arPropertyFields = array(
			'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'SEARCHABLE', 'MULTIPLE_CNT', 'SMART_FILTER','DEFAULT_VALUE')
		);

		ob_start();
		?>
		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AFD_LP_PSLIST_PAYSYSTEM')?>:</td>
			<td class="adm-detail-content-cell-r">
				<select name="<?=$strHTMLControlName['NAME']?>[PAYSYSTEM][]" size="<?=$size?>" multiple="multiple">
					<option value=""<?=(!$arSettings['PAYSYSTEM'] ? ' selected': '')?>><?=Loc::getMessage('AFD_LP_PSLIST_OPTION_VALUE_ALL')?></option>
					<?foreach($arPaySystems as $system):?>
						<?
						$selected = ($arSettings['PAYSYSTEM'] && in_array($system['ID'],$arSettings['PAYSYSTEM']) ? ' selected' : '');
						?>
						<option value="<?=$system['ID']?>"<?=$selected?>>[<?=$system['ID']?>] <?=$system['NAME']?></option>
					<?endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AFD_LP_PSLIST_VIEW')?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				echo SelectBoxFromArray(
					$strHTMLControlName['NAME'].'[VIEW]',
					array(
						'REFERENCE' => array(
							Loc::getMessage('AFD_LP_PSLIST_VIEW_L'),
							Loc::getMessage('AFD_LP_PSLIST_VIEW_R'),
						),
						'REFERENCE_ID' => array(
							'L','R'
						)
					),
					$arSettings['VIEW']
				);
				?>
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
		$arPSList = self::getPaySystems();
		$count    = count($arPSList);
		$size     = ($arProperty['MULTIPLE'] == 'Y' ? ($count>20?20:$count) : 1);

		ob_start();
		?>
		<select name="<?=$strHTMLControlName['VALUE']?>" size="<?=$size?>">
			<?if($arProperty['IS_REQUIRED'] != 'Y'):?>
				<option value=""><?=Loc::getMessage('AFD_LP_ESLIST_OPTION_NO_VALUE')?></option>
			<?endif?>
			<? foreach($arPSList as $section): ?>
				<option value="<?=$section['ID'];?>"<? if(($section['ID'] == $value['VALUE'])): ?> selected=""<? endif; ?>><?=$section['NAME'];?></option>
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


	public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		if($value['VALUE'])
		{
			if($arPSList = self::getPaySystems())
			{
				$strValue = '';
				foreach($arPSList as $arPS)
				{
					if($arPS['ID'] == $value['VALUE'])
						$strValue .= $arPS['NAME'] . ' [<a href="/bitrix/admin/sale_pay_system_edit.php?ID='. $arPS['ID'] .'&lang=ru">'. $arPS['ID'] .'</a>]'."\n";
				}

				$value['VALUE'] = $strValue;
			}
		}

		return $value['VALUE'];
	}
}