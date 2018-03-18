<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/************************************************************************************************************/
/*  Include Areas Component
/* Params:
/*	AREA_FILE_SHOW => {page | sect} - area to include. Default value - 'page'
/*	AREA_FILE_SUFFIX => string - suffix of file to seek. Default value - 'inc'
/*	AREA_FILE_RECURSIVE => {Y | N} - whether to search area file in parent directories. Used only when AREA_FILE_SHOW='sect'. Default value - 'Y'
/*	EDIT_MODE => {php | html | text} - default edit mode for an area. Default value - 'html'
/*	EDIT_TEMPLATE => string - default template to add new area. Default value - page_inc.php / sect_inc.php
/*
/************************************************************************************************************/

//$arParams["EDIT_MODE"] = in_array($arParams["EDIT_MODE"], array("php", "html", "text")) ? $arParams["EDIT_MODE"] : "html";
$arParams["EDIT_TEMPLATE"] = strlen($arParams["EDIT_TEMPLATE"]) > 0 ? $arParams["EDIT_TEMPLATE"] : $arParams["AREA_FILE_SHOW"]."_inc.php";

// check params values
$bHasPath = ($arParams["AREA_FILE_SHOW"] == 'file');
$sRealFilePath = $_SERVER["REAL_FILE_PATH"];

$io = CBXVirtualIo::GetInstance();

if (!$bHasPath)
{
	$arParams["AREA_FILE_SHOW"] = $arParams["AREA_FILE_SHOW"] == "sect" ? "sect" : "page";
	$arParams["AREA_FILE_SUFFIX"] = strlen($arParams["AREA_FILE_SUFFIX"]) > 0 ? $arParams["AREA_FILE_SUFFIX"] : "inc";
	$arParams["AREA_FILE_RECURSIVE"] = $arParams["AREA_FILE_RECURSIVE"] == "N" ? "N" : "Y";


	// check file for including
	if ($arParams["AREA_FILE_SHOW"] == "page")
	{
		// if page in SEF mode check real path
		if (strlen($sRealFilePath) > 0)
		{
			$slash_pos = strrpos($sRealFilePath, "/");
			$sFilePath = substr($sRealFilePath, 0, $slash_pos+1);
			$sFileName = substr($sRealFilePath, $slash_pos+1);
			$sFileName = substr($sFileName, 0, strlen($sFileName)-4)."_".$arParams["AREA_FILE_SUFFIX"].".php";
		}
		// otherwise use current
		else
		{
			$sFilePath = $APPLICATION->GetCurDir();
			$sFileName = substr($APPLICATION->GetCurPage(true), 0, strlen($APPLICATION->GetCurPage(true))-4)."_".$arParams["AREA_FILE_SUFFIX"].".php";
			$sFileName = substr($sFileName, strlen($sFilePath));
		}

		$sFilePathTMP = $sFilePath;
		$bFileFound = $io->FileExists($_SERVER['DOCUMENT_ROOT'].$sFilePath.$sFileName);
	}
	else
	{
		// if page is in SEF mode - check real path
		if (strlen($sRealFilePath) > 0)
		{
			$slash_pos = strrpos($sRealFilePath, "/");
			$sFilePath = substr($sRealFilePath, 0, $slash_pos+1);
		}
		// otherwise use current
		else
		{
			$sFilePath = $APPLICATION->GetCurDir();
		}

		$sFilePathTMP = $sFilePath;
		$sFileName = "sect_".$arParams["AREA_FILE_SUFFIX"].".php";

		$bFileFound = $io->FileExists($_SERVER['DOCUMENT_ROOT'].$sFilePath.$sFileName);

		// if file not found and is set recursive check - start it
		if (!$bFileFound && $arParams["AREA_FILE_RECURSIVE"] == "Y" && $sFilePath != "/")
		{
			$finish = false;

			do
			{
				// back one level
				if (substr($sFilePath, -1) == "/") $sFilePath = substr($sFilePath, 0, -1);
				$slash_pos = strrpos($sFilePath, "/");
				$sFilePath = substr($sFilePath, 0, $slash_pos+1);

				$bFileFound = $io->FileExists($_SERVER['DOCUMENT_ROOT'].$sFilePath.$sFileName);

				// if we are on the root - finish
				$finish = $sFilePath == "/";
			}
			while (!$finish && !$bFileFound);
		}
	}
}
else
{
	if (substr($arParams['PATH'], 0, 1) != '/')
	{
		// if page in SEF mode check real path
		if (strlen($sRealFilePath) > 0)
		{
			$slash_pos = strrpos($sRealFilePath, "/");
			$sFilePath = substr($sRealFilePath, 0, $slash_pos+1);
		}
		// otherwise use current
		else
		{
			$sFilePath = $APPLICATION->GetCurDir();
		}

		$arParams['PATH'] = Rel2Abs($sFilePath, $arParams['PATH']);
	}

	$slash_pos = strrpos($arParams['PATH'], "/");
	$sFilePath = substr($arParams['PATH'], 0, $slash_pos+1);
	$sFileName = substr($arParams['PATH'], $slash_pos+1);

	$bFileFound = $io->FileExists($_SERVER['DOCUMENT_ROOT'].$sFilePath.$sFileName);

	$sFilePathTMP = $sFilePath;
}

