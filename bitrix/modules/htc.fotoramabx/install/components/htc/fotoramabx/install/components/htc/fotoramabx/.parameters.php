<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

include_once('class.php'); //��� �������� ������������� �������� �� ������ ����������

$availableSources = array(); //������ ��� �������� ��������������� ���������� ����������� 
$sourceIdParameterName = ''; //��������� ���� ������� ID ��������� �����������

$fullscreenModes = array(
	CFotoramaComponent::FULLSCREEN_MODE_DISABLED => GetMessage('FULLSCREEN_DISABLED'), //������������� ����� ��������
	CFotoramaComponent::FULLSCREEN_MODE_ENABLED => GetMessage('FULLSCREEN_ENABLED'), //��������� �������� �� ��� ����
	CFotoramaComponent::FULLSCREEN_MODE_NATIVE => GetMessage('FULLSCREEN_NATIVE'), //��������� ������������� Fullscreen API
);

$navigationStyles = array(
	CFotoramaComponent::NAVIGATION_STYLE_THUMBS => GetMessage('NAVIGATION_THUMBS'), //��������� �����������
	CFotoramaComponent::NAVIGATION_STYLE_DOTS => GetMessage('NAVIGATION_DOTS'), //�����
	CFotoramaComponent::NAVIGATION_STYLE_DISABLED => GetMessage('NAVIGATION_NONE'), //��������� ���������
);

$navigationPositions = array(
	CFotoramaComponent::NAVIGATION_POSITION_BOTTOM => GetMessage('NAVIGATION_POSITION_BOTTOM'),
	CFotoramaComponent::NAVIGATION_POSITION_TOP => GetMessage('NAVIGATION_POSITION_TOP'),
);

$sourceTypes = array(); //���� ���������� �����������
if (CModule::IncludeModule("fileman"))
{
	$sourceTypes[CFotoramaComponent::SOURCE_TYPE_MEDIALIBRARY_COLLECTION] = GetMessage('MEDIALIBRARY_COLLECTION'); //��������� ���������������
}
if (CModule::IncludeModule("iblock"))
{
	$sourceTypes[CFotoramaComponent::SOURCE_TYPE_IBLOCK_SECTION] = GetMessage('IBLOCK_SECTION'); //������ ��������� (������������ ����������� ������ � ��������� ����������� ���������)
}

/**
 * ������������� ��������� ����������
 */
$customComponentParameters = array();

$customComponentParameters['CACHE_TIME'] = array( //��������� �����������
	'DEFAULT' => CFotoramaComponent::CACHE_TIME_DEFAULT,
);

$customComponentParameters['SOURCE_TYPE'] = array( //����� ��������� �����������
	'PARENT' => 'BASE',
	'NAME' => GetMessage('SOURCE_TYPE'),
	'TYPE' => 'LIST',
	'ADDITIONAL_VALUES' => 'N',
	'VALUES' => $sourceTypes,
	'REFRESH' => 'Y',
	'MULTIPLE' => 'N',
);

if ($arCurrentValues['SOURCE_TYPE'] === CFotoramaComponent::SOURCE_TYPE_IBLOCK_SECTION && isset($sourceTypes[CFotoramaComponent::SOURCE_TYPE_IBLOCK_SECTION]))
{
	$sourceIdParameterName = GetMessage('IBLOCK_SECTION');

	$iblocksList = array(); //������� ������ ���� �������� ����������

	$dbIblocks = CIBlock::GetList(
		array(
			'IBLOCK_TYPE' => 'ASC',
			'SORT' => 'ASC',
		),
		array(
			'ACTIVE' => 'Y',
		),
		false
	);

	while($iblockInfo = $dbIblocks->Fetch())
	{
		$iblocksList[$iblockInfo['ID']] = $iblockInfo['NAME'];
	}

	$customComponentParameters['IBLOCK_ID'] = array(
		'PARENT' => 'BASE',
		'NAME' => GetMessage('IBLOCK'),
		'TYPE' => 'LIST',
		'ADDITIONAL_VALUES' => 'N',
		'VALUES' => $iblocksList,
		'REFRESH' => 'Y',
		'MULTIPLE' => 'N',
	);

	if (!empty($arCurrentValues['IBLOCK_ID']) && $arCurrentValues['IBLOCK_ID'] > 0)
	{
		$dbIblockSections =	CIBlockSection::GetList(
			array(
				'SECTION' => 'ASC',
				'SORT' => 'ASC',
			),
			array(
				'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
				'ACTIVE' => 'Y',
			),
			false,
			array(
				'ID',
				'NAME'
			),
			false
		);

		while($iblockSectionInfo = $dbIblockSections->GetNext())
		{
			$availableSources[$iblockSectionInfo['ID']] = $iblockSectionInfo['NAME'];
		}
	}
}
elseif ($arCurrentValues['SOURCE_TYPE'] === CFotoramaComponent::SOURCE_TYPE_MEDIALIBRARY_COLLECTION && isset($sourceTypes[CFotoramaComponent::SOURCE_TYPE_MEDIALIBRARY_COLLECTION]))
{
	$sourceIdParameterName = GetMessage('MEDIALIBRARY_COLLECTION');

	CMedialib::Init(); //������ ��������������� ���������� �� �� �������������

	//CMedialibCollection::GetList ���������� ����� ������ � ����������� � ���������� 
	$medialibraryCollections = CMedialibCollection::GetList(
		array(
			'arFilter' => array(
				'ACTIVE' => 'Y'
			)
		)
	);

	foreach($medialibraryCollections as $medialibraryCollection)
	{
		$collectionId = $medialibraryCollection['ID'];
		$collectionName = $medialibraryCollection['NAME'];
		$availableSources[$collectionId] = $collectionName;
	}
}

