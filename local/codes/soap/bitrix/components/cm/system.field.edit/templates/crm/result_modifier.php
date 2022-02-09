<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("crm"))
	return;

global $USER;
$CCrmPerms = new CCrmPerms($USER->GetID());
$arSupportedTypes = array(); // all entity types are defined in settings
$arSettings = $arParams['arUserField']['SETTINGS'];
if (isset($arSettings['LEAD']) && $arSettings['LEAD'] === 'Y')
{
	$arSupportedTypes[] = 'LEAD';
}
if (isset($arSettings['CONTACT']) && $arSettings['CONTACT'] === 'Y')
{
	$arSupportedTypes[] = 'CONTACT';
}
if (isset($arSettings['COMPANY']) && $arSettings['COMPANY'] === 'Y')
{
	$arSupportedTypes[] = 'COMPANY';
}
if (isset($arSettings['DEAL']) && $arSettings['DEAL'] === 'Y')
{
	$arSupportedTypes[] = 'DEAL';
}
if (isset($arSettings['PRODUCT']) && $arSettings['PRODUCT'] === 'Y')
{
	$arSupportedTypes[] = 'PRODUCT';
}

$arParams['ENTITY_TYPE'] = array(); // only entity types are allowed for current user
foreach($arSupportedTypes as $supportedType)
{
	if(!$CCrmPerms->HavePerm($supportedType, BX_CRM_PERM_NONE, 'READ'))
	{
		$arParams['ENTITY_TYPE'][] = $supportedType;
	}
}

$arResult['PREFIX'] = count($arSupportedTypes) > 1 ? 'Y' : 'N';
$arResult['MULTIPLE'] = $arParams['arUserField']['MULTIPLE'];
if (!is_array($arResult['VALUE']))
	$arResult['VALUE'] = explode(';', $arResult['VALUE']);
else
{
	$ar = array();
	foreach ($arResult['VALUE'] as $value)
		foreach(explode(';', $value) as $val)
			if (!empty($val))
				$ar[$val] = $val;
	$arResult['VALUE'] = $ar;
}

$arResult['SELECTED'] = array();
foreach ($arResult['VALUE'] as $key => $value)
{
	if (empty($value))
	{
		continue;
	}

	if($arResult['PREFIX'] === 'Y')
	{
		$arResult['SELECTED'][$value] = $value;
	}
	else
	{
		// Try to get raw entity ID
		$ary = explode('_', $value);
		if(count($ary) > 1)
		{
			$value = $ary[1];
		}

		$arResult['SELECTED'][$value] = $value;
	}
}