if($APPLICATION->GetShowIncludeAreas())
{
	//need fm_lpa for every .php file, even with no php code inside
	$bPhpFile = (!$GLOBALS["USER"]->CanDoOperation('edit_php') && in_array(GetFileExtension($sFileName), GetScriptFileExt()));

	$bCanEdit = $USER->CanDoFileOperation('fm_edit_existent_file', array(SITE_ID, $sFilePath.$sFileName)) && (!$bPhpFile || $GLOBALS["USER"]->CanDoFileOperation('fm_lpa', array(SITE_ID, $sFilePath.$sFileName)));
	$bCanAdd = $USER->CanDoFileOperation('fm_create_new_file', array(SITE_ID, $sFilePathTMP.$sFileName)) && (!$bPhpFile || $GLOBALS["USER"]->CanDoFileOperation('fm_lpa', array(SITE_ID, $sFilePathTMP.$sFileName)));

	if($bCanEdit || $bCanAdd)
	{
		$editor = '&site='.SITE_ID.'&back_url='.urlencode($_SERVER['REQUEST_URI']).'&templateID='.urlencode(SITE_TEMPLATE_ID);

		if ($bFileFound)
		{
			if ($bCanEdit)
			{
				$arMenu = array();
				if($USER->CanDoOperation('edit_php'))
				{
					$arMenu[] = array(
						"ACTION" => 'javascript:'.$APPLICATION->GetPopupLink(
							array(
								'URL' => "/bitrix/admin/public_file_edit_src.php?lang=".LANGUAGE_ID."&template=".urlencode($arParams["EDIT_TEMPLATE"])."&path=".urlencode($sFilePath.$sFileName).$editor,
								"PARAMS" => array(
									'width' => 770,
									'height' => 570,
									'resize' => true,
									"dialog_type" => 'EDITOR'
								)
							)
						),
						"ICON" => "panel-edit-php",
						"TEXT"=>GetMessage("WD_POPUP_COMP_INCLUDE_EDIT_PHP"),
						"TITLE" => GetMessage("WD_POPUP_INCLUDE_AREA_EDIT_".$arParams["AREA_FILE_SHOW"]."_NOEDITOR"),
					);
				}
				$arIcons = array(
					array(
						"URL" => 'javascript:'.$APPLICATION->GetPopupLink(
							array(
								'URL' => "/bitrix/admin/public_file_edit.php?lang=".LANGUAGE_ID."&from=main.include&template=".urlencode($arParams["EDIT_TEMPLATE"])."&path=".urlencode($sFilePath.$sFileName).$editor,
								"PARAMS" => array(
									'width' => 770,
									'height' => 570,
									'resize' => true
								)
							)
						),
						"DEFAULT" => $APPLICATION->GetPublicShowMode() != 'configure',
						"ICON" => "bx-context-toolbar-edit-icon",
						"TITLE"=>GetMessage("WD_POPUP_COMP_INCLUDE_EDIT"),
						"ALT" => GetMessage("WD_POPUP_INCLUDE_AREA_EDIT_".$arParams["AREA_FILE_SHOW"]),
						"MENU" => $arMenu,
					),
				);
			}

			if ($sFilePath != $sFilePathTMP && $bCanAdd)
			{
				$arMenu = array();
				if($USER->CanDoOperation('edit_php'))
				{
					$arMenu[] = array(
						"ACTION" => 'javascript:'.$APPLICATION->GetPopupLink(
							array(
								'URL' => "/bitrix/admin/public_file_edit_src.php?lang=".LANGUAGE_ID."&new=Y&path=".urlencode($sFilePathTMP.$sFileName)."&new=Y&template=".urlencode($arParams["EDIT_TEMPLATE"]).$editor,
								"PARAMS" => array(
									'width' => 770,
									'height' => 570,
									'resize' => true,
									"dialog_type" => 'EDITOR'
								)
							)
						),
						"ICON" => "panel-edit-php",
						"TEXT"	=> GetMessage("WD_POPUP_COMP_INCLUDE_ADD_PHP"),
						"TITLE" => GetMessage("WD_POPUP_INCLUDE_AREA_ADD_".$arParams["AREA_FILE_SHOW"]."_NOEDITOR"),
					);
				}
				$arIcons[] = array(
					"URL" => 'javascript:'.$APPLICATION->GetPopupLink(
						array(
							'URL' => "/bitrix/admin/public_file_edit.php?lang=".LANGUAGE_ID."&from=main.include&new=Y&path=".urlencode($sFilePathTMP.$sFileName)."&new=Y&template=".urlencode($arParams["EDIT_TEMPLATE"]).$editor,
							"PARAMS" => array(
								'width' => 770,
								'height' => 570,
								'resize' => true
							)
						)
					),
					"DEFAULT" => $APPLICATION->GetPublicShowMode() != 'configure',
					"ICON" => "bx-context-toolbar-create-icon",
					"TITLE" => GetMessage("WD_POPUP_COMP_INCLUDE_ADD"),
					"ALT" => GetMessage("WD_POPUP_INCLUDE_AREA_ADD_".$arParams["AREA_FILE_SHOW"]),
					"MENU" => $arMenu,
				);
			}
		}
		elseif ($bCanAdd)
		{
			$arMenu = array();
			if($USER->CanDoOperation('edit_php'))
			{
				$arMenu[] = array(
					"ACTION" => 'javascript:'.$APPLICATION->GetPopupLink(
						array(
							'URL' => "/bitrix/admin/public_file_edit_src.php?lang=".LANGUAGE_ID."&path=".urlencode($sFilePathTMP)."&filename=".urlencode($sFileName)."&new=Y&template=".urlencode($arParams["EDIT_TEMPLATE"]).$editor,
							"PARAMS" => array(
								'width' => 770,
								'height' => 570,
								'resize' => true,
								"dialog_type" => 'EDITOR'
							)
						)
					),
					"ICON" => "panel-edit-php",
					"TEXT" => GetMessage("WD_POPUP_COMP_INCLUDE_ADD1_PHP"),
					"TITLE" => GetMessage("WD_POPUP_INCLUDE_AREA_ADD_".$arParams["AREA_FILE_SHOW"]."_NOEDITOR"),
				);
			}
			$arIcons = array(
				array(
					"URL" => 'javascript:'.$APPLICATION->GetPopupLink(
						array(
							'URL' => "/bitrix/admin/public_file_edit.php?lang=".LANGUAGE_ID."&from=main.include&path=".urlencode($sFilePathTMP.$sFileName)."&new=Y&template=".urlencode($arParams["EDIT_TEMPLATE"]).$editor,
							"PARAMS" => array(
								'width' => 770,
								'height' => 570,
								'resize' => true
							)
						)
					),
					"DEFAULT" => $APPLICATION->GetPublicShowMode() != 'configure',
					"ICON" => "bx-context-toolbar-create-icon",
					"TITLE" => GetMessage("WD_POPUP_COMP_INCLUDE_ADD1"),
					"ALT" => GetMessage("WD_POPUP_INCLUDE_AREA_ADD_".$arParams["AREA_FILE_SHOW"]),
					"MENU" => $arMenu,
				),
			);
		}

		if (is_array($arIcons) && count($arIcons) > 0)
		{
			$this->AddIncludeAreaIcons($arIcons);
		}
	}
}

