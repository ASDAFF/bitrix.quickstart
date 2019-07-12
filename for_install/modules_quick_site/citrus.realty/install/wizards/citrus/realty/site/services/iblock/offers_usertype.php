<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 *
 * Обращение # 525256
 *
 * Для пользовательских полей разделов инфоблока типа «Список» при экспорте и последующем импорте инфоблока (в XML) восстанавливаются не все настройки этого поля.
 * Не экспортируются в XML (и не импортируются обратно) языковые настройки поля (подписи) и значения списка. Из-за отсутствия списка для поля, не восстанавливаются и значения этого поля для разделов.
 *
 * Оригинальные настройки поля: http://awesomescreenshot.com/0ea3eumhed
 * Что получаем после импорта: http://awesomescreenshot.com/0133eumnd2
 *
 * Данный код заполняет недостающие настройки поля и проставляет значения для поля демо-данных.
 *
 */

if (!\Bitrix\Main\Loader::includeModule("citrus.realty"))
	return;

WizardServices::IncludeServiceLang(pathinfo(__FILE__, PATHINFO_BASENAME));

// значения списка
$enum = array (
	"n0" =>
		array (
			'VALUE' => GetMessage("CITRUS_UF_TYPE_LIST"),
			'DEF' => 'Y',
			'SORT' => '50',
			'XML_ID' => 'list',
		),
	"n1" =>
		array (
			'VALUE' => GetMessage("CITRUS_UF_TYPE_CARDS"),
			'DEF' => 'N',
			'SORT' => '100',
			'XML_ID' => 'cards',
		),
	"n2" =>
		array (
			'VALUE' => GetMessage("CITRUS_UF_TYPE_ONLY_TEXT"),
			'DEF' => 'N',
			'SORT' => '500',
			'XML_ID' => 'only_text',
		),
);

// настройки поля
$fieldSettings = array (
	'FIELD_NAME' => 'UF_TYPE',
	'USER_TYPE_ID' => 'enumeration',
	'XML_ID' => 'realty_offer_type',
	'SORT' => '100',
	'MULTIPLE' => 'N',
	'MANDATORY' => 'N',
	'SHOW_FILTER' => 'I',
	'SHOW_IN_LIST' => 'Y',
	'EDIT_IN_LIST' => 'Y',
	'IS_SEARCHABLE' => 'N',
	'SETTINGS' =>
		array (
			'DISPLAY' => 'CHECKBOX',
			'LIST_HEIGHT' => 3,
			'CAPTION_NO_VALUE' => GetMessage("CITRUS_UF_NOT_SELECTED"),
		),
	'EDIT_FORM_LABEL' =>
		array (
			'en' => 'Section type',
			'ru' => GetMessage("CITRUS_UF_TYPE"),
		),
	'LIST_COLUMN_LABEL' =>
		array (
			'en' => 'Section type',
			'ru' => GetMessage("CITRUS_UF_TYPE"),
		),
	'LIST_FILTER_LABEL' =>
		array (
			'en' => 'Section type',
			'ru' => GetMessage("CITRUS_UF_TYPE"),
		),
	'ERROR_MESSAGE' =>
		array (
			'en' => '',
			'ru' => '',
		),
	'HELP_MESSAGE' =>
		array (
			'en' => '',
			'ru' => GetMessage("CITRUS_UF_TYPE_DESC"),
		),
);

\Citrus\Realty\Helper::resetCache();
$iblockId = \Citrus\Realty\Helper::getIblock("offers", WIZARD_SITE_ID);
$userField = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$iblockId."_SECTION", "FIELD_NAME" => $fieldSettings["FIELD_NAME"]))->Fetch();

// обновление настроек поля
$entity = new CUserTypeEntity();
if (!$entity->Update($userField["ID"], $fieldSettings))
{
	$ex = $APPLICATION->GetException();
	die($fieldSettings["FILE_NAME"] . ' update error' . ($ex ? ': ' . $ex->GetString() : ''));
}

