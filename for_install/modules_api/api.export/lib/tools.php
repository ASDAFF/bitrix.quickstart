<?php

namespace Api\Export;

use \Bitrix\Main;
use \Bitrix\Currency;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Loader::includeModule('iblock');

class Tools
{
	public static $useCatalog;

	public static $iblockInfo;
	public static $useOffers;

	public static function isCatalog()
	{
		if(!isset($useCatalog))
			self::$useCatalog = Loader::includeModule('catalog');

		return self::$useCatalog;
	}

	public static function getIblockInfo($iblockId)
	{
		if(!isset(self::$iblockInfo) && self::$useCatalog) {
			$catalog = \CCatalogSku::GetInfoByIBlock($iblockId);
			if(!empty($catalog) && is_array($catalog)) {
				self::$iblockInfo = $catalog;

				self::$useOffers = ($catalog['CATALOG_TYPE'] == \CCatalogSku::TYPE_FULL || $catalog['CATALOG_TYPE'] == \CCatalogSku::TYPE_PRODUCT);
			}
		}
	}

	public static function construct()
	{
		self::isCatalog();
	}

	///////////////////////////////////////////////////////////////////////////
	/// Методы для работы с профилями в админке
	///////////////////////////////////////////////////////////////////////////

	//Выводит подсказки к полям в форме настроек профиля
	public static function showHint($id, $hint = '', $type = 'info', $params = array())
	{
		if(!$id)
			return false;

		if(!$hint)
			$hint = Loc::getMessage($id . '_HINT');

		if(!$hint)
			return false;

		$strParams = ", show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 350, hint: '" . \CUtil::JSEscape($hint) . "'";

		if($params) {
			$strParams      = '';
			$params['hint'] = (strlen($params['hint']) > 0 ? $params['hint'] : $hint);

			foreach($params as $key => $val) {
				$newVal    = (is_int($val) ? (int)$val : '"' . \CUtil::JSEscape($val) . '"');
				$strParams .= ', ' . $key . ': ' . $newVal;
			}
		}

		$html = '
				<i class="api-icon-' . $type . '" id="' . $id . '"></i>
				<script type="text/javascript">
					new top.BX.CHint({parent: top.BX("' . $id . '") ' . $strParams . '});
				</script>
		';

		return $html;
	}

