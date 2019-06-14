<?
/**
 * @author DEFA
 * @package copy
 */

IncludeModuleLangFile(__FILE__);

/**
 * Вспомогательный класс для копирования инфоблоков.
 *
 * Копирование инфоблоков производится либо с контентом, либо без него:
 *
 * <pre>
 *
 *  // Вариант 1. Копирование инфоблока 2 и его содержимого в тип news
 *  DefaToolsCopy::iblock(2)->WithContent()->ToType('news');
 *
 * </pre>
 *
 * <pre>
 *
 *  // Вариант 2. Копирование инфоблока 2 в тип news без содержимого
 *  DefaToolsCopy::iblock(2)->ToType('news');
 *
 * </pre>
 *
 * Использование других методов класса не реккомендуется, т.к. они могут быть изменены/удалены.
 * Обратная связь приветствуется. Форма находится на сайте http://smposter.idefa.ru/
 *
 * меню/диалоги в файле /interface/get_menu.php
 * обработка кастомных действий в файле /controller.php
 *
 * Class DefaToolsCopy
 */
class DefaToolsCopy
{
	private $iblock_id = null;
	private $section_ids = null;
	private $with_content = false;
	private $check_uniqueness = false;
	private $type = null;

	private function __construct()
	{
		if (!CModule::IncludeModule("iblock")) {
			throw new DefaToolsException(GetMessage("CAT_ERROR_IBLOCK_NOT_INSTALLED"));
		}
	}

	/**
	 * Возвращает инстанс класса DefaToolsCopy для дальнейших операций с ним.
	 *
	 * @param $iblock_id
	 * @return DefaToolsCopy
	 */
	public static function iblock($iblock_id)
	{
		if (!intval($iblock_id)) return false;

		$instance = new self($iblock_id);
		$instance->SetIblockId($iblock_id);

		return $instance;
	}

	/**
	 * Копирует разделы одного инфоблока в новый.
	 *
	 * ID инфоблока определяется через первый попавшийся раздел,
	 * поэтому разделы должны пренадлежать одному и тому же инфоблоку.
	 *
	 * @param array $section_ids
	 * @throws Exception
	 * @return DefaToolsCopy
	 */
	public static function sections(array $section_ids)
	{
		if (!is_array($section_ids)) throw new DefaToolsException('Section ids should be an array');
		if (!count($section_ids)) throw new DefaToolsException('Section ids array could not be empty');
		$section_ids = array_filter($section_ids, function ($val) {
			return intval($val);
		});

		if (!count($section_ids)) throw new DefaToolsException('Section ids elements should be only integers.');

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$res = CIBlockSection::GetById(current($section_ids))->Fetch();
		$iblock_id = $res['IBLOCK_ID'];

		$instance = new self();
		$instance->SetIblockId($iblock_id);
		$instance->SetSectionIds($section_ids);

		return $instance;
	}

	/**
	 * Добавляет копирование контента в цепочку.
	 *
	 * <pre>
	 *
	 *  // Скопируем инфоблок 2 и его содержание в тип news
	 *  DefaToolsCopy::iblock(2)->WithContent()->ToType('news');
	 *
	 * </pre>
	 *
	 * @param $really boolean
	 * @return $this
	 */
	public function WithContent($really = true)
	{
		if ($really) {
			$this->with_content = true;
		}

		return $this;
	}

	/**
	 * Завершающий метод копирования инфоблока. Все цепочки должны завершаться им.
	 *
	 * <pre>
	 *
	 *  // Скопируем инфоблок 2 без содержания в тип news
	 *  DefaToolsCopy::iblock(2)->ToType('news');
	 *
	 * </pre>
	 *
	 * @param string $type Тип инфоблока
	 * @return bool|int
	 */
	public function ToType($type)
	{
		$this->type = $type;

		return self::CopyIBlock($this->iblock_id, $this->type, $this->with_content, $this->check_uniqueness, $this->section_ids);
	}


	/**
	 * @param int $iblock_id
	 */
	private function SetIblockId($iblock_id)
	{
		$this->iblock_id = $iblock_id;
	}

	/**
	 * @param array $section_ids
	 */
	private function SetSectionIds(array $section_ids)
	{
		$this->section_ids = $section_ids;
	}