// last 50 entity
if (in_array('LEAD', $arParams['ENTITY_TYPE'], true))
{
	$arResult['ENTITY_TYPE'][] = 'lead';

	$arSelect = array('ID', 'TITLE', 'FULL_NAME', 'STATUS_ID');
	$obRes = CCrmLead::GetList(array('ID' => 'DESC'), Array(), $arSelect, 50);
	$arFiles = array();

	while ($arRes = $obRes->Fetch())
	{
		$arRes['SID'] = $arResult['PREFIX'] == 'Y'? 'L_'.$arRes['ID']: $arRes['ID'];
		if (isset($arResult['SELECTED'][$arRes['SID']]))
		{
			unset($arResult['SELECTED'][$arRes['SID']]);
			$sSelected = 'Y';
		}
		else
			$sSelected = 'N';

		$arResult['ELEMENT'][] = Array(
			'title' => (str_replace(array(';', ','), ' ', $arRes['TITLE'])),
			'desc' => $arRes['FULL_NAME'],
			'id' => $arRes['SID'],
			'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('crm', 'path_to_lead_show'),
				array(
					'lead_id' => $arRes['ID']
				)
			),
			'type'  => 'lead',
			'selected' => $sSelected
		);
	}
}
if (in_array('CONTACT', $arParams['ENTITY_TYPE'], true))
{
	$arResult['ENTITY_TYPE'][] = 'contact';

	$arSelect = array('ID', 'FULL_NAME', 'COMPANY_TITLE', 'PHOTO');
	$obRes = CCrmContact::GetList(array('ID' => 'DESC'), Array(), $arSelect, 50);
	while ($arRes = $obRes->Fetch())
	{
		$arImg = array();
		if (!empty($arRes['PHOTO']) && !isset($arFiles[$arRes['PHOTO']]))
		{
			if(intval($arRes['PHOTO']) > 0)
				$arImg = CFile::ResizeImageGet($arRes['PHOTO'], array('width' => 25, 'height' => 25), BX_RESIZE_IMAGE_EXACT);
		}

		$arRes['SID'] = $arResult['PREFIX'] == 'Y'? 'C_'.$arRes['ID']: $arRes['ID'];
		if (isset($arResult['SELECTED'][$arRes['SID']]))
		{
			unset($arResult['SELECTED'][$arRes['SID']]);
			$sSelected = 'Y';
		}
		else
			$sSelected = 'N';

		$arResult['ELEMENT'][] = Array(
			'title' => (str_replace(array(';', ','), ' ', $arRes['FULL_NAME'])),
			'desc'  => empty($arRes['COMPANY_TITLE'])? "": $arRes['COMPANY_TITLE'],
			'id' => $arRes['SID'],
			'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('crm', 'path_to_contact_show'),
				array(
					'contact_id' => $arRes['ID']
				)
			),
			'image' => $arImg['src'],
			'type'  => 'contact',
			'selected' => $sSelected
		);
	}
}
if (in_array('COMPANY', $arParams['ENTITY_TYPE'], true))
{
	$arResult['ENTITY_TYPE'][] = 'company';

	$arCompanyTypeList = CCrmStatus::GetStatusListEx('COMPANY_TYPE');
	$arCompanyIndustryList = CCrmStatus::GetStatusListEx('INDUSTRY');
	$arSelect = array('ID', 'TITLE', 'COMPANY_TYPE', 'INDUSTRY',  'LOGO');
	$obRes = CCrmCompany::GetList(array('ID' => 'DESC'), Array(), $arSelect, 50);
	$arFiles = array();
	while ($arRes = $obRes->Fetch())
	{
		$arImg = array();
		if (!empty($arRes['LOGO']) && !isset($arFiles[$arRes['LOGO']]))
		{
			if(intval($arRes['LOGO']) > 0)
				$arImg = CFile::ResizeImageGet($arRes['LOGO'], array('width' => 25, 'height' => 25), BX_RESIZE_IMAGE_EXACT);

			$arFiles[$arRes['LOGO']] = $arImg['src'];
		}

		$arRes['SID'] = $arResult['PREFIX'] == 'Y'? 'CO_'.$arRes['ID']: $arRes['ID'];
		if (isset($arResult['SELECTED'][$arRes['SID']]))
		{
			unset($arResult['SELECTED'][$arRes['SID']]);
			$sSelected = 'Y';
		}
		else
			$sSelected = 'N';

		$arDesc = Array();
		if (isset($arCompanyTypeList[$arRes['COMPANY_TYPE']]))
			$arDesc[] = $arCompanyTypeList[$arRes['COMPANY_TYPE']];
		if (isset($arCompanyIndustryList[$arRes['INDUSTRY']]))
			$arDesc[] = $arCompanyIndustryList[$arRes['INDUSTRY']];


		$arResult['ELEMENT'][] = Array(
			'title' => (str_replace(array(';', ','), ' ', $arRes['TITLE'])),
			'desc' => implode(', ', $arDesc),
			'id' => $arRes['SID'],
			'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('crm', 'path_to_company_show'),
				array(
					'company_id' => $arRes['ID']
				)
			),
			'image' => $arImg['src'],
			'type'  => 'company',
			'selected' => $sSelected
		);
	}
}
if (in_array('DEAL', $arParams['ENTITY_TYPE'], true))
{
	$arResult['ENTITY_TYPE'][] = 'deal';

	$arDealStageList = CCrmStatus::GetStatusListEx('DEAL_STAGE');
	$arSelect = array('ID', 'TITLE', 'STAGE_ID');
	$obRes = CCrmDeal::GetList(array('ID' => 'DESC'), Array(), $arSelect, 50);
	while ($arRes = $obRes->Fetch())
	{
		$arRes['SID'] = $arResult['PREFIX'] == 'Y'? 'D_'.$arRes['ID']: $arRes['ID'];
		if (isset($arResult['SELECTED'][$arRes['SID']]))
		{
			unset($arResult['SELECTED'][$arRes['SID']]);
			$sSelected = 'Y';
		}
		else
			$sSelected = 'N';

		$arResult['ELEMENT'][] = Array(
			'title' => (str_replace(array(';', ','), ' ', $arRes['TITLE'])),
			'desc' => isset($arDealStageList[$arRes['STAGE_ID']])? $arDealStageList[$arRes['STAGE_ID']]: '',
			'id' => $arRes['SID'],
			'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('crm', 'path_to_deal_show'),
				array(
					'deal_id' => $arRes['ID']
				)
			),
			'type'  => 'deal',
			'selected' => $sSelected
		);
	}
}
if (in_array('PRODUCT', $arParams['ENTITY_TYPE'], true))
{
	$arResult['ENTITY_TYPE'][] = 'product';

	$arSelect = array('ID', 'NAME', 'PRICE', 'CURRENCY_ID');
	$obRes = CCrmProduct::GetList(array('ID' => 'DESC'), array(), $arSelect, 50);
	while ($arRes = $obRes->Fetch())
	{
		$arRes['SID'] = $arResult['PREFIX'] == 'Y'? 'PROD_'.$arRes['ID']: $arRes['ID'];
		if (isset($arResult['SELECTED'][$arRes['SID']]))
		{
			unset($arResult['SELECTED'][$arRes['SID']]);
			$sSelected = 'Y';
		}
		else
			$sSelected = 'N';

		$arResult['ELEMENT'][] = array(
			'title' => $arRes['NAME'],
			'desc' => CCrmProduct::FormatPrice($arRes),
			'id' => $arRes['SID'],
			'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('crm', 'path_to_product_show'),
				array(
					'product_id' => $arRes['ID']
				)
			),
			'type'  => 'product',
			'selected' => $sSelected
		);
	}
}

