<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

//Инициализация вспомогательных массивов
$arEvent = $arFields = $arEventTypes = Array();

//Массив типов почтовых событий
$dbType = CEventType::GetList(array('LID' => LANGUAGE_ID));
while($arType = $dbType->GetNext())
{
	$arEventTypes[$arType['EVENT_NAME']] = '[' . $arType['EVENT_NAME'] . '] ' . $arType['NAME'];
}

//Массив типов полей (строка, число, дата...)
$arTypes = array(
	'HIDDEN' => GetMessage('UNIF_HIDDEN'),
	'STRING' => GetMessage('UNIF_STRING'),
	'INT' => GetMessage('UNIF_INT'),
	'CHECKBOX' => GetMessage('UNIF_CHECKBOX'),
	'DATE_TIME' => GetMessage('UNIF_DATE_TIME'),
	'DATE_TIME_INTERVAL' => GetMessage('UNIF_DATE_TIME_INTERVAL'),
	'TEXTAREA' => GetMessage('UNIF_TEXTAREA'),
	'EMAIL' => GetMessage('UNIF_EMAIL'),
	'SELECT' => GetMessage('UNIF_SELECT'),
	'RADIO' => GetMessage('UNIF_RADIO'),
	'MULTISELECT' => GetMessage('UNIF_MULTISELECT'),
	'MULTISELECT_CHECKBOXES' => GetMessage('UNIF_MULTISELECT_CHECKBOXES'),
	'FILE'	=> GetMessage('UNIF_FILE')
);

// массивы дефолтных свойств
$arPropertyF = array(
	'DETAIL_PICTURE' => GetMessage('UNIF_DETAIL_PICTURE'),
	'PREVIEW_PICTURE' => GetMessage('UNIF_PREVIEW_PICTURE')
);

$arPropertyS = array(
	'PREVIEW_TEXT' => GetMessage('UNIF_PREVIEW_TEXT'),
	'DETAIL_TEXT' => GetMessage('UNIF_DETAIL_TEXT'),
	'DATE_ACTIVE_FROM' => GetMessage('UNIF_DATE_FROM'),
	'DATE_ACTIVE_TO' => GetMessage('UNIF_DATE_TO'),
	'SORT' => GetMessage('UNIF_SORT')
);

//Массив групп
$arMGroups = array(
	'FIELD_SETTINGS' => array('NAME' => GetMessage('UNIF_FIELD_SETTINGS_GROUP')),
	'SAVE_SETTINGS' => array('NAME' => GetMessage('UNIF_SAVE_SETTINGS_GROUP')),
);