// установка значений списка (если уже не установлены)
$dbEnum = CUserFieldEnum::GetList(array(), array("USER_FIELD_ID" => $userField["ID"]));
if ($dbEnum->SelectedRowsCount() == 0)
{
	$obEnum = new CUserFieldEnum();
	if (!$obEnum->SetEnumValues($userField["ID"], $enum))
	{
		$ex = $APPLICATION->GetException();
		die('UF_TYPE update error' . ($ex ? ': ' . $ex->GetString() : ''));
	}

	// значения поля для разделов ИБ предложений
	$sectionTypes = array(
		104 => 'list',
		'DEMO_A6UFU' => 'list',
		'DEMO_X4toS' => 'list',
		'DEMO_vumJ1' => 'list',
		'DEMO_CCJ0M' => 'list',
		'DEMO_Fo73B' => 'cards',
		'DEMO_iQQVK' => 'cards',
		'DEMO_Zq4Bh' => 'list',
		'DEMO_mBVqv' => 'list',
		'DEMO_LjiVp' => 'list',
		107 => 'list',
		'DEMO_m2sGc' => 'cards',
		100 => 'list',
		108 => 'cards',
		'DEMO_5v5Ht' => 'list',
		109 => 'list',
		113 => 'cards',
		103 => 'list',
	);

	// ID значений списка
	$ufTypeIds = array();
	$rs = CUserFieldEnum::GetList(array(), array("USER_FIELD_ID" => $userField["ID"]));
	while ($ar = $rs->Fetch())
		$ufTypeIds[$ar["XML_ID"]] = $ar["ID"];

	// установка значений поля
	$rsSection = CIBlockSection::GetList(
		Array("SORT" => "ASC"),
		Array("IBLOCK_ID" => $iblockId),
		$bIncCnt = false,
		$arSelectFields = Array("ID", "XML_ID")
	);
	while ($arSection = $rsSection->GetNext())
		if (array_key_exists($arSection["XML_ID"], $sectionTypes))
			$GLOBALS["USER_FIELD_MANAGER"]->Update("IBLOCK_{$iblockId}_SECTION", $arSection["ID"], Array("UF_TYPE" => $ufTypeIds[$sectionTypes[$arSection["XML_ID"]]]));
}

/**
 * Устанавливает для пользовательского свойства подписи и ID инфоблока (для свойств с привязкой к свойствам устанавливает ID этого же инфоблока)
 *
 * @param string $fieldName Символьный код пользовательского свойства
 * @param string $iblockCode Символьный код инфоблока
 * @param array $labels Массив с подписями array(0 => 'полное название', 1 => 'сокращенное название')
 * @throws Exception
 */
$updateListField = function($fieldName, $iblockCode, $labels) use ($entity, $APPLICATION)
{
    $iblockId = \Citrus\Realty\Helper::getIblock($iblockCode, WIZARD_SITE_ID);
    $userField = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$iblockId."_SECTION", "FIELD_NAME" => $fieldName))->Fetch();
    if ($userField)
    {
        $fieldSettings = array (
            'FIELD_NAME' => $fieldName,
            'SETTINGS' => array (
                'DISPLAY' => 'LIST',
                'LIST_HEIGHT' => 5,
                'IBLOCK_ID' => $iblockId,
                'CAPTION_NO_VALUE' => GetMessage("CITRUS_UF_NOT_SELECTED"),
            ),
            'EDIT_FORM_LABEL' =>
                array (
                    'en' => '',
                    'ru' => $labels[0],
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'en' => '',
                    'ru' => $labels[1],
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'en' => '',
                    'ru' => $labels[1],
                ),
        );
        if (!$entity->Update($userField["ID"], $fieldSettings))
        {
            $ex = $APPLICATION->GetException();
            die($fieldSettings["FIELD_NAME"] . ' update error' . ($ex ? ': ' . $ex->GetString() : ''));
        }
    }

};

// установим ID инфоблока и подписи для поля с настройкой колонок табличного представления
$updateListField('UF_PROP_LINK', 'offers', array(
    GetMessage("CITRUS_REALTY_PROP_LINK"),
    GetMessage("CITRUS_REALTY_PROP_LINK_SHORT")
));

// установим ID инфоблока и подписи для поля с настройкой полей сортировки
$updateListField('UF_SORT_FIELDS', 'offers', array(
    GetMessage("CITRUS_REALTY_PROP_SORT"),
    GetMessage("CITRUS_REALTY_PROP_SORT_SHORT")
));