	/**
	 * Копирует инфоблок с указанным $ID и возвращает id созданного.
	 * Не реккомендуется использовать данный метод не в контексте модуля, т.к. параметры могут быть изменены.
	 * Если необходимо скопировать инфоблок программным способом, воспользуйтесь методом DefaToolsCopy::iblock().
	 *
	 * @param $ID
	 * @param $type
	 * @param bool $copyContent
	 * @param bool $checkUnique
	 * @param null $sectionIds
	 * @return bool|int
	 */
	public function CopyIBlock($ID, $type, $copyContent = true, $checkUnique = false, $sectionIds = null)
	{
		/** @var $rs CDBResult */
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$rs                    = CIBlock::GetByID($ID);
		$res                   = $rs->Fetch();
		$res["IBLOCK_TYPE_ID"] = $type;
		if (is_set($res, "PICTURE") && intval($res["PICTURE"]) > 0) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$res["PICTURE"] = CFile::MakeFileArray($res["PICTURE"]);
		}

		if ($checkUnique) {
			$res["EXTERNAL_ID"] = 'copy_' . $res["ID"];

			/** @var $rsIBlock CDBResult */
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$rsIBlock = CIBlock::GetList(array(), array("TYPE" => $res["IBLOCK_TYPE_ID"], "=XML_ID" => $res["EXTERNAL_ID"]));
			if ($arIBlock = $rsIBlock->Fetch())
				return $arIBlock["ID"];
		}

		$ib = new CIBlock();
		if ($NEW_ID = $ib->Add($res)) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			CIBlock::SetFields($NEW_ID, CIBlock::GetFields($ID));
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			CIBlock::SetPermission($NEW_ID, CIBlock::GetGroupPermissions($ID));

			$arProperties = $arPropertyEnums = $arUFProperties = $arUFPropertyEnums = array();

			self::syncIblockCatalog($ID, $NEW_ID);
			self::syncIblockProperties($ID, $NEW_ID, $arProperties, $arPropertyEnums, $arUFProperties, $arUFPropertyEnums);
			self::syncIblockPropertiesUserSettings($ID, $NEW_ID, $arProperties, $arUFProperties);