if(!empty($arCurrentValues['SOURCE_TYPE']))
{
	$customComponentParameters['SOURCE_ID'] = array( //������ ��������� ��������� ��������������� ��� �������� ����������
		'PARENT' => 'BASE',
		'NAME' => $sourceIdParameterName,
		'TYPE' => 'LIST',
		'ADDITIONAL_VALUES' => 'N',
		'VALUES' => $availableSources,
		'REFRESH' => 'N',
		'MULTIPLE' => 'N',
	);
}

$customComponentParameters['ALLOW_FULLSCREEN'] = array( //����� ������ ������������� ���������
	'PARENT' => 'FOTORAMA_EXTENDED_SETTINGS',
	'NAME' => GetMessage('ALLOW_FULLSCREEN'),
	'TYPE' => 'LIST',
	'ADDITIONAL_VALUES' => 'N',
	'VALUES' => $fullscreenModes,
	'REFRESH' => 'N',
	'MULTIPLE' => 'N',
);

$customComponentParameters['NAVIGATION_STYLE'] = array( //����� ����� ��������� (���������, ����� ��� ������� ���������)
	'PARENT' => 'FOTORAMA_EXTENDED_SETTINGS',
	'NAME' => GetMessage('NAVIGATION_STYLE'),
	'TYPE' => 'LIST',
	'ADDITIONAL_VALUES' => 'N',
	'VALUES' => $navigationStyles,
	'REFRESH' => 'N',
	'MULTIPLE' => 'N',
);

$customComponentParameters['SHOW_CAPTION'] = array( //���������� �������
	'PARENT' => 'FOTORAMA_EXTENDED_SETTINGS',
	'NAME' => GetMessage('SHOW_CAPTION'),
	'TYPE' => 'CHECKBOX',
);

$customComponentParameters['SHUFFLE'] = array( //������������ �� ����������� ������ ��� ����� �������
	'PARENT' => 'FOTORAMA_EXTENDED_SETTINGS',
	'NAME' => GetMessage('SHUFFLE'),
	'TYPE' => 'CHECKBOX',
);

$customComponentParameters['CHANGE_HASH'] = array( //�������� �� ��� � �������� ������
	'PARENT' => 'FOTORAMA_EXTENDED_SETTINGS',
	'NAME' => GetMessage('CHANGE_HASH'),
	'TYPE' => 'CHECKBOX',
);

$customComponentParameters['LAZY_LOAD'] = array( //������������ �������� � ����������� JS http://fotorama.io/customize/lazy-load/
	'PARENT' => 'FOTORAMA_EXTENDED_SETTINGS',
	'NAME' => GetMessage('LAZY_LOAD'),
	'TYPE' => 'CHECKBOX',
);

$customComponentParameters['NAVIGATION_POSITION'] = array( //������������ ���������
	'PARENT' => 'FOTORAMA_EXTENDED_SETTINGS',
	'NAME' => GetMessage('NAVIGATION_POSITION'),
	'TYPE' => 'LIST',
	'ADDITIONAL_VALUES' => 'N',
	'VALUES' => $navigationPositions,
	'REFRESH' => 'N',
	'MULTIPLE' => 'N',
);

$customComponentParameters['LOOP'] = array( //��������� ��������� �� ������������
	'PARENT' => 'FOTORAMA_EXTENDED_SETTINGS',
	'NAME' => GetMessage('LOOP'),
	'TYPE' => 'CHECKBOX',
);

/**
 * ��� ��������� ����������
 */
$arComponentParameters = array(
	'GROUPS' => array(
		'FOTORAMA_EXTENDED_SETTINGS' => array( //������ ����������� �������� ��������
			'NAME' => GetMessage('FOTORAMA_EXTENDED_SETTINGS'),
			'SORT' => 400,
		),
	),
	'PARAMETERS' => $customComponentParameters,
);