if ($bFileFound) {
	CModule::IncludeModule('webdebug.popup');
	define('WD_POPUP_NEED_INCLUDE',true);
	$arResult["FILE"] = $io->GetPhysicalName($_SERVER["DOCUMENT_ROOT"].$sFilePath.$sFileName);
	$arResult['POPUP_PARAMS'] = array(
		'ID' => $arParams['POPUP_ID'],
		'NAME' => $arParams['POPUP_NAME'],
		'WIDTH' => $arParams['POPUP_WIDTH'],
		'CLOSE' => $arParams['POPUP_CLOSE'],
		'APPEND_TO_BODY' => $arParams['POPUP_APPEND_TO_BODY'],
		'DISPLAY_NONE' => $arParams['POPUP_DISPLAY_NONE'],
		'ANIMATION' => in_array($arParams['POPUP_ANIMATION'],array('fade','fadeAndPop','none')) ? $arParams['POPUP_ANIMATION'] : 'fadeAndPop',
		'CALLBACK_INIT' => $arParams['POPUP_CALLBACK_INIT'],
		'CALLBACK_OPEN' => $arParams['POPUP_CALLBACK_OPEN'],
		'CALLBACK_SHOW' => $arParams['POPUP_CALLBACK_SHOW'],
		'CALLBACK_CLOSE' => $arParams['POPUP_CALLBACK_CLOSE'],
		'CLASSES' => $arParams['POPUP_CLASSES'],
		'LINK_TO' => $arParams['POPUP_LINK_TO'],
	);
	// Set own link params
	if ($arParams['POPUP_LINK_SHOW']!='N') {
		$GLOBALS['WD_POPUP_LINK_INDEX'] = IntVal($GLOBALS['WD_POPUP_LINK_INDEX'])+1;
		$arResult['POPUP_LINK_ID'] = 'wd_'.$arParams['POPUP_ID'].'_opener_'.$GLOBALS['WD_POPUP_LINK_INDEX'];
		$arResult['POPUP_PARAMS']['LINK_TO'] = '#'.$arResult['POPUP_LINK_ID'];
		if (trim($arParams['POPUP_LINK_TEXT'])=='') {
			$arParams['POPUP_LINK_TEXT'] = GetMessage('WD_POPUP_LINK_TEXT_DEFAULT');
		}
	}
	// Set default class
	if (is_array($arResult['POPUP_PARAMS']['CLASSES'])) {
		foreach($arResult['POPUP_PARAMS']['CLASSES'] as $Key => $Value) {
			if (trim($Value)=='') unset($arResult['POPUP_PARAMS']['CLASSES'][$Key]);
		}
	} else {
		$arResult['POPUP_PARAMS']['CLASSES'] = trim($arResult['POPUP_PARAMS']['CLASSES']);
	}
	if (empty($arResult['POPUP_PARAMS']['CLASSES'])) {
		$arResult['POPUP_PARAMS']['CLASSES'] = array('wd_popup_style_05');
	}
	$arResult['POPUP_PARAMS']['POPUP_AUTOOPEN'] = $arParams['POPUP_AUTOOPEN']=='Y' ? true : false;
	if (!is_numeric($arParams['POPUP_AUTOOPEN_DELAY']) || $arParams['POPUP_AUTOOPEN_DELAY']<0) $arParams['POPUP_AUTOOPEN_DELAY'] = 500;
	$arResult['POPUP_PARAMS']['POPUP_AUTOOPEN_DELAY'] = $arParams['POPUP_AUTOOPEN_DELAY'];
	// Set default ID
	if (trim($arResult['POPUP_PARAMS']['ID'])=='') {
		$GLOBALS['WD_POPUP_LINK_INDEX'] = IntVal($GLOBALS['WD_POPUP_LINK_INDEX'])+1;
		$arResult['POPUP_PARAMS']['ID'] = 'wd_popup_'.ToLower(RandString(8)).'_'.$GLOBALS['WD_POPUP_LINK_INDEX'];
	}
	$this->IncludeComponentTemplate();
}
?>