			if ($copyContent) {
				self::syncIblockContent($ID, $NEW_ID, $arProperties, $arPropertyEnums, $arUFPropertyEnums, $sectionIds);
			}
		}

		return $NEW_ID;
	}

	private function syncIblockContent($FROM_IBLOCK_ID, $TO_IBLOCK_ID, $arProperties, $arPropertyEnums, $arUFPropertyEnums, $sectionIds = null)
	{
		/** @global $USER_FIELD_MANAGER CUserTypeManager */
		global $USER_FIELD_MANAGER;
		/** @global $APPLICATION CMain */
		global $APPLICATION;

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		if (CModule::IncludeModule("catalog") && CCatalog::GetByID($FROM_IBLOCK_ID)) {
			$flagCopyCatalogProperties = true;
		} else {
			$flagCopyCatalogProperties = false;
		}

		$bs = new CIBlockSection;
		$be = new CIBlockElement;

		$arSections = $arElements = $arElementProps = array();

		if ($sectionIds) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$rsSection = CIBlockSection::GetList(array('depth_level'=>'asc'), array("IBLOCK_ID" => $FROM_IBLOCK_ID, "ID" => $sectionIds), false, array("SELECT" => "UF_*"));
		} else {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$rsSection = CIBlockSection::GetList(array('depth_level'=>'asc'), array("IBLOCK_ID" => $FROM_IBLOCK_ID), false, array("SELECT" => "UF_*"));
		}
		while ($resSection = $rsSection->Fetch()) {

			$ufProps = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_" . $FROM_IBLOCK_ID . "_SECTION", $resSection["ID"]);

			foreach (array("PICTURE", "DETAIL_PICTURE") as $code) {
				if (is_set($resSection, $code) && intval($resSection[$code]) > 0)
					/** @noinspection PhpDynamicAsStaticMethodCallInspection */
				$resSection[$code] = CFile::MakeFileArray($resSection[$code]);
			}

			$resSection["IBLOCK_ID"] = $TO_IBLOCK_ID;

			foreach (array("GLOBAL_ACTIVE", "LEFT_MARGIN", "RIGHT_MARGIN", "DEPTH_LEVEL", "IBLOCK_TYPE_ID", "IBLOCK_CODE", "IBLOCK_EXTERNAL_ID", "LIST_PAGE_URL", "SECTION_PAGE_URL", "SEARCHABLE_CONTENT") as $code)
				unset($resSection[$code]);

			if (intval($resSection["IBLOCK_SECTION_ID"]) > 0) {
				$resSection["IBLOCK_SECTION_ID"] = $arSections[$resSection["IBLOCK_SECTION_ID"]]["NEW_ID"];
			}

			unset($resSection["TIMESTAMP_X"]);

			foreach ($ufProps as $k => $v) {
				if (!empty($v["VALUE"]) && is_set($resSection, $k) && !empty($resSection[$k])) {

					switch ($v["USER_TYPE_ID"]) {
						case "enumeration":

							if (is_array($resSection[$k])) {
								foreach ($resSection[$k] as $kk => $vv)
									$resSection[$k][$kk] = $arUFPropertyEnums[$vv];
							} else
								$resSection[$k] = $arUFPropertyEnums[$resSection[$k]];

							break;
						case "file":

							if (is_array($resSection[$k])) {
								foreach ($resSection[$k] as $kk => $vv)
									/** @noinspection PhpDynamicAsStaticMethodCallInspection */
								$resSection[$k][$kk] = CFile::MakeFileArray($vv);
							} else
								/** @noinspection PhpDynamicAsStaticMethodCallInspection */
							$resSection[$k] = CFile::MakeFileArray($resSection[$k]);

							break;
					}
				}
			}

			if ($NEW_SECTION_ID = $bs->Add($resSection, true, true, true)) {
				$resSection["NEW_ID"] = $NEW_SECTION_ID;
			} else {
				// error
			}

			$arSections[$resSection["ID"]] = $resSection;
		}

		$old2newIDS = array();
		if ($sectionIds) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$rsElement = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $FROM_IBLOCK_ID, "SECTION_ID" => $sectionIds));
		} else {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$rsElement = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $FROM_IBLOCK_ID));
		}

		while ($obElement = $rsElement->GetNextElement()) {
			$oldResElement = $obElement->GetFields();
			$resElement    = array();

			$OLD_ID          = $oldResElement["ID"];
			$resElementProps = $obElement->GetProperties();

			foreach ($oldResElement as $k => $v)
				if (substr($k, 0, 1) == "~" && !empty($v))
					$resElement[substr($k, 1)] = $v;

			foreach (array("PREVIEW_PICTURE", "DETAIL_PICTURE") as $code) {
				if (is_set($resElement, $code) && intval($resElement[$code]) > 0)
					/** @noinspection PhpDynamicAsStaticMethodCallInspection */
				$resElement[$code] = CFile::MakeFileArray($resElement[$code]);
				else {
					unset($resElement[$code]);
					unset($resElement["~" . $code]);
				}
			}

			foreach (array("ID", "LOCK_STATUS", "WF_DATE_LOCK", "WF_LAST_HISTORY_ID", "WF_LOCKED_BY", "WF_NEW", "WF_PARENT_ELEMENT_ID") as $code) {
				unset($resElement[$code]);
				unset($resElement["~" . $code]);
			}

			if ($resElement["IBLOCK_SECTION_ID"] > 0)
				$resElement["IBLOCK_SECTION_ID"] = $arSections[$resElement["IBLOCK_SECTION_ID"]]["NEW_ID"];

			$resElement["IBLOCK_ID"] = $TO_IBLOCK_ID;
			$arElementProps          = array();

			foreach ($resElementProps as $fields) {

				foreach (array("VALUE", "DESCRIPTION", "VALUE_XML_ID", "VALUE_ENUM_ID") as $code) {
					if (!is_array($fields[$code]))
						$fields[$code] = array($fields[$code]);
				}

				foreach ($fields["VALUE"] as $propKey => $propValue) {
					switch ($fields["PROPERTY_TYPE"]) {
						case "F":
							/** @noinspection PhpDynamicAsStaticMethodCallInspection */
							$arElementProps[$arProperties[$fields["ID"]]][] = array("VALUE" => CFile::MakeFileArray($fields["VALUE"][$propKey]), "DESCRIPTION" => $fields["DESCRIPTION"][$propKey]);
							break;
						case "L":
							$arElementProps[$arProperties[$fields["ID"]]][] = array("VALUE" => $arPropertyEnums[$fields["VALUE_ENUM_ID"][$propKey]], "DESCRIPTION" => $arPropertyEnums[$fields["DESCRIPTION"][$propKey]]);
							break;
						default:
							$arElementProps[$arProperties[$fields["ID"]]][] = array("VALUE" => $fields["VALUE"][$propKey], "DESCRIPTION" => $fields["DESCRIPTION"][$propKey]);
							break;
					}
				}
			}
			$resElement["PROPERTY_VALUES"] = $arElementProps;

			if ($NEW_ELEMENT_ID = $be->Add($resElement)) {

				/** @noinspection PhpDynamicAsStaticMethodCallInspection */
				$rsElementSections  = CIBlockElement::GetElementGroups($OLD_ID, true);
				$resElementSections = array();

				while ($section = $rsElementSections->Fetch())
					$resElementSections[] = $arSections[$section["ID"]]["NEW_ID"];

				/** @noinspection PhpDynamicAsStaticMethodCallInspection */
				CIBlockElement::SetElementSection($NEW_ELEMENT_ID, $resElementSections);

			} else {

				/** @noinspection PhpUndefinedFieldInspection */
				$APPLICATION->ThrowException($be->LAST_ERROR);

				return false;
				// error
			}
			$old2newIDS[$OLD_ID] = $NEW_ELEMENT_ID;
		}

		// if infoblock mapped to catalog
		if ($flagCopyCatalogProperties) {
			foreach ($old2newIDS as $old => $new) {

				// 1. copy CPrice data
				/** @noinspection PhpDynamicAsStaticMethodCallInspection */
				$priceRes = CPrice::GetList(array(), array("PRODUCT_ID" => $old));

				while ($price = $priceRes->Fetch()) {

					$price['PRODUCT_ID'] = $new;
					unset($price['ID'], $price['TIMESTAMP_X']);
					/** @noinspection PhpDynamicAsStaticMethodCallInspection */
					CPrice::Add($price);
				}

				unset($fields, $priceRes, $price);

				// 2. copy CCatalogProduct data
				/** @noinspection PhpDynamicAsStaticMethodCallInspection */
				$product       = CCatalogProduct::GetByID($old);
				$product['ID'] = $new;
				unset($product['TIMESTAMP_X']);

				/** @noinspection PhpDynamicAsStaticMethodCallInspection */
				CCatalogProduct::Add($product);
				unset($product);
			}
		}

		return true;
	}

	private function syncIblockPropertiesUserSettings($FROM_IBLOCK_ID, $TO_IBLOCK_ID, $arProperties = array())
	{
		$arPropertiesNew = array();

		foreach ($arProperties as $k => $v) {
			$arPropertiesNew["--PROPERTY_" . $k . "--"] = "--PROPERTY_" . $v . "--";
			$arPropertiesNewClear["PROPERTY_" . $k]     = "PROPERTY_" . $v;
		}

		$iblockHashes = array();
		foreach (array($FROM_IBLOCK_ID, $TO_IBLOCK_ID) as $iblock) {
			/** @var $rs CDBResult */
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$rs                       = CIBlock::GetById($iblock);
			$res                      = $rs->Fetch();
			$iblockHashes[$res["ID"]] = "tbl_iblock_list_" . md5($res["IBLOCK_TYPE_ID"] . "." . $res["ID"]);
		}

		foreach (array($GLOBALS['USER']->GetID(), false) as $user) 
		{	
			foreach (array("section", "element") as $type) {
				// form
				$res = CUserOptions::GetOption("form", "form_" . $type . "_" . $FROM_IBLOCK_ID, false, $user);

				if (!empty($res["tabs"])) {
					$res["tabs"] = str_replace(array_keys($arPropertiesNew), array_values($arPropertiesNew), $res["tabs"]);
					CUserOptions::SetOption("form", "form_" . $type . "_" . $TO_IBLOCK_ID, $res, ($user === false ? "Y" : "N"), $user);
				}
				// /form
			}

			// list
			$res = CUserOptions::GetOption("list", $iblockHashes[$FROM_IBLOCK_ID], false, $user);

			if ($res["columns"]) {
				$res["columns"] = explode(",", $res["columns"]);
				foreach ($res["columns"] as $k => $v) {
					if (isset($arPropertiesNewClear[$v]))
						$res["columns"][$k] = $arPropertiesNewClear[$v];
				}
				$res["columns"] = implode(",", $res["columns"]);
			}

			if (isset($res["by"]) && isset($arPropertiesNewClear[$res["by"]]))
				$res["by"] = $arPropertiesNewClear[$res["by"]];

			CUserOptions::SetOption("list", $iblockHashes[$TO_IBLOCK_ID], $res, ($user === false ? "Y" : "N"), $user);
			// /list
		}
	}

	private function syncIblockProperties($FROM_IBLOCK_ID, $TO_IBLOCK_ID, &$arProperties = array(), &$arPropertyEnums = array(), &$arUFProperties = array(), &$arUFPropertyEnums = array())
	{
		/** @global $APPLICATION CMain */
		global $APPLICATION;

		if (!is_array($arUFPropertyEnums))
			$arUFPropertyEnums = array();


		$obUserField = new CUserTypeEntity;
		$obEnum      = new CUserFieldEnum;

		$arFilter = array(
			"ENTITY_ID" => "IBLOCK_" . $FROM_IBLOCK_ID . "_SECTION",
		);
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$rsData = CUserTypeEntity::GetList(array(), $arFilter);
		while ($resData = $rsData->Fetch()) {

			$UF_ID = $resData["ID"];
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$resData = CUserTypeEntity::GetByID($UF_ID);

			if ($resData["USER_TYPE_ID"] == "enumeration") {
				/** @noinspection PhpDynamicAsStaticMethodCallInspection */
				$rsEnum = CUserFieldEnum::GetList(array(), array("USER_FIELD_ID" => $resData["ID"]));
				$enum   = 0;
				while ($resEnum = $rsEnum->Fetch()) {
					$resData["ENUM"]["n" . $enum++] = $resEnum;
				}
			}

			$resData["ENTITY_ID"] = "IBLOCK_" . $TO_IBLOCK_ID . "_SECTION";

			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$rsDataExists = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => $resData["ENTITY_ID"],
					"FIELD_NAME" => $resData["FIELD_NAME"],)
			);

			if ($resDataExists = $rsDataExists->Fetch()) {

				$NEW_ID = $resDataExists["ID"];
				unset($resData["ID"]);

				$obUserField->Update($NEW_ID, $resData);
				if (!empty($resData["ENUM"])) {

					foreach ($resData["ENUM"] as $k => $v) {
						unset($resData["ENUM"][$k]["USER_FIELD_ID"]);
						unset($resData["ENUM"][$k]["ID"]);
					}

					$obEnum->SetEnumValues($NEW_ID, $resData["ENUM"]);
				}
			} else {
				unset($resData["ID"]);
				$NEW_ID = $obUserField->Add($resData);

				if (!empty($resData["ENUM"])) {
					foreach ($resData["ENUM"] as $k => $v) {
						unset($resData["ENUM"][$k]["USER_FIELD_ID"]);
						unset($resData["ENUM"][$k]["ID"]);
					}

					$obEnum->SetEnumValues($NEW_ID, $resData["ENUM"]);
				}
			}

			$resEnumsOld = $resEnumsNew = array();
			$rsEnums     = $obEnum->GetList(array(), array("USER_FIELD_ID" => $UF_ID));
			while ($resEnums = $rsEnums->Fetch()) {
				$resEnumsOld[] = $resEnums["ID"];
			}

			$rsEnums = $obEnum->GetList(array(), array("USER_FIELD_ID" => $NEW_ID));
			while ($resEnums = $rsEnums->Fetch()) {
				$resEnumsNew[] = $resEnums["ID"];
			}

			if (count($resEnumsOld) == count($resEnumsOld) && !empty($resEnumsOld)) {
				$arUFPropertyEnums += array_combine($resEnumsOld, $resEnumsNew);
			}

			$arUFProperties[$UF_ID] = $NEW_ID;

		}

		$arUpdateProperties = array();
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$rsProperty = CIBlockProperty::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $FROM_IBLOCK_ID));
		while ($arProperty = $rsProperty->Fetch()) {
			$arUpdateProperties[] = $arProperty;
		}

		foreach ($arUpdateProperties as $arProperty) {

			$arProperty["IBLOCK_ID"] = $TO_IBLOCK_ID;
			$arProperty["XML_ID"]    = "PROP_" . $arProperty["ID"];

			$ibp = new CIBlockProperty;
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$rs = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $arProperty["IBLOCK_ID"], "XML_ID" => $arProperty["XML_ID"]));
			if ($ar = $rs->Fetch()) {
				$ID = $ar["ID"];
				$ibp->Update($ar["ID"], $arProperty);
				$arProperties[$arProperty["ID"]] = $ar["ID"];
			} else {
				if (!($ID = $ibp->Add($arProperty))) {
					/** @noinspection PhpUndefinedFieldInspection */
					$APPLICATION->ThrowException(GetMessage("DEFATOOLS_IB_DEMO_PROP_CREATE_ERR") . ': ' . $ibp->LAST_ERROR);
				}
				$arProperties[$arProperty["ID"]] = $ID;
			}

			$arUpdateEnums = array();
			if ($arProperty["PROPERTY_TYPE"] == 'L') {
				if ($ID) {
					/** @var $rs CDBResult */
					/** @noinspection PhpDynamicAsStaticMethodCallInspection */
					$rs = CIBlockPropertyEnum::GetList(array("SORT" => "ASC"), array("PROPERTY_ID" => $arProperty["ID"]));
					while ($ar = $rs->Fetch()) {

						$ar["IBLOCK_ID"]   = $arProperty["IBLOCK_ID"];
						$ar["PROPERTY_ID"] = $ID;
//									$ar["XML_ID"] = "ENUM_".$ar["ID"];
						$arUpdateEnums[] = $ar;
					}
				}
			}

			foreach ($arUpdateEnums as $arEnum) {
				$ibpenum = new CIBlockPropertyEnum;

				/** @noinspection PhpDynamicAsStaticMethodCallInspection */
				$rs = CIBlockPropertyEnum::GetList(array(), array("PROPERTY_ID" => $ID, "XML_ID" => $arEnum["XML_ID"]));
				if ($ar = $rs->Fetch()) {
					$arPropertyEnums[$arEnum["ID"]] = $ar["ID"];
					unset($arEnum["ID"]);
					$ibpenum->Update($ar["ID"], $arEnum);
				} else {
					$arEnumID = $arEnum["ID"];
					unset($arEnum["ID"]);
					if (!($ENUM_ID = $ibpenum->Add($arEnum))) {
						$APPLICATION->ThrowException(GetMessage("DEFATOOLS_IB_DEMO_PROP_ADD_LTYPE_ERR"));
					}
					$arPropertyEnums[$arEnumID] = $ENUM_ID;
				}
			}
		}

		return true;
	}


	private function syncSectionCatalogToIblockCatalog($FROM_IBLOCK_ID, $TO_IBLOCK_ID)
	{

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		if (CModule::IncludeModule("catalog") && CCatalog::GetByID($FROM_IBLOCK_ID)) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			CCatalog::Add(array("IBLOCK_ID" => $TO_IBLOCK_ID, "YANDEX_EXPORT" => "N", "SUBSCRIPTION" => "N"));
		}

		return true;
	}

	private function syncIblockCatalog($FROM_IBLOCK_ID, $TO_IBLOCK_ID)
	{
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		if (CModule::IncludeModule("catalog") && CCatalog::GetByID($FROM_IBLOCK_ID)) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			CCatalog::Add(array("IBLOCK_ID" => $TO_IBLOCK_ID, "YANDEX_EXPORT" => "N", "SUBSCRIPTION" => "N"));
		}

		return true;
	}

}