if (!empty($arResult['SELECTED']))
{
	foreach ($arResult['SELECTED'] as $value)
	{
		if($arResult['PREFIX'] === 'Y')
		{
			$ar = explode('_', $value);
			$arSelected[CUserTypeCrm::GetLongEntityType($ar[0])][] = intval($ar[1]);
		}
		else
		{
			if (is_numeric($value))
				$arSelected[$arParams['ENTITY_TYPE'][0]][] = $value;
			else
			{
				$ar = explode('_', $value);
				$arSelected[CUserTypeCrm::GetLongEntityType($ar[0])][] = intval($ar[1]);
			}
		}
	}

	if ($arSettings['LEAD'] == 'Y'
	&& isset($arSelected['LEAD']) && !empty($arSelected['LEAD']))
	{
		$arSelect = array('ID', 'TITLE', 'FULL_NAME', 'STATUS_ID');
		$obRes = CCrmLead::GetList(array('ID' => 'DESC'), Array('ID' => $arSelected['LEAD']), $arSelect);
		$arFiles = array();
		$ar = Array();
		while ($arRes = $obRes->Fetch())
		{
			$arRes['SID'] = $arResult['PREFIX'] == 'Y'? 'L_'.$arRes['ID']: $arRes['ID'];
			if (isset($arResult['SELECTED'][$arRes['SID']]))
			{
				unset($arResult['SELECTED'][$arRes['SID']]);
				$sSelected = 'Y';
			}
			else
				$sSelected = 'N';

			$ar[] = Array(
				'title' => (str_replace(array(';', ','), ' ', $arRes['TITLE'])),
				'desc' => $arRes['FULL_NAME'],
				'id' => $arRes['SID'],
				'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('crm', 'path_to_lead_show'),
					array(
						'lead_id' => $arRes['ID']
					)
				),
				'type'  => 'lead',
				'selected' => $sSelected
			);
		}
		$arResult['ELEMENT'] = array_merge($ar, $arResult['ELEMENT']);
	}
	if ($arSettings['CONTACT'] == 'Y'
	&& isset($arSelected['CONTACT']) && !empty($arSelected['CONTACT']))
	{
		$arSelect = array('ID', 'FULL_NAME', 'COMPANY_TITLE', 'PHOTO');
		$obRes = CCrmContact::GetList(array('ID' => 'DESC'), Array('ID' => $arSelected['CONTACT']), $arSelect);
		$ar = Array();
		while ($arRes = $obRes->Fetch())
		{
			$arImg = array();
			if (!empty($arRes['PHOTO']) && !isset($arFiles[$arRes['PHOTO']]))
			{
				if(intval($arRes['PHOTO']) > 0)
					$arImg = CFile::ResizeImageGet($arRes['PHOTO'], array('width' => 25, 'height' => 25), BX_RESIZE_IMAGE_EXACT);
			}

			$arRes['SID'] = $arResult['PREFIX'] == 'Y'? 'C_'.$arRes['ID']: $arRes['ID'];
			if (isset($arResult['SELECTED'][$arRes['SID']]))
			{
				unset($arResult['SELECTED'][$arRes['SID']]);
				$sSelected = 'Y';
			}
			else
				$sSelected = 'N';

			$ar[] = Array(
				'title' => (str_replace(array(';', ','), ' ', $arRes['FULL_NAME'])),
				'desc'  => empty($arRes['COMPANY_TITLE'])? "": $arRes['COMPANY_TITLE'],
				'id' => $arRes['SID'],
				'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('crm', 'path_to_contact_show'),
					array(
						'contact_id' => $arRes['ID']
					)
				),
				'image' => $arImg['src'],
				'type'  => 'contact',
				'selected' => $sSelected
			);
		}
		$arResult['ELEMENT'] = array_merge($ar, $arResult['ELEMENT']);
	}
	if ($arSettings['COMPANY'] == 'Y'
	&& isset($arSelected['COMPANY']) && !empty($arSelected['COMPANY']))
	{
		$arCompanyTypeList = CCrmStatus::GetStatusListEx('COMPANY_TYPE');
		$arCompanyIndustryList = CCrmStatus::GetStatusListEx('INDUSTRY');
		$arSelect = array('ID', 'TITLE', 'COMPANY_TYPE', 'INDUSTRY',  'LOGO');
		$obRes = CCrmCompany::GetList(array('ID' => 'DESC'), Array('ID' => $arSelected['COMPANY']), $arSelect);
		$arFiles = array();
		$ar = Array();
		while ($arRes = $obRes->Fetch())
		{
			$arImg = array();
			if (!empty($arRes['LOGO']) && !isset($arFiles[$arRes['LOGO']]))
			{
				if(intval($arRes['LOGO']) > 0)
					$arImg = CFile::ResizeImageGet($arRes['LOGO'], array('width' => 25, 'height' => 25), BX_RESIZE_IMAGE_EXACT);

				$arFiles[$arRes['LOGO']] = $arImg['src'];
			}

			$arRes['SID'] = $arResult['PREFIX'] == 'Y'? 'CO_'.$arRes['ID']: $arRes['ID'];
			if (isset($arResult['SELECTED'][$arRes['SID']]))
			{
				unset($arResult['SELECTED'][$arRes['SID']]);
				$sSelected = 'Y';
			}
			else
				$sSelected = 'N';


			$arDesc = Array();
			if (isset($arCompanyTypeList[$arRes['COMPANY_TYPE']]))
				$arDesc[] = $arCompanyTypeList[$arRes['COMPANY_TYPE']];
			if (isset($arCompanyIndustryList[$arRes['INDUSTRY']]))
				$arDesc[] = $arCompanyIndustryList[$arRes['INDUSTRY']];

			$ar[] = Array(
				'title' => (str_replace(array(';', ','), ' ', $arRes['TITLE'])),
				'desc' => implode(', ', $arDesc),
				'id' => $arRes['SID'],
				'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('crm', 'path_to_company_show'),
					array(
						'company_id' => $arRes['ID']
					)
				),
				'image' => $arImg['src'],
				'type'  => 'company',
				'selected' => $sSelected
			);
		}
		$arResult['ELEMENT'] = array_merge($ar, $arResult['ELEMENT']);
	}
	if ($arSettings['DEAL'] == 'Y'
	&& isset($arSelected['DEAL']) && !empty($arSelected['DEAL']))
	{
		$arDealStageList = CCrmStatus::GetStatusListEx('DEAL_STAGE');
		$arSelect = array('ID', 'TITLE', 'STAGE_ID');
		$ar = Array();
		$obRes = CCrmDeal::GetList(array('ID' => 'DESC'), Array('ID' => $arSelected['DEAL']), $arSelect);
		while ($arRes = $obRes->Fetch())
		{
			$arRes['SID'] = $arResult['PREFIX'] == 'Y'? 'D_'.$arRes['ID']: $arRes['ID'];
			if (isset($arResult['SELECTED'][$arRes['SID']]))
			{
				unset($arResult['SELECTED'][$arRes['SID']]);
				$sSelected = 'Y';
			}
			else
				$sSelected = 'N';

			$ar[] = Array(
				'title' => (str_replace(array(';', ','), ' ', $arRes['TITLE'])),
				'desc' => isset($arDealStageList[$arRes['STAGE_ID']])? $arDealStageList[$arRes['STAGE_ID']]: '',
				'id' => $arRes['SID'],
				'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('crm', 'path_to_deal_show'),
					array(
						'deal_id' => $arRes['ID']
					)
				),
				'type'  => 'deal',
				'selected' => $sSelected
			);
		}
		$arResult['ELEMENT'] = array_merge($ar, $arResult['ELEMENT']);
	}
	if ($arSettings['DEAL'] == 'Y'
		&& isset($arSelected['DEAL']) && !empty($arSelected['DEAL']))
	{
		$arDealStageList = CCrmStatus::GetStatusListEx('DEAL_STAGE');
		$arSelect = array('ID', 'TITLE', 'STAGE_ID');
		$ar = Array();
		$obRes = CCrmDeal::GetList(array('ID' => 'DESC'), Array('ID' => $arSelected['DEAL']), $arSelect);
		while ($arRes = $obRes->Fetch())
		{
			$arRes['SID'] = $arResult['PREFIX'] == 'Y'? 'D_'.$arRes['ID']: $arRes['ID'];
			if (isset($arResult['SELECTED'][$arRes['SID']]))
			{
				unset($arResult['SELECTED'][$arRes['SID']]);
				$sSelected = 'Y';
			}
			else
				$sSelected = 'N';

			$ar[] = Array(
				'title' => (str_replace(array(';', ','), ' ', $arRes['TITLE'])),
				'desc' => isset($arDealStageList[$arRes['STAGE_ID']])? $arDealStageList[$arRes['STAGE_ID']]: '',
				'id' => $arRes['SID'],
				'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('crm', 'path_to_deal_show'),
					array(
						'deal_id' => $arRes['ID']
					)
				),
				'type'  => 'deal',
				'selected' => $sSelected
			);
		}
		$arResult['ELEMENT'] = array_merge($ar, $arResult['ELEMENT']);
	}
	if (isset($arSettings['PRODUCT'])
		&& $arSettings['PRODUCT'] == 'Y'
		&& isset($arSelected['PRODUCT'])
		&& !empty($arSelected['PRODUCT']))
	{
		$ar = array();
		$obRes = CCrmProduct::GetList(
			array('ID' => 'DESC'),
			array('ID' => $arSelected['PRODUCT']),
			array('ID', 'NAME', 'PRICE', 'CURRENCY_ID')
		);

		while ($arRes = $obRes->Fetch())
		{
			$arRes['SID'] = $arResult['PREFIX'] == 'Y'? 'D_'.$arRes['ID']: $arRes['ID'];
			if (isset($arResult['SELECTED'][$arRes['SID']]))
			{
				unset($arResult['SELECTED'][$arRes['SID']]);
				$sSelected = 'Y';
			}
			else
				$sSelected = 'N';

			$ar[] = array(
				'title' => $arRes['NAME'],
				'desc' => CCrmProduct::FormatPrice($arRes),
				'id' => $arRes['SID'],
				'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('crm', 'path_to_product_show'),
					array(
						'product_id' => $arRes['ID']
					)
				),
				'type'  => 'product',
				'selected' => $sSelected
			);
		}
		$arResult['ELEMENT'] = array_merge($ar, $arResult['ELEMENT']);
	}
}
?>