	//Ссылка на пользователя для списка профилей в админке
	public static function getFormatedUserName($userId, $bEnableId = true, $createEditLink = true)
	{
		static $formattedUsersName = array();
		static $siteNameFormat = '';

		$result   = (!is_array($userId)) ? '' : array();
		$newUsers = array();

		if(is_array($userId)) {
			foreach($userId as $id) {
				if(!isset($formattedUsersName[ $id ]))
					$newUsers[] = $id;
			}
		}
		else if(!isset($formattedUsersName[ $userId ])) {
			$newUsers[] = $userId;
		}

		if(count($newUsers) > 0) {
			$resUsers = \Bitrix\Main\UserTable::getList(
				 array(
						'select' => array('ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'LOGIN', 'EMAIL'),
						'filter' => array('ID' => $newUsers),
				 )
			);
			while($arUser = $resUsers->fetch()) {
				if(strlen($siteNameFormat) == 0)
					$siteNameFormat = \CSite::GetNameFormat(false);
				$formattedUsersName[ $arUser['ID'] ] = \CUser::FormatName($siteNameFormat, $arUser, true, true);
			}
		}

		if(is_array($userId)) {
			foreach($userId as $uId) {
				$formatted = '';
				if($bEnableId)
					$formatted = '[<a href="/bitrix/admin/user_edit.php?ID=' . $uId . '&lang=' . LANGUAGE_ID . '">' . $uId . '</a>] ';

				if(class_exists('CBXFeatures') && \CBXFeatures::IsFeatureEnabled('SaleAccounts') && !$createEditLink)
					$formatted .= '<a href="/bitrix/admin/sale_buyers_profile.php?USER_ID=' . $uId . '&lang=' . LANGUAGE_ID . '">';
				else
					$formatted .= '<a href="/bitrix/admin/user_edit.php?ID=' . $uId . '&lang=' . LANGUAGE_ID . '">';
				$formatted .= $formattedUsersName[ $uId ];

				$formatted .= '</a>';

				$result[ $uId ] = $formatted;
			}
		}
		else {
			if($bEnableId)
				$result .= '[<a href="/bitrix/admin/user_edit.php?ID=' . $userId . '&lang=' . LANGUAGE_ID . '">' . $userId . '</a>] ';

			if(class_exists('CBXFeatures') && \CBXFeatures::IsFeatureEnabled('SaleAccounts') && !$createEditLink)
				$result .= '<a href="/bitrix/admin/sale_buyers_profile.php?USER_ID=' . $userId . '&lang=' . LANGUAGE_ID . '">';
			else
				$result .= '<a href="/bitrix/admin/user_edit.php?ID=' . $userId . '&lang=' . LANGUAGE_ID . '">';

			$result .= $formattedUsersName[ $userId ];

			$result .= '</a>';
		}

		return $result;
	}

	//Все типы описаний с группировкой
	public static function getOfferTypes()
	{
		$arOfferTypes = array();

		$arExportTypes = array();
		$dir           = (__DIR__ . '/type');
		$arFiles       = scandir($dir);
		foreach($arFiles as $file) {
			if($file != '.' && $file != '..')
				require($dir . '/' . $file);
		}

		foreach($arExportTypes as $arType) {
			$arOfferTypes[ $arType['GROUP'] ][] = array(
				 'CODE' => $arType['CODE'],
				 'NAME' => $arType['NAME'],
			);
		}

		return $arOfferTypes;
	}

	//Один тип описания
	public static function getOfferType($type)
	{
		$arExportTypes = array();
		$dir           = (__DIR__ . '/type');

		$arFiles = scandir($dir);
		foreach($arFiles as $file) {
			if($file != '.' && $file != '..')
				require($dir . '/' . $file);
		}

		return $arExportTypes[ $type ];
	}

	//Валюты
	public static function getCurrency()
	{
		if(Loader::includeModule('currency')) {
			$currencies = Currency\CurrencyManager::getCurrencyList();
		}
		else {
			$currencies = Loc::getMessage('AELT_CURRENCIES');
		}

		return (array)$currencies;
	}

	//Курсы валют
	public static function getCurrencyRates()
	{
		return Loc::getMessage('AELT_CURRENCY_RATES');
	}

	//Типы цен
	public static function getPriceTypes()
	{
		$types = (array)Loc::getMessage('AELT_OPTIMAL_RICE');

		if(Loader::includeModule('catalog')) {
			$res = \CCatalogGroup::GetList(array('SORT' => 'ASC'), array(), false, false, array('ID', 'NAME', 'NAME_LANG'));
			while($type = $res->Fetch())
				$types[ $type['ID'] ] = $type['NAME_LANG'];
		}

		return $types;
	}

	//Инфоблоки или каталоги
	public static function getCatalogs($bUseCatalog = false)
	{
		$arAll = $arIblock = array();

		$bCatalog = Loader::includeModule('catalog');
		if(!$bCatalog)
			$bUseCatalog = false;


		$res = \CIBlock::GetList(array("NAME" => "ASC"));
		while($ar_res = $res->Fetch()) {
			if($bUseCatalog)
				if(!\CCatalog::GetByID($ar_res["ID"]))
					continue;

			$arIblock[] = $ar_res;
		}

		foreach($arIblock as $iBlock) {
			$arIbType = \CIBlockType::GetByIDLang($iBlock['IBLOCK_TYPE_ID'], LANG);

			$arAll[ $arIbType['ID'] ]['ID']                      = $arIbType['ID'];
			$arAll[ $arIbType['ID'] ]['NAME']                    = $arIbType['NAME'] . ' [' . $arIbType['ID'] . ']';
			$arAll[ $arIbType['ID'] ]['IBLOCK'][ $iBlock['ID'] ] = $iBlock['NAME'] . ' [' . $iBlock['ID'] . ']';;
		}

		return $arAll;
	}

	//Список или дерево разделов
	public static function getCatalogSections($iblockId, $getAll = false)
	{
		if(!$iblockId)
			return false;

		$arSections = array();

		$arSort   = array('left_margin' => 'asc');
		$arSelect = array('ID', 'DEPTH_LEVEL', 'NAME');
		$arFilter = array(
			 'IBLOCK_ID' => $iblockId,
			 'ACTIVE'    => 'Y',
		);

		if(!$getAll)
			$arFilter['DEPTH_LEVEL'] = 1;

		$rsSection = \CIBlockSection::GetList($arSort, $arFilter, false, $arSelect);

		while($arSection = $rsSection->Fetch()) {
			if($arFilter['DEPTH_LEVEL'] == 1)
				$arSections[ $arSection['ID'] ] = $arSection['NAME'];
			else
				$arSections[ $arSection['ID'] ] = str_repeat(' . ', $arSection['DEPTH_LEVEL']) . $arSection['NAME'];
		}
		return $arSections;
	}

	//Сайты
	public static function getSites()
	{
		$arSites = array();

		$res = \CSite::GetList($by = "sort", $order = "desc");
		while($ar_res = $res->Fetch()) {
			$arSites[ $ar_res['ID'] ] = $ar_res;
		}

		return $arSites;
	}

	//http-путь до файла экспорта
	public static function getHttpFilePath($path)
	{
		if($path)
			$path = (\CMain::IsHTTPS() ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $path;
		else
			$path = Loc::getMessage('AEAE_DEFAULT_FILE_PATH');

		return $path;
	}

	//Селектор полей элемента
	public static function getOfferFieldsSelect($iblockId)
	{
		static::construct();
		static::getIblockInfo($iblockId);

		static $iblockProps;
		static $offerProps;

		//Поля элемента
		$arFields['FIELDS'] = Loc::getMessage('AYI_OFFER_FIELDS_LANG');

		//Поля ТП
		$arFields['OFFER_FIELD'] = Loc::getMessage('AYI_OFFER_FIELDS_LANG');


		//Свойства элемента
		if(!isset($iblockProps)){
			$res = \CIBlockProperty::GetList(array('NAME' => 'ASC'), array('IBLOCK_ID' => $iblockId, 'ACTIVE' => "Y"));
			while($arProp = $res->Fetch()) {
				$iblockProps[ $arProp['ID'] ] = $arProp;
			}
		}
		if($iblockProps){
			$arFields['PROPERTY'] = $iblockProps;
		}


		//Свойства TП
		if(!isset($offerProps) && self::$useOffers){
			if($offerIblockId = self::$iblockInfo['IBLOCK_ID']){
				$res = \CIBlockProperty::GetList(array('NAME' => 'ASC'), array('IBLOCK_ID' => $offerIblockId, 'ACTIVE' => "Y"));
				while($arProp = $res->Fetch()) {
					$offerProps[ $arProp['ID'] ] = $arProp;
				}
			}
		}
		if($offerProps){
			$arFields['OFFER_PROPERTY'] = $offerProps;
		}

		//Поля товара
		$arFields["PRODUCT"] = Loc::getMessage('AYI_CATALOG_FIELDS_LANG');

		//Цена
		$arFields['PRICE'] = Loc::getMessage('AYI_PRICE_FIELDS_LANG');

		//Валюта
		$arFields['CURRENCY'] = static::getCurrency();

		//Мета-теги
		$arFields['IPROPERTY'] = Loc::getMessage('AYI_IPROPERTY_FIELDS_LANG');

		return $arFields;
	}

	public static function showOfferFieldsSelect($arIBlockId = array(), $type = 'FIELD', $value = '')
	{
		$arOptions   = array();
		$arOptions[] = '<option value="">' . Loc::getMessage('AYI_SELECT_OPTION_EMPTY') . '</option>';

		$arIBlock = self::getOfferFieldsSelect($arIBlockId);

		//Поля элемента
		if($type == 'FIELD' && $arIBlock['FIELDS']) {
			foreach($arIBlock["FIELDS"] as $id => $name) {
				$selected    = (is_array($value) && in_array($id, $value) || $id == $value) ? ' selected' : '';
				$arOptions[] = "<option value=\"$id\"$selected>[{$id}] {$name}</option>";
			}

			unset($id,$name,$selected);
		}

		//Поля ТП
		if($type == 'OFFER_FIELD' && $arIBlock['OFFER_FIELD']) {
			foreach($arIBlock["OFFER_FIELD"] as $id => $name) {
				$selected    = (is_array($value) && in_array($id, $value) || $id == $value) ? ' selected' : '';
				$arOptions[] = "<option value=\"$id\"$selected>[{$id}] {$name}</option>";
			}
			unset($id,$name,$selected);
		}

		//Свойства элемента
		if($type == 'PROPERTY' && $arIBlock['PROPERTY']) {
			foreach($arIBlock["PROPERTY"] as $id => $fields) {
				$selected    = ((is_array($value) && in_array($fields['CODE'], $value)) || $fields['CODE'] == $value ? ' selected' : '');
				$arOptions[] = "<option value=\"{$fields["CODE"]}\"$selected>[{$id}] {$fields["NAME"]}</option>";
			}

			unset($id,$name,$selected);
		}

		//Свойства ТП
		if($type == 'OFFER_PROPERTY' && $arIBlock['OFFER_PROPERTY']) {
			foreach($arIBlock["OFFER_PROPERTY"] as $id => $fields) {
				$selected    = ((is_array($value) && in_array($fields['CODE'], $value)) || $fields['CODE'] == $value ? ' selected' : '');
				$arOptions[] = "<option value=\"{$fields["CODE"]}\"$selected>[{$id}] {$fields["NAME"]}</option>";
			}

			unset($id,$name,$selected);
		}

		//Поля товара
		if($type == 'PRODUCT' && $arIBlock['PRODUCT']) {
			foreach($arIBlock["PRODUCT"] as $id => $name) {
				$selected    = (is_array($value) && in_array($id, $value) || $id == $value) ? ' selected' : '';
				$arOptions[] = "<option value=\"$id\"$selected>[{$id}] {$name}</option>";
			}
			unset($id,$name,$selected);
		}

		//Цена
		if($type == 'PRICE' && $arIBlock['PRICE']) {
			foreach($arIBlock["PRICE"] as $id => $name) {
				$selected    = (is_array($value) && in_array($id, $value) || $id == $value) ? ' selected' : '';
				$arOptions[] = "<option value=\"$id\"$selected>[{$id}] {$name}</option>";
			}
			unset($id,$name,$selected);
		}

		//Валюта
		if($type == 'CURRENCY' && $arIBlock['CURRENCY']) {
			foreach($arIBlock["CURRENCY"] as $id => $name) {
				$selected    = (is_array($value) && in_array($id, $value) || $id == $value) ? ' selected' : '';
				$arOptions[] = "<option value=\"$id\"$selected>[{$id}] {$name}</option>";
			}
			unset($id,$name,$selected);
		}

		//Мета-теги
		if($type == 'IPROPERTY' && $arIBlock['IPROPERTY']) {
			foreach($arIBlock["IPROPERTY"] as $group) {
				$arOptions[] = '<optgroup label="'. $group['NAME'] .'">';
				foreach($group["VALUES"] as $id => $name) {
					$selected    = (is_array($value) && in_array($id, $value) || $id == $value) ? ' selected' : '';
					$arOptions[] = "<option value=\"$id\"$selected>[{$id}] {$name}</option>";
				}
				$arOptions[] = '</optgroup>';
			}
			unset($id,$name,$selected);
		}


		$strOptions = implode("\n", $arOptions);

		return $strOptions;
	}

	/** Список "Тип поля" */
	public static function showFieldTypeSelect($iblockId, $typeId, $typeVal)
	{
		static::construct();
		static::getIblockInfo($iblockId);

		$options = array();

		$arType = (array)Loc::getMessage('AELT_FIELD_TYPE_SELECT');

		if(!self::$useOffers) {
			unset($arType['OFFER_FIELD'], $arType['OFFER_PROPERTY']);
		}

		if(!self::$useCatalog) {
			unset($arType['PRODUCT'], $arType['PRICE']);
		}

		foreach($arType as $key => $value) {
			$selected = ($key == $typeId ? 'selected' : '');

			$options[] = "<option value=\"{$key}\" {$selected}>{$value}</option>";
		}

		return implode("\n", $options);
	}


	//Настройки профиля по умолчанию
	public static function getProfileDefaults()
	{
		$defSite = array();
		$defType = 'ym_simple';

		$arSites = self::getSites();
		foreach($arSites as $arSite) {
			if($arSite['DEF'] == 'Y')
				$defSite = $arSite;
		}

		$defSite['SITE_NAME'] = ($defSite['SITE_NAME'] ? $defSite['SITE_NAME'] : $defSite['NAME']);

		$arType = self::getOfferType($defType);


		return array(
			//tab1
			'ACTIVE'          => 'N',
			'NAME'            => $defSite['SITE_NAME'],
			'SITE_ID'         => $defSite['ID'],
			'SORT'            => 500,
			'STEP_LIMIT'      => 500,

			//tab2
			'SHOP_NAME'       => $defSite['SITE_NAME'],
			'SHOP_COMPANY'    => $defSite['SITE_NAME'],
			'SHOP_URL'        => (\CMain::IsHTTPS() ? 'https://' : 'http://') . $defSite['SERVER_NAME'],
			'DELIVERY'        => unserialize('a:3:{s:4:"cost";a:1:{i:0;s:3:"300";}s:4:"days";a:1:{i:0;s:1:"1";}s:12:"order_before";a:1:{i:0;s:2:"17";}}'),
			'STOP_WORDS'      => Loc::getMessage('AELT_STOP_WORDS'),
			'PRICE_TYPE'      => 1,

			//tab3
			'USE_CATALOG'     => 'Y',
			'USE_SUBSECTIONS' => 'N',

			//tab4
			'ELEMENTS_FILTER' => unserialize('a:1:{s:6:"ACTIVE";s:1:"Y";}'),
			'OFFERS_FILTER'   => unserialize('a:1:{s:6:"ACTIVE";s:1:"Y";}'),

			//tab5
			'TYPE'            => $defType,
			'FIELDS'          => $arType['FIELDS'],
		);
	}

}