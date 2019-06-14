<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$MODULE_ID = 'api.print';
$IBLOCK_ID = intval(COption::GetOptionString($MODULE_ID, 'PRINT_IBLOCK_ID'));

$arResult['TEXT_TEMPLATE'] = !empty($_REQUEST['TEXT_TEMPLATE']) ? htmlspecialcharsback(base64_decode($_REQUEST['TEXT_TEMPLATE'])) : '';
$arResult['CSS_FILE_URL']  = !empty($_REQUEST['CSS_FILE_URL']) ? base64_decode($_REQUEST['CSS_FILE_URL']) : '';

$arResult['SITE_NAME'] = COption::GetOptionString('main', 'site_name', '', SITE_ID);
$rsSites               = CSite::GetByID(SITE_ID);
if($arSite = $rsSites->Fetch())
{
	if(!empty($arSite['SITE_NAME']))
		$arResult['SITE_NAME'] = $arSite['SITE_NAME'];
}

if(!$IBLOCK_ID)
	$arResult['ERROR_MESSAGE'][] = GetMessage('EMPTY_IBLOCK_ID');

if($_REQUEST['set_print'] == 'Y' && $IBLOCK_ID)
{
	CModule::IncludeModule('iblock');
	$arSelect      = !empty($_REQUEST['FIELD_CODE']) ? unserialize(base64_decode($_REQUEST['FIELD_CODE'])) : array();
	$arSelect      = array_unique(array_merge(array('ID','NAME','IBLOCK_ID'), $arSelect));
	$PROPERTY_CODE = !empty($_REQUEST['PROPERTY_CODE']) ? unserialize(base64_decode($_REQUEST['PROPERTY_CODE'])) : array();
	$D_A_F_L       = htmlspecialcharsEx(trim($_REQUEST['D_A_F_L']));
	$D_A_F_R       = htmlspecialcharsEx(trim($_REQUEST['D_A_F_R']));
	$C_A_S         = !empty($_REQUEST['C_A_S']);
	$PPS           = !empty($_REQUEST['PPS']) ? unserialize(base64_decode($_REQUEST['PPS'])) : array();
	$DPS           = !empty($_REQUEST['DPS']) ? unserialize(base64_decode($_REQUEST['DPS'])) : array();
	$allowPrint    = false;

	if(!empty($D_A_F_L) && !empty($D_A_F_R))
		$allowPrint = true;

	if($allowPrint)
	{
		$arFilter = array(
			'IBLOCK_ID'     => $IBLOCK_ID,
			'ACTIVE'        => 'Y',
			'ACTIVE_DATE'   => 'Y',
			'<=DATE_CREATE' => $D_A_F_R . " 23:59:59",
			'>=DATE_CREATE' => $D_A_F_L . " 00:00:00",
		);

		if($C_A_S)
		{
			$arFilter['SECTION_GLOBAL_ACTIVE'] = 'Y';
			$arFilter['SECTION_ACTIVE']        = 'Y';
			$arFilter['SECTION_ID']            = '';
		}

		$res = CIBlockElement::GetList(array('DATE_CREATE' => 'ASC'), $arFilter, false, false, $arSelect);

		while($obElement = $res->GetNextElement(true, false))
		{
			$arElement               = $obElement->GetFields();
			$arElement['PROPERTIES'] = $obElement->GetProperties();

			$arElement['DISPLAY_PROPERTIES'] = array();
			if(!empty($PROPERTY_CODE))
			{
				foreach($PROPERTY_CODE as $pid)
				{
					$prop = $arElement['PROPERTIES'][ $pid ];
					if(!empty($prop['VALUE']))
					{
						$arElement['DISPLAY_PROPERTIES'][ $pid ] = CIBlockFormatProperties::GetDisplayValue($arResult, $prop, 'print_out');
					}
				}
			}


			if(!empty($arElement['PREVIEW_PICTURE']))
				$arElement['PREVIEW_PICTURE'] = CFile::GetFileArray($arElement['PREVIEW_PICTURE']);

			if(!empty($arElement['DETAIL_PICTURE']))
				$arElement['DETAIL_PICTURE'] = CFile::GetFileArray($arElement['DETAIL_PICTURE']);


			if(!empty($PPS['WIDTH']) && !empty($PPS['HEIGHT']) && !empty($arElement['PREVIEW_PICTURE']))
			{

				$arFileTmp = CFile::ResizeImageGet(
					$arElement['PREVIEW_PICTURE'],
					array('width' => intval($PPS['WIDTH']), 'height' => intval($PPS['HEIGHT'])),
					BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
					true,
					array('name' => 'sharpen', 'precision' => 95)
				);

				$arElement['PREVIEW_PICTURE']['SRC']       = $arFileTmp['src'];
				$arElement['PREVIEW_PICTURE']['WIDTH']     = $arFileTmp['width'];
				$arElement['PREVIEW_PICTURE']['HEIGHT']    = $arFileTmp['height'];
				$arElement['PREVIEW_PICTURE']['FILE_SIZE'] = $arFileTmp['size'];
			}

			if(!empty($DPS['WIDTH']) && !empty($DPS['HEIGHT']) && !empty($arElement['DETAIL_PICTURE']))
			{
				$arFileTmp = CFile::ResizeImageGet(
					$arElement['DETAIL_PICTURE'],
					array('width' => intval($DPS['WIDTH']), 'height' => intval($DPS['HEIGHT'])),
					BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
					true,
					array('name' => 'sharpen', 'precision' => 95)
				);

				$arElement['DETAIL_PICTURE']['SRC']       = $arFileTmp['src'];
				$arElement['DETAIL_PICTURE']['WIDTH']     = $arFileTmp['width'];
				$arElement['DETAIL_PICTURE']['HEIGHT']    = $arFileTmp['height'];
				$arElement['DETAIL_PICTURE']['FILE_SIZE'] = $arFileTmp['size'];
			}

			$arElement['PREVIEW_PICTURE']['ALIGN']  = $PPS['ALIGN'];
			$arElement['PREVIEW_PICTURE']['BORDER'] = $PPS['BORDER'];
			$arElement['DETAIL_PICTURE']['ALIGN']   = $DPS['ALIGN'];
			$arElement['DETAIL_PICTURE']['BORDER']  = $DPS['BORDER'];


			$arResult['ITEMS'][] = $arElement;
		}

		if(!empty($arResult['TEXT_TEMPLATE']))
		{
			$arResult['TEXT_TEMPLATE'] = str_replace('#X#', count($arResult['ITEMS']), $arResult['TEXT_TEMPLATE']);
			$arResult['TEXT_TEMPLATE'] = str_replace('#DATE_ACTIVE_FROM#', $D_A_F_L, $arResult['TEXT_TEMPLATE']);
			$arResult['TEXT_TEMPLATE'] = str_replace('#DATE_ACTIVE_TO#', $D_A_F_R, $arResult['TEXT_TEMPLATE']);
		}

		if(empty($arResult['ITEMS']))
			$arResult['ERROR_MESSAGE'][] = GetMessage('ELEMENTS_NOT_FOUND');
	}
	else
		$arResult['ERROR_MESSAGE'][] = GetMessage('SET_ALL_DATE');
}
else
	$arResult['ERROR_MESSAGE'][] = GetMessage('PARAMS_IS_EMPTY');

$this->IncludeComponentTemplate();