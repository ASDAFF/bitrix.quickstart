<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
$arResult['MARGIN_LEFT'] = ($arParams['PICTURE'] && $arParams['RESIZE_PICTURE'] && $arParams['PICTURE_WIDTH'] ? $arParams['PICTURE_WIDTH'] + 15 : 0);
$arResult['DEFAULT_PICTURE'] = array();

if($arParams['PICTURE'])
{
	$arEmptyPreview  = array();
	$strEmptyPreviewSRC = $this->GetFolder() . '/images/no_photo.png';
	$strEmptyPreview = $_SERVER['DOCUMENT_ROOT'] . $strEmptyPreviewSRC;

	if(file_exists($strEmptyPreview))
	{
		if($arParams['RESIZE_PICTURE'])
		{
			$arImageSize = array(
				'width' => $arParams['PICTURE_WIDTH'],
				'height' => $arParams['PICTURE_HEIGHT']
			);

			$strDestinationFileSRC = '/upload/resize_cache/api_search/'.$arResult['COMPONENT_ID'].'_'.$arImageSize['width'].'_'.$arImageSize['height'].'_no_photo.png';
			$strDestinationFile = $_SERVER['DOCUMENT_ROOT'] . $strDestinationFileSRC;
			if(file_exists($strDestinationFile))
			{
				$arEmptyPreview = array(
					'SRC'    => $strDestinationFileSRC,
					'WIDTH'  => intval($arImageSize['width']),
					'HEIGHT' => intval($arImageSize['height']),
				);
			}
			else
			{
				if(CFile::ResizeImageFile($strEmptyPreview,$strDestinationFile,$arImageSize))
				{
					$arEmptyPreview = array(
						'SRC'    => $strDestinationFileSRC,
						'WIDTH'  => intval($arImageSize['width']),
						'HEIGHT' => intval($arImageSize['height']),
					);
				}
			}
		}

		if(empty($arEmptyPreview))
		{
			if($arImageSize = CFile::GetImageSize($strEmptyPreview))
			{
				$arEmptyPreview = array(
					'SRC'    => $strEmptyPreviewSRC,
					'WIDTH'  => intval($arImageSize[0]),
					'HEIGHT' => intval($arImageSize[1]),
					//'SIZE'   => $arImageSize[2],
				);
			}
		}
		
		if($arEmptyPreview['SRC'])
			$arEmptyPreview['SRC'] = CUtil::GetAdditionalFileURL($arEmptyPreview['SRC']);
	}

	$arResult['DEFAULT_PICTURE'] = $arEmptyPreview;
}