//Массив параметров
$arMParams = array(
	'FORM_ID' => Array(
		'NAME' => GetMessage('UNIF_FORM_ID'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
		'PARENT' => 'BASE'
	),
	'EMAIL_TO' => Array(
		'NAME' => GetMessage('UNIF_EMAIL_SEND_TO'),
		'TYPE' => 'STRING',
		'DEFAULT' => htmlspecialchars(COption::GetOptionString('main', 'email_from')),
		'PARENT' => 'BASE'
	),
	'EVENT_ID' => Array(
		'NAME' => GetMessage('UNIF_TYPES'),
		'TYPE' => 'LIST',
		'VALUES' => $arEventTypes,
		'DEFAULT' => 'FEEDBACK_FORM',
		'PARENT' => 'BASE',
		'REFRESH' => 'Y'
	),
	'JQUERY' => Array(
		'NAME' => GetMessage('UNIF_JQUERY'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
		'PARENT' => 'BASE'
	)
);

//Если указан тип почтового события - выводим все параметры   
if($arCurrentValues['EVENT_ID'] != '')
{
	//Выборка шаблонов привязанных к почтовому событию - $arEvent
	$dbType = CEventMessage::GetList($by = 'ID', $order = 'DESC', array('TYPE_ID' => $arCurrentValues['EVENT_ID'], 'ACTIVE' => 'Y'));
	while($arType = $dbType->GetNext())
	{
		$arEvent[$arType['ID']] = '[' . $arType['ID'] . '] ' . $arType['SUBJECT'];
	}
	
	//Выборка полей из почтового события - $arFields
	$dbType = CEventType::GetList(array('TYPE_ID' => $arCurrentValues['EVENT_ID'], 'LID' => LANGUAGE_ID));
	if($arType = $dbType->GetNext())
	{
		preg_match_all('|^#(.+)# - (.+)$|im', $arType['DESCRIPTION'], $matches);
		$a_size = sizeof($matches[1]);
		for($i = 0; $i < $a_size; $i++)
		{
			$arFields[$matches[1][$i]] = '[' . $matches[1][$i] . '] ' . trim($matches[2][$i]);
		}
	}
	
	$arTemp = array(
		'EVENT_MESSAGE_ID' => Array(
			'NAME' => GetMessage('UNIF_EMAIL_TEMPLATES'),
			'TYPE' => 'LIST',
			'VALUES' => $arEvent,
			'DEFAULT' => '',
			'MULTIPLE' => 'Y',
			'COLS' => 25,
			'PARENT' => 'BASE'
		),
		'OK_TEXT' => Array(
			'NAME' => GetMessage('UNIF_OK_MESSAGE'),
			'TYPE' => 'STRING',
			'DEFAULT' => GetMessage('UNIF_OK_TEXT'),
			'PARENT' => 'BASE'
		),
		'USE_CAPTCHA' => Array(
			'NAME' => GetMessage('UNIF_CAPTCHA'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'PARENT' => 'FIELD_SETTINGS'
		),
		'ENABLE_HIDDEN_ANTISPAM_FIELDS' => Array(
			'NAME' => GetMessage('UNIF_ENABLE_HIDDEN_ANTISPAM_FIELDS'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'PARENT' => 'FIELD_SETTINGS'
		),
		'FILE_EXT' => Array(
			'NAME' => GetMessage('UNIF_FILE_EXT'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
			'PARENT' => 'FIELD_SETTINGS'
		),
		'FILE_SAVE' => Array(
			'NAME' => GetMessage('UNIF_FILE_SAVE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT' => 'FIELD_SETTINGS'
		),
		'REQUIRED_FIELDS' => Array(
			'NAME' => GetMessage('UNIF_REQUIRED_FIELDS'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'VALUES' => $arFields,
			'DEFAULT' => '',
			'COLS' => 25,
			'PARENT' => 'FIELD_SETTINGS'
		),
		'SAVE_TO_IBLOCK' => Array(
			'NAME' => GetMessage('UNIF_SAVE_TO_IBLOCK'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
			'PARENT' => 'SAVE_SETTINGS'
		)
	);

	$arMParams = array_merge($arMParams, $arTemp);

	foreach($arFields as $key => $value)
	{
		if (!array_key_exists($key, $arMParams))
		{
			$arMParams[$key] = array(
				'NAME' => $value,
				'TYPE' => 'LIST',
				'VALUES' => $arTypes,
				'DEFAULT' => 'HIDDEN',
				'REFRESH' => 'Y',
				'PARENT' => 'FIELD_SETTINGS'
			);

			if ($arCurrentValues[$key] == 'HIDDEN')
			{
				$arMParams[$key . '_HIDDEN_VALUE'] = array(
					'NAME' => GetMessage('UNIF_HIDDEN_VALUE') . $value,
					'TYPE' => 'STRING',
					'DEFAULT' => '',
					'PARENT' => 'FIELD_SETTINGS'
				);
			}
			else if ($arCurrentValues[$key] == 'STRING')
			{
				$arMParams[$key . '_MASK'] = array(
					'NAME' => GetMessage('UNIF_MASK') . $value,
					'TYPE' => 'STRING',
					'DEFAULT' => '',
					'PARENT' => 'FIELD_SETTINGS'
				);
			}
			elseif ($arCurrentValues[$key] == 'SELECT' || $arCurrentValues[$key] == 'MULTISELECT' || $arCurrentValues[$key] == 'RADIO' || $arCurrentValues[$key] == 'MULTISELECT_CHECKBOXES')
			{
				$arMParams[$key . '_SELECT_VALUE'] = array(
					'NAME' => GetMessage('UNIF_SELECT_VALUE') . $value,
					'TYPE' => 'STRING',
					'DEFAULT' => '',
					'MULTIPLE' => 'Y',
					'ADDITIONAL_VALUES' => 'Y',
					'PARENT' => 'FIELD_SETTINGS'
				);
			}
		}
		else
		{
			unset($arFields[$key]);
		}
	}
	
	
}

if($arCurrentValues['SAVE_TO_IBLOCK'] == 'Y' && CModule::IncludeModule('iblock'))
{
	$arIBlockType = CIBlockParameters::GetIBlockTypes();
	
	$arIBlock = $arProperty = array();
	
	$rsIBlock = CIBlock::GetList(Array('sort' => 'asc'), Array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y'));
	while($arr = $rsIBlock->Fetch())
		$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
	
	$arMParams['IBLOCK_TYPE'] = array(
		'PARENT' => 'SAVE_SETTINGS',
		'NAME' => GetMessage('UNIF_IBLOCK_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $arIBlockType,
		'REFRESH' => 'Y'
	);
	$arMParams['IBLOCK_ID'] = array(
		'PARENT' => 'SAVE_SETTINGS',
		'NAME' => GetMessage('UNIF_IBLOCK_ID'),
		'TYPE' => 'LIST',
		'VALUES' => $arIBlock,
		'REFRESH' => 'Y'
	);
	
	if($arCurrentValues['IBLOCK_ID'] > 0)
	{
		$arMParams['FIELD_FOR_NAME'] = array(
			'NAME' => GetMessage('UNIF_IBLOCK_FIELD_FOR_NAME'), 
			'TYPE' => 'LIST',  
			'VALUES' => $arFields,
			'PARENT' => 'SAVE_SETTINGS'
		);
		$arMParams['FIELD_FOR_SECTION'] = array(
					'NAME' => GetMessage('UNIF_IBLOCK_FIELD_FOR_SECTION'), 
					'TYPE' => 'LIST',  
					'VALUES' => array_merge(array('0' => GetMessage('UNIF_IBLOCK_SECTION_ROOT')), $arFields),
					'PARENT' => 'SAVE_SETTINGS'
		);

		$rsProp = CIBlockProperty::GetList(Array('sort' => 'asc'), Array('ACTIVE' => 'Y', 'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']));
		while($arr = $rsProp->Fetch())
		{
			if ($arr['PROPERTY_TYPE'] == 'F')
			{
				$arPropertyF[$arr['ID']] = '[' . ($arr['CODE'] == '' ? $arr['ID'] : $arr['CODE']) . '] ' . $arr['NAME'];
			}
			else
			{
				$arPropertyS[$arr['ID']] = '[' . ($arr['CODE'] == '' ? $arr['ID'] : $arr['CODE']) . '] ' . $arr['NAME'];
			}
		}
		
		$arPropertyF[0] = $arPropertyS[0] = GetMessage('UNIF_IBLOCK_NOT_SAVE');
		
		foreach($arFields as $key => $value)
		{
			$arMParams[$key.'_TO_IBLOCK'] = array(
				'NAME' => GetMessage('UNIF_IBLOCK_PROP_SAVE').$value, 
				'TYPE' => 'LIST',  
				'VALUES' => $arCurrentValues[$key] == 'FILE' ? $arPropertyF : $arPropertyS,
				'DEFAULT' => 0,
				'PARENT' => 'SAVE_SETTINGS'
			);
		}
	}
}

//Подписки
if(CModule::IncludeModule('subscribe'))
{
	$arMGroups['SIGN_SETTINGS'] = array('NAME' => GetMessage('UNIF_SIGN_SETTINGS_GROUP'));
	
	$arMParams['SIGN'] = Array(
		'NAME' => GetMessage('UNIF_SIGN'), 
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N', 
		'REFRESH' => 'Y',
		'PARENT' => 'SIGN_SETTINGS'
	);
	if($arCurrentValues['SIGN'] == 'Y')
	{
		$rub = CRubric::GetList();
		while($rub_f = $rub->Fetch())
			$arSign[$rub_f['ID']] = $rub_f['NAME'];

		$arMParams['SIGN_EMAIL'] = Array(
			'NAME' => GetMessage('UNIF_SIGN_EMAIL_FIELD'),  
			'TYPE' => 'LIST', 
			'MULTIPLE' => 'N', 
			'VALUES' => $arFields, 
			'PARENT' => 'SIGN_SETTINGS'
		);
		$arMParams['SIGN_ON'] = Array(
			'NAME' => GetMessage('UNIF_SIGN_ON'), 
			'TYPE' => 'LIST', 
			'MULTIPLE' => 'Y', 
			'VALUES' => $arSign,
			'DEFAULT' => '', 
			'COLS' => 25, 
			'PARENT' => 'SIGN_SETTINGS'
		);
		$arMParams['SIGN_CONFIRM'] = Array(
			'NAME' => GetMessage('UNIF_SIGN_CONFIRM'), 
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT' => 'SIGN_SETTINGS'
		);
		$arMParams['SIGN_CAN_CHOOSE'] = Array(
			'NAME' => GetMessage('UNIF_SIGN_CAN_CHOOSE'), 
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT' => 'SIGN_SETTINGS',
			'REFRESH' => 'Y'
		);
		
		if ($arCurrentValues['SIGN_CAN_CHOOSE'] == 'Y')
		{
			foreach ($arCurrentValues['SIGN_ON'] as $signRubric)
			{
				$arMParams['SIGN_NAME_FOR_RUBRIC_'.$signRubric] = Array(
					'NAME' => GetMessage('UNIF_SIGN_NAME_FOR_RUBRIC').'"'.$arSign[$signRubric].'"', 
					'TYPE' => 'STRING',
					'DEFAULT' => GetMessage('UNIF_SIGN_NAME_FOR_RUBRIC_DEFAULT').'"'.$arSign[$signRubric].'"', 
					'PARENT' => 'SIGN_SETTINGS'
				);
			}
		}
		else
		{
			$arMParams['SIGN_NAME'] = Array(
				'NAME' => GetMessage('UNIF_SIGN_NAME'), 
				'TYPE' => 'STRING',
				'DEFAULT' => GetMessage('UNIF_SIGN_NAME_DEFAULT'), 
				'PARENT' => 'SIGN_SETTINGS'
			);
		}
	}
}

$arComponentParameters = array('GROUPS' => $arMGroups, 'PARAMETERS' => $arMParams);

?>